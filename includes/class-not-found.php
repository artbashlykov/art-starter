<?php
/**
 * Custom 404 page settings and helpers.
 *
 * @package Art_Starter
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Art_Starter_Not_Found
 */
class Art_Starter_Not_Found {

	const OPTION = 'art_starter_not_found';

	const MAX_EXTRA_BUTTONS = 2;

	const DEFAULT_PRIMARY_ICON = 'home';

	const DEFAULT_EXTRA_ICON = 'link';

	/**
	 * Register hooks.
	 */
	public static function init() {
		add_action( 'template_redirect', array( __CLASS__, 'maybe_render_not_found' ), 2 );
	}

	/**
	 * Whether custom ART Starter 404 is enabled in settings.
	 *
	 * @return bool
	 */
	public static function is_active_custom_not_found() {
		$settings = self::get_all();

		return ! empty( $settings['use_custom_not_found'] );
	}

	/**
	 * Whether the ART Starter 404 page should ignore the active theme styles.
	 *
	 * @return bool
	 */
	public static function should_isolate_from_theme() {
		return ! is_admin() && is_404() && self::is_active_custom_not_found();
	}

	/**
	 * @return string
	 */
	public static function get_default_template() {
		return Art_Starter_Homepage::get_default_template();
	}

	/**
	 * @return array<string, string>
	 */
	public static function get_templates() {
		return Art_Starter_Homepage::get_templates();
	}

	/**
	 * @return array<int, string>
	 */
	public static function get_theme_template_slugs() {
		return Art_Starter_Homepage::get_theme_template_slugs();
	}

	/**
	 * @param string $template Template slug.
	 * @return string
	 */
	public static function get_template_style_handle( $template ) {
		return 'art-starter-not-found-template-' . sanitize_key( (string) $template );
	}

	/**
	 * @param string $template Template slug.
	 * @return string
	 */
	public static function get_template_preview_frame_class( $template ) {
		$class    = 'art-starter-not-found-preview__frame';
		$template = self::is_valid_template( $template ) ? sanitize_key( $template ) : self::get_default_template();

		if ( Art_Starter_Homepage::TEMPLATE_CLASSIC !== $template ) {
			$class .= ' art-starter-not-found-preview__frame--' . $template;
		}

		return $class;
	}

	/**
	 * @param string $template Template slug.
	 * @return bool
	 */
	public static function is_valid_template( $template ) {
		$template = sanitize_key( (string) $template );

		return array_key_exists( $template, self::get_templates() );
	}

	/**
	 * @param string $template Template slug.
	 * @return string
	 */
	public static function get_template_body_class( $template ) {
		$template = self::is_valid_template( $template ) ? sanitize_key( $template ) : self::get_default_template();

		return 'art-starter-not-found art-starter-not-found--' . $template;
	}

	/**
	 * Register 404 template styles.
	 */
	public static function register_template_styles() {
		wp_register_style(
			'art-starter-not-found-reset',
			ART_STARTER_PLUGIN_URL . 'assets/css/not-found-reset.css',
			array(),
			ART_STARTER_VERSION
		);

		wp_register_style(
			'art-starter-not-found-template',
			ART_STARTER_PLUGIN_URL . 'assets/css/not-found-template.css',
			array( 'art-starter-not-found-reset' ),
			ART_STARTER_VERSION
		);

		$theme_files = array(
			Art_Starter_Homepage::TEMPLATE_LIGHT_BLUE => 'not-found-template-light-blue.css',
			Art_Starter_Homepage::TEMPLATE_PURPLE     => 'not-found-template-purple.css',
			Art_Starter_Homepage::TEMPLATE_ORANGE     => 'not-found-template-orange.css',
			Art_Starter_Homepage::TEMPLATE_GREEN      => 'not-found-template-green.css',
			Art_Starter_Homepage::TEMPLATE_PINK       => 'not-found-template-pink.css',
			Art_Starter_Homepage::TEMPLATE_BLACK      => 'not-found-template-black.css',
		);

		foreach ( $theme_files as $slug => $file ) {
			wp_register_style(
				self::get_template_style_handle( $slug ),
				ART_STARTER_PLUGIN_URL . 'assets/css/' . $file,
				array( 'art-starter-not-found-template' ),
				ART_STARTER_VERSION
			);
		}
	}

	/**
	 * @param string $template Template slug.
	 */
	public static function enqueue_template_styles( $template ) {
		self::register_template_styles();

		wp_enqueue_style( 'art-starter-not-found-template' );

		$template = sanitize_key( (string) $template );

		if ( Art_Starter_Homepage::TEMPLATE_CLASSIC !== $template && self::is_valid_template( $template ) ) {
			wp_enqueue_style( self::get_template_style_handle( $template ) );
		}
	}

	/**
	 * @return array<string, string>
	 */
	public static function get_default_primary_button() {
		return array(
			'label' => __( 'Вернуться на главную', 'art-starter' ),
			'url'   => '',
			'icon'  => self::DEFAULT_PRIMARY_ICON,
		);
	}

	/**
	 * @return array<string, string>
	 */
	public static function get_default_extra_button() {
		return array(
			'label' => '',
			'url'   => '',
			'icon'  => self::DEFAULT_EXTRA_ICON,
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	public static function get_defaults() {
		return array(
			'use_custom_not_found' => false,
			'template'             => self::get_default_template(),
			'image_url'            => '',
			'code'                 => '404',
			'title'                => __( 'Страница не найдена', 'art-starter' ),
			'buttons'              => array(
				self::get_default_primary_button(),
			),
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	public static function get_all() {
		$stored = get_option( self::OPTION, array() );

		if ( ! is_array( $stored ) ) {
			$stored = array();
		}

		$settings = array_replace_recursive( self::get_defaults(), $stored );

		return self::normalize_settings( $settings );
	}

	/**
	 * @param array<string, mixed> $settings Settings array.
	 * @return array<string, mixed>
	 */
	private static function normalize_settings( $settings ) {
		if ( ! is_array( $settings ) ) {
			return self::get_defaults();
		}

		if ( empty( $settings['buttons'] ) || ! is_array( $settings['buttons'] ) ) {
			$settings['buttons'] = array( self::get_default_primary_button() );
		}

		$buttons = array();
		foreach ( $settings['buttons'] as $index => $button ) {
			if ( ! is_array( $button ) ) {
				continue;
			}

			$is_primary = 0 === (int) $index;
			$buttons[]  = self::sanitize_button_item( $button, $is_primary );
		}

		if ( empty( $buttons ) ) {
			$buttons[] = self::get_default_primary_button();
		}

		$settings['buttons'] = $buttons;

		return $settings;
	}

	/**
	 * @param string $url         Button URL.
	 * @param bool   $is_primary  Whether this is the primary home button.
	 * @return string
	 */
	public static function resolve_button_url( $url, $is_primary = false ) {
		$url = (string) $url;

		if ( '' === $url && $is_primary ) {
			return home_url( '/' );
		}

		if ( '' === $url ) {
			return '';
		}

		return Art_Starter_Homepage::normalize_external_url( $url );
	}

	/**
	 * @param mixed $input Raw input.
	 * @return array<string, mixed>
	 */
	public static function sanitize( $input ) {
		$input = is_array( $input ) ? $input : array();
		$out   = self::get_defaults();

		$template = isset( $input['template'] ) ? sanitize_key( (string) $input['template'] ) : self::get_default_template();
		if ( ! self::is_valid_template( $template ) ) {
			$template = self::get_default_template();
		}

		$out['use_custom_not_found'] = ! empty( $input['use_custom_not_found'] );
		$out['template']             = $template;
		$out['image_url']            = isset( $input['image_url'] ) ? esc_url_raw( (string) $input['image_url'] ) : '';
		$out['code']                 = isset( $input['code'] ) ? sanitize_text_field( (string) $input['code'] ) : '404';
		$out['title']                = isset( $input['title'] ) ? sanitize_text_field( (string) $input['title'] ) : '';
		$out['buttons']              = self::parse_buttons_from_input( $input );

		return $out;
	}

	/**
	 * @param mixed $input Raw settings input.
	 * @return array<int, array<string, string>>
	 */
	private static function parse_buttons_from_input( $input ) {
		$buttons = array();
		$raw     = isset( $input['buttons'] ) && is_array( $input['buttons'] ) ? $input['buttons'] : array();

		$primary = isset( $raw[0] ) && is_array( $raw[0] ) ? $raw[0] : array();
		$buttons[] = self::sanitize_button_item( $primary, true );

		$extra_count = 0;
		$raw_count   = count( $raw );

		for ( $i = 1; $i < $raw_count && $extra_count < self::MAX_EXTRA_BUTTONS; $i++ ) {
			if ( ! is_array( $raw[ $i ] ) ) {
				continue;
			}

			$item = self::sanitize_button_item( $raw[ $i ], false );

			if ( '' === $item['label'] && '' === $item['url'] ) {
				continue;
			}

			$buttons[] = $item;
			++$extra_count;
		}

		return $buttons;
	}

	/**
	 * @param array<string, mixed> $item       Button item.
	 * @param bool                 $is_primary Whether this is the primary button.
	 * @return array<string, string>
	 */
	private static function sanitize_button_item( $item, $is_primary ) {
		$defaults = $is_primary ? self::get_default_primary_button() : self::get_default_extra_button();

		$label = isset( $item['label'] ) ? sanitize_text_field( (string) $item['label'] ) : '';
		if ( $is_primary && '' === $label ) {
			$label = $defaults['label'];
		}

		$url = isset( $item['url'] ) ? Art_Starter_Homepage::normalize_external_url( (string) $item['url'] ) : '';

		$icon = Art_Starter_Icons::sanitize_slug(
			isset( $item['icon'] ) ? (string) $item['icon'] : '',
			Art_Starter_Icons::get_picker_categories(),
			true
		);
		if ( '' === $icon ) {
			$icon = $defaults['icon'];
		}

		return array(
			'label' => $label,
			'url'   => $url,
			'icon'  => $icon,
		);
	}

	/**
	 * Render ART Starter custom 404 page.
	 */
	public static function maybe_render_not_found() {
		if ( is_admin() || ! is_404() || ! self::is_active_custom_not_found() ) {
			return;
		}

		status_header( 404 );
		nocache_headers();

		$settings = self::get_all();
		$template = isset( $settings['template'] ) ? (string) $settings['template'] : self::get_default_template();

		if ( ! self::is_valid_template( $template ) ) {
			$template = self::get_default_template();
		}

		self::enqueue_template_styles( $template );

		$view = ART_STARTER_PLUGIN_DIR . 'public/views/not-found.php';
		if ( ! is_readable( $view ) ) {
			return;
		}

		include $view;
		exit;
	}
}
