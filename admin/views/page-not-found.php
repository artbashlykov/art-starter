<?php
/**
 * Custom 404 page settings view.
 *
 * @package Art_Starter
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Template variables scoped to this view.

$option_name = Art_Starter_Not_Found::OPTION;

$buttons         = isset( $settings['buttons'] ) && is_array( $settings['buttons'] ) ? $settings['buttons'] : array();
$primary_button  = isset( $buttons[0] ) && is_array( $buttons[0] ) ? $buttons[0] : Art_Starter_Not_Found::get_default_primary_button();
$extra_buttons   = array_slice( $buttons, 1 );
$not_found_template = isset( $settings['template'] ) ? sanitize_key( (string) $settings['template'] ) : Art_Starter_Not_Found::get_default_template();

if ( ! Art_Starter_Not_Found::is_valid_template( $not_found_template ) ) {
	$not_found_template = Art_Starter_Not_Found::get_default_template();
}

$use_custom_not_found = ! empty( $settings['use_custom_not_found'] );
$image_url            = isset( $settings['image_url'] ) ? (string) $settings['image_url'] : '';
$code                 = isset( $settings['code'] ) ? (string) $settings['code'] : '404';
$title                = isset( $settings['title'] ) ? (string) $settings['title'] : '';

?>
<div class="wrap art-starter-admin">
	<h1><?php esc_html_e( 'Страница 404', 'art-starter' ); ?></h1>

	<p class="description">
		<?php esc_html_e( 'Выберите цветовую схему и заполните содержимое. Справа — превью (уменьшено в 1,5 раза), которое обновляется при вводе.', 'art-starter' ); ?>
	</p>

	<?php Art_Starter_Admin_Not_Found::render_settings_saved_notice(); ?>

	<div class="art-starter-homepage-layout">
		<div class="art-starter-homepage-settings">
			<form method="post" action="options.php" class="art-starter-not-found-form" id="art-starter-not-found-form">
				<?php settings_fields( Art_Starter_Admin_Not_Found::SETTINGS_GROUP ); ?>

				<div class="art-starter-panel" style="margin-top:10px;">
					<h2><?php esc_html_e( 'Выбор шаблона страницы', 'art-starter' ); ?></h2>
					<p class="description">
						<?php esc_html_e( 'Шаблон — это цветовая схема страницы 404. Доступно несколько вариантов.', 'art-starter' ); ?>
					</p>

					<table class="form-table art-starter-form-table" role="presentation">
						<tr>
							<th scope="row">
								<label for="art-starter-not-found-template"><?php esc_html_e( 'Вариант', 'art-starter' ); ?></label>
							</th>
							<td>
								<select id="art-starter-not-found-template" class="art-starter-field" name="<?php echo esc_attr( $option_name ); ?>[template]">
									<?php foreach ( Art_Starter_Not_Found::get_templates() as $template_slug => $template_label ) : ?>
										<option value="<?php echo esc_attr( $template_slug ); ?>" <?php selected( $settings['template'], $template_slug ); ?>>
											<?php echo esc_html( $template_label ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Страница 404 сайта', 'art-starter' ); ?></th>
							<td>
								<label for="art-starter-not-found-use-custom">
									<input
										type="checkbox"
										id="art-starter-not-found-use-custom"
										name="<?php echo esc_attr( $option_name ); ?>[use_custom_not_found]"
										value="1"
										<?php checked( $use_custom_not_found ); ?>
									>
									<?php esc_html_e( 'Использовать страницу 404 АРТ Стартер', 'art-starter' ); ?>
								</label>
								<p class="description">
									<?php esc_html_e( 'На фронтенде стили и шрифты активной темы к этой странице не применяются.', 'art-starter' ); ?>
								</p>
							</td>
						</tr>
					</table>
				</div>

				<div class="art-starter-panel">
					<h2><?php esc_html_e( 'Содержимое', 'art-starter' ); ?></h2>

					<table class="form-table art-starter-form-table" role="presentation">
						<tr>
							<th scope="row">
								<label for="art-starter-not-found-image"><?php esc_html_e( 'Изображение', 'art-starter' ); ?></label>
							</th>
							<td>
								<?php
								Art_Starter_Admin_Homepage::render_image_field(
									'art-starter-not-found-image',
									$option_name . '[image_url]',
									$image_url,
									'art-starter-media-field__thumb--not-found',
									true,
									true
								);
								?>
								<p class="description"><?php esc_html_e( 'Отображается вверху страницы, выше кода и текста.', 'art-starter' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="art-starter-not-found-code"><?php esc_html_e( 'Код', 'art-starter' ); ?></label>
							</th>
							<td>
								<input
									type="text"
									class="art-starter-field"
									id="art-starter-not-found-code"
									name="<?php echo esc_attr( $option_name ); ?>[code]"
									value="<?php echo esc_attr( $code ); ?>"
								>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="art-starter-not-found-title"><?php esc_html_e( 'Заголовок', 'art-starter' ); ?></label>
							</th>
							<td>
								<input
									type="text"
									class="art-starter-field"
									id="art-starter-not-found-title"
									name="<?php echo esc_attr( $option_name ); ?>[title]"
									value="<?php echo esc_attr( $title ); ?>"
								>
							</td>
						</tr>
					</table>
				</div>

				<div class="art-starter-panel">
					<h2><?php esc_html_e( 'Кнопки', 'art-starter' ); ?></h2>
					<p class="description">
						<?php esc_html_e( 'Первая кнопка — «Вернуться на главную». Можно добавить ещё две кнопки с иконкой, текстом и ссылкой.', 'art-starter' ); ?>
					</p>

					<div class="art-starter-not-found-primary-button">
						<h3><?php esc_html_e( 'Основная кнопка', 'art-starter' ); ?></h3>
						<?php Art_Starter_Admin_Not_Found::render_primary_button_row( $primary_button ); ?>
					</div>

					<div class="art-starter-not-found-extra-buttons" id="art-starter-not-found-extra-buttons">
						<h3><?php esc_html_e( 'Дополнительные кнопки', 'art-starter' ); ?></h3>
						<?php
						$extra_index = 1;
						foreach ( $extra_buttons as $extra_button ) {
							if ( ! is_array( $extra_button ) ) {
								continue;
							}
							Art_Starter_Admin_Not_Found::render_extra_button_row( $extra_index, $extra_button );
							++$extra_index;
						}
						?>
					</div>

					<p>
						<button type="button" class="button" id="art-starter-not-found-add-button">
							<?php esc_html_e( 'Добавить кнопку', 'art-starter' ); ?>
						</button>
					</p>
				</div>

				<?php submit_button( __( 'Сохранить', 'art-starter' ) ); ?>
			</form>
		</div>

		<aside class="art-starter-homepage-preview" aria-label="<?php echo esc_attr__( 'Превью', 'art-starter' ); ?>">
			<div
				class="<?php echo esc_attr( Art_Starter_Not_Found::get_template_preview_frame_class( $not_found_template ) ); ?>"
				id="art-starter-not-found-preview"
			>
				<div class="art-starter-not-found-preview__scale">
					<div
						class="art-starter-not-found-shell <?php echo esc_attr( Art_Starter_Not_Found::get_template_body_class( $not_found_template ) ); ?>"
						id="art-starter-not-found-preview-shell"
					>
						<div class="art-starter-not-found-card">
							<div class="art-starter-not-found-image" data-bind="image" hidden></div>
							<div class="art-starter-not-found-code" data-bind="code"><?php echo esc_html( $code ); ?></div>
							<div class="art-starter-not-found-title" data-bind="title"><?php echo esc_html( $title ); ?></div>
							<div class="art-starter-not-found-buttons" data-bind="buttons"></div>
						</div>
					</div>
				</div>
			</div>

			<script type="application/json" id="art-starter-not-found-data"><?php
			echo wp_json_encode(
				$settings,
				JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
			);
			?></script>
		</aside>
	</div>

	<div class="art-starter-icon-picker" id="art-starter-icon-picker" hidden>
		<div class="art-starter-icon-picker__backdrop" data-art-starter-icon-picker-close></div>
		<div class="art-starter-icon-picker__dialog" role="dialog" aria-modal="true" aria-labelledby="art-starter-icon-picker-title">
			<div class="art-starter-icon-picker__header">
				<h2 id="art-starter-icon-picker-title"><?php esc_html_e( 'Выбрать иконку', 'art-starter' ); ?></h2>
				<button type="button" class="art-starter-icon-picker__close" data-art-starter-icon-picker-close aria-label="<?php echo esc_attr__( 'Закрыть', 'art-starter' ); ?>">&times;</button>
			</div>
			<div class="art-starter-icon-picker__body" id="art-starter-icon-picker-grid"></div>
		</div>
	</div>
</div>

<template id="art-starter-not-found-extra-button-template">
	<?php Art_Starter_Admin_Not_Found::render_extra_button_row( 1, Art_Starter_Not_Found::get_default_extra_button() ); ?>
</template>
