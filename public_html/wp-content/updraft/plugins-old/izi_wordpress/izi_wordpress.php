<?php

/**
 * @package iZi_Facturacion
 * @version 1.5.0
 */
/*
Plugin Name: iZi Facturación
Plugin URI: 
Description: iZi Facturación para WordPress.
Author: iZi Facturación
Version: 1.5.0
Author URI: https://izifacturacion.com
*/

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) return;

define('iZi_PAYMENT_GATEWAY_PLUGIN_NAME', "izi-payment-gatway.php");


function call_invoice_api1($order_id) {

	$comprador_fieldname = get_post_meta($order_id, 'comprador_fieldname', true);
	$razon_social_fieldname = get_post_meta($order_id, 'razon_social_fieldname', true);

	$order = new WC_Order($order_id);
	$order_data = $order->get_data();
	$meta = $order_data["meta_data"];

	$comprador = get_post_meta($order_id, $comprador_fieldname, true);
	$razonSocial = get_post_meta($order_id, $razon_social_fieldname, true);

	if(!$comprador || $comprador == ""){
		foreach ($meta as $customField) {

			if ($customField->key == $comprador_fieldname) {
				$comprador = $customField->value;
			}
			if ($customField->key == $razon_social_fieldname) {
				$razonSocial = $customField->value;
			}
		}
	}

	if (!$comprador || $comprador == "") {
		$comprador = $_POST[$comprador_fieldname];
		$razonSocial = $_POST[$razon_social_fieldname];
	}


	
	$I_email = get_post_meta($order_id, '_billing_email', true);
	$in_generatetype = get_option('generatetype');
	$seller_legal_number = get_option('seller_legal_number');
	$sucursal = get_option('sucursal');
	$actividadEconomica = get_option('actividadEconomica');
	$user_id = get_option('user_idizi'); // this should be configurable in the plugin
	$public_key = get_option('public_keyizi'); // this should be configurable in the plugin
	$isinvoicegenerated = get_post_meta($order_id, 'isinvoicegenerated', true);

	if (is_null($comprador) || strlen($comprador) == 0) $comprador = "0";
	if (is_null($razonSocial) || strlen($razonSocial) == 0) $razonSocial = "SN";


	if ($isinvoicegenerated != '1') {
		if ($in_generatetype == 1) {

			//echo $p; exit;
			//echo $order_id;exit;
			$order = wc_get_order($order_id);

			//print_r($order->get_items());exit;
			$productArray = array();
			foreach ($order->get_items() as $item) {
				// echo $item['name'];exit;
				$product = wc_get_product($item['product_id']);
				$price = (float)$product->get_price();
				// echo $price;exit;
				//echo $product->get_price();exit;
				$productArray[] = array('articulo' => $item['name'], 'cantidad' => (int)$item['qty'], 'precioUnitario' => $price);
			}


			//call to API

			$factura_obj = array(
				"correoElectronicoComprador" => $I_email,
				"emisor" => $seller_legal_number,
				"sucursal" => (int)$sucursal,
				"actividadEconomica" => (int)$actividadEconomica,
				"razonSocial" => $razonSocial,
				"comprador" => $comprador,
				"listaItems" => $productArray,
				"descripcion" => $all_field_names
			);


			$json_factura = json_encode($factura_obj, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

			//echo "json: " . $json_factura . PHP_EOL . PHP_EOL;exit;

			// echo $secret_key;

			$encryption_method = "aes-256-cbc";



			// use this line to generate a 32 characters random string

			$secret_key = bin2hex(openssl_random_pseudo_bytes(16));

			//echo "secretKey " . $secret_key;

			//echo "\n";



			// use this line as constant secret key

			//$secret_key = "secret0key0to0encrypt0under0aes0";



			$iv_size = openssl_cipher_iv_length($encryption_method);

			$iv = openssl_random_pseudo_bytes($iv_size);

			//echo "iv " . bin2hex($iv);

			//echo "\n";

			//echo "\n";



			$encrypted_json_factura = @openssl_encrypt($json_factura, $encryption_method, $secret_key, 0, $iv);

			$encrypted_json_factura = bin2hex($iv) . $encrypted_json_factura;



			openssl_public_encrypt($secret_key, $encrypted_secret_key, $public_key, OPENSSL_PKCS1_PADDING);

			$encrypted_secret_key = base64_encode($encrypted_secret_key);



			$auth_str = $user_id . ":" . $encrypted_secret_key . ":" . $encrypted_json_factura;

			//echo "Auth String: " . $auth_str . PHP_EOL . PHP_EOL;



			/////////////////////////////////////////////////

			// ENVÍO

			/////////////////////////////////////////////////



			$curl = curl_init();



			curl_setopt_array($curl, array(
				CURLOPT_URL => "https://api.app.izifacturacion.com/v1/facturas",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => $json_factura,
				/* Cliente: bda1b8e7-9b49-4c66-8188-1e5d71d87c6a */
				CURLOPT_HTTPHEADER => array(
					"authorization: " . $auth_str,
					// "cache-control: no-cache",
					"content-type: application/json"
				),

			));



			$response = curl_exec($curl);

			$err = curl_error($curl);



			curl_close($curl);



			if ($err) {

				//echo "cURL Error #:" . $err;

			} else {

				//echo $response;

			}

			//echo PHP_EOL . PHP_EOL . PHP_EOL;

			//$decode= var_dump(json_decode($response, true));

			//echo PHP_EOL . PHP_EOL . PHP_EOL . "----------------------------------" . PHP_EOL;

			//exit;

			//$obj = json_decode($response);



			//print_r($products_order);exit;

			//end of call to API

			update_post_meta($order_id, 'isinvoicegenerated', 1);
		}
	}
}


function call_invoice_api_payment_complete($order_id) {

	$comprador_fieldname = get_post_meta($order_id, 'comprador_fieldname', true);
	$razon_social_fieldname = get_post_meta($order_id, 'razon_social_fieldname', true);

	$order = new WC_Order($order_id);
	$order_data = $order->get_data();
	$meta = $order_data["meta_data"];

	$comprador = get_post_meta($order_id, $comprador_fieldname, true);
	$razonSocial = get_post_meta($order_id, $razon_social_fieldname, true);

	if (!$comprador || $comprador == "") {
		foreach ($meta as $customField) {

			if ($customField->key == $comprador_fieldname) {
				$comprador = $customField->value;
			}
			if ($customField->key == $razon_social_fieldname) {
				$razonSocial = $customField->value;
			}
		}
	}

	if (!$comprador || $comprador == "") {
		$comprador = $_POST[$comprador_fieldname];
		$razonSocial = $_POST[$razon_social_fieldname];
	}
	
	$I_email = get_post_meta($order_id, '_billing_email', true);
	//$in_generatetype=get_option('generatetype');
	$seller_legal_number = get_option('seller_legal_number');
	$sucursal = get_option('sucursal');
	$actividadEconomica = get_option('actividadEconomica');
	$user_id = get_option('user_idizi'); // this should be configurable in the plugin
	$public_key = get_option('public_keyizi'); // this should be configurable in the plugin
	$isinvoicegenerated = get_post_meta($order_id, 'isinvoicegenerated', true);

	if (is_null($comprador) || strlen($comprador) == 0) $comprador = "0";
	if (is_null($razonSocial) || strlen($razonSocial) == 0) $razonSocial = "SN";

	if ($isinvoicegenerated != '1') {
		/* if($in_generatetype==0){
		if($in_generatetype!=1){ */
		//echo $p; exit;
		//echo $order_id;exit;
		$order = wc_get_order($order_id);

		//print_r($order->get_items());exit;
		$productArray = array();
		foreach ($order->get_items() as $item) {
			// echo $item['name'];exit;
			$product = wc_get_product($item['product_id']);
			$price = (float)$product->get_price();
			// echo $price;exit;
			//echo $product->get_price();exit;
			$productArray[] = array('articulo' => $item['name'], 'cantidad' => (int)$item['qty'], 'precioUnitario' => $price);
		}


		//call to API

		$factura_obj = array(
			"correoElectronicoComprador" => $I_email,
			"emisor" => $seller_legal_number,
			"sucursal" => (int)$sucursal,
			"actividadEconomica" => (int)$actividadEconomica,
			"razonSocial" => $razonSocial,
			"comprador" => $comprador,
			"listaItems" => $productArray,
			"descripcion" => $all_field_names
		);





		$json_factura = json_encode($factura_obj, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

		//echo "json: " . $json_factura . PHP_EOL . PHP_EOL;exit;

		// echo $secret_key;

		$encryption_method = "aes-256-cbc";



		// use this line to generate a 32 characters random string

		$secret_key = bin2hex(openssl_random_pseudo_bytes(16));

		//echo "secretKey " . $secret_key;

		//echo "\n";



		// use this line as constant secret key

		//$secret_key = "secret0key0to0encrypt0under0aes0";



		$iv_size = openssl_cipher_iv_length($encryption_method);

		$iv = openssl_random_pseudo_bytes($iv_size);

		//echo "iv " . bin2hex($iv);

		//echo "\n";

		//echo "\n";



		$encrypted_json_factura = @openssl_encrypt($json_factura, $encryption_method, $secret_key, 0, $iv);

		$encrypted_json_factura = bin2hex($iv) . $encrypted_json_factura;



		openssl_public_encrypt($secret_key, $encrypted_secret_key, $public_key, OPENSSL_PKCS1_PADDING);

		$encrypted_secret_key = base64_encode($encrypted_secret_key);



		$auth_str = $user_id . ":" . $encrypted_secret_key . ":" . $encrypted_json_factura;

		//echo "Auth String: " . $auth_str . PHP_EOL . PHP_EOL;



		/////////////////////////////////////////////////

		// ENVÍO

		/////////////////////////////////////////////////



		$curl = curl_init();



		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://api.app.izifacturacion.com/v1/facturas",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => $json_factura,
			/* Cliente: bda1b8e7-9b49-4c66-8188-1e5d71d87c6a */
			CURLOPT_HTTPHEADER => array(
				"authorization: " . $auth_str,
				// "cache-control: no-cache",
				"content-type: application/json"
			),

		));



		$response = curl_exec($curl);

		$err = curl_error($curl);



		curl_close($curl);



		if ($err) {

			//echo "cURL Error #:" . $err;

		} else {

			//echo $response;

		}

		//echo PHP_EOL . PHP_EOL . PHP_EOL;

		//$decode= var_dump(json_decode($response, true));

		//echo PHP_EOL . PHP_EOL . PHP_EOL . "----------------------------------" . PHP_EOL;

		//exit;

		//$obj = json_decode($response);



		//print_r($products_order);exit;

		//end of call to API

		update_post_meta($order_id, 'isinvoicegenerated', 1);
	}
}
/* }
} */


function call_invoice_api_status_changed($order_id, $old_status, $new_status) {

	//echo $new_status;exit;
	// if ($new_status == "processing" or $new_status == "completed") {
	if ($new_status == "completed") {
		//echo $order_id;exit;

		$comprador_fieldname = get_post_meta($order_id, 'comprador_fieldname', true);
		$razon_social_fieldname = get_post_meta($order_id, 'razon_social_fieldname', true);

		$order = new WC_Order($order_id);
		$order_data = $order->get_data();
		$meta = $order_data["meta_data"];

		$comprador = get_post_meta($order_id, $comprador_fieldname, true);
		$razonSocial = get_post_meta($order_id, $razon_social_fieldname, true);

		if (!$comprador || $comprador == "") {
			foreach ($meta as $customField) {

				if ($customField->key == $comprador_fieldname
				) {
					$comprador = $customField->value;
				}
				if ($customField->key == $razon_social_fieldname) {
					$razonSocial = $customField->value;
				}
			}
		}

		if (!$comprador || $comprador == "") {
			$comprador = $_POST[$comprador_fieldname];
			$razonSocial = $_POST[$razon_social_fieldname];
		}
		
		$I_email = get_post_meta($order_id, '_billing_email', true);
		//$in_generatetype=get_option('generatetype');
		$seller_legal_number = get_option('seller_legal_number');
		$sucursal = get_option('sucursal');
		$actividadEconomica = get_option('actividadEconomica');
		$user_id = get_option('user_idizi'); // this should be configurable in the plugin
		$public_key = get_option('public_keyizi'); // this should be configurable in the plugin
		$isinvoicegenerated = get_post_meta($order_id, 'isinvoicegenerated', true);

		if (is_null($comprador) || strlen($comprador) == 0) $comprador = "0";
		if (is_null($razonSocial) || strlen($razonSocial) == 0) $razonSocial = "SN";

		if ($isinvoicegenerated != '1') {
			/* if($in_generatetype==0) {
				if($in_generatetype!=1) { */
			//echo $p; exit;
			//echo $order_id;exit;
			$order = wc_get_order($order_id);

			//print_r($order->get_items());exit;
			$productArray = array();
			foreach ($order->get_items() as $item) {
				// echo $item['name'];exit;
				$product = wc_get_product($item['product_id']);
				$price = (float)$product->get_price();
				// echo $price;exit;
				//echo $product->get_price();exit;
				$productArray[] = array('articulo' => $item['name'], 'cantidad' => (int)$item['qty'], 'precioUnitario' => $price);
			}


			//call to API

			$factura_obj = array(
				"correoElectronicoComprador" => $I_email,
				"emisor" => $seller_legal_number,
				"sucursal" => (int)$sucursal,
				"actividadEconomica" => (int)$actividadEconomica,
				"razonSocial" => $razonSocial,
				"comprador" => $comprador,
				"listaItems" => $productArray,
				"descripcion" => $all_field_names
			);





			$json_factura = json_encode($factura_obj, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

			//echo "json: " . $json_factura . PHP_EOL . PHP_EOL;exit;

			// echo $secret_key;

			$encryption_method = "aes-256-cbc";



			// use this line to generate a 32 characters random string

			$secret_key = bin2hex(openssl_random_pseudo_bytes(16));

			//echo "secretKey " . $secret_key;

			//echo "\n";



			// use this line as constant secret key

			//$secret_key = "secret0key0to0encrypt0under0aes0";



			$iv_size = openssl_cipher_iv_length($encryption_method);

			$iv = openssl_random_pseudo_bytes($iv_size);

			//echo "iv " . bin2hex($iv);

			//echo "\n";

			//echo "\n";



			$encrypted_json_factura = @openssl_encrypt($json_factura, $encryption_method, $secret_key, 0, $iv);

			$encrypted_json_factura = bin2hex($iv) . $encrypted_json_factura;



			openssl_public_encrypt($secret_key, $encrypted_secret_key, $public_key, OPENSSL_PKCS1_PADDING);

			$encrypted_secret_key = base64_encode($encrypted_secret_key);



			$auth_str = $user_id . ":" . $encrypted_secret_key . ":" . $encrypted_json_factura;

			//echo "Auth String: " . $auth_str . PHP_EOL . PHP_EOL;



			/////////////////////////////////////////////////

			// ENVÍO

			/////////////////////////////////////////////////



			$curl = curl_init();



			curl_setopt_array($curl, array(
				CURLOPT_URL => "https://api.app.izifacturacion.com/v1/facturas",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "POST",
				CURLOPT_POSTFIELDS => $json_factura,
				/* Cliente: bda1b8e7-9b49-4c66-8188-1e5d71d87c6a */
				CURLOPT_HTTPHEADER => array(
					"authorization: " . $auth_str,
					// "cache-control: no-cache",
					"content-type: application/json"
				),
			));



			$response = curl_exec($curl);

			$err = curl_error($curl);



			curl_close($curl);



			if ($err) {

				//echo "cURL Error #:" . $err;

			} else {

				//echo $response;

			}

			//echo PHP_EOL . PHP_EOL . PHP_EOL;

			//$decode= var_dump(json_decode($response, true));

			//echo PHP_EOL . PHP_EOL . PHP_EOL . "----------------------------------" . PHP_EOL;

			//exit;

			//$obj = json_decode($response);



			//print_r($products_order);exit;

			//end of call to API

			update_post_meta($order_id, 'isinvoicegenerated', 1);
		}
	}
}

function astro_plugin_table_install() {
	global $wpdb;
	global $charset_collate;
	$table_name = $wpdb->prefix . 'izi_invoice';
	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
      `id` INT NOT NULL AUTO_INCREMENT,
      `orderid` INT NOT NULL,
       PRIMARY KEY (`id`)) ENGINE = InnoDB;";

	//echo $sql;exit;
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
}
register_activation_hook(__FILE__, 'astro_plugin_table_install');

function your_custom_function_call1($order_id, $old_status, $new_status) {
	//echo $order_id;
	echo get_option('myplugin_option_name');
	exit;
	if ($new_status == "completed") {
	}
}




function izi_register_settings() {
	add_option('user_idizi', '');
	register_setting('myplugin_options_group', 'user_idizi', 'myplugin_callback');

	add_option('public_keyizi', '');
	register_setting('myplugin_options_group', 'public_keyizi', 'myplugin_callback');

	add_option('seller_legal_number', '');
	register_setting('myplugin_options_group', 'seller_legal_number', 'myplugin_callback');

	add_option('sucursal', '');
	register_setting('myplugin_options_group', 'sucursal', 'myplugin_callback');

	add_option('actividadEconomica', '');
	register_setting('myplugin_options_group', 'actividadEconomica', 'myplugin_callback');

	/* add_option( 'generatetype', '1');
   register_setting( 'myplugin_options_group', 'generatetype', 'myplugin_callback' ); */
}

add_action('admin_init', 'izi_register_settings');


function izi_register_hooks() {
	//add_action( 'woocommerce_order_status_completed', 'call_invoice_api', 10, 1);
	//Generate invoice manually by changing the order status
	add_action('woocommerce_payment_complete', 'call_invoice_api_payment_complete');
	//Automatically generate invoice with each order
	//add_action('woocommerce_thankyou', 'call_invoice_api1', 10, 1);

	if (!is_plugin_active(iZi_PAYMENT_GATEWAY_PLUGIN_NAME)) {
		add_action('woocommerce_order_status_changed', 'call_invoice_api_status_changed', 10, 3);
	}
}

add_action('admin_init', 'izi_register_hooks');


function izi_register_options_page() {
	add_options_page('Configuración iZi', 'Configuración iZi', 'manage_options', 'izi_wordpress', 'myplugin_options_page');
}

add_action('admin_menu', 'izi_register_options_page');


function myplugin_options_page() {
?>
	<style>
		label.user_idizi:before {
			color: red;
			content: "*";
			font-size: 14px;
			line-height: 12px;
			position: relative;
		}
	</style>
	<div>
		<?php screen_icon(); ?>
		<h2>iZi - Configuración de llaves</h2>
		</br>

		<form method="post" action="options.php">
			<?php settings_fields('myplugin_options_group'); ?>

			<table>
				<tr valign="top">
					<th scope="row"><label for="user_idizi" class="user_idizi">User ID</label></th>
					<td><input type="text" id="user_idizi" name="user_idizi" value="<?php echo get_option('user_idizi'); ?>" style="width: 500px;" />
						<p class="help-block" style="font-style: italic;color: #959595;
							display: block;
							margin-bottom: 10px;
							margin-top: 5px;">
							Copia el código desde la aplicación de iZi.
						</p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><label for="public_keyizi" class="user_idizi">Public key</label></th>
					<td><textarea id="public_keyizi" name="public_keyizi" style="width: 500px;height: 184px;" /><?php echo get_option('public_keyizi'); ?></textarea>
						<p class="help-block" style="font-style: italic;color: #959595;
							display: block;
							margin-bottom: 10px;
							margin-top: 5px;">
							Copia la llave desde la aplicación de iZi.

						</p>
					</td>
				</tr>



				<tr valign="top">
					<th scope="row"><label for="seller_legal_number" class="user_idizi"> Seller legal number (NIT)</label></th>
					<td><input type="text" id="seller_legal_number" name="seller_legal_number" value="<?php echo get_option('seller_legal_number'); ?>" style="width: 500px;" />

					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><label for="sucursal" class="sucursal">Sucursal</label></th>
					<td><input type="text" id="sucursal" name="sucursal" value="<?php echo get_option('sucursal'); ?>" style="width: 500px;" />
						<p class="help-block" style="font-style: italic;color: #959595;
							display: block;
							margin-bottom: 10px;
							margin-top: 5px;">
							Si tienes más de una sucursal, ingresa el código con la cual deseas facturar.


						</p>
					</td>
				</tr>



				<tr valign="top">
					<th scope="row"><label for="actividadEconomica" class="actividadEconomica">Actividad Económica</label></th>
					<td><input type="text" id="actividadEconomica" name="actividadEconomica" value="<?php echo get_option('actividadEconomica'); ?>" style="width: 500px;" />
						<p class="help-block" style="font-style: italic;color: #959595;
							display: block;
							margin-bottom: 10px;
							margin-top: 5px;">
							Si tienes más de una actividad económica, configura la actividad con la que deseas facturar.


						</p>
					</td>
				</tr>


				<!--
  <tr valign="top">
  <th scope="row"><label for="actividadEconomica" class="actividadEconomica"></label></th>
  <td><input type="radio" id="generatetype" name="generatetype" value="1"  <?php if (get_option('generatetype') == "1") { ?>checked="checked" <?php } ?>/> Generar factura automáticamente con cada orden
  
  <input type="radio" id="generatetype" name="generatetype" value="0" <?php if (get_option('generatetype') == "0") { ?>checked="checked" <?php } ?> /> Generar factura manualmente cambiando el estado de la orden
 
  </td>
  </tr>-->


			</table>
			<?php submit_button(); ?>
		</form>
	</div>
<?php
}

/*
* WooCommerce does not load session class on backend, so we need to do this manually
*/
if (!class_exists('WC_Session')) {
	include_once(WP_PLUGIN_DIR . '/woocommerce/includes/abstracts/abstract-wc-session.php');
}

add_action('woocommerce_after_checkout_billing_form', 'customise_checkout_field');

function customise_checkout_field($checkout) {

	WC()->session = new WC_Session_Handler;

	WC()->customer = new WC_Customer;

	// var_dump(WC()->checkout->get_checkout_fields());

	$existe_comprador = false;
	$comprador_fieldname = "comprador";
	$existe_razon_social = false;
	$razon_social_fieldname = "razonSocial";

	foreach (WC()->checkout->get_checkout_fields() as $key => $arreglo) {
		foreach ($arreglo as $key_2 => $field) {
			if(strpos($key_2, "comprador") !== false){
				$existe_comprador = true;
				$comprador_fieldname = $key_2;
			}
			if (strpos($key_2, "razonSocial") !== false) {
				$existe_razon_social = true;
				$razon_social_fieldname = $key_2;
			}
		}
	}

	if(!$existe_comprador){
		echo '<div id="customise_checkout_field">';
		woocommerce_form_field('comprador', array(
			'type' => 'text',
			'class' => array(
				'my-field-class form-row-wide'
			),
			'label' => __('NIT'),
			'placeholder' => __(''),
			'required' => false,
		), $checkout->get_value('comprador'));
		echo '</div>';
	}


	if(!$existe_razon_social){
		echo '<div id="customise_checkout_field">';
		woocommerce_form_field('razonSocial', array(
			'type' => 'text',
			'class' => array(
				'my-field-class form-row-wide'
			),
			'label' => __('Razón Social'),
			'placeholder' => __(''),
			'required' => false,
		), $checkout->get_value('razonSocial'));
	}


	echo '<div id="user_link_hidden_checkout_field">
            	<input type="hidden" class="input-hidden" name="comprador_fieldname" id="comprado_fieldname" value="'.$comprador_fieldname.'">
			</div>';

	echo '<div id="user_link_hidden_checkout_field">
            	<input type="hidden" class="input-hidden" name="razon_social_fieldname" id="razon_social_fieldname" value="'.$razon_social_fieldname.'">
    		</div>';

	// foreach (WC()->checkout->get_checkout_fields() as $key => $arreglo) {
	// 	echo "////// $key <br>";
	// 	foreach ($arreglo as $key_2 => $field) {
	// 		echo "***** $key_2 <br>";
	// 	}
	// }



}





add_action('woocommerce_checkout_process', 'customise_checkout_field_process');

function customise_checkout_field_process() {

	// if the field is set, if not then show an error message.
	//   if (!$_POST['comprador']) wc_add_notice(__('Please enter NIT field.') , 'error');
	//   if (!$_POST['razonSocial']) wc_add_notice(__('Please enter Razón Social field.') , 'error');
}


/**
 * Update value of field
 */
add_action('woocommerce_checkout_update_order_meta', 'customise_checkout_field_update_order_meta');
function customise_checkout_field_update_order_meta($order_id) {
	if (!empty($_POST['comprador'])) {
		update_post_meta($order_id, 'comprador', sanitize_text_field($_POST['comprador']));
	}

	if (!empty($_POST['razonSocial'])) {
		update_post_meta($order_id, 'razonSocial', sanitize_text_field($_POST['razonSocial']));
	}

	/** hidden fields with razon social and nit field name */
	if (!empty($_POST['comprado_fieldname'])) {
		update_post_meta($order_id, 'comprado_fieldname', sanitize_text_field($_POST['comprado_fieldname']));
	}

	if (!empty($_POST['razon_social_fieldname'])) {
		update_post_meta($order_id, 'razon_social_fieldname', sanitize_text_field($_POST['razon_social_fieldname']));
	}
}
