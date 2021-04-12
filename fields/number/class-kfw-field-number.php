<?php
/**
 * Field: Number
 *
 * @package Kemet Framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

if ( ! class_exists( 'KFW_Field_Number' ) ) {

	/**
	 *
	 * Field: Number
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	class KFW_Field_Number extends KFW_Fields {

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
					'unit' => '',
				)
			);

			echo wp_kses( $this->field_before(), kfw_allowed_html( 'all' ) );
			echo wp_kses( '<div class="kfw--wrap">', kfw_allowed_html( array( 'div' ) ) );
			echo wp_kses( '<input type="number" name="' . $this->field_name() . '" value="' . $this->value . '"' . $this->field_attributes( array( 'class' => 'kfw-input-number' ) ) . ' />', kfw_allowed_html( array( 'input' ) ) );
			echo ( ! empty( $args['unit'] ) ) ? wp_kses( '<span class="kfw--unit">' . $args['unit'] . '</span>', kfw_allowed_html( array( 'span' ) ) ) : '';
			echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );
			echo wp_kses( '<div class="clear"></div>', kfw_allowed_html( array( 'div' ) ) );
			echo wp_kses( $this->field_after(), kfw_allowed_html( 'all' ) );

		}

		/**
		 * Output css
		 *
		 * @return string
		 */
		public function output() {

			$output    = '';
			$elements  = ( is_array( $this->field['output'] ) ) ? $this->field['output'] : array_filter( (array) $this->field['output'] );
			$important = ( ! empty( $this->field['output_important'] ) ) ? '!important' : '';
			$mode      = ( ! empty( $this->field['output_mode'] ) ) ? $this->field['output_mode'] : 'width';
			$unit      = ( ! empty( $this->field['unit'] ) ) ? $this->field['unit'] : 'px';

			if ( ! empty( $elements ) && isset( $this->value ) && '' !== $this->value ) {
				foreach ( $elements as $key_property => $element ) {
					if ( is_numeric( $key_property ) ) {
						if ( $mode ) {
							$output = implode( ',', $elements ) . '{' . $mode . ':' . $this->value . $unit . $important . ';}';
						}
						break;
					} else {
						$output .= $element . '{' . $key_property . ':' . $this->value . $unit . $important . '}';
					}
				}
			}

			$this->parent->output_css .= $output;

			return $output;

		}

	}
}
