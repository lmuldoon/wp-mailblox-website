<?php
if (!defined('ABSPATH')) exit;

class EB_WooCommerce
{
    public function __construct()
    {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes()
    {
        register_rest_route('eb/v1', '/products', [
            'methods'             => 'GET',
            'callback'            => [$this, 'search_products'],
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            },
            'args' => [
                'search' => [
                    'type'              => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                    'default'           => '',
                ],
            ],
        ]);

        register_rest_route('eb/v1', '/product-categories', [
            'methods'             => 'GET',
            'callback'            => [$this, 'get_categories'],
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            },
        ]);

        register_rest_route('eb/v1', '/recommended-products', [
            'methods'             => 'GET',
            'callback'            => [$this, 'get_recommended_products'],
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            },
            'args' => [
                'category'  => ['type' => 'integer', 'default' => 0],
                'orderby'   => ['type' => 'string',  'default' => 'date', 'sanitize_callback' => 'sanitize_text_field'],
                'count'     => ['type' => 'integer', 'default' => 3],
            ],
        ]);
    }

    public function search_products($request)
    {
        if (!class_exists('WooCommerce')) {
            return rest_ensure_response([]);
        }

        $search = $request->get_param('search');

        $query = new WP_Query([
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => 8,
            's'              => $search,
        ]);

        $products = [];

        foreach ($query->posts as $post) {
            $product = wc_get_product($post->ID);
            if (!$product) continue;

            $image_id      = $product->get_image_id();
            $image_url     = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';
            $is_on_sale    = $product->is_on_sale();
            $price         = strip_tags(wc_price($product->get_price()));
            $regular_price = $is_on_sale ? strip_tags(wc_price($product->get_regular_price())) : '';

            $products[] = [
                'id'                => $product->get_id(),
                'title'             => $product->get_name(),
                'price'             => $price,
                'regular_price'     => $regular_price,
                'is_on_sale'        => $is_on_sale,
                'image_url'         => $image_url ?: '',
                'permalink'         => get_permalink($post->ID),
                'short_description' => wp_strip_all_tags($product->get_short_description()),
            ];
        }

        return rest_ensure_response($products);
    }

    public function get_categories()
    {
        if (!class_exists('WooCommerce')) {
            return rest_ensure_response([]);
        }

        $terms = get_terms([
            'taxonomy'   => 'product_cat',
            'hide_empty' => true,
            'orderby'    => 'name',
        ]);

        if (is_wp_error($terms)) {
            return rest_ensure_response([]);
        }

        $categories = array_map(function ($term) {
            return ['id' => $term->term_id, 'name' => $term->name];
        }, $terms);

        return rest_ensure_response($categories);
    }

    public function get_recommended_products($request)
    {
        if (!class_exists('WooCommerce')) {
            return rest_ensure_response([]);
        }

        $category_id = intval($request->get_param('category'));
        $orderby     = $request->get_param('orderby');
        $count       = min(12, max(1, intval($request->get_param('count'))));

        $allowed_orderby = ['date', 'popularity', 'rating', 'price', 'rand'];
        if (!in_array($orderby, $allowed_orderby)) {
            $orderby = 'date';
        }

        $args = [
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => $count,
            'orderby'        => $orderby === 'popularity' ? 'meta_value_num' : ($orderby === 'rating' ? 'meta_value_num' : $orderby),
            'order'          => $orderby === 'price' ? 'ASC' : 'DESC',
        ];

        if ($orderby === 'popularity') {
            $args['meta_key'] = 'total_sales';
        } elseif ($orderby === 'rating') {
            $args['meta_key'] = '_wc_average_rating';
        }

        if ($category_id) {
            $args['tax_query'] = [[
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $category_id,
            ]];
        }

        $query    = new WP_Query($args);
        $products = [];

        foreach ($query->posts as $post) {
            $product = wc_get_product($post->ID);
            if (!$product) continue;

            $image_id      = $product->get_image_id();
            $image_url     = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';
            $is_on_sale    = $product->is_on_sale();
            $price         = strip_tags(wc_price($product->get_price()));
            $regular_price = $is_on_sale ? strip_tags(wc_price($product->get_regular_price())) : '';

            $products[] = [
                'id'                => $product->get_id(),
                'title'             => $product->get_name(),
                'price'             => $price,
                'regular_price'     => $regular_price,
                'is_on_sale'        => $is_on_sale,
                'image_url'         => $image_url ?: '',
                'permalink'         => get_permalink($post->ID),
                'short_description' => wp_strip_all_tags($product->get_short_description()),
            ];
        }

        return rest_ensure_response($products);
    }
}
