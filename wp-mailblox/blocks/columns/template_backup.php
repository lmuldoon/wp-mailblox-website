<?php
if (!defined('ABSPATH')) exit;

$inner_blocks = $template_attributes['innerBlocks'] ?? [];
if (!is_array($inner_blocks)) {
    $inner_blocks = [];
}

$context         = $context ?? [];
$container_width = $context['container_width'] ?? 640;
$column_count    = count($inner_blocks);
$gap             = intval($template_attributes['gap'] ?? 20);
$column_widths   = $template_attributes['columnWidths'] ?? [];
$reverse         = !empty($template_attributes['reverse']) && $column_count === 2;

$padding_top    = eb_snap_to_5($template_attributes['paddingTop'] ?? 20);
$padding_bottom = eb_snap_to_5($template_attributes['paddingBottom'] ?? 20);
$padding_left   = eb_snap_to_5($template_attributes['paddingLeft'] ?? 20);
$padding_right  = eb_snap_to_5($template_attributes['paddingRight'] ?? 20);

$hide_on_mobile = !empty($template_attributes['hideOnMobile']);
$mob_classes    = eb_mobile_classes($template_attributes, true, false);
if ($hide_on_mobile) $mob_classes = trim('eb-mob-hide ' . $mob_classes);

// Background
$background_color = $template_attributes['backgroundColor'] ?? '';

// Border radius
$border_radius_tl = intval($template_attributes['borderRadiusTL'] ?? 0);
$border_radius_tr = intval($template_attributes['borderRadiusTR'] ?? 0);
$border_radius_br = intval($template_attributes['borderRadiusBR'] ?? 0);
$border_radius_bl = intval($template_attributes['borderRadiusBL'] ?? 0);
$has_radius = $border_radius_tl || $border_radius_tr || $border_radius_br || $border_radius_bl;
$border_radius_style = $has_radius
    ? sprintf('border-radius:%dpx %dpx %dpx %dpx;', $border_radius_tl, $border_radius_tr, $border_radius_br, $border_radius_bl)
    : '';

if ($column_count === 0) return;

// Width calculations
$available_width   = $container_width - $padding_left - $padding_right;
$total_gap         = $gap * ($column_count - 1);
$columns_available = $available_width - $total_gap;

if (!empty($column_widths) && count($column_widths) === $column_count) {
    $pixel_widths = array_map(fn ($w) => max(40, intval($w)), $column_widths);
    if (array_sum($pixel_widths) > $columns_available) {
        $base         = floor($columns_available / $column_count);
        $remainder    = $columns_available - ($base * $column_count);
        $pixel_widths = array_fill(0, $column_count, $base);
        $pixel_widths[$column_count - 1] += $remainder;
    }
} else {
    $base         = floor($columns_available / $column_count);
    $remainder    = $columns_available - ($base * $column_count);
    $pixel_widths = array_fill(0, $column_count, $base);
    $pixel_widths[$column_count - 1] += $remainder;
}

$pixel_widths = array_map(fn ($w) => max(40, $w), $pixel_widths);
$mso_total = array_sum($pixel_widths) + $total_gap;
?>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
    style="max-width:<?php echo esc_attr($container_width); ?>px;">
    <tr>
        <td align="center"
            class="<?php echo esc_attr($mob_classes); ?>"
            style="
                font-size:0;
                line-height:0;
                padding:<?php echo $padding_top . 'px ' . $padding_right . 'px ' . $padding_bottom . 'px ' . $padding_left . 'px'; ?>;
                <?php if ($background_color) echo 'background-color:' . esc_attr($background_color) . ';'; ?>
                <?php echo $border_radius_style; ?>
            ">

            <!-- ================= MSO ================= -->
            <!--[if mso]>
            <?php if ($reverse) : ?>
            <table width="<?php echo $mso_total; ?>" dir="rtl" align="center"><tr>
            <?php else : ?>
            <table width="<?php echo $mso_total; ?>" align="center"><tr>
            <?php endif; ?>
            <![endif]-->

            <!-- ✅ NON-MSO REVERSE WRAPPER -->
            <!-- <?php if ($reverse): ?>
                <div dir="rtl">
            <?php endif; ?> -->
            <?php
            if ($reverse) {
                    $float = ($column_count === 2 && $i === 1) ? 'left' : 'right';
                } else {
                    $float = ($column_count === 2 && $i === 1) ? 'right' : 'left';
                }
            ?>
            <?php foreach ($inner_blocks as $i => $block) :
                $col_width = $pixel_widths[$i];
                $is_last   = ($i === $column_count - 1);
            ?>

<table align="<?php echo $float; ?>" cellpadding="0" cellspacing="0" role="none" style="mso-table-lspace:0pt;mso-table-rspace:0pt;border-collapse:collapse;border-spacing:0px;float:<?php echo $float; ?>;">
    <tr><td valign="top" align="center" style="padding:0; margin:0; width:<?php echo $col_width; ?>px;">
                <!--[if mso]>
                <td width="<?php echo $col_width; ?>" valign="top">
                <![endif]-->

                <table class="eb-column-wrap"
                    dir="ltr"
                    width="100%"
                    style="
                        border-collapse:collapse;
                        border-spacing:0;
                        width:100%;
                        display:inline-block;
                        vertical-align:top;
                    ">
                    <tr>
                        <td class="<?php echo (!$is_last) ? 'pb20' : ''; ?>"
                            style="width:100%;"
                            valign="top">
                            <?php echo eb_render_block_recursive($block, $template_context); ?>
                        </td>
                    </tr>
                </table>

                <!--[if mso]>
                </td>
                <![endif]-->
            </td></tr>
</table>
            <?php endforeach; ?>

            <!-- <?php if ($reverse): ?>
                </div>
            <?php endif; ?> -->

            <!--[if mso]>
            </tr></table>
            <![endif]-->

        </td>
    </tr>
</table>