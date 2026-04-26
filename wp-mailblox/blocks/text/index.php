<?php
if (!defined('ABSPATH')) exit;

function eb_register_text_block()
{

    wp_register_script(
        'eb-text-block',
        EB_PLUGIN_URL . 'blocks/text/block.js',
        ['wp-blocks', 'wp-element', 'wp-editor'],
        EB_VERSION
    );

    register_block_type('email-builder/text', [
        'editor_script' => 'eb-text-block',
        'parent'          => ['email-builder/column'],
        'render_callback' => '__return_null',
    ]);
}
add_action('init', 'eb_register_text_block');
