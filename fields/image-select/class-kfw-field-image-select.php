<?php
/**
 * Field: Image Select
 *
 * @package K Framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.


if ( ! class_exists( 'KFW_Field_Image_Select' ) ) {

	/**
	 *
	 * Field: Image Select
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	class KFW_Field_Image_Select extends KFW_Fields {

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
					'multiple' => false,
					'options'  => array(),
				)
			);

			$value = ( is_array( $this->value ) ) ? $this->value : array_filter( (array) $this->value );

			echo wp_kses( $this->field_before(), kfw_allowed_html( 'all' ) );

			if ( ! empty( $args['options'] ) ) {

				echo wp_kses( '<div class="kfw-siblings kfw--image-group" data-multiple="' . $args['multiple'] . '">', kfw_allowed_html( array( 'div' ) ) );

				$num = 1;

				foreach ( $args['options'] as $key => $option ) {

					$type    = ( $args['multiple'] ) ? 'checkbox' : 'radio';
					$extra   = ( $args['multiple'] ) ? '[]' : '';
					$active  = ( in_array( $key, $value ) ) ? ' kfw--active' : '';
					$checked = ( in_array( $key, $value ) ) ? ' checked' : '';
					$counter = $num++;
					echo wp_kses( '<div class="kfw--sibling kfw--image' . $active . '">', kfw_allowed_html( array( 'div' ) ) );
					echo wp_kses( '<img src="' . $option . '" alt="img-' . $counter . '" />', kfw_allowed_html( array( 'img' ) ) );
					echo wp_kses( '<input type="' . $type . '" name="' . $this->field_name( $extra ) . '" value="' . $key . '"' . $this->field_attributes() . esc_attr( $checked ) . ' />', kfw_allowed_html( array( 'input' ) ) );
					echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

				}

				echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

			}

			echo wp_kses( '<div class="clear"></div>', kfw_allowed_html( array( 'div' ) ) );

			echo wp_kses( $this->field_after(), kfw_allowed_html( 'all' ) );

		}

		/**
		 * Background image
		 *
		 * @return string
		 */
		public function output() {

			$output    = '';
			$bg_image  = array();
			$important = ( ! empty( $this->field['output_important'] ) ) ? '!important' : '';
			$elements  = ( is_array( $this->field['output'] ) ) ? join( ',', $this->field['output'] ) : $this->field['output'];

			if ( ! empty( $elements ) && isset( $this->value ) && '' !== $this->value ) {
				$output = $elements . '{background-image:url(' . $this->value . ')' . $important . ';}';
			}

			$this->parent->output_css .= $output;

			return $output;

		}

	}
}
