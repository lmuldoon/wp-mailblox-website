(function (wp) {
    const { registerBlockType } = wp.blocks;
    const { RichText, InspectorControls } = wp.blockEditor;
    const { PanelBody, ColorPalette, Button, ToggleControl } = wp.components;
    const { useState, useEffect, useRef } = wp.element;
    const { useSelect } = wp.data;

    function useSavedCursorOffset(containerRef) {
        const savedOffset = useRef(null);
        useEffect(() => {
            function onSelectionChange() {
                if (!containerRef.current) return;
                const sel = window.getSelection();
                if (!sel || sel.rangeCount === 0) return;
                const range = sel.getRangeAt(0);
                if (!containerRef.current.contains(range.startContainer)) return;
                const preRange = document.createRange();
                preRange.selectNodeContents(containerRef.current);
                preRange.setEnd(range.startContainer, range.startOffset);
                savedOffset.current = preRange.toString().length;
            }
            document.addEventListener('selectionchange', onSelectionChange);
            return () => document.removeEventListener('selectionchange', onSelectionChange);
        }, []);
        return savedOffset;
    }

    registerBlockType('email-builder/subheader', {
        title: 'Subheader',
        icon: 'editor-paragraph',
        category: 'email-builder',
        parent: ['email-builder/column'],

        attributes: {
            content:     { type: 'string', default: '' },
            textColor:   { type: 'string', default: '' },
            align:       { type: 'string', default: 'left' },
            disableDarkTextColour: { type: 'boolean', default: false },
            paddingTop:    { type: 'number', default: 0 },
            paddingBottom: { type: 'number', default: 0 },
            paddingLeft:   { type: 'number', default: 0 },
            paddingRight:  { type: 'number', default: 0 },

            mobilePaddingTop:    { type: 'number', default: null },
            mobilePaddingBottom: { type: 'number', default: null },
            mobilePaddingLeft:   { type: 'number', default: null },
            mobilePaddingRight:  { type: 'number', default: null },
            mobileAlign:         { type: 'string', default: '' },
            hideOnMobile:        { type: 'boolean', default: false },
        },

        edit: function (props) {
            const { attributes, setAttributes, clientId } = props;
            const {
                disableDarkTextColour,
                paddingTop,
                paddingBottom,
                paddingLeft,
                paddingRight,
            } = attributes;

            const [viewMode, setViewMode] = useState('desktop');
            const isMobilePreview  = window.EBUseIsMobilePreview();
            const effectiveAlign   = window.ebMobileVal(attributes.align, attributes.mobileAlign,         isMobilePreview);
            const effPaddingTop    = window.ebMobileVal(paddingTop,        attributes.mobilePaddingTop,    isMobilePreview);
            const effPaddingBottom = window.ebMobileVal(paddingBottom,     attributes.mobilePaddingBottom, isMobilePreview);
            const effPaddingLeft   = window.ebMobileVal(paddingLeft,       attributes.mobilePaddingLeft,   isMobilePreview);
            const effPaddingRight  = window.ebMobileVal(paddingRight,      attributes.mobilePaddingRight,  isMobilePreview);
            const autoColor = useSelect(function(select) {
                var store   = select('core/block-editor');
                var parents = store.getBlockParents(clientId);
                for (var i = parents.length - 1; i >= 0; i--) {
                    var block = store.getBlock(parents[i]);
                    if (!block) continue;
                    if (block.name === 'email-builder/column') {
                        var colBg = block.attributes.backgroundColor;
                        if (colBg && colBg !== 'transparent') return window.ebContrastColor(colBg);
                    }
                    if (block.name === 'email-builder/columns') {
                        var colBg = block.attributes.backgroundColor;
                        if (colBg && colBg !== 'transparent') return window.ebContrastColor(colBg);
                    }
                    if (block.name === 'email-builder/section') {
                        var secBg = block.attributes.backgroundColor;
                        var preset = (window.EB_EDITOR_DATA && window.EB_EDITOR_DATA.preset) || {};
                        var bg = (secBg && secBg !== 'transparent') ? secBg : (preset.bg_color || '#ffffff');
                        return window.ebContrastColor(bg);
                    }
                }
                var preset = (window.EB_EDITOR_DATA && window.EB_EDITOR_DATA.preset) || {};
                return window.ebContrastColor(preset.bg_color || '#ffffff');
            }, [clientId]);
            var _preset = (window.EB_EDITOR_DATA && window.EB_EDITOR_DATA.preset) || {};
            var _desktopFontSize = _preset.subheading_size || 24;
            var _mobileFontSize  = _preset.subheading_size_mobile || _desktopFontSize;
            var effectiveFontSize = window.ebMobileVal(_desktopFontSize, _mobileFontSize, isMobilePreview);

            const containerRef = useRef(null);
            const savedOffset  = useSavedCursorOffset(containerRef);

            function insertMergeTag(tag) {
                const current = attributes.content || '';
                const offset  = savedOffset.current;
                if (typeof offset === 'number') {
                    const richVal = wp.richText.create({ html: current });
                    const tagVal  = wp.richText.create({ text: tag });
                    const newVal  = wp.richText.insert(richVal, tagVal, offset, offset);
                    setAttributes({ content: wp.richText.toHTMLString({ value: newVal }) });
                } else {
                    setAttributes({ content: current + tag });
                }
            }

            return wp.element.createElement(
                'div',
                { className: 'eb-subheader-block', style: { opacity: (isMobilePreview && attributes.hideOnMobile) ? 0.3 : undefined }, ref: containerRef },

                wp.element.createElement(window.MergeTagToolbar, {
                    onInsert: insertMergeTag,
                    tagType:  'text'
                }),

                wp.element.createElement(
                    InspectorControls,
                    {},
                    wp.element.createElement(
                        PanelBody,
                        { title: 'Style', initialOpen: true },

                        wp.element.createElement(
                                'div',
                                { className: 'eb-field' },
                                wp.element.createElement('label', {}, 'Text Colour Override'),
                                wp.element.createElement(
                                    'p',
                                    { className: 'description' },
                                    'Leave empty to use automatic contrast colour from your preset.'
                                ),
                                wp.element.createElement(ColorPalette, {
                                    value:     attributes.textColor,
                                    onChange:  (color) => setAttributes({ textColor: color || '' }),
                                    clearable: true
                                }),
                                attributes.textColor && wp.element.createElement(
                                    Button,
                                    {
                                        isDestructive: true,
                                        isSmall:       true,
                                        onClick:       () => setAttributes({ textColor: '' })
                                    },
                                    'Reset to Auto'
                                )
                            ),

                            // Disable dark mode text colour
                         wp.element.createElement(
                            'div', { className: 'eb-field' },
                        wp.element.createElement(ToggleControl, {
                            label: 'Disable dark mode text colour',
                            checked: disableDarkTextColour,
                            onChange: (value) => setAttributes({ disableDarkTextColour: value })
                        }),
                        wp.element.createElement(
                                'p',
                                { className: 'description' },
                                'Some email clients may still automatically adjust text colours in dark mode, even when this setting is disabled.'
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
                            wp.element.createElement(window.EBAlignControl, {
                                attrKey: 'align',
                                attributes,
                                setAttributes,
                                isMobile: false,
                            }),
                            wp.element.createElement(window.EBPaddingFields, {
                                prefix: 'padding',
                                attributes,
                                setAttributes,
                                isMobile: false,
                            }),
                        ),

                        viewMode === 'mobile' && wp.element.createElement(
                            wp.element.Fragment,
                            {},
                            wp.element.createElement(
                                'p', { className: 'description', style: { marginBottom: '8px' } },
                                'Leave blank to inherit desktop setting.'
                            ),
                            wp.element.createElement(window.EBAlignControl, {
                                attrKey: 'mobileAlign',
                                attributes,
                                setAttributes,
                                isMobile: true,
                            }),
                            wp.element.createElement(window.EBPaddingFields, {
                                prefix: 'mobilePadding',
                                attributes,
                                setAttributes,
                                isMobile: true,
                            }),
                            wp.element.createElement(ToggleControl, {
                                label:    'Hide on Mobile',
                                checked:  attributes.hideOnMobile,
                                onChange: (val) => setAttributes({ hideOnMobile: val }),
                            })
                        ),
                    )
                ),

                wp.element.createElement(RichText, {
                    tagName:        'div',
                    allowedFormats: ['core/bold', 'core/italic', 'core/link', 'email-builder/highlight'],
                    value:          attributes.content,
                    onChange:       (content) => setAttributes({ content }),
                    placeholder:    'Add your subheader...',
                    style:       {
                        textAlign:  effectiveAlign,
                        color:      attributes.textColor || autoColor,
                        padding:    `${effPaddingTop}px ${effPaddingRight}px ${effPaddingBottom}px ${effPaddingLeft}px`,
                        fontSize:   effectiveFontSize + 'px',
                    }
                })
            );
        },

        save: function () {
            return null;
        }
    });
})(window.wp);
