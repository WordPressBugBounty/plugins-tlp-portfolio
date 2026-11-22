<?php
/**
 * Elementor Carousel Layout 3 — hover grid card, in slider mode
 *
 * Mirrors lib/templates/layouts/carousel3.php, which is grid layout 3's card in
 * slider mode: a white card whose cover reveals a gradient overlay on hover carrying
 * the title, the short description and the action icons, with the title and category
 * line in the caption below. The slider owns the slide width.
 *
 * @package RT_Portfolio
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}
//phpcs:disable
$icon_zoom = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>';
$icon_link = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>';

$metaRows = [];
if ( ! empty( $client_name ) ) {
	$metaRows[] = [ 'class' => 'client-name', 'icon' => 'client', 'label' => esc_html__( 'Client Name' . ':', 'tlp-portfolio' ), 'value' => esc_html( $client_name ) ];
}
if ( ! empty( $completed_date ) ) {
	$metaRows[] = [ 'class' => 'completed-date', 'icon' => 'date', 'label' => esc_html__( 'Completed Date' . ':', 'tlp-portfolio' ), 'value' => esc_html( $completed_date ) ];
}
if ( ! empty( $categories ) ) {
	$metaRows[] = [ 'class' => 'categories', 'icon' => 'category', 'label' => esc_html__( 'Categories :', 'tlp-portfolio' ), 'value' => esc_html( trim( wp_strip_all_tags( $categories ) ) ) ];
}
if ( ! empty( $tools ) ) {
	$metaRows[] = [ 'class' => 'tools', 'icon' => 'tool', 'label' => esc_html__( 'Tools' . ':', 'tlp-portfolio' ), 'value' => esc_html( wp_strip_all_tags( $tools ) ) ];
}
if ( ! empty( $project_url ) ) {
	$metaRows[] = [ 'class' => 'project-url', 'icon' => 'globe', 'label' => esc_html__( 'Project URL' . ':', 'tlp-portfolio' ), 'url' => $project_url_link ];
}
$optional_field   = TLPPortfolio()->pfpMetaList( $metaRows );
$description_html = ! empty( $short_d ) ? sprintf( '<div class="tlp-portfolio-sd">%s</div>', $short_d ) : '';

if ( ! empty( $enable_page_link ) ) {
	$display_title = sprintf(
		'<h3><a target="%s"%s href="%s">%s</a></h3>',
		esc_attr( $link_target ),
		TLPPortfolio()->pfpLinkRel( $link_target ),
		esc_url( $plink ),
		esc_html( $title )
	);
} else {
	$display_title = sprintf( '<h3>%s</h3>', esc_html( $title ) );
}

// The overlay repeats the title over the cover, as the design does.
$overlay_title = ! empty( $title ) ? sprintf( '<h3>%s</h3>', esc_html( $title ) ) : '';

// The caption carries the categories as a single line under the title.
$category_line = ! empty( $categories ) ? '<span class="tlp-cat">' . esc_html( trim( wp_strip_all_tags( $categories ) ) ) . '</span>' : '';

// The description lives in the overlay, which only exists when there is a cover.
// With the image off it falls back into the caption so it is never lost.
$caption_desc = $img ? '' : $description_html;
?>
<div class="tlp-col-xs-12 tlp-single-item tlp-grid-item tlp-equal-height">
	<div class="tlp-portfolio-item">

		<?php if ( $img ) : ?>
			<div class="tlp-portfolio-thum tlp-item">

				<?php echo wp_kses_post( $img ); ?>

				<div class="tlp-overlay">
					<?php echo wp_kses_post( (string) $overlay_title ); ?>
					<?php echo wp_kses_post( (string) $description_html ); ?>

					<?php if ( $image_zoom || ! empty( $enable_page_link ) ) : ?>
						<p class="link-icon">
							<?php if ( $image_zoom ) : ?>
								<a class="tlp-zoom" href="<?php echo esc_url( $imgFull ); ?>"
								   aria-label="<?php echo esc_attr( $title ); ?>"><?php echo $icon_zoom; ?></a>
							<?php endif; ?>

							<?php if ( ! empty( $enable_page_link ) ) : ?>
								<a target="<?php echo esc_attr( $link_target ); ?>"<?php echo TLPPortfolio()->pfpLinkRel( $link_target ); ?> href="<?php echo esc_url( $plink ); ?>"
								   aria-label="<?php echo esc_attr( $title ); ?>"><?php echo $icon_link; ?></a>
							<?php endif; ?>
						</p>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $title ) || $category_line || $caption_desc || $optional_field ) : ?>
			<div class="tlp-content">
				<div class="tlp-content-holder">
					<?php echo wp_kses_post( (string) $display_title ); ?>
					<?php echo wp_kses_post( (string) $category_line ); ?>
					<?php echo wp_kses_post( (string) $caption_desc ); ?>
					<?php echo $optional_field; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline SVG, escaped when built. ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>
