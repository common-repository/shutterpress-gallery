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

class Shutterpress_Gallery_Public_Render
{

    public $gallery_id;
    public $use_lightbox;
    public $layout;
    public $gap;
    public $columns_desktop;
    public $columns_tablet;
    public $columns_mobile;
    public $breakpoint_tablet;
    public $breakpoint_mobile;
    public $show_buttons;
    public $type;
    public $styles_output = '';
    private $gallery_instances = array();
    private $scripts_enqueued = false;
    public $scripts_to_enqueue = array();
    public $styles_to_enqueue = array();

    public function __construct()
    {
        $this->gallery_id = (intval(get_the_ID()));
        $this->use_lightbox = filter_var(get_option('sp_gallery_use_lightbox', true), FILTER_VALIDATE_BOOLEAN);
        $this->layout = sanitize_text_field(get_option('sp_gallery_layout', 'grid'));
        $this->gap = intval(get_option('sp_gallery_column_gap', 20));
        $this->columns_desktop = intval(get_option('sp_gallery_columns_desktop', 3));
        $this->columns_tablet = intval(get_option('sp_gallery_columns_tablet', 2));
        $this->columns_mobile = intval(get_option('sp_gallery_columns_mobile', 1));
        $this->breakpoint_tablet = intval(get_option('sp_gallery_breakpoint_tablet', 768));
        $this->breakpoint_mobile = intval(get_option('sp_gallery_breakpoint_mobile', 480));
        $this->show_buttons = true;
        $this->type = 'gallery-post';
        
    }

    /**
     * Sets the core rendering attributes for the Shutterpress Gallery.
    *
    * This method is used to initialize the core rendering attributes for the
    * Shutterpress Gallery based on the provided attributes array. The attributes
    * are then used throughout the rendering process to control the appearance and
    * behavior of the gallery.
    *
    * @param array $attributes An associative array of gallery rendering attributes.
    */
    public function sp_gallery_set_render_attributes($attributes = [])
    {
        $this->gallery_id = isset($attributes['gallery_id']) ? intval($attributes['gallery_id']) : (intval(get_the_ID()));
        $this->use_lightbox = isset($attributes['use_lightbox']) ? filter_var($attributes['use_lightbox'], FILTER_VALIDATE_BOOLEAN) : $this->use_lightbox;
        $this->layout = isset($attributes['layout']) ? sanitize_text_field($attributes['layout']) : $this->layout;
        $this->gap = isset($attributes['gap']) ? intval($attributes['gap']) : $this->gap;
        $this->columns_desktop = isset($attributes['columns_desktop']) ? intval($attributes['columns_desktop']) : $this->columns_desktop;
        $this->columns_tablet = isset($attributes['columns_tablet']) ? intval($attributes['columns_tablet']) : $this->columns_tablet;
        $this->columns_mobile = isset($attributes['columns_mobile']) ? intval($attributes['columns_mobile']) : $this->columns_mobile;
        $this->breakpoint_tablet = isset($attributes['breakpoint_tablet']) ? intval($attributes['breakpoint_tablet']) : $this->breakpoint_tablet;
        $this->breakpoint_mobile = isset($attributes['breakpoint_mobile']) ? intval($attributes['breakpoint_mobile']) : $this->breakpoint_mobile;
        $this->show_buttons = isset($attributes['show_buttons']) ? filter_var($attributes['show_buttons'], FILTER_VALIDATE_BOOLEAN) : $this->show_buttons;
        $this->type = isset($attributes['type']) ? sanitize_text_field($attributes['type']) : $this->type;
    }

    /**
     * Renders the gallery content on the front-end.
    *
    * @param string $content The content to be displayed.
    * @param int|null $gallery_id The ID of the gallery to be displayed, or null to use the current post ID.
    * @param string $type The type of gallery to be displayed (e.g. shortcode, block, etc.).
    * @param array $attributes Additional attributes to be used in rendering the gallery.
    * @return string The rendered gallery content.
    */
    public function sp_gallery_get_the_gallery($content)
    {
        $post_id = $this->gallery_id ? $this->gallery_id : get_the_ID();
    
        if (is_singular('shutterpress-gallery') || $this->gallery_id) {
            
            $output = $content;
    
            if (function_exists('rwmb_meta')) {
                $gallery_images = get_post_meta($post_id, '_sp_gallery_images_array', true);
                
                if (!empty($gallery_images)) {
    
                    $show_like_icon = get_post_meta($post_id, 'sp_gallery_show_like_icon', true);
                    $show_download_icon = get_post_meta($post_id, 'sp_gallery_show_download_icon', true);

                    if ($this->type) {
                        $gallery_type = ' sp-gallery-' . $this->type . '-gallery';
                        $item_type = ' sp-gallery-' . $this->type . '-item';
                        if ($this->type === 'elementor') {
                            $buttons_container_styles = ' sp-gallery-' . $this->type . '-buttons';
                            $button_container_styles = ' sp-gallery-' . $this->type . '-button-container';
                            $button_styles = ' sp-gallery-' . $this->type . '-button';
                        } elseif ($this->type === 'block') {
                            $buttons_container_styles = ' wp-block-buttons  sp-gallery-' . $this->type . '-buttons';
                            $button_container_styles = ' wp-block-button sp-gallery-' . $this->type . '-button-container';
                            $button_styles = ' wp-block-button__link sp-gallery-' . $this->type . '-button';
                        } else {
                            $buttons_container_styles = ' sp-gallery-' . $this->type . '-buttons';
                            $button_container_styles = ' sp-gallery-' . $this->type . '-button-container';
                            $button_styles = ' sp-gallery-' . $this->type . '-button';
                        }

                    } else {
                        $gallery_type = '';
                        $item_type = '';
                        $buttons_container_styles = '';
                        $button_container_styles = '';
                        $button_styles = '';
                    }

                    if ($this->show_buttons == true) {
                    
                        $buttons = [];
                        
                        if ($show_like_icon) {
                            $buttons[] = [
                                'id' => 'sp-gallery-filter-liked-photos-' . intval($post_id),
                                'label' => 'Show Favourite Photos',
                            ];
                        }
                        
                        if (function_exists('sp_gallery_add_pro_buttons')) {
                            $buttons = array_merge($buttons, sp_gallery_add_pro_buttons());
                        }
        
                        if (!empty($buttons)) {
                            $output .= '<div class="sp-gallery-buttons' . esc_attr($buttons_container_styles) . ' ">';
                            foreach ($buttons as $button) {
                                $output .= '<div class="sp-gallery-button-container' . esc_attr($button_container_styles) . '"><button id="' . esc_attr($button['id']) . '" class="sp-gallery-button' . esc_attr($button_styles) . '">' . esc_html($button['label']) . '</button></div>';
                            }
                            $output .= '</div>';
                        }

                    }

                    if ($this->layout === 'grid') {
                        $output .= '<div id="sp-gallery-' . esc_attr($post_id) . '" class="sp-gallery sp-gallery-hidden sp-gallery-grid-gallery' . esc_attr($gallery_type) . '" 
							data-layout="' . esc_attr($this->layout) . '" 
							data-lightbox="' . esc_attr($this->use_lightbox) . '"
							data-gap="' . esc_attr($this->gap) . '"
							data-columns="' . esc_attr($this->columns_desktop) . '" 
							data-columns-tablet="' . esc_attr($this->columns_tablet) . '" 
							data-columns-mobile="' . esc_attr($this->columns_mobile) . '" 
							data-breakpoint-tablet="' . intval($this->breakpoint_tablet) . '" 
							data-breakpoint-mobile="' . intval($this->breakpoint_mobile) . '">';
                    } else {
                        $output .= '<div id="sp-gallery-' . esc_attr($post_id) . '" class="sp-gallery sp-gallery-hidden sp-gallery-masonry-gallery' . esc_attr($gallery_type) . '" 
							data-layout="' . esc_attr($this->layout) . '" 
							data-lightbox="' . esc_attr($this->use_lightbox) . '" 
							data-gap="' . esc_attr($this->gap) . '" 
							data-columns="' . esc_attr($this->columns_desktop) . '" 
							data-columns-tablet="' . esc_attr($this->columns_tablet) . '" 
							data-columns-mobile="' . esc_attr($this->columns_mobile) . '"
							data-breakpoint-tablet="' . intval($this->breakpoint_tablet) . '" 
							data-breakpoint-mobile="' . intval($this->breakpoint_mobile) . '">';
                    }

                    $item_classes = 'sp-gallery-item sp-gallery-' . $this->layout . '-item' . esc_attr($item_type);
        
                    foreach ($gallery_images as $image_id) {
                        
                        $image_data = wp_get_attachment_image_src($image_id, 'large');
                        if ($image_data) {
                            $image_url = $image_data[0];
                        }

                        $image_data = wp_get_attachment_image_src($image_id, '2048x2048');
                        if ($image_data) {
                            $lightbox_image_url = $image_data[0];
                        }
                        
                        $download_fullsize_image = get_post_meta($post_id, 'sp_gallery_download_fullsize_image', true);

                        if ($show_download_icon) {
                            if ($download_fullsize_image) {
                                $download_image_url = wp_get_original_image_url($image_id);
                            } else {
                                $download_image_url = wp_get_attachment_url($image_id);
                            }
                        } else {
                            $download_image_url = 'false';
                        }

                        $output .= '<div class="' . esc_attr($item_classes) . '">';
    
                        if ($this->use_lightbox) {
                            $output .= '<a class="sp-gallery-item-lightbox" data-src="' . esc_url($lightbox_image_url) . '" data-download-url="' . esc_url($download_image_url) . '" data-image-id="' . esc_attr($image_id) . '">';
                            $output .= '<img src="' . esc_url($image_url) . '" alt="" />';
                            $output .= '</a>';
                        } else {
                            $output .= '<img src="' . esc_url($image_url) . '" alt="" />';
                        }

                        $output .= $this->sp_gallery_get_image_icons_html($image_id, $post_id, $show_like_icon, $show_download_icon, $download_image_url);
    
                        $output .= '</div>';
                    }
    
                    $output .= '</div>';
    
                    return $output;
                }
            }
        }
    
        return $content;
    }

    /**
     * Checks if the Elementor plugin is loaded.
    *
    * @return bool True if Elementor is loaded, false otherwise.
    */

    public function sp_gallery_is_elementor()
    {
        
        return did_action('elementor/loaded');
    }

    /**
     * Checks if the current page is being edited in the Elementor page builder.
    *
    * @return bool True if the current page is being edited in Elementor, false otherwise.
    */
    
    public function sp_gallery_is_elementor_in_edit_mode()
    {
        
        if (did_action('elementor/loaded')) {
    
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                return true;
            }
        }
    
        return false;
    }

    /**
     * Enqueue the gallery CSS and any required JavaScript assets.
    *
    * This function is hooked to the 'wp_enqueue_scripts' action with a priority of 100.
    * This ensures that the inline CSS is added after all other styles.

    * Used in elementor edit mode
    */
    public function sp_gallery_generate_gallery_styles($gallery_id)
    {
        
        $style_output = '';
    
        if ($this->layout === 'masonry') {
            $style_output = $this->sp_gallery_generate_masonry_styles($gallery_id);
        } elseif ($this->layout === 'grid') {
            $style_output = $this->sp_gallery_generate_grid_styles($gallery_id);
        }

        return $style_output;
    }
    
    /**
    * Generates the CSS styles for the masonry layout of the gallery.
    *
    * This function constructs the CSS styles for the masonry layout of the gallery, taking into account the number of columns and the gap between items for desktop, tablet, and mobile devices.
    *
    * @return string The CSS styles for the masonry layout.
    */
    
    private function sp_gallery_generate_masonry_styles($gallery_id)
    {
        
        $style_desktop = 'width: calc((100% - (' . $this->columns_desktop . ' - 1) * ' . $this->gap . 'px) / ' . $this->columns_desktop . ')';
        $style_tablet = 'width: calc((100% - (' . $this->columns_tablet . ' - 1) * ' . $this->gap . 'px) / ' . $this->columns_tablet . ')';
        $style_mobile = 'width: calc((100% - (' . $this->columns_mobile . ' - 1) * ' . $this->gap . 'px) / ' . $this->columns_mobile . ')';
    
        return "
			/* Default for desktop */
			#sp-gallery-{$gallery_id}.sp-gallery-masonry-gallery .sp-gallery-item {
				margin-bottom: {$this->gap}px;
				{$style_desktop};
			}
	
			/* For tablets (max-width: {$this->breakpoint_tablet}px) */
			@media (max-width: {$this->breakpoint_tablet}px) {
				#sp-gallery-{$gallery_id}.sp-gallery-masonry-gallery .sp-gallery-item {
					{$style_tablet};
				}
			}
	
			/* For mobile (max-width: {$this->breakpoint_mobile}px) */
			@media (max-width: {$this->breakpoint_mobile}px) {
				#sp-gallery-{$gallery_id}.sp-gallery-masonry-gallery .sp-gallery-item {
					{$style_mobile};
				}
			}
		";
    }
    
    /**
     * Generates the CSS styles for the grid layout of the gallery.
    *
    * This function constructs the CSS styles for the grid layout of the gallery, taking into account the number of columns and the gap between items for desktop, tablet, and mobile devices.
    *
    * @return string The CSS styles for the grid layout.
    */
    private function sp_gallery_generate_grid_styles($gallery_id)
    {
        
        $style_desktop = 'grid-template-columns: repeat(' . esc_attr($this->columns_desktop) . ', 1fr); grid-gap: ' . esc_attr($this->gap) . 'px';
        $style_tablet = 'grid-template-columns: repeat(' . esc_attr($this->columns_tablet) . ', 1fr); grid-gap: ' . esc_attr($this->gap) . 'px';
        $style_mobile = 'grid-template-columns: repeat(' . esc_attr($this->columns_mobile) . ', 1fr); grid-gap: ' . esc_attr($this->gap) . 'px';
    
        return "
			/* Default for desktop */
			#sp-gallery-{$gallery_id}.sp-gallery-grid-gallery {
				display:grid;
				{$style_desktop};
			}
	
			/* For tablets (max-width: {$this->breakpoint_tablet}px) */
			@media (max-width: {$this->breakpoint_tablet}px) {
				#sp-gallery-{$gallery_id}.sp-gallery-grid-gallery {
					{$style_tablet};
				}
			}
	
			/* For mobile (max-width: {$this->breakpoint_mobile}px) */
			@media (max-width: {$this->breakpoint_mobile}px) {
				#sp-gallery-{$gallery_id}.sp-gallery-grid-gallery {
					{$style_mobile};
				}
			}
		";
    }

    /**
     * Generates the HTML for the image icons in the gallery.
     *
     * This function constructs the HTML for the image icons (like, download, etc.) that are displayed on each image in the gallery. The icons to be displayed are determined by the plugin settings.
     *
     * @param int $image_id The ID of the image.
     * @param int $post_id The ID of the post containing the image.
     * @param bool $show_like_icon Whether to show the like icon.
     * @param bool $show_download_icon Whether to show the download icon.
     * @param string $download_image_url The URL for downloading the image.
     * @return string The HTML for the image icons.
     */
    private function sp_gallery_get_image_icons_html($image_id, $post_id, $show_like_icon, $show_download_icon, $download_image_url)
    {

        $icons = [];

        if ($show_like_icon) {
            $icons['like'] = [
                'type' => 'toggle',
                'icon' => wp_kses(file_get_contents(plugin_dir_path(__FILE__) . '../includes/images/heart.svg'), [
                    'svg' => [
                        'xmlns' => true,
                        'width' => true,
                        'height' => true,
                        'viewbox' => true,
                        'fill' => true,
                    ],
                    'path' => [
                        'd' => true,
                        'fill' => true,
                        'fill-rule' => true,
                        'clip-rule' => true,
                        'stroke-width' => true,
                        'stroke-linecap' => true,
                        'stroke-linejoin' => true,
                        'stroke' => true,
                    ]
                ]),
                'class' => 'sp-gallery-like-icon',
                'action' => 'add_to_favorites',
                'aria-label' => 'Add to favorites',
            ];
        }

        if ($show_download_icon) {
            $icons['download'] = [
                'type' => 'download',
                'icon' => wp_kses(file_get_contents(plugin_dir_path(__FILE__) . '../includes/images/download.svg'), [
                    'svg' => [
                        'xmlns' => true,
                        'width' => true,
                        'height' => true,
                        'viewbox' => true,
                        'fill' => true,
                    ],
                    'path' => [
                        'd' => true,
                        'fill' => true,
                        'fill-rule' => true,
                        'clip-rule' => true,
                        'stroke-width' => true,
                        'stroke-linecap' => true,
                        'stroke-linejoin' => true,
                        'stroke' => true,
                    ]
                ]),
                'class' => 'sp-gallery-download-icon sp-gallery-image-icon',
                'action' => 'download_image',
                'aria-label' => 'Download Image',
                'url' => $download_image_url,
            ];
        }
    
        $icons = apply_filters('sp_gallery_image_icons', $icons, $image_id);

        if (!empty($icons)) {
    
            $output = '<div class="sp-gallery-image-icons">';
    
            foreach ($icons as $icon_key => $icon_data) {
                
                if ($icon_data['type'] === 'link' || $icon_data['type'] === 'download') {
                    $output .= '<a href="' . esc_url($icon_data['url']) . '" ';
                    if ($icon_data['type'] === 'download') {
                        $output .= 'download ';
                    }
                    $output .= 'class="' . esc_attr($icon_data['class']) . '" aria-label="' . esc_attr($icon_data['aria-label']) . '">';
                    $output .= $icon_data['icon'];
                    $output .= '</a>';
                } else {
                    
                    $output .= '<div class="' . esc_attr($icon_data['class']) . '" data-image-id="' . esc_attr($image_id) . '" aria-label="' . esc_attr($icon_data['aria-label']) . '">';
                    $output .= $icon_data['icon'];
                    $output .= '</div>';
                }
            }
        
            $output .= '</div>';
            return $output;
        
        }
    
    }

}
