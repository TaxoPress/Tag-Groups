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

if (! class_exists('TagGroups_Shortcode_Table')) {
    class TagGroups_Shortcode_Table extends TagGroups_Shortcode_Common
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
        'add_premium_filter' => array(
        'type' => 'integer',
        'default' => 0,
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
        'exclude' => array(
        'type' => 'string',
        'default' => '',
        ),
        'exclude_terms' => array(
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
        'hide_empty_columns' => array(
        'type' => 'integer',
        'default' => 0,
        ),
        'include' => array(
        'type' => 'string',
        'default' => '',
        ),
        'include_terms' => array(
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
        'prepend' => array(
        'type' => 'string',
        'default' => '',
        ),
        'responsive_breakpoint' => array(
        'type' => 'integer',
        'default' => 800,
        ),
        'separator_size' => array(
        'type' => 'integer',
        'default' => 22,
        ),
        'separator' => array(
        'type' => 'string',
        'default' => '',
        ),
        'show_not_assigned' => array(
        'type' => 'integer',
        'default' => 0,
        ),
        'show_all_groups' => array(
        'type' => 'integer',
        'default' => 0,
        ),
        'show_tag_count' => array(
        'type' => 'integer',
        'default' => 1,
        ),
        'smallest' => array(
        'type' => 'integer',
        'default' => 12,
        ),
        'table_class' => array(
        'type' => 'string',
        'default' => 'tag-groups-cloud-table',
        ),
        'table_id' => array(
        'type' => 'string',
        'default' => '',
        ),
        'td_class' => array(
        'type' => 'string',
        'default' => '',
        ),
        'th_class' => array(
        'type' => 'string',
        'default' => '',
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
        public $html_table_headers;
        public $html_table_cells;


      /**
      * Shortcode that outputs the tags in a table
      *
      * @phpunit
      * @param array $atts
      * @return string
      */
        public function tag_groups_table($atts = array())
        {

            $this->init();
            $this->shortcode_id = 'tag_groups_table';
            $this->set_attributes(shortcode_atts(array(
            'adjust_separator_size' => true,
            'add_premium_filter' => 0,
            'amount' => 0,
            'append' => '',
            'assigned_class' => null,
            'custom_title' => null,
            'custom_title_zero' => null,
            'custom_title_plural' => null,
            'do_not_cache' => false,
            'exclude' => '',
            'exclude_terms' => '',
            'group_in_class' => 0,
            'groups_post_id' => -1,
            'hide_empty' => true,
            'hide_empty_columns' => false,
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
            'responsive_breakpoint' => 800,
            'separator_size' => 12,
            'separator' => '',
            'show_not_assigned' => false,
            'show_all_groups' => false,
            'show_tag_count' => true,
            'smallest' => 12,
            'source' => 'shortcode',
            'table_id' => '',
            'table_class' => 'tag-groups-cloud-table',
            'tags_post_id' => -1,
            'taxonomy' => implode(',', TagGroups_Taxonomy::get_enabled_taxonomies()),
            'td_class' => null,
            'th_class' => null,
            'threshold' => 0, // minimum number of posts, total (independent of groups)
            ), $atts));
    /**
             * Don't set it as default in extract( shortcode_atts() ) because the block sends an empty string
             */
            if (empty($this->attributes->html_id)) {
                $this->attributes->html_id = 'tag-groups-cloud-table-' . uniqid();
            }

            $table_id_output = $this->attributes->html_id ? ' id="' . TagGroups_Shortcode_Statics::sanitize_html_classes($this->attributes->html_id) . '"' : '';
            $table_class_output = $this->attributes->table_class ? ' class="' . TagGroups_Shortcode_Statics::sanitize_html_classes($this->attributes->table_class) . '"' : ' class="tg_table_align_top"';
            if (is_array($atts)) {
                asort($atts);
            }

          /**
           * Call this before creating the cache key
           */
            $this->get_post_id();
            $this->cache_key = md5('table' . serialize($atts) . serialize($this->attributes->tags_post_id) . serialize($this->attributes->groups_post_id));
    // check for a cached version (premium plugin)
            $html = apply_filters('tag_groups_hook_cache_get', false, $this->cache_key);
            if ($html) {
                $html = $this->finalize_html($html, $table_id_output, $table_class_output, $atts);
                return $html;
            }

            $this->check_attributes();
            $this->get_taxonomies();
            $this->get_tags();
            $this->make_include_array();
            $this->maybe_add_post_tags_or_groups();
            $this->html_table_headers = array();
            $this->html_table_cells = array();
    // apply sorting that cannot be done on database level
            if ('natural' == $this->attributes->orderby || 'random' == $this->attributes->orderby || $this->attributes->threshold) {
                $this->sort();
            }


            $this->make_header_html();
            $this->make_tags_html();
    /**
            * assemble header
            */
            $html = '<thead>' . implode("\n", $this->html_table_headers) . '</thead>';
    /**
            * assemble rows
            */
            $html .= '<tbody><tr>' . implode("\n", $this->html_table_cells) . '</tr></tbody>';
            if (! empty($this->post_counts) && ! $this->attributes->do_not_cache) {
              // we don't cache if we used a preliminary post count

                // create a cached version (premium plugin)
                do_action('tag_groups_hook_cache_set', $this->cache_key, $html);
            }


            $html = $this->finalize_html($html, $table_id_output, $table_class_output, $atts);
            return $html;
        }


      /**
       * Create the HTML for the header part
       *
       * @return void
       */
        public function make_header_html()
        {

            global $tag_group_groups;
            $this->html_table_headers[] = '<tr>';

            for ($i = $this->start_group; $i <= $tag_group_groups->get_max_position(); $i++) {
                if (! isset($this->tag_group_data[ $i ])) {
                    continue;
                }

                if ($this->attributes->show_all_groups || in_array($this->tag_group_data[ $i ]['term_group'], $this->include_array)) {
                    if ($i == 0) {
                            $group_name = $this->attributes->not_assigned_name;
                    } else {
                                    $group_name = $this->tag_group_data[ $i ]['label'];
                    }

                    $th_class_group = $this->attributes->th_class;
                    if (! empty($this->attributes->group_in_class)) {
                              $th_class_group .= ' ' . sanitize_html_class(' tg_header_group_id_' . $this->tag_group_data[ $i ]['term_group']) . ' ' . sanitize_html_class('tg_header_group_label_' . strtolower($this->tag_group_data[ $i ]['label']));
                    }

                    $th_class_output = $th_class_group ? ' class="' . TagGroups_Shortcode_Statics::sanitize_html_classes($th_class_group) . '"' : '';
                    $this->html_table_headers[] .= '<th' . $th_class_output . '>' . htmlentities($group_name, ENT_QUOTES, "UTF-8") . '</th>';
                }
            }

            $this->html_table_headers[] .= '</tr>';
        }


      /**
       * Create the HTML for the rows
       *
       * @return void
       */
        public function make_tags_html()
        {

            global $tag_group_groups, $tag_group_premium_terms;
            $this->post_counts = array();
            if (class_exists('TagGroups_Premium_Term') && method_exists($tag_group_premium_terms, 'get_post_counts')) {
                if (TagGroups_Premium_Meta_Box::metabox_is_activated($this->taxonomies)) {
                    $this->post_counts = $tag_group_premium_terms->maybe_get_post_counts();
                }
            }

            $this->determine_min_max();

            /*
          *  render the table content
            */
            for ($i = $this->start_group; $i <= $tag_group_groups->get_max_position(); $i++) {
                if (! isset($this->tag_group_data[ $i ])) {
                    continue;
                }

                $count_amount = 0;
                if (! $this->attributes->show_all_groups && ! empty($this->include_array) && ! in_array($this->tag_group_data[ $i ]['term_group'], $this->include_array)) {
                    continue;
                }

                $this->html_table_cells[ $i ] = '';
                if ('count' == $this->final_orderby && ! empty($this->post_counts)) {
                // We have to sort the tags according to the post counts for this particular group

                        $this->sort_within_groups($this->tag_group_data[ $i ]['term_group']);
                }

                foreach ($this->tags as $tag) {
                    $other_tag_classes = '';
                    if (! empty($this->attributes->amount) && $count_amount >= $this->attributes->amount) {
                            break;
                    }

                    $term_o = new TagGroups_Term($tag);
      // check if tag has posts for this particular group
                    if (! $term_o->has_group($this->tag_group_data[ $i ]['term_group'])) {
                        continue;
                    }

                    if (empty($this->include_tags_post_id_groups) || in_array($tag->term_id, $this->include_tags_post_id_groups[ $this->tag_group_data[ $i ]['term_group'] ])) {
          // check if tag has posts for this particular group
                        if (! empty($this->post_counts) && isset($this->post_counts[ $tag->term_id ][ $this->tag_group_data[ $i ]['term_group'] ])) {
                            $post_count = $this->post_counts[ $tag->term_id ][ $this->tag_group_data[ $i ]['term_group'] ];
                        } else {
                            $post_count = $tag->count;
                        }

                        if ($this->attributes->hide_empty && 0 == $post_count) {
                            continue;
                        }

                        $tag_link = $this->get_tag_link($tag, $i);
                        $font_size = $this->font_size($post_count, $this->min_max[ $this->tag_group_data[ $i ]['term_group'] ]['min'], $this->min_max[ $this->tag_group_data[ $i ]['term_group'] ]['max']);
                        $font_size_separator = $this->attributes->adjust_separator_size ? $font_size : $this->attributes->separator_size;
                        if ($count_amount > 0 && ! empty($this->attributes->separator)) {
                                  $this->html_table_cells[ $i ] .= '<span style="font-size:' . $font_size_separator . 'px">' . $this->attributes->separator . '</span> ';
                        }

                        $other_tag_classes = $this->get_assigned_tag_class($tag->term_id);

                        $title = $this->get_title($tag, $post_count);
                        $title = $this->maybe_filter_title($title, $tag->description, $post_count);
                        $title_html = ( $title == '' ) ? '' : ' title="' .  esc_attr($title) . '"';
          // replace placeholders in prepend and append
                        $prepend_output = $this->get_prepend_output($post_count);
                        $append_output = $this->get_append_output($post_count);
          // adding link target
                        $link_target = TagGroups_Shortcode_Statics::sanitize_link_target($this->attributes->link_target);
                        $link_target_html = ! empty($link_target) ? 'target="' . esc_attr($link_target) . '"' : '';
          // adding class for group
                        if (! empty($this->attributes->group_in_class)) {
                            $other_tag_classes .= ' ' . sanitize_html_class(' tg_tag_group_id_' . $this->tag_group_data[ $i ]['term_group']) . ' ' . sanitize_html_class('tg_tag_group_label_' . strtolower($this->tag_group_data[ $i ]['label']));
                        }

                      // assembling a tag
                        $this->html_table_cells[ $i ] .= '<span class="tag-groups-tag' . $other_tag_classes . '" style="font-size:' . $font_size . 'px"><a href="' . $tag_link . '" ' . $link_target_html . '' . $title_html . '  class="' . $tag->slug . '">';
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
                        $this->html_table_cells[ $i ] .= apply_filters('tag_groups_cloud_tag_prepend', $prepend_html, $tag->term_id, $font_size, $post_count, $this->shortcode_id);
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
                        $this->html_table_cells[ $i ] .= apply_filters('tag_groups_cloud_tag_outer', '<span class="tag-groups-label" style="font-size:' . $font_size . 'px">' . $inner_html . '</span>', $tag->term_id, $this->shortcode_id);
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
                        $this->html_table_cells[ $i ] .= apply_filters('tag_groups_cloud_tag_append', $append_html, $tag->term_id, $font_size, $post_count, $this->shortcode_id);
                        $this->html_table_cells[ $i ] .= '</a></span> ';
                        $count_amount++;
                    }
                }

                if ($this->attributes->hide_empty_columns && ! $count_amount) {
                    unset($this->html_table_headers[ $i ]);
                } else {
                    $td_class_output = $this->attributes->td_class ? ' class="' . TagGroups_Shortcode_Statics::sanitize_html_classes($this->attributes->td_class) . '"' : '';
                    $this->html_table_cells[ $i ] = '<td' . $td_class_output . '><span class="tag-groups-cloud-inner-container">' . $this->html_table_cells[ $i ] . '</span></td>';
                }
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
        public function finalize_html($html, $table_id_output, $table_class_output, $atts)
        {


            $html = '<table' . $table_id_output . $table_class_output . '>' . $html . '</table>';
    /**
            * add basictable for responsiveness
            */
            if ($this->attributes->responsive_breakpoint) {
                $view = new TagGroups_View('partials/responsive_table_js_snippet');
                $view->set(array(
                'responsive_breakpoint'  => $this->attributes->responsive_breakpoint,
                'table_id'               => $this->attributes->html_id
                ));
                $html .= $view->return_html();
            }

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
