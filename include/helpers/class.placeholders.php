<?php

// phpcs:disable WordPressVIPMinimum.Functions.CheckReturnValue, WordPressVIPMinimum.Functions.StripTags -- get_term_link checked, strip_tags for content extraction by design

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

if (! class_exists('TagGroups_Placeholders')) {
    class TagGroups_Placeholders
    {
          /**
           * WP Post object
           *
           * @var object
           */
          private $post;
    /**
           * Current HTML of post
           *
           * @var string
           */
          private $output;
    /**
           * HTML of sample post
           *
           * @var string
           */
          private $sample_post;
    /**
           * src to default image
           *
           * @var string
           */
          private $default_image_src;

        public function __construct($post, $template, $default_image_src = '', $sample_post = '')
        {

            $this->post = $post;
            $this->output = $template;
/**
         * Filter the post template before starting to process placeholders for a post
         * This can be used to create own placeholders for the post filters.
         *
         * @param string The HTML template (will be refreshed for each post)
         * @param object WP Post object
         * @return string The HTML of the post, optionally containing valid placeholders
         */
            $this->output = apply_filters('tag_groups_before_placeholders', $this->output, $post);
            $this->sample_post = $sample_post;
            $this->default_image_src = $default_image_src;
        }


      /**
       * Returns the current HTML of the post
       *
       * @phpunit
       * @return string
       */
        public function get_output()
        {

            return $this->output;
        }


      /**
       * Replaces a placeholder by its post-specific value
       *
       * @phpunit
       * @return $this
       */
        public function process_post_id()
        {

            if (strpos($this->output, '{post_id}') === false) {
                return $this;
            }

            $this->output = preg_replace("/(\{post_id\})/", $this->post->ID, $this->output);
            return $this;
        }


      /**
       * Replaces a placeholder by its post-specific value
       *
       * @phpunit
       * @return $this
       */
        public function process_post_title()
        {

            if (strpos($this->output, '{post_title}') === false) {
                return $this;
            }

            $this->output = str_replace('{post_title}', esc_html($this->post->post_title), $this->output);
            return $this;
        }


      /**
       * Replaces a placeholder by its post-specific value
       *
       * @phpunit
       * @return $this
       */
        public function process_permalink()
        {

            if (strpos($this->output, '{permalink}') === false) {
                return $this;
            }

            if ($this->sample_post) {
                $this->output = preg_replace("/(\{permalink\})/", '#', $this->output);
            } else {
                $this->output = preg_replace("/(\{permalink\})/", esc_url(get_permalink($this->post)), $this->output);
            }

            return $this;
        }


      /**
       * Replaces a placeholder by its post-specific value
       *
       * @phpunit
       * @return $this
       */
        public function process_post_guid()
        {

            if (strpos($this->output, '{post_guid}') === false) {
                return $this;
            }

            if ($this->sample_post) {
                $this->output = preg_replace("/(\{post_guid\})/", '#', $this->output);
            } else {
                $this->output = preg_replace("/(\{post_guid\})/", esc_url(get_permalink($this->post)), $this->output);
            }

            return $this;
        }


      /**
       * Replaces a placeholder by its post-specific value
       *
       * @phpunit
       * @return $this
       */
        public function process_post_excerpt()
        {

            if (strpos($this->output, '{post_excerpt}') === false) {
                return $this;
            }

            $this->output = preg_replace("/(\{post_excerpt\})/", $this->create_custom_excerpt($this->post), $this->output);
            return $this;
        }


      /**
       * Replaces a placeholder by its post-specific value
       *
       * @phpunit
       * @return $this
       */
        public function process_post_excerpt_html()
        {

            if (strpos($this->output, '{post_excerpt_html}') === false) {
                return $this;
            }

            $this->output = preg_replace("/(\{post_excerpt_html\})/", $this->create_custom_excerpt_keep_html($this->post), $this->output);
            return $this;
        }


      /**
       * Replaces a placeholder by its post-specific value
       *
       * @phpunit
       * @return $this
       */
        public function process_post_date()
        {

            if (strpos($this->output, '{post_date}') === false) {
                return $this;
            }

            $this->output = preg_replace("/(\{post_date\})/", date_i18n(get_option('date_format'), strtotime($this->post->post_date)), $this->output);
            return $this;
        }


      /**
       * Replaces a placeholder by its post-specific value
       *
       * @phpunit
       * @return $this
       */
        public function process_post_author()
        {

            if (strpos($this->output, '{post_author}') === false) {
                return $this;
            }

            if ($this->sample_post) {
                $this->output = preg_replace("/(\{post_author\})/", $this->post->post_author, $this->output);
            } else {
                $this->output = preg_replace("/(\{post_author\})/", get_the_author_meta('user_nicename', $this->post->post_author), $this->output);
            }

            return $this;
        }


      /**
       * Replaces a placeholder by its post-specific value
       *
       * @phpunit
       * @return $this
       */
        public function process_image_src()
        {

            if (strpos($this->output, '{image_src') === false) {
                return $this;
            }

            /**
            * extract size
            */
            $find_sizes = preg_match("/\{image_src\|([^x\}]+x[^x\}]+)\}/", $this->output, $matches);
            if ($find_sizes === 1) {
            /**
                       * values can be integers (pixel), percentages or the keyword "auto"
                       *
                       */
                  $size = explode('x', $matches[1]);
                $size_from_placeholder = true;
            } else {
            /**
                      * Search for keywords like 'thumbnail', 'large', ...
                      */
                  $find_sizes = preg_match("/\{image_src\|([^\}]+)\}/", $this->output, $matches);
                if ($find_sizes === 1) {
                    $size = $matches[1];
                } else {
                    $size = 'thumbnail';
                }

                $size_from_placeholder = false;
            }

            if ($this->sample_post) {
                $image_src = TAG_GROUPS_PLUGIN_URL . '/assets/images/post-template-sample-image.jpg';
                if (is_array($size)) {
                    $image_width = $size[0];
                    $image_height = $size[1];
                } else {
                    $image_width = (int) get_option($size . '_size_w', 200);
                    $image_height = (int) get_option($size . '_size_h', 200);
                }

                $size_from_placeholder = true;
            } else {
                if (has_post_thumbnail($this->post)) {
                    if (is_numeric($size[0]) && is_numeric($size[1])) {
                        $image_attributes = wp_get_attachment_image_src(get_post_thumbnail_id($this->post), $size);
                    } else {
                        if (is_array($size)) {
                /**
                                  * percentage or 'auto': get a large image
                                  */
                            $attachment_size[0] = (int) get_option('large_size_w', 500);
                            $attachment_size[1] = (int) get_option('large_size_h', 500);
                        } else {
            /**
                            * Keyword like 'thumbnail', 'large', ...
                            */
                            $attachment_size = $size;
                        }

                        $image_attributes = wp_get_attachment_image_src(get_post_thumbnail_id($this->post), $attachment_size);
                    }

                    if ($image_attributes === false) {
                        $image_src = $this->default_image_src;
                        $image_width = $size[0];
                        $image_height = $size[1];
                    } else {
                        $image_src = $image_attributes[0];
                        $image_width = $size[0];
                        $image_height = $size[1];
                    }
                } else {
                    if (is_array($size)) {
                        $image_src = $this->default_image_src;
                        $image_width = $size[0];
                        $image_height = $size[1];
                    } else {
                        $image_src = $this->default_image_src;
                        $image_width = '100%';
                        $image_height = 'auto';
                    }
                }
            }

            // src attribute of image (URL)
            $this->output = preg_replace("/(\{image_src\|?[0-9A-Za-z%]*\})/", $image_src, $this->output);
    // image size
            if ($size_from_placeholder) {
    // add the size anyway
                $this->output = preg_replace("/(<img )/", '<img ' . sprintf('width="%s" height="%s" ', $image_width, $image_height), $this->output);
            }

            return $this;
        }


      /**
       * Replaces a placeholder by its post-specific value
       *
       * @phpunit
       * @return $this
       */
        public function process_image_alt()
        {

            if (strpos($this->output, '{image_alt}') === false) {
                return $this;
            }

            // alt attribute of image
            if ($this->sample_post) {
                $this->output = preg_replace("/(\{image_alt\})/", 'sample alt attribute', $this->output);
            } else {
                $alt_html = get_post_meta($this->post->ID, '_wp_attachment_image_alt', true);
                if (empty($alt_html)) {
                    $alt_html = '';
                }

              // alt attribute of image
                $this->output = preg_replace("/(\{image_alt\})/", $alt_html, $this->output);
            }

            return $this;
        }


      /**
       * Replaces a placeholder by its post-specific value
       *
       * @phpunit
       * @return $this
       */
        public function process_post_category()
        {

            if (strpos($this->output, 'post_category') === false) {
                return $this;
            }

            if ($this->sample_post) {
                $category_html = '<a href="#">Category 1</a>, <a href="#">Category 2</a> ';
                $this->output = preg_replace("/(\{post_category\})/", $category_html, $this->output);
            } else {
                $category_objects = get_the_category($this->post->ID);
                if (! empty($category_objects)) {
                    $category_html = array();
                    foreach ($category_objects as $category_object) {
                        $category_html[] = '<a href="' . esc_url(get_category_link($category_object)) . '">' . esc_html($category_object->name) . '</a>';
                    }

                    $this->output = preg_replace("/(\{post_category\})/", implode(', ', $category_html), $this->output);
                } else {
                    $this->output = preg_replace("/(\{post_category\})/", '', $this->output);
                }
            }

            return $this;
        }


      /**
       * Replaces a placeholder by its post-specific value
       *
       * @phpunit
       * @return $this
       */
        public function process_post_tags()
        {

            if (strpos($this->output, 'post_tags') === false) {
                return $this;
            }

            if ($this->sample_post) {
                $tags_html = '<a href="#">Tag 1</a>, <a href="#">Tag 2</a> ';
                $this->output = preg_replace("/(\{post_tags\})/", $tags_html, $this->output);
            } else {
                $tag_objects = wp_get_post_tags($this->post->ID);
                if (! empty($tag_objects)) {
                    $tags_html = array();
                    foreach ($tag_objects as $tag_object) {
                        $tags_html[] = '<a href="' . esc_url(get_tag_link($tag_object)) . '">' . esc_html($tag_object->name) . '</a>';
                    }

                    $this->output = preg_replace("/(\{post_tags\})/", implode(', ', $tags_html), $this->output);
                } else {
                    $this->output = preg_replace("/(\{post_tags\})/", '', $this->output);
                }
            }

            return $this;
        }


      /**
       * Replaces a placeholder by its post-specific value
       *
       * @phpunit
       * @return $this
       */
        public function process_other_taxonomies()
        {

            /**
             * ideally this method should be called last
             */
            if (strpos($this->output, '{') === false) {
                return $this;
            }

            /**
             * Not available in the sample post
             */

            $taxonomies = get_object_taxonomies($this->post->post_type);
            foreach ($taxonomies as $taxonomy) {
                if ('post_tag' == $taxonomy || 'category' == $taxonomy) {
            /**
                         * We process them in separate methods
                         */

                    continue;
                }

                if (strpos($this->output, '{' . $taxonomy . '}') === false) {
                    continue;
                }

                $taxonomy_html = array();
                $terms = get_the_terms($this->post, $taxonomy);
                if (false === $terms || is_wp_error($terms)) {
                    $this->output = preg_replace("/(\{" . $taxonomy . "\})/", '', $this->output);
                    continue;
                }

                foreach ($terms as $term) {
                    $taxonomy_html[] = '<a href="' . esc_url(get_term_link($term)) . '">' . esc_html($term->name) . '</a>';
                }

                $this->output = preg_replace("/(\{" . $taxonomy . "\})/", implode(', ', $taxonomy_html), $this->output);
            }


            return $this;
        }


      /**
       * Replaces a placeholder by its post-specific value
       *
       * @phpunit
       * @return $this
       */
        public function process_custom_fields()
        {

            if (strpos($this->output, 'custom_field') === false) {
                return $this;
            }

            $found = preg_match_all('#{custom_field:([0-9a-zA-Z-_ ]+)}#', $this->output, $matches);
            if ($found) {
                foreach ($matches[0] as $key => $match) {
                    $custom_field_name = $matches[1][ $key ];
                    $meta_value = get_post_meta($this->post->ID, $custom_field_name, true);
            /**
                         * Filters the value of a custom post type.
                         *
                         * @param mixed $meta_value what ever is returned by get_post_meta
                         * @param string $custom_field_name name of custom field
                         * @param integer post ID
                         *
                         * @return string
                         */
                    $meta_value = apply_filters('tag_groups_template_custom_field', $meta_value, $custom_field_name, $this->post->ID);
                    if (is_array($meta_value)) {
                        // We handle only arrays with values that can be cast to string
                            $meta_value = implode(', ', array_map('strval', $meta_value));
                    } else {
                            $meta_value = (string) $meta_value;
                    }

                    $this->output = str_replace($match, esc_html($meta_value), $this->output);
                }
            }


            return $this;
        }


      /**
       * Returns the excerpt outside the loop
       *
       * @phpunit
       * @return string
       */
        public function create_custom_excerpt()
        {

            if (has_filter('tag_groups_excerpt')) {
    /**
               * Filter Hook tag_groups_excerpt
               *
               * This filter makes it possible to circumvent all of the plugin's native code for generating an excerpt and to create an excerpt with a custom callback function.
               * This function needs to take care of proper encoding and sanitation and it must make sure that HTML is self-consistent and that no HTML tags remain open.
               *
               * @var object WP_Post object
               * @return string
               */
                return (string) apply_filters('tag_groups_excerpt', $this->post);
            }

            $text = $this->post->post_excerpt;
            if (empty($text)) {
                $text = strip_shortcodes($this->post->post_content);
                $text = str_replace(']]>', ']]&gt;', $text);
                $text = strip_tags($text);
            }

            $tag_group_excerpt_length = TagGroups_Options::get_option('tag_group_excerpt_length', 50);
            $tag_group_excerpt_length = (int) apply_filters('excerpt_length', $tag_group_excerpt_length);
    // excerpt length set via filter https://codex.WordPress.org/Plugin_API/Filter_Reference/excerpt_length

            $tag_group_excerpt_ellipsis = TagGroups_Options::get_option('tag_group_excerpt_ellipsis', ' [...]');
            $tag_group_excerpt_ellipsis = apply_filters('excerpt_more', $tag_group_excerpt_ellipsis);
            $text = $this->truncate_ignore_html($text, $tag_group_excerpt_length, $tag_group_excerpt_ellipsis);
            $text = apply_filters('wp_trim_excerpt', $text, $this->post->post_content);
            return $text;
        }


      /**
       * Returns the excerpt outside the loop
       * experimental
       *
       * @phpunit
       * @return string
       */
        public function create_custom_excerpt_keep_html()
        {

            $text = $this->post->post_excerpt;
            $text = TagGroups_Options::wp_kses($text);
    // remove Gutenberg comments

            if (empty($text)) {
                $text = do_shortcode($this->post->post_content);
                if (function_exists('do_blocks')) {
                    $text = do_blocks($text);
                }

                $text = str_replace(']]>', ']]&gt;', $text);
    /**
               * Remove any JS; we cannot use wp_kses because we also want to remove content between the script tags
               */
                $text = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $text);
                $text = TagGroups_Options::wp_kses($text);
    // remove Gutenberg comments and scripts
            }


            $tag_group_excerpt_length = TagGroups_Options::get_option('tag_group_excerpt_length', 50);
            $tag_group_excerpt_length = (int) apply_filters('excerpt_length', $tag_group_excerpt_length);
    // excerpt length set via filter https://codex.WordPress.org/Plugin_API/Filter_Reference/excerpt_length

            $tag_group_excerpt_ellipsis = TagGroups_Options::get_option('tag_group_excerpt_ellipsis', ' [...]');
            $tag_group_excerpt_ellipsis = apply_filters('excerpt_more', $tag_group_excerpt_ellipsis);
            $text = $this->truncate_keep_html($text, $tag_group_excerpt_length, $tag_group_excerpt_ellipsis);
            $text = apply_filters('wp_trim_excerpt', $text, $this->post->post_content);
            return $text;
        }


      /**
       * repair a nested HTML structure
       *
       * @phpunit
       * @param string $html
       * @return string
       */
        public function repair_html($html)
        {

            $open_stack = array();
            $resulting_html = '';

            for ($i = 0; $i < mb_strlen($html); $i++) {
                $char = mb_substr($html, $i, 1);
                if ('<' != $char) {
                    $resulting_html .= $char;
                    continue;
                }

                $next_char = mb_substr($html, $i + 1, 1);
                if ('/' == $next_char) {
                    preg_match('#</([a-z]+)>#iU', mb_substr($html, $i), $matches);
                    if (count($matches) < 1) {
                        continue;
                    }

                    $open_stack_reversed = array_reverse($open_stack);
                    if (isset($open_stack_reversed[0]) && $matches[1] == $open_stack_reversed[0]) {
        // We can close one level
                        array_pop($open_stack);
                        $i = $i + strlen($matches[1]) + 3;
                        $resulting_html .= '</' . $matches[1] . '>';
                        continue;
                    } elseif (in_array($matches[1], $open_stack_reversed)) {
        // this tag was closed too early - we close all in between
                        while (isset($open_stack_reversed[0]) && $matches[1] != $open_stack_reversed[0]) {
                                $resulting_html .= '</' . $open_stack_reversed[0] . '>';
                                array_pop($open_stack);
                                array_shift($open_stack_reversed);
                        }

                      // Now we can close this one
                        $resulting_html .= '</' . $matches[1] . '>';
                        array_pop($open_stack);
                        array_shift($open_stack_reversed);
                        $i = $i + strlen($matches[1]) + 2;
                    } else {
        // it is closed but was never opened: we skip it

                        $i = $i + strlen($matches[1]) + 2;
                    }
                } else {
                    $will_need_closing_tag = preg_match('#<(?!meta|img|br|hr|input\b)\b([a-z]+)(?: .*)?(?<![/|/ ])>#iU', mb_substr($html, $i), $matches);
                    if ($will_need_closing_tag) {
                        $open_stack[] = $matches[1];
                    }

                    $resulting_html .= $char;
                }
            }

            // check if any open tags are left over
            if (count($open_stack)) {
                for ($j = count($open_stack) - 1; $j >= 0; $j--) {
                    $resulting_html .= '</' . $open_stack[ $j ] . '>';
                }
            }

            return $resulting_html;
        }


      /**
       * Reduce a text to a given number of words
       *
       * @phpunit
       * @param string $text
       * @param integer $excerpt_length
       * @param string $excerpt_ellipsis
       * @return string
       */
        public function truncate_ignore_html($text, $excerpt_length, $excerpt_ellipsis = '…')
        {

            $text = str_replace(array('<br>', '</p>', '<br />', '</div>'), "\n", $text);
            $text = strip_tags($text);
            $words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
            if (is_array($words) && count($words) > $excerpt_length) {
                array_pop($words);
                $text = implode(' ', $words);
                $text = $text . $excerpt_ellipsis;
            } else {
                $text = implode(' ', $words);
            }

            return $text;
        }


      /**
       * truncates text and keeps the HTML, closing all tags that we opened until we cut off
       *
       * @phpunit
       * @param string $text
       * @param integer $length in words; counting only the visible text
       * @param string $excerpt_ellipsis inserted directly after the text
       * @return string
       */
        public function truncate_keep_html($text, $length = 100, $excerpt_ellipsis = '…')
        {

            // if the plain text is shorter than the maximum length, return the whole text
            if ($this->get_word_count($text) <= $length) {
                return $text;
            }

            // splits all html-tags to scanable lines, separate for tag and following content
            preg_match_all('/(<.+?>)?([^<]*)/s', $text, $lines, PREG_SET_ORDER);
            $open_tags = array();
            $truncated_text = '';
            foreach ($lines as $line_matches) {
            // if there is any html-tag in this line, handle it and add it to the output
                if (! empty($line_matches[1])) {
                    if (preg_match('/^<(\S.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matches[1])) {
                    // if it's an "empty element" with or without xhtml-conform closing slash

                                // We don't add this to $open_tags
                    } elseif (preg_match('/^<\/(\S+?)\s*>$/s', $line_matches[1], $tag_matches)) {
                    // if tag is a closing tag

                              // delete tag from $open_tags list
                              $pos = array_search($tag_matches[1], $open_tags);
                        if ($pos !== false) {
                            unset($open_tags[ $pos ]);
                        }
                    } elseif (preg_match('/^<([^\s>!]+).*?>$/s', $line_matches[1], $tag_matches)) {
                    // if tag is an opening tag

                        if (strpos(strtolower($tag_matches[1]), '<script') === 0) {
                            continue;
                        }

                  // add tag to the beginning of $open_tags list
                        array_unshift($open_tags, strtolower($tag_matches[1]));
                    } else {
                    // continue;
                    }

            // add html-tag to $truncated_text
                    $truncated_text .= $line_matches[1];
                }

                if ($this->get_word_count($truncated_text) >= $length) {
                // if the maximum length is reached, get out of the loop

                      break;
                }

                if ($this->get_word_count($truncated_text) + $this->get_word_count($line_matches[2]) < $length) {
            // the entire remainder of this chunk fits inside
                    $truncated_text .= $line_matches[2];
                } elseif ($this->get_word_count($truncated_text) + $this->get_word_count($line_matches[2]) == $length) {
    // the entire remainder of this chunk fits exactly
                    $truncated_text .= $line_matches[2];
                    break;
                } else {
                    $first_char = mb_substr($line_matches[2], 0, 1);
                    if (' ' == $first_char || "\t" == $first_char) {
                            $truncated_text .= $first_char;
                    }

    // the remainder is too long and needs to be truncated
                    $words = preg_split("/[\n\r\t ]+/", $line_matches[2], -1, PREG_SPLIT_NO_EMPTY);
                    if (is_array($words)) {
                        $truncated_text .= implode(' ', array_slice($words, 0, $length - $this->get_word_count($truncated_text)));
                        break;
                    }
                }
            }


            // add the defined ending to the text
            $truncated_text .= $excerpt_ellipsis;
    // close all unclosed html-tags
            foreach ($open_tags as $tag) {
                $truncated_text .= '</' . $tag . '>';
            }

            return $truncated_text;
        }


      /**
       * Returns the word count, ignoring HTML (except line breaks)
       *
       * @phpunit
       * @param string $text
       * @return string
       */
        public function get_word_count($text)
        {

            $text = str_replace(array('<br>', '</p>', '<br />', '</div>'), "\n", $text);
            $text = strip_tags($text);
            $words = preg_split("/[\n\r\t ]+/", $text, -1, PREG_SPLIT_NO_EMPTY);
            if (! is_array($words)) {
                return 0;
            } else {
                return count($words);
            }
        }
    }


}
