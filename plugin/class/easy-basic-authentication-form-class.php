<?php 

class easy_basic_authentication_form_class {

    public $form_field = [];

    public function __construct()
    {   
        $this->form_field = array(
            '0' => array(
                'id'        => 'basic-auth-plugin-admin-enable',
                'title'     => __('Enable for wp-admin', 'easy-basic-authentication'),
                'callback'  => 'basic_auth_plugin_admin_enable_cb',
            ),
            '1' => array(
                'id'        => 'basic-auth-plugin-enable',
                'title'     => __('Enable for the entire site (only if wp-admin is enabled)', 'easy-basic-authentication'),
                'callback'  => 'basic_auth_plugin_enable_cb',
            ),
            '2' => array(
                'id'        => 'basic-auth-plugin-username',
                'title'     => __('Username', 'easy-basic-authentication'),
                'callback'  => 'basic_auth_plugin_username_cb',
            ),
            '3' => array(
                'id'        => 'basic-auth-plugin-password',
                'title'     => __('Password', 'easy-basic-authentication'),
                'callback'  => 'basic_auth_plugin_password_cb',
            ),
            '4' => array(
                'id'        => 'basic-auth-plugin-admin-log-enable',
                'title'     => __('Enable access logs', 'easy-basic-authentication'),
                'callback'  => 'basic_auth_plugin_admin_log_enable_cb',
            ),
            '5' => array(
                'id'        => 'basic-auth-plugin-alert-enable',
                'title'     => __('Enable email alert', 'easy-basic-authentication'),
                'callback'  => 'basic_auth_plugin_alert_enable_cb',
            ),
            '6' => array(
                'id'        => 'basic-auth-plugin-email-alert',
                'title'     => __('Email for alert', 'easy-basic-authentication'),
                'callback'  => 'basic_auth_plugin_alertemail_cb',
            ),
            '7' => array(
                'id'        => 'basic-auth-plugin-white-list',
                'title'     => __('Ip White list', 'easy-basic-authentication'),
                'callback'  => 'basic_auth_plugin_whitelist_cb',
            )
        );
    }
    
    public function basic_auth_plugin_settings_init() {
        register_setting( 'basic-auth-plugin-settings', 'basic_auth_plugin_admin_enable' );
        register_setting( 'basic-auth-plugin-settings', 'basic_auth_plugin_enable' );
        register_setting( 'basic-auth-plugin-settings', 'basic_auth_plugin_username' );
        register_setting( 'basic-auth-plugin-settings', 'basic_auth_plugin_admin_log_enable' );
    
        add_settings_section(
            'basic-auth-plugin-section',
            __('Configurations for Easy Basic Authentication', 'easy-basic-authentication'),
            array($this,'basic_auth_plugin_section_cb'),
            'basic-auth-plugin-settings'
        );
    
        foreach($this->form_field as $field) {
            add_settings_field(
                $field['id'],
                esc_html($field['title']),
                array($this,$field['callback']),
                'basic-auth-plugin-settings',
                'basic-auth-plugin-section'
            );
        }
        
    }

    public function basic_auth_plugin_section_cb() {
        echo esc_html__('Configure basic authentication', 'easy-basic-authentication');
    } 
        
    public function basic_auth_plugin_enable_cb() {
        $admin_enable = get_option( 'basic_auth_plugin_admin_enable' );
        $enable = get_option( 'basic_auth_plugin_enable' );
        ?>
        <input type="checkbox" name="basic_auth_plugin_enable" value="1" <?php checked( $enable, 1 ); ?><?php disabled( !$admin_enable ); ?>>
        <?php
    }

    public function basic_auth_plugin_admin_enable_cb() {
        $enable = get_option( 'basic_auth_plugin_admin_enable' );
        ?>
        <input type="checkbox" name="basic_auth_plugin_admin_enable" value="1" <?php checked( $enable, 1 ); ?>>
        <?php
    }
    
    public function basic_auth_plugin_username_cb() {
        $username = get_option( 'basic_auth_plugin_username' );
        $this->printInputText("text","basic_auth_plugin_username",esc_attr( $username ));
    }
    
    public function basic_auth_plugin_password_cb() {
        $password = get_option( 'basic_auth_plugin_password' )
                    ? __('Password entered', 'easy-basic-authentication')
                    : __('Enter the password', 'easy-basic-authentication');
        $this->printInputText("password","basic_auth_plugin_password",'',$password);
    }
    
    public function basic_auth_plugin_admin_log_enable_cb() {
        $enable = get_option( 'basic_auth_plugin_admin_log_enable' );
        ?>
        <input type="checkbox" name="basic_auth_plugin_admin_log_enable" value="1" <?php checked( $enable, 1 ); ?>>
        <?php
    }
    
    public function basic_auth_plugin_alert_enable_cb() {
        $enable = get_option( 'basic_auth_plugin_alert_enable' );
        ?>
        <input type="checkbox" name="basic_auth_plugin_alert_enable" value="1" <?php checked( $enable, 1 ); ?>>
        <?php
    }
    
    public function basic_auth_plugin_alertemail_cb() {
        $email_alert = get_option( 'basic_auth_plugin_alertemail' );
        $this->printInputText("text","basic_auth_plugin_alertemail",esc_attr( $email_alert ),__('Enter the email', 'easy-basic-authentication'));
    }
    
    public function basic_auth_plugin_whitelist_cb() {
        $white_list = get_option( 'basic_auth_plugin_whitelist' );
        $this->printInputText("text","basic_auth_plugin_whitelist",esc_attr( $white_list ),__('White list, separated by comma', 'easy-basic-authentication'));
    }

    public function printInputText($tipe, $name, $value='', $placeholder = '') {
        echo "<input type='" . esc_attr($tipe) . "' name='" . esc_attr($name) . "' value='" . esc_attr($value) . "' placeholder='" . esc_attr($placeholder) . "'>";
    }

    public function basic_auth_plugin_save_settings($param) {
        if ( isset( $param['eba_submit'] ) ) {
            $admin_enable = isset( $param['basic_auth_plugin_admin_enable'] ) ? 1 : 0;
            $enable = isset( $param['basic_auth_plugin_enable'] ) ? 1 : 0;
            $username = sanitize_text_field( $param['basic_auth_plugin_username'] );
            $password = sanitize_text_field( $param['basic_auth_plugin_password'] );
            $log_enable = isset( $param['basic_auth_plugin_admin_log_enable'] ) ? 1 : 0;
            $alert_enable = isset( $param['basic_auth_plugin_alert_enable'] ) ? 1 : 0;
            $alert_email = sanitize_text_field( $param['basic_auth_plugin_alertemail'] );
            $white_list = sanitize_text_field( $param['basic_auth_plugin_whitelist'] );

            if ( is_email( $alert_email ) || (!$alert_enable && $alert_email=='' ) ) {
                update_option( 'basic_auth_plugin_alertemail', $alert_email );
            } else {
                add_settings_error(
                    'basic_auth_plugin_alertemail',
                    'basic_auth_plugin_alertemail_error',
                    __('Invalid email address', 'easy-basic-authentication'),
                    'error'
                );
                return;
            }
    
            update_option( 'basic_auth_plugin_admin_enable', $admin_enable );
            update_option( 'basic_auth_plugin_enable', $enable );
            update_option( 'basic_auth_plugin_username', $username );
            update_option( 'basic_auth_plugin_admin_log_enable', $log_enable );
            update_option( 'basic_auth_plugin_alert_enable', $alert_enable );

            if( $this->validateIpList( $white_list ) || strlen($white_list) == 0 ) {
                update_option( 'basic_auth_plugin_whitelist', $white_list );
            } else {
                add_settings_error(
                    'basic_auth_plugin_whitelist',
                    'basic_auth_plugin_whitelist_error',
                    __('Invalid IP list format. Please enter valid IP addresses separated by commas.', 'easy-basic-authentication'),
                    'error'
                );
                return;
            }
               
            if ( ! empty( $password ) ) {
                $hashed_password = wp_hash_password( $password );
                update_option( 'basic_auth_plugin_password', $hashed_password );
            }
        }
    }
    
    public function basic_auth_plugin_settings_page() {
        include plugin_dir_path( __FILE__ ) . '../template/settings_page.php';
    } 

    public function validateIpList($white_list) {
        $ips = explode(',', $white_list);
        foreach ($ips as $ip) {
            $ip = trim($ip);
            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                return false;
            }
        }
        return true;
    }
}