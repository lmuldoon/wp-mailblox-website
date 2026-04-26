(function (wp) {
    const { registerBlockType } = wp.blocks;
    const { InspectorControls } = wp.blockEditor;
    const { PanelBody } = wp.components;

    registerBlockType('email-builder/html', {
        title:    'Custom HTML',
        icon:     'html',
        category: 'email-builder',
        parent:   ['email-builder/column'],

        attributes: {
            content: { type: 'string', default: '' },
        },

        edit: function ({ attributes, setAttributes }) {
            const { content } = attributes;

            return wp.element.createElement(
                'div',
                { className: 'eb-html-block' },

                // Textarea in sidebar
                wp.element.createElement(
                    InspectorControls,
                    {},
                    wp.element.createElement(
                        PanelBody,
                        { title: 'Custom HTML', initialOpen: true },
                        wp.element.createElement(
                            'p',
                            { className: 'description', style: { marginBottom: '8px' } },
                            'HTML is rendered verbatim in the exported email. Use email-safe, table-based markup.'
                        ),
                        wp.element.createElement('textarea', {
                            value:       content,
                            onChange:    (e) => setAttributes({ content: e.target.value }),
                            placeholder: '<!-- Your custom HTML here -->',
                            rows:        16,
                            spellCheck:  false,
                            style: {
                                width:        '100%',
                                fontFamily:   'monospace',
                                fontSize:     '12px',
                                background:   '#1e1e1e',
                                color:        '#d4d4d4',
                                border:       'none',
                                padding:      '10px',
                                display:      'block',
                                resize:       'vertical',
                                boxSizing:    'border-box',
                                borderRadius: '3px',
                            }
                        })
                    )
                ),

                // Canvas placeholder
                wp.element.createElement(
                    'div',
                    {
                        style: {
                            padding:      '10px 12px',
                            background:   '#f6f7f7',
                            border:       '1px dashed #ccc',
                            borderRadius: '3px',
                            display:      'flex',
                            alignItems:   'center',
                            gap:          '8px',
                        }
                    },
                    wp.element.createElement(
                        'span',
                        {
                            style: {
                                fontSize:        '10px',
                                fontWeight:      '600',
                                textTransform:   'uppercase',
                                color:           '#fff',
                                backgroundColor: '#1e1e1e',
                                padding:         '2px 6px',
                                borderRadius:    '3px',
                                flexShrink:      0,
                            }
                        },
                        'HTML'
                    ),
                    wp.element.createElement(
                        'span',
                        { style: { fontSize: '12px', color: '#666' } },
                        content
                            ? content.replace(/\s+/g, ' ').trim().substring(0, 80) + (content.length > 80 ? '…' : '')
                            : 'No HTML yet — add it in the sidebar.'
                    )
                )
            );
        },

        save: function () {
            return null;
        },
    });

})(window.wp);
