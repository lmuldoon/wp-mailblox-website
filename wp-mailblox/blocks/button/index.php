<?php
if (!defined('ABSPATH')) exit;

function eb_register_button_block()
{
    wp_register_script(
        'eb-button-block',
        EB_PLUGIN_URL . 'blocks/button/block.js',
        ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components'],
        EB_VERSION
    );

    wp_register_style(
        'eb-button-block-style',
        EB_PLUGIN_URL . 'blocks/button/style.css',
        [],
        EB_VERSION
    );

    register_block_type('email-builder/button', [
        'editor_script' => 'eb-button-block',
        'editor_style'  => 'eb-button-block-style',
        'style'         => 'eb-button-block-style',
        'parent'          => ['email-builder/column'],

        'render_callback' => '__return_null',
    ]);
}

add_action('init', 'eb_register_button_block');