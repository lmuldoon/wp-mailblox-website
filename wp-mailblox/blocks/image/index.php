<?php
if (!defined('ABSPATH')) exit;

function eb_register_image_block()
{

    wp_register_script(
        'eb-image-block',
        EB_PLUGIN_URL . 'blocks/image/block.js',
        ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components'],
        EB_VERSION
    );

    register_block_type('email-builder/image', [
        'editor_script' => 'eb-image-block',
        'parent'          => ['email-builder/column'],
        'render_callback' => '__return_null',
    ]);
}
add_action('init', 'eb_register_image_block');
