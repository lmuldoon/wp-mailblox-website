(function (wp) {
    const { registerBlockType } = wp.blocks;
    const { InnerBlocks, InspectorControls, MediaUpload, BlockControls } = wp.blockEditor;
    const { PanelBody, ColorPalette, ToggleControl, RangeControl, ToolbarGroup, ToolbarButton, Modal, TextControl, Button } = wp.components;
    const { useState } = wp.element;

    registerBlockType('email-builder/section', {
        title: 'Section',
        icon: 'table-col-after',
        category: 'email-builder',

        attributes: {
            backgroundColor:      { type: 'string', default: '' },
            darkBackgroundColor:  { type: 'string', default: '' },
            paddingTop:           { type: 'number', default: 0 },
            paddingBottom:        { type: 'number', default: 0 },
            mobilePaddingTop:     { type: 'number', default: null },
            mobilePaddingBottom:  { type: 'number', default: null },
            hideOnMobile:         { type: 'boolean', default: false },
            backgroundImageEnabled: { type: 'boolean', default: false },
            backgroundImageUrl: { type: 'string', default: '' },
            backgroundRepeat: { type: 'string', default: 'no-repeat' },
            backgroundPositionX: { type: 'string', default: 'center' },
            backgroundPositionY: { type: 'string', default: 'center' },
            backgroundSizeW: { type: 'string', default: 'cover' },
            backgroundSizeH: { type: 'string', default: 'auto' },
        },

        edit: ({ attributes, setAttributes, clientId }) => {
            const {
                backgroundColor,
                darkBackgroundColor,
                paddingTop,
                paddingBottom,
                backgroundImageEnabled,
                backgroundImageUrl,
                backgroundRepeat,
                backgroundPositionX,
                backgroundPositionY,
                backgroundSizeW,
                backgroundSizeH,
            } = attributes;

            const bgSizeVal = (backgroundSizeW === 'cover' || backgroundSizeW === 'contain')
                ? backgroundSizeW
                : backgroundSizeW + ' ' + backgroundSizeH;

            const [viewMode, setViewMode] = useState('desktop');
            const isMobilePreview = window.EBUseIsMobilePreview();

            const [showModuleModal, setShowModuleModal] = useState(false);
            const [moduleSaveName,  setModuleSaveName]  = useState('');
            const [savingModule,    setSavingModule]    = useState(false);
            const [moduleNotice,    setModuleNotice]    = useState('');

            const editorBgColor = (backgroundColor && backgroundColor !== 'transparent') ? backgroundColor : undefined;

            function snap(v) {
                return Math.min(50, Math.max(0, Math.round(Number(v || 0) / 5) * 5));
            }

            const snappedTop    = snap(paddingTop);
            const snappedBottom = snap(paddingBottom);
            const mobTopOverride    = attributes.mobilePaddingTop    !== null && attributes.mobilePaddingTop    !== undefined;
            const mobBottomOverride = attributes.mobilePaddingBottom !== null && attributes.mobilePaddingBottom !== undefined;

            const darkSwatches = [
                { color: '#000000', name: 'Pure Black' },
                { color: '#121212', name: 'Material Dark' },
                { color: '#1a1a1a', name: 'Soft Black' },
                { color: '#222222', name: 'Dark Grey' },
            ];

            return wp.element.createElement(
                    'div',
                    {
                        className: 'eb-section-wrapper',
                        style: {
                            backgroundColor: editorBgColor,
                            ...(backgroundImageEnabled && backgroundImageUrl ? {
                                backgroundImage: `url(${backgroundImageUrl})`,
                                backgroundRepeat: backgroundRepeat,
                                backgroundPosition: `${backgroundPositionX} ${backgroundPositionY}`,
                                backgroundSize: bgSizeVal,
                            } : {}),
                            paddingTop:    window.ebMobileVal(paddingTop, attributes.mobilePaddingTop, isMobilePreview) + 'px',
                            paddingBottom: window.ebMobileVal(paddingBottom, attributes.mobilePaddingBottom, isMobilePreview) + 'px',
                            minHeight: '50px',
                            opacity: (isMobilePreview && attributes.hideOnMobile) ? 0.3 : undefined,
                        }
                    },

                    // Toolbar — Save as Module
                    wp.element.createElement(
                        BlockControls,
                        {},
                        wp.element.createElement(
                            ToolbarGroup,
                            {},
                            wp.element.createElement(ToolbarButton, {
                                icon:  'saved',
                                label: 'Save as Module',
                                onClick: () => { setModuleSaveName(''); setModuleNotice(''); setShowModuleModal(true); },
                            })
                        )
                    ),

                    // Save as Module modal
                    showModuleModal && wp.element.createElement(
                        Modal,
                        {
                            title:           'Save as Module',
                            onRequestClose:  () => setShowModuleModal(false),
                            style:           { maxWidth: '400px' },
                        },
                        wp.element.createElement(TextControl, {
                            label:       'Module Name',
                            value:       moduleSaveName,
                            onChange:    setModuleSaveName,
                            placeholder: 'e.g. Footer, Promo Banner…',
                            autoFocus:   true,
                        }),
                        moduleNotice && wp.element.createElement(
                            'p',
                            { style: { color: moduleNotice.startsWith('✓') ? 'green' : '#d63638', fontSize: '13px', margin: '4px 0 8px' } },
                            moduleNotice
                        ),
                        wp.element.createElement(
                            'div',
                            { style: { display: 'flex', gap: '8px', marginTop: '16px', justifyContent: 'flex-end' } },
                            wp.element.createElement(Button, {
                                variant:  'tertiary',
                                onClick:  () => setShowModuleModal(false),
                                disabled: savingModule,
                            }, 'Cancel'),
                            wp.element.createElement(Button, {
                                variant:  'primary',
                                disabled: savingModule || !moduleSaveName.trim(),
                                onClick:  () => {
                                    const name = moduleSaveName.trim();
                                    if (!name) return;
                                    setSavingModule(true);
                                    setModuleNotice('');
                                    const block = wp.data.select('core/block-editor').getBlock(clientId);
                                    wp.apiFetch({
                                        path:   '/email-builder/v1/modules',
                                        method: 'POST',
                                        data:   { name, blocks: JSON.stringify([block]) },
                                    }).then(() => {
                                        setSavingModule(false);
                                        setModuleNotice('✓ Module saved successfully.');
                                    }).catch(() => {
                                        setSavingModule(false);
                                        setModuleNotice('Could not save module. Please try again.');
                                    });
                                },
                            }, savingModule ? 'Saving…' : 'Save Module')
                        )
                    ),

                    // Inspector Controls
                    wp.element.createElement(
                        InspectorControls,
                        {},
                        wp.element.createElement(
                            PanelBody,
                            { title: 'Style', initialOpen: true },

                            // Background Color
                            wp.element.createElement(
                                'div', { className: 'eb-field' },
                                wp.element.createElement('label', {}, 'Background Colour'),
                                wp.element.createElement(ColorPalette, {
                                    value: backgroundColor,
                                    onChange: (color) => setAttributes({ backgroundColor: color || '' })
                                }),
                                wp.element.createElement('p', { className: 'description' }, 'Background colour of this section.')
                            ),

                            // Background Image toggle and settings
                            wp.element.createElement(
                                'div', { className: 'eb-field' },
                                wp.element.createElement(ToggleControl, {
                                    label: 'Use Background Image',
                                    checked: backgroundImageEnabled,
                                    onChange: (value) => setAttributes({ backgroundImageEnabled: value })
                                }),
                            ),

                            backgroundImageEnabled && wp.element.createElement(
                                wp.element.Fragment,
                                {},
                                wp.element.createElement(MediaUpload, {
                                    onSelect: (media) => setAttributes({ backgroundImageUrl: media.url }),
                                    allowedTypes: ['image'],
                                    render: ({ open }) =>
                                        wp.element.createElement('button', { className: 'components-button is-secondary', onClick: open },
                                            backgroundImageUrl ? 'Replace Image' : 'Select Image'
                                        )
                                }),
                                backgroundImageUrl && wp.element.createElement(
                                    'div', { className: 'eb-bg-image-preview' },
                                    wp.element.createElement('img', {
                                        src:   backgroundImageUrl,
                                        alt:   '',
                                    })
                                ),
                                wp.element.createElement(window.EBButtonGroup, {
                                    label:   'Background Repeat',
                                    value:   backgroundRepeat,
                                    options: [
                                        { label: 'None',  value: 'no-repeat' },
                                        { label: 'Both',  value: 'repeat' },
                                        { label: 'X',     value: 'repeat-x' },
                                        { label: 'Y',     value: 'repeat-y' },
                                    ],
                                    onChange: (value) => setAttributes({ backgroundRepeat: value }),
                                }),
                                wp.element.createElement(window.EBButtonGroup, {
                                    label:   'Horizontal Position',
                                    value:   backgroundPositionX,
                                    options: [
                                        { label: 'Left',   value: 'left' },
                                        { label: 'Center', value: 'center' },
                                        { label: 'Right',  value: 'right' },
                                    ],
                                    onChange: (value) => setAttributes({ backgroundPositionX: value }),
                                }),
                                wp.element.createElement(window.EBButtonGroup, {
                                    label:   'Vertical Position',
                                    value:   backgroundPositionY,
                                    options: [
                                        { label: 'Top',    value: 'top' },
                                        { label: 'Center', value: 'center' },
                                        { label: 'Bottom', value: 'bottom' },
                                    ],
                                    onChange: (value) => setAttributes({ backgroundPositionY: value }),
                                }),
                                wp.element.createElement(window.EBButtonGroup, {
                                    label:   'Background Width',
                                    value:   backgroundSizeW,
                                    options: [
                                        { label: 'Cover',   value: 'cover' },
                                        { label: 'Contain', value: 'contain' },
                                        { label: 'Auto',    value: 'auto' },
                                        { label: '100%',    value: '100%' },
                                    ],
                                    onChange: (value) => setAttributes({ backgroundSizeW: value }),
                                }),
                                (backgroundSizeW !== 'cover' && backgroundSizeW !== 'contain') && wp.element.createElement(window.EBButtonGroup, {
                                    label:   'Background Height',
                                    value:   backgroundSizeH,
                                    options: [
                                        { label: 'Auto', value: 'auto' },
                                        { label: '100%', value: '100%' },
                                    ],
                                    onChange: (value) => setAttributes({ backgroundSizeH: value }),
                                })
                            ),

                            // Dark Mode Background
                            wp.element.createElement(
                                'div', { className: 'eb-field' },
                                wp.element.createElement('label', {}, 'Dark Mode Background'),
                                wp.element.createElement('p', { className: 'description' }, 'Leave empty to use the preset dark mode background.'),
                                wp.element.createElement('div', { style: { display: 'flex', gap: '6px', marginBottom: '8px', marginTop: '8px' } },
                                    ...darkSwatches.map(swatch =>
                                        wp.element.createElement('button', {
                                            type: 'button',
                                            title: swatch.name + ' ' + swatch.color,
                                            onClick: () => setAttributes({ darkBackgroundColor: swatch.color }),
                                            style: {
                                                backgroundColor: swatch.color,
                                                width: '28px',
                                                height: '28px',
                                                border: darkBackgroundColor === swatch.color ? '2px solid #2271b1' : '2px solid #ccc',
                                                borderRadius: '4px',
                                                cursor: 'pointer',
                                                padding: 0,
                                            }
                                        })
                                    )
                                ),
                                wp.element.createElement('p', { className: 'description', style: { fontSize: '11px' } },
                                    ...darkSwatches.map((swatch, i) =>
                                        wp.element.createElement('span', { key: i, style: { marginRight: '8px' } }, swatch.color + ' — ' + swatch.name)
                                    )
                                ),
                                darkBackgroundColor && wp.element.createElement('button', {
                                    type: 'button',
                                    className: 'components-button is-destructive is-small',
                                    style: { marginTop: '6px' },
                                    onClick: () => setAttributes({ darkBackgroundColor: '' })
                                }, 'Reset to Preset Default')
                            ),

                            // Padding
                            wp.element.createElement(window.EBResponsiveDivider),
                            wp.element.createElement(window.EBResponsiveToggle, {
                                value: viewMode,
                                onChange: setViewMode,
                            }),

                            viewMode === 'desktop' && wp.element.createElement(
                                wp.element.Fragment,
                                {},
                                wp.element.createElement(RangeControl, {
                                    label:    'Padding Top',
                                    value:    snappedTop,
                                    min:      0,
                                    max:      50,
                                    step:     5,
                                    onChange: (v) => setAttributes({ paddingTop: snap(v) }),
                                }),
                                wp.element.createElement(RangeControl, {
                                    label:    'Padding Bottom',
                                    value:    snappedBottom,
                                    min:      0,
                                    max:      50,
                                    step:     5,
                                    onChange: (v) => setAttributes({ paddingBottom: snap(v) }),
                                })
                            ),

                            viewMode === 'mobile' && wp.element.createElement(
                                wp.element.Fragment,
                                {},
                                wp.element.createElement(wp.components.ToggleControl, {
                                    label:    'Hide on Mobile',
                                    checked:  attributes.hideOnMobile,
                                    onChange: (v) => setAttributes({ hideOnMobile: v }),
                                }),

                                wp.element.createElement(
                                    'div', {},
                                    wp.element.createElement(wp.components.ToggleControl, {
                                        label:    'Override Padding Top',
                                        checked:  mobTopOverride,
                                        onChange: (v) => setAttributes({ mobilePaddingTop: v ? 0 : null }),
                                    }),
                                    mobTopOverride && wp.element.createElement(RangeControl, {
                                        value:    snap(attributes.mobilePaddingTop),
                                        min:      0,
                                        max:      50,
                                        step:     5,
                                        onChange: (v) => setAttributes({ mobilePaddingTop: snap(v) }),
                                    })
                                ),
                                wp.element.createElement(
                                    'div', {},
                                    wp.element.createElement(wp.components.ToggleControl, {
                                        label:    'Override Padding Bottom',
                                        checked:  mobBottomOverride,
                                        onChange: (v) => setAttributes({ mobilePaddingBottom: v ? 0 : null }),
                                    }),
                                    mobBottomOverride && wp.element.createElement(RangeControl, {
                                        value:    snap(attributes.mobilePaddingBottom),
                                        min:      0,
                                        max:      50,
                                        step:     5,
                                        onChange: (v) => setAttributes({ mobilePaddingBottom: snap(v) }),
                                    })
                                )
                            ),

                            // Padding & Border inputs
                            // ['Top', 'Bottom', 'Left', 'Right'].map((dir) =>
                            //     wp.element.createElement('div', { className: 'eb-field', key: dir },
                            //         wp.element.createElement('label', {}, wp.element.createElement('strong', {}, `Padding ${dir} (px)`)),
                            //         wp.element.createElement('input', {
                            //             type: 'number',
                            //             value: attributes[`padding${dir}`],
                            //             onChange: (e) => setAttributes({ [`padding${dir}`]: parseInt(e.target.value) || 0 }),
                            //             className: 'widefat'
                            //         })
                            //     )
                            // ),
                            // wp.element.createElement('div', { className: 'eb-field' },
                            //     wp.element.createElement('label', {}, 'Border Radius (px)'),
                            //     wp.element.createElement('input', {
                            //         type: 'number',
                            //         min: 0,
                            //         max: 50,
                            //         value: borderRadius,
                            //         onChange: (e) => setAttributes({ borderRadius: parseInt(e.target.value) || 0 }),
                            //         className: 'widefat'
                            //     })
                            // )
                        )
                    ),

                    // InnerBlocks
                    wp.element.createElement(InnerBlocks, {
                        allowedBlocks: ['email-builder/columns'],
                        template: [['email-builder/columns', { columns: 1 }]],
                        templateLock: false
                    })
                );
        },

        save: ({ attributes }) => {
            const {
                backgroundColor,
                paddingTop,
                paddingBottom,
                backgroundImageEnabled,
                backgroundImageUrl,
                backgroundRepeat,
                backgroundPositionX,
                backgroundPositionY
            } = attributes;

            return wp.element.createElement(
                'div',
                {
                    className: 'eb-section-wrapper',
                    style: {
                        backgroundColor:  backgroundColor || '#ffffff',
                        ...(backgroundImageEnabled && backgroundImageUrl ? {
                            backgroundImage:    `url(${backgroundImageUrl})`,
                            backgroundRepeat:   backgroundRepeat,
                            backgroundPosition: `${backgroundPositionX} ${backgroundPositionY}`,
                            backgroundSize:     'cover',
                        } : {}),
                        paddingTop:    (paddingTop    || 0) + 'px',
                        paddingBottom: (paddingBottom || 0) + 'px',
                    }
                },
                wp.element.createElement(InnerBlocks.Content)
            );
        }
    });
})(window.wp);