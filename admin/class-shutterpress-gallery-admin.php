<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://shutterpress.io
 * @since      1.0.0
 *
 * @package    Shutterpress_Gallery
 * @subpackage Shutterpress_Gallery/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Shutterpress_Gallery
 * @subpackage Shutterpress_Gallery/admin
 * @author     Shutterpress <info@shutterpress.io>
 */

namespace Shutterpress\Gallery;

if (! defined('WPINC')) {
    die;
}

class Shutterpress_Gallery_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }
        
    /**
     * Register the CSS stylesheet for the admin area.
    *
    * This function enqueues the plugin's admin-specific CSS stylesheet.
    *
    * @since    1.0.0
    */
    public function sp_gallery_enqueue_styles($hook_suffix)
    {

        wp_enqueue_style($this->plugin_name . '-admin', plugin_dir_url(__FILE__) . 'css/shutterpress-gallery-admin.css', array(), $this->version, 'all');

        if ($hook_suffix !== 'shutterpress-gallery_page_sp-gallery-settings') {
            return;
        }
        wp_enqueue_style('wp-color-picker');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function sp_gallery_enqueue_scripts($hook_suffix)
    {

        wp_enqueue_script($this->plugin_name . '-admin', plugin_dir_url(__FILE__) . 'js/shutterpress-gallery-admin.js', array( 'jquery','jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), $this->version, false);
        
        if ($hook_suffix !== 'shutterpress-gallery_page_sp-gallery-settings') {
            return;
        }
        wp_enqueue_script('wp-color-picker');
        
    }

    /**
     * Checks and updates the plugin's database version.
    *
    * This function checks the stored version and updates it if it doesn't match the current version. If the stored version is less than or equal to 1.0.2, it runs migration logic to update the old gallery images.
    *
    * @since 1.1.0
    */
    public function sp_gallery_check_and_update_version()
    {
        
        $stored_version = get_option('shutterpress_gallery_version');
    
        if ($stored_version === false || version_compare($stored_version, '1.0.2', '<=')) {
            
            $this->sp_gallery_migrate_old_gallery_images();
        }
    
        if ($stored_version !== $this->version) {
            update_option('shutterpress_gallery_version', $this->version);
        }
    }

    /**
     * Loads the custom image advanced field class.
    *
    * This function includes the file that defines the custom image advanced field class, which is used in the plugin's meta boxes.
    *
    * @since 1.0.0
    */
    public function sp_gallery_load_custom_fields()
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-custom-image-advanced-field.php';
    }

    /**
     * Registers the meta boxes for the gallery admin page
     *
     * @since    1.0.0
     */
    public function sp_gallery_register_meta_boxes($meta_boxes)
    {
        $prefix = 'sp_gallery_';
        $post_id = isset($_GET['post']) ? absint($_GET['post']) : get_the_ID();
    
        $meta_boxes[] = [
            'title'      => esc_html__('Images', 'shutterpress-gallery'),
            'id'         => sanitize_key($prefix . 'images-box'),
            'context'    => 'normal',
            'post_types' => 'shutterpress-gallery',
            'fields'     => [
                [
                    'type'   	=> 'custom_image_advanced',
                    'id' 		=> sanitize_key('_' . $prefix . 'images_array'),
                    'name' 		=> esc_html__('Images', 'shutterpress-gallery'),
                    'multiple' 	=> true,
                ],
                [
                    'name'            => esc_html__('Sorting', 'shutterpress-gallery'),
                    'id'              => sanitize_key($prefix . 'image_sorting'),
                    'type'            => 'select',
                    'multiple'        => false,
                    'options'         => [
                        'drag_n_drop'      	=> esc_html__('Drag & Drop', 'shutterpress-gallery'),
                        'filename_asc'     	=> esc_html__('Filename Ascending', 'shutterpress-gallery'),
                        'filename_desc'    	=> esc_html__('Filename Descending', 'shutterpress-gallery'),
                        'shoot_date_asc'   	=> esc_html__('Shoot Time Ascending', 'shutterpress-gallery'),
                        'shoot_date_desc'	=> esc_html__('Shoot Time Descending', 'shutterpress-gallery'),
                        'file_date_asc'     => esc_html__('Save Time Ascending', 'shutterpress-gallery'),
                        'file_date_desc'    => esc_html__('Save Time Descending', 'shutterpress-gallery'),
                        'upload_date_asc'   => esc_html__('Upload Time Ascending', 'shutterpress-gallery'),
                        'upload_date_desc'  => esc_html__('Upload Time Descending', 'shutterpress-gallery'),
                    ],
                ],
            ],
        ];

        $meta_boxes[] = [
            'title'      => esc_html__('Gallery Options', 'shutterpress-gallery'),
            'id'         => sanitize_key($prefix . 'options-box'),
            'context'    => 'normal',
            'post_types' => 'shutterpress-gallery',
            'fields'     => [
                [
                    'type'  => 'switch',
                    'name'  => esc_html__('Allow favourite images', 'shutterpress-gallery'),
                    'id'    => sanitize_key($prefix . 'show_like_icon'),
                    
                    'style' => 'rounded',
                    'std'   => intval(1),
                ],
                [
                    'type'  => 'switch',
                    'name'  => esc_html__('Make Images available for download', 'shutterpress-gallery'),
                    'id'    => sanitize_key($prefix . 'show_download_icon'),
                    
                    'style' => 'rounded',
                    'std'   => intval(1),
                ],
                [
                    'type'  => 'switch',
                    'name'  => esc_html__('Download Full-size Image', 'shutterpress-gallery'),
                    'id'    => sanitize_key($prefix . 'download_fullsize_image'),
                    'style' => 'rounded',
                    'std'   => intval(0),
                    'desc'  => esc_html__('Choose whether to download the full-size image or the scaled image. WordPress automatically scales images larger than 2560px on either side.', 'shutterpress-gallery'),
                ],
            ],
        ];

        if ($post_id) {
            $meta_boxes[] = [
                'title'      => esc_html__('Gallery Shortcode', 'shutterpress-gallery'),
                'id'         => sanitize_key($prefix . 'shortcode-box'),
                'context'    => 'side',
                'priority'   => 'low',
                'post_types' => 'shutterpress-gallery',
                'fields'     => [
                    [
                        'type' => 'custom_html',
                        'std'  => (
                            '<p>' . esc_html__('Use this shortcode to display the gallery:', 'shutterpress-gallery') . '</p>' .
                            '<input type="text" readonly="readonly" value="[sp_gallery id=' . esc_attr($post_id) . ']" style="width: 100%;" />'
                        ),
                    ],
                ],
            ];
        }
    
        return $meta_boxes;
        
    }

    /**
     * Register custom post type shutterpress-gallery
     *
     * @since    1.0.0
     */
    public static function sp_gallery_register_custom_post()
    {
    
        $labels = [
            "name" => esc_html__("Galleries", "shutterpress-gallery"),
            "singular_name" => esc_html__("Gallery", "shutterpress-gallery"),
            "menu_name" => esc_html__("SP Galleries", "shutterpress-gallery"),
            "all_items" => esc_html__("All Galleries", "shutterpress-gallery"),
            "add_new" => esc_html__("Add new", "shutterpress-gallery"),
            "add_new_item" => esc_html__("Add new Gallery", "shutterpress-gallery"),
            "edit_item" => esc_html__("Edit Gallery", "shutterpress-gallery"),
            "new_item" => esc_html__("New Gallery", "shutterpress-gallery"),
            "view_item" => esc_html__("View Gallery", "shutterpress-gallery"),
            "view_items" => esc_html__("View Galleries", "shutterpress-gallery"),
            "search_items" => esc_html__("Search Galleries", "shutterpress-gallery"),
            "not_found" => esc_html__("No Galleries found", "shutterpress-gallery"),
            "not_found_in_trash" => esc_html__("No Galleries found in trash", "shutterpress-gallery"),
            "parent" => esc_html__("Parent Gallery:", "shutterpress-gallery"),
            "featured_image" => esc_html__("Featured image for this Gallery", "shutterpress-gallery"),
            "set_featured_image" => esc_html__("Set featured image for this Gallery", "shutterpress-gallery"),
            "remove_featured_image" => esc_html__("Remove featured image for this Gallery", "shutterpress-gallery"),
            "use_featured_image" => esc_html__("Use as featured image for this Gallery", "shutterpress-gallery"),
            "archives" => esc_html__("Gallery archives", "shutterpress-gallery"),
            "insert_into_item" => esc_html__("Insert into Gallery", "shutterpress-gallery"),
            "uploaded_to_this_item" => esc_html__("Upload to this Gallery", "shutterpress-gallery"),
            "filter_items_list" => esc_html__("Filter Galleries list", "shutterpress-gallery"),
            "items_list_navigation" => esc_html__("Galleries list navigation", "shutterpress-gallery"),
            "items_list" => esc_html__("Galleries list", "shutterpress-gallery"),
            "attributes" => esc_html__("Galleries attributes", "shutterpress-gallery"),
            "name_admin_bar" => esc_html__("Gallery", "shutterpress-gallery"),
            "item_published" => esc_html__("Gallery published", "shutterpress-gallery"),
            "item_published_privately" => esc_html__("Gallery published privately.", "shutterpress-gallery"),
            "item_reverted_to_draft" => esc_html__("Gallery reverted to draft.", "shutterpress-gallery"),
            "item_scheduled" => esc_html__("Gallery scheduled", "shutterpress-gallery"),
            "item_updated" => esc_html__("Gallery updated.", "shutterpress-gallery"),
            "parent_item_colon" => esc_html__("Parent Gallery:", "shutterpress-gallery"),
        ];
    
        $args = [
            'label'                 => esc_html__('Galleries', 'shutterpress-gallery'),
            'labels'                => $labels,
            'description'           => '',
            'public'                => true,
            'publicly_queryable'    => true,
            'show_ui'               => true,
            'show_in_rest'          => true,
            'rest_base'             => sanitize_key(''),
            'rest_controller_class' => 'WP_REST_Posts_Controller',
            'rest_namespace'        => sanitize_text_field('wp/v2'),
            'has_archive'           => false,
            'show_in_menu'          => true,
            'show_in_nav_menus'     => true,
            'delete_with_user'      => false,
            'exclude_from_search'   => false,
            'capability_type'       => 'page',
            'map_meta_cap'          => true,
            'hierarchical'          => true,
            'can_export'            => false,
            'rewrite'               => [
                'slug'      		=> sanitize_title('gallery'),
                'with_front' 		=> true,
            ],
            'query_var'             => true,
            'menu_icon'             => esc_attr('dashicons-camera'),
            'supports'              => array_map('sanitize_key', [ 'title', 'thumbnail', 'custom-fields' ]),
            'show_in_graphql'       => false,
        ];
    
        register_post_type("shutterpress-gallery", $args);
    }

    /**
     * Delete an attachment from the gallery metadata of all posts.
     *
     * @param int \$attachment_id The attachment ID to delete.
     * @since 1.0.0
     */
    public function sp_gallery_delete_attachments_from_gallery($attachment_id)
    {

        $attachment_id = absint($attachment_id);

        $args = array(
            'post_type' => 'shutterpress-gallery',
            'posts_per_page' => -1
        );

        $query = new \WP_Query($args);
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();

                $gallery_images = get_post_meta(get_the_ID(), '_sp_gallery_images_array', true);
                if (is_array($gallery_images) && in_array($attachment_id, $gallery_images)) {
                    $updated_gallery_images = array_diff($gallery_images, array($attachment_id));
                    update_post_meta(get_the_ID(), '_sp_gallery_images_array', $updated_gallery_images);
                }
            }
            wp_reset_postdata();
        }
    }

    /**
     * Migrate old individual 'sp_gallery_images' meta entries into a single '_sp_gallery_images_array' in batches.
     *
     * @param int $batch_size The number of posts to process per batch.
     * @param int $offset     The number of posts to skip (used for batch processing).
     *
     * @return int The number of posts migrated in the current batch.
     */
    public function sp_gallery_migrate_old_gallery_images($batch_size = 100, $offset = 0)
    {
        
        $args = array(
            'post_type'      => 'shutterpress-gallery',
            'meta_key'       => 'sp_gallery_images',
            'posts_per_page' => $batch_size,
            'offset'         => $offset,
            'fields'         => 'ids',
        );

        $query = new \WP_Query($args);

        if (! $query->have_posts()) {
            
            return 0;
        }

        foreach ($query->posts as $post_id) {
            
            $image_ids = get_post_meta($post_id, 'sp_gallery_images', false);

            $image_ids = array_filter(array_map('intval', $image_ids));

            if (! empty($image_ids)) {
                
                update_post_meta($post_id, '_sp_gallery_images_array', $image_ids);
            }

            delete_post_meta($post_id, 'sp_gallery_images');
        }

        return count($query->posts);
    }

    /**
     * Registers the Shutterpress Gallery block.
    *
    * This function registers the Shutterpress Gallery block, which allows users to
    * insert a Shutterpress Gallery into their WordPress posts or pages.
    */
    public function sp_gallery_register_block()
    {
        register_block_type(plugin_dir_path(dirname(__FILE__)) .  '/includes/blocks/shutterpress-gallery-block/');
    }

    /**
     * Sorts the images associated with a Shutterpress Gallery post based on the selected sorting option.
     *
     * This function is a callback for the 'update_post_meta' action, which is triggered when the sorting option
     * for a Shutterpress Gallery post is updated. It retrieves the existing images array, sorts it based on the
     * new sorting option, and updates the '_sp_gallery_images_array' post meta with the sorted array.
     *
     * @param mixed  $null      Unused parameter.
     * @param string $field     The name of the custom field being updated.
     * @param mixed  $new       The new value of the custom field.
     * @param mixed  $old       The old value of the custom field.
     * @param int    $object_id The ID of the post being updated.
     */
    public function sp_gallery_sort_images($null, $field, $new, $old, $object_id)
    {
        
        if ($new !== $old && $new !== 'drag_n_drop') {
            $images = get_post_meta($object_id, '_sp_gallery_images_array', true);
            
            $sorting_option = $new;

            if (empty($images) || empty($sorting_option)) {
                return $images;
            }

            $sorted_images = $this->sp_gallery_sort_images_by_option($images, $sorting_option);

            update_post_meta($object_id, '_sp_gallery_images_array', $sorted_images);
        }
    }

    /**
     * Sorts the given array of images based on the specified sorting option.
     *
     * @param array $images The array of images to be sorted.
     * @param string $sorting_option The sorting option to use, such as 'shoot_date_asc', 'filename_desc', etc.
     * @return array The sorted array of images.
     */
    private function sp_gallery_sort_images_by_option($images, $sorting_option)
    {
        switch ($sorting_option) {
            case 'shoot_date_asc':
                usort($images, function ($a, $b) {
                    return $this->sp_gallery_get_shoot_date($a) - $this->sp_gallery_get_shoot_date($b);
                });
                break;

            case 'shoot_date_desc':
                usort($images, function ($a, $b) {
                    return $this->sp_gallery_get_shoot_date($b) - $this->sp_gallery_get_shoot_date($a);
                });
                break;

            case 'filename_asc':
                usort($images, function ($a, $b) {
                    return strcasecmp($this->sp_gallery_get_filename($a), $this->sp_gallery_get_filename($b));
                });
                break;

            case 'filename_desc':
                usort($images, function ($a, $b) {
                    return strcasecmp($this->sp_gallery_get_filename($b), $this->sp_gallery_get_filename($a));
                });
                break;

            case 'file_date_asc':
                usort($images, function ($a, $b) {
                    return $this->sp_gallery_get_file_date($a) - $this->sp_gallery_get_file_date($b);
                });
                break;

            case 'file_date_desc':
                usort($images, function ($a, $b) {
                    return $this->sp_gallery_get_file_date($b) - $this->sp_gallery_get_file_date($a);
                });
                break;

            case 'upload_date_asc':
                usort($images, function ($a, $b) {
                    return $this->sp_gallery_get_upload_date($a) - $this->sp_gallery_get_upload_date($b);
                });
                break;
    
            case 'upload_date_desc':
                usort($images, function ($a, $b) {
                    return $this->sp_gallery_get_upload_date($b) - $this->sp_gallery_get_upload_date($a);
                });
                break;

            case 'drag_n_drop':
                
                break;

            default:
                
                break;
        }

        return $images;
    }

    /**
     * Retrieves the shoot date of an image attachment.
     *
     * @param int $image_id The ID of the image attachment.
     * @return int The timestamp of the image attachment's shoot date, or 0 if the date is not available.
     */
    private function sp_gallery_get_shoot_date($image_id)
    {
        return wp_get_attachment_metadata($image_id)['image_meta']['created_timestamp'] ?? 0;
    }

    /**
     * Retrieves the file date of an image attachment.
     *
     * @param int $image_id The ID of the image attachment.
     * @return int The timestamp of the image attachment's file date, or 0 if the date is not available.
     */
    private function sp_gallery_get_file_date($image_id)
    {
        return filemtime(get_attached_file($image_id)) ?: 0;
    }

    /**
     * Retrieves the filename of an image attachment.
     *
     * @param int $image_id The ID of the image attachment.
     * @return string The filename of the image attachment.
     */
    private function sp_gallery_get_filename($image_id)
    {
        return basename(wp_get_attachment_url($image_id));
    }

    /**
     * Retrieves the upload date of an image attachment.
    *
    * @param int $image_id The ID of the image attachment.
    * @return int The timestamp of the image attachment's upload date, or 0 if the date is not available.
    */
    private function sp_gallery_get_upload_date($image_id)
    {
        $post = get_post($image_id);
        return isset($post->post_date) ? strtotime($post->post_date) : 0;
    }

    /**
     * Registers a custom REST API route to retrieve plugin options.
    *
    * This method registers a new REST API route at `/wp/v2/options` that can be accessed by users with the `manage_options` capability (typically administrators). When the route is accessed, it calls the `sp_gallery_get_options_for_api()` method to retrieve the following plugin options:
    *
    * - `sp_gallery_use_lightbox`: Whether the lightbox feature is enabled.
    * - `sp_gallery_layout`: The selected layout for the gallery.
    * - `sp_gallery_column_gap`: The gap between columns in the gallery.
    * - `sp_gallery_columns_desktop`: The number of columns to display on desktop devices.
    * - `sp_gallery_columns_tablet`: The number of columns to display on tablet devices.
    * - `sp_gallery_columns_mobile`: The number of columns to display on mobile devices.
    *
    * The method returns the options as a JSON response.
    */
    public function sp_gallery_register_options_route()
    {
        register_rest_route('shutterpress/v1', '/options', array(
            'methods' => 'GET',
            'callback' => array($this, 'sp_gallery_get_options_for_api'),
            'permission_callback' => function () {
                return current_user_can('manage_options');
            },
        ));
    }

    /**
     * Retrieves the options for the Shutterpress Gallery plugin and returns them as a JSON response.
    *
    * This method is registered as a custom REST API route at `/wp/v2/options`. It retrieves the following options:
    *
    * - `sp_gallery_use_lightbox`: Whether the lightbox feature is enabled.
    * - `sp_gallery_layout`: The selected layout for the gallery.
    * - `sp_gallery_column_gap`: The gap between columns in the gallery.
    * - `sp_gallery_columns_desktop`: The number of columns to display on desktop devices.
    * - `sp_gallery_columns_tablet`: The number of columns to display on tablet devices.
    * - `sp_gallery_columns_mobile`: The number of columns to display on mobile devices.
    *
    * The method returns the options as a JSON response, and is only accessible to users with the `manage_options` capability (typically administrators).
    *
    * @return WP_REST_Response The options as a JSON response.
    */
    public function sp_gallery_get_options_for_api()
    {
        
        $options = array(
            'sp_gallery_use_lightbox' => filter_var(get_option('sp_gallery_use_lightbox'), FILTER_VALIDATE_BOOLEAN),
            'sp_gallery_layout' => sanitize_text_field(get_option('sp_gallery_layout')),
            'sp_gallery_column_gap' => intval(get_option('sp_gallery_column_gap')),
            'sp_gallery_columns_desktop' => intval(get_option('sp_gallery_columns_desktop')),
            'sp_gallery_columns_tablet' => intval(get_option('sp_gallery_columns_tablet')),
            'sp_gallery_columns_mobile' => intval(get_option('sp_gallery_columns_mobile')),
        );
    
        return rest_ensure_response($options);
    }

    /**
     * Register List Widget.
     *
     * Include widget file and register widget class.
     *
     * @since 1.0.0
     * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
     * @return void
     */
    public function sp_gallery_register_elementor_widget($widgets_manager)
    {

        if ($this->sp_gallery_elementor_loaded()) {
            require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-shutterpress-gallery-elementor-widget.php';
            
            if (class_exists('\Elementor\Shutterpress_Gallery_Elementor_Widget')) {
                \Elementor\Plugin::instance()->widgets_manager->register(new \Elementor\Shutterpress_Gallery_Elementor_Widget());
            }
        }
    }

    /**
     * Checks if Elementor is loaded.
    *
    * This method checks if the Elementor plugin is loaded and active. It returns true if Elementor is loaded, and false otherwise.
    *
    * @return bool True if Elementor is loaded, false otherwise.
    */
    public function sp_gallery_elementor_loaded()
    {
        if (did_action('elementor/loaded')) {
            
            return true;
        } else {
            
            return false;
        }
    }

    /**
    * Register a custom category for Shutterpress Gallery widgets in Elementor.
    *
    * This method adds a new category named 'shutterpress' to the Elementor widgets manager,
    * with the title 'Shutterpress' and the icon 'fa fa-plug'.
    *
    * @param \Elementor\Widgets_Manager $elements_manager The Elementor widgets manager instance.
    * @return void
    */
    public function sp_gallery_add_elementor_category($elements_manager)
    {
        
        $category_id = sanitize_key('shutterpress');

        $elements_manager->add_category(
            $category_id,
            [
                'title' => esc_html__('Shutterpress', 'shutterpress-gallery'),
                'icon'  => esc_attr('fa fa-plug'),
            ]
        );
    }

}
