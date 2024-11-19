<?php 

class easy_basic_authentication_emailalert_class {

    public function __construct()
    {
        
    }
 
    public function is_enabled() {
        return get_option( 'basic_auth_plugin_alert_enable' )&&filter_var(get_option( 'basic_auth_plugin_alertemail' ), FILTER_VALIDATE_EMAIL);
    }

    public function sendAlert($access) {
        $to = get_option('basic_auth_plugin_alertemail');
        $subject = __('Access Attempt', 'easy-basic-authentication'); 
        
        $message = '<html><body>';
        $message .= '<p>' . __('Hi', 'easy-basic-authentication') . ',</p>';
        $message .= '<p>' . __('New access attempt detected:', 'easy-basic-authentication') . '</p>';
        $message .= '<ul style="list-style-type: none; padding-left: 0;">';
        $message .= '<li><strong>' . __('IP:', 'easy-basic-authentication') . '</strong> ' . esc_attr($access['REMOTE_ADDR']) . '</li>';
        $message .= '<li><strong>' . __('Browser:', 'easy-basic-authentication') . '</strong> ' . esc_attr($access['HTTP_USER_AGENT']) . '</li>';
        $message .= '<li><strong>' . __('Request URI:', 'easy-basic-authentication') . '</strong> ' . esc_attr($access['REQUEST_URI']) . '</li>';
        $message .= '</ul>';
        $message .= '<p>' . __('All text of the call:', 'easy-basic-authentication') . '</p>';
        $message .= '<pre style="font-size: 0.8em; background-color: #f8f8f8; padding: 10px;">' . esc_html(wp_json_encode($access, JSON_PRETTY_PRINT)) . '</pre>';
        $message .= '<p>' . __('You are receiving this email because you have installed Easy Basic Authentication on your website and enabled the email alert option.', 'easy-basic-authentication') . '</p>';
        $message .= '</body></html>';
    
        $site_email = get_option('admin_email');
    
        $headers = array(
            'Content-Type: text/html; charset=UTF-8', 
            'From: ' . $site_email, 
        );
    
        // Invio dell'email
        wp_mail($to, $subject, $message, $headers);
    }
    
 
}