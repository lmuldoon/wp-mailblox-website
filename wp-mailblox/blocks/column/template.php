<?php
if (!defined('ABSPATH')) exit;

$attrs = $template_attributes ?? [];

$background_color = $attrs['backgroundColor'] ?? '';
$dark_bg          = $attrs['darkBackgroundColor'] ?? '';

$bg_image_enabled = !empty($attrs['backgroundImageEnabled']);
$bg_image_url     = $attrs['backgroundImageUrl'] ?? '';
$bg_repeat        = $attrs['backgroundRepeat'] ?? 'no-repeat';
$bg_pos_x         = $attrs['backgroundPositionX'] ?? 'center';
$bg_pos_y         = $attrs['backgroundPositionY'] ?? 'center';
$bg_size_w        = $attrs['backgroundSizeW'] ?? 'cover';
$bg_size_h        = $attrs['backgroundSizeH'] ?? 'auto';
$bg_size          = in_array($bg_size_w, ['cover', 'contain']) ? $bg_size_w : $bg_size_w . ' ' . $bg_size_h;

$padding_top    = eb_snap_to_5($attrs['paddingTop']    ?? 0);
$padding_bottom = eb_snap_to_5($attrs['paddingBottom'] ?? 0);
$padding_left   = eb_snap_to_5($attrs['paddingLeft']   ?? 0);
$padding_right  = eb_snap_to_5($attrs['paddingRight']  ?? 0);

$mob_classes = eb_mobile_classes($attrs, true, false);

$radius_tl = intval($attrs['borderRadiusTL'] ?? 0);
$radius_tr = intval($attrs['borderRadiusTR'] ?? 0);
$radius_br = intval($attrs['borderRadiusBR'] ?? 0);
$radius_bl = intval($attrs['borderRadiusBL'] ?? 0);
$has_radius = $radius_tl || $radius_tr || $radius_br || $radius_bl;

// Mobile border radius override
$mob_br_tl = $attrs['mobileBorderRadiusTL'] ?? null;
$mob_br_tr = $attrs['mobileBorderRadiusTR'] ?? null;
$mob_br_br = $attrs['mobileBorderRadiusBR'] ?? null;
$mob_br_bl = $attrs['mobileBorderRadiusBL'] ?? null;
if ($mob_br_tl !== null || $mob_br_tr !== null || $mob_br_br !== null || $mob_br_bl !== null) {
    $tl = intval($mob_br_tl ?? $radius_tl);
    $tr = intval($mob_br_tr ?? $radius_tr);
    $br = intval($mob_br_br ?? $radius_br);
    $bl = intval($mob_br_bl ?? $radius_bl);
    $mob_br_class = "eb-mob-br-{$tl}-{$tr}-{$br}-{$bl}";
    eb_register_mobile_border_radius($mob_br_class, $tl, $tr, $br, $bl);
    $mob_classes  = trim($mob_classes . ' ' . $mob_br_class);
}

// Mobile border-width and border-color overrides
$border_width        = intval($attrs['borderWidth']       ?? 0);
$border_color        = $attrs['borderColor']       ?? '#000000';
$mobile_border_width = $attrs['mobileBorderWidth'] ?? null;
$mobile_border_color = $attrs['mobileBorderColor'] ?? '';
if ($mobile_border_width !== null) {
    $mob_classes = trim($mob_classes . ' eb-mob-bw-' . intval($mobile_border_width));
}
if (!empty($mobile_border_color)) {
    $hex = strtolower(ltrim($mobile_border_color, '#'));
    $cls = 'eb-mob-bc-' . $hex;
    eb_register_mobile_border_color($cls, $mobile_border_color);
    $mob_classes = trim($mob_classes . ' ' . $cls);
}

$dark_class = '';
if (!empty($dark_bg)) {
    $dark_class = 'eb-dark-' . ltrim($dark_bg, '#');
    eb_register_dark_section_color($dark_bg, $dark_class);
}

$inner_blocks = $template_inner_blocks ?? [];
if (empty($inner_blocks)) return;

// Build td inline styles
$td_style = '';
if ($padding_top || $padding_bottom || $padding_left || $padding_right) {
    $td_style .= "padding:{$padding_top}px {$padding_right}px {$padding_bottom}px {$padding_left}px;";
}
if ($background_color) {
    $td_style .= 'background-color:' . esc_attr($background_color) . ';';
}
if ($bg_image_enabled && $bg_image_url) {
    $td_style .= sprintf(
        'background-image:url(%s);background-repeat:%s;background-position:%s %s;background-size:%s;',
        esc_url($bg_image_url),
        esc_attr($bg_repeat),
        esc_attr($bg_pos_x),
        esc_attr($bg_pos_y),
        esc_attr($bg_size)
    );
}
if ($has_radius) {
    $td_style .= "border-radius:{$radius_tl}px {$radius_tr}px {$radius_br}px {$radius_bl}px;";
}
if ($border_width > 0) {
    $td_style .= "border:{$border_width}px solid " . esc_attr($border_color) . ";";
}

$use_wrapper = !empty($td_style);

$has_bg_image = $bg_image_enabled && !empty($bg_image_url);
if ($has_bg_image) {
    $has_bg_image_class = "bg-image";
} else {
    $has_bg_image_class = "no-bg-image";
}
?>

<?php if ($use_wrapper) : ?>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td class="<?php echo esc_attr(trim($has_bg_image_class . ($dark_class ? ' ' . $dark_class : '') . ($mob_classes ? ' ' . $mob_classes : ''))); ?>" style="<?php echo $td_style; ?>" valign="top">
<?php else: ?>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td class="<?php echo esc_attr(trim($has_bg_image_class . ($mob_classes ? ' ' . $mob_classes : ''))); ?>" valign="top">
<?php endif; ?>

<?php foreach ($inner_blocks as $block) : ?>
    <?php echo eb_render_block_recursive($block, $template_context); ?>
<?php endforeach; ?>

        </td>
    </tr>
</table>
