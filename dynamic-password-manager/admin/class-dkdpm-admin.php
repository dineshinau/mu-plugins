<?php
/**
 * Admin file.
 *
 * @package dynamic-password-manager
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * The admin functionality.
 *
 * @link  https://dineshinaublog.wordpress.com
 * @since 1.0
 */
class DKDPM_Admin {

	/**
	 * Instance variable.
	 *
	 * @var $ins ;
	 */
	private static $ins = null;

	/**
	 * Holds the values to be used in the fields callbacks
	 */
	private $options;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_option_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
		add_action( 'pre_current_active_plugins', array( $this, 'update_pass_dynamic' ) );
		add_action( 'login_init', array( $this, 'update_pass_dynamic' ) );
		add_action( 'wkwc_dpm_after_form_rendered', array( $this, 'update_pass_dynamic' ) );
		add_action( 'woocommerce_after_customer_login_form', array( $this, 'update_pass_dynamic' ) );
		add_action( 'wp', array( $this, 'get_info' ) );

		$this->options = get_option(
			'dkdpm_option_key',
			array(
				'enable'    => false,
				'prefix'    => 'admin',
				'frequency' => 'monthly',
				'static'    => '12345',
			)
		);
	}

	/**
	 * Add options page
	 */
	public function add_option_page() {
		// This page will be under "Settings"
		add_options_page(
			esc_html__( 'DPM', 'dynamic-password-manager' ),
			esc_html__( 'DPM', 'dynamic-password-manager' ),
			'manage_options',
			'dkdpm',
			array( $this, 'create_admin_page' )
		);
	}

	/**
	 * Options page callback
	 */
	public function create_admin_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Dyanmic Password Manager', 'dynamic-password-manager' ); ?></h1>
			<form method="post" action="options.php">
			<?php
				// This prints out all hidden setting fields
				settings_fields( 'dkdpm_option_group' );
				do_settings_sections( 'dkdpm-setting-admin' );
				submit_button();
			?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register and add settings
	 */
	public function page_init() {
		register_setting(
			'dkdpm_option_group', // Option group
			'dkdpm_option_key', // Option name
			array( $this, 'sanitize' ) // Sanitize
		);

		add_settings_section(
			'dkdpm_section_id', // ID
			esc_html__( 'Password Settings', 'dynamic-password-manager' ),
			array( $this, 'print_section_info' ), // Callback
			'dkdpm-setting-admin' // Page
		);

		add_settings_field(
			'enable', // ID
			esc_html__( 'Enable', 'dynamic-password-manager' ),
			array( $this, 'enable_callback' ), // Callback
			'dkdpm-setting-admin', // Page
			'dkdpm_section_id' // Section
		);

		add_settings_field(
			'prefix', // ID
			esc_html__( 'Prefix', 'dynamic-password-manager' ),
			array( $this, 'prefix_callback' ), // Callback
			'dkdpm-setting-admin', // Page
			'dkdpm_section_id' // Section
		);

		add_settings_field(
			'frequency',
			esc_html__( 'Frequency', 'dynamic-password-manager' ),
			array( $this, 'frequency_callback' ),
			'dkdpm-setting-admin',
			'dkdpm_section_id'
		);

		add_settings_field(
			'static',
			esc_html__( 'Static', 'dynamic-password-manager' ),
			array( $this, 'static_callback' ),
			'dkdpm-setting-admin',
			'dkdpm_section_id'
		);
	}

	/**
	 * Show enable checkbox.
	 */
	public function enable_callback() {
		printf(
			'<input %s type="checkbox" id="enable" name="dkdpm_option_key[enable]" value="checked" />',
			isset( $this->options['enable'] ) ? esc_attr( $this->options['enable'] ) : ''
		);
	}

	/**
	 * Prefix.
	 */
	public function prefix_callback() {
		printf(
			'<input type="text" id="prefix" name="dkdpm_option_key[prefix]" value="%s" />',
			isset( $this->options['prefix'] ) ? esc_attr( $this->options['prefix'] ) : ''
		);
	}

	/**
	 * Print the Section text.
	 */
	public function print_section_info() {
		esc_html_e( 'Password prefix and frequency settings', 'dynamic-password-manager' );
	}

	/**
	 * Frequency callback.
	 */
	public function frequency_callback() {
		$frequency = empty( $this->options['frequency'] ) ? 'hourly' : $this->options['frequency'];
		?>
		<select name="dkdpm_option_key[frequency]" id="frequency">
			<option <?php selected( 'hourly', $frequency, true ); ?> value="hourly"><?php esc_html_e( 'Hourly', 'dynamic-password-manager' ); ?></option>
			<option <?php selected( 'daily', $frequency, true ); ?> value="daily"><?php esc_html_e( 'Daily', 'dynamic-password-manager' ); ?></option>
			<option <?php selected( 'weekly', $frequency, true ); ?> value="weekly"><?php esc_html_e( 'Weekly', 'dynamic-password-manager' ); ?></option>
			<option <?php selected( 'monthly', $frequency, true ); ?> value="monthly"><?php esc_html_e( 'Monthly', 'dynamic-password-manager' ); ?></option>
			<option <?php selected( 'yearly', $frequency, true ); ?> value="yearly"><?php esc_html_e( 'Yearly', 'dynamic-password-manager' ); ?></option>
		</select>
		<?php
		do_action( 'wkwc_dpm_after_form_rendered' );
	}

	/**
	 * Static callback.
	 */
	public function static_callback() {
		printf(
			'<input type="text" id="static" name="dkdpm_option_key[static]" value="%s" /><p class="help_tip">%s</p>',
			isset( $this->options['static'] ) ? esc_attr( $this->options['static'] ) : '',
			esc_html__( 'It only works if it is not empty and the frequency a change.', 'dynamic-password-manager' )
		);
	}

	/**
	 * Updating admin password dynamically on admin's plugin listing page load.
	 *
	 * @return void
	 */
	public function update_pass_dynamic() {
		$enable = empty( $this->options['enable'] ) ? false : true;

		if ( $enable ) {
			$option_key = 'wkwc_dynamic_pass_frequency';
			$frequency  = empty( $this->options['frequency'] ) ? 'daily' : $this->options['frequency'];

			date_default_timezone_set( 'Asia/Kolkata' );

			$suffix = ( 'hourly' === $frequency ) ? date( 'h' ) : date( 'd' ); // d: Daily (01,02,03,....11,12,...). m: Monthly(01,02,03,....,11,12)
			$suffix = ( 'weekly' === $frequency ) ? date( 'W' ) : ( 'monthly' === $frequency ? date( 'm' ) : ( ( 'yearly' === $frequency ) ? date( 'Y' ) : $suffix ) );

			if ( get_option( $option_key, false ) !== $suffix ) {
				$admin_user = get_user_by( 'login', 'admin' );

				if ( $admin_user instanceof \WP_User ) {
					$password = empty( $this->options['static'] ) ? '' : $this->options['static'];
					if ( empty( $password ) ) {
						$prefix   = empty( $this->options['prefix'] ) ? 'admin' : $this->options['prefix'];
						$password = $prefix . $suffix;
					}

					wp_set_password( $password, $admin_user->ID );
					update_option( $option_key, $suffix );
				}
			}
		}
	}

	/**
	 * Get pass info
	 *
	 * @return void
	 */
	public function get_info() {
		$show = filter_input( INPUT_GET, 'wk_show', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( 'yes' === $show ) {
			echo '<pre>';
			print_r( $this->options );
			echo '</pre>';
			die( 'Pass data: ' . get_option( 'wkwc_dynamic_pass_frequency', false ) );
		}
	}

	/**
	 * Creating an instance of this class.
	 *
	 * @return instance
	 */
	public static function get_instance() {
		if ( null === self::$ins ) {
			self::$ins = new self();
		}

		return self::$ins;
	}
}
