<?php
/**
 * qTap Shared Fallback Menu Functions
 *
 * These functions provide a shared fallback menu system when qTap App
 * is not installed but multiple child plugins are active.
 *
 * IMPORTANT: All functions MUST be wrapped in function_exists() checks
 * because multiple qTap apps may include this file.
 *
 * @package KDC_qTap_Starter
 * @since   1.0.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'kdc_qtap_parent_is_active' ) ) {
	/**
	 * Check if qTap App (parent) is active.
	 *
	 * @since  1.0.1
	 * @return bool
	 */
	function kdc_qtap_parent_is_active() {
		return class_exists( 'KDC_qTap_Admin' );
	}
}

if ( ! function_exists( 'kdc_qtap_get_menu_slug' ) ) {
	/**
	 * Get the qTap menu slug.
	 *
	 * Returns the parent menu slug, whether from qTap App or fallback.
	 *
	 * @since  1.0.1
	 * @return string Menu slug.
	 */
	function kdc_qtap_get_menu_slug() {
		if ( kdc_qtap_parent_is_active() ) {
			return KDC_qTap_Admin::get_menu_slug();
		}
		return 'kdc-qtap';
	}
}

if ( ! function_exists( 'kdc_qtap_get_menu_position' ) ) {
	/**
	 * Get the qTap menu position.
	 *
	 * @since  1.0.1
	 * @return int Menu position.
	 */
	function kdc_qtap_get_menu_position() {
		if ( kdc_qtap_parent_is_active() ) {
			return KDC_qTap_Admin::get_menu_position();
		}
		return 56;
	}
}

if ( ! function_exists( 'kdc_qtap_fallback_menu_exists' ) ) {
	/**
	 * Check if fallback menu has already been created.
	 *
	 * @since  1.0.1
	 * @return bool
	 */
	function kdc_qtap_fallback_menu_exists() {
		global $kdc_qtap_fallback_menu_created;
		return ! empty( $kdc_qtap_fallback_menu_created );
	}
}

if ( ! function_exists( 'kdc_qtap_set_fallback_menu_created' ) ) {
	/**
	 * Mark fallback menu as created.
	 *
	 * @since 1.0.1
	 */
	function kdc_qtap_set_fallback_menu_created() {
		global $kdc_qtap_fallback_menu_created;
		$kdc_qtap_fallback_menu_created = true;
	}
}

if ( ! function_exists( 'kdc_qtap_get_fallback_menu_icon' ) ) {
	/**
	 * Get the fallback menu icon (base64 SVG).
	 *
	 * @since  1.0.1
	 * @return string Base64 encoded SVG data URI.
	 */
	function kdc_qtap_get_fallback_menu_icon() {
		$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><g fill="currentColor"><rect x="4.1" y="2.7" width="4.2" height="2" rx="1" ry="1"/><path d="M10.1,5.5h-3.3c-.5,1.3-.7,3.9-.7,3.9,0,0-.5-3-.4-3.9h-3.6C1.3,5.5,0,6.1,0,8.2s1.3,2.4,2.4,2.4h1.8c-.1-.5-.3-1-.4-1.5h-1.2s-1,0-1-.9c0-.9.9-1,.9-1h1.6c.3,2.6,1.5,6,1.6,6.2,0,0,2,0,2,0,0,0-.6-4.8.3-6.3h1.6s1.3,0,1.3,1-.6.9-1.2.9h-1.5c0,.4,0,.9,0,1.5h1.9c1.3,0,2.1-1.1,2.1-2.3s-.9-2.7-2.1-2.7Z"/><path d="M13.4,8.2c0-1.1-.3-2-.8-2.7h-.8c.1.1.2.2.3.4.5.6.7,1.4.7,2.3s-.3,1.5-.7,2l.5.4c.6-.7.9-1.5.9-2.4Z"/><path d="M14.7,8.2c0-1-.2-1.9-.6-2.7h-.7c.5.8.7,1.7.7,2.7s-.4,2.1-1,2.8l.5.4c.8-.9,1.2-2.1,1.2-3.2Z"/><path d="M15.9,8.2c0-1-.2-1.9-.5-2.7h-.7c.4.8.5,1.7.5,2.7s-.5,2.7-1.3,3.6l.5.4c1-1.1,1.5-2.6,1.5-4.1Z"/></g></svg>';

		return 'data:image/svg+xml;base64,' . base64_encode( $svg );
	}
}

if ( ! function_exists( 'kdc_qtap_ensure_fallback_menu' ) ) {
	/**
	 * Create or attach to the qTap fallback menu.
	 *
	 * When qTap App is not active, the first child plugin to call this
	 * creates the parent menu. Subsequent calls just return the slug.
	 *
	 * @since  1.0.1
	 * @param  callable|null $dashboard_callback Optional callback for dashboard page.
	 * @return string                            The parent menu slug.
	 */
	function kdc_qtap_ensure_fallback_menu( $dashboard_callback = null ) {
		// If qTap App is active, use its menu.
		if ( kdc_qtap_parent_is_active() ) {
			return kdc_qtap_get_menu_slug();
		}

		// If fallback menu already created by another plugin, just return slug.
		if ( kdc_qtap_fallback_menu_exists() ) {
			return kdc_qtap_get_menu_slug();
		}

		// Create the fallback parent menu.
		$menu_slug = kdc_qtap_get_menu_slug();

		add_menu_page(
			__( 'qTap', 'kdc-qtap' ),
			__( 'qTap', 'kdc-qtap' ),
			'manage_options',
			$menu_slug,
			$dashboard_callback ? $dashboard_callback : 'kdc_qtap_render_fallback_dashboard',
			kdc_qtap_get_fallback_menu_icon(),
			kdc_qtap_get_menu_position()
		);

		// Add Dashboard submenu to replace the duplicate parent link.
		add_submenu_page(
			$menu_slug,
			__( 'qTap Dashboard', 'kdc-qtap' ),
			__( 'Dashboard', 'kdc-qtap' ),
			'manage_options',
			$menu_slug,
			$dashboard_callback ? $dashboard_callback : 'kdc_qtap_render_fallback_dashboard'
		);

		// Mark as created so other plugins don't duplicate.
		kdc_qtap_set_fallback_menu_created();

		// Add inline styles for menu icon.
		add_action( 'admin_head', 'kdc_qtap_fallback_menu_styles' );

		return $menu_slug;
	}
}

if ( ! function_exists( 'kdc_qtap_fallback_menu_styles' ) ) {
	/**
	 * Add fallback menu icon styles.
	 *
	 * @since 1.0.1
	 */
	function kdc_qtap_fallback_menu_styles() {
		?>
		<style>
			#adminmenu .toplevel_page_kdc-qtap .wp-menu-image svg {
				fill: currentColor;
				width: 20px;
				height: 20px;
			}
			#adminmenu .toplevel_page_kdc-qtap .wp-menu-image svg * {
				fill: inherit;
			}
		</style>
		<?php
	}
}

if ( ! function_exists( 'kdc_qtap_render_fallback_dashboard' ) ) {
	/**
	 * Render the fallback dashboard page.
	 *
	 * Shows a simple dashboard when qTap App is not installed.
	 *
	 * @since 1.0.1
	 */
	function kdc_qtap_render_fallback_dashboard() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'kdc-qtap' ) );
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html__( 'qTap Dashboard', 'kdc-qtap' ); ?></h1>

			<div class="notice notice-info" style="margin: 20px 0;">
				<p>
					<strong><?php echo esc_html__( 'qTap App Not Installed', 'kdc-qtap' ); ?></strong><br>
					<?php
					printf(
						/* translators: %s: qTap App URL */
						esc_html__( 'Install the %s plugin for a full dashboard experience with all your qTap apps in one place.', 'kdc-qtap' ),
						'<a href="https://qtap.app" target="_blank">qTap App</a>'
					);
					?>
				</p>
			</div>

			<div class="card" style="max-width: 600px;">
				<h2><?php echo esc_html__( 'Your Active qTap Apps', 'kdc-qtap' ); ?></h2>
				<p><?php echo esc_html__( 'Use the submenu items to access your installed qTap apps.', 'kdc-qtap' ); ?></p>
			</div>
		</div>
		<?php
	}
}
