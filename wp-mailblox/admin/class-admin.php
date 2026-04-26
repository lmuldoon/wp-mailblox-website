<?php
// /admin/class-admin.php

if (!defined('ABSPATH')) exit;

class EB_Admin
{
    public function add_meta_boxes()
    {
        add_meta_box(
            'eb_preset_settings',
            'Preset Settings',
            [$this, 'render_preset_box'],
            'eb_preset',
            'advanced',
            'default'
        );

        add_meta_box(
            'eb_define_preset',
            'Set Email Preset',
            [$this, 'render_preset_defintion'],
            'eb_email_template',
            'side',
            'high'
        );

        add_meta_box(
            'eb_dark_mode_override',
            'Dark Mode Override',
            [$this, 'render_dark_mode_override_box'],
            'eb_email_template',
            'side',
            'default'
        );

        add_meta_box(
            'eb_preheader',
            'Subject & Preheader',
            [$this, 'render_preheader_box'],
            'eb_email_template',
            'side',
            'default'
        );

        add_meta_box(
            'eb_utm_defaults',
            'UTM Defaults',
            [$this, 'render_utm_box'],
            'eb_email_template',
            'side',
            'default'
        );
    }

    public function render_preset_defintion($post)
    {
        wp_nonce_field('eb_save_email_meta', 'eb_email_meta_nonce');
        $selected = get_post_meta($post->ID, 'eb_preset', true);

        $presets = get_posts([
            'post_type'      => 'eb_preset',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'orderby'        => 'title',
            'order'          => 'ASC',
        ]);

        $new_preset_url  = admin_url('post-new.php?post_type=eb_preset');
        $platform_override = get_post_meta($post->ID, 'eb_platform', true) ?: '';
    ?>
        <div class="eb-field">
            <?php if (empty($presets)) : ?>
                <p style="color: #cc0000; font-weight: 600; margin: 0 0 8px;">
                    ⚠ No presets found.
                </p>
                <p class="description" style="margin: 0 0 8px;">
                    A preset is required before you can preview or export this template.
                </p>
                <a href="<?php echo esc_url($new_preset_url); ?>" class="button button-primary">
                    Create a preset →
                </a>
            <?php else : ?>
                <select name="eb_preset" id="eb_preset" class="widefat">
                    <?php foreach ($presets as $preset) : ?>
                        <option value="<?php echo esc_attr($preset->ID); ?>" <?php selected($selected, $preset->ID); ?>>
                            <?php echo esc_html($preset->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="description">Choose which preset to use for this email.</p>
            <?php endif; ?>
        </div>

        <div class="eb-field">
            <label for="eb_platform_override">Platform Override</label>
            <select name="eb_platform" id="eb_platform_override" class="widefat">
                <option value="" <?php selected($platform_override, ''); ?>>— inherit from preset —</option>
                <?php foreach ($this->get_platforms() as $value => $label) : ?>
                    <option value="<?php echo esc_attr($value); ?>" <?php selected($platform_override, $value); ?>>
                        <?php echo esc_html($label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <p class="description">Override the platform merge tags for this email only. Inherits from the preset by default.</p>
        </div>
    <?php
    }

    public function render_utm_box($post)
    {
        $utm_source   = get_post_meta($post->ID, 'eb_utm_source',   true) ?: '';
        $utm_medium   = get_post_meta($post->ID, 'eb_utm_medium',   true) ?: '';
        $utm_campaign = get_post_meta($post->ID, 'eb_utm_campaign', true) ?: '';
        $utm_content  = get_post_meta($post->ID, 'eb_utm_content',  true) ?: '';
        $utm_term     = get_post_meta($post->ID, 'eb_utm_term',     true) ?: '';
    ?>
        <p class="description" style="margin-bottom: 10px;">
            Default UTM parameters appended to all links. Individual blocks can override these.
        </p>

        <div class="eb-field">
            <label for="eb_utm_source">Source</label>
            <input type="text" name="eb_utm_source" id="eb_utm_source" class="widefat"
                placeholder="e.g. newsletter" value="<?php echo esc_attr($utm_source); ?>">
        </div>

        <div class="eb-field">
            <label for="eb_utm_medium">Medium</label>
            <input type="text" name="eb_utm_medium" id="eb_utm_medium" class="widefat"
                placeholder="e.g. email" value="<?php echo esc_attr($utm_medium); ?>">
        </div>

        <div class="eb-field">
            <label for="eb_utm_campaign">Campaign</label>
            <input type="text" name="eb_utm_campaign" id="eb_utm_campaign" class="widefat"
                placeholder="e.g. spring-sale-2026" value="<?php echo esc_attr($utm_campaign); ?>">
        </div>

        <div class="eb-field">
            <label for="eb_utm_content">Content <span style="font-weight:400; color:#757575;">(optional)</span></label>
            <input type="text" name="eb_utm_content" id="eb_utm_content" class="widefat"
                placeholder="e.g. hero-button" value="<?php echo esc_attr($utm_content); ?>">
        </div>

        <div class="eb-field">
            <label for="eb_utm_term">Term <span style="font-weight:400; color:#757575;">(optional)</span></label>
            <input type="text" name="eb_utm_term" id="eb_utm_term" class="widefat"
                placeholder="e.g. running+shoes" value="<?php echo esc_attr($utm_term); ?>">
        </div>
    <?php
    }

    public function render_preheader_box($post)
    {
        $subject   = get_post_meta($post->ID, 'eb_subject', true) ?: '';
        $preheader = get_post_meta($post->ID, 'eb_preheader', true) ?: '';
    ?>
        <div class="eb-field">
            <label for="eb_subject">Subject Line</label>
            <input
                type="text"
                name="eb_subject"
                id="eb_subject"
                class="widefat"
                maxlength="200"
                placeholder="e.g. Your order has shipped!"
                value="<?php echo esc_attr($subject); ?>"
            >
            <p class="description">Used as the email &lt;title&gt; in the exported HTML.</p>
        </div>

        <div class="eb-field">
            <label for="eb_preheader">Preheader Text</label>
            <p class="description" style="margin-bottom: 6px;">Appears in inbox previews next to the subject line. Aim for 40–130 characters.</p>
            <textarea
                name="eb_preheader"
                id="eb_preheader"
                class="widefat"
                rows="3"
                maxlength="200"
                placeholder="e.g. Don't miss our biggest sale of the year..."
                style="resize: vertical;"
            ><?php echo esc_textarea($preheader); ?></textarea>
            <p class="description" style="margin-top: 4px;"><span id="eb-preheader-count"><?php echo esc_html(strlen($preheader)); ?></span>/200 characters</p>
        </div>
    <?php
    }

    public function render_dark_mode_override_box($post)
    {
        $dark_bg_override = get_post_meta($post->ID, 'eb_dark_bg_color_override', true) ?: '';

        $dark_swatches = [
            '#000000' => 'Pure Black',
            '#121212' => 'Material Dark',
            '#1a1a1a' => 'Soft Black',
            '#222222' => 'Dark Grey',
        ];
    ?>
        <script>
            jQuery(document).ready(function($) {
                $('.eb-color-field-override').wpColorPicker();
            });
        </script>

        <p class="description" style="margin-bottom: 10px;">
            Override the dark mode background from your preset for this email only. Leave empty to use the preset default.
        </p>

        <div class="eb-field">
            <label><strong>Recommended Dark Backgrounds</strong></label>
            <div style="display: flex; gap: 8px; margin-bottom: 8px; flex-wrap: wrap;">
                <?php foreach ($dark_swatches as $hex => $label) : ?>
                    <button type="button" class="eb-dark-swatch" data-color="<?php echo esc_attr($hex); ?>" title="<?php echo esc_attr($label); ?>" style="
                            background-color: <?php echo esc_attr($hex); ?>;
                            width: 32px;
                            height: 32px;
                            border: 2px solid #ccc;
                            border-radius: 4px;
                            cursor: pointer;
                            padding: 0;
                        "></button>
                <?php endforeach; ?>
            </div>
            <p class="description" style="margin-bottom: 8px;">
                <?php foreach ($dark_swatches as $hex => $label) : ?>
                    <span style="margin-right: 10px; font-size: 11px;"><?php echo esc_html($hex); ?> — <?php echo esc_html($label); ?></span>
                <?php endforeach; ?>
            </p>
        </div>

        <div class="eb-field">
            <!-- <label for="eb_dark_bg_color_override"><strong>Dark Mode Background Override</strong></label> -->
            <input type="text" name="eb_dark_bg_color_override" id="eb_dark_bg_color_override" value="<?php echo esc_attr($dark_bg_override); ?>" class="widefat eb-color-field-override" placeholder="Leave empty to use preset">
        </div>

        <script>
            jQuery(document).ready(function($) {
                $('.eb-dark-swatch').on('click', function() {
                    var color = $(this).data('color');
                    $('#eb_dark_bg_color_override').val(color).trigger('change');
                    if ($('#eb_dark_bg_color_override').wpColorPicker) {
                        $('#eb_dark_bg_color_override').wpColorPicker('color', color);
                    }
                    $('.eb-dark-swatch').css('border-color', '#ccc');
                    $(this).css('border-color', '#2271b1');
                });
            });
        </script>
    <?php
    }

    public function render_preset_box($post)
    {
        wp_nonce_field('eb_save_email_meta', 'eb_email_meta_nonce');

        $platform = get_post_meta($post->ID, 'eb_platform', true) ?: 'mailchimp';

        $container_width = intval(get_post_meta($post->ID, 'eb_container_width', true) ?: 640);


        $bg_color                  = get_post_meta($post->ID, 'eb_body_bg_color', true) ?: '#ffffff';
        $body_bg_image_enabled     = get_post_meta($post->ID, 'eb_body_bg_image_enabled', true) ?: '';
        $body_bg_image_url         = get_post_meta($post->ID, 'eb_body_bg_image_url', true) ?: '';
        $body_bg_image_repeat      = get_post_meta($post->ID, 'eb_body_bg_image_repeat', true) ?: 'no-repeat';
        $body_bg_image_position_x  = get_post_meta($post->ID, 'eb_body_bg_image_position_x', true) ?: 'center';
        $body_bg_image_position_y  = get_post_meta($post->ID, 'eb_body_bg_image_position_y', true) ?: 'center';
        $body_bg_image_size_w      = get_post_meta($post->ID, 'eb_body_bg_image_size_w', true) ?: 'cover';
        $text_color_dark    = get_post_meta($post->ID, 'eb_text_color_dark', true) ?: '#000000';
        $text_color_light   = get_post_meta($post->ID, 'eb_text_color_light', true) ?: '#ffffff';
        $button_color       = get_post_meta($post->ID, 'eb_button_color', true) ?: '#000000';
        $button_text_color  = get_post_meta($post->ID, 'eb_button_text_color', true) ?: '#ffffff';
        $sale_price_color   = get_post_meta($post->ID, 'eb_sale_price_color', true) ?: '#c0392b';

        $dark_bg_color          = get_post_meta($post->ID, 'eb_dark_bg_color', true) ?: '#121212';
        $dark_text_color        = get_post_meta($post->ID, 'eb_dark_text_color', true) ?: '#ffffff';
        $dark_button_color      = get_post_meta($post->ID, 'eb_dark_button_color', true) ?: '#ffffff';
        $dark_button_text_color = get_post_meta($post->ID, 'eb_dark_button_text_color', true) ?: '#000000';
        $dark_link_color        = get_post_meta($post->ID, 'eb_dark_link_color', true) ?: '#ffffff';

        $preset_logo_url   = get_post_meta($post->ID, 'eb_logo_url', true) ?: '';
        $preset_logo_alt   = get_post_meta($post->ID, 'eb_logo_alt', true) ?: '';
        $dark_logo_enabled = get_post_meta($post->ID, 'eb_dark_logo_enabled', true) ?: '';
        $dark_logo_url     = get_post_meta($post->ID, 'eb_dark_logo_url', true) ?: '';

        $dark_swatches = [
            '#000000' => 'Pure Black',
            '#121212' => 'Material Dark',
            '#1a1a1a' => 'Soft Black',
            '#222222' => 'Dark Grey',
        ];

        $websafe_fonts = [];
        $fonts_file = EB_PLUGIN_PATH . 'fonts/websafe.json';
        if (file_exists($fonts_file)) {
            $websafe_fonts = json_decode(file_get_contents($fonts_file), true) ?: [];
        }
    ?>

        <div class="eb-preset-tabs">

            <nav class="eb-preset-tab-nav">
                <button type="button" class="eb-tab-btn active" data-tab="general">General</button>
                <button type="button" class="eb-tab-btn" data-tab="logo">Logo</button>
                <button type="button" class="eb-tab-btn" data-tab="typography">Typography</button>
                <button type="button" class="eb-tab-btn" data-tab="colours">Colours</button>
                <button type="button" class="eb-tab-btn" data-tab="dark-mode">Dark Mode</button>
            </nav>

            <!-- ── GENERAL ──────────────────────────────────────────────────── -->
            <div class="eb-tab-panel active" data-panel="general">

                <div class="eb-field">
                    <label for="eb_platform">Platform</label>
                    <select name="eb_platform" id="eb_platform" class="widefat">
                        <?php foreach ($this->get_platforms() as $value => $label) : ?>
                            <option value="<?php echo esc_attr($value); ?>" <?php selected($platform, $value); ?>>
                                <?php echo esc_html($label); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description">Choose which platform this preset is for.</p>
                </div>

                <div class="eb-field">
                    <label for="eb_container_width">Container Width: <span id="eb-container-width-display"><?php echo esc_html($container_width); ?></span>px</label>
                    <input type="range" name="eb_container_width" id="eb_container_width" min="500" max="800" step="10" value="<?php echo esc_attr($container_width); ?>" class="widefat">
                    <p class="description" style="display:flex; justify-content:space-between;">
                        <span>500px</span>
                        <span>800px</span>
                    </p>
                    <p class="description">Width of the email container. 600–640px is recommended for best compatibility.</p>
                </div>

            </div><!-- /general -->

            <!-- ── LOGO ────────────────────────────────────────────────────── -->
            <div class="eb-tab-panel" data-panel="logo">

                <div class="eb-field">
                    <label>Logo Image</label>
                    <?php if ($preset_logo_url) : ?>
                        <div style="margin-bottom: 8px;">
                            <img id="eb-logo-preview" src="<?php echo esc_url($preset_logo_url); ?>" style="max-width: 200px; height: auto; display: block; border: 1px solid #ddd; padding: 4px;">
                        </div>
                    <?php endif; ?>
                    <input type="hidden" name="eb_logo_url" id="eb_logo_url" value="<?php echo esc_attr($preset_logo_url); ?>">
                    <button type="button" class="button" id="eb-logo-upload-btn">
                        <?php echo $preset_logo_url ? 'Replace Logo' : 'Upload Logo'; ?>
                    </button>
                    <button type="button" class="button" id="eb-logo-remove-btn" style="margin-left: 4px;<?php echo $preset_logo_url ? '' : ' display:none;'; ?>">Remove</button>
                    <p class="description">Default logo used across all emails using this preset.</p>
                </div>

                <div class="eb-field">
                    <label for="eb_logo_alt">Logo Alt Text</label>
                    <input type="text" name="eb_logo_alt" id="eb_logo_alt" value="<?php echo esc_attr($preset_logo_alt); ?>" class="widefat" placeholder="<?php echo esc_attr(get_bloginfo('name')); ?>">
                    <p class="description">Alt text for the logo image.</p>
                </div>

                <hr style="margin: 16px 0;">

                <div class="eb-field">
                    <div class="components-toggle-control eb-toggle-control-field">
                        <div class="components-base-control__field">
                            <span class="components-form-toggle <?php echo $dark_logo_enabled ? 'is-checked' : ''; ?>">
                                <input type="checkbox" id="eb_dark_logo_enabled" name="eb_dark_logo_enabled" value="1" class="components-form-toggle__input" <?php checked($dark_logo_enabled, '1'); ?>>
                                <span class="components-form-toggle__track"></span>
                                <span class="components-form-toggle__thumb"></span>
                            </span>
                            <label class="components-toggle-control__label" for="eb_dark_logo_enabled">Enable Dark Mode Logo</label>
                        </div>
                    </div>
                    <p class="description">Enable a separate logo for dark mode emails.</p>
                </div>

                <div id="eb-dark-logo-wrap" style="<?php echo $dark_logo_enabled ? '' : 'display:none;'; ?>">
                    <div class="eb-field">
                        <label>Dark Mode Logo</label>
                        <?php if ($dark_logo_url) : ?>
                            <div style="margin-bottom: 8px; display:inline-block; background-color: black;">
                                <img id="eb-dark-logo-preview" src="<?php echo esc_url($dark_logo_url); ?>" style="max-width: 200px; height: auto; display: block; border: 1px solid #ddd; padding: 4px;">
                            </div>
                        <?php endif; ?>
                        <div>
                            <input type="hidden" name="eb_dark_logo_url" id="eb_dark_logo_url" value="<?php echo esc_attr($dark_logo_url); ?>">
                            <button type="button" class="button" id="eb-dark-logo-upload-btn">
                                <?php echo $dark_logo_url ? 'Replace Dark Logo' : 'Upload Dark Logo'; ?>
                            </button>
                            <button type="button" class="button" id="eb-dark-logo-remove-btn" style="margin-left: 4px;<?php echo $dark_logo_url ? '' : ' display:none;'; ?>">Remove</button>
                        </div>
                        <p class="description">Shown in dark mode where supported.</p>
                    </div>
                </div>

            </div><!-- /logo -->

            <!-- ── TYPOGRAPHY ───────────────────────────────────────────────── -->
            <div class="eb-tab-panel" data-panel="typography">

                <?php $this->render_font_group($post, 'heading', 'Heading', [
                    'font'        => 'Arial',
                    'weight'      => '700',
                    'stack'       => 'Arial, Helvetica, sans-serif',
                    'fallback'    => 'Arial, Helvetica, sans-serif',
                    'size'        => '28',
                    'size_mobile' => '22',
                    'line_height' => '1.3',
                    'placeholder' => 'e.g. Roboto',
                ], $websafe_fonts); ?>

                <hr style="margin: 16px 0;">

                <?php $this->render_font_group($post, 'subheading', 'Subheading', [
                    'font'        => 'Arial',
                    'weight'      => '400',
                    'stack'       => 'Arial, Helvetica, sans-serif',
                    'fallback'    => 'Arial, Helvetica, sans-serif',
                    'size'        => '24',
                    'size_mobile' => '20',
                    'line_height' => '1.3',
                    'placeholder' => 'e.g. Roboto',
                ], $websafe_fonts); ?>

                <hr style="margin: 16px 0;">

                <?php $this->render_font_group($post, 'body', 'Body', [
                    'font'        => 'Helvetica',
                    'weight'      => '400',
                    'stack'       => 'Helvetica, Arial, sans-serif',
                    'fallback'    => 'Helvetica, Arial, sans-serif',
                    'size'        => '18',
                    'size_mobile' => '16',
                    'line_height' => '1.3',
                    'placeholder' => 'e.g. Open Sans',
                ], $websafe_fonts); ?>

                <hr style="margin: 16px 0;">

                <?php $this->render_font_group($post, 'button', 'Button', [
                    'font'        => 'Helvetica',
                    'weight'      => '700',
                    'stack'       => 'Helvetica, Arial, sans-serif',
                    'fallback'    => 'Helvetica, Arial, sans-serif',
                    'size'        => '16',
                    'size_mobile' => '14',
                    'line_height' => '1.2',
                    'placeholder' => 'e.g. Open Sans',
                ], $websafe_fonts); ?>

            </div><!-- /typography -->

            <!-- ── COLOURS ──────────────────────────────────────────────────── -->
            <div class="eb-tab-panel" data-panel="colours">

                <div class="eb-field">
                    <label for="eb_body_bg_color">Body Background</label>
                    <input type="text" name="eb_body_bg_color" id="eb_body_bg_color" value="<?php echo esc_attr($bg_color); ?>" class="widefat eb-color-field" placeholder="#ffffff">
                    <p class="description">Background colour of the email body.</p>
                </div>

                <div class="eb-field">
                    <label>
                        <input type="checkbox" name="eb_body_bg_image_enabled" id="eb_body_bg_image_enabled" value="1" <?php checked($body_bg_image_enabled, '1'); ?>>
                        Use Background Image
                    </label>
                    <p class="description">Overlay a background image on the email body. Background colour above is applied as a fallback.</p>
                </div>

                <div id="eb-body-bg-image-wrap" style="<?php echo $body_bg_image_enabled ? '' : 'display:none;'; ?>">

                    <div style="background:#fff8e1;border-left:3px solid #f0ad00;padding:10px 12px;margin-bottom:12px;border-radius:2px;font-size:12px;line-height:1.5;color:#4a3800;">
                        <strong>Performance notice:</strong> Background images in email can significantly increase load times and may be ignored by some clients (notably Outlook on Windows). For best results, keep the image file size under <strong>100 KB</strong>, use <strong>repeat</strong> tiling with a small seamless pattern where possible, and always ensure the background colour above provides a readable fallback.
                    </div>

                    <div class="eb-field">
                        <label>Background Image</label>
                        <?php if ($body_bg_image_url) : ?>
                            <img id="eb-body-bg-image-preview" src="<?php echo esc_url($body_bg_image_url); ?>" style="max-width:200px;height:auto;display:block;border:1px solid #ddd;padding:4px;margin-bottom:8px;">
                        <?php endif; ?>
                        <input type="hidden" name="eb_body_bg_image_url" id="eb_body_bg_image_url" value="<?php echo esc_attr($body_bg_image_url); ?>">
                        <button type="button" class="button" id="eb-body-bg-image-upload-btn">
                            <?php echo $body_bg_image_url ? 'Replace Image' : 'Upload Image'; ?>
                        </button>
                        <button type="button" class="button" id="eb-body-bg-image-remove-btn" style="margin-left:4px;<?php echo $body_bg_image_url ? '' : 'display:none;'; ?>">Remove</button>
                    </div>

                    <?php
                    // Helper: render an inline radio button group
                    function eb_radio_group($name, $current, $options) {
                        echo '<div style="display:flex;flex-wrap:wrap;gap:4px;">';
                        foreach ($options as $val => $lbl) {
                            $active = ($current === $val);
                            $style  = $active
                                ? 'padding:4px 10px;border-radius:3px;border:1px solid #2271b1;background:#2271b1;color:#fff;font-size:12px;cursor:pointer;'
                                : 'padding:4px 10px;border-radius:3px;border:1px solid #ddd;background:#f6f7f7;color:#1e1e1e;font-size:12px;cursor:pointer;';
                            printf(
                                '<label style="%s"><input type="radio" name="%s" value="%s"%s style="position:absolute;opacity:0;width:0;height:0;"> %s</label>',
                                esc_attr($style),
                                esc_attr($name),
                                esc_attr($val),
                                $active ? ' checked' : '',
                                esc_html($lbl)
                            );
                        }
                        echo '</div>';
                    }
                    ?>

                    <div class="eb-field">
                        <label>Background Repeat</label>
                        <?php eb_radio_group('eb_body_bg_image_repeat', $body_bg_image_repeat, [
                            'no-repeat' => 'No Repeat',
                            'repeat'    => 'Repeat',
                            'repeat-x'  => 'Repeat X',
                            'repeat-y'  => 'Repeat Y',
                        ]); ?>
                    </div>

                    <div class="eb-field">
                        <label>Horizontal Position</label>
                        <?php eb_radio_group('eb_body_bg_image_position_x', $body_bg_image_position_x, [
                            'left'   => 'Left',
                            'center' => 'Center',
                            'right'  => 'Right',
                        ]); ?>
                    </div>

                    <div class="eb-field">
                        <label>Vertical Position</label>
                        <?php eb_radio_group('eb_body_bg_image_position_y', $body_bg_image_position_y, [
                            'top'    => 'Top',
                            'center' => 'Center',
                            'bottom' => 'Bottom',
                        ]); ?>
                    </div>

                    <div class="eb-field">
                        <label>Background Width</label>
                        <?php eb_radio_group('eb_body_bg_image_size_w', $body_bg_image_size_w, [
                            'cover'   => 'Cover',
                            'contain' => 'Contain',
                            'auto'    => 'Auto',
                            '100%'    => '100%',
                        ]); ?>
                    </div>

                </div><!-- /eb-body-bg-image-wrap -->

                <div class="eb-field">
                    <label for="eb_text_color_dark">Dark Text</label>
                    <input type="text" name="eb_text_color_dark" id="eb_text_color_dark" value="<?php echo esc_attr($text_color_dark); ?>" class="widefat eb-color-field" placeholder="#000000">
                    <p class="description">Used on light backgrounds.</p>
                </div>

                <div class="eb-field">
                    <label for="eb_text_color_light">Light Text</label>
                    <input type="text" name="eb_text_color_light" id="eb_text_color_light" value="<?php echo esc_attr($text_color_light); ?>" class="widefat eb-color-field" placeholder="#ffffff">
                    <p class="description">Used on dark backgrounds.</p>
                </div>

                <div class="eb-field">
                    <label for="eb_button_color">Button Background</label>
                    <input type="text" name="eb_button_color" id="eb_button_color" value="<?php echo esc_attr($button_color); ?>" class="widefat eb-color-field" placeholder="#000000">
                    <p class="description">Default background colour for all buttons. Can be overridden per button block.</p>
                </div>

                <div class="eb-field">
                    <label for="eb_button_text_color">Button Text</label>
                    <input type="text" name="eb_button_text_color" id="eb_button_text_color" value="<?php echo esc_attr($button_text_color); ?>" class="widefat eb-color-field" placeholder="#ffffff">
                    <p class="description">Default text colour for all buttons. Leave blank to use automatic contrast colour.</p>
                </div>

                <div class="eb-field">
                    <label for="eb_sale_price_color">Sale Price</label>
                    <input type="text" name="eb_sale_price_color" id="eb_sale_price_color" value="<?php echo esc_attr($sale_price_color); ?>" class="widefat eb-color-field" placeholder="#c0392b">
                    <p class="description">Colour used for sale prices in product blocks.</p>
                </div>

            </div><!-- /colours -->

            <!-- ── DARK MODE ─────────────────────────────────────────────────── -->
            <div class="eb-tab-panel" data-panel="dark-mode">

                <p class="description" style="margin-bottom: 16px;">
                    Applied when the recipient's device is in dark mode. Supported in Apple Mail and iOS Mail.
                </p>

                <div class="eb-field">
                    <label for="eb_dark_bg_color">Background</label>
                    <div style="display: flex; gap: 8px; margin-bottom: 8px; flex-wrap: wrap;">
                        <?php foreach ($dark_swatches as $hex => $label) : ?>
                            <button type="button" class="eb-dark-swatch-preset" data-color="<?php echo esc_attr($hex); ?>" data-target="eb_dark_bg_color" title="<?php echo esc_attr($label); ?>" style="background-color:<?php echo esc_attr($hex); ?>;width:32px;height:32px;border:2px solid <?php echo $dark_bg_color === $hex ? '#2271b1' : '#ccc'; ?>;border-radius:4px;cursor:pointer;padding:0;"></button>
                        <?php endforeach; ?>
                    </div>
                    <p class="description">
                        <?php foreach ($dark_swatches as $hex => $label) : ?>
                            <span style="margin-right: 10px; font-size: 11px;"><?php echo esc_html($hex); ?> — <?php echo esc_html($label); ?></span>
                        <?php endforeach; ?>
                    </p>
                    <div style="margin-top: 10px;">
                    <input type="text" name="eb_dark_bg_color" id="eb_dark_bg_color" value="<?php echo esc_attr($dark_bg_color); ?>" class="widefat eb-color-field" placeholder="#121212">
                    </div>
                    <p class="description">Background colour in dark mode.</p>
                </div>

                <div class="eb-field">
                    <label for="eb_dark_text_color">Text</label>
                    <input type="text" name="eb_dark_text_color" id="eb_dark_text_color" value="<?php echo esc_attr($dark_text_color); ?>" class="widefat eb-color-field" placeholder="#ffffff">
                    <p class="description">All text colour in dark mode.</p>
                </div>

                <div class="eb-field">
                    <label for="eb_dark_button_color">Button Background</label>
                    <input type="text" name="eb_dark_button_color" id="eb_dark_button_color" value="<?php echo esc_attr($dark_button_color); ?>" class="widefat eb-color-field" placeholder="#ffffff">
                    <p class="description">Button background colour in dark mode.</p>
                </div>

                <div class="eb-field">
                    <label for="eb_dark_button_text_color">Button Text</label>
                    <input type="text" name="eb_dark_button_text_color" id="eb_dark_button_text_color" value="<?php echo esc_attr($dark_button_text_color); ?>" class="widefat eb-color-field" placeholder="#000000">
                    <p class="description">Button text colour in dark mode.</p>
                </div>

                <div class="eb-field">
                    <label for="eb_dark_link_color">Link Colour</label>
                    <input type="text" name="eb_dark_link_color" id="eb_dark_link_color" value="<?php echo esc_attr($dark_link_color); ?>" class="widefat eb-color-field" placeholder="#ffffff">
                    <p class="description">Link colour in dark mode.</p>
                </div>

            </div><!-- /dark-mode -->

        </div><!-- /.eb-preset-tabs -->

        <?php if (eb_is_pro()) : ?>
        <div style="margin-top:16px; border-top:1px solid #ddd; padding-top:12px; display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
            <button type="button" id="eb-export-preset-btn" class="button" data-post-id="<?php echo esc_attr($post->ID); ?>">Export Preset</button>
            <button type="button" id="eb-import-preset-btn" class="button">Import Preset</button>
            <input type="file" id="eb-import-preset-file" accept=".json" style="display:none;">
            <span id="eb-import-status" style="color:#2271b1; font-size:12px;"></span>
        </div>
        <p class="description" style="margin-top:6px; font-size:11px;">Import creates a new preset — it will not overwrite this one. Logo URLs may need updating after import to another site.</p>
        <?php else : ?>
        <div class="eb-pro-notice" style="margin-top:16px;">
            <span>Preset import &amp; export require <strong>WP Mailblox Pro</strong>.</span>
            <a href="<?php echo esc_url( wp_mailblox_fs()->get_upgrade_url() ); ?>" class="eb-upgrade-pill">Upgrade →</a>
        </div>
        <?php endif; ?>

    <?php
    }

    private function render_font_group($post, $prefix, $label, $defaults, $fonts)
    {
        $type     = get_post_meta($post->ID, "eb_{$prefix}_font_type", true) ?: 'websafe';
        $stack    = get_post_meta($post->ID, "eb_{$prefix}_font_stack", true) ?: $defaults['stack'];
        $font     = get_post_meta($post->ID, "eb_{$prefix}_font", true) ?: $defaults['font'];
        $fallback = get_post_meta($post->ID, "eb_{$prefix}_font_fallback", true) ?: $defaults['fallback'];
        $weight   = get_post_meta($post->ID, "eb_{$prefix}_font_weight", true) ?: $defaults['weight'];
        $size     = get_post_meta($post->ID, "eb_{$prefix}_size", true) ?: $defaults['size'];
        $mobile   = get_post_meta($post->ID, "eb_{$prefix}_size_mobile", true) ?: $defaults['size_mobile'];
        $line     = get_post_meta($post->ID, "eb_{$prefix}_line_height", true) ?: $defaults['line_height'];
    ?>
        <h4 style="margin-top:0;"><?php echo esc_html($label); ?></h4>

        <div class="eb-field">
            <label>Font Type</label>
            <input type="hidden" name="eb_<?php echo $prefix; ?>_font_type" id="eb_<?php echo esc_attr($prefix); ?>_font_type" value="<?php echo esc_attr($type); ?>">
            <div class="eb-toggle-group" data-target="eb_<?php echo esc_attr($prefix); ?>_font_type">
                <button type="button" class="eb-toggle-option<?php echo $type === 'websafe' ? ' active' : ''; ?>" data-value="websafe">Web Safe</button>
                <button type="button" class="eb-toggle-option<?php echo $type === 'google' ? ' active' : ''; ?>" data-value="google">Google Font</button>
            </div>
        </div>

        <div class="eb-field eb-font-websafe eb-font-<?php echo $prefix; ?>" <?php echo $type === 'google' ? 'style="display:none;"' : ''; ?>>
            <label>Font Family</label>
            <select name="eb_<?php echo $prefix; ?>_font_websafe" class="widefat">
                <?php foreach ($fonts as $f) : ?>
                    <option value="<?php echo esc_attr($f['stack']); ?>" <?php selected($stack, $f['stack']); ?>><?php echo esc_html($f['label']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="eb-field eb-font-google eb-font-<?php echo $prefix; ?>" <?php echo $type === 'websafe' ? 'style="display:none;"' : ''; ?>>
            <label>Google Font Name</label>
            <input type="text" name="eb_<?php echo $prefix; ?>_font_google" value="<?php echo $type === 'google' ? esc_attr($font) : ''; ?>" class="widefat" placeholder="<?php echo esc_attr($defaults['placeholder']); ?>">
            <p class="description"><a href="https://fonts.google.com" target="_blank">Browse Google Fonts ↗</a></p>
            <p class="description" style="color: #d63638;">⚠ Supported in Apple Mail / iOS Mail only. Other clients use the fallback.</p>
        </div>

        <div class="eb-field eb-font-google eb-font-<?php echo $prefix; ?>" <?php echo $type === 'websafe' ? 'style="display:none;"' : ''; ?>>
            <label>Fallback Font</label>
            <select name="eb_<?php echo $prefix; ?>_font_fallback" class="widefat">
                <?php foreach ($fonts as $f) : ?>
                    <option value="<?php echo esc_attr($f['stack']); ?>" <?php selected($fallback, $f['stack']); ?>><?php echo esc_html($f['label']); ?></option>
                <?php endforeach; ?>
            </select>
            <p class="description">Used in clients that don't support Google Fonts.</p>
        </div>

        <div class="eb-field">
            <label>Font Weight</label>
            <input type="text" name="eb_<?php echo $prefix; ?>_font_weight" value="<?php echo esc_attr($weight); ?>" class="widefat">
        </div>

        <div class="eb-field">
            <label>Desktop Font Size (px)</label>
            <input type="number" name="eb_<?php echo $prefix; ?>_size" value="<?php echo esc_attr($size); ?>" class="widefat">
        </div>

        <div class="eb-field">
            <label>Mobile Font Size (px)</label>
            <input type="number" name="eb_<?php echo $prefix; ?>_size_mobile" value="<?php echo esc_attr($mobile); ?>" class="widefat">
        </div>

        <div class="eb-field">
            <label>Line Height</label>
            <input type="number" step="any" name="eb_<?php echo $prefix; ?>_line_height" value="<?php echo esc_attr($line); ?>" class="widefat">
        </div>
    <?php
    }

    public function ajax_export_template()
    {
        $post_id = intval($_POST['post_id'] ?? 0);
        $nonce   = sanitize_text_field($_POST['nonce'] ?? '');

        if (!$post_id || !wp_verify_nonce($nonce, 'eb_export_template_' . $post_id) || !current_user_can('edit_post', $post_id)) {
            wp_send_json_error('Permission denied.');
        }

        $post   = get_post($post_id);
        if (!$post || $post->post_type !== 'eb_email_template') {
            wp_send_json_error('Invalid template.');
        }

        $blocks = parse_blocks($post->post_content);

        // Normalise parse_blocks() output to the name/attributes/innerBlocks shape
        // used by starter templates and expected by the JS importer.
        function eb_normalise_blocks( $blocks ) {
            $out = [];
            foreach ( $blocks as $b ) {
                if ( empty( $b['blockName'] ) ) continue;
                $out[] = [
                    'name'        => $b['blockName'],
                    'attributes'  => $b['attrs'] ?? [],
                    'innerBlocks' => eb_normalise_blocks( $b['innerBlocks'] ?? [] ),
                ];
            }
            return $out;
        }

        wp_send_json_success([
            'title'  => $post->post_title,
            'blocks' => eb_normalise_blocks( $blocks ),
        ]);
    }

    public function set_default_preset($post_id, $post)
    {
        // Only on initial auto-draft creation, before the user has saved anything
        if ($post->post_status !== 'auto-draft') return;
        if (get_post_meta($post_id, 'eb_preset', true)) return;

        $first_preset = get_posts([
            'post_type'      => 'eb_preset',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ]);

        if (!empty($first_preset)) {
            update_post_meta($post_id, 'eb_preset', $first_preset[0]->ID);
        }
    }

    public function save_meta($post_id)
    {
        if (
            !isset($_POST['post_type']) ||
            !in_array($_POST['post_type'], ['eb_email_template', 'eb_preset'], true)
        ) {
            return;
        }

        if (
            !isset($_POST['eb_email_meta_nonce']) ||
            !wp_verify_nonce($_POST['eb_email_meta_nonce'], 'eb_save_email_meta')
        ) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;

        if (isset($_POST['eb_preset'])) {
            update_post_meta($post_id, 'eb_preset', intval($_POST['eb_preset']));
        }

        if (isset($_POST['eb_platform'])) {
            update_post_meta($post_id, 'eb_platform', sanitize_text_field($_POST['eb_platform']));
        }

        foreach (['eb_utm_source', 'eb_utm_medium', 'eb_utm_campaign', 'eb_utm_content', 'eb_utm_term'] as $key) {
            if (isset($_POST[$key])) {
                update_post_meta($post_id, $key, sanitize_text_field($_POST[$key]));
            }
        }

        if (isset($_POST['eb_logo_url'])) {
            update_post_meta($post_id, 'eb_logo_url', esc_url_raw($_POST['eb_logo_url']));
        }
        if (isset($_POST['eb_logo_alt'])) {
            update_post_meta($post_id, 'eb_logo_alt', sanitize_text_field($_POST['eb_logo_alt']));
        }

        // Dark logo toggle
        update_post_meta(
            $post_id,
            'eb_dark_logo_enabled',
            isset($_POST['eb_dark_logo_enabled']) ? '1' : ''
        );

        // Dark logo URL
        if (isset($_POST['eb_dark_logo_url'])) {
            update_post_meta(
                $post_id,
                'eb_dark_logo_url',
                esc_url_raw($_POST['eb_dark_logo_url'])
            );
        }

        if (isset($_POST['eb_container_width'])) {
            $width = intval($_POST['eb_container_width']);
            $width = max(500, min(800, $width)); // clamp to valid range
            update_post_meta($post_id, 'eb_container_width', $width);
        }

        if (isset($_POST['eb_heading_font_type'])) {
            $type = sanitize_text_field($_POST['eb_heading_font_type']);
            update_post_meta($post_id, 'eb_heading_font_type', $type);

            if ($type === 'websafe' && isset($_POST['eb_heading_font_websafe'])) {
                $stack = sanitize_text_field($_POST['eb_heading_font_websafe']);
                update_post_meta($post_id, 'eb_heading_font_stack', $stack);
                update_post_meta($post_id, 'eb_heading_font', explode(',', $stack)[0]);
            } elseif ($type === 'google' && isset($_POST['eb_heading_font_google'])) {
                $google_font = sanitize_text_field($_POST['eb_heading_font_google']);
                $fallback    = sanitize_text_field($_POST['eb_heading_font_fallback'] ?? 'Arial, Helvetica, sans-serif');
                update_post_meta($post_id, 'eb_heading_font',          $google_font);
                update_post_meta($post_id, 'eb_heading_font_fallback', $fallback);
                update_post_meta($post_id, 'eb_heading_font_stack',    "'{$google_font}', {$fallback}");
            }
        }

        if (isset($_POST['eb_heading_font_weight'])) {
            update_post_meta($post_id, 'eb_heading_font_weight', sanitize_text_field($_POST['eb_heading_font_weight']));
        }
        if (isset($_POST['eb_heading_size'])) {
            update_post_meta($post_id, 'eb_heading_size', intval($_POST['eb_heading_size']));
        }
        if (isset($_POST['eb_heading_size_mobile'])) {
            update_post_meta($post_id, 'eb_heading_size_mobile', intval($_POST['eb_heading_size_mobile']));
        }
        if (isset($_POST['eb_heading_line_height'])) {
            update_post_meta($post_id, 'eb_heading_line_height', floatval($_POST['eb_heading_line_height']));
        }

        if (isset($_POST['eb_subheading_font_type'])) {
            $type = sanitize_text_field($_POST['eb_subheading_font_type']);
            update_post_meta($post_id, 'eb_subheading_font_type', $type);

            if ($type === 'websafe' && isset($_POST['eb_subheading_font_websafe'])) {
                $stack = sanitize_text_field($_POST['eb_subheading_font_websafe']);
                update_post_meta($post_id, 'eb_subheading_font_stack', $stack);
                update_post_meta($post_id, 'eb_subheading_font', explode(',', $stack)[0]);
            } elseif ($type === 'google' && isset($_POST['eb_subheading_font_google'])) {
                $google_font = sanitize_text_field($_POST['eb_subheading_font_google']);
                $fallback    = sanitize_text_field($_POST['eb_subheading_font_fallback'] ?? 'Arial, Helvetica, sans-serif');
                update_post_meta($post_id, 'eb_subheading_font',          $google_font);
                update_post_meta($post_id, 'eb_subheading_font_fallback', $fallback);
                update_post_meta($post_id, 'eb_subheading_font_stack',    "'{$google_font}', {$fallback}");
            }
        }

        if (isset($_POST['eb_subheading_font_weight'])) {
            update_post_meta($post_id, 'eb_subheading_font_weight', sanitize_text_field($_POST['eb_subheading_font_weight']));
        }
        if (isset($_POST['eb_subheading_size'])) {
            update_post_meta($post_id, 'eb_subheading_size', intval($_POST['eb_subheading_size']));
        }
        if (isset($_POST['eb_subheading_size_mobile'])) {
            update_post_meta($post_id, 'eb_subheading_size_mobile', intval($_POST['eb_subheading_size_mobile']));
        }
        if (isset($_POST['eb_subheading_line_height'])) {
            update_post_meta($post_id, 'eb_subheading_line_height', floatval($_POST['eb_subheading_line_height']));
        }

        if (isset($_POST['eb_body_font_type'])) {
            $type = sanitize_text_field($_POST['eb_body_font_type']);
            update_post_meta($post_id, 'eb_body_font_type', $type);

            if ($type === 'websafe' && isset($_POST['eb_body_font_websafe'])) {
                $stack = sanitize_text_field($_POST['eb_body_font_websafe']);
                update_post_meta($post_id, 'eb_body_font_stack', $stack);
                update_post_meta($post_id, 'eb_body_font', explode(',', $stack)[0]);
            } elseif ($type === 'google' && isset($_POST['eb_body_font_google'])) {
                $google_font = sanitize_text_field($_POST['eb_body_font_google']);
                $fallback    = sanitize_text_field($_POST['eb_body_font_fallback'] ?? 'Helvetica, Arial, sans-serif');
                update_post_meta($post_id, 'eb_body_font',          $google_font);
                update_post_meta($post_id, 'eb_body_font_fallback', $fallback);
                update_post_meta($post_id, 'eb_body_font_stack',    "'{$google_font}', {$fallback}");
            }
        }

        if (isset($_POST['eb_body_font_weight'])) {
            update_post_meta($post_id, 'eb_body_font_weight', sanitize_text_field($_POST['eb_body_font_weight']));
        }
        if (isset($_POST['eb_body_size'])) {
            update_post_meta($post_id, 'eb_body_size', intval($_POST['eb_body_size']));
        }
        if (isset($_POST['eb_body_size_mobile'])) {
            update_post_meta($post_id, 'eb_body_size_mobile', intval($_POST['eb_body_size_mobile']));
        }
        if (isset($_POST['eb_body_line_height'])) {
            update_post_meta($post_id, 'eb_body_line_height', floatval($_POST['eb_body_line_height']));
        }

        if (isset($_POST['eb_button_font_type'])) {
            $type = sanitize_text_field($_POST['eb_button_font_type']);
            update_post_meta($post_id, 'eb_button_font_type', $type);

            if ($type === 'websafe' && isset($_POST['eb_button_font_websafe'])) {
                $stack = sanitize_text_field($_POST['eb_button_font_websafe']);
                update_post_meta($post_id, 'eb_button_font_stack', $stack);
                update_post_meta($post_id, 'eb_button_font',       explode(',', $stack)[0]);
            } elseif ($type === 'google' && isset($_POST['eb_button_font_google'])) {
                $google_font = sanitize_text_field($_POST['eb_button_font_google']);
                $fallback    = sanitize_text_field($_POST['eb_button_font_fallback'] ?? 'Helvetica, Arial, sans-serif');
                update_post_meta($post_id, 'eb_button_font',          $google_font);
                update_post_meta($post_id, 'eb_button_font_fallback', $fallback);
                update_post_meta($post_id, 'eb_button_font_stack',    "'{$google_font}', {$fallback}");
            }
        }

        if (isset($_POST['eb_button_font_weight'])) {
            update_post_meta($post_id, 'eb_button_font_weight', sanitize_text_field($_POST['eb_button_font_weight']));
        }
        if (isset($_POST['eb_button_size'])) {
            update_post_meta($post_id, 'eb_button_size', intval($_POST['eb_button_size']));
        }
        if (isset($_POST['eb_button_size_mobile'])) {
            update_post_meta($post_id, 'eb_button_size_mobile', intval($_POST['eb_button_size_mobile']));
        }
        if (isset($_POST['eb_button_line_height'])) {
            update_post_meta($post_id, 'eb_button_line_height', floatval($_POST['eb_button_line_height']));
        }

        if (isset($_POST['eb_body_bg_color'])) {
            update_post_meta($post_id, 'eb_body_bg_color', sanitize_hex_color($_POST['eb_body_bg_color']));
        }
        update_post_meta($post_id, 'eb_body_bg_image_enabled', isset($_POST['eb_body_bg_image_enabled']) ? '1' : '');
        if (isset($_POST['eb_body_bg_image_url'])) {
            update_post_meta($post_id, 'eb_body_bg_image_url', esc_url_raw($_POST['eb_body_bg_image_url']));
        }
        if (isset($_POST['eb_body_bg_image_repeat'])) {
            $allowed_repeat = ['no-repeat', 'repeat', 'repeat-x', 'repeat-y'];
            $repeat = in_array($_POST['eb_body_bg_image_repeat'], $allowed_repeat, true) ? $_POST['eb_body_bg_image_repeat'] : 'no-repeat';
            update_post_meta($post_id, 'eb_body_bg_image_repeat', $repeat);
        }
        if (isset($_POST['eb_body_bg_image_position_x'])) {
            $allowed_pos_x = ['left', 'center', 'right'];
            $pos_x = in_array($_POST['eb_body_bg_image_position_x'], $allowed_pos_x, true) ? $_POST['eb_body_bg_image_position_x'] : 'center';
            update_post_meta($post_id, 'eb_body_bg_image_position_x', $pos_x);
        }
        if (isset($_POST['eb_body_bg_image_position_y'])) {
            $allowed_pos_y = ['top', 'center', 'bottom'];
            $pos_y = in_array($_POST['eb_body_bg_image_position_y'], $allowed_pos_y, true) ? $_POST['eb_body_bg_image_position_y'] : 'center';
            update_post_meta($post_id, 'eb_body_bg_image_position_y', $pos_y);
        }
        if (isset($_POST['eb_body_bg_image_size_w'])) {
            $allowed_size_w = ['cover', 'contain', 'auto', '100%'];
            $size_w = in_array($_POST['eb_body_bg_image_size_w'], $allowed_size_w, true) ? $_POST['eb_body_bg_image_size_w'] : 'cover';
            update_post_meta($post_id, 'eb_body_bg_image_size_w', $size_w);
        }
        if (isset($_POST['eb_text_color_dark'])) {
            update_post_meta($post_id, 'eb_text_color_dark', sanitize_hex_color($_POST['eb_text_color_dark']));
        }
        if (isset($_POST['eb_text_color_light'])) {
            update_post_meta($post_id, 'eb_text_color_light', sanitize_hex_color($_POST['eb_text_color_light']));
        }
        if (isset($_POST['eb_button_color'])) {
            update_post_meta($post_id, 'eb_button_color', sanitize_hex_color($_POST['eb_button_color']));
        }
        if (isset($_POST['eb_button_text_color'])) {
            update_post_meta($post_id, 'eb_button_text_color', sanitize_hex_color($_POST['eb_button_text_color']));
        }
        if (isset($_POST['eb_sale_price_color'])) {
            update_post_meta($post_id, 'eb_sale_price_color', sanitize_hex_color($_POST['eb_sale_price_color']));
        }

        if (isset($_POST['eb_dark_bg_color'])) {
            update_post_meta($post_id, 'eb_dark_bg_color', sanitize_hex_color($_POST['eb_dark_bg_color']));
        }
        if (isset($_POST['eb_dark_text_color'])) {
            update_post_meta($post_id, 'eb_dark_text_color', sanitize_hex_color($_POST['eb_dark_text_color']));
        }
        if (isset($_POST['eb_dark_button_color'])) {
            update_post_meta($post_id, 'eb_dark_button_color', sanitize_hex_color($_POST['eb_dark_button_color']));
        }
        if (isset($_POST['eb_dark_button_text_color'])) {
            update_post_meta($post_id, 'eb_dark_button_text_color', sanitize_hex_color($_POST['eb_dark_button_text_color']));
        }
        if (isset($_POST['eb_dark_link_color'])) {
            update_post_meta($post_id, 'eb_dark_link_color', sanitize_hex_color($_POST['eb_dark_link_color']));
        }

        if (isset($_POST['eb_dark_bg_color_override'])) {
            update_post_meta($post_id, 'eb_dark_bg_color_override', sanitize_hex_color($_POST['eb_dark_bg_color_override']));
        }

        if (isset($_POST['eb_subject'])) {
            update_post_meta($post_id, 'eb_subject', sanitize_text_field($_POST['eb_subject']));
        }

        if (isset($_POST['eb_preheader'])) {
            update_post_meta($post_id, 'eb_preheader', sanitize_text_field($_POST['eb_preheader']));
        }
    }

    public function export_email($post_id, $platform_override = null)
    {
        $post = get_post($post_id);
        if (!$post || $post->post_type !== 'eb_email_template') return '';

        ob_start();
        try {
            include EB_PLUGIN_PATH . 'templates/default.php';
        } catch (\Throwable $e) {
            ob_end_clean();
            error_log('WP Mailblox: export_email() failed for post ' . $post_id . ': ' . $e->getMessage());
            return '';
        }
        return ob_get_clean();
    }

    private function find_block($blocks, $name)
    {
        foreach ($blocks as $block) {
            if (($block['blockName'] ?? '') === $name) return $block;
            if (!empty($block['innerBlocks'])) {
                $found = $this->find_block($block['innerBlocks'], $name);
                if ($found) return $found;
            }
        }
        return null;
    }

    private function get_export_warnings($post_id)
    {
        $warnings     = [];
        $blocks       = parse_blocks(get_post_field('post_content', $post_id));
        $footer_block = $this->find_block($blocks, 'email-builder/footer');

        if (!$footer_block) {
            $warnings[] = 'No footer block found. Ensure your email platform automatically adds a physical address and unsubscribe link.';
        } else {
            $attrs = $footer_block['attrs'] ?? [];
            if (empty($attrs['address'])) {
                $warnings[] = 'Footer address is empty — a physical address is required by CAN-SPAM.';
            }
            if (isset($attrs['showUnsubscribe']) && $attrs['showUnsubscribe'] === false) {
                $warnings[] = 'Unsubscribe link is disabled in the footer. Most platforms require one.';
            }
        }

        $subject = get_post_meta($post_id, 'eb_subject', true);
        if (empty($subject)) {
            $warnings[] = 'No subject line set. The subject populates the email &lt;title&gt; tag in the exported HTML.';
        }

        $preheader = get_post_meta($post_id, 'eb_preheader', true);
        if (empty($preheader)) {
            $warnings[] = 'No preheader text set. Preheader text appears in inbox previews and improves open rates.';
        }

        return $warnings;
    }

    public function ajax_share_token()
    {
        $post_id = intval($_POST['post_id'] ?? 0);
        $action  = sanitize_key($_POST['share_action'] ?? '');

        if (
            !$post_id ||
            !isset($_POST['nonce']) ||
            !wp_verify_nonce($_POST['nonce'], 'eb_share_token_' . $post_id) ||
            !current_user_can('edit_post', $post_id)
        ) {
            wp_send_json_error(['message' => 'Permission denied.']);
        }

        if ($action === 'generate') {
            $token = wp_generate_password(32, false);
            update_post_meta($post_id, 'eb_share_token', $token);
            $url = add_query_arg(['eb_share' => $post_id, 'token' => $token], home_url('/'));
            wp_send_json_success(['url' => $url]);

        } elseif ($action === 'revoke') {
            delete_post_meta($post_id, 'eb_share_token');
            wp_send_json_success(['url' => '']);

        } else {
            wp_send_json_error(['message' => 'Unknown action.']);
        }
    }

    public function ajax_export_email()
    {
        if (
            !isset($_POST['post_id'], $_POST['nonce']) ||
            !wp_verify_nonce($_POST['nonce'], 'eb_export_email_nonce') ||
            !current_user_can('edit_post', $_POST['post_id'])
        ) {
            wp_send_json_error(['html' => 'Permission denied']);
        }

        $post_id  = intval($_POST['post_id']);
        $html     = $this->export_email($post_id);
        $warnings = $this->get_export_warnings($post_id);

        wp_send_json_success(['html' => $html, 'warnings' => $warnings]);
    }

    public function ajax_push_to_platform()
    {
        if (
            !isset($_POST['post_id'], $_POST['nonce']) ||
            !wp_verify_nonce($_POST['nonce'], 'eb_push_to_platform_nonce') ||
            !current_user_can('edit_post', intval($_POST['post_id']))
        ) {
            wp_send_json_error(['message' => 'Permission denied.']);
        }

        if (!eb_is_pro()) {
            wp_send_json_error(['message' => 'Direct platform push requires WP Mailblox Pro.']);
        }

        $post_id = intval($_POST['post_id']);
        $api     = new EB_Platform_API();
        $result  = $api->push($post_id);

        if ($result['success']) {
            wp_send_json_success($result);
        } else {
            wp_send_json_error($result);
        }
    }

    public function maybe_show_woocommerce_notice()
    {
        $screen = get_current_screen();
        if (!$screen || $screen->post_type !== 'eb_email_template') return;
        if (!eb_is_pro() || class_exists('WooCommerce')) return;
        echo '<div class="notice notice-warning"><p><strong>WP Mailblox:</strong> WooCommerce blocks require WooCommerce to be installed and active.</p></div>';
    }

    public function maybe_show_no_preset_notice()
    {
        $screen = get_current_screen();
        if (!$screen || $screen->post_type !== 'eb_email_template') return;

        $has_presets = get_posts([
            'post_type'      => 'eb_preset',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'fields'         => 'ids',
        ]);

        if (!empty($has_presets)) return;

        $new_preset_url = admin_url('post-new.php?post_type=eb_preset');
        ?>
        <div class="notice notice-warning" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
            <p style="margin:0;">
                <strong>No presets found.</strong>
                You need at least one preset before you can create or use email templates.
            </p>
            <a href="<?php echo esc_url($new_preset_url); ?>" class="button button-primary">
                Create your first preset →
            </a>
        </div>
        <style>
            .post-type-eb_email_template .page-title-action { display: none; }
        </style>
        <?php
    }

    /**
     * Enforce free-plan post limits on save.
     * Hooks into save_post — catches normal saves AND duplicate-post plugins.
     */
    public function enforce_post_limit($post_id, $post, $update)
    {
        if ($update) return;
        if ($post->post_status === 'auto-draft') return;

        $type = $post->post_type;
        if (!in_array($type, ['eb_email_template', 'eb_preset', 'eb_saved_template'], true)) return;

        $limit = eb_get_post_limit($type);
        if ($limit === PHP_INT_MAX) return;

        if (eb_count_active_posts($type) > $limit) {
            wp_trash_post($post_id);

            $label = $type === 'eb_email_template' ? 'email templates' : 'presets';
            $msg   = sprintf(
                'Free plan limit reached: you can have up to %d %s. <a href="%s">Upgrade to Pro</a> for unlimited.',
                $limit,
                $label,
                esc_url(admin_url('admin.php?page=eb_settings'))
            );
            set_transient('eb_limit_notice_' . get_current_user_id(), $msg, 60);
        }
    }

    /**
     * Block trashing of a preset that is still in use by email templates.
     */
    public function guard_preset_trash($post_id)
    {
        $post = get_post($post_id);
        if (!$post || $post->post_type !== 'eb_preset') return;

        $emails = get_posts([
            'post_type'      => 'eb_email_template',
            'posts_per_page' => -1,
            'post_status'    => 'any',
            'fields'         => 'ids',
            'meta_query'     => [[
                'key'   => 'eb_preset',
                'value' => $post_id,
            ]],
        ]);

        if (empty($emails)) return;

        // Post is already in trash at this point — reverse it immediately
        wp_untrash_post($post_id);

        set_transient('eb_preset_delete_blocked_' . get_current_user_id(), [
            'preset_name' => $post->post_title,
            'count'       => count($emails),
        ], 60);

        // Redirect before WordPress can send its own "trashed=1" redirect
        wp_safe_redirect(admin_url('edit.php?post_type=eb_preset'));
        exit;
    }

    /**
     * Block force-deletion of a preset that is still in use by email templates.
     */
    public function guard_preset_deletion($delete, $post, $_force_delete)
    {
        if (!$post || $post->post_type !== 'eb_preset') return $delete;

        $emails = get_posts([
            'post_type'      => 'eb_email_template',
            'posts_per_page' => -1,
            'post_status'    => 'any',
            'fields'         => 'ids',
            'meta_query'     => [[
                'key'   => 'eb_preset',
                'value' => $post->ID,
            ]],
        ]);

        if (empty($emails)) return $delete;

        set_transient('eb_preset_delete_blocked_' . get_current_user_id(), [
            'preset_name' => $post->post_title,
            'count'       => count($emails),
        ], 60);

        return false; // Cancel deletion
    }

    /**
     * Show notice when preset deletion was blocked due to dependent emails.
     */
    public function maybe_show_preset_deletion_notice()
    {
        $key  = 'eb_preset_delete_blocked_' . get_current_user_id();
        $data = get_transient($key);
        if (!$data) return;
        delete_transient($key);

        $count = intval($data['count']);
        $name  = esc_html($data['preset_name']);
        echo '<div class="notice notice-error is-dismissible"><p><strong>WP Mailblox:</strong> The preset &ldquo;' . $name . '&rdquo; could not be deleted — it is used by ' . $count . ' email' . ($count !== 1 ? 's' : '') . '. Reassign those emails to a different preset before deleting.</p></div>';
    }

    /**
     * Show limit-related admin notices (both from save_post and from the Add New redirect).
     */
    public function maybe_show_limit_notice()
    {
        $key    = 'eb_limit_notice_' . get_current_user_id();
        $notice = get_transient($key);

        if ($notice) {
            delete_transient($key);
            ?>
            <div class="notice notice-error is-dismissible">
                <p><?php echo wp_kses($notice, ['a' => ['href' => []]]); ?></p>
            </div>
            <?php
        }

        if (!empty($_GET['eb_limit_reached'])) {
            $type  = sanitize_key($_GET['eb_limit_reached']);
            $limit = eb_get_post_limit($type);
            $label = $type === 'eb_email_template' ? 'email templates' : 'presets';
            ?>
            <div class="notice notice-error is-dismissible">
                <p>
                    <strong>Free plan limit reached:</strong> you can have up to <?php echo esc_html($limit); ?> <?php echo esc_html($label); ?> on the free plan.
                    <a href="<?php echo esc_url(admin_url('admin.php?page=eb_settings')); ?>">Upgrade to Pro</a> for unlimited.
                </p>
            </div>
            <?php
        }
    }

    public function register_menu()
    {
        add_menu_page(
            'WP Mailblox Settings',
            'WP Mailblox',
            'edit_posts',
            'eb_settings',
            [$this, 'render_settings_page'],
            'dashicons-email',
            25
        );

        add_submenu_page(
            'eb_settings',
            'Settings',
            'Settings',
            'edit_posts',
            'eb_settings',
            [$this, 'render_settings_page']
        );

        add_submenu_page(
            'eb_settings',
            'Email Templates',
            'Email Templates',
            'edit_posts',
            'edit.php?post_type=eb_email_template'
        );

        add_submenu_page(
            'eb_settings',
            'Email Presets',
            'Email Presets',
            'edit_posts',
            'edit.php?post_type=eb_preset'
        );

        // Hidden onboarding page — not shown in menu
        add_submenu_page(
            null,
            'Welcome to WP Mailblox',
            '',
            'edit_posts',
            'eb_onboarding',
            [$this, 'render_onboarding_page']
        );
    }

    public function render_onboarding_page()
    {
        $platforms = $this->get_platforms();
        $nonce     = wp_create_nonce('eb_onboarding_save');
        $skip_url  = esc_url(add_query_arg(
            ['eb_skip_onboarding' => 1],
            admin_url('admin.php?page=eb_settings')
        ));
        wp_enqueue_media();
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_style('eb-admin-css', plugin_dir_url(__FILE__) . '../assets/css/admin.css', [], EB_VERSION);
    ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta charset="<?php bloginfo('charset'); ?>">
            <meta name="viewport" content="width=device-width">
            <title>Welcome — WP Mailblox</title>
            <?php wp_head(); ?>
        </head>
        <body class="wp-admin">

        <div class="eb-onboarding-wrap">

            <div class="eb-onboarding-header">
                <div class="eb-onboarding-logo"><?php echo file_get_contents(EB_PLUGIN_PATH . 'assets/images/logo-icon.svg'); // phpcs:ignore WordPress.Security.EscapeOutput ?> <span>WP Mailblox</span></div>
                <a href="<?php echo $skip_url; ?>" class="eb-onboarding-skip">Skip setup</a>
            </div>

            <!-- Step indicators -->
            <div class="eb-onboarding-steps">
                <div class="eb-step active" data-step="1"><span>1</span> Your Preset</div>
                <div class="eb-step-divider"></div>
                <div class="eb-step" data-step="2"><span>2</span> Brand</div>
                <div class="eb-step-divider"></div>
                <div class="eb-step" data-step="3"><span>3</span> Done</div>
            </div>

            <!-- Step 1: Preset name + platform -->
            <div class="eb-onboarding-panel active" id="eb-ob-step-1">
                <h1>Set up your first preset</h1>
                <p class="eb-ob-desc">A preset stores your brand settings — fonts, colours, logo — and is applied to every email you build.</p>

                <div class="eb-field">
                    <label for="eb_ob_name">Preset Name</label>
                    <input type="text" id="eb_ob_name" class="widefat" placeholder="e.g. My Brand" maxlength="80">
                </div>

                <div class="eb-field">
                    <label for="eb_ob_platform">Email Platform</label>
                    <select id="eb_ob_platform" class="widefat">
                        <?php foreach ($platforms as $value => $label) : ?>
                            <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="description">You can change this later. Sets the merge tags used in your templates.</p>
                </div>

                <div class="eb-ob-actions">
                    <button type="button" class="button button-primary button-large" id="eb-ob-next-1">Continue →</button>
                </div>
            </div>

            <!-- Step 2: Logo + colours -->
            <div class="eb-onboarding-panel" id="eb-ob-step-2">
                <h1>Add your brand</h1>
                <p class="eb-ob-desc">These settings can all be changed later in your preset.</p>

                <div class="eb-field">
                    <label>Logo</label>
                    <div id="eb-ob-logo-preview-wrap" style="margin-bottom:8px; display:none;">
                        <img id="eb-ob-logo-preview" src="" style="max-height:60px; max-width:200px; display:block; border:1px solid #ddd; padding:4px; border-radius:3px;">
                    </div>
                    <input type="hidden" id="eb_ob_logo_url">
                    <button type="button" class="button" id="eb-ob-logo-btn">Upload Logo</button>
                    <button type="button" class="button" id="eb-ob-logo-remove-btn" style="display:none; color:#d63638; border-color:#d63638;">Remove</button>
                    <p class="description">PNG recommended. You can skip this and add it later.</p>
                </div>

                <div class="eb-field">
                    <label for="eb_ob_bg_color">Background Colour</label>
                    <input type="text" id="eb_ob_bg_color" class="eb-ob-color" value="#ffffff" placeholder="#ffffff">
                </div>

                <div class="eb-field">
                    <label for="eb_ob_text_color">Text Colour</label>
                    <input type="text" id="eb_ob_text_color" class="eb-ob-color" value="#000000" placeholder="#000000">
                </div>

                <div class="eb-ob-actions">
                    <button type="button" class="button" id="eb-ob-back-2">← Back</button>
                    <button type="button" class="button button-primary button-large" id="eb-ob-next-2">Create Preset →</button>
                    <span class="spinner" id="eb-ob-spinner" style="float:none; margin:0 8px; visibility:hidden;"></span>
                </div>
            </div>

            <!-- Step 3: Done -->
            <div class="eb-onboarding-panel" id="eb-ob-step-3">
                <div class="eb-ob-success-icon">✓</div>
                <h1>You're all set!</h1>
                <p class="eb-ob-desc">Your preset has been created. Now build your first email template.</p>

                <div class="eb-ob-actions eb-ob-actions--center">
                    <a id="eb-ob-new-template-btn" href="<?php echo esc_url(admin_url('post-new.php?post_type=eb_email_template')); ?>" class="button button-primary button-large">Create First Template →</a>
                    <a id="eb-ob-edit-preset-btn" href="#" class="button button-large" style="margin-left:8px;">Edit Preset Settings</a>
                </div>
                <p style="margin-top:16px; text-align:center;">
                    <a href="<?php echo esc_url(admin_url('admin.php?page=eb_settings')); ?>">Go to Settings</a>
                </p>
            </div>

        </div><!-- /.eb-onboarding-wrap -->

        <script>
        jQuery(document).ready(function ($) {

            var nonce   = '<?php echo esc_js($nonce); ?>';
            var ajaxUrl = '<?php echo esc_js(admin_url('admin-ajax.php')); ?>';

            function goToStep(n) {
                $('.eb-onboarding-panel').removeClass('active');
                $('#eb-ob-step-' + n).addClass('active');
                $('.eb-step').removeClass('active done');
                for (var i = 1; i < n; i++) { $('.eb-step[data-step="' + i + '"]').addClass('done'); }
                $('.eb-step[data-step="' + n + '"]').addClass('active');
            }

            // Step 1 → 2
            $('#eb-ob-next-1').on('click', function () {
                var name = $.trim($('#eb_ob_name').val());
                if (!name) { $('#eb_ob_name').focus(); return; }
                goToStep(2);
            });

            // Step 2 → 1
            $('#eb-ob-back-2').on('click', function () { goToStep(1); });

            // Step 2 → save → 3
            $('#eb-ob-next-2').on('click', function () {
                var name = $.trim($('#eb_ob_name').val());
                if (!name) { goToStep(1); return; }

                $('#eb-ob-spinner').css('visibility', 'visible');
                $('#eb-ob-next-2').prop('disabled', true);

                $.post(ajaxUrl, {
                    action:    'eb_onboarding_save',
                    nonce:     nonce,
                    name:      name,
                    platform:  $('#eb_ob_platform').val(),
                    logo_url:  $('#eb_ob_logo_url').val(),
                    bg_color:  $('#eb_ob_bg_color').val(),
                    text_color: $('#eb_ob_text_color').val(),
                }, function (res) {
                    $('#eb-ob-spinner').css('visibility', 'hidden');
                    $('#eb-ob-next-2').prop('disabled', false);

                    if (!res.success) { alert(res.data || 'Something went wrong.'); return; }

                    var editUrl = res.data.edit_url;
                    $('#eb-ob-edit-preset-btn').attr('href', editUrl);
                    goToStep(3);
                });
            });

            // Logo uploader
            var logoUploader;
            $('#eb-ob-logo-btn').on('click', function (e) {
                e.preventDefault();
                if (logoUploader) { logoUploader.open(); return; }
                logoUploader = wp.media({
                    title: 'Select Logo', button: { text: 'Use this image' }, multiple: false,
                    library: { type: 'image' },
                });
                logoUploader.on('select', function () {
                    var att = logoUploader.state().get('selection').first().toJSON();
                    $('#eb_ob_logo_url').val(att.url);
                    $('#eb-ob-logo-preview').attr('src', att.url);
                    $('#eb-ob-logo-preview-wrap').show();
                    $('#eb-ob-logo-btn').text('Replace Logo');
                    $('#eb-ob-logo-remove-btn').show();
                });
                logoUploader.open();
            });
            $('#eb-ob-logo-remove-btn').on('click', function () {
                $('#eb_ob_logo_url').val('');
                $('#eb-ob-logo-preview-wrap').hide();
                $('#eb-ob-logo-btn').text('Upload Logo');
                $(this).hide();
            });

            // Colour pickers
            $('.eb-ob-color').wpColorPicker();
        });
        </script>

        <?php wp_footer(); ?>
        </body>
        </html>
    <?php
        exit; // Prevent WP from adding admin chrome around our full-page wizard
    }

    public function ajax_onboarding_save()
    {
        check_ajax_referer('eb_onboarding_save', 'nonce');
        if (!current_user_can('edit_posts')) wp_send_json_error('Unauthorized.');

        $name       = sanitize_text_field($_POST['name']       ?? '');
        $platform   = sanitize_key($_POST['platform']          ?? 'mailchimp');
        $logo_url   = esc_url_raw($_POST['logo_url']           ?? '');
        $bg_color   = sanitize_hex_color($_POST['bg_color']    ?? '#ffffff') ?: '#ffffff';
        $text_color = sanitize_hex_color($_POST['text_color']  ?? '#000000') ?: '#000000';

        if (empty($name)) wp_send_json_error('Preset name is required.');

        $post_id = wp_insert_post([
            'post_title'  => $name,
            'post_type'   => 'eb_preset',
            'post_status' => 'publish',
        ]);

        if (is_wp_error($post_id)) wp_send_json_error($post_id->get_error_message());

        update_post_meta($post_id, 'eb_platform',       $platform);
        update_post_meta($post_id, 'eb_logo_url',       $logo_url);
        update_post_meta($post_id, 'eb_body_bg_color',  $bg_color);
        update_post_meta($post_id, 'eb_text_color_dark', $text_color);

        // Mark onboarding complete
        update_option('eb_onboarding_complete', 1);

        wp_send_json_success(['edit_url' => get_edit_post_link($post_id, 'raw')]);
    }

    public function render_settings_page()
    {
        $active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'general';
        $tabs = [
            'general'   => 'General',
            'platforms' => 'Platform Connections',
        ];
    ?>
        <div class="wrap eb-admin-wrap">
            <h1>WP Mailblox Settings</h1>

            <nav class="nav-tab-wrapper" style="margin-bottom: 20px;">
                <?php foreach ($tabs as $slug => $label) :
                    $url = add_query_arg('tab', $slug, menu_page_url('eb_settings', false));
                ?>
                    <a href="<?php echo esc_url($url); ?>"
                       class="nav-tab <?php echo $active_tab === $slug ? 'nav-tab-active' : ''; ?>">
                        <?php echo esc_html($label); ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <?php if ($active_tab === 'general') : ?>

                <?php $this->render_general_tab(); ?>

            <?php elseif ($active_tab === 'platforms') : ?>

                <form method="post" action="options.php">
                    <?php settings_fields('eb_settings_platforms'); ?>
                    <?php
                    ob_start();
                    do_settings_sections('eb_settings_platforms');
                    $sections_html = ob_get_clean();
                    echo preg_replace(
                        '/(<table class="form-table"[^>]*>)(.*?)(<\/table>)/s',
                        '<div class="eb-form-table-wrap">$1$2$3</div>',
                        $sections_html
                    );
                    ?>
                    <?php submit_button('Save Platform Settings'); ?>
                </form>

            <?php endif; ?>
        </div>
    <?php
    }

    private function render_general_tab()
    {
        $is_pro         = eb_is_pro();
        $template_count = eb_count_active_posts('eb_email_template');
        $preset_count   = eb_count_active_posts('eb_preset');
        $template_limit = eb_get_post_limit('eb_email_template');
        $preset_limit   = eb_get_post_limit('eb_preset');
    ?>
        <div style="max-width:680px;">

            <div class="eb-settings-card">
                <h2>About</h2>
                <div class="eb-form-table-wrap">
                <table class="form-table" role="presentation" style="margin:0;">
                    <tr>
                        <th style="width:140px;">Plugin Version</th>
                        <td><?php echo esc_html(EB_VERSION); ?></td>
                    </tr>
                    <tr>
                        <th>Documentation</th>
                        <td><a href="#" target="_blank">View docs →</a></td>
                    </tr>
                    <tr>
                        <th>Support</th>
                        <td><a href="#" target="_blank">Get help →</a></td>
                    </tr>
                </table>
                </div>
            </div>

            <div class="eb-settings-card">
                <h2>Licence</h2>

                <?php if ($is_pro) : ?>
                    <p style="display:flex; align-items:center; gap:10px; margin:0;">
                        <span style="background:#00a32a; color:#fff; border-radius:3px; padding:3px 10px; font-size:12px; font-weight:700; letter-spacing:.5px;">PRO</span>
                        You have an active Pro licence. All features are unlocked.
                    </p>
                <?php else : ?>
                    <p style="display:flex; align-items:center; gap:10px; margin:0 0 16px;">
                        <span style="background:#757575; color:#fff; border-radius:3px; padding:3px 10px; font-size:12px; font-weight:700; letter-spacing:.5px;">FREE</span>
                        You are on the Free plan.
                    </p>

                    <div class="eb-form-table-wrap">
                    <table class="form-table" role="presentation" style="margin:0 0 16px;">
                        <tr>
                            <th style="width:140px;">Email Templates</th>
                            <td>
                                <?php echo esc_html($template_count); ?> / <?php echo esc_html($template_limit); ?> used
                                <?php if ($template_count >= $template_limit) : ?>
                                    <span style="color:#d63638; font-weight:600; margin-left:6px;">Limit reached</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Presets</th>
                            <td>
                                <?php echo esc_html($preset_count); ?> / <?php echo esc_html($preset_limit); ?> used
                                <?php if ($preset_count >= $preset_limit) : ?>
                                    <span style="color:#d63638; font-weight:600; margin-left:6px;">Limit reached</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                    </div>

                    <p style="margin:0 0 16px;">
                        <a href="#" class="button button-primary">Upgrade to Pro →</a>
                        <span style="margin-left:10px; color:#757575; font-size:12px;">Unlimited templates, presets, and all platform integrations.</span>
                    </p>

                    <div class="eb-field" style="max-width:400px; margin:0;">
                        <label for="eb_licence_key">Licence Key</label>
                        <input type="text" id="eb_licence_key" class="widefat" placeholder="xxxx-xxxx-xxxx-xxxx" disabled>
                        <p class="description">Licence activation will be available in a future update.</p>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    <?php
    }

    public function register_settings()
    {
        // ── Platform Connections tab ──────────────────────────────────────────
        $platform_options = [
            'eb_api_mailchimp_key'              => null,
            'eb_api_brevo_key'                  => null,
            'eb_api_brevo_sender_name'          => null,
            'eb_api_brevo_sender_email'         => null,
            'eb_api_klaviyo_key'                => null,
            'eb_api_campaign_monitor_key'       => null,
            'eb_api_campaign_monitor_client_id' => null,
            'eb_api_onesignal_key'              => null,
            'eb_api_onesignal_app_id'           => null,
            'eb_api_activecampaign_key'         => null,
            'eb_api_activecampaign_url'         => null,
        ];

        foreach (array_keys($platform_options) as $key) {
            register_setting('eb_settings_platforms', $key, [
                'sanitize_callback' => 'sanitize_text_field',
            ]);
        }

        add_settings_section(
            'eb_platforms_mailchimp',
            'Mailchimp',
            function() {
                echo '<p class="description">Enter your Mailchimp API key to enable direct template push. '
                   . 'Find it in <strong>Mailchimp → Account → Extras → API keys</strong>.</p>';
            },
            'eb_settings_platforms'
        );

        add_settings_field(
            'eb_api_mailchimp_key',
            'API Key',
            function() {
                $val = get_option('eb_api_mailchimp_key', '');
                echo '<input type="password" name="eb_api_mailchimp_key" value="' . esc_attr($val) . '" class="regular-text" autocomplete="off" placeholder="xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx-us12">';
                echo '<p class="description">Format: <code>key-datacenter</code> — the datacenter suffix (e.g. <code>us12</code>) is required.</p>';
            },
            'eb_settings_platforms',
            'eb_platforms_mailchimp'
        );

        add_settings_section(
            'eb_platforms_brevo',
            'Brevo (Sendinblue)',
            function() {
                echo '<p class="description">Enter your Brevo API key. '
                   . 'Find it in <strong>Brevo → Account → SMTP &amp; API → API Keys</strong>. '
                   . 'Brevo also requires a sender name and email for template creation.</p>';
            },
            'eb_settings_platforms'
        );

        add_settings_field(
            'eb_api_brevo_key',
            'API Key',
            function() {
                $val = get_option('eb_api_brevo_key', '');
                echo '<input type="password" name="eb_api_brevo_key" value="' . esc_attr($val) . '" class="regular-text" autocomplete="off">';
            },
            'eb_settings_platforms',
            'eb_platforms_brevo'
        );

        add_settings_field(
            'eb_api_brevo_sender',
            'Default Sender',
            function() {
                $name  = get_option('eb_api_brevo_sender_name', '');
                $email = get_option('eb_api_brevo_sender_email', '');
                echo '<input type="text" name="eb_api_brevo_sender_name" value="' . esc_attr($name) . '" placeholder="Sender Name" style="margin-right:8px; width:180px;">';
                echo '<input type="email" name="eb_api_brevo_sender_email" value="' . esc_attr($email) . '" placeholder="sender@example.com" style="width:200px;">';
                echo '<p class="description">Must match a verified sender in your Brevo account.</p>';
            },
            'eb_settings_platforms',
            'eb_platforms_brevo'
        );

        add_settings_section(
            'eb_platforms_klaviyo',
            'Klaviyo',
            function() {
                echo '<p class="description">Enter your Klaviyo Private API key. '
                   . 'Find it in <strong>Klaviyo → Settings → API Keys → Create Private API Key</strong>. '
                   . 'Requires at minimum <em>Write</em> access to Templates.</p>';
            },
            'eb_settings_platforms'
        );

        add_settings_field(
            'eb_api_klaviyo_key',
            'Private API Key',
            function() {
                $val = get_option('eb_api_klaviyo_key', '');
                echo '<input type="password" name="eb_api_klaviyo_key" value="' . esc_attr($val) . '" class="regular-text" autocomplete="off">';
            },
            'eb_settings_platforms',
            'eb_platforms_klaviyo'
        );

        add_settings_section(
            'eb_platforms_campaign_monitor',
            'Campaign Monitor',
            function() {
                echo '<p class="description">Enter your Campaign Monitor API key and client ID. '
                   . 'Find your API key in <strong>Campaign Monitor → Account Settings → API Keys</strong>. '
                   . 'Your client ID is in the URL when viewing a client: <code>app.createsend.com/clients/<strong>clientid</strong>/</code>.</p>'
                   . '<p class="description" style="color:#856404;">&#9888; Campaign Monitor fetches your email HTML from a temporary public URL. '
                   . 'Your WordPress site must be publicly accessible when pushing — localhost and private network addresses will not work.</p>';
            },
            'eb_settings_platforms'
        );

        add_settings_field(
            'eb_api_campaign_monitor_key',
            'API Key',
            function() {
                $val = get_option('eb_api_campaign_monitor_key', '');
                echo '<input type="password" name="eb_api_campaign_monitor_key" value="' . esc_attr($val) . '" class="regular-text" autocomplete="off">';
            },
            'eb_settings_platforms',
            'eb_platforms_campaign_monitor'
        );

        add_settings_field(
            'eb_api_campaign_monitor_client_id',
            'Client ID',
            function() {
                $val = get_option('eb_api_campaign_monitor_client_id', '');
                echo '<input type="text" name="eb_api_campaign_monitor_client_id" value="' . esc_attr($val) . '" class="regular-text" placeholder="e.g. abc123def456abc123def456">';
                echo '<p class="description">The client account to create the template under.</p>';
            },
            'eb_settings_platforms',
            'eb_platforms_campaign_monitor'
        );

        add_settings_section(
            'eb_platforms_onesignal',
            'OneSignal',
            function() {
                echo '<p class="description">Enter your OneSignal REST API key and App ID to push templates directly. '
                   . 'Find both in <strong>OneSignal → Settings → Keys &amp; IDs</strong>.</p>';
            },
            'eb_settings_platforms'
        );

        add_settings_field(
            'eb_api_onesignal_key',
            'REST API Key',
            function() {
                $val = get_option('eb_api_onesignal_key', '');
                echo '<input type="password" name="eb_api_onesignal_key" value="' . esc_attr($val) . '" class="regular-text" autocomplete="off">';
            },
            'eb_settings_platforms',
            'eb_platforms_onesignal'
        );

        add_settings_field(
            'eb_api_onesignal_app_id',
            'App ID',
            function() {
                $val = get_option('eb_api_onesignal_app_id', '');
                echo '<input type="text" name="eb_api_onesignal_app_id" value="' . esc_attr($val) . '" class="regular-text" placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx">';
                echo '<p class="description">The App ID of your OneSignal app (UUID format).</p>';
            },
            'eb_settings_platforms',
            'eb_platforms_onesignal'
        );

        add_settings_section(
            'eb_platforms_activecampaign',
            'ActiveCampaign',
            function() {
                echo '<p class="description">ActiveCampaign does not provide a public API for creating email templates. '
                   . 'Export HTML from WP Mailblox and paste it into the code editor when designing a campaign. '
                   . 'API credentials below are retained for reference.</p>';
            },
            'eb_settings_platforms'
        );

        add_settings_field(
            'eb_api_activecampaign_key',
            'API Token',
            function() {
                $val = get_option('eb_api_activecampaign_key', '');
                echo '<input type="password" name="eb_api_activecampaign_key" value="' . esc_attr($val) . '" class="regular-text" autocomplete="off">';
                echo '<p class="description">Found in <strong>ActiveCampaign → Settings → Developer → API Access</strong>.</p>';
            },
            'eb_settings_platforms',
            'eb_platforms_activecampaign'
        );

        add_settings_field(
            'eb_api_activecampaign_url',
            'Account URL',
            function() {
                $val = get_option('eb_api_activecampaign_url', '');
                echo '<input type="url" name="eb_api_activecampaign_url" value="' . esc_attr($val) . '" class="regular-text" placeholder="https://youraccountname.api-us1.com">';
                echo '<p class="description">Your unique ActiveCampaign API base URL.</p>';
            },
            'eb_settings_platforms',
            'eb_platforms_activecampaign'
        );
    }

    private function get_platforms()
    {
        $platforms    = [];
        $platform_dir = EB_PLUGIN_PATH . 'platforms/';
        $files        = glob($platform_dir . '*.json');

        if (!$files) return $platforms;

        foreach ($files as $file) {
            $slug             = basename($file, '.json');
            $label            = ucwords(str_replace('_', ' ', $slug));
            $platforms[$slug] = $label;
        }

        return $platforms;
    }

    public function register_template_rest_route()
    {
        register_rest_route('email-builder/v1', '/starter-templates', [
            'methods'             => 'GET',
            'callback'            => [$this, 'get_starter_templates'],
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            },
        ]);

        register_rest_route('email-builder/v1', '/preset/(?P<id>\d+)', [
            'methods'  => 'GET',
            'callback' => function ($request) {
                $id = intval($request['id']);
                return rest_ensure_response(eb_get_preset_settings($id));
            },
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            },
        ]);

        // Save current email as a reusable template
        register_rest_route('email-builder/v1', '/saved-templates', [
            [
                'methods'             => 'POST',
                'callback'            => [$this, 'save_template'],
                'permission_callback' => function () {
                    return current_user_can('edit_posts');
                },
                'args' => [
                    'name'    => ['required' => true, 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field'],
                    'blocks'  => ['required' => true, 'type' => 'string'],
                ],
            ],
            [
                'methods'             => 'GET',
                'callback'            => [$this, 'get_saved_templates'],
                'permission_callback' => function () {
                    return current_user_can('edit_posts');
                },
            ],
        ]);

        // Delete a saved template
        register_rest_route('email-builder/v1', '/saved-templates/(?P<id>\d+)', [
            'methods'             => 'DELETE',
            'callback'            => [$this, 'delete_saved_template'],
            'permission_callback' => function () {
                return current_user_can('delete_posts');
            },
        ]);

        // Send test email
        register_rest_route('email-builder/v1', '/send-test', [
            'methods'             => 'POST',
            'callback'            => [$this, 'send_test_email'],
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            },
            'args' => [
                'post_id' => ['required' => true, 'type' => 'integer', 'sanitize_callback' => 'absint'],
                'email'   => ['required' => true, 'type' => 'string',  'sanitize_callback' => 'sanitize_email', 'validate_callback' => 'is_email'],
            ],
        ]);

        // Vimeo thumbnail proxy (avoids CORS in browser)
        register_rest_route('email-builder/v1', '/video-thumbnail', [
            'methods'             => 'GET',
            'callback'            => [$this, 'get_video_thumbnail'],
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            },
            'args' => [
                'url' => ['required' => true, 'type' => 'string', 'sanitize_callback' => 'esc_url_raw'],
            ],
        ]);
    }

    public function register_module_rest_routes()
    {
        register_rest_route('email-builder/v1', '/modules', [
            [
                'methods'             => 'GET',
                'callback'            => [$this, 'get_modules'],
                'permission_callback' => function () {
                    return current_user_can('edit_posts');
                },
            ],
            [
                'methods'             => 'POST',
                'callback'            => [$this, 'save_module'],
                'permission_callback' => function () {
                    return current_user_can('edit_posts');
                },
                'args' => [
                    'name'   => ['required' => true, 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field'],
                    'blocks' => ['required' => true, 'type' => 'string'],
                ],
            ],
        ]);

        register_rest_route('email-builder/v1', '/modules/(?P<id>\d+)', [
            [
                'methods'             => 'GET',
                'callback'            => [$this, 'get_module'],
                'permission_callback' => function () {
                    return current_user_can('edit_posts');
                },
                'args' => ['id' => ['required' => true, 'sanitize_callback' => 'absint']],
            ],
            [
                'methods'             => 'DELETE',
                'callback'            => [$this, 'delete_module'],
                'permission_callback' => function () {
                    return current_user_can('delete_posts');
                },
            ],
        ]);
    }

    public function get_module($request)
    {
        $id   = absint($request['id']);
        $post = get_post($id);

        if (!$post || $post->post_type !== 'eb_module') {
            return new WP_Error('not_found', 'Module not found.', ['status' => 404]);
        }

        return rest_ensure_response([
            'id'     => $post->ID,
            'name'   => $post->post_title,
            'blocks' => $post->post_content,
        ]);
    }

    public function get_modules()
    {
        $posts = get_posts([
            'post_type'      => 'eb_module',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ]);

        return rest_ensure_response(array_map(function ($post) {
            return [
                'id'   => $post->ID,
                'name' => $post->post_title,
                'date' => get_the_date('d M Y', $post),
            ];
        }, $posts));
    }

    public function save_module($request)
    {
        $name   = $request->get_param('name');
        $blocks = $request->get_param('blocks');

        json_decode($blocks);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WP_Error('invalid_blocks', 'Invalid block data.', ['status' => 400]);
        }

        $post_id = wp_insert_post([
            'post_title'   => $name,
            'post_content' => wp_slash($blocks),
            'post_status'  => 'publish',
            'post_type'    => 'eb_module',
        ]);

        if (is_wp_error($post_id)) {
            return new WP_Error('save_failed', 'Could not save module.', ['status' => 500]);
        }

        return rest_ensure_response(['id' => $post_id, 'name' => $name]);
    }

    public function delete_module($request)
    {
        $id   = intval($request['id']);
        $post = get_post($id);

        if (!$post || $post->post_type !== 'eb_module') {
            return new WP_Error('not_found', 'Module not found.', ['status' => 404]);
        }

        wp_delete_post($id, true);
        return rest_ensure_response(['deleted' => true]);
    }

    public function save_template($request)
    {
        $name     = $request->get_param('name');
        $blocks   = $request->get_param('blocks');
        $tags     = $request->get_param('tags') ?: [];
        $category = sanitize_text_field($request->get_param('category') ?: '');

        // Validate that blocks is valid JSON
        json_decode($blocks);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WP_Error('invalid_blocks', 'Invalid block data.', ['status' => 400]);
        }

        $post_id = wp_insert_post([
            'post_title'   => $name,
            'post_content' => wp_slash($blocks),
            'post_status'  => 'publish',
            'post_type'    => 'eb_saved_template',
        ]);

        if (is_wp_error($post_id)) {
            return $post_id;
        }

        if (!empty($tags) && is_array($tags)) {
            $tag_names = array_map('sanitize_text_field', $tags);
            wp_set_object_terms($post_id, $tag_names, 'eb_template_tag');
        }

        if ($category !== '') {
            wp_set_object_terms($post_id, [$category], 'eb_template_category');
        }

        $saved_tags     = wp_get_object_terms($post_id, 'eb_template_tag', ['fields' => 'names']);
        $saved_cats     = wp_get_object_terms($post_id, 'eb_template_category', ['fields' => 'names']);
        $saved_category = (!is_wp_error($saved_cats) && !empty($saved_cats)) ? $saved_cats[0] : '';

        return rest_ensure_response(['id' => $post_id, 'name' => $name, 'tags' => $saved_tags, 'category' => $saved_category]);
    }

    public function get_saved_templates($request)
    {
        $tag_filter      = $request->get_param('tag')      ? sanitize_text_field($request->get_param('tag'))      : '';
        $category_filter = $request->get_param('category') ? sanitize_text_field($request->get_param('category')) : '';

        $query_args = [
            'post_type'      => 'eb_saved_template',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ];

        $tax_query = [];

        if ($tag_filter) {
            $tax_query[] = [
                'taxonomy' => 'eb_template_tag',
                'field'    => 'name',
                'terms'    => $tag_filter,
            ];
        }

        if ($category_filter) {
            $tax_query[] = [
                'taxonomy' => 'eb_template_category',
                'field'    => 'name',
                'terms'    => $category_filter,
            ];
        }

        if (!empty($tax_query)) {
            $query_args['tax_query'] = $tax_query;
        }

        $posts = get_posts($query_args);

        $templates = array_map(function ($post) {
            $blocks = json_decode($post->post_content, true);
            $tags   = wp_get_object_terms($post->ID, 'eb_template_tag', ['fields' => 'names']);
            $cats   = wp_get_object_terms($post->ID, 'eb_template_category', ['fields' => 'names']);
            return [
                'id'       => $post->ID,
                'name'     => $post->post_title,
                'date'     => get_the_date('d M Y', $post),
                'tags'     => is_array($tags) ? $tags : [],
                'category' => (!is_wp_error($cats) && !empty($cats)) ? $cats[0] : '',
                'blocks'   => is_array($blocks) ? $blocks : [],
            ];
        }, $posts);

        // Build lists of all tags and categories for the filter UI
        $all_tags = get_terms([
            'taxonomy'   => 'eb_template_tag',
            'hide_empty' => true,
            'fields'     => 'names',
        ]);

        $all_categories = get_terms([
            'taxonomy'   => 'eb_template_category',
            'hide_empty' => true,
            'fields'     => 'names',
            'orderby'    => 'name',
            'order'      => 'ASC',
        ]);

        return rest_ensure_response([
            'templates'      => $templates,
            'all_tags'       => is_array($all_tags) ? array_values($all_tags) : [],
            'all_categories' => is_array($all_categories) ? array_values($all_categories) : [],
        ]);
    }

    public function delete_saved_template($request)
    {
        $id   = intval($request['id']);
        $post = get_post($id);

        if (!$post || $post->post_type !== 'eb_saved_template') {
            return new WP_Error('not_found', 'Template not found.', ['status' => 404]);
        }

        if ((int) $post->post_author !== get_current_user_id() && !current_user_can('manage_options')) {
            return new WP_Error('forbidden', 'You do not have permission to delete this template.', ['status' => 403]);
        }

        wp_delete_post($id, true);
        return rest_ensure_response(['deleted' => true]);
    }

    public function send_test_email($request)
    {
        $rate_key = 'eb_test_email_rate_' . get_current_user_id();
        $count    = (int) get_transient($rate_key);
        if ($count >= 5) {
            return new WP_Error('rate_limited', 'Too many test emails sent. Please wait a few minutes and try again.', ['status' => 429]);
        }
        set_transient($rate_key, $count + 1, 5 * MINUTE_IN_SECONDS);

        $post_id = $request->get_param('post_id');
        $email   = $request->get_param('email');

        if (!current_user_can('edit_post', $post_id)) {
            return new WP_Error('forbidden', 'You do not have permission to export this email.', ['status' => 403]);
        }

        $post = get_post($post_id);
        if (!$post || $post->post_type !== 'eb_email_template') {
            return new WP_Error('not_found', 'Email template not found.', ['status' => 404]);
        }

        $html = $this->export_email($post_id);
        if (empty($html)) {
            return new WP_Error('export_failed', 'Could not generate email HTML. Make sure a preset is selected.', ['status' => 500]);
        }

        $subject = get_post_meta($post_id, 'eb_subject', true) ?: $post->post_title;
        $headers = ['Content-Type: text/html; charset=UTF-8'];

        $sent = wp_mail($email, '[Test] ' . $subject, $html, $headers);

        if (!$sent) {
            return new WP_Error('send_failed', 'WordPress could not send the email. Check your SMTP / mail configuration.', ['status' => 500]);
        }

        return rest_ensure_response(['sent' => true, 'to' => $email]);
    }

    public function get_video_thumbnail($request)
    {
        $url = $request->get_param('url');

        // Only allow Vimeo (YouTube thumbnails are fetched client-side)
        if (!preg_match('/vimeo\.com/i', $url)) {
            return new WP_Error('unsupported', 'Only Vimeo URLs are supported by this endpoint.', ['status' => 400]);
        }

        $oembed_url = add_query_arg('url', rawurlencode($url), 'https://vimeo.com/api/oembed.json');
        $response   = wp_remote_get($oembed_url, ['timeout' => 8, 'sslverify' => true]);

        if (is_wp_error($response)) {
            return new WP_Error('fetch_failed', 'Could not fetch Vimeo oEmbed data.', ['status' => 502]);
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        if (empty($body['thumbnail_url'])) {
            return new WP_Error('no_thumbnail', 'No thumbnail found for this Vimeo video.', ['status' => 404]);
        }

        return rest_ensure_response([
            'thumbnail_url' => $body['thumbnail_url'],
            'title'         => $body['title'] ?? '',
        ]);
    }

    public function get_starter_templates()
    {
        $templates = [];
        $dir       = EB_PLUGIN_PATH . 'starter-templates/';
        $files     = glob($dir . '*.json');

        if (!$files) return rest_ensure_response([]);

        foreach ($files as $file) {
            $raw  = str_replace('{{PLUGIN_URL}}', EB_PLUGIN_URL, file_get_contents($file));
            $data = json_decode($raw, true);
            if (!$data) continue;

            $slug          = basename($file, '.json');
            $preview_image = '';
            foreach (['png', 'jpg', 'jpeg', 'webp'] as $ext) {
                $img_path = EB_PLUGIN_PATH . 'assets/images/previews/' . $slug . '.' . $ext;
                if (file_exists($img_path)) {
                    $preview_image = EB_PLUGIN_URL . 'assets/images/previews/' . $slug . '.' . $ext;
                    break;
                }
            }

            $is_pro_template = !empty($data['pro']);

            $templates[] = [
                'slug'         => $slug,
                'title'        => $data['title'] ?? $slug,
                'description'  => $data['description'] ?? '',
                'previewImage' => $preview_image,
                'pro'          => $is_pro_template,
                'blocks'       => ($is_pro_template && !eb_is_pro()) ? [] : ($data['blocks'] ?? []),
            ];
        }

        return rest_ensure_response($templates);
    }

    public function ajax_export_preset()
    {
        check_ajax_referer('eb_export_preset', 'nonce');
        if (!current_user_can('edit_posts')) wp_send_json_error('Unauthorized.');

        $post_id = intval($_POST['post_id'] ?? 0);
        $post    = get_post($post_id);
        if (!$post || $post->post_type !== 'eb_preset') {
            wp_send_json_error('Invalid preset.');
        }

        $meta_keys = [
            'eb_platform', 'eb_container_width',
            'eb_heading_font', 'eb_heading_font_type', 'eb_heading_font_stack',
            'eb_heading_font_weight', 'eb_heading_font_fallback',
            'eb_heading_size', 'eb_heading_size_mobile', 'eb_heading_line_height',
            'eb_subheading_font', 'eb_subheading_font_type', 'eb_subheading_font_stack',
            'eb_subheading_font_weight', 'eb_subheading_font_fallback',
            'eb_subheading_size', 'eb_subheading_size_mobile', 'eb_subheading_line_height',
            'eb_body_font', 'eb_body_font_type', 'eb_body_font_stack',
            'eb_body_font_weight', 'eb_body_font_fallback',
            'eb_body_size', 'eb_body_size_mobile', 'eb_body_line_height',
            'eb_button_font', 'eb_button_font_type', 'eb_button_font_stack',
            'eb_button_font_weight', 'eb_button_font_fallback',
            'eb_button_size', 'eb_button_size_mobile', 'eb_button_line_height',
            'eb_body_bg_color', 'eb_text_color_dark', 'eb_text_color_light', 'eb_sale_price_color',
            'eb_dark_bg_color', 'eb_dark_text_color', 'eb_dark_button_color',
            'eb_dark_button_text_color', 'eb_dark_link_color',
            'eb_logo_url', 'eb_logo_alt', 'eb_dark_logo_enabled', 'eb_dark_logo_url',
        ];

        $data = [
            '_name'    => $post->post_title,
            '_version' => EB_VERSION,
        ];
        foreach ($meta_keys as $key) {
            $data[$key] = get_post_meta($post_id, $key, true);
        }

        wp_send_json_success($data);
    }

    public function ajax_import_preset()
    {
        check_ajax_referer('eb_import_preset', 'nonce');
        if (!current_user_can('edit_posts')) wp_send_json_error('Unauthorized.');

        $json = wp_unslash($_POST['json'] ?? '');
        $data = json_decode($json, true);

        if (!is_array($data) || empty($data['_name'])) {
            wp_send_json_error('Invalid preset file.');
        }

        $post_id = wp_insert_post([
            'post_title'  => sanitize_text_field($data['_name']) . ' (imported)',
            'post_type'   => 'eb_preset',
            'post_status' => 'publish',
        ]);

        if (is_wp_error($post_id)) {
            wp_send_json_error($post_id->get_error_message());
        }

        $skip = ['_name', '_version'];
        foreach ($data as $key => $value) {
            if (in_array($key, $skip, true)) continue;
            if (strpos($key, 'eb_') !== 0) continue;
            update_post_meta($post_id, sanitize_key($key), sanitize_text_field($value));
        }

        wp_send_json_success(['redirect' => get_edit_post_link($post_id, 'raw')]);
    }
}

// Enqueue admin styles
add_action('admin_enqueue_scripts', function () {
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
    wp_enqueue_style(
        'eb-admin-css',
        plugin_dir_url(__FILE__) . '../assets/css/admin.css',
        [],
        EB_VERSION
    );

    // Enqueue media uploader and preset JS for preset pages
    global $post;
    if ($post && $post->post_type === 'eb_preset') {
        wp_enqueue_media();
        wp_enqueue_style('wp-components');
        wp_enqueue_script(
            'eb-preset-admin',
            plugin_dir_url(__FILE__) . '../assets/js/preset-admin.js',
            ['jquery', 'wp-color-picker', 'media-upload'],
            EB_VERSION,
            true
        );
        wp_localize_script('eb-preset-admin', 'ebPresetAdmin', [
            'ajaxUrl'     => admin_url('admin-ajax.php'),
            'exportNonce' => wp_create_nonce('eb_export_preset'),
            'importNonce' => wp_create_nonce('eb_import_preset'),
        ]);
    }

    if ($post && $post->post_type === 'eb_email_template') {
        wp_enqueue_script(
            'eb-template-io',
            plugin_dir_url(__FILE__) . '../assets/js/template-io.js',
            ['jquery'],
            EB_VERSION,
            true
        );
    }
});

// admin_footer preset state script removed — now handled by email-settings-sidebar.js

// Pass fonts and platform tags to block editor
add_action('enqueue_block_editor_assets', function () {
    global $post;
    if (!$post || $post->post_type !== 'eb_email_template') return;

    $preset_id    = get_post_meta($post->ID, 'eb_preset', true);
    $heading_font = get_post_meta($preset_id, 'eb_heading_font', true) ?: 'Arial';
    $body_font    = get_post_meta($preset_id, 'eb_body_font', true) ?: 'Helvetica';

    // Load platform tags for merge field insertion (template override takes precedence)
    $platform      = get_post_meta($post->ID, 'eb_platform', true)
                  ?: get_post_meta($preset_id, 'eb_platform', true)
                  ?: 'mailchimp';
    $platform_file = EB_PLUGIN_PATH . 'platforms/' . sanitize_file_name($platform) . '.json';
    $platform_tags = [];

    $conditional_fields = [];

    if (file_exists($platform_file)) {
        $data = json_decode(file_get_contents($platform_file), true);
        if ($data) {
            foreach ($data as $group_name => $group) {
                if (!is_array($group)) continue;
                if ($group_name === 'conditional_fields') {
                    // Pass conditional_fields as-is to JS
                    foreach ($group as $field_key => $field_data) {
                        if (is_array($field_data) && isset($field_data['if'])) {
                            $conditional_fields[] = [
                                'key'   => $field_key,
                                'if'    => $field_data['if'],
                                'endif' => $field_data['endif'],
                                'label' => $field_data['label'] ?? $field_key,
                            ];
                        }
                    }
                    continue;
                }
                $platform_tags[$group_name] = [];
                foreach ($group as $key => $value) {
                    if (is_array($value) && isset($value['tag'])) {
                        $platform_tags[$group_name][] = [
                            'key'   => $key,
                            'tag'   => $value['tag'],
                            'label' => $value['label'] ?? $key,
                            'type'  => $value['type'] ?? 'text',
                        ];
                    }
                }
            }
        }
    }

    $preset_logo_url = get_post_meta($preset_id, 'eb_logo_url', true) ?: '';

    $preset_settings = $preset_id ? eb_get_preset_settings($preset_id) : [];

    // Build flat key→tag map for every platform so JS can do cross-platform merge tag replacement
    $all_platforms = [];
    foreach (glob(EB_PLUGIN_PATH . 'platforms/*.json') as $pf) {
        $pslug = basename($pf, '.json');
        $praw  = json_decode(file_get_contents($pf), true);
        if (!$praw) continue;
        $all_platforms[$pslug] = [];
        foreach ($praw as $pgroup) {
            if (!is_array($pgroup)) continue;
            foreach ($pgroup as $pkey => $pvalue) {
                if (is_array($pvalue) && isset($pvalue['tag'])) {
                    $all_platforms[$pslug][$pkey] = $pvalue['tag'];
                }
            }
        }
    }

    // Build toolbar / sidebar data
    $platform_override    = get_post_meta($post->ID, 'eb_platform', true) ?: '';
    $eff_platform         = $platform_override ?: get_post_meta($preset_id, 'eb_platform', true) ?: 'mailchimp';
    $platform_label_str   = ucwords(str_replace('_', ' ', $eff_platform));
    $api_key_option       = "eb_api_{$eff_platform}_key";
    $has_api_key          = !empty(get_option($api_key_option, ''));
    $platform_template_id = get_post_meta($post->ID, "eb_{$eff_platform}_template_id", true);
    $push_label           = !empty($platform_template_id) ? "Update in {$platform_label_str}" : "Push to {$platform_label_str}";

    $share_token = get_post_meta($post->ID, 'eb_share_token', true);
    $share_url   = $share_token
        ? add_query_arg(['eb_share' => $post->ID, 'token' => $share_token], home_url('/'))
        : '';

    wp_localize_script(
        'wp-blocks',
        'EB_EDITOR_DATA',
        [
            'fonts'            => [
                'heading' => $heading_font,
                'body'    => $body_font,
            ],
            'platform_tags'       => $platform_tags,
            'conditional_fields'  => $conditional_fields,
            'current_platform'    => $platform,
            'all_platforms'    => $all_platforms,
            'preset_logo'      => $preset_logo_url,
            'plugin_url'       => EB_PLUGIN_URL,
            'preset'           => $preset_settings,
            'is_pro'           => eb_is_pro(),
            'upgrade_url'      => function_exists('wp_mailblox_fs') ? wp_mailblox_fs()->get_upgrade_url() : '',
            // Toolbar / sidebar data
            'post_id'               => $post->ID,
            'preview_url'           => add_query_arg(['eb_preview' => $post->ID], home_url('/')),
            'share_url'             => $share_url,
            'share_nonce'           => wp_create_nonce('eb_share_token_' . $post->ID),
            'export_nonce'          => wp_create_nonce('eb_export_email_nonce'),
            'push_nonce'            => wp_create_nonce('eb_push_to_platform_nonce'),
            'template_export_nonce' => wp_create_nonce('eb_export_template_' . $post->ID),
            'has_api_key'           => $has_api_key,
            'push_label'            => $push_label,
            'platform_label'        => $platform_label_str,
            'new_preset_url'        => admin_url('post-new.php?post_type=eb_preset'),
            'user_email'            => wp_get_current_user()->user_email ?: '',
        ]
    );

    // --- Editor styles ---
    if (!$preset_id) return;

    $preset_settings = eb_get_preset_settings($preset_id);

    $container_width        = intval($preset_settings['container_width'] ?? 640);
    $bg_color               = $preset_settings['bg_color'] ?? '#ffffff';
    $heading_font_stack     = $preset_settings['heading_font_stack'] ?? 'Arial, Helvetica, sans-serif';
    $heading_font_weight    = $preset_settings['heading_font_weight'] ?? 700;
    $heading_size           = intval($preset_settings['heading_size'] ?? 28);
    $heading_size_mobile    = intval($preset_settings['heading_size_mobile'] ?? $heading_size);
    $heading_line_height    = $preset_settings['heading_line_height'] ?? 1.3;
    $subheading_font_stack  = $preset_settings['subheading_font_stack'] ?? 'Arial, Helvetica, sans-serif';
    $subheading_font_weight = $preset_settings['subheading_font_weight'] ?? 400;
    $subheading_size        = intval($preset_settings['subheading_size'] ?? 24);
    $subheading_size_mobile = intval($preset_settings['subheading_size_mobile'] ?? $subheading_size);
    $subheading_line_height = $preset_settings['subheading_line_height'] ?? 1.3;
    $body_font_stack        = $preset_settings['body_font_stack'] ?? 'Helvetica, Arial, sans-serif';
    $body_font_weight       = $preset_settings['body_font_weight'] ?? 400;
    $body_size              = intval($preset_settings['body_size'] ?? 16);
    $body_size_mobile       = intval($preset_settings['body_size_mobile'] ?? $body_size);
    $body_line_height       = $preset_settings['body_line_height'] ?? 1.3;
    $button_font_stack      = $preset_settings['button_font_stack'] ?? $body_font_stack;
    $button_font_weight     = $preset_settings['button_font_weight'] ?? 700;
    $button_size            = intval($preset_settings['button_size'] ?? $body_size);
    $button_size_mobile     = intval($preset_settings['button_size_mobile'] ?? $button_size);
    $text_color_dark        = $preset_settings['text_color_dark'] ?? '#000000';
    $text_color_light       = $preset_settings['text_color_light'] ?? '#ffffff';

    $editor_body_bg_image_css = '';
    if ( !empty($preset_settings['body_bg_image_enabled']) && !empty($preset_settings['body_bg_image_url']) ) {
        $bg_img_pos  = esc_attr($preset_settings['body_bg_image_pos_x']) . ' ' . esc_attr($preset_settings['body_bg_image_pos_y']);
        $bg_img_size = esc_attr($preset_settings['body_bg_image_size_w']);
        $editor_body_bg_image_css = sprintf(
            "background-image:url('%s'); background-repeat:%s; background-position:%s; background-size:%s;",
            esc_url($preset_settings['body_bg_image_url']),
            esc_attr($preset_settings['body_bg_image_repeat']),
            $bg_img_pos,
            $bg_img_size
        );
    }

    // Use same contrast logic as email output to pick correct editor text colour
    list($r, $g, $b) = sscanf($bg_color, '#%02x%02x%02x');
    $luminance          = ($r * 0.2126 + $g * 0.7152 + $b * 0.0722) / 255;
    $editor_text_color  = $luminance > 0.5 ? $text_color_dark : $text_color_light;

    // Google Fonts import if needed — collect per-font weights to match email output
    $google_font_weights = [];
    $font_groups = [
        'heading'    => ['type' => $preset_settings['heading_font_type']    ?? 'websafe', 'font' => $preset_settings['heading_font']    ?? '', 'weight' => intval($preset_settings['heading_font_weight']    ?? 400)],
        'subheading' => ['type' => $preset_settings['subheading_font_type'] ?? 'websafe', 'font' => $preset_settings['subheading_font'] ?? '', 'weight' => intval($preset_settings['subheading_font_weight'] ?? 400)],
        'body'       => ['type' => $preset_settings['body_font_type']       ?? 'websafe', 'font' => $preset_settings['body_font']       ?? '', 'weight' => intval($preset_settings['body_font_weight']       ?? 400)],
        'button'     => ['type' => $preset_settings['button_font_type']     ?? 'websafe', 'font' => $preset_settings['button_font']     ?? '', 'weight' => intval($preset_settings['button_font_weight']     ?? 700)],
    ];
    foreach ($font_groups as $group) {
        if ($group['type'] === 'google' && !empty($group['font'])) {
            $name = $group['font'];
            if (!isset($google_font_weights[$name])) {
                $google_font_weights[$name] = [];
            }
            $google_font_weights[$name][] = $group['weight'];
            $google_font_weights[$name][] = 400; // always include regular
        }
    }

    if (!empty($google_font_weights)) {
        $query_parts = [];
        foreach ($google_font_weights as $font_name => $weights) {
            $weights = array_unique($weights);
            sort($weights);
            $query_parts[] = str_replace(' ', '+', $font_name) . ':wght@' . implode(';', $weights);
        }
        $query = implode('&family=', $query_parts);
        wp_enqueue_style(
            'eb-editor-google-fonts',
            'https://fonts.googleapis.com/css2?family=' . $query . '&display=swap',
            [],
            null
        );
    }

    $editor_css = "
    /* Constrain editor to container width */
    .editor-styles-wrapper .is-root-container {
        //max-width: {$container_width}px;
        margin-left: auto;
        margin-right: auto;
        background-color: {$bg_color};
        {$editor_body_bg_image_css}
        overflow:hidden;
        max-width:90% !important;
        min-width: calc(2rem + {$container_width}px);
        box-shadow: 0 32px 80px rgba(0, 0, 0, 0.5);
        border-radius:8px;
    }

    /* Body background colour */
    .editor-styles-wrapper {
        // background-color: {$bg_color};
        background-color: var(--wp-admin-theme-color);
    }

    .editor-document-bar {
        display:none;
    }

    /* Post title — always readable regardless of email background */
    .editor-styles-wrapper .wp-block-post-title,
    .editor-styles-wrapper .editor-post-title__block,
    .editor-styles-wrapper .editor-post-title__input,
    .edit-post-visual-editor__post-title-wrapper {
        color: {$editor_text_color};
        margin-bottom:40px;
    }

    /* Header block typography */
    .editor-styles-wrapper .eb-header-block,
    .editor-styles-wrapper .eb-header-block div[contenteditable] {
        font-family: {$heading_font_stack};
        font-size: {$heading_size}px;
        font-weight: {$heading_font_weight};
        line-height: {$heading_line_height};
    }

    /* Subheader block typography */
    .editor-styles-wrapper .eb-subheader-block,
    .editor-styles-wrapper .eb-subheader-block div[contenteditable] {
        font-family: {$subheading_font_stack};
        font-size: {$subheading_size}px;
        font-weight: {$subheading_font_weight};
        line-height: {$subheading_line_height};
    }

    /* Text block typography */
    .editor-styles-wrapper .eb-text-block,
    .editor-styles-wrapper .eb-text-block div[contenteditable] {
        font-family: {$body_font_stack};
        font-size: {$body_size}px;
        font-weight: {$body_font_weight};
        line-height: {$body_line_height};
    }

    /* Button block typography */
    .editor-styles-wrapper .eb-button-link {
        font-family: {$button_font_stack};
        font-size: {$button_size}px;
        font-weight: {$button_font_weight};
    }

    /* Footer block typography */
    .editor-styles-wrapper .eb-footer-block {
        font-family: {$body_font_stack};
    }

    /* Remove default block margins that distort layout */
    .editor-styles-wrapper .wp-block {
        margin-top: 0;
        margin-bottom: 0;
    }

    /* Mobile preview font sizes — class toggled by JS when device type = Mobile */
    .editor-styles-wrapper.eb-device-mobile .eb-header-block,
    .editor-styles-wrapper.eb-device-mobile .eb-header-block div[contenteditable] {
        font-size: {$heading_size_mobile}px;
    }
    .editor-styles-wrapper.eb-device-mobile .eb-subheader-block,
    .editor-styles-wrapper.eb-device-mobile .eb-subheader-block div[contenteditable] {
        font-size: {$subheading_size_mobile}px;
    }
    .editor-styles-wrapper.eb-device-mobile .eb-text-block,
    .editor-styles-wrapper.eb-device-mobile .eb-text-block div[contenteditable] {
        font-size: {$body_size_mobile}px;
    }
    .editor-styles-wrapper.eb-device-mobile .eb-button-link {
        font-size: {$button_size_mobile}px;
    }
";

    // Suppress the block inserter hover preview panel — not useful for email blocks
    $editor_css .= "
    .block-editor-inserter__preview-container {
        display: none !important;
    }
";

    wp_add_inline_style('wp-edit-blocks', $editor_css);
});


// ──────────────────────────────────────────────────────────────────
// Shared preview shell renderer
// ──────────────────────────────────────────────────────────────────
function eb_render_preview_shell($post_id, $raw_url) {
    $post_title = esc_html(get_the_title($post_id));
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Preview: <?php echo $post_title; ?></title>
    <style>
        *, *::before, *::after { box-sizing: border-box; }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            display: flex;
            flex-direction: column;
            background: #e8e8e8;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            overflow: hidden;
        }

        .eb-preview-bar {
            flex: 0 0 auto;
            z-index: 100;
            background: #1e1e1e;
            color: #fff;
            padding: 10px 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            font-size: 13px;
        }

        .eb-preview-bar__title {
            font-weight: 600;
            opacity: 0.9;
            flex: 1;
        }

        .eb-preview-bar__close {
            background: none;
            border: 1px solid rgba(255,255,255,0.3);
            color: #fff;
            padding: 4px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }

        .eb-preview-bar__close:hover {
            background: rgba(255,255,255,0.1);
        }

        .eb-preview-bar__dark {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #333;
            border: 1px solid #555;
            border-radius: 6px;
            padding: 5px 12px;
            color: #fff;
            font-size: 12px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .eb-preview-bar__dark:hover {
            background: #444;
        }

        .eb-preview-bar__dark.active {
            background: #2271b1;
            border-color: #2271b1;
        }

        .eb-preview-shell {
            flex: 1;
            min-height: 0;
            display: flex;
            gap: 48px;
            padding: 32px 40px;
            align-items: flex-start;
            justify-content: center;
            overflow: hidden;
        }

        /* Desktop panel */
        .eb-preview-desktop {
            flex: 1;
            max-width: 900px;
            min-width: 0;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .eb-preview-label {
            flex: 0 0 auto;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #888;
            text-align: center;
            margin-bottom: 10px;
        }

        .eb-preview-desktop__frame {
            flex: 1;
            min-height: 500px;
            background: #fff;
            box-shadow: 0 8px 32px rgba(0,0,0,0.15);
            overflow: hidden;
            border-radius: 10px;
        }

        .eb-dark-mode-active .eb-preview-desktop__frame {
            background-color: #000;
        }

        .eb-preview-desktop__frame iframe {
            display: block;
            width: 100%;
            height: 100%;
            border: none;
        }

        /* Mobile panel */
        .eb-preview-mobile {
            flex: 0 0 auto;
        }

        .eb-preview-mobile__bezel {
            background: #1a1a1a;
            border-radius: 48px;
            padding: 14px 10px;
            box-shadow:
                0 0 0 1px #333,
                0 12px 40px rgba(0,0,0,0.35);
            width: 376px;
            height: 680px;
            display: flex;
            flex-direction: column;
        }

        .eb-preview-mobile__notch {
            flex: 0 0 auto;
            width: 90px;
            height: 22px;
            background: #1a1a1a;
            border-radius: 0 0 14px 14px;
            margin: 0 auto 10px;
        }

        .eb-preview-mobile__screen {
            flex: 1;
            min-height: 0;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
        }

        .eb-dark-mode-active .eb-preview-mobile__screen {
            background-color: #000;
        }

        .eb-preview-mobile__screen iframe {
            display: block;
            width: 356px;
            height: 100%;
            border: none;
        }

        .eb-preview-mobile__home {
            flex: 0 0 auto;
            width: 100px;
            height: 4px;
            background: #444;
            border-radius: 3px;
            margin: 10px auto 0;
        }

        /* On narrow viewports, hide the desktop panel */
        @media (max-width: 1080px) {
            .eb-preview-desktop {
                display: none;
            }
            .eb-preview-shell {
                padding: 24px 20px;
                justify-content: center;
            }
        }
    </style>
</head>
<body>

<div class="eb-preview-bar">
    <span class="eb-preview-bar__title">Preview: <?php echo $post_title; ?></span>
    <button class="eb-preview-bar__dark" id="eb-dark-toggle" onclick="toggleDarkMode()">
        <span id="eb-dark-icon">🌙</span>
        <span id="eb-dark-label">Dark Mode</span>
    </button>
    <button class="eb-preview-bar__close" onclick="window.close()">Close</button>
</div>

<div class="eb-preview-shell">

    <div class="eb-preview-desktop">
        <div class="eb-preview-label">Desktop</div>
        <div class="eb-preview-desktop__frame">
            <iframe id="eb-iframe-desktop" src="<?php echo $raw_url; ?>" scrolling="yes"></iframe>
        </div>
    </div>

    <div class="eb-preview-mobile">
        <div class="eb-preview-label">Mobile</div>
        <div class="eb-preview-mobile__bezel">
            <div class="eb-preview-mobile__notch"></div>
            <div class="eb-preview-mobile__screen">
                <iframe id="eb-iframe-mobile" src="<?php echo $raw_url; ?>" scrolling="yes"></iframe>
            </div>
            <div class="eb-preview-mobile__home"></div>
        </div>
    </div>

</div>


<script>
    function toggleDarkMode() {
        var btn    = document.getElementById('eb-dark-toggle');
        var icon   = document.getElementById('eb-dark-icon');
        var label  = document.getElementById('eb-dark-label');
        var iframes = [
            document.getElementById('eb-iframe-desktop'),
            document.getElementById('eb-iframe-mobile'),
        ];

        var isDark = btn.classList.toggle('active');

        document.body.classList.toggle('eb-dark-mode-active', isDark);

        iframes.forEach(function (iframe) {
            try {
                iframe.contentDocument.body.classList.toggle('eb-simulated-dark', isDark);
            } catch (e) {}
        });

        icon.textContent  = isDark ? '☀️' : '🌙';
        label.textContent = isDark ? 'Light Mode' : 'Dark Mode';
    }
</script>

</body>
</html>
    <?php
} // end eb_render_preview_shell()

// ──────────────────────────────────────────────────────────────────
// Logged-in preview: ?eb_preview=ID
// ──────────────────────────────────────────────────────────────────
add_action('template_redirect', function () {
    if (!isset($_GET['eb_preview'])) return;

    $post_id = intval($_GET['eb_preview']);
    if (!$post_id) wp_die('Invalid preview');
    if (!current_user_can('edit_post', $post_id)) wp_die('You do not have permission to preview this email.');

    $admin = new EB_Admin();
    $html  = $admin->export_email($post_id);
    if (!$html) wp_die('Could not generate preview');

    if (!empty($_GET['eb_raw'])) {
        echo $html; // phpcs:ignore WordPress.Security.EscapeOutput
        exit;
    }

    $raw_url = esc_url(add_query_arg(['eb_preview' => $post_id, 'eb_raw' => '1'], home_url('/')));
    eb_render_preview_shell($post_id, $raw_url);
    exit;
});

// ──────────────────────────────────────────────────────────────────
// Public share preview: ?eb_share=ID&token=TOKEN
// ──────────────────────────────────────────────────────────────────
add_action('template_redirect', function () {
    $post_id = intval($_GET['eb_share'] ?? 0);
    $token   = sanitize_text_field($_GET['token'] ?? '');
    if (!$post_id || !$token) return;

    $stored = get_post_meta($post_id, 'eb_share_token', true);
    if (!$stored || !hash_equals($stored, $token)) {
        status_header(403);
        wp_die('This preview link is invalid or has been revoked.', 403);
    }

    $admin = new EB_Admin();
    $html  = $admin->export_email($post_id);
    if (!$html) { status_header(500); wp_die('Could not generate preview.'); }

    if (!empty($_GET['eb_raw'])) {
        header('Content-Type: text/html; charset=utf-8');
        echo $html; // phpcs:ignore WordPress.Security.EscapeOutput
        exit;
    }

    $raw_url = esc_url(add_query_arg(['eb_share' => $post_id, 'token' => $token, 'eb_raw' => '1'], home_url('/')));
    eb_render_preview_shell($post_id, $raw_url);
    exit;
});


add_filter('use_block_editor_for_post_type', function($use_block_editor, $post_type) {
    if ($post_type === 'eb_email_template') {
        return true;
    }
    return $use_block_editor;
}, 99999999, 2);