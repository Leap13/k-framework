<?php
/**
 * Field: Group
 *
 * @package Kemet Framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

if ( ! class_exists( 'KFW_Field_Group' ) ) {

	/**
	 *
	 * Field: Group
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	class KFW_Field_Group extends KFW_Fields {

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
					'max'                    => 0,
					'min'                    => 0,
					'fields'                 => array(),
					'button_title'           => esc_html__( 'Add New', 'kfw' ),
					'accordion_title_prefix' => '',
					'accordion_title_number' => false,
					'accordion_title_auto'   => true,
				)
			);

			$title_prefix = ( ! empty( $args['accordion_title_prefix'] ) ) ? $args['accordion_title_prefix'] : '';
			$title_number = ( ! empty( $args['accordion_title_number'] ) ) ? true : false;
			$title_auto   = ( ! empty( $args['accordion_title_auto'] ) ) ? true : false;

			if ( ! empty( $this->parent ) && preg_match( '/' . preg_quote( '[' . $this->field['id'] . ']' ) . '/', $this->parent ) ) {

				echo wp_kses( '<div class="kfw-notice kfw-notice-danger">' . esc_html__( 'Error: Nested field id can not be same with another nested field id.', 'kfw' ) . '</div>', kfw_allowed_html( array( 'div' ) ) );

			} else {

				echo wp_kses( $this->field_before(), kfw_allowed_html( 'all' ) );

				echo wp_kses( '<div class="kfw-cloneable-item kfw-cloneable-hidden">', kfw_allowed_html( array( 'div' ) ) );

				echo wp_kses( '<div class="kfw-cloneable-helper">', kfw_allowed_html( array( 'div' ) ) );
				echo wp_kses( '<i class="kfw-cloneable-sort fa fa-arrows-alt"></i>', kfw_allowed_html( array( 'i' ) ) );
				echo wp_kses( '<i class="kfw-cloneable-clone fa fa-clone"></i>', kfw_allowed_html( array( 'i' ) ) );
				echo wp_kses( '<i class="kfw-cloneable-remove kfw-confirm fa fa-times" data-confirm="' . esc_html__( 'Are you sure to delete this item?', 'kfw' ) . '"></i>', kfw_allowed_html( array( 'i' ) ) );
				echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

				echo wp_kses( '<h4 class="kfw-cloneable-title">', kfw_allowed_html( array( 'h4' ) ) );
				echo wp_kses( '<span class="kfw-cloneable-text">', kfw_allowed_html( array( 'span' ) ) );
				echo ( $title_number ) ? wp_kses( '<span class="kfw-cloneable-title-number"></span>', kfw_allowed_html( array( 'span' ) ) ) : '';
				echo ( $title_prefix ) ? wp_kses( '<span class="kfw-cloneable-title-prefix">' . $title_prefix . '</span>', kfw_allowed_html( array( 'span' ) ) ) : '';
				echo ( $title_auto ) ? wp_kses( '<span class="kfw-cloneable-value">' . $this->field['title'] . '</span>', kfw_allowed_html( array( 'span' ) ) ) : '';
				echo wp_kses( '</span>', kfw_allowed_html( array( 'span' ) ) );
				echo wp_kses( '</h4>', kfw_allowed_html( array( 'h4' ) ) );

				echo wp_kses( '<div class="kfw-cloneable-content">', kfw_allowed_html( array( 'div' ) ) );
				foreach ( $this->field['fields'] as $field ) {

					$field_parent  = $this->parent . '[' . $this->field['id'] . ']';
					$field_default = ( isset( $field['default'] ) ) ? $field['default'] : '';

					kfw::field( $field, $field_default, '_nonce', 'field/group', $field_parent );

				}
				echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

				echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

				echo wp_kses( '<div class="kfw-cloneable-wrapper kfw-data-wrapper" data-title-number="' . $title_number . '" data-unique-id="' . $this->unique . '" data-field-id="[' . $this->field['id'] . ']" data-max="' . $args['max'] . '" data-min="' . $args['min'] . '">', kfw_allowed_html( array( 'div' ) ) );

				if ( ! empty( $this->value ) ) {

					$num = 0;

					foreach ( $this->value as $value ) {

						$first_id    = ( isset( $this->field['fields'][0]['id'] ) ) ? $this->field['fields'][0]['id'] : '';
						$first_value = ( isset( $value[ $first_id ] ) ) ? $value[ $first_id ] : '';

						echo wp_kses( '<div class="kfw-cloneable-item">', kfw_allowed_html( array( 'div' ) ) );

						echo wp_kses( '<div class="kfw-cloneable-helper">', kfw_allowed_html( array( 'div' ) ) );
						echo wp_kses( '<i class="kfw-cloneable-sort fa fa-arrows-alt"></i>', kfw_allowed_html( array( 'i' ) ) );
						echo wp_kses( '<i class="kfw-cloneable-clone fa fa-clone"></i>', kfw_allowed_html( array( 'i' ) ) );
						echo wp_kses( '<i class="kfw-cloneable-remove kfw-confirm fa fa-times" data-confirm="' . esc_html__( 'Are you sure to delete this item?', 'kfw' ) . '"></i>', kfw_allowed_html( array( 'i' ) ) );
						echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

						echo wp_kses( '<h4 class="kfw-cloneable-title">', kfw_allowed_html( array( 'h4' ) ) );
						echo wp_kses( '<span class="kfw-cloneable-text">', kfw_allowed_html( array( 'span' ) ) );
						echo ( $title_number ) ? wp_kses( '<span class="kfw-cloneable-title-number">' . ( $num + 1 ) . '.</span>', kfw_allowed_html( array( 'span' ) ) ) : '';
						echo ( $title_prefix ) ? wp_kses( '<span class="kfw-cloneable-title-prefix">' . $title_prefix . '</span>', kfw_allowed_html( array( 'span' ) ) ) : '';
						echo ( $title_auto ) ? wp_kses( '<span class="kfw-cloneable-value">' . $first_value . '</span>', kfw_allowed_html( array( 'span' ) ) ) : '';
						echo wp_kses( '</span>', kfw_allowed_html( array( 'span' ) ) );
						echo wp_kses( '</h4>', kfw_allowed_html( array( 'h4' ) ) );

						echo wp_kses( '<div class="kfw-cloneable-content">', kfw_allowed_html( array( 'div' ) ) );

						foreach ( $this->field['fields'] as $field ) {

							$field_parent = $this->parent . '[' . $this->field['id'] . ']';
							$field_unique = ( ! empty( $this->unique ) ) ? $this->unique . '[' . $this->field['id'] . '][' . $num . ']' : $this->field['id'] . '[' . $num . ']';
							$field_value  = ( isset( $field['id'] ) && isset( $value[ $field['id'] ] ) ) ? $value[ $field['id'] ] : '';

							kfw::field( $field, $field_value, $field_unique, 'field/group', $field_parent );

						}

						echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

						echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

						$num++;

					}
				}

				echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

				echo wp_kses( '<div class="kfw-cloneable-alert kfw-cloneable-max">' . esc_html__( 'You can not add more than', 'kfw' ) . ' ' . $args['max'] . '</div>', kfw_allowed_html( array( 'div' ) ) );
				echo wp_kses( '<div class="kfw-cloneable-alert kfw-cloneable-min">' . esc_html__( 'You can not remove less than', 'kfw' ) . ' ' . $args['min'] . '</div>', kfw_allowed_html( array( 'div' ) ) );

				echo wp_kses( '<a href="#" class="button button-primary kfw-cloneable-add">' . $args['button_title'] . '</a>', kfw_allowed_html( array( 'a' ) ) );

				echo wp_kses( $this->field_after(), kfw_allowed_html( 'all' ) );

			}
			if ( ! wp_script_is( 'jquery-ui-accordion' ) ) {
				wp_enqueue_script( 'jquery-ui-accordion' );
			}

			if ( ! wp_script_is( 'jquery-ui-sortable' ) ) {
				wp_enqueue_script( 'jquery-ui-sortable' );
			}
		}
	}
}
