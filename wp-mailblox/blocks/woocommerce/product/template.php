<?php
if (!defined('ABSPATH')) exit;

$attributes       = $template_attributes ?? [];
$product_title    = $attributes['productTitle']        ?? '';
$product_price    = $attributes['productPrice']        ?? '';
$regular_price    = $attributes['productRegularPrice'] ?? '';
$is_on_sale       = !empty($attributes['productIsOnSale']) && !empty($regular_price);
$product_image    = $attributes['productImageUrl']     ?? '';
$product_alt      = $attributes['productImageAlt']     ?? '';
$product_link     = $attributes['productPermalink']    ?? '#';
$button_text         = $attributes['buttonText']             ?? 'Shop Now';
$preset_button_color      = $attributes['button_color']      ?? '#000000';
$preset_button_text_color = $attributes['button_text_color']  ?? '';
$disableDarkTextColour      = !empty($template_attributes['disableDarkTextColour']);
$button_color        = !empty($attributes['buttonColor'])
    ? $attributes['buttonColor']
    : $preset_button_color;
$button_radius       = intval($attributes['buttonBorderRadius'] ?? 0);
$button_padding_top  = eb_snap_to_5($attributes['buttonPaddingTop']    ?? 10);
$button_padding_bot  = eb_snap_to_5($attributes['buttonPaddingBottom'] ?? 10);
$button_padding_left = eb_snap_to_5($attributes['buttonPaddingLeft']   ?? 25);
$button_padding_right= eb_snap_to_5($attributes['buttonPaddingRight']  ?? 25);
$button_border_width = intval($attributes['buttonBorderWidth']   ?? 0);
$button_border_color = $attributes['buttonBorderColor']          ?? '#000000';
$button_text_color   = !empty($attributes['buttonTextColor'])
    ? $attributes['buttonTextColor']
    : (!empty($preset_button_text_color)
        ? $preset_button_text_color
        : eb_get_contrast_colour($button_color, $template_context));
$show_price        = $attributes['showPrice']           ?? true;
$show_description  = $attributes['showDescription']    ?? true;
$product_description = $attributes['productDescription'] ?? '';
$image_align       = $attributes['imageAlign']          ?? 'center';

$padding_top      = eb_snap_to_5($attributes['paddingTop']    ?? 0);
$padding_bottom   = eb_snap_to_5($attributes['paddingBottom'] ?? 0);
$padding_left     = eb_snap_to_5($attributes['paddingLeft']   ?? 0);
$padding_right    = eb_snap_to_5($attributes['paddingRight']  ?? 0);
$mob_classes      = eb_mobile_classes($attributes, true, true);

// Mobile button border classes
$mobile_btn_radius = $attributes['mobileButtonBorderRadius'] ?? null;
$mobile_btn_width  = $attributes['mobileButtonBorderWidth']  ?? null;
$mobile_btn_color  = $attributes['mobileButtonBorderColor']  ?? '';
$btn_mob_classes   = '';
if ($mobile_btn_radius !== null) {
    $r   = intval($mobile_btn_radius);
    $cls = "eb-mob-br-{$r}-{$r}-{$r}-{$r}";
    eb_register_mobile_border_radius($cls, $r, $r, $r, $r);
    $btn_mob_classes = trim($btn_mob_classes . ' ' . $cls);
}
if ($mobile_btn_width !== null) {
    $btn_mob_classes = trim($btn_mob_classes . ' eb-mob-bw-' . intval($mobile_btn_width));
}
if (!empty($mobile_btn_color)) {
    $hex = strtolower(ltrim($mobile_btn_color, '#'));
    $cls = 'eb-mob-bc-' . $hex;
    eb_register_mobile_border_color($cls, $mobile_btn_color);
    $btn_mob_classes = trim($btn_mob_classes . ' ' . $cls);
}

$font_family      = $attributes['body_font_stack']        ?? $attributes['body_font'] ?? 'Helvetica, Arial, sans-serif';
$font_size        = $attributes['body_size']              ?? 16;
$sub_font_family  = $attributes['subheading_font_stack']  ?? $font_family;
$sub_font_size    = $attributes['subheading_size']        ?? 24;
$sub_font_weight  = $attributes['subheading_font_weight'] ?? 400;
$sub_line_height  = $attributes['subheading_line_height'] ?? 1.3;
$btn_font_family  = $attributes['button_font_stack']      ?? $font_family;
$btn_font_weight  = $attributes['button_font_weight']     ?? 700;
$btn_font_size    = $attributes['button_size']            ?? $font_size;

$background       = $template_context['background'] ?? '#ffffff';
$auto_text_color  = eb_get_contrast_colour($background, $template_context);
$sale_price_color = $template_attributes['sale_price_color'] ?? '#c0392b';

if (empty($product_title) && empty($product_image)) return;
?>

<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td align="<?php echo esc_attr($image_align); ?>" class="<?php echo $disableDarkTextColour ? 'eb-dark-text-colour-disabled' : 'eb-dark-text-colour-enabled'; ?> <?php echo esc_attr($mob_classes); ?>" style="padding: <?php echo esc_attr($padding_top); ?>px <?php echo esc_attr($padding_right); ?>px <?php echo esc_attr($padding_bottom); ?>px <?php echo esc_attr($padding_left); ?>px; mso-line-height-rule:exactly;">

            <?php if (!empty($product_image)) : ?>
                <a href="<?php echo esc_url($product_link); ?>" style="display: block; text-decoration: none; text-align: <?php echo esc_attr($image_align); ?>;">
                    <img
                        src="<?php echo esc_url($product_image); ?>"
                        alt="<?php echo esc_attr($product_alt); ?>"
                        class="eb_img_adapt"
                        style="max-width: 100%; height: auto; display: block; <?php echo $image_align === 'center' ? 'margin: 0 auto;' : ''; ?> border: none;"
                    >
                </a>
            <?php endif; ?>

            <?php if (!empty($product_title)) : ?>
                <p style="
                    margin: 12px 0 4px;
                    font-family: <?php echo esc_attr($sub_font_family); ?>;
                    font-size: <?php echo esc_attr($sub_font_size); ?>px;
                    font-weight: <?php echo esc_attr($sub_font_weight); ?>;
                    line-height: <?php echo esc_attr($sub_line_height); ?>;
                    color: <?php echo esc_attr($auto_text_color); ?>;
                    text-align: <?php echo esc_attr($image_align); ?>;
                ">
                    <a href="<?php echo esc_url($product_link); ?>" style="text-decoration: none; color: inherit;">
                        <?php echo esc_html($product_title); ?>
                    </a>
                </p>
            <?php endif; ?>

            <?php if ($show_description && !empty($product_description)) : ?>
                <p class="dark-mode-text" style="
                    margin: 0 0 12px;
                    font-family: <?php echo esc_attr($font_family); ?>;
                    font-size: <?php echo esc_attr($font_size); ?>px;
                    color: <?php echo esc_attr($auto_text_color); ?>;
                    opacity: 0.8;
                    text-align: <?php echo esc_attr($image_align); ?>;
                "><?php echo wp_kses_post($product_description); ?></p>
            <?php endif; ?>

            <?php if ($show_price && !empty($product_price)) : ?>
                <p class="dark-mode-text" style="margin: 0 0 16px; font-family: <?php echo esc_attr($font_family); ?>; font-size: <?php echo esc_attr($font_size); ?>px; text-align: <?php echo esc_attr($image_align); ?>; color: <?php echo esc_attr($auto_text_color); ?>;">
                    <?php if ($is_on_sale) : ?>
                        <span style="text-decoration: line-through; opacity: 0.5; margin-right: 6px;"><?php echo esc_html($regular_price); ?></span>
                        <span style="color: <?php echo esc_attr($sale_price_color); ?>; font-weight: 700;"><?php echo esc_html($product_price); ?></span>
                    <?php else : ?>
                        <?php echo esc_html($product_price); ?>
                    <?php endif; ?>
                </p>
            <?php endif; ?>

            <?php
            $btn_height_wc  = $button_padding_top + $button_padding_bot + intval($btn_font_size * 1.2);
            $arcsize_wc     = ($button_radius > 0) ? min(50, round(($button_radius / max($btn_height_wc, 1)) * 100)) : 0;
            $stroke_wc      = ($button_border_width > 0) ? $button_border_color : $button_color;
            ?>
            <table role="presentation" border="0" cellpadding="0" cellspacing="0" align="<?php echo esc_attr($image_align); ?>" style="<?php echo $image_align === 'center' ? 'margin: 0 auto;' : ''; ?>">
                <tr>
                    <td>
                        <?php if ($button_radius > 0) : ?>
                        <!--[if mso]>
                        <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word"
                            href="<?php echo esc_url($product_link); ?>"
                            style="height:<?php echo esc_attr($btn_height_wc); ?>px;v-text-anchor:middle;mso-wrap-style:none;"
                            arcsize="<?php echo esc_attr($arcsize_wc); ?>%"
                            strokecolor="<?php echo esc_attr($stroke_wc); ?>"
                            fillcolor="<?php echo esc_attr($button_color); ?>">
                            <w:anchorlock/>
                            <center style="color:<?php echo esc_attr($button_text_color); ?>;font-family:<?php echo esc_attr($btn_font_family); ?>;font-size:<?php echo esc_attr($btn_font_size); ?>px;font-weight:<?php echo esc_attr($btn_font_weight); ?>;">
                                <?php echo esc_html($button_text); ?>
                            </center>
                        </v:roundrect>
                        <![endif]-->
                        <!--[if !mso]><!-->
                        <?php endif; ?>
                        <a href="<?php echo esc_url($product_link); ?>" class="button<?php echo $btn_mob_classes ? ' ' . esc_attr($btn_mob_classes) : ''; ?>" style="
                            display: inline-block;
                            padding: <?php echo esc_attr($button_padding_top); ?>px <?php echo esc_attr($button_padding_right); ?>px <?php echo esc_attr($button_padding_bot); ?>px <?php echo esc_attr($button_padding_left); ?>px;
                            background-color: <?php echo esc_attr($button_color); ?>;
                            color: <?php echo esc_attr($button_text_color); ?>;
                            text-decoration: none;
                            border-radius: <?php echo esc_attr($button_radius); ?>px;
                            font-family: <?php echo esc_attr($btn_font_family); ?>;
                            font-size: <?php echo esc_attr($btn_font_size); ?>px;
                            font-weight: <?php echo esc_attr($btn_font_weight); ?>;
                            line-height: 1.2;
                            <?php if ($button_border_width > 0) : ?>border: <?php echo esc_attr($button_border_width); ?>px solid <?php echo esc_attr($button_border_color); ?>;<?php endif; ?>
                        ">
                            <?php echo esc_html($button_text); ?>
                        </a>
                        <?php if ($button_radius > 0) : ?>
                        <!--<![endif]-->
                        <?php endif; ?>
                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>
