<?php
// /includes/class-plugin.php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'EB_Plugin' ) ) :

class EB_Plugin {

    protected $loader;

    // ⚡ Declare all components as properties
    private EB_CPT $cpt;
    private EB_Meta $meta;
    private EB_Block_Restrict $blocks;
    private EB_Admin $admin;
    private EB_Block_Loader $block_loader;
    private EB_WooCommerce  $woocommerce;

    public function __construct() {
        $this->loader = new EB_Loader();
        $this->load_dependencies();
        $this->init_components();
        $this->define_hooks(); // hooks only; block loader handles blocks
    }

    private function load_dependencies() {
        require_once EB_PLUGIN_PATH . 'includes/class-cpt.php';
        require_once EB_PLUGIN_PATH . 'includes/class-meta.php';
        require_once EB_PLUGIN_PATH . 'includes/class-block-restrict.php';
        require_once EB_PLUGIN_PATH . 'admin/class-admin.php';
        require_once EB_PLUGIN_PATH . 'includes/class-block-loader.php';
        require_once EB_PLUGIN_PATH . 'includes/class-woocommerce.php';
        require_once EB_PLUGIN_PATH . 'includes/class-platform-api.php';
        require_once EB_PLUGIN_PATH . 'includes/helpers/render.php';
        require_once EB_PLUGIN_PATH . 'includes/helpers/font-colours.php';
        require_once EB_PLUGIN_PATH . 'includes/helpers/pro.php';
    }

    private function init_components() {
        $this->cpt    = new EB_CPT();
        $this->meta   = new EB_Meta();
        $this->blocks = new EB_Block_Restrict();
        $this->admin  = new EB_Admin();
        $this->loader->add_action('wp_ajax_eb_export_email',    $this->admin, 'ajax_export_email');
        $this->loader->add_action('wp_ajax_eb_export_preset',   $this->admin, 'ajax_export_preset');
        $this->loader->add_action('wp_ajax_eb_import_preset',   $this->admin, 'ajax_import_preset');
        $this->loader->add_action('wp_ajax_eb_export_template', $this->admin, 'ajax_export_template');
        $this->loader->add_action('wp_ajax_eb_push_to_platform', $this->admin, 'ajax_push_to_platform');
        $this->loader->add_action('wp_ajax_eb_share_token',     $this->admin, 'ajax_share_token');

        $this->block_loader = new EB_Block_Loader();
        add_action('init', function () {
            $this->woocommerce = new EB_WooCommerce();
        });
    }

    private function define_hooks() {
        // Campaign Monitor HTML endpoint — serves a signed one-time public URL
        // so Campaign Monitor's servers can fetch the rendered email HTML.
        add_action('template_redirect', function () {
            $post_id = intval($_GET['eb_cm_html'] ?? 0);
            $token   = sanitize_text_field($_GET['token'] ?? '');

            if (!$post_id || !$token) return;

            $stored = get_transient('eb_cm_token_' . $post_id);

            if (!$stored || !hash_equals($stored, $token)) {
                status_header(403);
                wp_die('Invalid or expired token.', 403);
            }

            delete_transient('eb_cm_token_' . $post_id);

            $html = $this->admin->export_email($post_id, 'campaign_monitor');

            if (empty($html)) {
                status_header(500);
                wp_die('Could not render email HTML.', 500);
            }

            header('Content-Type: text/html; charset=utf-8');
            // Prevent caching — this URL is single-use
            header('Cache-Control: no-store, no-cache, must-revalidate');
            header('Pragma: no-cache');

            // Campaign Monitor requires an editable region AND an unsubscribe link outside it.
            // The footer block outputs <!-- eb-footer-start --> so we can close <multiline>
            // just before the footer, keeping the <unsubscribe> tag in the fixed template area.
            $html = preg_replace('/(<body[^>]*>)/i', '$1<multiline label="Email Content" editable="true">', $html, 1);
            if (strpos($html, '<!-- eb-footer-start -->') !== false) {
                $html = str_replace('<!-- eb-footer-start -->', '</multiline><!-- eb-footer-start -->', $html);
            } else {
                $html = preg_replace('/<\/body>/i', '</multiline></body>', $html, 1);
            }


            echo $html; // phpcs:ignore WordPress.Security.EscapeOutput
            exit;
        });

        $this->loader->add_action('admin_menu', $this->admin, 'register_menu');
        $this->loader->add_action('admin_init', $this->admin, 'register_settings');
        $this->loader->add_action('init', $this->cpt, 'register');
        $this->loader->add_action('init', $this->meta, 'register');
        $this->loader->add_filter('allowed_block_types_all', $this->blocks, 'restrict_blocks', 10, 2);
        $this->loader->add_action('admin_notices', $this->admin, 'maybe_show_woocommerce_notice');
        $this->loader->add_action('admin_notices', $this->admin, 'maybe_show_no_preset_notice');
        $this->loader->add_action('admin_notices', $this->admin, 'maybe_show_limit_notice');
        $this->loader->add_action('admin_notices', $this->admin, 'maybe_show_preset_deletion_notice');
        $this->loader->add_action('wp_trash_post', $this->admin, 'guard_preset_trash');
        $this->loader->add_filter('pre_delete_post', $this->admin, 'guard_preset_deletion', 10, 3);
        $this->loader->add_action('save_post',     $this->admin, 'enforce_post_limit', 10, 3);
        $this->loader->add_action('add_meta_boxes', $this->admin, 'add_meta_boxes');
        $this->loader->add_action('save_post', $this->admin, 'save_meta');
        $this->loader->add_action('save_post_eb_email_template', $this->admin, 'set_default_preset', 10, 2);
        $this->loader->add_action('rest_api_init',    $this->admin, 'register_template_rest_route');
        $this->loader->add_action('rest_api_init',    $this->admin, 'register_module_rest_routes');

        // Register email template meta fields for the Gutenberg REST API / PluginSidebar
        add_action('init', function () {
            $string_meta = [
                'eb_platform', 'eb_subject', 'eb_preheader', 'eb_dark_bg_color_override',
                'eb_utm_source', 'eb_utm_medium', 'eb_utm_campaign', 'eb_utm_content', 'eb_utm_term',
            ];
            foreach ($string_meta as $key) {
                register_post_meta('eb_email_template', $key, [
                    'type'          => 'string',
                    'single'        => true,
                    'show_in_rest'  => true,
                    'auth_callback' => '__return_true',
                ]);
            }
            register_post_meta('eb_email_template', 'eb_preset', [
                'type'          => 'integer',
                'single'        => true,
                'show_in_rest'  => true,
                'auth_callback' => '__return_true',
            ]);
        });
        $this->loader->add_action('wp_ajax_eb_onboarding_save', $this->admin, 'ajax_onboarding_save');

        // Skip onboarding link handler
        add_action('admin_init', function () {
            if (!empty($_GET['eb_skip_onboarding']) && current_user_can('edit_posts')) {
                update_option('eb_onboarding_complete', 1);
                wp_redirect(admin_url('admin.php?page=eb_settings'));
                exit;
            }
        });

        // Redirect to onboarding on first activation
        add_action('admin_init', function () {
            if (!get_option('eb_onboarding_complete') && !get_option('eb_onboarding_started')) {
                // Don't redirect during AJAX, bulk actions, or if already on onboarding
                if (
                    wp_doing_ajax() ||
                    !empty($_GET['action']) ||
                    (isset($_GET['page']) && $_GET['page'] === 'eb_onboarding')
                ) return;
                update_option('eb_onboarding_started', 1);
                wp_redirect(admin_url('admin.php?page=eb_onboarding'));
                exit;
            }
        });

        // Block "Add New" screen when free-plan limit is reached
        add_action('load-post-new.php', function () {
            $type = sanitize_key($_GET['post_type'] ?? '');
            if (!in_array($type, ['eb_email_template', 'eb_preset'], true)) return;
            $limit = eb_get_post_limit($type);
            if ($limit === PHP_INT_MAX) return;
            if (eb_count_active_posts($type) >= $limit) {
                wp_redirect(add_query_arg(
                    ['eb_limit_reached' => $type],
                    admin_url('edit.php?post_type=' . $type)
                ));
                exit;
            }
        });
    }

    public function run() {
        $this->loader->run();
    }
}

endif;