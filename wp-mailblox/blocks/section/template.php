<?php
if (!defined('ABSPATH')) exit;

$container_width = $template_context['container_width'] ?? 640;
// Use the actual saved attribute for rendering (transparent when not set).
// $template_context['background'] is the effective background for child contrast calculations
// and is correctly set by render.php to the preset bg_color when no section bg is explicit.
$bg_attr         = $template_attributes['backgroundColor'] ?? '';
$backgroundColor = ($bg_attr && $bg_attr !== 'transparent') ? $bg_attr : 'transparent';
$darkBackgroundColor = $template_attributes['darkBackgroundColor'] ?? '';
$inner_blocks        = $template_attributes['innerBlocks'] ?? [];
$backgroundImageEnabled = $template_attributes['backgroundImageEnabled'] ?? false;
$backgroundImageUrl     = $template_attributes['backgroundImageUrl'] ?? '';
$backgroundRepeat       = $template_attributes['backgroundRepeat'] ?? 'no-repeat';
$backgroundPositionX    = $template_attributes['backgroundPositionX'] ?? 'center';
$backgroundPositionY    = $template_attributes['backgroundPositionY'] ?? 'center';
$bg_size_w              = $template_attributes['backgroundSizeW'] ?? 'cover';
$bg_size_h              = $template_attributes['backgroundSizeH'] ?? 'auto';
$backgroundSize         = in_array($bg_size_w, ['cover', 'contain']) ? $bg_size_w : $bg_size_w . ' ' . $bg_size_h;

$padding_top    = eb_snap_to_5($template_attributes['paddingTop']    ?? 0);
$padding_bottom = eb_snap_to_5($template_attributes['paddingBottom'] ?? 0);

$mob_pt = $template_attributes['mobilePaddingTop']    ?? null;
$mob_pb = $template_attributes['mobilePaddingBottom'] ?? null;

$hide_on_mobile = !empty($template_attributes['hideOnMobile']);

$mob_classes = [];
if ($hide_on_mobile)                     $mob_classes[] = 'eb-mob-hide';
if ($mob_pt !== null && $mob_pt !== '') $mob_classes[] = 'eb-mob-pt-' . intval($mob_pt);
if ($mob_pb !== null && $mob_pb !== '') $mob_classes[] = 'eb-mob-pb-' . intval($mob_pb);

// Generate a unique dark mode class for this section if it has a dark colour set
$dark_class = '';
if (!empty($darkBackgroundColor)) {
    $dark_class = 'eb-dark-' . ltrim($darkBackgroundColor, '#');
    eb_register_dark_section_color($darkBackgroundColor, $dark_class);
}

$all_classes = array_filter(array_merge(
    $dark_class ? [$dark_class] : [],
    $mob_classes
));

$has_bg_image = $backgroundImageEnabled && !empty($backgroundImageUrl);
if ($has_bg_image) {
    $has_bg_image_class = "bg-image";
} else {
    $has_bg_image_class = "no-bg-image";
}

?>

<table class="eb-section" role="presentation" cellspacing="0" cellpadding="0" border="0" align="center" width="100%">
    <tr>
        <?php
        $background_image_style = '';

        if ($backgroundImageEnabled && !empty($backgroundImageUrl)) {
            $background_image_style = sprintf(
                'background-image: url(%s); background-repeat: %s; background-position: %s %s; background-size: %s;',
                esc_url($backgroundImageUrl),
                esc_attr($backgroundRepeat),
                esc_attr($backgroundPositionX),
                esc_attr($backgroundPositionY),
                esc_attr($backgroundSize)
            );
        }
        ?>
        <td valign="top" align="center" class="<?php echo $has_bg_image_class; ?> section-td<?php echo $all_classes ? ' ' . esc_attr(implode(' ', $all_classes)) : ''; ?>" style="
    <?php if ($backgroundColor !== 'transparent') : ?>background-color: <?php echo esc_attr($backgroundColor); ?>;<?php endif; ?>
    <?php if ($padding_top > 0)    : ?>padding-top: <?php echo esc_attr($padding_top); ?>px;<?php endif; ?>
    <?php if ($padding_bottom > 0) : ?>padding-bottom: <?php echo esc_attr($padding_bottom); ?>px;<?php endif; ?>
    <?php echo $background_image_style; ?>">

            <?php
            foreach ($inner_blocks as $block) {
                echo eb_render_block_recursive($block, $template_context);
            }
            ?>
        </td>
    </tr>
</table>