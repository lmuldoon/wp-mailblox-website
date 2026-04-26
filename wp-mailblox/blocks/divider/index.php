<?php
if (!defined('ABSPATH')) exit;

function eb_register_divider_block() {

    wp_register_script(
        'eb-divider-block',
        EB_PLUGIN_URL . 'blocks/divider/block.js',
        ['wp-blocks','wp-element','wp-editor','wp-components'],
        EB_VERSION
    );

    register_block_type('email-builder/divider', [
        'editor_script' => 'eb-divider-block',
        'parent'          => ['email-builder/column'],
        'render_callback' => '__return_null',
    ]);
}

add_action('init', 'eb_register_divider_block');