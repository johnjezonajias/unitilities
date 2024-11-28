<?php

namespace Unitilities\Features;

/**
 * Class CommentFilter
 *
 * Provides functionality to filter and censor specific words or phrases in WordPress comments.
 * Censored words are replaced with "****" and can be managed via an admin settings page.
 */
class CommentFilter {
    /**
     * Constructor.
     *
     * Hooks into WordPress to apply censored word filtering to comments and
     * to register admin settings for configuring the censored words.
     */
    public function __construct() {
        // Hook to filter comment text and replace censored words.
        add_filter( 'comment_text', [ $this, 'filter_censored_text' ] );

        // Hook to register settings for managing censored words.
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }

    /**
     * Filter censored words in comment text.
     *
     * Replaces any occurrence of the specified censored words in the comment
     * text with "****", displayed with a tooltip explanation.
     *
     * @param string $comment_text The original comment text.
     * @return string The filtered comment text with censored words replaced.
     */
    public function filter_censored_text( $comment_text ) {
        // Retrieve the list of censored words from the settings.
        $censored_words = get_option( 'unitilities_censored_words', '' );

        // Convert the comma-separated list into an array.
        $censored_words = explode( ',', $censored_words );

        // Trim and remove any empty values.
        $censored_words = array_filter( array_map( 'trim', $censored_words ) );

        // Loop through each censored word and replace it with "****".
        foreach ( $censored_words as $word ) {
            if ( ! empty( $word ) ) {
                $tooltip = '<span class="tooltip" aria-label="This word is censored for inappropriate language.">****</span>';
                $comment_text = preg_replace(
                    '/' . preg_quote( $word, '/' ) . '/i', // Case-insensitive match.
                    $tooltip,
                    $comment_text
                );
            }
        }

        return $comment_text;
    }

    /**
     * Register the settings for managing censored words.
     *
     * Adds a setting field to allow users to define words or phrases to censor
     * in comments. The settings are displayed in the admin panel.
     */
    public function register_settings() {
        // Register the option to store censored words.
        register_setting(
            'unitilities_comment_filter_settings',
            'unitilities_censored_words',
            [
                'type'              => 'array', // Data type is array (stored as serialized string).
                'sanitize_callback' => [ $this, 'sanitize_censored_words' ],
                'default'           => [],
            ]
        );

        // Add a section for the comment filter settings.
        add_settings_section(
            'unitilities_comment_filter_section',
            __( 'Comment censorship settings', 'unitilities' ),
            null, // No description callback for the section.
            'unitilities_comment_filter'
        );

        // Add a field to input censored words.
        add_settings_field(
            'unitilities_censored_words',
            __( 'Censored Words', 'unitilities' ),
            [ $this, 'render_censored_words_field' ],
            'unitilities_comment_filter',
            'unitilities_comment_filter_section'
        );
    }

    /**
     * Sanitize the list of censored words.
     *
     * Ensures the input is a comma-separated string of trimmed words, removing
     * unnecessary spaces and empty entries.
     *
     * @param string $input The raw input from the settings form.
     * @return string A sanitized comma-separated string of words.
     */
    public function sanitize_censored_words( $input ) {
        if ( is_string( $input ) ) {
            // Split by commas, trim whitespace, and filter empty entries.
            $words = array_filter( array_map( 'trim', explode( ',', $input ) ) );

            // Join back into a comma-separated string.
            return implode( ',', $words );
        }

        return '';
    }
    
    /**
     * Render the censored words input field.
     *
     * Outputs a textarea for admins to define words or phrases to censor,
     * along with a description of the field's purpose.
     */
    public function render_censored_words_field() {
        // Retrieve the current setting for censored words.
        $censored_words = get_option( 'unitilities_censored_words', '' );

        // Convert to a string if stored as an array.
        if ( is_array( $censored_words ) ) {
            $censored_words = implode( ',', $censored_words );
        }

        // Render the textarea with the current value.
        ?>
        <textarea name="unitilities_censored_words" rows="5" cols="50" style="width: 100%;"><?php echo esc_textarea( $censored_words ); ?></textarea>
        <p class="description">
            <?php esc_html_e( 'Enter words or phrases separated by commas to be censored.', 'unitilities' ); ?>
        </p>
        <?php
    }
}
