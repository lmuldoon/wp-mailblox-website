<?php
if (!defined('ABSPATH')) exit;

register_block_type('email-builder/menu', [
    'editor_script'   => 'eb-block-menu',
    'parent'          => ['email-builder/column'],
    'render_callback' => '__return_null',
]);