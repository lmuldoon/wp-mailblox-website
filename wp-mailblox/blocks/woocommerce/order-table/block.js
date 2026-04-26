(function (wp) {

    const { registerBlockType } = wp.blocks;
    const { InspectorControls } = wp.blockEditor;
    const { PanelBody, ColorPalette, ToggleControl } = wp.components;
    const { useState } = wp.element;
    const { useSelect } = wp.data;

    const SAMPLE_ITEMS = [
        { name: 'Example Product One',   qty: 1, price: '$49.00' },
        { name: 'Example Product Two',   qty: 2, price: '$29.00' },
        { name: 'Example Product Three', qty: 1, price: '$19.00' },
    ];

    registerBlockType('email-builder/wc-order-table', {
        title:    'Order Table',
        icon:     'list-view',
        category: 'email-builder-woocommerce',
        parent:   ['email-builder/column'],

        attributes: {
            headerBgColor:   { type: 'string',  default: '' },
            headerTextColor: { type: 'string',  default: '' },
            borderColor:     { type: 'string',  default: '#eeeeee' },
            rowAltColor:     { type: 'string',  default: '' },
            textColor:       { type: 'string',  default: '' },
            showSubtotal:    { type: 'boolean', default: true },
            showDiscount:    { type: 'boolean', default: false },
            showTax:         { type: 'boolean', default: false },
            showShipping:    { type: 'boolean', default: true },
            showTotal:       { type: 'boolean', default: true },
            disableDarkTextColour: { type: 'boolean', default: false },
            buttonText:          { type: 'string',  default: 'View Order' },
            buttonColor:         { type: 'string',  default: '' },
            buttonTextColor:     { type: 'string',  default: '' },
            buttonBorderRadius:  { type: 'number',  default: 0 },
            buttonPaddingTop:    { type: 'number',  default: 10 },
            buttonPaddingBottom: { type: 'number',  default: 10 },
            buttonPaddingLeft:   { type: 'number',  default: 25 },
            buttonPaddingRight:  { type: 'number',  default: 25 },
            buttonBorderWidth:        { type: 'number',  default: 0 },
            buttonBorderColor:        { type: 'string',  default: '#000000' },
            mobileButtonBorderRadius: { type: 'number',  default: null },
            mobileButtonBorderWidth:  { type: 'number',  default: null },
            mobileButtonBorderColor:  { type: 'string',  default: '' },
            showButton:          { type: 'boolean', default: true },
            fontSize:            { type: 'number',  default: null },
            mobileFontSize:      { type: 'number',  default: null },
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
                headerBgColor, headerTextColor, borderColor, rowAltColor, textColor,
                showSubtotal, showDiscount, showTax, showShipping, showTotal,
                disableDarkTextColour, fontSize, mobileFontSize,
                buttonText, buttonColor, buttonTextColor, buttonBorderRadius, showButton,
                buttonPaddingTop, buttonPaddingBottom, buttonPaddingLeft, buttonPaddingRight,
                buttonBorderWidth, buttonBorderColor,
            } = attributes;

            var _preset = (window.EB_EDITOR_DATA && window.EB_EDITOR_DATA.preset) || {};
            var presetBtnColor    = _preset.button_color      || '#000000';
            var presetBtnTxtColor = _preset.button_text_color || '';
            var effectiveBtnColor = buttonColor || presetBtnColor;
            const displayBtnTextColor = buttonTextColor || presetBtnTxtColor || window.ebContrastColor(effectiveBtnColor);
            var btnFontStack  = _preset.button_font_stack  || _preset.body_font_stack || 'Helvetica, Arial, sans-serif';
            var btnFontSize   = (_preset.button_size   || _preset.body_size || 16) + 'px';
            var btnFontWeight = _preset.button_font_weight || 700;
            var bodyFontStack    = _preset.body_font_stack || 'Helvetica, Arial, sans-serif';
            var presetBodySize   = _preset.body_size || 16;
            var isMobilePreview  = window.EBUseIsMobilePreview();
            var effectiveFontSize = window.ebMobileVal(
                fontSize    !== null ? fontSize    : presetBodySize,
                mobileFontSize !== null ? mobileFontSize : null,
                isMobilePreview
            ) + 'px';
            var bodyFontSize = effectiveFontSize;

            const [viewMode, setViewMode] = useState('desktop');

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

            const cellStyle = (alt) => ({
                padding:         '10px 12px',
                borderBottom:    '1px solid ' + borderColor,
                fontFamily:      bodyFontStack,
                fontSize:        bodyFontSize,
                color:           textColor || autoColor,
                backgroundColor: alt ? (rowAltColor || undefined) : undefined,
            });

            const totalsRowStyle = {
                padding:      '8px 12px',
                fontFamily:   bodyFontStack,
                fontSize:     bodyFontSize,
                color:        textColor || autoColor,
                borderBottom: '1px solid ' + borderColor,
            };

            return wp.element.createElement(
                'div',
                { className: 'eb-wc-order-table-block' },

                wp.element.createElement(
                    InspectorControls,
                    {},

                    wp.element.createElement(
                        PanelBody,
                        { title: 'Totals', initialOpen: true },

                        wp.element.createElement(ToggleControl, {
                            label:    'Show Subtotal',
                            checked:  showSubtotal,
                            onChange: (value) => setAttributes({ showSubtotal: value }),
                        }),
                        wp.element.createElement(ToggleControl, {
                            label:    'Show Discount',
                            checked:  showDiscount,
                            onChange: (value) => setAttributes({ showDiscount: value }),
                        }),
                        wp.element.createElement(ToggleControl, {
                            label:    'Show Tax',
                            checked:  showTax,
                            onChange: (value) => setAttributes({ showTax: value }),
                        }),
                        wp.element.createElement(ToggleControl, {
                            label:    'Show Shipping',
                            checked:  showShipping,
                            onChange: (value) => setAttributes({ showShipping: value }),
                        }),
                        wp.element.createElement(ToggleControl, {
                            label:    'Show Total',
                            checked:  showTotal,
                            onChange: (value) => setAttributes({ showTotal: value }),
                        }),

                        wp.element.createElement(ToggleControl, {
                            label:    'Show View Order Button',
                            checked:  showButton,
                            onChange: (value) => setAttributes({ showButton: value }),
                        }),

                        showButton && wp.element.createElement(
                            'div',
                            { className: 'eb-field' },
                            wp.element.createElement('label', {}, 'Button Text'),
                            wp.element.createElement('input', {
                                type:      'text',
                                value:     buttonText,
                                onChange:  (e) => setAttributes({ buttonText: e.target.value }),
                                className: 'widefat',
                            })
                        )
                    ),

                    wp.element.createElement(
                        PanelBody,
                        { title: 'Style', initialOpen: false },

                            // Disable dark mode text colour
                         wp.element.createElement(
                            'div', { className: 'eb-field' },
                            wp.element.createElement(ToggleControl, {
                                label: 'Disable dark mode text colour',
                                checked: disableDarkTextColour,
                                onChange: (value) => setAttributes({ disableDarkTextColour: value })
                            }),
                            wp.element.createElement(
                                'p',
                                { className: 'description' },
                                'Some email clients may still automatically adjust text colours in dark mode, even when this setting is disabled.'
                            )
                        ),

                        wp.element.createElement(window.EBResponsiveDivider),
                        wp.element.createElement(window.EBResponsiveToggle, {
                            value: viewMode,
                            onChange: setViewMode,
                        }),

                        viewMode === 'desktop' && wp.element.createElement(
                            wp.element.Fragment, {},
                            wp.element.createElement(window.EBPaddingFields, {
                                prefix:        'padding',
                                attributes:    attributes,
                                setAttributes: setAttributes,
                                isMobile:      false,
                            }),
                            wp.element.createElement(
                                'div', { className: 'eb-field' },
                                wp.element.createElement('label', {}, 'Table Font Size (px)'),
                                wp.element.createElement('p', { className: 'description' }, 'Leave blank to use body preset size.'),
                                wp.element.createElement('input', {
                                    type:        'number',
                                    min:         8,
                                    max:         40,
                                    value:       fontSize !== null ? fontSize : '',
                                    placeholder: presetBodySize,
                                    onChange:    (e) => setAttributes({ fontSize: e.target.value !== '' ? parseInt(e.target.value) : null }),
                                    className:   'widefat',
                                })
                            ),
                            showButton && wp.element.createElement(
                                wp.element.Fragment, {},
                                wp.element.createElement(
                                    'div',
                                    { className: 'eb-field' },
                                    wp.element.createElement('label', {}, 'Button Border Radius (px)'),
                                    wp.element.createElement('input', {
                                        type:      'number',
                                        min:       0,
                                        max:       50,
                                        value:     buttonBorderRadius,
                                        onChange:  (e) => setAttributes({ buttonBorderRadius: parseInt(e.target.value) || 0 }),
                                        className: 'widefat',
                                    })
                                ),
                                wp.element.createElement(
                                    'div',
                                    { className: 'eb-field' },
                                    wp.element.createElement('label', {}, 'Button Border Width (px)'),
                                    wp.element.createElement('input', {
                                        type:      'number',
                                        min:       0,
                                        max:       10,
                                        value:     buttonBorderWidth,
                                        onChange:  (e) => setAttributes({ buttonBorderWidth: parseInt(e.target.value) || 0 }),
                                        className: 'widefat',
                                    })
                                ),
                                showButton && buttonBorderWidth > 0 && wp.element.createElement(
                                    'div',
                                    { className: 'eb-field' },
                                    wp.element.createElement('label', {}, 'Button Border Colour'),
                                    wp.element.createElement(ColorPalette, {
                                        value:    buttonBorderColor,
                                        onChange: (color) => setAttributes({ buttonBorderColor: color || '#000000' }),
                                    })
                                )
                            )
                        ),

                        viewMode === 'mobile' && wp.element.createElement(
                            wp.element.Fragment, {},
                            wp.element.createElement(
                                'p', { className: 'description', style: { marginBottom: '8px' } },
                                'Leave blank to inherit desktop setting.'
                            ),
                            wp.element.createElement(window.EBPaddingFields, {
                                prefix:        'mobilePadding',
                                attributes:    attributes,
                                setAttributes: setAttributes,
                                isMobile:      true,
                            }),
                            wp.element.createElement(
                                'div', { className: 'eb-field' },
                                wp.element.createElement('label', {}, 'Table Font Size (px)'),
                                wp.element.createElement('p', { className: 'description' }, 'Leave blank to inherit desktop setting.'),
                                wp.element.createElement('input', {
                                    type:        'number',
                                    min:         8,
                                    max:         40,
                                    value:       mobileFontSize !== null ? mobileFontSize : '',
                                    placeholder: fontSize !== null ? fontSize : presetBodySize,
                                    onChange:    (e) => setAttributes({ mobileFontSize: e.target.value !== '' ? parseInt(e.target.value) : null }),
                                    className:   'widefat',
                                })
                            ),
                            showButton && wp.element.createElement(
                                wp.element.Fragment, {},
                                wp.element.createElement(
                                    'div', { className: 'eb-field' },
                                    wp.element.createElement('label', {}, 'Button Border Radius Override (px)'),
                                    wp.element.createElement('input', {
                                        type:        'number',
                                        min:         0,
                                        max:         50,
                                        value:       attributes.mobileButtonBorderRadius !== null && attributes.mobileButtonBorderRadius !== undefined ? attributes.mobileButtonBorderRadius : '',
                                        placeholder: '—',
                                        onChange:    (e) => setAttributes({ mobileButtonBorderRadius: e.target.value !== '' ? Math.max(0, parseInt(e.target.value) || 0) : null }),
                                        className:   'widefat',
                                    }),
                                    wp.element.createElement('p', { className: 'description' }, 'Leave blank to inherit desktop setting.')
                                ),
                                wp.element.createElement(
                                    'div', { className: 'eb-field' },
                                    wp.element.createElement('label', {}, 'Button Border Width Override (px)'),
                                    wp.element.createElement('input', {
                                        type:        'number',
                                        min:         0,
                                        max:         10,
                                        value:       attributes.mobileButtonBorderWidth !== null && attributes.mobileButtonBorderWidth !== undefined ? attributes.mobileButtonBorderWidth : '',
                                        placeholder: '—',
                                        onChange:    (e) => setAttributes({ mobileButtonBorderWidth: e.target.value !== '' ? Math.max(0, parseInt(e.target.value) || 0) : null }),
                                        className:   'widefat',
                                    }),
                                    wp.element.createElement('p', { className: 'description' }, 'Leave blank to inherit desktop setting.')
                                ),
                                (attributes.buttonBorderWidth > 0 || attributes.mobileButtonBorderWidth > 0) && wp.element.createElement(
                                    'div', { className: 'eb-field' },
                                    wp.element.createElement('label', {}, 'Button Border Colour Override'),
                                    wp.element.createElement(ColorPalette, {
                                        value:     attributes.mobileButtonBorderColor,
                                        onChange:  (color) => setAttributes({ mobileButtonBorderColor: color || '' }),
                                        clearable: true,
                                    }),
                                    wp.element.createElement('p', { className: 'description' }, 'Leave empty to inherit desktop border colour.')
                                )
                            )
                        ),

                        wp.element.createElement(
                            'div',
                            { className: 'eb-field' },
                            wp.element.createElement('label', {}, 'Header Background'),
                            wp.element.createElement(ColorPalette, {
                                value:    headerBgColor,
                                onChange: (color) => setAttributes({ headerBgColor: color || '' }),
                            })
                        ),

                        wp.element.createElement(
                            'div',
                            { className: 'eb-field' },
                            wp.element.createElement('label', {}, 'Header Text Colour'),
                            wp.element.createElement('p', { className: 'description' }, 'Leave empty to use automatic contrast colour.'),
                            wp.element.createElement(ColorPalette, {
                                value:    headerTextColor,
                                onChange: (color) => setAttributes({ headerTextColor: color || '' }),
                            })
                        ),

                        wp.element.createElement(
                            'div',
                            { className: 'eb-field' },
                            wp.element.createElement('label', {}, 'Border Colour'),
                            wp.element.createElement(ColorPalette, {
                                value:    borderColor,
                                onChange: (color) => setAttributes({ borderColor: color || '#eeeeee' }),
                            })
                        ),

                        wp.element.createElement(
                            'div',
                            { className: 'eb-field' },
                            wp.element.createElement('label', {}, 'Alternate Row Colour'),
                            wp.element.createElement('p', { className: 'description' }, 'Leave empty for transparent rows.'),
                            wp.element.createElement(ColorPalette, {
                                value:    rowAltColor,
                                onChange: (color) => setAttributes({ rowAltColor: color || '' }),
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

                        showButton && wp.element.createElement(
                            'div',
                            { className: 'eb-field' },
                            wp.element.createElement('label', {}, 'Button Colour'),
                            wp.element.createElement(ColorPalette, {
                                value:    buttonColor || presetBtnColor,
                                onChange: (color) => setAttributes({ buttonColor: color || '' }),
                            }),
                            wp.element.createElement('p', { style: { fontSize: '11px', color: '#888', margin: '4px 0 0' } },
                                buttonColor ? 'Overriding preset colour.' : 'Using preset colour (' + presetBtnColor + ').'
                            )
                        ),

                        showButton && wp.element.createElement(
                            'div',
                            { className: 'eb-field' },
                            wp.element.createElement('label', {}, 'Button Text Colour Override'),
                            wp.element.createElement('p', { style: { fontSize: '11px', color: '#888', margin: '0 0 6px' } }, 'Default: auto-contrast with button colour'),
                            wp.element.createElement(ColorPalette, {
                                value:    buttonTextColor,
                                onChange: (color) => setAttributes({ buttonTextColor: color || '' }),
                            }),
                            buttonTextColor && wp.element.createElement(
                                'button',
                                {
                                    type:    'button',
                                    onClick: () => setAttributes({ buttonTextColor: '' }),
                                    style:   { fontSize: '11px', color: '#cc0000', background: 'none', border: 'none', cursor: 'pointer', padding: '0', marginTop: '4px' },
                                },
                                '× Clear (use auto-contrast)'
                            )
                        ),

                        showButton && wp.element.createElement(window.EBPaddingFields, {
                            prefix:        'buttonPadding',
                            attributes:    attributes,
                            setAttributes: setAttributes,
                            isMobile:      false,
                        })
                    )
                ),

                // Editor preview — sample table
                wp.element.createElement(
                    'div',
                    { style: { fontSize: '13px', fontFamily: 'Arial, sans-serif' } },

                    // Header row
                    wp.element.createElement(
                        'div',
                        {
                            style: {
                                display:         'flex',
                                backgroundColor: headerBgColor || undefined,
                                color:           headerTextColor || (headerBgColor ? window.ebContrastColor(headerBgColor) : autoColor),
                                padding:         '10px 12px',
                                fontFamily:      bodyFontStack,
                                fontSize:        bodyFontSize,
                                fontWeight:      '700',
                                borderBottom:    '1px solid ' + borderColor,
                            }
                        },
                        wp.element.createElement('span', { style: { flex: '1',textAlign: 'left' } }, 'Product'),
                        wp.element.createElement('span', { style: { width: '40px', textAlign: 'center' } }, 'Qty'),
                        wp.element.createElement('span', { style: { width: '70px', textAlign: 'right' } }, 'Price')
                    ),

                    // Sample rows
                    ...SAMPLE_ITEMS.map((item, i) =>
                        wp.element.createElement(
                            'div',
                            {
                                key:   i,
                                style: {
                                    display:         'flex',
                                    alignItems:      'center',
                                    ...cellStyle(i % 2 !== 0),
                                }
                            },
                            wp.element.createElement('span', { style: { flex: '1',textAlign: 'left' } }, item.name),
                            wp.element.createElement('span', { style: { width: '40px', textAlign: 'center' } }, item.qty),
                            wp.element.createElement('span', { style: { width: '70px', textAlign: 'right' } }, item.price)
                        )
                    ),

                    // Totals
                    showSubtotal && wp.element.createElement(
                        'div',
                        { style: { display: 'flex', justifyContent: 'flex-end', ...totalsRowStyle } },
                        wp.element.createElement('span', { style: { marginRight: '16px' } }, 'Subtotal'),
                        wp.element.createElement('span', { style: { fontWeight: '600', width: '70px', textAlign: 'right' } }, '$97.00')
                    ),
                    showDiscount && wp.element.createElement(
                        'div',
                        { style: { display: 'flex', justifyContent: 'flex-end', ...totalsRowStyle } },
                        wp.element.createElement('span', { style: { marginRight: '16px' } }, 'Discount'),
                        wp.element.createElement('span', { style: { fontWeight: '600', width: '70px', textAlign: 'right' } }, '-$10.00')
                    ),
                    showTax && wp.element.createElement(
                        'div',
                        { style: { display: 'flex', justifyContent: 'flex-end', ...totalsRowStyle } },
                        wp.element.createElement('span', { style: { marginRight: '16px' } }, 'Tax'),
                        wp.element.createElement('span', { style: { fontWeight: '600', width: '70px', textAlign: 'right' } }, '$9.50')
                    ),
                    showShipping && wp.element.createElement(
                        'div',
                        { style: { display: 'flex', justifyContent: 'flex-end', ...totalsRowStyle } },
                        wp.element.createElement('span', { style: { marginRight: '16px' } }, 'Shipping'),
                        wp.element.createElement('span', { style: { fontWeight: '600', width: '70px', textAlign: 'right' } }, '$8.00')
                    ),
                    showTotal && wp.element.createElement(
                        'div',
                        {
                            style: {
                                display:        'flex',
                                justifyContent: 'flex-end',
                                padding:        '10px 12px',
                                fontFamily:     bodyFontStack,
                                fontSize:       bodyFontSize,
                                fontWeight:     '700',
                                color:          textColor || autoColor,
                                borderTop:      '2px solid ' + borderColor,
                            }
                        },
                        wp.element.createElement('span', { style: { marginRight: '16px' } }, 'Total'),
                        wp.element.createElement('span', { style: { width: '70px', textAlign: 'right' } }, '$105.00')
                    ),

                    // Button
                    showButton && wp.element.createElement(
                        'div',
                        { style: { marginTop: '16px', textAlign: 'center' } },
                        wp.element.createElement(
                            'span',
                            {
                                style: {
                                    display:         'inline-block',
                                    padding:         buttonPaddingTop + 'px ' + buttonPaddingRight + 'px ' + buttonPaddingBottom + 'px ' + buttonPaddingLeft + 'px',
                                    backgroundColor: effectiveBtnColor,
                                    color:           displayBtnTextColor,
                                    borderRadius:    buttonBorderRadius + 'px',
                                    fontFamily:      btnFontStack,
                                    fontSize:        btnFontSize,
                                    fontWeight:      btnFontWeight,
                                    border:          buttonBorderWidth > 0 ? buttonBorderWidth + 'px solid ' + buttonBorderColor : 'none',
                                }
                            },
                            buttonText
                        )
                    ),

                    wp.element.createElement(
                        'p',
                        { style: { margin: '12px 0 0', fontSize: '11px', color: '#aaa', textAlign: 'center' } },
                        'Sample data shown for preview. Actual order data is populated by your email platform.'
                    )
                )
            );
        },

        save: function () {
            return null;
        }
    });

})(window.wp);
