<?php
/**
 * Field: Fieldset
 *
 * @package K Framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

if ( ! class_exists( 'KFW_Field_Fieldset' ) ) {

	/**
	 *
	 * Field: Fieldset
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	class KFW_Field_Fieldset extends KFW_Fields {

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

			echo wp_kses( $this->field_before(), kfw_allowed_html( 'all' ) );

			echo wp_kses( '<div class="kfw-fieldset-content">', kfw_allowed_html( array( 'div' ) ) );

			foreach ( $this->field['fields'] as $field ) {

				$field_id      = ( isset( $field['id'] ) ) ? $field['id'] : '';
				$field_default = ( isset( $field['default'] ) ) ? $field['default'] : '';
				$field_value   = ( isset( $this->value[ $field_id ] ) ) ? $this->value[ $field_id ] : $field_default;
				$unique_id     = ( ! empty( $this->unique ) ) ? $this->unique . '[' . $this->field['id'] . ']' : $this->field['id'];

				KFW::field( $field, $field_value, $unique_id, 'field/fieldset' );

			}

			echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

			echo wp_kses( $this->field_after(), kfw_allowed_html( 'all' ) );

		}

	}
}
