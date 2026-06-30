<?php
/**
 * Admin page: custom 404 page.
 *
 * @package Art_Starter
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class Art_Starter_Admin_Not_Found
 */
class Art_Starter_Admin_Not_Found {

	const SETTINGS_GROUP = 'art_starter_not_found_group';

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
			Art_Starter_Not_Found::OPTION,
			array(
				'sanitize_callback' => array( 'Art_Starter_Not_Found', 'sanitize' ),
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

		$settings = Art_Starter_Not_Found::get_all();

		include ART_STARTER_PLUGIN_DIR . 'admin/views/page-not-found.php';
	}

	/**
	 * Render primary 404 button row.
	 *
	 * @param array<string, string> $item Button item.
	 */
	public static function render_primary_button_row( $item = array() ) {
		$option_name = Art_Starter_Not_Found::OPTION;
		$label       = isset( $item['label'] ) ? (string) $item['label'] : '';
		$url         = isset( $item['url'] ) ? (string) $item['url'] : '';
		$icon        = isset( $item['icon'] ) ? (string) $item['icon'] : '';
		$name_prefix = $option_name . '[buttons][0]';

		?>
		<div class="art-starter-not-found-button-row art-starter-not-found-button-row--primary">
			<div class="art-starter-not-found-button-row__icon">
				<?php
				Art_Starter_Admin_Homepage::render_icon_field(
					$name_prefix . '[icon]',
					$icon,
					array(
						'input_id'     => 'art-starter-not-found-primary-icon',
						'categories'   => Art_Starter_Icons::get_picker_categories(),
						'default_slug' => Art_Starter_Not_Found::DEFAULT_PRIMARY_ICON,
						'allow_none'   => false,
					)
				);
				?>
			</div>
			<p class="art-starter-not-found-button-row__field">
				<label class="art-starter-not-found-button-row__label" for="art-starter-not-found-primary-label"><?php esc_html_e( 'Текст кнопки', 'art-starter' ); ?></label>
				<input
					type="text"
					class="art-starter-field"
					id="art-starter-not-found-primary-label"
					name="<?php echo esc_attr( $name_prefix . '[label]' ); ?>"
					value="<?php echo esc_attr( $label ); ?>"
					placeholder="<?php echo esc_attr__( 'Текст кнопки', 'art-starter' ); ?>"
				>
			</p>
			<p class="art-starter-not-found-button-row__field">
				<label class="art-starter-not-found-button-row__label" for="art-starter-not-found-primary-url"><?php esc_html_e( 'Ссылка кнопки', 'art-starter' ); ?></label>
				<input
					type="text"
					class="art-starter-field"
					id="art-starter-not-found-primary-url"
					name="<?php echo esc_attr( $name_prefix . '[url]' ); ?>"
					value="<?php echo esc_attr( $url ); ?>"
					placeholder="<?php echo esc_attr__( 'Если пусто - ссылка на главную', 'art-starter' ); ?>"
				>
			</p>
		</div>
		<?php
	}

	/**
	 * Render extra 404 button row.
	 *
	 * @param int                   $index Button index (1 or 2).
	 * @param array<string, string> $item  Button item.
	 */
	public static function render_extra_button_row( $index, $item = array() ) {
		$option_name = Art_Starter_Not_Found::OPTION;
		$index       = max( 1, min( Art_Starter_Not_Found::MAX_EXTRA_BUTTONS, (int) $index ) );
		$label       = isset( $item['label'] ) ? (string) $item['label'] : '';
		$url         = isset( $item['url'] ) ? (string) $item['url'] : '';
		$icon        = isset( $item['icon'] ) ? (string) $item['icon'] : '';
		$name_prefix = $option_name . '[buttons][' . $index . ']';

		?>
		<div class="art-starter-not-found-button-row" data-not-found-extra-button>
			<div class="art-starter-not-found-button-row__icon">
				<?php
				Art_Starter_Admin_Homepage::render_icon_field(
					$name_prefix . '[icon]',
					$icon,
					array(
						'input_id'     => 'art-starter-not-found-extra-icon-' . $index,
						'categories'   => Art_Starter_Icons::get_picker_categories(),
						'default_slug' => Art_Starter_Not_Found::DEFAULT_EXTRA_ICON,
					)
				);
				?>
			</div>
			<p class="art-starter-not-found-button-row__field">
				<label class="art-starter-not-found-button-row__label"><?php esc_html_e( 'Текст кнопки', 'art-starter' ); ?></label>
				<input
					type="text"
					class="art-starter-field"
					name="<?php echo esc_attr( $name_prefix . '[label]' ); ?>"
					value="<?php echo esc_attr( $label ); ?>"
					placeholder="<?php echo esc_attr__( 'Текст кнопки', 'art-starter' ); ?>"
					data-art-starter-button-label
					autocomplete="off"
				>
			</p>
			<p class="art-starter-not-found-button-row__field">
				<label class="art-starter-not-found-button-row__label"><?php esc_html_e( 'Ссылка кнопки', 'art-starter' ); ?></label>
				<input
					type="text"
					class="art-starter-field"
					name="<?php echo esc_attr( $name_prefix . '[url]' ); ?>"
					value="<?php echo esc_attr( $url ); ?>"
					placeholder="<?php echo esc_attr__( 'example.com или https://...', 'art-starter' ); ?>"
					data-art-starter-button-url
					autocomplete="off"
				>
			</p>
			<button type="button" class="button-link-delete art-starter-not-found-button-row__remove" aria-label="<?php echo esc_attr__( 'Удалить кнопку', 'art-starter' ); ?>">
				<?php esc_html_e( 'Удалить', 'art-starter' ); ?>
			</button>
		</div>
		<?php
	}
}
