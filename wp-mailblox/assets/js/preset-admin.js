jQuery(document).ready(function ($) {

    // ── Colour pickers ────────────────────────────────────────────────────────
    $('.eb-color-field').wpColorPicker();

    // ── Container width display ───────────────────────────────────────────────
    $('#eb_container_width').on('input', function () {
        $('#eb-container-width-display').text(this.value);
    });

    // ── Toggle group (button-group toggles replacing radio buttons) ──────────
    $(document).on('click', '.eb-toggle-group .eb-toggle-option', function () {
        var $btn     = $(this);
        var $group   = $btn.closest('.eb-toggle-group');
        var targetId = $group.data('target');
        var value    = $btn.data('value');

        $group.find('.eb-toggle-option').removeClass('active');
        $btn.addClass('active');
        $('#' + targetId).val(value);

        // Font type: show/hide websafe vs google fields
        if (targetId && targetId.indexOf('_font_type') !== -1) {
            var section = targetId.replace('eb_', '').replace('_font_type', '');
            if (value === 'websafe') {
                $('.eb-font-websafe.eb-font-' + section).show();
                $('.eb-font-google.eb-font-'  + section).hide();
            } else {
                $('.eb-font-websafe.eb-font-' + section).hide();
                $('.eb-font-google.eb-font-'  + section).show();
            }
        }
    });

    // ── Dark mode swatch presets ──────────────────────────────────────────────
    $('.eb-dark-swatch-preset').on('click', function () {
        var color  = $(this).data('color');
        var target = $(this).data('target');
        $('#' + target).val(color).trigger('change');
        $('.eb-dark-swatch-preset[data-target="' + target + '"]').css('border-color', '#ccc');
        $(this).css('border-color', '#2271b1');
    });

    // ── Logo uploader ─────────────────────────────────────────────────────────
    var logoUploader;

    $('#eb-logo-upload-btn').on('click', function (e) {
        e.preventDefault();
        if (logoUploader) { logoUploader.open(); return; }
        logoUploader = wp.media({
            title:    'Select Logo',
            button:   { text: 'Use this image' },
            multiple: false,
            library:  { type: 'image' },
        });
        logoUploader.on('select', function () {
            var attachment = logoUploader.state().get('selection').first().toJSON();
            $('#eb_logo_url').val(attachment.url);
            if ($('#eb-logo-preview').length) {
                $('#eb-logo-preview').attr('src', attachment.url).show();
            } else {
                $('<img id="eb-logo-preview">').attr('src', attachment.url).css({
                    maxWidth: '200px', height: 'auto', display: 'block',
                    border: '1px solid #ddd', padding: '4px', marginBottom: '8px',
                }).insertBefore('#eb-logo-upload-btn');
            }
            $('#eb-logo-upload-btn').text('Replace Logo');
            $('#eb-logo-remove-btn').show();
        });
        logoUploader.open();
    });

    $('#eb-logo-remove-btn').on('click', function () {
        $('#eb_logo_url').val('');
        $('#eb-logo-preview').remove();
        $(this).hide();
        $('#eb-logo-upload-btn').text('Upload Logo');
    });

    // ── Dark logo toggle ──────────────────────────────────────────────────────
    $('#eb_dark_logo_enabled').on('change', function () {
        $(this).closest('.components-form-toggle').toggleClass('is-checked', this.checked);
        $('#eb-dark-logo-wrap').toggle(this.checked);
    });

    // ── Dark logo uploader ────────────────────────────────────────────────────
    var darkLogoUploader;

    $('#eb-dark-logo-upload-btn').on('click', function (e) {
        e.preventDefault();
        if (darkLogoUploader) { darkLogoUploader.open(); return; }
        darkLogoUploader = wp.media({
            title:    'Select Dark Mode Logo',
            button:   { text: 'Use this image' },
            multiple: false,
            library:  { type: 'image' },
        });
        darkLogoUploader.on('select', function () {
            var attachment = darkLogoUploader.state().get('selection').first().toJSON();
            $('#eb_dark_logo_url').val(attachment.url);
            if ($('#eb-dark-logo-preview').length) {
                $('#eb-dark-logo-preview').attr('src', attachment.url).show();
            } else {
                $('<img id="eb-dark-logo-preview">').attr('src', attachment.url).css({
                    maxWidth: '200px', display: 'block',
                    border: '1px solid #ddd', padding: '4px', marginBottom: '8px',
                }).insertBefore('#eb-dark-logo-upload-btn');
            }
            $('#eb-dark-logo-upload-btn').text('Replace Dark Logo');
            $('#eb-dark-logo-remove-btn').show();
        });
        darkLogoUploader.open();
    });

    $('#eb-dark-logo-remove-btn').on('click', function () {
        $('#eb_dark_logo_url').val('');
        $('#eb-dark-logo-preview').remove();
        $(this).hide();
        $('#eb-dark-logo-upload-btn').text('Upload Dark Logo');
    });

    // ── Body background image toggle ─────────────────────────────────────────
    $('#eb_body_bg_image_enabled').on('change', function () {
        $('#eb-body-bg-image-wrap').toggle(this.checked);
    });

    // ── Radio button group visual toggle (bg image options) ───────────────────
    $('#eb-body-bg-image-wrap').on('change', 'input[type="radio"]', function () {
        var name = $(this).attr('name');
        $('input[name="' + name + '"]').each(function () {
            var $label = $(this).closest('label');
            if ($(this).is(':checked')) {
                $label.css({ background: '#2271b1', color: '#fff', border: '1px solid #2271b1' });
            } else {
                $label.css({ background: '#f6f7f7', color: '#1e1e1e', border: '1px solid #ddd' });
            }
        });
    });

    // ── Body background image uploader ────────────────────────────────────────
    var bodyBgUploader;

    $('#eb-body-bg-image-upload-btn').on('click', function (e) {
        e.preventDefault();
        if (bodyBgUploader) { bodyBgUploader.open(); return; }
        bodyBgUploader = wp.media({
            title:    'Select Background Image',
            button:   { text: 'Use this image' },
            multiple: false,
            library:  { type: 'image' },
        });
        bodyBgUploader.on('select', function () {
            var attachment = bodyBgUploader.state().get('selection').first().toJSON();
            $('#eb_body_bg_image_url').val(attachment.url);
            if ($('#eb-body-bg-image-preview').length) {
                $('#eb-body-bg-image-preview').attr('src', attachment.url).show();
            } else {
                $('<img id="eb-body-bg-image-preview">').attr('src', attachment.url).css({
                    maxWidth: '200px', height: 'auto', display: 'block',
                    border: '1px solid #ddd', padding: '4px', marginBottom: '8px',
                }).insertBefore('#eb-body-bg-image-upload-btn');
            }
            $('#eb-body-bg-image-upload-btn').text('Replace Image');
            $('#eb-body-bg-image-remove-btn').show();
        });
        bodyBgUploader.open();
    });

    $('#eb-body-bg-image-remove-btn').on('click', function () {
        $('#eb_body_bg_image_url').val('');
        $('#eb-body-bg-image-preview').remove();
        $(this).hide();
        $('#eb-body-bg-image-upload-btn').text('Upload Image');
    });

    // ── Export preset ─────────────────────────────────────────────────────────
    $('#eb-export-preset-btn').on('click', function () {
        var postId = $(this).data('post-id');
        $.post(ebPresetAdmin.ajaxUrl, {
            action:  'eb_export_preset',
            nonce:   ebPresetAdmin.exportNonce,
            post_id: postId,
        }, function (res) {
            if (!res.success) { alert(res.data || 'Export failed.'); return; }
            var json = JSON.stringify(res.data, null, 2);
            var blob = new Blob([json], { type: 'application/json' });
            var url  = URL.createObjectURL(blob);
            var a    = document.createElement('a');
            a.href     = url;
            a.download = (res.data._name || 'preset').replace(/[^a-z0-9_-]/gi, '-').toLowerCase() + '.json';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        });
    });

    // ── Import preset ─────────────────────────────────────────────────────────
    $('#eb-import-preset-btn').on('click', function () {
        $('#eb-import-preset-file').trigger('click');
    });

    $('#eb-import-preset-file').on('change', function () {
        var file = this.files[0];
        if (!file) return;
        var reader = new FileReader();
        reader.onload = function (e) {
            var json = e.target.result;
            try { JSON.parse(json); } catch (err) { alert('Invalid JSON file.'); return; }
            $('#eb-import-status').text('Importing\u2026');
            $.post(ebPresetAdmin.ajaxUrl, {
                action: 'eb_import_preset',
                nonce:  ebPresetAdmin.importNonce,
                json:   json,
            }, function (res) {
                if (!res.success) { $('#eb-import-status').text(res.data || 'Import failed.'); return; }
                window.location.href = res.data.redirect;
            });
        };
        reader.readAsText(file);
    });

    // ── Tab switching ─────────────────────────────────────────────────────────
    $(document).on('click', '.eb-tab-btn', function () {
        var tab = $(this).data('tab');
        $('.eb-tab-btn').removeClass('active');
        $(this).addClass('active');
        $('.eb-tab-panel').removeClass('active');
        $('.eb-tab-panel[data-panel="' + tab + '"]').addClass('active');
    });
});
