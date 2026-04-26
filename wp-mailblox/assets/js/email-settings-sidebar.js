/**
 * Email Toolbar Actions
 * Toolbar buttons: Preview, Send Test, Save as Template, Export
 * Settings (Preset, Subject/Preheader, Dark Mode, UTM) are handled by PHP meta boxes.
 */
(function (wp) {
    'use strict';

    var el       = wp.element.createElement;
    var Fragment = wp.element.Fragment;
    var useState = wp.element.useState;
    var useSelect = wp.data.useSelect;

    var _wp$components = wp.components;
    var Button  = _wp$components.Button;
    var Modal   = _wp$components.Modal;
    var Spinner = _wp$components.Spinner;
    var TextControl = _wp$components.TextControl;

    // ─────────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────────

    function ajaxPost(action, data, callback) {
        var params = Object.assign({ action: action }, data);
        var body   = Object.keys(params)
            .map(function (k) { return encodeURIComponent(k) + '=' + encodeURIComponent(params[k]); })
            .join('&');

        fetch(window.ajaxurl, {
            method:      'POST',
            credentials: 'same-origin',
            headers:     { 'Content-Type': 'application/x-www-form-urlencoded' },
            body:        body,
        })
            .then(function (r) { return r.json(); })
            .then(callback)
            .catch(function () { callback({ success: false, data: 'Request failed.' }); });
    }

    function ebData(key) {
        return window.EB_EDITOR_DATA && window.EB_EDITOR_DATA[key];
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // Toolbar Action Buttons (DOM injection)
    // ─────────────────────────────────────────────────────────────────────────────

    // ── Preview Modal ─────────────────────────────────────────────────────────

    function PreviewModal(props) {
        var onClose    = props.onClose;
        var previewUrl = ebData('preview_url') || '';
        var shareNonce = ebData('share_nonce') || '';
        var postId     = ebData('post_id') || 0;

        var shareState = useState({ url: ebData('share_url') || '', status: '', loading: false });
        var share      = shareState[0];
        var setShare   = shareState[1];

        function doShareRequest(shareAction) {
            setShare(function (s) { return Object.assign({}, s, { loading: true, status: '' }); });
            ajaxPost('eb_share_token', { post_id: postId, share_action: shareAction, nonce: shareNonce }, function (res) {
                if (res.success) {
                    setShare({ url: res.data.url || '', status: '', loading: false });
                } else {
                    setShare(function (s) { return Object.assign({}, s, { status: 'Error: ' + (res.data || 'Failed.'), loading: false }); });
                }
            });
        }

        return el(Modal, { title: 'Preview', onRequestClose: onClose, style: { maxWidth: '480px', width: '100%' } },
            el('p', { style: { marginBottom: '16px', color: '#555' } },
                'Opens a full-page preview of this email template.'
            ),
            el(Button, { variant: 'primary', href: previewUrl, target: '_blank', rel: 'noopener' }, 'Open Preview'),
            el('hr', { style: { margin: '20px 0' } }),
            el('p', { style: { fontWeight: 600, margin: '0 0 6px' } }, 'Share Preview Link'),
            el('p', { style: { fontSize: '13px', color: '#757575', marginBottom: '12px' } },
                'Anyone with this link can view the email — no login required. Use for client approvals.'
            ),
            share.loading && el(Spinner),
            !share.loading && share.url
                ? el(Fragment, {},
                    el('div', { style: { display: 'flex', gap: '8px', alignItems: 'center', marginBottom: '8px' } },
                        el('input', {
                            type:      'text',
                            readOnly:  true,
                            value:     share.url,
                            onClick:   function (e) { e.target.select(); },
                            style:     { flex: 1, fontSize: '12px', padding: '4px 6px', border: '1px solid #ccc', borderRadius: '3px' },
                        }),
                        el(Button, {
                            variant: 'secondary',
                            isSmall: true,
                            onClick: function () {
                                var url = share.url;
                                function onSuccess() {
                                    setShare(function (s) { return Object.assign({}, s, { status: 'Copied!' }); });
                                    setTimeout(function () { setShare(function (s) { return Object.assign({}, s, { status: '' }); }); }, 2000);
                                }
                                function execFallback() {
                                    var ta = document.createElement('textarea');
                                    ta.value = url;
                                    ta.style.cssText = 'position:fixed;top:0;left:0;opacity:0;pointer-events:none;';
                                    document.body.appendChild(ta);
                                    ta.focus();
                                    ta.select();
                                    try { document.execCommand('copy'); onSuccess(); } catch (e) {}
                                    document.body.removeChild(ta);
                                }
                                if (navigator.clipboard && navigator.clipboard.writeText) {
                                    navigator.clipboard.writeText(url).then(onSuccess).catch(execFallback);
                                } else {
                                    execFallback();
                                }
                            },
                        }, 'Copy')
                    ),
                    el(Button, { isDestructive: true, isSmall: true, onClick: function () { doShareRequest('revoke'); } }, 'Revoke Link')
                  )
                : !share.loading && el(Button, { variant: 'secondary', onClick: function () { doShareRequest('generate'); } }, 'Generate Share Link'),
            share.status && el('p', { style: { marginTop: '8px', fontSize: '12px', color: share.status === 'Copied!' ? '#00a32a' : '#cc1818' } }, share.status)
        );
    }

    // ── Send Test Modal ───────────────────────────────────────────────────────

    function SendTestModal(props) {
        var onClose    = props.onClose;
        var postId     = ebData('post_id') || 0;
        var userEmail  = ebData('user_email') || '';

        var emailState   = useState(userEmail);
        var statusState  = useState({ msg: '', ok: true });
        var sendingState = useState(false);

        var email   = emailState[0];   var setEmail   = emailState[1];
        var status  = statusState[0];  var setStatus  = statusState[1];
        var sending = sendingState[0]; var setSending = sendingState[1];

        function send() {
            var trimmed = email.trim();
            if (!trimmed) { setStatus({ msg: 'Please enter an email address.', ok: false }); return; }
            setSending(true);
            setStatus({ msg: '', ok: true });
            wp.apiFetch({
                path:   '/email-builder/v1/send-test',
                method: 'POST',
                data:   { post_id: postId, email: trimmed },
            }).then(function (res) {
                setSending(false);
                setStatus({ msg: '✓ Sent to ' + res.to, ok: true });
            }).catch(function (err) {
                setSending(false);
                setStatus({ msg: '✗ ' + (err && err.message ? err.message : 'Send failed. Check your mail settings.'), ok: false });
            });
        }

        return el(Modal, { title: 'Send Test Email', onRequestClose: onClose, style: { maxWidth: '420px', width: '100%' } },
            el('p', { style: { marginBottom: '16px', color: '#555' } },
                'Send a test of this email to an inbox to check rendering.'
            ),
            el(TextControl, {
                label:    'Email Address',
                type:     'email',
                value:    email,
                onChange: setEmail,
                placeholder: 'you@example.com',
            }),
            el('div', { style: { display: 'flex', gap: '8px', marginTop: '8px' } },
                el(Button, { variant: 'primary', onClick: send, isBusy: sending, disabled: sending },
                    sending ? 'Sending…' : 'Send Test Email'
                ),
                el(Button, { variant: 'tertiary', onClick: onClose, disabled: sending }, 'Cancel')
            ),
            status.msg && el('p', { style: { marginTop: '10px', fontSize: '12px', color: status.ok ? '#1e7e34' : '#cc1818' } }, status.msg),
            el('p', { style: { marginTop: '12px', fontSize: '11px', color: '#757575' } },
                'Uses your WordPress mail settings. Install an SMTP plugin for reliable delivery.'
            )
        );
    }

    // ── Export Modal ──────────────────────────────────────────────────────────

    function ExportModal(props) {
        var onClose       = props.onClose;
        var postId        = ebData('post_id') || 0;
        var postTitle     = useSelect(function (s) { return s('core/editor').getEditedPostAttribute('title') || 'email'; });
        var exportNonce   = ebData('export_nonce') || '';
        var pushNonce     = ebData('push_nonce') || '';
        var jsonNonce     = ebData('template_export_nonce') || '';
        var pushLabel     = ebData('push_label') || 'Push to Platform';
        var platformLabel = ebData('platform_label') || '';
        var hasApiKey     = ebData('has_api_key') || false;
        var isPro        = ebData('is_pro') || false;
        var upgradeUrl   = ebData('upgrade_url') || '';
        var platform     = (ebData('current_platform') || '').toLowerCase();

        var htmlState    = useState('');
        var warnState    = useState([]);
        var htmlStatus   = useState({ loading: false, done: false });
        var copyStatus   = useState('');
        var pushStatus   = useState({ msg: '', ok: true, loading: false });
        var jsonStatus   = useState('');

        var html       = htmlState[0];   var setHtml      = htmlState[1];
        var warnings   = warnState[0];   var setWarnings  = warnState[1];
        var hStatus    = htmlStatus[0];  var setHStatus   = htmlStatus[1];
        var copyMsg    = copyStatus[0];  var setCopyMsg   = copyStatus[1];
        var pushSt     = pushStatus[0];  var setPushSt    = pushStatus[1];
        var jsonMsg    = jsonStatus[0];  var setJsonMsg   = jsonStatus[1];

        function generateHtml() {
            setHStatus({ loading: true, done: false });
            setHtml('');
            setWarnings([]);
            ajaxPost('eb_export_email', { post_id: postId, nonce: exportNonce }, function (res) {
                if (res.success) {
                    setHtml(res.data.html || '');
                    setWarnings(res.data.warnings || []);
                    setHStatus({ loading: false, done: true });
                } else {
                    setHtml('');
                    setHStatus({ loading: false, done: false });
                    setWarnings([{ message: res.data || 'Export failed.' }]);
                }
            });
        }

        function copyHtml() {
            function onSuccess() {
                setCopyMsg('Copied!');
                setTimeout(function () { setCopyMsg(''); }, 2000);
            }
            function execFallback() {
                var ta = document.createElement('textarea');
                ta.value = html;
                ta.style.cssText = 'position:fixed;top:0;left:0;opacity:0;pointer-events:none;';
                document.body.appendChild(ta);
                ta.focus();
                ta.select();
                try { document.execCommand('copy'); onSuccess(); } catch (e) {}
                document.body.removeChild(ta);
            }
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(html).then(onSuccess).catch(execFallback);
            } else {
                execFallback();
            }
        }

        function downloadHtml() {
            var blob = new Blob([html], { type: 'text/html' });
            var url  = URL.createObjectURL(blob);
            var a    = document.createElement('a');
            var slug = postTitle.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
            var date = new Date().toISOString().slice(0, 10);
            a.href = url; a.download = slug + '-' + date + '.html';
            document.body.appendChild(a); a.click();
            document.body.removeChild(a); URL.revokeObjectURL(url);
        }

        function pushToPlatform() {
            setPushSt({ msg: '', ok: true, loading: true });
            ajaxPost('eb_push_to_platform', { post_id: postId, nonce: pushNonce }, function (res) {
                if (res.success) {
                    setPushSt({ msg: res.data.message || 'Pushed successfully.', ok: true, loading: false });
                } else {
                    setPushSt({ msg: res.data.message || res.data || 'Push failed.', ok: false, loading: false });
                }
            });
        }

        function exportJson() {
            setJsonMsg('Exporting…');
            ajaxPost('eb_export_template', { post_id: postId, nonce: jsonNonce }, function (res) {
                if (res.success) {
                    var json = JSON.stringify(res.data, null, 2);
                    var blob = new Blob([json], { type: 'application/json' });
                    var url  = URL.createObjectURL(blob);
                    var a    = document.createElement('a');
                    a.href = url;
                    a.download = (res.data.title || 'template').replace(/[^a-z0-9_-]/gi, '-').toLowerCase() + '.json';
                    document.body.appendChild(a); a.click();
                    document.body.removeChild(a); URL.revokeObjectURL(url);
                    setJsonMsg('');
                } else {
                    setJsonMsg('Export failed: ' + (res.data || 'Unknown error.'));
                }
            });
        }

        var EXPORT_ONLY_PLATFORMS = {
            hubspot:        { title: '⚠ HubSpot: export only',          body: 'HubSpot does not provide an API to upload raw HTML email templates. Export the HTML above and paste it into HubSpot\'s Design Manager manually.' },
            activecampaign: { title: '⚠ ActiveCampaign: export only',   body: 'ActiveCampaign does not provide an API to create campaign templates. Export the HTML above and paste it into the code editor when designing your campaign.' },
            convertkit:     { title: '⚠ Kit (ConvertKit): export only', body: 'Kit does not provide an API to store email templates. Export the HTML above and paste it into Kit\'s HTML template editor manually.' },
            emailoctopus:   { title: '⚠ EmailOctopus: export only',     body: 'EmailOctopus does not support programmatic template creation. Export the HTML above and upload it as a custom template in EmailOctopus.' },
            getresponse:    { title: '⚠ GetResponse: export only',      body: 'GetResponse does not provide an API to create reusable templates. Export the HTML above and import it via the GetResponse template editor.' },
        };
        var exportOnlyInfo    = EXPORT_ONLY_PLATFORMS[platform] || null;
        var exportOnlyWarning = exportOnlyInfo
            ? el('div', { style: { background: '#fff8e5', border: '1px solid #f0c040', borderRadius: '4px', padding: '10px 12px', marginBottom: '16px' } },
                el('strong', { style: { display: 'block', marginBottom: '4px', fontSize: '12px' } }, exportOnlyInfo.title),
                el('p', { style: { margin: 0, fontSize: '12px', color: '#555' } }, exportOnlyInfo.body)
              )
            : null;
        var hubspotWarning = exportOnlyWarning;

        return el(Modal, { title: 'Export', onRequestClose: onClose, style: { maxWidth: '560px', width: '100%' } },

            // HTML Export section
            el('p', { style: { fontWeight: 600, marginBottom: '8px' } }, 'Export HTML'),
            el(Button, { variant: 'secondary', onClick: generateHtml, isBusy: hStatus.loading, disabled: hStatus.loading },
                'Generate HTML'
            ),

            warnings.length > 0 && el('div', { style: { marginTop: '10px', background: '#fff8e5', border: '1px solid #f0c040', borderRadius: '4px', padding: '10px 12px' } },
                warnings.map(function (w, i) {
                    return el('p', { key: i, style: { margin: i === 0 ? 0 : '6px 0 0', fontSize: '12px' } }, w.message || w);
                })
            ),

            hStatus.done && html && el(Fragment, {},
                el('textarea', {
                    readOnly:  true,
                    value:     html,
                    rows:      10,
                    style:     { width: '100%', marginTop: '10px', fontSize: '11px', fontFamily: 'monospace', resize: 'vertical', borderRadius: '3px', border: '1px solid #ccc', padding: '6px' },
                    onClick:   function (e) { e.target.select(); },
                }),
                el('div', { style: { display: 'flex', gap: '8px', marginTop: '8px' } },
                    el(Button, { variant: 'secondary', isSmall: true, onClick: copyHtml },
                        copyMsg || 'Copy'
                    ),
                    el(Button, { variant: 'secondary', isSmall: true, onClick: downloadHtml }, 'Download')
                )
            ),

            el('hr', { style: { margin: '20px 0' } }),

            // Push to Platform section
            el('p', { style: { fontWeight: 600, marginBottom: '8px' } }, 'Push to Platform'),
            hubspotWarning,
            !hubspotWarning && (
                !isPro
                    ? el('div', { className: 'eb-pro-notice', style: { background: '#f0f6fc', border: '1px solid #72aee6', borderRadius: '4px', padding: '10px 12px', fontSize: '12px' } },
                        'Push directly to ',
                        el('strong', {}, platformLabel),
                        ' with one click — available in Pro. ',
                        upgradeUrl && el('a', { href: upgradeUrl }, 'Upgrade →')
                      )
                    : el(Fragment, {},
                        !hasApiKey && el('p', { style: { fontSize: '12px', color: '#cc1818', marginBottom: '8px' } },
                            '⚠ No API key configured for ' + platformLabel + '.'
                        ),
                        el(Button, {
                            variant:  'secondary',
                            onClick:  pushToPlatform,
                            isBusy:   pushSt.loading,
                            disabled: pushSt.loading || !hasApiKey,
                        }, pushLabel),
                        pushSt.msg && el('p', { style: { marginTop: '8px', fontSize: '12px', color: pushSt.ok ? '#1e7e34' : '#cc1818' } }, pushSt.msg)
                      )
            ),

            el('hr', { style: { margin: '20px 0' } }),

            // JSON Template export
            el('p', { style: { fontWeight: 600, marginBottom: '4px' } }, 'Export Template Layout'),
            el('p', { style: { fontSize: '12px', color: '#757575', marginBottom: '8px' } },
                'Download this email\'s block structure as a portable JSON file for importing into other installs.'
            ),
            el(Button, { variant: 'secondary', onClick: exportJson }, 'Export JSON'),
            jsonMsg && el('p', { style: { marginTop: '8px', fontSize: '12px', color: '#757575' } }, jsonMsg),

            el('hr', { style: { margin: '20px 0' } }),

            // JSON Import
            el('p', { style: { fontWeight: 600, marginBottom: '4px' } }, 'Import Template Layout'),
            el('p', { style: { fontSize: '12px', color: '#757575', marginBottom: '8px' } },
                'Replace the current editor content with a layout from a JSON file.'
            ),
            el(EBImportButton, { onClose: onClose })
        );
    }

    function EBImportButton(props) {
        var statusState = useState('');
        var status      = statusState[0];
        var setStatus   = statusState[1];

        function handleFile(e) {
            var file = e.target.files && e.target.files[0];
            if (!file) return;

            var reader = new FileReader();
            reader.onload = function (ev) {
                var data;
                try { data = JSON.parse(ev.target.result); } catch (err) {
                    setStatus('Invalid JSON file.');
                    return;
                }
                if (!data.blocks || !Array.isArray(data.blocks)) {
                    setStatus('This file does not contain a valid template.');
                    return;
                }
                if (!window.confirm('This will replace all current content with the imported template. Continue?')) return;

                function createBlock(def) {
                    var name  = def.name || def.blockName;
                    var attrs = def.attributes || def.attrs || {};
                    var inner = (def.innerBlocks || []).map(createBlock);
                    return wp.blocks.createBlock(name, attrs, inner);
                }

                wp.data.dispatch('core/block-editor').resetBlocks(data.blocks.map(createBlock));
                setStatus('');
                e.target.value = '';
                props.onClose && props.onClose();
            };
            reader.readAsText(file);
        }

        return el(Fragment, {},
            el(Button, {
                variant: 'secondary',
                onClick: function () {
                    var input = document.getElementById('eb-import-file-toolbar');
                    if (input) input.click();
                },
            }, 'Import from JSON'),
            el('input', {
                id:       'eb-import-file-toolbar',
                type:     'file',
                accept:   '.json',
                style:    { display: 'none' },
                onChange: handleFile,
            }),
            status && el('p', { style: { marginTop: '8px', fontSize: '12px', color: '#cc1818' } }, status)
        );
    }

    // ── EBToolbarActions main component ───────────────────────────────────────

    function EBToolbarActions() {
        var postType = useSelect(function (s) { return s('core/editor').getCurrentPostType(); });
        if (postType !== 'eb_email_template') return null;

        var previewOpen   = useState(false);
        var testOpen      = useState(false);
        var exportOpen    = useState(false);

        var showPreview  = previewOpen[0];   var setPreview  = previewOpen[1];
        var showTest     = testOpen[0];      var setTest     = testOpen[1];
        var showExport   = exportOpen[0];    var setExport   = exportOpen[1];

        var SaveTemplateComp = window.EBSaveTemplateButton || null;

        return el(Fragment, {},
            // Toolbar buttons
            el('div', { style: { display: 'flex', alignItems: 'center', gap: '4px', marginRight: '4px' } },
                el(Button, { variant: 'tertiary', size: 'compact', icon: 'visibility',     label: 'Preview',   onClick: function () { setPreview(true); } }),
                el(Button, { variant: 'tertiary', size: 'compact', icon: 'email-alt',      label: 'Send Test', onClick: function () { setTest(true); } }),
                // SaveTemplateButton manages its own open/close state internally
                SaveTemplateComp ? el(SaveTemplateComp) : null,
                el(Button, { variant: 'tertiary', size: 'compact', icon: 'download',       label: 'Export',    onClick: function () { setExport(true); } })
            ),

            // Modals
            showPreview  && el(PreviewModal,  { onClose: function () { setPreview(false); } }),
            showTest     && el(SendTestModal, { onClose: function () { setTest(false); } }),
            showExport   && el(ExportModal,   { onClose: function () { setExport(false); } })
        );
    }

    // ── Mount toolbar via subscribe ───────────────────────────────────────────

    var toolbarMounted = false;

    var unsubToolbar = wp.data.subscribe(function () {
        if (toolbarMounted) return;

        var headerSettings =
            document.querySelector('.edit-post-header__settings') ||
            document.querySelector('.editor-header__settings');
        if (!headerSettings) return;

        toolbarMounted = true;
        unsubToolbar();

        var container = document.createElement('div');
        container.id = 'eb-toolbar-actions';
        headerSettings.prepend(container);

        var comp = el(EBToolbarActions);
        if (wp.element.createRoot) {
            wp.element.createRoot(container).render(comp);
        } else {
            wp.element.render(comp, container);
        }
    });

    // Safety: stop subscribing after 30s
    setTimeout(function () { try { unsubToolbar(); } catch (e) {} }, 30000);

})(window.wp);
