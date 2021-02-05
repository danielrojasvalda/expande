<?php
/**
 * WC Quick View Template Gallery Class
 *
 * Class Function into woocommerce plugin
 *
 * Table Of Contents
 *
 * wc_dynamic_gallery_display()
 * wc_dynamic_gallery_preview()
 */

namespace A3Rev\WCQV;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Dynamic_Gallery
{

	public function __construct() {

	}

	public function wc_default_gallery_display( $product_id = 0 ) {
		global $post, $product;

		$product = wc_get_product( $product_id );
		$post    = get_post( $product_id );

		setup_postdata( $post );

		?>
		<div class="single-product">
			<div class="product">
		<?php

		wc_get_template( 'single-product/product-image.php' );

		wp_reset_postdata();
		?>
			</div>
		</div>
		<script type="text/javascript">
			jQuery( function( $ ) {
				/*
				 * Initialize all galleries on page.
				 */
				setTimeout( function() {
					$( '.quick_view_product_gallery_container .woocommerce-product-gallery' ).each( function() {
						$( this ).wc_product_gallery();
					} );
				}, 100 );
			});
		</script>
		<?php

	}
}
