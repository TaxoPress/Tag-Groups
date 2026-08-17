<?php

/**
 * @package     Tag Groups
 *
 * @author      Christoph Amthor
 * @copyright   2019 Christoph Amthor (@ Chatty Mango, chattymango.com)
 * @license     GPL-3.0+
 */

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ValidClassName.NotCamelCaps, PSR1.Methods.CamelCapsMethodName.NotCamelCaps
if (! class_exists('TagGroups_Enqueue')) {

  /**
   *
   */
    class TagGroups_Enqueue
    {
      /**
       * Add js and css to frontend
       *
       * @param  void
       * @return void
       */
        public function wp_enqueue_scripts()
        {

            if (is_admin()) {
                return;
            }

            if (TagGroups_Options::get_option('tag_group_enqueue_jquery', 1)) {
                wp_enqueue_script('jquery');

                wp_enqueue_script('jquery-ui-core');

                wp_enqueue_script('jquery-ui-tabs');

                wp_enqueue_script('jquery-ui-accordion');
            }

            if (defined('WP_DEBUG') && WP_DEBUG) {
                wp_register_script('tag-groups-js-frontend', TAG_GROUPS_PLUGIN_URL . '/assets/js/frontend.js', array(), TAG_GROUPS_VERSION);
            } else {
                wp_register_script('tag-groups-js-frontend', TAG_GROUPS_PLUGIN_URL . '/assets/js/frontend.min.js', array(), TAG_GROUPS_VERSION);
            }

            wp_enqueue_script('tag-groups-js-frontend');

            $this->load_theme_css();

            wp_enqueue_style('tag-groups-css-frontend-structure');

            wp_enqueue_style('tag-groups-css-frontend-theme');

            $this->enqueue_frontend_css();

            global $post;
            $content = is_a($post, 'WP_Post') ? $post->post_content : '';

            if ($this->content_uses_table_tag_cloud($content)) {
                $this->enqueue_table_tag_cloud_assets();
            }

            if ($this->content_uses_shuffle_box($content)) {
                $this->enqueue_shuffle_box_assets();
            }

            if ($this->content_uses_post_list($content)) {
                $this->enqueue_post_list_assets();
            }

            if ($this->content_uses_toggle_post_filter($content)) {
                $this->enqueue_toggle_post_filter_assets();
            }
        }

      /**
       * Add css to backend
       *
       * @param  string $where
       * @return void
       */
        public function admin_enqueue_scripts($where)
        {

            if (strpos($where, 'tag-groups-settings') !== false) {
                wp_enqueue_script('jquery');

                wp_enqueue_script('jquery-ui-core');

                wp_enqueue_script('jquery-ui-accordion');

                wp_enqueue_script('jquery-ui-tabs');

                wp_enqueue_script('jquery-ui-tooltip');

                wp_register_style('tag-groups-css-backend-structure', TAG_GROUPS_PLUGIN_URL . '/assets/css/jquery-ui.structure.min.css', array(), TAG_GROUPS_VERSION);

                wp_enqueue_style('tag-groups-css-backend-structure');

                wp_register_style('tag-groups-css-backend-theme', TAG_GROUPS_PLUGIN_URL . '/assets/css/base/jquery-ui.theme.min.css', array(), TAG_GROUPS_VERSION);

                wp_enqueue_style('tag-groups-css-backend-theme');

                if (defined('WP_DEBUG') && WP_DEBUG) {
                    wp_register_style('tag-groups-css-backend-tgb', TAG_GROUPS_PLUGIN_URL . '/assets/css/backend.css', array(), TAG_GROUPS_VERSION);
                } else {
                    wp_register_style('tag-groups-css-backend-tgb', TAG_GROUPS_PLUGIN_URL . '/assets/css/backend.min.css', array(), TAG_GROUPS_VERSION);
                }

                wp_enqueue_style('tag-groups-css-backend-tgb');

                wp_register_script('tag-groups-sumoselect-js', TAG_GROUPS_PLUGIN_URL . '/assets/js/jquery.sumoselect.min.js', array(), TAG_GROUPS_VERSION);

                wp_enqueue_script('tag-groups-sumoselect-js');

                $banners_style_path = '/publishpress/wordpress-banners/assets/css/style.css';
                $banners_vendor_url = '/vendor';

                if (defined('TAG_GROUPS_LIB_VENDOR_PATH') && file_exists(TAG_GROUPS_LIB_VENDOR_PATH . $banners_style_path)) {
                    $banners_vendor_url = '/lib/vendor';
                }

                wp_enqueue_style('pp-wordpress-banners-style', TAG_GROUPS_PLUGIN_URL . $banners_vendor_url . $banners_style_path, false, TAG_GROUPS_VERSION);


                $this->load_sumoselect_css();
            } elseif (strpos($where, '_page_tag-groups') !== false) {
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    wp_register_style('tag-groups-css-backend-tgb', TAG_GROUPS_PLUGIN_URL . '/assets/css/backend.css', array(), TAG_GROUPS_VERSION);
                } else {
                    wp_register_style('tag-groups-css-backend-tgb', TAG_GROUPS_PLUGIN_URL . '/assets/css/backend.min.css', array(), TAG_GROUPS_VERSION);
                }

                wp_enqueue_style('tag-groups-css-backend-tgb');

                if (defined('WP_DEBUG') && WP_DEBUG) {
                    wp_register_script('tag-groups-js-backend', TAG_GROUPS_PLUGIN_URL . '/assets/js/backend.js', array(), TAG_GROUPS_VERSION);
                } else {
                    wp_register_script('tag-groups-js-backend', TAG_GROUPS_PLUGIN_URL . '/assets/js/backend.min.js', array(), TAG_GROUPS_VERSION);
                }

                wp_enqueue_script('tag-groups-js-backend');

                wp_enqueue_script('jquery-ui-sortable');

                wp_enqueue_script('jquery-ui-core');

                wp_enqueue_script('jquery-ui-accordion');

                wp_enqueue_script('jquery-ui-tooltip');
            } elseif (strpos($where, 'edit-tags.php') !== false || strpos($where, 'term.php') !== false || strpos($where, 'edit.php') !== false) {
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    wp_register_script('tag-groups-sumoselect-js', TAG_GROUPS_PLUGIN_URL . '/assets/js/jquery.sumoselect.js', array(), TAG_GROUPS_VERSION);
                } else {
                    wp_register_script('tag-groups-sumoselect-js', TAG_GROUPS_PLUGIN_URL . '/assets/js/jquery.sumoselect.min.js', array(), TAG_GROUPS_VERSION);
                }

                wp_enqueue_script('tag-groups-sumoselect-js');

                $this->load_sumoselect_css();

                if (defined('WP_DEBUG') && WP_DEBUG) {
                    wp_register_style('tag-groups-css-backend-tgb', TAG_GROUPS_PLUGIN_URL . '/assets/css/backend.css', array(), TAG_GROUPS_VERSION);
                } else {
                    wp_register_style('tag-groups-css-backend-tgb', TAG_GROUPS_PLUGIN_URL . '/assets/css/backend.min.css', array(), TAG_GROUPS_VERSION);
                }

                wp_enqueue_style('tag-groups-css-backend-tgb');
            } elseif (strpos($where, 'post-new.php') !== false || strpos($where, 'post.php') !== false) {
            // phpcs:ignore Squiz.PHP.CommentedOutCode.Found
            // use following line to enable gutenberg on Appearance > Widgets
            // } elseif ( strpos( $where, 'post-new.php' ) !== false || strpos( $where, 'post.php' ) !== false || strpos( $where, 'widgets.php' ) !== false ) {

                if (defined('WP_DEBUG') && WP_DEBUG) {
                    wp_register_style('tag-groups-react-select-css', TAG_GROUPS_PLUGIN_URL . '/assets/css/react-select.css', array(), TAG_GROUPS_VERSION);
                } else {
                    wp_register_style('tag-groups-react-select-css', TAG_GROUPS_PLUGIN_URL . '/assets/css/react-select.min.css', array(), TAG_GROUPS_VERSION);
                }

                wp_enqueue_style('tag-groups-react-select-css');

                if (defined('WP_DEBUG') && WP_DEBUG) {
                    wp_register_style('tag-groups-css-backend-tgb', TAG_GROUPS_PLUGIN_URL . '/assets/css/backend.css', array(), TAG_GROUPS_VERSION);
                } else {
                    wp_register_style('tag-groups-css-backend-tgb', TAG_GROUPS_PLUGIN_URL . '/assets/css/backend.min.css', array(), TAG_GROUPS_VERSION);
                }

                wp_enqueue_style('tag-groups-css-backend-tgb');
            }

          /* If we have RTL, we load an additional file for support */
            if (wp_style_is('tag-groups-css-backend-tgb', 'enqueued') && is_rtl()) {
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    wp_register_style('tag-groups-css-backend-rtl-tgb', TAG_GROUPS_PLUGIN_URL . '/assets/css/backend-rtl.css', array(), TAG_GROUPS_VERSION);
                } else {
                    wp_register_style('tag-groups-css-backend-rtl-tgb', TAG_GROUPS_PLUGIN_URL . '/assets/css/backend-rtl.min.css', array(), TAG_GROUPS_VERSION);
                }

                wp_enqueue_style('tag-groups-css-backend-rtl-tgb');
            }

            if (TagGroups_Gutenberg::is_gutenberg_active()) {
                $this->admin_enqueue_scripts_for_gutenberg();
            }
        }

      /**
       * Adds js and css to the Gutenberg editor page
       *
       *
       * @param  void
       * @return void
       */
        public function admin_enqueue_scripts_for_gutenberg()
        {

          /** enqueue frontend scripts and styling only if shortcode in use */

            $screen = get_current_screen();

            if (is_object($screen) && property_exists($screen, 'base') && 'post' != $screen->base && 'site-editor' != $screen->base) {
            // use following line to enable gutenberg on Appearance > Widgets
            // if ( is_object( $screen ) && property_exists( $screen, 'base' ) && 'post' != $screen->base && 'widgets' != $screen->base ) {

                return;
            }

            wp_enqueue_script('jquery');

            wp_enqueue_script('jquery-ui-core');

            wp_enqueue_script('jquery-ui-tabs');

            wp_enqueue_script('jquery-ui-accordion');

            wp_enqueue_script('jquery-ui-tooltip');

            $this->load_theme_css();

            wp_enqueue_style('tag-groups-css-frontend-structure');

            wp_enqueue_style('tag-groups-css-frontend-theme');

            $this->enqueue_frontend_css();

          /**
           * load the JS
           */
            if (defined('WP_DEBUG') && WP_DEBUG) {
                wp_register_script('tag-groups-js-frontend', TAG_GROUPS_PLUGIN_URL . '/assets/js/frontend.js', array(), TAG_GROUPS_VERSION);
            } else {
                wp_register_script('tag-groups-js-frontend', TAG_GROUPS_PLUGIN_URL . '/assets/js/frontend.min.js', array(), TAG_GROUPS_VERSION);
            }

            wp_enqueue_script('tag-groups-js-frontend');

            $this->enqueue_table_tag_cloud_assets();
            $this->enqueue_shuffle_box_assets();
            $this->enqueue_post_list_assets();
            $this->enqueue_toggle_post_filter_assets();
        }

      /**
       * enqueue CSS for free features
       *
       * @return void
       */
        private function enqueue_frontend_css()
        {

            if (defined('WP_DEBUG') && WP_DEBUG) {
                wp_register_style('tag-groups-css-frontend', TAG_GROUPS_PLUGIN_URL . '/assets/css/frontend.css', array(), TAG_GROUPS_VERSION);
            } else {
                wp_register_style('tag-groups-css-frontend', TAG_GROUPS_PLUGIN_URL . '/assets/css/frontend.min.css', array(), TAG_GROUPS_VERSION);
            }

            wp_enqueue_style('tag-groups-css-frontend');
        }

      /**
       * Returns whether the content uses the simple tag cloud.
       *
       * @param string $content
       * @return bool
       */
        private function content_uses_simple_tag_cloud($content)
        {
            return is_string($content) && (
                has_shortcode($content, 'tag_groups_simple_cloud') ||
                has_shortcode($content, 'tag_groups_combined_cloud') ||
                strpos($content, '<!-- wp:chatty-mango/tag-groups-premium-cloud-combined') !== false
            );
        }

      /**
       * Returns whether the content uses the table tag cloud.
       *
       * @param string $content
       * @return bool
       */
        private function content_uses_table_tag_cloud($content)
        {
            return is_string($content) && (
                has_shortcode($content, 'tag_groups_table') ||
                strpos($content, '<!-- wp:chatty-mango/tag-groups-premium-cloud-table') !== false
            );
        }

      /**
       * Returns whether the content uses the shuffle box.
       *
       * @param string $content
       * @return bool
       */
        private function content_uses_shuffle_box($content)
        {
            return is_string($content) && (
                has_shortcode($content, 'tag_groups_shuffle_box') ||
                strpos($content, '<!-- wp:chatty-mango/tag-groups-premium-shuffle-box') !== false
            );
        }

      /**
       * Returns whether the content uses the post list.
       *
       * @param string $content
       * @return bool
       */
        private function content_uses_post_list($content)
        {
            return is_string($content) && (
                has_shortcode($content, 'tag_groups_post_list') ||
                strpos($content, '<!-- wp:chatty-mango/tag-groups-premium-post-filter') !== false
            );
        }

      /**
       * Returns whether the content uses the toggle post filter feature family.
       *
       * @param string $content
       * @return bool
       */
        private function content_uses_toggle_post_filter($content)
        {
            return is_string($content) && (
                has_shortcode($content, 'tag_groups_tpf_menu') ||
                has_shortcode($content, 'tag_groups_dpf_toggle_menu') ||
                has_shortcode($content, 'tag_groups_tpf_body') ||
                has_shortcode($content, 'tag_groups_dpf_toggle_body') ||
                has_shortcode($content, 'tag_groups_tpf_messages') ||
                has_shortcode($content, 'tag_groups_dpf_toggle_messages') ||
                has_shortcode($content, 'tag_groups_tpf_reset') ||
                has_shortcode($content, 'tag_groups_dpf_toggle_reset') ||
                has_shortcode($content, 'tag_groups_tpf_slider_button') ||
                has_shortcode($content, 'tag_groups_tpf_order_menu') ||
                has_shortcode($content, 'tag_groups_tpf_text_search') ||
                strpos($content, '<!-- wp:chatty-mango/chatty-mango-tpf-menu') !== false ||
                strpos($content, '<!-- wp:chatty-mango/chatty-mango-tpf-menu-') !== false ||
                strpos($content, '<!-- wp:chatty-mango/chatty-mango-guten-dpfwt-menu') !== false ||
                strpos($content, '<!-- wp:chatty-mango/chatty-mango-guten-dpfwt-body') !== false ||
                strpos($content, '<!-- wp:chatty-mango/chatty-mango-guten-dpfwt-messages') !== false ||
                strpos($content, '<!-- wp:chatty-mango/chatty-mango-guten-dpfwt-reset') !== false ||
                strpos($content, '<!-- wp:chatty-mango/chatty-mango-tpf-slider-button') !== false ||
                strpos($content, '<!-- wp:chatty-mango/chatty-mango-tpf-order-menu') !== false ||
                strpos($content, '<!-- wp:chatty-mango/chatty-mango-tpf-text-search') !== false
            );
        }

      /**
       * Enqueue assets for the table tag cloud.
       *
       * @return void
       */
        private function enqueue_table_tag_cloud_assets()
        {
            wp_register_style('tag-groups-basictable-css', TAG_GROUPS_PLUGIN_URL . '/assets/css/basictable.css', array(), TAG_GROUPS_VERSION);
            wp_enqueue_style('tag-groups-basictable-css');

            wp_register_script('tag-groups-basictable-js', TAG_GROUPS_PLUGIN_URL . '/assets/js/jquery.basictable.min.js', array( 'jquery' ), TAG_GROUPS_VERSION);
            wp_enqueue_script('tag-groups-basictable-js');
        }

      /**
       * Enqueue assets for the shuffle box.
       *
       * @return void
       */
        private function enqueue_shuffle_box_assets()
        {
            wp_register_style('tag-groups-shuffle-box-css', TAG_GROUPS_PLUGIN_URL . '/assets/css/shuffle-box.css', array(), TAG_GROUPS_VERSION);
            wp_enqueue_style('tag-groups-shuffle-box-css');

            wp_register_script('tag-groups-isotope-js', TAG_GROUPS_PLUGIN_URL . '/assets/js/isotope.pkgd.min.js', array( 'jquery' ), TAG_GROUPS_VERSION);
            wp_enqueue_script('tag-groups-isotope-js');

            if (defined('WP_DEBUG') && WP_DEBUG) {
                wp_register_script('tag-groups-shuffle-box-js', TAG_GROUPS_PLUGIN_URL . '/assets/js/shuffle-box.js', array( 'jquery', 'tag-groups-isotope-js' ), TAG_GROUPS_VERSION);
            } else {
                wp_register_script('tag-groups-shuffle-box-js', TAG_GROUPS_PLUGIN_URL . '/assets/js/shuffle-box.min.js', array( 'jquery', 'tag-groups-isotope-js' ), TAG_GROUPS_VERSION);
            }

            wp_enqueue_script('tag-groups-shuffle-box-js');
        }

      /**
       * Enqueue styles for the post list feature.
       *
       * @return void
       */
        private function enqueue_post_list_assets()
        {
            wp_register_style('tag-groups-post-list-css', TAG_GROUPS_PLUGIN_URL . '/assets/css/post-list.css', array(), TAG_GROUPS_VERSION);
            wp_enqueue_style('tag-groups-post-list-css');
        }

      /**
       * Enqueue assets for the toggle post filter feature family.
       *
       * @return void
       */
        private function enqueue_toggle_post_filter_assets()
        {
            wp_enqueue_script('jquery');
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-accordion');
            wp_enqueue_script('jquery-masonry');
            wp_enqueue_script('imagesloaded');
            wp_enqueue_style('dashicons');

            if (defined('WP_DEBUG') && WP_DEBUG) {
                wp_register_script('tag-groups-tpf-js', TAG_GROUPS_PLUGIN_URL . '/assets/js/tpf.js', array( 'jquery', 'jquery-ui-core' ), TAG_GROUPS_VERSION);
            } else {
                wp_register_script('tag-groups-tpf-js', TAG_GROUPS_PLUGIN_URL . '/assets/js/tpf.min.js', array( 'jquery', 'jquery-ui-core' ), TAG_GROUPS_VERSION);
            }

            wp_enqueue_script('tag-groups-tpf-js');

            wp_register_script('tag-groups-jnoty-js', TAG_GROUPS_PLUGIN_URL . '/assets/js/jnoty.min.js', array( 'jquery' ), TAG_GROUPS_VERSION);
            wp_enqueue_script('tag-groups-jnoty-js');

            wp_register_style('tag-groups-jnoty-css', TAG_GROUPS_PLUGIN_URL . '/assets/css/jnoty.min.css', array(), TAG_GROUPS_VERSION);
            wp_enqueue_style('tag-groups-jnoty-css');

            wp_register_script('tag-groups-sumoselect-js', TAG_GROUPS_PLUGIN_URL . '/assets/js/jquery.sumoselect.min.js', array( 'jquery' ), TAG_GROUPS_VERSION);
            wp_enqueue_script('tag-groups-sumoselect-js');

            wp_register_style('tag-groups-sumoselect-css', TAG_GROUPS_PLUGIN_URL . '/assets/css/sumoselect.min.css', array(), TAG_GROUPS_VERSION);
            wp_enqueue_style('tag-groups-sumoselect-css');
        }


      /**
       * Load the CSS of the theme
       *
       * @return void
       */
        public function load_theme_css()
        {

            $theme = TagGroups_Options::get_option('tag_group_theme', TAG_GROUPS_STANDARD_THEME);

            if ('' == $theme) {
                return;
            }

            wp_register_style('tag-groups-css-frontend-structure', TAG_GROUPS_PLUGIN_URL . '/assets/css/jquery-ui.structure.min.css', array(), TAG_GROUPS_VERSION);

            $default_themes = explode(',', TAG_GROUPS_BUILT_IN_THEMES);

            if (in_array($theme, $default_themes)) {
                wp_register_style('tag-groups-css-frontend-theme', TAG_GROUPS_PLUGIN_URL . '/assets/css/' . $theme . '/jquery-ui.theme.min.css', array(), TAG_GROUPS_VERSION);
            } else {

              /**
               * Load minimized css if available
               */
                if (file_exists(WP_CONTENT_DIR . '/uploads/' . $theme . '/jquery-ui.theme.min.css')) {
                    wp_register_style('tag-groups-css-frontend-theme', get_bloginfo('wpurl') . '/wp-content/uploads/' . $theme . '/jquery-ui.theme.min.css', array(), TAG_GROUPS_VERSION);
                } elseif (file_exists(WP_CONTENT_DIR . '/uploads/' . $theme . '/jquery-ui.theme.css')) {
                    wp_register_style('tag-groups-css-frontend-theme', get_bloginfo('wpurl') . '/wp-content/uploads/' . $theme . '/jquery-ui.theme.css', array(), TAG_GROUPS_VERSION);
                } else {

                  /**
                   * Fallback: Is this a custom theme of an old version or did we revert to old plugin version?
                   */
                    if (file_exists(WP_CONTENT_DIR . '/uploads/' . $theme)) {
                        $dh = opendir(WP_CONTENT_DIR . '/uploads/' . $theme);

                        if (! empty($dh)) {
                            while (false !== ( $filename = @readdir($dh) )) {
                                if (preg_match("/jquery-ui-\d+\.\d+\.\d+\.custom\.(min\.)?css/i", $filename)) {
                                        wp_register_style('tag-groups-css-frontend-theme', get_bloginfo('wpurl') . '/wp-content/uploads/' . $theme . '/' . $filename, array(), TAG_GROUPS_VERSION);

                                        break;
                                }
                            }
                        }
                    } else {
                        TagGroups_Error::log('[Tag Groups] Error finding %s/uploads/%s', WP_CONTENT_DIR, $theme);
                    }
                }
            }

            wp_enqueue_style('tag-groups-css-frontend-structure');

            wp_enqueue_style('tag-groups-css-frontend-theme');
        }


      /**
       * Load the Sumoselect CSS for the text direction
       *
       * @return void
       */
        public function load_sumoselect_css()
        {

            $direction = is_rtl() ? '-rtl' : '';

            if (defined('WP_DEBUG') && WP_DEBUG) {
                wp_register_style('tag-groups-sumoselect-css', TAG_GROUPS_PLUGIN_URL . '/assets/css/sumoselect' . $direction . '.css', array(), TAG_GROUPS_VERSION);
            } else {
                wp_register_style('tag-groups-sumoselect-css', TAG_GROUPS_PLUGIN_URL . '/assets/css/sumoselect' . $direction . '.min.css', array(), TAG_GROUPS_VERSION);
            }

            wp_enqueue_style('tag-groups-sumoselect-css');
        }
    }

}
