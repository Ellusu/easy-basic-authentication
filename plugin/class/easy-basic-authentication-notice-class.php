<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class easy_basic_authentication_notice_class {
    public function __construct() {
        add_action('admin_notices', array($this, 'display_notice'));
        add_action('wp_ajax_easy_basic_authentication_dismiss_notice', array($this, 'dismiss_notice'));
    }

    public function display_notice() {
        $current_user = wp_get_current_user();
        $user_language = esc_html(get_user_meta($current_user->ID, 'locale', true));
        
        $notice_dismissed = get_user_meta($current_user->ID, '_easy_basic_authentication_notice_dismissed', true);
        
        if ($notice_dismissed) {
            return;
        }

        ?>
        <div class="notice notice-info is-dismissible easy-basic-authentication-notice">
            <p>
                <?php 
                // Translators: 1: Link to profile, 2: User language, 3: Link to translation instructions
                printf(
                    wp_kses(
                        /* translators: 1: Link to profile, 2: User language, 3: Link to translation instructions */
                        __('Hi, I\'m <a href="%1$s">Matteo</a>, the developer of <b>Easy Basic Authentication</b>. If you are enjoying and finding this plugin useful, please consider helping us translate it into %2$s. Check if translations are available for your language and start contributing. <a href="%3$s">Click here to find out how you can help</a>.', 'easy-basic-authentication'),
                        array(
                            'a' => array(
                                'href' => array(),
                            ),
                            'b' => array(),
                        )
                    ),
                    esc_url('https://profiles.wordpress.org/matteoenna/'),
                    esc_html($user_language ? $user_language : __('your language', 'easy-basic-authentication')),
                    esc_url('https://make.wordpress.org/polyglots/handbook/translating/')
                ); 
                ?>
            </p>
        </div>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('.easy-basic-authentication-notice').on('click', '.notice-dismiss', function() {
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'easy_basic_authentication_dismiss_notice'
                        }
                    });
                });
            });
        </script>
        <?php
    }

    public function dismiss_notice() {
        $current_user = wp_get_current_user();
        update_user_meta($current_user->ID, '_easy_basic_authentication_notice_dismissed', true);
        wp_die();
    }
}