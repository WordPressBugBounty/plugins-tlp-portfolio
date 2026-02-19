<?php
/**
 * Plugin Name: Radius Portfolio – Filterable Grid, Gallery & Slider Portfolio Plugin
 * Plugin URI: https://www.radiustheme.com/demo/plugins/portfolio/
 * Description: WordPress Portfolio plugin with filterable gallery, grid, isotope & slider layouts.
 * Author: RadiusTheme
 * Author URI: https://radiustheme.com
 * Version: 3.2.1
 * Tested up to: 6.9
 * Requires PHP: 7.0
 * License: GPL-2.0-or-later
 * Tag: portfolio, portfolio plugin, filterable portfolio, portfolio gallery, portfolio display, portfolio slider, responsive portfolio, portfolio showcase, wp portfolio
 * Text Domain: tlp-portfolio
 * Domain Path: /languages
 *
 * @package RT_Portfolio
 */


// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

define( 'TLP_PORTFOLIO_VERSION', '3.2.1' );
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
