<?php
/**
 * Primary WordPress setup helpers.
 *
 * @package Art_Starter
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Art_Starter_Initial_Setup
 */
class Art_Starter_Initial_Setup {

	const PERMALINK_STRUCTURE = '/%postname%/';

	/**
	 * Known default titles for the factory «Hello world!» post.
	 *
	 * @return array<int, string>
	 */
	private static function get_hello_post_titles() {
		return array(
			'Hello world!',
			'Hello World!',
			'Привет, мир!',
			'Привет, мир',
		);
	}

	/**
	 * Known default slugs for the factory «Hello world!» post.
	 *
	 * @return array<int, string>
	 */
	private static function get_hello_post_slugs() {
		return array(
			'hello-world',
			'privet-mir',
			'привет-мир',
		);
	}

	/**
	 * Known default titles for the factory sample page.
	 *
	 * @return array<int, string>
	 */
	private static function get_sample_page_titles() {
		return array(
			'Sample Page',
			'Пример страницы',
		);
	}

	/**
	 * Known default slugs for the factory sample page.
	 *
	 * @return array<int, string>
	 */
	private static function get_sample_page_slugs() {
		return array(
			'sample-page',
			'primer-stranitsy',
		);
	}

	/**
	 * Normalize text for safe default-content comparison.
	 *
	 * @param string $text Raw text.
	 * @return string
	 */
	private static function normalize_text( $text ) {
		$text = wp_strip_all_tags( (string) $text );
		$text = html_entity_decode( $text, ENT_QUOTES, 'UTF-8' );
		$text = preg_replace( '/\s+/u', ' ', $text );
		$text = str_replace( array( 'ё', 'Ё' ), array( 'е', 'Е' ), $text );

		if ( function_exists( 'mb_strtolower' ) ) {
			$text = mb_strtolower( $text, 'UTF-8' );
		} else {
			$text = strtolower( $text );
		}

		return trim( (string) $text );
	}

	/**
	 * @param string $title Post title.
	 * @return bool
	 */
	private static function is_known_hello_title( $title ) {
		$normalized = self::normalize_text( $title );

		foreach ( self::get_hello_post_titles() as $known_title ) {
			if ( $normalized === self::normalize_text( $known_title ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param string $slug Post slug.
	 * @return bool
	 */
	private static function is_known_hello_slug( $slug ) {
		$slug = sanitize_title( (string) $slug );

		if ( '' === $slug ) {
			return false;
		}

		foreach ( self::get_hello_post_slugs() as $known_slug ) {
			if ( $slug === sanitize_title( $known_slug ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param string $content Post content.
	 * @return bool
	 */
	private static function is_default_hello_content( $content ) {
		$normalized = self::normalize_text( $content );

		if ( '' === $normalized ) {
			return false;
		}

		$prefixes = array(
			'welcome to wordpress. this is your first post.',
			'добро пожаловать в wordpress. это ваша первая запись.',
			'добро пожаловать на ',
		);

		foreach ( $prefixes as $prefix ) {
			if ( 0 === strpos( $normalized, $prefix ) ) {
				return true;
			}
		}

		return (bool) preg_match( '/^welcome to .+\. this is your first post\./u', $normalized );
	}

	/**
	 * @param string $content Page content.
	 * @return bool
	 */
	private static function is_default_sample_page_content( $content ) {
		$normalized = self::normalize_text( $content );

		if ( '' === $normalized ) {
			return false;
		}

		$prefixes = array(
			'This is an example page.',
			'Это пример страницы.',
		);

		foreach ( $prefixes as $prefix ) {
			if ( 0 === strpos( $normalized, self::normalize_text( $prefix ) ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param WP_Post $post Post object.
	 * @return bool
	 */
	private static function is_removable_hello_post( $post ) {
		if ( ! $post instanceof WP_Post || 'post' !== $post->post_type ) {
			return false;
		}

		if ( ! in_array( $post->post_status, array( 'publish', 'draft', 'private' ), true ) ) {
			return false;
		}

		if ( ! self::is_known_hello_title( $post->post_title ) ) {
			return false;
		}

		if ( ! self::is_known_hello_slug( $post->post_name ) && 1 !== (int) $post->ID ) {
			return false;
		}

		if ( ! self::is_default_hello_content( $post->post_content ) ) {
			return false;
		}

		$comment_count = (int) get_comments(
			array(
				'post_id' => $post->ID,
				'count'   => true,
			)
		);

		if ( $comment_count > 2 ) {
			return false;
		}

		return true;
	}

	/**
	 * @param WP_Post $post Page object.
	 * @return bool
	 */
	private static function is_removable_sample_page( $post ) {
		if ( ! $post instanceof WP_Post || 'page' !== $post->post_type ) {
			return false;
		}

		if ( ! in_array( $post->post_status, array( 'publish', 'draft', 'private' ), true ) ) {
			return false;
		}

		if ( (int) get_option( 'page_on_front' ) === (int) $post->ID ) {
			return false;
		}

		if ( (int) get_option( 'page_for_posts' ) === (int) $post->ID ) {
			return false;
		}

		if ( (int) get_option( 'wp_page_for_privacy_policy' ) === (int) $post->ID ) {
			return false;
		}

		if ( ! in_array( $post->post_title, self::get_sample_page_titles(), true ) ) {
			return false;
		}

		if ( ! in_array( $post->post_name, self::get_sample_page_slugs(), true ) ) {
			return false;
		}

		if ( ! self::is_default_sample_page_content( $post->post_content ) ) {
			return false;
		}

		return true;
	}

	/**
	 * @param WP_Theme $theme Theme object.
	 * @return bool
	 */
	private static function is_default_wordpress_theme( $theme ) {
		if ( ! $theme instanceof WP_Theme || ! $theme->exists() ) {
			return false;
		}

		$slug = (string) $theme->get_stylesheet();

		if ( 0 !== strpos( $slug, 'twenty' ) ) {
			return false;
		}

		$author = strtolower( self::normalize_text( (string) $theme->get( 'Author' ) ) );

		if ( false !== strpos( $author, 'wordpress' ) ) {
			return true;
		}

		$author_uri = strtolower( (string) $theme->get( 'AuthorURI' ) );
		$theme_uri  = strtolower( (string) $theme->get( 'ThemeURI' ) );

		return false !== strpos( $author_uri, 'wordpress.org' )
			|| false !== strpos( $theme_uri, 'wordpress.org/themes/' );
	}

	/**
	 * Find the factory default «Hello world!» post, if still untouched.
	 *
	 * @return WP_Post|null
	 */
	public static function find_hello_post() {
		$candidate_ids = array();

		foreach ( self::get_hello_post_slugs() as $slug ) {
			$post = get_page_by_path( $slug, OBJECT, 'post' );

			if ( $post instanceof WP_Post ) {
				$candidate_ids[ $post->ID ] = $post->ID;
			}
		}

		$default_post = get_post( 1 );

		if ( $default_post instanceof WP_Post && 'post' === $default_post->post_type ) {
			$candidate_ids[ $default_post->ID ] = $default_post->ID;
		}

		$posts_by_title = get_posts(
			array(
				'post_type'              => 'post',
				'post_status'            => array( 'publish', 'draft', 'private' ),
				'posts_per_page'         => 10,
				'orderby'                => 'ID',
				'order'                  => 'ASC',
				'ignore_sticky_posts'    => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'fields'                 => 'ids',
			)
		);

		foreach ( $posts_by_title as $post_id ) {
			$post = get_post( $post_id );

			if ( $post instanceof WP_Post && self::is_known_hello_title( $post->post_title ) ) {
				$candidate_ids[ $post->ID ] = $post->ID;
			}
		}

		foreach ( array_unique( $candidate_ids ) as $post_id ) {
			$post = get_post( $post_id );

			if ( self::is_removable_hello_post( $post ) ) {
				return $post;
			}
		}

		return null;
	}

	/**
	 * Find the factory default sample page, if still untouched.
	 *
	 * @return WP_Post|null
	 */
	public static function find_sample_page() {
		$candidate_ids = array();

		foreach ( self::get_sample_page_slugs() as $slug ) {
			$page = get_page_by_path( $slug, OBJECT, 'page' );

			if ( $page instanceof WP_Post ) {
				$candidate_ids[ $page->ID ] = $page->ID;
			}
		}

		$default_page = get_post( 2 );

		if ( $default_page instanceof WP_Post && 'page' === $default_page->post_type ) {
			$candidate_ids[ $default_page->ID ] = $default_page->ID;
		}

		foreach ( array_unique( $candidate_ids ) as $page_id ) {
			$page = get_post( $page_id );

			if ( self::is_removable_sample_page( $page ) ) {
				return $page;
			}
		}

		return null;
	}

	/**
	 * Find inactive bundled WordPress themes that are safe to remove.
	 *
	 * @return array<int, array<string, string>>
	 */
	public static function find_removable_themes() {
		if ( ! function_exists( 'wp_get_themes' ) ) {
			require_once ABSPATH . 'wp-admin/includes/theme.php';
		}

		$active_stylesheet = get_stylesheet();
		$active_template   = get_template();
		$items             = array();

		foreach ( wp_get_themes() as $slug => $theme ) {
			if ( $slug === $active_stylesheet || $slug === $active_template ) {
				continue;
			}

			if ( ! self::is_default_wordpress_theme( $theme ) ) {
				continue;
			}

			$items[] = array(
				'slug'  => $slug,
				'name'  => $theme->get( 'Name' ),
				'version' => (string) $theme->get( 'Version' ),
			);
		}

		usort(
			$items,
			static function ( $left, $right ) {
				return strnatcasecmp( $left['name'], $right['name'] );
			}
		);

		return $items;
	}

	/**
	 * Default bundled plugins that can be removed during setup.
	 *
	 * @return array<int, string>
	 */
	private static function get_default_removable_plugin_files() {
		return array(
			'akismet/akismet.php',
			'hello-dolly/hello.php',
		);
	}

	/**
	 * @param string               $plugin_file    Plugin basename.
	 * @param array<string, mixed> $plugin_data    Plugin header data.
	 * @param bool                 $include_active Whether active plugins may be listed.
	 * @return bool
	 */
	private static function is_removable_default_plugin( $plugin_file, $plugin_data, $include_active = true ) {
		if ( ! in_array( $plugin_file, self::get_default_removable_plugin_files(), true ) ) {
			return false;
		}

		if ( ! is_array( $plugin_data ) ) {
			return false;
		}

		if ( ! $include_active && is_plugin_active( $plugin_file ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Find bundled WordPress plugins that are safe to remove.
	 *
	 * @param bool $include_active Include active plugins (they will be deactivated before delete).
	 * @return array<int, array<string, string>>
	 */
	public static function find_removable_plugins( $include_active = true ) {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$items = array();

		foreach ( get_plugins() as $plugin_file => $plugin_data ) {
			if ( ! self::is_removable_default_plugin( $plugin_file, $plugin_data, $include_active ) ) {
				continue;
			}

			$items[] = array(
				'file'    => $plugin_file,
				'slug'    => dirname( $plugin_file ),
				'name'    => (string) $plugin_data['Name'],
				'version' => (string) $plugin_data['Version'],
				'active'  => is_plugin_active( $plugin_file ) ? '1' : '0',
			);
		}

		usort(
			$items,
			static function ( $left, $right ) {
				return strnatcasecmp( $left['name'], $right['name'] );
			}
		);

		return $items;
	}

	/**
	 * @return bool
	 */
	public static function is_permalink_postname_enabled() {
		return self::PERMALINK_STRUCTURE === (string) get_option( 'permalink_structure' );
	}

	/**
	 * @return bool
	 */
	public static function are_comments_disabled() {
		return 'closed' === (string) get_option( 'default_comment_status' )
			&& 'closed' === (string) get_option( 'default_ping_status' );
	}

	/**
	 * @return bool
	 */
	public static function are_pingbacks_disabled() {
		return '0' === (string) get_option( 'default_pingback_flag' )
			&& 'closed' === (string) get_option( 'default_ping_status' );
	}

	/**
	 * @return bool
	 */
	public static function is_registration_disabled() {
		return ! (bool) get_option( 'users_can_register' );
	}

	/**
	 * Whether site and WordPress URLs in the database use HTTPS.
	 *
	 * @return bool
	 */
	public static function is_site_urls_https_enabled() {
		$home    = (string) get_option( 'home' );
		$siteurl = (string) get_option( 'siteurl' );

		return 'https' === wp_parse_url( $home, PHP_URL_SCHEME )
			&& 'https' === wp_parse_url( $siteurl, PHP_URL_SCHEME );
	}

	/**
	 * Whether home/siteurl are overridden in wp-config.php.
	 *
	 * @return bool
	 */
	public static function are_site_urls_locked_by_constants() {
		return defined( 'WP_HOME' ) || defined( 'WP_SITEURL' );
	}

	/**
	 * Switch home and siteurl options from HTTP to HTTPS.
	 *
	 * @return bool Whether any option was updated.
	 */
	private static function switch_site_urls_to_https() {
		if ( function_exists( 'wp_update_urls_to_https' ) ) {
			return (bool) wp_update_urls_to_https();
		}

		$home        = (string) get_option( 'home' );
		$siteurl     = (string) get_option( 'siteurl' );
		$https_home  = (string) set_url_scheme( $home, 'https' );
		$https_admin = (string) set_url_scheme( $siteurl, 'https' );

		if ( $https_home === $home && $https_admin === $siteurl ) {
			return false;
		}

		update_option( 'home', $https_home );
		update_option( 'siteurl', $https_admin );

		return true;
	}

	/**
	 * Build preview data for the admin page.
	 *
	 * @return array<string, mixed>
	 */
	public static function get_form_state() {
		$hello_post   = self::find_hello_post();
		$sample_page  = self::find_sample_page();
		$themes       = self::find_removable_themes();
		$plugins      = self::find_removable_plugins();

		return array(
			'site_title'       => (string) get_option( 'blogname' ),
			'site_tagline'     => (string) get_option( 'blogdescription' ),
			'site_icon'        => (int) get_option( 'site_icon', 0 ),
			'technical'        => array(
				'permalink_postname' => array(
					'applied' => self::is_permalink_postname_enabled(),
					'label'   => self::PERMALINK_STRUCTURE,
				),
				'https_site_urls'    => array(
					'applied' => self::is_site_urls_https_enabled(),
					'locked'  => self::are_site_urls_locked_by_constants(),
					'home'    => (string) get_option( 'home' ),
					'siteurl' => (string) get_option( 'siteurl' ),
				),
				'disable_comments'   => array(
					'applied' => self::are_comments_disabled(),
				),
				'disable_pingbacks'  => array(
					'applied' => self::are_pingbacks_disabled(),
				),
				'disable_registration' => array(
					'applied' => self::is_registration_disabled(),
				),
			),
			'removable'        => array(
				'hello_post'  => self::format_post_preview( $hello_post ),
				'sample_page' => self::format_post_preview( $sample_page ),
				'themes'      => $themes,
				'plugins'     => $plugins,
			),
		);
	}

	/**
	 * @param WP_Post|null $post Post object.
	 * @return array<string, string>|null
	 */
	private static function format_post_preview( $post ) {
		if ( ! $post instanceof WP_Post ) {
			return null;
		}

		$edit_link = get_edit_post_link( $post->ID, 'raw' );
		$view_link = get_permalink( $post );

		return array(
			'id'    => (string) $post->ID,
			'title' => $post->post_title,
			'slug'  => $post->post_name,
			'type'  => $post->post_type,
			'edit'  => is_string( $edit_link ) ? $edit_link : '',
			'view'  => is_string( $view_link ) ? $view_link : '',
		);
	}

	/**
	 * Apply selected setup actions.
	 *
	 * @param array<string, mixed> $input Form input.
	 * @return array<string, mixed>
	 */
	public static function apply( $input ) {
		$input   = is_array( $input ) ? $input : array();
		$results = array(
			'updated' => array(),
			'skipped' => array(),
			'errors'  => array(),
		);

		$site_title   = isset( $input['site_title'] ) ? sanitize_text_field( wp_unslash( $input['site_title'] ) ) : null;
		$site_tagline = isset( $input['site_tagline'] ) ? sanitize_text_field( wp_unslash( $input['site_tagline'] ) ) : null;

		if ( null !== $site_title ) {
			update_option( 'blogname', $site_title );
			$results['updated'][] = __( 'Название сайта обновлено.', 'art-starter' );
		}

		if ( null !== $site_tagline ) {
			update_option( 'blogdescription', $site_tagline );
			$results['updated'][] = __( 'Краткое описание сайта обновлено.', 'art-starter' );
		}

		if ( array_key_exists( 'site_icon', $input ) ) {
			$site_icon = absint( $input['site_icon'] );

			if ( $site_icon > 0 ) {
				if ( wp_attachment_is_image( $site_icon ) ) {
					update_option( 'site_icon', $site_icon );
					$results['updated'][] = __( 'Фавикон сайта обновлён.', 'art-starter' );
				} else {
					$results['errors'][] = __( 'Не удалось установить фавикон: выбранный файл не является изображением.', 'art-starter' );
				}
			} elseif ( (int) get_option( 'site_icon', 0 ) > 0 ) {
				delete_option( 'site_icon' );
				$results['updated'][] = __( 'Фавикон сайта удалён.', 'art-starter' );
			}
		}

		if ( ! empty( $input['apply_permalink_postname'] ) ) {
			if ( self::is_permalink_postname_enabled() ) {
				$results['skipped'][] = __( 'Постоянные ссылки уже используют структуру «Название записи».', 'art-starter' );
			} else {
				update_option( 'permalink_structure', self::PERMALINK_STRUCTURE );
				flush_rewrite_rules();
				$results['updated'][] = __( 'Постоянные ссылки переключены на «Название записи».', 'art-starter' );
			}
		}

		if ( ! empty( $input['apply_https_site_urls'] ) ) {
			if ( self::are_site_urls_locked_by_constants() ) {
				$results['errors'][] = __( 'Адреса заданы в wp-config.php (WP_HOME / WP_SITEURL) — измените их вручную в конфигурации.', 'art-starter' );
			} elseif ( self::is_site_urls_https_enabled() ) {
				$results['skipped'][] = __( 'Адреса сайта и панели WordPress уже используют HTTPS.', 'art-starter' );
			} elseif ( self::switch_site_urls_to_https() ) {
				$results['updated'][] = __( 'Адреса сайта и панели WordPress переключены на HTTPS (Настройки → Общие).', 'art-starter' );
			} else {
				$results['errors'][] = __( 'Не удалось переключить адреса на HTTPS.', 'art-starter' );
			}
		}

		if ( ! empty( $input['apply_disable_comments'] ) ) {
			if ( self::are_comments_disabled() ) {
				$results['skipped'][] = __( 'Комментарии для новых записей уже отключены.', 'art-starter' );
			} else {
				update_option( 'default_comment_status', 'closed' );
				update_option( 'default_ping_status', 'closed' );
				$results['updated'][] = __( 'Комментарии для новых записей отключены.', 'art-starter' );
			}
		}

		if ( ! empty( $input['apply_disable_pingbacks'] ) ) {
			if ( self::are_pingbacks_disabled() ) {
				$results['skipped'][] = __( 'Пингбэки и трекбэки уже отключены.', 'art-starter' );
			} else {
				update_option( 'default_pingback_flag', '0' );
				update_option( 'default_ping_status', 'closed' );
				$results['updated'][] = __( 'Пингбэки и трекбэки отключены для новых записей.', 'art-starter' );
			}
		}

		if ( ! empty( $input['apply_disable_registration'] ) ) {
			if ( self::is_registration_disabled() ) {
				$results['skipped'][] = __( 'Регистрация новых пользователей уже запрещена.', 'art-starter' );
			} else {
				update_option( 'users_can_register', 0 );
				$results['updated'][] = __( 'Регистрация новых пользователей запрещена.', 'art-starter' );
			}
		}

		if ( ! empty( $input['delete_hello_post'] ) ) {
			$hello_post = self::find_hello_post();

			if ( ! $hello_post instanceof WP_Post ) {
				$results['skipped'][] = __( 'Запись «Привет, мир!» не найдена или уже изменена — удаление пропущено.', 'art-starter' );
			} else {
				$deleted = wp_delete_post( $hello_post->ID, true );

				if ( $deleted ) {
					/* translators: %s: post title */
					$results['updated'][] = sprintf( __( 'Удалена запись «%s».', 'art-starter' ), $hello_post->post_title );
				} else {
					$results['errors'][] = __( 'Не удалось удалить запись «Привет, мир!».', 'art-starter' );
				}
			}
		}

		if ( ! empty( $input['delete_sample_page'] ) ) {
			$sample_page = self::find_sample_page();

			if ( ! $sample_page instanceof WP_Post ) {
				$results['skipped'][] = __( 'Страница «Пример страницы» не найдена, используется сайтом или уже изменена — удаление пропущено.', 'art-starter' );
			} else {
				$deleted = wp_delete_post( $sample_page->ID, true );

				if ( $deleted ) {
					/* translators: %s: page title */
					$results['updated'][] = sprintf( __( 'Удалена страница «%s».', 'art-starter' ), $sample_page->post_title );
				} else {
					$results['errors'][] = __( 'Не удалось удалить страницу «Пример страницы».', 'art-starter' );
				}
			}
		}

		if ( ! empty( $input['delete_extra_themes'] ) ) {
			$theme_results = self::delete_removable_themes();
			$results       = self::merge_apply_results( $results, $theme_results );
		}

		if ( ! empty( $input['delete_default_plugins'] ) ) {
			$plugin_results = self::delete_removable_plugins();
			$results        = self::merge_apply_results( $results, $plugin_results );
		}

		return $results;
	}

	/**
	 * Prepare the WordPress filesystem API for theme/plugin deletion.
	 *
	 * @return bool
	 */
	private static function ensure_admin_filesystem() {
		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		global $wp_filesystem;

		if ( is_object( $wp_filesystem ) ) {
			return true;
		}

		ob_start();
		$credentials = request_filesystem_credentials( '' );
		ob_end_clean();

		if ( ! WP_Filesystem( $credentials ) ) {
			return false;
		}

		return is_object( $wp_filesystem );
	}

	/**
	 * @return array<string, mixed>
	 */
	private static function delete_removable_themes() {
		if ( ! function_exists( 'delete_theme' ) ) {
			require_once ABSPATH . 'wp-admin/includes/theme.php';
		}

		$results = array(
			'updated' => array(),
			'skipped' => array(),
			'errors'  => array(),
		);

		if ( ! self::ensure_admin_filesystem() ) {
			$results['errors'][] = __( 'Не удалось получить доступ к файловой системе для удаления тем.', 'art-starter' );
			return $results;
		}

		$themes = self::find_removable_themes();

		if ( empty( $themes ) ) {
			$results['skipped'][] = __( 'Лишние стандартные темы не найдены.', 'art-starter' );
			return $results;
		}

		foreach ( $themes as $theme ) {
			$slug   = $theme['slug'];
			$result = delete_theme( $slug );

			if ( true === $result ) {
				/* translators: %s: theme name */
				$results['updated'][] = sprintf( __( 'Удалена тема «%s».', 'art-starter' ), $theme['name'] );
				continue;
			}

			if ( null === $result ) {
				/* translators: %s: theme name */
				$results['errors'][] = sprintf( __( 'Не удалось удалить тему «%s»: нет доступа к файловой системе.', 'art-starter' ), $theme['name'] );
				continue;
			}

			if ( is_wp_error( $result ) ) {
				$results['errors'][] = sprintf(
					/* translators: 1: theme name, 2: error message */
					__( 'Не удалось удалить тему «%1$s»: %2$s', 'art-starter' ),
					$theme['name'],
					$result->get_error_message()
				);
			} else {
				/* translators: %s: theme name */
				$results['errors'][] = sprintf( __( 'Не удалось удалить тему «%s».', 'art-starter' ), $theme['name'] );
			}
		}

		return $results;
	}

	/**
	 * @return array<string, mixed>
	 */
	private static function delete_removable_plugins() {
		if ( ! function_exists( 'delete_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$results = array(
			'updated' => array(),
			'skipped' => array(),
			'errors'  => array(),
		);

		if ( ! self::ensure_admin_filesystem() ) {
			$results['errors'][] = __( 'Не удалось получить доступ к файловой системе для удаления плагинов.', 'art-starter' );
			return $results;
		}

		$plugins = self::find_removable_plugins( true );

		if ( empty( $plugins ) ) {
			$results['skipped'][] = __( 'Стартовые плагины WordPress не найдены.', 'art-starter' );
			return $results;
		}

		foreach ( $plugins as $plugin ) {
			if ( is_plugin_active( $plugin['file'] ) ) {
				deactivate_plugins( $plugin['file'], true );
			}

			$result = delete_plugins( array( $plugin['file'] ) );

			if ( true === $result ) {
				/* translators: %s: plugin name */
				$results['updated'][] = sprintf( __( 'Удалён плагин «%s».', 'art-starter' ), $plugin['name'] );
				continue;
			}

			if ( null === $result ) {
				/* translators: %s: plugin name */
				$results['errors'][] = sprintf( __( 'Не удалось удалить плагин «%s»: нет доступа к файловой системе.', 'art-starter' ), $plugin['name'] );
				continue;
			}

			if ( is_wp_error( $result ) ) {
				$results['errors'][] = sprintf(
					/* translators: 1: plugin name, 2: error message */
					__( 'Не удалось удалить плагин «%1$s»: %2$s', 'art-starter' ),
					$plugin['name'],
					$result->get_error_message()
				);
			} else {
				/* translators: %s: plugin name */
				$results['errors'][] = sprintf( __( 'Не удалось удалить плагин «%s».', 'art-starter' ), $plugin['name'] );
			}
		}

		return $results;
	}

	/**
	 * @param array<string, mixed> $base    Base results.
	 * @param array<string, mixed> $partial Partial results.
	 * @return array<string, mixed>
	 */
	private static function merge_apply_results( $base, $partial ) {
		foreach ( array( 'updated', 'skipped', 'errors' ) as $key ) {
			if ( empty( $partial[ $key ] ) || ! is_array( $partial[ $key ] ) ) {
				continue;
			}

			$base[ $key ] = array_merge( $base[ $key ], $partial[ $key ] );
		}

		return $base;
	}
}
