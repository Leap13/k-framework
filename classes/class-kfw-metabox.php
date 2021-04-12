<?php
/**
 * Metabox Class
 *
 * @package Kemet Framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

if ( ! class_exists( 'KFW_Metabox' ) ) {

	/**
	 *
	 * Metabox Class
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	class KFW_Metabox extends KFW_Abstract {

		/**
		 * Unique key
		 *
		 * @var string
		 */
		public $unique = '';

		/**
		 * Abstract type
		 *
		 * @var string
		 */
		public $abstract = 'metabox';

		/**
		 * Pre fields
		 *
		 * @var array
		 */
		public $pre_fields = array();

		/**
		 * Sections
		 *
		 * @var array
		 */
		public $sections = array();

		/**
		 * Post types
		 *
		 * @var array
		 */
		public $post_type = array();

		/**
		 * Args
		 *
		 * @var array
		 */
		public $args = array(
			'title'              => '',
			'post_type'          => 'post',
			'data_type'          => 'serialize',
			'context'            => 'advanced',
			'priority'           => 'default',
			'exclude_post_types' => array(),
			'page_templates'     => '',
			'post_formats'       => '',
			'show_restore'       => false,
			'enqueue_webfont'    => true,
			'async_webfont'      => false,
			'output_css'         => true,
			'theme'              => 'dark',
			'class'              => '',
			'defaults'           => array(),
		);

		/**
		 * Constructor
		 *
		 * @param string $key unique key.
		 * @param array  $params parameterds.
		 */
		public function __construct( $key, $params = array() ) {

			$this->unique         = $key;
			$this->args           = apply_filters( "kfw_{$this->unique}_args", wp_parse_args( $params['args'], $this->args ), $this );
			$this->sections       = apply_filters( "kfw_{$this->unique}_sections", $params['sections'], $this );
			$this->post_type      = ( is_array( $this->args['post_type'] ) ) ? $this->args['post_type'] : array_filter( (array) $this->args['post_type'] );
			$this->post_formats   = ( is_array( $this->args['post_formats'] ) ) ? $this->args['post_formats'] : array_filter( (array) $this->args['post_formats'] );
			$this->page_templates = ( is_array( $this->args['page_templates'] ) ) ? $this->args['page_templates'] : array_filter( (array) $this->args['page_templates'] );
			$meta_sections        = wp_list_sort( $this->sections, 'priority_num' );
			$this->pre_fields     = $this->pre_fields( $meta_sections );

			add_action( 'add_meta_boxes', array( &$this, 'add_meta_box' ) );
			add_action( 'save_post', array( &$this, 'save_meta_box' ) );
			add_action( 'edit_attachment', array( &$this, 'save_meta_box' ) );

			if ( ! empty( $this->page_templates ) || ! empty( $this->post_formats ) || ! empty( $this->args['class'] ) ) {
				foreach ( $this->post_type as $post_type ) {
					add_filter( 'postbox_classes_' . $post_type . '_' . $this->unique, array( &$this, 'add_metabox_classes' ) );
				}
			}

			// wp enqeueu for typography and output css.
			parent::__construct();

		}

		/**
		 * Instance
		 *
		 * @param string $key unique key.
		 * @param array  $params parameters.
		 * @return object
		 */
		public static function instance( $key, $params = array() ) {

			return new self( $key, $params );
		}

		/**
		 * Pre fields
		 *
		 * @param array $sections sections.
		 * @return array
		 */
		public function pre_fields( $sections ) {

			$result = array();

			foreach ( $sections as $key => $section ) {
				if ( ! empty( $section['fields'] ) ) {
					foreach ( $section['fields'] as $field ) {
						$result[] = $field;
					}
				}
			}

			return $result;
		}

		/**
		 * Add metabox classes
		 *
		 * @param array $classes classes.
		 * @return array
		 */
		public function add_metabox_classes( $classes ) {

			global $post;

			if ( ! empty( $this->post_formats ) ) {

				$saved_post_format = ( is_object( $post ) ) ? get_post_format( $post ) : false;
				$saved_post_format = ( ! empty( $saved_post_format ) ) ? $saved_post_format : 'default';

				$classes[] = 'kfw-post-formats';

				// Sanitize post format for standard to default.
				if ( false !== ( $key = array_search( 'standard', $this->post_formats ) ) ) { // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.FoundInControlStructure
					$this->post_formats[ $key ] = 'default';
				}

				foreach ( $this->post_formats as $format ) {
					$classes[] = 'kfw-post-format-' . $format;
				}

				if ( ! in_array( $saved_post_format, $this->post_formats ) ) {
					$classes[] = 'kfw-hide';
				} else {
					$classes[] = 'kfw-show';
				}
			}

			if ( ! empty( $this->page_templates ) ) {

				$saved_template = ( is_object( $post ) && ! empty( $post->page_template ) ) ? $post->page_template : 'default';

				$classes[] = 'kfw-page-templates';

				foreach ( $this->page_templates as $template ) {
					$classes[] = 'kfw-page-' . preg_replace( '/[^a-zA-Z0-9]+/', '-', strtolower( $template ) );
				}

				if ( ! in_array( $saved_template, $this->page_templates ) ) {
					$classes[] = 'kfw-hide';
				} else {
					$classes[] = 'kfw-show';
				}
			}

			if ( ! empty( $this->args['class'] ) ) {
				$classes[] = $this->args['class'];
			}

			return $classes;

		}

		/**
		 * Add metabox
		 *
		 * @param string $post_type meta post type.
		 * @return void
		 */
		public function add_meta_box( $post_type ) {

			if ( ! in_array( $post_type, $this->args['exclude_post_types'] ) ) {
				add_meta_box( $this->unique, $this->args['title'], array( &$this, 'add_meta_box_content' ), $this->post_type, $this->args['context'], $this->args['priority'], $this->args );
			}

		}

		/**
		 * Get default value
		 *
		 * @param array $field field.
		 * @return mixed
		 */
		public function get_default( $field ) {

			$default = ( isset( $field['id'] ) && isset( $this->args['defaults'][ $field['id'] ] ) ) ? $this->args['defaults'][ $field['id'] ] : null;
			$default = ( isset( $field['default'] ) ) ? $field['default'] : $default;

			return $default;
		}

		/**
		 * Get meta value
		 *
		 * @param array $field field.
		 * @return mixed
		 */
		public function get_meta_value( $field ) {

			global $post;

			$value = null;

			if ( is_object( $post ) && ! empty( $field['id'] ) ) {

				if ( 'serialize' !== $this->args['data_type'] ) {
					$meta  = get_post_meta( $post->ID, $field['id'] );
					$value = ( isset( $meta[0] ) ) ? $meta[0] : null;
				} else {
					$meta  = get_post_meta( $post->ID, $this->unique, true );
					$value = ( isset( $meta[ $field['id'] ] ) ) ? $meta[ $field['id'] ] : null;
				}
			}

			$default = $this->get_default( $field );
			$value   = ( isset( $value ) ) ? $value : $default;

			return $value;

		}

		/**
		 * Add metabox content
		 *
		 * @param object $post post.
		 * @param string $callback callback function.
		 * @return void
		 */
		public function add_meta_box_content( $post, $callback ) {

			global $post;

			$meta_sections = wp_list_sort( $this->sections, 'priority_num' );
			$has_nav       = ( count( $meta_sections ) > 1 && 'side' !== $this->args['context'] ) ? true : false;
			$show_all      = ( ! $has_nav ) ? ' kfw-show-all' : '';
			$errors        = ( is_object( $post ) ) ? get_post_meta( $post->ID, '_kfw_errors', true ) : array();
			$errors        = ( ! empty( $errors ) ) ? $errors : array();
			$theme         = ( $this->args['theme'] ) ? ' kfw-theme-' . $this->args['theme'] : '';

			if ( is_object( $post ) && ! empty( $errors ) ) {
				delete_post_meta( $post->ID, '_kfw_errors' );
			}

			wp_nonce_field( 'kfw_metabox_nonce', 'kfw_metabox_nonce' . $this->unique );

			echo wp_kses( '<div class="kfw kfw-metabox' . $theme . '">', kfw_allowed_html( array( 'div' ) ) );

			echo wp_kses( '<div class="kfw-wrapper' . $show_all . '">', kfw_allowed_html( array( 'div' ) ) );

			if ( $has_nav ) {

				echo wp_kses( '<div class="kfw-nav kfw-nav-metabox" data-unique="' . $this->unique . '">', kfw_allowed_html( array( 'div' ) ) );

				echo wp_kses( '<ul>', kfw_allowed_html( array( 'ul' ) ) );
				$tab_key       = 1;
				$meta_sections = wp_list_sort( $this->sections, 'priority_num' );
				foreach ( $meta_sections as $section ) {

					$tab_error = ( ! empty( $errors['sections'][ $tab_key ] ) ) ? '<i class="kfw-label-error kfw-error">!</i>' : '';
					$tab_icon  = ( ! empty( $section['icon'] ) ) ? '<i class="kfw-icon ' . $section['icon'] . '"></i>' : '';

					echo wp_kses( '<li><a href="#" data-section="' . $this->unique . '_' . $tab_key . '">' . $tab_icon . $section['title'] . $tab_error . '</a></li>', kfw_allowed_html( array( 'li', 'a', 'i' ) ) );

					$tab_key++;
				}
				echo wp_kses( '</ul>', kfw_allowed_html( array( 'ul' ) ) );

				echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

			}

			echo wp_kses( '<div class="kfw-content">', kfw_allowed_html( array( 'div' ) ) );

			echo wp_kses( '<div class="kfw-sections">', kfw_allowed_html( array( 'div' ) ) );

			$section_key   = 1;
			$meta_sections = wp_list_sort( $this->sections, 'priority_num' );
			foreach ( $meta_sections as $section ) {

				$onload = ( ! $has_nav ) ? ' kfw-onload' : '';

				echo wp_kses( '<div id="kfw-section-' . $this->unique . '_' . $section_key . '" class="kfw-section' . $onload . '">', kfw_allowed_html( array( 'div' ) ) );

				$section_icon  = ( ! empty( $section['icon'] ) ) ? '<i class="kfw-icon ' . $section['icon'] . '"></i>' : '';
				$section_title = ( ! empty( $section['title'] ) ) ? $section['title'] : '';

				echo wp_kses( ( $section_title || $section_icon ) ? '<div class="kfw-section-title"><h3>' . $section_icon . $section_title . '</h3></div>' : '', kfw_allowed_html( array( 'div', 'h3', 'i' ) ) );

				if ( ! empty( $section['fields'] ) ) {

					foreach ( $section['fields'] as $field ) {

						if ( ! empty( $field['id'] ) && ! empty( $errors['fields'][ $field['id'] ] ) ) {
							$field['_error'] = $errors['fields'][ $field['id'] ];
						}

						kfw::field( $field, $this->get_meta_value( $field ), $this->unique, 'metabox' );

					}
				} else {

					echo wp_kses( '<div class="kfw-no-option kfw-text-muted">' . esc_html__( 'No option provided by developer.', 'kfw' ) . '</div>', kfw_allowed_html( array( 'div' ) ) );

				}

				echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

				$section_key++;
			}

			echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

			echo wp_kses( '<div class="clear"></div>', kfw_allowed_html( array( 'div' ) ) );

			if ( ! empty( $this->args['show_restore'] ) ) {

				echo wp_kses( '<div class="kfw-restore-wrapper">', kfw_allowed_html( array( 'div' ) ) );
				echo wp_kses( '<label>', kfw_allowed_html( array( 'label' ) ) );
				echo wp_kses( '<input type="checkbox" name="' . $this->unique . '[_restore]" />', kfw_allowed_html( 'input' ) );
				echo wp_kses( '<span class="button kfw-button-restore">' . esc_html__( 'Restore', 'kfw' ) . '</span>', kfw_allowed_html( array( 'span' ) ) );
				echo wp_kses( '<span class="button kfw-button-cancel">', kfw_allowed_html( array( 'span' ) ) ) . sprintf( wp_kses( '<small>( %s )</small> %s', kfw_allowed_html( array( 'small' ) ) ), esc_html__( 'update post for restore ', 'kfw' ), esc_html__( 'Cancel', 'kfw' ) ) . '</span>';
				echo wp_kses( '</label>', kfw_allowed_html( array( 'label' ) ) );
				echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

			}

			echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

			echo ( $has_nav ) ? wp_kses( '<div class="kfw-nav-background"></div>', kfw_allowed_html( array( 'div' ) ) ) : '';

			echo wp_kses( '<div class="clear"></div>', kfw_allowed_html( array( 'div' ) ) );

			echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

			echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

		}

		/**
		 * Save meta
		 *
		 * @param int $post_id post id.
		 * @return mixed
		 */
		public function save_meta_box( $post_id ) {

			if ( ! wp_verify_nonce( kfw_get_var( 'kfw_metabox_nonce' . $this->unique ), 'kfw_metabox_nonce' ) ) {
				return $post_id;
			}

			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return $post_id;
			}

			$errors  = array();
			$request = kfw_get_var( $this->unique );

			if ( ! empty( $request ) ) {

				// ignore _nonce.
				if ( isset( $request['_nonce'] ) ) {
					unset( $request['_nonce'] );
				}

				// sanitize and validate.
				$section_key   = 1;
				$meta_sections = wp_list_sort( $this->sections, 'priority_num' );
				foreach ( $meta_sections as $section ) {

					if ( ! empty( $section['fields'] ) ) {

						foreach ( $section['fields'] as $field ) {

							if ( ! empty( $field['id'] ) ) {

								// sanitize.
								if ( ! empty( $field['sanitize'] ) ) {

									$sanitize                = $field['sanitize'];
									$value_sanitize          = isset( $request[ $field['id'] ] ) ? $request[ $field['id'] ] : '';
									$request[ $field['id'] ] = call_user_func( $sanitize, $value_sanitize );

								}

								// validate.
								if ( ! empty( $field['validate'] ) ) {

									$validate       = $field['validate'];
									$value_validate = isset( $request[ $field['id'] ] ) ? $request[ $field['id'] ] : '';
									$has_validated  = call_user_func( $validate, $value_validate );

									if ( ! empty( $has_validated ) ) {

											$errors['sections'][ $section_key ] = true;
											$errors['fields'][ $field['id'] ]   = $has_validated;
											$request[ $field['id'] ]            = $this->get_meta_value( $field );

									}
								}

								// auto sanitize.
								if ( ! isset( $request[ $field['id'] ] ) || is_null( $request[ $field['id'] ] ) ) {
									$request[ $field['id'] ] = '';
								}
							}
						}
					}

					$section_key++;
				}
			}

			$request = apply_filters( "kfw_{$this->unique}_save", $request, $post_id, $this );

			do_action( "kfw_{$this->unique}_save_before", $request, $post_id, $this );

			if ( empty( $request ) || ! empty( $request['_restore'] ) ) {

				if ( 'serialize' !== $this->args['data_type'] ) {
					foreach ( $request as $key => $value ) {
						delete_post_meta( $post_id, $key );
					}
				} else {
						delete_post_meta( $post_id, $this->unique );
				}
			} else {

				if ( 'serialize' !== $this->args['data_type'] ) {
					foreach ( $request as $key => $value ) {
						update_post_meta( $post_id, $key, $value );
					}
				} else {
					update_post_meta( $post_id, $this->unique, $request );
				}

				if ( ! empty( $errors ) ) {
					update_post_meta( $post_id, '_kfw_errors', $errors );
				}
			}

			do_action( "kfw_{$this->unique}_saved", $request, $post_id, $this );

			do_action( "kfw_{$this->unique}_save_after", $request, $post_id, $this );

		}
	}
}

