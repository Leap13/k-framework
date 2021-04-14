<?php
/**
 * Field: Select
 *
 * @package K Framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

if ( ! class_exists( 'KFW_Field_Select' ) ) {

	/**
	 *
	 * Field: Select
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	class KFW_Field_Select extends KFW_Fields {

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
					'placeholder' => '',
					'chosen'      => false,
					'multiple'    => false,
					'sortable'    => false,
					'ajax'        => false,
					'settings'    => array(),
					'query_args'  => array(),
				)
			);

			$this->value = ( is_array( $this->value ) ) ? $this->value : array_filter( (array) $this->value );

			echo wp_kses( $this->field_before(), kfw_allowed_html( 'all' ) );

			if ( isset( $this->field['options'] ) ) {

				if ( ! empty( $args['ajax'] ) ) {
					$args['settings']['data']['type']  = $args['options'];
					$args['settings']['data']['nonce'] = wp_create_nonce( 'kfw_chosen_ajax_nonce' );
					if ( ! empty( $args['query_args'] ) ) {
						$args['settings']['data']['query_args'] = $args['query_args'];
					}
				}

				$chosen_rtl       = ( is_rtl() ) ? ' chosen-rtl' : '';
				$multiple_name    = ( $args['multiple'] ) ? '[]' : '';
				$multiple_attr    = ( $args['multiple'] ) ? ' multiple="multiple"' : '';
				$chosen_sortable  = ( $args['chosen'] && $args['sortable'] ) ? ' kfw-chosen-sortable' : '';
				$chosen_ajax      = ( $args['chosen'] && $args['ajax'] ) ? ' kfw-chosen-ajax' : '';
				$placeholder_attr = ( $args['chosen'] && $args['placeholder'] ) ? ' data-placeholder="' . $args['placeholder'] . '"' : '';
				$field_class      = ( $args['chosen'] ) ? ' class="kfw-chosen' . $chosen_rtl . $chosen_sortable . $chosen_ajax . '"' : '';
				$field_name       = $this->field_name( $multiple_name );
				$field_attr       = $this->field_attributes();
				$maybe_options    = $this->field['options'];
				$chosen_data_attr = ( $args['chosen'] && ! empty( $args['settings'] ) ) ? ' data-chosen-settings="' . esc_attr( wp_json_encode( $args['settings'] ) ) . '"' : '';

				if ( is_string( $maybe_options ) && ! empty( $args['chosen'] ) && ! empty( $args['ajax'] ) ) {
					$options = $this->field_wp_query_data_title( $maybe_options, $this->value );
				} elseif ( is_string( $maybe_options ) ) {
					$options = $this->field_data( $maybe_options, false, $args['query_args'] );
				} else {
					$options = $maybe_options;
				}

				if ( ( is_array( $options ) && ! empty( $options ) ) || ( ! empty( $args['chosen'] ) && ! empty( $args['ajax'] ) ) ) {

					if ( ! empty( $args['chosen'] ) && ! empty( $args['multiple'] ) ) {

						echo wp_kses( '<select name="' . $field_name . '" class="kfw-hidden-select kfw-hidden"' . $multiple_attr . $field_attr . '>', kfw_allowed_html( array( 'select' ) ) );
						foreach ( $this->value as $option_key ) {
							echo wp_kses( '<option value="' . $option_key . '" selected>' . $option_key . '</option>', kfw_allowed_html( array( 'option' ) ) );
						}
						echo wp_kses( '</select>', kfw_allowed_html( array( 'select' ) ) );

						$field_name = '_pseudo';
						$field_attr = '';

					}

						echo wp_kses( '<select name="' . $field_name . '"' . $field_class . $multiple_attr . $placeholder_attr . $field_attr . $chosen_data_attr . '>', kfw_allowed_html( array( 'select' ) ) );

					if ( $args['placeholder'] && empty( $args['multiple'] ) ) {
						if ( ! empty( $args['chosen'] ) ) {
							echo wp_kses( '<option value=""></option>', kfw_allowed_html( array( 'option' ) ) );
						} else {
							echo wp_kses( '<option value="">' . $args['placeholder'] . '</option>', kfw_allowed_html( array( 'option' ) ) );
						}
					}

					foreach ( $options as $option_key => $option ) {

						if ( is_array( $option ) && ! empty( $option ) ) {

							echo wp_kses( '<optgroup label="' . $option_key . '">', kfw_allowed_html( array( 'optgroup' ) ) );

							foreach ( $option as $sub_key => $sub_value ) {
								$selected = ( in_array( $sub_key, $this->value ) ) ? ' selected' : '';
										echo wp_kses( '<option value="' . $sub_key . '" ' . $selected . '>' . $sub_value . '</option>', kfw_allowed_html( array( 'option' ) ) );
							}

							echo wp_kses( '</optgroup>', kfw_allowed_html( array( 'optgroup' ) ) );

						} else {
								$selected = ( in_array( $option_key, $this->value ) ) ? ' selected' : '';
								echo wp_kses( '<option value="' . $option_key . '" ' . $selected . '>' . $option . '</option>', kfw_allowed_html( array( 'option' ) ) );
						}
					}

					echo wp_kses( '</select>', kfw_allowed_html( array( 'select' ) ) );

				} else {

					echo ! empty( $this->field['empty_message'] ) ? esc_html( $this->field['empty_message'] ) : esc_html__( 'No data provided for this option type.', 'kfw' );

				}
			}

			echo wp_kses( $this->field_after(), kfw_allowed_html( 'all' ) );

		}

		/**
		 * Scripts
		 *
		 * @return void
		 */
		public function enqueue() {

			if ( ! wp_script_is( 'jquery-ui-sortable' ) ) {
				wp_enqueue_script( 'jquery-ui-sortable' );
			}

		}

	}
}

