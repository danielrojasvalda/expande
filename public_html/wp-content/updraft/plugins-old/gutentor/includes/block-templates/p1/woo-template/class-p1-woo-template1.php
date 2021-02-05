<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Gutentor_P1_Woo_Template1' ) ) {

	/**
	 * Blog_Post_Templates Class For Gutentor
	 *
	 * @package Gutentor
	 * @since 2.0.0
	 */
	class Gutentor_P1_Woo_Template1 extends Gutentor_Query_Elements {

		/**
		 * Gets an instance of this object.
		 * Prevents duplicate instances which avoid artefacts and improves performance.
		 *
		 * @static
		 * @access public
		 * @since 2.0.0
		 * @return object
		 */
		public static function get_instance() {

			// Store the instance locally to avoid private static replication
			static $instance = null;

			// Only run these methods if they haven't been ran previously
			if ( null === $instance ) {
				$instance = new self();
			}

			// Always return the instance
			return $instance;

		}

		/**
		 * Run Block
		 *
		 * @access public
		 * @since 2.0.0
		 * @return void
		 */
		public function run() {
			add_filter( 'gutentor_post_module_p1_template_data', array( $this, 'load_blog_post_template' ), 99999, 3 );
		}

		
		/**
		 * Get Category Meta
		 *
		 * @param {array} $post
		 * @param {array} $product
		 * @param {array} $attributes
		 * @return string
		 */
		function get_woo_sales_text( $post,$product,$attributes ) {

			$output          = '';
			if ( $product->is_on_sale() ) {
			$output = '<div class="gutentor-categories"><div class="post-category gutentor-wc-on-sale-wrap">' .apply_filters( 'woocommerce_sale_flash', '<span class="onsale">' . esc_html__( 'Sale!', 'gutentor' ) . '</span>', $post, $product ) . '</div></div>';

			}
			return $output;
		}

		function new_badge_product($post,$product){

			if(!$product){
				global $product;
			}
			$newness_days = 30;
			$created = strtotime( $product->get_date_created() );
			if ( ( time() - ( 60 * 60 * 24 * $newness_days ) ) < $created ) {
				return apply_filters( 'gutentor_woocommerce_new_badge','<div class="gutentor-post-format gutentor-wc-new-wrap"><span class="gutentor-wc-new">' . esc_html__( 'New!', 'gutentor' ) . '</span></div>', $post, $product ); 

			}
			return '';
		 }

		 /**
		 * Featured Image
		 *
		 * @static
		 * @access public
		 * @since 2.0.1
		 * @param {array} $post
		 * @param {array} $product
		 * @param {array} $attributes
		 * @return string
		 */
		public function get_woo_product_thumbnail( $post,$product, $attributes ) {
			$image_output = $target  = $rel  = $link_class = '';
			$image_link_enable         	= false;
			if ( array_key_exists( 'pImgOnLink', $attributes ) ) {
				$image_link_enable    		= ( isset( $attributes['pImgOnLink'] ) ) ? $attributes['pImgOnLink'] : false;
			}			
			if ( array_key_exists( 'pImgOpenNewTab', $attributes ) ) {
				$target 					= ( isset( $attributes['pImgOpenNewTab'] ) )  ? 'target="_blank"' : '';
			}
			if ( array_key_exists( 'pImgLinkRel', $attributes ) ) {
				$rel = ( $attributes['pImgLinkRel'] ) ? 'rel="' . $attributes['pImgLinkRel'] . '"' : '';

			}
			if ( array_key_exists( 'pImgClass', $attributes ) ) {
				$link_class = ( $attributes['pImgClass'] ) ? sanitize_html_class($attributes['pImgClass']) : '';

			}
			$overlay_obj    		= ( isset( $attributes['pFImgOColor'] ) ) ? $attributes['pFImgOColor'] : false;
			$thumbnail_size    		= ( isset( $attributes['pFImgSize'] ) ) ? $attributes['pFImgSize'] : '';
			$overlay_enable 		= ( $overlay_obj && array_key_exists( 'enable', $overlay_obj ) ) ? $attributes['pFImgOColor']['enable'] : false;
			$overlay              	= ( $overlay_enable ) ? 'gutentor-overlay' : '';
			if ( isset( $attributes['pOnFImg'] ) && $attributes['pOnFImg'] ) {
				$image_output = '';
				$image_output .= '<div class="' . gutentor_concat_space( 'gutentor-image-thumb', $overlay ) . '">';	
				if($image_link_enable){
					$link = apply_filters( 'woocommerce_loop_product_link', get_the_permalink($post->ID), $product );
					$image_output .= '<a href="' . esc_url( $link ) . '" class="woocommerce-LoopProduct-link woocommerce-loop-product__link ' . gutentor_concat_space($link_class) . '" '.gutentor_concat_space( $target, $rel ).'>';
					$image_output .= woocommerce_get_product_thumbnail($thumbnail_size);
					$image_output .= '</a>';
				}
				else{
					$image_output .= woocommerce_get_product_thumbnail($thumbnail_size);
				}			
		
				$image_output .= '</div>';
			}
			return $image_output;

		}

		/**
		 * Load Grid Template 1
		 *
		 * @param {string} $data
		 * @param {array}  $post
		 * @param {array}  $attributes
		 * @return {mix}
		 */
		public function gutentor_p1_grid_template1( $data, $post, $attributes ) {

			$product = wc_get_product( $post->ID );
			$rating  = $product->get_average_rating();
			$count   = $product->get_rating_count();
			$rating_html  = wc_get_rating_html( $rating, $count );

			$query_sorting       = array_key_exists( 'blockSortableItems', $attributes ) ? $attributes['blockSortableItems'] : false;
			$enable_post_format  = ( isset( $attributes['pOnPostFormatOpt'] ) ) ? $attributes['pOnPostFormatOpt'] : false;
			$post_format_pos     = ( isset( $attributes['pPostFormatPos'] ) ) ? $attributes['pPostFormatPos'] : false;
			$cat_pos             = ( isset( $attributes['pPostCatPos'] ) ) ? $attributes['pPostCatPos'] : false;
			$enable_featured_cat = ( isset( $attributes['pOnFeaturedCat'] ) ) ? $attributes['pOnFeaturedCat'] : false;
			$enable_featured_img = ( isset( $attributes['pOnFImg'] ) ) ? $attributes['pOnFImg'] : false;

			$output = '';
			if ( $query_sorting ) :
				foreach ( $query_sorting as $element ) :
					if ( ! ( array_key_exists( 'itemValue', $element ) ) ) {
						return $output;
					}
					switch ( $element['itemValue'] ) {
						case 'featured-image':
							if ( $enable_featured_img ) {
								$output .= '<div class="gutentor-post-image-box">';
								$output .= $this->get_woo_product_thumbnail( $post,$product, $attributes );
								if ( $enable_post_format && $this->post_format_on_image_condition( $post_format_pos ) ) {
									$output .= $this->new_badge_product($post, $product );
								}
								if ( $enable_featured_cat && $this->categories_on_image_condition( $cat_pos ) ) {
									$output .= $this->get_woo_sales_text( $post,$product, $attributes );
								}
								$output .= '</div>';
							}
							break;
						case 'title':
							if ( $cat_pos === 'gutentor-cat-pos-before-title' || $post_format_pos === 'gutentor-pf-pos-before-title' ) {
								$output .= '<div class="gutentor-post-title-data-wrap">';
								if ( $enable_post_format && $post_format_pos === 'gutentor-pf-pos-before-title' ) {

									$output .= $this->new_badge_product( $post, $product );
								}
								if ( $enable_featured_cat && $cat_pos === 'gutentor-cat-pos-before-title' ) {

									$output .= $this->get_woo_sales_text( $post,$product, $attributes );
								}
								$output .= $this->get_title( $post, $attributes );
								$output .= '</div>';
							} else {

								$output .= $this->get_title( $post, $attributes );
							}
							break;
						case 'price':
							if ( isset( $attributes['wooOnPrice'] ) && $attributes['wooOnPrice'] ) {
								if ( $product->get_price_html() ){
									$output .= '<div class="gutentor-wc-price">';
									$output .= $product->get_price_html();
									$output .= '</div>';
								}
							}
							break;
						case 'rating':
							if ( isset( $attributes['wooOnRating'] ) && $attributes['wooOnRating'] ) {
								if($rating_html){
									$output .= '<div class="gutentor-wc-rating">';
									$output .= $rating_html;
									$output .= '</div>';
								}
							}
							break;
						case 'primary-entry-meta':
						$output .= $this->get_primary_meta( $post, $attributes );
						break;
					
						case 'secondary-entry-meta':
						$output .= $this->get_secondary_meta( $post, $attributes );
						break;
						case 'description':
							if ( $cat_pos === 'gutentor-cat-pos-before-ct-box' || $post_format_pos === 'gutentor-pf-pos-before-ct-box' ) {
								$output .= '<div class="gutentor-post-desc-data-wrap">';
								if ( $enable_post_format && $post_format_pos === 'gutentor-pf-pos-before-ct-box' ) {

									$output .= $this->new_badge_product( $post,$product );
								}
								if ( $enable_featured_cat && $cat_pos === 'gutentor-cat-pos-before-ct-box' ) {

									$output .= $this->get_woo_sales_text( $post,$product, $attributes );

									
								}
								$output .= $this->get_description( $post, $attributes );
								$output .= '</div>';
							} else {

								$output .= $this->get_description( $post, $attributes );
							}
							break;
						case 'button':
							if ( $cat_pos === 'gutentor-cat-pos-before-button' || $post_format_pos === 'gutentor-pf-pos-before-button' ) {
								$output .= '<div class="gutentor-post-desc-data-wrap">';
								if ( $enable_post_format && $post_format_pos === 'gutentor-pf-pos-before-button' ) {

									$output .= $this->new_badge_product( $post,$product );
								}
								if ( $enable_featured_cat && $cat_pos === 'gutentor-cat-pos-before-button' ) {

									$output .= $this->get_woo_sales_text( $post,$product,$attributes );

								}
								if ( isset( $attributes['pOnBtn'] ) && $attributes['pOnBtn'] ) {
								$output .= '<div class="gutentor-woo-add-to-cart wc-block-grid__product-add-to-cart">';
								ob_start();
								woocommerce_template_loop_add_to_cart(array('gutentor-attributes' => $attributes));
								$output .= ob_get_clean();
								$output .= '</div>';
								}
								$output .= '</div>';
							} 
							else {
								if ( isset( $attributes['pOnBtn'] ) && $attributes['pOnBtn'] ) {
								$output .= '<div class="gutentor-woo-add-to-cart wc-block-grid__product-add-to-cart">';
								ob_start();
								woocommerce_template_loop_add_to_cart(array('gutentor-attributes' => $attributes));
								$output .= ob_get_clean();
								$output .= '</div>';
								}
							}
							break;
						default:
							$output .= '';
							break;
					}
				endforeach;
			endif;
			return $output;
		}

		/**
		 * Load List Template 1
		 *
		 * @param {string} $data
		 * @param {array}  $post
		 * @param {array}  $attributes
		 * @return {mix}
		 */
		public function gutentor_p1_list_template1( $data, $post, $attributes ) {


			$product = wc_get_product( $post->ID );
			$rating  = $product->get_average_rating();
			$count   = $product->get_rating_count();
			$rating_html  = wc_get_rating_html( $rating, $count );

			$query_sorting       = array_key_exists( 'blockSortableItems', $attributes ) ? $attributes['blockSortableItems'] : false;
			$enable_post_format  = ( isset( $attributes['pOnPostFormatOpt'] ) ) ? $attributes['pOnPostFormatOpt'] : false;
			$post_format_pos     = ( isset( $attributes['pPostFormatPos'] ) ) ? $attributes['pPostFormatPos'] : false;
			$cat_pos             = ( isset( $attributes['pPostCatPos'] ) ) ? $attributes['pPostCatPos'] : false;
			$enable_featured_cat = ( isset( $attributes['pOnFeaturedCat'] ) ) ? $attributes['pOnFeaturedCat'] : false;
			$enable_featured_img = ( isset( $attributes['pOnFImg'] ) ) ? $attributes['pOnFImg'] : false;

			$output              = '';
			if ( $enable_featured_img ) {
				$output .= '<div class="gutentor-post-image-box">';
				$output .= $this->get_woo_product_thumbnail( $post,$product, $attributes );
				if ( $enable_post_format && $this->post_format_on_image_condition( $post_format_pos ) ) {
					$output .= $this->new_badge_product( $post,$product );
				}
				if ( $enable_featured_cat && $this->categories_on_image_condition( $cat_pos ) ) {
					$output .= $this->get_woo_sales_text( $post,$product,$attributes );
				}
				$output .= '</div>';
			}
			$output .= '<div class="gutentor-post-content">';
			if ( $query_sorting ) :
				foreach ( $query_sorting as $element ) :
					if ( ! ( array_key_exists( 'itemValue', $element ) ) ) {
						return $output;
					}
					switch ( $element['itemValue'] ) {
						case 'title':
							if ( $cat_pos === 'gutentor-cat-pos-before-title' || $post_format_pos === 'gutentor-pf-pos-before-title' ) {
								$output .= '<div class="gutentor-post-title-data-wrap">';
								if ( $enable_post_format && $post_format_pos === 'gutentor-pf-pos-before-title' ) {

									$output .= $this->new_badge_product( $post, $product );
								}
								if ( $enable_featured_cat && $cat_pos === 'gutentor-cat-pos-before-title' ) {

									$output .= $this->get_woo_sales_text( $post,$product, $attributes );
								}
								$output .= $this->get_title( $post, $attributes );
								$output .= '</div>';
							} else {

								$output .= $this->get_title( $post, $attributes );
							}
							break;
						case 'price':
							if ( isset( $attributes['wooOnPrice'] ) && $attributes['wooOnPrice'] ) {
								if ( $product->get_price_html() ){
									$output .= '<div class="gutentor-wc-price">';
									$output .= $product->get_price_html();
									$output .= '</div>';
								}
							}
							break;
						case 'rating':
							if ( isset( $attributes['wooOnRating'] ) && $attributes['wooOnRating'] ) {
								if($rating_html){
									$output .= '<div class="gutentor-wc-rating">';
									$output .= $rating_html;
									$output .= '</div>';
								}
							}
							break;
						case 'primary-entry-meta':
							$output .= $this->get_primary_meta( $post, $attributes );
							break;
						case 'secondary-entry-meta':
							$output .= $this->get_secondary_meta( $post, $attributes );
							break;
						case 'description':
							if ( $cat_pos === 'gutentor-cat-pos-before-ct-box' || $post_format_pos === 'gutentor-pf-pos-before-ct-box' ) {
								$output .= '<div class="gutentor-post-desc-data-wrap">';
								if ( $enable_post_format && $post_format_pos === 'gutentor-pf-pos-before-ct-box' ) {

									$output .= $this->new_badge_product( $post,$product );
								}
								if ( $enable_featured_cat && $cat_pos === 'gutentor-cat-pos-before-ct-box' ) {

									$output .= $this->get_woo_sales_text( $post,$product, $attributes );

									
								}
								$output .= $this->get_description( $post, $attributes );
								$output .= '</div>';
							} else {

								$output .= $this->get_description( $post, $attributes );
							}
							break;
						case 'button':
							if ( $cat_pos === 'gutentor-cat-pos-before-button' || $post_format_pos === 'gutentor-pf-pos-before-button' ) {
								$output .= '<div class="gutentor-post-desc-data-wrap">';
								if ( $enable_post_format && $post_format_pos === 'gutentor-pf-pos-before-button' ) {

									$output .= $this->new_badge_product( $post,$product );
								}
								if ( $enable_featured_cat && $cat_pos === 'gutentor-cat-pos-before-button' ) {

									$output .= $this->get_woo_sales_text( $post,$product,$attributes );

								}
								if ( isset( $attributes['pOnBtn'] ) && $attributes['pOnBtn'] ) {
								$output .= '<div class="gutentor-woo-add-to-cart wc-block-grid__product-add-to-cart">';
								ob_start();
								woocommerce_template_loop_add_to_cart(array('gutentor-attributes' => $attributes));
								$output .= ob_get_clean();
								$output .= '</div>';
								}
								$output .= '</div>';
							} 
							else {

								if ( isset( $attributes['pOnBtn'] ) && $attributes['pOnBtn'] ) {
								$output .= '<div class="gutentor-woo-add-to-cart wc-block-grid__product-add-to-cart">';
								ob_start();
								woocommerce_template_loop_add_to_cart(array('gutentor-attributes' => $attributes));
								$output .= ob_get_clean();
								$output .= '</div>';
								}
							}
							break;
						default:
							$output .= '';
							break;
					}
				endforeach;
			endif;
			$output .= '</div>';/*.gutentor-post-content*/
			return $output;

		}

		/**
		 * Load Template 1
		 *
		 * @param {string} $data
		 * @param {array}  $post
		 * @param {array}  $attributes
		 * @return {mix}
		 */
		public function gutentor_p1_template1( $data, $post, $attributes ) {

			$template_style = isset( $attributes['gStyle'] ) ? $attributes['gStyle'] : false;
			$output         = '';
			if ( $template_style == 'gutentor-blog-grid' ) {
				$output = $this->gutentor_p1_grid_template1( $data, $post, $attributes );
			} elseif ( $template_style == 'gutentor-blog-list' ) {
				$output = $output = $this->gutentor_p1_list_template1( $data, $post, $attributes );
			}
			return $output;
		}

		/**
		 * Load Template 2
		 *
		 * @param {string} $data
		 * @param {array}  $post
		 * @param {array}  $attributes
		 * @return {mix}
		 */
		public function gutentor_p1_template2( $data, $post, $attributes ) {

			
			$product = wc_get_product( $post->ID );
			$rating  = $product->get_average_rating();
			$count   = $product->get_rating_count();
			$rating_html  = wc_get_rating_html( $rating, $count );
			
			$enable_post_format  = ( isset( $attributes['pOnPostFormatOpt'] ) ) ? $attributes['pOnPostFormatOpt'] : false;
			$post_format_pos     = ( isset( $attributes['pPostFormatPos'] ) ) ? $attributes['pPostFormatPos'] : false;
			$cat_pos             = ( isset( $attributes['pPostCatPos'] ) ) ? $attributes['pPostCatPos'] : false;
			$enable_featured_cat = ( isset( $attributes['pOnFeaturedCat'] ) ) ? $attributes['pOnFeaturedCat'] : false;
			$enable_featured_img = ( isset( $attributes['pOnFImg'] ) ) ? $attributes['pOnFImg'] : false;
			$output              = '';
			if ( $enable_featured_img ) {
				$output .= '<div class="gutentor-post-image-box">';
				$output .= $this->get_woo_product_thumbnail( $post,$product, $attributes );
				if ( $enable_post_format && $this->post_format_on_image_condition( $post_format_pos ) ) {
					$output .= $this->new_badge_product( $post,$product );
				}
				if ( $enable_featured_cat && $this->categories_on_image_condition( $cat_pos ) ) {
					$output .= $this->get_woo_sales_text( $post,$product,$attributes );
				}
				$output .= $this->get_primary_meta( $post, $attributes );
				$output .= '</div>';/*.gutentor-post-image-box*/
			}
			$output .= '<div class="gutentor-post-content">';
			if ( $enable_post_format && $post_format_pos === 'gutentor-pf-pos-before-title' ) {

				$output .= $this->new_badge_product( $post,$product );
			}
			if ( $enable_featured_cat && $cat_pos === 'gutentor-cat-pos-before-title' ) {

				$output .= $this->get_woo_sales_text( $post,$product,$attributes );

			}
			$output .= $this->get_title( $post, $attributes );
			if ( isset( $attributes['wooOnPrice'] ) && $attributes['wooOnPrice'] ) {
				if ( $product->get_price_html() ){
					$output .= '<div class="gutentor-wc-price">';
					$output .= $product->get_price_html();
					$output .= '</div>';
				}
			}
			if ( isset( $attributes['wooOnRating'] ) && $attributes['wooOnRating'] ) {
				if($rating_html){
					$output .= '<div class="gutentor-wc-rating">';
					$output .= $rating_html;
					$output .= '</div>';
				}
			}
			if ( $enable_post_format && $post_format_pos === 'gutentor-pf-pos-before-ct-box' ) {

				$output .= $this->new_badge_product( $post,$product );
			}
			if ( $enable_featured_cat && $cat_pos === 'gutentor-cat-pos-before-ct-box' ) {

				$output .= $this->get_woo_sales_text( $post,$product,$attributes );

			}
			$output .= $this->get_description( $post, $attributes );
			$output .= $this->get_secondary_meta( $post, $attributes );
			if ( $enable_post_format && $post_format_pos === 'gutentor-pf-pos-before-button' ) {

				$output .= $this->new_badge_product( $post,$product );
			}
			if ( $enable_featured_cat && $cat_pos === 'gutentor-cat-pos-before-button' ) {

				$output .= $this->get_woo_sales_text( $post,$product,$attributes );

			}
			if ( isset( $attributes['pOnBtn'] ) && $attributes['pOnBtn'] ) {
			$output .= '<div class="gutentor-woo-add-to-cart wc-block-grid__product-add-to-cart">';
			ob_start();
			woocommerce_template_loop_add_to_cart(array('gutentor-attributes' => $attributes));
			$output .= ob_get_clean();
			$output .= '</div>';
			}
			$output .= '</div>';/*.gutentor-post-content*/
			return $output;
		}

		/**
		 * Blog Post Templates
		 *
		 * @param {string} $data
		 * @param {array}  $post
		 * @param {array}  $attributes
		 * @return {mix}
		 */

		public function load_blog_post_template( $data, $post, $attributes ) {

			$output   = $data;
			$template = ( isset( $attributes['p1Temp'] ) ) ? $attributes['p1Temp'] : '';
			$post_type = ( isset( $attributes['pPostType'] ) ) ? $attributes['pPostType'] : 'post';
			if($post_type != 'product'){
				return $output;
			}
			if ( method_exists( $this, $template ) ) {
				$output = $this->$template( $data, $post, $attributes );
			} else {
				$output = $this->gutentor_p1_template1( $data, $post, $attributes );
			}
			return $output;
		}

	}
}
Gutentor_P1_Woo_Template1::get_instance()->run();
