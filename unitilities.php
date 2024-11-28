<?php
/**
 * Plugin Name: Unitilities
 * Plugin URI: https://webdevjohnajias.one/unitilities
 * Description: My common WordPress utility settings.
 * Version: 1.0.0
 * Author: John Jezon Ajias
 * Author URI: https://webdevjohnajias.one/
 * License: GPL-2.0+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: unitilities
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Set default options when the plugin is activated.
function unitilities_activate_plugin() {
    if ( false === get_option( 'unitilities_remove_wp_generator' ) ) {
        // Set default to true (1).
        update_option( 'unitilities_remove_wp_generator', '1' );
    }
}
register_activation_hook( __FILE__, 'unitilities_activate_plugin' );

// Register plugin settings.
function unitilities_register_settings() {
    register_setting( 'unitilities_settings', 'unitilities_remove_wp_generator' );
}
add_action( 'admin_init', 'unitilities_register_settings' );

// Add an options page for the plugin.
function unitilities_add_options_page() {
    add_options_page(
        __( 'Unitilities Settings', 'unitilities' ),
        __( 'Unitilities', 'unitilities' ),
        'manage_options',
        'unitilities-settings',
        'unitilities_render_settings_page'
    );
}
add_action( 'admin_menu', 'unitilities_add_options_page' );

// Render the plugin's settings page.
function unitilities_render_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Unitilities Settings', 'unitilities' ); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields( 'unitilities_settings' );
            do_settings_sections( 'unitilities_settings' );
            $remove_wp_generator = get_option( 'unitilities_remove_wp_generator', '0' );
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php esc_html_e( 'WordPress Meta-tag', 'unitilities' ); ?></th>
                    <td>
                        <input type="checkbox" name="unitilities_remove_wp_generator" value="1" <?php checked( '1', $remove_wp_generator ); ?> />
                        <label for="unitilities_remove_wp_generator"><?php esc_html_e( 'Disable WordPress version meta tag in the head?', 'unitilities' ); ?></label>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Hook into `wp_head` conditionally based on the setting.
function unitilities_toggle_wp_generator() {
    $remove_wp_generator = get_option( 'unitilities_remove_wp_generator', '0' );

    if ( '1' === $remove_wp_generator ) {
        remove_action( 'wp_head', 'wp_generator' );
    }
}
add_action( 'init', 'unitilities_toggle_wp_generator' );
