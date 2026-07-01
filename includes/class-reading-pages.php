<?php
/**
 * WordPress Reading settings and service pages for ART Starter homepage.
 *
 * @package Art_Starter
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Art_Starter_Reading_Pages
 */
class Art_Starter_Reading_Pages {

	const OPTION = 'art_starter_reading_pages';

	const META_MANAGED = '_art_starter_managed_page';

	const ROLE_MAIN = 'main';

	const ROLE_BLOG = 'blog';

	const SLUG_MAIN = 'art-main';

	const SLUG_BLOG = 'blog';

	/**
	 * Skip Reading sync side effects while plugin updates core options.
	 *
	 * @var bool
	 */
	private static $applying_reading_settings = false;

	/**
	 * Allow internal updates to managed service pages.
	 *
	 * @var bool
	 */
	private static $updating_managed_page = false;

	/**
	 * Register hooks.
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'maybe_migrate_legacy_reading' ), 4 );
		add_action( 'load-post.php', array( __CLASS__, 'block_managed_page_edit_screen' ) );
		add_action( 'load-post-new.php', array( __CLASS__, 'block_managed_page_create_redirect' ) );
		add_filter( 'wp_insert_post_data', array( __CLASS__, 'lock_managed_page_data' ), 10, 2 );
		add_filter( 'pre_delete_post', array( __CLASS__, 'prevent_managed_page_delete' ), 10, 3 );
		add_filter( 'pre_trash_post', array( __CLASS__, 'prevent_managed_page_delete' ), 10, 2 );
		add_action( 'admin_notices', array( __CLASS__, 'render_admin_notices' ) );
	}

	/**
	 * Whether Reading options are being updated by the plugin.
	 *
	 * @return bool
	 */
	public static function is_applying_reading_settings() {
		return self::$applying_reading_settings;
	}

	/**
	 * @return string
	 */
	public static function get_main_page_title() {
		return __( 'ART Starter — главная', 'art-starter' );
	}

	/**
	 * @return string
	 */
	public static function get_main_page_content() {
		return __( 'Главная страница ART Starter.', 'art-starter' );
	}

	/**
	 * @return string
	 */
	public static function get_blog_page_title() {
		return __( 'Блог', 'art-starter' );
	}

	/**
	 * @return string
	 */
	public static function get_blog_page_content() {
		return __( 'Страница блога ART Starter.', 'art-starter' );
	}

	/**
	 * @return array<string, mixed>
	 */
	public static function get_state() {
		$stored = get_option( self::OPTION, array() );

		if ( ! is_array( $stored ) ) {
			$stored = array();
		}

		$snapshot = isset( $stored['reading_snapshot'] ) && is_array( $stored['reading_snapshot'] )
			? $stored['reading_snapshot']
			: null;

		$page_ids = isset( $stored['page_ids'] ) && is_array( $stored['page_ids'] )
			? $stored['page_ids']
			: array();

		return array(
			'reading_snapshot' => $snapshot,
			'page_ids'         => array(
				'main' => isset( $page_ids['main'] ) ? (int) $page_ids['main'] : 0,
				'blog' => isset( $page_ids['blog'] ) ? (int) $page_ids['blog'] : 0,
			),
		);
	}

	/**
	 * @param array<string, mixed> $state Reading pages state.
	 */
	private static function save_state( $state ) {
		if ( ! is_array( $state ) ) {
			return;
		}

		update_option( self::OPTION, $state, false );
	}

	/**
	 * @return int
	 */
	public static function get_main_page_id() {
		$state  = self::get_state();
		$page_id = (int) $state['page_ids']['main'];

		if ( $page_id > 0 && get_post( $page_id ) instanceof WP_Post ) {
			return $page_id;
		}

		$page = get_page_by_path( self::SLUG_MAIN, OBJECT, 'page' );

		return $page instanceof WP_Post ? (int) $page->ID : 0;
	}

	/**
	 * @return int
	 */
	public static function get_blog_page_id() {
		$state   = self::get_state();
		$page_id = (int) $state['page_ids']['blog'];

		if ( $page_id > 0 && get_post( $page_id ) instanceof WP_Post ) {
			return $page_id;
		}

		$page = get_page_by_path( self::SLUG_BLOG, OBJECT, 'page' );

		return $page instanceof WP_Post ? (int) $page->ID : 0;
	}

	/**
	 * @param int $post_id Post ID.
	 * @return bool
	 */
	public static function is_managed_page( $post_id ) {
		$post_id = (int) $post_id;

		if ( $post_id <= 0 ) {
			return false;
		}

		$role = get_post_meta( $post_id, self::META_MANAGED, true );

		return self::ROLE_MAIN === $role || self::ROLE_BLOG === $role;
	}

	/**
	 * Whether Reading settings still point to ART Starter service pages.
	 *
	 * @return bool
	 */
	public static function is_reading_configuration_active() {
		$settings = Art_Starter_Homepage::get_all();

		if ( empty( $settings['use_as_front_page'] ) ) {
			return false;
		}

		$main_id = self::get_main_page_id();
		$blog_id = self::get_blog_page_id();

		if ( $main_id <= 0 || $blog_id <= 0 ) {
			return false;
		}

		return 'page' === get_option( 'show_on_front' )
			&& (int) get_option( 'page_on_front' ) === $main_id
			&& (int) get_option( 'page_for_posts' ) === $blog_id;
	}

	/**
	 * Migrate sites that still use the legacy "latest posts" front page mode.
	 */
	public static function maybe_migrate_legacy_reading() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$settings = Art_Starter_Homepage::get_all();

		if ( empty( $settings['use_as_front_page'] ) ) {
			return;
		}

		if ( self::is_reading_configuration_active() ) {
			return;
		}

		if ( 'posts' === get_option( 'show_on_front' ) ) {
			self::enable_front_page();
		}
	}

	/**
	 * Enable ART Starter homepage via WordPress Reading settings.
	 *
	 * @return true|WP_Error
	 */
	public static function enable_front_page() {
		self::capture_reading_snapshot_if_needed();

		$pages = self::ensure_service_pages();

		if ( is_wp_error( $pages ) ) {
			return $pages;
		}

		self::apply_reading_settings( (int) $pages['main'], (int) $pages['blog'] );

		$state = self::get_state();
		$state['page_ids'] = array(
			'main' => (int) $pages['main'],
			'blog' => (int) $pages['blog'],
		);
		self::save_state( $state );

		flush_rewrite_rules( false );

		return true;
	}

	/**
	 * Restore Reading settings captured before ART Starter took over.
	 *
	 * @return true|WP_Error
	 */
	public static function disable_front_page() {
		$state    = self::get_state();
		$snapshot = isset( $state['reading_snapshot'] ) && is_array( $state['reading_snapshot'] )
			? $state['reading_snapshot']
			: null;

		if ( is_array( $snapshot ) ) {
			self::restore_reading_snapshot( $snapshot );
			$state['reading_snapshot'] = null;
			self::save_state( $state );
		}

		flush_rewrite_rules( false );

		return true;
	}

	/**
	 * Re-apply Reading settings when the checkbox stays enabled.
	 *
	 * @return true|WP_Error
	 */
	public static function ensure_reading_synced() {
		if ( self::is_reading_configuration_active() ) {
			return true;
		}

		return self::enable_front_page();
	}

	/**
	 * Store current Reading settings before the first takeover.
	 */
	private static function capture_reading_snapshot_if_needed() {
		$state = self::get_state();

		if ( ! empty( $state['reading_snapshot'] ) && is_array( $state['reading_snapshot'] ) ) {
			return;
		}

		$state['reading_snapshot'] = array(
			'show_on_front'  => (string) get_option( 'show_on_front', 'posts' ),
			'page_on_front'  => (int) get_option( 'page_on_front', 0 ),
			'page_for_posts' => (int) get_option( 'page_for_posts', 0 ),
		);

		self::save_state( $state );
	}

	/**
	 * @return array{main: int, blog: int}|WP_Error
	 */
	private static function ensure_service_pages() {
		$main = self::ensure_service_page(
			self::ROLE_MAIN,
			self::SLUG_MAIN,
			self::get_main_page_title(),
			self::get_main_page_content()
		);

		if ( is_wp_error( $main ) ) {
			return $main;
		}

		$blog = self::ensure_service_page(
			self::ROLE_BLOG,
			self::SLUG_BLOG,
			self::get_blog_page_title(),
			self::get_blog_page_content()
		);

		if ( is_wp_error( $blog ) ) {
			return $blog;
		}

		return array(
			'main' => (int) $main,
			'blog' => (int) $blog,
		);
	}

	/**
	 * @param string $role    Managed page role.
	 * @param string $slug    Page slug.
	 * @param string $title   Page title.
	 * @param string $content Service note content.
	 * @return int|WP_Error
	 */
	private static function ensure_service_page( $role, $slug, $title, $content ) {
		$existing = get_page_by_path( $slug, OBJECT, 'page' );

		if ( $existing instanceof WP_Post ) {
			if ( ! self::page_has_role( (int) $existing->ID, $role ) ) {
				return new WP_Error(
					'art_starter_reading_slug_conflict',
					sprintf(
						/* translators: %s: page slug */
						__( 'Нельзя использовать slug «%s»: страница уже существует и не управляется ART Starter.', 'art-starter' ),
						$slug
					)
				);
			}

			self::update_managed_page(
				(int) $existing->ID,
				array(
					'post_title'   => $title,
					'post_content' => $content,
					'post_name'    => $slug,
				)
			);

			return (int) $existing->ID;
		}

		$state   = self::get_state();
		$stored  = self::ROLE_MAIN === $role ? (int) $state['page_ids']['main'] : (int) $state['page_ids']['blog'];
		$by_meta = self::find_page_id_by_role( $role );

		$candidate_id = $stored > 0 ? $stored : $by_meta;

		if ( $candidate_id > 0 ) {
			$candidate = get_post( $candidate_id );

			if ( $candidate instanceof WP_Post && 'page' === $candidate->post_type ) {
				self::update_managed_page(
					$candidate_id,
					array(
						'post_title'   => $title,
						'post_content' => $content,
						'post_name'    => $slug,
					)
				);

				return $candidate_id;
			}
		}

		$author_id = get_current_user_id();
		if ( $author_id <= 0 ) {
			$author_id = 1;
		}

		$page_id = wp_insert_post(
			array(
				'post_type'      => 'page',
				'post_status'    => 'publish',
				'post_author'    => $author_id,
				'post_title'     => $title,
				'post_content'   => $content,
				'post_name'      => $slug,
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
				'meta_input'     => array(
					self::META_MANAGED => $role,
				),
			),
			true
		);

		if ( is_wp_error( $page_id ) ) {
			return $page_id;
		}

		if ( $page_id <= 0 ) {
			return new WP_Error(
				'art_starter_reading_page_create',
				__( 'Не удалось создать служебную страницу ART Starter.', 'art-starter' )
			);
		}

		return (int) $page_id;
	}

	/**
	 * @param string $role Page role.
	 * @return int
	 */
	private static function find_page_id_by_role( $role ) {
		$pages = get_posts(
			array(
				'post_type'              => 'page',
				'post_status'            => array( 'publish', 'draft', 'private', 'pending' ),
				'posts_per_page'         => 1,
				'fields'                 => 'ids',
				'meta_key'               => self::META_MANAGED,
				'meta_value'             => $role,
				'orderby'                => 'ID',
				'order'                  => 'ASC',
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			)
		);

		return ! empty( $pages[0] ) ? (int) $pages[0] : 0;
	}

	/**
	 * @param int    $post_id Post ID.
	 * @param string $role    Expected role.
	 * @return bool
	 */
	private static function page_has_role( $post_id, $role ) {
		return $role === get_post_meta( (int) $post_id, self::META_MANAGED, true );
	}

	/**
	 * @param int                  $post_id Post ID.
	 * @param array<string, mixed> $args    Post fields to update.
	 */
	private static function update_managed_page( $post_id, $args ) {
		$post_id = (int) $post_id;

		if ( $post_id <= 0 ) {
			return;
		}

		$args['ID'] = $post_id;

		self::$updating_managed_page = true;
		wp_update_post( $args );
		self::$updating_managed_page = false;
	}

	/**
	 * @param int $main_page_id Front page ID.
	 * @param int $blog_page_id Posts page ID.
	 */
	private static function apply_reading_settings( $main_page_id, $blog_page_id ) {
		self::$applying_reading_settings = true;
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', (int) $main_page_id );
		update_option( 'page_for_posts', (int) $blog_page_id );
		self::$applying_reading_settings = false;
	}

	/**
	 * @param array<string, mixed> $snapshot Saved Reading settings.
	 */
	private static function restore_reading_snapshot( $snapshot ) {
		$show_on_front  = isset( $snapshot['show_on_front'] ) ? (string) $snapshot['show_on_front'] : 'posts';
		$page_on_front  = isset( $snapshot['page_on_front'] ) ? (int) $snapshot['page_on_front'] : 0;
		$page_for_posts = isset( $snapshot['page_for_posts'] ) ? (int) $snapshot['page_for_posts'] : 0;

		if ( ! in_array( $show_on_front, array( 'posts', 'page' ), true ) ) {
			$show_on_front = 'posts';
		}

		self::$applying_reading_settings = true;
		update_option( 'show_on_front', $show_on_front );
		update_option( 'page_on_front', $page_on_front );
		update_option( 'page_for_posts', $page_for_posts );
		self::$applying_reading_settings = false;
	}

	/**
	 * Whether a manual Reading change conflicts with ART Starter ownership.
	 *
	 * @param mixed $show_on_front  New show_on_front value.
	 * @param mixed $page_on_front  New page_on_front value.
	 * @param mixed $page_for_posts New page_for_posts value.
	 * @return bool
	 */
	public static function is_external_reading_change( $show_on_front, $page_on_front, $page_for_posts ) {
		$settings = Art_Starter_Homepage::get_all();

		if ( empty( $settings['use_as_front_page'] ) ) {
			return false;
		}

		$main_id = self::get_main_page_id();
		$blog_id = self::get_blog_page_id();

		if ( $main_id <= 0 || $blog_id <= 0 ) {
			return false;
		}

		return 'page' !== (string) $show_on_front
			|| (int) $page_on_front !== $main_id
			|| (int) $page_for_posts !== $blog_id;
	}

	/**
	 * Redirect away from the block/classic editor for managed pages.
	 */
	public static function block_managed_page_edit_screen() {
		if ( ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Admin screen routing only.
		$action = isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : '';

		if ( 'edit' !== $action ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Admin screen routing only.
		$post_id = isset( $_GET['post'] ) ? absint( wp_unslash( $_GET['post'] ) ) : 0;

		if ( $post_id <= 0 || ! self::is_managed_page( $post_id ) ) {
			return;
		}

		wp_safe_redirect(
			add_query_arg(
				array(
					'post_type'                 => 'page',
					'art_starter_managed_page'  => $post_id,
				),
				admin_url( 'edit.php' )
			)
		);
		exit;
	}

	/**
	 * Managed pages are created only by the plugin.
	 */
	public static function block_managed_page_create_redirect() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Admin screen routing only.
		if ( empty( $_GET['art_starter_managed'] ) ) {
			return;
		}

		wp_safe_redirect( admin_url( 'edit.php?post_type=page' ) );
		exit;
	}

	/**
	 * @param array<string, mixed> $data    Post data.
	 * @param array<string, mixed> $postarr Raw post data.
	 * @return array<string, mixed>
	 */
	public static function lock_managed_page_data( $data, $postarr ) {
		if ( self::$updating_managed_page ) {
			return $data;
		}

		$post_id = isset( $postarr['ID'] ) ? (int) $postarr['ID'] : 0;

		if ( $post_id <= 0 || ! self::is_managed_page( $post_id ) ) {
			return $data;
		}

		$role = get_post_meta( $post_id, self::META_MANAGED, true );

		if ( self::ROLE_MAIN === $role ) {
			$data['post_title']   = self::get_main_page_title();
			$data['post_content'] = self::get_main_page_content();
			$data['post_name']    = self::SLUG_MAIN;
		}

		if ( self::ROLE_BLOG === $role ) {
			$data['post_title']   = self::get_blog_page_title();
			$data['post_content'] = self::get_blog_page_content();
			$data['post_name']    = self::SLUG_BLOG;
		}

		$data['post_status'] = 'publish';

		return $data;
	}

	/**
	 * @param mixed   $delete        Whether to delete.
	 * @param WP_Post $post          Post object.
	 * @param bool    $force_delete  Force delete flag.
	 * @return mixed
	 */
	public static function prevent_managed_page_delete( $delete, $post, $force_delete = false ) {
		unset( $force_delete );

		if ( ! $post instanceof WP_Post || ! self::is_managed_page( (int) $post->ID ) ) {
			return $delete;
		}

		if ( self::$updating_managed_page || self::$applying_reading_settings ) {
			return $delete;
		}

		return false;
	}

	/**
	 * Show admin notices for managed pages and Reading errors.
	 */
	public static function render_admin_notices() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$user_id = get_current_user_id();
		$error   = get_transient( 'art_starter_reading_error_' . $user_id );

		if ( is_string( $error ) && '' !== $error ) {
			delete_transient( 'art_starter_reading_error_' . $user_id );
			echo '<div class="notice notice-error is-dismissible"><p>' . esc_html( $error ) . '</p></div>';
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Display-only notice flag.
		$managed_page_id = isset( $_GET['art_starter_managed_page'] ) ? absint( wp_unslash( $_GET['art_starter_managed_page'] ) ) : 0;

		if ( $managed_page_id <= 0 || ! self::is_managed_page( $managed_page_id ) ) {
			return;
		}

		$role    = get_post_meta( $managed_page_id, self::META_MANAGED, true );
		$message = self::ROLE_MAIN === $role
			? self::get_main_page_content()
			: ( self::ROLE_BLOG === $role ? self::get_blog_page_content() : '' );

		echo '<div class="notice notice-warning"><p>' . esc_html__( 'Эта страница управляется ART Starter. Редактирование отключено.', 'art-starter' ) . '</p>';

		if ( '' !== $message ) {
			echo '<p>' . esc_html( $message ) . '</p>';
		}

		echo '</div>';
	}

	/**
	 * Remove managed pages and restore Reading settings during uninstall cleanup.
	 */
	public static function cleanup_on_uninstall() {
		$state    = self::get_state();
		$snapshot = isset( $state['reading_snapshot'] ) && is_array( $state['reading_snapshot'] )
			? $state['reading_snapshot']
			: null;

		if ( is_array( $snapshot ) ) {
			self::restore_reading_snapshot( $snapshot );
		}

		self::$updating_managed_page = true;

		foreach ( array( self::ROLE_MAIN, self::ROLE_BLOG ) as $role ) {
			$page_id = self::find_page_id_by_role( $role );

			if ( $page_id > 0 ) {
				wp_delete_post( $page_id, true );
			}
		}

		self::$updating_managed_page = false;

		delete_option( self::OPTION );
	}

	/**
	 * @return array{main: string, blog: string}
	 */
	public static function get_public_page_urls() {
		$main_id = self::get_main_page_id();
		$blog_id = self::get_blog_page_id();

		return array(
			'main' => $main_id > 0 ? (string) home_url( '/' ) : '',
			'blog' => $blog_id > 0 ? (string) get_permalink( $blog_id ) : '',
		);
	}

	/**
	 * @return string
	 */
	public static function get_reading_warning_message() {
		$settings = Art_Starter_Homepage::get_all();

		if ( empty( $settings['use_as_front_page'] ) ) {
			return '';
		}

		if ( self::is_reading_configuration_active() ) {
			return '';
		}

		return __( 'Настройки чтения не совпадают с ожидаемыми ART Starter. Сохраните настройки главной ещё раз или проверьте «Настройки → Чтение».', 'art-starter' );
	}
}
