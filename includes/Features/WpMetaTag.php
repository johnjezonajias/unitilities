<?php

namespace Unitilities\Features;

class WpMetaTag {
    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'init', [ $this, 'remove_wp_generator' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
        //add_action( 'admin_menu', [ $this, 'add_settings_page' ] );
    }

    /**
     * Remove wp_generator based on option.
     */
    public function remove_wp_generator() {
        if ( '1' === get_option( 'unitilities_remove_wp_generator', '0' ) ) {
            remove_action( 'wp_head', 'wp_generator' );
        }
    }

    public function register_settings() {
        register_setting( 'unitilities_utilities_settings', 'unitilities_remove_wp_generator' );
        add_settings_section(
            'unitilities_utilities_section',
            __( 'Common utility settings', 'unitilities' ),
            null,
            'unitilities_utilities'
        );
        add_settings_field(
            'unitilities_remove_wp_generator',
            __( 'Remove WordPress Version', 'unitilities' ),
            [ $this, 'render_wp_generator_field' ],
            'unitilities_utilities',
            'unitilities_utilities_section'
        );
    }
    
    public function render_wp_generator_field() {
        $value = get_option( 'unitilities_remove_wp_generator', '0' );
        ?>
        <input type="checkbox" name="unitilities_remove_wp_generator" value="1" <?php checked( '1', $value ); ?> />
        <?php esc_html_e( 'Disable WordPress version meta tag in the head/source code?', 'unitilities' ); ?>
        <?php
    }
}
