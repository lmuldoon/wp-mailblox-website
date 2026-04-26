<?php
// /includes/class-cpt.php
if (!defined('ABSPATH')) exit;

class EB_CPT {

    public function register() {

        /**
         * Existing CPT: Email Templates
         */
        $labels_templates = [
            'name' => 'Email Templates',
            'singular_name' => 'Email Template',
            'add_new_item' => 'Add New Email Template',
            'edit_item' => 'Edit Email Template',
            'new_item' => 'New Email Template',
            'view_item' => 'View Email Template',
            'menu_name' => 'Email Templates',
        ];

        $args_templates = [
            'labels' => $labels_templates,
            'public' => false,
            'show_ui' => true,

            // Keep it under your plugin menu
            'show_in_menu' => false,

            'menu_icon' => 'dashicons-email',
            'supports' => ['title', 'editor'],
            'show_in_rest' => true,
        ];

        register_post_type('eb_email_template', $args_templates);


        /**
         * NEW CPT: Email Presets
         */
        $labels_presets = [
            'name' => 'Email Presets',
            'singular_name' => 'Email Preset',
            'add_new_item' => 'Add New Email Preset',
            'edit_item' => 'Edit Email Preset',
            'new_item' => 'New Email Preset',
            'view_item' => 'View Email Preset',
            'menu_name' => 'Email Presets',
        ];

        $args_presets = [
            'labels' => $labels_presets,
            'public' => false,
            'show_ui' => true,

            // This makes it appear as a submenu under your plugin menu
            'show_in_menu' => false,
            'show_in_rest' => true,
            'menu_icon'    => 'dashicons-admin-page',
            'supports'     => ['title'],
        ];

        register_post_type('eb_preset', $args_presets);

        /**
         * CPT: Saved Email Templates
         */
        register_post_type('eb_saved_template', [
            'labels' => [
                'name'          => 'Saved Templates',
                'singular_name' => 'Saved Template',
                'add_new_item'  => 'Add New Saved Template',
                'edit_item'     => 'Edit Saved Template',
                'menu_name'     => 'Saved Templates',
            ],
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => false,
            'show_in_rest' => true,
            'supports'     => ['title', 'editor'],
            'taxonomies'   => ['eb_template_tag'],
        ]);

        /**
         * CPT: Reusable Section Modules
         */
        register_post_type('eb_module', [
            'labels'       => [
                'name'          => 'Modules',
                'singular_name' => 'Module',
            ],
            'public'       => false,
            'show_ui'      => false,
            'show_in_rest' => true,
            'supports'     => ['title', 'editor'],
        ]);

        register_taxonomy('eb_template_tag', 'eb_saved_template', [
            'labels' => [
                'name'          => 'Template Tags',
                'singular_name' => 'Template Tag',
                'add_new_item'  => 'Add New Tag',
                'new_item_name' => 'New Tag Name',
                'search_items'  => 'Search Tags',
                'all_items'     => 'All Tags',
            ],
            'hierarchical'      => false,
            'show_ui'           => false,
            'show_in_rest'      => true,
            'show_admin_column' => false,
            'rewrite'           => false,
        ]);

        register_taxonomy('eb_template_category', 'eb_saved_template', [
            'labels' => [
                'name'          => 'Template Categories',
                'singular_name' => 'Template Category',
                'add_new_item'  => 'Add New Category',
                'new_item_name' => 'New Category Name',
                'search_items'  => 'Search Categories',
                'all_items'     => 'All Categories',
            ],
            'hierarchical'      => true,
            'show_ui'           => false,
            'show_in_rest'      => true,
            'show_admin_column' => false,
            'rewrite'           => false,
        ]);
    }
}