<?php
if (!defined('ABSPATH')) exit;

// Global dark mode section colour registry
$eb_dark_section_colors = [];

// Global mobile border-radius registry
$eb_mobile_border_radii = [];

// Global mobile border-color registry
$eb_mobile_border_colors = [];

/**
 * Snap a padding value to the nearest 5px grid (0–50px).
 */
function eb_snap_to_5($val) {
    $v = intval($val);
    $snapped = round($v / 5) * 5;
    return max(0, min(50, $snapped));
}

/**
 * Build a string of mobile utility classes from block attributes.
 * Only emits a class when the attribute is explicitly set (not null/empty).
 *
 * @param array $attributes  Block attributes
 * @param bool  $has_padding Include mobile padding classes
 * @param bool  $has_align   Include mobile alignment class
 * @return string            Space-separated class string (may be empty)
 */
function eb_mobile_classes($attributes, $has_padding = true, $has_align = true) {
    $classes = [];
    if ($has_padding) {
        $dirs = ['Top' => 'pt', 'Bottom' => 'pb', 'Left' => 'pl', 'Right' => 'pr'];
        foreach ($dirs as $dir => $abbr) {
            $val = $attributes['mobilePadding' . $dir] ?? null;
            if ($val !== null && $val !== '') {
                $classes[] = 'eb-mob-' . $abbr . '-' . intval($val);
            }
        }
    }
    if ($has_align) {
        $align = $attributes['mobileAlign'] ?? '';
        if (!empty($align)) {
            $classes[] = 'eb-mob-al-' . esc_attr($align);
        }
    }
    return implode(' ', $classes);
}

function eb_register_mobile_border_radius($class, $tl, $tr, $br, $bl) {
    global $eb_mobile_border_radii;
    $eb_mobile_border_radii[$class] = [$tl, $tr, $br, $bl];
}

function eb_get_mobile_border_radii() {
    global $eb_mobile_border_radii;
    return $eb_mobile_border_radii;
}

function eb_register_mobile_border_color($class, $color) {
    global $eb_mobile_border_colors;
    $eb_mobile_border_colors[$class] = $color;
}

function eb_get_mobile_border_colors() {
    global $eb_mobile_border_colors;
    return $eb_mobile_border_colors;
}

function eb_register_dark_section_color($color, $class)
{
    global $eb_dark_section_colors;
    $eb_dark_section_colors[$class] = $color;
}

function eb_get_dark_section_colors()
{
    global $eb_dark_section_colors;
    return $eb_dark_section_colors;
}

/**
 * Recursively render a block and its inner blocks
 */
function eb_render_block_recursive($block, $context = [])
{
    if (!isset($context['styles'])) {
        $context['styles'] = [];
    }
    $block_name   = $block['blockName'] ?? '';
    $attrs        = $block['attrs'] ?? [];
    $inner_blocks = $block['innerBlocks'] ?? [];

    // Get global styles
    $styles = $context['styles'] ?? [];

    // Merge preset styles + block attributes
    $template_attributes = array_merge($styles, $attrs);

    // Inject inner blocks into attributes so templates can access them
    $template_attributes['innerBlocks'] = $block['innerBlocks'] ?? [];

    // Pass RAW inner blocks (NOT rendered HTML)
    $template_inner_blocks = $inner_blocks;

    $template_context = $context;

    // SECTION BLOCK (preserve padding logic)
    if ($block_name === 'email-builder/section') {

        $padding_left  = $attrs['paddingLeft'] ?? 20;
        $padding_right = $attrs['paddingRight'] ?? 20;

        $child_context = $context;
        $child_context['section_padding'] = [
            'left'  => $padding_left,
            'right' => $padding_right,
        ];
        $bg_attr = $attrs['backgroundColor'] ?? '';
        $bg = ($bg_attr && $bg_attr !== 'transparent') ? $bg_attr : ($context['background'] ?? '#ffffff');
        $child_context['background'] = $bg;
        $child_context['has_background_image'] = !empty($attrs['backgroundImageEnabled']) && !empty($attrs['backgroundImageUrl']);

        $template_context      = $child_context;
        $template_inner_blocks = $inner_blocks;

        ob_start();
        include EB_PLUGIN_PATH . 'blocks/section/template.php';
        return ob_get_clean();
    }

    // CONDITIONAL BLOCK — wrap inner blocks in platform conditional syntax
    if ($block_name === 'email-builder/conditional') {
        $template_attributes['innerBlocks'] = $inner_blocks;
        $template_context['template_id']    = $template_context['template_id'] ?? get_the_ID();
        ob_start();
        include EB_PLUGIN_PATH . 'blocks/conditional/template.php';
        return ob_get_clean();
    }

    // COLUMN BLOCK — propagate its own background colour into child context
    if ($block_name === 'email-builder/column') {
        $child_context = $context;
        $column_bg = $attrs['backgroundColor'] ?? '';
        if ($column_bg && $column_bg !== 'transparent') {
            $child_context['background'] = $column_bg;
        }
        $child_context['has_background_image'] = !empty($attrs['backgroundImageEnabled']) && !empty($attrs['backgroundImageUrl']);

        $template_context      = $child_context;
        $template_inner_blocks = $inner_blocks;
        $template_attributes   = array_merge($styles, $attrs);
        $template_attributes['innerBlocks'] = $inner_blocks;

        ob_start();
        include EB_PLUGIN_PATH . 'blocks/column/template.php';
        return ob_get_clean();
    }

    // COLUMNS BLOCK — propagate background context to child columns
    if ($block_name === 'email-builder/columns') {
        $child_context = $context;
        $columns_bg = $attrs['backgroundColor'] ?? '';
        if ($columns_bg && $columns_bg !== 'transparent') {
            $child_context['background'] = $columns_bg;
        }
        $child_context['has_background_image'] = !empty($attrs['backgroundImageEnabled']) && !empty($attrs['backgroundImageUrl']);

        $template_context      = $child_context;
        $template_inner_blocks = $inner_blocks;

        ob_start();
        include EB_PLUGIN_PATH . 'blocks/columns/template.php';
        return ob_get_clean();
    }

    // Load template — check top-level blocks/ first, then subdirectories for prefixed blocks
    // e.g. email-builder/wc-product → blocks/wc-product/ → blocks/woocommerce/product/
    $block_slug    = str_replace('email-builder/', '', $block_name);
    $template_file = EB_PLUGIN_PATH . 'blocks/' . $block_slug . '/template.php';
    if (!file_exists($template_file)) {
        // Strip known prefixes and look in matching subdirectory
        // wc- → woocommerce/, extend here for future block groups
        $prefix_map = ['wc-' => 'woocommerce/'];
        foreach ($prefix_map as $prefix => $subdir) {
            if (strpos($block_slug, $prefix) === 0) {
                $template_file = EB_PLUGIN_PATH . 'blocks/' . $subdir . substr($block_slug, strlen($prefix)) . '/template.php';
                break;
            }
        }
    }

    if (file_exists($template_file)) {
        ob_start();
        include $template_file;
        return ob_get_clean();
    }

    return '';
}

/**
 * Render a list of blocks with global typography injected
 */
function eb_render_blocks($blocks, $template_id = null, $tags = [], $platform_override = null)
{
    if (!$template_id) {
        $template_id = get_the_ID();
    }

    $preset_id       = get_post_meta($template_id, 'eb_preset', true);
    $preset_settings = eb_get_preset_settings($preset_id);

    $defaults = [
        'preset_logo_url' => '',
        'preset_logo_alt' => '',
        'preset_logo_dark_enabled' => 0,
        'preset_logo_dark_url' => '',
        'container_width' => 640,
        'heading_font'           => 'Arial',
        'heading_font_stack'     => 'Arial, Helvetica, sans-serif',
        'heading_font_type'      => 'websafe',
        'heading_font_weight'    => 700,
        'heading_size'           => 28,
        'heading_size_mobile'    => 24,
        'heading_line_height'    => 1.3,
        'subheading_font'        => 'Arial',
        'subheading_font_stack'  => 'Arial, Helvetica, sans-serif',
        'subheading_font_type'   => 'websafe',
        'subheading_font_weight' => 400,
        'subheading_size'        => 24,
        'subheading_size_mobile' => 20,
        'subheading_line_height' => 1.3,
        'body_font'              => 'Helvetica',
        'body_font_stack'        => 'Helvetica, Arial, sans-serif',
        'body_font_type'         => 'websafe',
        'body_font_weight'       => 400,
        'body_size'              => 18,
        'body_size_mobile'       => 16,
        'body_line_height'       => 1.5,
        'button_font'            => 'Helvetica',
        'button_font_stack'      => 'Helvetica, Arial, sans-serif',
        'button_font_type'       => 'websafe',
        'button_font_weight'     => 700,
        'button_size'            => 16,
        'button_size_mobile'     => 14,
        'button_line_height'     => 1.2,
        'bg_color'               => '#ffffff',
        'text_color_dark'        => '#000000',
        'text_color_light'       => '#ffffff',
        'dark_bg_color'          => '#121212',
        'dark_text_color'        => '#ffffff',
        'dark_button_color'      => '#ffffff',
        'dark_button_text_color' => '#000000',
        'dark_link_color'        => '#ffffff',
    ];

    $global_styles = array_merge($defaults, $preset_settings);

    $platform = $platform_override
             ?: get_post_meta($template_id, 'eb_platform', true)
             ?: get_post_meta($preset_id, 'eb_platform', true)
             ?: 'mailchimp';

    $utm_defaults = [
        'utmSource'   => get_post_meta($template_id, 'eb_utm_source',   true) ?: '',
        'utmMedium'   => get_post_meta($template_id, 'eb_utm_medium',   true) ?: '',
        'utmCampaign' => get_post_meta($template_id, 'eb_utm_campaign', true) ?: '',
        'utmContent'  => get_post_meta($template_id, 'eb_utm_content',  true) ?: '',
        'utmTerm'     => get_post_meta($template_id, 'eb_utm_term',     true) ?: '',
    ];

    $context = [
        'container_width' => $global_styles['container_width'] ?? 640,
        'section_padding' => [
            'left'  => 0,
            'right' => 0,
        ],
        'styles'       => $global_styles,
        'background'   => $global_styles['bg_color'] ?? '#ffffff',
        'tags'         => $tags,
        'platform'     => $platform,
        'template_id'  => $template_id,
        'utm_defaults' => $utm_defaults,
    ];

    $html = '';

    foreach ($blocks as $block) {
        $html .= eb_render_block_recursive($block, $context);
    }

    $html = eb_add_class_to_links($html, 'eb-link');

    return $html;
}

/**
 * Get platform tags (Mailchimp / Campaign Monitor etc.)
 */
function eb_get_platform_tags($platform = 'mailchimp')
{
    $json_file = EB_PLUGIN_PATH . "platforms/{$platform}.json";

    if (!file_exists($json_file)) return [];

    $data = json_decode(file_get_contents($json_file), true);
    if (!$data) return [];

    $tags = [];

    foreach ($data as $group) {
        if (!is_array($group)) continue;
        foreach ($group as $key => $value) {
            if (is_array($value) && isset($value['tag'])) {
                // New format — extract just the tag value
                $tags[$key] = $value['tag'];
            } elseif (!is_array($value)) {
                // Old format fallback
                $tags[$key] = $value;
            }
        }
    }

    return $tags;
}

/**
 * Append UTM parameters to a URL.
 * Block-level attributes take precedence; template-level defaults fill any gaps.
 * Skips merge-tag URLs and empty URLs. Only appends to real http/https URLs.
 */
function eb_append_utm($url, $attributes, $defaults = []) {
    if (empty($url)) return $url;
    // Only append UTM to real HTTP URLs — skip any platform merge tag format
    if (!preg_match('#^https?://#i', $url)) return $url;
    $params = [];
    $resolve = function($key) use ($attributes, $defaults) {
        return !empty($attributes[$key]) ? $attributes[$key] : ($defaults[$key] ?? '');
    };
    if ($v = $resolve('utmSource'))   $params['utm_source']   = $v;
    if ($v = $resolve('utmMedium'))   $params['utm_medium']   = $v;
    if ($v = $resolve('utmCampaign')) $params['utm_campaign'] = $v;
    if ($v = $resolve('utmContent'))  $params['utm_content']  = $v;
    if ($v = $resolve('utmTerm'))     $params['utm_term']     = $v;
    if (empty($params)) return $url;
    $separator = (strpos($url, '?') !== false) ? '&' : '?';
    return $url . $separator . http_build_query($params);
}

/**
 * Add class to all links
 */
function eb_add_class_to_links($html, $class = 'eb-link')
{
    if (empty($html)) return $html;

    return preg_replace_callback(
        '/<a\s([^>]*)>/i',
        function ($matches) use ($class) {
            $attrs = $matches[1];

            if (preg_match('/class=["\']([^"\']*)["\']/', $attrs)) {
                $attrs = preg_replace(
                    '/class=["\']([^"\']*)["\']/',
                    'class="$1 ' . $class . '"',
                    $attrs
                );
            } else {
                $attrs .= ' class="' . $class . '"';
            }

            return '<a ' . $attrs . '>';
        },
        $html
    );
}

/**
 * Get all preset settings for a given preset ID
 */
function eb_get_preset_settings($preset_id)
{
    if (!$preset_id) {
        return [];
    }

    return [
        'preset_logo_url' => get_post_meta($preset_id, 'eb_logo_url', true) ?: '',
        'preset_logo_alt' => get_post_meta($preset_id, 'eb_logo_alt', true) ?: '',

        'preset_logo_dark_enabled' => get_post_meta($preset_id, 'eb_dark_logo_enabled', true) ?: '',
        'preset_logo_dark_url' => get_post_meta($preset_id, 'eb_dark_logo_url', true) ?: '',

        'container_width' => intval(get_post_meta($preset_id, 'eb_container_width', true) ?: 640),

        // Heading
        'heading_font'           => get_post_meta($preset_id, 'eb_heading_font', true) ?: 'Arial',
        'heading_font_stack'     => get_post_meta($preset_id, 'eb_heading_font_stack', true) ?: 'Arial, Helvetica, sans-serif',
        'heading_font_type'      => get_post_meta($preset_id, 'eb_heading_font_type', true) ?: 'websafe',
        'heading_font_weight'    => get_post_meta($preset_id, 'eb_heading_font_weight', true) ?: 700,
        'heading_size'           => get_post_meta($preset_id, 'eb_heading_size', true) ?: 24,
        'heading_size_mobile'    => get_post_meta($preset_id, 'eb_heading_size_mobile', true) ?: 20,
        'heading_line_height'    => get_post_meta($preset_id, 'eb_heading_line_height', true) ?: 1.3,

        // Subheading
        'subheading_font'        => get_post_meta($preset_id, 'eb_subheading_font', true) ?: 'Arial',
        'subheading_font_stack'  => get_post_meta($preset_id, 'eb_subheading_font_stack', true) ?: 'Arial, Helvetica, sans-serif',
        'subheading_font_type'   => get_post_meta($preset_id, 'eb_subheading_font_type', true) ?: 'websafe',
        'subheading_font_weight' => get_post_meta($preset_id, 'eb_subheading_font_weight', true) ?: 400,
        'subheading_size'        => get_post_meta($preset_id, 'eb_subheading_size', true) ?: 24,
        'subheading_size_mobile' => get_post_meta($preset_id, 'eb_subheading_size_mobile', true) ?: 20,
        'subheading_line_height'    => get_post_meta($preset_id, 'eb_subheading_line_height', true) ?: 1.3,

        // Body
        'body_font'              => get_post_meta($preset_id, 'eb_body_font', true) ?: 'Helvetica',
        'body_font_stack'        => get_post_meta($preset_id, 'eb_body_font_stack', true) ?: 'Helvetica, Arial, sans-serif',
        'body_font_type'         => get_post_meta($preset_id, 'eb_body_font_type', true) ?: 'websafe',
        'body_font_weight'       => get_post_meta($preset_id, 'eb_body_font_weight', true) ?: 400,
        'body_size'              => get_post_meta($preset_id, 'eb_body_size', true) ?: 16,
        'body_size_mobile'       => get_post_meta($preset_id, 'eb_body_size_mobile', true) ?: 14,
        'body_line_height'       => get_post_meta($preset_id, 'eb_body_line_height', true) ?: 1.5,
        'button_font'            => get_post_meta($preset_id, 'eb_button_font', true) ?: 'Helvetica',
        'button_font_stack'      => get_post_meta($preset_id, 'eb_button_font_stack', true) ?: 'Helvetica, Arial, sans-serif',
        'button_font_type'       => get_post_meta($preset_id, 'eb_button_font_type', true) ?: 'websafe',
        'button_font_weight'     => get_post_meta($preset_id, 'eb_button_font_weight', true) ?: 700,
        'button_size'            => get_post_meta($preset_id, 'eb_button_size', true) ?: 16,
        'button_size_mobile'     => get_post_meta($preset_id, 'eb_button_size_mobile', true) ?: 14,
        'button_line_height'     => get_post_meta($preset_id, 'eb_button_line_height', true) ?: 1.2,

        // Colours
        'bg_color'               => get_post_meta($preset_id, 'eb_body_bg_color', true) ?: '#ffffff',
        'body_bg_image_enabled'  => (bool) get_post_meta($preset_id, 'eb_body_bg_image_enabled', true),
        'body_bg_image_url'      => get_post_meta($preset_id, 'eb_body_bg_image_url', true) ?: '',
        'body_bg_image_repeat'   => get_post_meta($preset_id, 'eb_body_bg_image_repeat', true) ?: 'no-repeat',
        'body_bg_image_pos_x'    => get_post_meta($preset_id, 'eb_body_bg_image_position_x', true) ?: 'center',
        'body_bg_image_pos_y'    => get_post_meta($preset_id, 'eb_body_bg_image_position_y', true) ?: 'center',
        'body_bg_image_size_w'   => get_post_meta($preset_id, 'eb_body_bg_image_size_w', true) ?: 'cover',
        'text_color_dark'        => get_post_meta($preset_id, 'eb_text_color_dark', true) ?: '#000000',
        'text_color_light'       => get_post_meta($preset_id, 'eb_text_color_light', true) ?: '#ffffff',
        'button_color'           => get_post_meta($preset_id, 'eb_button_color', true) ?: '#000000',
        'button_text_color'      => get_post_meta($preset_id, 'eb_button_text_color', true) ?: '',
        'sale_price_color'       => get_post_meta($preset_id, 'eb_sale_price_color', true) ?: '#c0392b',

        // Dark mode
        'dark_bg_color'          => get_post_meta($preset_id, 'eb_dark_bg_color', true) ?: '#121212',
        'dark_text_color'        => get_post_meta($preset_id, 'eb_dark_text_color', true) ?: '#ffffff',
        'dark_button_color'      => get_post_meta($preset_id, 'eb_dark_button_color', true) ?: '#ffffff',
        'dark_button_text_color' => get_post_meta($preset_id, 'eb_dark_button_text_color', true) ?: '#000000',
        'dark_link_color'        => get_post_meta($preset_id, 'eb_dark_link_color', true) ?: '#ffffff',
    ];
}
