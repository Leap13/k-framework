<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access directly.
/**
 *
 * Field: switcher
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if( ! class_exists( 'KFW_Field_switcher' ) ) {
  class KFW_Field_switcher extends KFW_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      $active     = ( ! empty( $this->value ) ) ? ' kfw--active' : '';
      $text_on    = ( ! empty( $this->field['text_on'] ) ) ? $this->field['text_on'] : esc_html__( 'On', 'kfw' );
      $text_off   = ( ! empty( $this->field['text_off'] ) ) ? $this->field['text_off'] : esc_html__( 'Off', 'kfw' );


      echo '<div class="kfw--switcher'. esc_attr( $active ) .'">';
      echo '<span class="kfw--on">'. esc_html( $text_on ).'</span>';
      echo '<span class="kfw--off">'. esc_html( $text_off ).'</span>';
      echo '<span class="kfw--ball"></span>';
      echo '<input type="text" name="'. esc_attr( $this->field_name() ) .'" value="'. esc_html( $this->value ) .'" />';
      echo '</div>';

      echo ( ! empty( $this->field['label'] ) ) ? '<span class="kfw--label">'. esc_html( $this->field['label'] ) . '</span>' : '';

      echo '<div class="clear"></div>';

    }

  }
}
