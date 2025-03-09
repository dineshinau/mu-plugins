<?php
/**
 * Admin file.
 *
 * @package Kint Debugger
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * The admin functionality of the plugin.
 *
 * @since 1.0
 */
class WPKD_Admin {
	/**
	 * Instance variable.
	 *
	 * @var $ins ;
	 */
	private static $ins = null;

	/**
	 * Initialize the class and set its properties.
	 *
	 * Constructor.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		if(file_exists(__DIR__ . '/vendor/autoload.php')) {
			require_once __DIR__ . '/vendor/autoload.php';
		}
	}

	/**
	 * Creating an instance of this class.
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( null === self::$ins ) {
			self::$ins = new self();
		}

		return self::$ins;
	}
}
