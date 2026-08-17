<?php

// phpcs:disable WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize, WordPress.DB.SlowDBQuery -- serialize for caching, complex queries by design

/**
 * Tag Groups Pro
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * @package    Tag Groups Pro
 *
 * @author     Christoph Amthor
 * @copyright  2018 Christoph Amthor (@ Chatty Mango, chattymango.com)
 * @license    see official vendor website
 *
 * @since      1.39.0
 */

if (! class_exists('TagGroups_Post_Filter')) {
/**
   *  shared methods and properties of all post filters
   *
   */
    class TagGroups_Post_Filter
    {
        protected $args;
        protected $operator;
        protected $taxonomy;
        protected $posts_per_page;
        protected $paged;
        protected $language;
        protected $groups_only_ids;
        protected $template;
        protected $default_image_src;
        protected $sample_post;
        protected $pager;
        protected $post_query_with_row_count;
        protected $cached_result_ids;
        protected $result;
        protected $posts_count;
        protected $timestamp;
        protected $result_ids_transient_name;
        protected $result_count_transient_name;
        protected $caching_time;
        protected $groups_only_empty_result;
        protected $group_relation;
        protected $term_relation;


      /**
       * Retrieves post objects corresponding to a list of post IDs
       *
       * @param int[] $post_ids
       * @return object[]
       */
        public function get_posts_by_ids($post_ids)
        {

            if (empty($post_ids)) {
                return array();
            }

            $args = array(
            'posts_per_page'      => -1,
            'post_status'         => array( 'publish' ),
            'ignore_sticky_posts' => true,
            'post__in'            => $post_ids,
            'post_type'           => TagGroups_Taxonomy::post_types_from_taxonomies($this->taxonomy),
            'no_found_rows'       => true,
            );
            if (isset($this->args['meta_key'])) {
                  $args['meta_key'] = $this->args['meta_key'];
            }

            if (isset($this->args['order'])) {
                $args['order'] = $this->args['order'];
            }

            if (isset($this->args['orderby'])) {
                $args['orderby'] = $this->args['orderby'];
            }

            $post_query_full_posts_by_id = new WP_Query($args);
            return $post_query_full_posts_by_id->posts;
        }

      /**
       * generates the key used for caching the posts on the requested page
       *
       * @phpunit
       * @param  void
       * @return string
       */
        public function get_cache_key_posts()
        {

            $relevant_parameters_for_cache_key = array(
            $this->operator,
            $this->taxonomy,
            $this->posts_per_page,
            $this->paged,
            $this->language,
            );
            if (! empty($this->args['order'])) {
                  $relevant_parameters_for_cache_key['order'] = $this->args['order'];
            }

            if (! empty($this->args['orderby'])) {
                $relevant_parameters_for_cache_key['orderby'] = $this->args['orderby'];
            }

            if (isset($this->args['tax_query'])) {
                $relevant_parameters_for_cache_key['tax_query'] = $this->args['tax_query'];
            }

            if (isset($this->args['meta_query'])) {
                $relevant_parameters_for_cache_key['meta_query'] = $this->args['meta_query'];
            }

            if (isset($this->args['meta_key'])) {
                $relevant_parameters_for_cache_key['meta_key'] = $this->args['meta_key'];
            }

            if (isset($this->args['s'])) {
                $relevant_parameters_for_cache_key['s'] = $this->args['s'];
            }

            if (! empty($this->groups_only_ids)) {
                $relevant_parameters_for_cache_key['groups_only_ids'] = $this->groups_only_ids;
            }

            return md5(serialize($relevant_parameters_for_cache_key));
        }

      /**
       * generates the key used for caching the posts on the next page
       *
       * @phpunit
       * @param  void
       * @return string
       */
        public function get_cache_key_next_posts()
        {

            $relevant_parameters_for_cache_key = array(
            $this->operator,
            $this->taxonomy,
            $this->posts_per_page, // cannot use 1 because we need to match previous criteria
            $this->paged + 1,
            $this->language,
            );
            if (! empty($this->args['order'])) {
                  $relevant_parameters_for_cache_key['order'] = $this->args['order'];
            }

            if (! empty($this->args['orderby'])) {
                $relevant_parameters_for_cache_key['orderby'] = $this->args['orderby'];
            }

            if (isset($this->args['tax_query'])) {
                $relevant_parameters_for_cache_key['tax_query'] = $this->args['tax_query'];
            }

            if (isset($this->args['meta_query'])) {
                $relevant_parameters_for_cache_key['meta_query'] = $this->args['meta_query'];
            }

            if (isset($this->args['meta_key'])) {
                $relevant_parameters_for_cache_key['meta_key'] = $this->args['meta_key'];
            }

            if (isset($this->args['s'])) {
                $relevant_parameters_for_cache_key['s'] = $this->args['s'];
            }

            if (isset($this->groups_only_ids)) {
                $relevant_parameters_for_cache_key['groups_only_ids'] = $this->groups_only_ids;
            }

            return md5(serialize($relevant_parameters_for_cache_key));
        }

      /**
       * generates the key used for caching the post count
       *
       * @phpunit
       * @param  void
       * @return string
       */
        public function get_cache_key_post_count()
        {

            $relevant_parameters_for_cache_key = array(
            $this->operator,
            $this->taxonomy,
            $this->language,
            );
            if (isset($this->args['tax_query'])) {
                  $relevant_parameters_for_cache_key['tax_query'] = $this->args['tax_query'];
            }

            if (isset($this->args['meta_query'])) {
                $relevant_parameters_for_cache_key['meta_query'] = $this->args['meta_query'];
            }

            if (isset($this->args['s'])) {
                $relevant_parameters_for_cache_key['s'] = $this->args['s'];
            }

            if (! empty($this->groups_only_ids)) {
                $relevant_parameters_for_cache_key['groups_only_ids'] = $this->groups_only_ids;
            }

            return md5(serialize($relevant_parameters_for_cache_key));
        }

      /**
       * Processes all possible placeholders
       *
       * @phpunit
       * @param  object   $post
       * @return string
       */
        public function process_placeholders($post)
        {

            $placeholders = new TagGroups_Placeholders($post, $this->template, $this->default_image_src, $this->sample_post);
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
            return $placeholders->get_output();
        }

      /**
       * Parses the operator and creates an array for fine-grained operators
       *
       * @phpunit
       * @param  void
       * @return array|boolean
       */
        public function parse_operator()
        {

            if (empty($this->operator)) {
                return false;
            }

            $return_data = array(
            'all' => 'OR',
            );
            $array_groups = explode('|', $this->operator);
            foreach ($array_groups as $array_group) {
                  $group_operator = explode(':', $array_group);
                if (count($group_operator) != 2) {
                    return false;
                }

                if ('ALL' == strtoupper($group_operator[0])) {
                    switch (strtoupper($group_operator[1])) {
                        case 'AND':
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       $return_data['all'] = 'AND';

                            break;
                        case 'OR':
                        case 'IN':
                            $return_data['all'] = 'OR';

                            break;
                        default:
                            return false;
                        break;
                    }
                } elseif (is_numeric($group_operator[0]) && (int) $group_operator[0] >= 0) {
                    switch (strtoupper($group_operator[1])) {
                        case 'AND':
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              $return_data[(int) $group_operator[0]] = 'AND';

                            break;
                        case 'EXACT':
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        $return_data[(int) $group_operator[0]] = 'EXACT';

                            break;
                        case 'OR':
                        case 'IN':
                            $return_data[(int) $group_operator[0]] = 'OR';

                            break;
                        default:
                            return false;
                        break;
                    }
                } else {
                    return false;
                }
            }

            return $return_data;
        }

      /**
       * makes all required calculations for the pager
       *
       * Note: We have to count also password-protected posts that won't appear
       *
       * @return void
       */
        public function construct_pager()
        {

          /*
         * construct the pager
           */
            $this->pager = array(
            'up'          => false,
            'down'        => false,
            'total_pages' => 0,
            'page'        => 1,
            );
            if ($this->paged > 0) {
                  // This is not the first page -> offer option to go back
                  $this->pager['down'] = true;
            }

            if (count($this->result) == $this->posts_per_page) {
    /**
               * This page is full -> check if we have more posts on the next page
               */

              /**
               * check if we already know the total amount
               */
                if (isset($this->post_query_with_row_count)) {
                    $number_of_posts_on_next_page = $this->post_query_with_row_count->found_posts - ( $this->paged + 1 ) * $this->posts_per_page;
                } else {
                    if ($this->caching_time > 0) {
            // try to retrieve it from cache

                          $cache_key_pager = $this->get_cache_key_next_posts();
                        $this->cached_result_ids = TagGroups_Transients::get_transient($this->result_ids_transient_name . $cache_key_pager);
                    } else {
                        $this->cached_result_ids = false;
                    }

                    if (false !== $this->cached_result_ids) {
                            // We found the next page in the cache

                            $number_of_posts_on_next_page = count($this->cached_result_ids);
                    } else {
                        // We have to retrieve it

                              $this->args['posts_per_page'] = 1;
                        // here enough to know if it is >= 1

                              $this->args['offset'] = ( $this->paged + 1 ) * $this->posts_per_page;
                        $posts = get_posts($this->args);
                        $number_of_posts_on_next_page = is_array($posts) ? count($posts) : 0;
                    }
                }

                if ($number_of_posts_on_next_page > 0) {
      // We have more posts -> offer option to load next page
                    $this->pager['up'] = true;
                }
            }

            $this->pager['total_pages'] = ceil($this->posts_count / $this->posts_per_page);
            $this->pager['page'] = $this->paged + 1;
        }

      /**
       * Get the total number of posts
       *
       * Note: We have to count also password-protected posts that won't appear
       *
       * @return void
       */
        public function determine_post_count()
        {

            $cache_key_count = $this->get_cache_key_post_count();
            if (empty($this->result) || ( isset($this->groups_only_empty_result) && $this->groups_only_empty_result )) {
                    $this->posts_count = 0;
                if ($this->caching_time > 0) {
                    TagGroups_Transients::set_transient($this->result_count_transient_name . $cache_key_count, $this->posts_count, $this->caching_time * MINUTE_IN_SECONDS);
                }
            } else {
                  /**
                             * Check for cached data
                             */

                if ($this->caching_time > 0) {
                    $this->posts_count = TagGroups_Transients::get_transient($this->result_count_transient_name . $cache_key_count);
                } else {
                    $this->posts_count = false;
                }

                if (false == $this->posts_count) {
      /**
                   *  nothing from the cache
                  */

      /**
       * Check if we can skip the second query
       */
                    if (isset($this->post_query_with_row_count)) {
                        $this->posts_count = $this->post_query_with_row_count->found_posts;
                    } elseif (-1 == $this->args['posts_per_page'] && 0 == $this->args['offset']) {
                        $this->posts_count = count($this->result);
                    } else {
                        $args_count                   = $this->args;
                        $args_count['posts_per_page'] = -1;
                        $args_count['offset']         = 0;
                        $args_count['fields']         = 'ids';
                        $post_query_count_posts = new WP_Query($args_count);
                        $this->posts_count = $post_query_count_posts->found_posts;
                    }

                    $this->posts_count = (int) $this->posts_count;
                    if ($this->caching_time > 0) {
                        TagGroups_Transients::set_transient($this->result_count_transient_name . $cache_key_count, $this->posts_count, $this->caching_time * MINUTE_IN_SECONDS);
                    }
                }
            }
        }

      /**
       * Returns a sample post
       *
       * @phpunit
       * @param  void
       * @return array
       */
        public function get_sample_posts()
        {

            $post = (object) array(
            'ID'           => -99,
            'post_title'   => 'De finibus bonorum et malorum',
            'post_content' => '<p>Recusandae <b>eveniet architecto magni</b> enim voluptatibus quis alias tenetur. Neque quia quis et voluptatem in aperiam. Libero atque odit ea provident. Nisi illo sequi incidunt.</p><h3>Qui aliquam autem similique possimus et id. Odio corrupti et expedita quos ab qui.</h3>',
            'post_date'    => 'now',
            'post_author'  => 'Marcus Tullius Cicero',
            'post_excerpt' => '',
            );
            $posts = array(
            0 => $post,
            );
            return $posts;
        }

      /**
       * reduces complexity of "inner join" parts for performance
       * called via get_meta_sql filter - works only for "OR"
       *
       * credits: https://stackoverflow.com/a/15398104
       *
       * @param  array   $meta_sql
       * @return array
       */
        public function optimize_meta_sql($meta_sql)
        {

            if (defined('TAG_GROUPS_SIMPLIFY_QUERY') && ! TAG_GROUPS_SIMPLIFY_QUERY) {
                return $meta_sql;
            }

            global $wpdb;
            $posts_table = $wpdb->prefix . 'posts';
            $postmeta_table = $wpdb->prefix . 'postmeta';
  //use single INNER JOIN
            $meta_sql['join'] = " INNER JOIN {$postmeta_table} AS pmta ON ({$posts_table}.ID = pmta.post_id) ";
  //replace the mtNN aliases with wp_postmeta
            $where_clause = $meta_sql['where'];
            $where_clause = str_replace("{$postmeta_table}.", 'pmta.', $where_clause);
            $where_clause = preg_replace('/mt\d+\.meta_/i', 'pmta.meta_', $where_clause);
            $meta_sql['where'] = $where_clause;
            return $meta_sql;
        }

      /**
       * Checks if we can use a simplified query
       *
       * @return boolean
       */
        public function can_optimize_query()
        {

            return 'OR' == strtoupper($this->group_relation);
        }
    }


}
