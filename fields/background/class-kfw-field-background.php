<?php
/**
 * Field: background
 *
 * @package Kemet Framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

if ( ! class_exists( 'KFW_Field_Background' ) ) {

	/**
	 *
	 * Field: Background
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	class KFW_Field_Background extends KFW_Fields {

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
					'background_color'              => true,
					'background_image'              => true,
					'background_position'           => true,
					'background_repeat'             => true,
					'background_attachment'         => true,
					'background_size'               => true,
					'background_origin'             => false,
					'background_clip'               => false,
					'background_blend_mode'         => false,
					'background_gradient'           => false,
					'background_gradient_color'     => true,
					'background_gradient_direction' => true,
					'background_image_preview'      => true,
					'background_auto_attributes'    => false,
					'background_image_library'      => 'image',
					'background_image_placeholder'  => esc_html__( 'No background selected', 'kfw' ),
				)
			);

			$default_value = array(
				'background-color'              => '',
				'background-image'              => '',
				'background-position'           => '',
				'background-repeat'             => '',
				'background-attachment'         => '',
				'background-size'               => '',
				'background-origin'             => '',
				'background-clip'               => '',
				'background-blend-mode'         => '',
				'background-gradient-color'     => '',
				'background-gradient-direction' => '',
			);

			$default_value = ( ! empty( $this->field['default'] ) ) ? wp_parse_args( $this->field['default'], $default_value ) : $default_value;

			$this->value = wp_parse_args( $this->value, $default_value );

			echo wp_kses( $this->field_before(), kfw_allowed_html( 'all' ) );

			echo wp_kses( '<div class="kfw--background-colors">', kfw_allowed_html( array( 'div' ) ) );

			// Background Color.
			if ( ! empty( $args['background_color'] ) ) {

				echo wp_kses( '<div class="kfw--color">', kfw_allowed_html( array( 'div' ) ) );

				echo ( ! empty( $args['background_gradient'] ) ) ? wp_kses( '<div class="kfw--title">' . esc_html__( 'From', 'kfw' ) . '</div>', kfw_allowed_html( array( 'div' ) ) ) : '';

				KFW::field(
					array(
						'id'      => 'background-color',
						'type'    => 'color',
						'default' => $default_value['background-color'],
					),
					$this->value['background-color'],
					$this->field_name(),
					'field/background'
				);

				echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

			}

			// Background Gradient Color.
			if ( ! empty( $args['background_gradient_color'] ) && ! empty( $args['background_gradient'] ) ) {

				echo wp_kses( '<div class="kfw--color">', kfw_allowed_html( array( 'div' ) ) );

				echo ( ! empty( $args['background_gradient'] ) ) ? wp_kses( '<div class="kfw--title">' . esc_html__( 'To', 'kfw' ) . '</div>', kfw_allowed_html( array( 'div' ) ) ) : '';

				KFW::field(
					array(
						'id'      => 'background-gradient-color',
						'type'    => 'color',
						'default' => $default_value['background-gradient-color'],
					),
					$this->value['background-gradient-color'],
					$this->field_name(),
					'field/background'
				);

				echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

			}

			// Background Gradient Direction.
			if ( ! empty( $args['background_gradient_direction'] ) && ! empty( $args['background_gradient'] ) ) {

				echo wp_kses( '<div class="kfw--color">', kfw_allowed_html( array( 'div' ) ) );

				echo ( ! empty( $args['background_gradient'] ) ) ? wp_kses( '<div class="kfw---title">' . esc_html__( 'Direction', 'kfw' ) . '</div>', kfw_allowed_html( array( 'div' ) ) ) : '';

				KFW::field(
					array(
						'id'      => 'background-gradient-direction',
						'type'    => 'select',
						'options' => array(
							''          => esc_html__( 'Gradient Direction', 'kfw' ),
							'to bottom' => esc_html__( '&#8659; top to bottom', 'kfw' ),
							'to right'  => esc_html__( '&#8658; left to right', 'kfw' ),
							'135deg'    => esc_html__( '&#8664; corner top to right', 'kfw' ),
							'-135deg'   => esc_html__( '&#8665; corner top to left', 'kfw' ),
						),
					),
					$this->value['background-gradient-direction'],
					$this->field_name(),
					'field/background'
				);

				echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

			}

			echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

			// Background Image.
			if ( ! empty( $args['background_image'] ) ) {

				echo wp_kses( '<div class="kfw--background-image">', kfw_allowed_html( array( 'div' ) ) );

				KFW::field(
					array(
						'id'          => 'background-image',
						'type'        => 'media',
						'class'       => 'kfw-assign-field-background',
						'library'     => $args['background_image_library'],
						'preview'     => $args['background_image_preview'],
						'placeholder' => $args['background_image_placeholder'],
						'attributes'  => array( 'data-depend-id' => $this->field['id'] ),
					),
					$this->value['background-image'],
					$this->field_name(),
					'field/background'
				);

				echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

			}

			$auto_class   = ( ! empty( $args['background_auto_attributes'] ) ) ? ' kfw--auto-attributes' : '';
			$hidden_class = ( ! empty( $args['background_auto_attributes'] ) && empty( $this->value['background-image']['url'] ) ) ? ' kfw--attributes-hidden' : '';

			echo wp_kses( '<div class="kfw--background-attributes' . esc_attr( $auto_class . $hidden_class ) . '">', kfw_allowed_html( array( 'div' ) ) );

			// Background Position.
			if ( ! empty( $args['background_position'] ) ) {

				KFW::field(
					array(
						'id'      => 'background-position',
						'type'    => 'select',
						'options' => array(
							''              => esc_html__( 'Background Position', 'kfw' ),
							'left top'      => esc_html__( 'Left Top', 'kfw' ),
							'left center'   => esc_html__( 'Left Center', 'kfw' ),
							'left bottom'   => esc_html__( 'Left Bottom', 'kfw' ),
							'center top'    => esc_html__( 'Center Top', 'kfw' ),
							'center center' => esc_html__( 'Center Center', 'kfw' ),
							'center bottom' => esc_html__( 'Center Bottom', 'kfw' ),
							'right top'     => esc_html__( 'Right Top', 'kfw' ),
							'right center'  => esc_html__( 'Right Center', 'kfw' ),
							'right bottom'  => esc_html__( 'Right Bottom', 'kfw' ),
						),
					),
					$this->value['background-position'],
					$this->field_name(),
					'field/background'
				);

			}

			// Background Repeat.
			if ( ! empty( $args['background_repeat'] ) ) {

				KFW::field(
					array(
						'id'      => 'background-repeat',
						'type'    => 'select',
						'options' => array(
							''          => esc_html__( 'Background Repeat', 'kfw' ),
							'repeat'    => esc_html__( 'Repeat', 'kfw' ),
							'no-repeat' => esc_html__( 'No Repeat', 'kfw' ),
							'repeat-x'  => esc_html__( 'Repeat Horizontally', 'kfw' ),
							'repeat-y'  => esc_html__( 'Repeat Vertically', 'kfw' ),
						),
					),
					$this->value['background-repeat'],
					$this->field_name(),
					'field/background'
				);

			}

			// Background Attachment.
			if ( ! empty( $args['background_attachment'] ) ) {

				KFW::field(
					array(
						'id'      => 'background-attachment',
						'type'    => 'select',
						'options' => array(
							''       => esc_html__( 'Background Attachment', 'kfw' ),
							'scroll' => esc_html__( 'Scroll', 'kfw' ),
							'fixed'  => esc_html__( 'Fixed', 'kfw' ),
						),
					),
					$this->value['background-attachment'],
					$this->field_name(),
					'field/background'
				);

			}

			// Background Size.
			if ( ! empty( $args['background_size'] ) ) {

				KFW::field(
					array(
						'id'      => 'background-size',
						'type'    => 'select',
						'options' => array(
							''        => esc_html__( 'Background Size', 'kfw' ),
							'cover'   => esc_html__( 'Cover', 'kfw' ),
							'contain' => esc_html__( 'Contain', 'kfw' ),
							'auto'    => esc_html__( 'Auto', 'kfw' ),
						),
					),
					$this->value['background-size'],
					$this->field_name(),
					'field/background'
				);

			}

			// Background Origin.
			if ( ! empty( $args['background_origin'] ) ) {

				KFW::field(
					array(
						'id'      => 'background-origin',
						'type'    => 'select',
						'options' => array(
							''            => esc_html__( 'Background Origin', 'kfw' ),
							'padding-box' => esc_html__( 'Padding Box', 'kfw' ),
							'border-box'  => esc_html__( 'Border Box', 'kfw' ),
							'content-box' => esc_html__( 'Content Box', 'kfw' ),
						),
					),
					$this->value['background-origin'],
					$this->field_name(),
					'field/background'
				);

			}

			// Background Clip.
			if ( ! empty( $args['background_clip'] ) ) {

				KFW::field(
					array(
						'id'      => 'background-clip',
						'type'    => 'select',
						'options' => array(
							''            => esc_html__( 'Background Clip', 'kfw' ),
							'border-box'  => esc_html__( 'Border Box', 'kfw' ),
							'padding-box' => esc_html__( 'Padding Box', 'kfw' ),
							'content-box' => esc_html__( 'Content Box', 'kfw' ),
						),
					),
					$this->value['background-clip'],
					$this->field_name(),
					'field/background'
				);

			}

			// Background Blend Mode.
			if ( ! empty( $args['background_blend_mode'] ) ) {

				KFW::field(
					array(
						'id'      => 'background-blend-mode',
						'type'    => 'select',
						'options' => array(
							''            => esc_html__( 'Background Blend Mode', 'kfw' ),
							'normal'      => esc_html__( 'Normal', 'kfw' ),
							'multiply'    => esc_html__( 'Multiply', 'kfw' ),
							'screen'      => esc_html__( 'Screen', 'kfw' ),
							'overlay'     => esc_html__( 'Overlay', 'kfw' ),
							'darken'      => esc_html__( 'Darken', 'kfw' ),
							'lighten'     => esc_html__( 'Lighten', 'kfw' ),
							'color-dodge' => esc_html__( 'Color Dodge', 'kfw' ),
							'saturation'  => esc_html__( 'Saturation', 'kfw' ),
							'color'       => esc_html__( 'Color', 'kfw' ),
							'luminosity'  => esc_html__( 'Luminosity', 'kfw' ),
						),
					),
					$this->value['background-blend-mode'],
					$this->field_name(),
					'field/background'
				);

			}

			echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

			echo wp_kses( $this->field_after(), kfw_allowed_html( 'all' ) );

		}

		/**
		 * Output
		 *
		 * @return string
		 */
		public function output() {

			$output    = '';
			$bg_image  = array();
			$important = ( ! empty( $this->field['output_important'] ) ) ? '!important' : '';
			$element   = ( is_array( $this->field['output'] ) ) ? join( ',', $this->field['output'] ) : $this->field['output'];

			// Background image and gradient.
			$background_color        = ( ! empty( $this->value['background-color'] ) ) ? $this->value['background-color'] : '';
			$background_gd_color     = ( ! empty( $this->value['background-gradient-color'] ) ) ? $this->value['background-gradient-color'] : '';
			$background_gd_direction = ( ! empty( $this->value['background-gradient-direction'] ) ) ? $this->value['background-gradient-direction'] : '';
			$background_image        = ( ! empty( $this->value['background-image']['url'] ) ) ? $this->value['background-image']['url'] : '';

			if ( $background_color && $background_gd_color ) {
				$gd_direction = ( $background_gd_direction ) ? $background_gd_direction . ',' : '';
				$bg_image[]   = 'linear-gradient(' . $gd_direction . $background_color . ',' . $background_gd_color . ')';
				unset( $this->value['background-color'] );
			}

			if ( $background_image ) {
				$bg_image[] = 'url(' . $background_image . ')';
			}

			if ( ! empty( $bg_image ) ) {
				$output .= 'background-image:' . implode( ',', $bg_image ) . $important . ';';
			}

			// Common background properties.
			$properties = array( 'color', 'position', 'repeat', 'attachment', 'size', 'origin', 'clip', 'blend-mode' );

			foreach ( $properties as $property ) {
				$property = 'background-' . $property;
				if ( ! empty( $this->value[ $property ] ) ) {
					$output .= $property . ':' . $this->value[ $property ] . $important . ';';
				}
			}

			if ( $output ) {
				$output = $element . '{' . $output . '}';
			}

			$this->parent->output_css .= $output;

			return $output;
		}

	}
}
