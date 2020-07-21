<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access directly.
/**
 *
 * Fields Class
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if( ! class_exists( 'KFW_Fields' ) ) {
  abstract class KFW_Fields extends KFW_Abstract {
    public function __construct( $field = array(), $value = '', $unique = '', $where = '', $parent = '' ) {
      $this->field  = $field;
      $this->value  = $value;
      $this->unique = $unique;
      $this->where  = $where;
      $this->parent = $parent;
    }
    public function field_name( $nested_name = '' ) {

      $field_id   = ( ! empty( $this->field['id'] ) ) ? $this->field['id'] : '';
      $unique_id  = ( ! empty( $this->unique ) ) ? $this->unique .'['. $field_id .']' : $field_id;
      $field_name = ( ! empty( $this->field['name'] ) ) ? $this->field['name'] : $unique_id;
      $tag_prefix = ( ! empty( $this->field['tag_prefix'] ) ) ? $this->field['tag_prefix'] : '';

      if( ! empty( $tag_prefix ) ) {
        $nested_name = str_replace( '[', '['. $tag_prefix, $nested_name );
      }

      return $field_name . $nested_name;

    }
        public function field_attributes( $custom_atts = array() ) {

      $field_id   = ( ! empty( $this->field['id'] ) ) ? $this->field['id'] : '';
      $attributes = ( ! empty( $this->field['attributes'] ) ) ? $this->field['attributes'] : array();

      if( ! empty( $field_id ) ) {
        $attributes['data-depend-id'] = $field_id;
      }

      if( ! empty( $this->field['placeholder'] ) ) {
        $attributes['placeholder'] = $this->field['placeholder'];
      }

      $attributes = wp_parse_args( $attributes, $custom_atts );

      $atts = '';

      if( ! empty( $attributes ) ) {
        foreach ( $attributes as $key => $value ) {
          if( $value === 'only-key' ) {
            $atts .= ' '. $key;
          } else {
            $atts .= ' '. $key . '="'. $value .'"';
          }
        }
      }

      return $atts;

    }

    public function field_before() {
      return ( ! empty( $this->field['before'] ) ) ? $this->field['before'] : '';
    }

    public function field_after() {

      $output  = ( ! empty( $this->field['after'] ) ) ? $this->field['after'] : '';
      $output .= ( ! empty( $this->field['desc'] ) ) ? '<p class="kfw-text-desc">'. $this->field['desc'] .'</p>' : '';
      $output .= ( ! empty( $this->field['help'] ) ) ? '<span class="kfw-help"><span class="kfw-help-text">'. $this->field['help'] .'</span><span class="fa fa-question-circle"></span></span>' : '';
      $output .= ( ! empty( $this->field['_error'] ) ) ? '<p class="kfw-text-error">'. $this->field['_error'] .'</p>' : '';

      return $output;

    }

  }
}
