(function (wp) {
    const { registerBlockType } = wp.blocks;
    const { InspectorControls } = wp.blockEditor;
    const { PanelBody, ToggleControl, TextControl, ColorPalette, RangeControl } = wp.components;
    const { useState } = wp.element;

    const PLATFORMS = [
        { key: 'facebook',  label: 'Facebook' },
        { key: 'instagram', label: 'Instagram' },
        { key: 'twitter',   label: 'X (Twitter)' },
        { key: 'linkedin',  label: 'LinkedIn' },
        { key: 'youtube',   label: 'YouTube' },
        { key: 'tiktok',    label: 'TikTok' },
        { key: 'pinterest', label: 'Pinterest' },
        { key: 'threads',   label: 'Threads' },
    ];

    // Return active platforms in the stored order, with any newly-added ones appended
    function getOrderedActive(attributes) {
        const activePlatforms = PLATFORMS.filter(p => attributes[p.key]);
        const order = attributes.order || [];
        if (!order.length) return activePlatforms;
        const inOrder = order
            .map(key => PLATFORMS.find(p => p.key === key))
            .filter(p => p && attributes[p.key]);
        const rest = activePlatforms.filter(p => !order.includes(p.key));
        return [...inOrder, ...rest];
    }

    function badgeRadius(shape) {
        if (shape === 'circle')  return '50%';
        if (shape === 'rounded') return '8px';
        return '0';
    }

    registerBlockType('email-builder/social', {
        title: 'Social Links',
        icon:  'share',
        category: 'email-builder',
        parent: ['email-builder/column'],

        attributes: {
            align:          { type: 'string',  default: 'center' },
            iconSize:       { type: 'number',  default: 32 },
            iconSpacing:    { type: 'number',  default: 8 },
            badgeColor:     { type: 'string',  default: '#000000' },
            badgeShape:     { type: 'string',  default: 'circle' },
            badgePadding:   { type: 'number',  default: 6 },
            facebook:       { type: 'string',  default: '' },
            instagram:      { type: 'string',  default: '' },
            twitter:        { type: 'string',  default: '' },
            linkedin:       { type: 'string',  default: '' },
            youtube:        { type: 'string',  default: '' },
            tiktok:         { type: 'string',  default: '' },
            pinterest:      { type: 'string',  default: '' },
            threads:        { type: 'string',  default: '' },
            order:          { type: 'array',   default: [] },
            mobileAlign:    { type: 'string',  default: '' },
            mobileIconSize: { type: 'number',  default: null },
            hideOnMobile:   { type: 'boolean', default: false },
        },

        edit: function (props) {
            const { attributes, setAttributes } = props;
            const [viewMode, setViewMode]   = useState('desktop');
            const [dragIndex, setDragIndex] = useState(null);
            const [dragOver, setDragOver]   = useState(null);
            const isMobilePreview   = window.EBUseIsMobilePreview();
            const effectiveAlign    = window.ebMobileVal(attributes.align,    attributes.mobileAlign,    isMobilePreview);
            const effectiveIconSize = window.ebMobileVal(attributes.iconSize, attributes.mobileIconSize, isMobilePreview);

            const orderedActive = getOrderedActive(attributes);
            const pluginUrl = (window.EB_EDITOR_DATA && window.EB_EDITOR_DATA.plugin_url) || '';
            const radius    = badgeRadius(attributes.badgeShape);

            // Pick white or black icon variant based on badge colour luminance
            const _badgeHex  = (attributes.badgeColor || '#000000').replace('#', '');
            const _r = parseInt(_badgeHex.slice(0, 2), 16);
            const _g = parseInt(_badgeHex.slice(2, 4), 16);
            const _b = parseInt(_badgeHex.slice(4, 6), 16);
            const _lum       = (_r * 0.2126 + _g * 0.7152 + _b * 0.0722) / 255;
            const iconVariant = _lum > 0.5 ? '-black' : '';

            function handleDragStart(index) { setDragIndex(index); }
            function handleDragOver(e, index) { e.preventDefault(); setDragOver(index); }
            function handleDrop(index) {
                if (dragIndex === null || dragIndex === index) {
                    setDragIndex(null); setDragOver(null); return;
                }
                const keys = orderedActive.map(p => p.key);
                const [moved] = keys.splice(dragIndex, 1);
                keys.splice(index, 0, moved);
                setAttributes({ order: keys });
                setDragIndex(null); setDragOver(null);
            }
            function handleDragEnd() { setDragIndex(null); setDragOver(null); }

            return wp.element.createElement(
                'div',
                { className: 'eb-social-block', style: { textAlign: effectiveAlign, padding: '10px 0', opacity: (isMobilePreview && attributes.hideOnMobile) ? 0.3 : undefined } },

                wp.element.createElement(
                    InspectorControls,
                    {},

                    // URLs panel
                    wp.element.createElement(
                        PanelBody,
                        { title: 'Settings', initialOpen: true },
                        ...PLATFORMS.map(platform =>
                            wp.element.createElement(
                                'div',
                                { key: platform.key, className: 'eb-field' },
                                wp.element.createElement(TextControl, {
                                    label:       platform.label,
                                    value:       attributes[platform.key],
                                    onChange:    (value) => setAttributes({ [platform.key]: value }),
                                    placeholder: 'https://',
                                    help:        attributes[platform.key] ? '' : 'Leave empty to hide this icon.',
                                })
                            )
                        ),

                        // Drag-to-reorder — only shown when 2+ icons are active
                        orderedActive.length >= 2 && wp.element.createElement(
                            'div',
                            { style: { marginTop: '16px' } },
                            wp.element.createElement(
                                'p',
                                { style: { fontSize: '11px', fontWeight: '600', textTransform: 'uppercase', color: '#1e1e1e', marginBottom: '8px' } },
                                'Drag to reorder'
                            ),
                            ...orderedActive.map((platform, index) =>
                                wp.element.createElement(
                                    'div',
                                    {
                                        key:         platform.key,
                                        draggable:   true,
                                        onDragStart: () => handleDragStart(index),
                                        onDragOver:  (e) => handleDragOver(e, index),
                                        onDrop:      () => handleDrop(index),
                                        onDragEnd:   handleDragEnd,
                                        style: {
                                            display:      'flex',
                                            alignItems:   'center',
                                            gap:          '8px',
                                            padding:      '6px 8px',
                                            marginBottom: '4px',
                                            background:   dragOver === index && dragIndex !== index ? '#d0e8ff' : '#f0f0f0',
                                            borderRadius: '4px',
                                            cursor:       'grab',
                                            opacity:      dragIndex === index ? 0.4 : 1,
                                            border:       dragOver === index && dragIndex !== index ? '1px dashed #0073aa' : '1px solid transparent',
                                            userSelect:   'none',
                                        }
                                    },
                                    wp.element.createElement('span', { style: { fontSize: '16px', color: '#999', lineHeight: 1 } }, '⠿'),
                                    wp.element.createElement('span', { style: { fontSize: '13px' } }, platform.label)
                                )
                            )
                        )
                    ),

                    // Style panel
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
                                attrKey: 'align',
                                attributes,
                                setAttributes,
                                isMobile: false,
                            }),
                            wp.element.createElement(RangeControl, {
                                label:    'Icon Size (px)',
                                value:    attributes.iconSize,
                                min:      16,
                                max:      64,
                                onChange: (value) => setAttributes({ iconSize: value }),
                            }),
                            wp.element.createElement(RangeControl, {
                                label:    'Icon Spacing (px)',
                                value:    attributes.iconSpacing,
                                min:      0,
                                max:      32,
                                onChange: (value) => setAttributes({ iconSpacing: value }),
                            }),
                            wp.element.createElement(RangeControl, {
                                label:    'Badge Padding (px)',
                                value:    attributes.badgePadding,
                                min:      0,
                                max:      12,
                                onChange: (value) => setAttributes({ badgePadding: value }),
                            }),
                            wp.element.createElement(window.EBButtonGroup, {
                                label:    'Badge Shape',
                                value:    attributes.badgeShape,
                                options:  [
                                    { label: 'Circle',  value: 'circle'  },
                                    { label: 'Rounded', value: 'rounded' },
                                    { label: 'Square',  value: 'square'  },
                                ],
                                onChange: (value) => setAttributes({ badgeShape: value }),
                            }),
                            wp.element.createElement(
                                'div',
                                { className: 'eb-field' },
                                wp.element.createElement('label', {}, 'Badge Colour'),
                                wp.element.createElement(ColorPalette, {
                                    value:    attributes.badgeColor,
                                    onChange: (color) => setAttributes({ badgeColor: color || '#000000' }),
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
                            wp.element.createElement(ToggleControl, {
                                label:    'Override Icon Size',
                                checked:  attributes.mobileIconSize !== null && attributes.mobileIconSize !== undefined,
                                onChange: (v) => setAttributes({ mobileIconSize: v ? attributes.iconSize : null }),
                            }),
                            attributes.mobileIconSize !== null && attributes.mobileIconSize !== undefined &&
                                wp.element.createElement(RangeControl, {
                                    value:    attributes.mobileIconSize,
                                    min:      16,
                                    max:      64,
                                    step:     4,
                                    onChange: (v) => setAttributes({ mobileIconSize: v }),
                                }),
                            wp.element.createElement(ToggleControl, {
                                label:    'Hide on Mobile',
                                checked:  attributes.hideOnMobile,
                                onChange: (val) => setAttributes({ hideOnMobile: val }),
                            })
                        )
                    )
                ),

                // Editor preview — actual icon images with badge
                orderedActive.length > 0
                    ? wp.element.createElement(
                        'div',
                        { style: {
                            display:        'inline-flex',
                            flexWrap:       'wrap',
                            justifyContent: effectiveAlign === 'center' ? 'center' : effectiveAlign === 'right' ? 'flex-end' : 'flex-start',
                        } },
                        orderedActive.map(p =>
                            wp.element.createElement(
                                'a',
                                {
                                    key:   p.key,
                                    href:  '#',
                                    title: p.label,
                                    style: {
                                        display:         'inline-block',
                                        backgroundColor: attributes.badgeColor,
                                        borderRadius:    radius,
                                        padding:         attributes.badgePadding + 'px',
                                        margin:          '0 ' + attributes.iconSpacing + 'px',
                                        lineHeight:      '0',
                                        fontSize:        '0',
                                    }
                                },
                                wp.element.createElement('img', {
                                    src:    pluginUrl + 'assets/images/social/' + p.key + iconVariant + '.svg',
                                    alt:    p.label,
                                    width:  effectiveIconSize,
                                    height: effectiveIconSize,
                                    style:  { width: effectiveIconSize + 'px', height: effectiveIconSize + 'px', display: 'block' },
                                })
                            )
                        )
                    )
                    : wp.element.createElement(
                        'p',
                        { style: { color: '#999', fontSize: '12px' } },
                        'Add social URLs in the sidebar to show icons.'
                    )
            );
        },

        save: function () {
            return null;
        }
    });

})(window.wp);
