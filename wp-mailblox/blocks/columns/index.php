<?php
if (!defined('ABSPATH')) exit;

function eb_register_columns_block() {

    wp_register_script(
        'eb-columns-block',
        EB_PLUGIN_URL . 'blocks/columns/block.js',
        ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components'],
        EB_VERSION
    );

    register_block_type('email-builder/columns', [
        'editor_script' => 'eb-columns-block',
        'parent'          => ['email-builder/section'],
        'render_callback' => '__return_null',
    ]);
}

add_action('init', 'eb_register_columns_block');