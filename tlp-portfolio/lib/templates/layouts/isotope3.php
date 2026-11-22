<?php
/**
 * Isotope Layout 3 — hover grid card
 *
 * The same card as grid layout 3, in isotope mode: a white card with a 4:3 cover
 * whose hover reveals a gradient overlay carrying the title, the short description
 * and the action icons, with the title and category line in the caption below.
 *
 * @package RT_Portfolio
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}
//phpcs:disable
$exFeature = $project_title = $overlay_title = $short_description = $category_line = '';

$link_target = $link_target ? " target='" . esc_attr( $link_target ) . "'" . TLPPortfolio()->pfpLinkRel( $link_target ) : '';

$icon_zoom = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>';
$icon_link = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>';

if ( in_array( 'name', $items ) && $title ) {
	$project_title = $link
		? sprintf( '<h3><a href="%1$s" aria-label="%3$s" %2$s >%3$s</a></h3>', esc_url( $item_link ), $link_target, esc_html( $title ) )
		: sprintf( '<h3>%s</h3>', esc_html( $title ) );

	// The overlay repeats the title over the cover, as the design does.
	$overlay_title = sprintf( '<h3>%s</h3>', esc_html( $title ) );
}

if ( in_array( 'short_description', $items ) && $short_d ) {
	$short_description = '<div class="tlp-portfolio-sd">' . $short_d . '</div>';
}

// The caption carries the categories as a single line under the title.
if ( in_array( 'categories', $items ) && $categories ) {
	$category_line = '<span class="tlp-cat">' . esc_html( $categories ) . '</span>';
}

$metaRows = [];
if ( in_array( 'client_name', $items ) && $client_name ) {
	$metaRows[] = [ 'class' => 'client-name', 'icon' => 'client', 'label' => esc_html__( 'Client Name :', 'tlp-portfolio' ), 'value' => esc_html( $client_name ) ];
}
if ( in_array( 'completed_date', $items ) && $completed_date ) {
	$metaRows[] = [ 'class' => 'completed-date', 'icon' => 'date', 'label' => esc_html__( 'Completed Date :', 'tlp-portfolio' ), 'value' => esc_html( $completed_date ) ];
}
if ( in_array( 'categories', $items ) && $categories ) {
	$metaRows[] = [ 'class' => 'categories', 'icon' => 'category', 'label' => esc_html__( 'Categories :', 'tlp-portfolio' ), 'value' => esc_html( $categories ) ];
}
if ( in_array( 'tools', $items ) && $tools ) {
	$metaRows[] = [ 'class' => 'tools', 'icon' => 'tool', 'label' => esc_html__( 'Tools :', 'tlp-portfolio' ), 'value' => esc_html( $tools ) ];
}
if ( in_array( 'project_url', $items ) && $project_url ) {
	$metaRows[] = [ 'class' => 'project-url', 'icon' => 'globe', 'label' => esc_html__( 'Project URL :', 'tlp-portfolio' ), 'url' => $project_url ];
}
$exFeature = TLPPortfolio()->pfpMetaList( $metaRows );

$has_zoom = ( in_array( 'zoom_image', $items ) && $imgFull );
$has_link = ( $item_link && $link );

// The description lives in the overlay, which only exists when there is a cover.
// With the image off it falls back into the caption so it is never lost.
$has_cover    = ! empty( $img );
$caption_desc = $has_cover ? '' : $short_description;

// Isotope needs the per-item category classes on the column wrapper.
$grid = $grid . $isoFilter;
?>

<div class="<?php echo esc_attr( $grid ); ?>">
	<div class="tlp-portfolio-item">

		<?php if ( ! empty( $img ) ) : ?>
			<div class="tlp-portfolio-thum tlp-item">

				<?php echo wp_kses_post( $img ); ?>

				<div class="tlp-overlay">
					<?php echo wp_kses_post( $overlay_title ); ?>
					<?php echo wp_kses_post( $short_description ); ?>

					<?php if ( $has_zoom || $has_link ) : ?>
						<p class="link-icon">
							<?php if ( $has_zoom ) : ?>
								<a class="tlp-zoom" href="<?php echo esc_url( $imgFull ); ?>"
								   aria-label="<?php echo esc_attr( $title ); ?>"><?php echo $icon_zoom; ?></a>
							<?php endif; ?>

							<?php if ( $has_link ) : ?>
								<a href="<?php echo esc_url( $item_link ); ?>" <?php echo $link_target; ?>
								   aria-label="<?php echo esc_attr( $title ); ?>"><?php echo $icon_link; ?></a>
							<?php endif; ?>
						</p>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( $project_title || $category_line || $caption_desc || $exFeature ) : ?>
			<div class="tlp-content">
				<div class="tlp-content-holder">
					<?php echo wp_kses_post( $project_title ); ?>
					<?php echo wp_kses_post( $category_line ); ?>
					<?php echo wp_kses_post( $caption_desc ); ?>
					<?php echo $exFeature; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline SVG, escaped when built. ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>
