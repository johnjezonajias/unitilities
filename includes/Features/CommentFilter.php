<?php

namespace Unitilities\Features;

class CommentFilter {
    /**
     * Constructor.
     */
    public function __construct() {
        add_filter( 'comment_text', [ $this, 'filter_censored_text' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }

    /**
     * Replace censored words or phrases in comment text with ****.
     *
     * @param string $comment_text The comment text.
     * @return string Filtered comment text.
     */
    public function filter_censored_text( $comment_text ) {
        $censored_words = get_option( 'unitilities_censored_words', '' );

        // Split the comma-separated string into an array.
        $censored_words = explode( ',', $censored_words );

        // Filter out empty strings and trim whitespace from each word.
        $censored_words = array_filter( array_map( 'trim', $censored_words ) );

        foreach ( $censored_words as $word ) {
            if ( ! empty( $word ) ) {
                $tooltip = '<span class="tooltip" aria-label="This word is censored for inappropriate language.">****</span>';
                $comment_text = preg_replace(
                    '/' . preg_quote( $word, '/' ) . '/i',
                    $tooltip,
                    $comment_text
                );
            }
        }

        return $comment_text;
    }

    /**
     * Register the settings for censored words.
     */
    public function register_settings() {
        register_setting(
            'unitilities_comment_filter_settings',
            'unitilities_censored_words',
            [
                'type'              => 'array',
                'sanitize_callback' => [ $this, 'sanitize_censored_words' ],
                'default'           => [],
            ]
        );
        add_settings_section(
            'unitilities_comment_filter_section',
            __( 'Comment censorship settings', 'unitilities' ),
            null,
            'unitilities_comment_filter'
        );
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
     * @param string $input The raw input.
     * @return string Sanitized comma-separated string.
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
     */
    public function render_censored_words_field() {
        $censored_words = get_option( 'unitilities_censored_words', '' );

        // Ensure the retrieved value is a string.
        if ( is_array( $censored_words ) ) {
            $censored_words = implode( ',', $censored_words );
        }

        ?>
        <textarea name="unitilities_censored_words" rows="5" cols="50" style="width: 100%;"><?php echo esc_textarea( $censored_words ); ?></textarea>
        <p class="description">
            <?php esc_html_e( 'Enter words or phrases separated by commas to be censored.', 'unitilities' ); ?>
        </p>
        <?php
    }
}
