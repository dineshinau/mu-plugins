<?php
/**
 * Plugin Name: WP Kint Debugger (wkd)
 * Author: Kint
 * Description: To debug the php code using Kint. Just call the function wkd with the variable in arguments.
 * Version:     1.0
 * Text Domain: wp_kint_debug
 * Domain Path: /languages
 * Requires PHP: 7.4
 * Requires at least: 6.4
 *
 * @package WP Kint Debugger
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'WPKD_Core' ) ) {
	/**
	 * Plugin core class file.
	 */
	class WPKD_Core {
		/**
		 * Instance variable.
		 *
		 * @var $instance
		 */
		public static $instance = null;

		/**
		 * Admin instance.
		 *
		 * @var $admin
		 */
		public $admin = null;

		/**
		 * Constructor.
		 */
		public function __construct() {
			/**
			 * Load important variables and constants.
			 */
			$this->define_plugin_properties();

			/**
			 * Initiates and load hooks.
			 */
			$this->load_hooks();
		}

		/**
		 * Defining constants.
		 */
		public function define_plugin_properties() {
			define( 'WPKD_VERSION', '1.6.0' );
			define( 'WPKD_PLUGIN_FILE', __FILE__ );
			define( 'WPKD_PLUGIN_DIR', __DIR__ . '/kint-debugger/' );
			add_action( 'plugins_loaded', array( $this, 'load_wp_dependent_properties' ), 1 );
		}

		/**
		 * Define wp dependent properties.
		 */
		public function load_wp_dependent_properties() {
			define( 'WPKD_PLUGIN_URL', untrailingslashit( plugin_dir_url( WPKD_PLUGIN_FILE ) ) . '/kint-debugger' );
			define( 'WPKD_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		}

		/**
		 * Adding actions.
		 */
		public function load_hooks() {
			/**
			 * Initialize Localization.
			 */
			add_action( 'init', array( $this, 'localization' ) );
			add_action( 'plugins_loaded', array( $this, 'load_classes' ), 1 );
			require_once WPKD_PLUGIN_DIR . 'functions.php';
		}

		/**
		 * Loading plugin text domain.
		 */
		public function localization() {
			load_plugin_textdomain( 'wp_kint_debug', false, __DIR__ . '/languages/' );
		}

		/**
		 * Loading classes.
		 */
		public function load_classes() {
			/**
			 * Loads the Admin file.
			 */
			include WPKD_PLUGIN_DIR . 'class-wpkd-admin.php';
			$this->admin = WPKD_Admin::get_instance();
		}

		/**
		 * Function to create a new instance.
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}
	}

	WPKD_Core::get_instance();
}
