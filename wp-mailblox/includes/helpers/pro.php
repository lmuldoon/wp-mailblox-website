<?php
// /includes/helpers/pro.php

if (!defined('ABSPATH')) exit;

/**
 * Returns true if the current site has an active Pro licence.
 *
 * To enable Pro features during development, temporarily return true.
 * Wire this to Freemius or EDD when licensing (SC-1) is implemented:
 *
 *   Freemius: return freemius()->is_paying();
 *   EDD:      return edd_software_licensing()->check_site_license( ... );
 */
function eb_is_pro()
{
    if ( function_exists( 'wp_mailblox_fs' ) ) {
        return wp_mailblox_fs()->can_use_premium_code();
    }
    return false;
}

/**
 * Return the maximum number of posts allowed for a given post type on the free plan.
 * Returns PHP_INT_MAX for pro sites (unlimited).
 */
function eb_get_post_limit($post_type)
{
    $is_pro = eb_is_pro();

    $limits = [
        'eb_email_template'  => $is_pro ? 500 : 5,
        'eb_preset'   => $is_pro ? 500 : 1,
        'eb_saved_template' => $is_pro ? 500 : 10,
    ];

    return $limits[$post_type] ?? ($is_pro ? 500 : PHP_INT_MAX);
}

/**
 * Count all active (non-trashed) posts of a given type.
 * Includes publish, draft, private, and pending — all statuses a user can work with.
 */
function eb_count_active_posts($post_type)
{
    return count(get_posts([
        'post_type'      => $post_type,
        'post_status'    => ['publish', 'draft', 'private', 'pending'],
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ]));
}
