<?php
if (!defined('ABSPATH')) exit;

/**
 * Convert hex to RGB
 */
function eb_hex_to_rgb($hex) {
    $hex = str_replace('#', '', $hex);

    return [
        hexdec(substr($hex, 0, 2)),
        hexdec(substr($hex, 2, 2)),
        hexdec(substr($hex, 4, 2)),
    ];
}

/**
 * Calculate luminance
 */
function eb_luminance($r, $g, $b) {
    $a = array_map(function ($v) {
        $v /= 255;
        return ($v <= 0.03928)
            ? $v / 12.92
            : pow(($v + 0.055) / 1.055, 2.4);
    }, [$r, $g, $b]);

    return $a[0]*0.2126 + $a[1]*0.7152 + $a[2]*0.0722;
}

/**
 * Contrast ratio
 */
function eb_contrast($rgb1, $rgb2) {
    $l1 = eb_luminance($rgb1[0], $rgb1[1], $rgb1[2]);
    $l2 = eb_luminance($rgb2[0], $rgb2[1], $rgb2[2]);

    $brightest = max($l1, $l2);
    $darkest   = min($l1, $l2);

    return ($brightest + 0.05) / ($darkest + 0.05);
}

/**
 * Get best contrast colour (black or white)
 */
function eb_get_contrast_colour($background, $context = []) {

    // Get preset colours from context (passed down from render)
    $styles = $context['styles'] ?? [];

    $dark  = $styles['text_color_dark']  ?? '#000000';
    $light = $styles['text_color_light'] ?? '#FFFFFF';

    // Convert colours
    $bg    = eb_hex_to_rgb($background);
    $dark_rgb  = eb_hex_to_rgb($dark);
    $light_rgb = eb_hex_to_rgb($light);

    // Calculate contrast
    $contrast_dark  = eb_contrast($bg, $dark_rgb);
    $contrast_light = eb_contrast($bg, $light_rgb);

    // Return best contrast
    return ($contrast_dark > $contrast_light) ? $dark : $light;
}