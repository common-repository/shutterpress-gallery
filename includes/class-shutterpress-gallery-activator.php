<?php

/**
 * Fired during plugin activation
 *
 * @link       https://shutterpress.io
 * @since      1.0.0
 *
 * @package    Shutterpress_Gallery
 * @subpackage Shutterpress_Gallery/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
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

class Shutterpress_Gallery_Activator
{

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function sp_gallery_activate()
    {
        
        require_once plugin_dir_path(__FILE__) . '../admin/class-shutterpress-gallery-admin.php';

        Shutterpress_Gallery_Admin::sp_gallery_register_custom_post();

        flush_rewrite_rules();
    }
}
