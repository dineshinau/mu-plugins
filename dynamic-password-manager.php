<?php
/**
 * Plugin Name:       Dyanmic Password Manager
 * Plugin URI:        https://dineshinaublog.wordpress.com/dynamic-password-manager
 * Description:       Allows user to manage thier password dyanmically.
 * Version:           1.0.1
 * Author:            Dinesh Yadav
 * Author URI:        https://dineshinaublog.wordpress.com
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       dynamic-password-manager
 * Domain Path:       /languages
 *
 * Requires at least: 5.0
 * Tested up to: 6.3
 * Requires PHP: 7.3
 *
 * @package dynamic-password-manager
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'DKDPM_Core' ) ) {
	/**
	 * Plugin core class file.
	 *
	 * Class DKDPM_Core
	 */
	class DKDPM_Core {
		/**
		 * Instance variable.
		 *
		 * @var $instance
		 */
		public static $instance = null;

		/**
		 * DKQPS_Core constructor.
		 */
		public function __construct() {
			/**
			 * Initiates and load hooks.
			 */
			$this->load_hooks();
		}

		/**
		 * Defining constants.
		 */
		public function define_plugin_properties() {
			add_action( 'plugins_loaded', array( $this, 'load_wp_dependent_properties' ), 1 );
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
		}

		/**
		 * Loading plugin text domain.
		 */
		public function localization() {
			load_plugin_textdomain( 'dynamic-password-manager', false, __DIR__ . '/dynamic-password-manager/languages/' );
		}

		/**
		 * Loading classes.
		 */
		public function load_classes() {
			/**
			 * Loads the Admin file.
			 */
			include __DIR__ . '/dynamic-password-manager/admin/class-dkdpm-admin.php';
			DKDPM_Admin::get_instance();
		}

		/**
		 * Function to create a new instance.
		 *
		 * @return instance
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}
	}
}
/**
 * Initiating the class object.
 */
if ( ! function_exists( 'dkdpm_core' ) ) {
	/**
	 * Creating a new instance.
	 *
	 * @return DKDPM_Core|null
	 */
	function dkdpm_core() {
		return DKDPM_Core::get_instance();
	}
}
dkdpm_core();
