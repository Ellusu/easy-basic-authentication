<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


require_once (dirname(__FILE__).'/easy-basic-authentication-log-class.php');
require_once (dirname(__FILE__).'/easy-basic-authentication-emailalert-class.php');
require_once (dirname(__FILE__).'/easy-basic-authentication-form-class.php');
require_once (dirname(__FILE__).'/easy-basic-authentication-notice-class.php');
require_once (dirname(__FILE__).'/easy-basic-authentication-compatcheck-class.php');

class easy_basic_authentication_class {

    private $log;
    private $email;
    private $form;
    private $compatcheck;

    public function __construct()
    {
        $this->log = new easy_basic_authentication_log_class();
        $this->email = new easy_basic_authentication_emailalert_class();
        $this->form = new easy_basic_authentication_form_class();
        $notice = new easy_basic_authentication_notice_class();
        $this->compatcheck = new easy_basic_authentication_compatcheck_class();
        $this->compatcheck->register_hooks();

        // Fuori da una richiesta HTTP la sfida 401 non ha alcun senso: da riga di comando
        // diventa un exit() silenzioso che blocca WP-CLI, il cron di sistema e qualunque
        // script che faccia require di wp-load.php.
        if ( ! self::is_cli() ) {
            $pagenow = isset( $GLOBALS['pagenow'] ) ? $GLOBALS['pagenow'] : '';

            if(get_option( 'basic_auth_plugin_admin_enable' )) {
                if (in_array($pagenow, array('wp-login.php', 'wp-register.php'))) {
                    add_action( 'init', array($this,'basic_auth_root') );
                }
            }

            if(get_option( 'basic_auth_plugin_enable' ) && get_option( 'basic_auth_plugin_admin_enable' )){
                add_action( 'init', array($this,'basic_auth_root') );
            }
        }
        
        add_action( 'init', array($this, 'maybe_upgrade'), 1 );

        add_action( 'admin_menu', array($this,'basic_auth_plugin_menu' ));
        add_action( 'admin_init', array($this->form,'basic_auth_plugin_settings_init' ));

        add_action('admin_init', function () {
            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }

            // Il nonce e verificato dentro basic_auth_plugin_save_settings(), che riceve
            // i dati grezzi perche deve distinguere un campo assente da uno vuoto (le
            // checkbox non spuntate non vengono inviate).
            // phpcs:ignore WordPress.Security.NonceVerification.Missing
            $post_data = $_POST;
            $this->form->basic_auth_plugin_save_settings($post_data);
        });
        
    }

    /**
     * Se stiamo girando fuori da una richiesta HTTP (WP-CLI, cron di sistema, script).
     *
     * @return bool
     */
    public static function is_cli() {
        if ( defined( 'WP_CLI' ) && WP_CLI ) {
            return true;
        }

        return 'cli' === PHP_SAPI || 'phpdbg' === PHP_SAPI;
    }

    public function basic_auth_root()
    {
        if ( self::is_cli() ) {
            return;
        }

        $user = get_option('basic_auth_plugin_username');
        $pass = get_option('basic_auth_plugin_password');
        
        if ($this->whiteListChecker()) {
            return;
        }

        if ($this->urlWhiteListChecker()) {
            return;
        }

        // Alcuni server (nginx + FastCGI su tutti) non popolano PHP_AUTH_USER da soli:
        // le credenziali arrivano solo nell'header Authorization e vanno estratte a mano.
        if (!isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authorization = sanitize_text_field( wp_unslash( $_SERVER['HTTP_AUTHORIZATION'] ) );

            if ( preg_match( '#^Basic\s+([A-Za-z0-9+/=]+)$#i', $authorization, $matches ) ) {
                $decoded = base64_decode( $matches[1], true );

                // Senza i due punti non ci sono due campi: la list() originale
                // generava un warning "Undefined array key 1" su PHP 8.
                if ( false !== $decoded && false !== strpos( $decoded, ':' ) ) {
                    list( $sent_user, $sent_pass ) = explode( ':', $decoded, 2 );
                    $_SERVER['PHP_AUTH_USER'] = $sent_user;
                    $_SERVER['PHP_AUTH_PW']   = $sent_pass;
                }
            }
        }

        // Basic Auth prevede sempre un primo giro senza credenziali: il browser chiede,
        // riceve 401, e solo allora rimanda con utente e password. Quel 401 non e un
        // tentativo fallito, e registrarlo significherebbe una voce di log (e una mail)
        // per ogni visitatore e ogni bot che passa.
        $credentials_sent = isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']);

        if (!$credentials_sent) {
            $this->do_exit(true, false);
            return;
        }

        // L'utente e salvato passando per sanitize_text_field(), quindi quello in arrivo
        // va normalizzato allo stesso modo perche il confronto sia sensato.
        $sent_user = sanitize_text_field( wp_unslash( $_SERVER['PHP_AUTH_USER'] ) );

        // La password invece NON va sanitizzata: alterarla farebbe fallire l'accesso a
        // chiunque ne usi una con caratteri speciali. Non viene mai stampata, solo
        // confrontata con l'hash.
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $sent_pass = wp_unslash( $_SERVER['PHP_AUTH_PW'] );

        $credentials_valid = hash_equals( (string) $user, $sent_user )
            && wp_check_password( $sent_pass, $pass );

        if (!$credentials_valid) {
            // Credenziali inviate e sbagliate: questo si che va segnalato.
            $this->do_exit(true, true);
        }
    }
      
    public function urlWhiteListChecker() {
        if (empty($_SERVER['HTTP_HOST']) || empty($_SERVER['REQUEST_URI'])) {
            return false;
        }

        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

        $host = sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) );

        // L'URI serve solo al confronto con la whitelist, non viene mai stampato.
        // sanitize_text_field() qui sarebbe dannoso: rimuove le sequenze %xx e
        // cambierebbe il confronto su qualsiasi percorso con caratteri codificati.
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $request_uri = wp_unslash( $_SERVER['REQUEST_URI'] );

        $currentUrl = $scheme . '://' . $host . $request_uri;

        $whitelist = $this->getUrlWhiteList();

        foreach ($whitelist as $entry) {
            if ($this->isUrlAllowed($currentUrl, $entry)) {
                return true;
            }
        }

        return false;
    }

    private function isUrlAllowed($currentUrl, $entry) {
        $currentUrl = rtrim($currentUrl, '/');
        $entry = rtrim($entry, '/');

        if (strpos($entry, '/') === 0) {
            $path = wp_parse_url($currentUrl, PHP_URL_PATH);
            return stripos($path, $entry) === 0;
        }

        if (!preg_match('#^https?://#i', $entry)) {
            $scheme = wp_parse_url($currentUrl, PHP_URL_SCHEME) ?: 'https';
            $entry = $scheme . '://' . $entry;
        }

        return stripos($currentUrl, $entry) === 0;
    }

    public function whiteListChecker() {
        if (!isset($_SERVER['REMOTE_ADDR'])) {
            return false;
        }
    
        // Un REMOTE_ADDR malformato non deve arrivare ai confronti della whitelist:
        // ip2long() restituirebbe false e il risultato sarebbe imprevedibile.
        $ip = filter_var( wp_unslash( $_SERVER['REMOTE_ADDR'] ), FILTER_VALIDATE_IP );

        if ( false === $ip ) {
            return false;
        }

        $whitelist = $this->getWhiteList();

        foreach ($whitelist as $entry) {
            if ($this->isIpAllowed($ip, $entry)) {
                return true;
            }
        }
        return false;
    }
    
    private function isIpAllowed($ip, $entry) {
        if (filter_var($entry, FILTER_VALIDATE_IP)) {
            return $ip === $entry;
        } elseif (strpos($entry, '/') !== false) {
            return $this->isIpInCidr($ip, $entry);
        } elseif (strpos($entry, '-') !== false) {
            return $this->isIpInRange($ip, $entry);
        }
        return false;
    }
    
    private function isIpInCidr($ip, $cidr) {
        list($subnet, $mask) = explode('/', $cidr);
        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);
        $maskLong = -1 << (32 - $mask);
        return ($ipLong & $maskLong) === ($subnetLong & $maskLong);
    }
    
    private function isIpInRange($ip, $range) {
        list($start, $end) = array_map('trim', explode('-', $range));
        $ipLong = ip2long($ip);
        $startLong = ip2long($start);
        $endLong = ip2long($end);
        return ($ipLong >= $startLong && $ipLong <= $endLong);
    }

    /**
     * Manda la sfida 401.
     *
     * @param bool $admin_area   Se la richiesta riguarda l'area di amministrazione.
     * @param bool $log_attempt  Se registrare l'accesso fra i tentativi falliti.
     *                           False per il 401 iniziale, che fa parte del protocollo.
     */
    public function do_exit($admin_area = false, $log_attempt = true) {

        if ($log_attempt) {
            $this->basic_auth_action_failed_access();
        }

        do_action('basic_auth_before_401');

        if ($admin_area) {
            do_action('basic_auth_before_401_admin_area');
        }

        // I browser memorizzano le credenziali per realm: cambiare questa stringa fa
        // ricomparire la richiesta di accesso a chi era gia autenticato. Il valore
        // predefinito resta quello storico; chi vuole cambiarlo usa il filtro.
        $realm = apply_filters('basic_auth_realm', 'My Website', $admin_area);
        $realm = str_replace(array('"', "\r", "\n"), '', (string) $realm);

        header('WWW-Authenticate: Basic realm="' . $realm . '"');
        status_header(401);
        exit;
    }

    public function getWhiteList() {
        return get_option( 'basic_auth_plugin_whitelist' )?explode(',',get_option( 'basic_auth_plugin_whitelist' )):[];
    }

    public function getUrlWhiteList() {
        return get_option( 'basic_auth_plugin_urlwhitelist' )?explode(',',get_option( 'basic_auth_plugin_urlwhitelist' )):[];
    }

    public function basic_auth_plugin_menu() {
        add_menu_page(
            __('Configurations for Easy Basic Authentication', 'easy-basic-authentication'), 
            __('Easy Basic A.', 'easy-basic-authentication'), 
            'manage_options',
            'basic-auth-plugin',
            array($this->form, 'basic_auth_plugin_settings_page'),
            'dashicons-lock'
        );
        if($this->log->is_enabled()) {
            $this->log->getMenu();
        }
    }  
    
    /**
     * Migrazioni una tantum, eseguite quando cambia la versione del plugin.
     *
     * Fino alla 4.0.0 il log degli accessi stava in un'opzione in autoload: fino a
     * 500 voci, circa 144 KB, deserializzate a ogni richiesta del sito.
     */
    public function maybe_upgrade() {
        if ( get_option( 'basic_auth_plugin_db_version' ) === EASY_BASIC_AUTHENTICATION_VERSION ) {
            return;
        }

        $logs = get_option( 'basic_auth_failure_logs', null );
        if ( null !== $logs ) {
            update_option( 'basic_auth_failure_logs', $logs, false );
        }

        update_option( 'basic_auth_plugin_db_version', EASY_BASIC_AUTHENTICATION_VERSION, false );
    }

    public function basic_auth_action_failed_access() {
        if($this->log->is_enabled()) {
            $this->log->update_status($_SERVER);
        }
        if($this->email->is_enabled()) {
            $this->email->sendAlert($_SERVER);
        }
    }

}
