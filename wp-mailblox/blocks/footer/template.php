<?php
if (!defined('ABSPATH')) exit;

$attributes      = $template_attributes ?? [];
$show_unsub      = $attributes['showUnsubscribe'] ?? true;
$unsub_text      = $attributes['unsubscribeText'] ?? 'Unsubscribe';
$show_online     = $attributes['showViewOnline'] ?? true;
$online_text     = $attributes['viewOnlineText'] ?? 'View this email online';
$address         = $attributes['address'] ?? '';
$footer_text     = $attributes['footerText'] ?? '';

$font_family     = $attributes['body_font_stack'] ?? $attributes['body_font'] ?? 'Helvetica, Arial, sans-serif';
$font_size       = $attributes['body_size'] ?? 14;
$footer_size     = max(10, intval($font_size) - 4);
$address_size    = max(9, intval($font_size) - 5);
$background      = $template_context['background'] ?? '#ffffff';
$text_color      = eb_get_contrast_colour($background, $template_context);

// Pull resolved platform tags from context
$tags            = $template_context['tags'] ?? [];
$unsub_url       = $tags['UNSUB'] ?? '#';
$view_online_url = $tags['WEBVERSION'] ?? '#';
$cm_unsub_wrap   = ($template_context['platform'] ?? '') === 'campaign_monitor';
$is_emailoctopus = ($template_context['platform'] ?? '') === 'emailoctopus';
$eo_rewards_url  = $is_emailoctopus ? ($tags['REWARDS'] ?? null) : null;
$eo_sender_info  = $is_emailoctopus ? ($tags['SENDER_INFO'] ?? null) : null;
$has_bg_image = $template_context['has_background_image'] ?? false;
$align        = $template_attributes['align'] ?? 'left';

$text_color   = !empty($template_attributes['textColor'])
    ? $template_attributes['textColor']
    : eb_get_contrast_colour($background, $template_context);
?>

<!-- eb-footer-start --><table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td class="eb-dark-text-colour-enabled" style="
            font-family: <?php echo esc_attr($font_family); ?>;
            font-size: <?php echo esc_attr($footer_size); ?>px;
            color: <?php echo esc_attr($text_color); ?>;
            text-align: center;
            padding: 20px;
            opacity: 0.6;
            mso-line-height-rule: exactly;
        ">

            <?php if (!empty($footer_text)) : ?>
                <p class="<?php echo ($has_bg_image) ? 'section-bg-image' : 'dark-mode-text'; ?>" style="margin: 0 0 10px; font-size: <?php echo esc_attr($footer_size); ?>px; line-height: 1.5;">
                    <?php echo wp_kses_post($footer_text); ?>
                </p>
            <?php endif; ?>

            <?php if ($show_unsub || $show_online) : ?>
                <p class="<?php echo ($has_bg_image) ? 'section-bg-image' : 'dark-mode-text'; ?>" style="margin: 0 0 10px;">
                    <?php if ($show_online) : ?>
                        <?php // $view_online_url is a platform merge tag (e.g. *|ARCHIVE|*) — esc_url() would mangle it, intentionally raw ?>
                        <a href="<?php echo $view_online_url; ?>" style="color: <?php echo esc_attr($text_color); ?>; text-decoration: underline;">
                            <?php echo esc_html($online_text); ?>
                        </a>
                    <?php endif; ?>

                    <?php if ($show_online && $show_unsub) : ?>
                        &nbsp;|&nbsp;
                    <?php endif; ?>

                    <?php if ($show_unsub) : ?>
                        <?php // $unsub_url is a platform merge tag (e.g. *|UNSUB|*) — esc_url() would mangle it, intentionally raw ?>
                        <?php if ($cm_unsub_wrap) : ?>
                            <unsubscribe><?php echo esc_html($unsub_text); ?></unsubscribe>
                        <?php else : ?>
                            <?php // $unsub_url is a platform merge tag — esc_url() would mangle it, intentionally raw ?>
                            <a href="<?php echo $unsub_url; ?>" style="color: <?php echo esc_attr($text_color); ?>; text-decoration: underline;"><?php echo esc_html($unsub_text); ?></a>
                        <?php endif; ?>
                    <?php endif; ?>
                </p>
            <?php endif; ?>

            <?php if (!empty($address)) : ?>
                <p class="<?php echo ($has_bg_image) ? 'section-bg-image' : 'dark-mode-text'; ?>" style="margin: 0; font-size: <?php echo esc_attr($address_size); ?>px; line-height: 1.5;">
                    <?php echo wp_kses($address, ['br' => []]); ?>
                </p>
            <?php endif; ?>

            <?php if ($eo_sender_info) : ?>
                <?php // $eo_sender_info is the {{SenderInfo}} merge tag — esc_html() would mangle it, intentionally raw ?>
                <p class="<?php echo ($has_bg_image) ? 'section-bg-image' : 'dark-mode-text'; ?>" style="margin: <?php echo empty($address) ? '0' : '6px 0 0'; ?>; font-size: <?php echo esc_attr($address_size); ?>px; line-height: 1.5;">
                    <?php echo $eo_sender_info; ?>
                </p>
            <?php endif; ?>

            <?php if ($eo_rewards_url) : ?>
                <?php // $eo_rewards_url is the {{RewardsURL}} merge tag — esc_url() would mangle it, intentionally raw ?>
                <p class="<?php echo ($has_bg_image) ? 'section-bg-image' : 'dark-mode-text'; ?>" style="margin: 10px 0 0; font-size: <?php echo esc_attr($address_size); ?>px; line-height: 1.5;">
                    <a href="<?php echo $eo_rewards_url; ?>" style="color: <?php echo esc_attr($text_color); ?>; text-decoration: underline;">Email Marketing by EmailOctopus</a>
                </p>
            <?php endif; ?>

        </td>
    </tr>
</table>