<?php if (! defined('ABSPATH')) {
    die;
} // Cannot access directly.
/**
 *
 * Get icons from admin ajax
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if (! function_exists('kfw_get_icons')) {
    function kfw_get_icons()
    {
        if (! empty($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'kfw_icon_nonce')) {
            ob_start();

            kfw::include_plugin_file('fields/icon/default-icons.php');

            $icon_lists = apply_filters('kfw_field_icon_add_icons', kfw_get_default_icons());

            if (! empty($icon_lists)) {
                foreach ($icon_lists as $list) {
                    echo (count($icon_lists) >= 2) ? '<div class="kfw-icon-title">'. $list['title'] .'</div>' : '';

                    foreach ($list['icons'] as $icon) {
                        echo '<a class="kfw-icon-tooltip" data-kfw-icon="'. $icon .'" title="'. $icon .'"><span class="kfw-icon kfw-selector dashicons '. $icon .'"></span></a>';
                    }
                }
            } else {
                echo '<div class="kfw-text-error">'. esc_html__('No data provided by developer', 'kfw') .'</div>';
            }

            wp_send_json_success(array( 'content' => ob_get_clean() ));
        } else {
            wp_send_json_error(array( 'error' => esc_html__('Error: Nonce verification has failed. Please try again.', 'kfw') ));
        }
    }
    add_action('wp_ajax_kfw-get-icons', 'kfw_get_icons');
}

/**
 *
 * Export
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if (! function_exists('kfw_export')) {
    function kfw_export()
    {
        if (! empty($_GET['export']) && ! empty($_GET['nonce']) && wp_verify_nonce($_GET['nonce'], 'kfw_backup_nonce')) {
            header('Content-Type: application/json');
            header('Content-disposition: attachment; filename=backup-'. gmdate('d-m-Y') .'.json');
            header('Content-Transfer-Encoding: binary');
            header('Pragma: no-cache');
            header('Expires: 0');

            echo json_encode(get_option(wp_unslash($_GET['export'])));
        }

        die();
    }
    add_action('wp_ajax_kfw-export', 'kfw_export');
}

/**
 *
 * Import Ajax
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if (! function_exists('kfw_import_ajax')) {
    function kfw_import_ajax()
    {
        if (! empty($_POST['import_data']) && ! empty($_POST['unique']) && ! empty($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'kfw_backup_nonce')) {
            $import_data = json_decode(wp_unslash(trim($_POST['import_data'])), true);

            if (is_array($import_data)) {
                update_option(wp_unslash($_POST['unique']), wp_unslash($import_data));
                wp_send_json_success();
            }
        }

        wp_send_json_error(array( 'error' => esc_html__('Error: Nonce verification has failed. Please try again.', 'kfw') ));
    }
    add_action('wp_ajax_kfw-import', 'kfw_import_ajax');
}

/**
 *
 * Reset Ajax
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if (! function_exists('kfw_reset_ajax')) {
    function kfw_reset_ajax()
    {
        if (! empty($_POST['unique']) && ! empty($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'kfw_backup_nonce')) {
            delete_option(wp_unslash($_POST['unique']));
            wp_send_json_success();
        }

        wp_send_json_error(array( 'error' => esc_html__('Error: Nonce verification has failed. Please try again.', 'kfw') ));
    }
    add_action('wp_ajax_kfw-reset', 'kfw_reset_ajax');
}

/**
 *
 * Chosen Ajax
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if (! function_exists('kfw_chosen_ajax')) {
    function kfw_chosen_ajax()
    {
        if (! empty($_POST['term']) && ! empty($_POST['type']) && ! empty($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'kfw_chosen_ajax_nonce')) {
            $capability = apply_filters('kfw_chosen_ajax_capability', 'manage_options');

            if (current_user_can($capability)) {
                $type       = $_POST['type'];
                $term       = $_POST['term'];
                $query_args = (! empty($_POST['query_args'])) ? $_POST['query_args'] : array();
                $options    = kfw_Fields::field_data($type, $term, $query_args);

                wp_send_json_success($options);
            } else {
                wp_send_json_error(array( 'error' => esc_html__('You do not have required permissions to access.', 'kfw') ));
            }
        } else {
            wp_send_json_error(array( 'error' => esc_html__('Error: Nonce verification has failed. Please try again.', 'kfw') ));
        }
    }
    add_action('wp_ajax_kfw-chosen', 'kfw_chosen_ajax');
}

/**
 *
 * Set icons for wp dialog
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if (! function_exists('kfw_set_icons')) {
    function kfw_set_icons()
    {
        ?>
    <div id="kfw-modal-icon" class="kfw-modal kfw-modal-icon">
      <div class="kfw-modal-table">
        <div class="kfw-modal-table-cell">
          <div class="kfw-modal-overlay"></div>
          <div class="kfw-modal-inner">
            <div class="kfw-modal-title">
              <?php esc_html_e('Add Icon', 'kfw'); ?>
              <div class="kfw-modal-close kfw-icon-close"></div>
            </div>
            <div class="kfw-modal-header kfw-text-center">
              <input type="text" placeholder="<?php esc_html_e('Search a Icon...', 'kfw'); ?>" class="kfw-icon-search" />
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
    add_action('admin_footer', 'kfw_set_icons');
    add_action('customize_controls_print_footer_scripts', 'kfw_set_icons');
}
