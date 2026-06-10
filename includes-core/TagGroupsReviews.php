<?php

/**
 * Tag Groups Review Request System
 *
 * @package TaxoPress\TagGroups
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Initialize the PublishPress WordPress Reviews library for Tag Groups
 */
function tag_groups_init_reviews()
{
    if (!class_exists('PublishPress\WordPressReviews\ReviewsController')) {
        return;
    }

    $iconUrl = '';
    if (defined('TAG_GROUPS_PLUGIN_URL')) {
        $iconUrl = TAG_GROUPS_PLUGIN_URL . '/assets/images/icon-128x128.png';
    }

    $reviewsController = new \PublishPress\WordPressReviews\ReviewsController(
        'tag-groups',
        'Tag Groups',
        $iconUrl
    );

    $reviewsController->init();
}

add_action('init', 'tag_groups_init_reviews');
