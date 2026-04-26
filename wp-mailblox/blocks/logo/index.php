<?php
if (!defined('ABSPATH')) exit;

register_block_type('email-builder/logo', [
    'editor_script'   => 'eb-block-logo',
    'parent'          => ['email-builder/column'],
    'render_callback' => '__return_null',
]);