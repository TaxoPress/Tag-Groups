<?php

// phpcs:disable WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput, WordPress.WP.AlternativeFunctions.json_encode_json_encode, Squiz.PHP.CommentedOutCode, WordPress.DB.SlowDBQuery -- Ajax handlers with nonce checks, input sanitized, json_encode for JS output

/**
* Tag Groups Pro
*
* @package     Tag Groups Pro
* @author      Christoph Amthor
* @copyright   2017 Christoph Amthor (@ Chatty Mango, chattymango.com)
* @license     see official vendor website
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

if (! class_exists('TagGroups_Shortcode_TPF_AJAX')) {
    class TagGroups_Shortcode_TPF_AJAX extends TagGroups_Post_Filter
    {
        public function __construct()
        {

            $this->result_ids_transient_name = 'tag_groups_tpf_result_ids_';
            $this->result_count_transient_name = 'tag_groups_tpf_result_count_';
            $this->default_image_src = TAG_GROUPS_PLUGIN_URL .  '/assets/images/default-image.png';
        }


      /**
      * AJAX handler, returns all posts that match the provided criteria
      *
      * Used by the Toggle Post Filter shortcode
      *
      * @phpunit
      * @param type var Description
      * @return return type
      */
        public function tg_ajax_tpf_get_posts()
        {

            $this->posts_count = -1;
            $this->cached_result_ids = false;
    /*
          * time in minutes that results remain in the cache
            */
            if (isset($_REQUEST['caching_time'])) {
                $this->caching_time = (int) $_REQUEST['caching_time'];
            } else {
                $this->caching_time = 10;
            }
            $this->caching_time = max(0, min(60, $this->caching_time));

            /*
          * timestamp to make sure that later requests don't overtake earlier ones and then get overwritten
            */
            if (! empty($_REQUEST['timestamp'])) {
                $this->timestamp = filter_var($_REQUEST['timestamp'], FILTER_SANITIZE_NUMBER_FLOAT);
    // don't use intval! It changes the value on 16-bit systems.
            } else {
                $this->timestamp = 0;
            }

            /*
          * paging
            */
            if (! empty($_REQUEST['paged'])) {
                $this->paged = (int) $_REQUEST['paged'];
            } else {
                $this->paged = 0;
            }
            $this->paged = max(0, min(100, $this->paged));

            if (! empty($_REQUEST['posts_per_page'])) {
                $this->posts_per_page = (int) $_REQUEST['posts_per_page'];
            } else {
                $this->posts_per_page = 5;
            }
            $this->posts_per_page = max(1, min(50, $this->posts_per_page));

            /*
          * default image
            */
            if (! empty($_REQUEST['default_image_src'])) {
                $this->default_image_src = esc_url($_REQUEST['default_image_src']);
            }

            $this->args = array(
            'posts_per_page' => $this->posts_per_page,
            'offset' => $this->paged * $this->posts_per_page,
            'post_status' => array( 'publish' ),
            'ignore_sticky_posts' => true
            );
    /*
          * sorting
            */
            if (! empty($_REQUEST['order'])) {
                $this->args['order'] = sanitize_title($_REQUEST['order']);
            }

            if (! empty($_REQUEST['orderby'])) {
                $this->args['orderby'] = sanitize_text_field($_REQUEST['orderby']);
    /**
               * If the value for orderby is a meta key (custom field), we must add it to the query
               */
                $default_order_values = array(
                'ID',
                'author',
                'title',
                'name',
                'type',
                'date',
                'modified',
                'parent',
                'rand',
                'comment_count',
                'relevance',
                'menu_order'
                );
                if (! in_array($this->args['orderby'], $default_order_values)) {
                    if (substr($this->args['orderby'], -4, 4) == '%num') {
                        $this->args['meta_key'] = substr($this->args['orderby'], 0, -4);
                        $this->args['orderby'] = 'meta_value_num';
                    } else {
                        $this->args['meta_key'] = $this->args['orderby'];
                        $this->args['orderby'] = 'meta_value';
                    }
                }
            }

            /*
          * text search (case insensitive, make lower case for caching)
            */
            if (! empty($_REQUEST['s'])) {
                $this->args['s'] = mb_strtolower(sanitize_textarea_field($_REQUEST['s']));
            }


            /*
          * taxonomy of selected terms
            */
            if (! empty($_REQUEST['taxonomy'])) {
                $this->taxonomy = sanitize_title(trim($_REQUEST['taxonomy']));
            } else {
                $this->taxonomy = 'post_tag';
            }

            /*
          * operator connecting the selected terms
            */
            if (! empty($_REQUEST['operator'])) {
                $this->operator = sanitize_text_field(strtoupper(trim($_REQUEST['operator'])));
            } else {
                $this->operator = 'IN';
            }

            /*
          * additional filtering by one static taxonomy (e.g. category)
            */
            if (! empty($_REQUEST['static_taxonomy']) && ! empty($_REQUEST['static_terms'])) {
    // The posts should additionally be filtered by a second, static taxonomy

              // sanitize the elements (integer)
                $terms = array_slice(array_map('intval', explode(',', $_REQUEST['static_terms'])), 0, 50);
                $this->args['tax_query'] = array(
                'relation' => 'AND',
                array(
                'taxonomy'  => sanitize_title($_REQUEST['static_taxonomy']),
                'field'     => 'term_id',
                'terms'     => $terms
                )
                );
            }


            /*
          * template for post output
            */
            if (! empty($_REQUEST['template'])) {
                $this->template = TagGroups_Options::wp_kses(TagGroups_Shortcode_Statics::decode_string($_REQUEST['template']));
            } else {
                $tg_templates = new TagGroups_Templates();
            // default template
                  $this->template = TagGroups_Options::get_option('tag_group_dpf_template', $tg_templates->get_html_of_default());
            }


            // WPML and Polylang https://polylang.pro/doc/wpml-api/
            $current_language = TagGroups_WPML::get_current_language();
            if ($current_language) {
                if ('all' == $current_language) {
                    $this->language = (string) apply_filters('wpml_default_language', null);
                } else {
                    $this->language = $current_language;
                }
            } else {
                $this->language = '';
            }


            if ('IN AND' == $this->operator || 'OR AND' == $this->operator || 'INAND' == $this->operator || 'ORAND' == $this->operator) {
                $this->args['meta_query'] = array(
                'relation'  => 'AND'
                );
                $this->group_relation = 'AND';
                $this->term_relation = 'OR';
            } elseif ('AND' == $this->operator) {
                $this->args['meta_query'] = array(
                'relation'  => 'AND'
                );
                $this->group_relation = 'AND';
                $this->term_relation = 'AND';
            } elseif ('IN' == $this->operator || 'OR' == $this->operator) {
                $this->args['meta_query'] = array(
                'relation'  => 'OR'
                );
                $this->group_relation = 'OR';
                $this->term_relation = 'OR';
            } elseif ('EXACT' == $this->operator) {
                $this->args['meta_query'] = array(
                'relation'  => 'EXACT'
                );
                $this->group_relation = 'AND';
                $this->term_relation = 'AND';
            } else {
                $operator_array = $this->parse_operator($this->operator);
                if (false == $operator_array) {
                    TagGroups_Error::log('[Tag Groups] Wrong operator in ' . $this->operator);
                    $this->args['meta_query'] = array(
                                  'relation'  => 'OR'
                                );
                    $this->group_relation = 'OR';
                    $this->term_relation = 'OR';
                } else {
                                  $this->args['meta_query'] = array(
                                  'relation'  => $operator_array['all']
                                                );
                                  $this->group_relation = $operator_array['all'];
                                  $this->term_relation = false;
                }
            }

            if (! empty($_REQUEST['terms']) && is_array($_REQUEST['terms'])) {
                foreach (array_slice($_REQUEST['terms'], 0, 20, true) as $group) {
                    if (empty($group['termids'])) {
                            continue;
                    }

                    if ($this->term_relation) {
                        $subquery = array(
                        'relation' => $this->term_relation
                                  );
                    } elseif (! empty($operator_array[ (int) $group['groupid'] ])) {
                        if ('EXACT' == $operator_array[ (int) $group['groupid'] ]) {
                                  $subquery = array(
                                      'relation' => 'AND'
                                  );
                        } else {
                            $subquery = array(
                            'relation' => $operator_array[ (int) $group['groupid'] ]
                            );
                        }
                    } else {
                        TagGroups_Error::log('[Tag Groups] No operator given for group ' . (int) $group['groupid']);
                        $subquery = array(
                        'relation' => 'OR'
                        );
                    }

            // sort so that we avoid duplicate cache for different tag order
                    $group['termids'] = array_slice(array_map('intval', (array) $group['termids']), 0, 50);
                    asort($group['termids']);
                    if ('EXACT' == $this->operator || ( isset($operator_array[ (int) $group['groupid'] ]) && 'EXACT' == $operator_array[ (int) $group['groupid'] ] )) {
                        $subquery[] =
                        array(
                        'key'         => '_cm_post_terms_' . (int) $group['groupid'],
                        'value'       => ',' . implode(',', $group['termids']) . ',',
                        'compare'     => '='
                        );
                    } else {
                        foreach ($group['termids'] as $term_id) {
                            $subquery[] =
                            array(
                            'key'         => '_cm_post_terms_' . (int) $group['groupid'],
                            'value'       => ',' . $term_id . ',', // WP adds %...% ; see https://core.trac.wordpress.org/browser/tags/5.1/src/wp-includes/class-wp-meta-query.php#L549
                            'compare'     => 'LIKE'
                            );
                        }
                    }

                    $this->args['meta_query'][] = $subquery;
                }
            }

        /**
        * Check if we have it in the cache
        */
            if ($this->caching_time > 0) {
                $cache_key = $this->get_cache_key_posts();
                $this->cached_result_ids = TagGroups_Transients::get_transient($this->result_ids_transient_name . $cache_key);
            } else {
                $this->cached_result_ids = false;
            }


            $this->args['post_type'] = TagGroups_Taxonomy::post_types_from_taxonomies($this->taxonomy);
            if ($this->cached_result_ids !== false) {
                TagGroups_Error::verbose_log('[Tag Groups] Found posts for Toggle Post Filter in Cache');
                if (count($this->cached_result_ids) == 0) {
                    $posts = array();
                } else {
        /**
                     * Retrieve the full posts
                     */
                    $posts = $this->get_posts_by_ids($this->cached_result_ids);
                }
            } else {
        /**
         Filter the query args before we retrieve posts

         @param array $this->args WP_Query arguments
         @return array valid arguments for WP_Query
*/
                $this->args = apply_filters('tag_groups_tpf_before_query', $this->args);
                if ($this->can_optimize_query()) {
                    add_filter('get_meta_sql', array( $this,'optimize_meta_sql' ));
                }

        /**
         We retrieve posts in two steps:
         First only IDs so that we don't run into memory problems with INNER JOIN of many posts and complex queries
*/
                $this->args['fields'] = 'ids';
                $this->post_query_with_row_count = new WP_Query($this->args);
                $post_ids = $this->post_query_with_row_count->posts;
                if ($this->can_optimize_query()) {
                /**
                             * Remove filter again so that it won't interfere with other database queries
                             */
                    remove_filter('get_meta_sql', array( $this,'optimize_meta_sql' ));
                }

                if (count($post_ids) > 0) {
                          $posts = $this->get_posts_by_ids($post_ids);
                } else {
                    $posts = array();
                }
            }


            $this->result = array();
/**
         * We construct a fresh array of post IDs because some posts my be password protected
         */
            $result_ids = array();
/**
         * Fill post data into template
         */
            foreach ($posts as $post) {
                if (( ! defined('TAG_GROUPS_SHOW_PASSWORD_PROTECTED_POSTS') || ! TAG_GROUPS_SHOW_PASSWORD_PROTECTED_POSTS ) && post_password_required($post)) {
                    continue;
                }

          // Don't overwrite the original $this->template, because we are in a loop and need it again.
                $this->result[] = array(
                'id'       => $post->ID,
                'content'  => $this->process_placeholders($post)
                );
                $result_ids[] = $post->ID;
            }

            if ($this->caching_time > 0 && $this->cached_result_ids === false) {
                TagGroups_Transients::set_transient($this->result_ids_transient_name . $cache_key, $result_ids, $this->caching_time * MINUTE_IN_SECONDS);
            }

            if (! empty($_REQUEST['count_total'])) {
            /**
             Get the total number of posts
*/
                $this->determine_post_count();
            }

            $this->construct_pager();
            echo json_encode(array(
            'data' => 'success',
            'posts' => $this->result,
            'count' => $this->posts_count,
            'pager' => $this->pager,
            'timestamp' => $this->timestamp
            ));
            TagGroups_Utilities::die();
        }
    }


}
