<?php
/**
 * Admin page: Homepage templates.
 *
 * @package Art_Starter
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Art_Starter_Admin_Homepage
 */
class Art_Starter_Admin_Homepage {

	const PAGE_SLUG = 'art-starter-homepage';

	const SETTINGS_GROUP = 'art_starter_homepage_group';

	/**
	 * Register hooks.
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
	}

	/**
	 * Register settings.
	 */
	public static function register_settings() {
		register_setting(
			self::SETTINGS_GROUP,
			Art_Starter_Homepage::OPTION,
			array(
				'sanitize_callback' => array( 'Art_Starter_Homepage', 'sanitize' ),
			)
		);
	}

	/**
	 * Render page.
	 */
	public static function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		Art_Starter_Homepage::sync_front_page_flag_with_reading();

		$settings = Art_Starter_Homepage::get_all();

		include ART_STARTER_PLUGIN_DIR . 'admin/views/page-homepage.php';
	}

	/**
	 * Show success notice after options.php saves settings.
	 */
	public static function render_settings_saved_notice() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- options.php redirect flag only.
		if ( empty( $_GET['settings-updated'] ) ) {
			return;
		}

		echo '<div class="notice notice-success is-dismissible"><p>';
		esc_html_e( 'Настройки сохранены.', 'art-starter' );
		echo '</p></div>';
	}

	/**
	 * Render panel heading with front-end visibility toggle.
	 *
	 * @param string               $title     Panel title.
	 * @param string               $block_key Block key.
	 * @param array<string, mixed> $settings  Homepage settings.
	 */
	public static function render_panel_heading( $title, $block_key, $settings ) {
		$option_name = Art_Starter_Homepage::OPTION;
		$block_key   = sanitize_key( (string) $block_key );
		$blocks      = isset( $settings['blocks'] ) && is_array( $settings['blocks'] ) ? $settings['blocks'] : array();
		$hidden      = ! empty( $blocks[ $block_key ]['hidden'] );

		?>
		<div class="art-starter-panel__heading">
			<h2><?php echo esc_html( $title ); ?></h2>
			<label class="art-starter-panel__hide">
				<input
					type="checkbox"
					name="<?php echo esc_attr( $option_name ); ?>[blocks][<?php echo esc_attr( $block_key ); ?>][hidden]"
					value="1"
					<?php checked( $hidden ); ?>
					data-art-starter-block-visibility="<?php echo esc_attr( $block_key ); ?>"
				>
				<?php esc_html_e( 'Скрыть на сайте', 'art-starter' ); ?>
			</label>
		</div>
		<?php
	}

	/**
	 * Render image field with WordPress media library picker.
	 *
	 * @param string $input_id      Input element ID.
	 * @param string $input_name    Input name attribute.
	 * @param string $current_url   Current image URL.
	 * @param string $preview_class Optional preview modifier class.
	 * @param bool   $hide_url_input Whether to hide the URL input (media library only).
	 * @param bool   $inline_layout  Whether to show preview and actions on one row.
	 */
	public static function render_image_field( $input_id, $input_name, $current_url, $preview_class = '', $hide_url_input = false, $inline_layout = false ) {
		$current_url   = (string) $current_url;
		$preview_class = trim( 'art-starter-media-field__thumb ' . $preview_class );
		$has_image     = '' !== $current_url;
		$input_type    = $hide_url_input ? 'hidden' : 'url';
		$input_class   = 'art-starter-media-field__input' . ( $hide_url_input ? '' : ' art-starter-field' );
		$field_class   = 'art-starter-media-field';

		if ( $hide_url_input ) {
			$field_class .= ' art-starter-media-field--library-only';
		}
		if ( $inline_layout ) {
			$field_class .= ' art-starter-media-field--inline';
		}

		?>
		<div class="<?php echo esc_attr( $field_class ); ?>" data-art-starter-media-field>
			<input
				type="<?php echo esc_attr( $input_type ); ?>"
				class="<?php echo esc_attr( $input_class ); ?>"
				id="<?php echo esc_attr( $input_id ); ?>"
				name="<?php echo esc_attr( $input_name ); ?>"
				value="<?php echo esc_attr( $current_url ); ?>"
			>
			<div class="art-starter-media-field__row">
				<div class="art-starter-media-field__preview" <?php echo $has_image ? '' : ' hidden'; ?>>
					<img
						class="<?php echo esc_attr( $preview_class ); ?>"
						src="<?php echo esc_url( $current_url ); ?>"
						alt=""
						decoding="async"
					>
				</div>
				<div class="art-starter-media-field__actions">
					<button type="button" class="button art-starter-media-field__select">
						<?php echo esc_html( $has_image ? __( 'Заменить изображение', 'art-starter' ) : __( 'Выбрать изображение', 'art-starter' ) ); ?>
					</button>
					<button type="button" class="button-link-delete art-starter-media-field__remove" <?php disabled( ! $has_image ); ?>>
						<?php esc_html_e( 'Удалить', 'art-starter' ); ?>
					</button>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render site favicon field (stores attachment ID for site_icon option).
	 *
	 * @param string $input_id        Input element ID.
	 * @param string $input_name      Input name attribute.
	 * @param int    $attachment_id   Current site icon attachment ID.
	 */
	public static function render_site_icon_field( $input_id, $input_name, $attachment_id = 0 ) {
		$attachment_id = (int) $attachment_id;
		$image_url     = $attachment_id > 0 ? wp_get_attachment_image_url( $attachment_id, array( 64, 64 ) ) : '';
		$has_image     = is_string( $image_url ) && '' !== $image_url;

		?>
		<div class="art-starter-media-field art-starter-media-field--library-only art-starter-media-field--inline" data-art-starter-site-icon-field>
			<input
				type="hidden"
				class="art-starter-site-icon-field__input"
				id="<?php echo esc_attr( $input_id ); ?>"
				name="<?php echo esc_attr( $input_name ); ?>"
				value="<?php echo esc_attr( (string) $attachment_id ); ?>"
			>
			<div class="art-starter-media-field__row">
				<div class="art-starter-media-field__preview" <?php echo $has_image ? '' : ' hidden'; ?>>
					<img
						class="art-starter-media-field__thumb art-starter-media-field__thumb--favicon"
						src="<?php echo esc_url( $image_url ); ?>"
						alt=""
						decoding="async"
					>
				</div>
				<div class="art-starter-media-field__actions">
					<button type="button" class="button art-starter-site-icon-field__select">
						<?php echo esc_html( $has_image ? __( 'Заменить фавикон', 'art-starter' ) : __( 'Выбрать фавикон', 'art-starter' ) ); ?>
					</button>
					<button type="button" class="button-link-delete art-starter-site-icon-field__remove" <?php disabled( ! $has_image ); ?>>
						<?php esc_html_e( 'Удалить', 'art-starter' ); ?>
					</button>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render icon picker field.
	 *
	 * @param string               $input_name   Input name attribute.
	 * @param string               $current_slug Current icon slug.
	 * @param array<string, mixed> $args         Field args.
	 */
	public static function render_icon_field( $input_name, $current_slug = '', $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'input_id'     => '',
				'categories'   => Art_Starter_Icons::get_picker_categories(),
				'allow_none'   => true,
				'default_slug' => '',
			)
		);

		$current_slug = sanitize_key( (string) $current_slug );
		$display_slug = '' !== $current_slug ? $current_slug : sanitize_key( (string) $args['default_slug'] );
		$icon         = $display_slug ? Art_Starter_Icons::get( $display_slug ) : null;
		$input_id     = (string) $args['input_id'];
		$categories   = implode( ',', array_map( 'sanitize_key', (array) $args['categories'] ) );

		?>
		<div
			class="art-starter-icon-field"
			data-art-starter-icon-field
			data-icon-categories="<?php echo esc_attr( $categories ); ?>"
			data-icon-allow-none="<?php echo ! empty( $args['allow_none'] ) ? '1' : '0'; ?>"
			data-icon-default="<?php echo esc_attr( (string) $args['default_slug'] ); ?>"
		>
			<input
				type="hidden"
				class="art-starter-icon-field__input"
				<?php if ( $input_id ) : ?>
					id="<?php echo esc_attr( $input_id ); ?>"
				<?php endif; ?>
				name="<?php echo esc_attr( $input_name ); ?>"
				value="<?php echo esc_attr( $current_slug ); ?>"
			>
			<div class="art-starter-icon-field__controls">
				<button type="button" class="art-starter-icon-field__preview-btn art-starter-icon-field__toggle" title="<?php esc_attr_e( 'Выбрать иконку', 'art-starter' ); ?>">
					<span class="art-starter-icon-field__preview">
						<?php
						if ( $icon ) {
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG from internal icon registry.
							echo Art_Starter_Icons::render( $display_slug, array( 'class' => 'art-starter-icon-field__icon' ) );
						} else {
							echo '<span class="art-starter-icon-field__placeholder">' . esc_html__( 'Без', 'art-starter' ) . '</span>';
						}
						?>
					</span>
				</button>
				<button type="button" class="button art-starter-icon-field__toggle">
					<?php esc_html_e( 'Выбрать иконку', 'art-starter' ); ?>
				</button>
				<button type="button" class="button-link art-starter-icon-field__reset" <?php disabled( $current_slug === (string) $args['default_slug'] || ( '' === $current_slug && '' !== (string) $args['default_slug'] ) ); ?>>
					<?php esc_html_e( 'Сбросить', 'art-starter' ); ?>
				</button>
			</div>
		</div>
		<?php
	}

	/**
	 * Render move up/down controls for sortable rows.
	 */
	private static function render_sort_buttons() {
		?>
		<div class="art-starter-sortable-row__move">
			<button type="button" class="button art-starter-sortable-row__up" aria-label="<?php echo esc_attr__( 'Выше', 'art-starter' ); ?>">&#8593;</button>
			<button type="button" class="button art-starter-sortable-row__down" aria-label="<?php echo esc_attr__( 'Ниже', 'art-starter' ); ?>">&#8595;</button>
		</div>
		<?php
	}

	/**
	 * Render a single dynamic link row.
	 *
	 * @param array<string, string> $item Link item.
	 */
	public static function render_link_row( $item = array() ) {
		$option_name = Art_Starter_Homepage::OPTION;
		$label       = isset( $item['label'] ) ? (string) $item['label'] : '';
		$url         = isset( $item['url'] ) ? (string) $item['url'] : '';
		$icon        = isset( $item['icon'] ) ? (string) $item['icon'] : '';

		?>
		<div class="art-starter-link-row">
			<?php self::render_sort_buttons(); ?>
			<div class="art-starter-link-row__icon">
				<?php
				self::render_icon_field(
					$option_name . '[links][][icon]',
					$icon,
					array(
						'categories'   => Art_Starter_Icons::get_picker_categories(),
						'default_slug' => Art_Starter_Icons::DEFAULT_LINK_ICON,
					)
				);
				?>
			</div>
			<div class="art-starter-link-row__fields">
				<p class="art-starter-link-row__field">
					<label class="screen-reader-text"><?php esc_html_e( 'Текст ссылки', 'art-starter' ); ?></label>
					<input
						type="text"
						class="art-starter-field"
						name="<?php echo esc_attr( $option_name ); ?>[links][][label]"
						value="<?php echo esc_attr( $label ); ?>"
						placeholder="<?php echo esc_attr__( 'Текст ссылки', 'art-starter' ); ?>"
					>
				</p>
				<p class="art-starter-link-row__field">
					<label class="screen-reader-text"><?php esc_html_e( 'URL', 'art-starter' ); ?></label>
					<input
						type="text"
						class="art-starter-field"
						name="<?php echo esc_attr( $option_name ); ?>[links][][url]"
						value="<?php echo esc_attr( $url ); ?>"
						placeholder="<?php echo esc_attr__( 'example.com или https://...', 'art-starter' ); ?>"
					>
				</p>
			</div>
			<button type="button" class="button-link-delete art-starter-link-row__remove" aria-label="<?php echo esc_attr__( 'Удалить ссылку', 'art-starter' ); ?>">
				<?php esc_html_e( 'Удалить', 'art-starter' ); ?>
			</button>
		</div>
		<?php
	}

	/**
	 * Render a single dynamic social network row.
	 *
	 * @param array<string, string> $item  Social item.
	 * @param int|null              $index Row index for stable field names.
	 */
	public static function render_social_item_row( $item = array(), $index = null ) {
		$option_name = Art_Starter_Homepage::OPTION;
		$network     = isset( $item['network'] ) ? sanitize_key( (string) $item['network'] ) : '';
		$url         = isset( $item['url'] ) ? (string) $item['url'] : '';
		$networks    = Art_Starter_Icons::get_social_networks();
		$row_index   = null === $index ? '' : '[' . (int) $index . ']';

		if ( 'mail' === $network && preg_match( '#^mailto:#i', $url ) ) {
			$url = substr( $url, 7 );
		}

		?>
		<div class="art-starter-social-item-row">
			<?php self::render_sort_buttons(); ?>
			<div class="art-starter-social-item-row__fields">
				<p class="art-starter-social-item-row__field">
					<label class="screen-reader-text"><?php esc_html_e( 'Соцсеть', 'art-starter' ); ?></label>
					<select
						class="art-starter-field art-starter-social-item-row__network"
						name="<?php echo esc_attr( $option_name ); ?>[socials]<?php echo esc_attr( $row_index ); ?>[network]"
					>
						<option value=""><?php esc_html_e( '— выберите —', 'art-starter' ); ?></option>
						<?php foreach ( $networks as $slug => $network_label ) : ?>
							<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $network, $slug ); ?>>
								<?php echo esc_html( $network_label ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</p>
				<p class="art-starter-social-item-row__field">
					<label class="screen-reader-text"><?php esc_html_e( 'Ссылка', 'art-starter' ); ?></label>
					<input
						type="text"
						class="art-starter-field art-starter-social-item-row__url"
						name="<?php echo esc_attr( $option_name ); ?>[socials]<?php echo esc_attr( $row_index ); ?>[url]"
						value="<?php echo esc_attr( $url ); ?>"
						placeholder="<?php echo esc_attr__( 'example.com или https://...', 'art-starter' ); ?>"
					>
				</p>
			</div>
			<button type="button" class="button-link-delete art-starter-social-item-row__remove" aria-label="<?php echo esc_attr__( 'Удалить соцсеть', 'art-starter' ); ?>">
				<?php esc_html_e( 'Удалить', 'art-starter' ); ?>
			</button>
		</div>
		<?php
	}
}

