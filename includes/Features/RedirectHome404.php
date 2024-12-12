<?php

namespace Unitilities\Features;

/**
 * Class RedirectHome404
 *
 * Redirects 404 errors to the homepage, with an admin option to enable or disable the feature.
 */
class RedirectHome404 {
    /**
     * Constructor.
     *
     * Hooks into WordPress to redirect 404 errors and register admin settings.
     */
    public function __construct() {
        add_action( 'template_redirect', [ $this, 'redirect_on_404' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }

    /**
     * Redirects 404 errors to the homepage if the setting is enabled.
     */
    public function redirect_on_404() {
        $redirect_enabled = get_option( 'unitilities_redirect_404_to_home', false );

        if ( $redirect_enabled && is_404() ) {
            wp_redirect( home_url(), 301 ); // Redirect to homepage with a 301 (permanent) status code.
            exit;
        }
    }

    /**
     * Register the settings for enabling/disabling 404 redirection.
     */
    public function register_settings() {
        // Add the setting to the utilities tab.
        register_setting(
            'unitilities_utilities_settings',
            'unitilities_redirect_404_to_home',
            [
                'type'              => 'boolean',
                'sanitize_callback' => 'rest_sanitize_boolean',
                'default'           => false,
            ]
        );

        // Add a field for the setting.
        add_settings_field(
            'unitilities_redirect_404_to_home',
            __( 'Redirect 404 to Homepage', 'unitilities' ),
            [ $this, 'render_404_redirect_field' ],
            'unitilities_utilities',
            'unitilities_utilities_section'
        );
    }

    /**
     * Renders the checkbox for the 404 redirection setting.
     */
    public function render_404_redirect_field() {
        $redirect_enabled = get_option( 'unitilities_redirect_404_to_home', false );
        ?>
        <input type="checkbox" name="unitilities_redirect_404_to_home" value="1" <?php checked( $redirect_enabled, true ); ?>>
        <label for="unitilities_redirect_404_to_home">
            <?php esc_html_e( 'Enable redirection of 404 errors to the homepage.', 'unitilities' ); ?>
        </label>
        <?php
    }
}
