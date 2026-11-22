<?php
// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

if ( ! class_exists( 'TLPPortfolio' ) ) :
	class TLPPortfolio {
		public $options;
		public $post_type_slug;
		private $sc_post_type;
		public $post_type;
		public $taxonomies;
		public $assetsUrl;
		public $incPath;
		public $functionsPath;
		public $classesPath;
		public $modelsPath;
		public $widgetsPath;
		public $viewsPath;
		public $templatesPath;
		public $defaultSettings;
		public $objects;

		protected static $_instance;

		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		public function __construct() {
			$this->options = [
				'settings'            => 'tpl_portfolio_settings',
				'version'             => TLP_PORTFOLIO_VERSION,
				'tlp-portfolio-thumb' => 'tlp-portfolio-thumb',
				'installed_version'   => 'tlp_portfolio_installed_version',
			];

			$this->post_type      = 'portfolio';
			$this->sc_post_type   = 'portfolio-sc';
			$settings             = get_option( $this->options['settings'] );
			$this->post_type_slug = isset( $settings['slug'] ) ? ( $settings['slug'] ? sanitize_title_with_dashes( $settings['slug'] ) : 'portfolio' ) : 'portfolio';
			$this->taxonomies     = [
				'category' => $this->post_type . '-category',
				'tag'      => $this->post_type . '-tag',
			];
			$this->incPath        = dirname( __FILE__ );
			$this->functionsPath  = $this->incPath . '/functions/';
			$this->classesPath    = $this->incPath . '/classes/';
			$this->modelsPath     = $this->incPath . '/models/';
			$this->widgetsPath    = $this->incPath . '/widgets/';
			$this->viewsPath      = $this->incPath . '/views/';
			$this->assetsUrl      = TLP_PORTFOLIO_PLUGIN_URL . '/assets/';
			$this->templatesPath  = $this->incPath . '/templates/';

			$this->TLPLoadModel( $this->modelsPath );
			$this->TPLloadClass( $this->classesPath );
			$this->defaultSettings = [
				'primary_color'    => '#0367bf',
				'feature_img_size' => 'medium',
				'slug'             => 'portfolio',
				'link_detail_page' => 'yes',
				'custom_css'       => null,
			];

			register_activation_hook( TLP_PORTFOLIO_PLUGIN_ACTIVE_FILE_NAME, [ $this, 'activate' ] );
			register_deactivation_hook( TLP_PORTFOLIO_PLUGIN_ACTIVE_FILE_NAME, [ $this, 'deactivate' ] );
			add_action( 'admin_init', [ $this, 'maybeReGenerateCss' ] );
		}

		/**
		 * Rebuild the shared shortcode stylesheet when the pro plugin wrote it last.
		 *
		 * Both tiers write uploads/tlp-portfolio/portfolio-sc.css, each with its own container
		 * selectors (#tlp-portfolio-container-N here, #pfp-container-N in pro), and each records
		 * itself in the shared `tlp_portfolio_sc_css_owner` option when it writes. Deactivating
		 * pro runs no activation hook here when this plugin was already active, so the file kept
		 * pro's rules, which match nothing here, and every shortcode looked unstyled until it was
		 * saved again.
		 */
		public function maybeReGenerateCss() {
			if ( 'free' === get_option( 'tlp_portfolio_sc_css_owner' ) ) {
				return;
			}

			// Every block in the file belongs to the other tier, so start from an empty file
			// rather than leaving its unpublished shortcodes' blocks behind.
			$upload_dir = wp_upload_dir();
			wp_delete_file( $upload_dir['basedir'] . '/tlp-portfolio/portfolio-sc.css' );

			$this->reGenerateCss();
			update_option( 'tlp_portfolio_sc_css_owner', 'free' );
		}

		public function activate() {
			flush_rewrite_rules();

			$this->insertDefaultData();
			$this->reGenerateCss();

			add_option( 'rtport_activation_redirect', true );
		}

		public function reGenerateCss() {
			$scPostIds = get_posts(
				[
					'post_type'      => TLPPortfolio()->sc_post_type,
					'posts_per_page' => -1,
					'post_status'    => 'publish',
					'fields'         => 'ids',
				]
			);

			if ( is_array( $scPostIds ) && ! empty( $scPostIds ) ) {
				foreach ( $scPostIds as $scPostId ) {
					TLPPortfolio()->generatorShortCodeCss( $scPostId );
				}
			}
            wp_reset_postdata();
		}

		public function pro_version_link() {
			$proUrl = esc_url( 'https://www.radiustheme.com/downloads/tlp-portfolio-pro-for-wordpress/' );
			return $proUrl;
		}

		public function placeholder_img_src() {
			return $this->assetsUrl . 'images/demo.jpg';
		}

		public function deactivate() {
			flush_rewrite_rules();
		}

		public function TPLloadClass( $dir ) {
			if ( ! file_exists( $dir ) ) {
				return;
			}

			$classes = [];

			foreach ( scandir( $dir ) as $item ) {
				if ( preg_match( '/.php$/i', $item ) ) {
					require_once $dir . $item;
					$className = str_replace( '.php', '', $item );
					if ( class_exists( $className ) ) {
						$classes[] = new $className();
					}
				}
			}

			if ( $classes ) {
				foreach ( $classes as $class ) {
					$this->objects[] = $class;
				}
			}
		}

		public function TLPLoadModel( $dir ) {
			if ( ! file_exists( $dir ) ) {
				return;
			}
			foreach ( scandir( $dir ) as $item ) {
				if ( preg_match( '/.php$/i', $item ) ) {
					require_once $dir . $item;
				}
			}
		}

		public function loadWidget( $dir ) {
			if ( ! file_exists( $dir ) ) {
				return;
			}
			foreach ( scandir( $dir ) as $item ) {
				if ( preg_match( '/.php$/i', $item ) ) {
					require_once $dir . $item;
					$class = str_replace( '.php', '', $item );

					if ( method_exists( $class, 'register_widget' ) ) {
						$caller = new $class();
						$caller->register_widget();
					} else {
						register_widget( $class );
					}
				}
			}
		}

		/**
		 * @param       $viewName
		 * @param array    $args
		 * @param bool     $return
		 *
		 * @return string|void
		 */
		public function render_view( $viewName, $args = [], $return = false ) {
			$path     = str_replace( '.', '/', $viewName );
			$viewPath = $this->viewsPath . $path . '.php';
			if ( ! file_exists( $viewPath ) ) {
				return;
			}
			if ( $args ) {
				extract( $args );
			}
			if ( $return ) {
				ob_start();
				include $viewPath;

				return ob_get_clean();
			}
			include $viewPath;
		}

		/**
		 * @param       $viewName
		 * @param array    $args
		 * @param bool     $return
		 *
		 * @return string|void
		 */
		public function render( $viewName, $args = [], $return = false ) {
			$path = str_replace( '.', '/', $viewName );

			if ( $args ) {
				extract( $args );
			}
			$template = [
				"tlp-portfolio/{$path}.php",
			];

			if ( ! $template_file = locate_template( $template ) ) {
				$template_file = $this->templatesPath . $viewName . '.php';
			}

			if ( ! file_exists( $template_file ) ) {
				return;
			}

			if ( $return ) {
				ob_start();
				include $template_file;

				return ob_get_clean();
			} else {
				include $template_file;
			}
		}

		/**
		 * Dynamicaly call any  method from models class
		 * by pluginFramework instance
		 */
		public function __call( $name, $args ) {
			if ( ! is_array( $this->objects ) ) {
				return;
			}

			foreach ( $this->objects as $object ) {
				if ( method_exists( $object, $name ) ) {
					$count = count( $args );
					if ( $count == 0 ) {
						return $object->$name();
					} elseif ( $count == 1 ) {
						return $object->$name( $args[0] );
					} elseif ( $count == 2 ) {
						return $object->$name( $args[0], $args[1] );
					} elseif ( $count == 3 ) {
						return $object->$name( $args[0], $args[1], $args[2] );
					} elseif ( $count == 4 ) {
						return $object->$name( $args[0], $args[1], $args[2], $args[3] );
					} elseif ( $count == 5 ) {
						return $object->$name( $args[0], $args[1], $args[2], $args[3], $args[4] );
					} elseif ( $count == 6 ) {
						return $object->$name( $args[0], $args[1], $args[2], $args[3], $args[4], $args[5] );
					}
				}
			}
		}

		private function insertDefaultData() {
			update_option( TLPPortfolio()->options['installed_version'], TLPPortfolio()->options['version'] );

			if ( ! get_option( TLPPortfolio()->options['settings'] ) ) {
				update_option( TLPPortfolio()->options['settings'], TLPPortfolio()->defaultSettings );
			}
		}

		/**
		 * @return string
		 */
		public function getScPostType() {
			return $this->sc_post_type;
		}
	}

	/**
	 * @return TLPPortfolio
	 */
	function TLPPortfolio() {
			global $TLPportfolio;
			$TLPportfolio = TLPPortfolio::instance();
			return $TLPportfolio;
	}

	TLPPortfolio();

endif;
