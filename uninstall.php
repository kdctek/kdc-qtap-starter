<?php
/**
 * Uninstall qTap Starter
 *
 * This file runs when the plugin is deleted (not just deactivated).
 * Data is only deleted if:
 * 1. The user has enabled "Delete data on uninstall" option in this plugin, OR
 * 2. The parent qTap App has "Remove data on uninstall" enabled
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
 * We check both:
 * 1. This plugin's own setting
 * 2. Parent plugin's (qTap App) global setting
 *
 * Data is preserved by default unless explicitly requested.
 */
$kdc_qtap_starter_settings      = get_option( 'kdc_qtap_starter_settings', array() );
$kdc_qtap_starter_local_delete  = isset( $kdc_qtap_starter_settings['delete_data_on_uninstall'] ) && 'yes' === $kdc_qtap_starter_settings['delete_data_on_uninstall'];
$kdc_qtap_starter_parent_delete = false;

// Check parent plugin's setting if it exists.
$kdc_qtap_starter_parent_settings = get_option( 'kdc_qtap_settings', array() );
if ( ! empty( $kdc_qtap_starter_parent_settings['remove_data_uninstall'] ) ) {
	$kdc_qtap_starter_parent_delete = true;
}

// Only delete data if explicitly requested by user (local or parent setting).
if ( ! $kdc_qtap_starter_local_delete && ! $kdc_qtap_starter_parent_delete ) {
	// User wants to keep data - exit without deleting anything.
	return;
}

/**
 * Fires before plugin data is deleted.
 *
 * @since 1.0.4
 * @since 1.3.0 Added $source parameter.
 *
 * @param string $source Source of deletion trigger: 'local' or 'parent'.
 */
do_action( 'kdc_qtap_starter_before_uninstall', $kdc_qtap_starter_local_delete ? 'local' : 'parent' );

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
 * @since 1.3.0 Added $source parameter.
 *
 * @param string $source Source of deletion trigger: 'local' or 'parent'.
 */
do_action( 'kdc_qtap_starter_uninstall', $kdc_qtap_starter_local_delete ? 'local' : 'parent' );
