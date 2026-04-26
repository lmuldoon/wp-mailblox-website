<?php
if (!defined('ABSPATH')) exit;

register_block_type('email-builder/footer', [
    'editor_script' => 'eb-block-footer',
    'render_callback' => '__return_null',
    'parent'          => ['email-builder/column'],
]);