<?php
/**
 * Dashboard page view.
 *
 * @package Art_Starter
 */

defined( 'ABSPATH' ) || exit;

?>
<div class="wrap art-starter-admin">
	<h1><?php esc_html_e( 'ART Starter', 'art-starter' ); ?></h1>

	<div class="art-starter-panel" style="margin-top:10px;">
		<h2><?php esc_html_e( 'Быстрый старт нового сайта', 'art-starter' ); ?></h2>
		<p>
			<?php esc_html_e( 'АРТ Стартер помогает быстро настроить новый сайт WordPress: выставить простую главную страницу-визитку со ссылками (как у сервиса TypeLink) и отдельную страницу 404.', 'art-starter' ); ?>
		</p>
		<p>
			<a class="button button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=' . Art_Starter_Admin_Initial_Setup::PAGE_SLUG ) ); ?>">
				<?php esc_html_e( 'Настройки', 'art-starter' ); ?>
			</a>
			<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=' . Art_Starter_Admin_Homepage::PAGE_SLUG ) ); ?>">
				<?php esc_html_e( 'Главная страница', 'art-starter' ); ?>
			</a>
			<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=' . Art_Starter_Admin_Not_Found::PAGE_SLUG ) ); ?>">
				<?php esc_html_e( 'Страница 404', 'art-starter' ); ?>
			</a>
		</p>
	</div>

	<div class="art-starter-panel">
		<h2><?php esc_html_e( 'Возможности', 'art-starter' ); ?></h2>
		<ul class="art-starter-list">
			<li><?php esc_html_e( 'Мастер базовой настройки сайта', 'art-starter' ); ?></li>
			<li><?php esc_html_e( 'Главная страница-визитка со ссылками и цветовыми шаблонами', 'art-starter' ); ?></li>
			<li><?php esc_html_e( 'Отдельная страница 404 с настраиваемым содержимым', 'art-starter' ); ?></li>
		</ul>
	</div>
</div>
