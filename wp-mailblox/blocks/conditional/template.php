<?php
if (!defined('ABSPATH')) exit;

/**
 * Conditional block template.
 *
 * Wraps inner blocks in the platform-specific conditional syntax.
 * The `field` attribute is a canonical key (e.g. FNAME) which is resolved
 * to the correct if/endif tags from the current platform's conditional_fields.
 */

$field       = $template_attributes['field']       ?? '';
$inner_blocks = $template_attributes['innerBlocks'] ?? [];

if (empty($field) || empty($inner_blocks)) {
    // No field configured or no content — render inner blocks as-is
    foreach ($inner_blocks as $block) {
        echo eb_render_block_recursive($block, $template_context);
    }
    return;
}

// Resolve platform from render context (set by eb_render_blocks)
$platform      = $template_context['platform'] ?? 'mailchimp';
$platform_file = EB_PLUGIN_PATH . 'platforms/' . sanitize_file_name($platform) . '.json';
$if_tag        = '';
$endif_tag     = '';

if (file_exists($platform_file)) {
    $pdata = json_decode(file_get_contents($platform_file), true);
    if (!empty($pdata['conditional_fields'][$field])) {
        $cf        = $pdata['conditional_fields'][$field];
        $if_tag    = $cf['if']    ?? '';
        $endif_tag = $cf['endif'] ?? '';
    }
}

// If this platform has no conditional support for this field, render normally
if (empty($if_tag)) {
    foreach ($inner_blocks as $block) {
        echo eb_render_block_recursive($block, $template_context);
    }
    return;
}

echo $if_tag . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput

foreach ($inner_blocks as $block) {
    echo eb_render_block_recursive($block, $template_context);
}

echo "\n" . $endif_tag . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput
