<?php
if (!defined('ABSPATH')) exit;

$module_id = intval($template_attributes['moduleId'] ?? 0);
if (!$module_id) return;

$post = get_post($module_id);
if (!$post || $post->post_type !== 'eb_module') return;

$blocks = json_decode($post->post_content, true);
if (!is_array($blocks)) return;

/**
 * Bridge JS Gutenberg block format (name/attributes/innerBlocks)
 * to the format expected by eb_render_block_recursive (blockName/attrs/innerBlocks).
 * Handles both formats defensively in case the stored JSON already uses PHP format.
 */
function eb_normalize_module_block($block) {
    return [
        'blockName'   => $block['name']       ?? $block['blockName'] ?? '',
        'attrs'       => $block['attributes'] ?? $block['attrs']     ?? [],
        'innerBlocks' => array_map('eb_normalize_module_block', $block['innerBlocks'] ?? []),
    ];
}

foreach ($blocks as $block) {
    echo eb_render_block_recursive(eb_normalize_module_block($block), $template_context); // phpcs:ignore WordPress.Security.EscapeOutput
}
