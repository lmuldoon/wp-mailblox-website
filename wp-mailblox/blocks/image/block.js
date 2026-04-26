(function (wp) {
    const {
        registerBlockType
    } = wp.blocks;

    const {
        MediaUpload,
        MediaUploadCheck,
        InspectorControls
    } = wp.blockEditor;

    const {
        Button,
        ColorPalette,
        PanelBody,
        TextControl,
        ToggleControl,
    } = wp.components;
    const { useState, useEffect } = wp.element;
    const { useSelect } = wp.data;

    function EBPostSearch({ onSelect }) {
        const [search, setSearch]   = useState('');
        const [results, setResults] = useState([]);
        const [loading, setLoading] = useState(false);

        useEffect(() => {
            if (search.length < 2) { setResults([]); return; }
            setLoading(true);
            wp.apiFetch({
                path: wp.url.addQueryArgs('/wp/v2/search', {
                    search:   search,
                    per_page: 8,
                    type:     'post',
                    subtype:  'post,page',
                }),
            }).then((items) => { setResults(items); setLoading(false); })
              .catch(() => { setResults([]); setLoading(false); });
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
                style:       { marginBottom: '4px' },
            }),
            loading && wp.element.createElement('p', { style: { fontSize: '12px', color: '#888', margin: '4px 0' } }, 'Searching...'),
            results.length > 0 && wp.element.createElement(
                'div',
                { style: { border: '1px solid #ddd', borderRadius: '4px', overflow: 'hidden', marginBottom: '8px' } },
                results.map((item, index) =>
                    wp.element.createElement(
                        'button',
                        {
                            key:     item.id,
                            type:    'button',
                            onClick: () => { onSelect(item.url); setSearch(''); setResults([]); },
                            style: {
                                display: 'block', width: '100%', textAlign: 'left',
                                padding: '8px 10px', background: index % 2 === 0 ? '#fff' : '#f9f9f9',
                                border: 'none', borderBottom: index < results.length - 1 ? '1px solid #eee' : 'none',
                                cursor: 'pointer', fontSize: '12px',
                            },
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

    registerBlockType('email-builder/image', {
        title: 'Image',
        icon: 'format-image',
        category: 'email-builder',
        parent: ['email-builder/column'],

        attributes: {
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
                default: 0
            },
            height: {
                type: 'number',
                default: 0
            },
            displayWidth: {
                type: 'number',
                default: 600
            },
            align: {
                type: 'string',
                default: 'center'
            },
            paddingTop:          { type: 'number', default: 0 },
            paddingBottom:       { type: 'number', default: 0 },
            paddingLeft:         { type: 'number', default: 0 },
            paddingRight:        { type: 'number', default: 0 },
            borderRadiusTL:      { type: 'number', default: 0 },
            borderRadiusTR:      { type: 'number', default: 0 },
            borderRadiusBR:      { type: 'number', default: 0 },
            borderRadiusBL:      { type: 'number', default: 0 },
            mobileBorderRadiusTL: { type: 'number', default: null },
            mobileBorderRadiusTR: { type: 'number', default: null },
            mobileBorderRadiusBR: { type: 'number', default: null },
            mobileBorderRadiusBL: { type: 'number', default: null },
            borderWidth:         { type: 'number', default: 0 },
            borderColor:         { type: 'string', default: '#000000' },
            mobileBorderWidth:   { type: 'number', default: null },
            mobileBorderColor:   { type: 'string', default: '' },
            mobileAlign:         { type: 'string', default: '' },
            mobilePaddingTop:    { type: 'number', default: null },
            mobilePaddingBottom: { type: 'number', default: null },
            mobilePaddingLeft:   { type: 'number', default: null },
            mobilePaddingRight:  { type: 'number', default: null },
            hideOnMobile:        { type: 'boolean', default: false },
            darkUrl:     { type: 'string', default: '' },
            linkUrl:     { type: 'string', default: '' },
            id: {
                type: 'number',
                default: 0
            },
            utmSource:   { type: 'string', default: '' },
            utmMedium:   { type: 'string', default: '' },
            utmCampaign: { type: 'string', default: '' },
            utmContent:  { type: 'string', default: '' },
            utmTerm:     { type: 'string', default: '' },
        },

        edit: function (props) {

            const { attributes, setAttributes, clientId } = props;
            const [viewMode, setViewMode] = useState('desktop');
            const isMobilePreview = window.EBUseIsMobilePreview();
            const effectiveAlign  = window.ebMobileVal(attributes.align, attributes.mobileAlign, isMobilePreview);

            // ── Auto-derive display width from column context ──
            const { columnIndex, columnsAttrs, columnCount } = useSelect(function(select) {
                var store     = select('core/block-editor');
                var parents   = store.getBlockParents(clientId);
                var columnId  = parents[parents.length - 1];  // direct parent: column block
                var columnsId = parents[parents.length - 2];  // grandparent: columns block
                var colsBlock = columnsId ? store.getBlock(columnsId) : null;
                var siblings  = columnsId ? store.getBlocks(columnsId) : [];
                var idx       = siblings.findIndex(function(b) { return b.clientId === columnId; });
                return {
                    columnIndex:  idx,
                    columnsAttrs: (colsBlock && colsBlock.attributes) ? colsBlock.attributes : {},
                    columnCount:  (colsBlock && colsBlock.attributes && colsBlock.attributes.columns) ? colsBlock.attributes.columns : 1,
                };
            }, [clientId]);

            var containerWidth = (window.EB_EDITOR_DATA && window.EB_EDITOR_DATA.preset && window.EB_EDITOR_DATA.preset.container_width) || 640;
            var colsGap    = columnsAttrs.gap          != null ? columnsAttrs.gap          : 20;
            var colsPadL   = columnsAttrs.paddingLeft  != null ? columnsAttrs.paddingLeft  : 20;
            var colsPadR   = columnsAttrs.paddingRight != null ? columnsAttrs.paddingRight : 20;
            var colWidths  = columnsAttrs.columnWidths || [];
            var reverse    = columnsAttrs.reverse || false;
            var effIndex   = (reverse && columnCount === 2) ? (columnCount - 1 - columnIndex) : columnIndex;

            var columnPixelWidth;
            if (columnCount <= 1) {
                columnPixelWidth = containerWidth - colsPadL - colsPadR;
            } else {
                var available = containerWidth - colsPadL - colsPadR - (colsGap * (columnCount - 1));
                var base      = Math.floor(available / columnCount);
                columnPixelWidth = (colWidths[effIndex] != null) ? colWidths[effIndex] : base;
            }

            var autoDisplayWidth = Math.max(1, columnPixelWidth - attributes.paddingLeft - attributes.paddingRight);

            useEffect(function() {
                if (autoDisplayWidth !== attributes.displayWidth) {
                    setAttributes({ displayWidth: autoDisplayWidth });
                }
            }, [autoDisplayWidth]);

            function onSelectImage(media) {
                setAttributes({
                    id: media.id,
                    url: media.url,
                    alt: media.alt,
                    width: media.width,
                    height: media.height
                });
            }

            const isRetinaReady =
                attributes.width && attributes.displayWidth ?
                attributes.width >= attributes.displayWidth * 2 :
                true;

            const justifyMap = { left: 'flex-start', center: 'center', right: 'flex-end' };

            return wp.element.createElement(
                'div',
                { className: 'eb-image-block',
                    style: {
                        '--display-width'  : attributes.displayWidth + 'px',
                        display            : 'flex',
                        justifyContent     : justifyMap[effectiveAlign] || 'center',
                        opacity            : (isMobilePreview && attributes.hideOnMobile) ? 0.3 : undefined,
                    }
                },

                // Sidebar controls
                wp.element.createElement(
                    InspectorControls,
                    {},
                    wp.element.createElement(
                        PanelBody,
                        { title: 'Settings', initialOpen: true },

                        // Alt text field + warning
                        attributes.url && wp.element.createElement(
                            'div',
                            { className: 'eb-field' },
                            wp.element.createElement(TextControl, {
                                label:       'Alt Text',
                                value:       attributes.alt,
                                onChange:    (value) => setAttributes({ alt: value }),
                                placeholder: 'Describe the image…',
                            }),
                            !attributes.alt && wp.element.createElement(
                                'p',
                                { style: { color: '#cc1818', fontSize: '12px', marginTop: '-8px' } },
                                '⚠ Alt text is missing. Screen readers and some email clients display this instead of the image.'
                            )
                        ),

                        // Dark mode image
                        wp.element.createElement(
                            'p',
                            { className: 'description', style: { marginBottom: '12px', marginTop: '8px' } },
                            'Optional dark mode image — shown instead of the main image when the recipient\'s email client is in dark mode.'
                        ),

                        attributes.darkUrl
                            ? wp.element.createElement(
                                'div',
                                { style: { marginBottom: '8px' } },
                                wp.element.createElement('img', {
                                    src:   attributes.darkUrl,
                                    style: { maxWidth: '100%', display: 'block', marginBottom: '8px', border: '1px solid #ddd' },
                                }),
                                wp.element.createElement(
                                    'div',
                                    { style: { display: 'flex', gap: '8px' } },
                                    wp.element.createElement(
                                        MediaUploadCheck, {},
                                        wp.element.createElement(MediaUpload, {
                                            onSelect: (media) => setAttributes({ darkUrl: media.url }),
                                            allowedTypes: ['image/jpeg', 'image/png', 'image/gif'],
                                            render: ({ open }) => wp.element.createElement(Button, { onClick: open, variant: 'secondary' }, 'Replace'),
                                        })
                                    ),
                                    wp.element.createElement(
                                        Button,
                                        { onClick: () => setAttributes({ darkUrl: '' }), variant: 'tertiary', style: { color: '#d63638' } },
                                        'Remove'
                                    )
                                )
                            )
                            : wp.element.createElement(
                                MediaUploadCheck, {},
                                wp.element.createElement(MediaUpload, {
                                    onSelect: (media) => setAttributes({ darkUrl: media.url, darkId: media.id }),
                                    allowedTypes: ['image/jpeg', 'image/png', 'image/gif'],
                                    render: ({ open }) => wp.element.createElement(Button, { onClick: open, variant: 'secondary' }, 'Upload Dark Mode Image'),
                                })
                            )
                    ),

                    wp.element.createElement(
                        PanelBody,
                        { title: 'Style', initialOpen: false },

                        // Retina status (global — not desktop/mobile specific)
                        attributes.width > 0 &&
                        wp.element.createElement(
                            'div',
                            { className: 'eb-field' },
                            wp.element.createElement('label', {}, 'Retina Status'),
                            wp.element.createElement(
                                'p',
                                { style: { color: isRetinaReady ? 'green' : 'red', fontSize: '12px' } },
                                isRetinaReady
                                    ? '✓ Retina ready'
                                    : '⚠ Image should be at least 2× the display width'
                            ),
                            wp.element.createElement(
                                'p',
                                { className: 'description' },
                                'Ensures the image looks sharp on high-resolution screens.'
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
                                prefix:        'padding',
                                attributes,
                                setAttributes,
                                isMobile:      false,
                            }),
                            wp.element.createElement(
                                'div',
                                { className: 'eb-field' },
                                wp.element.createElement('label', {}, 'Border Radius (px)'),
                                wp.element.createElement(
                                    'p', { className: 'description', style: { marginBottom: '8px' } },
                                    'Note: Outlook on Windows does not support border-radius.'
                                ),
                                wp.element.createElement(
                                    'div', {
                                        style: {
                                            display:             'grid',
                                            gridTemplateColumns: '1fr 1fr',
                                            gap:                 '6px',
                                        }
                                    },
                                    ...[
                                        { key: 'borderRadiusTL', label: '↖ Top Left' },
                                        { key: 'borderRadiusTR', label: '↗ Top Right' },
                                        { key: 'borderRadiusBL', label: '↙ Bottom Left' },
                                        { key: 'borderRadiusBR', label: '↘ Bottom Right' },
                                    ].map(({ key, label }) =>
                                        wp.element.createElement(
                                            'div', { key },
                                            wp.element.createElement('label', { style: { fontSize: '11px', display: 'block', marginBottom: '2px' } }, label),
                                            wp.element.createElement('input', {
                                                type:      'number',
                                                min:       0,
                                                max:       50,
                                                value:     attributes[key] || 0,
                                                onChange:  (e) => setAttributes({ [key]: Math.max(0, parseInt(e.target.value) || 0) }),
                                                className: 'widefat',
                                            })
                                        )
                                    )
                                )
                            ),
                            wp.element.createElement(
                                'div', { className: 'eb-field' },
                                wp.element.createElement('label', {}, 'Border Width (px)'),
                                wp.element.createElement('input', {
                                    type:      'number',
                                    min:       0,
                                    max:       10,
                                    value:     attributes.borderWidth || 0,
                                    onChange:  (e) => setAttributes({ borderWidth: parseInt(e.target.value) || 0 }),
                                    className: 'widefat',
                                }),
                                wp.element.createElement('p', { className: 'description' }, 'Set to 0 for no border.')
                            ),
                            attributes.borderWidth > 0 && wp.element.createElement(
                                'div', { className: 'eb-field' },
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
                                prefix:        'mobilePadding',
                                attributes,
                                setAttributes,
                                isMobile:      true,
                            }),
                            wp.element.createElement(
                                'div', { className: 'eb-field', style: { marginTop: '8px' } },
                                wp.element.createElement('label', {}, 'Border Radius Override (px)'),
                                wp.element.createElement(
                                    'p', { className: 'description', style: { marginBottom: '8px' } },
                                    'Leave blank to inherit desktop setting.'
                                ),
                                wp.element.createElement(
                                    'div', { style: { display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '6px' } },
                                    ...[
                                        { key: 'mobileBorderRadiusTL', label: '↖ Top Left' },
                                        { key: 'mobileBorderRadiusTR', label: '↗ Top Right' },
                                        { key: 'mobileBorderRadiusBL', label: '↙ Bottom Left' },
                                        { key: 'mobileBorderRadiusBR', label: '↘ Bottom Right' },
                                    ].map(({ key, label }) =>
                                        wp.element.createElement(
                                            'div', { key },
                                            wp.element.createElement('label', { style: { fontSize: '11px', display: 'block', marginBottom: '2px' } }, label),
                                            wp.element.createElement('input', {
                                                type:        'number',
                                                min:         0,
                                                max:         50,
                                                value:       attributes[key] !== null && attributes[key] !== undefined ? attributes[key] : '',
                                                placeholder: '—',
                                                onChange:    (e) => setAttributes({ [key]: e.target.value !== '' ? Math.max(0, parseInt(e.target.value) || 0) : null }),
                                                className:   'widefat',
                                            })
                                        )
                                    )
                                )
                            ),
                            wp.element.createElement(
                                'div', { className: 'eb-field' },
                                wp.element.createElement('label', {}, 'Border Width Override (px)'),
                                wp.element.createElement('input', {
                                    type:        'number',
                                    min:         0,
                                    max:         10,
                                    value:       attributes.mobileBorderWidth !== null && attributes.mobileBorderWidth !== undefined ? attributes.mobileBorderWidth : '',
                                    placeholder: '—',
                                    onChange:    (e) => setAttributes({ mobileBorderWidth: e.target.value !== '' ? Math.max(0, parseInt(e.target.value) || 0) : null }),
                                    className:   'widefat',
                                }),
                                wp.element.createElement('p', { className: 'description' }, 'Leave blank to inherit desktop setting.')
                            ),
                            (attributes.borderWidth > 0 || attributes.mobileBorderWidth > 0) && wp.element.createElement(
                                'div', { className: 'eb-field' },
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
                        { title: 'Link & UTM', initialOpen: false },

                        wp.element.createElement(wp.components.TextControl, {
                            label:       'Link URL',
                            value:       attributes.linkUrl,
                            onChange:    (v) => setAttributes({ linkUrl: v }),
                            placeholder: 'https://',
                            help:        'Wraps the image in a link. Leave blank for no link.',
                        }),

                        wp.element.createElement(
                            'div',
                            { className: 'eb-field' },
                            wp.element.createElement('label', {}, 'Search Posts & Pages'),
                            wp.element.createElement(EBPostSearch, {
                                onSelect: (url) => setAttributes({ linkUrl: url }),
                            })
                        ),

                        wp.element.createElement(
                            'p',
                            { className: 'description', style: { marginTop: '8px', marginBottom: '8px' } },
                            'UTM parameters are appended to the link URL for campaign tracking. Leave blank to omit.'
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

                // Image + overlaid replace button
                attributes.url
                    ? wp.element.createElement(
                        'div',
                        { style: { position: 'relative', display: 'inline-block', verticalAlign: 'top' } },
                        wp.element.createElement('img', {
                            src: attributes.url,
                            style: {
                                maxWidth:     '100%',
                                display:      'block',
                                borderRadius: `${window.ebMobileVal(attributes.borderRadiusTL || 0, attributes.mobileBorderRadiusTL, isMobilePreview)}px ${window.ebMobileVal(attributes.borderRadiusTR || 0, attributes.mobileBorderRadiusTR, isMobilePreview)}px ${window.ebMobileVal(attributes.borderRadiusBR || 0, attributes.mobileBorderRadiusBR, isMobilePreview)}px ${window.ebMobileVal(attributes.borderRadiusBL || 0, attributes.mobileBorderRadiusBL, isMobilePreview)}px`,
                                border:       (isMobilePreview && attributes.mobileBorderWidth > 0 ? attributes.mobileBorderWidth : attributes.borderWidth) > 0
                                    ? `${(isMobilePreview && attributes.mobileBorderWidth > 0 ? attributes.mobileBorderWidth : attributes.borderWidth)}px solid ${(isMobilePreview && attributes.mobileBorderColor) ? attributes.mobileBorderColor : (attributes.borderColor || '#000000')}`
                                    : 'none',
                            }
                        }),
                        wp.element.createElement(
                            MediaUploadCheck,
                            {},
                            wp.element.createElement(MediaUpload, {
                                onSelect: onSelectImage,
                                allowedTypes: ['image/jpeg', 'image/png', 'image/gif'],
                                render: ({ open }) =>
                                    wp.element.createElement(
                                        Button,
                                        {
                                            onClick: open,
                                            style: {
                                                position: 'absolute',
                                                top: '50%',
                                                left: '50%',
                                                transform: 'translate(-50%,-50%)',
                                                whiteSpace: 'nowrap',
                                                color:'white'
                                            }
                                        },
                                        'Replace Image'
                                    )
                            })
                        )
                    )
                    : wp.element.createElement(
                        MediaUploadCheck,
                        {},
                        wp.element.createElement(MediaUpload, {
                            onSelect: onSelectImage,
                            allowedTypes: ['image/jpeg', 'image/png', 'image/gif'],
                            render: ({ open }) =>
                                wp.element.createElement(
                                    Button,
                                    { onClick: open },
                                    'Select Image'
                                )
                        })
                    )
            );
        },

        save: function () {
            return null;
        }
    });

})(window.wp);