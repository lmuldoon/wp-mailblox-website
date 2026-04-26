<?php
if (!defined('ABSPATH')) exit;

function eb_register_conditional_block() {
    register_block_type('email-builder/conditional', [
        'render_callback' => '__return_null',
        'parent'          => ['email-builder/column'],
    ]);
}
add_action('init', 'eb_register_conditional_block');
