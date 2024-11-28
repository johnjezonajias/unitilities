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

namespace Unitilities;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Autoloader for plugin classes.
spl_autoload_register( function ( $class ) {
    $namespace = __NAMESPACE__ . '\\';
    if ( strpos( $class, $namespace ) === 0 ) {
        $relative_class = str_replace( $namespace, '', $class );
        $file = plugin_dir_path( __FILE__ ) . 'includes/' . str_replace( '\\', '/', $relative_class ) . '.php';

        if ( file_exists( $file ) ) {
            require $file;
        }
    }
} );

// Main Plugin Class.
class Plugin {
    /**
     * Initialize the plugin.
    */
    public function __construct() {
        // Activation hook.
        register_activation_hook( __FILE__, [ $this, 'on_activation' ] );

        // Load features.
        $this->load_features();
    }

    /**
     * Set default settings on activation.
    */
    public function on_activation() {
        if ( false === get_option( 'unitilities_remove_wp_generator' ) ) {
            update_option( 'unitilities_remove_wp_generator', '1' );
        }
    }

    /**
     * Load plugin features.
    */
    private function load_features() {
        new Features\WpMetaTag();
    }
}

// Initialize the plugin.
new Plugin();
