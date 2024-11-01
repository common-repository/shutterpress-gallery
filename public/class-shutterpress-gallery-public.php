<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://shutterpress.io
 * @since      1.0.0
 *
 * @package    Shutterpress_Gallery
 * @subpackage Shutterpress_Gallery/public
 * @author     Shutterpress <info@shutterpress.io>
 */

namespace Shutterpress\Gallery;

if (! defined('WPINC')) {
    die;
}

class Shutterpress_Gallery_Public
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
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    
    /**
     * Initialize the class and set its properties.
    *
    * @since    1.0.0
    * @param      string    $plugin_name       The name of the plugin.
    * @param      string    $version    The version of this plugin.
    */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function sp_gallery_enqueue_styles()
    {

        wp_enqueue_style($this->plugin_name . '-public', plugin_dir_url(__FILE__) . 'css/shutterpress-gallery-public.css', array(), $this->version, 'all');
        wp_enqueue_style('lightgallery-css', plugin_dir_url(__FILE__) . 'css/lightgallery-bundle.min.css', array(), '2.7.1');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function sp_gallery_enqueue_scripts()
    {

        wp_enqueue_script(
            $this->plugin_name . '-public',
            plugin_dir_url(__FILE__) . 'js/shutterpress-gallery-public.js',
            array('jquery', 'js-cookie'),
            $this->version,
            true
        );

        wp_enqueue_script('js-cookie', plugin_dir_url(__FILE__) . 'js/js.cookie.min.js', array(), '3.0.1', true);

        wp_enqueue_script('lightgallery-js', plugin_dir_url(__FILE__) . 'js/lightgallery.bundle.js', array('jquery'), '2.7.1', true);

        wp_enqueue_script('masonry');
        wp_enqueue_script('imagesloaded');

    }

    /**
     * Enqueue the Shutterpress Gallery Elementor editor script.
    *
    * This function is responsible for enqueuing the Shutterpress Gallery Elementor editor script, which is required for the plugin's integration with the Elementor page builder.
    *
    * @since 4.1.4
    */
    public function sp_gallery_enqueue_elementor_editor_scripts()
    {
        wp_enqueue_script('shutterpress-gallery-elementor', plugin_dir_url(__FILE__) . 'js/shutterpress-gallery-elementor.js', array('jquery','elementor-frontend'), '4.1.4', true);
    }

    /**
     * Displays the gallery on the public-facing side of the site.
    *
    * This function is responsible for rendering the gallery content on the public-facing side of the site. It checks if the current page is a singular 'shutterpress-gallery' post, and if so, it creates a new instance of the `Shutterpress_Gallery_Public_Render` class, enqueues the gallery CSS, and returns the rendered gallery content.
    *
    * @param string $content The original content of the page.
    * @return string The modified content with the gallery rendered.
    */
    public function sp_gallery_display_gallery($content)
    {
        if (is_singular('shutterpress-gallery')) {
            
            $post_gallery = new Shutterpress_Gallery_Public_Render();
            $post_gallery->sp_gallery_set_render_attributes();
            
            return $post_gallery->sp_gallery_get_the_gallery($content);
        }
        return $content;
    }

    /**
     * Registers the 'sp_gallery' shortcode.
    *
    * This method registers the 'sp_gallery' shortcode, which allows users to embed a Shutterpress Gallery on their WordPress site. The shortcode is handled by the `sp_gallery_render_gallery_shortcode()` method.
    *
    * @since 1.0.0
    */
    public function sp_gallery_register_gallery_shortcode()
    {
        add_shortcode('sp_gallery', array($this, 'sp_gallery_render_gallery_shortcode'));
    }

    /**
     * Determines if a given value is considered "truthy".
    *
    * This method checks if a given value is considered truthy or falsey. It handles
    * empty strings and null values as truthy, and explicitly checks for common
    * falsey values like 'false', 'no', '0', and false.
    *
    * @param mixed $value The value to check.
    * @return bool True if the value is considered truthy, false otherwise.
    */
    public function sp_gallery_is_truthy($value)
    {
        
        if ($value === '' || $value === null) {
            return true;
        }
        
        $falsey_values = ['false', 'no', '0', false];
        return !in_array(strtolower($value), $falsey_values, true);
    }

    /**
     * Renders the Shutterpress Gallery shortcode.
    *
    * This method is responsible for rendering the Shutterpress Gallery based on the
    * provided attributes. It sets up the necessary rendering attributes, and then
    * calls the `sp_gallery_get_the_gallery()` method to generate the gallery HTML.
    *
    * @param array $attributes An associative array of shortcode attributes.
    * @return string The rendered gallery HTML.
    */
    public function sp_gallery_render_gallery_shortcode($attributes)
    {
        
        $use_lightbox = isset($attributes['use_lightbox'])
            ? $this->sp_gallery_is_truthy($attributes['use_lightbox'])
            : filter_var(get_option('sp_gallery_use_lightbox', true), FILTER_VALIDATE_BOOLEAN);
    
        $show_buttons = isset($attributes['show_buttons'])
            ? $this->sp_gallery_is_truthy($attributes['show_buttons'])
            : filter_var(get_option('sp_gallery_show_buttons', true), FILTER_VALIDATE_BOOLEAN);
    
        $gallery_attributes = [
            'gallery_id' => isset($attributes['id']) ? intval($attributes['id']) : null,
            'use_lightbox' => isset($use_lightbox) ? $use_lightbox : filter_var(get_option('sp_gallery_use_lightbox', true), FILTER_VALIDATE_BOOLEAN),
            'layout' => isset($attributes['layout']) ? $attributes['layout'] : sanitize_text_field(get_option('sp_gallery_layout', 'masonry')),
            'gap' => isset($attributes['gap']) ? $attributes['gap'] : intval(get_option('sp_gallery_column_gap', 20)),
            'show_buttons' => isset($show_buttons) ? $show_buttons : true,
            'type' => isset($attributes['type']) ? $attributes['type'] : 'shortcode',
        ];
    
        $class_prefix = 'shortcode_gallery_';
        $gallery_id = isset($attributes['id']) ? intval($attributes['id']) : null;
    
        ${$class_prefix . $gallery_id} = new Shutterpress_Gallery_Public_Render();
        ${$class_prefix . $gallery_id}->sp_gallery_set_render_attributes($gallery_attributes);
    
        return ${$class_prefix . $gallery_id}->sp_gallery_get_the_gallery('');

    }

    /**
     * Ajax call to toggle the user like icon
     *
     * @since    1.0.0
     */
    public function sp_gallery_toggle_user_like()
    {
        check_ajax_referer('shutterpress_nonce_action', '_ajax_nonce');
        
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            $image_id = isset($_POST['image_id']) ? intval($_POST['image_id']) : 0;

            $liked_images = $this->sp_gallery_get_liked_images($user_id);

            if (!$liked_images) {
                $liked_images = [];
            }

            if (in_array($image_id, $liked_images)) {
                
                $liked_images = array_diff($liked_images, [$image_id]);
                update_user_meta($user_id, 'sp_gallery_liked_images', $liked_images);
                
                wp_send_json_success(['message' => 'Image unliked successfully.', 'sp-gallery-liked-image' => false]);
            } else {
                
                $liked_images[] = $image_id;
                update_user_meta($user_id, 'sp_gallery_liked_images', $liked_images);
                
                wp_send_json_success(['message' => 'Image liked successfully.', 'sp-gallery-liked-image' => true]);
            }
        } else {
            
            wp_send_json_error(['message' => 'User not logged in.']);
        }

        wp_die();
    }

    /**
     * Retrieves the list of images liked by the specified user.
    *
    * @param int $user_id The ID of the user whose liked images should be retrieved.
    * @return array An array of image IDs that the user has liked.
    */
    private function sp_gallery_get_liked_images($user_id)
    {
        $liked_images = get_user_meta($user_id, 'sp_gallery_liked_images', true);
        return $liked_images;
    }

    /**
     * Localise data to send to javascript
     *
     * @since    1.0.0
     */
    public function sp_gallery_localize_frontend_data()
    {
        $localize_data = array(
            'ajax_url' => esc_url(admin_url('admin-ajax.php')),
            'api_url' => esc_url(rest_url('wp/v2/shutterpress-gallery')),
            'is_logged_in' => is_user_logged_in() ? true : false,
            'liked_images' => [],
            'nonce' => wp_create_nonce('shutterpress_nonce_action'),
        );
    
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            $liked_images = $this->sp_gallery_get_liked_images($user_id);
            $localize_data['liked_images'] = $liked_images ? $liked_images : [];

            if (is_array($liked_images)) {
                $liked_images = array_map('intval', $liked_images);
            } else {
                $liked_images = [];
            }
            $localize_data['liked_images'] = $liked_images;
        }
    
        wp_localize_script($this->plugin_name . '-public', 'shutterpressData', $localize_data);
    }

    /**
     * Disable post navigation on gallery pages
     *
     * @since    1.0.0
     */
    public function sp_gallery_disable_post_navigation_on_gallery_pages()
    {
        if (is_singular('shutterpress-gallery')) {
            add_filter('the_post_navigation', '__return_false', 10);
        }
    }

    /**
     * Synchronizes the user's liked images between the cookie and the user meta data.
    *
    * This function checks if the user is logged in and if there is a 'liked_images' cookie. If the cookie exists, it compares the liked images in the cookie with the liked images stored in the user's meta data. If they differ, it merges the new liked images from the cookie and removes the unliked images, and updates the user's meta data with the synced liked images.
    *
    * @since 1.0.0
    */
    public function sp_gallery_sync_liked_images_before_page_load()
    {
        
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            
            if (isset($_COOKIE['liked_images'])) {
                
                $liked_images_from_cookie = array_map('intval', json_decode(stripslashes($_COOKIE['liked_images']), true));
    
                if (!is_array($liked_images_from_cookie)) {
                    $liked_images_from_cookie = [];
                }
    
                $stored_liked_images = $this->sp_gallery_get_liked_images($user_id);
    
                if (!is_array($stored_liked_images)) {
                    $stored_liked_images = [];
                }
    
                if ($liked_images_from_cookie !== $stored_liked_images) {
                    
                    $unliked_images = array_diff($stored_liked_images, $liked_images_from_cookie);
                    $newly_liked_images = array_diff($liked_images_from_cookie, $stored_liked_images);
    
                    $updated_liked_images = array_unique(array_merge(array_diff($stored_liked_images, $unliked_images), $newly_liked_images));
    
                    update_user_meta($user_id, 'sp_gallery_liked_images', $updated_liked_images);
                }
            }
        }
    }

    /**
     * Get the sites colors
     *
     * @since    1.0.0
     */
    private function sp_gallery_get_site_color()
    {
        
        $color_palette = wp_get_global_settings(array('color', 'palette', 'theme'));
        $default_color = '#EE2E4F';
        $plugin_color = !empty(get_option('sp_gallery_button_color')) ? get_option('sp_gallery_button_color') : $default_color;
        error_log('Plugin Color: ' . $plugin_color);
        error_log(print_r($color_palette, true));
    
        if (is_array($color_palette) && !empty($color_palette)) {
            
            foreach ($color_palette as $color) {
                if (isset($color['slug'])) {
                    if ($color['slug'] === 'primary' || $color['slug'] === 'secondary') {
                        $plugin_color = $color['color'];
                        break;
                    }
                }
            }
        }
        error_log('Plugin Color: ' . $plugin_color);
        return $plugin_color;
    }

    /**
     * Convert a hex color to rgba format.
     *
     * @param string $hex Hex color code (e.g., "#ff5733" or "ff5733").
     * @param float $alpha Alpha (opacity) value between 0 and 1.
     * @return string RGBA color string (e.g., "rgba(255, 87, 51, 0.5)").
     */
    private function sp_gallery_hex_to_rgba($hex, $alpha = 1)
    {
        
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = str_repeat($hex[0], 2) . str_repeat($hex[1], 2) . str_repeat($hex[2], 2);
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return "rgba($r, $g, $b, $alpha)";
    }

    /**
     * Darken a hex color by a given percentage.
     *
     * @param string $hex Hex color code (e.g., "#ff5733" or "ff5733").
     * @param float $percent Percentage to darken the color (e.g., 0.2 for 20%).
     * @return string Darkened hex color code.
     */
    private function sp_gallery_darken_color($hex, $percent)
    {
        
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = str_repeat($hex[0], 2) . str_repeat($hex[1], 2) . str_repeat($hex[2], 2);
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $r = max(0, min(255, $r - ($r * $percent)));
        $g = max(0, min(255, $g - ($g * $percent)));
        $b = max(0, min(255, $b - ($b * $percent)));

        return sprintf("#%02x%02x%02x", $r, $g, $b);
    }

    public function sp_gallery_add_inline_styles()
    {
        $style_output = '';

        $color = esc_attr($this->sp_gallery_get_site_color());
        $color_hover = $this->sp_gallery_darken_color($color, 0.1);
        $style_output .= "
			.sp-gallery-button {
    			background-color:{$color};
			}

			.sp-gallery-button:hover, .sp-gallery-button:focus {
				background-color:{$color_hover};
			}

			.sp-gallery-image-icon:hover path {
				stroke: {$color};
			}
		";
        
        $style_output .= "
			.lg-progress-bar .lg-progress {
				background-color: {$color} !important;
			}
			.lg-outer .lg-thumb-item.active, .lg-outer .lg-thumb-item:hover {
				border-color: {$color} !important;
			}
			.lg-toolbar .lg-icon:hover {
				color: {$color} !important;
			}
			.lg-next:hover:not(.disabled), .lg-prev:hover:not(.disabled) {
				color: {$color} !important;
			}
		";
        
        $output = wp_strip_all_tags($style_output);

        wp_add_inline_style('shutterpress-gallery-public', $output);
    }

}
