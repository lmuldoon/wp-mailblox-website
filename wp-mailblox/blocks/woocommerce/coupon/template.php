<?php
if (!defined('ABSPATH')) exit;

$attributes          = $template_attributes ?? [];
$title               = $attributes['title']               ?? 'Your Exclusive Discount';
$description         = $attributes['description']         ?? 'Use this code at checkout';
$code                = $attributes['code']                ?? 'SAVE20';
$expiry_text         = $attributes['expiryText']          ?? '';
$background_color    = $attributes['backgroundColor']     ?? '';
$border_color        = $attributes['borderColor']  ?? '';
$border_style        = $attributes['borderStyle']  ?? 'dashed';
$border_width        = intval($attributes['borderWidth'] ?? 2);
$border_radius       = intval($attributes['borderRadius'] ?? 4);
$mobile_border_radius = $attributes['mobileBorderRadius'] ?? null;
$mobile_border_width  = $attributes['mobileBorderWidth']  ?? null;
$mobile_border_color  = $attributes['mobileBorderColor']  ?? '';
$code_bg_color       = $attributes['codeBackgroundColor'] ?? '';
$auto_text_color     = eb_get_contrast_colour($template_context['background'] ?? '#ffffff', $template_context);
$text_color          = !empty($attributes['textColor'])           ? $attributes['textColor']           : $auto_text_color;
$code_color          = !empty($attributes['codeColor'])            ? $attributes['codeColor']            : $auto_text_color;
$align               = $attributes['align']               ?? 'center';

$padding_top         = eb_snap_to_5($attributes['paddingTop']    ?? 0);
$padding_bottom      = eb_snap_to_5($attributes['paddingBottom'] ?? 0);
$padding_left        = eb_snap_to_5($attributes['paddingLeft']   ?? 0);
$padding_right       = eb_snap_to_5($attributes['paddingRight']  ?? 0);
$mob_classes         = eb_mobile_classes($attributes, true, true);

// Mobile border classes for the code <td>
$coupon_mob_classes = '';
if ($mobile_border_radius !== null) {
    $r   = intval($mobile_border_radius);
    $cls = "eb-mob-br-{$r}-{$r}-{$r}-{$r}";
    eb_register_mobile_border_radius($cls, $r, $r, $r, $r);
    $coupon_mob_classes = trim($coupon_mob_classes . ' ' . $cls);
}
if ($mobile_border_width !== null) {
    $coupon_mob_classes = trim($coupon_mob_classes . ' eb-mob-bw-' . intval($mobile_border_width));
}
if (!empty($mobile_border_color)) {
    $hex = strtolower(ltrim($mobile_border_color, '#'));
    $cls = 'eb-mob-bc-' . $hex;
    eb_register_mobile_border_color($cls, $mobile_border_color);
    $coupon_mob_classes = trim($coupon_mob_classes . ' ' . $cls);
}

$font_family         = $attributes['body_font_stack']        ?? $attributes['body_font'] ?? 'Helvetica, Arial, sans-serif';
$font_size           = intval($attributes['body_size']        ?? 16);
$sub_font_family     = $attributes['subheading_font_stack']   ?? $font_family;
$sub_font_size       = intval($attributes['subheading_size']  ?? 24);
$sub_font_weight     = $attributes['subheading_font_weight']  ?? 400;
$sub_line_height     = $attributes['subheading_line_height']  ?? 1.3;

$dark_bg_color       = $attributes['darkBackgroundColor'] ?? '';
$dark_class          = '';
if (!empty($dark_bg_color)) {
    $dark_class = 'eb-dark-' . ltrim($dark_bg_color, '#');
    eb_register_dark_section_color($dark_bg_color, $dark_class);
}

$dark_code_bg_color  = $attributes['darkCodeBackgroundColor'] ?? '';
$dark_code_class     = '';
if (!empty($dark_code_bg_color)) {
    $dark_code_class = 'eb-dark-' . ltrim($dark_code_bg_color, '#');
    eb_register_dark_section_color($dark_code_bg_color, $dark_code_class);
}
?>

<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td align="<?php echo esc_attr($align); ?>" class="<?php echo esc_attr($mob_classes); ?>" style="padding: <?php echo esc_attr($padding_top); ?>px <?php echo esc_attr($padding_right); ?>px <?php echo esc_attr($padding_bottom); ?>px <?php echo esc_attr($padding_left); ?>px; mso-line-height-rule:exactly;">
            <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
                <tr>
                    <td align="<?php echo esc_attr($align); ?>" class="<?php echo $dark_class ? esc_attr($dark_class) : ''; ?>" style="padding: 24px 20px;<?php if ($background_color) echo ' background-color:' . esc_attr($background_color) . ';'; ?>">

                        <?php if (!empty($title)) : ?>
                            <p class="dark-mode-text" style="
                                margin: 0 0 6px;
                                font-family: <?php echo esc_attr($sub_font_family); ?>;
                                font-size: <?php echo esc_attr($sub_font_size); ?>px;
                                font-weight: <?php echo esc_attr($sub_font_weight); ?>;
                                line-height: <?php echo esc_attr($sub_line_height); ?>;
                                color: <?php echo esc_attr($text_color); ?>;
                                text-align: <?php echo esc_attr($align); ?>;
                            ">
                                <?php echo esc_html($title); ?>
                            </p>
                        <?php endif; ?>

                        <?php if (!empty($description)) : ?>
                            <p class="dark-mode-text" style="
                                margin: 0 0 16px;
                                font-family: <?php echo esc_attr($font_family); ?>;
                                font-size: <?php echo esc_attr($font_size); ?>px;
                                color: <?php echo esc_attr($text_color); ?>;
                                text-align: <?php echo esc_attr($align); ?>;
                            ">
                                <?php echo esc_html($description); ?>
                            </p>
                        <?php endif; ?>

                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" align="<?php echo esc_attr($align); ?>" style="<?php echo $align === 'center' ? 'margin: 0 auto;' : ''; ?>">
                            <tr>
                                <td class="<?php echo esc_attr(trim(($dark_code_class ?: '') . ($coupon_mob_classes ? ' ' . $coupon_mob_classes : ''))); ?>" style="
                                    padding: 12px 28px;
                                    <?php if ($code_bg_color) echo 'background-color:' . esc_attr($code_bg_color) . ';'; ?>
                                    <?php if ($border_color) echo 'border:' . esc_attr($border_width) . 'px ' . esc_attr($border_style) . ' ' . esc_attr($border_color) . ';'; ?>
                                    border-radius: <?php echo esc_attr($border_radius); ?>px;
                                    font-family: <?php echo esc_attr($sub_font_family); ?>;
                                    font-size: <?php echo esc_attr($sub_font_size); ?>px;
                                    font-weight: <?php echo esc_attr($sub_font_weight); ?>;
                                    letter-spacing: 4px;
                                    color: <?php echo esc_attr($code_color); ?>;
                                    text-align: center;
                                ">
                                    <?php echo esc_html(strtoupper($code)); ?>
                                </td>
                            </tr>
                        </table>

                        <?php if (!empty($expiry_text)) : ?>
                            <p class="dark-mode-text" style="
                                margin: 12px 0 0;
                                font-family: <?php echo esc_attr($font_family); ?>;
                                font-size: <?php echo esc_attr($font_size - 2); ?>px;
                                color: <?php echo esc_attr($text_color); ?>;
                                opacity: 0.7;
                                text-align: <?php echo esc_attr($align); ?>;
                            ">
                                <?php echo esc_html($expiry_text); ?>
                            </p>
                        <?php endif; ?>

                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
