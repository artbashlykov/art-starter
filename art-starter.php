<?php
/**
 * Plugin Name:       ART Starter
 * Description:       Быстрая настройка нового сайта WordPress: простая главная страница-визитка со ссылками и страница 404.
 * Version:           1.0.3
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Арт Башлыков
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       art-starter
 * Domain Path:       /languages
 *
 * @package Art_Starter
 */

defined( 'ABSPATH' ) || exit;

define( 'ART_STARTER_VERSION', '1.0.3' );
define( 'ART_STARTER_ADMIN_MENU_SLUG', 'art-starter' );
define( 'ART_STARTER_AUTHOR_URL', 'https://forge.artbashlykov.ru' );
define( 'ART_STARTER_PLUGIN_FILE', __FILE__ );
define( 'ART_STARTER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ART_STARTER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'ART_STARTER_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

add_filter( 'puc_view_details_link-' . ART_STARTER_ADMIN_MENU_SLUG, '__return_empty_string' );

require_once ART_STARTER_PLUGIN_DIR . 'includes/class-activator.php';
require_once ART_STARTER_PLUGIN_DIR . 'includes/class-deactivator.php';
require_once ART_STARTER_PLUGIN_DIR . 'includes/class-plugin.php';

register_activation_hook( ART_STARTER_PLUGIN_FILE, array( 'Art_Starter_Activator', 'activate' ) );
register_deactivation_hook( ART_STARTER_PLUGIN_FILE, array( 'Art_Starter_Deactivator', 'deactivate' ) );

/**
 * Returns the main plugin instance.
 *
 * @return Art_Starter_Plugin
 */
function art_starter() {
	return Art_Starter_Plugin::instance();
}

art_starter()->run();
