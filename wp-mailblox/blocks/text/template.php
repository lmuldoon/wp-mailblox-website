<?php
if (!defined('ABSPATH')) exit;

$content     = $template_attributes['content'] ?? '';

$font_family = $template_attributes['body_font_stack'] ?? $template_attributes['body_font'] ?? 'Helvetica, Arial, sans-serif';
$font_weight = $template_attributes['body_font_weight'] ?? 400;
$font_size   = $template_attributes['body_size'] ?? 16;
$line_height = $template_attributes['body_line_height'] ?? 1.3;
$disableDarkTextColour      = !empty($template_attributes['disableDarkTextColour']);

$background  = $template_context['background'] ?? '#ffffff';
$align       = $template_attributes['align'] ?? 'left';
$text_color  = !empty($template_attributes['textColor'])
    ? $template_attributes['textColor']
    : eb_get_contrast_colour($background, $template_context);

$padding_top    = eb_snap_to_5($template_attributes['paddingTop'] ?? 0);
$padding_bottom = eb_snap_to_5($template_attributes['paddingBottom'] ?? 0);
$padding_left   = eb_snap_to_5($template_attributes['paddingLeft'] ?? 0);
$padding_right  = eb_snap_to_5($template_attributes['paddingRight'] ?? 0);

$mob_classes = eb_mobile_classes($template_attributes, true, true);
if (!empty($template_attributes['hideOnMobile'])) $mob_classes = trim('eb-mob-hide ' . $mob_classes);

$has_bg_image = $template_context['has_background_image'] ?? false;
?>

<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin:0;padding:0;">
    <tr>
        <td align="<?php echo esc_attr($align); ?>" class="<?php echo $disableDarkTextColour ? 'eb-dark-text-colour-disabled' : 'eb-dark-text-colour-enabled'; ?> <?php echo esc_attr($mob_classes); ?>" style="
            padding: <?php echo $padding_top; ?>px <?php echo $padding_right; ?>px <?php echo $padding_bottom; ?>px <?php echo $padding_left; ?>px;
            margin: 0;
            mso-line-height-rule: exactly;
        ">

            <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="width:100%;">
                <tr>
                    <td style="margin:0; padding:0;">

            <p class="eb-text-block text dark-mode-text" style="
                font-family: <?php echo esc_attr($font_family); ?>;
                font-size: <?php echo esc_attr($font_size); ?>px;
                font-weight: <?php echo esc_attr($font_weight); ?>;
                line-height: <?php echo esc_attr($line_height); ?>;
                color: <?php echo esc_attr($text_color); ?>;
                text-align: <?php echo esc_attr($align); ?>;
                margin: 0;
                padding: 0;
            ">
                <?php echo wp_kses_post($content); ?>
            </p>

                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>
