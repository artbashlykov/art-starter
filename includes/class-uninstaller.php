<?php
/**
 * Plugin uninstall cleanup.
 *
 * @package Art_Starter
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Art_Starter_Uninstaller
 */
class Art_Starter_Uninstaller {

	const DELETE_DATA_OPTION = 'art_starter_delete_data_on_uninstall';
	const PUC_OPTION         = 'external_updates-art-starter';
	const CRON_HOOK          = 'puc_cron_check_updates-art-starter';
	const PUC_ERROR_TRANSIENT = 'puc_manual_check_errors-art-starter';

	/**
	 * Run uninstall cleanup when the admin opted in.
	 */
	public static function run() {
		if ( ! self::is_delete_data_enabled() ) {
			return;
		}

		self::clear_cron();
		self::delete_plugin_options();
		self::delete_transients();
	}

	/**
	 * Whether the site admin enabled data removal on uninstall.
	 *
	 * @return bool
	 */
	public static function is_delete_data_enabled() {
		return 'yes' === get_option( self::DELETE_DATA_OPTION, 'no' );
	}

	/**
	 * Clear scheduled Plugin Update Checker events.
	 */
	private static function clear_cron() {
		$timestamp = wp_next_scheduled( self::CRON_HOOK );

		while ( $timestamp ) {
			wp_unschedule_event( $timestamp, self::CRON_HOOK );
			$timestamp = wp_next_scheduled( self::CRON_HOOK );
		}
	}

	/**
	 * Delete plugin options from the database.
	 *
	 * Removes homepage and 404 settings, uninstall preference, and PUC state.
	 * Does not touch WordPress core options changed through ART Starter setup.
	 */
	private static function delete_plugin_options() {
		global $wpdb;

		delete_site_option( self::PUC_OPTION );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Bulk cleanup during uninstall.
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
				$wpdb->esc_like( 'art_starter_' ) . '%'
			)
		);
	}

	/**
	 * Delete plugin transients and site transients.
	 */
	private static function delete_transients() {
		global $wpdb;

		delete_site_transient( self::PUC_ERROR_TRANSIENT );

		$like_transient = $wpdb->esc_like( '_transient_art_starter_' ) . '%';
		$like_timeout   = $wpdb->esc_like( '_transient_timeout_art_starter_' ) . '%';
		$like_site      = $wpdb->esc_like( '_site_transient_art_starter_' ) . '%';
		$like_site_to   = $wpdb->esc_like( '_site_transient_timeout_art_starter_' ) . '%';
		$like_puc_site  = $wpdb->esc_like( '_site_transient_puc_' ) . '%';
		$like_puc_site_to = $wpdb->esc_like( '_site_transient_timeout_puc_' ) . '%';

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Bulk cleanup during uninstall.
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s OR option_name LIKE %s OR option_name LIKE %s OR option_name LIKE %s OR option_name LIKE %s",
				$like_transient,
				$like_timeout,
				$like_site,
				$like_site_to,
				$like_puc_site,
				$like_puc_site_to
			)
		);
	}
}
