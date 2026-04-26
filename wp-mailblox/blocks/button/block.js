(function (wp) {

    const { registerBlockType } = wp.blocks;
    const { RichText, InspectorControls } = wp.blockEditor;
    const { PanelBody, ColorPalette, Button, ToggleControl } = wp.components;
    const { useState, useEffect } = wp.element;
    const { useSelect } = wp.data;

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
                        border:        '1px solid #ddd',
                        borderRadius:  '4px',
                        overflow:      'hidden',
                        marginBottom:  '8px',
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
                        wp.element.createElement(
                            'span',
                            { style: { display: 'block', fontWeight: '600' } },
                            item.title || item.url
                        ),
                        wp.element.createElement(
                            'span',
                            { style: { display: 'block', color: '#888', fontSize: '11px' } },
                            item.subtype || item.type
                        )
                    )
                )
            ),

            search.length >= 2 && results.length === 0 && !loading &&
            wp.element.createElement(
                'p',
                { style: { fontSize: '12px', color: '#888', margin: '4px 0' } },
                'No results found.'
            )
        );
    }

    registerBlockType('email-builder/button', {
        title:    'Button',
        icon:     'admin-links',
        category: 'email-builder',
        parent: ['email-builder/column'],

        attributes: {
            text:            { type: 'string', default: 'Click me' },
            href:            { type: 'string', default: '#' },
            backgroundColor: { type: 'string', default: '' },
            borderRadius:    { type: 'number', default: 0 },
            textColor:       { type: 'string', default: '' },
            align:           { type: 'string', default: 'center' },
            paddingTop:      { type: 'number', default: 10 },
            paddingBottom:   { type: 'number', default: 10 },
            paddingLeft:     { type: 'number', default: 25 },
            paddingRight:    { type: 'number', default: 25 },

            borderWidth:         { type: 'number', default: 0 },
            borderColor:         { type: 'string', default: '#000000' },
            mobileBorderRadius:  { type: 'number', default: null },
            mobileBorderWidth:   { type: 'number', default: null },
            mobileBorderColor:   { type: 'string', default: '' },
            mobilePaddingTop:    { type: 'number', default: null },
            mobilePaddingBottom: { type: 'number', default: null },
            mobilePaddingLeft:   { type: 'number', default: null },
            mobilePaddingRight:  { type: 'number', default: null },
            mobileAlign:         { type: 'string', default: '' },
            hideOnMobile:        { type: 'boolean', default: false },
            utmSource:   { type: 'string', default: '' },
            utmMedium:   { type: 'string', default: '' },
            utmCampaign: { type: 'string', default: '' },
            utmContent:  { type: 'string', default: '' },
            utmTerm:     { type: 'string', default: '' },
        },

        edit: function (props) {
            const { attributes, setAttributes } = props;
            const {
                text,
                href,
                backgroundColor,
                borderRadius,
                textColor,
                align,
                paddingTop,
                paddingBottom,
                paddingLeft,
                paddingRight,
            } = attributes;

            const [viewMode, setViewMode] = useState('desktop');
            const isMobilePreview  = window.EBUseIsMobilePreview();

            // Preset button colour defaults — reactive so they update if preset changes
            const { presetBgColor, presetTextColor } = useSelect(function() {
                var preset = (window.EB_EDITOR_DATA && window.EB_EDITOR_DATA.preset) || {};
                return {
                    presetBgColor:   preset.button_color      || '#000000',
                    presetTextColor: preset.button_text_color || '',
                };
            }, []);

            // Effective colours: block override → preset default
            var effectiveBgColor   = backgroundColor || presetBgColor;
            var effectiveTextColor = textColor || presetTextColor || window.ebContrastColor(effectiveBgColor);
            const effectiveAlign   = window.ebMobileVal(align,        attributes.mobileAlign,         isMobilePreview);
            const effPaddingTop    = window.ebMobileVal(paddingTop,    attributes.mobilePaddingTop,    isMobilePreview);
            const effPaddingBottom = window.ebMobileVal(paddingBottom, attributes.mobilePaddingBottom, isMobilePreview);
            const effPaddingLeft   = window.ebMobileVal(paddingLeft,   attributes.mobilePaddingLeft,   isMobilePreview);
            const effPaddingRight  = window.ebMobileVal(paddingRight,  attributes.mobilePaddingRight,  isMobilePreview);
            const effBorderRadius  = window.ebMobileVal(borderRadius,  attributes.mobileBorderRadius,  isMobilePreview);
            const effBorderWidth   = window.ebMobileVal(attributes.borderWidth, attributes.mobileBorderWidth, isMobilePreview);
            const effBorderColor   = (isMobilePreview && attributes.mobileBorderColor) ? attributes.mobileBorderColor : (attributes.borderColor || '#000000');

            return wp.element.createElement(
                'div',
                { className: 'eb-button-block', style: { textAlign: effectiveAlign, opacity: (isMobilePreview && attributes.hideOnMobile) ? 0.3 : undefined } },

                wp.element.createElement(
                    InspectorControls,
                    {},
                    wp.element.createElement(
                        PanelBody,
                        { title: 'Settings', initialOpen: true },

                        wp.element.createElement(
                            'div',
                            { className: 'eb-field' },
                            wp.element.createElement('label', {}, 'Button URL'),
                            wp.element.createElement('input', {
                                type:        'text',
                                value:       href,
                                onChange:    (e) => setAttributes({ href: e.target.value }),
                                placeholder: 'Paste a URL...',
                                className:   'widefat',
                                style:       { marginBottom: '8px' }
                            }),
                            wp.element.createElement(
                                'p',
                                { className: 'description' },
                                'Paste an external URL, search for a post or page, or use a merge tag below.'
                            )
                        ),

                        wp.element.createElement(
                            'div',
                            { className: 'eb-field' },
                            wp.element.createElement('label', {}, 'Search Posts & Pages'),
                            wp.element.createElement(EBPostSearch, {
                                onSelect: (url) => setAttributes({ href: url })
                            })
                        ),

                        wp.element.createElement(
                            'div',
                            { className: 'eb-field' },
                            wp.element.createElement('label', {}, 'Insert URL Merge Tag'),
                            wp.element.createElement(
                                'p',
                                { className: 'description' },
                                'Click a tag to set it as the button URL.'
                            ),
                            wp.element.createElement(
                                'div',
                                { style: { display: 'flex', flexDirection: 'column', gap: '4px' } },
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
                                                    style:     { justifyContent: 'flex-start', marginBottom: '2px' },
                                                    onClick:   () => setAttributes({ href: tag.tag })
                                                },
                                                tag.label
                                            )
                                        )
                                )
                            )
                        )
                    ),

                    wp.element.createElement(
                        PanelBody,
                        { title: 'Style', initialOpen: false },

                        wp.element.createElement(
                            'div',
                            { className: 'eb-field' },
                            wp.element.createElement('label', {}, 'Button Colour'),
                            wp.element.createElement(ColorPalette, {
                                value:    backgroundColor || presetBgColor,
                                onChange: (color) => setAttributes({ backgroundColor: color || '' })
                            }),
                            wp.element.createElement(
                                'p',
                                { className: 'description' },
                                backgroundColor
                                    ? 'Overriding preset colour.'
                                    : 'Using preset colour (' + presetBgColor + ').'
                            ),
                            backgroundColor && wp.element.createElement(
                                Button,
                                {
                                    isDestructive: true,
                                    isSmall:       true,
                                    onClick:       () => setAttributes({ backgroundColor: '' })
                                },
                                'Reset to Preset'
                            )
                        ),

                        wp.element.createElement(
                            'div',
                            { className: 'eb-field' },
                            wp.element.createElement('label', {}, 'Text Colour Override'),
                            wp.element.createElement(
                                'p',
                                { className: 'description' },
                                'Leave empty to use preset text colour or automatic contrast.'
                            ),
                            wp.element.createElement(ColorPalette, {
                                value:     textColor,
                                onChange:  (color) => setAttributes({ textColor: color || '' }),
                                clearable: true
                            }),
                            textColor && wp.element.createElement(
                                Button,
                                {
                                    isDestructive: true,
                                    isSmall:       true,
                                    onClick:       () => setAttributes({ textColor: '' })
                                },
                                'Reset to Auto'
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
                                attrKey: 'align',
                                attributes,
                                setAttributes,
                                isMobile: false,
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
                                wp.element.createElement('label', {}, 'Border Radius (px)'),
                                wp.element.createElement('input', {
                                    type:      'number',
                                    min:       0,
                                    max:       50,
                                    value:     borderRadius,
                                    onChange:  (e) => setAttributes({ borderRadius: parseInt(e.target.value) || 0 }),
                                    className: 'widefat'
                                })
                            ),
                            wp.element.createElement(
                                'div',
                                { className: 'eb-field' },
                                wp.element.createElement('label', {}, 'Border Width (px)'),
                                wp.element.createElement('input', {
                                    type:      'number',
                                    min:       0,
                                    max:       10,
                                    value:     attributes.borderWidth,
                                    onChange:  (e) => setAttributes({ borderWidth: parseInt(e.target.value) || 0 }),
                                    className: 'widefat'
                                }),
                                wp.element.createElement(
                                    'p',
                                    { className: 'description' },
                                    'Set to 0 for no border.'
                                )
                            ),
                            attributes.borderWidth > 0 && wp.element.createElement(
                                'div',
                                { className: 'eb-field' },
                                wp.element.createElement('label', {}, 'Border Colour'),
                                wp.element.createElement(ColorPalette, {
                                    value:    attributes.borderColor,
                                    onChange: (color) => setAttributes({ borderColor: color || '#000000' }),
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
                            wp.element.createElement(window.EBPaddingFields, {
                                prefix: 'mobilePadding',
                                attributes,
                                setAttributes,
                                isMobile: true,
                            }),
                            wp.element.createElement(
                                'div',
                                { className: 'eb-field' },
                                wp.element.createElement('label', {}, 'Border Radius Override (px)'),
                                wp.element.createElement('input', {
                                    type:      'number',
                                    min:       0,
                                    max:       50,
                                    value:     attributes.mobileBorderRadius !== null && attributes.mobileBorderRadius !== undefined ? attributes.mobileBorderRadius : '',
                                    placeholder: '—',
                                    onChange:  (e) => setAttributes({ mobileBorderRadius: e.target.value !== '' ? Math.max(0, parseInt(e.target.value) || 0) : null }),
                                    className: 'widefat',
                                }),
                                wp.element.createElement('p', { className: 'description' }, 'Leave blank to inherit desktop setting.')
                            ),
                            wp.element.createElement(
                                'div',
                                { className: 'eb-field' },
                                wp.element.createElement('label', {}, 'Border Width Override (px)'),
                                wp.element.createElement('input', {
                                    type:      'number',
                                    min:       0,
                                    max:       10,
                                    value:     attributes.mobileBorderWidth !== null && attributes.mobileBorderWidth !== undefined ? attributes.mobileBorderWidth : '',
                                    placeholder: '—',
                                    onChange:  (e) => setAttributes({ mobileBorderWidth: e.target.value !== '' ? Math.max(0, parseInt(e.target.value) || 0) : null }),
                                    className: 'widefat',
                                }),
                                wp.element.createElement('p', { className: 'description' }, 'Leave blank to inherit desktop setting.')
                            ),
                            (attributes.borderWidth > 0 || attributes.mobileBorderWidth > 0) && wp.element.createElement(
                                'div',
                                { className: 'eb-field' },
                                wp.element.createElement('label', {}, 'Border Colour Override'),
                                wp.element.createElement(ColorPalette, {
                                    value:     attributes.mobileBorderColor,
                                    onChange:  (color) => setAttributes({ mobileBorderColor: color || '' }),
                                    clearable: true,
                                }),
                                wp.element.createElement('p', { className: 'description' }, 'Leave empty to inherit desktop border colour.')
                            ),
                            wp.element.createElement(ToggleControl, {
                                label:    'Hide on Mobile',
                                checked:  attributes.hideOnMobile,
                                onChange: (val) => setAttributes({ hideOnMobile: val }),
                            })
                        )
                    ),

                    wp.element.createElement(
                        PanelBody,
                        { title: 'UTM Parameters', initialOpen: false },
                        wp.element.createElement(
                            'p',
                            { className: 'description', style: { marginBottom: '8px' } },
                            'Appended to the button URL for campaign tracking. Leave blank to omit.'
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

                wp.element.createElement(RichText, {
                    tagName:     'a',
                    className:   'eb-button-link',
                    value:       text,
                    onChange:    (value) => setAttributes({ text: value }),
                    placeholder: 'Button text',
                    style: {
                        display:         'inline-block',
                        backgroundColor: effectiveBgColor,
                        color:           effectiveTextColor,
                        textDecoration:  'none',
                        borderRadius:    effBorderRadius + 'px',
                        padding:         effPaddingTop + 'px ' + effPaddingRight + 'px ' + effPaddingBottom + 'px ' + effPaddingLeft + 'px',
                        border:          effBorderWidth > 0 ? effBorderWidth + 'px solid ' + effBorderColor : 'none',
                    }
                })
            );
        },

        save: function () {
            return null;
        }
    });

})(window.wp);
