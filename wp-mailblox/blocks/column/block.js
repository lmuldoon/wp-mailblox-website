(function (wp) {
    const { registerBlockType } = wp.blocks;
    const { InnerBlocks, InspectorControls, MediaUpload } = wp.blockEditor;
    const { PanelBody, ToggleControl, ColorPalette } = wp.components;
    const { useSelect } = wp.data;
    const { useState } = wp.element;

    registerBlockType('email-builder/column', {
        title:    'Column',
        icon:     'columns',
        category: 'email-builder',
        parent:   ['email-builder/columns'],
        supports: { inserter: false },

        attributes: {
            backgroundColor:        { type: 'string',  default: '' },
            darkBackgroundColor:    { type: 'string',  default: '' },
            backgroundImageEnabled: { type: 'boolean', default: false },
            backgroundImageUrl:     { type: 'string',  default: '' },
            backgroundRepeat:       { type: 'string',  default: 'no-repeat' },
            backgroundPositionX:    { type: 'string',  default: 'center' },
            backgroundPositionY:    { type: 'string',  default: 'center' },
            backgroundSizeW:        { type: 'string',  default: 'cover' },
            backgroundSizeH:        { type: 'string',  default: 'auto' },
            paddingTop:             { type: 'number',  default: 0 },
            paddingBottom:          { type: 'number',  default: 0 },
            paddingLeft:            { type: 'number',  default: 0 },
            paddingRight:           { type: 'number',  default: 0 },
            mobilePaddingTop:       { type: 'number',  default: null },
            mobilePaddingBottom:    { type: 'number',  default: null },
            mobilePaddingLeft:      { type: 'number',  default: null },
            mobilePaddingRight:     { type: 'number',  default: null },
            borderRadiusTL:         { type: 'number',  default: 0 },
            borderRadiusTR:         { type: 'number',  default: 0 },
            borderRadiusBR:         { type: 'number',  default: 0 },
            borderRadiusBL:         { type: 'number',  default: 0 },
            mobileBorderRadiusTL:   { type: 'number',  default: null },
            mobileBorderRadiusTR:   { type: 'number',  default: null },
            mobileBorderRadiusBR:   { type: 'number',  default: null },
            mobileBorderRadiusBL:   { type: 'number',  default: null },
            borderWidth:            { type: 'number',  default: 0 },
            borderColor:            { type: 'string',  default: '#000000' },
            mobileBorderWidth:      { type: 'number',  default: null },
            mobileBorderColor:      { type: 'string',  default: '' },
        },

        edit: function (props) {
            const { attributes, setAttributes, clientId } = props;
            const {
                backgroundColor,
                darkBackgroundColor,
                backgroundImageEnabled,
                backgroundImageUrl,
                backgroundRepeat,
                backgroundPositionX,
                backgroundPositionY,
                backgroundSizeW,
                backgroundSizeH,
                paddingTop,
                paddingBottom,
                paddingLeft,
                paddingRight,
            } = attributes;

            const bgSizeVal = (backgroundSizeW === 'cover' || backgroundSizeW === 'contain')
                ? backgroundSizeW
                : backgroundSizeW + ' ' + backgroundSizeH;

            const { columnIndex, columnWidths, reverse, gap } = useSelect(select => {
                const { getBlockParents, getBlock, getBlocks } = select('core/block-editor');
                const parents  = getBlockParents(clientId);
                const parentId = parents[parents.length - 1];
                const parent   = parentId ? getBlock(parentId) : null;
                const siblings = parentId ? getBlocks(parentId) : [];
                const index    = siblings.findIndex(b => b.clientId === clientId);
                const widths   = parent?.attributes?.columnWidths ?? [];
                const rev      = parent?.attributes?.reverse ?? false;
                const gapVal   = parent?.attributes?.gap ?? 20;
                return { columnIndex: index, columnWidths: widths, reverse: rev, gap: gapVal };
            }, [clientId]);

            const containerWidth = (window.EB_EDITOR_DATA && window.EB_EDITOR_DATA.container_width) || 640;
            const count          = columnWidths.length || 2;
            const available      = containerWidth - (gap * (count - 1));
            const effectiveIndex = (reverse && count === 2) ? (count - 1 - columnIndex) : columnIndex;
            const pixelWidth     = columnWidths[effectiveIndex] ?? Math.floor(available / count);

            const isMobilePreview = window.EBUseIsMobilePreview();
            const effPaddingTop    = window.ebMobileVal(paddingTop,    attributes.mobilePaddingTop,    isMobilePreview);
            const effPaddingBottom = window.ebMobileVal(paddingBottom, attributes.mobilePaddingBottom, isMobilePreview);
            const effPaddingLeft   = window.ebMobileVal(paddingLeft,   attributes.mobilePaddingLeft,   isMobilePreview);
            const effPaddingRight  = window.ebMobileVal(paddingRight,  attributes.mobilePaddingRight,  isMobilePreview);
            const effRadiusTL    = window.ebMobileVal(attributes.borderRadiusTL || 0, attributes.mobileBorderRadiusTL, isMobilePreview);
            const effRadiusTR    = window.ebMobileVal(attributes.borderRadiusTR || 0, attributes.mobileBorderRadiusTR, isMobilePreview);
            const effRadiusBR    = window.ebMobileVal(attributes.borderRadiusBR || 0, attributes.mobileBorderRadiusBR, isMobilePreview);
            const effRadiusBL    = window.ebMobileVal(attributes.borderRadiusBL || 0, attributes.mobileBorderRadiusBL, isMobilePreview);
            const effBorderWidth = window.ebMobileVal(attributes.borderWidth || 0, attributes.mobileBorderWidth, isMobilePreview);
            const effBorderColor = (isMobilePreview && attributes.mobileBorderColor) ? attributes.mobileBorderColor : (attributes.borderColor || '#000000');

            const [paddingViewMode, setPaddingViewMode] = useState('desktop');

            const darkSwatches = [
                { color: '#000000', name: 'Pure Black' },
                { color: '#121212', name: 'Material Dark' },
                { color: '#1a1a1a', name: 'Soft Black' },
                { color: '#222222', name: 'Dark Grey' },
            ];

            return wp.element.createElement(
                'div',
                {
                    className: 'eb-column-block',
                    style: {
                        flexShrink:         0,
                        minHeight:          '20px',
                        boxSizing:          'border-box',
                        padding:            `${effPaddingTop}px ${effPaddingRight}px ${effPaddingBottom}px ${effPaddingLeft}px`,
                        backgroundColor:    backgroundColor || undefined,
                        borderRadius:       `${effRadiusTL}px ${effRadiusTR}px ${effRadiusBR}px ${effRadiusBL}px`,
                        border:             effBorderWidth > 0 ? `${effBorderWidth}px solid ${effBorderColor}` : '1px dashed #ccc',
                        ...(backgroundImageEnabled && backgroundImageUrl ? {
                            backgroundImage:    `url(${backgroundImageUrl})`,
                            backgroundRepeat:   backgroundRepeat,
                            backgroundPosition: `${backgroundPositionX} ${backgroundPositionY}`,
                            backgroundSize:     bgSizeVal,
                        } : {}),
                    }
                },

                // Inspector controls
                wp.element.createElement(
                    InspectorControls, {},
                    wp.element.createElement(
                        PanelBody, { title: 'Style', initialOpen: true },

                        // Background Colour
                        wp.element.createElement(
                            'div', { className: 'eb-field' },
                            wp.element.createElement('label', {}, 'Background Colour'),
                            wp.element.createElement(ColorPalette, {
                                value:    backgroundColor,
                                onChange: (color) => setAttributes({ backgroundColor: color || '' })
                            }),
                            wp.element.createElement('p', { className: 'description' }, 'Background colour for this column.')
                        ),

                        // Background Image toggle
                        wp.element.createElement(
                            'div', { className: 'eb-field' },
                            wp.element.createElement(ToggleControl, {
                                label:    'Use Background Image',
                                checked:  backgroundImageEnabled,
                                onChange: (value) => setAttributes({ backgroundImageEnabled: value })
                            })
                        ),

                        // Background Image controls
                        backgroundImageEnabled && wp.element.createElement(
                            wp.element.Fragment, {},
                            wp.element.createElement(MediaUpload, {
                                onSelect:     (media) => setAttributes({ backgroundImageUrl: media.url }),
                                allowedTypes: ['image'],
                                render: ({ open }) =>
                                    wp.element.createElement('button', { className: 'components-button is-secondary', onClick: open },
                                        backgroundImageUrl ? 'Replace Image' : 'Select Image'
                                    )
                            }),
                            backgroundImageUrl && wp.element.createElement(
                                'div', { className: 'eb-bg-image-preview' },
                                wp.element.createElement('img', { src: backgroundImageUrl, alt: '' })
                            ),
                            wp.element.createElement(window.EBButtonGroup, {
                                label:    'Background Repeat',
                                value:    backgroundRepeat,
                                options:  [
                                    { label: 'None', value: 'no-repeat' },
                                    { label: 'Both', value: 'repeat' },
                                    { label: 'X',    value: 'repeat-x' },
                                    { label: 'Y',    value: 'repeat-y' },
                                ],
                                onChange: (value) => setAttributes({ backgroundRepeat: value }),
                            }),
                            wp.element.createElement(window.EBButtonGroup, {
                                label:    'Horizontal Position',
                                value:    backgroundPositionX,
                                options:  [
                                    { label: 'Left',   value: 'left' },
                                    { label: 'Center', value: 'center' },
                                    { label: 'Right',  value: 'right' },
                                ],
                                onChange: (value) => setAttributes({ backgroundPositionX: value }),
                            }),
                            wp.element.createElement(window.EBButtonGroup, {
                                label:    'Vertical Position',
                                value:    backgroundPositionY,
                                options:  [
                                    { label: 'Top',    value: 'top' },
                                    { label: 'Center', value: 'center' },
                                    { label: 'Bottom', value: 'bottom' },
                                ],
                                onChange: (value) => setAttributes({ backgroundPositionY: value }),
                            }),
                            wp.element.createElement(window.EBButtonGroup, {
                                label:    'Background Width',
                                value:    backgroundSizeW,
                                options:  [
                                    { label: 'Cover',   value: 'cover' },
                                    { label: 'Contain', value: 'contain' },
                                    { label: 'Auto',    value: 'auto' },
                                    { label: '100%',    value: '100%' },
                                ],
                                onChange: (value) => setAttributes({ backgroundSizeW: value }),
                            }),
                            (backgroundSizeW !== 'cover' && backgroundSizeW !== 'contain') && wp.element.createElement(window.EBButtonGroup, {
                                label:    'Background Height',
                                value:    backgroundSizeH,
                                options:  [
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
                            wp.element.createElement('p', { className: 'description' }, 'Leave empty to inherit from the parent block.'),
                            wp.element.createElement('div', { style: { display: 'flex', gap: '6px', marginBottom: '8px', marginTop: '8px' } },
                                ...darkSwatches.map(swatch =>
                                    wp.element.createElement('button', {
                                        type:    'button',
                                        title:   swatch.name + ' ' + swatch.color,
                                        onClick: () => setAttributes({ darkBackgroundColor: swatch.color }),
                                        style: {
                                            backgroundColor: swatch.color,
                                            width:           '28px',
                                            height:          '28px',
                                            border:          darkBackgroundColor === swatch.color ? '2px solid #2271b1' : '2px solid #ccc',
                                            borderRadius:    '4px',
                                            cursor:          'pointer',
                                            padding:         0,
                                        }
                                    })
                                )
                            ),
                            darkBackgroundColor && wp.element.createElement('button', {
                                type:      'button',
                                className: 'components-button is-destructive is-small',
                                style:     { marginTop: '6px' },
                                onClick:   () => setAttributes({ darkBackgroundColor: '' })
                            }, 'Reset to Inherited')
                        ),

                        // Padding
                        wp.element.createElement(window.EBResponsiveDivider),
                        wp.element.createElement(window.EBResponsiveToggle, {
                            value:    paddingViewMode,
                            onChange: setPaddingViewMode,
                        }),

                        paddingViewMode === 'desktop' && wp.element.createElement(
                            wp.element.Fragment, {},
                            wp.element.createElement(window.EBPaddingFields, {
                                prefix: 'padding', attributes, setAttributes, isMobile: false
                            }),
                            wp.element.createElement(
                                'div', { className: 'eb-field' },
                                wp.element.createElement('label', {}, 'Border Radius (px)'),
                                wp.element.createElement(
                                    'p', { className: 'description', style: { marginBottom: '8px' } },
                                    'Note: Outlook on Windows does not support border-radius.'
                                ),
                                wp.element.createElement(
                                    'div', { style: { display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '6px' } },
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

                        paddingViewMode === 'mobile' && wp.element.createElement(
                            wp.element.Fragment, {},
                            wp.element.createElement(
                                'p', { className: 'description', style: { marginBottom: '8px', color: '#856404', backgroundColor: '#fff3cd', padding: '6px 8px', borderRadius: '3px' } },
                                'Leave blank to inherit desktop setting.'
                            ),
                            wp.element.createElement(window.EBPaddingFields, {
                                prefix: 'mobilePadding', attributes, setAttributes, isMobile: true
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
                                                max:         500,
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
                                'div', { className: 'eb-field', style: { marginTop: '8px' } },
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
                            )
                        )
                    )
                ),

                // Inner blocks
                wp.element.createElement(InnerBlocks, {
                    allowedBlocks: [
                        'email-builder/text',
                        'email-builder/image',
                        'email-builder/button',
                        'email-builder/spacer',
                        'email-builder/divider',
                        'email-builder/header',
                        'email-builder/subheader',
                        'email-builder/logo',
                        'email-builder/social',
                        'email-builder/menu',
                        'email-builder/html',
                        'email-builder/conditional',
                    ],
                    templateLock:   false,
                    renderAppender: () => wp.element.createElement(InnerBlocks.ButtonBlockAppender)
                })
            );
        },

        save: function () {
            return wp.element.createElement(InnerBlocks.Content);
        }
    });

})(window.wp);
