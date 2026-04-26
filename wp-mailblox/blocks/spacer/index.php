<?php
if (!defined('ABSPATH')) exit;

function eb_register_spacer_block() {

    wp_register_script(
        'eb-spacer-block',
        EB_PLUGIN_URL . 'blocks/spacer/block.js',
        ['wp-blocks','wp-element','wp-editor','wp-components'],
        EB_VERSION
    );

    register_block_type('email-builder/spacer', [
        'editor_script' => 'eb-spacer-block',

        'render_callback' => '__return_null',
    ]);
}

add_action('init', 'eb_register_spacer_block');