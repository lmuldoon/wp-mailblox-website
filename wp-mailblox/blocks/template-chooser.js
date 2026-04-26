(function (wp) {

    const { useState, useEffect } = wp.element;
    const { Modal, Button, Spinner } = wp.components;
    const { useSelect, useDispatch } = wp.data;

    function TemplateChooser() {

        const [isOpen,    setIsOpen]    = useState(false);
        const [tab,       setTab]       = useState('starter');  // 'starter' | 'saved'
        const [starters,  setStarters]  = useState([]);
        const [saved,     setSaved]     = useState([]);
        const [allTags,        setAllTags]        = useState([]);
        const [activeTag,      setActiveTag]      = useState('');
        const [allCategories,  setAllCategories]  = useState([]);
        const [activeCategory, setActiveCategory] = useState('');
        const [loading,   setLoading]   = useState(true);
        const [selected,  setSelected]  = useState(null);
        const [applying,  setApplying]  = useState(false);
        const [deleting,  setDeleting]  = useState(null);

        const { replaceBlocks, insertBlocks } = useDispatch('core/block-editor');

        const isPro     = !!(window.EB_EDITOR_DATA && window.EB_EDITOR_DATA.is_pro);
        const upgradeUrl = (window.EB_EDITOR_DATA && window.EB_EDITOR_DATA.upgrade_url) || '';

        const postType  = useSelect(select => select('core/editor').getCurrentPostType());
        const isNewPost = useSelect(select => select('core/editor').isCleanNewPost());

        useEffect(() => {
            if (postType === 'eb_email_template' && isNewPost) {
                setIsOpen(true);
                fetchAll();
            }
        }, [postType, isNewPost]);

        function fetchAll() {
            setLoading(true);
            Promise.all([
                wp.apiFetch({ path: '/email-builder/v1/starter-templates' }).catch(() => []),
                wp.apiFetch({ path: '/email-builder/v1/saved-templates' }).catch(() => ({ templates: [], all_tags: [] })),
            ]).then(([starterData, savedData]) => {
                setStarters(starterData || []);
                setSaved(savedData.templates || []);
                setAllTags(savedData.all_tags || []);
                setAllCategories(savedData.all_categories || []);
                setLoading(false);
            });
        }

        function fetchSaved(tag, category) {
            const params = [];
            if (tag)      params.push('tag='      + encodeURIComponent(tag));
            if (category) params.push('category=' + encodeURIComponent(category));
            const path = '/email-builder/v1/saved-templates' + (params.length ? '?' + params.join('&') : '');
            wp.apiFetch({ path }).then(data => {
                setSaved(data.templates || []);
                setAllTags(data.all_tags || []);
                setAllCategories(data.all_categories || []);
            }).catch(() => {});
        }

        function createBlocks(blockDefs) {
            return blockDefs.map(def => {
                // Support both name/attributes (starter template format)
                // and blockName/attrs (parse_blocks / exported format)
                const name        = def.name || def.blockName;
                const attrs       = def.attributes || def.attrs || {};
                const innerBlocks = def.innerBlocks ? createBlocks(def.innerBlocks) : [];
                return wp.blocks.createBlock(name, attrs, innerBlocks);
            });
        }

        function applyTemplate() {
            if (!selected) return;
            setApplying(true);

            const list    = tab === 'starter' ? starters : saved;
            const idKey   = tab === 'starter' ? 'slug' : 'id';
            const template = list.find(t => t[idKey] === selected);

            if (!template || !template.blocks || (template.pro && !isPro)) {
                setApplying(false);
                return;
            }

            const newBlocks      = createBlocks(template.blocks);
            const existingBlocks = wp.data.select('core/block-editor').getBlocks();

            if (existingBlocks.length > 0) {
                replaceBlocks(existingBlocks.map(b => b.clientId), newBlocks);
            } else {
                insertBlocks(newBlocks);
            }

            setIsOpen(false);
            setApplying(false);
        }

        function deleteTemplate(id, e) {
            e.stopPropagation();
            if (!window.confirm('Delete this saved template? This cannot be undone.')) return;
            setDeleting(id);
            wp.apiFetch({
                path:   '/email-builder/v1/saved-templates/' + id,
                method: 'DELETE',
            }).then(() => {
                setSaved(prev => prev.filter(t => t.id !== id));
                if (selected === id) setSelected(null);
                setDeleting(null);
            }).catch(() => setDeleting(null));
        }

        if (!isOpen) return null;

        const tabStyle = (active) => ({
            padding:         '8px 16px',
            fontWeight:      active ? '600' : '400',
            borderBottom:    active ? '2px solid #2271b1' : '2px solid transparent',
            color:           active ? '#2271b1' : '#666',
            background:      'none',
            border:          'none',
            borderBottom:    active ? '2px solid #2271b1' : '2px solid transparent',
            cursor:          'pointer',
            fontSize:        '13px',
            marginBottom:    '-1px',
        });

        const currentList  = tab === 'starter' ? starters : saved;
        const isEmpty      = !loading && currentList.length === 0;

        function onTagChange(tag) {
            setActiveTag(tag);
            setSelected(null);
            fetchSaved(tag, activeCategory);
        }

        function onCategoryChange(category) {
            setActiveCategory(category);
            setSelected(null);
            fetchSaved(activeTag, category);
        }

        return wp.element.createElement(
            Modal,
            {
                title:          'Choose a Template',
                onRequestClose: () => setIsOpen(false),
                style:          { maxWidth: '820px', width: '100%' },
            },

            wp.element.createElement(
                'div',
                {},

                // Tabs
                wp.element.createElement(
                    'div',
                    { style: { display: 'flex', borderBottom: '1px solid #ddd', marginBottom: '20px' } },
                    wp.element.createElement('button', { style: tabStyle(tab === 'starter'), onClick: () => { setTab('starter'); setSelected(null); } }, 'Starter Templates'),
                    wp.element.createElement('button', { style: tabStyle(tab === 'saved'),   onClick: () => { setTab('saved');   setSelected(null); } },
                        'My Templates' + (saved.length > 0 ? ' (' + saved.length + ')' : '')
                    )
                ),

                // Category + Tag filters — only shown on My Templates tab
                tab === 'saved' && (allCategories.length > 0 || allTags.length > 0) && wp.element.createElement(
                    'div',
                    { style: { marginBottom: '16px', display: 'flex', alignItems: 'center', gap: '16px', flexWrap: 'wrap' } },

                    // Category filter
                    allCategories.length > 0 && wp.element.createElement(
                        'div',
                        { style: { display: 'flex', alignItems: 'center', gap: '8px' } },
                        wp.element.createElement('span', { style: { fontSize: '12px', color: '#757575', flexShrink: 0 } }, 'Category:'),
                        wp.element.createElement(
                            'select',
                            {
                                value:    activeCategory,
                                onChange: (e) => onCategoryChange(e.target.value),
                                style:    { fontSize: '12px', padding: '3px 6px', borderRadius: '3px', border: '1px solid #ccc' },
                            },
                            wp.element.createElement('option', { value: '' }, 'All'),
                            allCategories.map(cat => wp.element.createElement('option', { key: cat, value: cat }, cat))
                        ),
                        activeCategory && wp.element.createElement(
                            'button',
                            {
                                type:    'button',
                                onClick: () => onCategoryChange(''),
                                style:   { background: 'none', border: 'none', cursor: 'pointer', color: '#757575', fontSize: '12px', padding: '0' },
                            },
                            '✕'
                        )
                    ),

                    // Tag filter
                    allTags.length > 0 && wp.element.createElement(
                        'div',
                        { style: { display: 'flex', alignItems: 'center', gap: '8px' } },
                        wp.element.createElement('span', { style: { fontSize: '12px', color: '#757575', flexShrink: 0 } }, 'Tag:'),
                        wp.element.createElement(
                            'select',
                            {
                                value:    activeTag,
                                onChange: (e) => onTagChange(e.target.value),
                                style:    { fontSize: '12px', padding: '3px 6px', borderRadius: '3px', border: '1px solid #ccc' },
                            },
                            wp.element.createElement('option', { value: '' }, 'All'),
                            allTags.map(tag => wp.element.createElement('option', { key: tag, value: tag }, tag))
                        ),
                        activeTag && wp.element.createElement(
                            'button',
                            {
                                type:    'button',
                                onClick: () => onTagChange(''),
                                style:   { background: 'none', border: 'none', cursor: 'pointer', color: '#757575', fontSize: '12px', padding: '0' },
                            },
                            '✕'
                        )
                    )
                ),

                // Loading
                loading && wp.element.createElement(
                    'div', { style: { textAlign: 'center', padding: '40px' } },
                    wp.element.createElement(Spinner)
                ),

                // Empty state for My Templates
                !loading && isEmpty && tab === 'saved' && wp.element.createElement(
                    'div',
                    { style: { textAlign: 'center', padding: '40px 20px', color: '#666' } },
                    wp.element.createElement('p', { style: { fontSize: '32px', margin: '0 0 12px' } }, '📂'),
                    wp.element.createElement('p', { style: { fontWeight: '600', marginBottom: '4px' } }, 'No saved templates yet'),
                    wp.element.createElement('p', { style: { fontSize: '13px' } }, 'Open any email and use "Save as Template" in the sidebar to save your layouts here.')
                ),

                // Empty state for starters
                !loading && isEmpty && tab === 'starter' && wp.element.createElement(
                    'div',
                    { style: { textAlign: 'center', padding: '40px', color: '#666' } },
                    wp.element.createElement('p', {}, 'No starter templates found.')
                ),

                // Grid
                !loading && !isEmpty && wp.element.createElement(
                    'div',
                    {
                        style: {
                            display:             'grid',
                            gridTemplateColumns: 'repeat(3, 1fr)',
                            gap:                 '16px',
                            marginBottom:        '24px',
                            maxHeight:           '420px',
                            overflowY:           'auto',
                        }
                    },
                    currentList.map(template => {
                        const id         = tab === 'starter' ? template.slug : template.id;
                        const isActive   = selected === id;
                        const isProLocked = tab === 'starter' && template.pro && !isPro;
                        return wp.element.createElement(
                            'button',
                            {
                                key:      id,
                                type:     'button',
                                onClick:  isProLocked
                                    ? (upgradeUrl ? () => { window.open(upgradeUrl, '_blank'); } : undefined)
                                    : () => setSelected(id),
                                disabled: false,
                                style: {
                                    padding:      '16px',
                                    border:       isActive ? '2px solid #2271b1' : '2px solid #ddd',
                                    borderRadius: '8px',
                                    background:   isProLocked ? '#fafafa' : (isActive ? '#f0f6fc' : '#fff'),
                                    cursor:       isProLocked ? 'pointer' : 'pointer',
                                    textAlign:    'left',
                                    position:     'relative',
                                    opacity:      isProLocked ? 0.75 : 1,
                                }
                            },

                            // Pro badge
                            isProLocked && wp.element.createElement(
                                'span',
                                {
                                    style: {
                                        position:     'absolute',
                                        top:          '10px',
                                        right:        '10px',
                                        background:   '#f0ad00',
                                        color:        '#fff',
                                        fontSize:     '10px',
                                        fontWeight:   '700',
                                        padding:      '2px 7px',
                                        borderRadius: '3px',
                                        letterSpacing: '0.5px',
                                        lineHeight:   '1.6',
                                        zIndex:       1,
                                    }
                                },
                                'PRO'
                            ),

                            // Icon / preview area
                            wp.element.createElement(
                                'div',
                                {
                                    style: {
                                        width:           '100%',
                                        height:          '140px',
                                        background:      '#f4f4f4',
                                        borderRadius:    '4px',
                                        marginBottom:    '10px',
                                        display:         'flex',
                                        alignItems:      'center',
                                        justifyContent:  'center',
                                        fontSize:        '26px',
                                        overflow:        'hidden',
                                    }
                                },
                                tab === 'starter' && template.previewImage
                                    ? wp.element.createElement('img', {
                                        src:   template.previewImage,
                                        alt:   template.title,
                                        style: { width: '100%', height: '100%', objectFit: 'cover', display: 'block' },
                                    })
                                    : (tab === 'starter' ? getTemplateIcon(template.slug) : '📄')
                            ),

                            wp.element.createElement(
                                'strong',
                                { style: { display: 'block', fontSize: '13px', marginBottom: '2px' } },
                                tab === 'starter' ? template.title : template.name
                            ),

                            tab === 'starter' && template.description && wp.element.createElement(
                                'span',
                                { style: { fontSize: '12px', color: '#666', lineHeight: '1.4' } },
                                template.description
                            ),

                            tab === 'saved' && (template.date || template.category) && wp.element.createElement(
                                'span',
                                { style: { fontSize: '11px', color: '#999', display: 'block' } },
                                template.category
                                    ? wp.element.createElement(wp.element.Fragment, {},
                                        wp.element.createElement('span', {
                                            style: {
                                                background:   '#f0f0f0',
                                                color:        '#555',
                                                borderRadius: '3px',
                                                padding:      '1px 5px',
                                                fontSize:     '10px',
                                                fontWeight:   '600',
                                                marginRight:  '4px',
                                            }
                                        }, template.category),
                                        template.date ? 'Saved ' + template.date : ''
                                      )
                                    : ('Saved ' + template.date)
                            ),

                            tab === 'saved' && template.tags && template.tags.length > 0 && wp.element.createElement(
                                'div',
                                { style: { display: 'flex', flexWrap: 'wrap', gap: '4px', marginTop: '6px' } },
                                template.tags.map(tag => wp.element.createElement(
                                    'span',
                                    {
                                        key:   tag,
                                        style: {
                                            fontSize:     '10px',
                                            background:   '#e8f0fe',
                                            color:        '#1E40AF',
                                            borderRadius: '3px',
                                            padding:      '2px 6px',
                                            fontWeight:   '600',
                                        },
                                    },
                                    tag
                                ))
                            ),

                            // Delete button for saved templates
                            tab === 'saved' && wp.element.createElement(
                                'button',
                                {
                                    type:    'button',
                                    title:   'Delete template',
                                    onClick: (e) => deleteTemplate(template.id, e),
                                    style: {
                                        position:        'absolute',
                                        top:             '8px',
                                        right:           '8px',
                                        background:      'none',
                                        border:          'none',
                                        cursor:          'pointer',
                                        color:           '#cc1818',
                                        fontSize:        '14px',
                                        lineHeight:      '1',
                                        padding:         '2px 4px',
                                        opacity:         deleting === template.id ? 0.4 : 1,
                                    }
                                },
                                '✕'
                            )
                        );
                    })
                ),

                // Actions
                !loading && wp.element.createElement(
                    'div',
                    { style: { display: 'flex', justifyContent: 'flex-end', gap: '12px', borderTop: '1px solid #ddd', paddingTop: '16px' } },
                    wp.element.createElement(Button, { variant: 'tertiary', onClick: () => setIsOpen(false) }, 'Start from scratch'),
                    wp.element.createElement(
                        Button,
                        {
                            variant:  'primary',
                            onClick:  applyTemplate,
                            disabled: !selected || applying,
                            isBusy:   applying,
                        },
                        applying ? 'Applying...' : 'Use this template'
                    )
                )
            )
        );
    }

    function getTemplateIcon(slug) {
        const icons = {
            newsletter:    '📰',
            promotional:   '🎯',
            'two-column':  '📋',
            transactional: '📦',
            birthday:       '🎂',
            're-engagement': '💌',
            blank:          '✏️',
        };
        return icons[slug] || '📧';
    }

    wp.plugins.registerPlugin('eb-template-chooser', {
        render: TemplateChooser,
    });

})(window.wp);
