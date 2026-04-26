(function (wp) {

    const { registerBlockType } = wp.blocks;
    const { InspectorControls, MediaUpload, MediaUploadCheck } = wp.blockEditor;
    const {
        PanelBody, ToggleControl, RangeControl, TextControl,
        ColorPalette, Button,
    } = wp.components;
    const { useState, useEffect } = wp.element;

    const UTM_FIELDS = [
        { key: 'utmSource',   label: 'UTM Source' },
        { key: 'utmMedium',   label: 'UTM Medium' },
        { key: 'utmCampaign', label: 'UTM Campaign' },
        { key: 'utmContent',  label: 'UTM Content' },
        { key: 'utmTerm',     label: 'UTM Term' },
    ];

    function getYouTubeId(url) {
        const match = url.match(/(?:youtu\.be\/|youtube\.com\/(?:watch\?(?:.*&)?v=|embed\/|v\/|shorts\/))([a-zA-Z0-9_-]{11})/);
        return match ? match[1] : null;
    }

    function isVimeo(url) {
        return /vimeo\.com/.test(url);
    }

    registerBlockType('email-builder/video', {
        title:    'Video',
        icon:     'video-alt3',
        category: 'email-builder',
        parent:   ['email-builder/column'],

        attributes: {
            videoUrl:           { type: 'string',  default: '' },
            thumbnailUrl:       { type: 'string',  default: '' },
            customThumbnailUrl: { type: 'string',  default: '' },
            videoTitle:         { type: 'string',  default: '' },
            align:              { type: 'string',  default: 'center' },
            borderRadius:       { type: 'number',  default: 0 },
            showPlayButton:     { type: 'boolean', default: true },
            playButtonColor:    { type: 'string',  default: '#ffffff' },
            showCaption:        { type: 'boolean', default: false },
            caption:            { type: 'string',  default: '' },
            paddingTop:         { type: 'number',  default: 0 },
            paddingBottom:      { type: 'number',  default: 0 },
            paddingLeft:        { type: 'number',  default: 0 },
            paddingRight:       { type: 'number',  default: 0 },
            mobilePaddingTop:   { type: 'number',  default: null },
            mobilePaddingBottom:{ type: 'number',  default: null },
            mobilePaddingLeft:  { type: 'number',  default: null },
            mobilePaddingRight: { type: 'number',  default: null },
            hideOnMobile:       { type: 'boolean', default: false },
            utmSource:          { type: 'string',  default: '' },
            utmMedium:          { type: 'string',  default: '' },
            utmCampaign:        { type: 'string',  default: '' },
            utmContent:         { type: 'string',  default: '' },
            utmTerm:            { type: 'string',  default: '' },
        },

        edit: function (props) {
            const { attributes, setAttributes } = props;
            const {
                videoUrl, thumbnailUrl, customThumbnailUrl, videoTitle,
                align, borderRadius, showPlayButton, playButtonColor,
                showCaption, caption,
            } = attributes;

            const [urlInput,    setUrlInput]    = useState(videoUrl);
            const [fetchStatus, setFetchStatus] = useState('');
            const [useCustom,        setUseCustom]        = useState(!!customThumbnailUrl);
            const [spacingViewMode,  setSpacingViewMode]  = useState('desktop');
            const isMobilePreview  = window.EBUseIsMobilePreview();
            const effPaddingTop    = window.ebMobileVal(attributes.paddingTop    || 0, attributes.mobilePaddingTop,    isMobilePreview);
            const effPaddingBottom = window.ebMobileVal(attributes.paddingBottom || 0, attributes.mobilePaddingBottom, isMobilePreview);
            const effPaddingLeft   = window.ebMobileVal(attributes.paddingLeft   || 0, attributes.mobilePaddingLeft,   isMobilePreview);
            const effPaddingRight  = window.ebMobileVal(attributes.paddingRight  || 0, attributes.mobilePaddingRight,  isMobilePreview);

            const effectiveThumbnail = useCustom ? customThumbnailUrl : thumbnailUrl;

            function handleUrlChange(val) {
                setUrlInput(val);
                setAttributes({ videoUrl: val, thumbnailUrl: '', videoTitle: '' });
                setFetchStatus('');
            }

            function handleUrlBlur() {
                const url = urlInput.trim();
                if (!url) return;

                // YouTube — resolve directly in JS
                const ytId = getYouTubeId(url);
                if (ytId) {
                    const thumb = 'https://img.youtube.com/vi/' + ytId + '/maxresdefault.jpg';
                    setAttributes({ thumbnailUrl: thumb });
                    setFetchStatus('');
                    return;
                }

                // Vimeo — needs server-side proxy
                if (isVimeo(url)) {
                    setFetchStatus('Fetching thumbnail…');
                    wp.apiFetch({ path: wp.url.addQueryArgs('/email-builder/v1/video-thumbnail', { url: url }) })
                        .then((data) => {
                            if (data && data.thumbnail_url) {
                                setAttributes({ thumbnailUrl: data.thumbnail_url, videoTitle: data.title || '' });
                                setFetchStatus('');
                            } else {
                                setFetchStatus('Could not fetch thumbnail. Please upload one manually.');
                            }
                        })
                        .catch(() => setFetchStatus('Could not fetch thumbnail. Please upload one manually.'));
                    return;
                }

                setFetchStatus('Unrecognised URL. Paste a YouTube or Vimeo link, or upload a custom thumbnail below.');
            }

            const justifyMap = { left: 'flex-start', center: 'center', right: 'flex-end' };

            return wp.element.createElement(
                'div',
                { className: 'eb-video-block', style: { opacity: (isMobilePreview && attributes.hideOnMobile) ? 0.3 : undefined } },

                wp.element.createElement(
                    InspectorControls,
                    {},

                    // Settings
                    wp.element.createElement(
                        PanelBody,
                        { title: 'Settings', initialOpen: true },
                        wp.element.createElement(TextControl, {
                            label:       'YouTube or Vimeo URL',
                            value:       urlInput,
                            onChange:    handleUrlChange,
                            onBlur:      handleUrlBlur,
                            placeholder: 'https://www.youtube.com/watch?v=…',
                        }),
                        fetchStatus && wp.element.createElement('p', { style: { fontSize: '12px', color: '#856404', marginTop: '-8px' } }, fetchStatus),
                        wp.element.createElement('p', { style: { fontSize: '11px', color: '#888', marginTop: '4px' } }, 'Paste the URL and click outside the field to auto-fetch the thumbnail.'),

                        wp.element.createElement(ToggleControl, {
                            label:    'Use custom thumbnail',
                            checked:  useCustom,
                            onChange: (v) => { setUseCustom(v); if (!v) setAttributes({ customThumbnailUrl: '' }); },
                        }),
                        useCustom && wp.element.createElement(
                            MediaUploadCheck,
                            {},
                            wp.element.createElement(
                                MediaUpload,
                                {
                                    onSelect: (media) => setAttributes({ customThumbnailUrl: media.url }),
                                    allowedTypes: ['image'],
                                    value: customThumbnailUrl,
                                    render: ({ open }) => wp.element.createElement(
                                        'div',
                                        {},
                                        customThumbnailUrl && wp.element.createElement('img', {
                                            src:   customThumbnailUrl,
                                            style: { width: '100%', height: 'auto', display: 'block', marginBottom: '8px', borderRadius: '4px' },
                                        }),
                                        wp.element.createElement(Button, { variant: 'secondary', isSmall: true, onClick: open },
                                            customThumbnailUrl ? 'Replace Thumbnail' : 'Upload Thumbnail'
                                        ),
                                        customThumbnailUrl && wp.element.createElement(Button, {
                                            variant: 'tertiary', isSmall: true,
                                            onClick: () => setAttributes({ customThumbnailUrl: '' }),
                                            style: { marginLeft: '6px' },
                                        }, 'Remove')
                                    ),
                                }
                            )
                        )
                    ),

                    // Style (appearance + spacing merged)
                    wp.element.createElement(
                        PanelBody,
                        { title: 'Style', initialOpen: false },
                        wp.element.createElement(window.EBAlignControl, {
                            attrKey:       'align',
                            attributes:    attributes,
                            setAttributes: setAttributes,
                            isMobile:      false,
                        }),
                        wp.element.createElement(RangeControl, {
                            label:    'Border Radius',
                            value:    borderRadius,
                            min:      0,
                            max:      40,
                            onChange: (v) => setAttributes({ borderRadius: v }),
                        }),
                        wp.element.createElement(ToggleControl, {
                            label:    'Show Play Button',
                            checked:  showPlayButton,
                            onChange: (v) => setAttributes({ showPlayButton: v }),
                        }),
                        showPlayButton && wp.element.createElement(
                            'div', {},
                            wp.element.createElement('p', { style: { marginBottom: '8px', fontSize: '11px', fontWeight: '600', textTransform: 'uppercase', letterSpacing: '0.5px' } }, 'Play Button Colour'),
                            wp.element.createElement(ColorPalette, {
                                value:    playButtonColor,
                                onChange: (v) => setAttributes({ playButtonColor: v || '#ffffff' }),
                            })
                        ),
                        wp.element.createElement(ToggleControl, {
                            label:    'Show Caption',
                            checked:  showCaption,
                            onChange: (v) => setAttributes({ showCaption: v }),
                        }),
                        showCaption && wp.element.createElement(TextControl, {
                            label:    'Caption Text',
                            value:    caption,
                            onChange: (v) => setAttributes({ caption: v }),
                        }),
                        window.EBPaddingFields && wp.element.createElement(window.EBResponsiveDivider),
                        window.EBPaddingFields && wp.element.createElement(window.EBResponsiveToggle, {
                            value: spacingViewMode,
                            onChange: setSpacingViewMode,
                        }),
                        window.EBPaddingFields && spacingViewMode === 'desktop' && wp.element.createElement(window.EBPaddingFields, {
                            prefix:        'padding',
                            attributes:    attributes,
                            setAttributes: setAttributes,
                        }),
                        window.EBPaddingFields && spacingViewMode === 'mobile' && wp.element.createElement(window.EBPaddingFields, {
                            prefix:        'mobilePadding',
                            attributes:    attributes,
                            setAttributes: setAttributes,
                            isMobile:      true,
                        }),
                        window.EBPaddingFields && spacingViewMode === 'mobile' && wp.element.createElement(ToggleControl, {
                            label:    'Hide on Mobile',
                            checked:  attributes.hideOnMobile,
                            onChange: (val) => setAttributes({ hideOnMobile: val }),
                        })
                    ),

                    // UTM
                    wp.element.createElement(
                        PanelBody,
                        { title: 'UTM Parameters', initialOpen: false },
                        UTM_FIELDS.map(f =>
                            wp.element.createElement(TextControl, {
                                key:      f.key,
                                label:    f.label,
                                value:    attributes[f.key],
                                onChange: (v) => setAttributes({ [f.key]: v }),
                            })
                        )
                    )
                ),

                // Editor preview
                wp.element.createElement(
                    'div',
                    { style: { padding: `${effPaddingTop}px ${effPaddingRight}px ${effPaddingBottom}px ${effPaddingLeft}px`, display: 'flex', justifyContent: justifyMap[align] || 'center' } },
                    effectiveThumbnail
                        ? wp.element.createElement(
                            'div',
                            { style: { position: 'relative', display: 'inline-block', maxWidth: '100%' } },
                            wp.element.createElement('img', {
                                src:   effectiveThumbnail,
                                style: {
                                    display:      'block',
                                    width:        '100%',
                                    height:       'auto',
                                    borderRadius: borderRadius + 'px',
                                },
                            }),
                            showPlayButton && wp.element.createElement(
                                'div',
                                {
                                    style: {
                                        position:  'absolute',
                                        top:       '50%',
                                        left:      '50%',
                                        transform: 'translate(-50%, -50%)',
                                        width:     '60px',
                                        height:    '60px',
                                        borderRadius: '50%',
                                        background: 'rgba(0,0,0,0.55)',
                                        display:   'flex',
                                        alignItems:'center',
                                        justifyContent:'center',
                                    },
                                },
                                wp.element.createElement(
                                    'svg',
                                    { width: '24', height: '24', viewBox: '0 0 24 24', fill: playButtonColor },
                                    wp.element.createElement('polygon', { points: '6,3 20,12 6,21' })
                                )
                            ),
                            showCaption && caption && wp.element.createElement('p', {
                                style: { margin: '8px 0 0', fontSize: '12px', color: '#555', textAlign: align },
                            }, caption)
                        )
                        : wp.element.createElement(
                            'div',
                            { style: { textAlign: 'center', padding: '32px 16px', background: '#f0f0f0', borderRadius: '4px', width: '100%' } },
                            wp.element.createElement('p', { style: { margin: 0, fontSize: '13px', color: '#888' } },
                                videoUrl ? 'Paste a YouTube or Vimeo URL and click outside to load thumbnail.' : 'Paste a YouTube or Vimeo URL above.'
                            )
                        )
                )
            );
        },

        save: function () { return null; },
    });

})(window.wp);
