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

class Shutterpress_Gallery_Admin_Settings extends Shutterpress_Gallery_Admin
{

    /**
     * Register the settings page
     *
     * @since    1.0.0
     */
    public function sp_gallery_register_settings_page()
    {
        add_submenu_page(
            'edit.php?post_type=shutterpress-gallery',
            esc_html__('Shutterpress Gallery Settings', 'shutterpress-gallery'),
            esc_html__('Settings', 'shutterpress-gallery'),
            'manage_options',
            'sp-gallery-settings',
            array( $this, 'sp_gallery_settings_page_html' ),
            2
        );
    }

    /**
     * Render the html for the settings pags
     *
     * @since    1.0.0
     */
    public function sp_gallery_settings_page_html()
    {
        
        if (! current_user_can('manage_options')) {
            return;
        }
    
        $tabs = [
            'general' => [
                'title' => esc_html__('General', 'shutterpress-gallery'),
                'callback' => [ $this, 'sp_gallery_render_general_tab_content' ]
            ],
        ];
    
        $tabs = apply_filters('sp_gallery_settings_tabs', $tabs);
    
        $active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'general';
    
        ?>
		<div class="wrap sp-gallery-settings-page">
			<h1><?php echo esc_html__('Shutterpress Gallery Settings', 'shutterpress-gallery'); ?></h1>
			<h2 class="nav-tab-wrapper">
				<?php foreach ($tabs as $id => $tab) : ?>
					<a href="<?php echo esc_url(add_query_arg('tab', esc_attr($id), admin_url('admin.php?page=sp-gallery-settings'))); ?>" class="nav-tab<?php echo $id === $active_tab ? ' nav-tab-active' : ''; ?>">
						<?php echo esc_html($tab['title']); ?>
					</a>
				<?php endforeach; ?>
			</h2>
	
			<?php foreach ($tabs as $id => $tab) : ?>
				<div id="<?php echo esc_attr($id); ?>" class="tab-content" style="<?php echo $id === $active_tab ? '' : 'display:none;'; ?>">
					<?php
                    
                    if (is_callable($tab['callback'])) {
                        call_user_func($tab['callback']);
                    }
			    ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
    }

    /**
     * Renders the settings fields for the Shutterpress Gallery plugin.
    *
    * This function is responsible for outputting the necessary hidden fields and nonce
    * for the settings form, ensuring proper form submission and security
    *
    * Replace the wordpress settings_fields() function with a custom implementation to return the tab name
    *
    * @param string $option_group The option group for the settings.
    * @param string $tab_name     The name of the current tab.
    */
    public function sp_gallery_settings_fields($option_group, $tab_name)
    {
        
        $tab_name = sanitize_key($tab_name);
    
        echo "<input type='hidden' name='option_page' value='" . esc_attr($option_group) . "' />";
    
        echo '<input type="hidden" name="action" value="update" />';
    
        wp_nonce_field("{$option_group}-options", '_wpnonce', false);
    
        $referer_url = esc_url(add_query_arg(
            array(
                'post_type' => 'shutterpress-gallery',
                'page'      => 'sp-gallery-settings',
                'tab'       => $tab_name,
            ),
            admin_url('edit.php')
        ));
    
        echo '<input type="hidden" name="_wp_http_referer" value="' . $referer_url . '" />';
    }

    /**
     * Renders the settings form for the Shutterpress Gallery plugin.
    *
    * This function is responsible for rendering the settings form, including the necessary
    * hidden fields, nonce, and submit button. It takes in parameters to customize the
    * form's behavior, such as the option group, settings section callback, and submit
    * button text.
    *
    * @param string $option_group     The option group for the settings.
    * @param string $section_callback The callback function to render the settings section.
    * @param string $submit_text      The text to display on the submit button.
    * @param string $tab_name         The name of the current tab.
    */
    public function sp_gallery_render_settings_form($option_group, $section_callback, $submit_text, $tab_name)
    {
        ?>
		<form action="options.php" method="post">
			<?php
            
            $this->sp_gallery_settings_fields($option_group, $tab_name);
            
        do_settings_sections($section_callback);
            
        submit_button($submit_text);
        ?>
		</form>
		<?php
    }
    
    /**
     * Renders the general settings tab content for the Shutterpress Gallery plugin.
    *
    * This function is responsible for rendering the settings form for the general tab
    * of the Shutterpress Gallery plugin. It calls the `sp_gallery_render_settings_form()`
    * method to generate the form, passing in the appropriate parameters for the general
    * tab.
    */
    public function sp_gallery_render_general_tab_content()
    {
        $this->sp_gallery_render_settings_form(
            'sp_gallery_options_group',
            'sp-gallery-settings',
            esc_html__('Save Settings', 'shutterpress-gallery'),
            'general'
        );
    }
    
    /**
     * Initializes the settings for the Shutterpress Gallery plugin.
    *
    * This function registers the various settings used by the plugin, including the
    * layout, lightbox, column, and breakpoint settings. It also adds a new settings
    * section to the WordPress admin settings page.
    *
    * @since 1.0.0
    */
    public function sp_gallery_settings_init()
    {
        
        register_setting('sp_gallery_options_group', 'sp_gallery_layout', 'sanitize_text_field');
        register_setting('sp_gallery_options_group', 'sp_gallery_use_lightbox', 'absint');
        register_setting('sp_gallery_options_group', 'sp_gallery_columns_desktop', 'absint');
        register_setting('sp_gallery_options_group', 'sp_gallery_columns_tablet', 'absint');
        register_setting('sp_gallery_options_group', 'sp_gallery_columns_mobile', 'absint');
        register_setting('sp_gallery_options_group', 'sp_gallery_column_gap', 'absint');
    
        register_setting('sp_gallery_options_group', 'sp_gallery_breakpoint_tablet', 'absint');
        register_setting('sp_gallery_options_group', 'sp_gallery_breakpoint_mobile', 'absint');

        register_setting('sp_gallery_options_group', 'sp_gallery_button_color', 'sanitize_hex_color');
    
        add_settings_section(
            'sp_gallery_settings_section',
            esc_html__('SP Plugin Settings Section', 'shutterpress-gallery'),
            array($this, 'sp_gallery_section_callback'),
            'sp-gallery-settings'
        );
    
        $this->sp_gallery_add_settings_fields();
    }

    /**
     * Adds the various settings fields for the Shutterpress Gallery plugin.
    *
    * This method is responsible for registering all the settings fields that are
    * used to configure the behavior of the Shutterpress Gallery plugin. It adds
    * fields for the gallery layout, lightbox, number of columns, column gap, and
    * breakpoints.
    *
    * @since 1.0.0
    */
    private function sp_gallery_add_settings_fields()
    {

        $this->sp_gallery_add_layout_option_field();
        
        $this->sp_gallery_add_lightbox_option_field();
        
        $this->sp_gallery_add_columns_field('desktop', esc_html__('Number of Columns for Desktop', 'shutterpress-gallery'));
        $this->sp_gallery_add_columns_field('tablet', esc_html__('Number of Columns for Tablet', 'shutterpress-gallery'));
        $this->sp_gallery_add_columns_field('mobile', esc_html__('Number of Columns for Mobile', 'shutterpress-gallery'));
        
        add_settings_field(
            'sp_gallery_column_gap',
            esc_html__('Column Gap (px)', 'shutterpress-gallery'),
            array($this, 'sp_gallery_column_gap_callback'),
            'sp-gallery-settings',
            'sp_gallery_settings_section'
        );
        
        $this->sp_gallery_add_breakpoint_field('tablet', esc_html__('Tablet Breakpoint (px)', 'shutterpress-gallery'), 768);
        $this->sp_gallery_add_breakpoint_field('mobile', esc_html__('Mobile Breakpoint (px)', 'shutterpress-gallery'), 480);

        add_settings_field(
            'sp_gallery_color_picker',
            esc_html__('Gallery Button Color', 'shutterpress-gallery'),
            array($this, 'sp_gallery_color_picker_callback'),
            'sp-gallery-settings',
            'sp_gallery_settings_section'
        );
    }

    /**
     * Adds fields for number of columns
     *
     * @since    1.0.0
     */
    private function sp_gallery_add_columns_field($device, $label)
    {
        
        $device = sanitize_key($device);
    
        add_settings_field(
            "sp_gallery_columns_{$device}",
            esc_html($label),
            array($this, "sp_gallery_columns_{$device}_field_callback"),
            'sp-gallery-settings',
            'sp_gallery_settings_section'
        );
    }

    /**
     * Adds fields to enter breakpoints
     *
     * @since    1.0.0
     */
    private function sp_gallery_add_breakpoint_field($device, $label, $default)
    {
        
        $device = sanitize_key($device);
    
        add_settings_field(
            "sp_gallery_breakpoint_{$device}",
            esc_html($label),
            array($this, "sp_gallery_breakpoint_{$device}_field_callback"),
            'sp-gallery-settings',
            'sp_gallery_settings_section'
        );
    }

    /**
     * Adds field to select grid or masonry
     *
     * @since    1.0.0
     */
    private function sp_gallery_add_layout_option_field()
    {
        
        add_settings_field(
            'sp_gallery_gallery_layout',
            esc_html__('Gallery Layout', 'shutterpress-gallery'),
            [$this, 'sp_gallery_layout_field_callback'],
            'sp-gallery-settings',
            'sp_gallery_settings_section'
        );
    }

    /**
     * Adds field to enable lightbox
     *
     * @since    1.0.0
     */
    private function sp_gallery_add_lightbox_option_field()
    {
        
        add_settings_field(
            'sp_gallery_use_lightbox',
            esc_html__('Use Lightbox', 'shutterpress-gallery'),
            [$this, 'sp_gallery_lightbox_field_callback'],
            'sp-gallery-settings',
            'sp_gallery_settings_section'
        );
    }

    /**
     * Helper function to render number of columns field for a device
     *
     * @since    1.0.0
     */
    private function sp_gallery_render_columns_field($device, $default)
    {
        
        $device_sanitized = sanitize_key($device);
        $columns = get_option("sp_gallery_columns_" . $device_sanitized, $default);
    
        $columns = is_numeric($columns) ? intval($columns) : $default;
        
        printf(
            '<input type="number" name="%s" value="%s" min="1" max="12">',
            esc_attr("sp_gallery_columns_{$device_sanitized}"),
            esc_attr($columns)
        );
    
        printf(
            '<p class="description">%s %s</p>',
            esc_html__('Specify the number of columns for', 'shutterpress-gallery'),
            esc_html($device_sanitized)
        );
    }
 
    /**
     * Helper function to render breakpoints field for a device
     *
     * @since    1.0.0
     */
    private function sp_gallery_render_breakpoint_field($device, $default)
    {
        
        $device_sanitized = sanitize_key($device);
        $breakpoint = get_option("sp_gallery_breakpoint_" . $device_sanitized, $default);
    
        $breakpoint = is_numeric($breakpoint) ? intval($breakpoint) : $default;
        if ($breakpoint < 320 || $breakpoint > 1920) {
            $breakpoint = $default;
        }
    
        printf(
            '<input type="number" name="%s" value="%s" min="320" max="1920">',
            esc_attr("sp_gallery_breakpoint_{$device_sanitized}"),
            esc_attr($breakpoint)
        );
    
        printf(
            '<p class="description">%s %s %s</p>',
            esc_html__('Define the breakpoint for', 'shutterpress-gallery'),
            esc_html($device_sanitized),
            esc_html__('(in pixels)', 'shutterpress-gallery')
        );
    }

    /**
     * Section callback for description
     *
     * @since    1.0.0
     */
    public function sp_gallery_section_callback()
    {
        esc_html_e('Customize gallery settings for the SP Plugin.', 'shutterpress-gallery');
    }

    /**
     * Field callback for "Number of Columns" based on device type
     *
     * @since    1.0.0
     */
    public function sp_gallery_columns_desktop_field_callback()
    {
        $this->sp_gallery_render_columns_field('desktop', 3);
    }

    /**
     * Field callback for the number of columns on tablet devices.
    *
    * @since 1.0.0
    */
    public function sp_gallery_columns_tablet_field_callback()
    {
        $this->sp_gallery_render_columns_field('tablet', 2);
    }

    /**
     * Field callback for the number of columns on mobile devices.
    *
    * @since 1.0.0
    */
    public function sp_gallery_columns_mobile_field_callback()
    {
        $this->sp_gallery_render_columns_field('mobile', 1);
    }

    /**
     * Field callback for breakpoints
     *
     * @since    1.0.0
     */
    public function sp_gallery_breakpoint_tablet_field_callback()
    {
        $this->sp_gallery_render_breakpoint_field('tablet', 768);
    }
    
    /**
     * Field callback for the breakpoint for mobile devices.
    *
    * @since 1.0.0
    */

    public function sp_gallery_breakpoint_mobile_field_callback()
    {
        $this->sp_gallery_render_breakpoint_field('mobile', 480);
    }

    /**
     * Field callback for gallery layout
     *
     * @since    1.0.0
     */
    public function sp_gallery_layout_field_callback()
    {
        
        $layout = get_option('sp_gallery_layout', 'grid');
        
        $layout = in_array($layout, ['grid', 'masonry']) ? $layout : 'grid';
    
        printf(
            '<label><input type="radio" name="%s" value="grid" %s> %s</label><br>',
            esc_attr('sp_gallery_layout'),
            checked($layout, 'grid', false),
            esc_html__('Grid', 'shutterpress-gallery')
        );
        
        printf(
            '<label><input type="radio" name="%s" value="masonry" %s> %s</label>',
            esc_attr('sp_gallery_layout'),
            checked($layout, 'masonry', false),
            esc_html__('Masonry', 'shutterpress-gallery')
        );
    }

    /**
     * Field callback for Column Gap
     *
     * @since    1.0.0
     */
    public function sp_gallery_column_gap_callback()
    {
        
        $gap = get_option('sp_gallery_column_gap', 10);
    
        $gap = absint($gap);
    
        printf(
            '<input type="number" name="%s" value="%s" min="0">',
            esc_attr('sp_gallery_column_gap'),
            esc_attr($gap)
        );
    
        printf(
            '<p class="description">%s</p>',
            esc_html__('Specify the gap between columns in pixels', 'shutterpress-gallery')
        );
    }

    /**
     * Field callback for lightbox option
     *
     * @since    1.0.0
     */
    public function sp_gallery_lightbox_field_callback()
    {
        
        $use_lightbox = get_option('sp_gallery_use_lightbox', true);
    
        $use_lightbox = boolval($use_lightbox);
    
        printf(
            '<label><input type="checkbox" name="%s" value="1" %s> %s</label>',
            esc_attr('sp_gallery_use_lightbox'),
            checked($use_lightbox, true, false),
            esc_html__('Enable lightbox for gallery images', 'shutterpress-gallery')
        );
    }

    public function sp_gallery_color_picker_callback()
    {
        
        $color = get_option('sp_gallery_button_color', '#EE2E4F');
    
        printf(
            '<input type="text" name="%s" value="%s" class="sp-gallery-color-picker" />',
            esc_attr('sp_gallery_button_color'),
            esc_attr($color)
        );
        
        printf(
            '<p class="description">%s</p>',
            esc_html__('Select a button color for the gallery.', 'shutterpress-gallery')
        );
    }

    /**
     * Add meta boxes to the settings page
     *
     * @since    1.0.0
     */
    public function sp_gallery_add_meta_boxes()
    {
        add_meta_box(
            'sp_gallery_meta_box',
            __('SP Meta Box', 'shutterpress-gallery'),
            array($this, 'sp_gallery_meta_box_callback'),
            'settings_page_sp-gallery-settings',
            'normal',
            'default'
        );
    }

    /**
     * Meta box content
     *
     * @since    1.0.0
     */
    public function sp_gallery_meta_box_callback()
    {
        
        $value = esc_attr(get_option('sp_gallery_meta_value', ''));
    
        printf(
            '<label for="%s">%s</label>',
            esc_attr('sp_gallery_meta_field'),
            esc_html__('Meta Field', 'shutterpress-gallery')
        );
    
        printf(
            '<input type="text" id="%s" name="%s" value="%s">',
            esc_attr('sp_gallery_meta_field'),
            esc_attr('sp_gallery_meta_value'),
            $value
        );
    }
}
