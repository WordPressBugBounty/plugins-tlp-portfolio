<?php
/**
 * Frontend class.
 *
 * @package RT_Portfolio
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

if ( ! class_exists( 'TPLportFrontEnd' ) ) :
	/**
	 * Frontend class.
	 */
	class TPLportFrontEnd {

		public function __construct() {
			add_action( 'wp_head', [ $this, 'custom_css' ] );
			add_action( 'wp_enqueue_scripts', [ $this, 'tlp_portfolio_wp_enqueue_scripts' ] );

            add_action( 'wp_ajax_pfp_ajax_filter', [ $this, 'pfp_filter_callback' ] );
            add_action( 'wp_ajax_nopriv_pfp_ajax_filter', [ $this, 'pfp_filter_callback' ] );
		}

		public function custom_css() {
			global $TLPportfolio;

			$html     = null;
			$settings = get_option( $TLPportfolio->options['settings'] );
			$pc       = ( isset( $settings['primary_color'] ) ? ( $settings['primary_color'] ? $settings['primary_color'] : '#0367bf' ) : '#0367bf' );
			$cCss     = ( isset( $settings['custom_css'] ) ? ( $settings['custom_css'] ? $settings['custom_css'] : null ) : null );

			if ( $cCss || $pc ) { ?>
				<style>
					.tlp-team .short-desc, .tlp-team .tlp-team-isotope .tlp-content, .tlp-team .button-group .selected, .tlp-team .layout1 .tlp-content, .tlp-team .tpl-social a, .tlp-team .tpl-social li a.fa,.tlp-portfolio button.selected,.tlp-portfolio .layoutisotope .tlp-portfolio-item .tlp-content,.tlp-portfolio button:hover {
						background: <?php echo esc_html( $pc ); ?> ;
					}
					.tlp-portfolio .layoutisotope .tlp-overlay,.tlp-portfolio .layout1 .tlp-overlay,.tlp-portfolio .layout2 .tlp-overlay,.tlp-portfolio .layout3 .tlp-overlay, .tlp-portfolio .slider .tlp-overlay {
						background: <?php echo esc_html( $TLPportfolio->TLPhex2rgba( $pc, .8 ) ); ?>;
					}
					<?php echo esc_html( $cCss ); ?>
				</style>
				<?php
			}
		}

		public function tlp_portfolio_wp_enqueue_scripts() {
			global $TLPportfolio;

			wp_enqueue_style( 'tlpportfolio-css', $TLPportfolio->assetsUrl . 'css/tlpportfolio.css', [], '1.0', 'all' );

			$version    = '';
			$upload_dir = wp_upload_dir();
			$cssFile    = $upload_dir['basedir'] . '/tlp-portfolio/portfolio-sc.css';

			if ( file_exists( $cssFile ) ) {
				$version = filemtime( $cssFile );
				wp_enqueue_style( 'portfolio-sc', set_url_scheme( $upload_dir['baseurl'] ) . '/tlp-portfolio/portfolio-sc.css', '', $version );
			}
		}
        /**
         * AJAX callback for portfolio search filtering.
         *
         * Queries portfolio items matching the search term and returns
         * rendered HTML for the matching items.
         *
         * @return void Sends JSON response and exits.
         */
        public function pfp_filter_callback() {
            if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ?? '' ) ), TLPPortfolio()->nonceText() ) ) {
                wp_send_json_error( [ 'msg' => esc_html__( 'Invalid nonce.', 'tlp-portfolio' ) ] );
            }
            $sc_id   = isset( $_POST['sc_id'] ) ? absint( $_POST['sc_id'] ) : 0;
            $search  = isset( $_POST['search'] ) ? sanitize_text_field( wp_unslash( $_POST['search'] ) ) : '';
            $paged   = isset( $_POST['paged'] ) ? absint( $_POST['paged'] ) : 1;

            if ( ! $sc_id ) {
                wp_send_json_error( [ 'msg' => esc_html__( 'Invalid shortcode ID.', 'tlp-portfolio' ) ] );
            }

            $scMeta = get_post_meta( $sc_id );

            $layout   = isset( $scMeta['pfp_layout'][0] ) && ! empty( $scMeta['pfp_layout'][0] ) ? sanitize_text_field( $scMeta['pfp_layout'][0] ) : 'layout1';
            $dCol     = isset( $scMeta['pfp_desktop_column'][0] ) && ! empty( $scMeta['pfp_desktop_column'][0] ) ? absint( $scMeta['pfp_desktop_column'][0] ) : 3;
            $tCol     = isset( $scMeta['pfp_tab_column'][0] ) && ! empty( $scMeta['pfp_tab_column'][0] ) ? absint( $scMeta['pfp_tab_column'][0] ) : 2;
            $mCol     = isset( $scMeta['pfp_mobile_column'][0] ) && ! empty( $scMeta['pfp_mobile_column'][0] ) ? absint( $scMeta['pfp_mobile_column'][0] ) : 1;
            $imgSize  = isset( $scMeta['pfp_image_size'][0] ) && ! empty( $scMeta['pfp_image_size'][0] ) ? sanitize_text_field( $scMeta['pfp_image_size'][0] ) : 'medium';

            $customImgSize = isset( $scMeta['pfp_custom_image_size'][0] ) && ! empty( $scMeta['pfp_custom_image_size'][0] ) ? TLPPortfolio()->array_text_sanitization( $scMeta['pfp_custom_image_size'][0] ) : [];

            $excerpt_limit = isset( $scMeta['pfp_excerpt_limit'][0] ) && ! empty( $scMeta['pfp_excerpt_limit'][0] ) ? absint( $scMeta['pfp_excerpt_limit'][0] ) : 0;
            $disable_image = isset( $scMeta['pfp_disable_image'][0] ) && ! empty( $scMeta['pfp_disable_image'][0] ) ? true : false;

            $post__in     = isset( $scMeta['pfp_post__in'][0] ) && ! empty( $scMeta['pfp_post__in'][0] ) ? sanitize_text_field( trim( $scMeta['pfp_post__in'][0] ) ) : null;
            $post__not_in = isset( $scMeta['pfp_post__not_in'][0] ) && ! empty( $scMeta['pfp_post__not_in'][0] ) ? sanitize_text_field( trim( $scMeta['pfp_post__not_in'][0] ) ) : null;

            $limit      = ! isset( $scMeta['pfp_limit'][0] ) || ( isset( $scMeta['pfp_limit'][0] ) && ( empty( $scMeta['pfp_limit'][0] ) || $scMeta['pfp_limit'][0] === '-1' ) ) ? 10000000 : absint( $scMeta['pfp_limit'][0] );
            $pagination = isset( $scMeta['pfp_pagination'][0] ) && ! empty( $scMeta['pfp_pagination'][0] ) ? true : false;

            $cats     = isset( $scMeta['pfp_categories'] ) && ! empty( $scMeta['pfp_categories'] ) ? TLPPortfolio()->array_int_sanitization( $scMeta['pfp_categories'] ) : [];
            $tags     = isset( $scMeta['pfp_tags'] ) && ! empty( $scMeta['pfp_tags'] ) ? TLPPortfolio()->array_int_sanitization( $scMeta['pfp_tags'] ) : [];
            $relation = isset( $scMeta['pfp_taxonomy_relation'][0] ) && ! empty( $scMeta['pfp_taxonomy_relation'][0] ) ? sanitize_text_field( $scMeta['pfp_taxonomy_relation'][0] ) : 'AND';
            $order_by = isset( $scMeta['pfp_order_by'][0] ) && ! empty( $scMeta['pfp_order_by'][0] ) ? sanitize_text_field( $scMeta['pfp_order_by'][0] ) : null;
            $order    = isset( $scMeta['pfp_order'][0] ) && ! empty( $scMeta['pfp_order'][0] ) ? sanitize_text_field( $scMeta['pfp_order'][0] ) : null;

            $arg              = [];
            $arg['link']      = isset( $scMeta['pfp_detail_page_link'][0] ) && ! empty( $scMeta['pfp_detail_page_link'][0] );
            $arg['link_type'] = isset( $scMeta['pfp_detail_page_link_type'][0] ) && ! empty( $scMeta['pfp_detail_page_link_type'][0] ) ? sanitize_text_field( $scMeta['pfp_detail_page_link_type'][0] ) : 'inner_link';
            $arg['link_target'] = $arg['link_type'] == 'external_link' && isset( $scMeta['pfp_link_target'][0] ) && $scMeta['pfp_link_target'][0] == '_blank' ? '_blank' : null;

            $disable_equal_height = isset( $scMeta['pfp_disable_equal_height'][0] ) && ! empty( $scMeta['pfp_disable_equal_height'][0] );

            if ( isset( $scMeta['pfp_item_fields'] ) ) {
                $arg['items'] = ! empty( $scMeta['pfp_item_fields'] ) ? TLPPortfolio()->array_text_sanitization( $scMeta['pfp_item_fields'] ) : [];
            } else {
                $arg['items'] = [ 'name', 'short_description', 'zoom_image' ];
            }

            $isIsotope  = preg_match( '/isotope/', $layout );
            $isCarousel = preg_match( '/carousel/', $layout );
            $isLayout   = preg_match( '/layout/', $layout );

            if ( ! in_array( $layout, array_keys( TLPPortfolio()->scLayouts() ) ) ) {
                $layout = 'layout1';
            }

            if ( ! in_array( $dCol, array_keys( TLPPortfolio()->scColumns() ) ) ) {
                $dCol = 3;
            }
            if ( ! in_array( $tCol, array_keys( TLPPortfolio()->scColumns() ) ) ) {
                $tCol = 2;
            }
            if ( ! in_array( $mCol, array_keys( TLPPortfolio()->scColumns() ) ) ) {
                $mCol = 1;
            }

            $old_dCol = $dCol;
            $dCol_grid = round( 12 / $dCol );
            $tCol_grid = round( 12 / $tCol );
            $mCol_grid = round( 12 / $mCol );

            if ( $isCarousel ) {
                $dCol_grid = $tCol_grid = $mCol_grid = 12;
            }

            $arg['grid'] = sprintf(
                    'tlp-col-md-%d tlp-col-sm-%d tlp-col-xs-%d tlp-single-item%s%s%s%s',
                    $dCol_grid,
                    $tCol_grid,
                    $mCol_grid,
                    $isIsotope ? ' tlp-isotope-item' : null,
                    $isCarousel ? ' tlp-carousel-item' : null,
                    $isLayout ? ' tlp-grid-item' : null,
                    ! $isIsotope && ! $disable_equal_height ? ' tlp-equal-height' : null
            );

            if ( $old_dCol == 2 ) {
                $arg['image_area']   = 'tlp-col-lg-5 tlp-col-md-5 tlp-col-sm-6 tlp-col-xs-12 ';
                $arg['content_area'] = 'tlp-col-lg-7 tlp-col-md-7 tlp-col-sm-6 tlp-col-xs-12 ';
            } else {
                $arg['image_area']   = 'tlp-col-lg-3 tlp-col-md-3 tlp-col-sm-6 tlp-col-xs-12 ';
                $arg['content_area'] = 'tlp-col-lg-9 tlp-col-md-9 tlp-col-sm-6 tlp-col-xs-12 ';
            }

            $query_args = [
                    'post_type'   => TLPPortfolio()->post_type,
                    'post_status' => 'publish',
                    's'           => $search,
            ];

            if ( $post__in ) {
                $query_args['post__in'] = explode( ',', $post__in );
            }

            if ( $post__not_in ) {
                $query_args['post__not_in'] = explode( ',', $post__not_in );
            }

            $taxQ = [];

            if ( is_array( $cats ) && ! empty( $cats ) ) {
                $taxQ[] = [
                        'taxonomy' => TLPPortfolio()->taxonomies['category'],
                        'field'    => 'term_id',
                        'terms'    => $cats,
                ];
            }

            if ( is_array( $tags ) && ! empty( $tags ) ) {
                $taxQ[] = [
                        'taxonomy' => TLPPortfolio()->taxonomies['tag'],
                        'field'    => 'term_id',
                        'terms'    => $tags,
                ];
            }

            if ( ! empty( $taxQ ) ) {
                if ( count( $taxQ ) > 1 ) {
                    $taxQ['relation'] = $relation;
                }
                $query_args['tax_query'] = $taxQ;
            }

            if ( $order ) {
                $query_args['order'] = $order;
            }

            if ( $order_by ) {
                $query_args['orderby'] = $order_by;
            }

            /**
             * Handle pagination for search results.
             */
            if ( ! empty( $search ) ) {
                $query_args['posts_per_page'] = $limit;
            } elseif ( $pagination && ! ( $isCarousel || $isIsotope ) ) {
                $posts_per_page = isset( $scMeta['pfp_posts_per_page'][0] ) ? absint( $scMeta['pfp_posts_per_page'][0] ) : $limit;

                if ( $posts_per_page > $limit ) {
                    $posts_per_page = $limit;
                }

                $query_args['posts_per_page']      = $posts_per_page;
                $query_args['posts_per_page_meta'] = $posts_per_page;
                $query_args['paged']               = $paged;

                $offset         = $posts_per_page * ( (int) $paged - 1 );
                $remaining_post = $limit - $offset;

                if ( 0 < $remaining_post ) {
                    if ( intval( $query_args['posts_per_page'] ) > $remaining_post ) {
                        $query_args['posts_per_page'] = $remaining_post;
                        $query_args['offset']         = $offset;
                    }
                } else {
                    $query_args['posts_per_page'] = 0;
                }
            } else {
                $query_args['posts_per_page'] = $limit;
            }

            $portfolioQuery = new WP_Query( apply_filters( 'tlp_portfolio_sc_search_query_args', $query_args ) );

            global $TLPportfolio;

            $html              = '';
            $current_page_term = [];

            if ( $portfolioQuery->have_posts() ) {
                while ( $portfolioQuery->have_posts() ) :
                    $portfolioQuery->the_post();

                    $iID                   = get_the_ID();
                    $arg['categories']     = wp_strip_all_tags( get_the_term_list( $iID, $TLPportfolio->taxonomies['category'], '', ', ' ) );
                    $arg['title']          = get_the_title();
                    $arg['iID']            = $iID;
                    $arg['item_link']      = get_permalink();
                    $arg['project_url']    = get_post_meta( $iID, 'project_url', true );
                    $arg['client_name']    = get_post_meta( $iID, 'client_name', true );
                    $arg['completed_date'] = get_post_meta( $iID, 'completed_date', true );
                    $arg['tools']          = get_post_meta( $iID, 'tools', true );
                    $arg['pLink']          = get_permalink();

                    if ( $arg['link_type'] == 'external_link' && $arg['project_url'] ) {
                        $arg['item_link'] = $arg['project_url'];
                    }

                    $short_d        = get_post_meta( $iID, 'short_description', true );
                    $arg['short_d'] = TLPPortfolio()->get_short_description( $short_d, $excerpt_limit );

                    if ( $isIsotope ) {
                        $termAs    = wp_get_post_terms( $iID, TLPPortfolio()->taxonomies['category'], [ 'fields' => 'all' ] );
                        $isoFilter = null;

                        if ( ! empty( $termAs ) ) {
                            foreach ( $termAs as $term ) {
                                $isoFilter          .= ' ' . 'iso_' . $term->term_id;
                                $isoFilter          .= ' ' . $term->slug;
                                $current_page_term[] = $term->term_id;
                            }
                        }

                        $arg['isoFilter'] = $isoFilter;
                    }

                    if ( $disable_image ) {
                        $arg['content_area'] = 'tlp-col-md-12';
                        $arg['imgFull']      = $arg['img'] = null;
                    } else {
                        if ( has_post_thumbnail() ) {
                            $arg['img']     = TLPPortfolio()->getFeatureImageSrc( $iID, $imgSize, $customImgSize );
                            $imageFull      = wp_get_attachment_image_src( get_post_thumbnail_id( $iID ), 'full' );
                            $arg['imgFull'] = isset( $imageFull[0] ) ? $imageFull[0] : '';
                        } else {
                            $arg['img'] = $arg['imgFull'] = null;
                        }
                        $arg['imgFull'] = ! $arg['imgFull'] && $arg['img'] ? $arg['img'] : $arg['imgFull'];
                    }

                    $html .= TLPPortfolio()->render( 'layouts/' . $layout, $arg, true );
                endwhile;

                wp_reset_postdata();
            }

            $pagination_html = '';

            if ( empty( $search ) && $pagination && ! ( $isCarousel || $isIsotope ) && $portfolioQuery->have_posts() ) {
                $pagination_html = TLPPortfolio()->pagination( $portfolioQuery, $query_args, $scMeta );
            }

            $cat_ids = $cats;

            /**
             * Build isotope filter buttons for search results.
             */
            $filter_button = '';

            if ( $isIsotope && ! empty( $current_page_term ) ) {
                $terms = get_terms(
                        apply_filters(
                                'tlp_portfolio_sc_isotope_button_args',
                                [
                                        'taxonomy'   => TLPPortfolio()->taxonomies['category'],
                                        'orderby'    => 'name',
                                        'order'      => 'ASC',
                                        'hide_empty' => false,
                                ]
                        )
                );

                if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                    $filter_button .= sprintf(
                            '<div class="tlp-portfolio-isotope-button button-group filter-button-group option-set"><button data-filter="*" class="selected">%s</button>',
                            __( 'Show all', 'tlp-portfolio' )
                    );

                    foreach ( $terms as $term ) {
                        if ( ! empty( $cat_ids ) ) {
                            if ( in_array( $term->term_id, $cat_ids ) && in_array( $term->term_id, $current_page_term ) ) {
                                $filter_button .= "<button data-filter='." . esc_attr( $term->slug ) . "'>" . esc_html( $term->name ) . '</button>';
                            }
                        } else {
                            if ( in_array( $term->term_id, $current_page_term ) ) {
                                $filter_button .= "<button data-filter='." . esc_attr( $term->slug ) . "'>" . esc_html( $term->name ) . '</button>';
                            }
                        }
                    }

                    $filter_button .= '</div>';
                }
            }

            wp_send_json_success(
                    [
                            'html'            => $html,
                            'pagination'      => $pagination_html,
                            'filter_button'   => $filter_button,
                            'found_posts'     => $portfolioQuery->found_posts,
                            'is_isotope'      => (bool) $isIsotope,
                    ]
            );
        }

	}
endif;
