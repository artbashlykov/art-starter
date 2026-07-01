<?php
/**
 * Homepage template (front-end).
 *
 * @package Art_Starter
 *
 * @var array<string, mixed> $settings Homepage settings from Art_Starter_Homepage::get_all().
 * @var string              $template Active color template slug.
 */

defined( 'ABSPATH' ) || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Template variables scoped to this view.

$template = isset( $template ) ? sanitize_key( (string) $template ) : Art_Starter_Homepage::get_default_template();
if ( ! Art_Starter_Homepage::is_valid_template( $template ) ) {
	$template = Art_Starter_Homepage::get_default_template();
}

$profile   = isset( $settings['profile'] ) && is_array( $settings['profile'] ) ? $settings['profile'] : array();
$cta       = isset( $settings['cta'] ) && is_array( $settings['cta'] ) ? $settings['cta'] : array();
$links     = isset( $settings['links'] ) && is_array( $settings['links'] ) ? $settings['links'] : array();
$recommend = isset( $settings['recommend'] ) && is_array( $settings['recommend'] ) ? $settings['recommend'] : array();
$socials   = isset( $settings['socials'] ) && is_array( $settings['socials'] ) ? $settings['socials'] : array();

$avatar_url = isset( $profile['avatar_url'] ) ? (string) $profile['avatar_url'] : '';
$name       = isset( $profile['name'] ) ? (string) $profile['name'] : '';
$roles      = isset( $profile['roles'] ) ? (string) $profile['roles'] : '';
$bio        = isset( $profile['bio'] ) ? (string) $profile['bio'] : '';
$cta_label  = isset( $cta['label'] ) ? (string) $cta['label'] : '';
$cta_url    = isset( $cta['url'] ) ? Art_Starter_Homepage::normalize_external_url( (string) $cta['url'] ) : '';
$cta_icon   = isset( $cta['icon'] ) ? (string) $cta['icon'] : '';

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( Art_Starter_Homepage::get_template_body_class( $template ) ); ?>>
<?php wp_body_open(); ?>

<main class="art-starter-homepage-shell">
	<div class="art-starter-homepage-card bio-card">
		<?php if ( Art_Starter_Homepage::is_block_visible( $settings, 'profile' ) ) : ?>
		<div class="art-starter-homepage-profile">
			<div class="art-starter-homepage-avatar">
				<?php if ( $avatar_url ) : ?>
					<img src="<?php echo esc_url( $avatar_url ); ?>" alt="" decoding="async" loading="lazy">
				<?php else : ?>
					<span class="art-starter-homepage-avatar__placeholder"><?php echo esc_html( substr( $name !== '' ? $name : 'A', 0, 1 ) ); ?></span>
				<?php endif; ?>
			</div>

			<?php if ( $name ) : ?>
				<div class="art-starter-homepage-name"><?php echo esc_html( $name ); ?></div>
			<?php endif; ?>

			<?php if ( $roles ) : ?>
				<div class="art-starter-homepage-roles"><?php echo esc_html( $roles ); ?></div>
			<?php endif; ?>

			<?php if ( $bio ) : ?>
				<div class="art-starter-homepage-bio"><?php echo esc_html( $bio ); ?></div>
			<?php endif; ?>
		</div>
		<?php endif; ?>

		<?php if ( Art_Starter_Homepage::is_block_visible( $settings, 'cta' ) && $cta_label && $cta_url ) : ?>
			<a class="art-starter-homepage-cta<?php echo $cta_icon ? ' art-starter-homepage-cta--with-icon' : ''; ?>" href="<?php echo esc_url( $cta_url ); ?>" target="_blank" rel="noopener noreferrer">
				<?php if ( $cta_icon ) : ?>
					<span class="art-starter-homepage-cta__icon">
						<?php
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- icon markup from internal registry helper.
						echo Art_Starter_Icons::render_or_letter( $cta_icon, $cta_label, 'art-starter-homepage-cta__icon-svg' );
						?>
					</span>
				<?php endif; ?>
				<span class="art-starter-homepage-cta__label"><?php echo esc_html( $cta_label ); ?></span>
			</a>
		<?php endif; ?>

		<?php if ( Art_Starter_Homepage::is_block_visible( $settings, 'links' ) && ! empty( $links ) ) : ?>
			<div class="art-starter-homepage-links">
				<?php foreach ( $links as $link ) : ?>
					<?php
					if ( ! is_array( $link ) ) {
						continue;
					}

					$link_label = isset( $link['label'] ) ? (string) $link['label'] : '';
					$link_url   = isset( $link['url'] ) ? Art_Starter_Homepage::normalize_external_url( (string) $link['url'] ) : '';
					$link_icon  = isset( $link['icon'] ) ? (string) $link['icon'] : '';
					$link_new_tab = ! array_key_exists( 'new_tab', $link ) || ! empty( $link['new_tab'] );

					if ( '' === $link_label && '' === $link_url ) {
						continue;
					}
					?>
					<a class="art-starter-homepage-link" href="<?php echo esc_url( $link_url ); ?>"<?php echo $link_new_tab ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
						<span class="art-starter-homepage-link__left">
							<span class="art-starter-homepage-link__icon">
								<?php
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- icon markup from internal registry helper.
								echo Art_Starter_Icons::render_link_icon( $link_icon, 'art-starter-homepage-link__icon-svg' );
								?>
							</span>
							<span class="art-starter-homepage-link__text"><?php echo esc_html( $link_label ? $link_label : $link_url ); ?></span>
						</span>
						<?php
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- bundled SVG markup.
						echo Art_Starter_Homepage::render_link_arrow();
						?>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php
		$rec_title = isset( $recommend['title'] ) ? (string) $recommend['title'] : '';
		$rec_desc  = isset( $recommend['description'] ) ? (string) $recommend['description'] : '';
		$rec_image = isset( $recommend['image_url'] ) ? (string) $recommend['image_url'] : '';
		$rec_btn   = isset( $recommend['button_label'] ) ? (string) $recommend['button_label'] : '';
		$rec_url   = isset( $recommend['button_url'] ) ? Art_Starter_Homepage::normalize_external_url( (string) $recommend['button_url'] ) : '';
		$rec_badge = isset( $recommend['badge'] ) ? (string) $recommend['badge'] : '';

		if ( Art_Starter_Homepage::is_block_visible( $settings, 'recommend' ) && ( $rec_title || $rec_desc || $rec_image ) ) :
			?>
			<div class="art-starter-homepage-recommend">
				<?php if ( $rec_badge ) : ?>
					<div class="art-starter-homepage-recommend__badge"><?php echo esc_html( $rec_badge ); ?></div>
				<?php endif; ?>

				<div class="art-starter-homepage-recommend__layout">
					<?php if ( $rec_image ) : ?>
						<div class="art-starter-homepage-recommend__image">
							<img src="<?php echo esc_url( $rec_image ); ?>" alt="" decoding="async" loading="lazy">
						</div>
					<?php endif; ?>

					<div class="art-starter-homepage-recommend__body">
						<?php if ( $rec_title ) : ?>
							<div class="art-starter-homepage-recommend__title"><?php echo esc_html( $rec_title ); ?></div>
						<?php endif; ?>

						<?php if ( $rec_desc ) : ?>
							<div class="art-starter-homepage-recommend__desc"><?php echo esc_html( $rec_desc ); ?></div>
						<?php endif; ?>

						<?php if ( $rec_btn && $rec_url ) : ?>
							<a class="art-starter-homepage-recommend__button" href="<?php echo esc_url( $rec_url ); ?>" target="_blank" rel="noopener noreferrer">
								<?php echo esc_html( $rec_btn ); ?>
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( Art_Starter_Homepage::is_block_visible( $settings, 'socials' ) && ! empty( $socials ) ) : ?>
			<?php $show_social_labels = Art_Starter_Homepage::should_show_social_labels( $settings ); ?>
			<div class="art-starter-homepage-social">
				<?php foreach ( $socials as $item ) : ?>
					<?php
					if ( ! is_array( $item ) ) {
						continue;
					}

					$network = isset( $item['network'] ) ? sanitize_key( (string) $item['network'] ) : '';
					$href    = Art_Starter_Homepage::get_social_href( $item );

					if ( '' === $network || '' === $href ) {
						continue;
					}

					$icon_meta    = Art_Starter_Icons::get( $network );
					$fallback     = $icon_meta ? (string) $icon_meta['label'] : $network;
					$network_label = Art_Starter_Homepage::get_social_network_label( $network );
					$item_class   = 'art-starter-homepage-social__item';

					if ( $show_social_labels && '' !== $network_label ) {
						$item_class .= ' art-starter-homepage-social__item--labeled';
					}
					?>
					<a class="<?php echo esc_attr( $item_class ); ?>" href="<?php echo esc_url( $href ); ?>" target="_blank" rel="noopener noreferrer">
						<?php if ( $show_social_labels && '' !== $network_label ) : ?>
							<span class="art-starter-homepage-social__icon-wrap">
								<?php
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- icon markup from internal registry helper.
								echo Art_Starter_Icons::render_or_letter( $network, $fallback, 'art-starter-homepage-social__icon-svg' );
								?>
							</span>
							<span class="art-starter-homepage-social__label"><?php echo esc_html( $network_label ); ?></span>
						<?php else : ?>
							<?php
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- icon markup from internal registry helper.
							echo Art_Starter_Icons::render_or_letter( $network, $fallback, 'art-starter-homepage-social__icon-svg' );
							?>
						<?php endif; ?>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</main>

<?php wp_footer(); ?>
</body>
</html>
