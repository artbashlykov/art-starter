<?php
/**
 * Admin page: primary WordPress setup.
 *
 * @package Art_Starter
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Art_Starter_Admin_Initial_Setup
 */
class Art_Starter_Admin_Initial_Setup {

	const PAGE_SLUG = 'art-starter-initial-setup';

	const ACTION = 'art_starter_initial_setup';

	/**
	 * Register hooks.
	 */
	public static function init() {
		add_action( 'admin_post_' . self::ACTION, array( __CLASS__, 'handle_save' ) );
	}

	/**
	 * Render admin page.
	 */
	public static function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$form_state = Art_Starter_Initial_Setup::get_form_state();

		include ART_STARTER_PLUGIN_DIR . 'admin/views/page-initial-setup.php';
	}

	/**
	 * Handle form submission.
	 */
	public static function handle_save() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Недостаточно прав.', 'art-starter' ) );
		}

		check_admin_referer( self::ACTION );

		$results = Art_Starter_Initial_Setup::apply( wp_unslash( $_POST ) );

		set_transient(
			self::get_results_transient_key(),
			$results,
			MINUTE_IN_SECONDS
		);

		wp_safe_redirect(
			add_query_arg(
				array(
					'page'    => self::PAGE_SLUG,
					'applied' => '1',
				),
				admin_url( 'admin.php' )
			)
		);
		exit;
	}

	/**
	 * Render notices after save.
	 */
	public static function render_notices() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Display-only success flag.
		if ( empty( $_GET['applied'] ) ) {
			return;
		}

		$results = get_transient( self::get_results_transient_key() );

		if ( ! is_array( $results ) ) {
			return;
		}

		delete_transient( self::get_results_transient_key() );

		foreach ( array( 'updated', 'skipped', 'errors' ) as $type ) {
			if ( empty( $results[ $type ] ) || ! is_array( $results[ $type ] ) ) {
				continue;
			}

			$class = 'errors' === $type ? 'notice-error' : ( 'skipped' === $type ? 'notice-warning' : 'notice-success' );

			echo '<div class="notice ' . esc_attr( $class ) . ' is-dismissible"><ul class="art-starter-notice-list">';

			foreach ( $results[ $type ] as $message ) {
				echo '<li>' . esc_html( (string) $message ) . '</li>';
			}

			echo '</ul></div>';
		}
	}

	/**
	 * @return string
	 */
	private static function get_results_transient_key() {
		$user_id = get_current_user_id();

		return 'art_starter_initial_setup_' . $user_id;
	}

	/**
	 * Render preview for a removable post or page.
	 *
	 * @param array<string, string> $item    Preview item.
	 * @param string                $caption Preview caption.
	 */
	public static function render_removable_post_preview( $item, $caption ) {
		if ( empty( $item ) || ! is_array( $item ) ) {
			return;
		}

		?>
		<div class="art-starter-removable-preview">
			<p class="art-starter-removable-preview__caption"><?php echo esc_html( $caption ); ?></p>
			<ul class="art-starter-removable-preview__list">
				<li>
					<strong><?php echo esc_html( $item['title'] ); ?></strong>
					<span class="art-starter-removable-preview__meta">
						<?php
						printf(
							/* translators: 1: content type, 2: slug, 3: ID */
							esc_html__( 'тип: %1$s, slug: %2$s, ID: %3$s', 'art-starter' ),
							esc_html( $item['type'] ),
							esc_html( $item['slug'] ),
							esc_html( $item['id'] )
						);
						?>
					</span>
					<span class="art-starter-removable-preview__links">
						<?php if ( ! empty( $item['edit'] ) ) : ?>
							<a href="<?php echo esc_url( $item['edit'] ); ?>"><?php esc_html_e( 'Редактировать', 'art-starter' ); ?></a>
						<?php endif; ?>
						<?php if ( ! empty( $item['view'] ) ) : ?>
							<a href="<?php echo esc_url( $item['view'] ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Посмотреть', 'art-starter' ); ?></a>
						<?php endif; ?>
					</span>
				</li>
			</ul>
		</div>
		<?php
	}

	/**
	 * Render preview for removable themes.
	 *
	 * @param array<int, array<string, string>> $themes  Theme list.
	 * @param string                            $caption Preview caption.
	 */
	public static function render_removable_themes_preview( $themes, $caption ) {
		if ( empty( $themes ) || ! is_array( $themes ) ) {
			return;
		}

		?>
		<div class="art-starter-removable-preview">
			<p class="art-starter-removable-preview__caption"><?php echo esc_html( $caption ); ?></p>
			<ul class="art-starter-removable-preview__list">
				<?php foreach ( $themes as $theme ) : ?>
					<li>
						<strong><?php echo esc_html( $theme['name'] ); ?></strong>
						<span class="art-starter-removable-preview__meta">
							<?php
							printf(
								/* translators: 1: theme slug, 2: theme version */
								esc_html__( 'slug: %1$s, версия: %2$s', 'art-starter' ),
								esc_html( $theme['slug'] ),
								esc_html( $theme['version'] )
							);
							?>
						</span>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}
}
