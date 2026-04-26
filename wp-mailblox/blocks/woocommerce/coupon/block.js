(function (wp) {

    const { registerBlockType } = wp.blocks;
    const { InspectorControls }  = wp.blockEditor;
    const { PanelBody, ColorPalette } = wp.components;
    const { useState } = wp.element;
    const { useSelect } = wp.data;

    registerBlockType('email-builder/wc-coupon', {
        title:    'Coupon Code',
        icon:     'tickets-alt',
        category: 'email-builder-woocommerce',
        parent:   ['email-builder/column'],

        attributes: {
            darkBackgroundColor:     { type: 'string',  default: '' },
            darkCodeBackgroundColor: { type: 'string',  default: '' },
            title:               { type: 'string',  default: 'Your Exclusive Discount' },
            description:         { type: 'string',  default: 'Use this code at checkout' },
            code:                { type: 'string',  default: 'SAVE20' },
            expiryText:          { type: 'string',  default: '' },
            backgroundColor:     { type: 'string',  default: '' },
            borderColor:         { type: 'string',  default: '' },
            borderStyle:         { type: 'string',  default: 'dashed' },
            borderWidth:         { type: 'number',  default: 2 },
            borderRadius:        { type: 'number',  default: 4 },
            mobileBorderRadius:  { type: 'number',  default: null },
            mobileBorderWidth:   { type: 'number',  default: null },
            mobileBorderColor:   { type: 'string',  default: '' },
            codeBackgroundColor: { type: 'string',  default: '' },
            codeColor:           { type: 'string',  default: '' },
            textColor:           { type: 'string',  default: '' },
            align:               { type: 'string',  default: 'center' },
            mobileAlign:         { type: 'string',  default: '' },
            paddingTop:          { type: 'number',  default: 0 },
            paddingBottom:       { type: 'number',  default: 0 },
            paddingLeft:         { type: 'number',  default: 0 },
            paddingRight:        { type: 'number',  default: 0 },
            mobilePaddingTop:    { type: 'number',  default: null },
            mobilePaddingBottom: { type: 'number',  default: null },
            mobilePaddingLeft:   { type: 'number',  default: null },
            mobilePaddingRight:  { type: 'number',  default: null },
        },

        edit: function (props) {
            const { attributes, setAttributes, clientId } = props;
            const {
                title, description, code, expiryText,
                backgroundColor, borderColor, borderStyle, borderWidth,
                codeBackgroundColor, codeColor, textColor, align,
                darkBackgroundColor, darkCodeBackgroundColor,
            } = attributes;

            const autoColor = useSelect(function(select) {
                var store   = select('core/block-editor');
                var parents = store.getBlockParents(clientId);
                for (var i = parents.length - 1; i >= 0; i--) {
                    var block = store.getBlock(parents[i]);
                    if (!block) continue;
                    if (block.name === 'email-builder/column') {
                        var colBg = block.attributes.backgroundColor;
                        if (colBg && colBg !== 'transparent') return window.ebContrastColor(colBg);
                    }
                    if (block.name === 'email-builder/columns') {
                        var colBg = block.attributes.backgroundColor;
                        if (colBg && colBg !== 'transparent') return window.ebContrastColor(colBg);
                    }
                    if (block.name === 'email-builder/section') {
                        var secBg = block.attributes.backgroundColor;
                        var preset = (window.EB_EDITOR_DATA && window.EB_EDITOR_DATA.preset) || {};
                        var bg = (secBg && secBg !== 'transparent') ? secBg : (preset.bg_color || '#ffffff');
                        return window.ebContrastColor(bg);
                    }
                }
                var preset = (window.EB_EDITOR_DATA && window.EB_EDITOR_DATA.preset) || {};
                return window.ebContrastColor(preset.bg_color || '#ffffff');
            }, [clientId]);

            var _preset       = (window.EB_EDITOR_DATA && window.EB_EDITOR_DATA.preset) || {};
            var bodyFontStack = _preset.body_font_stack     || 'Helvetica, Arial, sans-serif';
            var bodyFontSize  = (_preset.body_size || 16)   + 'px';
            var subFontStack  = _preset.subheading_font_stack  || bodyFontStack;
            var subFontSize   = (_preset.subheading_size   || 24) + 'px';
            var subFontWeight = _preset.subheading_font_weight || 400;

            const darkSwatches = [
                { color: '#000000', name: 'Pure Black' },
                { color: '#121212', name: 'Material Dark' },
                { color: '#1a1a1a', name: 'Soft Black' },
                { color: '#222222', name: 'Dark Grey' },
            ];

            const [viewMode, setViewMode] = useState('desktop');

            return wp.element.createElement(
                'div',
                { className: 'eb-wc-coupon-block' },

                wp.element.createElement(
                    InspectorControls,
                    {},
                    wp.element.createElement(
                        PanelBody,
                        { title: 'Coupon Content', initialOpen: true },

                        wp.element.createElement(
                            'div',
                            { className: 'eb-field' },
                            wp.element.createElement('label', {}, 'Title'),
                            wp.element.createElement('input', {
                                type:      'text',
                                value:     title,
                                onChange:  (e) => setAttributes({ title: e.target.value }),
                                className: 'widefat',
                            })
                        ),

                        wp.element.createElement(
                            'div',
                            { className: 'eb-field' },
                            wp.element.createElement('label', {}, 'Description'),
                            wp.element.createElement('input', {
                                type:      'text',
                                value:     description,
                                onChange:  (e) => setAttributes({ description: e.target.value }),
                                className: 'widefat',
                            })
                        ),

                        wp.element.createElement(
                            'div',
                            { className: 'eb-field' },
                            wp.element.createElement('label', {}, 'Coupon Code'),
                            wp.element.createElement('input', {
                                type:      'text',
                                value:     code,
                                onChange:  (e) => setAttributes({ code: e.target.value }),
                                className: 'widefat',
                            })
                        ),

                        wp.element.createElement(
                            'div',
                            { className: 'eb-field' },
                            wp.element.createElement('label', {}, 'Expiry Text (optional)'),
                            wp.element.createElement('input', {
                                type:        'text',
                                value:       expiryText,
                                onChange:    (e) => setAttributes({ expiryText: e.target.value }),
                                placeholder: 'e.g. Expires 31 Dec 2025',
                                className:   'widefat',
                            })
                        )
                    ),

                    wp.element.createElement(
                        PanelBody,
                        { title: 'Style', initialOpen: false },

                        wp.element.createElement(window.EBResponsiveDivider),
                        wp.element.createElement(window.EBResponsiveToggle, {
                            value: viewMode,
                            onChange: setViewMode,
                        }),

                        viewMode === 'desktop' && wp.element.createElement(
                            wp.element.Fragment,
                            {},
                            wp.element.createElement(window.EBAlignControl, {
                                attrKey:       'align',
                                attributes:    attributes,
                                setAttributes: setAttributes,
                                isMobile:      false,
                            }),
                            wp.element.createElement(window.EBPaddingFields, {
                                prefix:        'padding',
                                attributes:    attributes,
                                setAttributes: setAttributes,
                                isMobile:      false,
                            }),
                            wp.element.createElement(
                                'div',
                                { className: 'eb-field' },
                                wp.element.createElement('label', {}, 'Code Border Colour'),
                                wp.element.createElement('p', { className: 'description' }, 'Leave empty for no border on the code box.'),
                                wp.element.createElement(ColorPalette, {
                                    value:    borderColor,
                                    onChange: (color) => setAttributes({ borderColor: color || '' }),
                                    clearable: true,
                                })
                            ),
                            borderColor && wp.element.createElement(
                                wp.element.Fragment, {},
                                wp.element.createElement(
                                    'div',
                                    { className: 'eb-field' },
                                    wp.element.createElement('label', {}, 'Border Style'),
                                    wp.element.createElement(window.EBButtonGroup, {
                                        value:    borderStyle,
                                        onChange: (val) => setAttributes({ borderStyle: val }),
                                        options:  [
                                            { label: 'Solid',  value: 'solid'  },
                                            { label: 'Dashed', value: 'dashed' },
                                            { label: 'Dotted', value: 'dotted' },
                                        ],
                                    })
                                ),
                                wp.element.createElement(
                                    'div',
                                    { className: 'eb-field' },
                                    wp.element.createElement('label', {}, 'Border Width (px)'),
                                    wp.element.createElement('input', {
                                        type:      'number',
                                        min:       1,
                                        max:       10,
                                        value:     borderWidth,
                                        onChange:  (e) => setAttributes({ borderWidth: parseInt(e.target.value) || 1 }),
                                        className: 'widefat',
                                    })
                                )
                            ),
                            wp.element.createElement(
                                'div',
                                { className: 'eb-field' },
                                wp.element.createElement('label', {}, 'Code Box Border Radius (px)'),
                                wp.element.createElement('input', {
                                    type:      'number',
                                    min:       0,
                                    max:       50,
                                    value:     attributes.borderRadius !== undefined ? attributes.borderRadius : 4,
                                    onChange:  (e) => setAttributes({ borderRadius: Math.max(0, parseInt(e.target.value) || 0) }),
                                    className: 'widefat',
                                })
                            )
                        ),

                        viewMode === 'mobile' && wp.element.createElement(
                            wp.element.Fragment,
                            {},
                            wp.element.createElement(
                                'p', { className: 'description', style: { marginBottom: '8px' } },
                                'Leave blank to inherit desktop setting.'
                            ),
                            wp.element.createElement(window.EBAlignControl, {
                                attrKey:       'mobileAlign',
                                attributes:    attributes,
                                setAttributes: setAttributes,
                                isMobile:      true,
                            }),
                            wp.element.createElement(window.EBPaddingFields, {
                                prefix:        'mobilePadding',
                                attributes:    attributes,
                                setAttributes: setAttributes,
                                isMobile:      true,
                            }),
                            wp.element.createElement(
                                'div', { className: 'eb-field' },
                                wp.element.createElement('label', {}, 'Border Radius Override (px)'),
                                wp.element.createElement('input', {
                                    type:        'number',
                                    min:         0,
                                    max:         50,
                                    value:       attributes.mobileBorderRadius !== null && attributes.mobileBorderRadius !== undefined ? attributes.mobileBorderRadius : '',
                                    placeholder: '—',
                                    onChange:    (e) => setAttributes({ mobileBorderRadius: e.target.value !== '' ? Math.max(0, parseInt(e.target.value) || 0) : null }),
                                    className:   'widefat',
                                }),
                                wp.element.createElement('p', { className: 'description' }, 'Leave blank to inherit desktop setting.')
                            ),
                            wp.element.createElement(
                                'div', { className: 'eb-field' },
                                wp.element.createElement('label', {}, 'Border Width Override (px)'),
                                wp.element.createElement('input', {
                                    type:        'number',
                                    min:         1,
                                    max:         10,
                                    value:       attributes.mobileBorderWidth !== null && attributes.mobileBorderWidth !== undefined ? attributes.mobileBorderWidth : '',
                                    placeholder: '—',
                                    onChange:    (e) => setAttributes({ mobileBorderWidth: e.target.value !== '' ? Math.max(1, parseInt(e.target.value) || 1) : null }),
                                    className:   'widefat',
                                }),
                                wp.element.createElement('p', { className: 'description' }, 'Leave blank to inherit desktop setting.')
                            ),
                            wp.element.createElement(
                                'div', { className: 'eb-field' },
                                wp.element.createElement('label', {}, 'Border Colour Override'),
                                wp.element.createElement(ColorPalette, {
                                    value:     attributes.mobileBorderColor,
                                    onChange:  (color) => setAttributes({ mobileBorderColor: color || '' }),
                                    clearable: true,
                                }),
                                wp.element.createElement('p', { className: 'description' }, 'Leave empty to inherit desktop border colour.')
                            )
                        ),

                        wp.element.createElement(
                            'div',
                            { className: 'eb-field' },
                            wp.element.createElement('label', {}, 'Box Background Colour'),
                            wp.element.createElement(ColorPalette, {
                                value:    backgroundColor,
                                onChange: (color) => setAttributes({ backgroundColor: color || '' }),
                            })
                        ),

                        wp.element.createElement(
                            'div',
                            { className: 'eb-field' },
                            wp.element.createElement('label', {}, 'Text Colour'),
                            wp.element.createElement('p', { className: 'description' }, 'Leave empty to use automatic contrast colour from your preset.'),
                            wp.element.createElement(ColorPalette, {
                                value:    textColor,
                                onChange: (color) => setAttributes({ textColor: color || '' }),
                            })
                        ),

                        wp.element.createElement(
                            'div',
                            { className: 'eb-field' },
                            wp.element.createElement('label', {}, 'Code Box Background'),
                            wp.element.createElement(ColorPalette, {
                                value:    codeBackgroundColor,
                                onChange: (color) => setAttributes({ codeBackgroundColor: color || '' }),
                            })
                        ),

                        wp.element.createElement(
                            'div',
                            { className: 'eb-field' },
                            wp.element.createElement('label', {}, 'Code Text Colour'),
                            wp.element.createElement('p', { className: 'description' }, 'Leave empty to use automatic contrast colour from your preset.'),
                            wp.element.createElement(ColorPalette, {
                                value:    codeColor,
                                onChange: (color) => setAttributes({ codeColor: color || '' }),
                            })
                        ),

                        wp.element.createElement(
                            'div',
                            { className: 'eb-field' },
                            wp.element.createElement('label', {}, 'Dark Mode Box Background'),
                            wp.element.createElement('p', { className: 'description' }, 'Override the box background in dark mode. Leave empty to use the preset dark background.'),
                            wp.element.createElement('div', { style: { display: 'flex', gap: '6px', marginBottom: '8px' } },
                                ...darkSwatches.map(swatch =>
                                    wp.element.createElement('button', {
                                        type: 'button',
                                        title: swatch.name + ' ' + swatch.color,
                                        onClick: () => setAttributes({ darkBackgroundColor: swatch.color }),
                                        style: {
                                            backgroundColor: swatch.color,
                                            width: '28px', height: '28px',
                                            border: darkBackgroundColor === swatch.color ? '2px solid #2271b1' : '2px solid #ccc',
                                            borderRadius: '4px', cursor: 'pointer', padding: 0,
                                        }
                                    })
                                )
                            ),
                            darkBackgroundColor && wp.element.createElement('button', {
                                type: 'button',
                                className: 'components-button is-destructive is-small',
                                style: { marginTop: '6px' },
                                onClick: () => setAttributes({ darkBackgroundColor: '' })
                            }, 'Reset to Preset Default')
                        ),

                        wp.element.createElement(
                            'div',
                            { className: 'eb-field' },
                            wp.element.createElement('label', {}, 'Dark Mode Code Box Background'),
                            wp.element.createElement('p', { className: 'description' }, 'Override the code box background in dark mode.'),
                            wp.element.createElement('div', { style: { display: 'flex', gap: '6px', marginBottom: '8px' } },
                                ...darkSwatches.map(swatch =>
                                    wp.element.createElement('button', {
                                        type: 'button',
                                        title: swatch.name + ' ' + swatch.color,
                                        onClick: () => setAttributes({ darkCodeBackgroundColor: swatch.color }),
                                        style: {
                                            backgroundColor: swatch.color,
                                            width: '28px', height: '28px',
                                            border: darkCodeBackgroundColor === swatch.color ? '2px solid #2271b1' : '2px solid #ccc',
                                            borderRadius: '4px', cursor: 'pointer', padding: 0,
                                        }
                                    })
                                )
                            ),
                            darkCodeBackgroundColor && wp.element.createElement('button', {
                                type: 'button',
                                className: 'components-button is-destructive is-small',
                                style: { marginTop: '6px' },
                                onClick: () => setAttributes({ darkCodeBackgroundColor: '' })
                            }, 'Reset to Default')
                        )
                    )
                ),

                // Editor preview
                wp.element.createElement(
                    'div',
                    {
                        style: {
                            textAlign:       align,
                            padding:         '20px',
                            backgroundColor: backgroundColor || undefined,
                        }
                    },
                    title && wp.element.createElement(
                        'p',
                        { style: { margin: '0 0 4px', fontFamily: subFontStack, fontWeight: subFontWeight, color: textColor || autoColor, fontSize: subFontSize } },
                        title
                    ),
                    description && wp.element.createElement(
                        'p',
                        { style: { margin: '0 0 12px', fontFamily: bodyFontStack, color: textColor || autoColor, fontSize: bodyFontSize } },
                        description
                    ),
                    wp.element.createElement(
                        'div',
                        {
                            style: {
                                display:         'inline-block',
                                padding:         '10px 24px',
                                backgroundColor: codeBackgroundColor || undefined,
                                border:          borderColor ? borderWidth + 'px ' + borderStyle + ' ' + borderColor : undefined,
                                borderRadius:    (attributes.borderRadius !== undefined ? attributes.borderRadius : 4) + 'px',
                                color:           codeColor || autoColor,
                                fontFamily:      subFontStack,
                                fontSize:        subFontSize,
                                fontWeight:      subFontWeight,
                                letterSpacing:   '3px',
                            }
                        },
                        code || 'SAVE20'
                    ),
                    expiryText && wp.element.createElement(
                        'p',
                        { style: { margin: '10px 0 0', fontFamily: bodyFontStack, color: textColor || autoColor, fontSize: bodyFontSize, opacity: '0.7' } },
                        expiryText
                    )
                )
            );
        },

        save: function () {
            return null;
        }
    });

})(window.wp);
