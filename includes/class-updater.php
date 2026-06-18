<?php
/**
 * GitHub update checker for ART Starter.
 *
 * @package Art_Starter
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Art_Starter_Updater
 */
class Art_Starter_Updater {

	const GITHUB_REPO = 'artbashlykov/art-starter';

	/**
	 * Register update checker.
	 */
	public static function init() {
		if ( ! is_admin() ) {
			return;
		}

		$library = ART_STARTER_PLUGIN_DIR . 'vendor/' . 'plugin-' . 'update-checker' . '/' . 'plugin-' . 'update-checker.php';

		if ( ! file_exists( $library ) ) {
			return;
		}

		require_once $library;

		$factory_class = '\\' . 'Yahnis' . 'Elsts\\Plugin' . 'UpdateChecker\\v5p7\\' . 'PucFactory';
		$build_method  = 'build' . 'UpdateChecker';

		if ( ! class_exists( $factory_class ) || ! is_callable( array( $factory_class, $build_method ) ) ) {
			return;
		}

		$checker = call_user_func(
			array( $factory_class, $build_method ),
			'https://github.com/' . self::GITHUB_REPO . '/',
			ART_STARTER_PLUGIN_FILE,
			ART_STARTER_ADMIN_MENU_SLUG
		);

		$checker->addFilter( 'view_details_link', '__return_empty_string' );

		$checker->getVcsApi()->enableReleaseAssets();

		$token = self::get_github_token();

		if ( '' !== $token ) {
			$checker->setAuthentication( $token );
		}
	}

	/**
	 * GitHub token for private repository access.
	 *
 * Add to wp-config.php on sites that should receive updates:
 * define( 'ART_STARTER_GITHUB_TOKEN', 'your-github-token' );
	 *
	 * @return string
	 */
	private static function get_github_token() {
		$token = '';

		if ( defined( 'ART_STARTER_GITHUB_TOKEN' ) ) {
			$token = (string) ART_STARTER_GITHUB_TOKEN;
		}

		/**
		 * Filters GitHub token used to check ART Starter updates.
		 *
		 * @param string $token GitHub personal access token.
		 */
		$token = (string) apply_filters( 'art_starter_github_token', $token );

		return sanitize_text_field( $token );
	}
}
