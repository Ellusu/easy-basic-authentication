<?php
   /*
   Plugin Name: Easy Basic Authentication
   description: Enhance WordPress security with Easy Basic Authentication plugin.
   Version: 3.7.0
   Author: Matteo Enna
   Author URI: https://matteoenna.it/it/wordpress-work/
   Text Domain: easy-basic-authentication
   License: GPL2
   */

    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    require_once (dirname(__FILE__).'/class/easy-basic-authentication-class.php');
     
    add_action('plugins_loaded', function () {
        new easy_basic_authentication_class();
    });
