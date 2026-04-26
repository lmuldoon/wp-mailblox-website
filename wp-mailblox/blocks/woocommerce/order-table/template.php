<?php
if (!defined('ABSPATH')) exit;

$attributes        = $template_attributes ?? [];
$header_bg         = $attributes['headerBgColor']      ?? '';
$header_text_raw   = $attributes['headerTextColor']    ?? '';
$border_color      = $attributes['borderColor']        ?? '#eeeeee';
$row_alt_color     = $attributes['rowAltColor']        ?? '';
$text_color_raw    = $attributes['textColor']          ?? '';
$auto_text_color   = eb_get_contrast_colour($template_context['background'] ?? '#ffffff', $template_context);
$text_color        = !empty($text_color_raw)  ? $text_color_raw  : $auto_text_color;
$header_text       = !empty($header_text_raw) ? $header_text_raw
                   : ($header_bg ? eb_get_contrast_colour($header_bg, $template_context) : $auto_text_color);
$show_subtotal     = $attributes['showSubtotal']       ?? true;
$show_discount     = $attributes['showDiscount']       ?? false;
$show_tax          = $attributes['showTax']            ?? false;
$show_shipping     = $attributes['showShipping']       ?? true;
$show_total        = $attributes['showTotal']          ?? true;
$button_text         = $attributes['buttonText']             ?? 'View Order';
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
$show_button       = $attributes['showButton']         ?? true;
$font_family       = $attributes['body_font_stack']    ?? $attributes['body_font'] ?? 'Helvetica, Arial, sans-serif';
$preset_font_size  = intval($attributes['body_size']   ?? 16);
$font_size         = isset($attributes['fontSize']) && $attributes['fontSize'] !== null
                   ? intval($attributes['fontSize'])
                   : $preset_font_size;
$mobile_font_size  = isset($attributes['mobileFontSize']) && $attributes['mobileFontSize'] !== null
                   ? intval($attributes['mobileFontSize'])
                   : null;
$btn_font_family   = $attributes['button_font_stack']  ?? $font_family;
$btn_font_weight   = $attributes['button_font_weight'] ?? 700;
$btn_font_size     = intval($attributes['button_size'] ?? $font_size);
$platform          = $template_context['platform']     ?? 'mailchimp';

$padding_top       = eb_snap_to_5($attributes['paddingTop']    ?? 0);
$padding_bottom    = eb_snap_to_5($attributes['paddingBottom'] ?? 0);
$padding_left      = eb_snap_to_5($attributes['paddingLeft']   ?? 0);
$padding_right     = eb_snap_to_5($attributes['paddingRight']  ?? 0);
$mob_classes = eb_mobile_classes($attributes, true, false);
if ($mobile_font_size !== null) {
    $mob_classes = trim($mob_classes . ' eb-mob-fs-' . $mobile_font_size);
}

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

// Shared cell styles as inline strings to avoid repetition
$header_cell = 'font-family:' . esc_attr($font_family) . '; font-size:' . esc_attr($font_size) . 'px; font-weight:700; padding:10px 12px; border-bottom:1px solid ' . esc_attr($border_color) . ';'
    . ($header_bg ? ' background-color:' . esc_attr($header_bg) . ';' : '')
    . ' color:' . esc_attr($header_text) . ';';
$body_cell   = 'font-family:' . esc_attr($font_family) . '; font-size:' . esc_attr($font_size) . 'px; color:' . esc_attr($text_color) . '; padding:10px 12px; border-bottom:1px solid ' . esc_attr($border_color) . ';';
$total_cell  = 'font-family:' . esc_attr($font_family) . '; font-size:' . esc_attr($font_size) . 'px; color:' . esc_attr($text_color) . '; padding:8px 12px; border-bottom:1px solid ' . esc_attr($border_color) . ';';

// Class strings — dark-mode-text handles colour, eb-order-row* handles background override
$body_class     = 'dark-mode-text eb-order-row';
$body_alt_class = 'dark-mode-text eb-order-row-alt';
$total_class    = 'dark-mode-text eb-order-totals-row';

// Platform-specific variables
$platforms = [
    'klaviyo' => [
        'loop_open'  => '{% for item in event.extra.line_items %}',
        'loop_close' => '{% endfor %}',
        'name'       => '{{ item.ProductName }}',
        'qty'        => '{{ item.Quantity }}',
        'price'      => '{{ item.ItemPrice }}',
        'subtotal'   => '{{ event.extra.SubtotalPrice }}',
        'discount'   => '{{ event.extra.DiscountValue }}',
        'tax'        => '{{ event.extra.TaxPrice }}',
        'shipping'   => '{{ event.extra.ShippingPrice }}',
        'total'      => '{{ event.value }}',
        'order_url'  => '{{ event.extra.OrderURL }}',
        'dynamic'    => true,
    ],
    'brevo' => [
        'loop_open'  => '{% for item in params.items %}',
        'loop_close' => '{% endfor %}',
        'name'       => '{{ item.name }}',
        'qty'        => '{{ item.quantity }}',
        'price'      => '{{ item.price }}',
        'subtotal'   => '{{ params.subtotal }}',
        'discount'   => '{{ params.discount }}',
        'tax'        => '{{ params.tax }}',
        'shipping'   => '{{ params.shipping }}',
        'total'      => '{{ params.total }}',
        'order_url'  => '{{ params.order_url }}',
        'dynamic'    => true,
    ],
];

$p         = $platforms[$platform] ?? null;
$is_dynamic = !empty($p['dynamic']);

// For static platforms, use sample data
$sample_items = [
    ['name' => 'Example Product One',   'qty' => 1, 'price' => '$49.00'],
    ['name' => 'Example Product Two',   'qty' => 2, 'price' => '$29.00'],
    ['name' => 'Example Product Three', 'qty' => 1, 'price' => '$19.00'],
];
?>

<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td class="<?php echo $disableDarkTextColour ? 'eb-dark-text-colour-disabled' : 'eb-dark-text-colour-enabled'; ?> <?php echo esc_attr($mob_classes); ?>" style="padding: <?php echo esc_attr($padding_top); ?>px <?php echo esc_attr($padding_right); ?>px <?php echo esc_attr($padding_bottom); ?>px <?php echo esc_attr($padding_left); ?>px; mso-line-height-rule:exactly;">

            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;">

                <!-- Header row -->
                <tr>
                    <td style="<?php echo $header_cell; ?>">Product</td>
                    <td align="center" style="<?php echo $header_cell; ?> width:50px;">Qty</td>
                    <td align="right"  style="<?php echo $header_cell; ?> width:80px;">Price</td>
                </tr>

                <?php if ($is_dynamic) : ?>

                    <!-- Dynamic line items — populated by <?php echo esc_html(ucfirst($platform)); ?> at send time -->
                    <?php echo $p['loop_open']; ?>

                    <tr>
                        <td class="<?php echo $body_class; ?>" style="<?php echo $body_cell; ?>"><?php echo $p['name']; ?></td>
                        <td align="center" class="<?php echo $body_class; ?>" style="<?php echo $body_cell; ?>"><?php echo $p['qty']; ?></td>
                        <td align="right"  class="<?php echo $body_class; ?>" style="<?php echo $body_cell; ?>"><?php echo $p['price']; ?></td>
                    </tr>

                    <?php echo $p['loop_close']; ?>

                <?php else : ?>

                    <!-- Static sample rows — replace with your platform's order loop syntax -->
                    <?php foreach ($sample_items as $i => $item) :
                        $is_alt   = ($i % 2 !== 0);
                        $row_cls  = $is_alt ? $body_alt_class : $body_class;
                        $row_cell = $body_cell . ($is_alt && !empty($row_alt_color) ? ' background-color:' . esc_attr($row_alt_color) . ';' : '');
                    ?>
                    <tr>
                        <td class="<?php echo $row_cls; ?>" style="<?php echo $row_cell; ?>"><?php echo esc_html($item['name']); ?></td>
                        <td align="center" class="<?php echo $row_cls; ?>" style="<?php echo $row_cell; ?>"><?php echo esc_html($item['qty']); ?></td>
                        <td align="right"  class="<?php echo $row_cls; ?>" style="<?php echo $row_cell; ?>"><?php echo esc_html($item['price']); ?></td>
                    </tr>
                    <?php endforeach; ?>

                <?php endif; ?>

                <!-- Totals -->
                <?php if ($show_subtotal) : ?>
                <tr>
                    <td colspan="2" align="right" class="<?php echo $total_class; ?>" style="<?php echo $total_cell; ?>">Subtotal</td>
                    <td align="right" class="<?php echo $total_class; ?>" style="<?php echo $total_cell; ?> font-weight:600;"><?php echo $is_dynamic ? $p['subtotal'] : '$97.00'; ?></td>
                </tr>
                <?php endif; ?>

                <?php if ($show_discount) : ?>
                <tr>
                    <td colspan="2" align="right" class="<?php echo $total_class; ?>" style="<?php echo $total_cell; ?>">Discount</td>
                    <td align="right" class="<?php echo $total_class; ?>" style="<?php echo $total_cell; ?> font-weight:600;"><?php echo $is_dynamic ? $p['discount'] : '-$10.00'; ?></td>
                </tr>
                <?php endif; ?>

                <?php if ($show_tax) : ?>
                <tr>
                    <td colspan="2" align="right" class="<?php echo $total_class; ?>" style="<?php echo $total_cell; ?>">Tax</td>
                    <td align="right" class="<?php echo $total_class; ?>" style="<?php echo $total_cell; ?> font-weight:600;"><?php echo $is_dynamic ? $p['tax'] : '$9.50'; ?></td>
                </tr>
                <?php endif; ?>

                <?php if ($show_shipping) : ?>
                <tr>
                    <td colspan="2" align="right" class="<?php echo $total_class; ?>" style="<?php echo $total_cell; ?>">Shipping</td>
                    <td align="right" class="<?php echo $total_class; ?>" style="<?php echo $total_cell; ?> font-weight:600;"><?php echo $is_dynamic ? $p['shipping'] : '$8.00'; ?></td>
                </tr>
                <?php endif; ?>

                <?php if ($show_total) : ?>
                <tr>
                    <td colspan="2" align="right" class="<?php echo $total_class; ?>" style="<?php echo $total_cell; ?> font-size:<?php echo esc_attr($font_size + 1); ?>px; font-weight:700; border-top:2px solid <?php echo esc_attr($border_color); ?>; border-bottom:none;">Total</td>
                    <td align="right" class="<?php echo $total_class; ?>" style="<?php echo $total_cell; ?> font-size:<?php echo esc_attr($font_size + 1); ?>px; font-weight:700; border-top:2px solid <?php echo esc_attr($border_color); ?>; border-bottom:none;"><?php echo $is_dynamic ? $p['total'] : '$105.00'; ?></td>
                </tr>
                <?php endif; ?>

            </table>

            <?php if ($show_button) :
                $btn_url = $is_dynamic ? $p['order_url'] : '#';
            ?>
            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top:20px;">
                <tr>
                    <td align="center">
                        <a href="<?php echo $btn_url; ?>" class="button<?php echo $btn_mob_classes ? ' ' . esc_attr($btn_mob_classes) : ''; ?>" style="
                            display:inline-block;
                            padding:<?php echo esc_attr($button_padding_top); ?>px <?php echo esc_attr($button_padding_right); ?>px <?php echo esc_attr($button_padding_bot); ?>px <?php echo esc_attr($button_padding_left); ?>px;
                            background-color:<?php echo esc_attr($button_color); ?>;
                            color:<?php echo esc_attr($button_text_color); ?>;
                            text-decoration:none;
                            border-radius:<?php echo esc_attr($button_radius); ?>px;
                            font-family:<?php echo esc_attr($btn_font_family); ?>;
                            font-size:<?php echo esc_attr($btn_font_size); ?>px;
                            font-weight:<?php echo esc_attr($btn_font_weight); ?>;
                            line-height:1.2;
                            <?php if ($button_border_width > 0) : ?>border:<?php echo esc_attr($button_border_width); ?>px solid <?php echo esc_attr($button_border_color); ?>;<?php endif; ?>
                        ">
                            <?php echo esc_html($button_text); ?>
                        </a>
                    </td>
                </tr>
            </table>
            <?php endif; ?>

        </td>
    </tr>
</table>
