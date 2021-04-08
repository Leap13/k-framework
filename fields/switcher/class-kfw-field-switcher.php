<?php
/**
 * Field: Switcher
 *
 * @package Kemet Framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

if ( ! class_exists( 'KFW_Field_Switcher' ) ) {

	/**
	 *
	 * Field: Switcher
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	class KFW_Field_Switcher extends KFW_Fields {

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

			$active     = ( ! empty( $this->value ) ) ? ' kfw--active' : '';
			$text_on    = ( ! empty( $this->field['text_on'] ) ) ? $this->field['text_on'] : esc_html__( 'On', 'kfw' );
			$text_off   = ( ! empty( $this->field['text_off'] ) ) ? $this->field['text_off'] : esc_html__( 'Off', 'kfw' );
			$text_width = ( ! empty( $this->field['text_width'] ) ) ? ' style="width: ' . $this->field['text_width'] . 'px;"' : '';

			echo wp_kses( $this->field_before(), kfw_allowed_html( 'all' ) );

			echo wp_kses( '<div class="kfw--switcher' . $active . '"' . $text_width . '>', kfw_allowed_html( array( 'div' ) ) );
			echo wp_kses( '<span class="kfw--on">' . $text_on . '</span>', kfw_allowed_html( array( 'span' ) ) );
			echo wp_kses( '<span class="kfw--off">' . $text_off . '</span>', kfw_allowed_html( array( 'span' ) ) );
			echo wp_kses( '<span class="kfw--ball"></span>', kfw_allowed_html( array( 'span' ) ) );
			echo wp_kses( '<input type="text" name="' . $this->field_name() . '" value="' . $this->value . '"' . $this->field_attributes() . ' />', kfw_allowed_html( array( 'input' ) ) );
			echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

			echo ( ! empty( $this->field['label'] ) ) ? wp_kses( '<span class="kfw--label">' . $this->field['label'] . '</span>', kfw_allowed_html( array( 'span' ) ) ) : '';

			echo wp_kses( '<div class="clear"></div>', kfw_allowed_html( array( 'div' ) ) );

			echo wp_kses( $this->field_after(), kfw_allowed_html( 'all' ) );

		}

	}
}
