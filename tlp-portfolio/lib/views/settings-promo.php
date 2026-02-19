<?php
if ( ! defined( 'ABSPATH' ) ) exit;
//phpcs:disable
?>
<div class="tlp-promo-container">
    <div class="tlp-promo-inner">
        <div class="promo-image">
            <img src="<?php echo esc_url( TLPPortfolio()->assetsUrl . 'images/portfolio-banner.png' ); ?>" alt="Portfolio Plugin">
        </div>
        <div class="promo-features">
            <h2 class="promo-title">
                Most Powerful Portfolio Plugin for WordPress
            </h2>
            <ul>
                <li>Multiple Layout (Grid, Slider,Isotope Filter)</li>
                <li>Portfolio Galley</li>
                <li>Elementor Widgets</li>
                <li>Custom Link for Detail Page</li>
                <li>Portfolio Single Page Builder With Elementor</li>
                <li>Even & Masonry Grid Style</li>
                <li>Carousel has different control</li>
                <li>4 Types of Pagination</li>
            </ul>
            <?php
            $current = time();
            if(mktime( 0, 0, 0, 11, 15, 2025 ) <= $current && $current <= mktime( 0, 0, 0, 1, 5, 2026 )) {
                ?>
                <div class="offer">
                    <a href="https://www.radiustheme.com/downloads/tlp-portfolio-pro-for-wordpress/" target="_blank">
                        <img style="width:100%" src="<?php echo esc_url( TLPPortfolio()->assetsUrl . 'images/p-offer.png'); ?>" alt="The portfolio">
                    </a>
                </div>
            <?php } ?>
            <a class="rt-admin-btn" href="https://www.radiustheme.com/downloads/tlp-portfolio-pro-for-wordpress/" target="_blank">
                Get The Deal!
            </a>
        </div>
    </div>
</div>