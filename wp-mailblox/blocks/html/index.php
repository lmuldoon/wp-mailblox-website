<?php
if (!defined('ABSPATH')) exit;

function eb_register_html_block()
{
    wp_register_script(
        'eb-html-block',
        EB_PLUGIN_URL . 'blocks/html/block.js',
        ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components'],
        EB_VERSION
    );

    register_block_type('email-builder/html', [
        'editor_script' => 'eb-html-block',
        'parent'        => ['email-builder/column'],

        'render_callback' => '__return_null',
    ]);
}

add_action('init', 'eb_register_html_block');
