<?php
/**
 * Plugin settings helpers.
 *
 * @package Art_Starter
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Art_Starter_Settings
 */
class Art_Starter_Settings {

	const OPTION_DELETE_DATA_ON_UNINSTALL = 'art_starter_delete_data_on_uninstall';

	/**
	 * Whether plugin data should be removed on uninstall.
	 *
	 * @return bool
	 */
	public static function should_delete_data_on_uninstall() {
		return 'yes' === get_option( self::OPTION_DELETE_DATA_ON_UNINSTALL, 'no' );
	}

	/**
	 * Persist the uninstall data removal preference.
	 *
	 * @param bool $enabled Whether to delete data on uninstall.
	 */
	public static function set_delete_data_on_uninstall( $enabled ) {
		update_option( self::OPTION_DELETE_DATA_ON_UNINSTALL, $enabled ? 'yes' : 'no', false );
	}
}
