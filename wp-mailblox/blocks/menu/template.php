<?php
if (!defined('ABSPATH')) exit;

$attributes      = $template_attributes ?? [];
$items           = $attributes['items'] ?? [];
$align           = $attributes['align'] ?? 'center';
$font_size       = intval($attributes['fontSize'] ?? 14);
$text_color      = $attributes['textColor'] ?? '#000000';
$separatorWidth  = $attributes['separatorWidth'] ?? 0;
$separator_color = $attributes['separatorColor'] ?? '#cccccc';
$padding_top     = eb_snap_to_5($template_attributes['paddingTop'] ?? 0);
$padding_bottom  = eb_snap_to_5($template_attributes['paddingBottom'] ?? 0);
$padding_left    = eb_snap_to_5($template_attributes['paddingLeft'] ?? 0);
$padding_right   = eb_snap_to_5($template_attributes['paddingRight'] ?? 0);
$font_family     = $attributes['body_font_stack'] ?? $attributes['body_font'] ?? 'Helvetica, Arial, sans-serif';

$mob_align_classes   = eb_mobile_classes($attributes, false, true);
$mob_font_size       = $attributes['mobileFontSize'] ?? null;
if ($mob_font_size !== null && $mob_font_size !== '') {
    $mob_align_classes = trim($mob_align_classes . ' eb-mob-fs-' . intval($mob_font_size));
}
if (!empty($attributes['hideOnMobile'])) $mob_align_classes = trim('eb-mob-hide ' . $mob_align_classes);

$mob_padding_classes = eb_mobile_classes($attributes, true, false);

$tags = $template_context['tags'] ?? [];

if (empty($items)) {
    $items = [
        ['label' => 'Home',    'url' => '#'],
        ['label' => 'About',   'url' => '#'],
        ['label' => 'Contact', 'url' => '#'],
    ];
}
$column_count = count($items);
$column_width = intval(100 / $column_count);

$resolved_items = array_map(function($item) use ($tags, $attributes, $template_context) {
    $url = $item['url'] ?? '#';
    foreach ($tags as $key => $tag) {
        $url = str_replace('[[' . $key . ']]', $tag, $url);
    }
    return [
        'label' => $item['label'] ?? '',
        'url'   => eb_append_utm($url, $attributes, $template_context['utm_defaults'] ?? []),
    ];
}, $items);
?>

<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" class="eb_menu">
    <tr>
        <td style="padding:0;">
            <table role="presentation" border="0" cellpadding="0" cellspacing="0"
                   align="<?php echo esc_attr($align); ?>"
                   class="eb-menu-inner">
                   <tbody>
                <tr>
                    <?php foreach ($resolved_items as $i => $item) :
                        $is_first = ($i === 0);
                    ?>
                        <td align="center"
                            class="<?php echo esc_attr(trim($mob_padding_classes . ' ' . $mob_align_classes)); ?>"
                            style="<?php echo (!$is_first) ? 'border-left: '.$separatorWidth.'px solid '.$separator_color : ''; ?> !important; width: <?php echo $column_width; ?>%; padding: <?php echo $padding_top; ?>px <?php echo $padding_right; ?>px <?php echo $padding_bottom; ?>px <?php echo $padding_left; ?>px; margin:0;border:0;">
                            <div style="display:block;">
                                <a href="<?php echo $item['url']; ?>" style="
                                    font-family: <?php echo esc_attr($font_family); ?>;
                                    font-size: <?php echo esc_attr($font_size); ?>px;
                                    color: <?php echo esc_attr($text_color); ?>;
                                    text-decoration: none;
                                    line-height: 1.5;
                                    display: block;
                                "><?php echo esc_html($item['label']); ?></a>
                            </div>
                        </td>
                    <?php endforeach; ?>
                </tr>
                   </tbody>
            </table>
        </td>
    </tr>
</table>
