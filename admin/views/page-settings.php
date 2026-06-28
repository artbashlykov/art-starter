<?php
/**
 * Settings hub page with tabs.
 *
 * @package Art_Starter
 *
 * @var string $active_tab Active tab slug.
 */

defined( 'ABSPATH' ) || exit;

?>
<div class="wrap art-starter-admin">
	<h1><?php esc_html_e( 'ART Starter', 'art-starter' ); ?></h1>

	<?php Art_Starter_Admin_Settings::render_tabs( $active_tab ); ?>

	<div class="art-starter-admin-tab-panel">
		<?php
		if ( Art_Starter_Admin_Settings::TAB_HOMEPAGE === $active_tab ) {
			Art_Starter_Admin_Homepage::render_page();
		} elseif ( Art_Starter_Admin_Settings::TAB_NOT_FOUND === $active_tab ) {
			Art_Starter_Admin_Not_Found::render_page();
		} else {
			Art_Starter_Admin_Initial_Setup::render_page();
		}
		?>
	</div>
</div>
