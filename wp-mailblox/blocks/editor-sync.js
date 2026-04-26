(function ($, wp) {

    const { apiFetch } = wp;
    const { dispatch, select } = wp.data;

    $(document).on('change', '#eb_preset', function () {

        const presetId = $(this).val();
        if (!presetId) return;

        apiFetch({
            path: '/email-builder/v1/preset/' + presetId
        }).then((data) => {

            // Update global editor data
            window.EB_EDITOR_DATA = {
                ...window.EB_EDITOR_DATA,
                ...data
            };

            // Force Gutenberg refresh
            dispatch('core/block-editor').__unstableMarkNextChangeAsNotPersistent();

            dispatch('core/block-editor').resetBlocks(
                select('core/block-editor').getBlocks()
            );

        }).catch(() => {
            dispatch('core/notices').createErrorNotice(
                'Failed to load preset data. Please reload the page and try again.',
                { type: 'snackbar', isDismissible: true }
            );
        });

    });

})(jQuery, window.wp);