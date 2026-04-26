<?php
if (!defined('ABSPATH')) exit;

$attributes   = $template_attributes ?? [];
$align        = $attributes['align']        ?? 'center';
$icon_size    = intval($attributes['iconSize']    ?? 32);
$icon_spacing = intval($attributes['iconSpacing'] ?? 8);
$badge_color  = $attributes['badgeColor']   ?? '#000000';
$badge_shape  = $attributes['badgeShape']   ?? 'circle';
$badge_pad    = intval($attributes['badgePadding'] ?? 6);

$badge_radius = $badge_shape === 'circle' ? '50%' : ($badge_shape === 'rounded' ? '8px' : '0');

// Choose white or black icons based on badge colour luminance
$badge_rgb  = eb_hex_to_rgb($badge_color);
$luminance  = eb_luminance($badge_rgb[0], $badge_rgb[1], $badge_rgb[2]);
$icon_variant = $luminance > 0.5 ? '-black' : '';

$platforms = [
    'facebook'  => 'Facebook',
    'instagram' => 'Instagram',
    'twitter'   => 'X (Twitter)',
    'linkedin'  => 'LinkedIn',
    'youtube'   => 'YouTube',
    'tiktok'    => 'TikTok',
    'pinterest' => 'Pinterest',
    'threads'   => 'Threads',
];

// Only include platforms with URLs set
$active = [];
foreach ($platforms as $key => $label) {
    $url = $attributes[$key] ?? '';
    if (!empty($url)) {
        $active[$key] = ['url' => $url, 'label' => $label];
    }
}

if (empty($active)) return;

// Apply stored order
$order = $attributes['order'] ?? [];
if (!empty($order)) {
    $sorted = [];
    foreach ($order as $key) {
        if (isset($active[$key])) $sorted[$key] = $active[$key];
    }
    foreach ($active as $key => $data) {
        if (!isset($sorted[$key])) $sorted[$key] = $data;
    }
    $active = $sorted;
}

$mob_classes   = eb_mobile_classes($attributes, false, true);
$mob_icon_size = $attributes['mobileIconSize'] ?? null;
if ($mob_icon_size !== null && $mob_icon_size !== '') {
    $mob_classes = trim($mob_classes . ' eb-mob-is-' . intval($mob_icon_size));
}
if (!empty($attributes['hideOnMobile'])) $mob_classes = trim('eb-mob-hide ' . $mob_classes);

$icons_base = EB_PLUGIN_URL . 'assets/images/social/';
?>

<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td align="<?php echo esc_attr($align); ?>" class="<?php echo esc_attr($mob_classes); ?>" style="padding: 10px 0;">
            <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="display: inline-table;">
                <tr>
                    <?php foreach ($active as $key => $data) :
                        // Prefer .png if it exists on disk, fall back to .svg
                        $icon_file = $key . $icon_variant;
                        $ext       = file_exists(EB_PLUGIN_PATH . 'assets/images/social/' . $icon_file . '.png') ? 'png' : 'svg';
                        $icon_url  = $icons_base . $icon_file . '.' . $ext;
                    ?>
                        <td style="padding: 0 <?php echo esc_attr($icon_spacing); ?>px;">
                            <a href="<?php echo esc_url($data['url']); ?>" style="display: inline-block; background-color: <?php echo esc_attr($badge_color); ?>; border-radius: <?php echo esc_attr($badge_radius); ?>; padding: <?php echo esc_attr($badge_pad); ?>px; line-height: 0; font-size: 0; text-decoration: none;">
                                <img src="<?php echo esc_url($icon_url); ?>"
                                     width="<?php echo esc_attr($icon_size); ?>"
                                     height="<?php echo esc_attr($icon_size); ?>"
                                     alt="<?php echo esc_attr($data['label']); ?>"
                                     style="width: <?php echo esc_attr($icon_size); ?>px; height: <?php echo esc_attr($icon_size); ?>px; display: block; border: 0;">
                            </a>
                        </td>
                    <?php endforeach; ?>
                </tr>
            </table>
        </td>
    </tr>
</table>
