<?php

// phpcs:disable WordPress.Security.NonceVerification, WordPress.DB.SlowDBQuery, WordPressVIPMinimum.Performance.RegexpCompare, Squiz.PHP.CommentedOutCode -- Nonce verified before use, complex queries by design, commented code is documentation

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
 * @package     Tag Groups Pro
 *
 * @author      Christoph Amthor
 * @copyright   2018 Christoph Amthor (@ Chatty Mango, chattymango.com)
 * @license     see official vendor website
 *
 * @since      1.8.0
 */

if (!class_exists('TagGroups_Transients')) {
    require_once(TAG_GROUPS_PLUGIN_ABSOLUTE_PATH . '/include/helpers/cache/class.transients.php');
}

if (! class_exists('TagGroups_Posts')) {
    class TagGroups_Posts
    {
          /**
           * All post terms from transient
           *
           * array(
           *   $post_id_1 = > array(
           *     $group_id_1 => array( $term_id_1, $term_id_2, .. ),
           *     $group_id_2 => array( $term_id_3, $term_id_4, .. ),
           *     ...
           *   ),
           *   $post_id_2 => ...
           * )
           *
           * @var array
           */
          private $all_post_terms;

          /**
           * Constructor
           *
           * @param  int    $term_group optional term_group
           * @return return type
           */
        public function __construct()
        {

            $this->load();
            return $this;
        }

      /**
       * Load the transient from the database
       *
       * @phpunit
       * @param  void
       * @return object $this
       */
        public function load()
        {

            $this->all_post_terms = TagGroups_Transients::get_transient('tag_groups_post_terms');
            return $this;
        }

      /**
       * Setter for $all_post_terms
       *
       * @phpunit
       * @param  array    $all_post_terms
       * @return object
       */
        public function set_all_post_terms($all_post_terms)
        {

            $this->all_post_terms = $all_post_terms;
            return $this;
        }

      /**
       * Getter for $all_post_terms
       *
       * @phpunit
       * @param  void
       * @return array
       */
        public function get_all_post_terms()
        {

            return $this->all_post_terms;
        }

      /**
       * Save the data of all post terms to the transient
       *
       * @phpunit
       * @param  void
       * @return void
       */
        public function save_transient()
        {

            if (is_array($this->all_post_terms)) {
                TagGroups_Transients::set_transient('tag_groups_post_terms', $this->remove_empty_items($this->all_post_terms), DAY_IN_SECONDS);
            }
        }

      /**
       * Adds the data for one post to the transient
       *
       * @phpunit
       * @param  int    $post_id
       * @param  array  $terms
       * @return void
       */
        public function add_to_transient($post_id, $terms)
        {

            if (empty($terms)) {
                return;
            }

            if (! is_array($this->all_post_terms)) {
                $this->all_post_terms = array();
            }

            $this->all_post_terms[$post_id] = $terms;
            $this->save_transient();
        }

      /**
       * Removes elements from an array that are identified by their values.
       *
       * @phpunit
       * @param  array $array From where to remove the elements
       * @param  mixed $value Value of elements that need to be removed; if $value is an array, then all elements will be processed.
       * @return array The initial $array without the removed elements
       */
        public function remove_elements_from_array($array, $value)
        {

            if (is_array($value)) {
                $keys = array();
                foreach ($value as $element) {
                    $keys = array_merge($keys, array_keys($array, $element));
                }
            } else {
                $keys = array_keys($array, $value);
            }

            if (! empty($keys)) {
                foreach ($keys as $key) {
                    unset($array[$key]);
                }
            }

            return $array;
        }

      /**
       * Returns the meta post query part to filter the list of posts by a term group
       *
       * @phpunit
       * @deprecated since 1.26.0
       *
       * @param  int     $term_group
       * @return array
       */
        public function get_meta_query_group($term_group)
        {

            TagGroups_Error::deprecated();
            return array(
            array(
            'key'     => '_cm_post_terms_' . $term_group,
            'compare' => 'EXISTS',
            ),
            );
        }

      /**
       * Adds a a table after a post with the post tags, sorted by their tag groups
       *
       * @phpunit
       * @param  string   $content
       * @return string
       */
        public function add_groups_and_tags_after_content($content)
        {

            global $post, $tag_group_groups;
    /**
             * We are treating WooCommerce products separately
             */

            if (is_object($post) && 'product' == $post->post_type && TagGroups_Options::get_option('tag_group_remove_the_product_tags', false)) {
                return $content;
            }

            $include = TagGroups_Options::get_option('tag_group_display_groups_under_posts', $tag_group_groups->get_group_ids());
            if (
                ( is_single() && TagGroups_Options::get_option('tag_group_display_groups_under_posts_single', false) )
                || ( is_feed() && TagGroups_Options::get_option('tag_group_display_groups_under_posts_feed', false) )
                || ( is_home() && TagGroups_Options::get_option('tag_group_display_groups_under_posts_home', false) )
                || ( is_archive() && TagGroups_Options::get_option('tag_group_display_groups_under_posts_archive', false) )
            ) {
            /**
                       * Add the table with the groups and tags
                       */
                if (class_exists('TagGroups_Premium_Post_Terms')) {
                    $content .= TagGroups_Premium_Post_Terms::get_table(array( 'return' => 'html', 'include' => $include, 'separator' => TagGroups_Options::get_option('tag_group_display_groups_under_posts_separator', '&nbsp;|&nbsp;') ));
                }
            }

            return $content;
        }

      /**
       * Sets the array of tags to empty so that the template won't display tags.
       *
       * @phpunit
       * @param  array   $term_links
       * @return array
       */
        public function remove_the_post_terms($term_links)
        {

            return array();
        }

      /**
       * Checks if post meta needs to be processed, after saving without meta box (MB is off, or quick edit, or API)
       *
       * @phpunit
       * @param  integer $post_id
       * @param  object  $post      WP post object
       * @return void
       */
        public function maybe_process_post($post_id, $post)
        {

            /**
             * Check if our nonce is set => We will process the return values from the Meta Box
             *
             */

            if (isset($_POST['tag-groups-posts-meta-box-nonce'])) {
                return $post_id;
            }

            /**
             * omit autosaves
             *
             */

            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    // apparently not equivalent to wp_is_post_autosave( $post_id )

                return $post_id;
            }

            /**
             * omit revisions
             *
             */

            if ('revision' == $post->post_type) {
                return $post_id;
            }

            // Check the user's permissions
            $post_type_object = get_post_type_object($post->post_type);
            if (is_object($post_type_object) && is_object($post_type_object->cap) && ! current_user_can($post_type_object->cap->edit_post, $post_id)) {
                return $post_id;
            }

            /**
             * check if there was a multisite switch before
             *
             */

            if (is_multisite() && ms_is_switched()) {
    /**
               * since version 1.3: In rare cases, the content of $GLOBALS['_wp_switched_stack'] will never be reset => The post is saved, but without the tags. Solution: undo switching
               * Replacing: return $post_id;
               */
                $GLOBALS['_wp_switched_stack'] = array();
                $GLOBALS['switched']           = false;
            }

            $tag_group_meta_box_taxonomies = TagGroups_Options::get_option('tag_group_meta_box_taxonomy', array());
            if (
                ! defined('XMLRPC_REQUEST')
                && ! ( isset($_REQUEST['action']) && 'inline-save' == $_REQUEST['action'] ) // carry on for quickedit
                && ! ( isset($_REQUEST['action']) && 'editpost' == $_REQUEST['action'] && empty($tag_group_meta_box_taxonomies) ) // carry on for Classic post screen without meta box
                && ! ( empty($_POST) && empty($tag_group_meta_box_taxonomies) ) // carry on for Gutenberg post screen without meta box
            ) {
                return $post_id;
            }

            if (isset($_POST['tg_taxonomy'])) {
                return $post_id;
            }

            $tg_post = new TagGroups_Post($post_id);
            $tg_post->update_meta();
            do_action('purge_post_list_transients', $post_id, $post);
            return true;
        }

      /**
       * remove items with empty values:
       * - posts without tags
       * - tag groups without tags
       *
       * Notes:
       * - We need to keep the keys
       *
       * @phpunit
       * @param  array   $target_array
       * @return array
       */
        public function remove_empty_items($target_array)
        {

            foreach ($target_array as $key => $value) {
                if (empty($value)) {
                    unset($target_array[$key]);
                } elseif (is_array($value)) {
                    $target_array[$key] = $this->remove_empty_items($value);
                    if (empty($target_array[$key])) {
                        unset($target_array[$key]);
                    }
                }
            }

            return $target_array;
        }

      /**
       * Returns all post IDs were post meta is not enclossed by commas
       *
       * Omits deleted posts. No extra caching (done by get_posts)
       * Note: The meta query can become very slow
       *
       * @param  void
       * @return array
       */
        public function get_post_ids_old_format()
        {

            global $tag_group_groups;
            $term_groups = $tag_group_groups->get_group_ids();
    /**
             * We will need the taxonomies
             */
            $enabled_taxonomies = TagGroups_Taxonomy::get_enabled_taxonomies();
    /**
             * We need an array with numeric indices, because wp-includes/taxonomy.php will search for $taxonomy[0]
             */

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
            $post_args['meta_query'] = array(
            'relation' => 'OR',
            );
            foreach ($term_groups as $term_group) {
                $post_args['meta_query'][] = array(
                'key'     => '_cm_post_terms_' . (int) $term_group,
                'value'   => '^,.*,$',
                'compare' => 'NOT REGEXP',
                );
            }

            return get_posts($post_args);
        }

      /**
       * Modifies the query to show on term archives only posts that belong to the right tag group
       *
       * @phpunit
       * @param  object $query WP Query object
       * @return return type
       */
        public function term_archive_filter($query)
        {

            if (! $query->is_main_query()) {
                return $query;
            }

            if (! empty($_GET['term_group']) && ! empty($_GET['term_id'])) {
                $term_group = (int) $_GET['term_group'];
                $term_id = (int) $_GET['term_id'];
                $query->set('meta_query', array(
                array(
                  'key'   => '_cm_post_terms_' . $term_group,
                  'value' => ',' . $term_id . ',', // WP adds %...% ; see https://core.trac.wordpress.org/browser/tags/5.1/src/wp-includes/class-wp-meta-query.php#L549
                  'compare' => 'LIKE',
                ),
                ));
            } elseif (! empty($_GET['term_group'])) {
                $term_group = (int) $_GET['term_group'];

                  $query->set(
                      'meta_query',
                      array(
                      array(
                      'key'     => '_cm_post_terms_' . $term_group,
                      'compare' => 'EXISTS',
                      ),
                      )
                  );
            }

            return $query;
        }

      /**
       * Modifies the query to show only posts that belong to particular tag group
       *
       * @phpunit
       * @param  array            $args      WP Query args
       * @param  array|int|string $group_ids Tag Group IDs (array of integers or comma-separated list of integers)
       * @param  string           $relation  Logic relation between the Tag Group IDs (and|or)
       * @return array
       */
        public function modify_query_args($args, $group_ids = null, $relation = 'OR')
        {

            global $tag_group_groups;

            if (empty($group_ids)) {
                return $args;
            }

            if (! is_array($group_ids)) {
                $group_ids = explode(',', $group_ids);
            }

            $group_ids = array_map('intval', $group_ids);
            /**
             * intval also trims spaces
             *
             */

            if (strtoupper($relation) != 'OR') {
                $relation = 'AND';
            }

            $meta_query = array( 'relation' => $relation );

            $group_ids = array_intersect($group_ids, $tag_group_groups->get_group_ids());

            if (count($group_ids) == 0) {
              // never matches -> create dummy condition that never is true
                $meta_query[] = array(
                'key'     => '_cm_post_terms_dummy',
                'compare' => 'EXISTS',
                );
            } else {
                foreach ($group_ids as $group_id) {
                    $meta_query[] = array(
                    'key'     => '_cm_post_terms_' . $group_id,
                    'compare' => 'EXISTS',
                    );
                }
            }

            $args['meta_query'] = $meta_query;

            return $args;
        }

      /**
       * Modifies the query to show only posts that belong to particular tag group
       *
       * @phpunit
       * @param  object           $query     WP Query object
       * @param  array|int|string $group_ids Tag Group IDs (array of integers or comma-separated list of integers)
       * @param  string           $relation  Logic relation between the Tag Group IDs (and|or)
       * @return object
       */
        public function modify_query($query, $group_ids = null, $relation = 'OR')
        {

            global $tag_group_groups;

            if (empty($group_ids)) {
                return $query;
            }

            if (! is_array($group_ids)) {
                $group_ids = explode(',', $group_ids);
            }

            $group_ids = array_map('intval', $group_ids);
            /**
             * intval also trims spaces
             *
             */

            if (strtoupper($relation) != 'OR') {
                $relation = 'AND';
            }

            $meta_query = array( 'relation' => $relation );

            $group_ids = array_intersect($group_ids, $tag_group_groups->get_group_ids());

            if (count($group_ids) == 0) {
              // never matches -> create dummy condition that never is true
                $meta_query[] = array(
                'key'     => '_cm_post_terms_dummy',
                'compare' => 'EXISTS',
                );
            } else {
                foreach ($group_ids as $group_id) {
                    $meta_query[] = array(
                    'key'     => '_cm_post_terms_' . $group_id,
                    'compare' => 'EXISTS',
                    );
                }
            }

            $query->set('meta_query', $meta_query);

            return $query;
        }
    }


}
