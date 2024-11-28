<?php

namespace Unitilities\Features;

class WpMetaTag {
    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'init', [ $this, 'remove_wp_generator' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
        add_action( 'admin_menu', [ $this, 'add_settings_page' ] );
    }

    /**
     * Remove wp_generator based on option.
     */
    public function remove_wp_generator() {
        if ( '1' === get_option( 'unitilities_remove_wp_generator', '0' ) ) {
            remove_action( 'wp_head', 'wp_generator' );
        }
    }

    /**
     * Register plugin settings.
     */
    public function register_settings() {
        register_setting( 'unitilities_settings', 'unitilities_remove_wp_generator' );
    }

    /**
     * Add settings page to admin menu.
     */
    public function add_settings_page() {
        add_options_page(
            __( 'Unitilities Settings', 'unitilities' ),
            __( 'Unitilities', 'unitilities' ),
            'manage_options',
            'unitilities-settings',
            [ $this, 'render_settings_page' ]
        );
    }

    /**
     * Render the settings page.
     */
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Unitilities Settings', 'unitilities' ); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'unitilities_settings' );
                $remove_wp_generator = get_option( 'unitilities_remove_wp_generator', '0' );
                ?>
                <table class="form-table">
                    <tr>
                        <th><?php esc_html_e( 'Remove wp_generator', 'unitilities' ); ?></th>
                        <td>
                            <input type="checkbox" name="unitilities_remove_wp_generator" value="1" <?php checked( '1', $remove_wp_generator ); ?> />
                            <?php esc_html_e( 'Disable WordPress version meta tag in the head.', 'unitilities' ); ?>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
