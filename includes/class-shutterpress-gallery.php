<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://shutterpress.io
 * @since      1.0.0
 *
 * @package    Shutterpress_Gallery
 * @subpackage Shutterpress_Gallery/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
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

class Shutterpress_Gallery
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Shutterpress_Gallery_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        if (defined('SHUTTERPRESS_GALLERY_VERSION')) {
            $this->version = SHUTTERPRESS_GALLERY_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'shutterpress-gallery';

        $this->sp_gallery_load_dependencies();
        $this->sp_gallery_set_locale();
        $this->sp_gallery_define_admin_hooks();
        $this->sp_gallery_define_public_hooks();

    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Shutterpress_Gallery_Loader. Orchestrates the hooks of the plugin.
     * - Shutterpress_Gallery_i18n. Defines internationalization functionality.
     * - Shutterpress_Gallery_Admin. Defines all hooks for the admin area.
     * - Shutterpress_Gallery_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function sp_gallery_load_dependencies()
    {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-shutterpress-gallery-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-shutterpress-gallery-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-shutterpress-gallery-admin.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-shutterpress-gallery-admin-settings.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-shutterpress-gallery-public.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-shutterpress-gallery-public-render.php';

        if (!class_exists('RW_Meta_Box')) {
            require_once plugin_dir_path(dirname(__FILE__)) . 'vendor/meta-box/meta-box/meta-box.php';
        }
        
        $this->loader = new Shutterpress_Gallery_Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Shutterpress_Gallery_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function sp_gallery_set_locale()
    {

        $plugin_i18n = new Shutterpress_Gallery_i18n();

        $this->loader->sp_gallery_add_action('plugins_loaded', $plugin_i18n, 'sp_gallery_load_plugin_textdomain');

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    protected function sp_gallery_define_admin_hooks()
    {

        $plugin_admin = new Shutterpress_Gallery_Admin($this->sp_gallery_get_plugin_name(), $this->sp_gallery_get_version());
        $plugin_admin_settings = new Shutterpress_Gallery_Admin_Settings($this->sp_gallery_get_plugin_name(), $this->sp_gallery_get_version());

        $this->loader->sp_gallery_add_action('admin_enqueue_scripts', $plugin_admin, 'sp_gallery_enqueue_scripts');
        $this->loader->sp_gallery_add_action('admin_enqueue_scripts', $plugin_admin, 'sp_gallery_enqueue_styles');
        $this->loader->sp_gallery_add_action('plugins_loaded', $plugin_admin, 'sp_gallery_check_and_update_version');
        $this->loader->sp_gallery_add_filter('rwmb_meta_boxes', $plugin_admin, 'sp_gallery_register_meta_boxes', 10);
        $this->loader->sp_gallery_add_action('init', $plugin_admin, 'sp_gallery_register_custom_post');
        $this->loader->sp_gallery_add_action('init', $plugin_admin, 'sp_gallery_load_custom_fields');
        $this->loader->sp_gallery_add_action('delete_attachment', $plugin_admin, 'sp_gallery_delete_attachments_from_gallery', 10, 2);
        $this->loader->sp_gallery_add_action('rwmb_sp_gallery_image_sorting_after_save_field', $plugin_admin, 'sp_gallery_sort_images', 30, 5);
        $this->loader->sp_gallery_add_action('admin_menu', $plugin_admin_settings, 'sp_gallery_register_settings_page');
        $this->loader->sp_gallery_add_action('admin_init', $plugin_admin_settings, 'sp_gallery_settings_init');
        $this->loader->sp_gallery_add_action('init', $plugin_admin, 'sp_gallery_register_block');
        $this->loader->sp_gallery_add_action('rest_api_init', $plugin_admin, 'sp_gallery_register_options_route');
        $this->loader->sp_gallery_add_action('elementor/widgets/register', $plugin_admin, 'sp_gallery_register_elementor_widget', 10);
        $this->loader->sp_gallery_add_action('elementor/elements/categories_registered', $plugin_admin, 'sp_gallery_add_elementor_category');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    protected function sp_gallery_define_public_hooks()
    {

        $plugin_public = new Shutterpress_Gallery_Public($this->sp_gallery_get_plugin_name(), $this->sp_gallery_get_version());

        $this->loader->sp_gallery_add_action('wp_enqueue_scripts', $plugin_public, 'sp_gallery_enqueue_styles');
        $this->loader->sp_gallery_add_action('wp_enqueue_scripts', $plugin_public, 'sp_gallery_enqueue_scripts');
        $this->loader->sp_gallery_add_action('init', $plugin_public, 'sp_gallery_register_gallery_shortcode');
        $this->loader->sp_gallery_add_action('wp_enqueue_scripts', $plugin_public, 'sp_gallery_add_inline_styles');
        $this->loader->sp_gallery_add_filter('the_content', $plugin_public, 'sp_gallery_display_gallery', 10, 2);
        $this->loader->sp_gallery_add_action('wp_ajax_sp_gallery_toggle_user_like', $plugin_public, 'sp_gallery_toggle_user_like');
        $this->loader->sp_gallery_add_action('wp_enqueue_scripts', $plugin_public, 'sp_gallery_localize_frontend_data');
        $this->loader->sp_gallery_add_action('wp', $plugin_public, 'sp_gallery_disable_post_navigation_on_gallery_pages');
        $this->loader->sp_gallery_add_action('template_redirect', $plugin_public, 'sp_gallery_sync_liked_images_before_page_load');
        $this->loader->sp_gallery_add_action('elementor/preview/enqueue_scripts', $plugin_public, 'sp_gallery_enqueue_elementor_editor_scripts');
        
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function sp_gallery_run()
    {
        $this->loader->sp_gallery_run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function sp_gallery_get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Shutterpress_Gallery_Loader    Orchestrates the hooks of the plugin.
     */
    public function sp_gallery_get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function sp_gallery_get_version()
    {
        return $this->version;
    }

}
