<?php
/*
Plugin Name: iZi Payment Gateway
Description: Pasarela de pagos de iZi - Acepta tarjetas de crédito y débito
Version: 0.1.2
Author: iZi Facturación
Author URI: https://izifacturacion.com/
Text Domain: iZi-gateway
WC requires at least: 2.2.0
WC tested up to: 4.2.0
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'iZi_FILE', __FILE__ );
define( 'iZi_DIRNAME', basename( dirname( __FILE__ ) ) );
define( 'iZi_RELPATH', basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );
define( 'iZi_PATH', plugin_dir_path( __FILE__ ) );
define( 'iZi_PLUGINPATH', WP_PLUGIN_URL . '/' . basename( dirname( __FILE__ ) ) );

add_action( 'admin_init', 'iZi_required_plugins' );

function iZi_required_plugins()
{
    if ( is_admin() && current_user_can( 'activate_plugins' ) &&  !is_plugin_active( 'woocommerce/woocommerce.php' ) &&  !is_plugin_active( 'izi_wordpress/izi_wordpress.php' )  )
    {
        add_action( 'admin_notices', 'iZi_required_plugins_notice' );

        deactivate_plugins( plugin_basename( __FILE__ ) ); 

        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
    }
}

function iZi_required_plugins_notice()
{
?>
	<div class="error">
		<p>
			<?php _e("Lo sentimos, el plugin iZi Wordpress es necesario para instalar la pasarela de pagos de iZi. Por favor instálalo y actívalo antes de instalar este plugin.","iZi-gateway") ?>
		</p>
	</div>
<?php
}

add_action( 'plugins_loaded', 'iZi_payment_gateway_init' );
function iZi_payment_gateway_init() {
  include_once( iZi_PATH . '/core/iZi-Class.php' );
  add_filter( 'woocommerce_payment_gateways', 'iZi_payment_gateway_add' );
  function iZi_payment_gateway_add( $methods ) {
    $methods[] = 'iZi_payment_gateway';
    return $methods;
  }
}
include_once( iZi_PATH . '/core/iZi-functions.php' );