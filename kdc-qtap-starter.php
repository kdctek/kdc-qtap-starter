<?php
/**
 * Plugin Name:       qTap Starter
 * Plugin URI:        https://github.com/kdctek/kdc-qtap-starter
 * Description:       A starter template for building qTap App child plugins. <strong>Requires qTap App.</strong>
 * Version:           1.4.0
 * Author:            KDC
 * Author URI:        https://kdc.in
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       kdc-qtap-starter
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
define( 'KDC_QTAP_STARTER_VERSION', '1.4.0' );
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

			// Register with qTap App dashboard (after init to avoid translation loading too early).
			add_action( 'init', array( $this, 'register_with_qtap' ) );

			// Integrate with parent plugin hooks.
			$this->init_parent_integration();
		} else {
			// Show admin notice if parent is missing.
			add_action( 'admin_notices', array( $this, 'dependency_notice' ) );
		}
	}

	/**
	 * Initialize integration with parent plugin (qTap App) hooks.
	 *
	 * @since 1.3.0
	 */
	private function init_parent_integration() {
		// Listen for accessibility mode changes.
		add_action( 'kdc_qtap_accessibility_mode_changed', array( $this, 'on_accessibility_changed' ), 10, 2 );

		// Add export data to parent's export.
		add_filter( 'kdc_qtap_export_data', array( $this, 'add_to_parent_export' ), 10, 2 );

		// Process import from parent's import.
		add_action( 'kdc_qtap_process_import', array( $this, 'process_parent_import' ), 10, 2 );

		// Add export checkbox to parent's Data Management.
		add_action( 'kdc_qtap_export_options', array( $this, 'render_parent_export_option' ) );

		// Hook into parent's remove data action.
		add_action( 'kdc_qtap_remove_data', array( $this, 'remove_data_on_parent_uninstall' ) );
	}

	/**
	 * Handle accessibility mode change from parent plugin.
	 *
	 * @since 1.3.0
	 *
	 * @param bool $enabled  New accessibility mode state.
	 * @param bool $previous Previous accessibility mode state.
	 */
	public function on_accessibility_changed( $enabled, $previous ) {
		/**
		 * Fires when parent plugin's accessibility mode changes.
		 *
		 * Child plugins can use this to react to accessibility mode changes.
		 *
		 * @since 1.3.0
		 * @param bool $enabled  Whether accessibility mode is now enabled.
		 * @param bool $previous Previous state.
		 */
		do_action( 'kdc_qtap_starter_accessibility_changed', $enabled, $previous );
	}

	/**
	 * Add this plugin's data to parent's export.
	 *
	 * @since 1.3.0
	 *
	 * @param array $data Export data array.
	 * @param array $post Posted form data.
	 * @return array Modified export data.
	 */
	public function add_to_parent_export( $data, $post ) {
		// Check if our checkbox was selected.
		if ( ! empty( $post['export_qtap_starter'] ) ) {
			$data['qtap_starter'] = array(
				'version'  => KDC_QTAP_STARTER_VERSION,
				'settings' => get_option( 'kdc_qtap_starter_settings', array() ),
			);

			/**
			 * Filter data added to parent's export.
			 *
			 * @since 1.3.0
			 * @param array $starter_data This plugin's export data.
			 */
			$data['qtap_starter'] = apply_filters( 'kdc_qtap_starter_parent_export_data', $data['qtap_starter'] );
		}
		return $data;
	}

	/**
	 * Process import from parent's Data Management.
	 *
	 * @since 1.3.0
	 *
	 * @param array $import_data Complete imported data array.
	 * @param int   $imported_count Number of items imported so far.
	 */
	public function process_parent_import( $import_data, $imported_count ) {
		if ( ! empty( $import_data['qtap_starter']['settings'] ) ) {
			update_option( 'kdc_qtap_starter_settings', $import_data['qtap_starter']['settings'] );

			/**
			 * Fires after settings are imported from parent.
			 *
			 * @since 1.3.0
			 * @param array $import_data The full imported data.
			 */
			do_action( 'kdc_qtap_starter_parent_import_complete', $import_data );
		}
	}

	/**
	 * Render export checkbox in parent's Data Management tab.
	 *
	 * @since 1.3.0
	 */
	public function render_parent_export_option() {
		?>
		<label style="display: block; margin-bottom: 8px;">
			<input type="checkbox" name="export_qtap_starter" value="1" checked />
			<?php esc_html_e( 'qTap Starter Settings', 'kdc-qtap-starter' ); ?>
		</label>
		<?php
	}

	/**
	 * Remove plugin data when parent triggers data removal.
	 *
	 * This respects the parent's "Remove data on uninstall" setting.
	 *
	 * @since 1.3.0
	 */
	public function remove_data_on_parent_uninstall() {
		// Delete this plugin's settings.
		delete_option( 'kdc_qtap_starter_settings' );
		delete_option( 'kdc_qtap_starter_version' );
		delete_transient( 'kdc_qtap_starter_cache' );

		/**
		 * Fires when parent triggers data removal.
		 *
		 * @since 1.3.0
		 */
		do_action( 'kdc_qtap_starter_parent_remove_data' );
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
	 * @since 1.0.0
	 */
	private function load_dependencies() {
		// Core classes.
		require_once KDC_QTAP_STARTER_PLUGIN_DIR . 'includes/class-kdc-qtap-starter-admin.php';
	}

	/**
	 * Initialize plugin components.
	 *
	 * @since 1.0.0
	 */
	private function init_components() {
		// Initialize admin.
		if ( is_admin() ) {
			KDC_qTap_Starter_Admin::get_instance();
		}

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
	 * Called on 'init' action to ensure translations are loaded.
	 *
	 * @since 1.0.0
	 * @since 1.3.1 Changed hook from kdc_qtap_loaded to init.
	 */
	public function register_with_qtap() {
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
	 * Check if accessibility mode is enabled (from parent plugin).
	 *
	 * @since  1.3.0
	 * @return bool
	 */
	public function is_accessibility_enabled() {
		if ( function_exists( 'kdc_qtap_is_accessibility_enabled' ) ) {
			return kdc_qtap_is_accessibility_enabled();
		}
		return false;
	}

	/**
	 * Check if data should be removed on uninstall (from parent plugin).
	 *
	 * @since  1.3.0
	 * @return bool
	 */
	public function should_remove_data() {
		// Check parent's setting first.
		if ( function_exists( 'kdc_qtap_should_remove_data' ) && kdc_qtap_should_remove_data() ) {
			return true;
		}

		// Fall back to local setting.
		$settings = get_option( 'kdc_qtap_starter_settings', array() );
		return isset( $settings['delete_data_on_uninstall'] ) && 'yes' === $settings['delete_data_on_uninstall'];
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

/**
 * Check if accessibility mode is enabled (wrapper function).
 *
 * @since  1.3.0
 * @return bool
 */
function kdc_qtap_starter_is_accessibility_enabled() {
	return kdc_qtap_starter()->is_accessibility_enabled();
}

// Initialize the plugin.
kdc_qtap_starter();
