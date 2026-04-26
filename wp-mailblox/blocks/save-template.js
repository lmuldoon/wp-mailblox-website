(function (wp) {

    const { useState } = wp.element;
    const { Modal, Button, TextControl } = wp.components;
    const { useSelect } = wp.data;

    function SaveTemplateButton() {
        const [isOpen,         setIsOpen]         = useState(false);
        const [name,           setName]           = useState('');
        const [tags,           setTags]           = useState('');
        const [category,       setCategory]       = useState('');
        const [newCategory,    setNewCategory]    = useState('');
        const [allCategories,  setAllCategories]  = useState([]);
        const [saving,         setSaving]         = useState(false);
        const [saved,          setSaved]          = useState(false);
        const [error,          setError]          = useState('');

        const postType = useSelect(select =>
            select('core/editor').getCurrentPostType()
        );

        const blocks = useSelect(select =>
            select('core/block-editor').getBlocks()
        );

        if (postType !== 'eb_email_template') return null;

        const savedCount = (window.EB_EDITOR_DATA && window.EB_EDITOR_DATA.saved_template_count) || 0;
        const savedLimit = (window.EB_EDITOR_DATA && window.EB_EDITOR_DATA.saved_template_limit) || 10;
        const atLimit    = savedCount >= savedLimit;

        function openModal() {
            setName('');
            setTags('');
            setCategory('');
            setNewCategory('');
            setError('');
            setSaved(false);
            setIsOpen(true);

            // Fetch existing categories to populate the dropdown
            wp.apiFetch({ path: '/email-builder/v1/saved-templates' }).then(data => {
                setAllCategories(data.all_categories || []);
            }).catch(() => {});
        }

        function doSave() {
            const trimmed = name.trim();
            if (!trimmed) {
                setError('Please enter a name for the template.');
                return;
            }

            setSaving(true);
            setError('');

            const tagList      = tags.split(',').map(t => t.trim()).filter(Boolean);
            const categoryName = category === '__new__' ? newCategory.trim() : category;

            wp.apiFetch({
                path:   '/email-builder/v1/saved-templates',
                method: 'POST',
                data: {
                    name:     trimmed,
                    blocks:   JSON.stringify(blocks),
                    tags:     tagList,
                    category: categoryName,
                },
            }).then(() => {
                setSaving(false);
                setSaved(true);
            }).catch((err) => {
                setSaving(false);
                setError(err.message || 'Could not save template. Please try again.');
            });
        }

        return wp.element.createElement(
            wp.element.Fragment,
            {},

            // Toolbar button
            wp.element.createElement(
                Button,
                {
                    variant:  'tertiary',
                    size:     'compact',
                    icon:     'media-document',
                    label:    atLimit ? (savedCount + '/' + savedLimit + ' saved templates used.') : 'Save as Template',
                    onClick:  atLimit ? undefined : openModal,
                    disabled: atLimit,
                }
            ),

            isOpen && wp.element.createElement(
                Modal,
                {
                    title:           'Save as Template',
                    onRequestClose:  () => setIsOpen(false),
                    style:           { maxWidth: '420px', width: '100%' },
                },

                saved
                    ? wp.element.createElement(
                        'div',
                        { style: { textAlign: 'center', padding: '16px 0' } },
                        wp.element.createElement(
                            'p',
                            { style: { fontSize: '32px', margin: '0 0 12px' } },
                            '✅'
                        ),
                        wp.element.createElement(
                            'p',
                            { style: { fontWeight: '600', marginBottom: '4px' } },
                            'Template saved!'
                        ),
                        wp.element.createElement(
                            'p',
                            { style: { color: '#666', fontSize: '13px', marginBottom: '20px' } },
                            '"' + name.trim() + '" is now available in My Templates.'
                        ),
                        wp.element.createElement(
                            Button,
                            { variant: 'primary', onClick: () => setIsOpen(false) },
                            'Done'
                        )
                    )
                    : wp.element.createElement(
                        'div',
                        {},
                        wp.element.createElement(
                            'p',
                            { style: { color: '#666', marginBottom: '16px', fontSize: '13px' } },
                            'This will save the current layout and content as a reusable template. The preset is not saved — the template will use whichever preset is active when it is applied.'
                        ),
                        wp.element.createElement(TextControl, {
                            label:       'Template Name',
                            value:       name,
                            onChange:    setName,
                            placeholder: 'e.g. Monthly Newsletter',
                            autoFocus:   true,
                        }),
                        wp.element.createElement(TextControl, {
                            label:       'Tags (optional)',
                            value:       tags,
                            onChange:    setTags,
                            placeholder: 'e.g. promotional, woocommerce',
                            help:        'Comma-separated. Used to filter templates in the chooser.',
                        }),

                        // Category select
                        wp.element.createElement(
                            'div',
                            { style: { marginBottom: '16px' } },
                            wp.element.createElement(
                                'label',
                                { style: { display: 'block', fontWeight: '600', marginBottom: '4px', fontSize: '11px', textTransform: 'uppercase', letterSpacing: '0.5px' } },
                                'Category (optional)'
                            ),
                            wp.element.createElement(
                                'select',
                                {
                                    value:    category,
                                    onChange: (e) => { setCategory(e.target.value); if (e.target.value !== '__new__') setNewCategory(''); },
                                    style:    { width: '100%', padding: '6px 8px', borderRadius: '3px', border: '1px solid #ddd', fontSize: '13px' },
                                },
                                wp.element.createElement('option', { value: '' }, '— No category —'),
                                allCategories.map(cat => wp.element.createElement('option', { key: cat, value: cat }, cat)),
                                wp.element.createElement('option', { value: '__new__' }, '+ Create new category…')
                            ),
                            category === '__new__' && wp.element.createElement(TextControl, {
                                value:       newCategory,
                                onChange:    setNewCategory,
                                placeholder: 'New category name',
                                style:       { marginTop: '8px' },
                            })
                        ),
                        error && wp.element.createElement(
                            'p',
                            { style: { color: '#cc1818', fontSize: '13px', marginTop: '-8px', marginBottom: '8px' } },
                            error
                        ),
                        wp.element.createElement(
                            'div',
                            { style: { display: 'flex', justifyContent: 'flex-end', gap: '8px', marginTop: '16px' } },
                            wp.element.createElement(
                                Button,
                                { variant: 'tertiary', onClick: () => setIsOpen(false), disabled: saving },
                                'Cancel'
                            ),
                            wp.element.createElement(
                                Button,
                                {
                                    variant:  'primary',
                                    onClick:  doSave,
                                    isBusy:   saving,
                                    disabled: saving || !name.trim(),
                                },
                                saving ? 'Saving...' : 'Save Template'
                            )
                        )
                    )
            )
        );
    }

    // Expose component globally so the email-settings-sidebar toolbar can render it.
    window.EBSaveTemplateButton = SaveTemplateButton;

})(window.wp);
