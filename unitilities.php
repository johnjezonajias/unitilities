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

        // Register main settings page.
        add_action( 'admin_menu', [ $this, 'register_settings_page' ] );
    }

    /**
     * Set default settings on activation.
    */
    public function on_activation() {
        if ( false === get_option( 'unitilities_remove_wp_generator' ) ) {
            update_option( 'unitilities_remove_wp_generator', '1' );
        }
        if ( false === get_option( 'unitilities_censored_words' ) ) {
            update_option( 'unitilities_censored_words', [] );
        }
    }

    /**
     * Load plugin features.
    */
    private function load_features() {
        new Features\WpMetaTag();
        new Features\CommentFilter();
    }

    /**
     * Register unified settings page.
     */
    public function register_settings_page() {
        add_options_page(
            __( 'Unitilities Settings', 'unitilities' ),
            __( 'Unitilities', 'unitilities' ),
            'manage_options',
            'unitilities-settings',
            [ $this, 'render_settings_page' ]
        );
    }

    /**
     * Render the unified settings page with tabs.
     */
    public function render_settings_page() {
        $active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'utilities';
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Unitilities Settings', 'unitilities' ); ?></h1>
            <h2 class="nav-tab-wrapper">
                <a href="?page=unitilities-settings&tab=utilities" class="nav-tab <?php echo $active_tab === 'utilities' ? 'nav-tab-active' : ''; ?>">
                    <?php esc_html_e( 'Basic Settings', 'unitilities' ); ?>
                </a>
                <a href="?page=unitilities-settings&tab=comment_filter" class="nav-tab <?php echo $active_tab === 'comment_filter' ? 'nav-tab-active' : ''; ?>">
                    <?php esc_html_e( 'Comment Filter', 'unitilities' ); ?>
                </a>
                <a href="?page=unitilities-settings&tab=future_features" class="nav-tab <?php echo $active_tab === 'future_features' ? 'nav-tab-active' : ''; ?>">
                    <?php esc_html_e( 'Future Features', 'unitilities' ); ?>
                </a>
            </h2>
            <form method="post" action="options.php">
                <?php
                    if ( $active_tab === 'utilities' ) {
                        do_settings_sections( 'unitilities_utilities' );
                        settings_fields( 'unitilities_utilities_settings' );
                    } elseif ( $active_tab === 'comment_filter' ) {
                        do_settings_sections( 'unitilities_comment_filter' );
                        settings_fields( 'unitilities_comment_filter_settings' );
                    } else {
                        echo '<p>' . esc_html__( 'Future features will be added here.', 'unitilities' ) . '</p>';
                    }
                    submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}

// Initialize the plugin.
new Plugin();
