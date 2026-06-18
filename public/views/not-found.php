<?php
/**
 * Custom 404 page (front-end).
 *
 * @package Art_Starter
 *
 * @var array<string, mixed> $settings Not-found settings from Art_Starter_Not_Found::get_all().
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Template variables scoped to this view.

$image_url = isset( $settings['image_url'] ) ? (string) $settings['image_url'] : '';
$code      = isset( $settings['code'] ) ? (string) $settings['code'] : '404';
$title     = isset( $settings['title'] ) ? (string) $settings['title'] : '';
$buttons   = isset( $settings['buttons'] ) && is_array( $settings['buttons'] ) ? $settings['buttons'] : array();
$template  = isset( $settings['template'] ) ? (string) $settings['template'] : Art_Starter_Not_Found::get_default_template();

if ( ! Art_Starter_Not_Found::is_valid_template( $template ) ) {
	$template = Art_Starter_Not_Found::get_default_template();
}

if ( '' === $code ) {
	$code = '404';
}

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( Art_Starter_Not_Found::get_template_body_class( $template ) ); ?>>
<?php wp_body_open(); ?>

<main class="art-starter-not-found-shell">
	<div class="art-starter-not-found-card">
		<?php if ( $image_url ) : ?>
			<div class="art-starter-not-found-image">
				<img src="<?php echo esc_url( $image_url ); ?>" alt="" decoding="async" loading="lazy">
			</div>
		<?php endif; ?>

		<?php if ( $code ) : ?>
			<div class="art-starter-not-found-code"><?php echo esc_html( $code ); ?></div>
		<?php endif; ?>

		<?php if ( $title ) : ?>
			<div class="art-starter-not-found-title"><?php echo esc_html( $title ); ?></div>
		<?php endif; ?>

		<?php if ( ! empty( $buttons ) ) : ?>
			<div class="art-starter-not-found-buttons">
				<?php foreach ( $buttons as $index => $button ) : ?>
					<?php
					if ( ! is_array( $button ) ) {
						continue;
					}

					$is_primary  = 0 === (int) $index;
					$button_label = isset( $button['label'] ) ? (string) $button['label'] : '';
					$button_url   = Art_Starter_Not_Found::resolve_button_url(
						isset( $button['url'] ) ? (string) $button['url'] : '',
						$is_primary
					);
					$button_icon  = isset( $button['icon'] ) ? (string) $button['icon'] : '';

					if ( ! $is_primary && '' === $button_label && '' === $button_url ) {
						continue;
					}

					if ( '' === $button_label ) {
						continue;
					}

					if ( '' === $button_url ) {
						continue;
					}
					?>
					<a
						class="art-starter-not-found-button<?php echo $is_primary ? ' art-starter-not-found-button--primary' : ' art-starter-not-found-button--secondary'; ?>"
						href="<?php echo esc_url( $button_url ); ?>"
					>
						<span class="art-starter-not-found-button__icon">
							<?php
							$icon_markup = Art_Starter_Icons::render_or_letter(
								$button_icon,
								$button_label,
								'art-starter-not-found-button__icon-svg'
							);
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- icon markup from internal registry helper.
							echo $icon_markup;
							?>
						</span>
						<span class="art-starter-not-found-button__label"><?php echo esc_html( $button_label ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</main>

<?php wp_footer(); ?>
</body>
</html>
