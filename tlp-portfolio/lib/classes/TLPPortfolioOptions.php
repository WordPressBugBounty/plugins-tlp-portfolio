<?php
/**
 * Options class.
 *
 * @package RT_Portfolio
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

if ( ! class_exists( 'TLPPortfolioOptions' ) ) :
	/**
	 * Options class.
	 */
	class TLPPortfolioOptions {
		public function scLayoutMetaFields() {
			global $TLPportfolio;

			return [
				'pfp_layout_type'               => [
					'type'    => 'radio-image',
					'label'   => esc_html__( 'Layout type', 'tlp-portfolio' ),
					'id'      => 'rtpfp-layout-type',
					'options' => [
						'grid'     => [
							'title'   => esc_html__( 'Grid Layout', 'tlp-portfolio' ),
							'demoUrl' => '',
							'img'     => $TLPportfolio->assetsUrl . 'images/layout_type_grid.png',
						],
						'isotope'  => [
							'title'   => esc_html__( 'Isotop Layout', 'tlp-portfolio' ),
							'demoUrl' => '',
							'img'     => $TLPportfolio->assetsUrl . 'images/layout_type_isotope.png',
						],
						'carousel' => [
							'title'   => esc_html__( 'Slider Layout', 'tlp-portfolio' ),
							'demoUrl' => '',
							'img'     => $TLPportfolio->assetsUrl . 'images/layout_type_slider.png',
						],
					],
				],
				'pfp_layout'                    => [
					'label'   => esc_html__( 'Layout', 'tlp-portfolio' ),
					'type'    => 'radio-image',
					'class'   => 'rt-select2',
					'options' => $this->scLayouts(),
				],
				'pfp_image_width'               => [
					'type'        => 'number',
					'holderClass' => 'tlp-card-split-item pfp-hidden',
					'label'       => esc_html__( 'Image area width (%)', 'tlp-portfolio' ),
					'class'       => 'small-text',
					'default'     => 40,
					'description' => __( 'Layout 2, Isotope 2 and Carousel 2 only. Percentage of the card taken by the image; the content takes the rest. Default 40 (so the content is 60). Ignored on mobile, where the card stacks.', 'tlp-portfolio' ),
				],
				'pfp_isotope_filter_taxonomy'   => [
					'type'        => 'select',
					'label'       => __( "Isotope filter (Selected item) <span style='color:red;'>Pro feature</span>", 'tlp-portfolio' ),
					'holderClass' => 'pfp-isotope-item pfp-hidden',
					'class'       => 'rt-select2',
					'attr'        => 'disabled',
				],
				'pfp_isotope_filter_show_all'   => [
					'type'        => 'checkbox',
					'label'       => __( "Isotope filter (Show All item) <span style='color:red;'>Pro feature</span>", 'tlp-portfolio' ),
					'holderClass' => 'pfp-isotope-item pfp-hidden',
					'id'          => 'rt-tpg-sc-isotope-filter-show-all',
					'optionLabel' => esc_html__( 'Enable', 'tlp-portfolio' ),
					'option'      => 1,
					'attr'        => 'disabled',
				],
				'pfp_isotope_search_filtering'  => [
					'type'        => 'checkbox',
					'label'       => "Isotope search filter <span style='color:red;'>Pro feature</span>",
					'holderClass' => 'pfp-isotope-item pfp-hidden',
					'optionLabel' => 'Enable',
					'option'      => 1,
					'attr'        => 'disabled',
				],

				'pfp_carousel_speed'            => [
					'label'       => esc_html__( 'Speed', 'tlp-portfolio' ),
					'holderClass' => 'pfp-hidden pfp-carousel-item',
					'type'        => 'number',
					'default'     => 2000,
					'description' => esc_html__( 'Auto play Speed in milliseconds', 'tlp-portfolio' ),
				],
				'pfp_carousel_options'          => [
					'label'       => esc_html__( 'Carousel Options', 'tlp-portfolio' ),
					'holderClass' => 'pfp-hidden pfp-carousel-item',
					'type'        => 'checkbox',
					'multiple'    => true,
					'alignment'   => 'vertical',
					'options'     => $this->owlProperty(),
					// Option keys from owlProperty(). It listed arrows / responsive / infinite, which are not keys, so a new
					// carousel started with no arrows and no loop: nav and loop are what those names meant.
					'default'     => [ 'autoplay', 'nav', 'dots', 'loop' ],
				],
				'pfp_carousel_autoplay_timeout' => [
					'label'       => esc_html__( 'Autoplay timeout', 'tlp-portfolio' ),
					'holderClass' => 'pfp-hidden pfp-carousel-auto-play-timeout',
					'type'        => 'number',
					'default'     => 5000,
					'description' => esc_html__( 'Autoplay interval timeout', 'tlp-portfolio' ),
				],
				'pfp_desktop_column'            => [
					'type'    => 'select',
					'label'   => esc_html__( 'Desktop column', 'tlp-portfolio' ),
					'class'   => 'rt-select2',
					'default' => 3,
					'options' => $this->scColumns(),
				],
				'pfp_tab_column'                => [
					'type'    => 'select',
					'label'   => esc_html__( 'Tab column', 'tlp-portfolio' ),
					'class'   => 'rt-select2',
					'default' => 2,
					'options' => $this->scColumns(),
				],
				'pfp_mobile_column'             => [
					'type'    => 'select',
					'label'   => esc_html__( 'Mobile column', 'tlp-portfolio' ),
					'class'   => 'rt-select2',
					'default' => 1,
					'options' => $this->scColumns(),
				],
                "pfp_filter" => array(
                    "type"        => "checkbox",
                    "label"       => esc_html__("Filter", 'tlp-portfolio'),
                    "multiple"    => true,
                    "alignment"   => "vertical",
                    "default"     => 'search',
                    "options"     => [
                        'search' => esc_html__('Search', 'tlp-portfolio'),
                    ],
                    'description' => __(
                        'Filter works only Grid & Isotope layout.',
                        'tlp-portfolio'
                    ),
                ),
				'pfp_pagination'                => [
					'type'        => 'checkbox',
					'label'       => esc_html__( 'Pagination', 'tlp-portfolio' ),
					'holderClass' => 'pagination',
					'optionLabel' => esc_html__( 'Enable', 'tlp-portfolio' ),
					'option'      => 1,
				],
				'pfp_posts_per_page'            => [
					'type'        => 'number',
					'label'       => esc_html__( 'Display per page', 'tlp-portfolio' ),
					'holderClass' => 'pfp-pagination-item pfp-hidden',
					'default'     => 5,
					'description' => esc_html__(
						'If value of Limit setting is not blank (empty), this value should be smaller than Limit value.',
						'tlp-portfolio'
					),
				],
				'pfp_image_size'                => [
					'type'    => 'select',
					'label'   => esc_html__( 'Image Size', 'tlp-portfolio' ),
					'class'   => 'rt-select2',
					'options' => TLPPortfolio()->get_image_sizes(),
				],
				'pfp_custom_image_size'         => [
					'type'        => 'image_size',
					'label'       => esc_html__( 'Custom Image Size', 'tlp-portfolio' ),
					'holderClass' => 'pfp-hidden',
					'description' => __( 'We prefer to upload image larger than your custom image size. <span style="margin-top: 5px; display: block; color: #9A2A2A; font-weight: 400;">Please note that, if you enter image size larger than the actual image iteself, the image sizes will fallback to the full size image.</span>', 'tlp-portfolio' ),
				],
				'pfp_disable_image'             => [
					'type'        => 'checkbox',
					'label'       => esc_html__( 'Disable image', 'tlp-portfolio' ),
					'option'      => 1,
					'optionLabel' => esc_html__( 'Disable', 'tlp-portfolio' ),
				],
				'pfp_excerpt_limit'             => [
					'type'        => 'number',
					'label'       => esc_html__( 'Short description limit', 'tlp-portfolio' ),
					'description' => esc_html__(
						'Short description limit only integer number is allowed, Leave it blank for full text.',
						'tlp-portfolio'
					),
				],
				'pfp_detail_page_link'          => [
					'type'        => 'checkbox',
					'label'       => esc_html__( 'Detail page link', 'tlp-portfolio' ),
					'optionLabel' => esc_html__( 'Enable', 'tlp-portfolio' ),
					'default'     => 1,
					'option'      => 1,
				],
				'pfp_detail_page_link_type'     => [
					'type'        => 'radio',
					'label'       => esc_html__( 'Detail page link type', 'tlp-portfolio' ),
					'default'     => 'inner_link',
					'holderClass' => 'pfp_detail_page_link_type pfp-hidden pfp-detail-page-link-item',
					'alignment'   => 'vertical',
					'options'     => [
						'inner_link'    => 'Inner Link',
						'external_link' => 'External Link',
					],
				],
				'pfp_link_target'               => [
					'type'        => 'radio',
					'label'       => esc_html__( 'Link Target', 'tlp-portfolio' ),
					'default'     => '_blank',
					'holderClass' => 'pfp_link_target pfp-hidden pfp-detail-page-link-item',
					'alignment'   => 'vertical',
					'options'     => [
						'_self'  => 'Open in Same Tab',
						'_blank' => 'Open in New Tab',
					],
				],
			];
		}

		public function scFilterMetaFields() {
			return [
				'pfp_post__in'          => [
					'label'       => esc_html__( 'Include only', 'tlp-portfolio' ),
					'type'        => 'text',
					'description' => esc_html__(
						'List of post IDs to show (comma-separated values, for example: 1,2,3)',
						'tlp-portfolio'
					),
				],
				'pfp_post__not_in'      => [
					'label'       => esc_html__( 'Exclude', 'tlp-portfolio' ),
					'type'        => 'text',
					'description' => esc_html__(
						'List of post IDs to show (comma-separated values, for example: 1,2,3)',
						'tlp-portfolio'
					),
				],
				'pfp_limit'             => [
					'label'       => esc_html__( 'Limit', 'tlp-portfolio' ),
					'type'        => 'number',
					'description' => esc_html__(
						'The number of posts to show. Set empty to show all found posts.',
						'tlp-portfolio'
					),
				],
				'pfp_categories'        => [
					'label'       => esc_html__( 'Categories', 'tlp-portfolio' ),
					'type'        => 'select',
					'class'       => 'rt-select2',
					'multiple'    => true,
					'description' => esc_html__(
						'Select the category you want to filter, Leave it blank for All category',
						'tlp-portfolio'
					),
					'options'     => TLPPortfolio()->getAllPortFolioCategoryList(),
				],
				'pfp_tags'              => [
					'label'       => esc_html__( 'Tags', 'tlp-portfolio' ),
					'type'        => 'select',
					'class'       => 'rt-select2',
					'multiple'    => true,
					'description' => esc_html__(
						'Select the category you want to filter, Leave it blank for All category',
						'tlp-portfolio'
					),
					'options'     => TLPPortfolio()->getAllPortFolioTagList(),
				],
				'pfp_taxonomy_relation' => [
					'label'       => esc_html__( 'Taxonomy relation', 'tlp-portfolio' ),
					'type'        => 'select',
					'class'       => 'rt-select2',
					'description' => esc_html__(
						'Select this option if you select more than one taxonomy like category and tag, or category , tag and tools',
						'tlp-portfolio'
					),
					'options'     => $this->scTaxonomyRelation(),
				],
				'pfp_order_by'          => [
					'label'   => esc_html__( 'Order By', 'tlp-portfolio' ),
					'type'    => 'select',
					'class'   => 'rt-select2',
					'default' => 'date',
					'options' => $this->scOrderBy(),
				],
				'pfp_order'             => [
					'label'     => esc_html__( 'Order', 'tlp-portfolio' ),
					'type'      => 'radio',
					'options'   => $this->scOrder(),
					'default'   => 'DESC',
					'alignment' => 'vertical',
				],
			];
		}

		public function field() {
			return [
				'name'              => 'Name',
				'short_description' => 'Short description',
				'client_name'       => 'Client Name',
				'project_url'       => 'Project Url',
				'completed_date'    => 'Completed Date',
				'tools'             => 'Tools',
				'categories'        => 'Categories',
				'zoom_image'        => 'Zoom Image',
			];
		}

		public function scItemDefaultMetaFields() {
			return [
				'name'              => 'Name',
				'short_description' => 'Short description',
				'zoom_image'        => 'Zoom Image',
			];
		}

		public function scItemMetaFields() {
			return [
				'pfp_item_fields' => [
					'type'        => 'checkbox',
					'label'       => esc_html__( 'Field selection', 'tlp-portfolio' ),
					'multiple'    => true,
					'alignment'   => 'vertical',
					'default'     => array_keys( $this->scItemDefaultMetaFields() ),
					'options'     => $this->field(),
					'description' => esc_html__( 'Check the field which you want to display', 'tlp-portfolio' ),
				],
			];
		}

		public function scStyleFields() {
			return [
				'pfp_parent_class'            => [
					'type'        => 'text',
					'label'       => esc_html__( 'Parent class', 'tlp-portfolio' ),
					'class'       => 'medium-text',
					'description' => esc_html__( 'Parent class for adding custom css', 'tlp-portfolio' ),
				],
				'pfp_primary_color'           => [
					'type'  => 'colorpicker',
					'label' => esc_html__( 'Primary Color', 'tlp-portfolio' ),
					'alpha' => true,
				],
				'pfp_overlay_color'           => [
					'type'  => 'colorpicker',
					'label' => esc_html__( 'Overlay color', 'tlp-portfolio' ),
					'alpha' => true,
				],
				'pfp_gutter'                  => [
					'type'        => 'number',
					'label'       => esc_html__( 'Padding', 'tlp-portfolio' ),
					'description' => __( 'Unit will be pixel, No need to give any unit. Only integer value will be valid.<br> Leave it blank for default', 'tlp-portfolio' ),
				],
				'pfp_name_style'              => [
					'type'  => 'style',
					'label' => esc_html__( 'Name / Title', 'tlp-portfolio' ),
				],
				'pfp_name_hover_style'        => [
					'type'  => 'style',
					'label' => esc_html__( 'Name Hover', 'tlp-portfolio' ),
				],
				'pfp_short_description_style' => [
					'type'  => 'style',
					'label' => esc_html__( 'Short description', 'tlp-portfolio' ),
				],
				'pfp_icon_style'              => [
					'type'  => 'style',
					'label' => esc_html__( 'Icon style', 'tlp-portfolio' ),
				],
				'pfp_meta_style'              => [
					'type'  => 'style',
					'label' => esc_html__( 'Meta style', 'tlp-portfolio' ),
				],
			] + $this->scButtonStyleFields();
		}

		/**
		 * The Styling tab's button sections: one "All buttons" set, then a set per kind of button
		 * the shortcode renders. A colour left empty in a button's own section falls back to the
		 * matching "All buttons" colour (TLPPortfolioHelper::buttonGroupsCss()), so a shortcode saved
		 * before the split looks the same. Every key here is shared with pro, which reads the same
		 * portfolio-sc meta - rename one in both plugins or neither. Free has no Load More button,
		 * so pro's pfp_lm_btn_* section has no counterpart here.
		 *
		 * Headings ('type' => 'heading') render a section title and are never saved.
		 *
		 * @return array
		 */
		public function scButtonStyleFields() {
			return [
				'pfp_btn_all_heading'           => [
					'type'        => 'heading',
					'label'       => esc_html__( 'All buttons', 'tlp-portfolio' ),
					'description' => esc_html__( 'Default colours for every button. A colour set in a button section below wins over these for that button only.', 'tlp-portfolio' ),
				],
				'pfp_button_bg_color'           => [
					'type'        => 'colorpicker',
					'label'       => esc_html__( 'Button background color', 'tlp-portfolio' ),
					'description' => esc_html__( 'Page numbers, isotope filter buttons, carousel arrows and dots.', 'tlp-portfolio' ),
				],
				'pfp_button_hover_bg_color'     => [
					'type'        => 'colorpicker',
					'label'       => esc_html__( 'Button hover background color', 'tlp-portfolio' ),
					'description' => esc_html__( 'Hover state of page numbers, isotope filter buttons and carousel arrows.', 'tlp-portfolio' ),
				],
				'pfp_button_active_bg_color'    => [
					'type'        => 'colorpicker',
					'label'       => esc_html__( 'Button active background color', 'tlp-portfolio' ),
					'description' => esc_html__( 'The current page number, the selected isotope filter button and the active carousel dot.', 'tlp-portfolio' ),
				],
				// No default: left empty, the active buttons keep the design's white text. Same key as pro.
				'pfp_button_active_text_color'  => [
					'type'        => 'colorpicker',
					'label'       => esc_html__( 'Button active text color', 'tlp-portfolio' ),
					'description' => esc_html__( 'Text of the current page number and the selected isotope filter button.', 'tlp-portfolio' ),
				],
				'pfp_button_text_color'         => [
					'type'        => 'colorpicker',
					'label'       => esc_html__( 'Button text color', 'tlp-portfolio' ),
					'description' => esc_html__( 'Page numbers, isotope filter buttons and carousel arrows.', 'tlp-portfolio' ),
				],

				// Isotope filter buttons - isotope layouts only.
				'pfp_iso_btn_heading'           => [
					'type'        => 'heading',
					'holderClass' => 'pfp-isotope-item pfp-hidden',
					'label'       => esc_html__( 'Isotope filter buttons', 'tlp-portfolio' ),
					'description' => esc_html__( 'The category buttons above the grid. Leave a colour empty to use the All buttons colour.', 'tlp-portfolio' ),
				],
				'pfp_iso_btn_bg_color'          => [
					'type'        => 'colorpicker',
					'holderClass' => 'pfp-isotope-item pfp-hidden',
					'label'       => esc_html__( 'Background color', 'tlp-portfolio' ),
				],
				'pfp_iso_btn_text_color'        => [
					'type'        => 'colorpicker',
					'holderClass' => 'pfp-isotope-item pfp-hidden',
					'label'       => esc_html__( 'Text color', 'tlp-portfolio' ),
				],
				'pfp_iso_btn_hover_bg_color'    => [
					'type'        => 'colorpicker',
					'holderClass' => 'pfp-isotope-item pfp-hidden',
					'label'       => esc_html__( 'Hover background color', 'tlp-portfolio' ),
				],
				'pfp_iso_btn_hover_text_color'  => [
					'type'        => 'colorpicker',
					'holderClass' => 'pfp-isotope-item pfp-hidden',
					'label'       => esc_html__( 'Hover text color', 'tlp-portfolio' ),
				],
				'pfp_iso_btn_active_bg_color'   => [
					'type'        => 'colorpicker',
					'holderClass' => 'pfp-isotope-item pfp-hidden',
					'label'       => esc_html__( 'Selected background color', 'tlp-portfolio' ),
				],
				'pfp_iso_btn_active_text_color' => [
					'type'        => 'colorpicker',
					'holderClass' => 'pfp-isotope-item pfp-hidden',
					'label'       => esc_html__( 'Selected text color', 'tlp-portfolio' ),
				],

				// Numbered pagination - free paginates grid layouts only, which is when settings.js shows
				// the pfp-pagination-item fields.
				'pfp_page_btn_heading'          => [
					'type'        => 'heading',
					'holderClass' => 'pfp-pagination-item pfp-hidden',
					'label'       => esc_html__( 'Pagination numbers', 'tlp-portfolio' ),
					'description' => esc_html__( 'The page number buttons. Leave a colour empty to use the All buttons colour.', 'tlp-portfolio' ),
				],
				'pfp_page_btn_bg_color'         => [
					'type'        => 'colorpicker',
					'holderClass' => 'pfp-pagination-item pfp-hidden',
					'label'       => esc_html__( 'Background color', 'tlp-portfolio' ),
				],
				'pfp_page_btn_text_color'       => [
					'type'        => 'colorpicker',
					'holderClass' => 'pfp-pagination-item pfp-hidden',
					'label'       => esc_html__( 'Text color', 'tlp-portfolio' ),
				],
				'pfp_page_btn_hover_bg_color'   => [
					'type'        => 'colorpicker',
					'holderClass' => 'pfp-pagination-item pfp-hidden',
					'label'       => esc_html__( 'Hover background color', 'tlp-portfolio' ),
				],
				'pfp_page_btn_hover_text_color' => [
					'type'        => 'colorpicker',
					'holderClass' => 'pfp-pagination-item pfp-hidden',
					'label'       => esc_html__( 'Hover text color', 'tlp-portfolio' ),
				],
				'pfp_page_btn_active_bg_color'  => [
					'type'        => 'colorpicker',
					'holderClass' => 'pfp-pagination-item pfp-hidden',
					'label'       => esc_html__( 'Current page background color', 'tlp-portfolio' ),
				],
				'pfp_page_btn_active_text_color' => [
					'type'        => 'colorpicker',
					'holderClass' => 'pfp-pagination-item pfp-hidden',
					'label'       => esc_html__( 'Current page text color', 'tlp-portfolio' ),
				],

				// Carousel arrows and dots - carousel layouts only.
				'pfp_nav_btn_heading'           => [
					'type'        => 'heading',
					'holderClass' => 'pfp-carousel-item pfp-hidden',
					'label'       => esc_html__( 'Carousel arrows & dots', 'tlp-portfolio' ),
					'description' => esc_html__( 'Leave a colour empty to use the All buttons colour.', 'tlp-portfolio' ),
				],
				'pfp_nav_btn_bg_color'          => [
					'type'        => 'colorpicker',
					'holderClass' => 'pfp-carousel-item pfp-hidden',
					'label'       => esc_html__( 'Arrow background color', 'tlp-portfolio' ),
				],
				'pfp_nav_btn_text_color'        => [
					'type'        => 'colorpicker',
					'holderClass' => 'pfp-carousel-item pfp-hidden',
					'label'       => esc_html__( 'Arrow icon color', 'tlp-portfolio' ),
				],
				'pfp_nav_btn_hover_bg_color'    => [
					'type'        => 'colorpicker',
					'holderClass' => 'pfp-carousel-item pfp-hidden',
					'label'       => esc_html__( 'Arrow hover background color', 'tlp-portfolio' ),
				],
				'pfp_nav_btn_hover_text_color'  => [
					'type'        => 'colorpicker',
					'holderClass' => 'pfp-carousel-item pfp-hidden',
					'label'       => esc_html__( 'Arrow hover icon color', 'tlp-portfolio' ),
				],
				'pfp_nav_dot_color'             => [
					'type'        => 'colorpicker',
					'holderClass' => 'pfp-carousel-item pfp-hidden',
					'label'       => esc_html__( 'Dot color', 'tlp-portfolio' ),
				],
				'pfp_nav_dot_active_color'      => [
					'type'        => 'colorpicker',
					'holderClass' => 'pfp-carousel-item pfp-hidden',
					'label'       => esc_html__( 'Active dot color', 'tlp-portfolio' ),
				],
			];
		}

		public function imageCropType() {
			return [
				'soft' => esc_html__( 'Soft Crop', 'tlp-portfolio' ),
				'hard' => esc_html__( 'Hard Crop', 'tlp-portfolio' ),
			];
		}

		public function scTaxonomyRelation() {
			return [
				'OR'  => 'OR Relation',
				'AND' => 'AND Relation',
			];
		}

		public function socialLink() {
			return [
				'facebook' => 'Facebook',
				'twitter'  => 'Twitter',
				'linkedin' => 'LinkedIn',
			];
		}

		public function scColumns() {
			return [
				1 => '1 Column',
				2 => '2 Column',
				3 => '3 Column',
				4 => '4 Column',
				5 => '5 Column',
				6 => '6 Column',
			];
		}


		public function scLayouts() {
			global $TLPportfolio;

			$layout_root_url = 'https://www.radiustheme.com/demo/plugins/portfolio/';

			return [
				'layout1'   => [
					'title'   => 'Layout 1',
					'layout'  => 'grid',
					// 'demoUrl' => esc_url( $layout_root_url . 'portfolio-layout-1/' ),
					'img'     => $TLPportfolio->assetsUrl . 'images/layout/layout1.png',
				],
				'layout2'   => [
					'title'   => 'Layout 2',
					'layout'  => 'grid',
					// 'demoUrl' => esc_url( $layout_root_url . 'portfolio-layout-2/' ),
					'img'     => $TLPportfolio->assetsUrl . 'images/layout/layout2.png',
				],
				'layout3'   => [
					'title'   => 'Layout 3',
					'layout'  => 'grid',
					// 'demoUrl' => esc_url( $layout_root_url . 'portfolio-layout-3/' ),
					'img'     => $TLPportfolio->assetsUrl . 'images/layout/layout3.png',
				],

				'isotope1'  => [
					'title'   => 'Isotope Layout 1',
					'layout'  => 'isotope',
					// 'demoUrl' => esc_url( $layout_root_url . 'isotope-layout/' ),
					'img'     => $TLPportfolio->assetsUrl . 'images/layout/isotope1.png',
				],
				// isotope2 layout similar like  isotope3 Pro.
				'isotope2'  => [
					'title'   => 'Isotope Layout 2',
					'layout'  => 'isotope',
					// 'demoUrl' => esc_url( $layout_root_url . 'isotope-layout-2/' ),
					'img'     => $TLPportfolio->assetsUrl . 'images/layout/isotope2.png',
				],
				'isotope3'  => [
					'title'   => 'Isotope Layout 3',
					'layout'  => 'isotope',
					// 'demoUrl' => esc_url( $layout_root_url . 'isotope-layout-3/' ),
					'img'     => $TLPportfolio->assetsUrl . 'images/layout/isotope3.png',
				],
				'carousel1' => [
					'title'   => 'Carousel Layout 1',
					'layout'  => 'carousel',
					// 'demoUrl' => esc_url( $layout_root_url . 'carousel-layout-1/' ),
					'img'     => $TLPportfolio->assetsUrl . 'images/layout/carousel1.png',
				],

				'carousel2' => [
					'title'   => 'Carousel Layout 2',
					'layout'  => 'carousel',
					// 'demoUrl' => esc_url( $layout_root_url . 'carousel-layout-2/' ),
					'img'     => $TLPportfolio->assetsUrl . 'images/layout/carousel2.png',
				],

				'carousel3' => [
					'title'   => 'Carousel Layout 3',
					'layout'  => 'carousel',
					// 'demoUrl' => esc_url( $layout_root_url . 'carousel-layout-3/' ),
					'img'     => $TLPportfolio->assetsUrl . 'images/layout/carousel3.png',
				],
			];
		}

		public function owlProperty() {
			return [
				'loop'               => esc_html__( 'Loop', 'tlp-portfolio' ),
				'autoplay'           => esc_html__( 'Auto Play', 'tlp-portfolio' ),
				'autoplayHoverPause' => esc_html__( 'Pause on mouse hover', 'tlp-portfolio' ),
				'nav'                => esc_html__( 'Nav Button', 'tlp-portfolio' ),
				'dots'               => esc_html__( 'Dot Navigation', 'tlp-portfolio' ),
				'auto_height'        => esc_html__( 'Auto Height', 'tlp-portfolio' ),
				'lazy_load'          => esc_html__( 'Lazy Load', 'tlp-portfolio' ),
				'rtl'                => esc_html__( 'Right to left (RTL)', 'tlp-portfolio' ),
			];
		}

		public function oldScLayouts() {
			return [
				1           => 'Layout 1',
				2           => 'Layout 2',
				3           => 'Layout 3',
				// 'layout4'  => 'Layout 4', // Next layout format
				'isotope'   => 'Isotope Layout 1',
				'isotope2'  => 'Isotope Layout 2',
				'isotope3'  => 'Isotope Layout 3',
				'carousel1' => 'Carousel Slider 1',
				'carousel2' => 'Carousel Slider 2',
				'carousel3' => 'Carousel Slider 3',
			];
		}

		public function scOrderBy() {
			return [
				'menu_order' => 'Menu Order',
				'title'      => 'Name',
				'ID'         => 'ID',
				'date'       => 'Date',
			];
		}

		public function scOrder() {
			return [
				'ASC'  => 'Ascending',
				'DESC' => 'Descending',
			];
		}

		public function owl_property() {
			return [
				'auto_play'   => esc_html__( 'Auto Play', 'tlp-portfolio' ),
				'navigation'  => esc_html__( 'Navigation', 'tlp-portfolio' ),
				'pagination'  => esc_html__( 'Dot Navigation', 'tlp-portfolio' ),
				'stop_hover'  => esc_html__( 'Stop Hover', 'tlp-portfolio' ),
				'responsive'  => esc_html__( 'Responsive', 'tlp-portfolio' ),
				'auto_height' => esc_html__( 'Auto Height', 'tlp-portfolio' ),
				'lazy_load'   => esc_html__( 'Lazy Load', 'tlp-portfolio' ),
			];
		}

		public function scFontSize() {
			$num = [];
			for ( $i = 10; $i <= 50; $i ++ ) {
				$num[ $i ] = $i . 'px';
			}

			return $num;
		}

		public function scAlignment() {
			return [
				'left'    => 'Left',
				'right'   => 'Right',
				'center'  => 'Center',
				'justify' => 'Justify',
			];
		}

		public function scTextWeight() {
			return [
				'normal'  => 'Normal',
				'bold'    => 'Bold',
				'bolder'  => 'Bolder',
				'lighter' => 'Lighter',
				'inherit' => 'Inherit',
				'initial' => 'Initial',
				'unset'   => 'Unset',
				100       => '100',
				200       => '200',
				300       => '300',
				400       => '400',
				500       => '500',
				600       => '600',
				700       => '700',
				800       => '800',
				900       => '900',
			];
		}

		private function isotope_filter_taxonomy() {
			return apply_filters( 'tlp_portfolio_isotope_filter_taxonomy', array_flip( TLPPortfolio()->taxonomies ) );
		}
	}
endif;
