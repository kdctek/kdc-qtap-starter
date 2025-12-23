<?php
/**
 * qTap Starter Admin Class
 *
 * Handles admin menu and settings page.
 * Data Management (Export, Import, Data Retention) is handled by the parent qTap App plugin.
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
	 * @since 1.4.0 Removed export/import handlers (now in parent plugin).
	 */
	private function init_hooks() {
		// Use qTap App's admin menu hook (requires qTap App).
		add_action( 'kdc_qtap_admin_menu', array( $this, 'add_admin_menu' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'admin_init', array( $this, 'handle_settings_save' ) );
		add_filter( 'plugin_action_links_' . KDC_QTAP_STARTER_PLUGIN_BASENAME, array( $this, 'add_plugin_action_links' ) );
	}

	/**
	 * Check if accessibility mode is enabled.
	 *
	 * @since  1.3.0
	 * @return bool
	 */
	private function is_accessibility_enabled() {
		return kdc_qtap_starter()->is_accessibility_enabled();
	}

	/**
	 * Add admin menu as submenu of qTap App.
	 *
	 * @since 1.0.0
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
	 * Loads WCAG AAA compliant CSS only when accessibility mode is enabled
	 * in the parent plugin. Otherwise, uses minimal CSS with WordPress core styles.
	 *
	 * @since 1.0.0
	 * @since 1.3.0 Added conditional CSS loading based on accessibility mode.
	 *
	 * @param string $hook_suffix The current admin page.
	 */
	public function enqueue_admin_assets( $hook_suffix ) {
		if ( ! $this->is_plugin_page( $hook_suffix ) ) {
			return;
		}

		// Load different CSS based on accessibility mode.
		if ( $this->is_accessibility_enabled() ) {
			// Load WCAG AAA compliant CSS.
			wp_enqueue_style(
				'kdc-qtap-starter-admin',
				KDC_QTAP_STARTER_PLUGIN_URL . 'assets/css/admin-accessible.css',
				array(),
				KDC_QTAP_STARTER_VERSION
			);
		} else {
			// Load minimal CSS (WordPress core styles).
			wp_enqueue_style(
				'kdc-qtap-starter-admin',
				KDC_QTAP_STARTER_PLUGIN_URL . 'assets/css/admin.css',
				array(),
				KDC_QTAP_STARTER_VERSION
			);
		}

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
				'ajaxUrl'              => admin_url( 'admin-ajax.php' ),
				'nonce'                => wp_create_nonce( 'kdc_qtap_starter_ajax' ),
				'accessibilityEnabled' => $this->is_accessibility_enabled(),
				'i18n'                 => array(
					'saving' => __( 'Saving...', 'kdc-qtap-starter' ),
					'saved'  => __( 'Settings saved!', 'kdc-qtap-starter' ),
					'error'  => __( 'An error occurred.', 'kdc-qtap-starter' ),
				),
			)
		);

		/**
		 * Fires when admin assets are enqueued.
		 *
		 * @since 1.0.0
		 * @since 1.3.0 Added accessibility mode parameter.
		 *
		 * @param string $hook_suffix        The current admin page.
		 * @param bool   $accessibility_mode Whether accessibility mode is enabled.
		 */
		do_action( 'kdc_qtap_starter_admin_enqueue_scripts', $hook_suffix, $this->is_accessibility_enabled() );
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
	 * Get a specific setting.
	 *
	 * @since  1.0.0
	 * @param  string $key     Setting key.
	 * @param  mixed  $default Default value.
	 * @return mixed
	 */
	private function get_setting( $key, $default = '' ) {
		$settings = get_option( self::OPTION_NAME, array() );
		return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
	}

	/**
	 * Handle settings save.
	 *
	 * @since 1.0.0
	 */
	public function handle_settings_save() {
		if ( ! isset( $_POST['kdc_qtap_starter_save'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_key( $_POST['_wpnonce'] ?? '' ), self::NONCE_ACTION ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$settings = get_option( self::OPTION_NAME, array() );

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$input = isset( $_POST['kdc_qtap_starter'] ) ? wp_unslash( $_POST['kdc_qtap_starter'] ) : array();

		$settings['example_text']     = isset( $input['example_text'] ) ? sanitize_text_field( $input['example_text'] ) : '';
		$settings['example_textarea'] = isset( $input['example_textarea'] ) ? sanitize_textarea_field( $input['example_textarea'] ) : '';
		$settings['example_checkbox'] = isset( $input['example_checkbox'] ) ? 'yes' : 'no';
		$settings['example_select']   = isset( $input['example_select'] ) ? sanitize_key( $input['example_select'] ) : 'option1';
		$settings['example_number']   = isset( $input['example_number'] ) ? absint( $input['example_number'] ) : 10;

		/**
		 * Filter settings before saving.
		 *
		 * @since 1.0.0
		 * @param array $settings Current settings.
		 * @param array $input    Raw input data.
		 */
		$settings = apply_filters( 'kdc_qtap_starter_save_settings', $settings, $input );

		update_option( self::OPTION_NAME, $settings );

		// Redirect to prevent resubmission.
		wp_safe_redirect(
			add_query_arg(
				array(
					'page'    => self::PAGE_SLUG,
					'message' => 'saved',
				),
				admin_url( 'admin.php' )
			)
		);
		exit;
	}

	/**
	 * Render settings page.
	 *
	 * @since 1.0.0
	 * @since 1.3.0 Added accessibility mode integration.
	 * @since 1.4.0 Simplified to single settings page (Data Management in parent).
	 */
	public function render_settings_page() {
		$settings  = get_option( self::OPTION_NAME, array() );
		$a11y_mode = $this->is_accessibility_enabled();

		// Wrapper class based on accessibility mode.
		$wrapper_class = 'kdc-qtap-starter-settings';
		if ( $a11y_mode ) {
			$wrapper_class .= ' kdc-qtap-starter-accessible';
		}
		?>
		<div class="wrap <?php echo esc_attr( $wrapper_class ); ?>">
			<?php $this->render_admin_notices(); ?>

			<h1><?php esc_html_e( 'qTap Starter Settings', 'kdc-qtap-starter' ); ?></h1>

			<?php if ( $a11y_mode ) : ?>
				<p class="description" style="margin-bottom: 15px;">
					<span class="dashicons dashicons-universal-access" aria-hidden="true"></span>
					<?php esc_html_e( 'Accessibility Mode is enabled. Enhanced styles are active.', 'kdc-qtap-starter' ); ?>
				</p>
			<?php endif; ?>

			<?php $this->render_settings_form( $settings ); ?>

			<hr style="margin: 30px 0;" />

			<p class="description">
				<?php
				printf(
					/* translators: %s: Link to qTap Data Management */
					esc_html__( 'For Export, Import, and Data Management settings, visit %s.', 'kdc-qtap-starter' ),
					'<a href="' . esc_url( admin_url( 'admin.php?page=kdc-qtap&tab=data' ) ) . '">' . esc_html__( 'qTap → Data Management', 'kdc-qtap-starter' ) . '</a>'
				);
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Render admin notices.
	 *
	 * @since 1.0.4
	 */
	private function render_admin_notices() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$message = isset( $_GET['message'] ) ? sanitize_key( $_GET['message'] ) : '';

		if ( empty( $message ) ) {
			return;
		}

		$notices = array(
			'saved' => array(
				'type' => 'success',
				'text' => __( 'Settings saved successfully.', 'kdc-qtap-starter' ),
			),
		);

		if ( isset( $notices[ $message ] ) ) {
			printf(
				'<div class="notice notice-%s is-dismissible"><p>%s</p></div>',
				esc_attr( $notices[ $message ]['type'] ),
				esc_html( $notices[ $message ]['text'] )
			);
		}
	}

	/**
	 * Render settings form.
	 *
	 * @since 1.0.4
	 * @since 1.4.0 Renamed from render_general_tab.
	 *
	 * @param array $settings Current settings.
	 */
	private function render_settings_form( $settings ) {
		?>
		<form method="post" action="">
			<?php wp_nonce_field( self::NONCE_ACTION ); ?>

			<div class="kdc-qtap-starter-settings-section">
				<h2><?php esc_html_e( 'General Settings', 'kdc-qtap-starter' ); ?></h2>

				<table class="form-table" role="presentation">
					<tbody>
						<tr>
							<th scope="row">
								<label for="kdc_qtap_starter_example_text"><?php esc_html_e( 'Example Text', 'kdc-qtap-starter' ); ?></label>
							</th>
							<td>
								<input type="text" id="kdc_qtap_starter_example_text" name="kdc_qtap_starter[example_text]" value="<?php echo esc_attr( $this->get_setting( 'example_text', '' ) ); ?>" class="regular-text" />
								<p class="description"><?php esc_html_e( 'Enter some example text.', 'kdc-qtap-starter' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="kdc_qtap_starter_example_textarea"><?php esc_html_e( 'Example Textarea', 'kdc-qtap-starter' ); ?></label>
							</th>
							<td>
								<textarea id="kdc_qtap_starter_example_textarea" name="kdc_qtap_starter[example_textarea]" rows="5" class="large-text"><?php echo esc_textarea( $this->get_setting( 'example_textarea', '' ) ); ?></textarea>
								<p class="description"><?php esc_html_e( 'Enter multiple lines of text.', 'kdc-qtap-starter' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Example Checkbox', 'kdc-qtap-starter' ); ?></th>
							<td>
								<label for="kdc_qtap_starter_example_checkbox">
									<input type="checkbox" id="kdc_qtap_starter_example_checkbox" name="kdc_qtap_starter[example_checkbox]" value="yes" <?php checked( $this->get_setting( 'example_checkbox', 'no' ), 'yes' ); ?> />
									<?php esc_html_e( 'Enable this feature', 'kdc-qtap-starter' ); ?>
								</label>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="kdc_qtap_starter_example_select"><?php esc_html_e( 'Example Select', 'kdc-qtap-starter' ); ?></label>
							</th>
							<td>
								<select id="kdc_qtap_starter_example_select" name="kdc_qtap_starter[example_select]">
									<option value="option1" <?php selected( $this->get_setting( 'example_select', 'option1' ), 'option1' ); ?>><?php esc_html_e( 'Option 1', 'kdc-qtap-starter' ); ?></option>
									<option value="option2" <?php selected( $this->get_setting( 'example_select', 'option1' ), 'option2' ); ?>><?php esc_html_e( 'Option 2', 'kdc-qtap-starter' ); ?></option>
									<option value="option3" <?php selected( $this->get_setting( 'example_select', 'option1' ), 'option3' ); ?>><?php esc_html_e( 'Option 3', 'kdc-qtap-starter' ); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="kdc_qtap_starter_example_number"><?php esc_html_e( 'Example Number', 'kdc-qtap-starter' ); ?></label>
							</th>
							<td>
								<input type="number" id="kdc_qtap_starter_example_number" name="kdc_qtap_starter[example_number]" value="<?php echo esc_attr( $this->get_setting( 'example_number', 10 ) ); ?>" min="0" max="100" class="small-text" />
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<?php
			/**
			 * Fires after settings fields.
			 *
			 * @since 1.0.0
			 * @param array $settings Current settings.
			 */
			do_action( 'kdc_qtap_starter_settings_fields', $settings );
			?>

			<p class="submit">
				<button type="submit" name="kdc_qtap_starter_save" class="button button-primary"><?php esc_html_e( 'Save Settings', 'kdc-qtap-starter' ); ?></button>
			</p>
		</form>
		<?php
	}
}
