<?php 

require_once (dirname(__FILE__).'/easy-basic-authentication-log-class.php');
require_once (dirname(__FILE__).'/easy-basic-authentication-emailalert-class.php');
require_once (dirname(__FILE__).'/easy-basic-authentication-form-class.php');

class easy_basic_authentication_class {

    private $log;
    private $email;
    private $form;

    public function __construct()
    {
        $this->log = new easy_basic_authentication_log_class();
        $this->email = new easy_basic_authentication_emailalert_class();
        $this->form = new easy_basic_authentication_form_class();

        if(get_option( 'basic_auth_plugin_admin_enable' )) {
            if (in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'))) {
                add_action( 'init', array($this,'basic_auth_admin') );
            }
        }

        if(get_option( 'basic_auth_plugin_enable' ) && get_option( 'basic_auth_plugin_admin_enable' )){
            add_action( 'init', array($this,'basic_auth_root') );
        }
        
        add_action( 'admin_menu', array($this,'basic_auth_plugin_menu' ));
        add_action( 'admin_init', array($this->form,'basic_auth_plugin_settings_init' ));

        add_action('admin_init', function () {
            $post_data = $_POST;
            $this->form->basic_auth_plugin_save_settings($post_data);
        });
        
    }

    public function basic_auth_root() {
        $user = get_option( 'basic_auth_plugin_username' ); 
        $pass = get_option( 'basic_auth_plugin_password' ); 
    
        if ( !isset( $_SERVER['PHP_AUTH_USER'] ) || !isset( $_SERVER['PHP_AUTH_PW'] ) ||
             $_SERVER['PHP_AUTH_USER'] != $user || !wp_check_password( $_SERVER['PHP_AUTH_PW'], $pass ) ) {
            
            $this->do_exit(true);
        }
    }
    
    public function basic_auth_admin() {
        $user = get_option( 'basic_auth_plugin_username' ); 
        $pass = get_option( 'basic_auth_plugin_password' ); 
    
        if ( !isset( $_SERVER['PHP_AUTH_USER'] ) || !isset( $_SERVER['PHP_AUTH_PW'] ) ||
            $_SERVER['PHP_AUTH_USER'] != $user || !wp_check_password( $_SERVER['PHP_AUTH_PW'], $pass ) ) {
                        
            $this->do_exit(true);
        }        
    }
    
    public function do_exit($admin_area = false) {

        if (in_array($_SERVER['REMOTE_ADDR'], $this->getWhiteList())) {
            return;
        }
        $this->basic_auth_action_failed_access();
        do_action('basic_auth_before_401');

        if($admin_area) {
            do_action('basic_auth_before_401_admin_area');
            
            header( 'WWW-Authenticate: Basic realm="My Website"' );
            header( 'HTTP/1.0 401 Unauthorized' );
            exit;
        } else {
            header( 'HTTP/1.1 401 Unauthorized' );
            header( 'WWW-Authenticate: Basic realm="Admin Area"' );
            exit;
        }
    }

    public function getWhiteList() {
        return get_option( 'basic_auth_plugin_whitelist' )?explode(',',get_option( 'basic_auth_plugin_whitelist' )):[];
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
    
    public function basic_auth_action_failed_access() {
        if($this->log->is_enabled()) {
            $this->log->update_status($_SERVER);
        }
        if($this->email->is_enabled()) {
            $this->email->sendAlert($_SERVER);
        }
    }

}
