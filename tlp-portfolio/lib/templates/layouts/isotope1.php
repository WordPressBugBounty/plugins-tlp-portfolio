<?php
/**
 * Isotope Layout 1 — card grid
 *
 * Same card as grid layout 1; only the isotope filter class on the column differs.
 *
 * @package RT_Portfolio
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}
//phpcs:disable
$project_title = $short_description = '';
$link_target = $link_target ? " target='" . esc_attr( $link_target ) . "'" . TLPPortfolio()->pfpLinkRel( $link_target ) : '';

$icon_zoom  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>';
$icon_link  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>';
$icon_arrow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>';

if ( in_array( 'short_description', $items ) && $short_d ) {
	$short_description = '<div class="tlp-portfolio-sd">' . $short_d . '</div>';
}

if ( in_array( 'name', $items ) && $title ) {
	$project_title = $link
		? sprintf( '<h3><a href="%1$s" aria-label="%3$s" %2$s >%3$s</a></h3>', esc_url( $item_link ), $link_target, esc_html( $title ) )
		: sprintf( '<h3>%s</h3>', esc_html( $title ) );
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

// The badge carries a single category, so take the first of the comma separated list.
$badge = '';
if ( ! empty( $categories ) ) {
	$badge_parts = explode( ',', wp_strip_all_tags( $categories ) );
	$badge       = trim( $badge_parts[0] );
}

// Isotope needs each column tagged with its category slugs so the filter can match it.
$grid = $grid . $isoFilter;
?>

<div class="<?php echo esc_attr( $grid ); ?>">
	<div class="tlp-portfolio-item">

		<?php if ( ! empty( $img ) ) : ?>
			<div class="tlp-portfolio-thum tlp-item">

				<?php if ( $badge ) : ?>
					<span class="tlp-badge"><?php echo esc_html( $badge ); ?></span>
				<?php endif; ?>

				<?php echo wp_kses_post( $img ); ?>

				<div class="tlp-overlay">
					<p class="link-icon">
						<?php if ( in_array( 'zoom_image', $items ) && $imgFull ) : ?>
							<a class="tlp-zoom" href="<?php echo esc_url( $imgFull ); ?>"
							   aria-label="<?php echo esc_attr( $title ); ?>"><?php echo $icon_zoom; ?></a>
						<?php endif; ?>

						<?php if ( $item_link && $link ) : ?>
							<a href="<?php echo esc_url( $item_link ); ?>" <?php echo $link_target; ?>
							   aria-label="<?php echo esc_attr( $title ); ?>"><?php echo $icon_link; ?></a>
						<?php endif; ?>
					</p>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( $project_title || $short_description || $exFeature ) : ?>
			<div class="tlp-content">
				<div class="tlp-content-holder">
					<?php echo wp_kses_post( $project_title ); ?>
					<?php echo wp_kses_post( $short_description ); ?>
					<?php echo $exFeature; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline SVG, escaped when built. ?>
					<?php if ( $item_link && $link ) : ?>
						<a class="tlp-more" href="<?php echo esc_url( $item_link ); ?>" <?php echo $link_target; ?>><?php echo esc_html__( 'View project', 'tlp-portfolio' ); ?> <?php echo $icon_arrow; ?></a>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>
