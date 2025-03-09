<?php
/**
 * Functions.php
 *
 * @package Kint Debugger
 */

defined( 'ABSPATH' ) || exit;

/**
 * WordPress PC debugging function.
 *
 * @param mixed $data Data that needs to add to debug.
 */
function wkd( $data ) {
	if ( class_exists( 'Kint' ) ) {
		Kint::dump( $data );
	}
}
