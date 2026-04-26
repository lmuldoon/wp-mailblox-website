<?php
if (!defined('ABSPATH')) exit;

$attributes  = $template_attributes ?? [];
$spacing     = eb_snap_to_5($attributes['spacing'] ?? 20);
$mob_spacing = $attributes['mobileSpacing'] ?? null;
$mob_class   = ($mob_spacing !== null && $mob_spacing !== '') ? 'eb-mob-pt-' . intval($mob_spacing) : '';
if (!empty($attributes['hideOnMobile'])) $mob_class = trim('eb-mob-hide ' . $mob_class);
$bg_color    = $attributes['bgColor'] ?? '';
$dark_bg     = $attributes['darkBgColor'] ?? '';

$bg_style = $bg_color ? ' background-color:' . esc_attr($bg_color) . ';' : '';

// Register dark mode override for this spacer if set
$dark_class = '';
if ($dark_bg) {
    $dark_class = 'eb-spacer-dark-' . substr(md5($dark_bg . uniqid()), 0, 8);
    eb_register_dark_section_color($dark_bg, $dark_class);
}
?>

<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
    <tr>
        <td class="<?php echo esc_attr(trim($mob_class . ' ' . $dark_class)); ?>" style="padding: <?php echo esc_attr($spacing); ?>px 0 0; font-size:0; line-height:0;<?php echo $bg_style; ?>">
            &nbsp;
        </td>
    </tr>
</table>
