<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access directly.
/**
 *
 * Field: checkbox
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if( ! class_exists( 'KFW_Field_checkbox' ) ) {
  class KFW_Field_checkbox extends KFW_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      echo '<label class="kfw-checkbox">';
      echo '<input type="hidden" name="'. esc_attr( $this->field_name() ) .'" value="'. esc_attr( $this->value ) .'" class="kfw--input" />';
      echo '<input type="checkbox" class="kfw--checkbox"'. esc_html( checked( $this->value, 1, false ) ) .'/>';
      echo ( ! empty( $this->field['label'] ) ) ? ' '. esc_html( $this->field['label'] ) : '';
      echo '</label>';


    }

  }
}
