<?php

// phpcs:disable WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn, WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude, WordPressVIPMinimum.Performance.RemoteRequestTimeout, WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize, Squiz.PHP.CommentedOutCode, PSR12.Classes.ClosingBrace.StatementAfter -- exclude param, remote timeout, serialize for caching by design

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

if (! class_exists('TagGroups_Shortcode_TPF')) {
    class TagGroups_Shortcode_TPF extends TagGroups_Shortcode_Common
    {
          /**
           * attributes that we can use in the Gutenberg editor for server-side render
           *
           * @var array
           */
        public static $serverside_render_attributes_menu_legacy = array(
        'source' => array(
        'type' => 'string',
        'default' => '',
        ),
        'accordion' => array(
        'type' => 'integer',
        'default' => 0,
        ),
        'caching_time' => array(
        'type' => 'integer',
        'default' => 10,
        ),
        'default_show_posts' => array(
        'type' => 'integer',
        'default' => 0,
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
        'exclude_terms' => array(
        'type' => 'string',
        'default' => '',
        ),
        'hide_empty' => array(
        'type' => 'integer',
        'default' => 1,
        ),
        'include' => array(
        'type' => 'string',
        'default' => '',
        ),
        'include_terms' => array(
        'type' => 'string',
        'default' => '',
        ),
        'layout' => array(
        'type' => 'string',
        'default' => 'classic',
        ),
        'legacy' => array(
        'type' => 'integer',
        'default' => 1,
        ),
        'message_amount_plural' => array(
        'type' => 'string',
        'default' => '{count} posts found.',
        ),
        'message_amount_singular' => array(
        'type' => 'string',
        'default' => '1 post found.',
        ),
        'message_go_back' => array(
        'type' => 'string',
        'default' => 'Go back',
        ),
        'message_load_more' => array(
        'type' => 'string',
        'default' => 'Load more',
        ),
        'message_nothing_found' => array(
        'type' => 'string',
        'default' => 'Nothing found.',
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
        'persistent_filter' => array(
        'type' => 'integer',
        'default' => 30,
        ),
        'placeholder_text_search' => array(
        'type' => 'string',
        'default' => 'type here',
        ),
        'posts_per_page' => array(
        'type' => 'integer',
        'default' => 5,
        ),
        'posts_placeholder' => array(
        'type' => 'string',
        'default' => 'Please select a tag.',
        ),
        'preset_tags' => array(
        'type' => 'string',
        'default' => '',
        ),
        'static_taxonomy' => array(
        'type' => 'string',
        'default' => '',
        ),
        'static_terms' => array(
        'type' => 'string',
        'default' => '',
        ),
        'taxonomy' => array(
        'type' => 'string',
        'default' => '',
        ),
        'template' => array(
        'type' => 'string',
        'default' => '',
        ),
        'term_order' => array(
        'type' => 'string',
        'default' => '',
        ),
        'term_orderby' => array(
        'type' => 'string',
        'default' => 'name',
        ),
        'text_search' => array(
        'type' => 'integer',
        'default' => 0,
        ),
        'title_text_search' => array(
        'type' => 'string',
        'default' => 'Text Search',
        ),
        'transition' => array(
        'type' => 'string',
        'default' => 'fade',
        ),
        );
    /**
           * attributes that we can use in the Gutenberg editor for server-side render
           *
           * @var array
           */
        public static $serverside_render_attributes_menu = array(
        'source' => array(
        'type' => 'string',
        'default' => '',
        ),
        'accordion' => array(
        'type' => 'integer',
        'default' => 0,
        ),
        'caching_time' => array(
        'type' => 'integer',
        'default' => 10,
        ),
        'div_class' => array(
        'type' => 'string',
        'default' => '',
        ),
        'div_id' => array(
        'type' => 'string',
        'default' => '',
        ),
        'exclude' => array(
        'type' => 'string',
        'default' => '',
        ),
        'exclude_terms' => array(
        'type' => 'string',
        'default' => '',
        ),
        'hide_empty' => array(
        'type' => 'integer',
        'default' => 1,
        ),
        'icon_class' => array(
        'type' => 'string',
        'default' => '',
        ),
        'include' => array(
        'type' => 'string',
        'default' => '',
        ),
        'include_terms' => array(
        'type' => 'string',
        'default' => '',
        ),
        'layout' => array(
        'type' => 'string',
        'default' => 'classic',
        ),
        'legacy' => array(
        'type' => 'integer',
        'default' => 0,
        ),
        'one_only_groups' => array(
        'type' => 'string',
        'default' => '',
        ),
        'operator' => array(
        'type' => 'string',
        'default' => 'IN',
        ),
        'persistent_filter' => array(
        'type' => 'integer',
        'default' => 30,
        ),
        'placeholder_text_search' => array(
        'type' => 'string',
        'default' => 'type here',
        ),
        'preset_tags' => array(
        'type' => 'string',
        'default' => '',
        ),
        'selected_tag_color' => array(
        'type' => 'string',
        'default' => '#e05500', //'#0089e0',
        ),
        'slider_width' => array(
        'type' => 'integer',
        'default' => 600,
        ),
        'static_taxonomy' => array(
        'type' => 'string',
        'default' => '',
        ),
        'static_terms' => array(
        'type' => 'string',
        'default' => '',
        ),
        'tag_color' => array(
        'type' => 'string',
        'default' => '#ddd',
        ),
        'taxonomy' => array(
        'type' => 'string',
        'default' => '',
        ),
        'term_order' => array(
        'type' => 'string',
        'default' => '',
        ),
        'term_orderby' => array(
        'type' => 'string',
        'default' => 'name',
        ),
        'text_search' => array(
        'type' => 'integer',
        'default' => 0,
        ),
        'theme' => array(
        'type' => 'string',
        'default' => 'light',
        ),
        'timeout' => array(
        'type' => 'integer',
        'default' => 1000,
        ),
        'title_text_search' => array(
        'type' => 'string',
        'default' => 'Text Search',
        ),
        );
    /**
           * attributes that we can use in the Gutenberg editor for server-side render
           *
           * @var array
           */
        public static $serverside_render_attributes_body = array(
        'source' => array(
        'type' => 'string',
        'default' => '',
        ),
        'default_image_src' => array(
        'type' => 'string',
        'default' => '',
        ),
        'default_show_posts' => array(
        'type' => 'integer',
        'default' => 0,
        ),
        'display_amount' => array(
        'type' => 'integer',
        'default' => 2,
        ),
        'div_class' => array(
        'type' => 'string',
        'default' => '',
        ),
        'div_id' => array(
        'type' => 'string',
        'default' => 'tag_groups_dpf_toggle_body',
        ),
        'layout' => array(
        'type' => 'string',
        'default' => 'classic',
        ),
        'legacy' => array(
        'type' => 'integer',
        'default' => 0,
        ),
        'message_amount_plural' => array(
        'type' => 'string',
        'default' => '{count} posts found.',
        ),
        'message_amount_singular' => array(
        'type' => 'string',
        'default' => '1 post found.',
        ),
        'message_go_back' => array(
        'type' => 'string',
        'default' => 'Go back',
        ),
        'message_load_more' => array(
        'type' => 'string',
        'default' => 'Load more',
        ),
        'message_nothing_found' => array(
        'type' => 'string',
        'default' => 'Nothing found.',
        ),
        'order' => array(
        'type' => 'string',
        'default' => 'DESC',
        ),
        'orderby' => array(
        'type' => 'string',
        'default' => 'date',
        ),
        'posts_per_page' => array(
        'type' => 'integer',
        'default' => 5,
        ),
        'pager' => array(
        'type' => 'integer',
        'default' => 0,
        ),
        'pager_position' => array(
        'type' => 'string',
        'default' => 'bottom',
        ),
        'template' => array(
        'type' => 'string',
        'default' => '',
        ),
        'theme' => array(
        'type' => 'string',
        'default' => 'light',
        ),
        'transition' => array(
        'type' => 'string',
        'default' => 'fade',
        ),
        );
    /**
           * attributes that we can use in the Gutenberg editor for server-side render
           *
           * @var array
           */
        public static $serverside_render_attributes_messages = array(
        'source' => array(
        'type' => 'string',
        'default' => '',
        ),
        );
    /**
           * attributes that we can use in the Gutenberg editor for server-side render
           *
           * @var array
           */
        public static $serverside_render_attributes_slider_button = array(
        'source' => array(
        'type' => 'string',
        'default' => '',
        ),
        'button_class' => array(
        'type' => 'string',
        'default' => '',
        ),
        'button_text' => array(
        'type' => 'string',
        'default' => 'Filter',
        ),
        'theme' => array(
        'type' => 'string',
        'default' => 'light',
        ),
        );
    /**
           * attributes that we can use in the Gutenberg editor for server-side render
           *
           * @var array
           */
        public static $serverside_render_attributes_reset = array(
        'source' => array(
        'type' => 'string',
        'default' => '',
        ),
        'button_class' => array(
        'type' => 'string',
        'default' => '',
        ),
        'button_text' => array(
        'type' => 'string',
        'default' => 'Reset filter',
        ),
        'theme' => array(
        'type' => 'string',
        'default' => 'light',
        ),
        );
    /**
           * attributes that we can use in the Gutenberg editor for server-side render
           *
           * @var array
           */
        public static $serverside_render_attributes_order_menu = array(
        'source' => array(
        'type' => 'string',
        'default' => '',
        ),
        'div_class' => array(
        'type' => 'string',
        'default' => 'tg_dpf_order_menu',
        ),
        'order_text' => array(
        'type' => 'string',
        'default' => 'Order:',
        ),
        'orderby_text' => array(
        'type' => 'string',
        'default' => 'Order by:',
        ),
        'order_options' => array(
        'type' => 'string',
        'default' => 'desc:↓|asc:↑',
        ),
        'orderby_options' => array(
        'type' => 'string',
        'default' => 'date:date|author:author|title:title',
        ),
        'sumoselect' => array(
        'type' => 'integer',
        'default' => 1,
        ),
        'theme' => array(
        'type' => 'string',
        'default' => 'light',
        ),
        );
    /**
           * attributes that we can use in the Gutenberg editor for server-side render
           *
           * @var array
           */
        public static $serverside_render_attributes_text_search = array(
        'source' => array(
        'type' => 'string',
        'default' => '',
        ),
        'placeholder' => array(
        'type' => 'string',
        'default' => 'type here',
        ),
        'search_trigger' => array(
        'type' => 'integer',
        'default' => 2,
        ),
        );




      /**
       * wrapper for legacy shortcode
       *
       * @phpunit
       * @param array $atts
       * @return string
       */
        public function tag_groups_dpf_toggle_menu($atts)
        {

            if (is_array($atts)) {
                $atts['legacy'] = 1;
            }

            $this->legacy_shortcode_message('tag_groups_dpf_toggle_menu', 'tag_groups_tpf_menu');
            return $this->tag_groups_tpf_menu($atts);
        }


      /**
       * wrapper for legacy shortcode
       *
       * @phpunit
       * @param array $atts
       * @return string
       */
        public function tag_groups_dpf_toggle_body($atts)
        {

            if (is_array($atts)) {
                $atts['legacy'] = 1;
            }

            $this->legacy_shortcode_message('tag_groups_dpf_toggle_body', 'tag_groups_tpf_body');
            return $this->tag_groups_tpf_body($atts);
        }


      /**
       * wrapper for legacy shortcode
       *
       * @phpunit
       * @param array $atts
       * @return string
       */
        public function tag_groups_dpf_toggle_messages($atts)
        {

            if (is_array($atts)) {
                $atts['legacy'] = 1;
            }

            $this->legacy_shortcode_message('tag_groups_dpf_toggle_messages', 'tag_groups_tpf_messages');
            return $this->tag_groups_tpf_messages($atts);
        }


      /**
       * wrapper for legacy shortcode
       *
       * @phpunit
       * @param array $atts
       * @return string
       */
        public function tag_groups_dpf_toggle_reset($atts)
        {

            if (is_array($atts)) {
                $atts['legacy'] = 1;
            }

            $this->legacy_shortcode_message('tag_groups_dpf_toggle_reset', 'tag_groups_tpf_reset');
            return $this->tag_groups_tpf_reset($atts);
        }


      /**
       * Log a deprecation message
       *
       * @phpunit
       * @param string $shortcode_old
       * @param string $shortcode_new
       * @return void
       */
        public function legacy_shortcode_message($shortcode_old, $shortcode_new)
        {

            TagGroups_Error::verbose_log('[Tag Groups] Shortcode %s is deprecated, please replace by %s', $shortcode_old, $shortcode_new);
        }


      /**
       * Toggle Post Filter - menu part
       *
       * Shortcode that creates a 2-level filter with toggles to display posts
       *
       * @phpunit
       * @param array $args
       * @return string
       */
        public function tag_groups_tpf_menu($atts)
        {

            global $tag_group_groups;
            extract(shortcode_atts(array(
            'accordion' => 0,
            'caching_time' => 10,
            'default_show_posts' => 0,
            'display_amount' => 2,
            'div_class' => '',
            'div_id' => '',
            'do_not_cache' => false,
            'exclude' => null,
            'exclude_terms' => '',
            'hide_empty' => 1,
            'icon_class'   => '',
            'include' => null,
            'include_terms' => '',
            'layout'  => 'classic', // 'wide'
            'legacy'  => 0,
            'message_amount_plural' => __('{count} posts found.', 'tag-groups'),
            'message_amount_singular' => __('1 post found.', 'tag-groups'),
            'message_go_back' => __('Go back', 'tag-groups'),
            'message_load_more' => __('Load more', 'tag-groups'),
            'message_nothing_found' => __('Nothing found.', 'tag-groups'),
            'one_only_groups' => '',
            'operator' => 'IN',
            'order' => 'DESC',
            'orderby' => '',
            'pager' => 0,
            'persistent_filter' => 30,
            'placeholder_text_search' => __('type here', 'tag-groups'),
            'posts_per_page' => 5,
            'posts_placeholder' => __('Please select a tag.', 'tag-groups'),
            'preset_tags' => '', // comma-separated list of slugs
            'selected_tag_color'  => '#e05500', // '#0089e0', // for the _tags layouts
            'slider_width'  => 600, // only available for sliders with tags
            'source' => 'shortcode',
            'static_taxonomy' => '',
            'static_terms' => '', // comma-separated list of IDs
            'tag_color'  => '#ddd', // for the _tags layouts
            'taxonomy' => '',
            'template' => null, // template is maybe encoded
            'term_order' => null,
            'term_orderby' => 'name',
            'text_search' => 0, // 0: off; 1: on enter; 2: enter or timed
            'theme' => 'light',
            'timeout' => 1000,
            'title_text_search' => __('Text Search', 'tag-groups'),
            'transition' => 'fade',
            ), $atts));
            $cache_key = md5('TPF menu' . serialize($atts));
    // check for a cached version (premium plugin)
            $html = apply_filters('tag_groups_hook_cache_get', false, $cache_key);
            if ($html) {
                return $html;
            }

            $term_group_ids = $tag_group_groups->get_group_ids_by_position();
            if (! is_null($include) && '' != $include) {
                $include_a = array_map('intval', explode(',', $include));
                $include_a = $tag_group_groups->expand_parents($include_a);
                $term_groups_included = array_intersect($term_group_ids, $include_a);
            } else {
                $term_groups_included = $term_group_ids;
            }

            if (! is_null($exclude) && '' != $exclude) {
                $exclude_a = array_map('intval', explode(',', $exclude));
                $exclude_a = $tag_group_groups->expand_parents($exclude_a);
                $term_groups_included = array_diff($term_groups_included, $exclude_a);
            }

            if (empty($div_id)) {
                $div_id = 'tg_filter_box_toggle';
            }

            $taxonomy = TagGroups_Taxonomy::get_enabled_taxonomies($taxonomy);
            $include_terms = str_replace(' ', '', $include_terms);
            $exclude_terms = str_replace(' ', '', $exclude_terms);
            $include_terms_array = explode(',', $include_terms);
            $exclude_terms_array = explode(',', $exclude_terms);
            $div_class = $this->set_theme_to_class($div_class, $theme);
/**
         * Initial selection of terms via shortcode parameter
         */
            if (! empty($preset_tags)) {
                $preset_term_slugs = explode(',', str_replace(' ', '', $preset_tags));
                $preset_term_slugs = array_map('sanitize_title', $preset_term_slugs);
            } else {
                $preset_term_slugs = array();
            }


      /*
      * Create the URL for Ajax calls
      */
            $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
            $ajax_link = admin_url('admin-ajax.php', $protocol);
            $toggle_groups = array();
            foreach ($term_groups_included as $term_group_included) {
                $tg_group = new TagGroups_Group($term_group_included);
                $terms = $tg_group->get_group_terms($taxonomy, $hide_empty, 'all', 0, $term_orderby, $term_order);
                foreach ($terms as $key => $term) {
                    if (! empty($include_terms) && ! in_array($term->term_id, $include_terms_array)) {
                              unset($terms[ $key ]);
                    }

                    if (! empty($exclude_terms) && in_array($term->term_id, $exclude_terms_array)) {
                        unset($terms[ $key ]);
                    }
                }

                if (count($terms)) {
                    $toggle_groups[] = array(
                    'group_id'  => $term_group_included,
                    'label'     => $tg_group->get_label(),
                    'terms'     => $terms
                    );
                }
            }

            $need_partial_button_state = false;
            $need_partial_tag_state = false;
            $is_slider = false;
            $inline_js_view = '';
            switch ($layout) {
                case 'plain':
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          $view_layout = 'shortcodes/tpf_menu_plain';
                    $div_class .= ' tg_filter_box_toggle_plain';
                    $inline_js_view = 'partials/tpf_inline_js_generic_vertical';
                    $one_only_groups_view = 'partials/tpf_one_only_groups_plain';


                    break;
                case 'wide':
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      $view_layout = 'shortcodes/tpf_menu_wide';
                    $div_class .= ' tg_filter_box_toggle_wide';
                    $inline_js_view = 'partials/tpf_inline_js_generic_horizontal';
                    $one_only_groups_view = 'partials/tpf_one_only_groups_toggle';


                    break;
                case 'button':
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      $view_layout = 'shortcodes/tpf_menu_button';
                    $div_class .= ' tg_filter_box_toggle_button tg_uses_buttons';
                    $need_partial_button_state = true;
                    $inline_js_view = 'partials/tpf_inline_js_generic_vertical';
                    $one_only_groups_view = 'partials/tpf_one_only_groups_button';


                    break;
                case 'wide_button':
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      $view_layout = 'shortcodes/tpf_menu_wide_button';
                    $div_class .= ' tg_filter_box_toggle_wide_button tg_uses_buttons';
                    $need_partial_button_state = true;
                    $inline_js_view = 'partials/tpf_inline_js_generic_horizontal';
                    $one_only_groups_view = 'partials/tpf_one_only_groups_button';


                    break;
                case 'wide_tags':
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      $view_layout = 'shortcodes/tpf_menu_wide_tags';
                    $div_class .= ' tg_filter_box_toggle_wide_tags';
                    $need_partial_tag_state = true;
                    $inline_js_view = 'partials/tpf_inline_js_generic_horizontal';
                    $one_only_groups_view = 'partials/tpf_one_only_groups_tags';


                    break;
                case 'slider_left':
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      $view_layout = 'shortcodes/tpf_menu_slider_left';
                    $div_class .= ' tg_filter_box_toggle_slider_left tg_uses_buttons';
                    $need_partial_button_state = true;
                    $inline_js_view = 'partials/tpf_inline_js_slider_left';
                    $one_only_groups_view = 'partials/tpf_one_only_groups_button';
                    $is_slider = true;


                    break;
                case 'slider_right':
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      $view_layout = 'shortcodes/tpf_menu_slider_right';
                    $div_class .= ' tg_filter_box_toggle_slider_right tg_uses_buttons';
                    $need_partial_button_state = true;
                    $inline_js_view = 'partials/tpf_inline_js_slider_right';
                    $one_only_groups_view = 'partials/tpf_one_only_groups_button';
                    $is_slider = true;


                    break;
                case 'slider_left_tags':
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      $view_layout = 'shortcodes/tpf_menu_slider_left_tags';
                    $div_class .= ' tg_filter_box_toggle_slider_left';
                    $need_partial_tag_state = true;
                    $inline_js_view = 'partials/tpf_inline_js_slider_left';
                    $one_only_groups_view = 'partials/tpf_one_only_groups_tags';
                    $is_slider = true;


                    break;
                case 'slider_right_tags':
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      $view_layout = 'shortcodes/tpf_menu_slider_right_tags';
                    $div_class .= ' tg_filter_box_toggle_slider_right';
                    $need_partial_tag_state = true;
                    $inline_js_view = 'partials/tpf_inline_js_slider_right';
                    $one_only_groups_view = 'partials/tpf_one_only_groups_tags';
                    $is_slider = true;


                    break;
                case 'classic_tags':
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      $view_layout = 'shortcodes/tpf_menu_tags';
                    $div_class .= ' tg_filter_box_toggle_left';
                // float left

                        $need_partial_tag_state = true;
                    $inline_js_view = 'partials/tpf_inline_js_generic_vertical';
                    $one_only_groups_view = 'partials/tpf_one_only_groups_tags';


                    break;
                case 'classic':
                default:
                    $view_layout = 'shortcodes/tpf_menu';
                    $div_class .= ' tg_filter_box_toggle_left';
          // float left

                    $inline_js_view = 'partials/tpf_inline_js_generic_vertical';
                    $one_only_groups_view = 'partials/tpf_one_only_groups_toggle';


                    break;
            }

            $view_generic = new TagGroups_View('partials/tpf_inline_js_generic_options_init');
            $view_generic->set(array(
            'accordion'                 => $accordion,
            'ajax_link'                 => $ajax_link,
            'cache_key'                 => $cache_key,
            'caching_time'              => $caching_time,
            'default_show_posts'        => $default_show_posts,
            'display_amount'            => $display_amount,
            'div_id'                    => $div_id,
            'do_not_cache'              => $do_not_cache,
            'is_slider'                 => $is_slider,
            'legacy'                    => $legacy,
            'message_amount_plural'     => $message_amount_plural,
            'message_amount_singular'   => $message_amount_singular,
            'message_go_back'           => $message_go_back,
            'message_load_more'         => $message_load_more,
            'message_nothing_found'     => $message_nothing_found,
            'operator'                  => $operator,
            'order'                     => $order,
            'orderby'                   => $orderby,
            'pager'                     => $pager,
            'persistent_filter'         => $persistent_filter,
            'posts_per_page'            => $posts_per_page,
            'posts_placeholder'         => $posts_placeholder,
            'preset_term_slugs'         => $preset_term_slugs,
            'static_taxonomy'           => $static_taxonomy,
            'static_terms'              => $static_terms,
            'taxonomy'                  => implode(',', $taxonomy),
            'template'                  => html_entity_decode(is_string($template) ? $template : ''),
            'term_groups_included'      => $term_groups_included,
            'text_search'               => $text_search,
            'timeout'                   => $timeout,
            'transition'                => $transition,
            ));
            $html = $view_generic->return_html();
            $view_layout = new TagGroups_View($view_layout);
            $view_layout->set(array(
            'autocomplete'              => ! empty($persistent_filter) ? 'on' : 'off',
            'div_class'                 => $div_class,
            'div_id'                    => $div_id,
            'icon_class'                => $icon_class,
            'placeholder_text_search'   => $placeholder_text_search,
            'slider_width'              => $slider_width,
            'source'                    => $source,
            'text_search'               => $text_search,
            'toggle_groups'             => $toggle_groups,
            'title_text_search'         => $title_text_search,
            ));
            $html .= $view_layout->return_html();
            if ($need_partial_button_state) {
                $view_buttons = new TagGroups_View('partials/tpf_change_button_state');
                $html .= $view_buttons->return_html();
            }

            if ($need_partial_tag_state) {
                $view_tags = new TagGroups_View('partials/tpf_change_tag_state');
                $html .= $view_tags->return_html();
                $view_tag_style = new TagGroups_View('partials/tpf_tag_style');
                $view_tag_style->set(array(
                'selected_tag_color'  => $selected_tag_color,
                'tag_color'           => $tag_color,
                ));
                $html .= $view_tag_style->return_html();
            }

            if ($inline_js_view) {
                $view_inline_js = new TagGroups_View($inline_js_view);
                $view_inline_js->set('div_id', $div_id);
      // needed for vertical layouts

                $html .= $view_inline_js->return_html();
            }

            if (! empty($one_only_groups) && ! empty($one_only_groups_view)) {
                $view_one_only_groups = new TagGroups_View($one_only_groups_view);

                $view_one_only_groups->set(
                    'groups',
                    explode(',', $one_only_groups)
                );
                $html .= $view_one_only_groups->return_html();
            }

            if (! $do_not_cache) {
      // create a cached version (premium plugin)
                do_action('tag_groups_hook_cache_set', $this->cache_key, $html);
            }

            return $html;
        }


      /**
       * Toggle Post Filter - body part
       *
       * Shortcode that creates a 2-level filter with toggles to display posts
       *
       * @phpunit
       * @param array $args
       * @return string
       */
        public function tag_groups_tpf_body($atts)
        {

            extract(shortcode_atts(array(
            'default_image_src' => '',
            'default_show_posts' => 0,
            'display_amount' => 2,
            'div_class' => '',
            'div_id'    => 'tag_groups_dpf_toggle_body',
            'layout'    => 'classic', // 'wide', 'boxed', 'masonry', 'masonry-keep-together'
            'legacy'  => 0,
            'message_amount_plural' => __('{count} posts found.', 'tag-groups'),
            'message_amount_singular' => __('1 post found.', 'tag-groups'),
            'message_go_back' => __('Go back', 'tag-groups'),
            'message_load_more' => __('Load more', 'tag-groups'),
            'message_nothing_found' => __('Nothing found.', 'tag-groups'),
            'order' => 'DESC',
            'orderby' => '',
            'pager' => false,
            'pager_position' => 'bottom',
            'posts_per_page' => 5,
            'posts_placeholder' => __('Please select a tag.', 'tag-groups'),
            'template' => null, // template is maybe encoded
            'theme' => 'light',
            'transition' => 'fade',
            ), $atts));
            $div_class = $this->set_theme_to_class($div_class, $theme);
            switch ($layout) {
                case 'masonry':
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                $div_class .= ' tag_groups_dpf_toggle_body_masonry';


                    break;
                case 'masonry-small':
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              $div_class .= ' tag_groups_dpf_toggle_body_masonry_small';


                    break;
                case 'masonry-large':
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              $div_class .= ' tag_groups_dpf_toggle_body_masonry_large';


                    break;
                case 'columns':
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          $div_class .= ' tag_groups_dpf_toggle_body_columns';


                    break;
                case 'columns-keep-together':
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          $div_class .= ' tag_groups_dpf_toggle_body_columns_keep_together';


                    break;
                case 'wide':
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          $div_class .= ' tag_groups_dpf_toggle_body_wide tg_dpf_article_clear';


                    break;
                case 'boxed':
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          $div_class .= ' tag_groups_dpf_toggle_body_boxed';


                    break;
                case 'classic':
                default:
                    $div_class .= ' tag_groups_dpf_toggle_body tg_dpf_article_clear';


                    break;
            }

            $view = new TagGroups_View('shortcodes/tpf_body');
            $view->set(array(
            'default_image_src'         => $default_image_src,
            'default_show_posts'        => $default_show_posts,
            'display_amount'            => $display_amount,
            'div_id'                    => $div_id,
            'div_class'                 => $div_class,
            'layout'                    => str_replace("'", '', $layout),
            'legacy'                    => $legacy,
            'message_amount_plural'     => $message_amount_plural,
            'message_amount_singular'   => $message_amount_singular,
            'message_go_back'           => $message_go_back,
            'message_load_more'         => $message_load_more,
            'message_nothing_found'     => $message_nothing_found,
            'order'                     => $order,
            'orderby'                   => $orderby,
            'pager'                     => $pager,
            'pager_position'            => $pager_position,
            'posts_placeholder'         => $posts_placeholder,
            'posts_per_page'            => $posts_per_page,
            'template'                  => html_entity_decode(is_string($template) ? $template : ''),
            'transition'                => $transition,
            ));
            return $view->return_html();
        }


      /**
       * Toggle Post Filter - message part
       *
       * Shortcode that creates a 2-level filter with toggles to display posts
       *
       * @phpunit
       * @param array $args
       * @return string
       */
        public function tag_groups_tpf_messages($atts)
        {

            $view = new TagGroups_View('shortcodes/tpf_messages');
            return $view->return_html();
        }


      /**
       * Toggle Post Filter - reset button
       *
       * Shortcode that creates a 2-level filter with toggles to display posts
       *
       * @phpunit
       * @param array $args
       * @return string
       */
        public function tag_groups_tpf_reset($atts)
        {

            extract(shortcode_atts(array(
            'button_class'  => '',
            'button_text'   => 'Reset filter',
            'theme'         => 'light',
            ), $atts));
            $button_class = $this->set_theme_to_class($button_class, $theme);
            $view = new TagGroups_View('shortcodes/tpf_reset');
            $view->set(array(
            'button_class'  => $button_class,
            'button_text'   => $button_text
            ));
            return $view->return_html();
        }


      /**
       * Toggle Post Filter - slider toggle button
       *
       * Shortcode that creates a 2-level filter with toggles to display posts
       *
       * @phpunit
       * @param array $args
       * @return string
       */
        public function tag_groups_tpf_slider_button($atts)
        {

            extract(shortcode_atts(array(
            'button_class'  => '',
            'button_text'   => 'Filter',
            'theme'         => 'light',
            ), $atts));
            $button_class = $this->set_theme_to_class($button_class, $theme);
            $view = new TagGroups_View('shortcodes/tpf_menu_slider_button');
            $view->set(array(
            'button_class'  => $button_class,
            'button_text'   => $button_text
            ));
            return $view->return_html();
        }


      /**
       * Toggle Post Filter - order and order by menu
       *
       * Shortcode that creates a 2-level filter with toggles to display posts
       *
       * @phpunit
       * @param array $args
       * @return string
       */
        public function tag_groups_tpf_order_menu($atts)
        {

            extract(shortcode_atts(array(
            'div_class'     => 'tg_dpf_order_menu',
            'order_text'    => 'Order:',
            'orderby_text'  => 'Order by:',
            'sumoselect'    => 1,
            'theme'         => 'light',
            'orderby_options' => 'date:date|author:author|title:title',
            'order_options'   => 'desc:↓|asc:↑',
            ), $atts));
            $div_class = $this->set_theme_to_class($div_class, $theme);
            $view = new TagGroups_View('shortcodes/tpf_order_menu');
            $view->set(array(
            'select_id_1'         => uniqid('tg-select-1-'),
            'select_id_2'         => uniqid('tg-select-2-'),
            'div_class'           => $div_class,
            'order_text'          => $order_text,
            'orderby_text'        => $orderby_text,
            'sumoselect'          => $sumoselect,
            'orderby_options'  => explode('|', $orderby_options),
            'order_options'    => explode('|', $order_options),
            ));
            return $view->return_html();
        }


      /**
       * Toggle Post Filter - text search field
       *
       * Shortcode that creates a 2-level filter with toggles to display posts
       *
       * @phpunit
       * @param array $args
       * @return string
       */
        public function tag_groups_tpf_text_search($atts)
        {

            extract(shortcode_atts(array(
            'placeholder'     => __('type here', 'tag-groups'),
            'search_trigger'  => 2, // 1: on enter; 2: enter or timed
            ), $atts));
            $view = new TagGroups_View('shortcodes/tpf_text_search');
            $view->set(array(
            'placeholder'     => $placeholder,
            'search_trigger'  => $search_trigger,
            ));
            return $view->return_html();
        }


      /**
       * modifies the class attribute to match a theme
       *
       * @phpunit
       * @param string $class
       * @param string $theme
       * @return string
       */
        public function set_theme_to_class($class, $theme)
        {

            if (empty($theme)) {
                return $class;
            }

            $classes = explode(' ', $class);
            $all_classes = array(
            'dpf_toggle_menu_light',
            'dpf_toggle_menu_dark'
            );
            switch ($theme) {
                case 'dark':
                                                                                                                                                                                                                                          $add_class = 'dpf_toggle_menu_dark';

                    break;
                case 'light':
                default:
                    $add_class = 'dpf_toggle_menu_light';

                    break;
            }

            // remove
            foreach ($classes as $key => $class_item) {
                if (in_array($class_item, $all_classes) && $class_item != $add_class) {
                    unset($classes[ $key ]);
                }
            }

            // add
            if (! in_array($add_class, $classes)) {
                $classes[] = $add_class;
            }

            return implode(' ', $classes);
        }
    } //class


}
