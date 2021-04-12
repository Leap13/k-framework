<?php
/**
 * Field: Image Select
 *
 * @package Kemet Framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

if ( ! class_exists( 'KFW_Field_Media' ) ) {

	/**
	 *
	 * Field: Media
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	class KFW_Field_Media extends KFW_Fields {

		/**
		 * Constructor
		 *
		 * @param array  $field field options.
		 * @param string $value value.
		 * @param string $unique key.
		 * @param string $where location.
		 * @param string $parent parent.
		 * @return void
		 */
		public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
			parent::__construct( $field, $value, $unique, $where, $parent );
		}

		/**
		 * Render field html
		 *
		 * @return void
		 */
		public function render() {

			$args = wp_parse_args(
				$this->field,
				array(
					'url'          => true,
					'preview'      => true,
					'library'      => array(),
					'button_title' => esc_html__( 'Upload', 'kfw' ),
					'remove_title' => esc_html__( 'Remove', 'kfw' ),
					'preview_size' => 'thumbnail',
				)
			);

			$default_values = array(
				'url'         => '',
				'id'          => '',
				'width'       => '',
				'height'      => '',
				'thumbnail'   => '',
				'alt'         => '',
				'title'       => '',
				'description' => '',
			);

			// fallback.
			if ( is_numeric( $this->value ) ) {

				$this->value = array(
					'id'        => $this->value,
					'url'       => wp_get_attachment_url( $this->value ),
					'thumbnail' => wp_get_attachment_image_src( $this->value, 'thumbnail', true )[0],
				);

			}

			$this->value = wp_parse_args( $this->value, $default_values );

			$library     = ( is_array( $args['library'] ) ) ? $args['library'] : array_filter( (array) $args['library'] );
			$library     = ( ! empty( $library ) ) ? implode( ',', $library ) : '';
			$preview_src = ( 'thumbnail' !== $args['preview_size'] ) ? $this->value['url'] : $this->value['thumbnail'];
			$hidden_url  = ( empty( $args['url'] ) ) ? ' hidden' : '';
			$hidden_auto = ( empty( $this->value['url'] ) ) ? ' hidden' : '';
			$placeholder = ( empty( $this->field['placeholder'] ) ) ? ' placeholder="' . esc_html__( 'No media selected', 'kfw' ) . '"' : '';

			echo wp_kses( $this->field_before(), kfw_allowed_html( 'all' ) );

			if ( ! empty( $args['preview'] ) ) {
				echo wp_kses( '<div class="kfw--preview' . esc_attr( $hidden_auto ) . '">', kfw_allowed_html( array( 'div' ) ) );
				echo wp_kses( '<div class="kfw-image-preview"><a href="#" class="kfw--remove fa fa-times"></a><img src="' . esc_url( $preview_src ) . '" class="kfw--src" /></div>', kfw_allowed_html( array( 'div', 'a', 'img' ) ) );
				echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );
			}

			echo wp_kses( '<div class="kfw--placeholder">', kfw_allowed_html( array( 'div' ) ) );
			echo wp_kses( '<input type="text" name="' . esc_attr( $this->field_name( '[url]' ) ) . '" value="' . esc_attr( $this->value['url'] ) . '" class="kfw--url' . esc_attr( $hidden_url ) . '" readonly="readonly"' . $this->field_attributes() . $placeholder . ' />', kfw_allowed_html( array( 'input' ) ) );
			echo wp_kses( '<a href="#" class="button button-primary kfw--button" data-library="' . esc_attr( $library ) . '" data-preview-size="' . esc_attr( $args['preview_size'] ) . '">' . wp_kses_post( $args['button_title'] ) . '</a>', kfw_allowed_html( array( 'a' ) ) );
			echo ( empty( $args['preview'] ) ) ? wp_kses( '<a href="#" class="button button-secondary kfw-warning-primary kfw--remove' . esc_attr( $hidden_auto ) . '">' . wp_kses_post( $args['remove_title'] ) . '</a>', kfw_allowed_html( array( 'a' ) ) ) : '';
			echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

			echo wp_kses( '<input type="hidden" name="' . esc_attr( $this->field_name( '[id]' ) ) . '" value="' . esc_attr( $this->value['id'] ) . '" class="kfw--id"/>', kfw_allowed_html( array( 'input' ) ) );
			echo wp_kses( '<input type="hidden" name="' . esc_attr( $this->field_name( '[width]' ) ) . '" value="' . esc_attr( $this->value['width'] ) . '" class="kfw--width"/>', kfw_allowed_html( array( 'input' ) ) );
			echo wp_kses( '<input type="hidden" name="' . esc_attr( $this->field_name( '[height]' ) ) . '" value="' . esc_attr( $this->value['height'] ) . '" class="kfw--height"/>', kfw_allowed_html( array( 'input' ) ) );
			echo wp_kses( '<input type="hidden" name="' . esc_attr( $this->field_name( '[thumbnail]' ) ) . '" value="' . esc_attr( $this->value['thumbnail'] ) . '" class="kfw--thumbnail"/>', kfw_allowed_html( array( 'input' ) ) );
			echo wp_kses( '<input type="hidden" name="' . esc_attr( $this->field_name( '[alt]' ) ) . '" value="' . esc_attr( $this->value['alt'] ) . '" class="kfw--alt"/>', kfw_allowed_html( array( 'input' ) ) );
			echo wp_kses( '<input type="hidden" name="' . esc_attr( $this->field_name( '[title]' ) ) . '" value="' . esc_attr( $this->value['title'] ) . '" class="kfw--title"/>', kfw_allowed_html( array( 'input' ) ) );
			echo wp_kses( '<input type="hidden" name="' . esc_attr( $this->field_name( '[description]' ) ) . '" value="' . esc_attr( $this->value['description'] ) . '" class="kfw--description"/>', kfw_allowed_html( array( 'input' ) ) );

			echo wp_kses( $this->field_after(), kfw_allowed_html( 'all' ) );

		}

	}
}
