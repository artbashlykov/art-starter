<?php
/**
 * Homepage (link-in-bio) settings and helpers.
 *
 * @package Art_Starter
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Art_Starter_Homepage
 */
class Art_Starter_Homepage {

	const OPTION = 'art_starter_homepage';

	const TEMPLATE_CLASSIC     = 'classic';
	const TEMPLATE_LIGHT_BLUE  = 'light-blue';
	const TEMPLATE_PURPLE      = 'purple';
	const TEMPLATE_ORANGE      = 'orange';
	const TEMPLATE_GREEN       = 'green';
	const TEMPLATE_PINK        = 'pink';
	const TEMPLATE_BLACK       = 'black';

	/**
	 * Default homepage template for new installations.
	 *
	 * @return string
	 */
	public static function get_default_template() {
		return self::TEMPLATE_LIGHT_BLUE;
	}

	/**
	 * @return array<string, string>
	 */
	public static function get_templates() {
		return array(
			self::TEMPLATE_CLASSIC    => __( 'Светлый', 'art-starter' ),
			self::TEMPLATE_LIGHT_BLUE => __( 'Светло-синий', 'art-starter' ),
			self::TEMPLATE_PURPLE     => __( 'Фиолетовый', 'art-starter' ),
			self::TEMPLATE_ORANGE     => __( 'Оранжевый', 'art-starter' ),
			self::TEMPLATE_GREEN      => __( 'Зеленый', 'art-starter' ),
			self::TEMPLATE_PINK       => __( 'Розовый', 'art-starter' ),
			self::TEMPLATE_BLACK      => __( 'Черный', 'art-starter' ),
		);
	}

	/**
	 * Template slugs that ship a dedicated theme stylesheet.
	 *
	 * @return array<int, string>
	 */
	public static function get_theme_template_slugs() {
		return array_values(
			array_diff(
				array_keys( self::get_templates() ),
				array( self::TEMPLATE_CLASSIC )
			)
		);
	}

	/**
	 * @param string $template Template slug.
	 * @return string
	 */
	public static function get_template_style_handle( $template ) {
		return 'art-starter-homepage-template-' . sanitize_key( (string) $template );
	}

	/**
	 * @param string $template Template slug.
	 * @return string
	 */
	public static function get_template_preview_frame_class( $template ) {
		$class    = 'art-starter-homepage-preview__frame';
		$template = self::is_valid_template( $template ) ? sanitize_key( $template ) : self::get_default_template();

		if ( self::TEMPLATE_CLASSIC !== $template ) {
			$class .= ' art-starter-homepage-preview__frame--' . $template;
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

		return 'art-starter-homepage art-starter-homepage--' . $template;
	}

	/**
	 * Register homepage template styles.
	 */
	public static function register_template_styles() {
		wp_register_style(
			'art-starter-homepage-reset',
			ART_STARTER_PLUGIN_URL . 'assets/css/homepage-reset.css',
			array(),
			ART_STARTER_VERSION
		);

		wp_register_style(
			'art-starter-homepage-template',
			ART_STARTER_PLUGIN_URL . 'assets/css/homepage-template.css',
			array( 'art-starter-homepage-reset' ),
			ART_STARTER_VERSION
		);

		$theme_files = array(
			self::TEMPLATE_LIGHT_BLUE => 'homepage-template-light-blue.css',
			self::TEMPLATE_PURPLE     => 'homepage-template-purple.css',
			self::TEMPLATE_ORANGE     => 'homepage-template-orange.css',
			self::TEMPLATE_GREEN      => 'homepage-template-green.css',
			self::TEMPLATE_PINK       => 'homepage-template-pink.css',
			self::TEMPLATE_BLACK      => 'homepage-template-black.css',
		);

		foreach ( $theme_files as $slug => $file ) {
			wp_register_style(
				self::get_template_style_handle( $slug ),
				ART_STARTER_PLUGIN_URL . 'assets/css/' . $file,
				array( 'art-starter-homepage-template' ),
				ART_STARTER_VERSION
			);
		}
	}

	/**
	 * @param string $template Template slug.
	 */
	public static function enqueue_template_styles( $template ) {
		self::register_template_styles();

		wp_enqueue_style( 'art-starter-homepage-reset' );
		wp_enqueue_style( 'art-starter-homepage-template' );

		$template = sanitize_key( (string) $template );

		if ( self::TEMPLATE_CLASSIC !== $template && self::is_valid_template( $template ) ) {
			wp_enqueue_style( self::get_template_style_handle( $template ) );
		}
	}

	const MAX_SOCIAL_ITEMS = 5;

	/**
	 * Homepage content blocks that can be hidden on the front end.
	 *
	 * @return array<int, string>
	 */
	public static function get_block_keys() {
		return array(
			'profile',
			'cta',
			'links',
			'recommend',
			'socials',
		);
	}

	/**
	 * Skip auto-uncheck while plugin updates Reading options.
	 *
	 * @var bool
	 */
	private static $applying_reading_settings = false;

	/**
	 * Register hooks.
	 */
	public static function init() {
		add_action( 'update_option_' . self::OPTION, array( __CLASS__, 'on_option_updated' ), 10, 2 );
		add_action( 'update_option_show_on_front', array( __CLASS__, 'on_reading_show_on_front_changed' ), 10, 2 );
		add_action( 'update_option_page_on_front', array( __CLASS__, 'on_reading_page_on_front_changed' ), 10, 2 );
		add_action( 'template_redirect', array( __CLASS__, 'maybe_render_front_page' ), 1 );
	}

	/**
	 * Whether the ART Starter homepage should ignore the active theme styles.
	 *
	 * @return bool
	 */
	public static function should_isolate_from_theme() {
		return ! is_admin() && is_front_page() && self::is_active_as_front_page();
	}

	/**
	 * @return array<string, mixed>
	 */
	public static function get_defaults() {
		return array(
			'template'          => self::get_default_template(),
			'use_as_front_page' => false,
			'blocks'            => array(
				'profile'   => array( 'hidden' => false ),
				'cta'       => array( 'hidden' => false ),
				'links'     => array( 'hidden' => false ),
				'recommend' => array( 'hidden' => false ),
				'socials'   => array( 'hidden' => false ),
			),
			'profile'           => array(
				'avatar_url' => '',
				'name'       => '',
				'roles'      => '',
				'bio'        => '',
			),
			'cta'               => array(
				'label' => '',
				'url'   => '',
				'icon'  => '',
			),
			'links'             => array(),
			'recommend'         => array(
				'badge'        => __( 'Рекомендуем', 'art-starter' ),
				'title'        => '',
				'description'  => '',
				'button_label' => __( 'Смотреть', 'art-starter' ),
				'button_url'   => '',
				'image_url'    => '',
			),
			'socials'           => array(),
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

		return self::normalize_settings( $settings, $stored );
	}

	/**
	 * Normalize stored settings (legacy social migration, etc.).
	 *
	 * @param array<string, mixed> $settings   Merged settings array.
	 * @param array<string, mixed> $raw_stored Raw option value from the database.
	 * @return array<string, mixed>
	 */
	private static function normalize_settings( $settings, $raw_stored = array() ) {
		if ( ! is_array( $settings ) ) {
			return self::get_defaults();
		}

		if ( ! is_array( $raw_stored ) ) {
			$raw_stored = array();
		}

		if ( ! array_key_exists( 'socials', $raw_stored ) ) {
			$settings['socials'] = self::migrate_legacy_socials( $settings );
		} elseif ( ! is_array( $settings['socials'] ) ) {
			$settings['socials'] = array();
		}

		unset( $settings['social'], $settings['social_icons'] );

		return $settings;
	}

	/**
	 * Whether a homepage block is hidden on the front end.
	 *
	 * @param array<string, mixed> $settings  Homepage settings.
	 * @param string               $block_key Block key.
	 * @return bool
	 */
	public static function is_block_hidden( $settings, $block_key ) {
		$block_key = sanitize_key( (string) $block_key );

		if ( ! in_array( $block_key, self::get_block_keys(), true ) ) {
			return false;
		}

		$blocks = isset( $settings['blocks'] ) && is_array( $settings['blocks'] ) ? $settings['blocks'] : array();

		return ! empty( $blocks[ $block_key ]['hidden'] );
	}

	/**
	 * @param array<string, mixed> $settings  Homepage settings.
	 * @param string               $block_key Block key.
	 * @return bool
	 */
	public static function is_block_visible( $settings, $block_key ) {
		return ! self::is_block_hidden( $settings, $block_key );
	}

	/**
	 * Convert legacy fixed social fields to dynamic list.
	 *
	 * @param array<string, mixed> $settings Settings array.
	 * @return array<int, array{network: string, label: string, url: string}>
	 */
	private static function migrate_legacy_socials( $settings ) {
		$social = isset( $settings['social'] ) && is_array( $settings['social'] ) ? $settings['social'] : array();
		$legacy = array(
			'telegram'  => 'telegram',
			'vk'        => 'vk',
			'youtube'   => 'youtube',
			'instagram' => 'instagram',
			'email'     => 'mail',
		);
		$items  = array();

		foreach ( $legacy as $key => $network ) {
			$raw = isset( $social[ $key ] ) ? trim( (string) $social[ $key ] ) : '';
			if ( '' === $raw ) {
				continue;
			}

			$icon = Art_Starter_Icons::get( $network );
			$url  = 'mail' === $network ? $raw : self::normalize_external_url( $raw );

			$items[] = array(
				'network' => $network,
				'label'   => $icon ? (string) $icon['label'] : '',
				'url'     => $url,
			);
		}

		return $items;
	}

	/**
	 * Build front-end href for a social item.
	 *
	 * @param array{network?: string, url?: string} $item Social item.
	 * @return string
	 */
	public static function get_social_href( $item ) {
		if ( ! is_array( $item ) ) {
			return '';
		}

		$network = isset( $item['network'] ) ? sanitize_key( (string) $item['network'] ) : '';
		$url     = isset( $item['url'] ) ? trim( (string) $item['url'] ) : '';

		if ( '' === $network || '' === $url ) {
			return '';
		}

		if ( 'mail' === $network ) {
			if ( preg_match( '#^mailto:#i', $url ) ) {
				return $url;
			}

			$email = sanitize_email( $url );
			return $email ? 'mailto:' . $email : '';
		}

		return self::normalize_external_url( $url );
	}

	/**
	 * Whether ART Starter homepage is assigned as the site front page.
	 */
	public static function is_active_as_front_page() {
		$settings = self::get_all();

		if ( empty( $settings['use_as_front_page'] ) ) {
			return false;
		}

		return 'posts' === get_option( 'show_on_front' );
	}

	/**
	 * Sync checkbox state when Reading settings were changed elsewhere.
	 */
	public static function sync_front_page_flag_with_reading() {
		$settings = self::get_all();

		if ( empty( $settings['use_as_front_page'] ) ) {
			return;
		}

		if ( 'page' === get_option( 'show_on_front' ) && (int) get_option( 'page_on_front' ) > 0 ) {
			self::clear_use_as_front_page_flag();
		}
	}

	/**
	 * Normalize user URL so paths without a scheme open outside the site.
	 *
	 * @param string $url Raw URL.
	 * @return string
	 */
	public static function normalize_external_url( $url ) {
		$url = trim( (string) $url );

		if ( '' === $url ) {
			return '';
		}

		if ( preg_match( '#^(https?://|mailto:|tel:)#i', $url ) ) {
			return esc_url_raw( $url );
		}

		return esc_url_raw( 'https://' . ltrim( $url, '/' ) );
	}

	/**
	 * Chevron markup for link rows (› style, SVG for vertical alignment).
	 *
	 * @return string
	 */
	public static function get_link_arrow_markup() {
		static $markup = null;

		if ( null !== $markup ) {
			return $markup;
		}

		$path = ART_STARTER_PLUGIN_DIR . 'assets/icons/link-chevron.svg';

		if ( is_readable( $path ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- bundled static asset.
			$markup = (string) file_get_contents( $path );
		} else {
			$markup = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 7l5 5-5 5"/></svg>';
		}

		return $markup;
	}

	/**
	 * @return string
	 */
	public static function render_link_arrow() {
		return '<span class="art-starter-homepage-link__arrow" aria-hidden="true"><span class="art-starter-homepage-link__arrow-svg">' . self::get_link_arrow_markup() . '</span></span>';
	}

	/**
	 * @param mixed $input Raw input.
	 * @return array<string, mixed>
	 */
	public static function sanitize( $input ) {
		$input = is_array( $input ) ? $input : array();

		$out = self::get_defaults();

		$template = isset( $input['template'] ) ? sanitize_key( (string) $input['template'] ) : self::get_default_template();
		if ( ! self::is_valid_template( $template ) ) {
			$template = self::get_default_template();
		}
		$out['template']          = $template;
		$out['use_as_front_page'] = ! empty( $input['use_as_front_page'] );

		$blocks_input = isset( $input['blocks'] ) && is_array( $input['blocks'] ) ? $input['blocks'] : array();

		foreach ( self::get_block_keys() as $block_key ) {
			$block = isset( $blocks_input[ $block_key ] ) && is_array( $blocks_input[ $block_key ] ) ? $blocks_input[ $block_key ] : array();
			$out['blocks'][ $block_key ]['hidden'] = ! empty( $block['hidden'] );
		}

		$profile                      = isset( $input['profile'] ) && is_array( $input['profile'] ) ? $input['profile'] : array();
		$out['profile']['name']       = isset( $profile['name'] ) ? sanitize_text_field( (string) $profile['name'] ) : '';
		$out['profile']['roles']      = isset( $profile['roles'] ) ? sanitize_text_field( (string) $profile['roles'] ) : '';
		$out['profile']['bio']        = isset( $profile['bio'] ) ? sanitize_textarea_field( (string) $profile['bio'] ) : '';
		$out['profile']['avatar_url'] = isset( $profile['avatar_url'] ) ? esc_url_raw( (string) $profile['avatar_url'] ) : '';

		$cta                 = isset( $input['cta'] ) && is_array( $input['cta'] ) ? $input['cta'] : array();
		$out['cta']['label'] = isset( $cta['label'] ) ? sanitize_text_field( (string) $cta['label'] ) : '';
		$out['cta']['url']   = isset( $cta['url'] ) ? self::normalize_external_url( (string) $cta['url'] ) : '';
		$out['cta']['icon']  = Art_Starter_Icons::sanitize_slug(
			isset( $cta['icon'] ) ? (string) $cta['icon'] : '',
			Art_Starter_Icons::get_picker_categories()
		);

		$links        = isset( $input['links'] ) && is_array( $input['links'] ) ? $input['links'] : array();
		$out['links'] = array();

		foreach ( $links as $link ) {
			if ( ! is_array( $link ) ) {
				continue;
			}

			$label = isset( $link['label'] ) ? sanitize_text_field( (string) $link['label'] ) : '';
			$url   = isset( $link['url'] ) ? self::normalize_external_url( (string) $link['url'] ) : '';
			$icon  = Art_Starter_Icons::resolve_link_icon(
				isset( $link['icon'] ) ? (string) $link['icon'] : ''
			);

			if ( '' === $label && '' === $url ) {
				continue;
			}

			$out['links'][] = array(
				'label' => $label,
				'url'   => $url,
				'icon'  => $icon,
			);
		}

		$recommend = isset( $input['recommend'] ) && is_array( $input['recommend'] ) ? $input['recommend'] : array();
		$out['recommend']['badge']        = isset( $recommend['badge'] ) ? sanitize_text_field( (string) $recommend['badge'] ) : $out['recommend']['badge'];
		$out['recommend']['title']        = isset( $recommend['title'] ) ? sanitize_text_field( (string) $recommend['title'] ) : '';
		$out['recommend']['description']  = isset( $recommend['description'] ) ? sanitize_textarea_field( (string) $recommend['description'] ) : '';
		$out['recommend']['button_label'] = isset( $recommend['button_label'] ) ? sanitize_text_field( (string) $recommend['button_label'] ) : $out['recommend']['button_label'];
		$out['recommend']['button_url']   = isset( $recommend['button_url'] ) ? self::normalize_external_url( (string) $recommend['button_url'] ) : '';
		$out['recommend']['image_url']    = isset( $recommend['image_url'] ) ? esc_url_raw( (string) $recommend['image_url'] ) : '';

		$out['socials'] = array();
		$socials        = self::parse_socials_from_input( $input );

		foreach ( $socials as $item ) {
			if ( count( $out['socials'] ) >= self::MAX_SOCIAL_ITEMS ) {
				break;
			}

			if ( ! is_array( $item ) ) {
				continue;
			}

			$network = Art_Starter_Icons::sanitize_slug(
				isset( $item['network'] ) ? (string) $item['network'] : '',
				array( Art_Starter_Icons::CATEGORY_SOCIAL ),
				false
			);

			if ( '' === $network ) {
				continue;
			}

			$url = isset( $item['url'] ) ? trim( (string) $item['url'] ) : '';
			if ( '' === $url ) {
				continue;
			}

			if ( 'mail' === $network ) {
				if ( preg_match( '#^mailto:#i', $url ) ) {
					$url = esc_url_raw( $url );
				} else {
					$email = sanitize_email( $url );
					$url   = $email ? 'mailto:' . $email : '';
				}
			} else {
				$url = self::normalize_external_url( $url );
			}

			if ( '' === $url ) {
				continue;
			}

			$icon  = Art_Starter_Icons::get( $network );
			$label = $icon ? (string) $icon['label'] : '';

			$out['socials'][] = array(
				'network' => $network,
				'label'   => sanitize_text_field( $label ),
				'url'     => $url,
			);
		}

		return $out;
	}

	/**
	 * Parse social rows from Settings API POST data.
	 *
	 * @param array<string, mixed> $input Raw settings input.
	 * @return array<int, array<string, mixed>>
	 */
	private static function parse_socials_from_input( $input ) {
		if ( ! is_array( $input ) ) {
			return array();
		}

		if ( isset( $input['socials_payload'] ) ) {
			$payload = trim( wp_unslash( (string) $input['socials_payload'] ) );

			if ( '' !== $payload ) {
				$decoded = json_decode( $payload, true );

				if ( is_array( $decoded ) ) {
					return $decoded;
				}
			}
		}

		if ( isset( $input['socials'] ) && is_array( $input['socials'] ) ) {
			return array_values( $input['socials'] );
		}

		return array();
	}

	/**
	 * Apply Reading settings after homepage option save.
	 *
	 * @param mixed $old_value Old option value.
	 * @param mixed $value     New option value.
	 */
	public static function on_option_updated( $old_value, $value ) {
		if ( ! is_array( $value ) || empty( $value['use_as_front_page'] ) ) {
			return;
		}

		self::activate_as_front_page();
	}

	/**
	 * Uncheck ART Starter front page when Reading uses a static page.
	 *
	 * @param mixed $old_value Old option value.
	 * @param mixed $value     New option value.
	 */
	public static function on_reading_show_on_front_changed( $old_value, $value ) {
		if ( self::$applying_reading_settings ) {
			return;
		}

		if ( 'page' === $value ) {
			self::clear_use_as_front_page_flag();
		}
	}

	/**
	 * Uncheck ART Starter front page when a static page is selected.
	 *
	 * @param mixed $old_value Old option value.
	 * @param mixed $value     New option value.
	 */
	public static function on_reading_page_on_front_changed( $old_value, $value ) {
		if ( self::$applying_reading_settings ) {
			return;
		}

		if ( (int) $value > 0 && 'page' === get_option( 'show_on_front' ) ) {
			self::clear_use_as_front_page_flag();
		}
	}

	/**
	 * Render ART Starter homepage on the site front page.
	 */
	public static function maybe_render_front_page() {
		if ( is_admin() || ! is_front_page() || ! self::is_active_as_front_page() ) {
			return;
		}

		status_header( 200 );
		nocache_headers();

		$settings = self::get_all();
		$template = isset( $settings['template'] ) ? (string) $settings['template'] : self::get_default_template();

		if ( ! self::is_valid_template( $template ) ) {
			$template = self::get_default_template();
		}

		self::enqueue_template_styles( $template );
		wp_enqueue_style(
			'art-starter-public',
			ART_STARTER_PLUGIN_URL . 'assets/css/public.css',
			array( 'art-starter-homepage-template' ),
			ART_STARTER_VERSION
		);

		$view = ART_STARTER_PLUGIN_DIR . 'public/views/homepage.php';
		if ( ! is_readable( $view ) ) {
			return;
		}

		include $view;
		exit;
	}

	/**
	 * Point WordPress Reading settings to the latest posts front page.
	 */
	private static function activate_as_front_page() {
		self::$applying_reading_settings = true;
		update_option( 'show_on_front', 'posts' );
		update_option( 'page_on_front', 0 );
		self::$applying_reading_settings = false;
	}

	/**
	 * Remove ART Starter front page flag from saved settings.
	 */
	private static function clear_use_as_front_page_flag() {
		$settings = get_option( self::OPTION, array() );
		if ( ! is_array( $settings ) || empty( $settings['use_as_front_page'] ) ) {
			return;
		}

		$settings['use_as_front_page'] = false;
		update_option( self::OPTION, $settings );
	}
}
