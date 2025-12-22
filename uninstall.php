<?php
/**
 * Uninstall qTap Starter
 *
 * This file runs when the plugin is deleted (not just deactivated).
 * It cleans up any data the plugin may have stored.
 *
 * @package KDC_qTap_Starter
 * @since   1.0.0
 */

// Exit if not called by WordPress.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Clean up plugin data.
 *
 * By default, we preserve user data. Uncomment sections below
 * to delete data on uninstall.
 */

// Delete plugin options.
delete_option( 'kdc_qtap_starter_settings' );
delete_option( 'kdc_qtap_starter_version' );

// Delete transients.
delete_transient( 'kdc_qtap_starter_cache' );

// Delete user meta (for all users).
// delete_metadata( 'user', 0, 'kdc_qtap_starter_preferences', '', true );

// Drop custom database tables.
// global $wpdb;
// $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}kdc_qtap_starter_data" );

// Clear scheduled events.
// wp_clear_scheduled_hook( 'kdc_qtap_starter_daily_event' );

/**
 * Fires when qTap Starter is uninstalled.
 *
 * @since 1.0.0
 */
do_action( 'kdc_qtap_starter_uninstall' );
