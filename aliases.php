<?php

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
 * @copyright  2019 Christoph Amthor (@ Chatty Mango, chattymango.com)
 * @license    see official vendor website
 *
 * @since      1.19.0
 */

/**
 * Aliases for backwards compatibility and convenience
 *
 */
if (!function_exists('tag_groups_cloud') && class_exists('TagGroups_Shortcode_Tabs')) {
    /**
     *
     * Wrapper for the static method tag_groups_cloud
     *
     * @param  array    $atts
     * @param  bool     $return_array
     * @return string
     */
    function tag_groups_cloud($atts = array(), $return_array = false)
    {
        return ( new TagGroups_Shortcode_Tabs() )->tag_groups_cloud($atts, $return_array);
    }

}
if (!function_exists('tag_groups_accordion') && class_exists('TagGroups_Shortcode_Accordion')) {
    /**
     *
     * Wrapper for the static method tag_groups_accordion
     *
     * @param  array    $atts
     * @return string
     */
    function tag_groups_accordion($atts = array())
    {
        return ( new TagGroups_Shortcode_Accordion() )->tag_groups_accordion($atts);
    }

}
if (!function_exists('tag_groups_simple_cloud') && class_exists('TagGroups_Shortcode_Simple')) {
    /**
     * Wrapper for the simple tag cloud shortcode renderer.
     *
     * @param  array $atts
     * @return string
     */
    function tag_groups_simple_cloud($atts = array())
    {
        return ( new TagGroups_Shortcode_Simple() )->tag_groups_simple_cloud($atts);
    }

}
if (!function_exists('tag_groups_table') && class_exists('TagGroups_Shortcode_Table')) {
    /**
     * Wrapper for the table tag cloud shortcode renderer.
     *
     * @param  array $atts
     * @return string
     */
    function tag_groups_table($atts = array())
    {
        return ( new TagGroups_Shortcode_Table() )->tag_groups_table($atts);
    }

}
if (!function_exists('tag_groups_shuffle_box') && class_exists('TagGroups_Shortcode_Shuffle_Box')) {
    /**
     * Wrapper for the shuffle box shortcode renderer.
     *
     * @param  array $atts
     * @return string
     */
    function tag_groups_shuffle_box($atts = array())
    {
        return ( new TagGroups_Shortcode_Shuffle_Box() )->tag_groups_shuffle_box($atts);
    }

}
if (!function_exists('tag_groups_post_list') && class_exists('TagGroups_Shortcode_Post_List')) {
    /**
     * Wrapper for the post list shortcode renderer.
     *
     * @param  array $atts
     * @return string
     */
    function tag_groups_post_list($atts = array())
    {
        return ( new TagGroups_Shortcode_Post_List() )->tag_groups_post_list($atts);
    }

}
if (!function_exists('tag_groups_tpf_menu') && class_exists('TagGroups_Shortcode_TPF')) {
    /**
     * Wrapper for the toggle post filter menu shortcode renderer.
     *
     * @param  array $atts
     * @return string
     */
    function tag_groups_tpf_menu($atts = array())
    {
        return ( new TagGroups_Shortcode_TPF() )->tag_groups_tpf_menu($atts);
    }

}
if (!function_exists('tag_groups_tpf_body') && class_exists('TagGroups_Shortcode_TPF')) {
    /**
     * Wrapper for the toggle post filter body shortcode renderer.
     *
     * @param  array $atts
     * @return string
     */
    function tag_groups_tpf_body($atts = array())
    {
        return ( new TagGroups_Shortcode_TPF() )->tag_groups_tpf_body($atts);
    }

}
if (!function_exists('tag_groups_tpf_messages') && class_exists('TagGroups_Shortcode_TPF')) {
    /**
     * Wrapper for the toggle post filter messages shortcode renderer.
     *
     * @param  array $atts
     * @return string
     */
    function tag_groups_tpf_messages($atts = array())
    {
        return ( new TagGroups_Shortcode_TPF() )->tag_groups_tpf_messages($atts);
    }

}
if (!function_exists('tag_groups_tpf_reset') && class_exists('TagGroups_Shortcode_TPF')) {
    /**
     * Wrapper for the toggle post filter reset shortcode renderer.
     *
     * @param  array $atts
     * @return string
     */
    function tag_groups_tpf_reset($atts = array())
    {
        return ( new TagGroups_Shortcode_TPF() )->tag_groups_tpf_reset($atts);
    }

}
if (!function_exists('tag_groups_tpf_slider_button') && class_exists('TagGroups_Shortcode_TPF')) {
    /**
     * Wrapper for the toggle post filter slider button shortcode renderer.
     *
     * @param  array $atts
     * @return string
     */
    function tag_groups_tpf_slider_button($atts = array())
    {
        return ( new TagGroups_Shortcode_TPF() )->tag_groups_tpf_slider_button($atts);
    }

}
if (!function_exists('tag_groups_tpf_order_menu') && class_exists('TagGroups_Shortcode_TPF')) {
    /**
     * Wrapper for the toggle post filter order menu shortcode renderer.
     *
     * @param  array $atts
     * @return string
     */
    function tag_groups_tpf_order_menu($atts = array())
    {
        return ( new TagGroups_Shortcode_TPF() )->tag_groups_tpf_order_menu($atts);
    }

}
if (!function_exists('tag_groups_tpf_text_search') && class_exists('TagGroups_Shortcode_TPF')) {
    /**
     * Wrapper for the toggle post filter text search shortcode renderer.
     *
     * @param  array $atts
     * @return string
     */
    function tag_groups_tpf_text_search($atts = array())
    {
        return ( new TagGroups_Shortcode_TPF() )->tag_groups_tpf_text_search($atts);
    }

}
if (!function_exists('post_in_tag_group')) {
    /**
     * Checks if the post with $post_id has a tag that is in the tag group with $tag_group_id.
     *
     * @param  int       $post_id
     * @param  int       $tag_group_id
     * @return boolean
     */
    function post_in_tag_group($post_id, $tag_group_id)
    {
        $tag_group_taxonomies = TagGroups_Taxonomy::get_enabled_taxonomies();
        $tags = array();
        foreach ($tag_group_taxonomies as $tag_group_taxonomy) {
            $tags_tax = get_the_terms($post_id, $tag_group_taxonomy);

            if (is_array($tags_tax)) {
                $tags = array_merge($tags, $tags_tax);
            } elseif (false === $tags_tax) {
                return false;
            }
        }
        if ($tags) {
            foreach ($tags as $tag) {
                $tg_term = new TagGroups_Term($tag);
                if ($tg_term->has_group($tag_group_id)) {
                    return true;
                }
            }
        }
        return false;
    }

}
