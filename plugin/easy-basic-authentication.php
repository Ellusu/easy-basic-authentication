<?php
   /*
   Plugin Name: Easy Basic Authentication
   description: Enhance WordPress security with Easy Basic Authentication plugin.
   Version: 2.1
   Author: Matteo Enna
   Author URI: https://matteoenna.it/it/wordpress-work/
   Text Domain: easy-basic-authentication
   License: GPL2
   */

    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    require_once (dirname(__FILE__).'/class/easy-basic-authentication-class.php');

    function easy_basic_authentication_admin_notice() {
        $current_user = wp_get_current_user();
        $user_language = esc_html(get_user_meta($current_user->ID, 'locale', true));
    
        ?>
        <div class="notice notice-info is-dismissible">
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
        <?php
    }
    add_action('admin_notices', 'easy_basic_authentication_admin_notice');
     
    $scb = new easy_basic_authentication_class();
