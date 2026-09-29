<?php
   /*
   Plugin Name: Easy Basic Authentication
   description: Enhance WordPress security with Easy Basic Authentication plugin.
   Version: 4.2.0
   Author: Matteo Enna
   Author URI: https://matteoenna.it/it/wordpress-work/
   Text Domain: easy-basic-authentication
   License: GPL2
   */

    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    define( 'EASY_BASIC_AUTHENTICATION_VERSION', '4.2.0' );

    require_once (dirname(__FILE__).'/class/easy-basic-authentication-class.php');
     
    add_action('plugins_loaded', function () {
        new easy_basic_authentication_class();
    });
