<?php
/*
Plugin Name: Products Quick View for WooCommerce
Description: This plugin adds the ultimate Quick View feature to your Shop page, Product category and Product tags listings. Opens the full pages content - add to cart and even view cart without leaving the page.
Version: 1.9.7
Requires at least: 5.0
Tested up to: 5.6
Author: a3rev Software
Author URI: https://a3rev.com/
Text Domain: woocommerce-products-quick-view
Domain Path: /languages
WC requires at least: 3.0.0
WC tested up to: 4.9.0
License: This software is under commercial license and copyright to A3 Revolution Software Development team

	WooCommerce Quick View. Plugin for the WooCommerce.
	Copyright© 2011 A3 Revolution Software Development team

	A3 Revolution Software Development team
	admin@a3rev.com
	PO Box 1170
	Gympie 4570
	QLD Australia
*/
?>
<?php
define('WC_QUICK_VIEW_ULTIMATE_FILE_PATH', dirname(__FILE__));
define('WC_QUICK_VIEW_ULTIMATE_DIR_NAME', basename(WC_QUICK_VIEW_ULTIMATE_FILE_PATH));
define('WC_QUICK_VIEW_ULTIMATE_FOLDER', dirname(plugin_basename(__FILE__)));
define('WC_QUICK_VIEW_ULTIMATE_URL', untrailingslashit(plugins_url('/', __FILE__)));
define('WC_QUICK_VIEW_ULTIMATE_DIR', WP_PLUGIN_DIR . '/' . WC_QUICK_VIEW_ULTIMATE_FOLDER);
define('WC_QUICK_VIEW_ULTIMATE_NAME', plugin_basename(__FILE__));
define('WC_QUICK_VIEW_ULTIMATE_TEMPLATE_PATH', WC_QUICK_VIEW_ULTIMATE_FILE_PATH . '/templates');
define('WC_QUICK_VIEW_ULTIMATE_IMAGES_URL', WC_QUICK_VIEW_ULTIMATE_URL . '/assets/images');
define('WC_QUICK_VIEW_ULTIMATE_JS_URL', WC_QUICK_VIEW_ULTIMATE_URL . '/assets/js');
define('WC_QUICK_VIEW_ULTIMATE_CSS_URL', WC_QUICK_VIEW_ULTIMATE_URL . '/assets/css');
if (!defined("WC_QUICK_VIEW_ULTIMATE_AUTHOR_URI")) define("WC_QUICK_VIEW_ULTIMATE_AUTHOR_URI", "https://a3rev.com/shop/woocommerce-quick-view-ultimate/");

if (!defined("WC_QUICK_VIEW_ULTIMATE_DOCS_URI")) define("WC_QUICK_VIEW_ULTIMATE_DOCS_URI", "http://docs.a3rev.com/user-guides/plugins-extensions/woocommerce-quick-view-ultimate/");

define( 'WC_QUICK_VIEW_ULTIMATE_KEY', 'wc_quick_view_ultimate' );
define( 'WC_QUICK_VIEW_ULTIMATE_PREFIX', 'wc_qv_' );
define( 'WC_QUICK_VIEW_ULTIMATE_VERSION', '1.9.7' );
define( 'WC_QUICK_VIEW_ULTIMATE_G_FONTS', true );

use \A3Rev\WCQV\FrameWork;

if ( version_compare( PHP_VERSION, '5.6.0', '>=' ) ) {
	require __DIR__ . '/vendor/autoload.php';

	/**
	 * Plugin Framework init
	 */
	$GLOBALS[WC_QUICK_VIEW_ULTIMATE_PREFIX.'admin_interface'] = new FrameWork\Admin_Interface();

	global $wc_qv_admin_page;
	$wc_qv_admin_page = new FrameWork\Pages\Quick_View();

	$GLOBALS[WC_QUICK_VIEW_ULTIMATE_PREFIX.'admin_init'] = new FrameWork\Admin_Init();

	$GLOBALS[WC_QUICK_VIEW_ULTIMATE_PREFIX.'less'] = new FrameWork\Less_Sass();

	// End - Plugin Framework init

	global $wc_quick_view_ultimate_style;
	$wc_quick_view_ultimate_style = new \A3Rev\WCQV\Style();

	global $wc_quick_view_ultimate;
	$wc_quick_view_ultimate = new \A3Rev\WCQV();

	global $wc_quick_view_template_default_gallery_class;
	$wc_quick_view_template_default_gallery_class = new \A3Rev\WCQV\Dynamic_Gallery();

} else {
	return;
}

/**
 * Load Localisation files.
 *
 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
 *
 * Locales found in:
 * 		- WP_LANG_DIR/woocommerce-products-quick-view/woocommerce-products-quick-view-LOCALE.mo
 * 	 	- WP_LANG_DIR/plugins/woocommerce-products-quick-view-LOCALE.mo
 * 	 	- /wp-content/plugins/woocommerce-products-quick-view/languages/woocommerce-products-quick-view-LOCALE.mo (which if not found falls back to)
 */
function quick_view_ultimate_plugin_textdomain() {
	$locale = apply_filters( 'plugin_locale', get_locale(), 'woocommerce-products-quick-view' );

	load_textdomain( 'woocommerce-products-quick-view', WP_LANG_DIR . '/woocommerce-products-quick-view/woocommerce-products-quick-view-' . $locale . '.mo' );
	load_plugin_textdomain( 'woocommerce-products-quick-view', false, WC_QUICK_VIEW_ULTIMATE_FOLDER . '/languages/' );
}

include 'admin/woocommerce-quick-view-ultimate-init.php';

// Compatibilities
include 'includes/compatibilities/carousel-slider-plugin/functions.php';
include 'includes/compatibilities/x-theme/x-theme.php';

/**
 * Call when the plugin is activated
 */
register_activation_hook(__FILE__, 'wc_quick_view_ultimate_install');
