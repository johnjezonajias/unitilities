<?php

namespace Unitilities\Features;

/**
 * Class WpMetaTag
 *
 * Provides utilities for managing WordPress meta tags in the site header.
 * Specifically, this class allows the removal of the `wp_generator` meta tag
 * that exposes the WordPress version in the site's source code.
 */
class WpMetaTag {
    /**
     * Constructor.
     *
     * Hooks into WordPress actions to register settings and manage the removal
     * of the `wp_generator` meta tag based on user preferences.
     */
    public function __construct() {
        // Hook to check and conditionally remove the wp_generator tag.
        add_action( 'init', [ $this, 'remove_wp_generator' ] );

        // Hook to register admin settings for this feature.
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }

    /**
     * Remove wp_generator meta tag.
     *
     * This method checks the user's settings to determine if the `wp_generator`
     * tag, which displays the WordPress version in the source code, should be
     * removed. The setting is stored as an option in the database.
     */
    public function remove_wp_generator() {
        // Check if the option to remove the generator meta tag is enabled.
        if ( '1' === get_option( 'unitilities_remove_wp_generator', '0' ) ) {
            // Remove the WordPress version meta tag from the head.
            remove_action( 'wp_head', 'wp_generator' );
        }
    }

    /**
     * Register the setting for managing wp_generator.
     *
     * This method adds a new setting to the WordPress admin under the
     * Unitilities settings group. It also defines the section and field for
     * controlling whether the wp_generator tag is removed.
     */
    public function register_settings() {
        // Register the option to store the wp_generator removal setting.
        register_setting( 'unitilities_utilities_settings', 'unitilities_remove_wp_generator' );

        // Add a new section in the settings page.
        add_settings_section(
            'unitilities_utilities_section',
            __( 'Common utility settings', 'unitilities' ),
            null, // No callback for section description.
            'unitilities_utilities'
        );

        // Add a checkbox field to control wp_generator removal.
        add_settings_field(
            'unitilities_remove_wp_generator',
            __( 'Remove WordPress Version', 'unitilities' ),
            [ $this, 'render_wp_generator_field' ],
            'unitilities_utilities',
            'unitilities_utilities_section'
        );
    }
    
    /**
     * Render the checkbox for wp_generator removal.
     *
     * This method outputs the HTML for a checkbox that allows users to enable
     * or disable the removal of the `wp_generator` meta tag from the site's
     * source code.
     */
    public function render_wp_generator_field() {
        // Get the current value of the setting.
        $value = get_option( 'unitilities_remove_wp_generator', '0' );

        // Render the checkbox with the current value checked if enabled.
        ?>
        <input type="checkbox" name="unitilities_remove_wp_generator" value="1" <?php checked( '1', $value ); ?> />
        <?php esc_html_e( 'Disable WordPress version meta tag in the head/source code?', 'unitilities' ); ?>
        <?php
    }
}
