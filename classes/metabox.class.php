<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access directly.
/**
 *
 * Metabox Class
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if( ! class_exists( 'KFW_Metabox' ) ) {
  class KFW_Metabox extends KFW_Abstract{

    // constans
    public $unique         = '';
    public $abstract       = 'metabox';
    public $pre_fields     = array();
    public $sections       = array();
    public $post_type      = array();
    public $args           = array(
      'title'              => '',
      'post_type'          => 'post',
      'data_type'          => 'serialize',
      'context'            => 'advanced',
      'priority'           => 'default',
      'exclude_post_types' => array(),
      'page_templates'     => '',
      'post_formats'       => '',
      'show_restore'       => false,
      'enqueue_webfont'    => true,
      'async_webfont'      => false,
      'output_css'         => true,
      'theme'              => 'dark',
      'class'              => '',
      'defaults'           => array(),
    );

    // run metabox construct
    public function __construct( $key, $params = array() ) {

      $this->unique         = $key;
      $this->args           = apply_filters( "kfw_{$this->unique}_args", wp_parse_args( $params['args'], $this->args ), $this );
      $this->sections       = apply_filters( "kfw_{$this->unique}_sections", $params['sections'], $this );
      $this->post_type      = ( is_array( $this->args['post_type'] ) ) ? $this->args['post_type'] : array_filter( (array) $this->args['post_type'] );
      $this->post_formats   = ( is_array( $this->args['post_formats'] ) ) ? $this->args['post_formats'] : array_filter( (array) $this->args['post_formats'] );
      $this->page_templates = ( is_array( $this->args['page_templates'] ) ) ? $this->args['page_templates'] : array_filter( (array) $this->args['page_templates'] );
      $this->pre_fields     = $this->pre_fields( $this->sections );

      add_action( 'add_meta_boxes', array( &$this, 'add_meta_box' ) );
      add_action( 'save_post', array( &$this, 'save_meta_box' ) );
      add_action( 'edit_attachment', array( &$this, 'save_meta_box' ) );

      if( ! empty( $this->page_templates ) || ! empty( $this->post_formats ) ) {
        foreach( $this->post_type as $post_type ) {
          add_filter( 'postbox_classes_'. $post_type .'_'. $this->unique, array( &$this, 'add_metabox_classes' ) );
        }
      }

      // wp enqeueu for typography and output css
      parent::__construct();

    }

    // instance
    public static function instance( $key, $params = array() ) {
      return new self( $key, $params );
    }

    public function pre_fields( $sections ) {

      $result  = array();

      foreach( $sections as $key => $section ) {
        if( ! empty( $section['fields'] ) ) {
          foreach( $section['fields'] as $field ) {
            $result[] = $field;
          }
        }
      }

      return $result;
    }

    public function add_metabox_classes( $classes ) {

      global $post;

      if( ! empty( $this->post_formats ) ) {

        $saved_post_format = ( is_object( $post ) ) ? get_post_format( $post ) : false;
        $saved_post_format = ( ! empty( $saved_post_format ) ) ? $saved_post_format : 'default';

        $classes[] = 'kfw-post-formats';

        // Sanitize post format for standard to default
        if( ( $key = array_search( 'standard', $this->post_formats ) ) !== false ) {
          $this->post_formats[$key] = 'default';
        }

        foreach( $this->post_formats as $format ) {
          $classes[] = 'kfw-post-format-'. $format;
        }

        if( ! in_array( $saved_post_format, $this->post_formats ) ) {
          $classes[] = 'kfw-hide';
        } else {
          $classes[] = 'kfw-show';
        }

      }

      if( ! empty( $this->page_templates ) ) {

        $saved_template = ( is_object( $post ) && ! empty( $post->page_template ) ) ? $post->page_template : 'default';

        $classes[] = 'kfw-page-templates';

        foreach( $this->page_templates as $template ) {
          $classes[] = 'kfw-page-'. preg_replace( '/[^a-zA-Z0-9]+/', '-', strtolower( $template ) );
        }

        if( ! in_array( $saved_template, $this->page_templates ) ) {
          $classes[] = 'kfw-hide';
        } else {
          $classes[] = 'kfw-show';
        }

      }

      return $classes;

    }

    // add metabox
    public function add_meta_box( $post_type ) {

      if( ! in_array( $post_type, $this->args['exclude_post_types'] ) ) {
        add_meta_box( $this->unique, $this->args['title'], array( &$this, 'add_meta_box_content' ), $this->post_type, $this->args['context'], $this->args['priority'], $this->args );
      }

    }

    // get default value
    public function get_default( $field ) {

      $default = ( isset( $this->args['defaults'][$field['id']] ) ) ? $this->args['defaults'][$field['id']] : '';
      $default = ( isset( $field['default'] ) ) ? $field['default'] : $default;

      return $default;

    }

    // get meta value
    public function get_meta_value( $field ) {

      global $post;

      $value = '';

      if( is_object( $post ) && ! empty( $field['id'] ) ) {
       
        $meta    = get_post_meta( $post->ID, $this->unique, true );
        $value   = ( isset( $meta[$field['id']] ) ) ? $meta[$field['id']] : null;
        $default = $this->get_default( $field );
        $value   = ( isset( $value ) ) ? $value : $default;

      }

      return $value;

    }

    // add metabox content
    public function add_meta_box_content( $post, $callback ) {

      global $post;

      wp_nonce_field( 'kfw_metabox_nonce', 'kfw_metabox_nonce'. $this->unique );

      echo '<div class="kfw kfw-metabox kfw-theme-light">';

        echo '<div class="kfw-wrapper kfw-show-all">';

          echo '<div class="kfw-content">';

            echo '<div class="kfw-sections">';

            foreach( $this->sections as $section ) {

              echo '<div class="kfw-section kfw-onload">';

              $section_icon  = ( ! empty( $section['icon'] ) ) ? '<i class="kfw-icon '. esc_attr( $section['icon'] ) .'"></i>' : '';
              $section_title = ( ! empty( $section['title'] ) ) ? esc_attr( $section['title'] ) : '';

              echo ( $section_title || $section_icon ) ? '<div class="kfw-section-title"><h3>'. ( $section_icon ) . ( $section_title ) .'</h3></div>' : '';

              if( ! empty( $section['fields'] ) ) {

                foreach ( $section['fields'] as $field ) {
                  KFW::field( $field, $this->get_meta_value( $field ), $this->unique, 'metabox' );
                }

              } else {

                echo '<div class="kfw-no-option kfw-text-muted">'. esc_html__( 'No option provided by developer.', 'kfw' ) .'</div>';

              }

              echo '</div>';

            }

            echo '</div>';

            echo '<div class="clear"></div>';

          echo '</div>';

          echo '<div class="clear"></div>';

        echo '</div>';

      echo '</div>';

    }

    // save metabox
    public function save_meta_box( $post_id ) {

      $nonce = 'kfw_metabox_nonce'. $this->unique;

      if( ! isset( $_POST[$nonce] ) && ! wp_verify_nonce( $_POST[$nonce], 'kfw_metabox_nonce' ) ) {
        return $post_id;
      }

      if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return $post_id;
      }

      update_post_meta( $post_id, $this->unique, wp_unslash( (array) $_POST[$this->unique] ) );

    }
  }
}
