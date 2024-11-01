<?php

/**
 *
 * @link       https://shutterpress.io
 * @since      1.0.0
 *
 * @package    Shutterpress_Gallery
 * @subpackage Shutterpress_Gallery/includes
 */

/**
 *
 * @since      1.0.0
 * @package    Shutterpress_Gallery
 * @subpackage Shutterpress_Gallery/includes
 * @author     Shutterpress <info@shutterpress.io>
 */

namespace Elementor;

class Shutterpress_Gallery_Elementor_Widget extends Widget_Base
{

    public $gallery_attributes = array();
    public $gallery_id;
    public $use_lightbox;
    public $layout;
    public $gap;
    public $show_buttons;
    public $columns_desktop;
    public $columns_tablet;
    public $columns_mobile;
    public $breakpoint_tablet;
    public $breakpoint_mobile;
    
    public function get_name()
    {
        return 'shutterpress_gallery';
    }

    public function get_title()
    {
        return 'Shutterpress Gallery';
    }

    public function get_icon()
    {
        return 'eicon-gallery-grid';
    }

    public function get_categories()
    {
        return array('shutterpress');
    }

    public function get_keywords()
    {
        return array('gallery', 'masonry', 'lightbox','shutterpress',);
    }

    public function get_custom_help_url()
    {
        return 'https://shutterpress.io';
    }

    protected function get_upsale_data()
    {
        return [];
    }

    public function get_script_depends()
    {
        return [ 'shutterpress-gallery-elementor','imagesloaded', ];

    }

    public function get_style_depends()
    {
        return ['shutterpress-gallery-public','lightgallery-css','elementor-frontend' ];
    }

    protected function register_controls()
    {

        $this->start_controls_section(
            'sp_gallery_section',
            array(
                'label' => esc_html__('Gallery', 'shutterpress-gallery'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'sp_gallery_id',
            array(
                'label' => esc_html__('Gallery', 'shutterpress-gallery'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 0,
                'options' => array_map('esc_attr', $this->sp_gallery_get_galleries())
            )
        );

        $lightbox_default = get_option('sp_gallery_use_lightbox', true) ? 'yes' : 'no';

        $this->add_control(
            'sp_gallery_use_lightbox',
            array(
                'label' => esc_html__('Use Lightbox', 'shutterpress-gallery'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => in_array($lightbox_default, ['yes', 'no']) ? $lightbox_default : 'yes',
                'label_on' => esc_html__('Yes', 'shutterpress-gallery'),
                'label_off' => esc_html__('No', 'shutterpress-gallery'),
            )
        );

        $description = sprintf(
            wp_kses_post(__('If you want to add custom buttons, create a button and set the Button ID to "sp-gallery-filter-liked-photos-{{Gallery_id}}". See <a href="%s">Shutterpress.io</a> for more info.', 'shutterpress-gallery')),
            esc_url('https://shutterpress.io')
        );

        $this->add_control(
            'sp_gallery_show_default_buttons',
            array(
                'label' => esc_html__('Use Default Buttons', 'shutterpress-gallery'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'default' => in_array(get_option('sp_gallery_show_default_buttons', 'yes'), ['yes', 'no']) ? get_option('sp_gallery_show_default_buttons', 'yes') : 'yes',
                'label_on' => esc_html__('Yes', 'shutterpress-gallery'),
                'label_off' => esc_html__('No', 'shutterpress-gallery'),
                'description' => $description,
            )
        );

        $this->add_control(
            'sp_gallery_layout',
            array(
                'label' => esc_html__('Layout', 'shutterpress-gallery'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => sanitize_text_field(get_option('sp_gallery_layout', 'masonry')),
                'options' => array(
                    'masonry' => esc_html__('Masonry', 'shutterpress-gallery'),
                    'grid' => esc_html__('Grid', 'shutterpress-gallery'),
                ),
            )
        );

        $this->add_control(
            'sp_gallery_gap',
            array(
                'label' => esc_html__('Image Gap', 'shutterpress-gallery'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'default' => array(
                    'size' => absint(get_option('sp_gallery_column_gap', 20)),
                ),
                'range' => array(
                    'px' => array(
                        'min' => 0,
                        'max' => 100,
                    ),
                ),
                'size_units' => array('px'),
            )
        );

        $this->add_responsive_control(
            'sp_gallery_columns',
            array(
                'label' => esc_html__('Number of Columns', 'shutterpress-gallery'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'devices' => ['desktop', 'tablet', 'mobile'],
                'default' => intval(get_option('sp_gallery_columns_desktop', 3)),
                'options' => array(
                    '1' => esc_html__('1', 'shutterpress-gallery'),
                    '2' => esc_html__('2', 'shutterpress-gallery'),
                    '3' => esc_html__('3', 'shutterpress-gallery'),
                    '4' => esc_html__('4', 'shutterpress-gallery'),
                    '5' => esc_html__('5', 'shutterpress-gallery'),
                    '6' => esc_html__('6', 'shutterpress-gallery'),
                    '7' => esc_html__('7', 'shutterpress-gallery'),
                    '8' => esc_html__('8', 'shutterpress-gallery'),
                    '9' => esc_html__('9', 'shutterpress-gallery'),
                    '10' => esc_html__('10', 'shutterpress-gallery'),
                ),
            )
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $this->gallery_id = isset($settings['sp_gallery_id']) ? intval($settings['sp_gallery_id']) : 0;
    
        $use_lightbox = ($settings['sp_gallery_use_lightbox'] == 'yes');
        $show_buttons = ($settings['sp_gallery_show_default_buttons'] == 'yes');
    
        $elementor_breakpoints = \Elementor\Plugin::$instance->breakpoints->get_breakpoints_config();
        $elementor_mobile_breakpoint = isset($elementor_breakpoints['mobile']['value']) ? intval($elementor_breakpoints['mobile']['value']) : 480;
        $elementor_tablet_breakpoint = isset($elementor_breakpoints['tablet']['value']) ? intval($elementor_breakpoints['tablet']['value']) : 768;
    
        $this->use_lightbox = $use_lightbox;
        $this->layout = isset($settings['sp_gallery_layout']) ? esc_attr($settings['sp_gallery_layout']) : 'grid';
        $this->gap = isset($settings['sp_gallery_gap']['size']) ? intval($settings['sp_gallery_gap']['size']) : get_option('sp_gallery_column_gap', 20);
        $this->show_buttons = $show_buttons;
        
        $this->columns_desktop = isset($settings['sp_gallery_columns']) && !empty($settings['sp_gallery_columns'])
            ? intval($settings['sp_gallery_columns'])
            : get_option('sp_gallery_columns_desktop', 3);
    
        $this->columns_tablet = isset($settings['sp_gallery_columns_tablet']) && !empty($settings['sp_gallery_columns_tablet'])
            ? intval($settings['sp_gallery_columns_tablet'])
            : $this->columns_desktop;
    
        $this->columns_mobile = isset($settings['sp_gallery_columns_mobile']) && !empty($settings['sp_gallery_columns_mobile'])
            ? intval($settings['sp_gallery_columns_mobile'])
            : $this->columns_tablet;
    
        $this->breakpoint_tablet = $elementor_tablet_breakpoint;
        $this->breakpoint_mobile = $elementor_mobile_breakpoint;
    
        $this->gallery_attributes = array(
            'gallery_id'            => $this->gallery_id,
            'use_lightbox'          => $this->use_lightbox,
            'layout'                => $this->layout,
            'gap'                   => $this->gap,
            'show_buttons'          => $this->show_buttons,
            'columns_desktop'       => $this->columns_desktop,
            'columns_tablet'        => $this->columns_tablet,
            'columns_mobile'        => $this->columns_mobile,
            'breakpoint_tablet'     => $this->breakpoint_tablet,
            'breakpoint_mobile'     => $this->breakpoint_mobile,
            'type'                  => 'elementor',
        );
        
        $class_prefix = 'elementor_gallery_';
        $gallery_id = $this->gallery_id ? $this->gallery_id : null;
    
        ${$class_prefix . $gallery_id} = new \Shutterpress\Gallery\Shutterpress_Gallery_Public_Render();
        ${$class_prefix . $gallery_id}->sp_gallery_set_render_attributes($this->gallery_attributes);

        echo ${$class_prefix . $gallery_id}->sp_gallery_get_the_gallery('');
        if (${$class_prefix . $gallery_id}->sp_gallery_is_elementor_in_edit_mode()) {
            $style_output = ${$class_prefix . $gallery_id}->sp_gallery_generate_gallery_styles($this->gallery_id);
            echo '<style>' . esc_html($style_output) . '</style>';
        }
    }

    /**
     * Retrieves a list of all published Shutterpress Gallery posts.
     *
     * @return array An associative array of gallery post IDs and titles, where the key is the post ID and the value is the post title.
     */
    protected function sp_gallery_get_galleries()
    {
        
        $args = array(
            'post_type'      => 'shutterpress-gallery',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        );
    
        $posts = get_posts($args);
    
        $options = array(
            '0' => esc_html__('Select a gallery', 'shutterpress-gallery'),
        );
    
        if (!empty($posts)) {
            foreach ($posts as $post) {
                $options[intval($post->ID)] = esc_html($post->post_title);
            }
        } else {
            $options['0'] = esc_html__('No galleries found', 'shutterpress-gallery');
        }
    
        return $options;
    }

    /**
     * Retrieves an array of available image size options.
     *
     * The returned array includes the 'full' image size as well as all the intermediate image sizes defined in WordPress.
     *
     * @return array An associative array of image size options, where the key is the size name and the value is the human-readable size name.
     */

    private function sp_gallery_get_image_size_options()
    {
        
        $image_sizes = get_intermediate_image_sizes();
        $options = array();
    
        $options['full'] = esc_html__('Full', 'shutterpress-gallery');
    
        foreach ($image_sizes as $size) {
            $options[$size] = esc_html(ucfirst($size));
        }
    
        return $options;
    }

    /**
     * Checks if the current page is in Elementor's edit mode.
     *
     * @return bool True if the current page is in Elementor's edit mode, false otherwise.
     */

    public function sp_gallery_is_elementor()
    {
        
        return did_action('elementor/loaded');
    }

    public function sp_gallery_is_elementor_in_edit_mode()
    {
        
        if (did_action('elementor/loaded')) {
    
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                return true;
            }
        }
    
        return false;
    }

}
