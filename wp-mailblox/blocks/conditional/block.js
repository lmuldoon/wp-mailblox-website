(function (wp) {

    const { registerBlockType } = wp.blocks;
    const { InnerBlocks, InspectorControls } = wp.blockEditor;
    const { PanelBody } = wp.components;

    registerBlockType('email-builder/conditional', {
        title:    'Conditional Content',
        icon:     'visibility',
        category: 'email-builder',
        description: 'Show or hide inner blocks based on whether a subscriber field has a value.',

        attributes: {
            field: { type: 'string', default: '' },
        },

        edit: function({ attributes, setAttributes }) {

            const conditionalFields = (window.EB_EDITOR_DATA && window.EB_EDITOR_DATA.conditional_fields) || [];
            const platform          = (window.EB_EDITOR_DATA && window.EB_EDITOR_DATA.current_platform) || 'mailchimp';

            const selectedField = conditionalFields.find(function(f) { return f.key === attributes.field; });
            const labelText     = selectedField
                ? 'Shown if: ' + selectedField.label + ' is not empty'
                : 'Conditional Content';

            const noSupport = conditionalFields.length === 0;

            return wp.element.createElement(
                wp.element.Fragment,
                {},

                wp.element.createElement(
                    InspectorControls,
                    {},
                    wp.element.createElement(
                        PanelBody,
                        { title: 'Condition', initialOpen: true },

                        noSupport && wp.element.createElement(
                            'p',
                            { style: { color: '#cc1818', fontSize: '12px', marginBottom: '12px' } },
                            'Your current platform (' + platform + ') does not support conditional content. Switch to Mailchimp, Klaviyo, Brevo, ActiveCampaign, HubSpot, Campaign Monitor, or GetResponse.'
                        ),

                        !noSupport && wp.element.createElement(
                            'div',
                            { className: 'eb-field' },
                            wp.element.createElement(
                                'label',
                                { style: { display: 'block', fontWeight: '600', marginBottom: '6px', fontSize: '11px', textTransform: 'uppercase', letterSpacing: '0.5px' } },
                                'Show this content if…'
                            ),
                            wp.element.createElement(
                                'select',
                                {
                                    value:    attributes.field,
                                    onChange: function(e) { setAttributes({ field: e.target.value }); },
                                    style:    { width: '100%', padding: '6px 8px', borderRadius: '3px', border: '1px solid #ddd', fontSize: '13px' },
                                },
                                wp.element.createElement('option', { value: '' }, '— Select a field —'),
                                conditionalFields.map(function(f) {
                                    return wp.element.createElement('option', { key: f.key, value: f.key }, f.label + ' is not empty');
                                })
                            ),
                            wp.element.createElement(
                                'p',
                                { className: 'description', style: { marginTop: '8px' } },
                                'The inner content will only be shown to subscribers who have a value for the selected field.'
                            )
                        )
                    )
                ),

                // Editor preview — dashed blue border with label
                wp.element.createElement(
                    'div',
                    { className: 'eb-conditional-block' },
                    wp.element.createElement(
                        'span',
                        { className: 'eb-conditional-label' },
                        labelText
                    ),
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
                            'email-builder/html',
                        ],
                    })
                )
            );
        },

        save: function() {
            return wp.element.createElement(InnerBlocks.Content);
        },
    });

})(window.wp);
