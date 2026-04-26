<?php
if (!defined('ABSPATH')) exit;

function eb_register_module_block() {
    register_block_type('email-builder/module', [
        'render_callback' => '__return_null',
    ]);
}
add_action('init', 'eb_register_module_block');
