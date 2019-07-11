<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access directly.
/**
 *
 * Field: select
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if( ! class_exists( '_Field_select' ) ) {
  class KFW_Field_select extends KFW_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      $args = wp_parse_args( $this->field, array(
        'chosen'      => false,
        'multiple'    => false,
        'placeholder' => '',
      ) );

      $this->value = ( is_array( $this->value ) ) ? $this->value : array_filter( (array) $this->value );


      if( isset( $this->field['options'] ) ) {

       $options = ( is_array( $this->field['options'] ) ) ? $this->field['options'] : array();

        if( is_array( $options ) && ! empty( $options ) ) {

          echo '<select name="'. esc_attr( $this->field_name() ) .'">';

          if( $args['placeholder'] && empty( $args['multiple'] ) ) {
            if( ! empty( $args['chosen'] ) ) {
              echo '<option value=""></option>';
            } else {
              echo '<option value="">'. esc_html( $args['placeholder'] ) .'</option>';
            }
          }

          foreach ( $options as $option_key => $option ) {
    
            $selected = ( in_array( $option_key, $this->value ) ) ? ' selected' : '';
            echo '<option value="'. esc_attr( $option_key ) .'" '. esc_attr( $selected ) .'>'. esc_html( $option ) .'</option>';

          }

          echo '</select>';

        }

      }

    }

  }
}
