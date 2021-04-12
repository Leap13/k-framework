<?php
/**
 * Field: Checkbox
 *
 * @package Kemet Framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

if ( ! class_exists( 'KFW_Field_Checkbox' ) ) {

	/**
	 *
	 * Field: Checkbox
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	class KFW_Field_Checkbox extends KFW_Fields {

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

			echo wp_kses( '<label class="kfw-checkbox">', kfw_allowed_html( array( 'label' ) ) );
			echo wp_kses( '<input type="hidden" name="' . esc_attr( $this->field_name() ) . '" value="' . esc_attr( $this->value ) . '" class="kfw--input" />', kfw_allowed_html( array( 'input' ) ) );
			echo wp_kses( '<input type="checkbox" class="kfw--checkbox"' . esc_html( checked( $this->value, 1, false ) ) . '/>', kfw_allowed_html( array( 'input' ) ) );
			echo ( ! empty( $this->field['label'] ) ) ? ' ' . esc_html( $this->field['label'] ) : '';
			echo wp_kses( '</label>', kfw_allowed_html( array( 'label' ) ) );
		}

	}
}
