<?php
if (!defined('ABSPATH')) exit;

/**
 * EB_Block_Loader
 * Automatically includes block PHP, JS, and CSS files.
 */
class EB_Block_Loader
{

    protected $blocks_path;
    protected $blocks_url;

    public function __construct()
    {
        $this->blocks_path = EB_PLUGIN_PATH . 'blocks/';
        $this->blocks_url = EB_PLUGIN_URL . 'blocks/';

        $this->load_blocks();
        add_filter('block_categories_all', [$this, 'register_category'], 10, 2);
        add_action('enqueue_block_editor_assets', [$this, 'enqueue_block_assets']);
        add_action('enqueue_block_assets', [$this, 'enqueue_frontend_assets']);
    }

    public function register_category($categories)
    {
        $custom = [
            [
                'slug'  => 'email-builder',
                'title' => 'Email Builder',
                'icon'  => 'email',
            ],
            [
                'slug'  => 'email-builder-woocommerce',
                'title' => 'WooCommerce',
                'icon'  => 'cart',
            ],
        ];

        return array_merge($custom, $categories);
    }

    private function load_blocks()
    {
        $dirs = array_merge(
            glob($this->blocks_path . '*', GLOB_ONLYDIR),
            glob($this->blocks_path . 'woocommerce/*', GLOB_ONLYDIR)
        );
        foreach ($dirs as $dir) {
            $index = $dir . '/index.php';
            if (file_exists($index)) require_once $index;
        }
    }

    public function enqueue_block_assets()
    {

        $chooser_file = EB_PLUGIN_PATH . 'blocks/template-chooser.js';
        $chooser_src  = EB_PLUGIN_URL . 'blocks/template-chooser.js';

        if (file_exists($chooser_file)) {
            wp_enqueue_script(
                'eb-template-chooser',
                $chooser_src,
                ['wp-plugins', 'wp-element', 'wp-components', 'wp-data', 'wp-blocks', 'wp-api-fetch'],
                EB_VERSION,
                true
            );
        }

        $editor_sync_file = EB_PLUGIN_PATH . 'blocks/editor-sync.js';
        $editor_sync_src  = EB_PLUGIN_URL . 'blocks/editor-sync.js';

        if (file_exists($editor_sync_file)) {
            wp_enqueue_script(
                'eb-editor-sync',
                $editor_sync_src,
                ['wp-data', 'wp-api-fetch', 'jquery'],
                EB_VERSION,
                true
            );
        }

        $platform_switcher_file = EB_PLUGIN_PATH . 'blocks/platform-switcher.js';
        $platform_switcher_src  = EB_PLUGIN_URL . 'blocks/platform-switcher.js';

        if (file_exists($platform_switcher_file)) {
            wp_enqueue_script(
                'eb-platform-switcher',
                $platform_switcher_src,
                ['wp-data', 'jquery'],
                EB_VERSION,
                true
            );
        }

        // ✅ Enqueue global blocks script (category, shared logic)
        $global_js = $this->blocks_path . 'blocks.js';
        if (file_exists($global_js)) {
            wp_enqueue_script(
                'eb-blocks-global',
                $this->blocks_url . 'blocks.js',
                ['wp-blocks', 'wp-hooks'],
                EB_VERSION,
                true
            );
        }

        // Save as Template button
        $save_template_js = $this->blocks_path . 'save-template.js';
        if (file_exists($save_template_js)) {
            wp_enqueue_script(
                'eb-save-template',
                $this->blocks_url . 'save-template.js',
                ['wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-data', 'wp-api-fetch'],
                EB_VERSION,
                true
            );
        }

        // Email Settings Sidebar + Toolbar Actions
        $settings_sidebar_js = EB_PLUGIN_PATH . 'assets/js/email-settings-sidebar.js';
        if (file_exists($settings_sidebar_js)) {
            wp_enqueue_script(
                'eb-email-settings-sidebar',
                EB_PLUGIN_URL . 'assets/js/email-settings-sidebar.js',
                ['wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-data', 'wp-api-fetch', 'wp-blocks', 'eb-save-template'],
                EB_VERSION,
                true
            );
        }

        // 🔁 Per-block loop (top-level + woocommerce subdirectory)
        $block_dirs = [];
        foreach (glob($this->blocks_path . '*', GLOB_ONLYDIR) as $dir) {
            $block_dirs[] = ['dir' => $dir, 'handle_prefix' => 'eb-block-', 'url_base' => $this->blocks_url];
        }
        // This block will be auto-removed from the free version by Freemius.
        if ( function_exists('wp_mailblox_fs') && wp_mailblox_fs()->is__premium_only() ) {
            if ( eb_is_pro() && class_exists('WooCommerce') ) {
                foreach (glob($this->blocks_path . 'woocommerce/*', GLOB_ONLYDIR) as $dir) {
                    $block_dirs[] = ['dir' => $dir, 'handle_prefix' => 'eb-block-wc-', 'url_base' => $this->blocks_url . 'woocommerce/'];
                }
            }
        }

        foreach ($block_dirs as $entry) {
            $dir           = $entry['dir'];
            $handle_prefix = $entry['handle_prefix'];
            $url_base      = $entry['url_base'];
            $name          = basename($dir);

            $js = $dir . '/block.js';
            if (file_exists($js)) {
                wp_enqueue_script(
                    $handle_prefix . $name,
                    $url_base . $name . '/block.js',
                    ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-hooks'],
                    EB_VERSION,
                    true
                );
            }

            $css = $dir . '/editor.css';
            if (file_exists($css)) {
                wp_enqueue_style(
                    $handle_prefix . 'editor-' . $name,
                    $url_base . $name . '/editor.css',
                    [],
                    EB_VERSION
                );
            }
        }
    }

    public function enqueue_frontend_assets()
    {
        $all_dirs = array_merge(
            glob($this->blocks_path . '*', GLOB_ONLYDIR),
            glob($this->blocks_path . 'woocommerce/*', GLOB_ONLYDIR)
        );
        foreach ($all_dirs as $dir) {
            $css = $dir . '/style.css';
            if (file_exists($css)) {
                wp_enqueue_style(
                    'eb-block-frontend-' . basename($dir),
                    $this->blocks_url . basename($dir) . '/style.css',
                    [],
                    EB_VERSION
                );
            }
        }
    }
}
