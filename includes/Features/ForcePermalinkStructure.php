<?php

namespace Unitilities\Features;

/**
 * Class to enforce a consistent permalink structure.
 * 
 * This class enforces a consistent permalink structure across the WordPress site.
 * It hooks into the `option_permalink_structure` filter and overrides the setting
 * to ensure that all permalinks use the format "/%postname%/".
 *
 * This ensures the permalink structure cannot be accidentally changed via the admin settings.
 */
class ForcePermalinkStructure {

    /**
     * Constructor to initialize the class and hook into WordPress filters.
     */
    public function __construct() {
        // Hook into the filter to override the permalink structure option.
        add_filter( 'option_permalink_structure', [ $this, 'set_permalink_structure' ] );
    }

    /**
     * Enforce a specific permalink structure.
     * 
     * This method is called by the `option_permalink_structure` filter. It forces the
     * permalink structure to always use "/%postname%/" regardless of the admin setting.
     * 
     * @return string The enforced permalink structure.
     */
    public function set_permalink_structure() {
        return '/%postname%/';
    }
}
