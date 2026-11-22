<?php
/**
 * Elementor Layout 2 — horizontal list card
 *
 * Also used by the attribute form of the shortcode via
 * TLPportShortCode::get_team_old_layout(), which is what the Elementor widget emits.
 *
 * @package RT_Portfolio
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}
//phpcs:disable
$icon_zoom  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>';
$icon_link  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>';
$icon_arrow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>';

// The badge carries a single category, so take the first of the comma separated list.
$badge = '';
if ( ! empty( $categories ) ) {
	$badge_parts = explode( ',', wp_strip_all_tags( $categories ) );
	$badge       = trim( $badge_parts[0] );
}


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
$optional_field = TLPPortfolio()->pfpMetaList( $metaRows );
?>
<div class="tlp-col-lg-<?php echo esc_attr( $grid ); ?> tlp-col-md-<?php echo esc_attr( $grid ); ?> tlp-col-sm-<?php echo esc_attr( $tgrid ); ?> tlp-col-xs-12 tlp-single-item tlp-grid-item tlp-equal-height">
	<div class="tlp-portfolio-item">

		<?php if ( $img ) : ?>
			<div class="tlp-portfolio-thum tlp-item <?php echo esc_attr( $image_area ); ?>">
				<figure><?php echo wp_kses_post( $img ); ?></figure>

				<div class="tlp-overlay">
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
				</div>
			</div>
		<?php endif; ?>

		<?php if ( $badge || ! empty( $title ) || ! empty( $short_d ) || $optional_field ) : ?>
			<div class="tlp-content tlp-content2 <?php echo esc_attr( $content_area ); ?>">
				<div class="tlp-content-holder">
					<?php if ( $badge ) : ?>
						<span class="tlp-badge"><?php echo esc_html( $badge ); ?></span>
					<?php endif; ?>

					<?php if ( ! empty( $title ) ) : ?>
						<?php if ( ! empty( $enable_page_link ) ) : ?>
							<h3><a target="<?php echo esc_attr( $link_target ); ?>"<?php echo TLPPortfolio()->pfpLinkRel( $link_target ); ?> href="<?php echo esc_url( $plink ); ?>"><?php echo esc_html( $title ); ?></a></h3>
						<?php else : ?>
							<h3><?php echo esc_html( $title ); ?></h3>
						<?php endif; ?>
					<?php endif; ?>

					<?php if ( ! empty( $short_d ) ) : ?>
						<div class="tlp-portfolio-sd"><?php echo wp_kses_post( $short_d ); ?></div>
					<?php endif; ?>

					<?php echo $optional_field; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- inline SVG, escaped when built. ?>

					<?php if ( ! empty( $enable_page_link ) ) : ?>
						<a class="tlp-more" target="<?php echo esc_attr( $link_target ); ?>"<?php echo TLPPortfolio()->pfpLinkRel( $link_target ); ?> href="<?php echo esc_url( $plink ); ?>"><?php echo esc_html__( 'See details', 'tlp-portfolio' ); ?> <?php echo $icon_arrow; ?></a>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>
