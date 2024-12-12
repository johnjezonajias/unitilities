<?php
/**
 * Plugin Name: Unitilities
 * Plugin URI: https://webdevjohnajias.one/unitilities
 * Description: A collection of common WordPress utility settings and features.
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
     * Constructor to initialize the plugin, register hooks, and load features.
     */
    public function __construct() {
        // Activation hook to set default settings.
        register_activation_hook( __FILE__, [ $this, 'on_activation' ] );

        // Load additional plugin features.
        $this->load_features();

        // Register the settings page for the plugin in the admin menu.
        add_action( 'admin_menu', [ $this, 'register_settings_page' ] );
    }

    /**
     * Set default settings on plugin activation.
     *
     * This function ensures that the necessary options are set when the plugin is activated.
     * It checks for the existence of settings and initializes them if they are missing.
     */
    public function on_activation() {
        if ( false === get_option( 'unitilities_remove_wp_generator' ) ) {
            update_option( 'unitilities_remove_wp_generator', '1' );
        }
        if ( false === get_option( 'unitilities_censored_words' ) ) {
            update_option( 'unitilities_censored_words', ['puha', 'kill', 'dumb', 'stupid', 'ass'] );
        }
    }

    /**
     * Load the features for the plugin.
     *
     * This function initializes the different features of the plugin such as WpMetaTag and CommentFilter.
     */
    private function load_features() {
        new Features\RedirectHome404();
        new Features\WpMetaTag();
        new Features\ForcePermalinkStructure();
        new Features\ForceCommentApproval();
        new Features\ForceThreadCommentsDepth();
        new Features\CommentFilter();
    }

    /**
     * Register the plugin's settings page in the WordPress admin menu.
     *
     * This function adds a new options page where users can configure settings for the plugin.
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
     * Render the plugin's settings page with different sections (tabs).
     *
     * Depending on the active tab, this function renders the corresponding settings sections.
     */
    public function render_settings_page() {
        // Get the active tab from the query string, default to 'utilities'.
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
                    // Render different sections based on the active tab.
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
