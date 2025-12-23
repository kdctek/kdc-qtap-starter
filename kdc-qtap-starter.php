<?php
/**
 * Plugin Name:       qTap Starter
 * Plugin URI:        https://github.com/kdctek/kdc-qtap-starter
 * Description:       A starter template for building qTap App child plugins. <strong>Requires qTap App.</strong>
 * Version:           1.2.0
 * Author:            KDC
 * Author URI:        https://kdc.in
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       kdc-qtap-starter
 * Domain Path:       /languages
 * Requires at least: 5.8
 * Tested up to:      6.9
 * Requires PHP:      7.4
 * Requires Plugins:  kdc-qtap
 *
 * WC requires at least: 5.0
 * WC tested up to:      8.4
 *
 * @package KDC_qTap_Starter
 * @since   1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Prevent loading if already loaded.
if ( defined( 'KDC_QTAP_STARTER_VERSION' ) ) {
	return;
}

/**
 * Plugin constants.
 */
define( 'KDC_QTAP_STARTER_VERSION', '1.2.0' );
define( 'KDC_QTAP_STARTER_PLUGIN_FILE', __FILE__ );
define( 'KDC_QTAP_STARTER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'KDC_QTAP_STARTER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'KDC_QTAP_STARTER_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Required parent plugin slug.
 */
define( 'KDC_QTAP_STARTER_REQUIRED_PARENT', 'kdc-qtap/kdc-qtap.php' );

/**
 * Main qTap Starter Class.
 *
 * @since 1.0.0
 */
final class KDC_qTap_Starter {

	/**
	 * Single instance of the class.
	 *
	 * @since 1.0.0
	 * @var   KDC_qTap_Starter|null
	 */
	private static $instance = null;

	/**
	 * Whether the required parent plugin is active.
	 *
	 * @since 1.2.0
	 * @var   bool
	 */
	private $parent_active = false;

	/**
	 * Get the singleton instance.
	 *
	 * @since  1.0.0
	 * @return KDC_qTap_Starter
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		$this->init_hooks();
	}

	/**
	 * Prevent cloning.
	 *
	 * @since 1.0.0
	 */
	private function __clone() {}

	/**
	 * Prevent unserializing.
	 *
	 * @since 1.0.0
	 * @throws Exception If trying to unserialize.
	 */
	public function __wakeup() {
		throw new Exception( 'Cannot unserialize singleton' );
	}

	/**
	 * Initialize hooks.
	 *
	 * @since 1.0.0
	 */
	private function init_hooks() {
		// Load translations first (WordPress 6.7+ requirement).
		add_action( 'init', array( $this, 'load_textdomain' ), -1 );

		// Check dependency and initialize.
		add_action( 'plugins_loaded', array( $this, 'check_dependency' ), 5 );

		// Declare WooCommerce compatibility.
		add_action( 'before_woocommerce_init', array( $this, 'declare_wc_compatibility' ) );

		// Activation and deactivation hooks.
		register_activation_hook( KDC_QTAP_STARTER_PLUGIN_FILE, array( $this, 'activate' ) );
		register_deactivation_hook( KDC_QTAP_STARTER_PLUGIN_FILE, array( $this, 'deactivate' ) );
	}

	/**
	 * Check if qTap App (parent plugin) is active.
	 *
	 * @since 1.2.0
	 */
	public function check_dependency() {
		// Check if qTap App is active.
		if ( function_exists( 'kdc_qtap_is_active' ) && kdc_qtap_is_active() ) {
			$this->parent_active = true;
			$this->load_dependencies();
			$this->init_components();

			// Register with qTap App dashboard.
			add_action( 'kdc_qtap_loaded', array( $this, 'register_with_qtap' ) );
		} else {
			// Show admin notice if parent is missing.
			add_action( 'admin_notices', array( $this, 'dependency_notice' ) );
		}
	}

	/**
	 * Display admin notice when qTap App is not active.
	 *
	 * @since 1.2.0
	 */
	public function dependency_notice() {
		// Only show to users who can install plugins.
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		// Check if qTap App is installed but not active.
		$plugins        = get_plugins();
		$qtap_installed = isset( $plugins[ KDC_QTAP_STARTER_REQUIRED_PARENT ] );

		if ( $qtap_installed ) {
			// Plugin installed but not active - show activate link.
			$activate_url = wp_nonce_url(
				admin_url( 'plugins.php?action=activate&plugin=' . KDC_QTAP_STARTER_REQUIRED_PARENT ),
				'activate-plugin_' . KDC_QTAP_STARTER_REQUIRED_PARENT
			);
			$message = sprintf(
				/* translators: 1: Plugin name, 2: Required plugin name, 3: Activate link */
				__( '%1$s requires %2$s to be activated. %3$s', 'kdc-qtap-starter' ),
				'<strong>qTap Starter</strong>',
				'<strong>qTap App</strong>',
				'<a href="' . esc_url( $activate_url ) . '">' . __( 'Activate now', 'kdc-qtap-starter' ) . '</a>'
			);
		} else {
			// Plugin not installed - show install/download links.
			$message = sprintf(
				/* translators: 1: Plugin name, 2: Required plugin name, 3: Download link */
				__( '%1$s requires %2$s to be installed and activated. %3$s', 'kdc-qtap-starter' ),
				'<strong>qTap Starter</strong>',
				'<strong>qTap App</strong>',
				'<a href="https://qtap.app" target="_blank">' . __( 'Download from qtap.app', 'kdc-qtap-starter' ) . '</a>'
			);
		}

		printf(
			'<div class="notice notice-error"><p>%s</p></div>',
			wp_kses(
				$message,
				array(
					'strong' => array(),
					'a'      => array(
						'href'   => array(),
						'target' => array(),
					),
				)
			)
		);
	}

	/**
	 * Load required files.
	 *
	 * Only called when qTap App is active.
	 *
	 * @since 1.0.0
	 */
	private function load_dependencies() {
		// Core classes.
		require_once KDC_QTAP_STARTER_PLUGIN_DIR . 'includes/class-kdc-qtap-starter-admin.php';

		// Add additional includes here.
		// require_once KDC_QTAP_STARTER_PLUGIN_DIR . 'includes/class-kdc-qtap-starter-frontend.php';
	}

	/**
	 * Load plugin translations.
	 *
	 * @since 1.0.0
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'kdc-qtap-starter',
			false,
			dirname( KDC_QTAP_STARTER_PLUGIN_BASENAME ) . '/languages'
		);
	}

	/**
	 * Initialize plugin components.
	 *
	 * Only called when qTap App is active.
	 *
	 * @since 1.0.0
	 */
	private function init_components() {
		// Initialize admin.
		if ( is_admin() ) {
			KDC_qTap_Starter_Admin::get_instance();
		}

		// Initialize frontend components here.
		// if ( ! is_admin() ) {
		//     KDC_qTap_Starter_Frontend::get_instance();
		// }

		/**
		 * Fires after qTap Starter components are initialized.
		 *
		 * @since 1.2.0
		 */
		do_action( 'kdc_qtap_starter_loaded' );
	}

	/**
	 * Register this app with qTap App dashboard.
	 *
	 * @since 1.0.0
	 * @param KDC_qTap_Dashboard $qtap The qTap App instance.
	 */
	public function register_with_qtap( $qtap ) {
		if ( function_exists( 'kdc_qtap_register_plugin' ) ) {
			kdc_qtap_register_plugin(
				array(
					'id'           => 'starter',
					'name'         => __( 'Starter', 'kdc-qtap-starter' ),
					'description'  => __( 'A starter template for qTap apps.', 'kdc-qtap-starter' ),
					'icon'         => '🚀',
					'settings_url' => admin_url( 'admin.php?page=kdc-qtap-starter' ),
					'version'      => KDC_QTAP_STARTER_VERSION,
					'is_active'    => true,
					'priority'     => 50,
				)
			);
		}
	}

	/**
	 * Check if qTap App (parent) is active.
	 *
	 * @since  1.2.0
	 * @return bool
	 */
	public function is_parent_active() {
		return $this->parent_active;
	}

	/**
	 * Declare WooCommerce HPOS compatibility.
	 *
	 * @since 1.0.0
	 */
	public function declare_wc_compatibility() {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
				'custom_order_tables',
				KDC_QTAP_STARTER_PLUGIN_FILE,
				true
			);
		}
	}

	/**
	 * Plugin activation.
	 *
	 * @since 1.0.0
	 */
	public function activate() {
		// Set default options.
		$defaults = array(
			'version'        => KDC_QTAP_STARTER_VERSION,
			'example_option' => 'default_value',
		);

		// Only set defaults if options don't exist.
		if ( false === get_option( 'kdc_qtap_starter_settings' ) ) {
			add_option( 'kdc_qtap_starter_settings', $defaults );
		}

		/**
		 * Fires when the plugin is activated.
		 *
		 * @since 1.0.0
		 */
		do_action( 'kdc_qtap_starter_activated' );
	}

	/**
	 * Plugin deactivation.
	 *
	 * @since 1.0.0
	 */
	public function deactivate() {
		/**
		 * Fires when the plugin is deactivated.
		 *
		 * @since 1.0.0
		 */
		do_action( 'kdc_qtap_starter_deactivated' );
	}

	/**
	 * Get plugin settings.
	 *
	 * @since  1.0.0
	 * @param  string $key     Optional. Specific setting key to retrieve.
	 * @param  mixed  $default Optional. Default value if key doesn't exist.
	 * @return mixed           Setting value or all settings.
	 */
	public function get_settings( $key = '', $default = null ) {
		$settings = get_option( 'kdc_qtap_starter_settings', array() );

		if ( empty( $key ) ) {
			return $settings;
		}

		return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
	}

	/**
	 * Update plugin settings.
	 *
	 * @since  1.0.0
	 * @param  string|array $key   Setting key or array of settings.
	 * @param  mixed        $value Setting value (if $key is string).
	 * @return bool                Whether the settings were updated.
	 */
	public function update_settings( $key, $value = null ) {
		$settings = get_option( 'kdc_qtap_starter_settings', array() );

		if ( is_array( $key ) ) {
			$settings = array_merge( $settings, $key );
		} else {
			$settings[ $key ] = $value;
		}

		return update_option( 'kdc_qtap_starter_settings', $settings );
	}
}

/**
 * Get qTap Starter instance.
 *
 * @since  1.0.0
 * @return KDC_qTap_Starter
 */
function kdc_qtap_starter() {
	return KDC_qTap_Starter::get_instance();
}

/**
 * Check if qTap Starter is active.
 *
 * @since  1.0.0
 * @return bool Always true when this plugin is loaded.
 */
function kdc_qtap_starter_is_active() {
	return true;
}

// Initialize the plugin.
kdc_qtap_starter();
