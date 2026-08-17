<?php

// phpcs:disable WordPress.Security.NonceVerification, WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize, WordPress.DB.SlowDBQuery, PSR12.Classes.ClosingBrace.StatementAfter -- nonce, serialize for caching, complex queries by design

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

if (! class_exists('TagGroups_Shortcode_Post_List')) {
    class TagGroups_Shortcode_Post_List extends TagGroups_Shortcode_Common
    {
          /**
           * attributes that we can use in the Gutenberg editor for server-side render
           *
           * @var array
           */
        public static $serverside_render_attributes = array(
        'source' => array(
        'type' => 'string',
        'default' => '',
        ),
        'article_class' => array(
        'type' => 'string',
        'default' => 'tg-post',
        ),
        'caching_time' => array(
        'type' => 'integer',
        'default' => 10,
        ),
        'default_image_src' => array(
        'type' => 'string',
        'default' => '',
        ),
        'display_amount' => array(
        'type' => 'integer',
        'default' => 0,
        ),
        'div_class' => array(
        'type' => 'string',
        'default' => '',
        ),
        'div_id' => array(
        'type' => 'string',
        'default' => '',
        ),
        'include' => array(
        'type' => 'string',
        'default' => '',
        ),
        'message_amount_plural' => array(
        'type' => 'string',
        'default' => '{count} posts found.',
        ),
        'message_amount_singular' => array(
        'type' => 'string',
        'default' => '1 post found.',
        ),
        'message_nothing_found' => array(
        'type' => 'string',
        'default' => 'Nothing found.',
        ),
        'message_load_more' => array(
        'type' => 'string',
        'default' => 'Load more',
        ),
        'message_go_back' => array(
        'type' => 'string',
        'default' => 'Go back',
        ),
        'operator' => array(
        'type' => 'string',
        'default' => 'IN',
        ),
        'order' => array(
        'type' => 'string',
        'default' => 'DESC',
        ),
        'orderby' => array(
        'type' => 'string',
        'default' => 'date',
        ),
        'pager' => array(
        'type' => 'integer',
        'default' => 0,
        ),
        'pager_position' => array(
        'type' => 'string',
        'default' => 'bottom',
        ),
        'posts_per_page' => array(
        'type' => 'integer',
        'default' => 5,
        ),
        'taxonomy' => array(
        'type' => 'string',
        'default' => '',
        ),
        'template' => array(
        'type' => 'string',
        'default' => '',
        ),
        );


      /**
      * Post List
      *
      * Shortcode that creates a static list of posts
      *
      * @phpunit
      * @param array $args
      * @return string
      */
        public function tag_groups_post_list($atts)
        {

            global $tag_group_groups;
            extract(shortcode_atts(array(
            'article_class' => 'tg-post',
            'author'  => null,
            'caching_time'  => null,
            'cat'  => null,
            'default_image_src' => TAG_GROUPS_PLUGIN_URL .  '/assets/images/default-image.png',
            'display_amount' => 0,
            'div_class' => '',
            'div_id'  => '',
            'do_not_cache' => false,
            'search' => null,
            'include' => null,
            'message_amount_plural' => __('{count} posts found.', 'tag-groups'),
            'message_amount_singular' => __('1 post found.', 'tag-groups'),
            'message_nothing_found' => __('Nothing found.', 'tag-groups'),
            'message_load_more' => __('Load more', 'tag-groups'),
            'message_go_back' => __('Go back', 'tag-groups'),
            'operator' => 'IN', // backwards compatibility, 'IN' used in Gutenberg block
            'order' => 'DESC',
            'orderby' => '',
            'pager' => 0,
            'pager_position'  => 'bottom',
            'posts_per_page' => 5,
            'source' => 'shortcode',
            'tag' => null,
            'tag__and' => null,
            'tag__in' => null,
            'tag__not_in' => null,
            'tag_slug__and' => null,
            'tag_slug__in' => null,
            'tag_id' => null,
            'taxonomy' => '',
            'template' => null,
            ), $atts));
    /**
             * Don't set it as default in extract( shortcode_atts() ) because the block sends an empty string
             */
            if (empty($div_id)) {
                $div_id = 'tag-groups-post-list-' . uniqid();
            }

            $cache_key = md5('post list' . serialize($atts) . $this->wpml_language);
    // add language

          // check for a cached version (premium plugin)
            $html = apply_filters('tag_groups_hook_cache_get', false, $cache_key);
            if ($html) {
                return $html;
            }

            $template = html_entity_decode($template);
            $template = TagGroups_Shortcode_Statics::decode_string($template);
    // Needed here because we call this shortcode from Gutenberg blocks

            $term_group_ids = $tag_group_groups->get_group_ids();
            if (! empty($include)) {
                $include_a = array_map('trim', explode(',', $include));
                $include_a = $tag_group_groups->expand_parents($include_a);
                $term_groups_included = array_intersect($term_group_ids, $include_a);
            } else {
                $term_groups_included = $term_group_ids;
            }


      /**
      * Retrieve the posts
      */

            $posts_count = -1;
/*
      * time in minutes that results remain in the cache
      */
            if (isset($caching_time)) {
                $caching_time = (int) $caching_time;
            } else {
                $caching_time = 0;
            }

      /*
      * paging - we use tg-list-paged so that we don't conflict with other paging
      */
            if (! empty($_REQUEST['tg-list-paged'])) {
                $paged = (int) $_REQUEST['tg-list-paged'];
            } else {
                $paged = 0;
            }

            if (! empty($posts_per_page)) {
                $posts_per_page = (int) $posts_per_page;
            } else {
                  $posts_per_page = 5;
            }

            $args = array(
            'posts_per_page' => $posts_per_page,
            'offset' => $paged * $posts_per_page,
            'post_status' => array( 'publish' ),
            'ignore_sticky_posts' => true
            );
/*
      * tags
      */
            if (! empty($tag) && !empty($taxonomy) && $taxonomy !== 'post_tag') {
                $query_term = explode(',', $tag);
                $query_term = array_filter($query_term);
                if (!empty($query_term)) {
                    $args['tax_query'] = [];
                    $taxonomy_args = [
                      [
                        'taxonomy' => $taxonomy,
                        'field' => 'slug',
                        'terms' => $query_term
                      ]
                    ];
                    $args['tax_query'][] = $taxonomy_args;
                }
            } elseif (! empty($tag)) {
                $args['tag'] = $tag;
            }

            if (! empty($tag_id)) {
                $args['tag_id'] = $tag_id;
            }

            if (! empty($tag__and)) {
                $args['tag__and'] = array_map('trim', explode(',', $tag__and));
            }

            if (! empty($tag__in)) {
                $args['tag__in'] = array_map('trim', explode(',', $tag__in));
            }

            if (! empty($tag__not_in)) {
                $args['tag__not_in'] = array_map('trim', explode(',', $tag__not_in));
            }

            if (! empty($tag_slug__and)) {
                $args['tag_slug__and'] = array_map('trim', explode(',', $tag_slug__and));
            }

            if (! empty($tag_slug__in)) {
                $args['tag_slug__in'] = array_map('trim', explode(',', $tag_slug__in));
            }


      /*
      * author
      */
            if (! empty($author)) {
                $args['author'] = $author;
            }

      /*
      * category
      */
            if (! empty($cat)) {
                $args['cat'] = $cat;
            }

      /*
      * search for keyword
      */
            if (! empty($search)) {
                $args['s'] = $search;
            }

      /*
      * sorting
      */
            if (! empty($order)) {
                $args['order'] = $order;
            }

            if (! empty($orderby)) {
                $args['orderby'] = $orderby;
            }


      /*
      * taxonomy of selected terms
      */
            if (empty($taxonomy)) {
                $taxonomy_array = TagGroups_Taxonomy::get_enabled_taxonomies();
            } else {
                $taxonomy_array = array_map('trim', explode(',', $taxonomy));
            }

      /*
      * operator connecting the selected terms
      */
            if (! empty($operator)) {
                $operator = strtoupper(trim($operator));
            } else {
                $operator = 'OR';
            }


            if ('AND' != $operator && 'OR' != $operator) {
      // 'IN' will be treated as OR

                $operator = 'OR';
            }


      // WPML and Polylang https://polylang.pro/doc/wpml-api/
            $current_language = TagGroups_WPML::get_current_language();
            if ($current_language) {
                if ('all' == $current_language) {
                    $language = (string) apply_filters('wpml_default_language', null);
                } else {
                    $language = $current_language;
                }
            } else {
                    $language = '';
            }



        /*
      * template for post output
        */
            if (empty($template)) {
                $tg_templates = new TagGroups_Templates();
    // default template
                $template = TagGroups_Options::get_option('tag_group_dpf_template', $tg_templates->get_html_of_default());
            }


            $args['post_type'] = TagGroups_Taxonomy::post_types_from_taxonomies($taxonomy_array);
// sort so that we avoid duplicate cache for different group order
            asort($term_groups_included);
            if ($caching_time > 0) {
                $relevant_parameters_for_cache_key = array(
                $operator,
                $template,
                $taxonomy_array,
                $posts_per_page,
                $paged,
                $term_groups_included,
                $language,
                $default_image_src,
                );
                if (! empty($order)) {
                    $relevant_parameters_for_cache_key['order'] = $order;
                }

                if (! empty($orderby)) {
                    $relevant_parameters_for_cache_key['orderby'] = $orderby;
                }

                if (isset($args['tax_query'])) {
                    $relevant_parameters_for_cache_key['tax_query'] = $args['tax_query'];
                }

                asort($relevant_parameters_for_cache_key);
                $cache_key = md5(serialize($relevant_parameters_for_cache_key));
                $tgp_post_list_result = TagGroups_Transients::get_transient('tag_groups_post_list_result_' . $cache_key);
            } else {
                      $tgp_post_list_result = false;
            }

            if ($tgp_post_list_result !== false) {
                $result = $tgp_post_list_result;
            } else {
                $first_result = true;
                $result_per_group_ids = array();
                $args_groups = array(
                      'post_type' => TagGroups_Taxonomy::post_types_from_taxonomies($taxonomy_array),
                      'posts_per_page' => -1,
                      'offset' => 0,
                      'post_status' => array( 'publish' ),
                      'fields' => 'ids',
                      'ignore_sticky_posts' => true
                );
                foreach ($term_groups_included as $term_group_included) {
                    $args_groups['meta_query'] = array(
                    'relation'  => 'AND'
                    );
                    $args_groups['meta_query'][] = array(
                    'key'         => '_cm_post_terms_' . $term_group_included,
                    'compare'  => 'EXISTS'
                    );
                    $post_query = new WP_Query($args_groups);
                    $post_ids = $post_query->posts;
                    if ('OR' == $operator) {
                        $result_per_group_ids = array_merge($result_per_group_ids, $post_ids);
                    } else {
                // AND

                        if ($first_result) {
                            $result_per_group_ids = $post_ids;
                            $first_result = false;
                        } else {
                            $result_per_group_ids = array_intersect($result_per_group_ids, $post_ids);
                        }

                        if (count($result_per_group_ids) == 0) {
                        // we don't need to search any further - the results will always remain empty

                              break;
                        }
                    }
                }

                $args['post__in'] = $result_per_group_ids;
                if (isset($args['post__in']) && empty($args['post__in'])) {
                        $posts = array();
                        $groups_only_empty_result = true;
                } else {
                      $post_query = new WP_Query($args);
                      $posts = $post_query->posts;
                }

                $result = array();
  // Fill post data into template
                foreach ($posts as $post) {
                    if (post_password_required($post)) {
                                  continue;
                    }

                          // Don't overwrite the original $template, because we are in a loop and need it again.

                            $placeholders = new TagGroups_Placeholders($post, $template, $default_image_src);
                    // post ID
                            $placeholders->process_post_id();
                    // post title
                            $placeholders->process_post_title();
                    // link to full post
                            $placeholders->process_permalink();
                    // link to full post
                            $placeholders->process_post_guid();
                    // post excerpt
                            $placeholders->process_post_excerpt();
                    // post excerpt w/ html
                            $placeholders->process_post_excerpt_html();
                    // post date
                            $placeholders->process_post_date();
                    // post author
                            $placeholders->process_post_author();
                    // src attribute
                            $placeholders->process_image_src();
                    // alt attribute
                            $placeholders->process_image_alt();
                    // categories
                            $placeholders->process_post_category();
                    // tags
                            $placeholders->process_post_tags();
                    // custom fields
                            $placeholders->process_custom_fields();
                    // other taxonomies
                            $placeholders->process_other_taxonomies();
                            $result[] = array(
                            'id'       => $post->ID,
                            'content'  => $placeholders->get_output()
                            );
                }


                if ($caching_time > 0) {
                    TagGroups_Transients::set_transient('tag_groups_post_list_result_' . $cache_key, $result, $caching_time * MINUTE_IN_SECONDS);
                }
            }


        /**
        * Get the total number of posts
        */
            if (! empty($display_amount) || $pager) {
                $relevant_parameters_for_cache_key = array(
                $operator,
                $taxonomy_array,
                $term_groups_included,
                $language
                );
                asort($relevant_parameters_for_cache_key);
                $cache_key_count = md5(serialize($relevant_parameters_for_cache_key));
                if (empty($result) || ( isset($groups_only_empty_result) && $groups_only_empty_result )) {
                    $posts_count = 0;
                    if ($caching_time > 0) {
                        TagGroups_Transients::set_transient('tag_groups_post_list_result_count_' . $cache_key_count, $posts_count, $caching_time * MINUTE_IN_SECONDS);
                    }
                } else {
                            /**
                                        * Check for cached data
                                        */

                    if ($caching_time > 0) {
                        $posts_count = TagGroups_Transients::get_transient('tag_groups_post_list_result_count_' . $cache_key_count);
                    } else {
                        $posts_count = false;
                    }

                    if ($posts_count == false) {
                    // nothing from the cache

                    /**
                     Check if we can skip the second query
*/
                        if (isset($post_query)) {
                              $posts_count = $post_query->found_posts;
                        } elseif ($args['posts_per_page'] == -1 && $args['offset'] == 0) {
                            $posts_count = count($result);
                        } else {
                            $args_count = $args;
                            $args_count['posts_per_page'] = -1;
                            $args_count['offset'] = 0;
                            $args_count['fields'] = 'ids';
                            $post_query = new WP_Query($args_count);
                            $posts_count = $post_query->found_posts;
                        }

                        if ($caching_time > 0) {
                            TagGroups_Transients::set_transient('tag_groups_post_list_result_count_' . $cache_key_count, $posts_count, $caching_time * MINUTE_IN_SECONDS);
                        }
                    }
                }
            } // display_amount



            $view = new TagGroups_View('shortcodes/post_list');
// construct the pager
            if ($pager) {
                $result_pager = array(
                'up' => false,
                'down' => false
                );
                if ($paged > 0) {
                    // This is not the first page -> offer option to go back
                      $result_pager['down'] = true;
                }

                if (count($result) == $posts_per_page) {
      // This page is full -> check if we have more posts on the next page

                  // check if we already know the total amount
                    if (isset($post_query)) {
                        $posts_on_next_page = $post_query->found_posts - ( $paged + 1 ) * $posts_per_page;
                    } else {
                        if ($caching_time > 0) {
                // try to retrieve it from cache

                            $relevant_parameters_for_cache_key = array(
                              $operator,
                              $template,
                              $taxonomy_array,
                              $posts_per_page, // cannot use 1 because we need to match previous criteria
                              $paged + 1,
                              $term_groups_included,
                              $language
                            );
                            if (! empty($order)) {
                                              $relevant_parameters_for_cache_key['order'] = $order;
                            }

                            if (! empty($orderby)) {
                                $relevant_parameters_for_cache_key['orderby'] = $orderby;
                            }

                            asort($relevant_parameters_for_cache_key);
                            $cache_key_pager = md5(serialize($relevant_parameters_for_cache_key));
                            $tgp_post_list_result = TagGroups_Transients::get_transient('tag_groups_post_list_result_' . $cache_key_pager);
                        } else {
                            $tgp_post_list_result = false;
                        }

                        if ($tgp_post_list_result !== false) {
                            // We found the next page in the cache

                            $posts_on_next_page = $tgp_post_list_result;
                        } else {
                                        // We have to retrieve it

                            $args['posts_per_page'] = 1;
                                        // here enough to know if it is >= 1

                            $args['offset'] = ( $paged + 1 ) * $posts_per_page;
                                        $posts = get_posts($args);
                                        $posts_on_next_page = is_array($posts) ? count($posts) : 0;
                        }
                    }

                    if ($posts_on_next_page > 0) {
        // We have more posts -> offer option to load next page
                        $result_pager['up'] = true;
                    }
                }

                $result_pager['total_pages'] = ceil($posts_count / $posts_per_page);
                $result_pager['page'] = $paged + 1;
                $view->set('result_pager', $result_pager);
            }


            $pager_top = '';
            $pager_bottom = '';
            if ($pager == 1) {
                $view_pager = new TagGroups_View('partials/post_list_pager');
                $view_pager->set(array(
                      'pager_data'        => $result_pager,
                      'mesage_go_back'    => $message_go_back,
                      'message_load_more' => $message_load_more,
                      'paged'             => $paged,
                ));
                if ('top' == $pager_position || 'both' == $pager_position) {
                    $pager_top = $view_pager->return_html();
                }

                if ('bottom' == $pager_position || 'both' == $pager_position) {
                    $pager_bottom = $view_pager->return_html();
                }
            } elseif ($pager == 2) {
                $view_pager = new TagGroups_View('partials/post_list_pagination');
                $view_pager->set(array(
                      'pager_data'        => $result_pager,
                ));
                if ('top' == $pager_position || 'both' == $pager_position) {
                    $pager_top = $view_pager->return_html();
                }

                if ('bottom' == $pager_position || 'both' == $pager_position) {
                          $pager_bottom = $view_pager->return_html();
                }
            }


        /**
        * Construct the post list
        */
            if (! empty($display_amount) && $posts_count > 0) {
                $message_amount = ( $posts_count == 1 ) ? $message_amount_singular : $message_amount_plural;
                $message_amount = str_replace('{count}', $posts_count, $message_amount);
                $view->set('message_amount', $message_amount);
            }

            $message_nothing_found = htmlentities(str_replace("'", "/'", $message_nothing_found), ENT_QUOTES, "UTF-8");
            $article_class = TagGroups_Shortcode_Statics::sanitize_html_classes($article_class);
            $view->set(array(
            'article_class'           => $article_class,
            'div_id'                  => $div_id,
            'div_class'               => $div_class,
            'message_go_back'         => $message_go_back,
            'message_load_more'       => $message_load_more,
            'message_nothing_found'   => $message_nothing_found,
            'pager_top'               => $pager_top,
            'pager_bottom'            => $pager_bottom,
            'paged'                   => $paged,
            'result'                  => $result,
            ));
            $html = $view->return_html();
            if (! $do_not_cache) {
            // create a cached version (premium plugin)
                  do_action('tag_groups_hook_cache_set', $this->cache_key, $html);
            }

            return $html;
        }
    } //class


}
