(function (wp) {
    const { registerBlockType } = wp.blocks;
    const { InspectorControls } = wp.blockEditor;
    const { PanelBody, ColorPalette, RangeControl, SelectControl, ToggleControl } = wp.components;
    const { useState } = wp.element;

    registerBlockType('email-builder/divider', {
        title: 'Divider',
        icon: 'minus',
        category: 'email-builder',
        parent: ['email-builder/column'],

        attributes: {
            color: {
                type: 'string',
                default: '#cccccc'
            },
            thickness: {
                type: 'number',
                default: 1
            },
            spacing: {
                type: 'number',
                default: 20
            },
            mobileSpacing: {
                type: 'number',
                default: null
            },
            hideOnMobile: {
                type: 'boolean',
                default: false
            },
        },

        edit: ({ attributes, setAttributes }) => {
            const { color, thickness, spacing, mobileSpacing, hideOnMobile } = attributes;
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
                { style: { padding: effectiveSpacing + 'px 0', opacity: (isMobilePreview && hideOnMobile) ? 0.3 : undefined } },

                wp.element.createElement(
                    InspectorControls,
                    {},
                    wp.element.createElement(
                        PanelBody,
                        { title: 'Divider Settings', initialOpen: true },

                        wp.element.createElement(
                            'div',
                            { className: 'eb-field' },
                            wp.element.createElement('label', {}, 'Line Colour'),
                            wp.element.createElement(ColorPalette, {
                                value:    color,
                                onChange: (c) => setAttributes({ color: c })
                            }),
                            wp.element.createElement(
                                'p',
                                { className: 'description' },
                                'Colour of the divider line.'
                            )
                        ),

                        wp.element.createElement(
                            'div',
                            { className: 'eb-field' },
                            wp.element.createElement('label', {}, 'Thickness'),
                            wp.element.createElement(RangeControl, {
                                value:    thickness,
                                onChange: (value) => setAttributes({ thickness: value }),
                                min:      1,
                                max:      10
                            }),
                            wp.element.createElement(
                                'p',
                                { className: 'description' },
                                'Thickness of the divider line in pixels.'
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
                            wp.element.createElement(
                                SelectControl,
                                {
                                    label:    'Spacing',
                                    value:    snappedSpacing,
                                    options:  options,
                                    onChange: (v) => setAttributes({ spacing: parseInt(v) || 0 }),
                                }
                            ),
                            wp.element.createElement(
                                'p',
                                { className: 'description' },
                                'Vertical spacing above and below the divider.'
                            )
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
                                onChange: (value) => setAttributes({ hideOnMobile: value }),
                                help:     'Hide this divider entirely on mobile screens.',
                            })
                        )
                    )
                ),

                // Visual preview in editor
                wp.element.createElement('div', {
                    style: {
                        borderTop: thickness + 'px solid ' + color,
                        width:     '100%'
                    }
                })
            );
        },

        save: () => {
            return null;
        }
    });

})(window.wp);
