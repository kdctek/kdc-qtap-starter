<?php
/**
 * Uninstall qTap Starter
 *
 * This file runs when the plugin is deleted (not just deactivated).
 * Data is only deleted if the user has enabled "Delete data on uninstall" option.
 *
 * @package KDC_qTap_Starter
 * @since   1.0.0
 */

// Exit if not called by WordPress.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Check if user wants to delete data on uninstall.
 *
 * By default, we preserve user data. Data is only deleted if the user
 * explicitly enabled the "Delete data on uninstall" option.
 */
$settings = get_option( 'kdc_qtap_starter_settings', array() );

// Only delete data if explicitly requested by user.
if ( ! isset( $settings['delete_data_on_uninstall'] ) || 'yes' !== $settings['delete_data_on_uninstall'] ) {
	// User wants to keep data - exit without deleting anything.
	return;
}

/**
 * Fires before plugin data is deleted.
 *
 * @since 1.0.4
 */
do_action( 'kdc_qtap_starter_before_uninstall' );

/**
 * Delete plugin options.
 */
delete_option( 'kdc_qtap_starter_settings' );
delete_option( 'kdc_qtap_starter_version' );

/**
 * Delete transients.
 */
delete_transient( 'kdc_qtap_starter_cache' );

/**
 * Delete user meta (for all users).
 * Uncomment if your plugin stores user meta.
 */
// delete_metadata( 'user', 0, 'kdc_qtap_starter_preferences', '', true );

/**
 * Drop custom database tables.
 * Uncomment and modify if your plugin creates custom tables.
 */
// global $wpdb;
// $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}kdc_qtap_starter_data" );

/**
 * Clear scheduled events.
 * Uncomment if your plugin schedules cron events.
 */
// wp_clear_scheduled_hook( 'kdc_qtap_starter_daily_event' );

/**
 * Fires after plugin data is deleted.
 *
 * @since 1.0.4
 */
do_action( 'kdc_qtap_starter_uninstall' );
