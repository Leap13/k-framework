<?php
/**
 * Field: Color
 *
 * @package Kemet Framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

if ( ! class_exists( 'KFW_Field_Color' ) ) {

	/**
	 *
	 * Field: Color
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	class KFW_Field_Color extends KFW_Fields {

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

			$default_attr = ( ! empty( $this->field['default'] ) ) ? ' data-default-color="' . $this->field['default'] . '"' : '';

			echo wp_kses( $this->field_before(), kfw_allowed_html( 'all' ) );
			echo wp_kses( '<input type="text" name="' . $this->field_name() . '" value="' . $this->value . '" class="kfw-color"' . $default_attr . $this->field_attributes() . '/>', kfw_allowed_html( array( 'input' ) ) );
			echo wp_kses( $this->field_after(), kfw_allowed_html( 'all' ) );

		}

		/**
		 * Color
		 *
		 * @return string
		 */
		public function output() {

			$output    = '';
			$elements  = ( is_array( $this->field['output'] ) ) ? $this->field['output'] : array_filter( (array) $this->field['output'] );
			$important = ( ! empty( $this->field['output_important'] ) ) ? '!important' : '';
			$mode      = ( ! empty( $this->field['output_mode'] ) ) ? $this->field['output_mode'] : 'color';

			if ( ! empty( $elements ) && isset( $this->value ) && '' !== $this->value ) {
				foreach ( $elements as $key_property => $element ) {
					if ( is_numeric( $key_property ) ) {
						$output = implode( ',', $elements ) . '{' . $mode . ':' . $this->value . $important . ';}';
						break;
					} else {
						$output .= $element . '{' . $key_property . ':' . $this->value . $important . '}';
					}
				}
			}

			$this->parent->output_css .= $output;

			return $output;

		}

	}
}
