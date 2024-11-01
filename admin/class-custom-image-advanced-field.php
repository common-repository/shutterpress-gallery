<?php

if (! defined('WPINC')) {
    die;
}

class RWMB_Custom_Image_Advanced_Field extends RWMB_Image_Advanced_Field
{

    /**
     * Enqueue the necessary scripts for the Custom Image Advanced field.
     *
     * This method extends the parent class's `admin_enqueue_scripts()` method and
     * also enqueues the `rwmb-media` and `rwmb` scripts, as well as a custom
     * `shutterpress-gallery-image-advanced.js` script.
     */
    public static function admin_enqueue_scripts()
    {
        parent::admin_enqueue_scripts();
        RWMB_Image_Field::admin_enqueue_scripts();
        wp_enqueue_script('shutterpress-gallery-image-advanced', plugin_dir_url(__FILE__) . 'js/shutterpress-gallery-image-advanced.js', array( 'jquery','rwmb-media', 'rwmb' ), RWMB_VER, true);
    }

    /**
     * Saves the custom image advanced field data.
     *
     * This method is responsible for sanitizing and updating the post meta with the
     * array of image IDs for the custom image advanced field.
     *
     * @param mixed  $new     The new value of the field.
     * @param mixed  $old     The old value of the field.
     * @param int    $post_id The ID of the post being saved.
     * @param array  $field   The field configuration array.
     */
    public static function save($new, $old, $post_id, $field)
    {
        
        $new_flat = is_array($new) ? array_values(array_filter($new)) : (array) $new;
    
        $image_ids = array_map('intval', $new_flat);
    
        $image_ids = array_filter($image_ids, function ($id) {
            return wp_attachment_is_image($id);
        });
    
        $meta_field_id = sanitize_key($field['id']);
    
        if (empty($image_ids)) {
            delete_post_meta($post_id, $meta_field_id);
        } else {
            
            update_post_meta($post_id, $meta_field_id, $image_ids);
        }
    }

    /**
     * Gets the attributes for the custom image advanced field.
     *
     * This method is responsible for preparing the attributes for the custom image
     * advanced field, including setting the type, name, ID, value, and class.
     * It also adds attachment details to the attributes.
     *
     * @param array $field The field configuration array.
     * @param mixed $value The field value.
     * @return array The prepared attributes for the field.
     */
    public static function get_attributes($field, $value = null)
    {
        $value = (array) $value;

        if (isset($value[0]) && is_array($value[0])) {
            
            $value = $value[0];
        }

        $attributes           = parent::get_attributes($field, $value);
        $attributes['type']   = 'hidden';
        $attributes['name']   = $field['clone'] ? str_replace('[]', '', $attributes['name']) : $attributes['name'];
        $attributes['id']     = false;
        $attributes['value']  = implode(',', $value);
        $attributes['class'] .= ' rwmb-media';

        $attachments                    = array_values(array_filter(array_map('wp_prepare_attachment_for_js', $value)));
        $attributes['data-attachments'] = wp_json_encode($attachments);

        if (empty($attachments)) {
            unset($attributes['value']);
        }
        return $attributes;
    }

}
