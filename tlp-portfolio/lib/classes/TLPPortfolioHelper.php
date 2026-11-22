<?php
/**
 * Helper class.
 *
 * @package RT_Portfolio
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

if ( ! class_exists( 'TLPPortfolioHelper' ) ) :
	/**
	 * Helper class.
	 */
	class TLPPortfolioHelper {
		public function verifyNonce() {
			$nonce     = isset( $_REQUEST['tlp_nonce'] ) && ! empty( $_REQUEST['tlp_nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tlp_nonce'] ) ) : null;
			$nonceText = $this->nonceText();

			if ( wp_verify_nonce( $nonce, $nonceText ) ) {
				return true;
			}

			return false;
		}

		public function getNonce(  ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return isset( $_REQUEST[  $this->nonceId() ] ) ? sanitize_text_field( wp_unslash( $_REQUEST[ $this->nonceId() ] ) ) : null;

		}

		public function nonceId() {
			return 'tlp_nonce';
		}

		public function nonceText() {
			return 'tlp_portfolio_nonce';
		}


		public function meta_exist( $post_id, $meta_key, $type = 'post' ) {
			if ( ! $post_id ) {
				return false;
			}

			return metadata_exists( $type, $post_id, $meta_key );
		}

		public function get_short_description( $content, $limit = 0, $tags = '', $invert = false ) {
			$filter_content = wp_kses_post( html_entity_decode( $content ) );

			if ( $limit ) {
				$filter_content = wp_filter_nohtml_kses( $filter_content );

				if ( $limit > 0 && strlen( $filter_content ) > $limit ) {
					$filter_content = mb_substr( $filter_content, 0, $limit, 'utf-8' );
					$filter_content = preg_replace( '/\W\w+\s*(\W*)$/', '$1', $filter_content );
				}
			}

			return apply_filters( 'tlp_portfolio_get_short_description', $filter_content, $content, $limit );
		}

		/**
		 * @return string
		 * Remove select2Js confection
		 */
		public function getSelect2JsId() {
			$select2Id = 'tlp-select2';

//			if ( class_exists( 'WPSEO_Admin_Asset_Manager' ) && class_exists( 'Avada' ) ) {
//				$select2Id = 'yoast-seo-select2';
//			} elseif ( class_exists( 'WPSEO_Admin_Asset_Manager' ) ) {
//				$select2Id = 'yoast-seo-select2';
//			} elseif ( class_exists( 'Avada' ) ) {
//				$select2Id = 'select2-avada-js';
//			}

			return $select2Id;
		}

		public function pfpScMetaFields() {
			return array_merge(
				TLPPortfolio()->scLayoutMetaFields(),
				TLPPortfolio()->scFilterMetaFields(),
				TLPPortfolio()->scItemMetaFields(),
				TLPPortfolio()->scStyleFields()
			);
		}

		public function rtFieldGenerator( $fields = [] ) {
			$html = null;

			if ( is_array( $fields ) && ! empty( $fields ) ) {
				$PFProField = new TlpPortfolioField();

				foreach ( $fields as $fieldKey => $field ) {
					$html .= $PFProField->Field( $fieldKey, $field );
				}
			}

			return $html;
		}

		public function getAllPortFolioCategoryList() {
			$terms    = [];
			//$termList = get_terms( [ TLPPortfolio()->taxonomies['category'] ], [ 'hide_empty' => 0 ] );
			$termList = get_terms( array(
				'taxonomy'   => TLPPortfolio()->taxonomies['category'],
				'hide_empty' => false,
			) );
			if ( is_array( $termList ) && ! empty( $termList ) && empty( $termList['errors'] ) ) {
				foreach ( $termList as $term ) {
					$terms[ $term->term_id ] = $term->name;
				}
			}

			return $terms;
		}


		public function getAllPortFolioTagList() {
			$terms    = [];
			//$termList = get_terms( [ TLPPortfolio()->taxonomies['tag'] ], [ 'hide_empty' => 0 ] );
			$termList = get_terms( array(
				'taxonomy'   => TLPPortfolio()->taxonomies['tag'],
				'hide_empty' => false,
			) );
			if ( is_array( $termList ) && ! empty( $termList ) && empty( $termList['errors'] ) ) {
				foreach ( $termList as $term ) {
					$terms[ $term->term_id ] = $term->name;
				}
			}

			return $terms;
		}

		public function get_image_sizes() {
			global $_wp_additional_image_sizes;

			$sizes      = [];
			$interSizes = get_intermediate_image_sizes();

			if ( ! empty( $interSizes ) ) {
				foreach ( get_intermediate_image_sizes() as $_size ) {
					// medium_large (768px wide) is a core size too, read from options like the other three. Without it the
					// select could not show a saved medium_large: the Layout Preview fell back to medium and Update cleared it.
					if ( in_array( $_size, [ 'thumbnail', 'medium', 'medium_large', 'large' ] ) ) {
						$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
						$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
						$sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
					} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
						$sizes[ $_size ] = [
							'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
							'height' => $_wp_additional_image_sizes[ $_size ]['height'],
							'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
						];
					}
				}
			}

			$imgSize = [
				'' => esc_html__( 'Select One', 'tlp-portfolio' ),
			];

			if ( ! empty( $sizes ) ) {
				foreach ( $sizes as $key => $img ) {
					$imgSize[ $key ] = ucfirst( $key ) . " ({$img['width']}*{$img['height']})";
				}
			}

			$imgSize['pfp_custom'] = esc_html__( 'Custom image size', 'tlp-portfolio' );

			return $imgSize;
		}

		public function getFeatureImageSrc( $post_id = null, $fImgSize = 'team-thumb', $customImgSize = [] ) {
			global $post;

			$img_class = 'img-responsive';
			$post_id   = ( $post_id ? absint( $post_id ) : $post->ID );
			$alt       = esc_attr( get_the_title( $post_id ) );
			$imgSrc    = $image = null;
			$cSize     = false;
			// if ($fImgSize == 'rt_custom') {

			if ( $fImgSize == 'pfp_custom' || $fImgSize == 'rt_custom' ) {
				$fImgSize = 'full';
				$cSize    = true;
			}
			// pfp_custom

			if ( $aID = get_post_thumbnail_id( $post_id ) ) {
				$image  = wp_get_attachment_image( $aID, $fImgSize, '', [ 'class' => $img_class ] );
				$imageS = wp_get_attachment_image_src( $aID, $fImgSize );
				$imgSrc = isset( $imageS[0] ) ? $imageS[0] : '';
			}

			if ( $imgSrc && $cSize ) {
				global $TLPportfolio;

				$w = ( ! empty( $customImgSize['width'] ) ? absint( $customImgSize['width'] ) : null );
				$h = ( ! empty( $customImgSize['height'] ) ? absint( $customImgSize['height'] ) : null );
				$c = ( ! empty( $customImgSize['crop'] ) && $customImgSize['crop'] == 'soft' ? false : true );

				if ( $w && $h ) {
					$imgSrc = $TLPportfolio->rtImageReSize( $imgSrc, $w, $h, $c );
                    if ( $imgSrc ) {
                        $attachment_id = attachment_url_to_postid( $imgSrc );
                        if ( $attachment_id ) {
                            $image = wp_get_attachment_image( $attachment_id, array( $w, $h ), false, array( 'alt' => $alt, 'class' => $img_class ) );
                        }
                    }
				}
			}

			return $image;
		}

		public function rtImageReSize( $url, $width = null, $height = null, $crop = null, $single = true, $upscale = false ) {
			$rtResize = new PFProReSizer();

			return $rtResize->process( $url, $width, $height, $crop, $single, $upscale );
		}


		/**
		 * Sanitize field value
		 *
		 * @param array $field
		 * @param null  $value
		 *
		 * @return array|null
		 * @internal param $value
		 */
		public function sanitize( $field = [], $value = null ) {
			$newValue = null;

			if ( is_array( $field ) ) {
				$type = ( ! empty( $field['type'] ) ? $field['type'] : 'text' );

				if ( empty( $field['multiple'] ) ) {
					if ( $type == 'url' ) {
						$newValue = esc_url( $value );
					} elseif ( $type == 'slug' ) {
						$newValue = sanitize_title_with_dashes( $value );
					} elseif ( $type == 'textarea' ) {
						$newValue = wp_kses_post( $value );
					} elseif ( $type == 'custom_css' ) {
						$newValue = wp_strip_all_tags( stripslashes( $value ) );
					} elseif ( $type == 'image_size' ) {
						$newValue = [];

						foreach ( $value as $k => $v ) {
							$newValue[ $k ] = esc_attr( $v );
						}
					} elseif ( $type == 'style' ) {
						$newValue = [];

						foreach ( $value as $k => $v ) {
							if ( $k == 'color' ) {
								$newValue[ $k ] = $this->sanitize_hex_color( $v );
							} else {
								$newValue[ $k ] = $this->sanitize( [ 'type' => 'text' ], $v );
							}
						}
					} else {
						$newValue = sanitize_text_field( $value );
					}
				} else {
					$newValue = [];
					if ( ! empty( $value ) ) {
						if ( is_array( $value ) ) {
							foreach ( $value as $key => $val ) {
								if ( $type == 'style' && $key == 0 ) {
									if ( function_exists( 'sanitize_hex_color' ) ) {
										$newValue = sanitize_hex_color( $val );
									} else {
										$newValue[] = $this->sanitize_hex_color( $val );
									}
								} else {
									$newValue[] = sanitize_text_field( $val );
								}
							}
						} else {
							$newValue[] = sanitize_text_field( $value );
						}
					}
				}
			}

			return $newValue;
		}

		public function sanitize_hex_color( $color ) {
			if ( function_exists( 'sanitize_hex_color' ) ) {
				return sanitize_hex_color( $color );
			} else {
				if ( '' === $color ) {
					return '';
				}

				// 3 or 6 hex digits, or the empty string.
				if ( preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
					return $color;
				}
			}
		}

		/**
		 * Sanitize a colour coming from the colour-picker field.
		 *
		 * The picker is declared with `alpha => true`, so it saves `rgb()` / `rgba()`
		 * strings as well as hex. sanitize_hex_color() returns an empty string for those,
		 * which silently discarded the Primary, Overlay and button colours - the control
		 * looked like it did nothing at all.
		 *
		 * Kept in step with the pro plugin's PFProHelper::sanitizeCssColor().
		 *
		 * @param string $color Raw stored value.
		 *
		 * @return string Empty string when the value is not a colour.
		 */
		public function sanitizeCssColor( $color ) {
			$color = trim( (string) $color );

			if ( '' === $color ) {
				return '';
			}

			if ( preg_match( '/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/', $color ) ) {
				return $color;
			}

			if ( preg_match( '/^rgba?\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*(?:,\s*(?:0|1|0?\.\d+)\s*)?\)$/', $color ) ) {
				return $color;
			}

			return '';
		}

		public function TLPhex2rgba( $color, $opacity = false ) {
			$default = 'rgb(0,0,0)';

			// Return default if no color provided.
			if ( empty( $color ) ) {
				return $default;
			}

			// The picker can hand us rgb()/rgba() rather than hex; reuse the channels.
			if ( preg_match( '/^rgba?\((\s*\d{1,3}\s*),(\s*\d{1,3}\s*),(\s*\d{1,3}\s*)/', $color, $m ) ) {
				$r = absint( $m[1] );
				$g = absint( $m[2] );
				$b = absint( $m[3] );

				return false !== $opacity
					? "rgba({$r},{$g},{$b}," . floatval( $opacity ) . ')'
					: "rgb({$r},{$g},{$b})";
			}

			// Sanitize $color if "#" is provided.
			if ( $color[0] == '#' ) {
				$color = substr( $color, 1 );
			}

			// Check if color has 6 or 3 characters and get values.
			if ( strlen( $color ) == 6 ) {
				$hex = [ $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] ];
			} elseif ( strlen( $color ) == 3 ) {
				$hex = [ $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] ];
			} else {
				return $default;
			}

			// Convert hexadec to rgb.
			$rgb = array_map( 'hexdec', $hex );

			// Check if opacity is set(rgba or rgb).
			if ( $opacity ) {
				if ( abs( $opacity ) > 1 ) {
					$opacity = 1.0;
				}
				$output = 'rgba(' . implode( ',', $rgb ) . ',' . $opacity . ')';
			} else {
				$output = 'rgb(' . implode( ',', $rgb ) . ')';
			}

			// Return rgb(a) color string.
			return $output;
		}

		public function singlePortfolioMeta( $id = null ) {
			global $TLPportfolio;

			$id = ( ! $id ? get_the_ID() : $id );

			if ( ! $id ) {
				return;
			}

			$project_url    = get_post_meta( $id, 'project_url', true );
			$tools          = get_post_meta( $id, 'tools', true );
			$client_name    = get_post_meta( $id, 'client_name', true );
			$completed_date = get_post_meta( $id, 'completed_date', true );
			$categories     = wp_strip_all_tags( get_the_term_list( $id, $TLPportfolio->taxonomies['category'], '', ', ' ) );
			$tags           = wp_strip_all_tags( get_the_term_list( $id, $TLPportfolio->taxonomies['tag'], '', ', ' ) );

			$html  = null;

			if ( $project_url ) {
				$html .= '<li><label>' . esc_html__( 'Project URL', 'tlp-portfolio' ) . ':</label> <a  href="' . esc_url( $project_url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html( $project_url ) . '</a></li>';
			}

			if ( $categories ) {
				$html .= '<li class="categories"><label>' . esc_html__( 'Categories: ', 'tlp-portfolio' ) . '</label><span>' . esc_html( $categories ) . '</span></li>';
			}

			if ( $tools ) {
				$html .= '<li class="tools"><label>' . esc_html__( 'Tools', 'tlp-portfolio' ) . ': </label><span>' . esc_html( $tools ) . '</span></li>';
			}

			if ( $client_name ) {
				$html .= '<li class="client-name"><label>' . esc_html__( 'Client', 'tlp-portfolio' ) . ': </label> <span>' . esc_html( $client_name ) . '</span></li>';
			}

			if ( $completed_date ) {
				$html .= '<li class="completed-date"> <label>' . esc_html__( 'Completed Date', 'tlp-portfolio' ) . ': </label><span>' . esc_html( $completed_date ) . '</span></li>';
			}

			if ( $tags ) {
				$html .= '<li class="tags"><label>' . esc_html__( 'Tags :', 'tlp-portfolio' ) . '</label><span>' . esc_html( $tags ) . '</span></li>';
			}


			if ( $html ) {
				$html = "<ul class='single-item-meta'>{$html}</ul>";
			}

			return wp_kses_post( $html );
		}

		public function socialShare( $pLink ) {
            $html = '';
            $html  = "<div class='single-portfolio-share'>";
            $html .= "<div class='fb-share rt-share-item'>
                <div class='fb-share-button' data-href='" . esc_url( $pLink ) . "' data-layout='button_count'></div>
              </div>";
            $html .= "<div class='twitter-share rt-share-item'>
                <a href='https://twitter.com/share?url=" . esc_url( $pLink ) . "' class='twitter-share-button'>Tweet</a>
              </div>";
            $html .= "<div class='linkedin-share rt-share-item'>
                <script type='IN/Share' data-url='" . esc_url( $pLink ) . "'></script>
              </div>";
            $html .= "</div>";
            $html .=  "<div id='fb-root'></div>";
            $this->enqueueSocialScripts();
			return $html;
		}

        private function enqueueSocialScripts() {
            wp_enqueue_script(
                'facebook-sdk',
                'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5',
                [],
                '1.0.0',
                true
            );

            // Enqueue Twitter widgets
            wp_enqueue_script(
                'twitter-widgets',
                'https://platform.twitter.com/widgets.js',
                [],
                '1.0.0',
                true
            );

            // Enqueue LinkedIn platform
            wp_enqueue_script(
                'linkedin-platform',
                'https://platform.linkedin.com/in.js',
                [],
                '1.0.0',
                true
            );

            // Enqueue Pinterest
            wp_enqueue_script(
                'pinterest-script',
                'https://assets.pinterest.com/js/pinit.js',
                [],
                '1.0.0',
                true
            );

            // Enqueue Google+ platform
            wp_enqueue_script(
                'google-platform',
                'https://apis.google.com/js/platform.js',
                [],
                '1.0.0',
                true
            );
        }

		public function proFeatureList() {
			ob_start();
			?>
			<ol>
				<li>Full Responsive & Mobile Friendly</li>
				<li>57 Layouts (Even Grid, Masonry Grid, Even Isotope, Masonry Isotope & Carousel Slider)</li>
				<li>Unlimited Layouts Variation</li>
				<li>Unlimited Colors</li>
				<li>Unlimited ShortCode Generator</li>
				<li>Drag & Drop Ordering</li>
				<li>Drag & Drop Taxonomy ie Category Ordering</li>
				<li>Gutter / Padding Control</li>
				<li>Dynamic image Re-size & Custom image size</li>
				<li>Detail page with popup next preview button and normal view</li>
				<li>Device wise Item View Control</li>
				<li>Visual Composer Compatibility</li>
				<li> 4 Types of Pagination Normal number, AJAX number Pagination, AJAX Load More & Auto Load on Scroll</li>
				<li>Layout Preview Under ShortCode Generator</li>
				<li>Count for Isotope Button</li>
				<li>Search for Isotope Layouts</li>
				<li>All Fields Control show/hide</li>
				<li>RTL Added for Carousel Slider</li>
				<li>All text color, Size and Text align control</li>
				<li>Related Portfolio</li>
			</ol>
			<p><a href="<?php echo esc_url( TLPPortfolio()->pro_version_link() ); ?>" class="button button-primary" target="_blank">Get Pro Version</a></p>
			<?php
			return ob_get_clean();

		}

		/**
		 * @param     $query
		 * @param int    $args
		 * @param     $scMeta
		 *
		 * @return string|null
		 */
		public function pagination( $query, $args, $scMeta ) {
			$html      = null;
			$range     = isset( $args['posts_per_page'] ) ? $args['posts_per_page'] : 4;
			$range     = isset( $args['posts_per_page_meta'] ) ? $args['posts_per_page_meta'] : $range;
			$showitems = ( $range * 2 ) + 1;

			global $paged;

			if ( is_front_page() ) {
				$paged = ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1;
			} else {
				$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
			}

			if ( empty( $paged ) ) {
				$paged = 1;
			}

			$pages = $query->max_num_pages;

			if ( isset( $scMeta['pfp_limit'][0] ) && ! empty( $scMeta['pfp_limit'][0] ) ) {
				$found_post = $query->found_posts;

				if ( $range && $found_post > $scMeta['pfp_limit'][0] ) {
					$found_post = $scMeta['pfp_limit'][0];
					$pages      = ceil( $found_post / $range );
				}
			}

			if ( ! $pages ) {
				global $wp_query;
				$pages = $wp_query->max_num_pages;
				$pages = $pages ? $pages : 1;
			}

			if ( 1 != $pages ) {
				$li = null;

				// Chevrons match the shared pagination design; every layout renders through here.
				$chevron_left  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>';
				$chevron_right = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>';

				// Off by default so the markup matches the design; filter it back on if wanted.
				if ( apply_filters( 'tlp_portfolio_pagination_page_count', false ) ) {
					$li .= sprintf(
						'<li class="disabled hidden-xs"><span><span aria-hidden="true">%s</span></span></li>',
						/* translators: %d is the page number */
						sprintf( esc_html__( 'Page %1$d of %2$d', 'tlp-portfolio' ), $paged, $pages )
					);
				}

				// Previous — always rendered, disabled on the first page.
				if ( $paged > 1 ) {
					$li .= sprintf(
						'<li class="tlp-prev"><a href="%1$s" aria-label="%2$s">%3$s</a></li>',
						get_pagenum_link( $paged - 1 ),
						esc_attr__( 'Previous page', 'tlp-portfolio' ),
						$chevron_left
					);
				} else {
					$li .= sprintf(
						'<li class="tlp-prev disabled"><span aria-label="%1$s">%2$s</span></li>',
						esc_attr__( 'Previous page', 'tlp-portfolio' ),
						$chevron_left
					);
				}

				for ( $i = 1; $i <= $pages; $i++ ) {
					if ( 1 != $pages && ( ! ( $i >= $paged + $range + 1 || $i <= $paged - $range - 1 ) || $pages <= $showitems ) ) {
						$li .= $paged == $i ? sprintf( '<li class="active"><span aria-current="page">%d</span></li>', $i )
							: sprintf( '<li><a href="%s">%d</a></li>', get_pagenum_link( $i ), $i );

					}
				}

				// Next — always rendered, disabled on the last page.
				if ( $paged < $pages ) {
					$li .= sprintf(
						'<li class="tlp-next"><a href="%1$s" aria-label="%2$s">%3$s</a></li>',
						get_pagenum_link( $paged + 1 ),
						esc_attr__( 'Next page', 'tlp-portfolio' ),
						$chevron_right
					);
				} else {
					$li .= sprintf(
						'<li class="tlp-next disabled"><span aria-label="%1$s">%2$s</span></li>',
						esc_attr__( 'Next page', 'tlp-portfolio' ),
						$chevron_right
					);
				}

				$html = sprintf(
					'<div class="tlp-pagination-wrap" data-total-pages="%d" data-posts-per-page="%d">%s</div>',
					$query->max_num_pages,
					$args['posts_per_page'],
					$li ? sprintf( '<ul class="tlp-pagination">%s</ul>', $li ) : ''
				);
			}

			return apply_filters( 'tlp_portfolio_pagination_html', $html, $query, $args, $scMeta );
		}

		public function get_shortCode_list() {
			$scList = [];
			$scQ    = get_posts(
				[
					'post_type'      => TLPPortfolio()->getScPostType(),
					'order_by'       => 'title',
					'order'          => 'ASC',
					'post_status'    => 'publish',
					'posts_per_page' => -1,
				]
			);

			if ( ! empty( $scQ ) ) {
				$scList = wp_list_pluck( $scQ, 'post_title', 'ID' );
			}

			return $scList;
		}

		// Date format.
		public function date_format_php_to_js() {
			$sFormat = get_option( 'date_format' );

			switch ( $sFormat ) {
				// Predefined WP date formats
				case 'F j, Y':
					return ( 'MM dd, yyyy' );
					break;
				case 'Y/m/d':
					return ( 'yyyy/mm/dd' );
					break;
				case 'm/d/Y':
					return ( 'mm/dd/yyyy' );
					break;
				case 'd/m/Y':
					return ( 'dd/mm/yyyy' );
					break;
				default:
					return ( 'dd-mm-yyyy' );
					break;
			}
		}

		/**
		 * A custom sanitization function that will take the incoming input, and sanitize
		 * the input before handing it back to WordPress to save to the database and The output of frontend.
		 *
		 * @since    1.0.0
		 *
		 * @param    array $input  Array value string .
		 * @return   array.
		 */
		public function array_text_sanitization( $input ) {
			$input = maybe_unserialize( $input );
			return ! empty( $input ) ? array_map( 'sanitize_text_field', array_filter( $input ) ) : [];
		}

		/**
		 * Dynamic CSS for the card grid (layout 1).
		 *
		 * Layout 1 is a white card, so the shared rules that paint `.tlp-content` with the
		 * primary colour and force a 15px content padding would destroy it. Primary is
		 * re-targeted at the card's own accents instead — the hover overlay and the hover
		 * shadow — and the controls the card markup would otherwise ignore are wired up.
		 *
		 * Kept in step with the pro plugin's PFProHelper::cardLayoutCss().
		 *
		 * @param string $layoutID Container id.
		 * @param string $layout   Selected layout key.
		 * @param array  $args     primary_color, name_hover_color, icon_size, icon_align,
		 *                         overlay_color.
		 *
		 * @return string
		 */
		/**
		 * Default desktop column count for a layout.
		 *
		 * The horizontal card layouts are far too tight three-up, so they default to two.
		 * Only used when the shortcode has no desktop column saved, so an explicit choice
		 * is never overridden.
		 *
		 * Kept in step with the pro plugin's PFProHelper::defaultColumns().
		 *
		 * @param string $layout Selected layout key.
		 *
		 * @return int
		 */
		public function defaultColumns( $layout ) {
			switch ( $layout ) {
				case 'layout2':
				case 'isotope2':
				case 'carousel2':
					return 2;

				default:
					return 3;
			}
		}

		/**
		 * Icon for a meta row, as inline SVG.
		 *
		 * Emitted as raw markup by the caller, never through a kses'd string - wp_kses_post()
		 * strips <svg> entirely.
		 *
		 * Kept in step with the pro plugin's PFProHelper::pfpMetaIcon().
		 *
		 * @param string $name One of client, date, category, tool, tag, url.
		 *
		 * @return string
		 */
		public function pfpMetaIcon( $name ) {
			$open  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">';
			$paths = [
				'client'   => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
				'date'     => '<rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
				'category' => '<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>',
				'tool'     => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>',
				'tag'      => '<line x1="4" y1="9" x2="20" y2="9"/><line x1="4" y1="15" x2="20" y2="15"/><line x1="10" y1="3" x2="8" y2="21"/><line x1="16" y1="3" x2="14" y2="21"/>',
				'url'      => '<path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>',
				'globe'    => '<circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>',
			];

			$paths['link'] = $paths['url'];

			return ! empty( $paths[ $name ] ) ? $open . $paths[ $name ] . '</svg>' : '';
		}

		/**
		 * The rel attribute for a link that opens in a new tab, with a leading space.
		 *
		 * Empty for any other target, so it can be printed straight after the target attribute.
		 *
		 * Kept in step with the pro plugin's PFProHelper::pfpLinkRel().
		 *
		 * @param string $target Link target.
		 *
		 * @return string Fixed markup, safe to print as is.
		 */
		public function pfpLinkRel( $target ) {
			return '_blank' === $target ? ' rel="noopener noreferrer"' : '';
		}

		/**
		 * Build the card layouts' meta list.
		 *
		 * The markup keeps `.extra-features > ul > li`, the per-field `li` classes and the
		 * `<label>` element, because the Meta Style control and the Elementor meta selectors
		 * all target those. The icon tile and the value span are additions the design needs.
		 *
		 * The value is deliberately left with no colour of its own so the value colour set
		 * on the `li` still reaches it.
		 *
		 * Kept in step with the pro plugin's PFProHelper::pfpMetaList().
		 *
		 * @param array $rows Ordered rows: [ 'class', 'icon', 'label', 'value' ].
		 *                    `value` is already escaped or safe markup.
		 *
		 * @return string Empty string when there is nothing to show.
		 */
		public function pfpMetaList( $rows ) {
			$html = '';

			foreach ( (array) $rows as $row ) {
				// A row with nothing to show is dropped entirely - label included - so the
				// card never prints "Project URL :" or "Tools :" with a blank after it. A url
				// row has no text of its own (the value is the masked link button), so it is
				// judged on the url rather than on rendered text.
				$rowUrl  = ! empty( $row['url'] );
				$rowText = isset( $row['value'] ) ? trim( wp_strip_all_tags( (string) $row['value'] ) ) : '';
				if ( ! $rowUrl && '' === $rowText ) {
					continue;
				}

				// A url row is masked: the address itself is long enough to double the card's
				// height, so the value is a single link carrying the URL in its href and
				// title. Hovering shows it, clicking opens it.
				if ( ! empty( $row['url'] ) ) {
					$value = '<a class="tlp-meta-link" href="' . esc_url( $row['url'] ) . '"'
						. ' target="_blank" rel="noopener noreferrer"'
						. ' title="' . esc_attr( $row['url'] ) . '"'
						. ' aria-label="' . esc_attr( sprintf( __( 'Open %s in a new tab', 'tlp-portfolio' ), $row['url'] ) ) . '">'
						. $this->pfpMetaIcon( 'link' ) . '</a>';
				} else {
					$value = $row['value'];
				}

				$html .= '<li class="' . esc_attr( $row['class'] ) . '">'
					. '<span class="tlp-meta-ico">' . $this->pfpMetaIcon( $row['icon'] ) . '</span>'
					. '<label>' . esc_html( $row['label'] ) . '</label>'
					. '<span class="tlp-meta-value">' . $value . '</span>'
					. '</li>';
			}

			return $html ? '<div class="extra-features tlp-meta"><ul>' . $html . '</ul></div>' : '';
		}

		/**
		 * Dynamic CSS for the shortcode's buttons: the isotope filter pills, the numbered pagination
		 * and the carousel arrows and dots.
		 *
		 * Each kind has its own colour section in the Styling tab (TLPPortfolioOptions::scButtonStyleFields()).
		 * A colour left empty there falls back to the matching "All buttons" colour - the pfp_button_* keys
		 * every shortcode had before the split - so an older shortcode renders as it did. Primary still fills
		 * the selected pill and the current page when neither colour is set. The Primary rules for the
		 * carousel arrow hover and the active dot stay in the generators, before this call, so these
		 * colours win over them by order.
		 *
		 * Called from both generators - lib/templates/portfolio-sc-css.php and the Layout Preview's
		 * TLPportShortCode::customStyle(). Pro's PFProHelper::buttonGroupsCss() is the same logic on its
		 * own markup, plus the Load More button free does not have.
		 *
		 * @param string $layoutID     Container id, e.g. `tlp-portfolio-container-12`.
		 * @param array  $meta         Shortcode values: get_post_meta() shape (value at [0]) or the preview's flat $_REQUEST.
		 * @param string $primaryColor Resolved Primary colour, or null.
		 *
		 * @return string
		 */
		public function buttonGroupsCss( $layoutID, $meta, $primaryColor = null ) {
			$get = function ( $key, $fallback = '' ) use ( $meta ) {
				$value = isset( $meta[ $key ] ) ? $meta[ $key ] : '';
				if ( is_array( $value ) ) {
					$value = isset( $value[0] ) ? $value[0] : '';
				}
				$value = $this->sanitizeCssColor( $value );

				return '' !== $value ? $value : $fallback;
			};

			$id  = "#{$layoutID}";
			$css = '';

			// All buttons - the default for every section below.
			$allBg        = $get( 'pfp_button_bg_color' );
			$allTxt       = $get( 'pfp_button_text_color' );
			$allHoverBg   = $get( 'pfp_button_hover_bg_color' );
			$allActiveBg  = $get( 'pfp_button_active_bg_color' );
			$allActiveTxt = $get( 'pfp_button_active_text_color' );

			/* Isotope filter buttons */
			$isoBg        = $get( 'pfp_iso_btn_bg_color', $allBg );
			$isoTxt       = $get( 'pfp_iso_btn_text_color', $allTxt );
			$isoHoverBg   = $get( 'pfp_iso_btn_hover_bg_color', $allHoverBg );
			$isoHoverTxt  = $get( 'pfp_iso_btn_hover_text_color' );
			$isoActiveBg  = $get( 'pfp_iso_btn_active_bg_color', $allActiveBg );
			$isoActiveTxt = $get( 'pfp_iso_btn_active_text_color', $allActiveTxt );
			$pill         = "{$id} .tlp-portfolio-isotope-button button";

			// A background reaches every pill, the selected one too, until a selected colour is set.
			if ( $isoBg ) {
				$css .= "{$pill}{background:{$isoBg};}";
			}
			if ( $isoTxt ) {
				$css .= "{$pill}{color:{$isoTxt};}";
			}
			// Hover leaves the selected pill alone, like the stylesheet's own hover.
			if ( $isoHoverBg ) {
				$css .= "{$pill}:not(.selected):hover{background:{$isoHoverBg};}";
			}
			if ( $isoHoverTxt ) {
				$css .= "{$pill}:not(.selected):hover{color:{$isoHoverTxt};}";
			}
			if ( $isoActiveBg ) {
				$css .= "{$pill}.selected{background:{$isoActiveBg};}";
			}
			// Not the pill's count: it sits on its own badge.
			if ( $isoActiveTxt ) {
				$css .= "{$pill}.selected{color:{$isoActiveTxt};}";
			}
			// The selected pill lifts on a tint of whichever colour fills it.
			$pillShadow = $isoActiveBg ? $isoActiveBg : $isoBg;
			if ( $pillShadow ) {
				$css .= "{$pill}.selected{box-shadow:0 6px 16px " . $this->TLPhex2rgba( $pillShadow, 0.32 ) . ';}';
			} elseif ( $primaryColor ) {
				$css .= "{$pill}.selected{background:{$primaryColor};box-shadow:0 6px 16px " . $this->TLPhex2rgba( $primaryColor, 0.32 ) . ';}';
			}

			/* Pagination numbers */
			$pageBg        = $get( 'pfp_page_btn_bg_color', $allBg );
			$pageTxt       = $get( 'pfp_page_btn_text_color', $allTxt );
			$pageHoverBg   = $get( 'pfp_page_btn_hover_bg_color', $allHoverBg );
			$pageHoverTxt  = $get( 'pfp_page_btn_hover_text_color' );
			$pageActiveBg  = $get( 'pfp_page_btn_active_bg_color', $allActiveBg );
			$pageActiveTxt = $get( 'pfp_page_btn_active_text_color', $allActiveTxt );
			$pageList      = "{$id} .tlp-pagination";
			// The active page is kept out of every resting rule: it is the filled pill in the design.
			$pageRest      = "{$pageList} li:not(.active) span, {$pageList} li:not(.active) a";
			$pageRestHover = "{$pageList} li:not(.active) span:hover, {$pageList} li:not(.active) a:hover";
			$pageCurrent   = "{$pageList} li.active span, {$pageList} li.active a";

			// A filled page would otherwise keep the design's pale outline as a ring round the fill.
			if ( $pageBg ) {
				$css .= "{$pageRest}{background:{$pageBg};border-color:{$pageBg};}";
			}
			if ( $pageTxt ) {
				$css .= "{$pageRest}{color:{$pageTxt};}";
			}
			if ( $pageHoverBg ) {
				$css .= "{$pageRestHover}{background:{$pageHoverBg};border-color:{$pageHoverBg};}";
			}
			if ( $pageHoverTxt ) {
				$css .= "{$pageRestHover}{color:{$pageHoverTxt};}";
			}
			// The current page takes its own colour, else Primary; the outline and shadow follow the fill.
			$pageFill = $pageActiveBg ? $pageActiveBg : $primaryColor;
			if ( $pageFill ) {
				$css .= "{$pageCurrent}{background:{$pageFill};border-color:{$pageFill};box-shadow:0 6px 16px " . $this->TLPhex2rgba( $pageFill, 0.26 ) . ';}';
			}
			if ( $pageActiveTxt ) {
				$css .= "{$pageCurrent}{color:{$pageActiveTxt};}";
			}
			// A resting page turns the accent colour on hover, while no pagination colour owns the hover.
			if ( $primaryColor && ! $pageBg && ! $pageTxt && ! $pageHoverBg && ! $pageHoverTxt ) {
				$css .= "{$pageList} li:not(.active) a:hover{color:{$primaryColor};}";
			}

			/* Carousel arrows and dots */
			$arrowBg       = $get( 'pfp_nav_btn_bg_color', $allBg );
			$arrowTxt      = $get( 'pfp_nav_btn_text_color', $allTxt );
			$arrowHoverBg  = $get( 'pfp_nav_btn_hover_bg_color', $allHoverBg );
			$arrowHoverTxt = $get( 'pfp_nav_btn_hover_text_color' );
			$dotBg         = $get( 'pfp_nav_dot_color', $allBg );
			$dotActiveBg   = $get( 'pfp_nav_dot_active_color', $allActiveBg );
			$arrow         = "{$id} .owl-theme .owl-nav [class*=owl-]";

			if ( $arrowBg ) {
				$css .= "{$arrow}{background:{$arrowBg};}";
			}
			if ( $dotBg ) {
				$css .= "{$id} .owl-theme .owl-dots .owl-dot span{background:{$dotBg};}";
			}
			if ( $arrowTxt ) {
				$css .= "{$arrow}{color:{$arrowTxt};}";
			}
			// The arrow lifts on a tinted shadow of its own colour, so the shadow follows the fill.
			if ( $arrowHoverBg ) {
				$css .= "{$arrow}:hover{background:{$arrowHoverBg};border-color:{$arrowHoverBg};box-shadow:0 10px 22px " . $this->TLPhex2rgba( $arrowHoverBg, 0.32 ) . ';}';
			}
			if ( $arrowHoverTxt ) {
				$css .= "{$arrow}:hover{color:{$arrowHoverTxt};}";
			}
			if ( $dotActiveBg ) {
				$css .= "{$id} .owl-theme .owl-dots .owl-dot.active span{background:{$dotActiveBg};}";
			}

			return $css;
		}

		public function cardLayoutCss( $layoutID, $layout, $args = [] ) {

			// The card layouts share the same overlay / content markup, so they share this
			// block. layout1 is the vertical card and isotope1 the same card in isotope mode,
			// layout2 the horizontal one, layout3 the hover card whose overlay carries the
			// title and description.
			if ( ! in_array( $layout, [ 'layout1', 'isotope1', 'carousel1', 'layout2', 'isotope2', 'carousel2', 'layout3', 'isotope3', 'carousel3' ], true ) ) {
				return '';
			}

			// layout1, isotope1 and carousel1 are the same design in three modes.
			$isVerticalCard = in_array( $layout, [ 'layout1', 'isotope1', 'carousel1' ], true );

			// layout3, isotope3 and carousel3 are the same hover card in three modes.
			$isHoverCard = in_array( $layout, [ 'layout3', 'isotope3', 'carousel3' ], true );

			// A slider carries no per-item lift shadow - inside a carousel the cards sit flat
			// on the page, so the card's hover shadow is left off. See the carousel1 block in
			// the stylesheet, which drops the resting shadow for the same reason.
			$isSlider = in_array( $layout, [ 'carousel1', 'carousel3' ], true );

			$css        = '';
			$scope      = "#{$layoutID} .{$layout}";
			$primary    = ! empty( $args['primary_color'] ) ? $args['primary_color'] : '';
			$nameHover  = ! empty( $args['name_hover_color'] ) ? $args['name_hover_color'] : '';
			$nameColor  = ! empty( $args['name_color'] ) ? $args['name_color'] : '';
			$iconSize   = ! empty( $args['icon_size'] ) ? absint( $args['icon_size'] ) : 0;
			$iconAlign  = ! empty( $args['icon_align'] ) ? $args['icon_align'] : '';
			$iconColor  = ! empty( $args['icon_color'] ) ? $args['icon_color'] : '';
			$imageWidth = isset( $args['image_width'] ) && '' !== $args['image_width'] ? absint( $args['image_width'] ) : 0;

			// Image / content split. The stylesheet reads --tlp-card-img-w inside the desktop
			// media query, so the card still stacks on mobile whatever this is set to.
			// layout1, isotope1, carousel1, layout3 and isotope3 are vertical cards and have no split.
			if ( ! $isVerticalCard && ! $isHoverCard && $imageWidth > 0 && $imageWidth < 100 ) {
				$css .= "{$scope}{--tlp-card-img-w:{$imageWidth}%;}";
			}

			if ( $primary ) {
				// Undo the shared primary rules, which assume a solid content block.
				$css .= "{$scope} .tlp-portfolio-item .tlp-content{background:transparent;}";
				if ( $isVerticalCard ) {
					$padding = '22px 24px 26px';
				} elseif ( $isHoverCard ) {
					$padding = '20px 24px 22px';
				} else {
					$padding = '24px 26px';
				}
				$css .= "{$scope} .tlp-portfolio-item .tlp-content .tlp-content-holder{padding:{$padding};}";

				// Primary drives the hover overlay and the hover shadow, unless an explicit
				// overlay colour is set — that control is the more specific intent.
				if ( empty( $args['overlay_color'] ) ) {
					if ( $isHoverCard ) {
						// Layout 3's overlay is a gradient, so primary has to drive both stops
						// or the flat colour would flatten the design. Set in every state, not only
						// :hover - the overlay is invisible at rest but fades out after the pointer
						// leaves, and a hover-only colour snapped back to the stylesheet's blue for
						// the length of that fade. Same as pro's layout 3.
						$css .= "{$scope} .tlp-portfolio-item .tlp-overlay{background:linear-gradient(165deg,"
							. TLPPortfolio()->TLPhex2rgba( $primary, 0.92 ) . ","
							. TLPPortfolio()->TLPhex2rgba( $primary, 0.95 ) . ");}";
					} else {
						// Layout 1's and layout 2's wash is a gradient that fades out after the
						// pointer leaves. A hover-only flat colour dropped the gradient and snapped
						// back to the stylesheet's blue for the length of that fade, so the colour is
						// held at rest too (the overlay is invisible at rest). Same as pro's layout 1 / 2.
						$css .= "{$scope} .tlp-portfolio-item .tlp-overlay{background:linear-gradient(180deg,"
							. TLPPortfolio()->TLPhex2rgba( $primary, 0.28 ) . ","
							. TLPPortfolio()->TLPhex2rgba( $primary, 0.82 ) . ");}";
					}
				}
				if ( ! $isSlider ) {
					$css .= "{$scope} .tlp-portfolio-item:hover{box-shadow:0 18px 40px "
						. TLPPortfolio()->TLPhex2rgba( $primary, 0.28 ) . ";}";
				}

				// Badge and the "view project" link have no control of their own. The icon and
				// the title hover deliberately do not follow primary — they have dedicated
				// controls that must win.
				// The caption title turns primary when the card is hovered. The stylesheet
				// hardcodes #004bff for that, so it has to be restated here or the title keeps
				// the stock blue whatever Primary is set to. Name Hover is emitted after this
				// and still wins.
				$css .= "{$scope} .tlp-portfolio-item:hover .tlp-content h3, {$scope} .tlp-portfolio-item:hover .tlp-content h3 a{color:{$primary};}";

				if ( $isHoverCard ) {
					// Layout 3 has no badge and no "view project" link. Its accent is the icon
					// hover, which sits on the primary gradient - so it inverts instead of
					// filling with primary.
					$css .= "{$scope} .tlp-overlay .link-icon a:hover{background:#fff;color:{$primary};border-color:#fff;}";
				} else {
					$css .= "{$scope} .tlp-badge{color:{$primary};}";
					if ( ! $isVerticalCard ) {
						// The horizontal card's badge sits on the card, not on the image, so it
						// needs a tinted background rather than layout1's translucent white.
						$css .= "{$scope} .tlp-badge{background:" . TLPPortfolio()->TLPhex2rgba( $primary, 0.10 ) . ";}";
					}
					$css .= "{$scope} .tlp-more{color:{$primary};}";
					// The overlay icon sits in a white disc and the stylesheet pins its glyph to
					// #004bff, which clashes once Primary is anything else. Primary drives it -
					// unless the Icon Colour control has a value, which must win.
					if ( ! $iconColor ) {
						$css .= "{$scope} .tlp-overlay .link-icon a{color:{$primary};}";
					}
					$css .= "{$scope} .tlp-overlay .link-icon a:hover{background:{$primary};color:#fff;}";
				}

				// The meta list is the card's most visible brand surface at rest - the icon
				// tiles and the masked project-URL button. Without these the Primary control
				// only changes hover states and looks like it does nothing.
				$css .= "{$scope} .extra-features .tlp-meta-ico{background:"
					. TLPPortfolio()->TLPhex2rgba( $primary, 0.10 ) . ";color:{$primary};}";
				$css .= "{$scope} .extra-features a.tlp-meta-link{color:{$primary};}";
				$css .= "{$scope} .extra-features a.tlp-meta-link:hover{background:{$primary};border-color:{$primary};color:#fff;}";
			}

			// Layout 1's and layout 2's overlay is a tint over the photo that fades in on hover. A plain
			// hex Overlay colour painted it solid, so hovering a card hid the photo completely. A hex
			// colour gets pro's default 80% - the same shortcode then looks the same in both tiers - while
			// a colour picked with the alpha slider (rgba) keeps the alpha it was given. The alpha
			// picker saves a fully opaque colour as rgb(), not hex, so both count as opaque. Layout 3's
			// overlay carries the caption text, so it is left as the user set it.
			$overlayColor = ! empty( $args['overlay_color'] ) ? $args['overlay_color'] : '';

			if ( ! $isHoverCard && $overlayColor && ( '#' === $overlayColor[0] || 0 === strpos( $overlayColor, 'rgb(' ) ) ) {
				$css .= "{$scope} .tlp-portfolio-item .tlp-overlay{background:" . TLPPortfolio()->TLPhex2rgba( $overlayColor, 0.8 ) . ';}';
			}

			// Layout 3 / isotope 3 repeat the title inside the overlay, on a dark gradient. The Name
			// colour control owns the caption title; the overlay copy stays white so it
			// cannot disappear into the overlay.
			if ( $isHoverCard && $nameColor ) {
				$css .= "{$scope} .tlp-overlay h3, {$scope} .tlp-overlay h3 a{color:#fff;}";
			}

			// Name hover colour should apply on card hover, not only on hovering the text.
			if ( $nameHover ) {
				$css .= "{$scope} .tlp-portfolio-item:hover .tlp-content h3, {$scope} .tlp-portfolio-item:hover .tlp-content h3 a{color:{$nameHover};}";
			}

			// The icons are inline SVG sized in em, so drive them from the anchor font-size.
			if ( $iconSize ) {
				$css .= "{$scope} .tlp-overlay .link-icon a{font-size:{$iconSize}px;}";
			}

			// The icon row is a flex box, so text-align has no effect.
			if ( $iconAlign ) {
				$map     = [ 'left' => 'flex-start', 'center' => 'center', 'right' => 'flex-end' ];
				$justify = isset( $map[ $iconAlign ] ) ? $map[ $iconAlign ] : 'center';
				$css    .= "{$scope} .tlp-overlay .link-icon{justify-content:{$justify};}";
			}

			return $css;
		}

		/**
		 * Sanitize a shortcode's item-field list and make pro-written lists usable.
		 *
		 * Both plugins write the same `pfp_item_fields` meta on the shared `portfolio-sc`
		 * post type but disagree on the values: pro stores the zoom control as `zoom` where
		 * this plugin expects `zoom_image`. A shortcode configured under pro and then opened
		 * here would lose its zoom icon. Pro also stores `featured_image`, which has no
		 * meaning here because the layouts always render the thumbnail when one exists.
		 *
		 * @param mixed $input Raw meta value.
		 *
		 * @return array
		 */
		public function normalize_sc_item_fields( $input ) {
			$fields = $this->array_text_sanitization( $input );

			if ( empty( $fields ) ) {
				return $fields;
			}

			if ( in_array( 'zoom', $fields, true ) && ! in_array( 'zoom_image', $fields, true ) ) {
				$fields[] = 'zoom_image';
			}

			return $fields;
		}

		/**
		 * A custom sanitization function that will take the incoming input, and sanitize
		 * the input before handing it back to WordPress to save to the database and The output of frontend.
		 *
		 * @since    1.0.0
		 *
		 * @param    array $input  Array with integer value  .
		 * @return   array.
		 */
		public function array_int_sanitization( $input ) {
			$input = maybe_unserialize( $input );
			return ! empty( $input ) ? array_map( 'absint', array_filter( $input ) ) : [];
		}

		/**
		 * Prints HTMl.
		 *
		 * @param string $html HTML.
		 * @param bool   $allHtml All HTML.
		 *
		 * @return mixed
		 */
		public function print_html( $html, $allHtml = false ) {
			if ( $allHtml ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo stripslashes_deep( $html );
			} else {
				echo wp_kses_post( stripslashes_deep( $html ) );
			}
		}

		/**
		 * Allowed HTML for wp_kses.
		 *
		 * @param string $level Tag level.
		 *
		 * @return mixed
		 */
		public function allowedHtml( $level = 'basic' ) {
			$allowed_html = [];

			switch ( $level ) {
				case 'basic':
					$allowed_html = [
						'b'      => [
							'class' => [],
							'id'    => [],
						],
						'i'      => [
							'class' => [],
							'id'    => [],
						],
						'u'      => [
							'class' => [],
							'id'    => [],
						],
						'br'     => [
							'class' => [],
							'id'    => [],
						],
						'em'     => [
							'class' => [],
							'id'    => [],
						],
						'span'   => [
							'class' => [],
							'id'    => [],
							'style' => [],
						],
						'strong' => [
							'class' => [],
							'id'    => [],
						],
						'hr'     => [
							'class' => [],
							'id'    => [],
						],
						'div'    => [
							'class' => [],
							'id'    => [],
						],
						'a'      => [
							'href'   => [],
							'title'  => [],
							'class'  => [],
							'id'     => [],
							'target' => [],
						],
					];
					break;

				case 'advanced':
					$allowed_html = [
						'b'      => [
							'class' => [],
							'id'    => [],
						],
						'i'      => [
							'class' => [],
							'id'    => [],
						],
						'u'      => [
							'class' => [],
							'id'    => [],
						],
						'br'     => [
							'class' => [],
							'id'    => [],
						],
						'em'     => [
							'class' => [],
							'id'    => [],
						],
						'span'   => [
							'class' => [],
							'id'    => [],
						],
						'strong' => [
							'class' => [],
							'id'    => [],
						],
						'hr'     => [
							'class' => [],
							'id'    => [],
						],
						'a'      => [
							'href'   => [],
							'title'  => [],
							'class'  => [],
							'id'     => [],
							'target' => [],
						],
						'input'  => [
							'type'  => [],
							'name'  => [],
							'class' => [],
							'value' => [],
						],
					];
					break;

				case 'image':
					$allowed_html = [
						'img' => [
							'src'      => [],
							'data-src' => [],
							'alt'      => [],
							'height'   => [],
							'width'    => [],
							'class'    => [],
							'id'       => [],
							'style'    => [],
							'srcset'   => [],
							'loading'  => [],
							'sizes'    => [],
						],
						'div' => [
							'class' => [],
						],
					];
					break;

				case 'anchor':
					$allowed_html = [
						'a' => [
							'href'  => [],
							'title' => [],
							'class' => [],
							'id'    => [],
							'style' => [],
						],
					];
					break;

				default:
					// code...
					break;
			}

			return $allowed_html;
		}

		/**
		 * Definition for wp_kses.
		 *
		 * @param string $string String to check.
		 * @param string $level Tag level.
		 *
		 * @return mixed
		 */
		public function htmlKses( $string, $level ) {
			if ( empty( $string ) ) {
				return;
			}

			return wp_kses( $string, self::allowedHtml( $level ) );
		}
	}
endif;
