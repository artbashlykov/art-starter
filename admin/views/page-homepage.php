<?php
/**
 * Homepage templates page view.
 *
 * @package Art_Starter
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Template variables scoped to this view.

$option_name = Art_Starter_Homepage::OPTION;

$profile   = isset( $settings['profile'] ) && is_array( $settings['profile'] ) ? $settings['profile'] : array();
$cta       = isset( $settings['cta'] ) && is_array( $settings['cta'] ) ? $settings['cta'] : array();
$links     = isset( $settings['links'] ) && is_array( $settings['links'] ) ? $settings['links'] : array();
$recommend = isset( $settings['recommend'] ) && is_array( $settings['recommend'] ) ? $settings['recommend'] : array();
$socials   = isset( $settings['socials'] ) && is_array( $settings['socials'] ) ? $settings['socials'] : array();
$homepage_template = isset( $settings['template'] ) ? sanitize_key( (string) $settings['template'] ) : Art_Starter_Homepage::get_default_template();

if ( ! Art_Starter_Homepage::is_valid_template( $homepage_template ) ) {
	$homepage_template = Art_Starter_Homepage::get_default_template();
}

$use_as_front_page = ! empty( $settings['use_as_front_page'] );
$reading_url       = admin_url( 'options-reading.php' );

?>
<div class="art-starter-admin-tab-homepage">
	<p class="description">
		<?php esc_html_e( 'Выберите шаблон и заполните блоки. Справа — превью (уменьшено в 1,5 раза), которое обновляется при вводе.', 'art-starter' ); ?>
	</p>

	<div class="art-starter-homepage-layout">
		<div class="art-starter-homepage-settings">
			<form method="post" action="options.php" class="art-starter-homepage-form" id="art-starter-homepage-form">
				<?php settings_fields( Art_Starter_Admin_Homepage::SETTINGS_GROUP ); ?>

				<div class="art-starter-panel" style="margin-top:10px;">
					<h2><?php esc_html_e( 'Шаблон', 'art-starter' ); ?></h2>
					<p class="description">
						<?php esc_html_e( 'Доступно несколько цветовых вариантов. Справа — превью выбранного шаблона.', 'art-starter' ); ?>
					</p>

					<table class="form-table art-starter-form-table" role="presentation">
						<tr>
							<th scope="row">
								<label for="art-starter-homepage-template"><?php esc_html_e( 'Вариант', 'art-starter' ); ?></label>
							</th>
							<td>
								<select id="art-starter-homepage-template" class="art-starter-field" name="<?php echo esc_attr( $option_name ); ?>[template]">
									<?php foreach ( Art_Starter_Homepage::get_templates() as $template_slug => $template_label ) : ?>
										<option value="<?php echo esc_attr( $template_slug ); ?>" <?php selected( $settings['template'], $template_slug ); ?>>
											<?php echo esc_html( $template_label ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Главная сайта', 'art-starter' ); ?></th>
							<td>
								<label for="art-starter-homepage-use-as-front">
									<input
										type="checkbox"
										id="art-starter-homepage-use-as-front"
										name="<?php echo esc_attr( $option_name ); ?>[use_as_front_page]"
										value="1"
										<?php checked( $use_as_front_page ); ?>
									>
									<?php esc_html_e( 'Использовать в качестве главной', 'art-starter' ); ?>
								</label>
								<p class="description">
									<?php
									printf(
										wp_kses(
											/* translators: %s: link to Settings → Reading screen. */
											__( 'Можно назначить другую главную в %s. Если там выбрана статическая страница, этот чекбокс снимется автоматически.', 'art-starter' ),
											array(
												'a' => array(
													'href' => array(),
												),
											)
										),
										'<a href="' . esc_url( $reading_url ) . '">' . esc_html__( 'Настройки → Чтение', 'art-starter' ) . '</a>'
									);
									?>
								</p>
							</td>
						</tr>
					</table>
				</div>

				<div class="art-starter-panel">
					<?php Art_Starter_Admin_Homepage::render_panel_heading( __( 'ШАПКА ПРОФИЛЯ', 'art-starter' ), 'profile', $settings ); ?>

					<table class="form-table art-starter-form-table" role="presentation">
						<tr>
							<th scope="row">
								<label for="art-starter-homepage-avatar"><?php esc_html_e( 'Аватар', 'art-starter' ); ?></label>
							</th>
							<td>
								<?php
								Art_Starter_Admin_Homepage::render_image_field(
									'art-starter-homepage-avatar',
									$option_name . '[profile][avatar_url]',
									(string) ( $profile['avatar_url'] ?? '' ),
									'art-starter-media-field__thumb--avatar',
									true,
									true
								);
								?>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="art-starter-homepage-name"><?php esc_html_e( 'Имя', 'art-starter' ); ?></label>
							</th>
							<td>
								<input
									type="text"
									class="art-starter-field"
									id="art-starter-homepage-name"
									name="<?php echo esc_attr( $option_name ); ?>[profile][name]"
									value="<?php echo esc_attr( (string) ( $profile['name'] ?? '' ) ); ?>"
								>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="art-starter-homepage-roles"><?php esc_html_e( 'Роли / теги', 'art-starter' ); ?></label>
							</th>
							<td>
								<input
									type="text"
									class="art-starter-field"
									id="art-starter-homepage-roles"
									name="<?php echo esc_attr( $option_name ); ?>[profile][roles]"
									value="<?php echo esc_attr( (string) ( $profile['roles'] ?? '' ) ); ?>"
									placeholder="<?php echo esc_attr__( 'Например: Маркетолог • Автор курсов', 'art-starter' ); ?>"
								>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="art-starter-homepage-bio"><?php esc_html_e( 'Описание', 'art-starter' ); ?></label>
							</th>
							<td>
								<textarea
									class="art-starter-field"
									rows="3"
									id="art-starter-homepage-bio"
									name="<?php echo esc_attr( $option_name ); ?>[profile][bio]"
								><?php echo esc_textarea( (string) ( $profile['bio'] ?? '' ) ); ?></textarea>
							</td>
						</tr>
					</table>
				</div>

				<div class="art-starter-panel">
					<?php Art_Starter_Admin_Homepage::render_panel_heading( __( 'Главная кнопка', 'art-starter' ), 'cta', $settings ); ?>
					<table class="form-table art-starter-form-table" role="presentation">
						<tr>
							<th scope="row">
								<label for="art-starter-homepage-cta-label"><?php esc_html_e( 'Текст', 'art-starter' ); ?></label>
							</th>
							<td>
								<input
									type="text"
									class="art-starter-field"
									id="art-starter-homepage-cta-label"
									name="<?php echo esc_attr( $option_name ); ?>[cta][label]"
									value="<?php echo esc_attr( (string) ( $cta['label'] ?? '' ) ); ?>"
									placeholder="<?php echo esc_attr__( 'Например: Записаться на консультацию', 'art-starter' ); ?>"
								>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="art-starter-homepage-cta-url"><?php esc_html_e( 'Ссылка', 'art-starter' ); ?></label>
							</th>
							<td>
								<input
									type="text"
									class="art-starter-field"
									id="art-starter-homepage-cta-url"
									name="<?php echo esc_attr( $option_name ); ?>[cta][url]"
									value="<?php echo esc_attr( (string) ( $cta['url'] ?? '' ) ); ?>"
									placeholder="<?php echo esc_attr__( 'example.com или https://...', 'art-starter' ); ?>"
								>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Иконка', 'art-starter' ); ?></th>
							<td>
								<?php
								Art_Starter_Admin_Homepage::render_icon_field(
									$option_name . '[cta][icon]',
									(string) ( $cta['icon'] ?? '' ),
									array(
										'input_id'   => 'art-starter-homepage-cta-icon',
										'categories' => Art_Starter_Icons::get_picker_categories(),
									)
								);
								?>
							</td>
						</tr>
					</table>
				</div>

				<div class="art-starter-panel">
					<?php Art_Starter_Admin_Homepage::render_panel_heading( __( 'Ссылки (кнопки)', 'art-starter' ), 'links', $settings ); ?>
					<p class="description">
						<?php esc_html_e( 'Добавляйте ссылки по одной. Пустые строки не покажутся на странице.', 'art-starter' ); ?>
					</p>

					<div class="art-starter-links-list" id="art-starter-homepage-links">
						<?php
						if ( ! empty( $links ) ) {
							foreach ( $links as $index => $item ) {
								if ( ! is_array( $item ) ) {
									continue;
								}
								Art_Starter_Admin_Homepage::render_link_row( $item, (int) $index );
							}
						}
						?>
					</div>

					<p>
						<button type="button" class="button" id="art-starter-homepage-add-link">
							<?php esc_html_e( 'Добавить ссылку', 'art-starter' ); ?>
						</button>
					</p>
				</div>

				<div class="art-starter-panel">
					<?php Art_Starter_Admin_Homepage::render_panel_heading( __( 'Рекомендуем', 'art-starter' ), 'recommend', $settings ); ?>

					<table class="form-table art-starter-form-table" role="presentation">
						<tr>
							<th scope="row">
								<label for="art-starter-homepage-rec-badge"><?php esc_html_e( 'Бейдж блока', 'art-starter' ); ?></label>
							</th>
							<td>
								<input
									type="text"
									class="art-starter-field"
									id="art-starter-homepage-rec-badge"
									name="<?php echo esc_attr( $option_name ); ?>[recommend][badge]"
									value="<?php echo esc_attr( (string) ( $recommend['badge'] ?? __( 'Рекомендуем', 'art-starter' ) ) ); ?>"
								>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="art-starter-homepage-rec-image"><?php esc_html_e( 'Обложка', 'art-starter' ); ?></label>
							</th>
							<td>
								<?php
								Art_Starter_Admin_Homepage::render_image_field(
									'art-starter-homepage-rec-image',
									$option_name . '[recommend][image_url]',
									(string) ( $recommend['image_url'] ?? '' ),
									'art-starter-media-field__thumb--recommend',
									true,
									true
								);
								?>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="art-starter-homepage-rec-title"><?php esc_html_e( 'Заголовок', 'art-starter' ); ?></label>
							</th>
							<td>
								<input
									type="text"
									class="art-starter-field"
									id="art-starter-homepage-rec-title"
									name="<?php echo esc_attr( $option_name ); ?>[recommend][title]"
									value="<?php echo esc_attr( (string) ( $recommend['title'] ?? '' ) ); ?>"
								>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="art-starter-homepage-rec-desc"><?php esc_html_e( 'Описание', 'art-starter' ); ?></label>
							</th>
							<td>
								<textarea
									class="art-starter-field"
									rows="3"
									id="art-starter-homepage-rec-desc"
									name="<?php echo esc_attr( $option_name ); ?>[recommend][description]"
								><?php echo esc_textarea( (string) ( $recommend['description'] ?? '' ) ); ?></textarea>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="art-starter-homepage-rec-btn"><?php esc_html_e( 'Кнопка', 'art-starter' ); ?></label>
							</th>
							<td>
								<div class="art-starter-inline-grid">
									<input
										type="text"
										class="art-starter-field"
										id="art-starter-homepage-rec-btn"
										name="<?php echo esc_attr( $option_name ); ?>[recommend][button_label]"
										value="<?php echo esc_attr( (string) ( $recommend['button_label'] ?? '' ) ); ?>"
										placeholder="<?php echo esc_attr__( 'Текст', 'art-starter' ); ?>"
									>
									<input
										type="text"
										class="art-starter-field"
										name="<?php echo esc_attr( $option_name ); ?>[recommend][button_url]"
										value="<?php echo esc_attr( (string) ( $recommend['button_url'] ?? '' ) ); ?>"
										placeholder="<?php echo esc_attr__( 'example.com или https://...', 'art-starter' ); ?>"
									>
								</div>
							</td>
						</tr>
					</table>
				</div>

				<div class="art-starter-panel">
					<?php Art_Starter_Admin_Homepage::render_panel_heading( __( 'Соцсети', 'art-starter' ), 'socials', $settings ); ?>
					<p class="description">
						<?php
						printf(
							/* translators: %d: maximum number of social networks */
							esc_html__( 'Добавляйте до %d соцсетей. Иконка подставляется автоматически.', 'art-starter' ),
							(int) Art_Starter_Homepage::MAX_SOCIAL_ITEMS
						);
						?>
					</p>

					<?php
					$socials_block = isset( $settings['blocks']['socials'] ) && is_array( $settings['blocks']['socials'] )
						? $settings['blocks']['socials']
						: array();
					$show_social_labels = ! empty( $socials_block['show_labels'] );
					?>
					<label class="art-starter-social-labels-toggle">
						<input
							type="checkbox"
							name="<?php echo esc_attr( $option_name ); ?>[blocks][socials][show_labels]"
							value="1"
							<?php checked( $show_social_labels ); ?>
							data-art-starter-social-labels-toggle
						>
						<?php esc_html_e( 'Отображать подписи соцсетей на сайте', 'art-starter' ); ?>
					</label>

					<div class="art-starter-socials-list" id="art-starter-homepage-socials">
						<?php
						if ( ! empty( $socials ) ) {
							foreach ( $socials as $index => $item ) {
								if ( ! is_array( $item ) ) {
									continue;
								}
								Art_Starter_Admin_Homepage::render_social_item_row( $item, (int) $index );
							}
						}
						?>
					</div>

					<input
						type="hidden"
						id="art-starter-socials-payload"
						name="<?php echo esc_attr( $option_name ); ?>[socials_payload]"
						value=""
					>

					<p>
						<button type="button" class="button" id="art-starter-homepage-add-social">
							<?php esc_html_e( 'Добавить соцсеть', 'art-starter' ); ?>
						</button>
					</p>
				</div>

				<?php submit_button( __( 'Сохранить', 'art-starter' ) ); ?>
			</form>
		</div>

		<aside class="art-starter-homepage-preview" aria-label="<?php echo esc_attr__( 'Превью', 'art-starter' ); ?>">
			<div
				class="<?php echo esc_attr( Art_Starter_Homepage::get_template_preview_frame_class( $homepage_template ) ); ?>"
				id="art-starter-homepage-preview"
			>
				<div class="art-starter-homepage-preview__scale">
				<div
					class="art-starter-homepage-shell <?php echo esc_attr( Art_Starter_Homepage::get_template_body_class( $homepage_template ) ); ?>"
					id="art-starter-homepage-preview-shell"
				>
				<div class="art-starter-homepage-card bio-card">
					<div class="art-starter-homepage-profile">
						<div class="art-starter-homepage-avatar" data-bind="avatar">
							<span class="art-starter-homepage-avatar__placeholder">A</span>
						</div>
						<div class="art-starter-homepage-name" data-bind="name"><?php echo esc_html( (string) ( $profile['name'] ?? '' ) ); ?></div>
						<div class="art-starter-homepage-roles" data-bind="roles"><?php echo esc_html( (string) ( $profile['roles'] ?? '' ) ); ?></div>
						<div class="art-starter-homepage-bio" data-bind="bio"><?php echo esc_html( (string) ( $profile['bio'] ?? '' ) ); ?></div>
					</div>

					<a class="art-starter-homepage-cta" href="#" data-bind="cta">
						<span class="art-starter-homepage-cta__icon" data-bind="cta-icon" hidden></span>
						<span class="art-starter-homepage-cta__label"><?php echo esc_html( (string) ( $cta['label'] ?? '' ) ); ?></span>
					</a>

					<div class="art-starter-homepage-links" data-bind="links">
						<!-- JS will render links -->
					</div>

					<div class="art-starter-homepage-recommend" data-bind="recommend">
						<div class="art-starter-homepage-recommend__badge" data-bind="recommend-badge"><?php echo esc_html( (string) ( $recommend['badge'] ?? '' ) ); ?></div>
						<div class="art-starter-homepage-recommend__layout">
							<div class="art-starter-homepage-recommend__image" data-bind="recommend-image" hidden></div>
							<div class="art-starter-homepage-recommend__body">
								<div class="art-starter-homepage-recommend__title"><?php echo esc_html( (string) ( $recommend['title'] ?? '' ) ); ?></div>
								<div class="art-starter-homepage-recommend__desc"><?php echo esc_html( (string) ( $recommend['description'] ?? '' ) ); ?></div>
								<div class="art-starter-homepage-recommend__button"><?php echo esc_html( (string) ( $recommend['button_label'] ?? '' ) ); ?></div>
							</div>
						</div>
					</div>

					<div class="art-starter-homepage-social" data-bind="social">
						<!-- JS will render socials -->
					</div>
				</div>
				</div>
				</div>
			</div>

			<script type="application/json" id="art-starter-homepage-data"><?php
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
