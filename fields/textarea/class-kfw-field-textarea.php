<?php
/**
 * Field: Text
 *
 * @package Kemet Framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

if ( ! class_exists( 'KFW_Field_Textarea' ) ) {

	/**
	 *
	 * Field: Textarea
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	class KFW_Field_Textarea extends KFW_Fields {

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
			echo $this->shortcoder(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo wp_kses( '<textarea name="' . esc_attr( $this->field_name() ) . '"' . $this->field_attributes() . '>' . $this->value . '</textarea>', kfw_allowed_html( array( 'textarea' ) ) );
			echo wp_kses( $this->field_after(), kfw_allowed_html( 'all' ) );

		}

		/**
		 * Shortcode
		 *
		 * @return void
		 */
		public function shortcoder() {

			if ( ! empty( $this->field['shortcoder'] ) ) {

				$shortcoder = ( is_array( $this->field['shortcoder'] ) ) ? $this->field['shortcoder'] : array_filter( (array) $this->field['shortcoder'] );

				foreach ( $shortcoder as $shortcode_id ) {

					if ( isset( KFW::$args['shortcode_options'][ $shortcode_id ] ) ) {

						$setup_args   = KFW::$args['shortcode_options'][ $shortcode_id ];
						$button_title = ( ! empty( $setup_args['button_title'] ) ) ? $setup_args['button_title'] : esc_html__( 'Add Shortcode', 'kfw' );

						echo wp_kses( '<a href="#" class="button button-primary kfw-shortcode-button" data-modal-id="' . esc_attr( $shortcode_id ) . '">' . wp_kses_post( $button_title ) . '</a>', kfw_allowed_html( array( 'a' ) ) );

					}
				}
			}

		}
	}
}
