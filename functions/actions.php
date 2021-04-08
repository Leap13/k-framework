<?php
/**
 * Actions
 *
 * @package Kemet Framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

if ( ! function_exists( 'kfw_get_icons' ) ) {
	/**
	 *
	 * Get icons from admin ajax
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	function kfw_get_icons() {
		if ( isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'kfw_icon_nonce' ) ) {
			ob_start();

			kfw::include_plugin_file( 'fields/icon/default-icons.php' );

			$icon_lists = apply_filters( 'kfw_field_icon_add_icons', kfw_get_default_icons() );

			if ( ! empty( $icon_lists ) ) {
				foreach ( $icon_lists as $list ) {
					echo ( count( $icon_lists ) >= 2 ) ? wp_kses( '<div class="kfw-icon-title">' . $list['title'] . '</div>', kfw_allowed_html( array( 'div' ) ) ) : '';

					foreach ( $list['icons'] as $icon ) {
						echo wp_kses( '<a class="kfw-icon-tooltip" data-kfw-icon="' . $icon . '" title="' . $icon . '"><span class="kfw-icon kfw-selector dashicons ' . $icon . '"></span></a>', kfw_allowed_html( array( 'a', 'span' ) ) );
					}
				}
			} else {
				echo wp_kses( '<div class="kfw-text-error">' . esc_html__( 'No data provided by developer', 'kfw' ) . '</div>', kfw_allowed_html( array( 'a', 'div' ) ) );
			}

			wp_send_json_success( array( 'content' => ob_get_clean() ) );
		} else {
			wp_send_json_error( array( 'error' => esc_html__( 'Error: Nonce verification has failed. Please try again.', 'kfw' ) ) );
		}
	}
	add_action( 'wp_ajax_kfw-get-icons', 'kfw_get_icons' );
}

if ( ! function_exists( 'kfw_set_icons' ) ) {
	/**
	 *
	 * Set icons for wp dialog
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 */
	function kfw_set_icons() {
		?>
	<div id="kfw-modal-icon" class="kfw-modal kfw-modal-icon">
		<div class="kfw-modal-table">
			<div class="kfw-modal-table-cell">
				<div class="kfw-modal-overlay"></div>
					<div class="kfw-modal-inner">
						<div class="kfw-modal-title">
							<?php esc_html_e( 'Add Icon', 'kfw' ); ?>
							<div class="kfw-modal-close kfw-icon-close"></div>
						</div>
						<div class="kfw-modal-header kfw-text-center">
							<input type="text" placeholder="<?php esc_html_e( 'Search a Icon...', 'kfw' ); ?>" class="kfw-icon-search" />
						</div>
						<div class="kfw-modal-content">
						<div class="kfw-modal-loading"><div class="kfw-loading"></div></div>
						<div class="kfw-modal-load"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
		<?php
	}
	add_action( 'admin_footer', 'kfw_set_icons' );
	add_action( 'customize_controls_print_footer_scripts', 'kfw_set_icons' );
}
