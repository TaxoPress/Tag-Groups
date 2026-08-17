<?php

// phpcs:disable Squiz.PHP.CommentedOutCode -- Commented code is intentional documentation

/**
* Tag Groups Pro
*
* @package     Tag Groups Pro
* @author      Christoph Amthor
* @copyright   2018 Christoph Amthor (@ Chatty Mango, chattymango.com)
* @license     see official vendor website
* @since      1.8.0
*
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
* THE SOFTWARE.
*
*/

if (! class_exists('TagGroups_Post')) {
    class TagGroups_Post
    {
          /**
          * ID of this post
          *
          * @var int
          */
          private $post_id;
    /**
          * object of posts
          *
          * @var object
          */
          private $tg_posts;
    /**
          * Terms of this post in the form: term => array of groups and name of this term
          *
          * array(
          *  $term_id_1 = > array(
          *     'name' => 'name',
          *     'groups' => array(
          *       $group_id_1, $group_id_2, ..
          *     ),
          *  ),
          *  $term_id_2 => ...
          * )
          *
          *
          * @var array
          */
          private $post_terms;
    /**
           * Whether we have retrieved the data from the transient tag_groups_post_terms
           *
           * @var boolean
           */
          private $is_data_from_cache;
    /**
          * Constructor
          *
          * @phpunit
          * @param int $term_group optional term_group
          * @return return type
          */
        public function __construct($post_id = null)
        {

            global $tag_group_posts;
            $this->tg_posts = $tag_group_posts;
            $this->is_data_from_cache = false;
            if (isset($post_id)) {
                $this->post_id = $post_id;
                $this->load();
                if (! $this->is_data_from_cache) {
                    $this->save_transient();
                }
            }

            return $this;
        }


      /**
      * Load all terms for this post from the database
      *
      * @phpunit
      * @param boolean $fast Speed up loading by not loading the tag names
      * @return object $this
      */
        public function load($fast = false)
        {

            global $tag_group_groups;
            if (! isset($this->post_id)) {
                return $this;
            }

            $all_post_terms = $this->tg_posts->get_all_post_terms();
            $taxonomies = TagGroups_Taxonomy::get_enabled_taxonomies();
    /**
             * try to get cached version
             */
            if ($all_post_terms === false ||  ! isset($all_post_terms[ $this->post_id ])) {
                $all_group_ids = $tag_group_groups->get_group_ids();
                $meta_terms = array();
                $all_assigned_meta_terms = array();
                unset($all_group_ids[0]);
                foreach ($all_group_ids as $group_id) {
                    $meta_terms_s = get_post_meta($this->post_id, '_cm_post_terms_' . $group_id, true);
                    if (! empty($meta_terms_s)) {
                        $meta_terms[ $group_id ] = explode(',', $meta_terms_s);

                        // remove empty values
                        $meta_terms[ $group_id ] = array_filter($meta_terms[ $group_id ], function ($v) {
                            return $v != '';
                        });
                        $all_assigned_meta_terms = array_merge($all_assigned_meta_terms, $meta_terms[ $group_id ]);
                    }
                }

                $all_assigned_meta_terms = array_unique($all_assigned_meta_terms);
                $all_post_term_ids = wp_get_post_terms($this->post_id, $taxonomies, array( 'fields' => 'ids' ));
    // rest is unassigned
                if (is_array($all_post_term_ids)) {
                    foreach ($all_post_term_ids as $all_post_term_id) {
                        if (! in_array($all_post_term_id, $all_assigned_meta_terms)) {
                            $meta_terms[ 0 ][] = $all_post_term_id;
                        }
                    }
                }
            } else {
                $meta_terms = $all_post_terms[ $this->post_id ];
                $this->is_data_from_cache = true;
            }


            /**
            * Check if we have the old (term_group saved only with terms) or new structure (using post meta)
            */
            if (empty($meta_terms)) {
    /**
              * old structure: We need to separate the terms according to their group membership.
              */

                $post_terms = array();
                $unsorted_terms = wp_get_post_terms($this->post_id, $taxonomies);
                if (is_array($unsorted_terms)) {
                    foreach ($unsorted_terms as $unsorted_term) {
                        if (is_object($unsorted_term)) {
                                $term_o = new TagGroups_Term($unsorted_term);
                                /**
                                                  * make sure that term for this term_id exists
                                                  */
                            if (false !== $term_o) {
                                $group_ids = $term_o->get_groups();
                                $post_terms[ $unsorted_term->term_id ]['groups'] = $group_ids;
                                $post_terms[ $unsorted_term->term_id ]['name'] = $unsorted_term->name;
                            }
                        }
                    }
                }
            } else {
            /**
                      * new structure
                      * Check if the group membership is still correct and add the name
                      */

                  $post_terms = array();
                foreach ($meta_terms as $term_group => $term_ids) {
            /**
                        * exclude groups that don't have terms in this post
                        */
                    if (! empty($term_ids)) {
                        if ($fast) {
                            foreach ($term_ids as $term_id) {
                                if (! isset($post_terms[ $term_id ])) {
                                            $post_terms[ $term_id ] = array(
                                              'groups'  => array()
                                                );
                                }

                                $post_terms[ $term_id ]['groups'][] = $term_group;
                            }
                        } else {
                            foreach ($term_ids as $term_id) {
                                $term_o = new TagGroups_Term($term_id);
                                if (! isset($post_terms[ $term_id ])) {
                                    $post_terms[ $term_id ] = array(
                                          'name'    => $term_o->get_name(),
                                          'groups'  => array()
                                    );
                                }

                  /**
                  * Add group only if post term is still assigned to its groups
                  */
                                $group_ids = $term_o->get_groups();
                                if (is_array($group_ids)) {
                                    if (in_array($term_group, $group_ids)) {
                                /**
                                                        * Check if term is still in selected taxonomy
                                                        */
                                        if (! method_exists($term_o, 'get_taxonomy') || in_array($term_o->get_taxonomy(), $taxonomies)) {
                                                $post_terms[ $term_id ]['groups'][] = $term_group;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $this->post_terms = $post_terms;
            return $this;
        }


      /**
      * Setter for $post_id
      *
      * Usually followed by load()
      *
      * @phpunit
      * @param int $post_id ID of the post
      * @return object
      */
        public function set_post_id($post_id)
        {

            $this->post_id = $post_id;
            return $this;
        }


      /**
      * Getter for $post_id
      *
      * @phpunit
      * @param void
      * @return int
      */
        public function get_post_id()
        {

            return $this->post_id;
        }


      /**
      * Saves group-relevant data to the database; doesn't save any post data
      *
      * @phpunit
      * @param void
      * @return object $this
      */
        public function save()
        {

            /**
            * We need the terms with their group membership
            */
            $terms_by_group = $this->get_terms_by_group();
            foreach ($terms_by_group as $group => $terms) {
            /**
                      * If the term group was not used, remove it from post meta
                      */
                if (! empty($terms)) {
                    sort($terms);
                    update_post_meta($this->post_id, '_cm_post_terms_' . $group, ',' . implode(',', $terms) . ',');
                } else {
                    delete_post_meta($this->post_id, '_cm_post_terms_' . $group);
                }
            }


            /**
            * Save all the tags the traditional way so that they are accessible by WP
            */
            wp_set_post_terms($this->post_id, $this->get_flat_terms());
            $this->save_transient();
            return $this;
        }


      /**
      * Save this post's terms to the transient of terms for all posts
      *
      * @phpunit
      * @param void
      * @return object $this
      */
        public function save_transient()
        {

            if (empty($this->post_id)) {
                return;
            }

            $this->tg_posts->add_to_transient($this->post_id, $this->get_terms_by_group());
            return $this;
        }


      /**
      * sets $this->post_terms from array
      *
      * @phpunit
      * @param array $terms array of array( 'name'|'term_id'|'slug' => (string|int), 'taxonomy' => (string), 'term_group' => (int|array) )
      * @param array $unassigned_term_ids
      * @param string $field 'id', 'slug', 'name' (or 'term_taxonomy_id')
      * @return object $this
      */
        public function set_terms($terms, $unassigned_term_ids = array(), $field = 'name')
        {

            $result = array();
    /**
            * WP uses here 'id' for quering the database
            */
            if ('id' == $field) {
                $key = 'term_id';
            } elseif ('term_id' == $field) {
                $key = 'term_id';
                $field = 'id';
            } else {
                $key = $field;
            }

            foreach ($terms as $term) {
    /**
              * identify term by name
              */
                $wp_term = get_term_by($field, $term[ $key ], $term['taxonomy']);
                if (false !== $wp_term) {
                    $term_id = (int) $wp_term->term_id;
                /**
                            *   get_term_by does not consider WPML, need to "translate" the term ID
                            */
                        $current_language = TagGroups_WPML::get_current_language();
                    if ($current_language && function_exists('icl_object_id')) {
                        $term_id = icl_object_id($term_id, $wp_term->taxonomy, true, $current_language);
                    }

                    if (! isset($result[ $term_id ])) {
                            $result[ $term_id ] = array(
                            'name'    => $wp_term->name,
                            'groups'  => array()
                            );
                    }

                    if (is_array($term['term_group'])) {
                        $result[ $term_id ]['groups'] = array_merge($result[ $term_id ]['groups'], $term['term_group']);
                    } else {
                        if (! in_array($term['term_group'], $result[ $term_id ]['groups'])) {
                            $result[ $term_id ]['groups'][] = $term['term_group'];
                        }
                    }
                } else {
                    TagGroups_Error::log('[Tag Groups Pro] set_terms: Term not found.');
                }
            }

            if (! empty($unassigned_term_ids)) {
                foreach ($unassigned_term_ids as $unassigned_term_id) {
                    if (! isset($result[ $unassigned_term_id ])) {
                        $result[ $unassigned_term_id ]['groups'] = array();
                    }

                    $result[ $unassigned_term_id ]['groups'][] = 0;
                }
            }

            $this->post_terms = $result;
            return $this;
        }


      /**
      * Returns an one-dimensional array of terms
      *
      * @phpunit
      * @param void
      * @return array
      */
        public function get_flat_terms()
        {

            return array_keys($this->post_terms);
        }


      /**
      * Returns the terms of this post sorted into groups
      *
      * @phptest
      * @param void
      * @return array
      */
        public function get_term_names_by_id($all_groups_as_keys = false)
        {

            global $tag_group_groups;
            $result = array();
            if ($all_groups_as_keys) {
            // We need all groups as keys for the meta box

                foreach ($tag_group_groups->get_group_ids_by_position() as $id) {
                    $result[ $id ] = array();
                }
            }

            foreach ($this->post_terms as $post_term) {
                foreach ($post_term['groups'] as $term_group) {
                // if ( $term_group != 0 ) {

                    if (! isset($result[ $term_group ])) {
                        $result[ $term_group ] = array();
                    }

                    $result[ $term_group ][] = $post_term['name'];
                }
            }

            return $result;
        }


      /**
      * Returns all the post terms with term_group as key and an array of term ids as value, or a one-dimensional array of terms assigned to $requested_term_group (term_id)
      *
      * @phpunit
      * @param int|object $requested_term_group term group
      * @param object group object - possibility to avoid creating object again if already available
      * @return array
      */
        public function get_terms_by_group($requested_term_group = null)
        {

            global $tag_group_groups;
            $result = array();
            if (is_object($requested_term_group) || is_null($requested_term_group)) {
            /**
                      * return an array with all groups and their assigned terms (term_id)
                      */

                  $term_groups = $tag_group_groups->get_group_ids();
            /**
                      * We need to prepare the array, so that for every term_group one key is set
                      */
                foreach ($term_groups as $term_group) {
                    $result[ $term_group ] = array();
                }

                foreach ($this->post_terms as $term_id => $post_term) {
                    foreach ($post_term['groups'] as $term_group) {
                        if (isset($result[ $term_group ]) && is_array($result[ $term_group ]) && ! in_array($term_id, $result[ $term_group ])) {
                            $result[ $term_group ][] = $term_id;
                        }
                    }
                }
            } else {
            /**
                      * return a one-dimensional array of terms assigned to $requested_term_group (term_id)
                      */

                  // if ( is_object( $requested_term_group ) ) {

                  //   $requested_term_group = (int) $requested_term_group->get_group_id();

                  // }

                foreach ($this->post_terms as $term_id => $post_term) {
                    if (in_array($requested_term_group, $post_term['groups']) && ! in_array($term_id, $result)) {
                        $result[] = $term_id;
                    }
                }
            }

            return $result;
        }


      /**
      * Checks if this post has any terms that are assigned to groups
      *
      * @phpunit
      * @param void
      * @return boolean
      */
        public function has_assigned_terms()
        {

            foreach ($this->post_terms as $post_term) {
                foreach ($post_term['groups'] as $group_id) {
                    if (0 !== $group_id) {
                        return true;
                    }
                }
            }

            return false;
        }


      /**
      * Loops through all post terms of a given post and adds these terms for all their groups to the post meta.
      *
      * Must be called after terms have been migrated.
      *
      * Should only be applied to posts that don't have any tags in post meta yet.
      *
      * @phpunit
      * @param int $post_id
      * @param array $taxonomies
      * @return boolean Whether we saved any changes
      */
        public function create_post_meta_from_tags_and_groups($taxonomies)
        {

            global $tag_group_groups;
            $has_changed = false;
            $post_meta_flat_terms = array();
            $meta_terms = array();
            $group_ids = $tag_group_groups->get_group_ids();
    /**
            * Try to get information from meta
            */
            $post_meta_all = get_post_meta($this->post_id);
            foreach ($group_ids as $group_id) {
                if (isset($post_meta_all[ '_cm_post_terms_' . $group_id ])) {
                    $post_meta_a = explode(',', $post_meta_all[ '_cm_post_terms_' . $group_id ][0]);

                      // remove empty values
                      $post_meta_a = array_filter($post_meta_a, function ($v) {
                        return $v != '';
                      });
                    $post_meta_flat_terms = array_merge($post_meta_flat_terms, $post_meta_a);
                    $meta_terms[ $group_id ] = $post_meta_a;
                }
            }

            /**
             * Get the post terms (uncached)
             */
            $post_term_ids = wp_get_post_terms($this->post_id, $taxonomies, array( 'fields' => 'ids' ));
            if (is_array($post_term_ids)) {
            // loop through all post terms and sort them by groups
                foreach ($post_term_ids as $post_term_id) {
                    if (0 == $post_term_id) {
                        continue;
                    }

                    if (in_array($post_term_id, $post_meta_flat_terms)) {
        // We add the term only if not already somewhere in this post's meta

                        continue;
                    }

                    $tg_term = new TagGroups_Term($post_term_id);
// Get groups from term meta, if availbable; otherwise from term_group
                    $group_ids = $tg_term->get_groups();
// We assume that the tag was used with this post in all assigned groups.
                    if (is_array($group_ids)) {
                        foreach ($group_ids as $group_id) {
                                // if ( $group_id != 0 ) {

                            if (! isset($meta_terms[ $group_id ])) {
                                $meta_terms[ $group_id ] = array();
                            }

                                  $meta_terms[ $group_id ][] = $post_term_id;
                                // }
                        }
                    }
                }

                foreach ($meta_terms as $group_id => $term_ids) {
                    if (! empty($term_ids)) {
                        sort($term_ids);
                        $result = update_post_meta($this->post_id, '_cm_post_terms_' . $group_id, ',' . implode(',', $term_ids) . ',');
                    } else {
        /**
                      * If the term group was not used, remove it from post meta
                      */

                        $result = delete_post_meta($this->post_id, '_cm_post_terms_' . $group_id);
                    }

                    if ($result !== false) {
                        $has_changed = true;
                    }
                }
            }

            if ($has_changed) {
                do_action('tag_groups_post_tags_saved', $this->post_id);
            }

            return $has_changed;
        }


      /**
      * Checks whether the post has terms in the given term group
      *
      * @phpunit
      * @param int $term_group
      * @return boolean
      */
        public function has_group($group_id)
        {

            $meta = get_post_meta($this->post_id, '_cm_post_terms_' . $group_id, true);
            if (empty($meta)) {
                return false;
            } else {
                return true;
            }
        }


      /**
      * Fixes all post terms in post meta
      *
      * non-existent groups, non-existent terms, terms in wrong groups, empty meta, grouped post terms missing in post meta
      *
      * @phpunit
      * @param array $taxonomies
      * @param array $all_terms
      * @param array $group_ids
      * @return int $count
      */
        public function fix_incorrect_post_terms($taxonomies, $all_terms = null, $group_ids = null)
        {

            global $tag_group_groups;
            $fixed_this_post = false;
            if (empty($all_terms)) {
            /**
                      * get all terms
                      */
                  $args = array(
                'hide_empty'  => false,
                'fields'  => 'ids',
                'taxonomies' => $taxonomies
                  );
                  $all_terms = get_terms($args);
            }

            if (empty($group_ids)) {
    /**
              * get all group ids
              */
                $group_ids = $tag_group_groups->get_group_ids();
            }

            /**
            * get the post meta
            */
            $all_post_meta_entries = get_post_meta($this->post_id);
    /**
             * get first elements
             */
            foreach ($all_post_meta_entries as $key => $value) {
                $all_post_meta_entries[ $key ] = reset($value);
            }

            /**
            * Check for invalid keys in post meta
            */
            foreach ($all_post_meta_entries as $post_meta_key => $post_meta_values) {
                if (strpos($post_meta_key, '_cm_post_terms_') === 0) {
                    $post_meta_group = (int) str_replace('_cm_post_terms_', '', $post_meta_key);
                /**
                            * Check if group exists
                            */
                    if (! in_array($post_meta_group, $group_ids)) {
/**
                * Remove this meta
                */
                        delete_post_meta($this->post_id, '_cm_post_terms_' . $post_meta_group);
                        unset($all_post_meta_entries[ '_cm_post_terms_' . $post_meta_group ]);
                        $fixed_this_post = true;
                    }
                }
            }

        /**
         * Get the post terms (uncached)
         */
            $post_term_ids = wp_get_post_terms($this->post_id, $taxonomies, array( 'fields' => 'ids' ));
/**
        * Check for non-existent terms and for terms in wrong groups
        */
            foreach ($group_ids as $group_id) {
                $changed = false;
                if (! empty($all_post_meta_entries[ '_cm_post_terms_' . $group_id ])) {
                    $meta_terms_s = $all_post_meta_entries[ '_cm_post_terms_' . $group_id ];
                    $meta_terms_a = explode(',', $meta_terms_s);
                // remove empty values at beginning and end

                    if ('' == $meta_terms_a[0]) {
                        unset($meta_terms_a[0]);
                    }

                    if (function_exists('array_key_last')) {
                        // while supporting PHP < 7.3.0

                              $last_key = array_key_last($meta_terms_a);
                    } else {
                        $last_key = array_keys($meta_terms_a)[count($meta_terms_a) - 1];
                    }

                    if ('' == $meta_terms_a[ $last_key ]) {
                            unset($meta_terms_a[ $last_key ]);
                    }

                    foreach ($meta_terms_a as $key => $meta_term) {
                        if ('' == $meta_term) {
                        /**
                                        * remove empty values
                                        */
                                        unset($meta_terms_a[ $key ]);
                            TagGroups_Error::verbose_log('[Tag Groups Pro] We remove an empty term from the post meta of post ID %d for the group %d.', $this->post_id, $group_id);
                            $changed = true;
                            continue;
                        }

                        if (! in_array($meta_term, $all_terms)) {
                  /**
                                  * term doesn't exist
                                  */
                            unset($meta_terms_a[ $key ]);
                            TagGroups_Error::verbose_log('[Tag Groups Pro] We remove the term ID %d from the post meta of post ID %d for the group %d because it doesn\'t exist.', $meta_term, $this->post_id, $group_id);
                            $changed = true;
                        } elseif (! in_array($meta_term, $post_term_ids)) {
                        /**
                                        * term is not a post term
                                        */
                                        unset($meta_terms_a[ $key ]);
                            TagGroups_Error::verbose_log('[Tag Groups Pro] We remove the term ID %d from the post meta of post ID %d for the group %d because it is not a post term.', $meta_term, $this->post_id, $group_id);
                            $changed = true;
                        } else {
                            $term_o = new TagGroups_Term($meta_term);
                            if (! $term_o->has_group($group_id)) {
                /**
                                  * term does not belong in this group
                                  */
                                unset($meta_terms_a[ $key ]);
                                TagGroups_Error::verbose_log('[Tag Groups Pro] We remove the term ID %d from the post meta of post ID %d because it doesn\'t belong in the group %d.', $meta_term, $this->post_id, $group_id);
                                $changed = true;
                            } else {
    /**
                       * term is good - remove from $post_term_ids
                       * Don't remove, it might appear again
                       */
          // $post_term_ids = array_diff( $post_term_ids, array( $meta_term ) );
                            }
                        }
                    }

                    $meta_terms_a = array_values($meta_terms_a);
                    $meta_terms_a_unsorted = $meta_terms_a;
                    sort($meta_terms_a);
                    if ($meta_terms_a_unsorted !== $meta_terms_a) {
                    // check the order

                          TagGroups_Error::verbose_log('[Tag Groups Pro] We fix the term order in the post meta of post ID %d for the group %d', $this->post_id, $group_id);
                        $changed = true;
                    }

                    if ($changed) {
                        if (! empty($meta_terms_a)) {
                            update_post_meta($this->post_id, '_cm_post_terms_' . $group_id, ',' . implode(',', $meta_terms_a) . ',');
                            $all_post_meta_entries[ '_cm_post_terms_' . $group_id ] = ',' . implode(',', $meta_terms_a) . ',';
                        } else {
                            delete_post_meta($this->post_id, '_cm_post_terms_' . $group_id);
                            unset($all_post_meta_entries[ '_cm_post_terms_' . $group_id ]);
                        }

                        $fixed_this_post = true;
                    }
                } else {
                    $custom_keys = get_post_custom_keys($this->post_id);
                    if (is_array($custom_keys) && in_array('_cm_post_terms_' . $group_id, $custom_keys)) {
            /**
                          * If the value of the meta entry exists and is empty, we delete it
                          */
                          delete_post_meta($this->post_id, '_cm_post_terms_' . $group_id);
                        unset($all_post_meta_entries[ '_cm_post_terms_' . $group_id ]);
                        $fixed_this_post = true;
                    }
                }
            }


        /*
       * Check if any post terms have groups but are not in the meta: add in that post to ALL groups
       */
            if (! empty($post_term_ids)) {
                foreach ($post_term_ids as $post_term_id) {
                    $term_o = new TagGroups_Term($post_term_id);
                    $group_ids = $term_o->get_groups();
                    foreach ($group_ids as $group_id) {
                        if (isset($all_post_meta_entries[ '_cm_post_terms_' . $group_id ])) {
                                $post_meta = $all_post_meta_entries[ '_cm_post_terms_' . $group_id ];
                        } else {
                                    $post_meta = get_post_meta($this->post_id, '_cm_post_terms_' . $group_id, true);
                                    $all_post_meta_entries[ '_cm_post_terms_' . $group_id ] = $post_meta;
                        }

                        $meta_terms_a = explode(',', $post_meta);

                // remove empty values
                        $meta_terms_a = array_filter($meta_terms_a, function ($v) {
                            return $v != '';
                        });
                        if (in_array($post_term_id, $meta_terms_a)) {
                                        // We found it in the post meta

                                      continue 2;
                        }
                    }

                    TagGroups_Error::verbose_log('[Tag Groups Pro] Post term %d was not found in the post meta and will therefore be added to all of its group to post %d.', $post_term_id, $this->post_id);
                // We get here only if it was not found in the post meta
                    foreach ($group_ids as $group_id) {
                            $post_meta = $all_post_meta_entries[ '_cm_post_terms_' . $group_id ];
                            $meta_terms_a = explode(',', $post_meta);

                          // remove empty values
                          $meta_terms_a = array_filter($meta_terms_a, function ($v) {
                            return $v != '';
                          });
                        $meta_terms_a[] = $post_term_id;
                        sort($meta_terms_a);
                        update_post_meta($this->post_id, '_cm_post_terms_' . $group_id, ',' . implode(',', $meta_terms_a) . ',');
                        $fixed_this_post = true;
                    }
                }
            }

            return $fixed_this_post ? 1 : 0;
        }


      /**
       * Updates the post meta after a post has been saved without meta box (MB is off, or quick edit, or API)
       *
       * The post already has all final post terms but we need to update the post meta accordingly
       *
       * @phpunit
       * @param void
       * @return void
       */
        public function update_meta()
        {

            $enabled_taxonomies = TagGroups_Taxonomy::get_enabled_taxonomies();
            $post_term_ids = wp_get_post_terms($this->post_id, $enabled_taxonomies, array( "fields" => "ids" ));
            $current_post_terms = $this->get_terms_by_group();
            $changed = false;
    /**
            * Check if a term was added
            */
            foreach ($post_term_ids as $post_term_id) {
                $term_is_in_post_meta = false;
    /**
               * Check if the term is already in the post meta
               */
                foreach ($current_post_terms as $group => $current_post_terms_in_group) {
      // if ( 0 == $group ) {
                    //
                    //   continue;
                    //
                    // }

                    if (in_array($post_term_id, $current_post_terms_in_group)) {
                        $term_is_in_post_meta = true;
                        break;
                    }
                }

                if (! $term_is_in_post_meta) {
                    $tg_term = new TagGroups_Term($post_term_id);
                    $groups_of_term = $tg_term->get_groups();
                    foreach ($groups_of_term as $group) {
                            $current_post_terms[ $group ][] = $post_term_id;
                            $changed = true;
                        if (0 == $group) {
      // an unassigned term has been added - possibly new => save to write meta and clear cache

                            $tg_term->save();
                        }
                    }
                }
            }


            /**
            * Check if a term was removed through quick edit
            */
            foreach ($current_post_terms as $group => $current_post_terms_in_group) {
                $removed_terms = array_diff($current_post_terms_in_group, $post_term_ids);
                if (! empty($removed_terms)) {
                    $current_post_terms[ $group ] = array_intersect($current_post_terms_in_group, $post_term_ids);
                    $changed = true;
                }
            }


            if ($changed) {
                $terms_for_metadata = array();
                $assigned_terms = array();
                foreach ($current_post_terms as $group_id => $current_post_term_ids) {
                    foreach ($current_post_term_ids as $current_post_term_id) {
                        $tg_term = new TagGroups_Term($current_post_term_id);
                        $terms_for_metadata[] = array(
                          'term_id'  => $current_post_term_id,
                          'term_group' => $group_id,
                          'taxonomy'  => $tg_term->get_taxonomy(),
                        );
                        if (0 != $group_id && ! in_array($current_post_term_id, $assigned_terms)) {
                                    $assigned_terms[] = $current_post_term_id;
                        }
                    }
                }

                $unassigned_terms = array_diff($post_term_ids, $assigned_terms);
                $this->set_terms($terms_for_metadata, $unassigned_terms, 'id')->save();
                $this->save_transient();
    // make sure the terms are saved in the post meta
                $this->fix_incorrect_post_terms($enabled_taxonomies);
    // Post count cache needs to be renewed
                if (class_exists('TagGroups_Premium_Post_Count_Cache')) {
                    TagGroups_Premium_Post_Count_Cache::renew_tag_groups_post_counts_cache();
                }
                do_action('tag_groups_post_tags_saved', $this->post_id);
            }
        }
    }


}
