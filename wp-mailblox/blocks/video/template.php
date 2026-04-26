<?php
if (!defined('ABSPATH')) exit;

$attributes   = $template_attributes ?? [];
$video_url    = $attributes['videoUrl']           ?? '';
$thumbnail    = ($attributes['customThumbnailUrl'] ?? '') ?: ($attributes['thumbnailUrl'] ?? '');

if (empty($thumbnail) && empty($video_url)) return;

$align          = in_array($attributes['align'] ?? 'center', ['left', 'center', 'right'])
                  ? ($attributes['align'] ?? 'center') : 'center';
$border_radius  = max(0, intval($attributes['borderRadius'] ?? 0));
$show_play      = $attributes['showPlayButton'] ?? true;
$play_color     = $attributes['playButtonColor'] ?? '#ffffff';
$show_caption   = $attributes['showCaption'] ?? false;
$caption        = $attributes['caption'] ?? '';

$padding_top    = eb_snap_to_5($attributes['paddingTop']    ?? 0);
$padding_bottom = eb_snap_to_5($attributes['paddingBottom'] ?? 0);
$padding_left   = eb_snap_to_5($attributes['paddingLeft']   ?? 0);
$padding_right  = eb_snap_to_5($attributes['paddingRight']  ?? 0);
$mob_classes    = eb_mobile_classes($attributes, true, false);
if (!empty($attributes['hideOnMobile'])) $mob_classes = trim('eb-mob-hide ' . $mob_classes);

$font_family = $template_context['styles']['body_font_stack']  ?? $template_context['styles']['body_font'] ?? 'Helvetica, Arial, sans-serif';
$font_size   = intval($template_context['styles']['body_size'] ?? 16);
$background  = $template_context['background'] ?? '#ffffff';
$auto_color  = eb_get_contrast_colour($background, $template_context);

$container_width = intval($template_context['container_width'] ?? 640);
$section_pad     = $template_context['section_padding'] ?? ['left' => 20, 'right' => 20];
$available       = $container_width - $section_pad['left'] - $section_pad['right'] - $padding_left - $padding_right;

$utm_context = array_merge(
    $template_context['utm_defaults'] ?? [],
    array_filter([
        'utmSource'   => $attributes['utmSource']   ?? '',
        'utmMedium'   => $attributes['utmMedium']   ?? '',
        'utmCampaign' => $attributes['utmCampaign'] ?? '',
        'utmContent'  => $attributes['utmContent']  ?? '',
        'utmTerm'     => $attributes['utmTerm']     ?? '',
    ])
);

$link_url = $video_url ? eb_append_utm($video_url, $utm_context) : '';

$td_align = $align === 'left' ? 'left' : ($align === 'right' ? 'right' : 'center');

// Build an inline SVG play button as a data URI for Outlook compatibility
// We use a position:relative overlay on a <td> for modern clients,
// and omit it for Outlook via MSO conditionals.
$play_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="52" height="52" viewBox="0 0 52 52">'
          . '<circle cx="26" cy="26" r="26" fill="rgba(0,0,0,0.5)"/>'
          . '<polygon points="20,14 40,26 20,38" fill="' . esc_attr($play_color) . '"/>'
          . '</svg>';
$play_data_uri = 'data:image/svg+xml;base64,' . base64_encode($play_svg);

?>

<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td class="<?php echo esc_attr($mob_classes); ?>" align="<?php echo esc_attr($td_align); ?>" style="padding: <?php echo esc_attr($padding_top); ?>px <?php echo esc_attr($padding_right); ?>px <?php echo esc_attr($padding_bottom); ?>px <?php echo esc_attr($padding_left); ?>px;">

            <?php if ($thumbnail) : ?>

            <!--[if mso]>
            <table role="presentation" border="0" cellspacing="0" cellpadding="0" align="<?php echo esc_attr($td_align); ?>"><tr><td>
            <![endif]-->

            <div style="position: relative; display: inline-block; max-width: 100%; line-height: 0;">
                <?php if ($link_url) : ?>
                <a href="<?php echo esc_url($link_url); ?>" style="display: block; text-decoration: none; line-height: 0;">
                <?php endif; ?>

                    <img src="<?php echo esc_url($thumbnail); ?>"
                         alt="<?php echo esc_attr($attributes['videoTitle'] ?? 'Watch video'); ?>"
                         width="<?php echo esc_attr($available); ?>"
                         style="display: block; width: 100%; max-width: <?php echo esc_attr($available); ?>px; height: auto; border: none; border-radius: <?php echo esc_attr($border_radius); ?>px;">

                    <?php if ($show_play) : ?>
                    <!--[if !mso]><!-->
                    <img src="<?php echo esc_attr($play_data_uri); ?>"
                         alt="Play"
                         width="52"
                         height="52"
                         style="position: absolute; top: 50%; left: 50%; margin-top: -26px; margin-left: -26px; display: block; border: none; pointer-events: none;">
                    <!--<![endif]-->
                    <?php endif; ?>

                <?php if ($link_url) : ?>
                </a>
                <?php endif; ?>
            </div>

            <!--[if mso]>
            </td></tr></table>
            <![endif]-->

            <?php else : ?>

            <?php if ($link_url) : ?>
            <a href="<?php echo esc_url($link_url); ?>" class="button" style="
                display: inline-block;
                padding: <?php echo esc_attr(intval($font_size * 0.7)); ?>px <?php echo esc_attr(intval($font_size * 1.5)); ?>px;
                background-color: #cc0000;
                color: #ffffff;
                text-decoration: none;
                border-radius: 4px;
                font-family: <?php echo esc_attr($font_family); ?>;
                font-size: <?php echo esc_attr($font_size); ?>px;
                font-weight: 600;
            ">▶ Watch Video</a>
            <?php endif; ?>

            <?php endif; ?>

            <?php if ($show_caption && $caption) : ?>
            <p style="margin: 8px 0 0; font-family: <?php echo esc_attr($font_family); ?>; font-size: <?php echo esc_attr($font_size - 2); ?>px; color: <?php echo esc_attr($auto_color); ?>; opacity: 0.7; text-align: <?php echo esc_attr($align); ?>;"><?php echo esc_html($caption); ?></p>
            <?php endif; ?>

        </td>
    </tr>
</table>
