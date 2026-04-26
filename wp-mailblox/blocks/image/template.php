<?php
if (!defined('ABSPATH')) exit;

$attributes = $template_attributes ?? [];

//$url           = $attributes['url'] ?? '';
$alt           = $attributes['alt'] ?? '';
$align         = $attributes['align'] ?? 'center';
$display_width = intval($attributes['displayWidth'] ?? 600);
$actual_width  = intval($attributes['width'] ?? 0);
$container_width = intval($attributes['container_width'] ?? 600);
$image_id = $attributes['id'];

$url = $attributes['url'] ?? '';

$target_width = $container_width * 2;

$meta = wp_get_attachment_metadata($image_id);

if (!empty($meta)) {

    $upload_dir = wp_upload_dir();
    $base_url   = trailingslashit($upload_dir['baseurl']) . dirname($meta['file']);

    $candidates = [];

    // 1. Add original image as a candidate
    if (!empty($meta['width'])) {
        $candidates[] = [
            'width'  => (int) $meta['width'],
            'height' => (int) ($meta['height'] ?? 0),
            'file'   => $meta['file'],
            'url'    => wp_get_attachment_url($image_id),
        ];
    }

    // 2. Add generated sizes
    if (!empty($meta['sizes'])) {
        foreach ($meta['sizes'] as $size) {
            $candidates[] = [
                'width'  => (int) $size['width'],
                'height' => (int) ($size['height'] ?? 0),
                'file'   => $size['file'],
                'url'    => trailingslashit($base_url) . $size['file'],
            ];
        }
    }

    // 3. Find best match
    $best = null;

    foreach ($candidates as $candidate) {
        if ($candidate['width'] >= $target_width) {
            if (!$best || $candidate['width'] < $best['width']) {
                $best = $candidate;
            }
        }
    }

    // 4. Fallback: largest available
    if (!$best) {
        foreach ($candidates as $candidate) {
            if (!$best || $candidate['width'] > $best['width']) {
                $best = $candidate;
            }
        }
    }

    if ($best && !empty($best['url'])) {
        $url = $best['url'];
    } else {
        $url = wp_get_attachment_url($image_id);
    }
}

// --- Validate image exists ---
if (!$url) return;

// --- Validate file type ---
$path = parse_url($url, PHP_URL_PATH);
$ext  = strtolower(pathinfo($path, PATHINFO_EXTENSION));
$allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

if (!in_array($ext, $allowed_types)) {
    return;
}

// --- Retina check ---
$is_retina_ready = ($actual_width >= ($display_width * 2));

// --- Display height (for Outlook height attribute) ---
$display_height = '';
if (!empty($best) && !empty($best['width']) && !empty($best['height'])) {
    $display_height = (string) round($best['height'] * ($display_width / $best['width']));
}

$dark_url       = $attributes['darkUrl'] ?? '';
$has_dark       = !empty($dark_url);
$link_url       = eb_append_utm($attributes['linkUrl'] ?? '', $attributes, $template_context['utm_defaults'] ?? []);
$border_radius_tl = intval($attributes['borderRadiusTL'] ?? $attributes['borderRadius'] ?? 0);
$border_radius_tr = intval($attributes['borderRadiusTR'] ?? $attributes['borderRadius'] ?? 0);
$border_radius_br = intval($attributes['borderRadiusBR'] ?? $attributes['borderRadius'] ?? 0);
$border_radius_bl = intval($attributes['borderRadiusBL'] ?? $attributes['borderRadius'] ?? 0);
$has_radius = $border_radius_tl || $border_radius_tr || $border_radius_br || $border_radius_bl;
$padding_top    = eb_snap_to_5($attributes['paddingTop']    ?? 0);
$padding_bottom = eb_snap_to_5($attributes['paddingBottom'] ?? 0);
$padding_left   = eb_snap_to_5($attributes['paddingLeft']   ?? 0);
$padding_right  = eb_snap_to_5($attributes['paddingRight']  ?? 0);

$mob_classes = eb_mobile_classes($attributes, true, true);
if (!empty($attributes['hideOnMobile'])) $mob_classes = trim('eb-mob-hide ' . $mob_classes);

// Mobile border-radius registration
$mob_br_tl = $attributes['mobileBorderRadiusTL'] ?? null;
$mob_br_tr = $attributes['mobileBorderRadiusTR'] ?? null;
$mob_br_br = $attributes['mobileBorderRadiusBR'] ?? null;
$mob_br_bl = $attributes['mobileBorderRadiusBL'] ?? null;
$border_width        = intval($attributes['borderWidth']       ?? 0);
$border_color        = $attributes['borderColor']       ?? '#000000';
$mobile_border_width = $attributes['mobileBorderWidth'] ?? null;
$mobile_border_color = $attributes['mobileBorderColor'] ?? '';

// Classes that target the <img> element (border-radius, border-width, border-color)
$img_mob_classes = '';
if ($mob_br_tl !== null || $mob_br_tr !== null || $mob_br_br !== null || $mob_br_bl !== null) {
    $tl = intval($mob_br_tl ?? $border_radius_tl);
    $tr = intval($mob_br_tr ?? $border_radius_tr);
    $br = intval($mob_br_br ?? $border_radius_br);
    $bl = intval($mob_br_bl ?? $border_radius_bl);
    $mob_br_class = "eb-mob-br-{$tl}-{$tr}-{$br}-{$bl}";
    eb_register_mobile_border_radius($mob_br_class, $tl, $tr, $br, $bl);
    $img_mob_classes = trim($img_mob_classes . ' ' . $mob_br_class);
}
if ($mobile_border_width !== null) {
    $img_mob_classes = trim($img_mob_classes . ' eb-mob-bw-' . intval($mobile_border_width));
}
if (!empty($mobile_border_color)) {
    $hex = strtolower(ltrim($mobile_border_color, '#'));
    $cls = 'eb-mob-bc-' . $hex;
    eb_register_mobile_border_color($cls, $mobile_border_color);
    $img_mob_classes = trim($img_mob_classes . ' ' . $cls);
}

$img_style = "
    width:{$display_width}px;
    max-width:100%;
    height:auto;
    display:block;
    border:0;
    line-height:0;
    font-size:0;
" . ($has_radius ? "border-radius:{$border_radius_tl}px {$border_radius_tr}px {$border_radius_br}px {$border_radius_bl}px;" : '')
  . ($border_width > 0 ? "border:{$border_width}px solid " . esc_attr($border_color) . ";" : '');
?>

<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td align="<?php echo esc_attr($align); ?>" valign="top" class="<?php echo esc_attr($mob_classes); ?>" style="padding: <?php echo $padding_top; ?>px <?php echo $padding_right; ?>px <?php echo $padding_bottom; ?>px <?php echo $padding_left; ?>px;">

            <?php if ($link_url) : ?><a href="<?php echo esc_url($link_url); ?>" style="display:block;line-height:0;font-size:0;"><?php endif; ?>

            <!-- Light image (default) -->
            <img
                src="<?php echo esc_url($url); ?>"
                alt="<?php echo esc_attr($alt); ?>"
                width="<?php echo esc_attr($display_width); ?>"
                <?php if ($display_height) : ?>height="<?php echo esc_attr($display_height); ?>"<?php endif; ?>
                class="eb_img_adapt<?php echo $has_dark ? ' eb-img-light' : ''; ?><?php echo $img_mob_classes ? ' ' . esc_attr($img_mob_classes) : ''; ?>"
                style="<?php echo $img_style; ?>"
            />

            <?php if ($has_dark) : ?>
            <!-- Dark mode image -->
            <img
                src="<?php echo esc_url($dark_url); ?>"
                alt="<?php echo esc_attr($alt); ?>"
                width="<?php echo esc_attr($display_width); ?>"
                <?php if ($display_height) : ?>height="<?php echo esc_attr($display_height); ?>"<?php endif; ?>
                class="eb_img_adapt eb-img-dark"
                style="<?php echo $img_style; ?>display:none;"
            />
            <?php endif; ?>

            <?php if ($link_url) : ?></a><?php endif; ?>

        </td>
    </tr>
</table>