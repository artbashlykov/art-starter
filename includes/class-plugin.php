<?php
/**
 * Main plugin bootstrap.
 *
 * @package Art_Starter
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Art_Starter_Plugin
 */
class Art_Starter_Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var Art_Starter_Plugin|null
	 */
	private static $instance = null;

	/**
	 * Whether admin modules were initialized.
	 *
	 * @var bool
	 */
	private static $admin_initialized = false;

	/**
	 * @return Art_Starter_Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->load_dependencies();
	}

	/**
	 * Load required class files.
	 */
	private function load_dependencies() {
		require_once ART_STARTER_PLUGIN_DIR . 'includes/class-settings.php';
		require_once ART_STARTER_PLUGIN_DIR . 'includes/class-icons.php';
		require_once ART_STARTER_PLUGIN_DIR . 'includes/class-homepage.php';
		require_once ART_STARTER_PLUGIN_DIR . 'includes/class-not-found.php';
		require_once ART_STARTER_PLUGIN_DIR . 'includes/class-updater.php';
		require_once ART_STARTER_PLUGIN_DIR . 'public/class-public.php';

		if ( is_admin() ) {
			require_once ART_STARTER_PLUGIN_DIR . 'includes/class-initial-setup.php';
			require_once ART_STARTER_PLUGIN_DIR . 'admin/class-admin-settings.php';
			require_once ART_STARTER_PLUGIN_DIR . 'admin/class-admin-initial-setup.php';
			require_once ART_STARTER_PLUGIN_DIR . 'admin/class-admin-homepage.php';
			require_once ART_STARTER_PLUGIN_DIR . 'admin/class-admin-not-found.php';
			require_once ART_STARTER_PLUGIN_DIR . 'admin/class-admin-menu.php';
		}
	}

	/**
	 * Register hooks and initialize modules.
	 */
	public function run() {
		add_action( 'init', array( $this, 'init' ) );
		$this->init_admin();
	}

	/**
	 * Initialize plugin modules.
	 */
	public function init() {
		Art_Starter_Homepage::init();
		Art_Starter_Not_Found::init();
		Art_Starter_Public::init();
	}

	/**
	 * Initialize admin modules.
	 */
	public function init_admin() {
		if ( self::$admin_initialized || ! is_admin() ) {
			return;
		}

		self::$admin_initialized = true;

		Art_Starter_Updater::init();
		Art_Starter_Admin_Settings::init();
		Art_Starter_Admin_Initial_Setup::init();
		Art_Starter_Admin_Homepage::init();
		Art_Starter_Admin_Not_Found::init();
		Art_Starter_Admin_Menu::init();
	}
}
