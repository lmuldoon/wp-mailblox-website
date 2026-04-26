<?php
/**
 * Plugin Name:       WP Mailblox
 * Plugin URI:        https://TODO-your-website.com/wp-mailblox
 * Description:       Build and export HTML emails from WordPress using a Gutenberg-based email editor.
 * Version:           1.0.0
 * Author:            TODO: Your Name or Company
 * Author URI:        https://wpmailblox.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wp-mailblox
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Tested up to:      6.7
 * Requires PHP:      7.4
 *
 * @fs_premium_only /blocks/woocommerce/, /blocks/html/
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( function_exists( 'wp_mailblox_fs' ) ) {
    wp_mailblox_fs()->set_basename( true, __FILE__ );
} else {

    if ( ! function_exists( 'wp_mailblox_fs' ) ) {
        function wp_mailblox_fs() {
            global $wp_mailblox_fs;
            if ( ! isset( $wp_mailblox_fs ) ) {
                require_once dirname( __FILE__ ) . '/vendor/freemius/start.php';
                $wp_mailblox_fs = fs_dynamic_init( array(
                    'id'                  => '27104',
                    'slug'                => 'wp-mailblox',
                    'type'                => 'plugin',
                    'public_key'          => 'pk_fbd2ede93fb1cbfae4808e8582b0a',
                    'is_premium'          => true,
                    'premium_suffix'      => 'Pro',
                    'has_premium_version' => true,
                    'has_addons'          => false,
                    'has_paid_plans'      => true,
                    'is_org_compliant'    => true,
                    'wp_org_gatekeeper'   => 'OA7#BoRiBNqdf52FvzEf!!074aRLPs8fspif$7K1#4u4Csys1fQlCecVcUTOs2mcpeVHi#C2j9d09fOTvbC0HloPT7fFee5WdS3G',
                    'menu'                => array(
                        'slug'       => 'eb_settings',
                        'first-path' => 'admin.php?page=eb_onboarding',
                    ),
                ) );
            }
            return $wp_mailblox_fs;
        }
        wp_mailblox_fs();
        do_action( 'wp_mailblox_fs_loaded' );

        // Cleanup on uninstall — hooked via Freemius so uninstall feedback is captured.
        wp_mailblox_fs()->add_action( 'after_uninstall', 'wp_mailblox_fs_uninstall_cleanup' );
    }

    function wp_mailblox_fs_uninstall_cleanup() {
        // Delete all plugin options
        $options = [
            'eb_onboarding_complete',
            'eb_onboarding_started',
            'eb_api_mailchimp_key',
            'eb_api_brevo_key',
            'eb_api_brevo_sender_name',
            'eb_api_brevo_sender_email',
            'eb_api_klaviyo_key',
            'eb_api_campaign_monitor_key',
            'eb_api_campaign_monitor_client_id',
            'eb_api_activecampaign_key',
            'eb_api_activecampaign_url',
        ];
        foreach ( $options as $option ) {
            delete_option( $option );
        }

        // Delete all plugin posts and their meta
        $post_types = [ 'eb_email_template', 'eb_preset', 'eb_saved_template' ];
        foreach ( $post_types as $post_type ) {
            $posts = get_posts( [
                'post_type'      => $post_type,
                'post_status'    => 'any',
                'posts_per_page' => -1,
                'fields'         => 'ids',
            ] );
            foreach ( $posts as $post_id ) {
                wp_delete_post( $post_id, true );
            }
        }
    }

    define( 'EB_VERSION',     '1.0.0' );
    define( 'EB_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
    define( 'EB_PLUGIN_URL',  plugin_dir_url( __FILE__ ) );

    require_once EB_PLUGIN_PATH . 'includes/class-loader.php';
    require_once EB_PLUGIN_PATH . 'includes/class-block-loader.php';
    require_once EB_PLUGIN_PATH . 'includes/class-plugin.php';

    $eb_plugin = new EB_Plugin();
    $eb_plugin->run();
}
