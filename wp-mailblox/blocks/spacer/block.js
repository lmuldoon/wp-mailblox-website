(function (wp) {
    const { registerBlockType } = wp.blocks;
    const { InspectorControls } = wp.blockEditor;
    const { PanelBody, SelectControl, ColorPalette, ToggleControl } = wp.components;
    const { useState } = wp.element;

    registerBlockType('email-builder/spacer', {
        title: 'Spacer',
        icon: 'plus',
        category: 'email-builder',
        parent: ['email-builder/column'],

        attributes: {
            spacing: {
                type: 'number',
                default: 20
            },
            mobileSpacing: {
                type: 'number',
                default: null
            },
            bgColor:      { type: 'string',  default: '' },
            darkBgColor:  { type: 'string',  default: '' },
            hideOnMobile: { type: 'boolean', default: false },
        },

        edit: ({ attributes, setAttributes }) => {
            const { spacing, mobileSpacing, bgColor, darkBgColor, hideOnMobile } = attributes;

            const darkSwatches = [
                { color: '#000000', name: 'Pure Black' },
                { color: '#121212', name: 'Material Dark' },
                { color: '#1a1a1a', name: 'Soft Black' },
                { color: '#222222', name: 'Dark Grey' },
            ];
            const [viewMode, setViewMode] = useState('desktop');
            const isMobilePreview = window.EBUseIsMobilePreview();

            const options = window.EB_PADDING_OPTIONS || [];
            const mobileOptions = [{ label: 'Inherit Desktop', value: '' }].concat(options);

            const snappedSpacing = Math.min(50, Math.max(0, Math.round((spacing || 0) / 5) * 5));
            const effectiveSpacing = (isMobilePreview && mobileSpacing !== null && mobileSpacing !== undefined)
                ? Math.min(50, Math.max(0, Math.round(mobileSpacing / 5) * 5))
                : snappedSpacing;
            const mobileVal = (mobileSpacing == null)
                ? ''
                : Math.min(50, Math.max(0, Math.round(mobileSpacing / 5) * 5));

            return wp.element.createElement(
                'div',
                { style: { padding: effectiveSpacing + 'px 0 0', backgroundColor: bgColor || 'transparent', opacity: (isMobilePreview && hideOnMobile) ? 0.3 : undefined } },

                wp.element.createElement(
                    InspectorControls,
                    {},
                    wp.element.createElement(
                        PanelBody,
                        { title: 'Style', initialOpen: true },

                        wp.element.createElement(
                            'div',
                            { className: 'eb-field' },
                            wp.element.createElement('label', {}, 'Background Colour'),
                            wp.element.createElement(
                                'p',
                                { className: 'description', style: { marginBottom: '8px' } },
                                'Leave blank for transparent.'
                            ),
                            wp.element.createElement(ColorPalette, {
                                value:     bgColor,
                                onChange:  (c) => setAttributes({ bgColor: c || '' }),
                                clearable: true,
                            })
                        ),
                        wp.element.createElement(
                            'div',
                            { className: 'eb-field' },
                            wp.element.createElement('label', {}, 'Dark Mode Background'),
                            wp.element.createElement('p', { className: 'description' }, 'Leave empty to use the preset dark mode background.'),
                            wp.element.createElement('div', { style: { display: 'flex', gap: '6px', marginBottom: '8px', marginTop: '8px' } },
                                ...darkSwatches.map(swatch =>
                                    wp.element.createElement('button', {
                                        key:     swatch.color,
                                        type:    'button',
                                        title:   swatch.name + ' ' + swatch.color,
                                        onClick: () => setAttributes({ darkBgColor: swatch.color }),
                                        style: {
                                            backgroundColor: swatch.color,
                                            width:           '28px',
                                            height:          '28px',
                                            border:          darkBgColor === swatch.color ? '2px solid #2271b1' : '2px solid #ccc',
                                            borderRadius:    '4px',
                                            cursor:          'pointer',
                                            padding:         0,
                                        }
                                    })
                                )
                            ),
                            darkBgColor && wp.element.createElement('button', {
                                type:      'button',
                                className: 'components-button is-destructive is-small',
                                style:     { marginTop: '6px' },
                                onClick:   () => setAttributes({ darkBgColor: '' }),
                            }, 'Reset to Preset Default')
                        ),

                        wp.element.createElement(window.EBResponsiveDivider),
                        wp.element.createElement(window.EBResponsiveToggle, {
                            value: viewMode,
                            onChange: setViewMode,
                        }),

                        viewMode === 'desktop' && wp.element.createElement(
                            wp.element.Fragment,
                            {},
                            wp.element.createElement(SelectControl, {
                                label:    'Spacing',
                                value:    snappedSpacing,
                                options:  options,
                                onChange: (v) => setAttributes({ spacing: parseInt(v) || 0 }),
                            })
                        ),

                        viewMode === 'mobile' && wp.element.createElement(
                            wp.element.Fragment,
                            {},
                            wp.element.createElement(
                                'p',
                                { className: 'description', style: { marginBottom: '8px' } },
                                'Leave blank to inherit desktop setting.'
                            ),
                            wp.element.createElement(
                                SelectControl,
                                {
                                    label:    'Spacing',
                                    value:    mobileVal,
                                    options:  mobileOptions,
                                    onChange: (v) => setAttributes({ mobileSpacing: v === '' ? null : parseInt(v) }),
                                }
                            ),
                            wp.element.createElement(ToggleControl, {
                                label:    'Hide on Mobile',
                                checked:  hideOnMobile,
                                onChange: (val) => setAttributes({ hideOnMobile: val }),
                            })
                        )
                    )
                ),

                wp.element.createElement('div', { style: { width: '100%' } })
            );
        },

        save: () => {
            return null;
        }
    });

})(window.wp);
