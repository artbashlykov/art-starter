<?php
/**
 * Public front-end hooks.
 *
 * @package Art_Starter
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Art_Starter_Public
 */
class Art_Starter_Public {

	/**
	 * Register hooks.
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'strip_foreign_styles' ), 9999 );
	}

	/**
	 * Whether ART Starter should ignore active theme styles on the current request.
	 *
	 * @return bool
	 */
	public static function should_isolate_from_theme() {
		if ( is_admin() ) {
			return false;
		}

		return Art_Starter_Homepage::should_isolate_from_theme()
			|| Art_Starter_Not_Found::should_isolate_from_theme();
	}

	/**
	 * @param string $handle Style handle.
	 * @return bool
	 */
	public static function is_allowed_style_handle( $handle ) {
		$handle = sanitize_key( (string) $handle );

		if ( 0 === strpos( $handle, 'art-starter-' ) ) {
			return true;
		}

		$allowed = array(
			'admin-bar',
			'dashicons',
		);

		return in_array( $handle, $allowed, true );
	}

	/**
	 * Remove active theme and third-party styles from ART Starter front pages.
	 */
	public static function strip_foreign_styles() {
		if ( ! self::should_isolate_from_theme() ) {
			return;
		}

		global $wp_styles;

		if ( ! ( $wp_styles instanceof WP_Styles ) ) {
			return;
		}

		foreach ( array_keys( $wp_styles->registered ) as $handle ) {
			if ( self::is_allowed_style_handle( $handle ) ) {
				continue;
			}

			wp_dequeue_style( $handle );
			wp_deregister_style( $handle );
		}
	}

	/**
	 * Enqueue public assets.
	 */
	public static function enqueue_assets() {
		Art_Starter_Homepage::register_template_styles();
		Art_Starter_Not_Found::register_template_styles();

		wp_register_style(
			'art-starter-public',
			ART_STARTER_PLUGIN_URL . 'assets/css/public.css',
			array( 'art-starter-homepage-template' ),
			ART_STARTER_VERSION
		);
	}
}
