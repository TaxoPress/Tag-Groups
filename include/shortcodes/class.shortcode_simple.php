<?php

// phpcs:disable WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn, WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_exclude, WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize, PSR12.Classes.ClosingBrace.StatementAfter -- exclude param, serialize for caching by design

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

if (! class_exists('TagGroups_Shortcode_Simple')) {
    class TagGroups_Shortcode_Simple extends TagGroups_Shortcode_Common
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
        'adjust_separator_size' => array(
        'type' => 'integer',
        'default' => 1,
        ),
        'amount' => array(
        'type' => 'integer',
        'default' => 0,
        ),
        'append' => array(
        'type' => 'string',
        'default' => '',
        ),
        'assigned_class' => array(
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
        'default' => 'tag-groups-cloud-simple',
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
        'largest' => array(
        'type' => 'integer',
        'default' => 22,
        ),
        'link_append' => array(
        'type' => 'string',
        'default' => '',
        ),
        'link_target' => array(
        'type' => 'string',
        'default' => '_self',
        ),
        'order' => array(
        'type' => 'string',
        'default' => 'ASC',
        ),
        'orderby' => array(
        'type' => 'string',
        'default' => 'name',
        ),
        'prepend' => array(
        'type' => 'string',
        'default' => '',
        ),
        'separator_size' => array(
        'type' => 'integer',
        'default' => 22,
        ),
        'separator' => array(
        'type' => 'string',
        'default' => '',
        ),
        'show_tag_count' => array(
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


      /**
      * Shortcode that outputs the tags in a simple cloud
      *
      * @phpunit
      * @param array $atts
      * @return string
      */
        public function tag_groups_simple_cloud($atts = array())
        {

            $this->init();
            $this->shortcode_id = 'tag_groups_simple_cloud';
            $this->set_attributes(shortcode_atts(array(
            'adjust_separator_size' => true,
            'amount' => 0,
            'append' => '',
            'assigned_class' => null,
            'custom_title' => null,
            'custom_title_zero' => null,
            'custom_title_plural' => null,
            'div_id' => null,
            'div_class' => 'tag-groups-cloud-simple',
            'do_not_cache' => false,
            'exclude' => '',
            'exclude_terms' => '',
            'groups_post_id' => -1,
            'hide_empty' => true,
            'include' => '',
            'include_terms' => '',
            'largest' => 22,
            'link_target' => '',
            'link_append' => '',
            'not_assigned_name' => 'not assigned',
            'order' => 'ASC',
            'orderby' => 'name',
            'prepend' => '',
            'remove_filters' => 1,
            'separator_size' => 12,
            'separator' => '',
            'show_not_assigned' => false,
            'show_tag_count' => true,
            'smallest' => 12,
            'source' => 'shortcode',
            'tags_post_id' => -1,
            'taxonomy' => implode(',', TagGroups_Taxonomy::get_enabled_taxonomies()),
            'threshold' => 0, // minimum number of posts, total (independent of groups)
            ), $atts));
            $div_id_output = $this->attributes->html_id ? ' id="' . TagGroups_Shortcode_Statics::sanitize_html_classes($this->attributes->html_id) . '"' : '';
            $div_class_output = $this->attributes->div_class ? ' class="' . TagGroups_Shortcode_Statics::sanitize_html_classes($this->attributes->div_class) . '"' : ' class="tg_table_align_top"';
            if (is_array($atts)) {
                asort($atts);
            }

          /**
           * Call this before creating the cache key
           */
            $this->get_post_id();
            $this->cache_key = md5('simple' . serialize($atts) . serialize($this->attributes->tags_post_id) . serialize($this->attributes->groups_post_id));
    // check for a cached version (premium plugin)
            $html = apply_filters('tag_groups_hook_cache_get', false, $this->cache_key);
            if ($html) {
                $html = $this->finalize_html($html, $div_id_output, $div_class_output, $atts);
                return $html;
            }

            $this->check_attributes();
            $this->get_taxonomies();
            $this->get_tags();
            $this->make_include_array();
            $this->maybe_add_post_tags_or_groups();
    // apply sorting that cannot be done on database level
            if ('natural' == $this->attributes->orderby || 'random' == $this->attributes->orderby || $this->attributes->threshold) {
                $this->sort();
            }


            $this->determine_min_max();
            $html = $this->make_HTML();
            if (! $this->attributes->do_not_cache) {
              // create a cached version (premium plugin)
                do_action('tag_groups_hook_cache_set', $this->cache_key, $html);
            }

      /*
      *  render the div
      */
            $html = $this->finalize_html($html, $div_id_output, $div_class_output, $atts);
            return $html;
        }


      /**
       * Create the HTML of the tag cloud
       *
       * @return void
       */
        public function make_HTML()
        {

            $html = '';
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
                $other_tag_classes = '';
                if (! empty($this->attributes->amount) && $count_amount >= $this->attributes->amount) {
                    break;
                }

                $term_o = new TagGroups_Term($tag);
            // check if tag has posts for this set of groups
                if (! $term_o->has_group($this->include_array)) {
                    continue;
                }

          /**
          * We are using here simple, group-agnostic tag counts, since each tag links to the full set of posts that use this tag.
          */

                if (! $this->attributes->hide_empty || $tag->count > 0) {
                    $tag_link = $this->get_tag_link($tag);
            /**
                        * Note: We cannot append a parameter to separate terms by group on the archive page, since we don't use groups here.
                        */
                    $font_size = $this->font_size($tag->count, $min, $max);
                    $font_size_separator = $this->attributes->adjust_separator_size ? $font_size : $this->attributes->separator_size;
                    if ($count_amount > 0 && ! empty($this->attributes->separator)) {
                        $html .= '<span style="font-size:' . $font_size_separator . 'px">' . $this->attributes->separator . '</span> ';
                    }

                    $other_tag_classes = $this->get_assigned_tag_class($tag->term_id);

                    $title = $this->get_title($tag, $tag->count);
                    $title = $this->maybe_filter_title($title, $tag->description, $tag->count);
                    $title_html = ( $title == '' ) ? '' : ' title="' .  esc_attr($title) . '"';
            // replace placeholders in prepend and append
                    $prepend_output = $this->get_prepend_output($tag->count);
                    $append_output = $this->get_append_output($tag->count);
            // adding link target
                    $link_target = TagGroups_Shortcode_Statics::sanitize_link_target($this->attributes->link_target);
                    $link_target_html = ! empty($link_target) ? 'target="' . esc_attr($link_target) . '"' : '';
            // assembling a tag
                    $html .= '<span class="tag-groups-tag' . $other_tag_classes . '" style="font-size:' . $font_size . 'px"><a href="' . $tag_link . '" ' . $link_target_html . '' . $title_html . '  class="' . $tag->slug . '">';
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
                    $html .= apply_filters('tag_groups_cloud_tag_prepend', $prepend_html, $tag->term_id, $font_size, $tag->count, $this->shortcode_id);
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
                    $html .= apply_filters('tag_groups_cloud_tag_outer', '<span class="tag-groups-label" style="font-size:' . $font_size . 'px">' . $inner_html . '</span>', $tag->term_id, $this->shortcode_id);
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
                    $html .= apply_filters('tag_groups_cloud_tag_append', $append_html, $tag->term_id, $font_size, $tag->count, $this->shortcode_id);
                    $html .= '</a></span> ';
                    $count_amount++;
                }
            }

            return $html;
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

            $html = '<div' . $div_id_output . $div_class_output . '><div class="tag-groups-cloud-inner-container">' . $html . '</div></div>';
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
