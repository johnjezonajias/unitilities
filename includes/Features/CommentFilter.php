<?php

namespace Unitilities\Features;

/**
 * Class CommentFilter
 *
 * This class provides functionality to censor specific words or phrases in WordPress comments.
 * Censored words are replaced with a tooltip display for transparency. 
 * The list of censored words can be managed via an admin settings page.
 */
class CommentFilter {

    /**
     * Constructor.
     *
     * Hooks into WordPress to filter comment text and replace censored words, 
     * and registers admin settings for configuring the censored words.
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
     * Replaces occurrences of specified censored words in the comment text
     * with "*****", displayed with a tooltip explanation.
     *
     * @param string $comment_text The original comment text.
     * @return string The filtered comment text with censored words replaced.
     */
    public function filter_censored_text( $comment_text ) {
        // Retrieve the list of censored words from settings.
        $censored_words = get_option( 'unitilities_censored_words', '' );

        // Ensure the result is a string before processing.
        if ( is_array( $censored_words ) ) {
            $censored_words = implode( ',', $censored_words );
        }

        // Convert the comma-separated string into an array.
        $censored_words = explode( ',', $censored_words );

        // Trim whitespace and remove empty entries.
        $censored_words = array_filter( array_map( 'trim', $censored_words ) );

        // Replace each censored word in the comment text with "*****".
        foreach ( $censored_words as $word ) {
            if ( ! empty( $word ) ) {
                $tooltip = '<span class="tooltip" aria-label="This word is censored for inappropriate language.">*****</span>';
                $escaped_word = preg_quote( $word, '/' ); // Escape special characters.
                $comment_text = preg_replace(
                    '/\b' . $escaped_word . '\b/i', // Match whole words, case-insensitive.
                    $tooltip,
                    $comment_text
                );
            }
        }

        return $comment_text;
    }

    /**
     * Register settings for managing censored words.
     *
     * Adds a setting to the admin panel to define words or phrases to censor in comments.
     */
    public function register_settings() {
        // Register the option for storing censored words.
        register_setting(
            'unitilities_comment_filter_settings',
            'unitilities_censored_words',
            [
                'type'              => 'string', // Ensure a single string value.
                'sanitize_callback' => [ $this, 'sanitize_censored_words' ],
                'default'           => '',
            ]
        );

        // Add a section for the comment filter settings.
        add_settings_section(
            'unitilities_comment_filter_section',
            __( 'Comment Censorship Settings', 'unitilities' ),
            null, // No description callback for the section.
            'unitilities_comment_filter'
        );

        // Add a field for inputting censored words.
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
     * Ensures the input is a comma-separated string of trimmed words.
     *
     * @param string $input The raw input from the settings form.
     * @return string A sanitized, comma-separated string of words.
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
     * Displays a textarea for admins to define censored words or phrases.
     */
    public function render_censored_words_field() {
        // Retrieve the current setting for censored words.
        $censored_words = get_option( 'unitilities_censored_words', '' );

        // Ensure the value is a string.
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
