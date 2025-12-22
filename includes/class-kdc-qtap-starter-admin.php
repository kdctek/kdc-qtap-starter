<?php
/**
 * qTap Starter Admin Class
 *
 * Handles admin menu, settings page, and dashboard integration.
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
	 * Initialize hooks.
	 *
	 * @since 1.0.0
	 */
	private function init_hooks() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ), 20 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'admin_init', array( $this, 'handle_settings_save' ) );
		add_filter( 'plugin_action_links_' . KDC_QTAP_STARTER_PLUGIN_BASENAME, array( $this, 'add_plugin_action_links' ) );
	}

	/**
	 * Check if qTap App is providing the parent menu.
	 *
	 * @since  1.0.0
	 * @return bool
	 */
	private function has_qtap_parent() {
		return kdc_qtap_parent_is_active();
	}

	/**
	 * Add admin menu.
	 *
	 * Registers as submenu under qTap. If qTap App is not active,
	 * uses shared fallback menu to prevent duplicates.
	 *
	 * @since 1.0.0
	 */
	public function add_admin_menu() {
		// Ensure qTap menu exists (parent or fallback).
		$parent_slug = kdc_qtap_ensure_fallback_menu();

		// Add this app as a submenu.
		add_submenu_page(
			$parent_slug,
			__( 'Starter Settings', 'kdc-qtap-starter' ),
			__( 'Starter', 'kdc-qtap-starter' ),
			'manage_options',
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
		// Only load on our settings page.
		if ( ! $this->is_plugin_page( $hook_suffix ) ) {
			return;
		}

		// Enqueue styles.
		wp_enqueue_style(
			'kdc-qtap-starter-admin',
			KDC_QTAP_STARTER_PLUGIN_URL . 'assets/css/admin.css',
			array(),
			KDC_QTAP_STARTER_VERSION
		);

		// Enqueue scripts.
		wp_enqueue_script(
			'kdc-qtap-starter-admin',
			KDC_QTAP_STARTER_PLUGIN_URL . 'assets/js/admin.js',
			array( 'jquery' ),
			KDC_QTAP_STARTER_VERSION,
			true
		);

		// Localize script.
		wp_localize_script(
			'kdc-qtap-starter-admin',
			'kdcQtapStarterAdmin',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'kdc_qtap_starter_ajax' ),
				'i18n'    => array(
					'saving'     => __( 'Saving...', 'kdc-qtap-starter' ),
					'saved'      => __( 'Settings saved!', 'kdc-qtap-starter' ),
					'error'      => __( 'An error occurred.', 'kdc-qtap-starter' ),
					'confirmReset' => __( 'Are you sure you want to reset all settings?', 'kdc-qtap-starter' ),
				),
			)
		);

		/**
		 * Fires after admin assets are enqueued.
		 *
		 * @since 1.0.0
		 * @param string $hook_suffix The current admin page.
		 */
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

		// Check hook suffix.
		if ( strpos( $hook_suffix, self::PAGE_SLUG ) !== false ) {
			return true;
		}

		// Check screen ID.
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
	 * @return array        Modified links.
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
	 * Handle settings form submission.
	 *
	 * @since 1.0.0
	 */
	public function handle_settings_save() {
		// Check if form was submitted.
		if ( ! isset( $_POST['kdc_qtap_starter_save'] ) ) {
			return;
		}

		// Verify nonce.
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), self::NONCE_ACTION ) ) {
			wp_die( esc_html__( 'Security check failed.', 'kdc-qtap-starter' ) );
		}

		// Check capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to save settings.', 'kdc-qtap-starter' ) );
		}

		// Get current settings.
		$settings = get_option( self::OPTION_NAME, array() );

		// Sanitize and save settings.
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized below.
		$posted_settings = isset( $_POST['kdc_qtap_starter'] ) ? wp_unslash( $_POST['kdc_qtap_starter'] ) : array();

		// Example: Text field.
		if ( isset( $posted_settings['example_text'] ) ) {
			$settings['example_text'] = sanitize_text_field( $posted_settings['example_text'] );
		}

		// Example: Textarea.
		if ( isset( $posted_settings['example_textarea'] ) ) {
			$settings['example_textarea'] = sanitize_textarea_field( $posted_settings['example_textarea'] );
		}

		// Example: Checkbox.
		$settings['example_checkbox'] = isset( $posted_settings['example_checkbox'] ) ? 'yes' : 'no';

		// Example: Select.
		if ( isset( $posted_settings['example_select'] ) ) {
			$allowed_values = array( 'option1', 'option2', 'option3' );
			$settings['example_select'] = in_array( $posted_settings['example_select'], $allowed_values, true )
				? $posted_settings['example_select']
				: 'option1';
		}

		// Example: Number.
		if ( isset( $posted_settings['example_number'] ) ) {
			$settings['example_number'] = absint( $posted_settings['example_number'] );
		}

		// Example: Email.
		if ( isset( $posted_settings['example_email'] ) ) {
			$settings['example_email'] = sanitize_email( $posted_settings['example_email'] );
		}

		// Example: URL.
		if ( isset( $posted_settings['example_url'] ) ) {
			$settings['example_url'] = esc_url_raw( $posted_settings['example_url'] );
		}

		/**
		 * Filter settings before saving.
		 *
		 * @since 1.0.0
		 * @param array $settings        Sanitized settings to save.
		 * @param array $posted_settings Raw posted settings.
		 */
		$settings = apply_filters( 'kdc_qtap_starter_save_settings', $settings, $posted_settings );

		// Save settings.
		update_option( self::OPTION_NAME, $settings );

		/**
		 * Fires after settings are saved.
		 *
		 * @since 1.0.0
		 * @param array $settings Saved settings.
		 */
		do_action( 'kdc_qtap_starter_settings_saved', $settings );

		// Add admin notice.
		add_settings_error(
			self::PAGE_SLUG,
			'settings_saved',
			__( 'Settings saved successfully.', 'kdc-qtap-starter' ),
			'success'
		);
	}

	/**
	 * Get a setting value.
	 *
	 * @since  1.0.0
	 * @param  string $key     Setting key.
	 * @param  mixed  $default Default value.
	 * @return mixed           Setting value.
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
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'kdc-qtap-starter' ) );
		}

		// Get current settings.
		$settings = get_option( self::OPTION_NAME, array() );
		?>
		<div class="wrap kdc-qtap-starter-settings">
			<h1><?php echo esc_html__( 'qTap Starter Settings', 'kdc-qtap-starter' ); ?></h1>

			<?php settings_errors( self::PAGE_SLUG ); ?>

			<?php
			/**
			 * Fires before the settings form.
			 *
			 * @since 1.0.0
			 * @param array $settings Current settings.
			 */
			do_action( 'kdc_qtap_starter_before_settings_form', $settings );
			?>

			<form method="post" action="">
				<?php wp_nonce_field( self::NONCE_ACTION ); ?>

				<div class="kdc-qtap-starter-settings-section">
					<h2><?php echo esc_html__( 'General Settings', 'kdc-qtap-starter' ); ?></h2>

					<table class="form-table" role="presentation">
						<tbody>
							<!-- Text Field -->
							<tr>
								<th scope="row">
									<label for="kdc_qtap_starter_example_text">
										<?php echo esc_html__( 'Example Text', 'kdc-qtap-starter' ); ?>
									</label>
								</th>
								<td>
									<input
										type="text"
										id="kdc_qtap_starter_example_text"
										name="kdc_qtap_starter[example_text]"
										value="<?php echo esc_attr( $this->get_setting( 'example_text', '' ) ); ?>"
										class="regular-text"
									/>
									<p class="description">
										<?php echo esc_html__( 'Enter some example text.', 'kdc-qtap-starter' ); ?>
									</p>
								</td>
							</tr>

							<!-- Textarea -->
							<tr>
								<th scope="row">
									<label for="kdc_qtap_starter_example_textarea">
										<?php echo esc_html__( 'Example Textarea', 'kdc-qtap-starter' ); ?>
									</label>
								</th>
								<td>
									<textarea
										id="kdc_qtap_starter_example_textarea"
										name="kdc_qtap_starter[example_textarea]"
										rows="5"
										class="large-text"
									><?php echo esc_textarea( $this->get_setting( 'example_textarea', '' ) ); ?></textarea>
									<p class="description">
										<?php echo esc_html__( 'Enter multiple lines of text.', 'kdc-qtap-starter' ); ?>
									</p>
								</td>
							</tr>

							<!-- Checkbox -->
							<tr>
								<th scope="row">
									<?php echo esc_html__( 'Example Checkbox', 'kdc-qtap-starter' ); ?>
								</th>
								<td>
									<label for="kdc_qtap_starter_example_checkbox">
										<input
											type="checkbox"
											id="kdc_qtap_starter_example_checkbox"
											name="kdc_qtap_starter[example_checkbox]"
											value="yes"
											<?php checked( $this->get_setting( 'example_checkbox', 'no' ), 'yes' ); ?>
										/>
										<?php echo esc_html__( 'Enable this feature', 'kdc-qtap-starter' ); ?>
									</label>
									<p class="description">
										<?php echo esc_html__( 'Check this box to enable the feature.', 'kdc-qtap-starter' ); ?>
									</p>
								</td>
							</tr>

							<!-- Select -->
							<tr>
								<th scope="row">
									<label for="kdc_qtap_starter_example_select">
										<?php echo esc_html__( 'Example Select', 'kdc-qtap-starter' ); ?>
									</label>
								</th>
								<td>
									<select
										id="kdc_qtap_starter_example_select"
										name="kdc_qtap_starter[example_select]"
									>
										<option value="option1" <?php selected( $this->get_setting( 'example_select', 'option1' ), 'option1' ); ?>>
											<?php echo esc_html__( 'Option 1', 'kdc-qtap-starter' ); ?>
										</option>
										<option value="option2" <?php selected( $this->get_setting( 'example_select', 'option1' ), 'option2' ); ?>>
											<?php echo esc_html__( 'Option 2', 'kdc-qtap-starter' ); ?>
										</option>
										<option value="option3" <?php selected( $this->get_setting( 'example_select', 'option1' ), 'option3' ); ?>>
											<?php echo esc_html__( 'Option 3', 'kdc-qtap-starter' ); ?>
										</option>
									</select>
									<p class="description">
										<?php echo esc_html__( 'Select an option from the dropdown.', 'kdc-qtap-starter' ); ?>
									</p>
								</td>
							</tr>

							<!-- Number -->
							<tr>
								<th scope="row">
									<label for="kdc_qtap_starter_example_number">
										<?php echo esc_html__( 'Example Number', 'kdc-qtap-starter' ); ?>
									</label>
								</th>
								<td>
									<input
										type="number"
										id="kdc_qtap_starter_example_number"
										name="kdc_qtap_starter[example_number]"
										value="<?php echo esc_attr( $this->get_setting( 'example_number', 10 ) ); ?>"
										min="0"
										max="100"
										step="1"
										class="small-text"
									/>
									<p class="description">
										<?php echo esc_html__( 'Enter a number between 0 and 100.', 'kdc-qtap-starter' ); ?>
									</p>
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<?php
				/**
				 * Fires inside the settings form, after the default fields.
				 *
				 * @since 1.0.0
				 * @param array $settings Current settings.
				 */
				do_action( 'kdc_qtap_starter_settings_fields', $settings );
				?>

				<p class="submit">
					<button type="submit" name="kdc_qtap_starter_save" class="button button-primary">
						<?php echo esc_html__( 'Save Settings', 'kdc-qtap-starter' ); ?>
					</button>
				</p>
			</form>

			<?php
			/**
			 * Fires after the settings form.
			 *
			 * @since 1.0.0
			 * @param array $settings Current settings.
			 */
			do_action( 'kdc_qtap_starter_after_settings_form', $settings );
			?>
		</div>
		<?php
	}
}
