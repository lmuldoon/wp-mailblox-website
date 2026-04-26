<?php
if (!defined('ABSPATH')) exit;

register_block_type('email-builder/social', [
    'editor_script'   => 'eb-block-social',
    'parent'          => ['email-builder/column'],
    'render_callback' => '__return_null',
]);