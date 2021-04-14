<?php
/**
 * Field: Group
 *
 * @package K Framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

if ( ! class_exists( 'KFW_Field_Icon' ) ) {

	/**
	 *
	 * Field: Icon
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	class KFW_Field_Icon extends KFW_Fields {

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
					'button_title' => esc_html__( 'Add Icon', 'kfw' ),
					'remove_title' => esc_html__( 'Remove Icon', 'kfw' ),
				)
			);

			echo wp_kses( $this->field_before(), kfw_allowed_html( 'all' ) );

			$nonce  = wp_create_nonce( 'kfw_icon_nonce' );
			$hidden = ( empty( $this->value ) ) ? ' hidden' : '';

			echo wp_kses( '<div class="kfw-icon-select">', kfw_allowed_html( array( 'div' ) ) );
			echo wp_kses( '<span class="kfw-icon-preview' . $hidden . '"><span class="dashicons ' . $this->value . '"></span></span>', kfw_allowed_html( array( 'span' ) ) );
			echo wp_kses( '<a href="#" class="button button-primary kfw-icon-add" data-nonce="' . $nonce . '">' . $args['button_title'] . '</a>', kfw_allowed_html( array( 'a' ) ) );
			echo wp_kses( '<a href="#" class="button kfw-warning-primary kfw-icon-remove' . $hidden . '">' . $args['remove_title'] . '</a>', kfw_allowed_html( array( 'a' ) ) );
			echo wp_kses( '<input type="text" name="' . $this->field_name() . '" value="' . $this->value . '" class="kfw-icon-value"' . $this->field_attributes() . ' />', kfw_allowed_html( array( 'input' ) ) );
			echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

			echo wp_kses( $this->field_after(), kfw_allowed_html( 'all' ) );

		}

	}
}
