<?php

namespace Shutterpress\Gallery;

/**
 * Render callback for the dynamic block.
 */

$gallery_attributes = [
    'gallery_id' => isset($attributes['galleryId']) && intval($attributes['galleryId']) !== 0 ? intval($attributes['galleryId']) : '',
    'use_lightbox' => isset($attributes['useLightbox']) ? filter_var($attributes['useLightbox'], FILTER_VALIDATE_BOOLEAN) : filter_var(get_option('sp_gallery_use_lightbox'), FILTER_VALIDATE_BOOLEAN),
    'layout' => isset($attributes['galleryLayout']) ? sanitize_text_field($attributes['galleryLayout']) : sanitize_text_field(get_option('sp_gallery_layout')),
    'gap' => isset($attributes['galleryGap']) ? intval($attributes['galleryGap']) : intval(get_option('sp_gallery_column_gap')),
    'columns_desktop' => isset($attributes['columns_desktop']) ? intval($attributes['columns_desktop']) : intval(get_option('sp_gallery_columns_desktop')),
    'columns_tablet' => isset($attributes['columns_tablet']) ? intval($attributes['columns_tablet']) : intval(get_option('sp_gallery_columns_tablet')),
    'columns_mobile' => isset($attributes['columns_mobile']) ? intval($attributes['columns_mobile']) : intval(get_option('sp_gallery_columns_mobile')),
    'show_buttons' => isset($attributes['showDefaultButtons']) ? filter_var($attributes['showDefaultButtons'], FILTER_VALIDATE_BOOLEAN) : true,
    'type' => 'block',
];

$class_prefix = 'block_gallery_';
$gallery_id = isset($gallery_attributes['gallery_id']) ? intval($gallery_attributes['gallery_id']) : null;

${$class_prefix . $gallery_id} = new Shutterpress_Gallery_Public_Render();
${$class_prefix . $gallery_id}->sp_gallery_set_render_attributes($gallery_attributes);

$content='';

$output = '<div ' . get_block_wrapper_attributes() . '>';
$output .= ${$class_prefix . $gallery_id}->sp_gallery_get_the_gallery(esc_html($content));

$output .= '</div>';

echo($output);
