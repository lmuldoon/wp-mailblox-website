<?php
if (!defined('ABSPATH')) exit;

class EB_Block_Restrict
{

    public function restrict_blocks($allowed_blocks, $context)
    {

        // Only restrict for email templates
        if (!isset($context->post) || $context->post->post_type !== 'eb_email_template') {
            return $allowed_blocks;
        }

        // Allowed blocks for email templates
        $allowed = [
            'email-builder/logo',
            'email-builder/menu',
            'email-builder/section',
            'email-builder/header',
            'email-builder/subheader',
            'email-builder/text',
            'email-builder/image',
            'email-builder/button',
            'email-builder/spacer',
            'email-builder/divider',
            'email-builder/columns',
            'email-builder/column',
            'email-builder/social',
            'email-builder/footer',
            'email-builder/video',
            'email-builder/module',
            'email-builder/conditional',
        ];

        // This block will be auto-removed from the free version by Freemius.
        if ( function_exists('wp_mailblox_fs') && wp_mailblox_fs()->is__premium_only() ) {
            if ( eb_is_pro() ) {
                $allowed[] = 'email-builder/html';
                $allowed[] = 'email-builder/wc-product';
                $allowed[] = 'email-builder/wc-order-table';
                $allowed[] = 'email-builder/wc-coupon';
            }
        }

        return $allowed;
    }

    // Optional: restrict settings for certain blocks
    // public function restrict_block_settings($settings, $block)
    // {
    //     // Example: prevent user from changing columns in your custom column block
    //     if ($block['name'] === 'email-builder/columns') {
    //         $settings['allowedColumns'] = [1, 2];
    //     }
    //     return $settings;
    // }
}
