<?php

namespace Unitilities\Features;

/**
 * Class to forcefully set the `thread_comments_depth` option to 2.
 * 
 * This class hooks into the `option_thread_comments_depth` filter to ensure that
 * the `thread_comments_depth` option is always set to 2. This is useful in scenarios
 * where you want to enforce a specific depth limit for threaded comments across all
 * WordPress sites, overriding the default or user-set values.
 */
class ForceThreadCommentsDepth {
    
    /**
     * Constructor to initialize the class and hook into the WordPress filter.
     * 
     * The constructor adds a filter to the `option_thread_comments_depth` option,
     * ensuring that every time this option is accessed, the `set_thread_comments_depth`
     * method is executed to enforce a depth of 2.
     */
    public function __construct() {
        add_filter( 'option_thread_comments_depth', [ $this, 'set_thread_comments_depth' ] );
    }

    /**
     * Forcefully sets the `thread_comments_depth` option to 2.
     * 
     * This method overrides the current value of the `thread_comments_depth` option
     * and ensures that threaded comments are always displayed with a maximum depth of 2,
     * regardless of the existing configuration or user settings.
     *
     * @param int $value The current value of the `thread_comments_depth` option.
     *                   This value is ignored because the method always returns `2`.
     * @return int Always returns `2`, effectively limiting the threaded comment depth.
     */
    public function set_thread_comments_depth( $value ) {
        return 2;
    }
}
