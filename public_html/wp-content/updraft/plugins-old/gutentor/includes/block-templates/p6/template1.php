<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Gutentor_P1_Duplex_Template1' ) ) {

	/**
	 * Gutentor_P1_Duplex_Template1 Class For Gutentor
	 *
	 * @package Gutentor
	 * @since 2.0.0
	 */
	class Gutentor_P1_Duplex_Template1 extends Gutentor_Query_Elements {

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
			if($template != 'gutentor_p6_template1'){
				return $output;
			}
			$index = 0;
			while ( $the_query->have_posts() ) :
				$the_query->the_post();
				if ( $index === 0 ) {
					$output .= "<div class='" . apply_filters( 'gutentor_post_module_grid_column_class', 'grid-lg-6 grid-md-6 grid-12', $attributes ) . "'>";
					$output .= $this->p6_featured_single_article( get_post(), $attributes, $index );
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
Gutentor_P1_Duplex_Template1::get_instance()->run();
