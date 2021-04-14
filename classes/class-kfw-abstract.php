<?php
/**
 * Abstract Class
 *
 * @package K Framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

if ( ! class_exists( 'KFW_Abstract' ) ) {

	/**
	 *
	 * Abstract Class
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	abstract class KFW_Abstract {

		/**
		 * Abstract
		 *
		 * @var string
		 */
		public $abstract = '';

		/**
		 * Css
		 *
		 * @var string
		 */
		public $output_css = '';

		/**
		 * Typographies
		 *
		 * @var array
		 */
		public $typographies = array();

		/**
		 * Constructor
		 */
		public function __construct() {

			// Check for embed google web fonts.
			if ( ! empty( $this->args['enqueue_webfont'] ) ) {
				add_action( 'wp_enqueue_scripts', array( &$this, 'add_enqueue_google_fonts' ), 100 );
			}

			// Check for embed custom css styles.
			if ( ! empty( $this->args['output_css'] ) ) {
				add_action( 'wp_head', array( &$this, 'add_output_css' ), 100 );
			}
		}

		/**
		 * Google fonts styles & scripts
		 *
		 * @return void
		 */
		public function add_enqueue_google_fonts() {

			if ( ! empty( $this->pre_fields ) ) {

				foreach ( $this->pre_fields as $field ) {

					$field_id     = ( ! empty( $field['id'] ) ) ? $field['id'] : '';
					$field_type   = ( ! empty( $field['type'] ) ) ? $field['type'] : '';
					$field_output = ( ! empty( $field['output'] ) ) ? $field['output'] : '';
					$field_check  = ( 'typography' === $field_type || $field_output ) ? true : false;

					if ( $field_type && $field_id ) {

						KFW::maybe_include_field( $field_type );

						$class_name = 'KFW_Field_' . $field_type;

						if ( class_exists( $class_name ) ) {

							if ( method_exists( $class_name, 'output' ) || method_exists( $class_name, 'enqueue_google_fonts' ) ) {

								$field_value = '';

								if ( $field_check && ( 'options' === $this->abstract || 'customize' === $this->abstract ) ) {
									$field_value = ( isset( $this->options[ $field_id ] ) && '' !== $this->options[ $field_id ] ) ? $this->options[ $field_id ] : '';
								} elseif ( $field_check && 'metabox' === $this->abstract ) {
									$field_value = $this->get_meta_value( $field );
								}

								$instance = new $class_name( $field, $field_value, $this->unique, 'wp/enqueue', $this );

								// typography enqueue and embed google web fonts.
								if ( 'typography' === $field_type && $this->args['enqueue_webfont'] && ! empty( $field_value['font-family'] ) ) {
										$instance->enqueue_google_fonts();
								}

								// output css.
								if ( $field_output && $this->args['output_css'] ) {
										$instance->output();
								}

								unset( $instance );
							}
						}
					}
				}
			}

			if ( ! empty( $this->typographies ) && empty( $this->args['async_webfont'] ) ) {

				$query  = array( 'family' => implode( '|', $this->typographies ) );
				$api    = '//fonts.googleapis.com/css';
				$handle = 'kfw-google-web-fonts-' . $this->unique;
				$src    = esc_url( add_query_arg( $query, $api ) );

				wp_enqueue_style( $handle, $src, array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
			}

			if ( ! empty( $this->typographies ) && ! empty( $this->args['async_webfont'] ) ) {

				$api   = '//ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js';
				$html  = '<script type="text/javascript">';
				$html .= 'WebFontConfig={google:{families:[' . "'" . implode( "','", $this->typographies ) . "'" . ']}};';
				$html .= '!function(e){var t=e.createElement("script"),s=e.scripts[0];t.src="' . $api . '",t.async=!0,s.parentNode.insertBefore(t,s)}(document);';
				$html .= '</script>';
				echo wp_kses(
					$html,
					array(
						'script' => array(
							'type' => true,
						),
					)
				);
			}

		}

		/**
		 * Style
		 *
		 * @return void
		 */
		public function add_output_css() {

			$this->output_css = apply_filters( "kfw_{$this->unique}_output_css", $this->output_css, $this );

			if ( ! empty( $this->output_css ) ) {
				echo wp_kses(
					'<style type="text/css">' . $this->output_css . '</style>',
					array(
						'style' => array(
							'type' => true,
							'id'   => true,
						),
					)
				);
			}

		}

	}
}

