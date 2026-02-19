<?php
/**
 * Get Help Page.
 *
 * @package RT_Portfolio
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

global $TLPportfolio;
?>
	<style>
		.rtport-help-wrapper {
			width: 70%;
			margin: 0 auto;
            overflow:hidden;
		}
		.rtport-help-section .embed-wrapper {
			position: relative;
			display: block;
			width: 100%;
			padding: 0;
			overflow: hidden;
		}
		.rtport-help-section .embed-wrapper::before {
			display: block;
			content: "";
			padding-top: 56.25%;
		}
		.rtport-help-section iframe {
			position: absolute;
			top: 0;
			bottom: 0;
			left: 0;
			width: 100%;
			height: 100%;
			border: 0;
		}
		.rtport-help-wrapper .rtport-help-section {
			margin-top: 30px;
		}
		.rtport-feature-list ul {
			column-count: 2;
			column-gap: 30px;
			margin-bottom: 0;
		}
		.rtport-feature-list ul li {
			padding: 0 0 12px;
			margin-bottom: 0;
			width: 100%;
			font-size: 14px;
		}
		.rtport-feature-list ul li:last-child {
			padding-bottom: 0;
		}
		.rtport-feature-list ul li i {
			color: #4C6FFF;
		}
		.rtport-pro-feature-content {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
		}
        @media(max-width:991px){
            .rtport-pro-feature-content{
                grid-template-columns: repeat(2, 1fr);
                column-gap: 20px;
                row-gap:0;
            }
        }
        @media(max-width:767px){
            .rtport-pro-feature-content{
                display: block;
            }
        }
		.rtport-pro-feature-content .rt-document-box {
			display:flex;
            flex-direction:column;
		}
		.rtport-testimonials {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
		}
        @media(max-width:991px){
            .rtport-testimonials{
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }
        }
		.rtport-testimonial .client-info {
			display: flex;
			flex-wrap: wrap;
			font-size: 14px;
			align-items: center;
            margin-top: auto;
		}
		.rtport-testimonial .client-info img {
			width: 60px;
			height: 60px;
			object-fit: cover;
			border-radius: 50%;
			margin-right: 10px;
		}
		.rtport-testimonial .client-info .rtport-star {
			color: #4C6FFF;
		}
		.rtport-testimonial .client-info .client-name {
			display: block;
			color: #000;
			font-size: 16px;
			font-weight: 600;
			margin: 8px 0 5px;
		}
		.rtport-call-to-action {
			background-size: cover;
			background-repeat: no-repeat;
			background-position: center;
			height: 150px;
			color: #ffffff;
			margin: 30px 0;
		}
		.rtport-call-to-action a {
			color: inherit;
			display: flex;
			flex-wrap: wrap;
			width: 100%;
			height: 100%;
			flex: 1;
			align-items: center;
			font-size: 28px;
			font-weight: 700;
			text-decoration: none;
			margin-left: 130px;
			position: relative;
			outline: none;
			-webkit-box-shadow: none;
			box-shadow: none;
		}
		.rtport-call-to-action a::before {
			content: "";
			position: absolute;
			left: -30px;
			top: 50%;
			height: 30%;
			width: 5px;
			background: #fff;
			-webkit-transform: translateY(-50%);
			transform: translateY(-50%);
		}
		.rtport-call-to-action:hover a {
			text-decoration: underline;
		}
		.rtport-testimonial p {
			text-align: justify;
		}
		@media all and (max-width: 1400px) {
			.rtport-help-wrapper {
				width: 80%;
			}
		}
		/*@media all and (max-width: 1025px) {*/
		/*	.rtport-pro-feature-content .rt-document-box {*/
		/*		flex: 0 0 calc(50% - 55px)*/
		/*	}*/
		/*	.rtport-pro-feature-content .rt-document-box + .rt-document-box + .rt-document-box {*/
		/*		margin-left: 0;*/
		/*	}*/
		/*}*/
		@media all and (max-width: 991px) {
			.rtport-help-wrapper {
				width: calc(100% - 40px);
			}
			.rtport-call-to-action a {
				justify-content: center;
				margin-left: auto;
				margin-right: auto;
				text-align: center;
			}
			.rtport-call-to-action a::before {
				content: none;
			}
		}
		@media all and (max-width: 600px) {
			.rt-document-box .rt-box-content .rt-box-title {
				line-height: 28px;
			}
			.rtport-help-section .embed-wrapper {
				width: 100%;
			}
			.rtport-feature-list ul {
				column-count: 1;
			}
			.rtport-feature-list ul li {
				width: 100%;
			}
			.rtport-call-to-action a {
				padding-left: 25px;
				padding-right: 25px;
				font-size: 20px;
				line-height: 28px;
				width: 80%;
			}
			.rtport-testimonials {
				display: block;
			}
			.rtport-testimonials .rtport-testimonial + .rtport-testimonial {
				margin-left: 0;
				margin-top: 30px;
				padding-top: 30px;
				border-top: 1px solid #ddd;
			}
			/*.rtport-pro-feature-content .rt-document-box {*/
			/*	width: 100%;*/
			/*	flex: auto;*/
			/*}*/
			/*.rtport-pro-feature-content .rt-document-box + .rt-document-box {*/
			/*	margin-left: 0;*/
			/*}*/

			.rtport-help-wrapper .rt-document-box {
				display: block;
				position: relative;
			}

			/*.rtport-help-wrapper .rt-document-box .rt-box-icon {*/
			/*	position: absolute;*/
			/*	left: 20px;*/
			/*	top: 30px;*/
			/*	margin-top: 0;*/
			/*}*/

			/*.rt-document-box .rt-box-content .rt-box-title {*/
			/*	margin-left: 45px;*/
			/*}*/
		}
	</style>
	<div class="rtport-help-wrapper" >
		<div class="rtport-help-section rt-document-box">
			<div class="rt-heading">
                <div class="rt-box-icon"><i class="dashicons dashicons-media-document"></i></div>
                <h3 class="rt-box-title">Thank you for installing Radius Portfolio Plugin</h3>
            </div>
            <div class="rt-box-content youtube-video">
                <div class="embed-wrapper">
                    <iframe  src="https://www.youtube.com/embed/uB4jPd4umaM?si=TLY_4ZQCs1ZTeae0" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                </div>
                <div class="embed-wrapper">
                    <iframe  src="https://www.youtube.com/embed/uB4jPd4umaM?si=TLY_4ZQCs1ZTeae0" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                </div>
            </div>

		</div>
		<div class="rt-document-box">
            <div class="rt-heading">
                <div class="rt-box-icon"><i class="dashicons dashicons-megaphone"></i></div>
                <h3 class="rt-box-title">Pro Features</h3>
            </div>
			<div class="rt-box-content rtport-feature-list">
				<ul>
					<li><i class="dashicons dashicons-saved"></i> 57 Amazing Layouts with Grid, Masonry, Slider, Isotope.</li>
					<li><i class="dashicons dashicons-saved"></i> Gallery Image View.</li>
					<li><i class="dashicons dashicons-saved"></i> Even and Masonry Grid.</li>
					<li><i class="dashicons dashicons-saved"></i> Layout Preview in Shortcode Settings.</li>
					<li><i class="dashicons dashicons-saved"></i> Elementor Widgets.</li>
					<li><i class="dashicons dashicons-saved"></i> Portfolio Single Page Builder With Elementor.</li>
					<li><i class="dashicons dashicons-saved"></i> Taxonomy support (Category, Tag).</li>
					<li><i class="dashicons dashicons-saved"></i> All Text and Color control.</li>
					<li><i class="dashicons dashicons-saved"></i> Custom image size control.</li>
					<li><i class="dashicons dashicons-saved"></i> AJAX Pagination (Numbered, Load more and Load on Scrolling).</li>
					<li><i class="dashicons dashicons-saved"></i> Popup Support for detail page.</li>
					<li><i class="dashicons dashicons-saved"></i> Search field on Isotope.</li>
					<li><i class="dashicons dashicons-saved"></i> Order by Name, Id, Date, Random and Menu order.</li>
					<li><i class="dashicons dashicons-saved"></i> Related Portfolio</li>
					<li><i class="dashicons dashicons-saved"></i> Responsive Display Control.</li>
					<li><i class="dashicons dashicons-saved"></i> More Features...</li>
				</ul>
			</div>
		</div>
		<div class="rtport-call-to-action" style="background-image: url('<?php echo esc_url( $TLPportfolio->assetsUrl ); ?>images/admin/banner.png')">
			<a href="https://www.radiustheme.com/downloads/tlp-portfolio-pro-for-wordpress/" target="_blank" class="rt-update-pro-btn">
				Update to Pro & Get More Features
			</a>
		</div>
		<div class="rt-document-box">
            <div class="rt-heading">
                <div class="rt-box-icon"><i class="dashicons dashicons-thumbs-up"></i></div>
                <h3 class="rt-box-title">Happy clients of the Radius Portfolio Plugin</h3>
            </div>
			<div class="rt-box-content">
				<div class="rtport-testimonials">
					<div class="rtport-testimonial">
						<p>Great support and very clean and nice portfolio. Simple to publish photographs.</p>
						<div class="client-info">
							<img src="<?php echo esc_url( $TLPportfolio->assetsUrl ); // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>images/admin/client1.png">
							<div>
								<div class="rtport-star">
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
								</div>
								<span class="client-name">lennartphotography</span>
							</div>
						</div>
					</div>
					<div class="rtport-testimonial">
						<p>I’ve used a few different portfolio apps and I like the options, simplicity and layout of this one. It works great! Thanks you!</p>
						<div class="client-info">
							<img src="<?php echo esc_url( $TLPportfolio->assetsUrl ); // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>images/admin/client2.png">
							<div>
								<div class="rtport-star">
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
									<i class="dashicons dashicons-star-filled"></i>
								</div>
								<span class="client-name">goldgrl7</span>
							</div>
						</div>
					</div>
                    <div class="rtport-testimonial">
                        <p>I discovered a small problem and within hours a new version was online. Sure, I provided the solution, but other plugin authors can take weeks to fix even the smallest things, even if they have the solution.</p>
                        <div class="client-info">
                            <img src="<?php echo esc_url( $TLPportfolio->assetsUrl ); // phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage ?>images/admin/client1.png">
                            <div>
                                <div class="rtport-star">
                                    <i class="dashicons dashicons-star-filled"></i>
                                    <i class="dashicons dashicons-star-filled"></i>
                                    <i class="dashicons dashicons-star-filled"></i>
                                    <i class="dashicons dashicons-star-filled"></i>
                                    <i class="dashicons dashicons-star-filled"></i>
                                </div>
                                <span class="client-name">mathzf</span>
                            </div>
                        </div>
                    </div>
				</div>
			</div>
		</div>
		<div class="rtport-pro-feature-content">
			<div class="rt-document-box">
                <div class="rt-heading">
                    <div class="rt-box-icon"><i class="dashicons dashicons-media-document"></i></div>
                    <h3 class="rt-box-title">Documentation</h3>
                </div>
				<div class="rt-box-content">
					<p>Get started by spending some time with the documentation we included step by step process with screenshots with video.</p>
				</div>
                <div class="doc-button">
                    <a href="https://www.radiustheme.com/docs/portfolio/" target="_blank" class="rt-admin-btn">Documentation</a>
                </div>
			</div>
			<div class="rt-document-box">
                <div class="rt-heading">
                    <div class="rt-box-icon"><i class="dashicons dashicons-sos"></i></div>
                    <h3 class="rt-box-title">Need Help?</h3>
                </div>
				<div class="rt-box-content">
					<p>Stuck with something? Please create a
						<a href="https://www.radiustheme.com/contact/">ticket here</a> or post on <a href="https://www.facebook.com/groups/234799147426640/">facebook group</a>. For emergency case join our <a href="https://www.radiustheme.com/">live chat</a>.</p>
				</div>
                <div class="doc-button">
                    <a href="https://www.radiustheme.com/contact/" target="_blank" class="rt-admin-btn">Get Support</a>
                </div>
			</div>
			<div class="rt-document-box">
                <div class="rt-heading">
                    <div class="rt-box-icon"><i class="dashicons dashicons-smiley"></i></div>
                    <h3 class="rt-box-title">Happy Our Work?</h3>
                </div>
				<div class="rt-box-content">
					<p>If you happy with <strong>Radius Portfolio Plugin</strong> plugin, please add a rating. It would be glad to us.</p>
				</div>
                <div class="doc-button">
                    <a href="https://wordpress.org/support/plugin/tlp-portfolio/reviews/" class="rt-admin-btn" target="_blank">Post Review</a>
                </div>
			</div>
		</div>
	</div>
<?php
