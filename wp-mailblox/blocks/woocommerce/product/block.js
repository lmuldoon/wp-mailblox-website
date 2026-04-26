(function (wp) {

    const { registerBlockType } = wp.blocks;
    const { InspectorControls } = wp.blockEditor;
    const { PanelBody, ColorPalette, ToggleControl } = wp.components;
    const { useState, useEffect } = wp.element;

    function EBProductSearch({ onSelect }) {
        const [search, setSearch]   = useState('');
        const [results, setResults] = useState([]);
        const [loading, setLoading] = useState(false);

        useEffect(() => {
            if (search.length < 2) {
                setResults([]);
                return;
            }
            setLoading(true);
            wp.apiFetch({
                path: wp.url.addQueryArgs('/eb/v1/products', { search }),
            }).then((items) => {
                setResults(items || []);
                setLoading(false);
            }).catch(() => {
                setResults([]);
                setLoading(false);
            });
        }, [search]);

        return wp.element.createElement(
            'div',
            { className: 'eb-post-search' },
            wp.element.createElement('input', {
                type:        'text',
                value:       search,
                onChange:    (e) => setSearch(e.target.value),
                placeholder: 'Search products...',
                className:   'widefat',
                style:       { marginBottom: '4px' },
            }),
            loading && wp.element.createElement(
                'p',
                { style: { fontSize: '12px', color: '#888', margin: '4px 0' } },
                'Searching...'
            ),
            results.length > 0 && wp.element.createElement(
                'div',
                {
                    style: {
                        border:       '1px solid #ddd',
                        borderRadius: '4px',
                        overflow:     'hidden',
                        marginBottom: '8px',
                    }
                },
                results.map((item, index) =>
                    wp.element.createElement(
                        'button',
                        {
                            key:     item.id,
                            type:    'button',
                            onClick: () => {
                                onSelect(item);
                                setSearch('');
                                setResults([]);
                            },
                            style: {
                                display:      'flex',
                                alignItems:   'center',
                                gap:          '8px',
                                width:        '100%',
                                textAlign:    'left',
                                padding:      '8px 10px',
                                background:   index % 2 === 0 ? '#fff' : '#f9f9f9',
                                border:       'none',
                                borderBottom: index < results.length - 1 ? '1px solid #eee' : 'none',
                                cursor:       'pointer',
                                fontSize:     '12px',
                            }
                        },
                        item.image_url && wp.element.createElement('img', {
                            src:   item.image_url,
                            style: { width: '32px', height: '32px', objectFit: 'cover', flexShrink: 0 },
                        }),
                        wp.element.createElement(
                            'div',
                            {},
                            wp.element.createElement('span', { style: { display: 'block', fontWeight: '600' } }, item.title),
                            wp.element.createElement('span', { style: { display: 'block', color: '#888' } }, item.price)
                        )
                    )
                )
            ),
            search.length >= 2 && results.length === 0 && !loading &&
                wp.element.createElement('p', { style: { fontSize: '12px', color: '#888', margin: '4px 0' } }, 'No products found.')
        );
    }

    registerBlockType('email-builder/wc-product', {
        title:    'Product',
        icon:     'cart',
        category: 'email-builder-woocommerce',
        parent:   ['email-builder/column'],

        attributes: {
            productId:            { type: 'number',  default: 0 },
            productTitle:         { type: 'string',  default: '' },
            productPrice:         { type: 'string',  default: '' },
            productRegularPrice:  { type: 'string',  default: '' },
            productIsOnSale:      { type: 'boolean', default: false },
            productImageUrl:      { type: 'string',  default: '' },
            productImageAlt:      { type: 'string',  default: '' },
            productPermalink:     { type: 'string',  default: '#' },
            buttonText:          { type: 'string', default: 'Shop Now' },
            disableDarkTextColour: { type: 'boolean', default: false },
            buttonColor:         { type: 'string', default: '' },
            buttonTextColor:     { type: 'string', default: '' },
            buttonBorderRadius:  { type: 'number', default: 0 },
            buttonPaddingTop:    { type: 'number', default: 10 },
            buttonPaddingBottom: { type: 'number', default: 10 },
            buttonPaddingLeft:   { type: 'number', default: 25 },
            buttonPaddingRight:  { type: 'number', default: 25 },
            buttonBorderWidth:        { type: 'number', default: 0 },
            buttonBorderColor:        { type: 'string', default: '#000000' },
            mobileButtonBorderRadius: { type: 'number', default: null },
            mobileButtonBorderWidth:  { type: 'number', default: null },
            mobileButtonBorderColor:  { type: 'string', default: '' },
            productDescription: { type: 'string',  default: '' },
            showPrice:          { type: 'boolean', default: true },
            showDescription:    { type: 'boolean', default: true },
            imageAlign:        { type: 'string',  default: 'center' },
            mobileAlign:       { type: 'string',  default: '' },
            paddingTop:        { type: 'number',  default: 0 },
            paddingBottom:     { type: 'number',  default: 0 },
            paddingLeft:       { type: 'number',  default: 0 },
            paddingRight:      { type: 'number',  default: 0 },
            mobilePaddingTop:    { type: 'number', default: null },
            mobilePaddingBottom: { type: 'number', default: null },
            mobilePaddingLeft:   { type: 'number', default: null },
            mobilePaddingRight:  { type: 'number', default: null },
        },

        edit: function (props) {
            const { attributes, setAttributes, clientId } = props;
            const {
                productId, productTitle, productPrice, productRegularPrice, productIsOnSale,
                productImageUrl, productImageAlt, productPermalink, productDescription,
                disableDarkTextColour,
                buttonText, buttonColor, buttonTextColor, buttonBorderRadius,
                buttonPaddingTop, buttonPaddingBottom, buttonPaddingLeft, buttonPaddingRight,
                buttonBorderWidth, buttonBorderColor,
                showPrice, showDescription, imageAlign,
            } = attributes;

            var _preset = (window.EB_EDITOR_DATA && window.EB_EDITOR_DATA.preset) || {};
            var presetBtnColor    = _preset.button_color       || '#000000';
            var presetBtnTxtColor = _preset.button_text_color  || '';
            var effectiveBtnColor = buttonColor || presetBtnColor;
            const displayBtnTextColor = buttonTextColor || presetBtnTxtColor || window.ebContrastColor(effectiveBtnColor);
            var btnFontStack  = _preset.button_font_stack  || _preset.body_font_stack || 'Helvetica, Arial, sans-serif';
            var btnFontSize   = (_preset.button_size   || _preset.body_size || 16) + 'px';
            var btnFontWeight = _preset.button_font_weight || 700;
            var bodyFontStack = _preset.body_font_stack  || 'Helvetica, Arial, sans-serif';
            var bodyFontSize  = (_preset.body_size  || 16) + 'px';
            var subFontStack  = _preset.subheading_font_stack  || bodyFontStack;
            var subFontSize   = (_preset.subheading_size || 24) + 'px';
            var subFontWeight = _preset.subheading_font_weight || 400;

            const [viewMode, setViewMode] = useState('desktop');

            const autoColor = wp.data.useSelect(function(select) {
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
            const salePriceColor = (window.EB_EDITOR_DATA && window.EB_EDITOR_DATA.preset && window.EB_EDITOR_DATA.preset.sale_price_color) || '#c0392b';

            function onSelectProduct(product) {
                setAttributes({
                    productId:           product.id,
                    productTitle:        product.title,
                    productPrice:        product.price,
                    productRegularPrice: product.regular_price || '',
                    productIsOnSale:     product.is_on_sale || false,
                    productImageUrl:     product.image_url,
                    productImageAlt:     product.title,
                    productPermalink:    product.permalink,
                    productDescription:  product.short_description || '',
                });
            }

            const hasProduct = productId > 0;

            return wp.element.createElement(
                'div',
                { className: 'eb-wc-product-block' },

                wp.element.createElement(
                    InspectorControls,
                    {},

                    wp.element.createElement(
                        PanelBody,
                        { title: 'Product', initialOpen: true },

                        wp.element.createElement(
                            'div',
                            { className: 'eb-field' },
                            wp.element.createElement('label', {}, 'Search Products'),
                            wp.element.createElement(EBProductSearch, { onSelect: onSelectProduct })
                        ),

                        hasProduct && wp.element.createElement(
                            'div',
                            { className: 'eb-field' },
                            wp.element.createElement('label', {}, 'Product URL'),
                            wp.element.createElement('input', {
                                type:      'text',
                                value:     productPermalink,
                                onChange:  (e) => setAttributes({ productPermalink: e.target.value }),
                                className: 'widefat',
                            })
                        ),

                        wp.element.createElement(
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
                            wp.element.Fragment,
                            {},
                            wp.element.createElement(window.EBAlignControl, {
                                attrKey:       'imageAlign',
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
                            buttonBorderWidth > 0 && wp.element.createElement(
                                'div',
                                { className: 'eb-field' },
                                wp.element.createElement('label', {}, 'Button Border Colour'),
                                wp.element.createElement(ColorPalette, {
                                    value:    buttonBorderColor,
                                    onChange: (color) => setAttributes({ buttonBorderColor: color || '#000000' }),
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
                        ),

                        wp.element.createElement(ToggleControl, {
                            label:    'Show Price',
                            checked:  showPrice,
                            onChange: (value) => setAttributes({ showPrice: value }),
                        }),

                        wp.element.createElement(ToggleControl, {
                            label:    'Show Short Description',
                            checked:  showDescription,
                            onChange: (value) => setAttributes({ showDescription: value }),
                        }),

                        wp.element.createElement(
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

                        wp.element.createElement(
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

                        wp.element.createElement(window.EBPaddingFields, {
                            prefix:        'buttonPadding',
                            attributes:    attributes,
                            setAttributes: setAttributes,
                            isMobile:      false,
                        })
                    )
                ),

                // Editor preview
                !hasProduct
                    ? wp.element.createElement(
                        'div',
                        {
                            style: {
                                padding:         '24px',
                                textAlign:       'center',
                                border:          '2px dashed #ddd',
                                borderRadius:    '4px',
                                backgroundColor: '#fafafa',
                                color:           '#999',
                                fontSize:        '13px',
                            }
                        },
                        wp.element.createElement('p', { style: { margin: '0 0 4px', fontWeight: '600' } }, 'Product Block'),
                        wp.element.createElement('p', { style: { margin: 0 } }, 'Search for a product in the sidebar to get started.')
                    )
                    : wp.element.createElement(
                        'div',
                        { style: { textAlign: imageAlign } },
                        productImageUrl && wp.element.createElement('img', {
                            src:   productImageUrl,
                            alt:   productImageAlt,
                            style: { maxWidth: '100%', display: 'block', margin: imageAlign === 'center' ? '0 auto 12px' : '0 0 12px' },
                        }),
                        wp.element.createElement(
                            'p',
                            { style: { margin: '0 0 4px', fontFamily: subFontStack, fontWeight: subFontWeight, fontSize: subFontSize, color: autoColor } },
                            productTitle
                        ),
                        showDescription && productDescription && wp.element.createElement(
                            'p',
                            { style: { margin: '0 0 8px', fontFamily: bodyFontStack, fontSize: bodyFontSize, color: autoColor, opacity: 0.8 } },
                            productDescription
                        ),
                        showPrice && productPrice && wp.element.createElement(
                            'p',
                            { style: { margin: '0 0 12px', fontFamily: bodyFontStack, fontSize: bodyFontSize, color: autoColor } },
                            productIsOnSale && productRegularPrice && wp.element.createElement(
                                'span',
                                { style: { textDecoration: 'line-through', opacity: '0.5', marginRight: '6px' } },
                                productRegularPrice
                            ),
                            wp.element.createElement(
                                'span',
                                { style: { color: productIsOnSale ? salePriceColor : autoColor, fontWeight: productIsOnSale ? '700' : '400' } },
                                productPrice
                            )
                        ),
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
                                    textDecoration:  'none',
                                    border:          buttonBorderWidth > 0 ? buttonBorderWidth + 'px solid ' + buttonBorderColor : 'none',
                                }
                            },
                            buttonText
                        )
                    )
            );
        },

        save: function () {
            return null;
        }
    });

})(window.wp);
