<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Check if the user has selected removing all plugin data from the database after uninstalling the plugin - defaults to 0 (false)
$remove_data = get_option( 'basic_auth_plugin_remove_data_after_uninstall', '0' );

if ( $remove_data === '1' ) {
  global $wpdb;

  $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'basic_auth_plugin_%'");
}