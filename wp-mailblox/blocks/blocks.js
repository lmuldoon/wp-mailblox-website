(function (wp) {
    const {
        addFilter
    } = wp.hooks;
    const {
        BlockControls
    } = wp.blockEditor;
    const {
        ToolbarGroup,
        ToolbarButton,
        Dropdown,
        MenuGroup,
        MenuItem
    } = wp.components;

    // ===== SHARED COLOUR HELPER =====

    /**
     * Returns the correct preset text colour for a given background hex.
     * Uses text_color_dark when the background is light, text_color_light when dark.
     * Falls back to #000000 / #ffffff if preset data isn't available.
     */
    window.ebContrastColor = function ebContrastColor(bgHex) {
        var preset  = (window.EB_EDITOR_DATA && window.EB_EDITOR_DATA.preset) || {};
        var dark    = preset.text_color_dark  || '#000000';
        var light   = preset.text_color_light || '#ffffff';
        var hex     = (bgHex || '#ffffff').replace('#', '');
        if (hex.length === 3) hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
        var r = parseInt(hex.slice(0, 2), 16);
        var g = parseInt(hex.slice(2, 4), 16);
        var b = parseInt(hex.slice(4, 6), 16);
        var luminance = (r * 0.2126 + g * 0.7152 + b * 0.0722) / 255;
        return luminance > 0.5 ? dark : light;
    };

    /**
     * Walks up the block tree from clientId to find the nearest section block's
     * backgroundColor, falling back to the preset bg_color.
     */
    window.ebGetSectionBackground = function ebGetSectionBackground(clientId) {
        var store   = wp.data.select('core/block-editor');
        var preset  = (window.EB_EDITOR_DATA && window.EB_EDITOR_DATA.preset) || {};
        var id      = clientId;
        while (id) {
            var block = store.getBlock(id);
            if (block) {
                // Columns background takes priority — only use it if explicitly set
                if (block.name === 'email-builder/columns') {
                    var colBg = block.attributes.backgroundColor;
                    if (colBg && colBg !== 'transparent') return colBg;
                }
                // Section background — only use if explicitly set (default is now '')
                if (block.name === 'email-builder/section') {
                    var secBg = block.attributes.backgroundColor;
                    if (secBg && secBg !== 'transparent') return secBg;
                    return preset.bg_color || '#ffffff';
                }
            }
            id = store.getBlockRootClientId(id);
        }
        return preset.bg_color || '#ffffff';
    };

    // ===== SHARED RESPONSIVE COMPONENTS =====

    const PADDING_OPTIONS = [
        { label: '0',    value: 0 },
        { label: '5px',  value: 5 },
        { label: '10px', value: 10 },
        { label: '15px', value: 15 },
        { label: '20px', value: 20 },
        { label: '25px', value: 25 },
        { label: '30px', value: 30 },
        { label: '35px', value: 35 },
        { label: '40px', value: 40 },
        { label: '45px', value: 45 },
        { label: '50px', value: 50 },
    ];
    window.EB_PADDING_OPTIONS = PADDING_OPTIONS;

    window.EBResponsiveToggle = function EBResponsiveToggle({ value, onChange }) {
        return wp.element.createElement(
            'div',
            {
                className: 'eb-toggle-group eb-responsive-toggle',
                style: {
                    display: 'flex',
                    marginBottom: '8px',
                    borderRadius: '4px',
                    overflow: 'hidden',
                    border: '1px solid #ddd',
                }
            },
            ['desktop', 'mobile'].map(function (mode) {
                return wp.element.createElement(
                    'button',
                    {
                        key: mode,
                        type: 'button',
                        onClick: function () { onChange(mode); },
                        style: {
                            flex: 1,
                            padding: '6px 0',
                            fontSize: '12px',
                            fontWeight: value === mode ? '700' : '400',
                            cursor: 'pointer',
                            border: 'none',
                            backgroundColor: value === mode ? '#2271b1' : '#f6f7f7',
                            color: value === mode ? '#fff' : '#50575e',
                        }
                    },
                    mode === 'desktop' ? 'Desktop' : 'Mobile'
                );
            })
        );
    };

    window.EBPaddingFields = function EBPaddingFields({ prefix, attributes, setAttributes, isMobile }) {
        const { RangeControl, ToggleControl } = wp.components;
        const dirs = ['Top', 'Bottom', 'Left', 'Right'];

        function snap(v) {
            return Math.min(50, Math.max(0, Math.round(Number(v) / 5) * 5));
        }

        return wp.element.createElement(
            wp.element.Fragment,
            {},
            dirs.map(function (dir) {
                const key = prefix + dir;
                const raw = attributes[key];

                if (isMobile) {
                    const hasOverride = raw !== null && raw !== undefined && raw !== '';
                    const val = hasOverride ? snap(raw) : 0;

                    return wp.element.createElement(
                        'div',
                        { key: dir },
                        wp.element.createElement(ToggleControl, {
                            label:    'Override Padding ' + dir,
                            checked:  hasOverride,
                            onChange: function (checked) {
                                setAttributes({ [key]: checked ? 0 : null });
                            },
                        }),
                        hasOverride && wp.element.createElement(RangeControl, {
                            value:    val,
                            min:      0,
                            max:      50,
                            step:     5,
                            onChange: function (v) {
                                setAttributes({ [key]: snap(v) });
                            },
                        })
                    );
                }

                const val = snap(raw || 0);
                return wp.element.createElement(RangeControl, {
                    key:      dir,
                    label:    'Padding ' + dir,
                    value:    val,
                    min:      0,
                    max:      50,
                    step:     5,
                    onChange: function (v) {
                        setAttributes({ [key]: snap(v) });
                    },
                });
            })
        );
    };

    window.EBButtonGroup = function EBButtonGroup({ label, value, onChange, options }) {
        return wp.element.createElement(
            'div',
            { className: 'eb-button-group-field', style: { marginBottom: '16px' } },
            label && wp.element.createElement(
                'p',
                { style: { margin: '0 0 6px', fontSize: '11px', fontWeight: '500', textTransform: 'uppercase', color: '#1e1e1e' } },
                label
            ),
            wp.element.createElement(
                'div',
                {
                    className: 'eb-toggle-group',
                    style: {
                        display: 'flex',
                        borderRadius: '4px',
                        overflow: 'hidden',
                        border: '1px solid #ddd',
                    }
                },
                options.map(function (opt, i) {
                    var active = value === opt.value;
                    return wp.element.createElement(
                        'button',
                        {
                            key: opt.value,
                            type: 'button',
                            onClick: function () { onChange(opt.value); },
                            style: {
                                flex: 1,
                                padding: '6px 4px',
                                fontSize: '12px',
                                fontWeight: active ? '700' : '400',
                                cursor: 'pointer',
                                border: 'none',
                                borderLeft: i > 0 ? '1px solid #ddd' : 'none',
                                backgroundColor: active ? '#2271b1' : '#f6f7f7',
                                color: active ? '#fff' : '#50575e',
                                whiteSpace: 'nowrap',
                                overflow: 'hidden',
                                textOverflow: 'ellipsis',
                            }
                        },
                        opt.label
                    );
                })
            )
        );
    };

    window.EBAlignControl = function EBAlignControl({ attrKey, attributes, setAttributes, isMobile }) {
        const alignOptions = [
            { label: 'Left',   value: 'left' },
            { label: 'Centre', value: 'center' },
            { label: 'Right',  value: 'right' },
        ];
        const options = isMobile
            ? [{ label: 'Inherit', value: '' }].concat(alignOptions)
            : alignOptions;
        return wp.element.createElement(window.EBButtonGroup, {
            label:    'Alignment',
            value:    attributes[attrKey] || '',
            options:  options,
            onChange: function (v) { setAttributes({ [attrKey]: v }); },
        });
    };

    window.EBResponsiveDivider = function EBResponsiveDivider() {
        return wp.element.createElement(
            'p',
            {
                style: {
                    fontSize:      '11px',
                    fontWeight:    '600',
                    textTransform: 'uppercase',
                    letterSpacing: '0.5px',
                    color:         '#757575',
                    margin:        '16px 0 8px',
                    paddingTop:    '12px',
                    borderTop:     '1px solid #e0e0e0',
                }
            },
            'Responsive'
        );
    };

    // ===== END SHARED RESPONSIVE COMPONENTS =====

    // ===== MOBILE PREVIEW HOOK =====

    window.EBUseIsMobilePreview = function() {
        return wp.data.useSelect(function(select) {
            var store = select('core/editor');
            return store && typeof store.getDeviceType === 'function'
                ? store.getDeviceType() === 'Mobile'
                : false;
        });
    };

    window.ebMobileVal = function(desktop, mobile, isMobile) {
        if (!isMobile) return desktop;
        return (mobile !== null && mobile !== undefined && mobile !== '') ? mobile : desktop;
    };

    // ===== MOBILE PREVIEW FONT SIZES =====
    // When the editor switches to Mobile preview, injects a <style> tag directly
    // into the editor iframe with the mobile font sizes from the preset.
    // Uses direct injection (same method as updateEditorStyles) because
    // wp_add_inline_style targets the parent document, not the iframe.
    (function() {
        var lastDevice = null;

        function getEditorDoc() {
            var frame = document.querySelector('iframe[name="editor-canvas"]');
            if (frame) {
                try { return frame.contentDocument || frame.contentWindow.document; }
                catch (e) {}
            }
            return document;
        }

        function applyMobileStyles(isMobile) {
            var doc = getEditorDoc();

            // Remove any existing mobile override
            var existing = doc.getElementById('eb-mobile-font-styles');
            if (existing) existing.parentNode.removeChild(existing);

            if (!isMobile) return;

            var preset        = (window.EB_EDITOR_DATA && window.EB_EDITOR_DATA.preset) || {};
            var headingMobile = preset.heading_size_mobile || preset.heading_size || 28;
            var subMobile     = preset.subheading_size_mobile || preset.subheading_size || 24;
            var bodyMobile    = preset.body_size_mobile || preset.body_size || 16;

            var css =
                '.eb-header-block, .eb-header-block div[contenteditable] { font-size: ' + headingMobile + 'px !important; } ' +
                '.eb-subheader-block, .eb-subheader-block div[contenteditable] { font-size: ' + subMobile + 'px !important; } ' +
                '.eb-text-block, .eb-text-block div[contenteditable] { font-size: ' + bodyMobile + 'px !important; } ' +
                '.eb-button-link { font-size: ' + bodyMobile + 'px !important; }';

            var style = doc.createElement('style');
            style.id = 'eb-mobile-font-styles';
            style.textContent = css;
            (doc.head || doc.body).appendChild(style);
        }

        wp.data.subscribe(function() {
            var store = wp.data.select('core/editor');
            if (!store || typeof store.getDeviceType !== 'function') return;
            var device = store.getDeviceType();
            if (device === lastDevice) return;
            lastDevice = device;
            // Defer slightly so the iframe finishes any re-render before we inject
            setTimeout(function() { applyMobileStyles(device === 'Mobile'); }, 50);
        });
    })();

    // Add email builder block category
    function addEmailBuilderCategory(categories) {
        return [
            ...categories,
            {
                slug: 'email-builder',
                title: 'Email Builder',
                icon: 'email',
            },
        ];
    }

    addFilter(
        'blocks.categories',
        'email-builder/category',
        addEmailBuilderCategory
    );

    wp.hooks.addFilter(
        'blocks.registerBlockType',
        'email-builder/disable-html-editing',
        function (settings) {
            return {
                ...settings,
                supports: {
                    ...settings.supports,
                    html:            false,
                    customClassName: false,
                },
            };
        }
    );

    // Group labels for display
    const groupLabels = {
        system_tags: 'System',
        audience_tags: 'Audience',
        campaign_tags: 'Campaign',
        ecommerce_tags: 'E-commerce',
        conditional_tags: 'Conditional',
        owner_tags: 'Owner',
    };

    /**
     * MergeTagToolbar
     * Renders a toolbar dropdown for inserting merge tags into RichText blocks.
     *
     * @param {Function} onInsert - called with the tag string to insert
     * @param {string}   tagType  - 'text' to show only text tags, 'url' for url tags, 'all' for everything
     */
    window.MergeTagToolbar = function MergeTagToolbar({
        onInsert,
        tagType = 'text'
    }) {

        const tags = (window.EB_EDITOR_DATA && window.EB_EDITOR_DATA.platform_tags) || {};

        // Check if there are any tags to show
        const hasGroups = Object.keys(tags).length > 0;

        if (!hasGroups) return null;

        return wp.element.createElement(
            BlockControls, {},
            wp.element.createElement(
                ToolbarGroup, {},
                wp.element.createElement(
                    Dropdown, {
                        renderToggle: ({
                                isOpen,
                                onToggle
                            }) =>
                            wp.element.createElement(
                                ToolbarButton, {
                                    icon: 'shortcode',
                                    label: 'Insert Merge Tag',
                                    onClick: onToggle,
                                    isPressed: isOpen,
                                }
                            ),

                        renderContent: ({
                                onClose
                            }) =>
                            wp.element.createElement(
                                'div', {
                                    style: {
                                        minWidth: '240px',
                                        maxHeight: '320px',
                                        overflowY: 'auto'
                                    }
                                },

                                Object.entries(tags).map(([groupKey, groupTags]) => {

                                    // Filter tags by type
                                    const filtered = groupTags.filter(tag => {
                                        if (tagType === 'all') return true;
                                        return tag.type === tagType;
                                    });

                                    if (filtered.length === 0) return null;

                                    return wp.element.createElement(
                                        MenuGroup, {
                                            key: groupKey,
                                            label: groupLabels[groupKey] || groupKey,
                                        },
                                        filtered.map(tag =>
                                            wp.element.createElement(
                                                MenuItem, {
                                                    key: tag.key,
                                                    onClick: () => {
                                                        onInsert(tag.tag);
                                                        onClose();
                                                    },
                                                    info: tag.tag,
                                                },
                                                tag.label
                                            )
                                        )
                                    );
                                })
                            )
                    }
                )
            )
        );
    };

    // ===== CUSTOM HIGHLIGHT FORMAT TYPE =====
    // Replaces core/text-color: outputs <span class="eb-color" style="color:#xxx">
    // so the colour is an inline style (email-safe) rather than a CSS class.
    (function() {
        var registerFormatType   = wp.richText.registerFormatType;
        var RichTextToolbarButton = wp.blockEditor.RichTextToolbarButton;
        var ColorPalette          = wp.components.ColorPalette;
        var Popover               = wp.components.Popover;
        var Button                = wp.components.Button;
        var useState              = wp.element.useState;
        var Fragment              = wp.element.Fragment;
        var applyFormat           = wp.richText.applyFormat;
        var removeFormat          = wp.richText.removeFormat;

        registerFormatType('email-builder/highlight', {
            title:     'Highlight',
            tagName:   'span',
            className: 'eb-color',
            attributes: {
                style: 'style',
            },

            edit: function EBHighlight(props) {
                var isActive        = props.isActive;
                var activeAttributes = props.activeAttributes;
                var value           = props.value;
                var onChange        = props.onChange;

                var openState  = useState(false);
                var isOpen     = openState[0];
                var setIsOpen  = openState[1];

                // Extract colour currently applied to selection
                var currentColor = '';
                if (activeAttributes && activeAttributes.style) {
                    var m = activeAttributes.style.match(/color:\s*([^;]+)/i);
                    if (m) currentColor = m[1].trim();
                }

                return wp.element.createElement(
                    Fragment,
                    {},
                    wp.element.createElement(RichTextToolbarButton, {
                        icon:     'editor-textcolor',
                        title:    'Highlight',
                        onClick:  function() { setIsOpen(!isOpen); },
                        isActive: isActive,
                    }),
                    isOpen && wp.element.createElement(
                        Popover,
                        { onClose: function() { setIsOpen(false); }, placement: 'bottom' },
                        wp.element.createElement(
                            'div',
                            { style: { padding: '12px', minWidth: '220px' } },
                            wp.element.createElement(
                                'p',
                                { style: { margin: '0 0 8px', fontWeight: '600', fontSize: '12px' } },
                                'Highlight Colour'
                            ),
                            wp.element.createElement(ColorPalette, {
                                value:    currentColor,
                                onChange: function(color) {
                                    if (color) {
                                        onChange(applyFormat(value, {
                                            type: 'email-builder/highlight',
                                            attributes: { style: 'color: ' + color },
                                        }));
                                    } else {
                                        onChange(removeFormat(value, 'email-builder/highlight'));
                                    }
                                    setIsOpen(false);
                                },
                                clearable: true,
                            }),
                            isActive && wp.element.createElement(
                                Button,
                                {
                                    isDestructive: true,
                                    isSmall:       true,
                                    onClick: function() {
                                        onChange(removeFormat(value, 'email-builder/highlight'));
                                        setIsOpen(false);
                                    },
                                    style: { marginTop: '6px' },
                                },
                                'Remove Colour'
                            )
                        )
                    )
                );
            },
        });
    })();

})(window.wp);