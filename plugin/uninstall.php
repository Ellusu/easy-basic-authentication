<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Check if the user has selected removing all plugin data from the database after uninstalling the plugin - defaults to 0 (false)
$basic_auth_remove_data = get_option( 'basic_auth_plugin_remove_data_after_uninstall', '0' );

if ( $basic_auth_remove_data === '1' ) {
  global $wpdb;

  // Le opzioni del plugin non condividono tutte lo stesso prefisso: il log degli accessi
  // si chiama basic_auth_failure_logs e con il solo LIKE 'basic_auth_plugin_%' restava
  // nel database dopo la disinstallazione.
  // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
  $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'basic_auth_%'");
}