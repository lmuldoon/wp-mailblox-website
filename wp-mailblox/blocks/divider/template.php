<?php
if (!defined('ABSPATH')) exit;

$attributes  = $template_attributes ?? [];
$color       = $attributes['color']     ?? '#cccccc';
$thickness   = $attributes['thickness'] ?? 1;
$spacing     = eb_snap_to_5($attributes['spacing'] ?? 20);
$mob_spacing = $attributes['mobileSpacing'] ?? null;
$hide_mobile = !empty($attributes['hideOnMobile']);

$mob_classes = [];
if ($mob_spacing !== null && $mob_spacing !== '') {
    $v             = intval($mob_spacing);
    $mob_classes[] = "eb-mob-pt-{$v}";
    $mob_classes[] = "eb-mob-pb-{$v}";
}
if ($hide_mobile) {
    $mob_classes[] = 'eb-mob-hide';
}
$mob_class_str = implode(' ', $mob_classes);
?>

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
    <tr>
        <td class="<?php echo esc_attr($mob_class_str); ?>" style="padding: <?php echo esc_attr($spacing); ?>px 0;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                <tr>
                    <td style="border-top: <?php echo esc_attr($thickness); ?>px solid <?php echo esc_attr($color); ?>; font-size:0; line-height:0;">
                        &nbsp;
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
