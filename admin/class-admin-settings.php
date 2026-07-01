<?php
/**
 * Admin settings hub with tabs.
 *
 * @package Art_Starter
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Admin tab routing uses sanitized GET parameters.

/**
 * Class Art_Starter_Admin_Settings
 */
class Art_Starter_Admin_Settings {

	const PAGE = ART_STARTER_ADMIN_MENU_SLUG;

	const TAB_SETUP     = 'setup';
	const TAB_HOMEPAGE  = 'homepage';
	const TAB_NOT_FOUND = 'not-found';

	/**
	 * Register hooks.
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'redirect_admin_menu_url' ) );
	}

	/**
	 * @return array<string, string>
	 */
	public static function get_tabs() {
		return array(
			self::TAB_SETUP     => __( 'Настройки', 'art-starter' ),
			self::TAB_HOMEPAGE  => __( 'Главная страница', 'art-starter' ),
			self::TAB_NOT_FOUND => __( 'Страница 404', 'art-starter' ),
		);
	}

	/**
	 * Redirect old top-level menu URL to Settings submenu.
	 */
	public static function redirect_admin_menu_url() {
		global $pagenow;

		if ( 'admin.php' !== $pagenow ) {
			return;
		}

		$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';

		if ( self::PAGE !== $page ) {
			return;
		}

		$query = array(
			'page' => self::PAGE,
		);

		if ( isset( $_GET['tab'] ) ) {
			$query['tab'] = sanitize_key( wp_unslash( $_GET['tab'] ) );
		}

		if ( ! empty( $_GET['settings-updated'] ) ) {
			$query['settings-updated'] = 'true';
		}

		if ( ! empty( $_GET['applied'] ) ) {
			$query['applied'] = sanitize_text_field( wp_unslash( $_GET['applied'] ) );
		}

		wp_safe_redirect( self::get_page_url( $query ) );
		exit;
	}

	/**
	 * Build admin URL for the settings page.
	 *
	 * @param array<string, string> $args Query arguments.
	 * @return string
	 */
	public static function get_page_url( $args = array() ) {
		$args = array_merge(
			array(
				'page' => self::PAGE,
			),
			$args
		);

		return add_query_arg( $args, admin_url( 'options-general.php' ) );
	}

	/**
	 * Render settings hub page.
	 */
	public static function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$active_tab = self::get_current_tab();

		include ART_STARTER_PLUGIN_DIR . 'admin/views/page-settings.php';
	}

	/**
	 * Render tab navigation and top save action.
	 *
	 * @param string $current_tab Active tab slug.
	 */
	public static function render_tabs( $current_tab ) {
		$form_id    = self::get_active_form_id( $current_tab );
		$save_label = self::get_save_button_label( $current_tab );

		echo '<div class="art-starter-admin-tabs-bar">';

		echo '<nav class="nav-tab-wrapper art-starter-admin-tabs" aria-label="' . esc_attr__( 'Вкладки', 'art-starter' ) . '">';

		foreach ( self::get_tabs() as $tab_id => $label ) {
			$url   = self::get_tab_url( $tab_id );
			$class = 'nav-tab';

			if ( $current_tab === $tab_id ) {
				$class .= ' nav-tab-active';
			}

			printf(
				'<a href="%s" class="%s">%s</a>',
				esc_url( $url ),
				esc_attr( $class ),
				esc_html( $label )
			);
		}

		echo '</nav>';

		if ( '' !== $form_id ) {
			printf(
				'<button type="submit" form="%s" class="button button-primary art-starter-admin-tabs-bar__save">%s</button>',
				esc_attr( $form_id ),
				esc_html( $save_label )
			);
		}

		echo '</div>';
	}

	/**
	 * @param string $tab Tab slug.
	 * @return string
	 */
	public static function get_active_form_id( $tab ) {
		switch ( $tab ) {
			case self::TAB_HOMEPAGE:
				return 'art-starter-homepage-form';
			case self::TAB_NOT_FOUND:
				return 'art-starter-not-found-form';
			case self::TAB_SETUP:
				return 'art-starter-initial-setup-form';
		}

		return '';
	}

	/**
	 * @param string $tab Tab slug.
	 * @return string
	 */
	public static function get_save_button_label( $tab ) {
		if ( self::TAB_SETUP === $tab ) {
			return __( 'Применить настройки', 'art-starter' );
		}

		return __( 'Сохранить', 'art-starter' );
	}

	/**
	 * Build admin URL for a settings tab.
	 *
	 * @param string $tab Tab slug.
	 * @return string
	 */
	public static function get_tab_url( $tab ) {
		return self::get_page_url(
			array(
				'tab' => $tab,
			)
		);
	}

	/**
	 * Resolve active tab slug.
	 *
	 * @return string
	 */
	public static function get_current_tab() {
		$allowed = array_keys( self::get_tabs() );
		$tab     = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : self::TAB_SETUP;

		if ( ! in_array( $tab, $allowed, true ) ) {
			return self::TAB_SETUP;
		}

		return $tab;
	}
}
