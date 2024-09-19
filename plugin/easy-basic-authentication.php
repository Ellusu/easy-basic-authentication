<?php
   /*
   Plugin Name: Easy Basic Authentication
   description: Enhance WordPress security with Easy Basic Authentication plugin.
   Version: 2.5.2
   Author: Matteo Enna
   Author URI: https://matteoenna.it/it/wordpress-work/
   Text Domain: easy-basic-authentication
   License: GPL2
   */

    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    require_once (dirname(__FILE__).'/class/easy-basic-authentication-class.php');
     
    $scb = new easy_basic_authentication_class();
