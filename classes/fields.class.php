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

  }
}
