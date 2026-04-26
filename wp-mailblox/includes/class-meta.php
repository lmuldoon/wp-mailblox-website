<?php
// /includes/class-meta.php
if (!defined('ABSPATH')) exit;

class EB_Meta
{

    public function register()
    {

        /*
        |--------------------------------------------------------------------------
        | EMAIL TEMPLATE — PLATFORM PUSH IDs
        |--------------------------------------------------------------------------
        */

        foreach (['eb_mailchimp_template_id', 'eb_brevo_template_id', 'eb_klaviyo_template_id', 'eb_campaign_monitor_template_id', 'eb_hubspot_email_id'] as $key) {
            register_post_meta('eb_email_template', $key, [
                'show_in_rest' => false,
                'single'       => true,
                'type'         => 'string',
                'default'      => '',
            ]);
        }

        register_post_meta('eb_email_template', 'eb_last_pushed', [
            'show_in_rest' => false,
            'single'       => true,
            'type'         => 'string',
            'default'      => '',
        ]);

        register_post_meta('eb_email_template', 'eb_last_pushed_platform', [
            'show_in_rest' => false,
            'single'       => true,
            'type'         => 'string',
            'default'      => '',
        ]);

        /*
        |--------------------------------------------------------------------------
        | EMAIL TEMPLATE — UTM DEFAULTS
        |--------------------------------------------------------------------------
        */

        foreach (['eb_utm_source', 'eb_utm_medium', 'eb_utm_campaign', 'eb_utm_content', 'eb_utm_term'] as $key) {
            register_post_meta('eb_email_template', $key, [
                'show_in_rest' => true,
                'single'       => true,
                'type'         => 'string',
                'default'      => '',
            ]);
        }

        // Platform selection
        register_post_meta('eb_email_template', 'eb_platform', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
            'default'      => 'mailchimp',
        ]);

        /*
        |--------------------------------------------------------------------------
        | HEADING TYPOGRAPHY
        |--------------------------------------------------------------------------
        */

        register_post_meta('eb_preset', 'eb_heading_font', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
            'default'      => 'Arial',
        ]);

        register_post_meta('eb_preset', 'eb_heading_font_weight', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
            'default'      => '700',
        ]);

        register_post_meta('eb_preset', 'eb_heading_size', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'number',
            'default'      => 24,
        ]);

        register_post_meta('eb_preset', 'eb_heading_size_mobile', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'number',
            'default'      => 20,
        ]);

        register_post_meta('eb_preset', 'eb_heading_font_type', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
            'default'      => 'websafe',
        ]);

        register_post_meta('eb_preset', 'eb_heading_font_stack', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
            'default'      => 'Arial, Helvetica, sans-serif',
        ]);

        register_post_meta('eb_preset', 'eb_heading_line_height', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'number',
            'default'      => 1.3,
        ]);

        /*
        |--------------------------------------------------------------------------
        | SUBHEADING TYPOGRAPHY
        |--------------------------------------------------------------------------
        */

        register_post_meta('eb_preset', 'eb_subheading_font', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
            'default'      => 'Arial',
        ]);

        register_post_meta('eb_preset', 'eb_subheading_font_weight', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
            'default'      => '700',
        ]);

        register_post_meta('eb_preset', 'eb_subheading_size', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'number',
            'default'      => 24,
        ]);

        register_post_meta('eb_preset', 'eb_subheading_size_mobile', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'number',
            'default'      => 20,
        ]);

        register_post_meta('eb_preset', 'eb_subheading_font_type', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
            'default'      => 'websafe',
        ]);

        register_post_meta('eb_preset', 'eb_subheading_font_stack', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
            'default'      => 'Arial, Helvetica, sans-serif',
        ]);

        register_post_meta('eb_preset', 'eb_subheading_line_height', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'number',
            'default'      => 1.3,
        ]);

        /*
        |--------------------------------------------------------------------------
        | BODY TYPOGRAPHY
        |--------------------------------------------------------------------------
        */

        register_post_meta('eb_preset', 'eb_body_font', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
            'default'      => 'Helvetica',
        ]);

        register_post_meta('eb_preset', 'eb_body_font_weight', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
            'default'      => '400',
        ]);

        register_post_meta('eb_preset', 'eb_body_size', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'number',
            'default'      => 16,
        ]);

        register_post_meta('eb_preset', 'eb_body_size_mobile', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'number',
            'default'      => 14,
        ]);

        register_post_meta('eb_preset', 'eb_body_font_type', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
            'default'      => 'websafe',
        ]);

        register_post_meta('eb_preset', 'eb_body_font_stack', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
            'default'      => 'Helvetica, Arial, sans-serif',
        ]);

        register_post_meta('eb_preset', 'eb_body_line_height', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'number',
            'default'      => 1.5,
        ]);

        /*
        |--------------------------------------------------------------------------
        | BODY BACKGROUND
        |--------------------------------------------------------------------------
        */

        register_post_meta('eb_preset', 'eb_body_bg_color', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
            'default'      => '#ffffff',
        ]);
        register_post_meta('eb_preset', 'eb_body_bg_image_enabled', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'boolean',
            'default'      => false,
        ]);
        register_post_meta('eb_preset', 'eb_body_bg_image_url', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
            'default'      => '',
        ]);
        register_post_meta('eb_preset', 'eb_body_bg_image_repeat', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
            'default'      => 'no-repeat',
        ]);
        register_post_meta('eb_preset', 'eb_body_bg_image_position_x', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
            'default'      => 'center',
        ]);
        register_post_meta('eb_preset', 'eb_body_bg_image_position_y', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
            'default'      => 'center',
        ]);
        register_post_meta('eb_preset', 'eb_body_bg_image_size_w', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
            'default'      => 'cover',
        ]);

        /*
        |--------------------------------------------------------------------------
        | TEXT COLOURS
        |--------------------------------------------------------------------------
        */

        register_post_meta('eb_preset', 'eb_text_color_dark', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
            'default'      => '#000000',
        ]);

        register_post_meta('eb_preset', 'eb_text_color_light', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
            'default'      => '#ffffff',
        ]);

        /*
        |--------------------------------------------------------------------------
        | DARK MODE — PRESET
        |--------------------------------------------------------------------------
        */

        // Body background in dark mode
        register_post_meta('eb_preset', 'eb_dark_bg_color', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
            'default'      => '#121212',
        ]);

        // Text colour in dark mode (all text)
        register_post_meta('eb_preset', 'eb_dark_text_color', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
            'default'      => '#ffffff',
        ]);

        // Button background in dark mode
        register_post_meta('eb_preset', 'eb_dark_button_color', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
            'default'      => '#ffffff',
        ]);

        // Button text in dark mode
        register_post_meta('eb_preset', 'eb_dark_button_text_color', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
            'default'      => '#000000',
        ]);

        // Link colour in dark mode
        register_post_meta('eb_preset', 'eb_dark_link_color', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
            'default'      => '#ffffff',
        ]);

        register_post_meta('eb_preset', 'eb_logo_url', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
            'default'      => '',
        ]);

        register_post_meta('eb_preset', 'eb_logo_alt', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
            'default'      => '',
        ]);

        /*
        |--------------------------------------------------------------------------
        | DARK MODE — EMAIL TEMPLATE OVERRIDE
        |--------------------------------------------------------------------------
        */

        // Per-email dark mode background override
        register_post_meta('eb_email_template', 'eb_dark_bg_color_override', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'string',
            'default'      => '',
        ]);

        /*
        |--------------------------------------------------------------------------
        | EMAIL TEMPLATE — PRESET SELECTION
        |--------------------------------------------------------------------------
        */

        register_post_meta('eb_email_template', 'eb_preset', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'integer',
            'default'      => 0,
        ]);

        register_post_meta('eb_preset', 'eb_container_width', [
            'show_in_rest' => true,
            'single'       => true,
            'type'         => 'integer',
            'default'      => 600,
        ]);
    }
}
