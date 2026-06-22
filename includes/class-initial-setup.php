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

		return trim( (string) $text );
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

		$defaults = array(
			'Welcome to WordPress. This is your first post. Edit or delete it, then start writing!',
			'Добро пожаловать в WordPress. Это ваша первая запись. Отредактируйте или удалите её, затем начинайте писать!',
			'Добро пожаловать в WordPress. Это ваша первая запись. Отредактируйте или удалите ее, затем начинайте писать!',
		);

		foreach ( $defaults as $default ) {
			if ( $normalized === self::normalize_text( $default ) ) {
				return true;
			}
		}

		return false;
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

		if ( ! in_array( $post->post_title, self::get_hello_post_titles(), true ) ) {
			return false;
		}

		if ( ! in_array( $post->post_name, self::get_hello_post_slugs(), true ) ) {
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

		$author = strtolower( (string) $theme->get( 'Author' ) );

		if ( false === strpos( $author, 'wordpress' ) ) {
			return false;
		}

		$slug = $theme->get_stylesheet();

		return 0 === strpos( $slug, 'twenty' );
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
	 * Build preview data for the admin page.
	 *
	 * @return array<string, mixed>
	 */
	public static function get_form_state() {
		$hello_post   = self::find_hello_post();
		$sample_page  = self::find_sample_page();
		$themes       = self::find_removable_themes();

		return array(
			'site_title'       => (string) get_option( 'blogname' ),
			'site_tagline'     => (string) get_option( 'blogdescription' ),
			'site_icon'        => (int) get_option( 'site_icon', 0 ),
			'technical'        => array(
				'permalink_postname' => array(
					'applied' => self::is_permalink_postname_enabled(),
					'label'   => self::PERMALINK_STRUCTURE,
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

		return $results;
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
