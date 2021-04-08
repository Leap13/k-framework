<?php
/**
 * Options Class
 *
 * @package Kemet Framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

if ( ! class_exists( 'KFW_Options' ) ) {

	/**
	 *
	 * Options Class
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	class KFW_Options extends KFW_Abstract {

		/**
		 * Unique key
		 *
		 * @var string
		 */
		public $unique = '';

		/**
		 * Notice
		 *
		 * @var string
		 */
		public $notice = '';

		/**
		 * Abstract type
		 *
		 * @var string
		 */
		public $abstract = 'options';

		/**
		 * Sections
		 *
		 * @var array
		 */
		public $sections = array();

		/**
		 * Options
		 *
		 * @var array
		 */
		public $options = array();

		/**
		 * Errors list
		 *
		 * @var array
		 */
		public $errors = array();

		/**
		 * Pre tabs
		 *
		 * @var array
		 */
		public $pre_tabs = array();

		/**
		 * Pre fields
		 *
		 * @var array
		 */
		public $pre_fields = array();

		/**
		 * Pre sections
		 *
		 * @var array
		 */
		public $pre_sections = array();

		/**
		 * Args
		 *
		 * @var array
		 */
		public $args = array(

			// framework title.
			'framework_title'         => 'Welcome to <strong>Kfw Theme </strong><small>by Leap13</small>',
			'framework_class'         => '',

			// menu settings.
			'menu_title'              => '',
			'menu_slug'               => '',
			'menu_type'               => 'menu',
			'menu_capability'         => 'manage_options',
			'menu_icon'               => null,
			'menu_position'           => null,
			'menu_hidden'             => false,
			'menu_parent'             => '',

			// menu extras.
			'show_bar_menu'           => true,
			'show_sub_menu'           => true,
			'show_network_menu'       => true,
			'show_in_customizer'      => false,

			'show_search'             => true,
			'show_reset_all'          => true,
			'show_reset_section'      => true,
			'show_footer'             => true,
			'show_all_options'        => true,
			'sticky_header'           => true,
			'save_defaults'           => true,
			'ajax_save'               => true,

			// admin bar menu settings.
			'admin_bar_menu_icon'     => '',
			'admin_bar_menu_priority' => 80,

			// footer.
			'footer_text'             => '',
			'footer_after'            => '',
			'footer_credit'           => '',

			// database model.
			'database'                => '', // options, transient, theme_mod, network.
			'transient_time'          => 0,

			// contextual help.
			'contextual_help'         => array(),
			'contextual_help_sidebar' => '',

			// typography options.
			'enqueue_webfont'         => true,
			'async_webfont'           => false,

			// others.
			'output_css'              => true,

			// theme.
			'theme'                   => 'dark',
			'class'                   => '',

			// external default values.
			'defaults'                => array(),

		);

		/**
		 * Constructor
		 *
		 * @param string $key unique key.
		 * @param array  $params parameterds.
		 */
		public function __construct( $key, $params = array() ) {
			$this->unique   = $key;
			$this->args     = apply_filters( "kfw_{$this->unique}_args", wp_parse_args( $params['args'], $this->args ), $this );
			$this->sections = apply_filters( "kfw_{$this->unique}_sections", $params['sections'], $this );

			// run only is admin panel options, avoid performance loss.
			$this->pre_tabs     = $this->pre_tabs( $this->sections );
			$this->pre_fields   = $this->pre_fields( $this->sections );
			$this->pre_sections = $this->pre_sections( $this->sections );

			$this->get_options();
			$this->set_options();
			$this->save_defaults();

			add_action( 'admin_menu', array( &$this, 'add_admin_menu' ) );
			add_action( 'admin_bar_menu', array( &$this, 'add_admin_bar_menu' ), $this->args['admin_bar_menu_priority'] );
			add_action( 'wp_ajax_kfw_' . $this->unique . '_ajax_save', array( &$this, 'ajax_save' ) );

			if ( ! empty( $this->args['show_network_menu'] ) ) {
				add_action( 'network_admin_menu', array( &$this, 'add_admin_menu' ) );
			}

			// wp enqeueu for typography and output css.
			parent::__construct();
		}

		/**
		 * Instance
		 *
		 * @param string $key unique key.
		 * @param array  $params parameters.
		 * @return object
		 */
		public static function instance( $key, $params = array() ) {
			return new self( $key, $params );
		}

		/**
		 * Pre fields
		 *
		 * @param array $sections sections.
		 * @return array
		 */
		public function pre_tabs( $sections ) {
			$result  = array();
			$parents = array();
			$count   = 100;

			foreach ( $sections as $key => $section ) {
				if ( ! empty( $section['parent'] ) ) {
					$section['priority']             = ( isset( $section['priority'] ) ) ? $section['priority'] : $count;
					$parents[ $section['parent'] ][] = $section;
					unset( $sections[ $key ] );
				}
				$count++;
			}

			foreach ( $sections as $key => $section ) {
				$section['priority'] = ( isset( $section['priority'] ) ) ? $section['priority'] : $count;
				if ( ! empty( $section['id'] ) && ! empty( $parents[ $section['id'] ] ) ) {
					$section['subs'] = wp_list_sort( $parents[ $section['id'] ], array( 'priority' => 'ASC' ), 'ASC', true );
				}
				$result[] = $section;
				$count++;
			}

			return wp_list_sort( $result, array( 'priority' => 'ASC' ), 'ASC', true );
		}

		/**
		 * Pre fields
		 *
		 * @param array $sections sections.
		 * @return array
		 */
		public function pre_fields( $sections ) {
			$result = array();

			foreach ( $sections as $key => $section ) {
				if ( ! empty( $section['fields'] ) ) {
					foreach ( $section['fields'] as $field ) {
						$result[] = $field;
					}
				}
			}

			return $result;
		}

		/**
		 * Pre sections
		 *
		 * @param array $sections sections.
		 * @return array
		 */
		public function pre_sections( $sections ) {
			$result = array();

			foreach ( $this->pre_tabs as $tab ) {
				if ( ! empty( $tab['subs'] ) ) {
					foreach ( $tab['subs'] as $sub ) {
						$result[] = $sub;
					}
				}
				if ( empty( $tab['subs'] ) ) {
					$result[] = $tab;
				}
			}

			return $result;
		}

		/**
		 * Add item to admin bar
		 *
		 * @param object $wp_admin_bar admin bar object.
		 * @return void
		 */
		public function add_admin_bar_menu( $wp_admin_bar ) {
			if ( ! empty( $this->args['show_bar_menu'] ) && empty( $this->args['menu_hidden'] ) ) {
				global $submenu;

				$menu_slug = $this->args['menu_slug'];
				$menu_icon = ( ! empty( $this->args['admin_bar_menu_icon'] ) ) ? '<span class="kfw-ab-icon ab-icon ' . $this->args['admin_bar_menu_icon'] . '"></span>' : '';

				$wp_admin_bar->add_node(
					array(
						'id'    => $menu_slug,
						'title' => $menu_icon . $this->args['menu_title'],
						'href'  => ( is_network_admin() ) ? network_admin_url( 'admin.php?page=' . $menu_slug ) : admin_url( 'admin.php?page=' . $menu_slug ),
					)
				);

				if ( ! empty( $submenu[ $menu_slug ] ) ) {
					foreach ( $submenu[ $menu_slug ] as $key => $menu ) {
						$wp_admin_bar->add_node(
							array(
								'parent' => $menu_slug,
								'id'     => $menu_slug . '-' . $key,
								'title'  => $menu[0],
								'href'   => ( is_network_admin() ) ? network_admin_url( 'admin.php?page=' . $menu[2] ) : admin_url( 'admin.php?page=' . $menu[2] ),
							)
						);
					}
				}

				if ( ! empty( $this->args['show_network_menu'] ) ) {
					$wp_admin_bar->add_node(
						array(
							'parent' => 'network-admin',
							'id'     => $menu_slug . '-network-admin',
							'title'  => $menu_icon . $this->args['menu_title'],
							'href'   => network_admin_url( 'admin.php?page=' . $menu_slug ),
						)
					);
				}
			}
		}

		/**
		 * Save data via ajax
		 *
		 * @return void
		 */
		public function ajax_save() {
			if ( ! empty( $_POST['data'] ) ) {
				$_POST = isset( $_POST['data'] ) ? json_decode( wp_unslash( $_POST['data'] ), true ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

				if ( wp_verify_nonce( kfw_get_var( 'kfw_options_nonce' . $this->unique ), 'kfw_options_nonce' ) ) {
					$this->set_options();

					wp_send_json_success(
						array(
							'success' => true,
							'notice'  => $this->notice,
							'errors'  => $this->errors,
						)
					);
				}
			}

			wp_send_json_error(
				array(
					'success' => false,
					'error'   => esc_html__( 'Error while saving.', 'kfw' ),
				)
			);
		}

		/**
		 * Get default value
		 *
		 * @param array $field field.
		 * @param array $options options.
		 * @return array
		 */
		public function get_default( $field, $options = array() ) {
			$default = ( isset( $this->args['defaults'][ $field['id'] ] ) ) ? $this->args['defaults'][ $field['id'] ] : '';
			$default = ( isset( $field['default'] ) ) ? $field['default'] : $default;
			$default = ( isset( $options[ $field['id'] ] ) ) ? $options[ $field['id'] ] : $default;

			return $default;
		}

		/**
		 * Save defaults and set new fields value to main options
		 *
		 * @return void
		 */
		public function save_defaults() {
			$tmp_options = $this->options;

			foreach ( $this->pre_fields as $field ) {
				if ( ! empty( $field['id'] ) ) {
					$this->options[ $field['id'] ] = $this->get_default( $field, $this->options );
				}
			}

			if ( $this->args['save_defaults'] && empty( $tmp_options ) ) {
				$this->save_options( $this->options );
			}
		}

		/**
		 * Set options
		 *
		 * @return boolean
		 */
		public function set_options() {
			if ( wp_verify_nonce( kfw_get_var( 'kfw_options_nonce' . $this->unique ), 'kfw_options_nonce' ) ) {
				$request    = kfw_get_var( $this->unique, array() );
				$transient  = kfw_get_var( 'kfw_transient' );
				$section_id = ( ! empty( $transient['section'] ) ) ? $transient['section'] : '';

				// import data.
				if ( ! empty( $transient['kfw_import_data'] ) ) {
					$import_data = json_decode( stripslashes( trim( $transient['kfw_import_data'] ) ), true );
					$request     = ( is_array( $import_data ) ) ? $import_data : array();

					$this->notice = esc_html__( 'Success. Imported backup options.', 'kfw' );
				} elseif ( ! empty( $transient['reset'] ) ) {
					foreach ( $this->pre_fields as $field ) {
						if ( ! empty( $field['id'] ) ) {
							$request[ $field['id'] ] = $this->get_default( $field );
						}
					}

					$this->notice = esc_html__( 'Default options restored.', 'kfw' );
				} elseif ( ! empty( $transient['reset_section'] ) && ! empty( $section_id ) ) {
					if ( ! empty( $this->pre_sections[ $section_id - 1 ]['fields'] ) ) {
						foreach ( $this->pre_sections[ $section_id - 1 ]['fields'] as $field ) {
							if ( ! empty( $field['id'] ) ) {
								$request[ $field['id'] ] = $this->get_default( $field );
							}
						}
					}

					$this->notice = esc_html__( 'Default options restored for only this section.', 'kfw' );
				} else {

					// sanitize and validate.
					foreach ( $this->pre_fields as $field ) {
						if ( ! empty( $field['id'] ) ) {

							// sanitize.
							if ( ! empty( $field['sanitize'] ) ) {
								$sanitize                = $field['sanitize'];
								$value_sanitize          = isset( $request[ $field['id'] ] ) ? $request[ $field['id'] ] : '';
								$request[ $field['id'] ] = call_user_func( $sanitize, $value_sanitize );
							}

							// validate.
							if ( ! empty( $field['validate'] ) ) {
								$value_validate = isset( $request[ $field['id'] ] ) ? $request[ $field['id'] ] : '';
								$has_validated  = call_user_func( $field['validate'], $value_validate );

								if ( ! empty( $has_validated ) ) {
									$request[ $field['id'] ]      = ( isset( $this->options[ $field['id'] ] ) ) ? $this->options[ $field['id'] ] : '';
									$this->errors[ $field['id'] ] = $has_validated;
								}
							}

							// auto sanitize.
							if ( ! isset( $request[ $field['id'] ] ) || is_null( $request[ $field['id'] ] ) ) {
								$request[ $field['id'] ] = '';
							}
						}
					}
				}

				// ignore nonce requests.
				if ( isset( $request['_nonce'] ) ) {
					unset( $request['_nonce'] );
				}

				$request = wp_unslash( $request );

				$request = apply_filters( "kfw_{$this->unique}_save", $request, $this );

				do_action( "kfw_{$this->unique}_save_before", $request, $this );

				$this->options = $request;

				$this->save_options( $request );

				do_action( "kfw_{$this->unique}_save_after", $request, $this );

				if ( empty( $this->notice ) ) {
					$this->notice = esc_html__( 'Settings saved.', 'kfw' );
				}
			}

			return true;
		}

		/**
		 * Save options database
		 *
		 * @param object $request request object.
		 * @return void
		 */
		public function save_options( $request ) {
			if ( 'transient' === $this->args['database'] ) {
				set_transient( $this->unique, $request, $this->args['transient_time'] );
			} elseif ( 'theme_mod' === $this->args['database'] ) {
				set_theme_mod( $this->unique, $request );
			} elseif ( 'network' === $this->args['database'] ) {
				update_site_option( $this->unique, $request );
			} else {
				update_option( $this->unique, $request );
			}

			do_action( "kfw_{$this->unique}_saved", $request, $this );
		}

		/**
		 * Get options from database
		 *
		 * @return mixed
		 */
		public function get_options() {
			if ( 'transient' === $this->args['database'] ) {
				$this->options = get_transient( $this->unique );
			} elseif ( 'theme_mod' === $this->args['database'] ) {
				$this->options = get_theme_mod( $this->unique );
			} elseif ( 'network' === $this->args['database'] ) {
				$this->options = get_site_option( $this->unique );
			} else {
				$this->options = get_option( $this->unique );
			}

			if ( empty( $this->options ) ) {
				$this->options = array();
			}

			return $this->options;
		}

		/**
		 * Add admin menu
		 *
		 * @return void
		 */
		public function add_admin_menu() {
			extract( $this->args ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract

			if ( 'submenu' === $menu_type ) {
				$menu_page = call_user_func( 'add_submenu_page', $menu_parent, $menu_title, $menu_title, $menu_capability, $menu_slug, array( &$this, 'add_options_html' ) );
			} else {
				$menu_page = call_user_func( 'add_menu_page', $menu_title, $menu_title, $menu_capability, $menu_slug, array( &$this, 'add_options_html' ), $menu_icon, $menu_position );

				if ( ! empty( $this->args['show_sub_menu'] ) && count( $this->pre_tabs ) > 1 ) {

					// create submenus.
					$tab_key = 1;
					foreach ( $this->pre_tabs as $section ) {
						call_user_func( 'add_submenu_page', $menu_slug, $section['title'], $section['title'], $menu_capability, $menu_slug . '#tab=' . $tab_key, '__return_null' );

						if ( ! empty( $section['subs'] ) ) {
							$tab_key += ( count( $section['subs'] ) - 1 );
						}

						$tab_key++;
					}

					remove_submenu_page( $menu_slug, $menu_slug );
				}

				if ( ! empty( $menu_hidden ) ) {
					remove_menu_page( $menu_slug );
				}
			}

			add_action( 'load-' . $menu_page, array( &$this, 'add_page_on_load' ) );
		}

		/**
		 * Add page on load
		 *
		 * @return void
		 */
		public function add_page_on_load() {
			if ( ! empty( $this->args['contextual_help'] ) ) {
				$screen = get_current_screen();

				foreach ( $this->args['contextual_help'] as $tab ) {
					$screen->add_help_tab( $tab );
				}

				if ( ! empty( $this->args['contextual_help_sidebar'] ) ) {
					$screen->set_help_sidebar( $this->args['contextual_help_sidebar'] );
				}
			}

			add_filter( 'admin_footer_text', array( &$this, 'add_admin_footer_text' ) );
		}

		/**
		 * Admin footer text
		 *
		 * @return void
		 */
		public function add_admin_footer_text() {
			$default = 'Thank you for creating with <a href="' . esc_url( 'http://codestarframework.com/' ) . '" target="_blank">Kemet Framework</a>';
			echo ! empty( $this->args['footer_credit'] ) ? wp_kses( $this->args['footer_credit'], kfw_allowed_html( 'all' ) ) : wp_kses( $default, kfw_allowed_html( array( 'a' ) ) );
		}

		/**
		 * Errprs checker
		 *
		 * @param array  $sections sections.
		 * @param string $err errors.
		 * @return array
		 */
		public function error_check( $sections, $err = '' ) {
			if ( ! $this->args['ajax_save'] ) {
				if ( ! empty( $sections['fields'] ) ) {
					foreach ( $sections['fields'] as $field ) {
						if ( ! empty( $field['id'] ) ) {
							if ( array_key_exists( $field['id'], $this->errors ) ) {
								$err = '<span class="kfw-label-error">!</span>';
							}
						}
					}
				}

				if ( ! empty( $sections['subs'] ) ) {
					foreach ( $sections['subs'] as $sub ) {
						$err = $this->error_check( $sub, $err );
					}
				}

				if ( ! empty( $sections['id'] ) && array_key_exists( $sections['id'], $this->errors ) ) {
					$err = $this->errors[ $sections['id'] ];
				}
			}

			return $err;
		}

		/**
		 * Add option page html output
		 *
		 * @return void
		 */
		public function add_options_html() {
			$has_nav       = ( count( $this->pre_tabs ) > 1 ) ? true : false;
			$show_all      = ( ! $has_nav ) ? ' kfw-show-all' : '';
			$ajax_class    = ( $this->args['ajax_save'] ) ? ' kfw-save-ajax' : '';
			$sticky_class  = ( $this->args['sticky_header'] ) ? ' kfw-sticky-header' : '';
			$wrapper_class = ( $this->args['framework_class'] ) ? ' ' . $this->args['framework_class'] : '';
			$theme         = ( $this->args['theme'] ) ? ' kfw-theme-' . $this->args['theme'] : '';
			$class         = ( $this->args['class'] ) ? ' ' . $this->args['class'] : '';

			echo wp_kses( '<div class="kfw kfw-options' . $theme . $class . $wrapper_class . '" data-slug="' . $this->args['menu_slug'] . '" data-unique="' . $this->unique . '">', kfw_allowed_html( array( 'div' ) ) );

			$notice_class = ( ! empty( $this->notice ) ) ? ' kfw-form-show' : '';
			$notice_text  = ( ! empty( $this->notice ) ) ? $this->notice : '';

			echo wp_kses( '<div class="kfw-form-result kfw-form-success' . $notice_class . '">' . $notice_text . '</div>', kfw_allowed_html( array( 'div' ) ) );

			$error_class = ( ! empty( $this->errors ) ) ? ' kfw-form-show' : '';

			echo wp_kses( '<div class="kfw-form-result kfw-form-error' . $error_class . '">', kfw_allowed_html( array( 'div' ) ) );
			if ( ! empty( $this->errors ) ) {
				foreach ( $this->errors as $error ) {
					echo wp_kses( '<i class="kfw-label-error">!</i> ' . $error . '<br />', kfw_allowed_html( array( 'i', 'br' ) ) );
				}
			}
			echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

			echo wp_kses( '<div class="kfw-container">', kfw_allowed_html( array( 'div' ) ) );

			echo wp_kses( '<form method="post" action="" enctype="multipart/form-data" id="kfw-form" autocomplete="off">', kfw_allowed_html( array( 'form' ) ) );

			echo wp_kses( '<input type="hidden" class="kfw-section-id" name="kfw_transient[section]" value="1">', kfw_allowed_html( array( 'input' ) ) );
			wp_nonce_field( 'kfw_options_nonce', 'kfw_options_nonce' . $this->unique );

			echo wp_kses( '<div class="kfw-header' . esc_attr( $sticky_class ) . '">', kfw_allowed_html( array( 'div' ) ) );
			echo wp_kses( '<div class="kfw-header-inner">', kfw_allowed_html( array( 'div' ) ) );

			echo wp_kses( '<div class="kfw-header-left">', kfw_allowed_html( array( 'div' ) ) );
			echo wp_kses( '<h1>' . $this->args['framework_title'] . '</h1>', kfw_allowed_html( array( 'h1' ) ) );
			echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

			echo wp_kses( '<div class="kfw-header-right">', kfw_allowed_html( array( 'div' ) ) );

			echo ( $has_nav && $this->args['show_all_options'] ) ? wp_kses( '<div class="kfw-expand-all" title="' . esc_html__( 'show all options', 'kfw' ) . '"><i class="fa fa-outdent"></i></div>', kfw_allowed_html( array( 'div', 'i' ) ) ) : '';

			echo ( $this->args['show_search'] ) ? wp_kses( '<div class="kfw-search"><input type="text" name="kfw-search" placeholder="' . esc_html__( 'Search option(s)', 'kfw' ) . '" autocomplete="off" /></div>', kfw_allowed_html( array( 'div', 'input' ) ) ) : '';

			echo wp_kses( '<div class="kfw-buttons">', kfw_allowed_html( array( 'div' ) ) );
			echo wp_kses( '<input type="submit" name="' . $this->unique . '[_nonce][save]" class="button button-primary kfw-save' . $ajax_class . '" value="' . esc_html__( 'Save', 'kfw' ) . '" data-save="' . esc_html__( 'Saving...', 'kfw' ) . '">', kfw_allowed_html( array( 'input' ) ) );
			echo ( $this->args['show_reset_section'] ) ? wp_kses( '<input type="submit" name="kfw_transient[reset_section]" class="button button-secondary kfw-reset-section kfw-confirm" value="' . esc_html__( 'Reset Section', 'kfw' ) . '" data-confirm="' . esc_html__( 'Are you sure to reset this section options?', 'kfw' ) . '">', kfw_allowed_html( array( 'input' ) ) ) : '';
			echo ( $this->args['show_reset_all'] ) ? wp_kses( '<input type="submit" name="kfw_transient[reset]" class="button button-secondary kfw-warning-primary kfw-reset-all kfw-confirm" value="' . esc_html__( 'Reset All', 'kfw' ) . '" data-confirm="' . esc_html__( 'Are you sure to reset all options?', 'kfw' ) . '">', kfw_allowed_html( array( 'input' ) ) ) : '';
			echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

			echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

			echo wp_kses( '<div class="clear"></div>', kfw_allowed_html( array( 'div' ) ) );
			echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );
			echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

			echo wp_kses( '<div class="kfw-wrapper' . $show_all . '">', kfw_allowed_html( array( 'div' ) ) );

			if ( $has_nav ) {
				echo wp_kses( '<div class="kfw-nav kfw-nav-options">', kfw_allowed_html( array( 'div' ) ) );

				echo wp_kses( '<ul>', kfw_allowed_html( array( 'ul' ) ) );

				$tab_key = 1;

				foreach ( $this->pre_tabs as $tab ) {
					$reset_options = ( isset( $tab['reset_options'] ) && false == $tab['reset_options'] ) ? 'false' : 'true';
					$tab_error     = $this->error_check( $tab );
					$tab_icon      = ( ! empty( $tab['icon'] ) ) ? '<i class="' . $tab['icon'] . '"></i>' : '';

					if ( ! empty( $tab['subs'] ) ) {
						echo wp_kses( '<li class="kfw-tab-depth-0">', kfw_allowed_html( array( 'li' ) ) );

						echo wp_kses( '<a href="#tab=' . $tab_key . '" class="kfw-arrow">' . $tab_icon . $tab['title'] . $tab_error . '</a>', kfw_allowed_html( array( 'a', 'i' ) ) );

						echo wp_kses( '<ul>', kfw_allowed_html( array( 'ul' ) ) );

						foreach ( $tab['subs'] as $sub ) {
							$sub_error = $this->error_check( $sub );
							$sub_icon  = ( ! empty( $sub['icon'] ) ) ? '<i class="' . $sub['icon'] . '"></i>' : '';

							echo wp_kses( '<li class="kfw-tab-depth-1"><a id="kfw-tab-link-' . $tab_key . '" href="#tab=' . $tab_key . '">' . $sub_icon . $sub['title'] . $sub_error . '</a></li>', kfw_allowed_html( array( 'li', 'a', 'i' ) ) );

							$tab_key++;
						}

						echo wp_kses( '</ul>', kfw_allowed_html( array( 'ul' ) ) );

						echo wp_kses( '</li>', kfw_allowed_html( array( 'li' ) ) );
					} else {
						echo wp_kses( '<li class="kfw-tab-depth-0" data-reset="' . $reset_options . '" ><a id="kfw-tab-link-' . $tab_key . '" href="#tab=' . $tab_key . '">' . $tab_icon . $tab['title'] . $tab_error . '</a></li>', kfw_allowed_html( array( 'li', 'a', 'i' ) ) );

						$tab_key++;
					}
				}

				echo wp_kses( '</ul>', kfw_allowed_html( array( 'ul' ) ) );

				echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );
			}

			echo wp_kses( '<div class="kfw-content">', kfw_allowed_html( array( 'div' ) ) );

			echo wp_kses( '<div class="kfw-sections">', kfw_allowed_html( array( 'div' ) ) );

			$section_key = 1;

			foreach ( $this->pre_sections as $section ) {
				$onload       = ( ! $has_nav ) ? ' kfw-onload' : '';
				$section_icon = ( ! empty( $section['icon'] ) ) ? '<i class="kfw-icon ' . $section['icon'] . '"></i>' : '';

				echo wp_kses( '<div id="kfw-section-' . $section_key . '" class="kfw-section' . $onload . '">', kfw_allowed_html( array( 'div' ) ) );
				echo ( $has_nav ) ? wp_kses( '<div class="kfw-section-title"><h3>' . $section_icon . $section['title'] . '</h3></div>', kfw_allowed_html( array( 'div', 'h3', 'i' ) ) ) : '';
				echo ( ! empty( $section['description'] ) ) ? wp_kses( '<div class="kfw-field kfw-section-description">' . $section['description'] . '</div>', kfw_allowed_html( array( 'div' ) ) ) : '';

				if ( ! empty( $section['fields'] ) ) {
					foreach ( $section['fields'] as $field ) {
						$is_field_error = $this->error_check( $field );

						if ( ! empty( $is_field_error ) ) {
							$field['_error'] = $is_field_error;
						}

						$value = ( ! empty( $field['id'] ) && isset( $this->options[ $field['id'] ] ) ) ? $this->options[ $field['id'] ] : '';

						KFW::field( $field, $value, $this->unique, 'options' );
					}
				} else {
					echo wp_kses( '<div class="kfw-no-option kfw-text-muted">' . esc_html__( 'No option provided by developer.', 'kfw' ) . '</div>', kfw_allowed_html( array( 'div' ) ) );
				}

				echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

				$section_key++;
			}

			echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

			echo wp_kses( '<div class="clear"></div>', kfw_allowed_html( array( 'div' ) ) );

			echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

			echo wp_kses( '<div class="kfw-nav-background"></div>', kfw_allowed_html( array( 'div' ) ) );

			echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

			if ( ! empty( $this->args['show_footer'] ) ) {
				echo wp_kses( '<div class="kfw-footer">', kfw_allowed_html( array( 'div' ) ) );

				echo wp_kses( '<div class="kfw-buttons">', kfw_allowed_html( array( 'div' ) ) );
				echo wp_kses( '<input type="submit" name="kfw_transient[save]" class="button button-primary kfw-save' . $ajax_class . '" value="' . esc_html__( 'Save', 'kfw' ) . '" data-save="' . esc_html__( 'Saving...', 'kfw' ) . '">', kfw_allowed_html( array( 'input' ) ) );
				echo ( $this->args['show_reset_section'] ) ? wp_kses( '<input type="submit" name="kfw_transient[reset_section]" class="button button-secondary kfw-reset-section kfw-confirm" value="' . esc_html__( 'Reset Section', 'kfw' ) . '" data-confirm="' . esc_html__( 'Are you sure to reset this section options?', 'kfw' ) . '">', kfw_allowed_html( array( 'input' ) ) ) : '';
				echo ( $this->args['show_reset_all'] ) ? wp_kses( '<input type="submit" name="kfw_transient[reset]" class="button button-secondary kfw-warning-primary kfw-reset-all kfw-confirm" value="' . esc_html__( 'Reset All', 'kfw' ) . '" data-confirm="' . esc_html__( 'Are you sure to reset all options?', 'kfw' ) . '">', kfw_allowed_html( array( 'input' ) ) ) : '';
				echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

				echo ( ! empty( $this->args['footer_text'] ) ) ? wp_kses( '<div class="kfw-copyright">' . $this->args['footer_text'] . '</div>', kfw_allowed_html( array( 'div', 'p', 'a' ) ) ) : '';

				echo wp_kses( '<div class="clear"></div>', kfw_allowed_html( array( 'div' ) ) );
				echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );
			}

			echo wp_kses( '</form>', kfw_allowed_html( array( 'form' ) ) );

			echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );

			echo wp_kses( '<div class="clear"></div>', kfw_allowed_html( array( 'form' ) ) );

			echo ( ! empty( $this->args['footer_after'] ) ) ? wp_kses( $this->args['footer_after'], kfw_allowed_html( 'all' ) ) : '';

			echo wp_kses( '</div>', kfw_allowed_html( array( 'div' ) ) );
		}
	}
}
