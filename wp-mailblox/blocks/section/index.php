<?php
if (!defined('ABSPATH')) exit;

function eb_register_section_block() {
    register_block_type('email-builder/section', [
        'render_callback' => '__return_null',
    ]);
}
add_action('init', 'eb_register_section_block');