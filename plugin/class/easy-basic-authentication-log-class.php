<?php 

class easy_basic_authentication_log_class {

    public function __construct()
    {
        
    }
 
    public function is_enabled() {
        return get_option( 'basic_auth_plugin_admin_log_enable' );
    }

    public function update_status($access) {
        $logs = get_option('basic_auth_failure_logs', array());

        $log_entry = array(
            'id' => uniqid(),
            'ip' => esc_attr($access['REMOTE_ADDR']),
            'data' => current_time('mysql'),
            'browser' => esc_attr($access['HTTP_USER_AGENT']),
            'request_uri' => esc_attr($access["REQUEST_URI"]),
            'viewed' => 0
        );

        $log_entry = apply_filters('basic_auth_single_log_entry', $log_entry);

        $logs[] = $log_entry;

        $logs = apply_filters('basic_auth_logs_entry', $logs);

        update_option('basic_auth_failure_logs', $logs);
    }

    public function getMenu() {
        $access_count = $this->basic_auth_count_not_viewed_log_failed_access();
            add_submenu_page(
                'basic-auth-plugin',
                __('Log Page', 'easy-basic-authentication'), 
                __('Log Page', 'easy-basic-authentication'). '<span class="update-plugins count-' . $access_count . '"><span class="plugin-count">' . $access_count . '</span></span>', 
                'manage_options',
                'basic-auth-login', 
                array($this, 'basic_auth_login_page')
            );
    }

    public function basic_auth_not_viewed_log_failed_access() {
        $return_not_viewed = array_filter($this->basic_auth_get_log_failed_access(), function ($entry) {
            return isset($entry['viewed']) && $entry['viewed'] === 0;
        });
        return $return_not_viewed;
    }

    public function basic_auth_count_not_viewed_log_failed_access() {
        return count($this->basic_auth_not_viewed_log_failed_access());
    }

    public function basic_auth_get_log_failed_access() {
        return get_option('basic_auth_failure_logs', array());
    }

    public function basic_auth_count_log_failed_access() {
        return count($this->basic_auth_get_log_failed_access());
    }
    
    public function basic_auth_login_page() {
        if(array_key_exists('clear_mode',$_POST)) {
            update_option('basic_auth_failure_logs', array());
        }
        $user_log_data = array_reverse($this->basic_auth_get_log_failed_access());
        include plugin_dir_path( __FILE__ ) . '../template/log_page.php';
        $this->update_view();
    }

    public function update_view() {
        $logs = get_option('basic_auth_failure_logs', array());
        foreach ($logs as &$entry) {
            $entry['viewed'] = 1;
        }
        update_option('basic_auth_failure_logs', $logs);
    }
}