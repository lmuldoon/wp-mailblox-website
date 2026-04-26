<?php
if (!defined('ABSPATH')) exit;

function eb_register_column_block() {

    wp_register_script(
        'eb-column-block',
        EB_PLUGIN_URL . 'blocks/column/block.js',
        ['wp-blocks', 'wp-element', 'wp-block-editor'],
        EB_VERSION
    );

    wp_register_style(
        'eb-column-block-style',
        EB_PLUGIN_URL . 'blocks/column/style.css',
        [],
        EB_VERSION
    );

    register_block_type('email-builder/column', [
        'editor_script' => 'eb-column-block',
        'editor_style'  => 'eb-column-block-style',
        'style'         => 'eb-column-block-style',

        // ✅ Restrict this block to ONLY be used inside columns block
        'parent' => ['email-builder/columns'],

        'render_callback' => '__return_null',
    ]);
}

add_action('init', 'eb_register_column_block');