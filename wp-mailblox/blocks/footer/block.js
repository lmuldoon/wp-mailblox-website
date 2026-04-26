(function (wp) {
    const { registerBlockType } = wp.blocks;
    const { InspectorControls, RichText } = wp.blockEditor;
    const { PanelBody, ToggleControl, TextControl, ColorPalette } = wp.components;

    registerBlockType('email-builder/footer', {
        title: 'Footer',
        icon: 'editor-kitchensink',
        category: 'email-builder',
        parent: ['email-builder/column'],

        attributes: {
            showUnsubscribe: { type: 'boolean', default: true },
            unsubscribeText: { type: 'string', default: 'Unsubscribe' },
            showViewOnline: { type: 'boolean', default: true },
            viewOnlineText: { type: 'string', default: 'View this email online' },
            address: { type: 'string', default: '' },
            footerText: { type: 'string', default: '' },
            textColor: { type: 'string', default: '' },
        },

        edit: function (props) {
            const { attributes, setAttributes } = props;
            const previewColor = attributes.textColor || '#888888';

            return wp.element.createElement(
                'div',
                { className: 'eb-footer-block' },

                // Sidebar controls
                wp.element.createElement(
                    InspectorControls,
                    {},
                    wp.element.createElement(
                        PanelBody,
                        { title: 'Footer Settings', initialOpen: true },

                        // Show Unsubscribe
                        wp.element.createElement(ToggleControl, {
                            label: 'Show Unsubscribe Link',
                            checked: attributes.showUnsubscribe,
                            onChange: (value) => setAttributes({ showUnsubscribe: value })
                        }),

                        // Unsubscribe text
                        attributes.showUnsubscribe && wp.element.createElement(
                            'div',
                            { className: 'eb-field' },
                            wp.element.createElement(TextControl, {
                                label: 'Unsubscribe Link Text',
                                value: attributes.unsubscribeText,
                                onChange: (value) => setAttributes({ unsubscribeText: value })
                            })
                        ),

                        // Show View Online
                        wp.element.createElement(ToggleControl, {
                            label: 'Show View Online Link',
                            checked: attributes.showViewOnline,
                            onChange: (value) => setAttributes({ showViewOnline: value })
                        }),

                        // View Online text
                        attributes.showViewOnline && wp.element.createElement(
                            'div',
                            { className: 'eb-field' },
                            wp.element.createElement(TextControl, {
                                label: 'View Online Link Text',
                                value: attributes.viewOnlineText,
                                onChange: (value) => setAttributes({ viewOnlineText: value })
                            })
                        ),

                        // Address
                        wp.element.createElement(
                            'div',
                            { className: 'eb-field' },
                            wp.element.createElement(TextControl, {
                                label: 'Physical Address',
                                help: 'Required by CAN-SPAM and GDPR. Use <br> for line breaks.',
                                value: attributes.address,
                                onChange: (value) => setAttributes({ address: value })
                            })
                        ),

                        // Font colour override
                        wp.element.createElement(
                            'div',
                            { className: 'eb-field' },
                            wp.element.createElement('label', {}, 'Font Colour'),
                            wp.element.createElement(
                                'p', { className: 'description', style: { marginBottom: '8px' } },
                                'Leave unset to inherit from the section background.'
                            ),
                            wp.element.createElement(ColorPalette, {
                                value:    attributes.textColor,
                                onChange: (color) => setAttributes({ textColor: color || '' }),
                            }),
                            attributes.textColor && wp.element.createElement(
                                'button',
                                {
                                    type:      'button',
                                    className: 'components-button is-destructive is-small',
                                    style:     { marginTop: '4px' },
                                    onClick:   () => setAttributes({ textColor: '' }),
                                },
                                'Reset to Inherited'
                            )
                        ),
                    )
                ),

                // Editor preview
                wp.element.createElement(
                    'div',
                    {
                        style: {
                            // padding: '16px',
                             textAlign: 'center',
                            // fontSize: '12px',
                            // color: '#888',
                            // backgroundColor: '#f9f9f9'
                        }
                    },

                    // Footer copy
                    wp.element.createElement(RichText, {
                        tagName: 'p',
                        value: attributes.footerText,
                        onChange: (value) => setAttributes({ footerText: value }),
                        placeholder: 'Add footer copy e.g. "You\'re receiving this because you signed up at Example.com"',
                        style: { fontSize: '12px', color: previewColor, margin: '0 0 8px' }
                    }),

                    // Links preview
                    (attributes.showUnsubscribe || attributes.showViewOnline) &&
                    wp.element.createElement(
                        'p',
                        { style: { margin: '0 0 8px', fontSize: '12px' } },
                        attributes.showViewOnline && wp.element.createElement(
                            'span',
                            {},
                            wp.element.createElement(
                                'a',
                                { href: '#', style: { color: previewColor } },
                                attributes.viewOnlineText
                            )
                        ),
                        attributes.showViewOnline && attributes.showUnsubscribe &&
                        wp.element.createElement('span', {}, ' | '),
                        attributes.showUnsubscribe && wp.element.createElement(
                            'span',
                            {},
                            wp.element.createElement(
                                'a',
                                { href: '#', style: { color: previewColor } },
                                attributes.unsubscribeText
                            )
                        )
                    ),

                    // Address preview
                    attributes.address && wp.element.createElement(
                        'p',
                        { style: { margin: '0', fontSize: '11px', color: previewColor } },
                        attributes.address
                    )
                )
            );
        },

        save: function () {
            return null;
        }
    });

})(window.wp);