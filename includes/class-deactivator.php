<?php
/**
 * Plugin deactivation.
 *
 * @package Art_Starter
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Art_Starter_Deactivator
 */
class Art_Starter_Deactivator {

	/**
	 * Run on plugin deactivation.
	 */
	public static function deactivate() {
		flush_rewrite_rules();
	}
}
