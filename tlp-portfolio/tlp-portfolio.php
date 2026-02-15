<?php
/**
 * Plugin Name: Radius Portfolio
 * Plugin URI: http://demo.radiustheme.com/wordpress/plugins/tlp-portfolio/
 * Description: Radius Portfolio is Fully Responsive and Mobile Friendly portfolio for WordPress to display your portfolio work in Grid and Isotope Views.
 * Author: RadiusTheme
 * Author URI: https://radiustheme.com
 * Version: 3.1.0
 * Tested up to: 6.8
 * Requires PHP: 7.0
 * License: GPL-2.0-or-later
 * Tag: portfolio, portfolio plugin,filterable portfolio, portfolio gallery, portfolio display, portfolio slider, responsive portfolio, portfolio showcase, wp portfolio
 * Text Domain: tlp-portfolio
 * Domain Path: /languages
 *
 * @package RT_Portfolio
 */


// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

define( 'TLP_PORTFOLIO_VERSION', '3.1.0' );
define( 'TLP_PORTFOLIO_PLUGIN_PATH', dirname( __FILE__ ) );
define( 'TLP_PORTFOLIO_PLUGIN_ACTIVE_FILE_NAME', plugin_basename( __FILE__ ) );
define( 'TLP_PORTFOLIO_PLUGIN_URL', plugins_url( '', __FILE__ ) );
define( 'TLP_PORTFOLIO_LANGUAGE_PATH', dirname( plugin_basename( __FILE__ ) ) . '/languages' );

/**
 * Check Pro Version.
 */
if ( ! class_exists( 'TLPpPro' ) ) {
	require 'lib/init.php';
}
