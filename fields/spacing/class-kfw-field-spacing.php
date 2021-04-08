<?php
/**
 * Field: Spacing
 *
 * @package Kemet Framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

if ( ! class_exists( 'KFW_Field_Spacing' ) ) {

	/**
	 *
	 * Field: Spacing
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	class KFW_Field_Spacing extends KFW_Fields {

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
					'top_icon'           => '<i class="fa fa-arrow-up"></i>',
					'right_icon'         => '<i class="fa fa-arrow-right"></i>',
					'bottom_icon'        => '<i class="fa fa-arrow-down"></i>',
					'left_icon'          => '<i class="fa fa-arrow-left"></i>',
					'all_icon'           => '<i class="fa fa-arrows-alt"></i>',
					'top_placeholder'    => esc_html__( 'top', 'kfw' ),
					'right_placeholder'  => esc_html__( 'right', 'kfw' ),
					'bottom_placeholder' => esc_html__( 'bottom', 'kfw' ),
					'left_placeholder'   => esc_html__( 'left', 'kfw' ),
					'all_placeholder'    => esc_html__( 'all', 'kfw' ),
					'top'                => true,
					'left'               => true,
					'bottom'             => true,
					'right'              => true,
					'unit'               => true,
					'show_units'         => true,
					'all'                => false,
					'units'              => array( 'px', '%', 'em' ),
				)
			);

			$default_values = array(
				'top'    => '',
				'right'  => '',
				'bottom' => '',
				'left'   => '',
				'all'    => '',
				'unit'   => 'px',
			);

			$value   = wp_parse_args( $this->value, $default_values );
			$unit    = ( count( $args['units'] ) === 1 && ! empty( $args['unit'] ) ) ? $args['units'][0] : '';
			$is_unit = ( ! empty( $unit ) ) ? ' kfw--is-unit' : '';

			echo wp_kses( $this->field_before(), kfw_allowed_html( 'all' ) );

			echo wp_kses( '<div class="kfw--inputs">', kfw_allowed_html( array( 'div' ) ) );

			if ( ! empty( $args['all'] ) ) {

				$placeholder = ( ! empty( $args['all_placeholder'] ) ) ? ' placeholder="' . $args['all_placeholder'] . '"' : '';

				echo wp_kses( '<div class="kfw--input">', kfw_allowed_html( array( 'div' ) ) );
				echo ( ! empty( $args['all_icon'] ) ) ? wp_kses( '<span class="kfw--label kfw--icon">' . $args['all_icon'] . '</span>', kfw_allowed_html( array( 'span', 'i' ) ) ) : '';
				echo wp_kses( '<input type="number" name="' . $this->field_name( '[all]' ) . '" value="' . $value['all'] . '"' . $placeholder . ' class="kfw-input-number' . $is_unit . '" />', kfw_allowed_html( array( 'input' ) ) );
				echo ( $unit ) ? wp_kses( '<span class="kfw--label kfw--unit">' . $args['units'][0] . '</span>', kfw_allowed_html( array( 'span' ) ) ) : '';
				echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

			} else {

				$properties = array();

				foreach ( array( 'top', 'right', 'bottom', 'left' ) as $prop ) {
					if ( ! empty( $args[ $prop ] ) ) {
						$properties[] = $prop;
					}
				}

				$properties = ( array( 'right', 'left' ) === $properties ) ? array_reverse( $properties ) : $properties;

				foreach ( $properties as $property ) {

					$placeholder = ( ! empty( $args[ $property . '_placeholder' ] ) ) ? ' placeholder="' . $args[ $property . '_placeholder' ] . '"' : '';

					echo wp_kses( '<div class="kfw--input">', kfw_allowed_html( array( 'div' ) ) );
					echo ( ! empty( $args[ $property . '_icon' ] ) ) ? wp_kses( '<span class="kfw--label kfw--icon">' . $args[ $property . '_icon' ] . '</span>', kfw_allowed_html( array( 'span', 'i' ) ) ) : '';
					echo wp_kses( '<input type="number" name="' . $this->field_name( '[' . $property . ']' ) . '" value="' . $value[ $property ] . '"' . $placeholder . ' class="kfw-input-number' . $is_unit . '" />', kfw_allowed_html( array( 'input' ) ) );
					echo ( $unit ) ? wp_kses( '<span class="kfw--label kfw--unit">' . $args['units'][0] . '</span>', kfw_allowed_html( array( 'span' ) ) ) : '';
					echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

				}
			}

			if ( ! empty( $args['unit'] ) && ! empty( $args['show_units'] ) && count( $args['units'] ) > 1 ) {
				echo wp_kses( '<div class="kfw--input">', kfw_allowed_html( array( 'div' ) ) );
				echo wp_kses( '<select name="' . $this->field_name( '[unit]' ) . '">', kfw_allowed_html( array( 'select' ) ) );
				foreach ( $args['units'] as $unit ) {
					$selected = ( $value['unit'] === $unit ) ? ' selected' : '';
					echo wp_kses( '<option value="' . $unit . '"' . $selected . '>' . $unit . '</option>', kfw_allowed_html( array( 'option' ) ) );
				}
				echo wp_kses( '</select>', kfw_allowed_html( array( 'select' ) ) );
				echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );
			}

			echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

			echo wp_kses( $this->field_after(), kfw_allowed_html( 'all' ) );

		}

		/**
		 * Output css
		 *
		 * @return string
		 */
		public function output() {

			$output    = '';
			$element   = ( is_array( $this->field['output'] ) ) ? join( ',', $this->field['output'] ) : $this->field['output'];
			$important = ( ! empty( $this->field['output_important'] ) ) ? '!important' : '';
			$unit      = ( ! empty( $this->value['unit'] ) ) ? $this->value['unit'] : 'px';

			$mode = ( ! empty( $this->field['output_mode'] ) ) ? $this->field['output_mode'] : 'padding';
			$mode = ( 'relative' === $mode || 'absolute' === $mode || 'none' === $mode ) ? '' : $mode;
			$mode = ( ! empty( $mode ) ) ? $mode . '-' : '';

			if ( ! empty( $this->field['all'] ) && isset( $this->value['all'] ) && '' !== $this->value['all'] ) {

				$output  = $element . '{';
				$output .= $mode . 'top:' . $this->value['all'] . $unit . $important . ';';
				$output .= $mode . 'right:' . $this->value['all'] . $unit . $important . ';';
				$output .= $mode . 'bottom:' . $this->value['all'] . $unit . $important . ';';
				$output .= $mode . 'left:' . $this->value['all'] . $unit . $important . ';';
				$output .= '}';

			} else {

				$top    = ( isset( $this->value['top'] ) && '' !== $this->value['top'] ) ? $mode . 'top:' . $this->value['top'] . $unit . $important . ';' : '';
				$right  = ( isset( $this->value['right'] ) && '' !== $this->value['right'] ) ? $mode . 'right:' . $this->value['right'] . $unit . $important . ';' : '';
				$bottom = ( isset( $this->value['bottom'] ) && '' !== $this->value['bottom'] ) ? $mode . 'bottom:' . $this->value['bottom'] . $unit . $important . ';' : '';
				$left   = ( isset( $this->value['left'] ) && '' !== $this->value['left'] ) ? $mode . 'left:' . $this->value['left'] . $unit . $important . ';' : '';

				if ( '' !== $top || '' !== $right || '' !== $bottom || '' !== $left ) {
					$output = $element . '{' . $top . $right . $bottom . $left . '}';
				}
			}

			$this->parent->output_css .= $output;

			return $output;

		}

	}
}
