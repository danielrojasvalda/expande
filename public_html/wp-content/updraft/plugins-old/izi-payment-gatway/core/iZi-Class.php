<?php
/**
 * iZi Payment Gateway.
 *
 * Provides a iZi Payment Gateway.
 *
 * @class       iZi_payment_gateway
 * @extends     WC_Payment_Gateway
 * @version     0.0.1
 * @package     WooCommerce/Classes/Payment
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Gateway_Paypal Class.
 */
class iZi_payment_gateway extends WC_Payment_Gateway {

	function __construct() {

		$this->id = "izi_payment_gateway";
		$this->method_title = __( "iZi Payment Gateway", 'iZi-gateway' );
		$this->description = __( "Paga con tarjetas de crédito/débito", 'iZi-gateway' );
		$this->method_description = $this->get_iZi_description($this->description);
		$this->has_fields        = false;
		$this->order_button_text = __( 'Continuar con iZi', 'iZi-gateway' );
		$this->title = __( "iZi Payment Gateway", 'iZi-gateway' );
		$this->supports           = array(
					'products',
					'refunds',
				);
		 $this->icon = $this->getiZiIcon();
		// setting defines
		$this->init_form_fields();

		// load time variable setting
		$this->init_settings();
		
		// Turn these settings into variables we can use
		foreach ( $this->settings as $setting_key => $value ) {
			$this->$setting_key = $value;
		}
		
		add_action('woocommerce_api_izi_payment_gateway', array($this, 'izicallback'));
		
		// Save settings
		if ( is_admin() ) {
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		}
	}		

	/**
	 * Initialise Gateway Settings Form Fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'		=> __( 'Enable / Disable', 'iZi-gateway' ),
				'label'		=> __( 'Enable this payment gateway', 'iZi-gateway' ),
				'type'		=> 'checkbox',
				'default'	=> 'no',
			),
			'title' => array(
				'title'		=> __( 'Title', 'iZi-gateway' ),
				'type'		=> 'text',
				'desc_tip'	=> __( 'Payment title of checkout process.', 'iZi-gateway' ),
				'default'	=> __( 'iZi Pagos', 'iZi-gateway' ),
			),
			'description' => array(
				'title'		=> __( 'Description', 'iZi-gateway' ),
				'type'		=> 'textarea',
				'desc_tip'	=> __( 'Payment title of checkout process.', 'iZi-gateway' ),
				'default'	=> __( 'Paga con tarjetas de crédito/débito.', 'iZi-gateway' ),
				'css'		=> 'max-width:450px;'
			),
		);		
	}
	
	/**
	 * Process the payment and return the result.
	 *
	 * @param  int $order_id Order ID.
	 * @return array
	 */
	public function process_payment( $order_id ) {
		global $woocommerce;

		$customer_order = new WC_Order( $order_id );
		$result = iZi_get_url($order_id);
		if($result['msg'] == 'success')
		{
			$response = $result['response'];
			$response = json_decode($response, true);
			if(isset($response['link']) && !empty($response['link']))
			{
				$woocommerce->cart->empty_cart();

				return array(
					'result'   => 'success',
					'redirect' => $response['link'],
				);

			}
			else
			{
				wc_add_notice(  $result['response'], 'error' );
			}
		}
		else
		{
			wc_add_notice(  $result['response'], 'error' );
		}		
		
	}
	
	/*
	 Validate fields
	*/
	public function validate_fields() {
		return true;
	}
	/*
	 Callback function on success or faliure
	*/
	public function izicallback()
	{
		$result = isset($_REQUEST['result']) ? $_REQUEST['result'] : '';
		$order_id = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
		$order = wc_get_order( $order_id );
		if(empty($order))
		{
			wp_redirect(get_site_url());
			exit();
		}
		$url = $order->get_checkout_order_received_url();
		if(!empty($result))
		{			
			if($result == 'success')
			{
				$order->update_status( 'processing' );
				$order->add_order_note( __("iZi Pagos : Payment received successfully", "iZi-gateway") );
			} else 
			{
				$order->update_status( 'failed' );
				$order->add_order_note( __("iZi Pagos : Payment failed", "iZi-gateway") );
			}			
		}
		else
		{
			$order->update_status( 'failed' );
			$order->add_order_note( __("iZi Pagos : Payment failed", "iZi-gateway") );
		}
		wp_redirect($url);
		exit();		
	}

	/*
	Add icon on payment page
	*/

	public function getiZiIcon()
    {
        return plugins_url( '/assets/images/iZi-front-logo.png', iZi_RELPATH );
    }

    /**
	 * Add icon to payment page settings
	 *
	 * @param  Method description.
	 * @return icon url
	 */

    public function get_iZi_description($method_description='')
    {
        return '<div class="mp-header-logo">
            <div class="mp-left-header">
                <img src="' . plugins_url( '/assets/images/iZi-admin-logo.png', iZi_RELPATH ) . '" style="width:35px;">
            </div>
            <div>' . $method_description . '</div>
        </div>';
    }
}