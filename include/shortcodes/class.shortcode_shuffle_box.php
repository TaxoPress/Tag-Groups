<?php

// phpcs:disable WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn, WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude, WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize, Squiz.PHP.CommentedOutCode, PSR12.Classes.ClosingBrace.StatementAfter -- exclude param, serialize for caching by design

/**
 * Tag Groups
*
 * @package     Tag Groups
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

if (! class_exists('TagGroups_Shortcode_Shuffle_Box')) {
    class TagGroups_Shortcode_Shuffle_Box extends TagGroups_Shortcode_Common
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
        'add_premium_filter' => array(
        'type' => 'integer',
        'default' => 0,
        ),
        'amount' => array(
        'type' => 'integer',
        'default' => 200,
        ),
        'append' => array(
        'type' => 'string',
        'default' => '',
        ),
        'custom_title' => array(
        'type' => 'string',
        'default' => '{description} ({count})',
        ),
        'custom_title_zero' => array(
        'type' => 'string',
        'default' => '{description} ({count})',
        ),
        'custom_title_plural' => array(
        'type' => 'string',
        'default' => '{description} ({count})',
        ),
        'div_class' => array(
        'type' => 'string',
        'default' => 'cm-shuffle-box-theme-default',
        ),
        'div_id' => array(
        'type' => 'string',
        'default' => '',
        ),
        'exclude' => array(
        'type' => 'string',
        'default' => '',
        ),
        'groups_post_id' => array(
        'type' => 'integer',
        'default' => -1,
        ),
        'hide_empty' => array(
        'type' => 'integer',
        'default' => 1,
        ),
        'include' => array(
        'type' => 'string',
        'default' => '',
        ),
        'initial_group' => array(
        'type' => 'integer',
        'default' => -1,
        ),
        'largest' => array(
        'type' => 'integer',
        'default' => 22,
        ),
        'layout_mode' => array(
        'type' => 'string',
        'default' => 'fitRows',
        ),
        'link_append' => array(
        'type' => 'string',
        'default' => '',
        ),
        'link_target' => array(
        'type' => 'string',
        'default' => '_self',
        ),
        'not_assigned_name' => array(
        'type' => 'string',
        'default' => 'not assigned',
        ),
        'order' => array(
        'type' => 'string',
        'default' => 'ASC',
        ),
        'orderby' => array(
        'type' => 'string',
        'default' => 'name',
        ),
        'placeholder' => array(
        'type' => 'string',
        'default' => 'search',
        ),
        'prepend' => array(
        'type' => 'string',
        'default' => '',
        ),
        'show_all_name' => array(
        'type' => 'string',
        'default' => 'all groups',
        ),
        'show_filter_all_groups' => array(
        'type' => 'integer',
        'default' => 1,
        ),
        'show_group_filter' => array(
        'type' => 'integer',
        'default' => 1,
        ),
        'show_tag_count' => array(
        'type' => 'integer',
        'default' => 1,
        ),
        'show_text_filter' => array(
        'type' => 'integer',
        'default' => 1,
        ),
        'smallest' => array(
        'type' => 'integer',
        'default' => 12,
        ),
        'tags_post_id' => array(
        'type' => 'integer',
        'default' => -1,
        ),
        'taxonomy' => array(
        'type' => 'string',
        'default' => '',
        ),
        'threshold' => array(
        'type' => 'integer',
        'default' => 0,
        ),
        );
        public $html_div;
        public $html_header;
        public $timeout;
        public $js_identifier;

      /**
      * Shortcode that outputs the tags in a filterable tag cloud
      *
      * @phpunit
      * @param array $atts
      * @return string
      */
        public function tag_groups_shuffle_box($atts = array())
        {

            $this->init();
            $this->shortcode_id = 'tag_groups_shuffle_box';
            $this->set_attributes(shortcode_atts(array(
            'add_premium_filter' => 0,
            'amount' => 0,
            'append' => '',
            'custom_title' => null,
            'custom_title_zero' => null,
            'custom_title_plural' => null,
            'div_class' => 'cm-shuffle-box-theme-default',
            'div_id' => '',
            'do_not_cache' => false,
            'exclude' => '',
            'exclude_terms' => '',
            'groups_post_id' => -1,
            'hide_empty' => true,
            'include' => '',
            'include_terms' => '',
            'initial_group' => -1,
            'largest' => 22,
            'layout_mode' => 'fitRows', // 'fitRows', 'masonry', 'vertical'
            'link_append' => '',
            'link_target' => '',
            'not_assigned_name' => __('not assigned', 'tag-groups'),
            'order' => 'ASC',
            'orderby' => 'name',
            'placeholder' => __('search', 'tag-groups'),
            'prepend' => '',
            'remove_filters' => 1,
            'show_all_name' => __('all groups', 'tag-groups'),
            'show_filter_all_groups' => 1,
            'show_group_filter' => 1,
            'show_tag_count' => 1,
            'show_text_filter' => 1,
            'smallest' => 12,
            'source' => 'shortcode',
            'tags_post_id' => -1,
            'taxonomy' => '',
            'threshold' => 0, // minimum number of posts, total (independent of groups)
            ), $atts));
/**
         * Don't set it as default in extract( shortcode_atts() ) because the block sends an empty string
         */
            if (empty($this->attributes->html_id)) {
                $this->attributes->html_id = 'tag-groups-shuffle-box-' . uniqid();
            }



            if (is_array($atts)) {
                asort($atts);
            }

            $this->attributes->html_id = sanitize_html_class($this->attributes->html_id);
            $div_id_output = $this->attributes->html_id ? ' id="' . $this->attributes->html_id . '"' : '';
            $div_class_output = ' class="cm-shuffle-box-container ' . TagGroups_Shortcode_Statics::sanitize_html_classes($this->attributes->div_class) . '"';
            $cache_key = md5('shuffle_box' . serialize($atts) . serialize($this->attributes->tags_post_id) . serialize($this->attributes->groups_post_id));
// check for a cached version (premium plugin)
            $html = apply_filters('tag_groups_hook_cache_get', false, $cache_key);
            if ($html) {
                $html = $this->finalize_html($html, $div_id_output, $div_class_output, $atts);
                return $html;
            }


            $this->check_attributes();
            $this->get_taxonomies();
            $this->get_tags();
            $this->make_include_array();
            $this->maybe_add_post_tags_or_groups();
            $this->html_header = array();
            $this->html_div = array();
            $this->js_identifier = str_replace('-', '_', sanitize_title($this->attributes->html_id));
// apply sorting that cannot be done on database level
            if ('natural' == $this->attributes->orderby || 'random' == $this->attributes->orderby || $this->attributes->threshold) {
                $this->sort();
            }

            $this->make_header_html();
            $this->determine_min_max();
            $this->make_div_html();
/*
      * assemble content
      */
            $view = new TagGroups_View('shortcodes/shuffle_box');
            $view->set(array(
            'html_header'         => $this->html_header,
            'div_id_inner'        => $this->attributes->html_id . '_inner',
            'html_div'            => $this->html_div,
            'js_identifier'       => $this->js_identifier,
            'layout_mode'         => $this->attributes->layout_mode,
            'initial_group'       => $this->attributes->initial_group,
            'add_premium_filter'  => $this->attributes->add_premium_filter,
            'source'              => $this->attributes->source,
            'timeout'             => $this->timeout
            ));
            $html = $view->return_html();
            if (! $this->attributes->do_not_cache) {
            // create a cached version (premium plugin)
                  do_action('tag_groups_hook_cache_set', $this->cache_key, $html);
            }

            $html = $this->finalize_html($html, $div_id_output, $div_class_output, $atts);
            return $html;
        }


      /**
       * Create the input field and buttons
       *
       * @return void
       */
        public function make_header_html()
        {

            global $tag_group_groups;
            if ($this->attributes->show_text_filter) {
                $this->html_header[] .= '<input class="cm-shuffle-box-input cm-shuffle-box-quicksearch" value="" placeholder="' . $this->attributes->placeholder . '" autocomplete="off">';
            }

            if (! $this->attributes->show_group_filter) {
                return;
            }

            if ($this->attributes->show_filter_all_groups) {
                $this->html_header[] .= '<button class="cm-shuffle-box-button cm-shuffle-box-button--1" data-id="-1" tabindex="0">' . $this->attributes->show_all_name . '</button>';
            }

            $tabindex = 0;

            for ($i = 0; $i <= $tag_group_groups->get_max_position(); $i++) {
                if (! isset($this->tag_group_data[ $i ]) || ! in_array($this->tag_group_data[ $i ]['term_group'], $this->include_array)) {
                    continue;
                }

                if ($i == 0) {
                    $group_name = $this->attributes->not_assigned_name;
                } else {
                    $group_name = $this->tag_group_data[ $i ]['label'];
                }

                $tabindex++;
                $this->html_header[] .= '<button class="cm-shuffle-box-button cm-shuffle-box-button-' . $this->tag_group_data[ $i ]['term_group'] . '" data-id="' . $this->tag_group_data[ $i ]['term_group'] . '" tabindex="' . $tabindex . '" aria-label="' . $group_name . '">' . $group_name . '</button>';
                if ($this->attributes->initial_group == -1 && ! $this->attributes->show_filter_all_groups) {
                    $this->attributes->initial_group = $this->tag_group_data[ $i ]['term_group'];
                }
            }
        }


      /**
       * create the part with tags
       *
       * @return void
       */
        public function make_div_html()
        {

            /**
            * We need here absolute min and max, independent of groups
            */
            $min = 0;
            $max = 0;
            foreach ($this->min_max as $min_max_item) {
                if (0 == $min || $min_max_item['min'] < $min) {
                    $min = $min_max_item['min'];
                }

                if ($min_max_item['max'] > $max) {
                    $max = $min_max_item['max'];
                }
            }

            /*
          *  render the content
            */
            $count_amount = 0;
            foreach ($this->tags as $tag) {
                if (! empty($this->attributes->amount) && $count_amount >= $this->attributes->amount) {
                    break;
                }

                $term_o = new TagGroups_Term($tag);
            // check if tag has posts for the selected groups
                if (! $term_o->has_group($this->include_array)) {
                    continue;
                }

                $post_count = $tag->count;
                if ($this->attributes->hide_empty && 0 == $post_count) {
                    continue;
                }

                $tag_link = $this->get_tag_link($tag);
                $font_size = $this->font_size($tag->count, $min, $max);
                $title = $this->get_title($tag, $post_count);
                $title = $this->maybe_filter_title($title, $tag->description, $post_count);
                $title_html = ( $title == '' ) ? '' : ' title="' .  esc_attr($title) . '"';
    // replace placeholders in prepend and append
                $prepend_output = $this->get_prepend_output($post_count);
                $append_output = $this->get_append_output($post_count);
                $link_target = TagGroups_Shortcode_Statics::sanitize_link_target($this->attributes->link_target);
                $link_target_html = ! empty($link_target) ? 'target="' . esc_attr($link_target) . '"' : '';
                $groups_of_term = $term_o->get_groups();

                $group_classes = array_map(function ($g) {
                    return 'cm-shuffle-box-group-' . $g;
                }, $groups_of_term);
                $html_tag = '';
                if ('' != $prepend_output) {
                    $prepend_html = '<span class="tag-groups-prepend" style="font-size:' . $font_size . 'px">' . htmlentities($prepend_output, ENT_QUOTES, "UTF-8") . '</span>';
                } else {
                    $prepend_html = '';
                }

              /**
               * Hook to filter the prepended HTML
               *
               * @param string $prepend_html
               * @param int $tag->term_id
               * @param int $font_size
               * @param int $post_count
               * @param string $this->shortcode_id
               * @return string
               */
                $html_tag .= apply_filters('tag_groups_cloud_tag_prepend', $prepend_html, $tag->term_id, $font_size, $post_count, $this->shortcode_id);
            /**
                       * Hook to filter inner HTML
                       *
                       * @param string $tag->name
                       * @param int $tag->term_id
                       * @param string $this->shortcode_id
                       * @return string
                       */
                $inner_html = apply_filters('tag_groups_cloud_tag_inner', $tag->name, $tag->term_id, $this->shortcode_id);
            /**
                       * Hook to filter outer HTML
                       *
                       * @param string HTML
                       * @param int $tag->term_id
                       * @param string $this->shortcode_id
                       * @return string
                       */
                $html_tag .= apply_filters('tag_groups_cloud_tag_outer', '<span class="tag-groups-label" style="font-size:' . $font_size . 'px" aria-label="' . str_replace('"', '', $inner_html) . '">' . $inner_html . '</span>', $tag->term_id, $this->shortcode_id);
                if ('' != $append_output) {
                    $append_html = '<span class="tag-groups-append" style="font-size:' . $font_size . 'px">' . htmlentities($append_output, ENT_QUOTES, "UTF-8") . '</span>';
                } else {
                    $append_html = '';
                }

              /**
               * Hook to filter the appended HTML
               *
               * @param string $append_html
               * @param int $tag->term_id
               * @param int $font_size
               * @param int $post_count
               * @param string $this->shortcode_id
               * @return string
               */
                $html_tag .= apply_filters('tag_groups_cloud_tag_append', $append_html, $tag->term_id, $font_size, $post_count, $this->shortcode_id);
                $this->html_div[] = '<span class="tag-groups-tag ' . implode(' ', $group_classes) . '" style="font-size:' . $font_size . 'px">' .
                '<a href="' . $tag_link . '" ' . $link_target_html . '' . $title_html . '  class="' . $tag->slug . '" data-href="' . $tag_link . '" data-termid="' . $tag->term_id . '">' .
                $html_tag  .
                '</a>' .
                '</span>';
                $count_amount++;
            }

            if ($count_amount > 300) {
                $this->timeout = 700;
            } else {
                $this->timeout = 100;
            }
        }


      /**
       * wrap the HTML in code that is independent of caching
       *
       * @param string $html
       * @param string $div_id_output
       * @param string $div_class_output
       * @param array $atts
       * @return string
       */
        public function finalize_html($html, $div_id_output, $div_class_output, $atts)
        {

            $html = '<div' . $div_id_output . $div_class_output . '>' . $html . '</div>';
    /**
             * Hook to filter final HTML
             *
             * @param string $html
             * @param string $this->shortcode_id
             * @param array $atts
             * @return string
             */
            $html = apply_filters('tag_groups_cloud_html', $html, $this->shortcode_id, $atts);
            return $html;
        }
    } //class


}
