(function (wp) {
    const {
        registerBlockType
    } = wp.blocks;
    const {
        InspectorControls,
        MediaUpload,
        MediaUploadCheck
    } = wp.blockEditor;
    const {
        PanelBody,
        ToggleControl,
        Button,
        TextControl
    } = wp.components;
    const { useState } = wp.element;

    registerBlockType('email-builder/logo', {
        title: 'Logo',
        icon: 'format-image',
        category: 'email-builder',
        parent: ['email-builder/column'],

        attributes: {
            // Override image — if empty falls back to preset
            url: {
                type: 'string',
                default: ''
            },
            alt: {
                type: 'string',
                default: ''
            },
            width: {
                type: 'number',
                default: 200
            },
            align: {
                type: 'string',
                default: 'center'
            },
            linkUrl: {
                type: 'string',
                default: ''
            },
            darkLogoEnabled: {
                type: 'boolean',
                default: false
            },
            darkUrl: {
                type: 'string',
                default: ''
            },
            paddingTop:          { type: 'number', default: 0 },
            paddingBottom:       { type: 'number', default: 0 },
            paddingLeft:         { type: 'number', default: 0 },
            paddingRight:        { type: 'number', default: 0 },
            mobileAlign:         { type: 'string', default: '' },
            mobilePaddingTop:    { type: 'number', default: null },
            mobilePaddingBottom: { type: 'number', default: null },
            mobilePaddingLeft:   { type: 'number', default: null },
            mobilePaddingRight:  { type: 'number', default: null },
            hideOnMobile:        { type: 'boolean', default: false },
        },

        edit: function (props) {
            const {
                attributes,
                setAttributes
            } = props;
            const [viewMode, setViewMode] = useState('desktop');

            // Try to get preset logo from localized data
            const presetLogoUrl = (
                window.EB_EDITOR_DATA &&
                window.EB_EDITOR_DATA.preset_logo
            ) || '';

            const presetDarkLogoUrl = (
                window.EB_EDITOR_DATA &&
                window.EB_EDITOR_DATA.preset ?.preset_logo_dark_url
            ) || '';

            const presetDarkEnabled = !!(
                window.EB_EDITOR_DATA &&
                window.EB_EDITOR_DATA.preset ?.preset_logo_dark_enabled
            );

            const displayLight = attributes.url || presetLogoUrl;

            const darkActive = attributes.darkLogoEnabled || presetDarkEnabled;

            const displayDark = attributes.darkUrl || presetDarkLogoUrl;

            function onSelectImage(media) {
                setAttributes({
                    url: media.url,
                    alt: media.alt || '',
                });
            }

            function onRemoveImage() {
                setAttributes({
                    url: '',
                    alt: ''
                });
            }

            const isMobilePreview = window.EBUseIsMobilePreview();
            const effectiveAlign  = window.ebMobileVal(attributes.align, attributes.mobileAlign, isMobilePreview);

            return wp.element.createElement(
                'div', {
                    className: 'eb-logo-block',
                    style: {
                        textAlign: effectiveAlign,
                        opacity: (isMobilePreview && attributes.hideOnMobile) ? 0.3 : undefined,
                    }
                },

                wp.element.createElement(
                    InspectorControls, {},
                    wp.element.createElement(
                        PanelBody, {
                            title: 'Settings',
                            initialOpen: true
                        },

                        // Image override
                        wp.element.createElement(
                            'div', { className: 'eb-field' },
                            wp.element.createElement('label', {}, 'Logo Image Override'),
                            wp.element.createElement(
                                'p', {
                                    className: 'description',
                                    style: { marginBottom: '8px' }
                                },
                                attributes.url ?
                                'Using custom image. Remove to use preset logo.' :
                                presetLogoUrl ?
                                'Using logo from preset. Upload to override.' :
                                'No logo set in preset. Upload an image.'
                            ),
                            wp.element.createElement(
                                MediaUploadCheck, {},
                                wp.element.createElement(MediaUpload, {
                                    onSelect: onSelectImage,
                                    allowedTypes: ['image/png', 'image/gif', 'image/svg+xml'],
                                    render: ({ open }) =>
                                        wp.element.createElement(
                                            Button, {
                                                onClick: open,
                                                className: 'components-button is-secondary is-small',
                                            },
                                            attributes.url ? 'Replace Image' : 'Upload Image'
                                        )
                                })
                            ),
                            attributes.url && wp.element.createElement(
                                Button, {
                                    isDestructive: true,
                                    isSmall: true,
                                    onClick: onRemoveImage,
                                    style: { marginTop: '6px', display: 'block' },
                                },
                                'Remove Override'
                            )
                        ),

                        // Alt text field + warning
                        (attributes.url || presetLogoUrl) && wp.element.createElement(
                            'div',
                            { className: 'eb-field' },
                            wp.element.createElement(TextControl, {
                                label:       'Alt Text',
                                value:       attributes.alt,
                                onChange:    (value) => setAttributes({ alt: value }),
                                placeholder: 'e.g. Company Name',
                                help:        attributes.url ? undefined : 'Applies to the preset logo.',
                            }),
                            !attributes.alt && wp.element.createElement(
                                'p',
                                { style: { color: '#cc1818', fontSize: '12px', marginTop: '-8px' } },
                                '⚠ Alt text is missing.'
                            )
                        ),

                        // Dark mode toggle
                        wp.element.createElement(ToggleControl, {
                            label:    'Override Dark Mode Logo',
                            help:     'Enable to override the preset dark logo for this block.',
                            checked:  attributes.darkLogoEnabled,
                            onChange: (val) => setAttributes({ darkLogoEnabled: val }),
                        }),

                        // Dark logo upload (only if enabled)
                        attributes.darkLogoEnabled && wp.element.createElement(
                            'div', { className: 'eb-field' },
                            wp.element.createElement('label', {}, 'Dark Mode Logo Override'),
                            wp.element.createElement(
                                'p', {
                                    className: 'description',
                                    style: { marginBottom: '8px' }
                                },
                                attributes.darkUrl ?
                                'Using custom dark logo.' :
                                presetDarkLogoUrl ?
                                'Using dark logo from preset. Upload to override.' :
                                'No dark logo set in preset.'
                            ),
                            wp.element.createElement(
                                MediaUploadCheck, {},
                                wp.element.createElement(MediaUpload, {
                                    onSelect: (media) => setAttributes({ darkUrl: media.url }),
                                    allowedTypes: ['image/png', 'image/gif', 'image/svg+xml'],
                                    render: ({ open }) =>
                                        wp.element.createElement(
                                            Button, {
                                                onClick: open,
                                                className: 'components-button is-secondary is-small',
                                            },
                                            attributes.darkUrl ? 'Replace Dark Logo' : 'Upload Dark Logo'
                                        )
                                })
                            ),
                            attributes.darkUrl && wp.element.createElement(
                                Button, {
                                    isDestructive: true,
                                    isSmall: true,
                                    onClick: () => setAttributes({ darkUrl: '' }),
                                    style: { marginTop: '6px', display: 'block' },
                                },
                                'Remove Dark Override'
                            )
                        ),

                        // Link URL
                        wp.element.createElement(
                            'div', { className: 'eb-field' },
                            wp.element.createElement(TextControl, {
                                label: 'Link URL',
                                value: attributes.linkUrl,
                                onChange: (value) => setAttributes({ linkUrl: value }),
                                placeholder: 'https://yoursite.com',
                                help: 'Optional — wraps the logo in a link.',
                            })
                        )
                    ),

                    wp.element.createElement(
                        PanelBody, {
                            title: 'Style',
                            initialOpen: false
                        },

                        // Width
                        wp.element.createElement(
                            'div', { className: 'eb-field' },
                            wp.element.createElement('label', {}, 'Width (px)'),
                            wp.element.createElement('input', {
                                type: 'number',
                                min: 50,
                                max: 600,
                                value: attributes.width,
                                onChange: (e) => setAttributes({ width: parseInt(e.target.value) || 200 }),
                                className: 'widefat',
                            }),
                            wp.element.createElement(
                                'p', { className: 'description' },
                                'Display width of the logo in pixels.'
                            )
                        ),

                        // Alignment with desktop/mobile toggle
                        wp.element.createElement(window.EBResponsiveDivider),
                        wp.element.createElement(window.EBResponsiveToggle, {
                            value: viewMode,
                            onChange: setViewMode,
                        }),

                        viewMode === 'desktop' && wp.element.createElement(
                            wp.element.Fragment,
                            {},
                            wp.element.createElement(window.EBAlignControl, {
                                attrKey: 'align',
                                attributes,
                                setAttributes,
                                isMobile: false,
                            }),
                            wp.element.createElement(window.EBPaddingFields, {
                                prefix:   'padding',
                                attributes,
                                setAttributes,
                                isMobile: false,
                            })
                        ),

                        viewMode === 'mobile' && wp.element.createElement(
                            wp.element.Fragment,
                            {},
                            wp.element.createElement(
                                'p', { className: 'description', style: { marginBottom: '8px' } },
                                'Leave blank to inherit desktop setting.'
                            ),
                            wp.element.createElement(window.EBAlignControl, {
                                attrKey: 'mobileAlign',
                                attributes,
                                setAttributes,
                                isMobile: true,
                            }),
                            wp.element.createElement(window.EBPaddingFields, {
                                prefix:   'mobilePadding',
                                attributes,
                                setAttributes,
                                isMobile: true,
                            }),
                            wp.element.createElement(ToggleControl, {
                                label:    'Hide on Mobile',
                                checked:  attributes.hideOnMobile,
                                onChange: (val) => setAttributes({ hideOnMobile: val }),
                            })
                        )
                    )
                ),

                // Editor preview
                // Editor preview
                displayLight ?
                wp.element.createElement(
                    'div', {},

                    // Light preview
                    wp.element.createElement(
                        'div', {
                            style: {
                                marginBottom: '6px'
                            }
                        },
                        wp.element.createElement('img', {
                            src: displayLight,
                            alt: attributes.alt || 'Logo',
                            style: {
                                width: attributes.width + 'px',
                                maxWidth: '100%',
                                height: 'auto',
                                display: 'inline-block',
                            }
                        })
                    ),

                    // Dark preview (if exists)
                    // (darkActive && displayDark) && wp.element.createElement(
                    //     'div', {
                    //         style: {
                    //             background: '#121212',
                    //             padding: '10px',
                    //             display: 'inline-block'
                    //         }
                    //     },
                    //     wp.element.createElement('img', {
                    //         src: displayDark,
                    //         alt: 'Dark Logo',
                    //         style: {
                    //             width: attributes.width + 'px',
                    //             maxWidth: '100%',
                    //             height: 'auto',
                    //             display: 'inline-block',
                    //         }
                    //     })
                    // )
                ) :
                wp.element.createElement(
                    'div', {
                        style: {
                            width: attributes.width + 'px',
                            height: '60px',
                            background: '#f0f0f0',
                            border: '2px dashed #ccc',
                            display: 'inline-flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                            color: '#999',
                            fontSize: '12px',
                            maxWidth: '100%',
                        }
                    },
                    'No logo set'
                )
            );
        },

        save: function () {
            return null;
        }
    });

})(window.wp);