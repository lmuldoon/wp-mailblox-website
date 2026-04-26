(function (wp) {
    const { registerBlockType } = wp.blocks;
    const { Spinner } = wp.components;
    const { useState, useEffect } = wp.element;

    registerBlockType('email-builder/module', {
        title: 'Module',
        icon: 'screenoptions',
        category: 'email-builder',

        attributes: {
            moduleId:   { type: 'number', default: 0 },
            moduleName: { type: 'string', default: '' },
        },

        edit: ({ clientId }) => {
            const [modules,   setModules]   = useState(null);
            const [loading,   setLoading]   = useState(false);
            const [error,     setError]     = useState('');
            const [deletingId, setDeletingId] = useState(null);

            useEffect(() => {
                setLoading(true);
                setError('');
                wp.apiFetch({ path: '/email-builder/v1/modules' })
                    .then((data) => {
                        setModules(data);
                        setLoading(false);
                    })
                    .catch(() => {
                        setError('Could not load modules.');
                        setLoading(false);
                    });
            }, []);

            function deleteModule(mod) {
                if (!window.confirm('Delete module "' + mod.name + '"? This cannot be undone.')) return;
                setDeletingId(mod.id);
                wp.apiFetch({ path: '/email-builder/v1/modules/' + mod.id, method: 'DELETE' })
                    .then(() => {
                        setModules((prev) => prev.filter((m) => m.id !== mod.id));
                        setDeletingId(null);
                    })
                    .catch(() => {
                        setError('Could not delete module. Please try again.');
                        setDeletingId(null);
                    });
            }

            function insertModule(mod) {
                wp.apiFetch({ path: '/email-builder/v1/modules/' + mod.id })
                    .then((data) => {
                        const raw    = JSON.parse(data.blocks);
                        const cloned = raw.map((b) => wp.blocks.cloneBlock(b));

                        const select   = wp.data.select('core/block-editor');
                        const dispatch = wp.data.dispatch('core/block-editor');

                        const rootClientId = select.getBlockRootClientId(clientId);
                        const index        = select.getBlockIndex(clientId);

                        dispatch.insertBlocks(cloned, index, rootClientId);
                        dispatch.removeBlock(clientId);
                    })
                    .catch(() => {
                        setError('Could not load module blocks. Please try again.');
                    });
            }

            return wp.element.createElement(
                'div',
                {
                    style: {
                        border:       '2px dashed #ccc',
                        borderRadius: '4px',
                        padding:      '24px 16px',
                        background:   '#fafafa',
                    }
                },
                wp.element.createElement('p', {
                    style: { fontWeight: 'bold', marginTop: 0, marginBottom: '12px' }
                }, 'Select a Module to Insert'),

                loading && wp.element.createElement(Spinner),

                error && wp.element.createElement('p', {
                    style: { color: '#d63638' }
                }, error),

                !loading && !error && modules && modules.length === 0 && wp.element.createElement(
                    'p',
                    { style: { color: '#666', fontStyle: 'italic' } },
                    'No modules saved yet. Select a Section and use "Save as Module" in its toolbar.'
                ),

                !loading && !error && modules && modules.length > 0 && wp.element.createElement(
                    'ul',
                    { style: { listStyle: 'none', margin: 0, padding: 0 } },
                    ...modules.map((mod) =>
                        wp.element.createElement(
                            'li',
                            { key: mod.id, style: { marginBottom: '6px', display: 'flex', gap: '6px' } },

                            // Insert button
                            wp.element.createElement(
                                'button',
                                {
                                    type:    'button',
                                    onClick: () => insertModule(mod),
                                    style: {
                                        flex:         '1',
                                        textAlign:    'left',
                                        padding:      '10px 14px',
                                        background:   '#fff',
                                        border:       '1px solid #ccc',
                                        borderRadius: '3px',
                                        cursor:       'pointer',
                                        fontSize:     '13px',
                                        fontFamily:   'inherit',
                                    }
                                },
                                wp.element.createElement('span', {
                                    className: 'dashicons dashicons-screenoptions',
                                    style: { marginRight: '8px', verticalAlign: 'middle' }
                                }),
                                mod.name,
                                wp.element.createElement('span', {
                                    style: { float: 'right', color: '#999', fontSize: '11px', lineHeight: '1.6' }
                                }, mod.date)
                            ),

                            // Delete button
                            wp.element.createElement(
                                'button',
                                {
                                    type:     'button',
                                    disabled: deletingId === mod.id,
                                    onClick:  () => deleteModule(mod),
                                    title:    'Delete module',
                                    style: {
                                        padding:      '10px 12px',
                                        background:   '#fff',
                                        border:       '1px solid #ccc',
                                        borderRadius: '3px',
                                        cursor:       'pointer',
                                        color:        '#d63638',
                                        fontSize:     '13px',
                                        lineHeight:   1,
                                    }
                                },
                                deletingId === mod.id
                                    ? wp.element.createElement('span', { className: 'dashicons dashicons-update', style: { verticalAlign: 'middle' } })
                                    : wp.element.createElement('span', { className: 'dashicons dashicons-trash', style: { verticalAlign: 'middle' } })
                            )
                        )
                    )
                )
            );
        },

        save: () => null,
    });
})(window.wp);
