<?php
/**
 * Setup Class
 *
 * @package Kemet Framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.


if ( ! class_exists( 'KFW' ) ) {

	/**
	 *
	 * Setup Class
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	class KFW {

		/**
		 * Version
		 *
		 * @var string
		 */
		public static $version = '2.0.9';

		/**
		 * Dir
		 *
		 * @var string
		 */
		public static $dir = null;

		/**
		 * Url
		 *
		 * @var string
		 */
		public static $url = null;

		/**
		 * Inited
		 *
		 * @var array
		 */
		public static $inited = array();

		/**
		 * Fields
		 *
		 * @var array
		 */
		public static $fields = array();

		/**
		 * Arguments
		 *
		 * @var array
		 */
		public static $args = array(
			'options'          => array(),
			'metaboxes'        => array(),
			'nav_menu_options' => array(),
		);

		/**
		 * Shortcode
		 *
		 * @var array
		 */
		public static $shortcode_instances = array();

		/**
		 * Init
		 *
		 * @return void
		 */
		public static function init() {

			// init action.
			do_action( 'kfw_init' );

			// set constants.
			self::constants();

			// include files.
			self::includes();

			add_action( 'after_setup_theme', array( 'KFW', 'setup' ) );
			add_action( 'init', array( 'KFW', 'setup' ) );
			add_action( 'switch_theme', array( 'KFW', 'setup' ) );
			add_action( 'admin_enqueue_scripts', array( 'KFW', 'add_admin_enqueue_scripts' ), 1 );
		}

		/**
		 * Setup
		 *
		 * @return void
		 */
		public static function setup() {

			// setup options.
			$params = array();
			if ( ! empty( self::$args['options'] ) ) {
				foreach ( self::$args['options'] as $key => $value ) {
					if ( ! empty( self::$args['sections'][ $key ] ) && ! isset( self::$inited[ $key ] ) ) {
						$params['args']       = $value;
						$params['sections']   = self::$args['sections'][ $key ];
						self::$inited[ $key ] = true;

						KFW_Options::instance( $key, $params );
					}
				}
			}

			// setup metaboxes.
			$params = array();
			if ( ! empty( self::$args['metaboxes'] ) ) {
				foreach ( self::$args['metaboxes'] as $key => $value ) {
					if ( ! empty( self::$args['sections'][ $key ] ) && ! isset( self::$inited[ $key ] ) ) {
						$params['args']       = $value;
						$params['sections']   = self::$args['sections'][ $key ];
						self::$inited[ $key ] = true;

						KFW_Metabox::instance( $key, $params );
					}
				}
			}

			// Setup nav menu option framework.
			$params = array();
			if ( ! empty( self::$args['nav_menu_options'] ) ) {
				foreach ( self::$args['nav_menu_options'] as $key => $value ) {
					if ( ! empty( self::$args['sections'][ $key ] ) && ! isset( self::$inited[ $key ] ) ) {
						$params['args']       = $value;
						$params['sections']   = self::$args['sections'][ $key ];
						self::$inited[ $key ] = true;

						KFW_Nav_Menu_Options::instance( $key, $params );
					}
				}
			}

			do_action( 'kfw_loaded' );
		}

		/**
		 * Create options
		 *
		 * @param string $id option id.
		 * @param array  $args arguments.
		 * @return void
		 */
		public static function create_options( $id, $args = array() ) {
			self::$args['options'][ $id ] = $args;
		}

		/**
		 * Create metabox options
		 *
		 * @param string $id option id.
		 * @param array  $args option arguments.
		 * @return void
		 */
		public static function create_metabox( $id, $args = array() ) {
			self::$args['metaboxes'][ $id ] = $args;
		}

		/**
		 * Create menu options
		 *
		 * @param string $id option id.
		 * @param array  $args option arguments.
		 * @return void
		 */
		public static function create_nav_menu_options( $id, $args = array() ) {
			self::$args['nav_menu_options'][ $id ] = $args;
		}

		/**
		 * Create section
		 *
		 * @param string $id section id.
		 * @param array  $sections section attrs.
		 * @return void
		 */
		public static function create_section( $id, $sections ) {
			self::$args['sections'][ $id ][] = $sections;
			self::set_used_fields( $sections );
		}

		/**
		 * Constants
		 *
		 * @return void
		 */
		public static function constants() {

			// we need this path-finder code for set URL of framework.
			$dirname        = wp_normalize_path( dirname( dirname( __FILE__ ) ) );
			$theme_dir      = wp_normalize_path( get_parent_theme_file_path() );
			$plugin_dir     = wp_normalize_path( WP_PLUGIN_DIR );
			$located_plugin = ( preg_match( '#' . self::sanitize_dirname( $plugin_dir ) . '#', self::sanitize_dirname( $dirname ) ) ) ? true : false;
			$directory      = ( $located_plugin ) ? $plugin_dir : $theme_dir;
			$directory_uri  = ( $located_plugin ) ? plugins_url() : get_parent_theme_file_uri();
			$foldername     = str_replace( $directory, '', $dirname );

			self::$dir = $dirname;
			self::$url = $directory_uri . $foldername;
		}

		/**
		 * Include file
		 *
		 * @param string  $file file.
		 * @param boolean $load load it.
		 * @return string
		 */
		public static function include_plugin_file( $file, $load = true ) {
			$path     = '';
			$file     = ltrim( $file, '/' );
			$override = apply_filters( 'kfw_override', 'kfw-override' );

			if ( file_exists( get_parent_theme_file_path( $override . '/' . $file ) ) ) {
				$path = get_parent_theme_file_path( $override . '/' . $file );
			} elseif ( file_exists( get_theme_file_path( $override . '/' . $file ) ) ) {
				$path = get_theme_file_path( $override . '/' . $file );
			} elseif ( file_exists( self::$dir . '/' . $override . '/' . $file ) ) {
				$path = self::$dir . '/' . $override . '/' . $file;
			} elseif ( file_exists( self::$dir . '/' . $file ) ) {
				$path = self::$dir . '/' . $file;
			}

			if ( ! empty( $path ) && ! empty( $file ) && $load ) {
				global $wp_query;

				if ( is_object( $wp_query ) && function_exists( 'load_template' ) ) {
					load_template( $path, true );
				} else {
					require_once $path;
				}
			} else {
				return self::$dir . '/' . $file;
			}
		}

		/**
		 * Plugin activate
		 *
		 * @param string $file plugin path.
		 * @return boolean
		 */
		public static function is_active_plugin( $file = '' ) {
			return in_array( $file, (array) get_option( 'active_plugins', array() ) );
		}

		/**
		 * Sanitize dirname
		 *
		 * @param string $dirname dir name.
		 * @return string
		 */
		public static function sanitize_dirname( $dirname ) {
			return preg_replace( '/[^A-Za-z]/', '', $dirname );
		}

		/**
		 * Set plugin url
		 *
		 * @param string $file file name.
		 * @return string
		 */
		public static function include_plugin_url( $file ) {
			return self::$url . '/' . ltrim( $file, '/' );
		}

		/**
		 * General includes
		 *
		 * @return void
		 */
		public static function includes() {
			// Functions.
			self::include_plugin_file( 'functions/helpers.php' );
			self::include_plugin_file( 'functions/actions.php' );
			// Classes.
			self::include_plugin_file( 'classes/class-kfw-abstract.php' );
			self::include_plugin_file( 'classes/class-kfw-fields.php' );
			self::include_plugin_file( 'classes/class-kfw-metabox.php' );
			self::include_plugin_file( 'classes/class-kfw-nav-menu-options.php' );
			self::include_plugin_file( 'classes/class-kfw-options.php' );
		}

		/**
		 * Include field
		 *
		 * @param string $type file type.
		 * @return void
		 */
		public static function maybe_include_field( $type = '' ) {
			if ( ! class_exists( 'KFW_Field_' . ucfirst( $type ) ) && class_exists( 'KFW_Fields' ) ) {
				self::include_plugin_file( 'fields/' . $type . '/class-kfw-field-' . $type . '.php' );
			}
		}

		/**
		 * Get all of fields
		 *
		 * @param array $sections all sections.
		 * @return void
		 */
		public static function set_used_fields( $sections ) {
			if ( ! empty( $sections['fields'] ) ) {
				foreach ( $sections['fields'] as $field ) {
					if ( ! empty( $field['fields'] ) ) {
						self::set_used_fields( $field );
					}

					if ( ! empty( $field['tabs'] ) ) {
						self::set_used_fields( array( 'fields' => $field['tabs'] ) );
					}

					if ( ! empty( $field['accordions'] ) ) {
						self::set_used_fields( array( 'fields' => $field['accordions'] ) );
					}

					if ( ! empty( $field['type'] ) ) {
						self::$fields[ $field['type'] ] = $field;
					}
				}
			}
		}

		/**
		 * Enqueue admin and fields styles and scripts
		 *
		 * @return void
		 */
		public static function add_admin_enqueue_scripts() {
			global $wp_version;

			// check for developer mode.
			$min = ( apply_filters( 'kfw_dev_mode', false ) || SCRIPT_DEBUG ) ? '' : '.min';

			// admin utilities.
			wp_enqueue_media();

			// wp color picker.
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );

			/*
			 * This is only needed in WordPress version >= 5.5 because wpColorPickerL10n has been removed.
			 *
			 * @see https://github.com/WordPress/WordPress/commit/7e7b70cd1ae5772229abb769d0823411112c748b
			 *
			 * This is should be removed once the issue is fixed from wp-color-picker-alpha repo.
			 * @see https://github.com/kallookoo/wp-color-picker-alpha/issues/35
			 */
			if ( version_compare( $wp_version, '5.4.99', '>=' ) ) {
				wp_localize_script(
					'wp-color-picker',
					'wpColorPickerL10n',
					array(
						'clear'            => __( 'Clear', 'kfw' ),
						'clearAriaLabel'   => __( 'Clear color', 'kfw' ),
						'defaultString'    => __( 'Default', 'kfw' ),
						'defaultAriaLabel' => __( 'Select default color', 'kfw' ),
						'pick'             => __( 'Select Color', 'kfw' ),
						'defaultLabel'     => __( 'Color value', 'kfw' ),
					)
				);
			}

			// cdn styles.
			wp_enqueue_style( 'kfw-fa', self::include_plugin_url( 'assets/css/font-awesome' . $min . '.css' ), array(), '4.7.0', 'all' );

			// framework core styles.
			wp_enqueue_style( 'kfw', self::include_plugin_url( 'assets/css/kfw' . $min . '.css' ), array(), '1.0.0', 'all' );

			// rtl styles.
			if ( is_rtl() ) {
				wp_enqueue_style( 'kfw-rtl', self::include_plugin_url( 'assets/css/kfw-rtl' . $min . '.css' ), array(), '1.0.0', 'all' );
			}

			// framework core scripts.
			wp_enqueue_script( 'kfw-plugins', self::include_plugin_url( 'assets/js/kfw-plugins' . $min . '.js' ), array(), '1.0.0', true );
			wp_enqueue_script( 'kfw', self::include_plugin_url( 'assets/js/kfw' . $min . '.js' ), array( 'kfw-plugins' ), '1.0.0', true );

			wp_localize_script(
				'kfw',
				'kfw_vars',
				array(
					'color_palette' => apply_filters( 'kfw_color_palette', array() ),
					'i18n'          => array(
						'confirm'             => esc_html__( 'Are you sure?', 'kfw' ),
						'reset_notification'  => esc_html__( 'Restoring options.', 'kfw' ),
						'import_notification' => esc_html__( 'Importing options.', 'kfw' ),
					),
				)
			);

			// load admin enqueue scripts and styles.
			$enqueued = array();

			if ( ! empty( self::$fields ) ) {
				foreach ( self::$fields as $field ) {
					if ( ! empty( $field['type'] ) ) {
						$classname = 'KFW_Field_' . ucfirst( $field['type'] );
						self::maybe_include_field( $field['type'] );
						if ( class_exists( $classname ) && method_exists( $classname, 'enqueue' ) ) {
							$instance = new $classname( $field );
							if ( method_exists( $classname, 'enqueue' ) ) {
								$instance->enqueue();
							}
							unset( $instance );
						}
					}
				}
			}

			do_action( 'kfw_enqueue' );
		}

		/**
		 * Add a new framework field
		 *
		 * @param array  $field field options.
		 * @param string $value value.
		 * @param string $unique key.
		 * @param string $where location.
		 * @param string $parent parent.
		 * @return void
		 */
		public static function field( $field = array(), $value = '', $unique = '', $where = '', $parent = '' ) {

			// Check for unallow fields.
			if ( ! empty( $field['_notice'] ) ) {
				$field_type = $field['type'];

				$field = array();
				/* translators: %s: field type */
				$field['content'] = sprintf( esc_html__( 'Ooops! This field type (%s) can not be used here, yet.', 'kfw' ), '<strong>' . $field_type . '</strong>' );
				$field['type']    = 'notice';
				$field['style']   = 'danger';
			}

			$depend     = '';
			$hidden     = '';
			$unique     = ( ! empty( $unique ) ) ? $unique : '';
			$class      = ( ! empty( $field['class'] ) ) ? ' ' . $field['class'] : '';
			$is_pseudo  = ( ! empty( $field['pseudo'] ) ) ? ' kfw-pseudo-field' : '';
			$field_type = ( ! empty( $field['type'] ) ) ? $field['type'] : '';

			if ( ! empty( $field['dependency'] ) ) {
				$dependency      = $field['dependency'];
				$hidden          = ' hidden';
				$data_controller = '';
				$data_condition  = '';
				$data_value      = '';
				$data_global     = '';

				if ( is_array( $dependency[0] ) ) {
					$data_controller = implode( '|', array_column( $dependency, 0 ) );
					$data_condition  = implode( '|', array_column( $dependency, 1 ) );
					$data_value      = implode( '|', array_column( $dependency, 2 ) );
					$data_global     = implode( '|', array_column( $dependency, 3 ) );
				} else {
					$data_controller = ( ! empty( $dependency[0] ) ) ? $dependency[0] : '';
					$data_condition  = ( ! empty( $dependency[1] ) ) ? $dependency[1] : '';
					$data_value      = ( ! empty( $dependency[2] ) ) ? $dependency[2] : '';
					$data_global     = ( ! empty( $dependency[3] ) ) ? $dependency[3] : '';
				}

				$depend .= ' data-controller="' . $data_controller . '"';
				$depend .= ' data-condition="' . $data_condition . '"';
				$depend .= ' data-value="' . $data_value . '"';
				$depend .= ( ! empty( $data_global ) ) ? ' data-depend-global="true"' : '';
			}

			if ( ! empty( $field_type ) ) {
				echo wp_kses( '<div class="kfw-field kfw-field-' . $field_type . $is_pseudo . $class . $hidden . '"' . $depend . '>', kfw_allowed_html( array( 'div' ) ) );

				if ( ! empty( $field['title'] ) ) {
					$subtitle = ( ! empty( $field['subtitle'] ) ) ? '<p class="kfw-text-subtitle">' . $field['subtitle'] . '</p>' : '';
					echo wp_kses( '<div class="kfw-title"><h4>' . $field['title'] . '</h4>' . $subtitle . '</div>', kfw_allowed_html( 'all' ) );
				}

				echo ( ! empty( $field['title'] ) ) ? wp_kses( '<div class="kfw-fieldset">', kfw_allowed_html( array( 'div' ) ) ) : '';

				$value = ( ! isset( $value ) && isset( $field['default'] ) ) ? $field['default'] : $value;
				$value = ( isset( $field['value'] ) ) ? $field['value'] : $value;

				self::maybe_include_field( $field_type );

				$classname = 'KFW_Field_' . ucfirst( str_replace( '-', '_', $field_type ) );

				if ( class_exists( $classname ) ) {
					$instance = new $classname( $field, $value, $unique, $where, $parent );
					$instance->render();
				} else {
					echo '<p>' . esc_html__( 'This field class is not available!', 'kfw' ) . '</p>';
				}
			} else {
				echo '<p>' . esc_html__( 'This type is not found!', 'kfw' ) . '</p>';
			}

			echo ( ! empty( $field['title'] ) ) ? '</div>' : '';
			echo '<div class="clear"></div>';
			echo '</div>';
		}
	}

	KFW::init();
}
