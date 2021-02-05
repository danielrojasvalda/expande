<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Gutentor_P1_Duplex_Template2' ) ) {

	/**
	 * Gutentor_P1_Duplex_Template2 Class For Gutentor
	 *
	 * @package Gutentor
	 * @since 2.0.0
	 */
	class Gutentor_P1_Duplex_Template2 extends Gutentor_Query_Elements {

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
			add_filter( 'gutentor_p6_post_module_template_data', array( $this, 'template_data' ), 999, 3 );
		}


		/**
		 * Get Featured Single item data
		 *
		 * @param {string} $data
		 * @param {array}  $post
		 * @param {array}  $attributes
		 * @return {mix}
		 */
		public function p6_template2_featured_single_article( $post, $attributes, $index ) {
			$output              = '';
			$query_sorting       = array_key_exists( 'blockFPSortableItems', $attributes ) ? $attributes['blockFPSortableItems'] : false;
			$enable_featured_image  = ( isset( $attributes['pOnFPFImg'] ) ) ? $attributes['pOnFPFImg'] : false;
			$enable_post_format  = ( isset( $attributes['pOnFPPostFormatOpt'] ) ) ? $attributes['pOnFPPostFormatOpt'] : false;
			$post_format_pos     = ( isset( $attributes['pFPPostFormatOpt'] ) ) ? $attributes['pFPPostFormatOpt'] : false;
			$cat_pos             = ( isset( $attributes['pFPCatPos'] ) ) ? $attributes['pFPCatPos'] : false;
			$enable_featured_cat = ( isset( $attributes['pOnFPFeaturedCat'] ) ) ? $attributes['pOnFPFeaturedCat'] : false;
			$thumb_class         = (has_post_thumbnail() && $enable_featured_image) ? '' : 'gutentor-post-no-thumb';
			$output             .= "<article class='" . apply_filters( 'gutentor_post_module_article_class', gutentor_concat_space( 'gutentor-post', 'gutentor-post-featured',$thumb_class, 'gutentor-post-item-' . $index ), $attributes ) . "'>";
			$output             .= '<div class="gutentor-post-featured-item">';
			if ( $enable_featured_image && has_post_thumbnail( $post->ID ) ) {
				$output .= '<div class="gutentor-post-image-box">';
				$output .= $this->get_featured_post_featured_image( $post, $attributes );
				if ( $enable_post_format && $this->featured_post_format_on_image_condition( $post_format_pos ) ) {
					$output .= $this->get_featured_post_format_data( $post, $attributes );
				}
				if ( $enable_featured_cat && $this->featured_post_categories_on_image_condition( $cat_pos ) ) {
					$output .= $this->get_featured_post_categories_collection( $post, $attributes );
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
							if ( $cat_pos === 'gutentor-fp-cat-pos-before-title' || $post_format_pos === 'gutentor-fp-pf-pos-before-title' ) {
								$output .= '<div class="gutentor-post-title-data-wrap">';
								if ( $enable_post_format && $post_format_pos === 'gutentor-fp-pf-pos-before-title' ) {

									$output .= $this->get_featured_post_format_data( $post, $attributes );
								}
								if ( $enable_featured_cat && $cat_pos === 'gutentor-fp-cat-pos-before-title' ) {

									$output .= $this->get_featured_post_categories_collection( $post, $attributes );
								}
								$output .= $this->get_featured_post_title( $post, $attributes );
								$output .= '</div>';
							} else {

								$output .= $this->get_featured_post_title( $post, $attributes );
							}
							break;
						case 'primary-entry-meta':
							$output .= $this->get_featured_post_primary_meta( $post, $attributes );
							break;
						case 'secondary-entry-meta':
							$output .= $this->get_featured_post_secondary_meta( $post, $attributes );
							break;
						case 'description':
							if ( $cat_pos === 'gutentor-fp-cat-pos-before-ct-box' || $post_format_pos === 'gutentor-fp-pf-pos-before-ct-box' ) {
								$output .= '<div class="gutentor-post-desc-data-wrap">';
								if ( $enable_post_format && $post_format_pos === 'gutentor-fp-pf-pos-before-ct-box' ) {

									$output .= $this->get_featured_post_format_data( $post, $attributes );
								}
								if ( $enable_featured_cat && $cat_pos === 'gutentor-fp-cat-pos-before-ct-box' ) {

									$output .= $this->get_featured_post_categories_collection( $post, $attributes );
								}
								$output .= $this->get_featured_post_description( $post, $attributes );
								$output .= '</div>';
							} else {

								$output .= $this->get_featured_post_description( $post, $attributes );
							}
							break;
						case 'button':
							if ( $cat_pos === 'gutentor-fp-cat-pos-before-button' || $post_format_pos === 'gutentor-fp-pf-pos-before-button' ) {
								$output .= '<div class="gutentor-post-desc-data-wrap">';
								if ( $enable_post_format && $post_format_pos === 'gutentor-fp-pf-pos-before-button' ) {

									$output .= $this->get_featured_post_format_data( $post, $attributes );
								}
								if ( $enable_featured_cat && $cat_pos === 'gutentor-fp-cat-pos-before-button' ) {

									$output .= $this->get_featured_post_categories_collection( $post, $attributes );
								}
								$output .= $this->get_featured_post_button( $post, $attributes );
								$output .= '</div>';
							} else {

								$output .= $this->get_featured_post_button( $post, $attributes );
							}
							break;
						default:
							$output .= '';
							break;
					}
				endforeach;
			endif;
			$output .= '</div>';/*.gutentor-post-content*/
			$output .= '</div>';/*.gutentor-post-featured-item*/
			$output .= '</article>';/*.article*/
			return $output;

		}

		/**
		 * Content On Image Template 1
		 *
		 * @param {string} $data
		 * @param {array}  $post
		 * @param {array}  $attributes
		 * @return {mix}
		 */
		public function template_data( $output, $the_query, $attributes ) {

			$template                = $attributes['p6Temp'] ? $attributes['p6Temp'] : '';
			$post_number                = $attributes['postsToShow'] ? $attributes['postsToShow'] : '';
			if($template != 'gutentor_p6_template2'){
				return $output;
			}
			$index = 0;
			while ( $the_query->have_posts() ) :
				$the_query->the_post();
				if ( $index === 0 ) {
					$output .= "<div class='" . apply_filters( 'gutentor_post_module_grid_column_class', 'grid-lg-6 grid-md-6 grid-12', $attributes ) . "'>";
					$output .= $this->p6_template2_featured_single_article( get_post(), $attributes, $index );
					$output .= '</div>';
				}
				if ( $index === 1 ) {
					$output .= "<div class='" . apply_filters( 'gutentor_post_module_grid_column_class', 'grid-lg-6 grid-md-6 grid-12', $attributes ) . "'>";
				}
				if ( $index > 0 && $index < $post_number ) {
					$output .= $this->p6_single_article( get_post(), $attributes, $index );

				}
				if ( $index + 1 === $post_number) {
					$output .= '</div>';

				}
				$index++;
			endwhile;
			return $output;
		}
	}
}
Gutentor_P1_Duplex_Template2::get_instance()->run();
