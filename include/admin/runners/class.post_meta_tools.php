<?php

// phpcs:disable Squiz.PHP.CommentedOutCode, Squiz.ControlStructures.ControlSignature, WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize, WordPress.DB.SlowDBQuery -- Complex DB queries and serialize for caching by design

/**
 * @package     Tag Groups
 *
 * @author      Christoph Amthor
 * @copyright   2018 Christoph Amthor (@ Chatty Mango, chattymango.com)
 * @license     GPL-3.0+
 */

if (! class_exists('TagGroups_Post_Meta_Tools')) {
/**
   *
   */
    class TagGroups_Post_Meta_Tools
    {
      /**
       * Fixes all terms in post meta of all posts
       *
       * @phpunit
       * @param  integer           $group_id
       * @param  boolean           $count_only
       * @param  integer           $offset
       * @param  integer           $length
       * @return integer|boolean
       */
        public static function fix_all_incorrect_post_terms($count_only = false, $offset = null, $length = null)
        {

            global $tag_group_groups;
            $count = 0;
            if ($count_only) {
                    // In case we restarted the tasks, we need to get fresh data

                      $all_terms = false;
            } else {
                  $all_terms = TagGroups_Transients::get_transient('tag_groups_all_term_ids', false);
            }

            if (false === $all_terms) {
    /**
               * get all terms
               */
                $args = array(
                'taxoomy'    => TagGroups_Taxonomy::get_enabled_taxonomies(),
                'hide_empty' => false,
                'fields'     => 'ids',
                );
                $all_terms = get_terms($args);
    // Try to keep for 10 minutes so that our offset always starts with unprocessed items.
                TagGroups_Transients::set_transient('tag_groups_all_term_ids', $all_terms, 600);
            }

            $post_ids = self::get_post_ids_matching_groups();
            if ($count_only) {
                if (is_array($post_ids)) {
                    return count($post_ids);
                } else {
                    return 0;
                }
            }

            if (isset($offset) && isset($length)) {
                if (! is_array($post_ids) || $offset + $length > count($post_ids)) {
                    return false;
                }

                $post_ids = array_slice($post_ids, $offset, $length);
            }

        /**
         * get all group ids
         */
            $group_ids = $tag_group_groups->get_group_ids();
            $enabled_taxonomies = TagGroups_Taxonomy::get_enabled_taxonomies();
            if (is_array($post_ids)) {
                foreach ($post_ids as $post_id) {
                    $tg_post = new TagGroups_Post($post_id);
                    $count += $tg_post->fix_incorrect_post_terms($enabled_taxonomies, $all_terms, $group_ids);
                }
            }

            TagGroups_Error::verbose_log('[Tag Groups Pro] We fixed the meta of %d posts.', $count);
            if ($count) {
                do_action('tag_groups_post_tags_saved', 0);
            }

            return $count;
        }

      /**
       * Convert all term info to the new format for saving with post meta
       *
       * Posts without any tags will be re-processed every time since we cannot exclude them in WP QUERY (without knowing their taxonomies).
       *
       * @phpunit
       * @param  void
       * @return int|bool number of processed items
       */
        public static function convert_to_post_meta($count_only = false, $offset = null, $length = null)
        {

            global $tag_group_terms;
            $post_ids = self::get_post_ids_with_missing_group(null, $count_only);
            if ($count_only) {
                if (is_array($post_ids)) {
                    return count($post_ids);
                } else {
                    return 0;
                }
            }

            if (isset($offset) && isset($length)) {
                if (! is_array($post_ids) || $offset + $length > count($post_ids)) {
                        return false;
                }

                $post_ids = array_slice($post_ids, $offset, $length);
            }

            $count = 0;
            $enabled_taxonomies = TagGroups_Taxonomy::get_enabled_taxonomies();
  /**
           * Convert the posts
           */

            if (is_array($post_ids)) {
                foreach ($post_ids as $post_id) {
                        $tg_post = new TagGroups_Post($post_id);
                        $has_changed = $tg_post->create_post_meta_from_tags_and_groups($enabled_taxonomies);
                    if ($has_changed) {
                        TagGroups_Error::verbose_log('[Tag Groups Pro] Migrated meta for post ID %d.', $post_id);
                        $count++;
                    }
                }
            }

            if ($count) {
                if (empty($length)) {
                        $tag_group_terms->clear_post_count_transient();
                        // clear post counts, not transient we just created
                }

                do_action('tag_groups_post_tags_saved', 0);
            }

            return $count;
        }

      /**
       * Converts post meta to new format that is faster to process
       *
       * @phpunit
       * @param  void
       * @return int    number of processed items
       */
        public static function post_meta_add_comma($count_only = false, $offset = null, $length = null)
        {

            if ($count_only) {
    // In case we restarted the tasks, we need to get fresh data

                $post_ids = false;
            } else {
                $post_ids = TagGroups_Transients::get_transient('tag_groups_post_meta_add_comma_ids');
            }

            if (false === $post_ids) {
                $post_ids = self::get_post_ids();
    // Try to keep for 10 minutes so that our offset always starts with unprocessed items.
                TagGroups_Transients::set_transient('tag_groups_post_meta_add_comma_ids', $post_ids, 600);
            }

            if ($count_only) {
                if (is_array($post_ids)) {
                    return count($post_ids);
                } else {
                    return 0;
                }
            }

            if (isset($offset) && isset($length)) {
                $post_ids = array_slice($post_ids, $offset, $length);
            }

        /**
         * Convert the posts
         */
            $count = 0;
            foreach ($post_ids as $post_id) {
                /**
                           * get the post meta
                           */
                $post_meta_array = get_post_meta($post_id);
                /**
                           * Check for invalid keys in post meta
                           */

                foreach ($post_meta_array as $post_meta_key => $post_meta_values) {
                    if (strpos($post_meta_key, '_cm_post_terms_') !== 0) {
                        continue;
                    }

                    $post_meta_value = TagGroups_Utilities::get_first_element($post_meta_values);
                    if (( substr($post_meta_value, 0, 1) == ',' && substr($post_meta_value, -1, 1) == ',' )) {
                        continue;
                    }

                    $post_meta_array = explode(',', $post_meta_value);

            // remove empty values
                    $post_meta_array = array_filter($post_meta_array, function ($v) {
                        return '' != $v;
                    });
                    if (0 == count($post_meta_array)) {
                        $post_meta_array = array( 0 );
                    }

                    sort($post_meta_array);
                    $result = update_post_meta($post_id, $post_meta_key, ',' . implode(',', $post_meta_array) . ',');
                    if ($result) {
                        TagGroups_Error::verbose_log('[Tag Groups Pro] Post meta %s changed to new format for post ID %d.', $post_meta_key, $post_id);
                        $count++;
                    }
                }
            }

            if ($count) {
                do_action('tag_groups_post_tags_saved', 0);
            }

            return $count;
        }

      /**
       * Changes the corresponding post meta values after a term has changed its group affiliation
       *
       * @phpunit
       * @param  int       $term_id         modified term
       * @param  array     $new_group_ids
       * @return integer
       */
        public static function update_post_meta_for_term($new_group_ids, $term_id = 0)
        {

            global $tag_group_groups, $tag_group_terms, $tag_group_posts;
            TagGroups_Error::verbose_log('[Tag Groups Pro] Checking if posts need to be migrated for term ID %d.', $term_id);
            $start_time = microtime(true);
            if (! is_array($new_group_ids)) {
                    $new_group_ids = array( $new_group_ids );
            }

            $all_post_terms = TagGroups_Transients::get_transient('tag_groups_post_terms');
            if (! is_array($all_post_terms)) {
                  $all_post_terms = array();
            }

        /**
         * Check if term was unassigned
         */

            if (in_array(0, $new_group_ids)) {
                $term_was_unassigned = true;
                $new_group_ids = array( 0 );
            } else {
                $term_was_unassigned = false;
            }

            $all_term_groups = $tag_group_groups->get_group_ids_by_position();
            $post_ids = self::get_post_ids_matching_term($term_id);
            $count = 0;
            if (! is_array($post_ids) || empty($post_ids)) {
                return 0;
            }

        /**
         * Loop through all posts that match the post types of enabled taxonomies
         */

            foreach ($post_ids as $post_id) {
                if (empty($all_post_terms[$post_id])) {
                // not in the transient - need to create

                    $post_o = new TagGroups_Post($post_id);
                    $all_post_terms[$post_id] = $post_o->get_terms_by_group();
                }

                $changed_meta = array();
                $post_meta = get_post_meta($post_id);
                if ($term_was_unassigned) {
                /**
                             * case a): Term was unassigned from all groups.
                             *
                             */

                    /**
                     * 1. add to unassigned group
                     */

                    if (isset($post_meta['_cm_post_terms_0'])) {
                        $post_meta_array = self::make_post_meta_array($post_meta, 0);
                    } else {
                        $post_meta_array = array();
                    }

                    if (! in_array($term_id, $post_meta_array)) {
                        $post_meta_array[] = $term_id;
                        sort($post_meta_array);
                        update_post_meta($post_id, '_cm_post_terms_0', ',' . implode(',', $post_meta_array) . ',');
                    }

            /**
             * 2. remove from all other groups
             */

                    foreach ($all_term_groups as $term_group) {
                        if (0 == $term_group) {
                            continue;
                        }

                        if (isset($post_meta['_cm_post_terms_' . $term_group])) {
                            $changed = false;
                            $post_meta_array = self::make_post_meta_array($post_meta, $term_group);
                            if (in_array($term_id, $post_meta_array)) {
                                                $post_meta_array = $tag_group_posts->remove_elements_from_array($post_meta_array, $term_id);
                                                $changed = true;
                            }

                            if (0 == count($post_meta_array)) {
                                delete_post_meta($post_id, '_cm_post_terms_' . $term_group);
                            } elseif ($changed) {
                                      sort($post_meta_array);
                                      update_post_meta($post_id, '_cm_post_terms_' . $term_group, ',' . implode(',', $post_meta_array) . ',');
                            }
                        }
                    }

                    unset($all_post_terms[$post_id]);
                // simply unset, will be recreated

                    $count++;
                } else {
                /**
                             * case b): Term was assigned to groups that are different than "not assigned".
                             *
                             */

                    /**
                     * Don't add tag to post meta if it was already part of that post in a different group, because we don't want to change the group selection of post tags when adding a group to one of its tags
                     *
                     * 1. Check if the tag appears in any post meta of that post.
                     */

                    $term_appears_in_post_meta = false;
                    foreach ($all_term_groups as $term_group) {
            /**
                           *  We don't include 0 (unassigned) because if a tag appears here we can safely move it to other group
                           */

                        if (0 != $term_group && isset($post_meta['_cm_post_terms_' . $term_group])) {
  // Make sure we don't reduce the $post_meta array by taking the first element

                            $post_meta_array = explode(',', TagGroups_Utilities::get_first_element($post_meta['_cm_post_terms_' . $term_group]));
                            if (in_array($term_id, $post_meta_array)) {
                                $term_appears_in_post_meta = true;
                                break;
                            }
                        }
                    }

            /**
             * Loop through all groups, as post meta is segmented by groups
             */

                    foreach ($all_term_groups as $term_group) {
                        if (0 == $term_group) {
                            continue;
                        }

                        if (isset($post_meta['_cm_post_terms_' . $term_group])) {
        /**
                         * case a): We already have a meta entry for that group.
                         */

                      // Make sure we don't reduce the $post_meta array by taking the first element
                            $post_meta_array = self::make_post_meta_array($post_meta, $term_group);
        /**
                         * Remove tag from post meta if not supposed to be here
                         */
                            if (! in_array($term_group, $new_group_ids) && in_array($term_id, $post_meta_array)) {
                                  $changed_meta[$term_group] = $tag_group_posts->remove_elements_from_array($post_meta_array, $term_id);
                            }

                      /**
                       * Add tag to post meta if it was newly assigned to group for that post
                       */
                      // elseif ( in_array( $term_group, $new_group_ids ) && ! in_array( $term_id, $post_meta_array ) ) {

                            elseif (in_array($term_group, $new_group_ids) && ! in_array($term_id, $post_meta_array) && ! $term_appears_in_post_meta) {
                                if (! isset($changed_meta[$term_group])) {
                                    $changed_meta[$term_group] = $post_meta_array;
                                }

                                    array_push($changed_meta[$term_group], $term_id);
                                self::maybe_assign_term_to_group($term_id, $term_group);
                            }
                        } else {
                  /**
                                   * case b): We don't have a meta entry for that group.
                                   */

                          /**
                           * Add tag if in that group
                           */

                            if (in_array($term_group, $new_group_ids)) {
            // We are creating a new meta entry
                                    $changed_meta[$term_group] = array( $term_id );
                                    self::maybe_assign_term_to_group($term_id, $term_group);
                            }
                        }

                        $changed_meta = self::maybe_remove_from_unassigned($changed_meta, $term_group, $post_meta, $term_id);
                    }

            /**
             * Save post meta that has been removed or added.
             * Also add meta for unassigned terms so we know we have processed them.
             */

                    if (! empty($changed_meta)) {
                        foreach ($changed_meta as $group_id => $post_meta_array) {
                            if (count($post_meta_array) > 0) {
                                sort($post_meta_array);
                                update_post_meta($post_id, '_cm_post_terms_' . $group_id, ',' . implode(',', $post_meta_array) . ',');
                            } else {
    // We don't save meta entries with empty value.
                                delete_post_meta($post_id, '_cm_post_terms_' . $group_id);
                            }
                        }

                      // delete entry from tag_groups_post_terms, will be recreated
                        unset($all_post_terms[$post_id]);
                        $count++;
                    }
                }
            }

            $tag_group_posts->save_transient();
            TagGroups_Error::verbose_log('[Tag Groups Pro] Meta of %d post(s) updated in %d milliseconds.', $count, round(( microtime(true) - $start_time ) * 1000));
            if ($count) {
                $tag_group_terms->clear_post_count_transient();
            // clear post counts, not transient we just created

                    do_action('tag_groups_post_tags_saved', 0);
            }

            return $count;
        }

      /**
       * Returns all post IDs that have tags for certain tag groups and taxonomies
       *
       * Omits deleted posts. Uses transient cache. If parameters are not supplied, it uses any group and all enabled taxonomies.
       *
       * @phpunit
       * @param  array   $term_groups
       * @param  array   $taxonomies
       * @return array
       */
        public static function get_post_ids_matching_groups($term_groups = array(), $taxonomies = null)
        {

            $post_ids_transient = TagGroups_Transients::get_transient('tag_groups_post_ids_groups');
            $enabled_taxonomies = TagGroups_Taxonomy::get_enabled_taxonomies();
            if (empty($taxonomies)) {
                    /**
                               * We will need the taxonomies
                               */
                    $taxonomies = $enabled_taxonomies;
            }

          /**
           * Avoid duplicate cache keys
           */
            asort($taxonomies);
  /**
           * We need an array with numeric indices, because wp-includes/taxonomy.php will search for $taxonomy[0]
           */
            $taxonomies = array_values($taxonomies);
  /**
           * Avoid duplicate cache keys
           */
            asort($term_groups);
            $cache_key = md5(serialize($term_groups) . serialize($taxonomies));
            if (false !== $post_ids_transient && isset($post_ids_transient[$cache_key])) {
                  $post_ids = $post_ids_transient[$cache_key];
            } else {
                /**
                           * Get all relevant post types
                           */
                $post_types = TagGroups_Taxonomy::post_types_from_taxonomies($enabled_taxonomies);
                /**
                           * search for posts
                           */
                $post_args = array(
                      'post_type'      => $post_types,
                      'fields'         => 'ids',
                      'posts_per_page' => -1,
                      'post_status'    => array( 'publish', 'pending', 'draft', 'future', 'private' ), // we are omitting trashed posts
                );
                if (empty($term_groups)) {
                    $post_args['meta_query'] = array(
                        'relation' => 'OR',
                      );
                } else {
                    $post_args['meta_query'] = array(
                        'relation' => 'AND',
                    );
                }

                foreach ($term_groups as $term_group) {
                    $post_args['meta_query'][] = array(
                    'key'     => '_cm_post_terms_' . (int) $term_group,
                    'compare' => 'EXISTS',
                    );
                }

                $post_ids = get_posts($post_args);
                if (! is_array($post_ids_transient)) {
                    $post_ids_transient = array();
                }

                $post_ids_transient[$cache_key] = $post_ids;
                TagGroups_Transients::set_transient('tag_groups_post_ids_groups', $post_ids_transient, 180);
          // keep for three minutes
            }

            return $post_ids;
        }

      /**
       * Returns all post IDs where at least one group is missing in the post meta
       *
       * Omits deleted posts. Uses transient cache.
       *
       * @phpunit
       * @param  array   $taxonomies
       * @param  boolean $refresh
       * @return array
       */
        public static function get_post_ids_with_missing_group($taxonomies = null, $refresh = false)
        {

            if ($refresh) {
                $post_ids_transient = false;
            } else {
                  $post_ids_transient = TagGroups_Transients::get_transient('tag_groups_post_ids_missing_group');
            }

            $enabled_taxonomies = TagGroups_Taxonomy::get_enabled_taxonomies();
            if (empty($taxonomies)) {
                /**
                           * We will need the taxonomies
                           */
                $taxonomies = $enabled_taxonomies;
            }

        /**
         * Avoid duplicate cache keys
         */
            asort($taxonomies);
  /**
           * We need an array with numeric indices, because wp-includes/taxonomy.php will search for $taxonomy[0]
           */
            $taxonomies = array_values($taxonomies);
  /**
           * We cannot use class TagGroups_Group since it might not be available yet
           */
            $term_groups = TagGroups_Options::get_option('term_groups', array());
            if (empty($term_groups)) {
              // data from old version
                    $term_groups = array_keys(array_flip(TagGroups_Options::get_option('tag_group_ids', array())));
            }

        // Remove term group 0 - this is never saved in post meta
            unset($term_groups[0]);
  /**
           * Avoid duplicate cache keys
           */
            asort($term_groups);
            $cache_key = md5(serialize($term_groups) . serialize($taxonomies));
            if (false !== $post_ids_transient && isset($post_ids_transient[$cache_key])) {
                $post_ids = $post_ids_transient[$cache_key];
            } else {
            /**
                       * Get all relevant post types
                       */
                    $post_types = TagGroups_Taxonomy::post_types_from_taxonomies($enabled_taxonomies);
            /**
                       * search for posts that don't have any _cm_post_terms_{int} meta entry
                       */
                    $post_args = array(
                      'post_type'      => $post_types,
                      'meta_query'     => array(
                      'relation' => 'OR',
                    ),
                    'fields'         => 'ids',
                    'posts_per_page' => -1,
                    'post_status'    => array( 'publish', 'pending', 'draft', 'future', 'private' ), // we are omitting trashed posts
                    );
                    foreach ($term_groups as $term_group) {
                        $post_args['meta_query'][] = array(
                              'key'     => '_cm_post_terms_' . (int) $term_group,
                              'compare' => 'NOT EXISTS',
                            );
                    }

                    $post_ids = get_posts($post_args);
                    if (! is_array($post_ids_transient)) {
                        $post_ids_transient = array();
                    }

                    $post_ids_transient[$cache_key] = $post_ids;
                    TagGroups_Transients::set_transient('tag_groups_post_ids_missing_group', $post_ids_transient, 600);
  // keep for 10 minutes
            }

            return $post_ids;
        }

      /**
       * Returns all post IDs
       *
       * Omits deleted posts. No extra caching (done by get_posts)
       *
       * @phpunit
       * @param  void
       * @return array
       */
        public static function get_post_ids()
        {

          /**
           * We will need the taxonomies
           */
            $enabled_taxonomies = TagGroups_Taxonomy::get_enabled_taxonomies();
  // /**

          // * We need an array with numeric indices, because wp-includes/taxonomy.php will search for $taxonomy[0]

          // */

          // $enabled_taxonomies_values = array_values( $enabled_taxonomies );

          /**
           * Get all relevant post types
           */
            $post_types = TagGroups_Taxonomy::post_types_from_taxonomies($enabled_taxonomies);
  /**
           * search for posts that have a _cm_post_terms_{int} meta entry
           */
            $post_args = array(
            'post_type'      => $post_types,
            'fields'         => 'ids',
            'posts_per_page' => -1,
            'post_status'    => array( 'publish', 'pending', 'draft', 'future', 'private' ), // we are omitting trashed posts
            );
            return get_posts($post_args);
        }

      /**
       * Returns all post IDs that have a certain tag
       *
       * Omits deleted posts. Uses transient cache.
       *
       * @phpunit
       * @param  int     $tag_id
       * @return array
       */
        public static function get_post_ids_matching_term($tag_id = null)
        {

          // Don't use a transient cache here since we deal with situations that change quickly

          /**
           * We will need the taxonomies
           */
            $enabled_taxonomies = TagGroups_Taxonomy::get_enabled_taxonomies();
  /**
           * We need an array with numeric indices, because wp-includes/taxonomy.php will search for $taxonomy[0]
           */
            $enabled_taxonomies = array_values($enabled_taxonomies);
  /**
           * Get all relevant post types
           */
            $post_types_for_enabled_taxonomies = TagGroups_Taxonomy::post_types_from_taxonomies($enabled_taxonomies);
            $term = get_term($tag_id);
            if (! is_object($term) || is_wp_error($term)) {
                    return array();
            }

            $post_args = array(
            'post_type'      => $post_types_for_enabled_taxonomies,
            'fields'         => 'ids',
            'posts_per_page' => -1,
            'post_status'    => array( 'publish', 'pending', 'draft', 'future', 'private' ),
          // we are omitting trashed posts
          // 'tag_id'      => $tag_id, // doesn't work for custom taxonomies
            'numberposts'    => -1,
            'tax_query'      => array(
            array(
            'taxonomy' => $term->taxonomy,
            'terms'    => $tag_id,
            ),
            ),
            );
            $post_ids = get_posts($post_args);
            return $post_ids;
        }


      /**
       * conditionally removes a tag from the unassigned group
       *
       * @param array $changed_meta
       * @param int $term_group
       * @param array $post_meta
       * @param int $term_id
       * @return array
       */
        public static function maybe_remove_from_unassigned($changed_meta, $term_group, $post_meta, $term_id)
        {

            global $tag_group_posts;
            if (0 == $term_group) {
                    return $changed_meta;
            }

            if (isset($post_meta['_cm_post_terms_0'])) {
    /**
               * It cannot be also in the unassigned group
               */
                $post_meta_array = self::make_post_meta_array($post_meta, 0);
                if (in_array($term_id, $post_meta_array)) {
                        $changed_meta[0] = $tag_group_posts->remove_elements_from_array($post_meta_array, $term_id);
                }

                return $changed_meta;
            }
        }


      /**
       * turns post meta into array
       *
       * @param array $post_meta
       * @param int $term_group
       * @return int[]
       */
        public static function make_post_meta_array($post_meta, $term_group)
        {

          /**
           *  Make sure we don't reduce the $post_meta array by taking the first element
           */
            $post_meta_array = explode(',', TagGroups_Utilities::get_first_element($post_meta['_cm_post_terms_' . $term_group]));

          // remove empty values
            $post_meta_array = array_filter($post_meta_array, function ($v) {
                return '' != $v;
            });
            return $post_meta_array;
        }

        public static function maybe_assign_term_to_group($term_id, $term_group)
        {

            $tg_term = new TagGroups_Term($term_id);
            if (! $tg_term->has_group($term_group)) {
                    $tg_term->add_group($term_group);
                    $tg_term->save();
            }
        }
    }


}
