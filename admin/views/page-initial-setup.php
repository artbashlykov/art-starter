<?php
/**
 * Primary setup page view.
 *
 * @package Art_Starter
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Template variables scoped to this view.

$technical = $form_state['technical'];
$removable = $form_state['removable'];

?>
<div class="wrap art-starter-admin">
	<h1><?php esc_html_e( 'Первичные настройки', 'art-starter' ); ?></h1>

	<p class="description">
		<?php esc_html_e( 'Базовые настройки для нового сайта. Технические опции меняют только значения по умолчанию WordPress. Удаление затрагивает лишь распознанный демо-контент — перед применением проверьте список ниже.', 'art-starter' ); ?>
	</p>

	<?php Art_Starter_Admin_Initial_Setup::render_notices(); ?>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="art-starter-initial-setup-form">
		<?php wp_nonce_field( Art_Starter_Admin_Initial_Setup::ACTION ); ?>
		<input type="hidden" name="action" value="<?php echo esc_attr( Art_Starter_Admin_Initial_Setup::ACTION ); ?>">

		<div class="art-starter-panel" style="margin-top:10px;">
			<h2><?php esc_html_e( 'О сайте', 'art-starter' ); ?></h2>

			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">
						<label for="art-starter-site-title"><?php esc_html_e( 'Название сайта', 'art-starter' ); ?></label>
					</th>
					<td>
						<input
							type="text"
							class="regular-text"
							id="art-starter-site-title"
							name="site_title"
							value="<?php echo esc_attr( $form_state['site_title'] ); ?>"
						>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="art-starter-site-tagline"><?php esc_html_e( 'Краткое описание', 'art-starter' ); ?></label>
					</th>
					<td>
						<input
							type="text"
							class="regular-text"
							id="art-starter-site-tagline"
							name="site_tagline"
							value="<?php echo esc_attr( $form_state['site_tagline'] ); ?>"
						>
						<p class="description">
							<?php esc_html_e( 'Короткая фраза о проекте. Можно оставить пустой для страницы-визитки.', 'art-starter' ); ?>
						</p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="art-starter-site-icon"><?php esc_html_e( 'Фавикон', 'art-starter' ); ?></label>
					</th>
					<td>
						<?php
						Art_Starter_Admin_Homepage::render_site_icon_field(
							'art-starter-site-icon',
							'site_icon',
							(int) $form_state['site_icon']
						);
						?>
						<p class="description">
							<?php esc_html_e( 'Иконка сайта во вкладке браузера. Рекомендуется квадратное изображение не меньше 512×512 px.', 'art-starter' ); ?>
						</p>
					</td>
				</tr>
			</table>
		</div>

		<div class="art-starter-panel">
			<h2><?php esc_html_e( 'Технические настройки', 'art-starter' ); ?></h2>
			<p class="description">
				<?php esc_html_e( 'Отметьте пункты, которые нужно применить. Уже настроенные опции будут пропущены.', 'art-starter' ); ?>
			</p>

			<div class="art-starter-checkbox-list">
				<label class="art-starter-checkbox-item">
					<input
						type="checkbox"
						name="apply_permalink_postname"
						value="1"
						<?php checked( ! $technical['permalink_postname']['applied'] ); ?>
						<?php disabled( $technical['permalink_postname']['applied'] ); ?>
					>
					<span class="art-starter-checkbox-item__label">
						<strong><?php esc_html_e( 'Постоянные ссылки: «Название записи»', 'art-starter' ); ?></strong>
						<span class="description">
							<?php
							echo esc_html(
								$technical['permalink_postname']['applied']
									? __( 'Уже применено.', 'art-starter' )
									: sprintf(
										/* translators: %s: permalink structure */
										__( 'Будет установлена структура %s', 'art-starter' ),
										$technical['permalink_postname']['label']
									)
							);
							?>
						</span>
					</span>
				</label>

				<label class="art-starter-checkbox-item">
					<input
						type="checkbox"
						name="apply_disable_comments"
						value="1"
						<?php checked( ! $technical['disable_comments']['applied'] ); ?>
						<?php disabled( $technical['disable_comments']['applied'] ); ?>
					>
					<span class="art-starter-checkbox-item__label">
						<strong><?php esc_html_e( 'Отключить комментарии', 'art-starter' ); ?></strong>
						<span class="description">
							<?php
							echo esc_html(
								$technical['disable_comments']['applied']
									? __( 'Уже применено для новых записей.', 'art-starter' )
									: __( 'Закроет комментарии только для новых записей и страниц. Существующий контент не меняется.', 'art-starter' )
							);
							?>
						</span>
					</span>
				</label>

				<label class="art-starter-checkbox-item">
					<input
						type="checkbox"
						name="apply_disable_pingbacks"
						value="1"
						<?php checked( ! $technical['disable_pingbacks']['applied'] ); ?>
						<?php disabled( $technical['disable_pingbacks']['applied'] ); ?>
					>
					<span class="art-starter-checkbox-item__label">
						<strong><?php esc_html_e( 'Отключить пингбэки и трекбэки', 'art-starter' ); ?></strong>
						<span class="description">
							<?php
							echo esc_html(
								$technical['disable_pingbacks']['applied']
									? __( 'Уже применено для новых записей.', 'art-starter' )
									: __( 'Отключит пингбэки и трекбэки для новых публикаций.', 'art-starter' )
							);
							?>
						</span>
					</span>
				</label>

				<label class="art-starter-checkbox-item">
					<input
						type="checkbox"
						name="apply_disable_registration"
						value="1"
						<?php checked( ! $technical['disable_registration']['applied'] ); ?>
						<?php disabled( $technical['disable_registration']['applied'] ); ?>
					>
					<span class="art-starter-checkbox-item__label">
						<strong><?php esc_html_e( 'Запретить регистрацию новых пользователей', 'art-starter' ); ?></strong>
						<span class="description">
							<?php
							echo esc_html(
								$technical['disable_registration']['applied']
									? __( 'Уже применено.', 'art-starter' )
									: __( 'Снимет галочку «Любой может зарегистрироваться» в настройках WordPress.', 'art-starter' )
							);
							?>
						</span>
					</span>
				</label>
			</div>
		</div>

		<div class="art-starter-panel">
			<h2><?php esc_html_e( 'Удаление мусора', 'art-starter' ); ?></h2>
			<p class="description">
				<?php esc_html_e( 'Плагин удаляет только стандартный демо-контент WordPress, который не был изменён и не используется сайтом. Если элемент не найден, удаление будет пропущено.', 'art-starter' ); ?>
			</p>

			<div class="art-starter-checkbox-list">
				<div class="art-starter-checkbox-item art-starter-checkbox-item--block">
					<label>
						<input
							type="checkbox"
							name="delete_hello_post"
							value="1"
							<?php disabled( empty( $removable['hello_post'] ) ); ?>
						>
						<span class="art-starter-checkbox-item__label">
							<strong><?php esc_html_e( 'Удалить запись «Привет, мир!»', 'art-starter' ); ?></strong>
						</span>
					</label>
					<?php if ( ! empty( $removable['hello_post'] ) ) : ?>
						<?php
						Art_Starter_Admin_Initial_Setup::render_removable_post_preview(
							$removable['hello_post'],
							__( 'Будет удалена запись:', 'art-starter' )
						);
						?>
					<?php else : ?>
						<p class="art-starter-removable-empty">
							<?php esc_html_e( 'Стандартная запись не найдена или уже изменена — удалять нечего.', 'art-starter' ); ?>
						</p>
					<?php endif; ?>
				</div>

				<div class="art-starter-checkbox-item art-starter-checkbox-item--block">
					<label>
						<input
							type="checkbox"
							name="delete_sample_page"
							value="1"
							<?php disabled( empty( $removable['sample_page'] ) ); ?>
						>
						<span class="art-starter-checkbox-item__label">
							<strong><?php esc_html_e( 'Удалить страницу «Пример страницы»', 'art-starter' ); ?></strong>
						</span>
					</label>
					<?php if ( ! empty( $removable['sample_page'] ) ) : ?>
						<?php
						Art_Starter_Admin_Initial_Setup::render_removable_post_preview(
							$removable['sample_page'],
							__( 'Будет удалена страница:', 'art-starter' )
						);
						?>
					<?php else : ?>
						<p class="art-starter-removable-empty">
							<?php esc_html_e( 'Стандартная страница не найдена, используется сайтом или уже изменена — удалять нечего.', 'art-starter' ); ?>
						</p>
					<?php endif; ?>
				</div>

				<div class="art-starter-checkbox-item art-starter-checkbox-item--block">
					<label>
						<input
							type="checkbox"
							name="delete_extra_themes"
							value="1"
							<?php disabled( empty( $removable['themes'] ) ); ?>
						>
						<span class="art-starter-checkbox-item__label">
							<strong><?php esc_html_e( 'Удалить лишние темы', 'art-starter' ); ?></strong>
						</span>
					</label>
					<?php if ( ! empty( $removable['themes'] ) ) : ?>
						<?php
						Art_Starter_Admin_Initial_Setup::render_removable_themes_preview(
							$removable['themes'],
							__( 'Будут удалены неактивные стандартные темы WordPress:', 'art-starter' )
						);
						?>
					<?php else : ?>
						<p class="art-starter-removable-empty">
							<?php esc_html_e( 'Неактивные стандартные темы WordPress не найдены — удалять нечего.', 'art-starter' ); ?>
						</p>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<?php submit_button( __( 'Применить настройки', 'art-starter' ) ); ?>
	</form>
</div>
