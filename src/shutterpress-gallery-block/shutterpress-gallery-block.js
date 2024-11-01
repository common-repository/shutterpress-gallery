import { registerBlockType } from '@wordpress/blocks';
import { useState, useEffect } from '@wordpress/element';
import { SelectControl, ToggleControl, RangeControl, TextControl, PanelBody, Spinner } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import apiFetch from '@wordpress/api-fetch'; // Use WordPress built-in fetch
import metadata from './block.json';

// Register the block
registerBlockType(metadata.name, {
    

    attributes: {
        galleryId: {
            type: 'number',
            default: 0,
        },
        useLightbox: {
            type: 'boolean',
            default: true,
        },
        showDefaultButtons: {
            type: 'boolean',
            default: true,
        },
        galleryLayout: {
            type: 'string',
            default: 'masonry',
        },
        galleryGap: {
            type: 'number',
            default: 20,
        },
        columns_desktop: {
            type: 'number',
            default: 3,
        },
        columns_tablet: {
            type: 'number',
            default: 2,
        },
        columns_mobile: {
            type: 'number',
            default: 1,
        },
        defaults_set: {
            type: 'boolean',
            default: false,
        },
    },

    edit: function (props) {
        const { attributes, setAttributes } = props;
        const { galleryId, useLightbox, showDefaultButtons, galleryLayout, galleryGap, columns_desktop, columns_tablet, columns_mobile, defaults_set } = attributes;

        const [galleries, setGalleries] = useState([]);
        const [loading, setLoading] = useState(true);
        const [error, setError] = useState(null);

        const blockProps = useBlockProps();

        useEffect(() => {
            setLoading(true);
            apiFetch({ path: 'wp/v2/shutterpress-gallery' })
                .then(data => {
                    setGalleries(data);
                    setLoading(false);
                    
                    // Set the initial galleryId if not already set (for example, if it's 0)
                    if (galleryId === 0 && data.length > 0) {
                        setAttributes({ galleryId: data[0].id });  // Set to the first available gallery
                    }
                })
                .catch(error => {
                    setError(error.message);
                    setGalleries([]);
                    setLoading(false);
                });
            if (defaults_set === false) {
                apiFetch({ path: 'shutterpress/v1/options' })
                    .then(options => {
                        setAttributes({ useLightbox: options.sp_gallery_use_lightbox });
                        setAttributes({ galleryLayout: options.sp_gallery_layout });
                        setAttributes({ galleryGap: parseInt(options.sp_gallery_column_gap, 10)  });
                        setAttributes({ columns_desktop: parseInt(options.sp_gallery_columns_desktop, 10)  });
                        setAttributes({ columns_tablet: parseInt(options.sp_gallery_columns_tablet, 10)  });
                        setAttributes({ columns_mobile: parseInt(options.sp_gallery_columns_mobile, 10)  });
                        setAttributes({ defaults_set: true });  // Mark options as loaded
                    })
                    .catch(error => {
                        console.error('Error fetching options:', error);
                    });
            }
        }, [defaults_set]);

        const galleryOptions = !loading && galleries.length > 0
            ? galleries.map((gallery) => ({
                label: gallery.title.rendered || 'Untitled Gallery',
                value: gallery.id,
            }))
            : [{ label: loading ? 'Loading galleries...' : 'No galleries found', value: '' }];

        return (
            <div {...blockProps}>
                {loading && <Spinner />}
                <SelectControl 
                    label={__('Select Gallery')}
                    value={galleryId}
                    options={galleryOptions}
                    onChange={(newValue) => setAttributes({ galleryId: parseInt(newValue, 10) })} // Convert newValue to number
                    disabled={loading || error}
                />
                {error && <p style={{ color: 'red' }}>{__('Error: ', 'shutterpress-gallery')}{error}</p>}

                <InspectorControls>
                    <PanelBody title={__('Gallery Settings')} initialOpen={true}>
                        <ToggleControl
                            label={__('Use Lightbox')}
                            checked={useLightbox} 
                            onChange={(newValue) => setAttributes({ useLightbox: newValue ? true : false })}
                        />
                        <ToggleControl
                            label={__('Use Default Buttons')}
                            checked={showDefaultButtons}
                            onChange={(newValue) => setAttributes({ showDefaultButtons: newValue ? true : false })}
                        />
                        <p className="components-base-control__help">
                            {__(
                                `If you want to add custom buttons, create a button and set the button HTML ANCHOR to "sp-gallery-filter-liked-photos-${galleryId}". See `,
                                'shutterpress-gallery'
                            )}
                            <a href="https://shutterpress.io" target="_blank" rel="noopener noreferrer">
                                Shutterpress.io
                            </a>
                            {' for more info.'}
                        </p>
                        <SelectControl
                            label={__('Layout')}
                            value={galleryLayout}
                            options={[
                                { label: 'Masonry', value: 'masonry' },
                                { label: 'Grid', value: 'grid' },
                            ]}
                            onChange={(newValue) => setAttributes({ galleryLayout: newValue })}
                        />
                        <RangeControl
                            label={__('Image Gap')}
                            value={galleryGap}
                            onChange={(newValue) => setAttributes({ galleryGap: parseInt(newValue, 10)  })}
                            min={0}
                            max={100}
                        />
                        <SelectControl
                            label={__('Number of Desktop Columns')}
                            value={columns_desktop}
                            options={[
                                { label: '1', value: 1 },
                                { label: '2', value: 2 },
                                { label: '3', value: 3 },
                                { label: '4', value: 4 },
                                { label: '5', value: 5 },
                                { label: '6', value: 6 },
                                { label: '7', value: 7 },
                                { label: '8', value: 8 },
                                { label: '9', value: 9 },
                                { label: '10', value: 10 },
                            ]}
                            onChange={(newValue) => setAttributes({ columns_desktop: parseInt(newValue, 10) })}
                        />
                        <SelectControl
                            label={__('Number of Tablet Columns')}
                            value={columns_tablet}
                            options={[
                                { label: '1', value: 1 },
                                { label: '2', value: 2 },
                                { label: '3', value: 3 },
                                { label: '4', value: 4 },
                                { label: '5', value: 5 },
                                { label: '6', value: 6 },
                                { label: '7', value: 7 },
                                { label: '8', value: 8 },
                            ]}
                            onChange={(newValue) => setAttributes({ columns_tablet: parseInt(newValue, 10) })}
                        />
                        <SelectControl
                            label={__('Number of Mobile Columns')}
                            value={columns_mobile}
                            options={[
                                { label: '1', value: 1 },
                                { label: '2', value: 2 },
                                { label: '3', value: 3 },
                                { label: '4', value: 4 },
                            ]}
                            onChange={(newValue) => setAttributes({ columns_mobile: parseInt(newValue, 10) })}
                        />
                    </PanelBody>
                </InspectorControls>
            </div>
        );
    },

    save: function () {
        return null; // Dynamic block, nothing is saved
    },
});