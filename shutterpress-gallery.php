<?php

/**
 * The plugin bootstrap file
 *
 * @link              https://shutterpress.io
 * @since             1.0.0
 * @package           Shutterpress-Gallery
 *
 * @wordpress-plugin
 * Plugin Name:       Shutterpress Gallery
 * Plugin URI:        shutterpress.io
 * Description:       An awesome gallery plugin for photographers to share their work
 * Version:           1.2.3
 * Author:            Shutterpress
 * Author URI:        https://shutterpress.io
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       shutterpress-gallery
 * Domain Path:       /languages
 */

namespace Shutterpress\Gallery;

if (! defined('WPINC')) {
    die;
}

/**
 * Current plugin version.
 */
define('SHUTTERPRESS_GALLERY_VERSION', '1.2.3');

define('SP_GALLERY_DIR', plugin_dir_path(__FILE__));

/**
 * The code that runs during plugin activation.
 */
function sp_gallery_activate_shutterpress_gallery()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-shutterpress-gallery-activator.php';
    Shutterpress_Gallery_Activator::sp_gallery_activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function sp_gallery_deactivate_shutterpress_gallery()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-shutterpress-gallery-deactivator.php';
    Shutterpress_Gallery_Deactivator::sp_gallery_deactivate();
}

register_activation_hook(__FILE__, 'Shutterpress\Gallery\sp_gallery_activate_shutterpress_gallery');
register_deactivation_hook(__FILE__, 'Shutterpress\Gallery\sp_gallery_deactivate_shutterpress_gallery');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-shutterpress-gallery.php';

use Shutterpress\Gallery\Shutterpress_Gallery;

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function sp_gallery_run_shutterpress_gallery()
{

    $plugin = new Shutterpress_Gallery();
    $plugin->sp_gallery_run();

}
sp_gallery_run_shutterpress_gallery();
