<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Gutentor_Query_Elements' ) ) {

	/**
	 * Base Class For Gutentor for common functions
	 *
	 * @package Gutentor
	 * @since 1.0.1
	 */
	class Gutentor_Query_Elements {

		/**
		 * Gets an instance of this object.
		 * Prevents duplicate instances which avoid artefacts and improves performance.
		 *
		 * @static
		 * @access public
		 * @since 1.0.1
		 * @return object
		 */
		public static function get_base_instance() {

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
		 * Title
		 *
		 * @static
		 * @access public
		 * @since 2.0.1
		 * @param {array} $post
		 * @param {array} $attributes
		 * @return string
		 */
		public function get_title( $post, $attributes ) {
			$output = '';
			if ( isset( $attributes['pOnTitle'] ) && $attributes['pOnTitle'] ) {
				$title_tag = $attributes['pTitleTag'];
				$output   .= '<' . $title_tag . ' class="gutentor-post-title">';
				$output   .= '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">';
				$output   .= get_the_title();
				$output   .= '</a>';
				$output   .= '</' . $title_tag . '>';
			}
			return $output;

		}


		/**
		 * Description
		 *
		 * @static
		 * @access public
		 * @since 2.0.1
		 * @param {array} $post
		 * @param {array} $attributes
		 * @return string
		 */
		public function get_description( $post, $attributes ) {
			$output = '';
			if ( ! isset( $attributes['PExcerptLen'] ) || ! isset( $attributes['pOnDesc'] ) ) {
				return $output;
			}
			if ( $attributes['PExcerptLen'] > 0 && $attributes['pOnDesc'] ) {
			    $in_words = (isset($attributes['pExcerptLenInWords']) && $attributes['pExcerptLenInWords']);
				$output .= '<div class="gutentor-post-desc">';
				$output .= '<p>' . gutentor_get_excerpt_by_id( $post->ID, $attributes['PExcerptLen'], $in_words ) . '</p>';
				$output .= '</div>';
			}
			return $output;

		}

		/**
		 * Featured Image
		 *
		 * @static
		 * @access public
		 * @since 2.0.1
		 * @param {array} $post
		 * @param {array} $attributes
		 * @return string
		 */
		public function get_featured_image( $post, $attributes ) {
			$output         = '';
			$overlay_obj    = ( isset( $attributes['pFImgOColor'] ) ) ? $attributes['pFImgOColor'] : false;
			$overlay_enable = ( $overlay_obj && array_key_exists( 'enable', $overlay_obj ) ) ? $attributes['pFImgOColor']['enable'] : false;

			$overlay              = ( $overlay_enable ) ? "<div class='overlay'></div>" : '';
			$overlay              = ( $overlay_enable ) ? 'gutentor-overlay' : '';
			$enable_image_display = isset( $attributes['pOnImgDisplayOpt'] ) ? $attributes['pOnImgDisplayOpt'] : false;
			$image_display        = isset( $attributes['pImgDisplayOpt'] ) ? $attributes['pImgDisplayOpt'] : false;
			if ( isset( $attributes['pOnFImg'] ) && $attributes['pOnFImg'] ) {
				$image_output = '';
				if ( has_post_thumbnail() ) {
					if ( 'bg-image' == $image_display && $enable_image_display ) {
						$url = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ) , $attributes['pFImgSize'] );
						if ( $url ) {
							$image_output .= '<div class="' . gutentor_concat_space( 'gutentor-bg-image', $overlay ) . '" style="background-image:url(' . esc_url($url[0]) . ')">';
							$image_output .= $overlay;
							$image_output .= '</div>';
						}
					} else {
						$image_output .= '<div class="' . gutentor_concat_space( 'gutentor-image-thumb', $overlay ) . '">';
						$image_output .= get_the_post_thumbnail( $post->ID , $attributes['pFImgSize'] , '' );
						$image_output .= $overlay;
						$image_output .= '</div>';
					}
					$output .= apply_filters( 'gutentor_save_item_image_display_data', $image_output, get_permalink(), $attributes );
				}
			}
			return $output;

		}

		/**
		 * Primary Meta Date
		 *
		 * @static
		 * @access public
		 * @since 2.0.5
		 * @param {array} $post
		 * @param {array} $attributes
		 * @return string
		 */
		public function get_primary_meta_date_data( $post, $attributes ) {
			$output = '';
			if ( isset( $attributes['pOnDateMeta1'] ) && $attributes['pOnDateMeta1'] ) {
				$dateFontAwesomeClass = (int) gutentor_get_options( 'gutentor_font_awesome_version' ) === 4 ? 'fa fa-calendar' : 'far fa-calendar-alt';
				$output              .= '<div class="posted-on"><i class="' . $dateFontAwesomeClass . '"></i>';
				$output              .= '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . get_the_date() . '</a>';
				$output              .= '</div>';

			}
			return $output;
		}

		/**
		 * Secondary Meta Date
		 *
		 * @static
		 * @access public
		 * @since 2.0.5
		 * @param {array} $post
		 * @param {array} $attributes
		 * @return string
		 */
		public function get_secondary_meta_date_data( $post, $attributes ) {
			$output = '';
			if ( isset( $attributes['pOnDateMeta2'] ) && $attributes['pOnDateMeta2'] ) {

				$dateFontAwesomeClass = (int) gutentor_get_options( 'gutentor_font_awesome_version' ) === 4 ? 'fa fa-calendar' : 'far fa-calendar-alt';
				$output              .= '<div class="posted-on"><i class="' . $dateFontAwesomeClass . '"></i>';
				$output              .= '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . get_the_date() . '</a>';
				$output              .= '</div>';

			}
			return $output;

		}

		/**
		 * Primary Meta Comment
		 *
		 * @static
		 * @access public
		 * @since 2.0.5
		 * @param {array} $post
		 * @param {array} $attributes
		 * @return string
		 */
		public function get_primary_meta_comment_data( $post, $attributes ) {
			$output = '';
			if ( ! is_object( $post ) ) {
				return $output;
			}
			$comment_data = wp_count_comments( $post->ID );

			if ( ! $comment_data->total_comments ) {
				return $output;
			}
			if ( isset( $attributes['pOnCommentMeta1'] ) && $attributes['pOnCommentMeta1'] ) {
				$commentFontAwesomeClass = (int) gutentor_get_options( 'gutentor_font_awesome_version' ) === 4 ? 'fa fa-comment' : 'fas fa-comment';
				$output                 .= '<div class="comments-link"><i class="' . $commentFontAwesomeClass . '"></i>';
				$output                 .= $comment_data->total_comments;
				$output                 .= '</div>';

			}
			return $output;
		}

		/**
		 * Secondary Meta Comment
		 *
		 * @static
		 * @access public
		 * @since 2.0.5
		 * @param {array} $post
		 * @param {array} $attributes
		 * @return string
		 */
		public function get_secondary_meta_comment_data( $post, $attributes ) {
			$output       = '';
			$comment_data = wp_count_comments( $post->ID );

			if ( ! $comment_data->total_comments ) {
				return $output;
			}
			if ( isset( $attributes['pOnCommentMeta2'] ) && $attributes['pOnCommentMeta2'] ) {
				$commentFontAwesomeClass = (int) gutentor_get_options( 'gutentor_font_awesome_version' ) === 4 ? 'fa fa-comment' : 'fas fa-comment';
				$output                 .= '<div class="comments-link"><i class="' . $commentFontAwesomeClass . '"></i>';
				$output                 .= $comment_data->total_comments;
				$output                 .= '</div>';

			}
			return $output;
		}

		/**
		 * Primary Meta Author
		 *
		 * @static
		 * @access public
		 * @since 2.0.5
		 * @param {array} $post
		 * @param {array} $attributes
		 * @return string
		 */
		public function get_primary_meta_author_data( $post, $attributes ) {
			$output = '';
			if ( isset( $attributes['pOnAuthorMeta1'] ) && $attributes['pOnAuthorMeta1'] ) {
				$authorFontAwesomeClass = (int) gutentor_get_options( 'gutentor_font_awesome_version' ) === 4 ? 'fa fa-user' : 'far fa-user';
				$output                .= '<div class="author vcard"><i class="' . $authorFontAwesomeClass . '"></i>';
				$output                .= '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . get_the_author() . '</a>';
				$output                .= '</div>';
			}
			return $output;

		}

		/**
		 * Secondary Meta Author
		 *
		 * @static
		 * @access public
		 * @since 2.0.5
		 * @param {array} $post
		 * @param {array} $attributes
		 * @return string
		 */
		public function get_secondary_meta_author_data( $post, $attributes ) {
			$output = '';
			if ( isset( $attributes['pOnAuthorMeta2'] ) && $attributes['pOnAuthorMeta2'] ) {
				$authorFontAwesomeClass = (int) gutentor_get_options( 'gutentor_font_awesome_version' ) === 4 ? 'fa fa-user' : 'far fa-user';
				$output                .= '<div class="author vcard"><i class="' . $authorFontAwesomeClass . '"></i>';
				$output                .= '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . get_the_author() . '</a>';
				$output                .= '</div>';
			}
			return $output;

		}

		/**
		 * Primary Meta Categories
		 *
		 * @static
		 * @access public
		 * @since 2.0.5
		 * @param {array} $post
		 * @param {array} $attributes
		 * @return string
		 */
		public function get_primary_meta_categories_data( $post, $attributes ) {
			$output = '';
			if ( isset( $attributes['pOnCatMeta1'] ) && $attributes['pOnCatMeta1'] ) {
				$categories_list = get_the_category_list( esc_html__( ', ', 'gutentor' ) );
				if ( $categories_list ) {
					$catFontAwesomeClass = (int) gutentor_get_options( 'gutentor_font_awesome_version' ) === 4 ? 'fa fa-tags' : 'fas fa-tags';
					$output             .= '<div class="gutentor-meta-categories"><i class="' . $catFontAwesomeClass . '"></i>' . $categories_list . '</div>';
				}
			}
			return $output;

		}

		
		/**
		 * Primary Meta Tags
		 *
		 * @static
		 * @access public
		 * @since 2.0.5
		 * @param {array} $post
		 * @param {array} $attributes
		 * @return string
		 */
		public function get_primary_meta_tags_data( $post, $attributes ) {
			$output = '';
			$get_postType = isset( $attributes['pPostType'] ) ? $attributes['pPostType'] : 'post';
			$catFontAwesomeClass = (int) gutentor_get_options( 'gutentor_font_awesome_version' ) === 4 ? 'fa fa-tags' : 'fas fa-tags';
			if ( isset( $attributes['pOnTagMeta1'] ) && $attributes['pOnTagMeta1'] ) {
				if($get_postType === 'product'){
					$terms = wp_get_post_terms( $post->ID, 'product_tag' );
					if( count($terms) > 0 ){
					$tag_output = array();
					$output             .= '<div class="gutentor-meta-categories"><i class="' . $catFontAwesomeClass . '"></i>';
					foreach ( $terms as $term ) {
						$tag_output[]             = '<a href="'.get_tag_link( $term->term_id ).'" rel="tag">'.$term->name.'</a>';
					}
					$output .= implode( ', ', $tag_output );
					$output             .= '</div>';
					}

				}
				else{
					$tags_list = get_the_tag_list( esc_html__( ', ', 'gutentor' ) );
					if ( $tags_list ) {
						$output             .= '<div class="gutentor-meta-categories"><i class="' . $catFontAwesomeClass . '"></i>' . $tags_list . '</div>';
					}
				}

			}
			return $output;

		}

		/**
		 * Secondary Categories
		 *
		 * @static
		 * @access public
		 * @since 2.0.5
		 * @param {array} $post
		 * @param {array} $attributes
		 * @return string
		 */
		public function get_secondary_meta_category_data( $post, $attributes ) {
			$output = '';
			if ( isset( $attributes['pOnCatMeta2'] ) && $attributes['pOnCatMeta2'] ) {
				$categories_list = get_the_category_list( esc_html__( ', ', 'gutentor' ) );
				if ( $categories_list ) {
					$catFontAwesomeClass = (int) gutentor_get_options( 'gutentor_font_awesome_version' ) === 4 ? 'fa fa-tags' : 'fas fa-tags';
					$output             .= '<div class="gutentor-meta-categories"><i class="' . $catFontAwesomeClass . '"></i>' . $categories_list . '</div>';
				}
			}
			return $output;

		}

		/**
		 * Secondary Tags
		 *
		 * @static
		 * @access public
		 * @since 2.0.5
		 * @param {array} $post
		 * @param {array} $attributes
		 * @return string
		 */
		public function get_secondary_meta_tag_data( $post, $attributes ) {
			$output = '';
			$get_postType = isset( $attributes['pPostType'] ) ? $attributes['pPostType'] : 'post';
			$catFontAwesomeClass = (int) gutentor_get_options( 'gutentor_font_awesome_version' ) === 4 ? 'fa fa-tags' : 'fas fa-tags';
			if ( isset( $attributes['pOnTagMeta2'] ) && $attributes['pOnTagMeta2'] ) {
				if($get_postType === 'product'){
					$terms = wp_get_post_terms( $post->ID, 'product_tag' );
					if( count($terms) > 0 ){
					$tag_output = array();
					$output             .= '<div class="gutentor-meta-categories"><i class="' . $catFontAwesomeClass . '"></i>';
					foreach ( $terms as $term ) {
						$tag_output[]             = '<a href="'.get_tag_link( $term->term_id ).'" rel="tag">'.$term->name.'</a>';
					}
					$output .= implode( ', ', $tag_output );
					$output             .= '</div>';
					}

				}
				else{
					$tags_list = get_the_tag_list( esc_html__( ', ', 'gutentor' ) );
					if ( $tags_list ) {
						$output             .= '<div class="gutentor-meta-categories"><i class="' . $catFontAwesomeClass . '"></i>' . $tags_list . '</div>';
					}
				}

			}
			return $output;

		}

		/**
		 * Primary Entry Meta
		 *
		 * @static
		 * @access public
		 * @since 2.0.1
		 * @param {array} $post
		 * @param {array} $attributes
		 * @return string
		 */
		public function get_primary_meta_sorting_data( $post, $attributes ) {
			$meta_sorting = array_key_exists( 'pMeta1Sorting', $attributes ) ? $attributes['pMeta1Sorting'] : false;
			$output       = '';
			if ( ! $meta_sorting ) {
				return $output;
			}
			$output .= '<div class="gutentor-entry-meta gutentor-entry-meta-primary">';
			foreach ( $meta_sorting as $element ) :
				if ( ! ( array_key_exists( 'itemValue', $element ) ) ) {
					return $output;
				}
				switch ( $element['itemValue'] ) {
					case 'meta-author':
						$output .= $this->get_primary_meta_author_data( $post, $attributes );
						break;
					case 'meta-date':
						$output .= $this->get_primary_meta_date_data( $post, $attributes );
						break;
					case 'meta-category':
						$output .= $this->get_primary_meta_categories_data( $post, $attributes );
						break;
					case 'meta-tag':
						$output .= $this->get_primary_meta_tags_data( $post, $attributes );
						break;
					case 'meta-comment':
						$output .= $this->get_primary_meta_comment_data( $post, $attributes );
						break;
					default:
						$output .= '';
						break;
				}
			endforeach;
			$output .= '</div>';/*.entry-meta*/
			return $output;

		}

		/**
		 * Primary Entry Meta
		 *
		 * @static
		 * @access public
		 * @since 2.0.1
		 * @param {array} $post
		 * @param {array} $attributes
		 * @return string
		 */
		public function get_secondary_meta_sorting_data( $post, $attributes ) {
			$meta_sorting = array_key_exists( 'pMeta2Sorting', $attributes ) ? $attributes['pMeta2Sorting'] : false;
			$output       = '';
			if ( ! $meta_sorting ) {
				return $output;
			}
			$output .= '<div class="gutentor-entry-meta gutentor-entry-meta-secondary">';
			foreach ( $meta_sorting as $element ) :
				if ( ! ( array_key_exists( 'itemValue', $element ) ) ) {
					return $output;
				}
				switch ( $element['itemValue'] ) {
					case 'meta-author':
						$output .= $this->get_secondary_meta_author_data( $post, $attributes );
						break;
					case 'meta-date':
						$output .= $this->get_secondary_meta_date_data( $post, $attributes );
						break;
					case 'meta-category':
						$output .= $this->get_secondary_meta_category_data( $post, $attributes );
						break;
					case 'meta-tag':
						$output .= $this->get_secondary_meta_tag_data( $post, $attributes );
						break;
					case 'meta-comment':
						$output .= $this->get_secondary_meta_comment_data( $post, $attributes );
						break;
					default:
						$output .= '';
						break;
				}
			endforeach;
			$output .= '</div>';/*.entry-meta*/
			return $output;

		}

		/**
		 * Primary Meta
		 *
		 * @static
		 * @access public
		 * @since 2.0.1
		 * @param {array} $post
		 * @param {array} $attributes
		 * @return string
		 */
		public function get_primary_meta( $post, $attributes ) {
			$output = '';
			if ( isset( $attributes['pOnMeta1'] ) && $attributes['pOnMeta1'] ) {
				$output .= $this->get_primary_meta_sorting_data( $post, $attributes );
			}
			return $output;
		}

		/**
		 * Secondary Meta
		 *
		 * @static
		 * @access public
		 * @since 2.0.1
		 * @param {array} $post
		 * @param {array} $attributes
		 * @return string
		 */
		public function get_secondary_meta( $post, $attributes ) {
			$output = '';
			if ( isset( $attributes['pOnMeta2'] ) && $attributes['pOnMeta2'] ) {
				$output .= $this->get_secondary_meta_sorting_data( $post, $attributes );
			}
			return $output;
		}

		/**
		 * Button
		 *
		 * @static
		 * @access public
		 * @since 2.0.1
		 * @param {array} $post
		 * @param {array} $attributes
		 * @return string
		 */
		public function get_button( $post, $attributes ) {
			$output = '';
			if ( isset( $attributes['pOnBtn'] ) && $attributes['pOnBtn'] ) {
				$default_class = gutentor_concat_space( 'gutentor-button', 'gutentor-post-button' );
				$icon          = ( isset( $attributes['pBtnIcon'] ) && $attributes['pBtnIcon']['value'] ) ? '<i class="gutentor-button-icon ' . $attributes['pBtnIcon']['value'] . '" ></i>' : '';
				$icon_options  = ( isset( $attributes['pBtnIconOpt'] ) ) ? $attributes['pBtnIconOpt'] : '';
				$link_options  = ( isset( $attributes['pBtnLink'] ) ) ? $attributes['pBtnLink'] : '';
				$output       .= '<a class="' . gutentor_concat_space( $default_class, GutentorButtonOptionsClasses( $icon_options ) ) . '" ' . apply_filters( 'gutentor_save_link_attr', '', esc_url( get_permalink() ), $link_options ) . '>' . $icon . '<span>' . esc_html( $attributes['pBtnText'] ) . '</span></a>';
			}
			return $output;

		}

		
		/**
		 * Get Single block
		 *
		 * @param {string} $data
		 * @param {array}  $post
		 * @param {array}  $attributes
		 * @return {mix}
		 */
		public function p2_single_article( $post, $attributes, $index ) {
			$output              = '';
			$enable_post_format  = ( isset( $attributes['pOnPostFormatOpt'] ) ) ? $attributes['pOnPostFormatOpt'] : false;
			$post_format_pos     = ( isset( $attributes['pPostFormatPos'] ) ) ? $attributes['pPostFormatPos'] : false;
			$cat_pos             = ( isset( $attributes['pPostCatPos'] ) ) ? $attributes['pPostCatPos'] : false;
			$enable_featured_cat = ( isset( $attributes['pOnFeaturedCat'] ) ) ? $attributes['pOnFeaturedCat'] : false;
			$thumb_class         = has_post_thumbnail() ? '' : 'gutentor-post-no-thumb';
			$output             .= "<article class='" . apply_filters( 'gutentor_post_module_article_class', gutentor_concat_space( 'gutentor-post', $thumb_class, 'gutentor-post-item-' . $index ), $attributes ) . "'>";
			$output             .= '<div class="gutentor-post-item">';
			if ( has_post_thumbnail( $post->ID ) ) {
				$enable_overlayImage = false;
				$overlayImage        = ( isset( $attributes['pFImgOColor'] ) ) ? $attributes['pFImgOColor'] : false;
				if ( $overlayImage ) {
					$enable_overlayImage = ( isset( $attributes['pFImgOColor']['enable'] ) ) ? $attributes['pFImgOColor']['enable'] : false;
				}
                $url     = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ) , $attributes['pFImgSize'] );
				$overlay = $enable_overlayImage ? 'gutentor-overlay' : '';
				$output .= '<div class="' . gutentor_concat_space( 'gutentor-bg-image', 'gutentor-post-height', $overlay ) . '" style="background-image:url(' . esc_url($url[0]) . ')">';
				if ( $enable_post_format && $this->post_format_on_image_condition( $post_format_pos ) ) {
					$output .= $this->get_post_format_data( $post, $attributes );
				}
				if ( $enable_featured_cat && $this->categories_on_image_condition( $cat_pos ) ) {
					$output .= $this->get_post_module_categories_collection( $post, $attributes );
				}
				$output .= '<div class="gutentor-post-content">';
				$output .= $this->get_primary_meta( $post, $attributes );
				if ( $enable_post_format && $post_format_pos === 'gutentor-pf-pos-before-title' ) {

					$output .= $this->get_post_format_data( $post, $attributes );
				}
				if ( $enable_featured_cat && $cat_pos === 'gutentor-cat-pos-before-title' ) {

					$output .= $this->get_post_module_categories_collection( $post, $attributes );
				}
				$output .= $this->get_title( $post, $attributes );
				if ( $enable_post_format && $post_format_pos === 'gutentor-pf-pos-before-ct-box' ) {

					$output .= $this->get_post_format_data( $post, $attributes );
				}
				if ( $enable_featured_cat && $cat_pos === 'gutentor-cat-pos-before-ct-box' ) {

					$output .= $this->get_post_module_categories_collection( $post, $attributes );
				}
				$output .= $this->get_description( $post, $attributes );
				$output .= $this->get_secondary_meta( $post, $attributes );
				if ( $enable_post_format && $post_format_pos === 'gutentor-pf-pos-before-button' ) {

					$output .= $this->get_post_format_data( $post, $attributes );
				}
				if ( $enable_featured_cat && $cat_pos === 'gutentor-cat-pos-before-button' ) {

					$output .= $this->get_post_module_categories_collection( $post, $attributes );
				}
				$output .= $this->get_button( $post, $attributes );
				$output .= '</div>';/*.gutentor-post-content*/
				$output .= '</div>';/*.gutentor-bg-image*/
			} else {
				$output .= '<div class="gutentor-post-height">';
				if ( $enable_post_format && $this->post_format_on_image_condition( $post_format_pos ) ) {
					$output .= $this->get_post_format_data( $post, $attributes );
				}
				if ( $enable_featured_cat && $this->categories_on_image_condition( $cat_pos ) ) {
					$output .= $this->get_post_module_categories_collection( $post, $attributes );
				}
				$output .= '<div class="gutentor-post-content">';
				$output .= $this->get_primary_meta( $post, $attributes );
				if ( $enable_post_format && $post_format_pos === 'gutentor-pf-pos-before-title' ) {

					$output .= $this->get_post_format_data( $post, $attributes );
				}
				if ( $enable_featured_cat && $cat_pos === 'gutentor-cat-pos-before-title' ) {

					$output .= $this->get_post_module_categories_collection( $post, $attributes );
				}
				$output .= $this->get_title( $post, $attributes );
				if ( $enable_post_format && $post_format_pos === 'gutentor-pf-pos-before-ct-box' ) {

					$output .= $this->get_post_format_data( $post, $attributes );
				}
				if ( $enable_featured_cat && $cat_pos === 'gutentor-cat-pos-before-ct-box' ) {

					$output .= $this->get_post_module_categories_collection( $post, $attributes );
				}
				$output .= $this->get_description( $post, $attributes );
				$output .= $this->get_secondary_meta( $post, $attributes );
				if ( $enable_post_format && $post_format_pos === 'gutentor-pf-pos-before-button' ) {

					$output .= $this->get_post_format_data( $post, $attributes );
				}
				if ( $enable_featured_cat && $cat_pos === 'gutentor-cat-pos-before-button' ) {

					$output .= $this->get_post_module_categories_collection( $post, $attributes );
				}
				$output .= $this->get_button( $post, $attributes );
				$output .= '</div>';/*.gutentor-post-content*/
				$output .= '</div>';/*.gutentor-post-height*/
			}
			$output .= '</div>';/*.gutentor-post-item*/
			$output .= '</article>';/*.article*/
			return $output;

		}



		/**
		 * Get Categories Data
		 *
		 * @param {mix} $post_id
		 * @return string
		 */
		function get_post_module_categories_data( $post_id = false ) {

			global $wp_rewrite;
			$categories = get_the_category( $post_id );
			$rel        = ( is_object( $wp_rewrite ) && $wp_rewrite->using_permalinks() ) ? 'rel="category tag"' : 'rel="category"';
			$i          = 0;
			$cat_list   = '';

			foreach ( $categories as $category ) {
				if ( 0 <= $i ) {
					$cat_list .= '<a href="' . get_category_link( $category->term_id ) . ' " class="post-category gutentor-cat-' . $category->slug . '" ' . $rel . '>' . $category->name . '</a>';
				}
				$cat_list .= ' ';
				++$i;
			}
			return $cat_list;
		}


		/**
		 * Get Categories Data
		 *
		 * @param {mix} $post_id
		 * @return string
		 */
		function get_post_featured_module_categories_data( $post_id = false ) {

			global $wp_rewrite;
			$categories = get_the_category( $post_id );
			$rel        = ( is_object( $wp_rewrite ) && $wp_rewrite->using_permalinks() ) ? 'rel="category tag"' : 'rel="category"';
			$i          = 0;
			$cat_list   = '';

			foreach ( $categories as $category ) {
				if ( 0 <= $i ) {
					$cat_list .= '<a href="' . get_category_link( $category->term_id ) . ' " class="post-featured-category gutentor-cat-' . $category->slug . '" ' . $rel . '>' . $category->name . '</a>';
				}
				$cat_list .= ' ';
				++$i;
			}
			return $cat_list;
		}

		/**
		 * Get Category Meta
		 *
		 * @param {mix} $post_id
		 * @return string
		 */
		function get_post_module_categories_collection( $post, $attributes ) {
			$output          = '';
			$categories_list = get_the_category_list( esc_html__( ' ', 'gutentor' ) );
			if ( $categories_list ) {
				$output = '<div class="gutentor-categories">' . $this->get_post_module_categories_data( $post->ID ) . '</div>';
			}
			return $output;
		}

		/**
		 * Get post Format Data
		 *
		 * @param {mix} $post_id
		 * @return string
		 */
		function get_post_format_data( $post, $attributes ) {
			$enable_post_format = false;
			$output             = '';
			if ( ! gutentor_check_post_format_support_enable() ) {
				return $output;
			}
			if ( isset( $attributes['pOnPostFormatOpt'] ) && $attributes['pOnPostFormatOpt'] ) {
				$enable_post_format = $attributes['pOnPostFormatOpt'];
			}
			if ( ! $enable_post_format ) {
				return $output;
			}
			$post_format       = get_post_format() ? : 'standard';
			$string_icon       = gutentor_get_post_format_icon( $post_format );
			$decoded_icon      = json_decode( $string_icon );
			$icon              = is_null( $decoded_icon ) ? $string_icon : $decoded_icon;
			$icon              = ( gettype( $icon ) === 'object' ) ? $decoded_icon->icon : $string_icon;
			$icon              = $icon ? $icon : 'fas fa-file-alt';
			$post_format_class = 'gutentor-post-format-' . $post_format;
			$output           .= '<span class="' . gutentor_concat_space( 'gutentor-post-format', $post_format_class ) . '"><i class="' . $icon . '"></i></span>';
			return $output;
		}

		/**
		 * Categories On Image
		 *
		 * @param {string} condition
		 * @return {boolean}
		 */
		function categories_on_image_condition( $condition ) {
			if ( ! $condition ) {
				return false;
			}
			$match_condition = array(
				'gutentor-cat-pos-img-top-left',
				'gutentor-cat-pos-img-top-right',
				'gutentor-cat-pos-img-bottom-left',
				'gutentor-cat-pos-img-bottom-right',
			);
			if ( in_array( $condition, $match_condition ) ) {
				return true;
			}
			return false;
		}

		/**
		 * Categories On Image
		 *
		 * @param {string} condition
		 * @return {boolean}
		 */
		function featured_post_categories_on_image_condition( $condition ) {
			if ( ! $condition ) {
				return false;
			}
			$match_condition = array(
				'gutentor-fp-cat-pos-img-top-left',
				'gutentor-fp-cat-pos-img-top-right',
				'gutentor-fp-cat-pos-img-bottom-left',
				'gutentor-fp-cat-pos-img-bottom-right',
			);
			if ( in_array( $condition, $match_condition ) ) {
				return true;
			}
			return false;
		}

		/**
		 * Post Format On Image
		 *
		 * @param {string} condition
		 * @return {boolean}
		 */
		function post_format_on_image_condition( $condition ) {
			if ( ! $condition ) {
				return false;
			}
			$match_condition = array(
				'gutentor-pf-pos-img-top-left',
				'gutentor-pf-pos-img-top-right',
				'gutentor-pf-pos-img-bottom-left',
				'gutentor-pf-pos-img-bottom-right',
			);
			if ( in_array( $condition, $match_condition ) ) {
				return true;
			}
			return false;
		}

		
		/**
		 * Post Format On Image
		 *
		 * @param {string} condition
		 * @return {boolean}
		 */
		function featured_post_format_on_image_condition( $condition ) {
			if ( ! $condition ) {
				return false;
			}
			$match_condition = array(
				'gutentor-fp-pf-pos-img-top-left',
				'gutentor-fp-pf-pos-img-top-right',
				'gutentor-fp-pf-pos-img-bottom-left',
				'gutentor-fp-pf-pos-img-bottom-right',
			);
			if ( in_array( $condition, $match_condition ) ) {
				return true;
			}
			return false;
		}

				/**
		 * Title
		 *
		 * @static
		 * @access public
		 * @since 2.0.1
		 * @param {array} $post
		 * @param {array} $attributes
		 * @return string
		 */
		public function get_featured_post_title( $post, $attributes ) {
			$output = '';
			if ( isset( $attributes['pOnFPTitle'] ) && $attributes['pOnFPTitle'] ) {
				$title_tag = $attributes['pTitleTag'];
				$output   .= '<' . $title_tag . ' class="gutentor-post-featured-title">';
				$output   .= '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">';
				$output   .= get_the_title();
				$output   .= '</a>';
				$output   .= '</' . $title_tag . '>';
			}
			return $output;

		}


		/**
		 * Description
		 *
		 * @static
		 * @access public
		 * @since 2.0.1
		 * @param {array} $post
		 * @param {array} $attributes
		 * @return string
		 */
		public function get_featured_post_description( $post, $attributes ) {
			$output = '';
			if ( ! isset( $attributes['pFPExcerptLen'] ) || ! isset( $attributes['pOnFPDesc'] ) ) {
				return $output;
			}
			if ( $attributes['pFPExcerptLen'] > 0 && $attributes['pOnFPDesc'] ) {
			    $in_words = (isset($attributes['pFPExcerptLenInWords']) && $attributes['pFPExcerptLenInWords']);
				$output .= '<div class="gutentor-post-featured-desc">';
				$output .= '<p>' . gutentor_get_excerpt_by_id( $post->ID, $attributes['pFPExcerptLen'], $in_words ) . '</p>';
				$output .= '</div>';
			}
			return $output;

		}

		
		/**
		 * Featured Image
		 *
		 * @static
		 * @access public
		 * @since 2.0.1
		 * @param {array} $post
		 * @param {array} $attributes
		 * @return string
		 */
		public function get_featured_post_featured_image( $post, $attributes ) {
			$output         = '';
			$overlay_obj    = ( isset( $attributes['pFPFImgOColor'] ) ) ? $attributes['pFPFImgOColor'] : false;
			$overlay_enable = ( $overlay_obj && array_key_exists( 'enable', $overlay_obj ) ) ? $attributes['pFPFImgOColor']['enable'] : false;

			$overlay              = ( $overlay_enable ) ? "<div class='overlay'></div>" : '';
			$overlay              = ( $overlay_enable ) ? 'gutentor-overlay' : '';
			if ( isset( $attributes['pOnFPFImg'] ) && $attributes['pOnFPFImg'] ) {
				$image_output = '';
				if ( has_post_thumbnail() ) {
						$image_output .= '<div class="' . gutentor_concat_space( 'gutentor-image-thumb', $overlay ) . '">';
						$image_output .= get_the_post_thumbnail( $post->ID , $attributes['pFPFImgSize'] , '' );
						$image_output .= $overlay;
						$image_output .= '</div>';
					$output .= apply_filters( 'gutentor_save_item_image_display_data', $image_output, get_permalink(), $attributes );
				}
			}
			return $output;

		}

		/**
		 * Primary Meta Date
		 *
		 * @static
		 * @access public
		 * @since 2.0.5
		 * @param {array} $post
		 * @param {array} $attributes
		 * @return string
		 */
		public function get_featured_post_primary_meta_date_data( $post, $attributes ) {
			$output = '';
			if ( isset( $attributes['pOnFPDateMeta1'] ) && $attributes['pOnFPDateMeta1'] ) {
				$dateFontAwesomeClass = (int) gutentor_get_options( 'gutentor_font_awesome_version' ) === 4 ? 'fa fa-calendar' : 'far fa-calendar-alt';
				$output              .= '<div class="posted-on"><i class="' . $dateFontAwesomeClass . '"></i>';
				$output              .= '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . get_the_date() . '</a>';
				$output              .= '</div>';

			}
			return $output;
		}

		/**
		 * Secondary Meta Date
		 *
		 * @static
		 * @access public
		 * @since 2.0.5
		 * @param {array} $post
		 * @param {array} $attributes
		 * @return string
		 */
		public function get_featured_post_secondary_meta_date_data( $post, $attributes ) {
			$output = '';
			if ( isset( $attributes['pOnFPDateMeta2'] ) && $attributes['pOnFPDateMeta2'] ) {

				$dateFontAwesomeClass = (int) gutentor_get_options( 'gutentor_font_awesome_version' ) === 4 ? 'fa fa-calendar' : 'far fa-calendar-alt';
				$output              .= '<div class="posted-on"><i class="' . $dateFontAwesomeClass . '"></i>';
				$output              .= '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . get_the_date() . '</a>';
				$output              .= '</div>';

			}
			return $output;

		}

		/**
		 * Primary Meta Comment
		 *
		 * @static
		 * @access public
		 * @since 2.0.5
		 * @param {array} $post
		 * @param {array} $attributes
		 * @return string
		 */
		public function get_featured_post_primary_meta_comment_data( $post, $attributes ) {
			$output = '';
			if ( ! is_object( $post ) ) {
				return $output;
			}
			$comment_data = wp_count_comments( $post->ID );

			if ( ! $comment_data->total_comments ) {
				return $output;
			}
			if ( isset( $attributes['pOnFPCommentMeta1'] ) && $attributes['pOnFPCommentMeta1'] ) {
				$commentFontAwesomeClass = (int) gutentor_get_options( 'gutentor_font_awesome_version' ) === 4 ? 'fa fa-comment' : 'fas fa-comment';
				$output                 .= '<div class="comments-link"><i class="' . $commentFontAwesomeClass . '"></i>';
				$output                 .= $comment_data->total_comments;
				$output                 .= '</div>';

			}
			return $output;
		}

		/**
		 * Secondary Meta Comment
		 *
		 * @static
		 * @access public
		 * @since 2.0.5
		 * @param {array} $post
		 * @param {array} $attributes
		 * @return string
		 */
		public function get_featured_post_secondary_meta_comment_data( $post, $attributes ) {
			$output       = '';
			$comment_data = wp_count_comments( $post->ID );

			if ( ! $comment_data->total_comments ) {
				return $output;
			}
			if ( isset( $attributes['pOnFPCommentMeta2'] ) && $attributes['pOnFPCommentMeta2'] ) {
				$commentFontAwesomeClass = (int) gutentor_get_options( 'gutentor_font_awesome_version' ) === 4 ? 'fa fa-comment' : 'fas fa-comment';
				$output                 .= '<div class="comments-link"><i class="' . $commentFontAwesomeClass . '"></i>';
				$output                 .= $comment_data->total_comments;
				$output                 .= '</div>';

			}
			return $output;
		}

		/**
		 * Primary Meta Author
		 *
		 * @static
		 * @access public
		 * @since 2.0.5
		 * @param {array} $post
		 * @param {array} $attributes
		 * @return string
		 */
		public function get_featured_post_primary_meta_author_data( $post, $attributes ) {
			$output = '';
			if ( isset( $attributes['pOnFPAuthorMeta1'] ) && $attributes['pOnFPAuthorMeta1'] ) {
				$authorFontAwesomeClass = (int) gutentor_get_options( 'gutentor_font_awesome_version' ) === 4 ? 'fa fa-user' : 'far fa-user';
				$output                .= '<div class="author vcard"><i class="' . $authorFontAwesomeClass . '"></i>';
				$output                .= '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . get_the_author() . '</a>';
				$output                .= '</div>';
			}
			return $output;

		}

		/**
		 * Secondary Meta Author
		 *
		 * @static
		 * @access public
		 * @since 2.0.5
		 * @param {array} $post
		 * @param {array} $attributes
		 * @return string
		 */
		public function get_featured_post_secondary_meta_author_data( $post, $attributes ) {
			$output = '';
			if ( isset( $attributes['pOnFPAuthorMeta2'] ) && $attributes['pOnFPAuthorMeta2'] ) {
				$authorFontAwesomeClass = (int) gutentor_get_options( 'gutentor_font_awesome_version' ) === 4 ? 'fa fa-user' : 'far fa-user';
				$output                .= '<div class="author vcard"><i class="' . $authorFontAwesomeClass . '"></i>';
				$output                .= '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . get_the_author() . '</a>';
				$output                .= '</div>';
			}
			return $output;

		}

		/**
		 * Primary Meta Categories
		 *
		 * @static
		 * @access public
		 * @since 2.0.5
		 * @param {array} $post
		 * @param {array} $attributes
		 * @return string
		 */
		public function get_featured_post_primary_meta_categories_data( $post, $attributes ) {
			$output = '';
			if ( isset( $attributes['pOnFPCatMeta1'] ) && $attributes['pOnFPCatMeta1'] ) {
				$categories_list = get_the_category_list( esc_html__( ', ', 'gutentor' ) );
				if ( $categories_list ) {
					$catFontAwesomeClass = (int) gutentor_get_options( 'gutentor_font_awesome_version' ) === 4 ? 'fa fa-tags' : 'fas fa-tags';
					$output             .= '<div class="gutentor-meta-categories"><i class="' . $catFontAwesomeClass . '"></i>' . $categories_list . '</div>';
				}
			}
			return $output;

		}

		/**
		 * Secondary Categories
		 *
		 * @static
		 * @access public
		 * @since 2.0.5
		 * @param {array} $post
		 * @param {array} $attributes
		 * @return string
		 */
		public function get_featured_post_secondary_meta_category_data( $post, $attributes ) {
			$output = '';
			if ( isset( $attributes['pOnFPCatMeta2'] ) && $attributes['pOnFPCatMeta2'] ) {
				$categories_list = get_the_category_list( esc_html__( ', ', 'gutentor' ) );
				if ( $categories_list ) {
					$catFontAwesomeClass = (int) gutentor_get_options( 'gutentor_font_awesome_version' ) === 4 ? 'fa fa-tags' : 'fas fa-tags';
					$output             .= '<div class="gutentor-meta-categories"><i class="' . $catFontAwesomeClass . '"></i>' . $categories_list . '</div>';
				}
			}
			return $output;

		}

		/**
		 * Primary Entry Meta
		 *
		 * @static
		 * @access public
		 * @since 2.0.1
		 * @param {array} $post
		 * @param {array} $attributes
		 * @return string
		 */
		public function get_featured_post_primary_meta_sorting_data( $post, $attributes ) {
			$meta_sorting = array_key_exists( 'pFPMeta1Sorting', $attributes ) ? $attributes['pFPMeta1Sorting'] : false;
			$output       = '';
			if ( ! $meta_sorting ) {
				return $output;
			}
			$output .= '<div class="gutentor-entry-meta gutentor-entry-meta-featured-primary">';
			foreach ( $meta_sorting as $element ) :
				if ( ! ( array_key_exists( 'itemValue', $element ) ) ) {
					return $output;
				}
				switch ( $element['itemValue'] ) {
					case 'meta-author':
						$output .= $this->get_featured_post_primary_meta_author_data( $post, $attributes );
						break;
					case 'meta-date':
						$output .= $this->get_featured_post_primary_meta_date_data( $post, $attributes );
						break;
					case 'meta-category':
						$output .= $this->get_featured_post_primary_meta_categories_data( $post, $attributes );
						break;
					case 'meta-comment':
						$output .= $this->get_featured_post_primary_meta_comment_data( $post, $attributes );
						break;
					default:
						$output .= '';
						break;
				}
			endforeach;
			$output .= '</div>';/*.entry-meta*/
			return $output;

		}

		/**
		 * Primary Entry Meta
		 *
		 * @static
		 * @access public
		 * @since 2.0.1
		 * @param {array} $post
		 * @param {array} $attributes
		 * @return string
		 */
		public function get_featured_post_secondary_meta_sorting_data( $post, $attributes ) {
			$meta_sorting = array_key_exists( 'pFPMeta2Sorting', $attributes ) ? $attributes['pFPMeta2Sorting'] : false;
			$output       = '';
			if ( ! $meta_sorting ) {
				return $output;
			}
			$output .= '<div class="gutentor-entry-meta gutentor-entry-meta-featured-secondary">';
			foreach ( $meta_sorting as $element ) :
				if ( ! ( array_key_exists( 'itemValue', $element ) ) ) {
					return $output;
				}
				switch ( $element['itemValue'] ) {
					case 'meta-author':
						$output .= $this->get_featured_post_secondary_meta_author_data( $post, $attributes );
						break;
					case 'meta-date':
						$output .= $this->get_featured_post_secondary_meta_date_data( $post, $attributes );
						break;
					case 'meta-category':
						$output .= $this->get_featured_post_secondary_meta_category_data( $post, $attributes );
						break;
					case 'meta-comment':
						$output .= $this->get_featured_post_secondary_meta_comment_data( $post, $attributes );
						break;
					default:
						$output .= '';
						break;
				}
			endforeach;
			$output .= '</div>';/*.entry-meta*/
			return $output;

		}

		/**
		 * Primary Meta
		 *
		 * @static
		 * @access public
		 * @since 2.0.1
		 * @param {array} $post
		 * @param {array} $attributes
		 * @return string
		 */
		public function get_featured_post_primary_meta( $post, $attributes ) {
			$output = '';
			if ( isset( $attributes['pOnFPMeta1'] ) && $attributes['pOnFPMeta1'] ) {
				$output .= $this->get_featured_post_primary_meta_sorting_data( $post, $attributes );
			}
			return $output;
		}

		/**
		 * Secondary Meta
		 *
		 * @static
		 * @access public
		 * @since 2.0.1
		 * @param {array} $post
		 * @param {array} $attributes
		 * @return string
		 */
		public function get_featured_post_secondary_meta( $post, $attributes ) {
			$output = '';
			if ( isset( $attributes['pOnFPMeta2'] ) && $attributes['pOnFPMeta2'] ) {
				$output .= $this->get_featured_post_secondary_meta_sorting_data( $post, $attributes );
			}
			return $output;
		}

		/**
		 * Button
		 *
		 * @static
		 * @access public
		 * @since 2.0.1
		 * @param {array} $post
		 * @param {array} $attributes
		 * @return string
		 */
		public function get_featured_post_button( $post, $attributes ) {
			$output = '';
			if ( isset( $attributes['pOnFPBtn'] ) && $attributes['pOnFPBtn'] ) {
				$default_class = gutentor_concat_space( 'gutentor-button', 'gutentor-post-featured-button' );
				$icon          = ( isset( $attributes['pFPBtnIcon'] ) && $attributes['pFPBtnIcon']['value'] ) ? '<i class="gutentor-button-icon ' . $attributes['pFPBtnIcon']['value'] . '" ></i>' : '';
				$icon_options  = ( isset( $attributes['pFPBtnIconOpt'] ) ) ? $attributes['pFPBtnIconOpt'] : '';
				$link_options  = ( isset( $attributes['pFPBtnLink'] ) ) ? $attributes['pFPBtnLink'] : '';
				$output       .= '<a class="' . gutentor_concat_space( $default_class, GutentorButtonOptionsClasses( $icon_options ) ) . '" ' . apply_filters( 'gutentor_save_link_attr', '', esc_url( get_permalink() ), $link_options ) . '>' . $icon . '<span>' . esc_html( $attributes['pFPBtnText'] ) . '</span></a>';
			}
			return $output;

		}


		/**
		 * Get Category Meta
		 *
		 * @param {mix} $post_id
		 * @return string
		 */
		function get_featured_post_categories_collection( $post, $attributes ) {
			$output          = '';
			$categories_list = get_the_category_list( esc_html__( ' ', 'gutentor' ) );
			if ( $categories_list ) {
				$output = '<div class="gutentor-categories gutentor-featured-post-categories">' . $this->get_post_featured_module_categories_data( $post->ID ) . '</div>';
			}
			return $output;
		}

		/**
		 * Get post Format Data
		 *
		 * @param {mix} $post_id
		 * @return string
		 */
		function get_featured_post_format_data( $post, $attributes ) {
			$enable_post_format = false;
			$output             = '';
			if ( ! gutentor_check_post_format_support_enable() ) {
				return $output;
			}
			if ( isset( $attributes['pOnFPPostFormatOpt'] ) && $attributes['pOnFPPostFormatOpt'] ) {
				$enable_post_format = $attributes['pOnFPPostFormatOpt'];
			}
			if ( ! $enable_post_format ) {
				return $output;
			}
			$post_format       = get_post_format() ? : 'standard';
			$string_icon       = gutentor_get_post_format_icon( $post_format );
			$decoded_icon      = json_decode( $string_icon );
			$icon              = is_null( $decoded_icon ) ? $string_icon : $decoded_icon;
			$icon              = ( gettype( $icon ) === 'object' ) ? $decoded_icon->icon : $string_icon;
			$icon              = $icon ? $icon : 'fas fa-file-alt';
			$post_format_class = 'gutentor-post-format-' . $post_format;
			$output           .= '<span class="' . gutentor_concat_space( 'gutentor-post-featured-format', $post_format_class ) . '"><i class="' . $icon . '"></i></span>';
			return $output;
		}


		
		/**
		 * Get Featured Single item data
		 *
		 * @param {string} $data
		 * @param {array}  $post
		 * @param {array}  $attributes
		 * @return {mix}
		 */
		public function p6_featured_single_article( $post, $attributes, $index ) {
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
				$enable_overlayImage = false;
				$overlayImage        = ( isset( $attributes['pFPFImgOColor'] ) ) ? $attributes['pFPFImgOColor'] : false;
				if ( $overlayImage ) {
					$enable_overlayImage = ( isset( $attributes['pFPFImgOColor']['enable'] ) ) ? $attributes['pFPFImgOColor']['enable'] : false;
				}
                $url     = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ) , $attributes['pFPFImgSize'] );
				$overlay = $enable_overlayImage ? 'gutentor-overlay' : '';
				$output .= '<div class="' . gutentor_concat_space( 'gutentor-post-featured-height','gutentor-bg-image', $overlay ) . '" style="background-image:url(' . esc_url($url[0]) . ')">';
				if ( $enable_post_format && $this->featured_post_format_on_image_condition( $post_format_pos ) ) {
					$output .= $this->get_featured_post_format_data( $post, $attributes );
				}
				if ( $enable_featured_cat && $this->featured_post_categories_on_image_condition( $cat_pos ) ) {
					$output .= $this->get_featured_post_categories_collection( $post, $attributes );
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
				$output .= '</div>';/*.gutentor-bg-image*/
			} else {
				$output .= '<div class="gutentor-post-featured-height">';
				if ( $enable_post_format && $this->featured_post_format_on_image_condition( $post_format_pos ) ) {
					$output .= $this->get_featured_post_format_data( $post, $attributes );
				}
				if ( $enable_featured_cat && $this->featured_post_categories_on_image_condition( $cat_pos ) ) {
					$output .= $this->get_featured_post_categories_collection( $post, $attributes );
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
				$output .= '</div>';/*.gutentor-post-height*/
			}
			$output .= '</div>';/*.gutentor-post-featured-item*/
			$output .= '</article>';/*.article*/
			return $output;

		}

		/**
		 * Get Single block
		 *
		 * @param {string} $data
		 * @param {array}  $post
		 * @param {array}  $attributes
		 * @return {mix}
		 */
		public function p6_single_article( $post, $attributes, $index ) {

			$query_sorting       = array_key_exists( 'blockSortableItems', $attributes ) ? $attributes['blockSortableItems'] : false;
			$enable_featured_image  = ( isset( $attributes['pOnFImg'] ) ) ? $attributes['pOnFImg'] : false;
			$enable_post_format  = ( isset( $attributes['pOnPostFormatOpt'] ) ) ? $attributes['pOnPostFormatOpt'] : false;
			$post_format_pos     = ( isset( $attributes['pPostFormatPos'] ) ) ? $attributes['pPostFormatPos'] : false;
			$cat_pos             = ( isset( $attributes['pPostCatPos'] ) ) ? $attributes['pPostCatPos'] : false;
			$enable_featured_cat = ( isset( $attributes['pOnFeaturedCat'] ) ) ? $attributes['pOnFeaturedCat'] : false;
			$thumb_class         = (has_post_thumbnail() && $enable_featured_image) ? '' : 'gutentor-post-no-thumb';

			$output              = '';
			$output             .= "<article class='" . apply_filters( 'gutentor_post_module_article_class', gutentor_concat_space( 'gutentor-post','gutentor-post-normal',$thumb_class, 'gutentor-post-item-' . $index ), $attributes ) . "'>";
			$output             .= '<div class="gutentor-post-item">';
			if ( $enable_featured_image && has_post_thumbnail( $post->ID ) ) {
				$output .= '<div class="gutentor-post-image-box">';
				$output .= $this->get_featured_image( $post, $attributes );
				if ( $enable_post_format && $this->post_format_on_image_condition( $post_format_pos ) ) {
					$output .= $this->get_post_format_data( $post, $attributes );
				}
				if ( $enable_featured_cat && $this->categories_on_image_condition( $cat_pos ) ) {
					$output .= $this->get_post_module_categories_collection( $post, $attributes );
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

									$output .= $this->get_post_format_data( $post, $attributes );
								}
								if ( $enable_featured_cat && $cat_pos === 'gutentor-cat-pos-before-title' ) {

									$output .= $this->get_post_module_categories_collection( $post, $attributes );
								}
								$output .= $this->get_title( $post, $attributes );
								$output .= '</div>';
							} else {

								$output .= $this->get_title( $post, $attributes );
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

									$output .= $this->get_post_format_data( $post, $attributes );
								}
								if ( $enable_featured_cat && $cat_pos === 'gutentor-cat-pos-before-ct-box' ) {

									$output .= $this->get_post_module_categories_collection( $post, $attributes );
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

									$output .= $this->get_post_format_data( $post, $attributes );
								}
								if ( $enable_featured_cat && $cat_pos === 'gutentor-cat-pos-before-button' ) {

									$output .= $this->get_post_module_categories_collection( $post, $attributes );
								}
								$output .= $this->get_button( $post, $attributes );
								$output .= '</div>';
							} else {

								$output .= $this->get_button( $post, $attributes );
							}
							break;
						default:
							$output .= '';
							break;
					}
				endforeach;
			endif;
			$output .= '</div>';/*.gutentor-post-content*/
			$output .= '</div>';/*.gutentor-post-item*/
			$output .= '</article>';/*.article*/
			return $output;

		}

		
	}
}

