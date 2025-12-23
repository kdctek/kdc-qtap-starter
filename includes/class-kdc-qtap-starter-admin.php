<?php
/**
 * qTap Starter Admin Class
 *
 * Handles admin menu, settings page, import/export, and data management.
 *
 * @package KDC_qTap_Starter
 * @since   1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin Class.
 *
 * @since 1.0.0
 */
class KDC_qTap_Starter_Admin {

	/**
	 * Single instance of the class.
	 *
	 * @since 1.0.0
	 * @var   KDC_qTap_Starter_Admin|null
	 */
	private static $instance = null;

	/**
	 * Settings page slug.
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	const PAGE_SLUG = 'kdc-qtap-starter';

	/**
	 * Settings option name.
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	const OPTION_NAME = 'kdc_qtap_starter_settings';

	/**
	 * Nonce action for settings form.
	 *
	 * @since 1.0.0
	 * @var   string
	 */
	const NONCE_ACTION = 'kdc_qtap_starter_save_settings';

	/**
	 * Available tabs.
	 *
	 * @since 1.0.4
	 * @var   array
	 */
	private $tabs = null;

	/**
	 * Get the singleton instance.
	 *
	 * @since  1.0.0
	 * @return KDC_qTap_Starter_Admin
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
	 * Get tabs (lazy initialization to avoid early translation loading).
	 *
	 * @since 1.0.8
	 * @return array
	 */
	private function get_tabs() {
		if ( null === $this->tabs ) {
			$this->tabs = array(
				'general'       => __( 'General', 'kdc-qtap-starter' ),
				'import-export' => __( 'Import / Export', 'kdc-qtap-starter' ),
				'data'          => __( 'Data Management', 'kdc-qtap-starter' ),
			);

			/**
			 * Filter the available tabs.
			 *
			 * @since 1.0.4
			 * @param array $tabs Array of tab slug => label.
			 */
			$this->tabs = apply_filters( 'kdc_qtap_starter_admin_tabs', $this->tabs );
		}
		return $this->tabs;
	}

	/**
	 * Initialize hooks.
	 *
	 * @since 1.0.0
	 */
	private function init_hooks() {
		// Use qTap App's admin menu hook (requires qTap App).
		add_action( 'kdc_qtap_admin_menu', array( $this, 'add_admin_menu' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'admin_init', array( $this, 'handle_settings_save' ) );
		add_action( 'admin_init', array( $this, 'handle_export' ) );
		add_action( 'admin_init', array( $this, 'handle_import' ) );
		add_filter( 'plugin_action_links_' . KDC_QTAP_STARTER_PLUGIN_BASENAME, array( $this, 'add_plugin_action_links' ) );
	}

	/**
	 * Add admin menu as submenu of qTap App.
	 *
	 * @since 1.0.0
	 * @since 1.2.0 Now receives parent_slug and capability from qTap App hook.
	 *
	 * @param string $parent_slug The parent menu slug (kdc-qtap).
	 * @param string $capability  The required capability.
	 */
	public function add_admin_menu( $parent_slug, $capability ) {
		add_submenu_page(
			$parent_slug,
			__( 'Starter Settings', 'kdc-qtap-starter' ),
			__( 'Starter', 'kdc-qtap-starter' ),
			$capability,
			self::PAGE_SLUG,
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @since 1.0.0
	 * @param string $hook_suffix The current admin page.
	 */
	public function enqueue_admin_assets( $hook_suffix ) {
		if ( ! $this->is_plugin_page( $hook_suffix ) ) {
			return;
		}

		wp_enqueue_style(
			'kdc-qtap-starter-admin',
			KDC_QTAP_STARTER_PLUGIN_URL . 'assets/css/admin.css',
			array(),
			KDC_QTAP_STARTER_VERSION
		);

		wp_enqueue_script(
			'kdc-qtap-starter-admin',
			KDC_QTAP_STARTER_PLUGIN_URL . 'assets/js/admin.js',
			array( 'jquery' ),
			KDC_QTAP_STARTER_VERSION,
			true
		);

		wp_localize_script(
			'kdc-qtap-starter-admin',
			'kdcQtapStarterAdmin',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'kdc_qtap_starter_ajax' ),
				'i18n'    => array(
					'saving'        => __( 'Saving...', 'kdc-qtap-starter' ),
					'saved'         => __( 'Settings saved!', 'kdc-qtap-starter' ),
					'error'         => __( 'An error occurred.', 'kdc-qtap-starter' ),
					'confirmReset'  => __( 'Are you sure you want to reset all settings?', 'kdc-qtap-starter' ),
					'confirmDelete' => __( 'WARNING: This will permanently delete ALL plugin data when uninstalled. This cannot be undone. Continue?', 'kdc-qtap-starter' ),
					'exportFirst'   => __( 'We recommend downloading a backup first. Continue anyway?', 'kdc-qtap-starter' ),
					'importConfirm' => __( 'This will replace all current settings. Continue?', 'kdc-qtap-starter' ),
					'invalidFile'   => __( 'Please select a valid JSON file.', 'kdc-qtap-starter' ),
					'copied'        => __( 'Copied to clipboard!', 'kdc-qtap-starter' ),
				),
			)
		);

		do_action( 'kdc_qtap_starter_admin_enqueue_scripts', $hook_suffix );
	}

	/**
	 * Check if current page is our plugin page.
	 *
	 * @since  1.0.0
	 * @param  string $hook_suffix The current admin page hook.
	 * @return bool
	 */
	private function is_plugin_page( $hook_suffix ) {
		$screen = get_current_screen();
		if ( strpos( $hook_suffix, self::PAGE_SLUG ) !== false ) {
			return true;
		}
		if ( $screen && strpos( $screen->id, self::PAGE_SLUG ) !== false ) {
			return true;
		}
		return false;
	}

	/**
	 * Add plugin action links.
	 *
	 * @since  1.0.0
	 * @param  array $links Existing links.
	 * @return array
	 */
	public function add_plugin_action_links( $links ) {
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			esc_url( admin_url( 'admin.php?page=' . self::PAGE_SLUG ) ),
			esc_html__( 'Settings', 'kdc-qtap-starter' )
		);
		array_unshift( $links, $settings_link );
		return $links;
	}

	/**
	 * Get current tab.
	 *
	 * @since  1.0.4
	 * @return string
	 */
	private function get_current_tab() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'general';
		return array_key_exists( $tab, $this->get_tabs() ) ? $tab : 'general';
	}

	/**
	 * Handle settings form submission.
	 *
	 * @since 1.0.0
	 */
	public function handle_settings_save() {
		if ( ! isset( $_POST['kdc_qtap_starter_save'] ) ) {
			return;
		}

		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), self::NONCE_ACTION ) ) {
			wp_die( esc_html__( 'Security check failed.', 'kdc-qtap-starter' ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Permission denied.', 'kdc-qtap-starter' ) );
		}

		$settings = get_option( self::OPTION_NAME, array() );
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$posted = isset( $_POST['kdc_qtap_starter'] ) ? wp_unslash( $_POST['kdc_qtap_starter'] ) : array();
		$tab    = isset( $_POST['kdc_qtap_starter_tab'] ) ? sanitize_key( $_POST['kdc_qtap_starter_tab'] ) : 'general';

		if ( 'general' === $tab ) {
			if ( isset( $posted['example_text'] ) ) {
				$settings['example_text'] = sanitize_text_field( $posted['example_text'] );
			}
			if ( isset( $posted['example_textarea'] ) ) {
				$settings['example_textarea'] = sanitize_textarea_field( $posted['example_textarea'] );
			}
			$settings['example_checkbox'] = isset( $posted['example_checkbox'] ) ? 'yes' : 'no';
			if ( isset( $posted['example_select'] ) ) {
				$allowed = array( 'option1', 'option2', 'option3' );
				$settings['example_select'] = in_array( $posted['example_select'], $allowed, true ) ? $posted['example_select'] : 'option1';
			}
			if ( isset( $posted['example_number'] ) ) {
				$settings['example_number'] = absint( $posted['example_number'] );
			}
		}

		if ( 'data' === $tab ) {
			$settings['delete_data_on_uninstall'] = isset( $posted['delete_data_on_uninstall'] ) ? 'yes' : 'no';
		}

		$settings = apply_filters( 'kdc_qtap_starter_save_settings', $settings, $posted, $tab );
		update_option( self::OPTION_NAME, $settings );
		do_action( 'kdc_qtap_starter_settings_saved', $settings, $tab );

		add_settings_error( self::PAGE_SLUG, 'settings_saved', __( 'Settings saved.', 'kdc-qtap-starter' ), 'success' );
	}

	/**
	 * Handle export request.
	 *
	 * @since 1.0.4
	 */
	public function handle_export() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_GET['kdc_qtap_starter_export'] ) ) {
			return;
		}

		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'kdc_qtap_starter_export' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'kdc-qtap-starter' ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Permission denied.', 'kdc-qtap-starter' ) );
		}

		$export_data = $this->get_export_data();
		$filename    = 'kdc-qtap-starter-backup-' . gmdate( 'Y-m-d-His' ) . '.json';

		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=' . $filename );
		header( 'Cache-Control: no-cache, no-store, must-revalidate' );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );

		echo wp_json_encode( $export_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
		exit;
	}

	/**
	 * Get all data for export.
	 *
	 * @since  1.0.4
	 * @return array
	 */
	public function get_export_data() {
		$export_data = array(
			'plugin'      => 'kdc-qtap-starter',
			'version'     => KDC_QTAP_STARTER_VERSION,
			'exported_at' => gmdate( 'Y-m-d H:i:s' ),
			'site_url'    => get_site_url(),
			'data'        => array(
				'settings' => get_option( self::OPTION_NAME, array() ),
			),
		);

		return apply_filters( 'kdc_qtap_starter_export_data', $export_data );
	}

	/**
	 * Handle import request.
	 *
	 * @since 1.0.4
	 */
	public function handle_import() {
		if ( ! isset( $_POST['kdc_qtap_starter_import'] ) ) {
			return;
		}

		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'kdc_qtap_starter_import' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'kdc-qtap-starter' ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Permission denied.', 'kdc-qtap-starter' ) );
		}

		if ( ! isset( $_FILES['import_file'] ) || empty( $_FILES['import_file']['tmp_name'] ) ) {
			add_settings_error( self::PAGE_SLUG, 'import_error', __( 'Please select a file.', 'kdc-qtap-starter' ), 'error' );
			return;
		}

		$file_info = wp_check_filetype( sanitize_file_name( $_FILES['import_file']['name'] ) );
		if ( 'json' !== $file_info['ext'] ) {
			add_settings_error( self::PAGE_SLUG, 'import_error', __( 'Invalid file type. Use JSON.', 'kdc-qtap-starter' ), 'error' );
			return;
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$contents = file_get_contents( $_FILES['import_file']['tmp_name'] );
		if ( false === $contents ) {
			add_settings_error( self::PAGE_SLUG, 'import_error', __( 'Failed to read file.', 'kdc-qtap-starter' ), 'error' );
			return;
		}

		$import_data = json_decode( $contents, true );
		if ( null === $import_data || json_last_error() !== JSON_ERROR_NONE ) {
			add_settings_error( self::PAGE_SLUG, 'import_error', __( 'Invalid JSON file.', 'kdc-qtap-starter' ), 'error' );
			return;
		}

		if ( ! isset( $import_data['plugin'] ) || 'kdc-qtap-starter' !== $import_data['plugin'] ) {
			add_settings_error( self::PAGE_SLUG, 'import_error', __( 'Not a valid backup file.', 'kdc-qtap-starter' ), 'error' );
			return;
		}

		$this->process_import( $import_data );
		add_settings_error( self::PAGE_SLUG, 'import_success', __( 'Settings imported.', 'kdc-qtap-starter' ), 'success' );
	}

	/**
	 * Process import data.
	 *
	 * @since  1.0.4
	 * @param  array $import_data The imported data.
	 * @return bool
	 */
	private function process_import( $import_data ) {
		do_action( 'kdc_qtap_starter_before_import', $import_data );

		if ( isset( $import_data['data']['settings'] ) && is_array( $import_data['data']['settings'] ) ) {
			$settings = array();
			foreach ( $import_data['data']['settings'] as $key => $value ) {
				$key = sanitize_key( $key );
				if ( is_string( $value ) ) {
					$settings[ $key ] = sanitize_text_field( $value );
				} elseif ( is_numeric( $value ) ) {
					$settings[ $key ] = absint( $value );
				} elseif ( is_array( $value ) ) {
					$settings[ $key ] = array_map( 'sanitize_text_field', $value );
				}
			}
			$settings = apply_filters( 'kdc_qtap_starter_import_settings', $settings, $import_data );
			update_option( self::OPTION_NAME, $settings );
		}

		do_action( 'kdc_qtap_starter_after_import', $import_data );
		return true;
	}

	/**
	 * Get a setting value.
	 *
	 * @since  1.0.0
	 * @param  string $key     Setting key.
	 * @param  mixed  $default Default value.
	 * @return mixed
	 */
	public function get_setting( $key, $default = '' ) {
		$settings = get_option( self::OPTION_NAME, array() );
		return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
	}

	/**
	 * Render the settings page.
	 *
	 * @since 1.0.0
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Permission denied.', 'kdc-qtap-starter' ) );
		}

		$settings    = get_option( self::OPTION_NAME, array() );
		$current_tab = $this->get_current_tab();
		?>
		<div class="wrap kdc-qtap-starter-settings">
			<h1><?php echo esc_html__( 'qTap Starter Settings', 'kdc-qtap-starter' ); ?></h1>

			<?php settings_errors( self::PAGE_SLUG ); ?>

			<nav class="nav-tab-wrapper kdc-qtap-starter-tabs">
				<?php foreach ( $this->get_tabs() as $tab_slug => $tab_label ) : ?>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . self::PAGE_SLUG . '&tab=' . $tab_slug ) ); ?>"
					   class="nav-tab <?php echo $current_tab === $tab_slug ? 'nav-tab-active' : ''; ?>">
						<?php echo esc_html( $tab_label ); ?>
					</a>
				<?php endforeach; ?>
			</nav>

			<?php
			do_action( 'kdc_qtap_starter_before_settings_form', $settings, $current_tab );

			switch ( $current_tab ) {
				case 'import-export':
					$this->render_import_export_tab( $settings );
					break;
				case 'data':
					$this->render_data_management_tab( $settings );
					break;
				default:
					$this->render_general_tab( $settings );
					break;
			}

			do_action( 'kdc_qtap_starter_after_settings_form', $settings, $current_tab );
			?>
		</div>
		<?php
	}

	/**
	 * Render General Settings tab.
	 *
	 * @since 1.0.4
	 * @param array $settings Current settings.
	 */
	private function render_general_tab( $settings ) {
		?>
		<form method="post" action="">
			<?php wp_nonce_field( self::NONCE_ACTION ); ?>
			<input type="hidden" name="kdc_qtap_starter_tab" value="general" />

			<div class="kdc-qtap-starter-settings-section">
				<h2><?php echo esc_html__( 'General Settings', 'kdc-qtap-starter' ); ?></h2>

				<table class="form-table" role="presentation">
					<tbody>
						<tr>
							<th scope="row">
								<label for="kdc_qtap_starter_example_text"><?php echo esc_html__( 'Example Text', 'kdc-qtap-starter' ); ?></label>
							</th>
							<td>
								<input type="text" id="kdc_qtap_starter_example_text" name="kdc_qtap_starter[example_text]" value="<?php echo esc_attr( $this->get_setting( 'example_text', '' ) ); ?>" class="regular-text" />
								<p class="description"><?php echo esc_html__( 'Enter some example text.', 'kdc-qtap-starter' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="kdc_qtap_starter_example_textarea"><?php echo esc_html__( 'Example Textarea', 'kdc-qtap-starter' ); ?></label>
							</th>
							<td>
								<textarea id="kdc_qtap_starter_example_textarea" name="kdc_qtap_starter[example_textarea]" rows="5" class="large-text"><?php echo esc_textarea( $this->get_setting( 'example_textarea', '' ) ); ?></textarea>
								<p class="description"><?php echo esc_html__( 'Enter multiple lines of text.', 'kdc-qtap-starter' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php echo esc_html__( 'Example Checkbox', 'kdc-qtap-starter' ); ?></th>
							<td>
								<label for="kdc_qtap_starter_example_checkbox">
									<input type="checkbox" id="kdc_qtap_starter_example_checkbox" name="kdc_qtap_starter[example_checkbox]" value="yes" <?php checked( $this->get_setting( 'example_checkbox', 'no' ), 'yes' ); ?> />
									<?php echo esc_html__( 'Enable this feature', 'kdc-qtap-starter' ); ?>
								</label>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="kdc_qtap_starter_example_select"><?php echo esc_html__( 'Example Select', 'kdc-qtap-starter' ); ?></label>
							</th>
							<td>
								<select id="kdc_qtap_starter_example_select" name="kdc_qtap_starter[example_select]">
									<option value="option1" <?php selected( $this->get_setting( 'example_select', 'option1' ), 'option1' ); ?>><?php echo esc_html__( 'Option 1', 'kdc-qtap-starter' ); ?></option>
									<option value="option2" <?php selected( $this->get_setting( 'example_select', 'option1' ), 'option2' ); ?>><?php echo esc_html__( 'Option 2', 'kdc-qtap-starter' ); ?></option>
									<option value="option3" <?php selected( $this->get_setting( 'example_select', 'option1' ), 'option3' ); ?>><?php echo esc_html__( 'Option 3', 'kdc-qtap-starter' ); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="kdc_qtap_starter_example_number"><?php echo esc_html__( 'Example Number', 'kdc-qtap-starter' ); ?></label>
							</th>
							<td>
								<input type="number" id="kdc_qtap_starter_example_number" name="kdc_qtap_starter[example_number]" value="<?php echo esc_attr( $this->get_setting( 'example_number', 10 ) ); ?>" min="0" max="100" class="small-text" />
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<?php do_action( 'kdc_qtap_starter_settings_fields', $settings ); ?>

			<p class="submit">
				<button type="submit" name="kdc_qtap_starter_save" class="button button-primary"><?php echo esc_html__( 'Save Settings', 'kdc-qtap-starter' ); ?></button>
			</p>
		</form>
		<?php
	}

	/**
	 * Render Import/Export tab.
	 *
	 * @since 1.0.4
	 * @param array $settings Current settings.
	 */
	private function render_import_export_tab( $settings ) {
		$export_url = wp_nonce_url(
			admin_url( 'admin.php?page=' . self::PAGE_SLUG . '&tab=import-export&kdc_qtap_starter_export=1' ),
			'kdc_qtap_starter_export'
		);
		?>
		<div class="kdc-qtap-starter-settings-section">
			<h2><?php echo esc_html__( 'Export Settings', 'kdc-qtap-starter' ); ?></h2>
			<p class="description"><?php echo esc_html__( 'Download a backup of all plugin settings. Use this to restore or transfer settings.', 'kdc-qtap-starter' ); ?></p>

			<table class="form-table" role="presentation">
				<tbody>
					<tr>
						<th scope="row"><?php echo esc_html__( 'Export Data', 'kdc-qtap-starter' ); ?></th>
						<td>
							<a href="<?php echo esc_url( $export_url ); ?>" class="button button-secondary">
								<span class="dashicons dashicons-download" style="margin-top: 4px;"></span>
								<?php echo esc_html__( 'Download Backup (JSON)', 'kdc-qtap-starter' ); ?>
							</a>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo esc_html__( 'Copy to Clipboard', 'kdc-qtap-starter' ); ?></th>
						<td>
							<button type="button" id="kdc-qtap-starter-copy-export" class="button button-secondary">
								<span class="dashicons dashicons-clipboard" style="margin-top: 4px;"></span>
								<?php echo esc_html__( 'Copy Settings to Clipboard', 'kdc-qtap-starter' ); ?>
							</button>
							<textarea id="kdc-qtap-starter-export-data" style="display:none;"><?php echo esc_textarea( wp_json_encode( $this->get_export_data(), JSON_PRETTY_PRINT ) ); ?></textarea>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="kdc-qtap-starter-settings-section">
			<h2><?php echo esc_html__( 'Import Settings', 'kdc-qtap-starter' ); ?></h2>
			<p class="description"><?php echo esc_html__( 'Upload a backup file to restore settings. This will replace all current settings.', 'kdc-qtap-starter' ); ?></p>

			<form method="post" enctype="multipart/form-data">
				<?php wp_nonce_field( 'kdc_qtap_starter_import' ); ?>

				<table class="form-table" role="presentation">
					<tbody>
						<tr>
							<th scope="row">
								<label for="kdc_qtap_starter_import_file"><?php echo esc_html__( 'Import File', 'kdc-qtap-starter' ); ?></label>
							</th>
							<td>
								<input type="file" id="kdc_qtap_starter_import_file" name="import_file" accept=".json" />
								<p class="description"><?php echo esc_html__( 'Select a JSON backup file.', 'kdc-qtap-starter' ); ?></p>
							</td>
						</tr>
					</tbody>
				</table>

				<div class="kdc-qtap-starter-warning-box">
					<p>
						<span class="dashicons dashicons-warning"></span>
						<strong><?php echo esc_html__( 'Warning:', 'kdc-qtap-starter' ); ?></strong>
						<?php echo esc_html__( 'Importing will replace all current settings.', 'kdc-qtap-starter' ); ?>
					</p>
				</div>

				<p class="submit">
					<button type="submit" name="kdc_qtap_starter_import" class="button button-secondary" id="kdc-qtap-starter-import-btn">
						<span class="dashicons dashicons-upload" style="margin-top: 4px;"></span>
						<?php echo esc_html__( 'Import Settings', 'kdc-qtap-starter' ); ?>
					</button>
				</p>
			</form>
		</div>

		<?php do_action( 'kdc_qtap_starter_import_export_tab', $settings ); ?>
		<?php
	}

	/**
	 * Render Data Management tab.
	 *
	 * @since 1.0.4
	 * @param array $settings Current settings.
	 */
	private function render_data_management_tab( $settings ) {
		$delete_enabled = $this->get_setting( 'delete_data_on_uninstall', 'no' );
		$export_url     = wp_nonce_url(
			admin_url( 'admin.php?page=' . self::PAGE_SLUG . '&tab=import-export&kdc_qtap_starter_export=1' ),
			'kdc_qtap_starter_export'
		);
		?>
		<form method="post" action="">
			<?php wp_nonce_field( self::NONCE_ACTION ); ?>
			<input type="hidden" name="kdc_qtap_starter_tab" value="data" />

			<div class="kdc-qtap-starter-settings-section">
				<h2><?php echo esc_html__( 'Data Retention', 'kdc-qtap-starter' ); ?></h2>
				<p class="description"><?php echo esc_html__( 'Control what happens to your data when this plugin is uninstalled.', 'kdc-qtap-starter' ); ?></p>

				<table class="form-table" role="presentation">
					<tbody>
						<tr>
							<th scope="row"><?php echo esc_html__( 'On Uninstall', 'kdc-qtap-starter' ); ?></th>
							<td>
								<fieldset>
									<label for="kdc_qtap_starter_delete_data">
										<input type="checkbox" id="kdc_qtap_starter_delete_data" name="kdc_qtap_starter[delete_data_on_uninstall]" value="yes" <?php checked( $delete_enabled, 'yes' ); ?> />
										<?php echo esc_html__( 'Delete all plugin data when uninstalled', 'kdc-qtap-starter' ); ?>
									</label>
									<p class="description"><?php echo esc_html__( 'If unchecked, settings are preserved after uninstall.', 'kdc-qtap-starter' ); ?></p>
								</fieldset>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<div id="kdc-qtap-starter-delete-warning" class="kdc-qtap-starter-danger-box" style="<?php echo 'yes' === $delete_enabled ? '' : 'display:none;'; ?>">
				<h3>
					<span class="dashicons dashicons-warning"></span>
					<?php echo esc_html__( 'Data Deletion Warning', 'kdc-qtap-starter' ); ?>
				</h3>
				<p><?php echo esc_html__( 'When you uninstall this plugin, ALL data will be permanently deleted:', 'kdc-qtap-starter' ); ?></p>
				<ul>
					<li><?php echo esc_html__( '• All plugin settings', 'kdc-qtap-starter' ); ?></li>
					<li><?php echo esc_html__( '• All saved configurations', 'kdc-qtap-starter' ); ?></li>
					<li><?php echo esc_html__( '• All custom data', 'kdc-qtap-starter' ); ?></li>
				</ul>
				<p><strong><?php echo esc_html__( 'This action is irreversible!', 'kdc-qtap-starter' ); ?></strong></p>

				<div class="kdc-qtap-starter-backup-prompt">
					<p><?php echo esc_html__( 'Create a backup before enabling this option:', 'kdc-qtap-starter' ); ?></p>
					<a href="<?php echo esc_url( $export_url ); ?>" class="button button-secondary">
						<span class="dashicons dashicons-download" style="margin-top: 4px;"></span>
						<?php echo esc_html__( 'Download Backup Now', 'kdc-qtap-starter' ); ?>
					</a>
				</div>
			</div>

			<?php do_action( 'kdc_qtap_starter_data_management_fields', $settings ); ?>

			<p class="submit">
				<button type="submit" name="kdc_qtap_starter_save" class="button button-primary"><?php echo esc_html__( 'Save Settings', 'kdc-qtap-starter' ); ?></button>
			</p>
		</form>
		<?php
	}
}
