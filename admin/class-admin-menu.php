<?php
/**
 * Admin menu and pages.
 *
 * @package Art_Starter
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Art_Starter_Admin_Menu
 */
class Art_Starter_Admin_Menu {

	const MENU_SLUG = ART_STARTER_ADMIN_MENU_SLUG;

	/**
	 * Register hooks.
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'register_menu' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_filter( 'plugin_action_links_' . ART_STARTER_PLUGIN_BASENAME, array( __CLASS__, 'plugin_action_links' ) );
		add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta_forge' ), 10, 2 );
		add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta_strip_details' ), 100, 2 );
	}

	/**
	 * Register admin menu item.
	 */
	public static function register_menu() {
		add_options_page(
			__( 'ART Starter', 'art-starter' ),
			__( 'ART Starter', 'art-starter' ),
			'manage_options',
			self::MENU_SLUG,
			array( 'Art_Starter_Admin_Settings', 'render_page' )
		);
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public static function enqueue_assets( $hook ) {
		if ( 'settings_page_' . self::MENU_SLUG !== $hook ) {
			return;
		}

		wp_enqueue_style(
			'art-starter-admin',
			ART_STARTER_PLUGIN_URL . 'assets/css/admin.css',
			array(),
			ART_STARTER_VERSION
		);

		$tab = Art_Starter_Admin_Settings::get_current_tab();

		if ( Art_Starter_Admin_Settings::TAB_HOMEPAGE === $tab ) {
			self::enqueue_homepage_assets();
		}

		if ( Art_Starter_Admin_Settings::TAB_SETUP === $tab ) {
			self::enqueue_setup_assets();
		}

		if ( Art_Starter_Admin_Settings::TAB_NOT_FOUND === $tab ) {
			self::enqueue_not_found_assets();
		}
	}

	/**
	 * Enqueue homepage tab assets.
	 */
	private static function enqueue_homepage_assets() {
		Art_Starter_Homepage::register_template_styles();
		wp_enqueue_style( 'art-starter-homepage-template' );

		foreach ( Art_Starter_Homepage::get_theme_template_slugs() as $theme_slug ) {
			wp_enqueue_style( Art_Starter_Homepage::get_template_style_handle( $theme_slug ) );
		}

		wp_enqueue_style(
			'art-starter-public',
			ART_STARTER_PLUGIN_URL . 'assets/css/public.css',
			array( 'art-starter-homepage-template' ),
			ART_STARTER_VERSION
		);

		wp_enqueue_media();

		wp_enqueue_script(
			'art-starter-admin-homepage',
			ART_STARTER_PLUGIN_URL . 'assets/js/admin-homepage.js',
			array( 'jquery' ),
			ART_STARTER_VERSION,
			true
		);

		wp_localize_script(
			'art-starter-admin-homepage',
			'artStarterHomepageAdmin',
			array(
				'optionName'           => Art_Starter_Homepage::OPTION,
				'defaultTemplate'      => Art_Starter_Homepage::get_default_template(),
				'icons'                => Art_Starter_Icons::get_for_js(),
				'linkArrowSvg'         => Art_Starter_Homepage::get_link_arrow_markup(),
				'linkDefaultIcon'      => Art_Starter_Icons::DEFAULT_LINK_ICON,
				'iconCategories'       => Art_Starter_Icons::get_category_labels(),
				'socialNetworks'       => Art_Starter_Icons::get_social_networks(),
				'maxSocials'           => Art_Starter_Homepage::MAX_SOCIAL_ITEMS,
				'iconPickerCategories' => implode( ',', Art_Starter_Icons::get_picker_categories() ),
				'strings'              => array(
					'selectImage'            => __( 'Выбрать изображение', 'art-starter' ),
					'changeImage'            => __( 'Заменить изображение', 'art-starter' ),
					'removeImage'            => __( 'Удалить', 'art-starter' ),
					'addLink'                => __( 'Добавить ссылку', 'art-starter' ),
					'removeLink'             => __( 'Удалить', 'art-starter' ),
					'linkText'               => __( 'Текст ссылки', 'art-starter' ),
					'linkUrl'                => __( 'URL', 'art-starter' ),
					'linkUrlPlaceholder'     => __( 'example.com или https://...', 'art-starter' ),
					'removeLinkAria'         => __( 'Удалить ссылку', 'art-starter' ),
					'addSocial'              => __( 'Добавить соцсеть', 'art-starter' ),
					'removeSocial'           => __( 'Удалить', 'art-starter' ),
					'removeSocialAria'       => __( 'Удалить соцсеть', 'art-starter' ),
					'socialNetwork'          => __( 'Соцсеть', 'art-starter' ),
					'socialUrl'              => __( 'Ссылка', 'art-starter' ),
					'socialSelect'           => __( '— выберите —', 'art-starter' ),
					'socialEmailPlaceholder' => __( 'name@example.com', 'art-starter' ),
					'moveUp'                 => __( 'Выше', 'art-starter' ),
					'moveDown'               => __( 'Ниже', 'art-starter' ),
					'selectIcon'             => __( 'Выбрать иконку', 'art-starter' ),
					'resetIcon'              => __( 'Сбросить', 'art-starter' ),
					'noIcon'                 => __( 'Без иконки', 'art-starter' ),
					'noIconShort'            => __( 'Без', 'art-starter' ),
					'close'                  => __( 'Закрыть', 'art-starter' ),
				),
			)
		);
	}

	/**
	 * Enqueue setup tab assets.
	 */
	private static function enqueue_setup_assets() {
		wp_enqueue_media();

		wp_enqueue_script(
			'art-starter-admin-initial-setup',
			ART_STARTER_PLUGIN_URL . 'assets/js/admin-initial-setup.js',
			array( 'jquery' ),
			ART_STARTER_VERSION,
			true
		);

		wp_localize_script(
			'art-starter-admin-initial-setup',
			'artStarterInitialSetupAdmin',
			array(
				'strings' => array(
					'selectFavicon' => __( 'Выбрать фавикон', 'art-starter' ),
					'changeFavicon' => __( 'Заменить фавикон', 'art-starter' ),
					'removeFavicon' => __( 'Удалить', 'art-starter' ),
				),
			)
		);
	}

	/**
	 * Enqueue 404 tab assets.
	 */
	private static function enqueue_not_found_assets() {
		Art_Starter_Not_Found::register_template_styles();
		wp_enqueue_style( 'art-starter-not-found-template' );

		foreach ( Art_Starter_Not_Found::get_theme_template_slugs() as $theme_slug ) {
			wp_enqueue_style( Art_Starter_Not_Found::get_template_style_handle( $theme_slug ) );
		}

		wp_enqueue_media();

		wp_enqueue_script(
			'art-starter-admin-not-found',
			ART_STARTER_PLUGIN_URL . 'assets/js/admin-not-found.js',
			array( 'jquery' ),
			ART_STARTER_VERSION,
			true
		);

		wp_localize_script(
			'art-starter-admin-not-found',
			'artStarterNotFoundAdmin',
			array(
				'optionName'           => Art_Starter_Not_Found::OPTION,
				'maxExtraButtons'      => Art_Starter_Not_Found::MAX_EXTRA_BUTTONS,
				'primaryDefaultIcon'   => Art_Starter_Not_Found::DEFAULT_PRIMARY_ICON,
				'extraDefaultIcon'     => Art_Starter_Not_Found::DEFAULT_EXTRA_ICON,
				'defaultTemplate'      => Art_Starter_Not_Found::get_default_template(),
				'homeUrl'              => home_url( '/' ),
				'icons'                => Art_Starter_Icons::get_for_js(),
				'iconCategories'       => Art_Starter_Icons::get_category_labels(),
				'iconPickerCategories' => implode( ',', Art_Starter_Icons::get_picker_categories() ),
				'strings'              => array(
					'selectImage'          => __( 'Выбрать изображение', 'art-starter' ),
					'changeImage'          => __( 'Заменить изображение', 'art-starter' ),
					'removeImage'          => __( 'Удалить', 'art-starter' ),
					'primaryButtonDefault' => __( 'Вернуться на главную', 'art-starter' ),
					'noIcon'               => __( 'Без иконки', 'art-starter' ),
					'noIconShort'          => __( 'Без', 'art-starter' ),
					'close'                => __( 'Закрыть', 'art-starter' ),
				),
			)
		);
	}

	/**
	 * Add settings link on the plugins list page.
	 *
	 * @param array<int, string> $links Plugin action links.
	 * @return array<int, string>
	 */
	public static function plugin_action_links( $links ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return $links;
		}

		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			esc_url( Art_Starter_Admin_Settings::get_tab_url( Art_Starter_Admin_Settings::TAB_SETUP ) ),
			esc_html__( 'Настройки', 'art-starter' )
		);

		return array_merge( array( $settings_link ), $links );
	}

	/**
	 * Add author materials link on plugins page (before PUC «Check for updates»).
	 *
	 * @param array<int, string> $links Plugin row meta links.
	 * @param string             $file  Plugin basename.
	 * @return array<int, string>
	 */
	public static function plugin_row_meta_forge( $links, $file ) {
		if ( ART_STARTER_PLUGIN_BASENAME !== $file ) {
			return $links;
		}

		$links[] = sprintf(
			'<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
			esc_url( ART_STARTER_AUTHOR_URL ),
			esc_html__( 'Больше материалов автора', 'art-starter' )
		);

		return $links;
	}

	/**
	 * Remove PUC «View details» link from plugin row meta.
	 *
	 * @param array<int, string> $links Plugin row meta links.
	 * @param string             $file  Plugin basename.
	 * @return array<int, string>
	 */
	public static function plugin_row_meta_strip_details( $links, $file ) {
		if ( ART_STARTER_PLUGIN_BASENAME !== $file ) {
			return $links;
		}

		return array_values(
			array_filter(
				$links,
				static function ( $link ) {
					return false === strpos( $link, 'open-plugin-details-modal' )
						&& false === strpos( $link, 'plugin-install.php?tab=plugin-information' );
				}
			)
		);
	}
}
