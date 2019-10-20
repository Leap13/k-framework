<?php
/**
 * Minify Loader Class
 *
 * @package     Kemet
 * @author      Brainstorm Force
 * @copyright   Copyright (c) 2019, Brainstorm Force
 * @link        https://www.brainstormforce.com
 * @since       Kemet 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Kemet_Minify' ) ) {

	/**
	 * Kemet_Minify
	 */
	class Kemet_Minify {

		/**
		 * WordPress Filesystem
		 *
		 * @since 1.0
		 * @access private
		 * @var bool $_in_customizer_preview
		 */
		static private $kemet_filesystem = null;

		/**
		 * Directory Info
		 *
		 * @since 1.0
		 * @access private
		 * @var bool $_dir_info
		 */
		static private $_dir_info = null;

		/**
		 * A flag for whether or not we're in a Customizer
		 * preview or not.
		 *
		 * @since 1.0
		 * @access private
		 * @var bool $_in_customizer_preview
		 */
		static private $_in_customizer_preview = false;

		/**
		 * The prefix for the option that is stored in the
		 * database for the cached CSS file key.
		 *
		 * @since 1.0
		 * @access private
		 * @var string $_css_key
		 */
		static private $_css_key = 'kemet_theme_css_key';

		/**
		 * Additional CSS to enqueue.
		 *
		 * @since 1.0
		 * @var array $css
		 */
		static private $css_files = array();



		/**
		 * Instance
		 *
		 * @since 1.6.0
		 *
		 * @access private
		 * @var object Class object.
		 */
		private static $instance;

		/**
		 * Initiator
		 *
		 * @since 1.6.0
		 *
		 * @return object initialized object of class.
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Construct
		 */
		function __construct() {

			// add_action( 'customize_preview_init', __CLASS__ . '::preview_init', 11 );
			// add_action( 'kemet_admin_settings_save', __CLASS__ . '::refresh_assets', 11 );
			// add_action( 'customize_save_after', __CLASS__ . '::refresh_assets', 11 );
			// add_action( 'kemet_addon_activated', __CLASS__ . '::refresh_assets', 11 );
            // add_action( 'kemet_addon_deactivated', __CLASS__ . '::refresh_assets', 11 );
            add_action( 'kfw_kmt_framework_saved', 'refresh_assets');	
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		/**
		 * Enqueue Scripts
		 */
		function enqueue_scripts() {

			/**
			 * Filters to disable all the styles and scripts added from addon.
			 *
			 * @since 1.5.0
			 *
			 * @param bool true | false enable/disable all styels,scripts of kemet addon.
			 */
			//if ( apply_filters( 'kemet_addon_enqueue_assets', true ) ) {

				$css_url = self::get_css_url();

				if ( false != $css_url ) {
					wp_enqueue_style( 'kemet-addon-css', $css_url, array(), KEMET_ADDONS_VERSION, 'all' );
				}


				// if ( ! function_exists( 'kemet_filesystem' ) ) {
				// 	wp_add_inline_style( 'kemet-addon-css', apply_filters( 'kemet_dynamic_css', '' ) );
				// }

			//}
		}

		/**
		 * Load WordPress filesystem
		 *
		 * @since 1.0
		 * @return void
		 */
		public static function load_filesystem() {

			if ( null === self::$kemet_filesystem ) {

				global $wp_filesystem;
				if ( empty( $wp_filesystem ) ) {
					require_once ABSPATH . '/wp-admin/includes/file.php';
					WP_Filesystem();
				}

				self::$kemet_filesystem = $wp_filesystem;
			}
		}

		/**
		 * Used to add enqueue frontend styles.
		 *
		 * @since 1.0
		 * @param string  $src    Source URL.
		 * @param boolean $handle Script handle.
		 * @return void
		 */
		public static function add_css( $src = null, $handle = false ) {
			if ( false != $handle ) {
				self::$css_files[ $handle ] = $src;
			} else {
				self::$css_files[] = $src;
			}
		}


		/**
		 * Get css files to HTTP/2.
		 *
		 * @since 1.0
		 * @return array()
		 */
		public static function get_http2_css_files() {

			// Get the css key.
			$css_slug  = self::_asset_slug();
			$css_files = get_option( self::$_css_key . '-files-' . $css_slug, array() );

			// No css files, recompile the files.
			if ( ! $css_files ) {
				self::render_http2_css();
				return self::get_http2_css_files();
			}

			// Return the url.
			return $css_files;
		}

		/**
		 * Get css files to generate.
		 *
		 * @since 1.0
		 * @return array()
		 */
		public static function get_css_files() {

			if ( 1 > count( self::$css_files ) ) {
				do_action( 'kemet_get_css_files' );
			}

			return apply_filters( 'kemet_add_css_file', self::$css_files );
		}


		/**
		 * Checks to see if the current site is being accessed over SSL.
		 *
		 * @since 1.0
		 * @return bool
		 */
		public static function kemet_is_ssl() {
			if ( is_ssl() ) {

				return true;
			} elseif ( 0 === stripos( get_option( 'siteurl' ), 'https://' ) ) {

				return true;
			} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' == $_SERVER['HTTP_X_FORWARDED_PROTO'] ) {

				return true;
			}

			return false;
		}

		/**
		 * Returns an array with the path and URL for the cache directory.
		 *
		 * @since 1.0
		 * @return array
		 */
		public static function get_cache_dir() {

			if ( null != self::$_dir_info ) {
				return self::$_dir_info;
			}

			$dir_name = 'kemet-addon-lastt';
			$wp_info  = wp_upload_dir();

			// SSL workaround.
			if ( self::kemet_is_ssl() ) {
				$wp_info['baseurl'] = str_ireplace( 'http://', 'https://', $wp_info['baseurl'] );
			}

			// Build the paths.
			$dir_info = array(
				'path' => $wp_info['basedir'] . '/' . $dir_name . '/',
				'url'  => $wp_info['baseurl'] . '/' . $dir_name . '/',
			);

			// Create the cache dir if it doesn't exist.
			if ( ! file_exists( $dir_info['path'] ) ) {
				wp_mkdir_p( $dir_info['path'] );
			}

			self::$_dir_info = $dir_info;

			return self::$_dir_info;
		}

		/**
		 * Checks to see if this is a Customizer preview or not.
		 *
		 * @since 1.0
		 * @return bool
		 */
		public static function is_customizer_preview() {
			return self::$_in_customizer_preview;
		}

		/**
		 * Returns the prefix slug for the CSS cache file.
		 *
		 * @since 1.0
		 * @access private
		 * @return string
		 */
		private static function _asset_slug() {
			if ( self::is_customizer_preview() ) {
				$slug = 'kmt-customizer';
			} else {
				$slug = 'kemet-addon';
			}

			return $slug;
		}

		/**
		 * Clears and rebuilds the cached CSS file.
		 *
		 * @since 1.0
		 * @return void
		 */
		public static function refresh_assets() {
			//self::clear_assets_cache();
			self::render_assets();
			do_action( 'kemet_addon_assets_refreshed' );
		}

		/**
		 * Deletes cached CSS files based on the current
		 * context (live, preview or customizer) or all if
		 * $all is set to true.
		 *
		 * @since 1.0
		 * @return boolean Returns True if files were successfull deleted,  False If files could not be deleted.
		 */
		public static function clear_assets_cache() {

			// Make sure the filesystem is loaded.
			self::load_filesystem();

			$dir_name   = 'kemet-addon-lastt';
			$cache_dir  = self::get_cache_dir();
			$asset_slug = self::_asset_slug();

			/* Delete CSS Keys */
			delete_option( self::$_css_key . '-' . $asset_slug );
			delete_option( self::$_css_key . '-files-' . $asset_slug );

			if ( ! empty( $cache_dir['path'] ) && stristr( $cache_dir['path'], $dir_name ) ) {
				$directory     = trailingslashit( $cache_dir['path'] );
				$filelist      = (array) self::$kemet_filesystem->dirlist( $directory, true );
				$delete_status = true;

				foreach ( $filelist as $file ) {

					// don't delete dynamic css files.
					// @TODO: use Kemet_Cache to generate and manage CSS files.
					if ( false !== strpos( $file['name'], 'dynamic-css' ) ) {
						continue;
					}

					$file = $directory . $file['name'];

					if ( is_file( $file ) && file_exists( $file ) ) {
						$delete_status = self::$kemet_filesystem->delete( $file );
					}
				}

				// If the file was not correctly deleted.
				if ( false == $delete_status ) {
					// Set status CSS status True. This will load the CSS as inline.
					update_option( 'kmt-theme-css-status', true );

					return false;
				}
			}

			return true;
		}

		/**
		 * Renders the CSS and JS assets.
		 *
		 * @since 1.0
		 * @return void
		 */
		public static function render_assets() {
			if ( defined( 'KEMET_THEME_HTTP2' ) && KEMET_THEME_HTTP2 ) {
				self::render_http2_css();
			} else {
				self::render_css();
			}
		}

		/**
		 * Returns a URL for the cached CSS file.
		 *
		 * @since 1.0
		 * @return string
		 */
		public static function get_css_url() {

			if ( defined( 'KEMET_THEME_HTTP2' ) && KEMET_THEME_HTTP2 ) {

				self::enqueue_http2_css();
				return false;
			} elseif ( ! get_option( 'kmt-theme-css-status' ) ) {

				// Get the cache dir and css key.
				$cache_dir = self::get_cache_dir();
				$css_slug  = self::_asset_slug();
				$css_key   = get_option( self::$_css_key . '-' . $css_slug );
				$css_path  = $cache_dir['path'] . $css_slug . '-' . $css_key . '.css';
				$css_url   = $cache_dir['url'] . $css_slug . '-' . $css_key . '.css';

				if ( ! $css_key ) {
					self::render_css();
					return self::get_css_url();
				}

				// Check to see if the file exists.
				if ( ! file_exists( $css_path ) ) {
					self::render_fallback_css();
					return false;
				}

				// Return the url.
				return $css_url;
			} else {

				self::render_fallback_css();
				return false;
			}
		}

		/**
		 * Returns a HTTP/2 Dynamic CSS data.
		 *
		 * @since 1.0
		 * @return string
		 */
		public static function get_http2_dynamic_css() {

			// Get the css key.
			$css_slug = self::_asset_slug();

			// No css data, recompile the css.
			if ( ! $css_data ) {
				self::render_http2_css();
				return self::get_http2_dynamic_css();
			}

			// Return the url.
			return $css_data;
		}

		/**
		 * Returns a Dynamic CSS data.
		 *
		 * @since 1.0
		 * @return string
		 */
		public static function get_dynamic_css() {

			// Get the cache dir and css key.
			$cache_dir = self::get_cache_dir();
			$css_slug  = self::_asset_slug();

			// No css data, recompile the css.
			if ( ! $css_data ) {
				self::render_css();
				return self::get_dynamic_css();
			}

			// Return the url.
			return $css_data;
		}


		/**
		 * Compiles the cached CSS file.
		 *
		 * @since 1.0
		 * @access private
		 * @return void
		 */
		private static function render_http2_css() {

			$css_slug  = self::_asset_slug();
			$css_files = self::get_css_files();

			/* Update Dynamic css in DB */
			update_option( self::$_css_key . '-files-' . $css_slug, $css_files );
		}

		/**
		 * Compiles the cached CSS file.
		 *
		 * @since 1.0
		 * @access private
		 * @return void|false Checks early if cache directory was emptied before generating the new files
		 */
		private static function render_css() {

			self::load_filesystem();

			if ( ! defined( 'FS_CHMOD_FILE' ) ) {
				define( 'FS_CHMOD_FILE', ( fileperms( ABSPATH . 'index.php' ) & 0777 | 0644 ) );
			}

			if ( get_option( 'kmt-theme-css-status' ) ) {
				$assets_status = self::clear_assets_cache();

				if ( false == $assets_status ) {
					return false;
				}
			}

			$cache_dir   = self::get_cache_dir();
			$new_css_key = str_replace( '.', '-', uniqid( '', true ) );
			$css_slug    = self::_asset_slug();
			$css_files   = self::get_css_files();
			$css         = '';
			$css_min     = '';
			$filepath    = $cache_dir['path'] . $css_slug . '-' . $new_css_key . '.css';

			if ( count( $css_files ) > 0 ) {

				foreach ( $css_files as $k => $file ) {

					if ( ! empty( $file ) && file_exists( $file ) ) {
						$css .= self::$kemet_filesystem->get_contents(
							$file,
							FS_CHMOD_FILE
						);
					}
				}
			}

			$css = apply_filters( 'kemet_render_css', $css );

			$status = self::$kemet_filesystem->put_contents(
				$filepath,
				$css,
				FS_CHMOD_FILE
			);

			$status = ! $status;

			// Save the new css key.
			update_option( 'kmt-theme-css-status', $status );
			update_option( self::$_css_key . '-' . $css_slug, $new_css_key );
		}

		/**
		 * Render HTTP/2 CSS : enqueue individual CSS file.
		 *
		 * @since 1.0
		 * @access private
		 * @return void
		 */
		private static function enqueue_http2_css() {

			$css_files   = self::get_http2_css_files();
			$files_count = count( $css_files );

			if ( $files_count > 0 ) {

				foreach ( $css_files as $k => $file ) {

					if ( $files_count == $k + 1 ) {
						$handle = 'kemet-addon-css';
					} else {
						$handle = 'kemet-addon-css-' . $k;
					}

					wp_enqueue_style(
						$handle,
						$file,
						array(),
						KEMET_ADDONS_VERSION,
						'all'
					);
				}
			}
		}

		/**
		 * Fallback to enqueue individual CSS file.
		 *
		 * @since 1.0
		 * @access private
		 * @return void
		 */
		private static function render_fallback_css() {

			$css_files   = self::get_css_files();
			$files_count = count( $css_files );

			if ( $files_count > 0 ) {

				foreach ( $css_files as $index => $file_path ) {

					if ( ! file_exists( $file_path ) ) {
						continue;
					}

					$new_file = plugins_url( str_replace( plugin_dir_path( KEMET_ADDONS_FILE ), '', $file_path ), KEMET_ADDONS_FILE );

					if ( $files_count == $index + 1 ) {

						$handle = 'kemet-addon-css';
					} else {
						$handle = 'kemet-addon-css-' . $index;
					}

					wp_enqueue_style(
						$handle,
						$new_file,
						array(),
						KEMET_ADDONS_VERSION,
						'all'
					);

				}
			}
		}

		/**
		 * Called by the customize_preview_init action to initialize
		 * a Customizer preview.
		 *
		 * @since 1.0
		 * @return void
		 */
		public static function preview_init() {
			self::$_in_customizer_preview = true;

			self::refresh_assets();
		}

		/**
		 * Trim CSS
		 *
		 * @since 1.0
		 * @param string $css CSS content to trim.
		 * @return string
		 */
		public static function trim_css( $css = '' ) {

			// Trim white space for fkmter page loading.
			if ( ! empty( $css ) ) {
				$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
				$css = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    ' ), '', $css );
				$css = str_replace( ', ', ',', $css );
			}

			return $css;
		}
	}

	Kemet_Minify::get_instance();
}