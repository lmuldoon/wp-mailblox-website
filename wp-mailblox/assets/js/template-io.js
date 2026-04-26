(function ($) {
    'use strict';

    $(document).ready(function () {

        // Export — fetch block JSON from server and download as a file
        $('#eb-export-template-btn').on('click', function () {
            var btn    = $(this);
            var postId = btn.data('post-id');
            var nonce  = btn.data('nonce');

            btn.prop('disabled', true).text('Exporting…');

            $.post(ajaxurl, {
                action:  'eb_export_template',
                post_id: postId,
                nonce:   nonce,
            }, function (res) {
                btn.prop('disabled', false).text('Export Template');

                if (!res.success) {
                    alert(res.data || 'Export failed.');
                    return;
                }

                var json = JSON.stringify(res.data, null, 2);
                var blob = new Blob([json], { type: 'application/json' });
                var url  = URL.createObjectURL(blob);
                var a    = document.createElement('a');
                a.href     = url;
                a.download = (res.data.title || 'template')
                    .replace(/[^a-z0-9_-]/gi, '-')
                    .replace(/-+/g, '-')
                    .toLowerCase() + '.json';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            }).fail(function () {
                btn.prop('disabled', false).text('Export Template');
                alert('Export request failed. Please try again.');
            });
        });

        // Import — trigger file picker
        $('#eb-import-template-btn').on('click', function () {
            $('#eb-import-template-file').trigger('click');
        });

        // Import — handle file selection
        $('#eb-import-template-file').on('change', function () {
            var file = this.files[0];
            if (!file) return;

            var $status = $('#eb-import-template-status');
            $status.text('');

            var reader = new FileReader();

            reader.onload = function (e) {
                var data;

                try {
                    data = JSON.parse(e.target.result);
                } catch (err) {
                    $status.text('Invalid JSON file.');
                    return;
                }

                if (!data.blocks || !Array.isArray(data.blocks)) {
                    $status.text('This file does not contain a valid template (missing blocks array).');
                    return;
                }

                if (!window.confirm('This will replace all current content with the imported template. Continue?')) {
                    return;
                }

                function createBlock(def) {
                    var inner = (def.innerBlocks || []).map(createBlock);
                    return wp.blocks.createBlock(def.name, def.attributes || {}, inner);
                }

                var blocks = data.blocks.map(createBlock);
                wp.data.dispatch('core/block-editor').resetBlocks(blocks);

                $status.css('color', '#1a7a1a').text('Template imported successfully.');
                setTimeout(function () { $status.text(''); }, 4000);

                // Allow same file to be re-imported if needed
                $('#eb-import-template-file').val('');
            };

            reader.onerror = function () {
                $status.text('Could not read the file.');
            };

            reader.readAsText(file);
        });

    });

})(jQuery);
