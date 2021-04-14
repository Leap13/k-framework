<?php
/**
 * Field: Text
 *
 * @package K Framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

if ( ! class_exists( 'KFW_Field_Text' ) ) {

	/**
	 *
	 * Field: Text
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	class KFW_Field_Text extends KFW_Fields {

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

			$type = ( ! empty( $this->field['attributes']['type'] ) ) ? $this->field['attributes']['type'] : 'text';

			echo wp_kses( $this->field_before(), kfw_allowed_html( 'all' ) );

			echo wp_kses( '<input type="' . $type . '" name="' . $this->field_name() . '" value="' . $this->value . '"' . $this->field_attributes() . ' />', kfw_allowed_html( array( 'input' ) ) );

			echo wp_kses( $this->field_after(), kfw_allowed_html( 'all' ) );

		}

	}
}
