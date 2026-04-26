<?php
if (!defined('ABSPATH')) exit;

$content     = $template_attributes['content'] ?? '';
$font_family = $template_attributes['heading_font_stack'] ?? $template_attributes['heading_font'] ?? 'Arial, Helvetica, sans-serif';
$font_weight = $template_attributes['heading_font_weight'] ?? 700;
$font_size   = $template_attributes['heading_size'] ?? 24;
$line_height   = $template_attributes['heading_line_height'] ?? 1.3;
$background  = $template_context['background'] ?? '#ffffff';
$align      = $template_attributes['align'] ?? 'left';
$disableDarkTextColour      = !empty($template_attributes['disableDarkTextColour']);
$text_color  = !empty($template_attributes['textColor'])
    ? $template_attributes['textColor']
    : eb_get_contrast_colour($background, $template_context);

$has_bg_image = $template_context['has_background_image'] ?? false;
$padding_top    = eb_snap_to_5($template_attributes['paddingTop'] ?? 0);
$padding_bottom = eb_snap_to_5($template_attributes['paddingBottom'] ?? 0);
$padding_left   = eb_snap_to_5($template_attributes['paddingLeft'] ?? 0);
$padding_right  = eb_snap_to_5($template_attributes['paddingRight'] ?? 0);

$mob_classes = eb_mobile_classes($template_attributes, true, true);
if (!empty($template_attributes['hideOnMobile'])) $mob_classes = trim('eb-mob-hide ' . $mob_classes);
?>

<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td class="<?php echo $disableDarkTextColour ? 'eb-dark-text-colour-disabled' : 'eb-dark-text-colour-enabled'; ?> <?php echo $mob_classes ? ' ' . esc_attr($mob_classes) : ''; ?>" style="padding: <?php echo $padding_top; ?>px <?php echo $padding_right; ?>px <?php echo $padding_bottom; ?>px <?php echo $padding_left; ?>px; margin:0; mso-line-height-rule:exactly;">
            <p class="eb-header-block heading dark-mode-text" style="
                font-family: <?php echo esc_attr($font_family); ?>;
                font-size: <?php echo esc_attr($font_size); ?>px;
                font-weight: <?php echo esc_attr($font_weight); ?>;
                line-height: <?php echo esc_attr($line_height); ?>;
                color: <?php echo esc_attr($text_color); ?>;
                text-align: <?php echo esc_attr($align); ?>;
                margin:0; padding:0;">
                <?php echo wp_kses_post($content); ?>
            </p>
        </td>
    </tr>
</table>