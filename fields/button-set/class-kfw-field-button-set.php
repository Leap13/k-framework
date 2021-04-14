<?php
/**
 * Field: Button_Set
 *
 * @package K Framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

if ( ! class_exists( 'KFW_Field_Button_Set' ) ) {

	/**
	 *
	 * Field: Button_Set
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	class KFW_Field_Button_Set extends KFW_Fields {

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

				echo wp_kses( '<div class="kfw-siblings kfw--button-group" data-multiple="' . $args['multiple'] . '">', kfw_allowed_html( array( 'div' ) ) );

				foreach ( $args['options'] as $key => $option ) {

					$type    = ( $args['multiple'] ) ? 'checkbox' : 'radio';
					$extra   = ( $args['multiple'] ) ? '[]' : '';
					$active  = ( in_array( $key, $value ) || ( empty( $value ) && empty( $key ) ) ) ? ' kfw--active' : '';
					$checked = ( in_array( $key, $value ) || ( empty( $value ) && empty( $key ) ) ) ? ' checked' : '';

					echo wp_kses( '<div class="kfw--sibling kfw--button' . $active . '">', kfw_allowed_html( array( 'div' ) ) );
					echo wp_kses( '<input type="' . $type . '" name="' . $this->field_name( $extra ) . '" value="' . $key . '"' . $this->field_attributes() . $checked . ' />', kfw_allowed_html( array( 'input' ) ) );
					echo esc_html( $option );
					echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

				}

				echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

			}

			echo wp_kses( '<div class="clear"></div>', kfw_allowed_html( array( 'div' ) ) );

			echo wp_kses( $this->field_after(), kfw_allowed_html( 'all' ) );
		}

	}
}
