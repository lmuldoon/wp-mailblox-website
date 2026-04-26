(function (wp) {

    const { registerBlockType } = wp.blocks;
    const { InspectorControls } = wp.blockEditor;
    const { PanelBody, RangeControl, ColorPalette, Button, TextControl, ToggleControl } = wp.components;
    const { useState, useEffect } = wp.element;

    function EBPostSearch({ onSelect }) {
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
                path: wp.url.addQueryArgs('/wp/v2/search', {
                    search:   search,
                    per_page: 8,
                    type:     'post',
                    subtype:  'post,page',
                }),
            }).then((items) => {
                setResults(items);
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
                placeholder: 'Search posts and pages...',
                className:   'widefat',
                style:       { marginBottom: '4px' }
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
                                onSelect(item.url);
                                setSearch('');
                                setResults([]);
                            },
                            style: {
                                display:      'block',
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
                        wp.element.createElement('span', { style: { display: 'block', fontWeight: '600' } }, item.title || item.url),
                        wp.element.createElement('span', { style: { display: 'block', color: '#888', fontSize: '11px' } }, item.subtype || item.type)
                    )
                )
            ),
            search.length >= 2 && results.length === 0 && !loading &&
            wp.element.createElement('p', { style: { fontSize: '12px', color: '#888', margin: '4px 0' } }, 'No results found.')
        );
    }

    registerBlockType('email-builder/menu', {
        title:    'Menu',
        icon:     'menu',
        category: 'email-builder',
        parent: ['email-builder/column'],

        attributes: {
            items:          { type: 'array',  default: [
                { label: 'Home',    url: '#' },
                { label: 'About',   url: '#' },
                { label: 'Contact', url: '#' },
            ]},
            align:          { type: 'string', default: 'center' },
            fontSize:       { type: 'number', default: 14 },
            textColor:      { type: 'string', default: '#000000' },
            separatorWidth: { type: 'string', default: '0' },
            separatorColor: { type: 'string', default: '#cccccc' },
            paddingTop:     { type: 'number', default: 0 },
            paddingBottom:  { type: 'number', default: 0 },
            paddingLeft:    { type: 'number', default: 0 },
            paddingRight:   { type: 'number', default: 0 },

            mobilePaddingTop:    { type: 'number', default: null },
            mobilePaddingBottom: { type: 'number', default: null },
            mobilePaddingLeft:   { type: 'number', default: null },
            mobilePaddingRight:  { type: 'number', default: null },
            mobileAlign:         { type: 'string', default: '' },
            mobileFontSize:      { type: 'number', default: null },
            hideOnMobile:        { type: 'boolean', default: false },
            utmSource:   { type: 'string', default: '' },
            utmMedium:   { type: 'string', default: '' },
            utmCampaign: { type: 'string', default: '' },
            utmContent:  { type: 'string', default: '' },
            utmTerm:     { type: 'string', default: '' },
        },

        edit: function (props) {
            const { attributes, setAttributes } = props;
            const { items, align, fontSize, textColor, separatorWidth, separatorColor,
                paddingTop, paddingBottom, paddingLeft, paddingRight } = attributes;

            const [viewMode, setViewMode] = useState('desktop');
            const isMobilePreview  = window.EBUseIsMobilePreview();
            const _preset           = (window.EB_EDITOR_DATA && window.EB_EDITOR_DATA.preset) || {};
            const bodyFontStack     = _preset.body_font_stack || 'Helvetica, Arial, sans-serif';
            const effectiveAlign    = window.ebMobileVal(align,    attributes.mobileAlign,    isMobilePreview);
            const effectiveFontSize = window.ebMobileVal(fontSize, attributes.mobileFontSize, isMobilePreview);
            const effPaddingTop    = window.ebMobileVal(paddingTop,    attributes.mobilePaddingTop,    isMobilePreview);
            const effPaddingBottom = window.ebMobileVal(paddingBottom, attributes.mobilePaddingBottom, isMobilePreview);
            const effPaddingLeft   = window.ebMobileVal(paddingLeft,   attributes.mobilePaddingLeft,   isMobilePreview);
            const effPaddingRight  = window.ebMobileVal(paddingRight,  attributes.mobilePaddingRight,  isMobilePreview);

            function updateItem(index, key, value) {
                const newItems = items.map((item, i) =>
                    i === index ? { ...item, [key]: value } : item
                );
                setAttributes({ items: newItems });
            }

            function addItem() {
                setAttributes({
                    items: [...items, { label: 'New Item', url: '#' }]
                });
            }

            function removeItem(index) {
                setAttributes({
                    items: items.filter((_, i) => i !== index)
                });
            }

            return wp.element.createElement(
                'div',
                { className: 'eb-menu-block', style: { textAlign: effectiveAlign, padding: '0' } },

                wp.element.createElement(
                    InspectorControls,
                    {},

                    // Menu items panel — always visible
                    wp.element.createElement(
                        PanelBody,
                        { title: 'Menu Items', initialOpen: true },

                        ...items.map((item, i) =>
                            wp.element.createElement(
                                'div',
                                {
                                    key: i,
                                    style: {
                                        border:       '1px solid #ddd',
                                        borderRadius: '4px',
                                        padding:      '8px',
                                        marginBottom: '8px',
                                        background:   '#fafafa',
                                    }
                                },

                                wp.element.createElement(
                                    'div',
                                    { style: { display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '6px' } },
                                    wp.element.createElement(
                                        'strong',
                                        { style: { fontSize: '12px' } },
                                        'Item ' + (i + 1)
                                    ),
                                    items.length > 1 && wp.element.createElement(
                                        Button,
                                        {
                                            isDestructive: true,
                                            isSmall:       true,
                                            onClick:       () => removeItem(i),
                                        },
                                        '✕'
                                    )
                                ),

                                wp.element.createElement(TextControl, {
                                    label:    'Label',
                                    value:    item.label,
                                    onChange: (value) => updateItem(i, 'label', value),
                                }),

                                wp.element.createElement(TextControl, {
                                    label:    'URL',
                                    value:    item.url,
                                    onChange: (value) => updateItem(i, 'url', value),
                                    placeholder: 'https://',
                                }),

                                wp.element.createElement(
                                    'div',
                                    { className: 'eb-field' },
                                    wp.element.createElement('label', {}, 'Search Posts & Pages'),
                                    wp.element.createElement(EBPostSearch, {
                                        onSelect: (url) => updateItem(i, 'url', url)
                                    })
                                ),

                                wp.element.createElement(
                                    'div',
                                    { style: { display: 'flex', flexWrap: 'wrap', gap: '4px', marginTop: '4px' } },
                                    ...Object.entries((window.EB_EDITOR_DATA && window.EB_EDITOR_DATA.platform_tags) || {}).flatMap(([groupKey, groupTags]) =>
                                        groupTags
                                            .filter(tag => tag.type === 'url')
                                            .map(tag =>
                                                wp.element.createElement(
                                                    'button',
                                                    {
                                                        key:       tag.key,
                                                        type:      'button',
                                                        className: 'components-button is-secondary is-small',
                                                        onClick:   () => updateItem(i, 'url', tag.tag),
                                                    },
                                                    tag.label
                                                )
                                            )
                                    )
                                )
                            )
                        ),

                        wp.element.createElement(
                            Button,
                            {
                                variant: 'secondary',
                                onClick: addItem,
                                style:   { width: '100%', justifyContent: 'center', marginTop: '4px' },
                            },
                            '+ Add Menu Item'
                        )
                    ),

                    // Style panel
                    wp.element.createElement(
                        PanelBody,
                        { title: 'Style', initialOpen: false },

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

                            wp.element.createElement(RangeControl, {
                                label:    'Font Size (px)',
                                value:    fontSize,
                                min:      10,
                                max:      24,
                                onChange: (value) => setAttributes({ fontSize: value }),
                            }),

                            wp.element.createElement(window.EBPaddingFields, {
                                prefix: 'padding',
                                attributes,
                                setAttributes,
                                isMobile: false,
                            }),

                            wp.element.createElement(
                                'div',
                                { className: 'eb-field' },
                                wp.element.createElement('label', {}, 'Text Colour'),
                                wp.element.createElement(ColorPalette, {
                                    value:    textColor,
                                    onChange: (color) => setAttributes({ textColor: color || '#000000' }),
                                })
                            ),

                            wp.element.createElement(
                                'div',
                                { className: 'eb-field' },
                                wp.element.createElement('label', {}, 'Separator Width'),
                                wp.element.createElement('input', {
                                    type:      'number',
                                    value:     separatorWidth,
                                    onChange:  (e) => setAttributes({ separatorWidth: e.target.value }),
                                    className: 'widefat'
                                })
                            ),

                            separatorWidth && wp.element.createElement(
                                'div',
                                { className: 'eb-field' },
                                wp.element.createElement('label', {}, 'Separator Colour'),
                                wp.element.createElement(ColorPalette, {
                                    value:    separatorColor,
                                    onChange: (color) => setAttributes({ separatorColor: color || '#cccccc' }),
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
                                attrKey: 'mobileAlign',
                                attributes,
                                setAttributes,
                                isMobile: true,
                            }),
                            wp.element.createElement(wp.components.ToggleControl, {
                                label:    'Override Font Size',
                                checked:  attributes.mobileFontSize !== null && attributes.mobileFontSize !== undefined,
                                onChange: (v) => setAttributes({ mobileFontSize: v ? fontSize : null }),
                            }),
                            attributes.mobileFontSize !== null && attributes.mobileFontSize !== undefined &&
                                wp.element.createElement(wp.components.RangeControl, {
                                    value:    attributes.mobileFontSize,
                                    min:      10,
                                    max:      24,
                                    onChange: (v) => setAttributes({ mobileFontSize: v }),
                                }),
                            wp.element.createElement(window.EBPaddingFields, {
                                prefix: 'mobilePadding',
                                attributes,
                                setAttributes,
                                isMobile: true,
                            })
                        )
                    ),

                    wp.element.createElement(
                        PanelBody,
                        { title: 'UTM Parameters', initialOpen: false },
                        wp.element.createElement(
                            'p',
                            { className: 'description', style: { marginBottom: '8px' } },
                            'Appended to all menu item URLs for campaign tracking. Leave blank to omit.'
                        ),
                        ...[
                            { key: 'utmSource',   label: 'Source',   placeholder: 'e.g. mailchimp' },
                            { key: 'utmMedium',   label: 'Medium',   placeholder: 'e.g. email' },
                            { key: 'utmCampaign', label: 'Campaign', placeholder: 'e.g. spring-sale' },
                            { key: 'utmContent',  label: 'Content',  placeholder: 'optional' },
                            { key: 'utmTerm',     label: 'Term',     placeholder: 'optional' },
                        ].map(f => wp.element.createElement(wp.components.TextControl, {
                            key:         f.key,
                            label:       f.label,
                            value:       attributes[f.key] || '',
                            onChange:    (v) => setAttributes({ [f.key]: v }),
                            placeholder: f.placeholder,
                        }))
                    )
                ),

                // Editor preview
                wp.element.createElement(
                    'div',
                    { style: { display: 'inline-flex', flexWrap: 'wrap', alignItems: 'center', gap: '0', justifyContent: effectiveAlign === 'center' ? 'center' : effectiveAlign === 'right' ? 'flex-end' : 'flex-start', width: '100%' } },
                    items.flatMap((item, i) => {
                        const els = [
                            wp.element.createElement(
                                'a',
                                {
                                    key:   'item-' + i,
                                    href:  '#',
                                    style: {
                                        '--separator-border': separatorWidth + 'px solid ' + separatorColor,
                                        color:          textColor,
                                        fontFamily:     bodyFontStack,
                                        fontSize:       effectiveFontSize + 'px',
                                        textDecoration: 'none',
                                        padding: `${effPaddingTop}px ${effPaddingRight}px ${effPaddingBottom}px ${effPaddingLeft}px`,
                                        whiteSpace:     'nowrap',
                                    }
                                },
                                item.label
                            )
                        ];

                        if (separatorWidth && i < items.length - 1) {
                            els.push(
                                wp.element.createElement(
                                    'span',
                                    {
                                        key:   'sep-' + i,
                                        style: { color: separatorColor, fontSize: fontSize + 'px' }
                                    },
                                    ''
                                )
                            );
                        }

                        return els;
                    })
                )
            );
        },

        save: function () {
            return null;
        }
    });

})(window.wp);
