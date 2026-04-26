(function (wp) {

    const {
        registerBlockType
    } = wp.blocks;
    const {
        InspectorControls,
        InnerBlocks,
        MediaUpload
    } = wp.blockEditor;
    const {
        PanelBody,
        SelectControl,
        RangeControl,
        ToggleControl,
        ColorPalette
    } = wp.components;
    const {
        useSelect,
        useDispatch
    } = wp.data;
    const {
        useEffect,
        useState
    } = wp.element;

    registerBlockType('email-builder/columns', {
        title: 'Columns',
        icon: 'columns',
        category: 'email-builder',
        parent: ['email-builder/section'],

        attributes: {
            columns: {
                type: 'number',
                default: 1
            },
            gap: {
                type: 'number',
                default: 20
            },
            columnWidths: {
                type: 'array',
                default: []
            },
            reverse: {
                type: 'boolean',
                default: false
            },
            paddingTop:    { type: 'number', default: 20 },
            paddingBottom: { type: 'number', default: 20 },
            paddingLeft:   { type: 'number', default: 20 },
            paddingRight:  { type: 'number', default: 20 },

            mobilePaddingTop:    { type: 'number', default: null },
            mobilePaddingBottom: { type: 'number', default: null },
            mobilePaddingLeft:   { type: 'number', default: null },
            mobilePaddingRight:  { type: 'number', default: null },
            hideOnMobile:        { type: 'boolean', default: false },
            backgroundColor:      { type: 'string', default: '' },
            darkBackgroundColor:  { type: 'string', default: '' },
            borderRadiusTL:       { type: 'number', default: 0 },
            borderRadiusTR:       { type: 'number', default: 0 },
            borderRadiusBR:       { type: 'number', default: 0 },
            borderRadiusBL:       { type: 'number', default: 0 },
            mobileBorderRadiusTL: { type: 'number', default: null },
            mobileBorderRadiusTR: { type: 'number', default: null },
            mobileBorderRadiusBR: { type: 'number', default: null },
            mobileBorderRadiusBL: { type: 'number', default: null },
            backgroundImageEnabled: { type: 'boolean', default: false },
            backgroundImageUrl:   { type: 'string', default: '' },
            backgroundRepeat:     { type: 'string', default: 'no-repeat' },
            backgroundPositionX:  { type: 'string', default: 'center' },
            backgroundPositionY:  { type: 'string', default: 'center' },
            backgroundSizeW:      { type: 'string', default: 'cover' },
            backgroundSizeH:      { type: 'string', default: 'auto' },
        },

        edit: function (props) {

            const {
                attributes,
                setAttributes,
                clientId
            } = props;
            const {
                columns: columnCount,
                gap,
                columnWidths,
                reverse,
                paddingTop,
                paddingBottom,
                paddingLeft,
                paddingRight,
                backgroundColor,
                darkBackgroundColor,
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

            const darkSwatches = [
                { color: '#000000', name: 'Pure Black' },
                { color: '#121212', name: 'Material Dark' },
                { color: '#1a1a1a', name: 'Soft Black' },
                { color: '#222222', name: 'Dark Grey' },
            ];

            const isMounted = wp.element.useRef(false);
            const [paddingViewMode, setPaddingViewMode] = useState('desktop');
            const isMobilePreview = window.EBUseIsMobilePreview();
            const effPaddingTop    = window.ebMobileVal(paddingTop,    attributes.mobilePaddingTop,    isMobilePreview);
            const effPaddingBottom = window.ebMobileVal(paddingBottom, attributes.mobilePaddingBottom, isMobilePreview);
            const effPaddingLeft   = window.ebMobileVal(paddingLeft,   attributes.mobilePaddingLeft,   isMobilePreview);
            const effPaddingRight  = window.ebMobileVal(paddingRight,  attributes.mobilePaddingRight,  isMobilePreview);
            const effRadiusTL = window.ebMobileVal(attributes.borderRadiusTL || 0, attributes.mobileBorderRadiusTL, isMobilePreview);
            const effRadiusTR = window.ebMobileVal(attributes.borderRadiusTR || 0, attributes.mobileBorderRadiusTR, isMobilePreview);
            const effRadiusBR = window.ebMobileVal(attributes.borderRadiusBR || 0, attributes.mobileBorderRadiusBR, isMobilePreview);
            const effRadiusBL = window.ebMobileVal(attributes.borderRadiusBL || 0, attributes.mobileBorderRadiusBL, isMobilePreview);

            const {
                insertBlock,
                removeBlock
            } = useDispatch('core/block-editor');

            const innerBlocks = useSelect(
                (select) => select('core/block-editor').getBlocks(clientId),
                [clientId]
            );

            // Get container width from preset
            const { containerWidth } = useSelect(() => {
                const presetWidth = window.EB_EDITOR_DATA?.preset?.container_width;
                return { containerWidth: presetWidth || 640 };
            }, [
                clientId,
                window.EB_EDITOR_DATA?.preset?.container_width
            ]);
            // Available width accounts for columns padding and gaps
            function getAvailable(count, gapSize) {
                return containerWidth - paddingLeft - paddingRight - (gapSize * (count - 1));
            }

            // Generate equal pixel widths
            function equalWidths(count, gapSize) {
                const avail = getAvailable(count, gapSize);
                const base = Math.floor(avail / count);
                const rem = avail - (base * count);
                return Array.from({
                        length: count
                    }, (_, i) =>
                    i === count - 1 ? base + rem : base
                );
            }

            // Sync columns when count changes
            useEffect(() => {
                if (!isMounted.current) return;

                const currentCount = innerBlocks.length;

                if (currentCount < columnCount) {
                    for (let i = currentCount; i < columnCount; i++) {
                        insertBlock(
                            wp.blocks.createBlock('email-builder/column'),
                            i,
                            clientId
                        );
                    }
                }

                if (currentCount > columnCount) {
                    for (let i = currentCount; i > columnCount; i--) {
                        const blockToRemove = innerBlocks[i - 1];
                        if (blockToRemove) removeBlock(blockToRemove.clientId);
                    }
                }

                const updates = {
                    columnWidths: equalWidths(columnCount, gap)
                };
                if (columnCount !== 2) updates.reverse = false;
                setAttributes(updates);

            }, [columnCount]);

            // Reset when gap changes
            useEffect(() => {
                if (!isMounted.current) return;
                setAttributes({
                    columnWidths: equalWidths(columnCount, gap)
                });
            }, [gap]);

            // Reset when padding changes — column pixel widths are absolute so they
            // must recalculate whenever the available space changes
            useEffect(() => {
                if (!isMounted.current) return;
                setAttributes({
                    columnWidths: equalWidths(columnCount, gap)
                });
            }, [paddingLeft, paddingRight]);

            // Reset when preset container width changes
            useEffect(() => {
                if (!isMounted.current) return;
                const presetWidth = window.EB_EDITOR_DATA ?.preset ?.container_width;
                if (!presetWidth) return;
                setAttributes({
                    columnWidths: equalWidths(columnCount, gap)
                });
            }, [window.EB_EDITOR_DATA ?.preset ?.container_width]);

            // Mark as mounted AFTER all the above effects have run on the first render.
            // Effects run in definition order, so this fires last on mount and flips the
            // guard — all subsequent re-renders will see isMounted = true.
            useEffect(() => {
                isMounted.current = true;
            }, []);

            // Handle width change — redistribute remaining to last column
            function onWidthChange(index, newValue) {
                const available = getAvailable(columnCount, gap);
                const minWidth = 40;
                const parsed = parseInt(newValue) || minWidth;

                // Max this column can be: leave minWidth for every other column
                const maxForThis = available - ((columnCount - 1) * minWidth);
                const clamped = Math.min(Math.max(parsed, minWidth), maxForThis);

                const newWidths = [...currentWidths];
                newWidths[index] = clamped;

                // Last column absorbs the remainder
                const otherSum = newWidths.reduce(
                    (sum, w, i) => i === columnCount - 1 ? sum : sum + w,
                    0
                );
                newWidths[columnCount - 1] = Math.max(minWidth, available - otherSum);

                setAttributes({
                    columnWidths: newWidths
                });
            }

            const available = getAvailable(columnCount, gap);
            const currentWidths = (columnWidths.length === columnCount) ?
                columnWidths :
                equalWidths(columnCount, gap);

            const columnVars = columnCount === 1
                ? { '--column-gap': '0', '--column-1-width': '100%' }
                : { '--column-gap': gap + 'px' };

            if (columnCount > 1) {
                currentWidths.forEach((width, i) => {
                    const names = ['one', 'two', 'three', 'four'];
                    if (names[i]) {
                        columnVars[`--column-${i + 1}-width`] = width + 'px';
                    }
                });
            }

            const totalUsed = currentWidths.reduce((sum, w) => sum + w, 0);
            const isValid = totalUsed === available;

            return wp.element.createElement(
                'div', {
                    className: 'eb-columns-block',
                    style: {
                    width: containerWidth + 'px',
                            margin: '0 auto',
                    opacity: (isMobilePreview && attributes.hideOnMobile) ? 0.3 : undefined,
                    }
                },

                wp.element.createElement(
                    InspectorControls, {},

                    // Settings panel
                    wp.element.createElement(
                        PanelBody, {
                            title: 'Settings',
                            initialOpen: true
                        },

                        // Column count
                        wp.element.createElement(
                            'div', {
                                className: 'eb-field'
                            },
                            wp.element.createElement('label', {}, 'Number of Columns'),
                            wp.element.createElement(SelectControl, {
                                value: columnCount,
                                options: (function() {
                                    const isPro = !!(window.EB_EDITOR_DATA && window.EB_EDITOR_DATA.is_pro);
                                    const base = [
                                        { label: '1 Column',  value: 1 },
                                        { label: '2 Columns', value: 2 },
                                    ];
                                    if (isPro) {
                                        base.push({ label: '3 Columns', value: 3 });
                                        base.push({ label: '4 Columns', value: 4 });
                                    }
                                    return base;
                                })(),
                                onChange: (value) => setAttributes({
                                    columns: parseInt(value)
                                })
                            }),
                            wp.element.createElement(
                                'p', {
                                    className: 'description'
                                },
                                !!(window.EB_EDITOR_DATA && window.EB_EDITOR_DATA.is_pro)
                                    ? 'Select how many columns to display.'
                                    : wp.element.createElement(wp.element.Fragment, {},
                                        '3 and 4 columns require WP Mailblox Pro. ',
                                        wp.element.createElement('a', {
                                            href: (window.EB_EDITOR_DATA && window.EB_EDITOR_DATA.upgrade_url) || '#',
                                            style: { fontWeight: '600' }
                                        }, 'Upgrade →')
                                    )
                            )
                        ),

                        // Gap
                        columnCount > 1 && wp.element.createElement(RangeControl, {
                            label: 'Gap Between Columns (px)',
                            value: gap,
                            min: 0,
                            max: 60,
                            onChange: (value) => setAttributes({
                                gap: value
                            }),
                        }),

                        // Reverse (2 columns only)
                        columnCount === 2 && wp.element.createElement(ToggleControl, {
                            label: 'Reverse Column Order',
                            checked: reverse,
                            onChange: (value) => setAttributes({
                                reverse: value
                            }),
                        }),

                        // Column widths
                        columnCount > 1 && wp.element.createElement(
                            'div', {
                                className: 'eb-field'
                            },

                            wp.element.createElement('label', {}, 'Column Widths'),

                            // Summary bar
                            wp.element.createElement(
                                'div', {
                                    style: {
                                        display: 'flex',
                                        height: '8px',
                                        borderRadius: '4px',
                                        overflow: 'hidden',
                                        margin: '8px 0',
                                        background: '#eee',
                                    }
                                },
                                ...currentWidths.map((w, i) => {
                                    const colors = ['#2271b1', '#72aee6', '#a7c9e8', '#d0e4f5'];
                                    return wp.element.createElement('div', {
                                        key: i,
                                        style: {
                                            width: (w / available * 100) + '%',
                                            background: colors[i % colors.length],
                                        }
                                    });
                                })
                            ),

                            wp.element.createElement(
                                'p', {
                                    className: 'description',
                                    style: {
                                        marginBottom: '8px'
                                    }
                                },
                                'Available: ' + available + 'px  |  Used: ' + totalUsed + 'px'
                            ),

                            ...Array.from({
                                    length: columnCount
                                }, (_, i) =>
                                wp.element.createElement(
                                    'div', {
                                        key: i,
                                        style: {
                                            display: 'flex',
                                            alignItems: 'center',
                                            gap: '8px',
                                            marginBottom: '6px'
                                        }
                                    },

                                    // Colour dot matching summary bar
                                    wp.element.createElement('div', {
                                        style: {
                                            width: '10px',
                                            height: '10px',
                                            borderRadius: '50%',
                                            flexShrink: 0,
                                            background: ['#2271b1', '#72aee6', '#a7c9e8', '#d0e4f5'][i % 4],
                                        }
                                    }),

                                    wp.element.createElement(
                                        'label', {
                                            style: {
                                                minWidth: '60px',
                                                fontSize: '12px'
                                            }
                                        },
                                        'Col ' + (i + 1)
                                    ),

                                    wp.element.createElement('input', {
                                        type: 'number',
                                        min: 40,
                                        max: available - ((columnCount - 1) * 40),
                                        value: currentWidths[i] ?? Math.floor(available / columnCount),
                                        onChange: (e) => onWidthChange(i, e.target.value),
                                        disabled: i === columnCount - 1,
                                        className: 'widefat',
                                        style: {
                                            width: '70px',
                                            opacity: i === columnCount - 1 ? 0.6 : 1,
                                            backgroundColor: i === columnCount - 1 ? '#f0f0f0' : '#fff',
                                        }
                                    }),

                                    wp.element.createElement(
                                        'span', {
                                            style: {
                                                fontSize: '12px',
                                                color: '#666'
                                            }
                                        },
                                        'px'
                                    ),

                                    // Percentage hint
                                    wp.element.createElement(
                                        'span', {
                                            style: {
                                                fontSize: '11px',
                                                color: '#aaa'
                                            }
                                        },
                                        '(' + Math.round(currentWidths[i] / available * 100) + '%)'
                                    )
                                )
                            ),

                            // Valid/invalid indicator
                            wp.element.createElement(
                                'p', {
                                    style: {
                                        fontSize: '11px',
                                        marginTop: '4px',
                                        fontWeight: '600',
                                        color: isValid ? '#468847' : '#cc0000',
                                    }
                                },
                                isValid ?
                                '✓ Columns fill available width' :
                                '⚠ Total ' + totalUsed + 'px — should be ' + available + 'px'
                            ),

                            wp.element.createElement(
                                'button', {
                                    type: 'button',
                                    className: 'components-button is-secondary is-small',
                                    style: {
                                        marginTop: '6px'
                                    },
                                    onClick: () => setAttributes({
                                        columnWidths: equalWidths(columnCount, gap)
                                    })
                                },
                                'Reset to Equal'
                            )
                        )
                    ),

                    // Style panel
                    wp.element.createElement(
                        PanelBody, {
                            title: 'Style',
                            initialOpen: false
                        },

                        // Background Color
                        wp.element.createElement(
                            'div', { className: 'eb-field' },
                            wp.element.createElement('label', {}, 'Background Colour'),
                            wp.element.createElement(ColorPalette, {
                                value: backgroundColor,
                                onChange: (color) => setAttributes({ backgroundColor: color || '' })
                            }),
                            wp.element.createElement('p', { className: 'description' }, 'Background colour of this container.')
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
                                    { label: 'None',      value: 'no-repeat' },
                                    { label: 'Both',      value: 'repeat' },
                                    { label: 'X',         value: 'repeat-x' },
                                    { label: 'Y',         value: 'repeat-y' },
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

                        // Padding inputs with desktop/mobile toggle
                        wp.element.createElement(window.EBResponsiveDivider),
                        wp.element.createElement(window.EBResponsiveToggle, {
                            value: paddingViewMode,
                            onChange: setPaddingViewMode,
                        }),

                        paddingViewMode === 'desktop' && wp.element.createElement(
                            wp.element.Fragment, {},
                            wp.element.createElement(window.EBPaddingFields, {
                                prefix:    'padding',
                                attributes,
                                setAttributes,
                                isMobile: false,
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
                                                max:       500,
                                                value:     attributes[key] || 0,
                                                onChange:  (e) => setAttributes({ [key]: Math.max(0, parseInt(e.target.value) || 0) }),
                                                className: 'widefat',
                                            })
                                        )
                                    )
                                )
                            )
                        ),

                        paddingViewMode === 'mobile' && wp.element.createElement(
                            wp.element.Fragment, {},
                            wp.element.createElement(ToggleControl, {
                                label:    'Hide on Mobile',
                                checked:  attributes.hideOnMobile,
                                onChange: (v) => setAttributes({ hideOnMobile: v }),
                            }),
                            wp.element.createElement(
                                'p', { className: 'description', style: { marginBottom: '8px', color: '#856404', backgroundColor: '#fff3cd', padding: '6px 8px', borderRadius: '3px' } },
                                'Visual only — does not affect column widths. Leave blank to inherit desktop setting.'
                            ),
                            wp.element.createElement(window.EBPaddingFields, {
                                prefix:    'mobilePadding',
                                attributes,
                                setAttributes,
                                isMobile: true,
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
                            )
                        )
                    )
                ),

                // Preview
                wp.element.createElement(
                    'div', {
                        className: reverse ? 'eb-columns-reversed' : 'eb-columns-standard' ,
                        style: {
                            width: '100%',
                            margin: '0 auto',
                            padding: `${effPaddingTop}px ${effPaddingRight}px ${effPaddingBottom}px ${effPaddingLeft}px`,
                            backgroundColor: backgroundColor || undefined,
                            borderRadius: `${effRadiusTL}px ${effRadiusTR}px ${effRadiusBR}px ${effRadiusBL}px`,
                            ...(backgroundImageEnabled && backgroundImageUrl ? {
                                backgroundImage: `url(${backgroundImageUrl})`,
                                backgroundRepeat: backgroundRepeat,
                                backgroundPosition: `${backgroundPositionX} ${backgroundPositionY}`,
                                backgroundSize: bgSizeVal,
                            } : {}),
                            ...columnVars
                        }
                    },
                    wp.element.createElement(InnerBlocks, {
                        allowedBlocks: ['email-builder/column'],
                        template: Array.from({ length: columnCount }, () => ['email-builder/column', {}]),
                        templateLock: false,
                        renderAppender: false
                    })
                )
            );
        },

        save: function () {
            return wp.element.createElement(InnerBlocks.Content);
        }
    });

})(window.wp);