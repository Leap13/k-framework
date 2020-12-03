<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access directly.
/**
 *
 * Field: textarea
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'KFW_Field_textarea' ) ) {
  class KFW_Field_textarea extends KFW_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      echo $this->field_before();
      echo $this->shortcoder();
      echo '<textarea name="'. esc_attr( $this->field_name() ) .'"'. $this->field_attributes() .'>'. $this->value .'</textarea>';
      echo $this->field_after();

    }

    public function shortcoder() {

      if ( ! empty( $this->field['shortcoder'] ) ) {

        $shortcoder = ( is_array( $this->field['shortcoder'] ) ) ? $this->field['shortcoder'] : array_filter( (array) $this->field['shortcoder'] );

        foreach ( $shortcoder as $shortcode_id ) {

          if ( isset( KFW::$args['shortcode_options'][$shortcode_id] ) ) {

            $setup_args   = KFW::$args['shortcode_options'][$shortcode_id];
            $button_title = ( ! empty( $setup_args['button_title'] ) ) ? $setup_args['button_title'] : esc_html__( 'Add Shortcode', 'kfw' );

            echo '<a href="#" class="button button-primary kfw-shortcode-button" data-modal-id="'. esc_attr( $shortcode_id ) .'">'. wp_kses_post( $button_title ) .'</a>';

          }

        }

      }

    }
  }
}
