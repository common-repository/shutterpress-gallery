<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://shutterpress.io
 * @since      1.0.0
 *
 * @package    Shutterpress_Gallery
 * @subpackage Shutterpress_Gallery/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Shutterpress_Gallery
 * @subpackage Shutterpress_Gallery/includes
 * @author     Shutterpress <info@shutterpress.io>
 */

namespace Shutterpress\Gallery;

if (! defined('WPINC')) {
    die;
}

class Shutterpress_Gallery_Deactivator
{

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function sp_gallery_deactivate()
    {
        
        flush_rewrite_rules();
    }
}
