<?php
if (!defined('ABSPATH')) exit;

function eb_register_video_block()
{
    wp_register_script(
        'eb-video-block',
        EB_PLUGIN_URL . 'blocks/video/block.js',
        ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components'],
        EB_VERSION
    );

    register_block_type('email-builder/video', [
        'editor_script'   => 'eb-video-block',
        'parent'          => ['email-builder/column'],
        'render_callback' => '__return_null',
    ]);
}
add_action('init', 'eb_register_video_block');
