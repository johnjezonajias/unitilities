<?php

namespace Unitilities\Features;

/**
 * Class to forcefully set the `comment_previously_approved` option to `true`.
 * 
 * This class hooks into the `option_comment_previously_approved` filter to ensure that
 * the `comment_previously_approved` option is always set to `true`, overriding any 
 * previous settings. This functionality is useful in scenarios where you want to
 * programmatically enforce comment approval behavior across all WordPress sites.
 */
class ForceCommentApproval {
    
    /**
     * Constructor to initialize the class and hook into the WordPress filters.
     * 
     * The constructor adds a filter to the `option_comment_previously_approved` option,
     * ensuring that every time this option is accessed, the `force_comment_previously_approved`
     * method is executed.
     */
    public function __construct() {
        add_filter( 'option_comment_previously_approved', [ $this, 'force_comment_previously_approved' ] );
    }

    /**
     * Forcefully sets the `comment_previously_approved` option to `true`.
     * 
     * This method overrides the current value of the `comment_previously_approved` option,
     * returning `true` regardless of the previous setting. It ensures that WordPress will always
     * consider comments as previously approved when the option is accessed.
     *
     * @param mixed $value The current value of the `comment_previously_approved` option. 
     *                     This value is ignored because the method always returns `true`.
     * @return bool Always returns `true`, effectively setting the `comment_previously_approved`
     *              option to true.
     */
    public function force_comment_previously_approved( $value ) {
        return true;
    }
}
