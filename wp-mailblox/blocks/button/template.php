<?php
if (!defined('ABSPATH')) exit;

$attributes    = $template_attributes ?? [];

$text          = $attributes['text'] ?? 'Click me';
$href          = eb_append_utm($attributes['href'] ?? '#', $attributes, $template_context['utm_defaults'] ?? []);
$preset_button_color      = $attributes['button_color']      ?? '#000000';
$preset_button_text_color = $attributes['button_text_color']  ?? '';
// Use block-level override if set, otherwise fall back to preset default
$background    = !empty($attributes['backgroundColor'])
    ? $attributes['backgroundColor']
    : $preset_button_color;
$borderRadius  = $attributes['borderRadius'] ?? 0;
$borderWidth   = intval($attributes['borderWidth'] ?? 0);
$borderColor   = $attributes['borderColor'] ?? '#000000';
$font_family   = $attributes['button_font_stack'] ?? $attributes['body_font_stack'] ?? $attributes['body_font'] ?? 'Helvetica, Arial, sans-serif';
$font_weight   = $attributes['button_font_weight'] ?? $attributes['body_font_weight'] ?? 700;
$font_size     = $attributes['button_size'] ?? $attributes['body_size'] ?? 16;
$padding_top   = eb_snap_to_5($attributes['paddingTop']    ?? 10);
$padding_bottom = eb_snap_to_5($attributes['paddingBottom'] ?? 10);
$padding_left  = eb_snap_to_5($attributes['paddingLeft']   ?? 25);
$padding_right = eb_snap_to_5($attributes['paddingRight']  ?? 25);
$align         = $attributes['align'] ?? 'center';
$text_color    = !empty($attributes['textColor'])
    ? $attributes['textColor']
    : (!empty($preset_button_text_color)
        ? $preset_button_text_color
        : eb_get_contrast_colour($background, $template_context));

$td_mob_classes  = eb_mobile_classes($attributes, false, true); // alignment only on wrapper td
$btn_mob_classes = eb_mobile_classes($attributes, true, false);  // padding only on the <a>
if (!empty($attributes['hideOnMobile'])) $td_mob_classes = trim('eb-mob-hide ' . $td_mob_classes);

// Mobile border overrides
$mobile_border_radius = $attributes['mobileBorderRadius'] ?? null;
$mobile_border_width  = $attributes['mobileBorderWidth']  ?? null;
$mobile_border_color  = $attributes['mobileBorderColor']  ?? '';

if ($mobile_border_radius !== null) {
    $r   = intval($mobile_border_radius);
    $cls = "eb-mob-br-{$r}-{$r}-{$r}-{$r}";
    eb_register_mobile_border_radius($cls, $r, $r, $r, $r);
    $btn_mob_classes = trim($btn_mob_classes . ' ' . $cls);
}
if ($mobile_border_width !== null) {
    $btn_mob_classes = trim($btn_mob_classes . ' eb-mob-bw-' . intval($mobile_border_width));
}
if (!empty($mobile_border_color)) {
    $hex = strtolower(ltrim($mobile_border_color, '#'));
    $cls = 'eb-mob-bc-' . $hex;
    eb_register_mobile_border_color($cls, $mobile_border_color);
    $btn_mob_classes = trim($btn_mob_classes . ' ' . $cls);
}

// Safe href: preserve merge tags as-is; escape plain URLs
$is_merge_tag = (strpos($href, '[[') !== false || strpos($href, '*|') !== false || strpos($href, '{{') !== false);
$safe_href    = $is_merge_tag ? $href : esc_url($href);

// VML button dimensions (used only when borderRadius > 0)
$btn_height  = $padding_top + $padding_bottom + intval($font_size * 1.2);
$arcsize_pct = ($borderRadius > 0) ? min(50, round(($borderRadius / max($btn_height, 1)) * 100)) : 0;
$stroke_color = ($borderWidth > 0) ? $borderColor : $background;
?>

<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td align="<?php echo esc_attr($align); ?>" class="<?php echo esc_attr($td_mob_classes); ?>">

            <?php if ($borderRadius > 0) : ?>
            <!--[if mso]>
            <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word"
                href="<?php echo $safe_href; ?>"
                style="height:<?php echo esc_attr($btn_height); ?>px;v-text-anchor:middle;mso-wrap-style:none;"
                arcsize="<?php echo esc_attr($arcsize_pct); ?>%"
                strokecolor="<?php echo esc_attr($stroke_color); ?>"
                fillcolor="<?php echo esc_attr($background); ?>">
                <w:anchorlock/>
                <center style="color:<?php echo esc_attr($text_color); ?>;font-family:<?php echo esc_attr($font_family); ?>;font-size:<?php echo esc_attr($font_size); ?>px;font-weight:<?php echo esc_attr($font_weight); ?>;">
                    <?php echo esc_html(wp_strip_all_tags($text)); ?>
                </center>
            </v:roundrect>
            <![endif]-->
            <!--[if !mso]><!-->
            <?php endif; ?>

            <a href="<?php echo $safe_href; ?>" class="button<?php echo $btn_mob_classes ? ' ' . esc_attr($btn_mob_classes) : ''; ?>" style="
                display:inline-block;
                padding:<?php echo esc_attr($padding_top); ?>px <?php echo esc_attr($padding_right); ?>px <?php echo esc_attr($padding_bottom); ?>px <?php echo esc_attr($padding_left); ?>px;
                background-color:<?php echo esc_attr($background); ?>;
                color:<?php echo esc_attr($text_color); ?>;
                text-decoration:none;
                border-radius:<?php echo esc_attr($borderRadius); ?>px;
                font-family:<?php echo esc_attr($font_family); ?>;
                font-size:<?php echo esc_attr($font_size); ?>px;
                font-weight:<?php echo esc_attr($font_weight); ?>;
                line-height:1.2;
                <?php if ($borderWidth > 0) : ?>border:<?php echo esc_attr($borderWidth); ?>px solid <?php echo esc_attr($borderColor); ?>;<?php endif; ?>
            ">
                <?php echo wp_kses_post($text); ?>
            </a>

            <?php if ($borderRadius > 0) : ?>
            <!--<![endif]-->
            <?php endif; ?>

        </td>
    </tr>
</table>
