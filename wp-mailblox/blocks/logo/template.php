<?php
if (!defined('ABSPATH')) exit;

$attributes = $template_attributes ?? [];

$width    = intval($attributes['width'] ?? 200);
$align    = $attributes['align'] ?? 'center';
$link_url = $attributes['linkUrl'] ?? '';
$alt      = $attributes['alt'] ?? get_bloginfo('name');

/**
 * -------------------------
 * LIGHT LOGO
 * -------------------------
 */

// Priority:
// 1. Block override
// 2. Preset logo
// 3. WP custom logo

$light_url = $attributes['url'] ?? '';

if (empty($light_url)) {
    $light_url = $attributes['preset_logo_url'] ?? '';
}

if (empty($light_url)) {
    $logo_id = get_theme_mod('custom_logo');
    if ($logo_id) {
        $logo_data = wp_get_attachment_image_src($logo_id, 'full');
        $light_url = $logo_data ? $logo_data[0] : '';
    }
}

// If still no logo → stop rendering
if (empty($light_url)) {
    return;
}

$padding_top    = eb_snap_to_5($attributes['paddingTop']    ?? 0);
$padding_bottom = eb_snap_to_5($attributes['paddingBottom'] ?? 0);
$padding_left   = eb_snap_to_5($attributes['paddingLeft']   ?? 0);
$padding_right  = eb_snap_to_5($attributes['paddingRight']  ?? 0);

$mob_classes = eb_mobile_classes($attributes, true, true);
if (!empty($attributes['hideOnMobile'])) $mob_classes = trim('eb-mob-hide ' . $mob_classes);

/**
 * -------------------------
 * DARK LOGO
 * -------------------------
 *
 * Priority:
 * 1. Block override (darkLogoEnabled + darkUrl)
 * 2. Preset dark logo (preset_logo_dark_enabled + preset_logo_dark_url)
 * 3. Fallback to light logo
 */

$block_dark_enabled  = !empty($attributes['darkLogoEnabled']);
$block_dark_url      = $attributes['darkUrl'] ?? '';
$preset_dark_enabled = !empty($attributes['preset_logo_dark_enabled']);
$preset_dark_url     = $attributes['preset_logo_dark_url'] ?? '';

if ($block_dark_enabled && !empty($block_dark_url)) {
    // Block has its own dark logo
    $dark_enabled = true;
    $dark_url     = $block_dark_url;
} elseif ($preset_dark_enabled && !empty($preset_dark_url)) {
    // Fall back to preset dark logo
    $dark_enabled = true;
    $dark_url     = $preset_dark_url;
} else {
    $dark_enabled = false;
    $dark_url     = $light_url;
}
?>

<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td align="<?php echo esc_attr($align); ?>" class="<?php echo esc_attr($mob_classes); ?>" style="padding: <?php echo $padding_top; ?>px <?php echo $padding_right; ?>px <?php echo $padding_bottom; ?>px <?php echo $padding_left; ?>px;">

            <?php if (!empty($link_url)) : ?>
                <a href="<?php echo esc_url($link_url); ?>" style="display:inline-block;">
            <?php endif; ?>

            <!-- Light logo -->
            <img
                src="<?php echo esc_url($light_url); ?>"
                alt="<?php echo esc_attr($alt); ?>"
                width="<?php echo esc_attr($width); ?>"
                class="<?php echo $dark_enabled ? 'eb-logo-light' : ''; ?>"
                style="
                    width: <?php echo esc_attr($width); ?>px;
                    max-width: 100%;
                    height: auto;
                    display: inline-block;
                    border: 0;
                    line-height: 0;
                    font-size: 0;
                "
            />

            <?php if ($dark_enabled) : ?>
                <!--[if !mso]><!-->
                <!-- Dark logo — hidden by default, shown via @media prefers-color-scheme:dark -->
                <img
                    src="<?php echo esc_url($dark_url); ?>"
                    alt="<?php echo esc_attr($alt); ?>"
                    width="<?php echo esc_attr($width); ?>"
                    class="eb-logo-dark"
                    style="
                        width: <?php echo esc_attr($width); ?>px;
                        max-width: 100%;
                        height: auto;
                        display: none;
                        border: 0;
                        line-height: 0;
                        font-size: 0;
                    "
                />
                <!--<![endif]-->
            <?php endif; ?>

            <?php if (!empty($link_url)) : ?>
                </a>
            <?php endif; ?>

        </td>
    </tr>
</table>