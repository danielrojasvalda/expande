<?php
function iZi_get_url($order_id)
{

$user_id = get_option('user_idizi');
$public_key = get_option('public_keyizi');
$sucursal = get_option('sucursal');
$actividadEconomica = get_option('actividadEconomica');
$seller_legal_number = get_option('seller_legal_number');
$order = wc_get_order( $order_id );
$data = $order->get_data();

$pagadorData = array(
	"nombres" => $data['billing']['first_name'],
	"apellidos" => $data['billing']['last_name'],
	"pais" => $data['billing']['country'],
	"estado" => $data['billing']['state'],
	"correoElectronico" => $data['billing']['email']
);
$descripcion = '';
$moneda = strtoupper(get_woocommerce_currency());
if($moneda != 'USD')
{
	$moneda = 'BOB';
}
$monto = (float) $order->get_total();
$itemsFactura = array();


foreach ( $order->get_items() as $item_id => $item ) {
	$product = $item->get_product();
	$Itemname = $item->get_name();
	$quantity = $item->get_quantity();
	$price = $product->get_price();
	$descripcion = $product->get_description();
	$itemsFactura[] = array("articulo" => $Itemname, "cantidad" => $quantity, "precioUnitario" => $price);
}

$nitFactura = get_post_meta($order_id, 'comprador', true);
$razonSocialFactura = get_post_meta($order_id, 'razonSocial', true);
if(empty($nitFactura))	$nitFactura = 0;
if(empty($razonSocialFactura))	$razonSocialFactura = 'S/N';
$email = $order->get_billing_email();
	 $factura_obj = array(

		"descripcion" => 'Compra en línea',

			"monto" => $monto,

		  "pasarela" => "CYBERSOURCE",

		  "correoElectronico" => $email,

			"notificarPagador" => false,
			
			"nitFactura" => $nitFactura,
			
			"razonSocialFactura" => $razonSocialFactura,

			"sucursal" => $sucursal,

			"actividadEconomica" => $actividadEconomica,

			"order" => $order_id,
			
			"notificacionUrl" => get_site_url().'/?wc-api=iZi_payment_gateway',

			"itemsFactura" => $itemsFactura,

			"pagadorData" => $pagadorData
			);
		

		$json_factura = json_encode($factura_obj, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

		$encryption_method = "aes-256-cbc";

		// use this line to generate a 32 characters random string

		$secret_key = bin2hex(openssl_random_pseudo_bytes(16));

		$iv_size = openssl_cipher_iv_length($encryption_method);

		$iv = openssl_random_pseudo_bytes($iv_size);

		$encrypted_json_factura = @openssl_encrypt($json_factura, $encryption_method, $secret_key, 0, $iv);

		$encrypted_json_factura = bin2hex($iv) . $encrypted_json_factura;



		openssl_public_encrypt($secret_key, $encrypted_secret_key, $public_key, OPENSSL_PKCS1_PADDING);

		$encrypted_secret_key = base64_encode($encrypted_secret_key);



		$auth_str = $user_id . ":" . $encrypted_secret_key . ":" . $encrypted_json_factura;


		$curl = curl_init();

		curl_setopt_array($curl, array(

		  CURLOPT_URL => "https://api.app.izifacturacion.com/v1/cobros",

		  CURLOPT_RETURNTRANSFER => true,

		  CURLOPT_ENCODING => "",

		  CURLOPT_MAXREDIRS => 10,

		  CURLOPT_TIMEOUT => 30,

		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

		  CURLOPT_CUSTOMREQUEST => "POST",

		  CURLOPT_POSTFIELDS => $json_factura,

		CURLOPT_HTTPHEADER => array(

		"authorization: " . $auth_str,  

		 "content-type: application/json"  

		   ),

		));



		$response = curl_exec($curl);

		$err = curl_error($curl);



		curl_close($curl);


		$result['response'] = $response;

		if ($err) {

		  $result['msg'] = 'fail';

		} else {

		 $result['msg'] = 'success';

		}
		return  $result;
}