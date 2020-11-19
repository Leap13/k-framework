<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access directly.
/**
 *
 * Field: icon
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if( ! class_exists( 'KFW_Field_icon' ) ) {
  class KFW_Field_icon extends KFW_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      $args = wp_parse_args( $this->field, array(
        'button_title' => esc_html__( 'Add Icon', 'kfw' ),
        'remove_title' => esc_html__( 'Remove Icon', 'kfw' ),
      ) );

      echo $this->field_before();

      $nonce  = wp_create_nonce( 'kfw_icon_nonce' );
      $hidden = ( empty( $this->value ) ) ? ' hidden' : '';

      echo '<div class="kfw-icon-select">';
      echo '<span class="kfw-icon-preview'. $hidden .'"><span class="dashicons '. $this->value .'"></span></span>';
      echo '<a href="#" class="button button-primary kfw-icon-add" data-nonce="'. $nonce .'">'. $args['button_title'] .'</a>';
      echo '<a href="#" class="button kfw-warning-primary kfw-icon-remove'. $hidden .'">'. $args['remove_title'] .'</a>';
      echo '<input type="text" name="'. $this->field_name() .'" value="'. $this->value .'" class="kfw-icon-value"'. $this->field_attributes() .' />';
      echo '</div>';

      echo $this->field_after();

    }

  }
}
