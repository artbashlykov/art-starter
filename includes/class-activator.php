<?php
/**
 * Plugin activation.
 *
 * @package Art_Starter
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Art_Starter_Activator
 */
class Art_Starter_Activator {

	/**
	 * Run on plugin activation.
	 */
	public static function activate() {
		if ( false === get_option( Art_Starter_Homepage::OPTION, false ) ) {
			add_option( Art_Starter_Homepage::OPTION, Art_Starter_Homepage::get_defaults() );
		}

		if ( false === get_option( Art_Starter_Not_Found::OPTION, false ) ) {
			add_option( Art_Starter_Not_Found::OPTION, Art_Starter_Not_Found::get_defaults() );
		}

		flush_rewrite_rules();
	}
}
