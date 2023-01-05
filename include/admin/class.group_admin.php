<?php

/**
* @package     Tag Groups
* @author      Christoph Amthor
* @copyright   2021 Christoph Amthor (@ Chatty Mango, chattymango.com)
* @license     GPL-3.0+
*/
if ( !class_exists( 'TagGroups_Group_Admin' ) ) {
    class TagGroups_Group_Admin
    {
        /**
         * Outputs a table on a submenu page where you can add, delete, change tag groups, their labels and their order.
         *
         * @param void
         * @return void
         */
        static function render_group_administration()
        {
            global  $tag_groups_premium_fs_sdk, $tag_group_groups ;
            $tag_group_show_filter_tags = TagGroups_Options::get_option( 'tag_group_show_filter_tags', 1 );
            //tags
            $tag_group_show_filter = TagGroups_Options::get_option( 'tag_group_show_filter', 1 );
            // posts
            $this_post_type = preg_replace( '/tag-groups_(.+)/', '$1', sanitize_title( $_GET['page'] ) );
            $post_type_taxonomies = get_object_taxonomies( $this_post_type );
            $first_enabled_taxonomy = '';
            $taxonomies = TagGroups_Taxonomy::get_enabled_taxonomies( $post_type_taxonomies );
            /**
             * Check if the tag filter is activated
             */
            if ( $tag_group_show_filter_tags ) {
                // get first of taxonomies that are associated with that $post_type
                /**
                 * Show the link to the taxonomy filter only if there is only one taxonomy for this post type (otherwise ambiguous where to link)
                 */
                if ( !empty($taxonomies) && count( $taxonomies ) == 1 ) {
                    $first_enabled_taxonomy = TagGroups_Utilities::get_first_element( $taxonomies );
                }
            }
            /**
             * In case we use the WPML plugin: consider the language
             */
            $current_language = TagGroups_WPML::get_current_language();
            
            if ( $current_language ) {
                
                if ( 'all' == $current_language ) {
                    $wpml_piece = '&lang=' . (string) apply_filters( 'wpml_default_language', NULL );
                } else {
                    $wpml_piece = '&lang=' . $current_language;
                }
            
            } else {
                $wpml_piece = '';
            }
            
            
            if ( $this_post_type == 'post' ) {
                $post_type_piece = '';
            } else {
                $post_type_piece = '&post_type=' . $this_post_type;
            }
            
            $items_per_page = self::get_items_per_page();
            $protocol = ( isset( $_SERVER['HTTPS'] ) ? 'https://' : 'http://' );
            $post_url = ( empty($tag_group_show_filter) ? '' : admin_url( 'edit.php?post_type=' . $this_post_type . $wpml_piece, $protocol ) );
            $tags_url = ( empty($first_enabled_taxonomy) ? '' : admin_url( 'edit-tags.php?taxonomy=' . $first_enabled_taxonomy . $wpml_piece . $post_type_piece, $protocol ) );
            $settings_url = admin_url( 'admin.php?page=tag-groups-settings' );
            $admin_url = admin_url( 'admin-ajax.php', $protocol );
            if ( isset( $_GET['lang'] ) ) {
                $admin_url = add_query_arg( 'lang', sanitize_key( $_GET['lang'] ), $admin_url );
            }
            
            if ( 'all' == $current_language ) {
                $view = new TagGroups_View( 'partials/language_notice' );
                $view->render();
            }
            
            $view = new TagGroups_View( 'admin/tag_groups_admin' );
            $view->set( array(
                'tag_group_show_filter' => $tag_group_show_filter || $tag_group_show_filter_tags,
                'show_parents'          => $tag_groups_premium_fs_sdk->is_plan_or_trial( 'premium' ),
                'post_url'              => $post_url,
                'tags_url'              => $tags_url,
                'items_per_page'        => $items_per_page,
                'settings_url'          => $settings_url,
                'admin_url'             => $admin_url,
                'taxonomies'            => $taxonomies,
            ) );
            $view->render();
        }
        
        /**
         * AJAX handler to manage Tag Groups
         */
        static function ajax_manage_groups()
        {
            global  $tag_groups_premium_fs_sdk, $tag_group_groups ;
            /**
             * default: We allow duplicate group names if we have parents
             */
            $allow_duplicate_group_name = $tag_groups_premium_fs_sdk->is_plan_or_trial( 'premium' ) && !empty($tag_group_groups->get_parents());
            /**
             * Here we can programmatically allow duplicate group names
             * 
             * @param bool
             * @return bool
             */
            $allow_duplicate_group_names = apply_filters( 'tag_groups_allow_duplicate_group_names', $allow_duplicate_group_name );
            
            if ( isset( $_REQUEST['tag_groups_task'] ) ) {
                $task = $_REQUEST['tag_groups_task'];
            } else {
                $task = 'refresh';
            }
            
            
            if ( isset( $_REQUEST['tag_groups_taxonomy'] ) ) {
                
                if ( is_array( $_REQUEST['tag_groups_taxonomy'] ) ) {
                    $taxonomy = array_map( 'sanitize_title', $_REQUEST['tag_groups_taxonomy'] );
                } else {
                    $taxonomy = sanitize_title( $_REQUEST['tag_groups_taxonomy'] );
                }
            
            } else {
                $taxonomy = array( 'post_tag' );
            }
            
            $message = '';
            $tag_group_role_edit_groups = 'edit_pages';
            
            if ( $task != 'refresh' && $task != 'test' && !(current_user_can( $tag_group_role_edit_groups ) && wp_verify_nonce( $_REQUEST['nonce'], 'tg_groups_management' )) ) {
                self::ajax_send_error( 'Security check', $task );
                exit;
            }
            
            
            if ( isset( $_REQUEST['tag_groups_position'] ) ) {
                $position = (int) $_REQUEST['tag_groups_position'];
            } else {
                $position = 0;
            }
            
            
            if ( isset( $_REQUEST['tag_groups_new_position'] ) ) {
                $new_position = (int) $_REQUEST['tag_groups_new_position'];
            } else {
                $new_position = 0;
            }
            
            
            if ( isset( $_REQUEST['tag_groups_filter_label'] ) ) {
                $tag_groups_filter_label = sanitize_text_field( $_REQUEST['tag_groups_filter_label'] );
            } else {
                $tag_groups_filter_label = '';
            }
            
            if ( isset( $_REQUEST['tag_groups_start_position'] ) ) {
                $start_position = (int) $_REQUEST['tag_groups_start_position'];
            }
            if ( empty($start_position) || $start_position < 1 ) {
                $start_position = 1;
            }
            $tg_group = new TagGroups_Group();
            switch ( $task ) {
                case "sortup":
                    $tag_group_groups->sort( 'up' )->save();
                    $message = __( 'The groups have been sorted alphabetically.', 'tag-groups' );
                    break;
                case "sortdown":
                    $tag_group_groups->sort( 'down' )->save();
                    $message = __( 'The groups have been sorted alphabetically.', 'tag-groups' );
                    break;
                case "new":
                    if ( isset( $_REQUEST['tag_groups_label'] ) ) {
                        $label = stripslashes( sanitize_text_field( $_REQUEST['tag_groups_label'] ) );
                    }
                    
                    if ( empty($label) ) {
                        $message = __( 'The label cannot be empty.', 'tag-groups' );
                        self::ajax_send_error( $message, $task );
                    } elseif ( !$allow_duplicate_group_names && $tg_group->find_by_label( $label ) ) {
                        $message = sprintf( __( 'A tag group with the label \'%s\' already exists, or the label has not changed. Please choose another one or go back.', 'tag-groups' ), $label );
                        self::ajax_send_error( $message, $task );
                    } else {
                        $tg_group->create( $label, $position + 1 );
                        $message = sprintf( __( 'A new tag group with the label \'%s\' has been created!', 'tag-groups' ), $label );
                    }
                    
                    break;
                case "new-parent":
                    break;
                case "update":
                    if ( isset( $_REQUEST['tag_groups_label'] ) ) {
                        $label = stripslashes( sanitize_text_field( $_REQUEST['tag_groups_label'] ) );
                    }
                    
                    if ( empty($label) ) {
                        $message = __( 'The label cannot be empty.', 'tag-groups' );
                        self::ajax_send_error( $message, $task );
                    } elseif ( !$allow_duplicate_group_names && $tg_group->find_by_label( $label ) ) {
                        
                        if ( !empty($position) && $position == $tg_group->get_position() ) {
                            // Label hast not changed, just ignore
                        } else {
                            $message = sprintf( __( 'A tag group with the label \'%s\' already exists.', 'tag-groups' ), $label );
                            self::ajax_send_error( $message, $task );
                        }
                    
                    } else {
                        
                        if ( !empty($position) ) {
                            if ( $tg_group->find_by_position( $position ) ) {
                                $tg_group->set_label( $label )->save();
                            }
                        } else {
                            self::ajax_send_error( 'error: invalid position: ' . $position, $task );
                        }
                        
                        $message = sprintf( __( 'The tag group with the label \'%s\' has been saved!', 'tag-groups' ), $label );
                    }
                    
                    break;
                case "delete":
                    
                    if ( !empty($position) && $tg_group->find_by_position( $position ) ) {
                        $message = sprintf( __( 'A tag group with the id %1$s and the label \'%2$s\' has been deleted.', 'tag-groups' ), $tg_group->get_group_id(), $tg_group->get_label() );
                        $tg_group->delete();
                    } else {
                        self::ajax_send_error( 'error: invalid position: ' . $position, $task );
                    }
                    
                    break;
                case "up":
                    if ( $position > 1 && $tg_group->find_by_position( $position ) ) {
                        if ( $tg_group->move_to_position( $position - 1 ) !== false ) {
                            $tg_group->save();
                        }
                    }
                    break;
                case "down":
                    if ( $position < $tag_group_groups->get_max_position() && $tg_group->find_by_position( $position ) ) {
                        if ( $tg_group->move_to_position( $position + 1 ) !== false ) {
                            $tg_group->save();
                        }
                    }
                    break;
                case "move":
                    if ( $new_position < 1 ) {
                        $new_position = 1;
                    }
                    if ( $new_position > $tag_group_groups->get_max_position() ) {
                        $new_position = $tag_group_groups->get_max_position();
                    }
                    if ( $position == $new_position ) {
                        break;
                    }
                    if ( $tg_group->find_by_position( $position ) ) {
                        if ( $tg_group->move_to_position( $new_position ) !== false ) {
                            $tg_group->save();
                        }
                    }
                    break;
                case "refresh":
                    // do nothing here
                    break;
                case 'test':
                    echo  json_encode( array(
                        'data'         => 'success',
                        'supplemental' => array(
                        'message' => 'This is the regular Ajax response.',
                    ),
                    ) ) ;
                    exit;
                    break;
            }
            $tag_group_groups_filtered = clone $tag_group_groups;
            $number_of_filtered_term_groups = $tag_group_groups_filtered->filter_by_substring( $tag_groups_filter_label )->get_number_of_term_groups();
            if ( $start_position > $number_of_filtered_term_groups ) {
                $start_position = $number_of_filtered_term_groups;
            }
            $items_per_page = self::get_items_per_page();
            // calculate start and end positions
            $start_position = (int) floor( ($start_position - 1) / $items_per_page ) * $items_per_page + 1;
            
            if ( $start_position + $items_per_page - 1 < $number_of_filtered_term_groups ) {
                $end_position = $start_position + $items_per_page - 1;
            } else {
                $end_position = $number_of_filtered_term_groups;
            }
            
            echo  json_encode( array(
                'data'         => 'success',
                'supplemental' => array(
                'task'              => $task,
                'message'           => $message,
                'nonce'             => wp_create_nonce( 'tg_groups_management' ),
                'start_position'    => $start_position,
                'groups'            => self::assemble_group_table(
                $start_position,
                $end_position,
                $taxonomy,
                $tag_group_groups_filtered
            ),
                'max_number'        => $number_of_filtered_term_groups,
                'parents_available' => $tag_groups_premium_fs_sdk->is_plan_or_trial( 'premium' ) && !empty($tag_group_groups->get_parents()),
                'only_parents'      => $tag_groups_premium_fs_sdk->is_plan_or_trial( 'premium' ) && $tag_group_groups->is_only_parents(),
                'is_filtered'       => !empty($tag_groups_filter_label),
            ),
            ) ) ;
            exit;
        }
        
        /**
         *  Rerturns an error message to AJAX
         */
        static function ajax_send_error( $message = 'error', $task = 'unknown' )
        {
            echo  json_encode( array(
                'data'         => 'error',
                'supplemental' => array(
                'message' => $message,
                'nonce'   => wp_create_nonce( 'tg_groups_management' ),
                'task'    => $task,
            ),
            ) ) ;
            exit;
        }
        
        /**
         * Assemble the content of the table of tag groups for AJAX
         */
        static function assemble_group_table(
            $start_position,
            $end_position,
            $taxonomy,
            $tag_group_groups_filtered
        )
        {
            $term_groups = array_values( $tag_group_groups_filtered->get_all_with_position_as_key( true ) );
            $output = array();
            if ( count( $term_groups ) <= 1 ) {
                return $output;
            }
            for ( $i = $start_position ;  $i <= $end_position ;  $i++ ) {
                if ( empty($term_groups[$i]) ) {
                    continue;
                }
                $tg_group = new TagGroups_Group( $term_groups[$i]['term_group'] );
                array_push( $output, array(
                    'id'           => $term_groups[$i]['term_group'],
                    'label'        => $term_groups[$i]['label'],
                    'position'     => $term_groups[$i]['position'],
                    'amount'       => $tg_group->get_number_of_terms( $taxonomy ),
                    'is_parent'    => $tg_group->is_parent,
                    'parent_label' => $tg_group->get_parent_label(),
                ) );
            }
            return $output;
        }
        
        /**
         * Returns the items per page on the tag groups screen
         *
         * @param void
         * @return int
         */
        public static function get_items_per_page()
        {
            global  $tag_groups_premium_fs_sdk ;
            $items_per_page = TAG_GROUPS_ITEMS_PER_PAGE;
            return $items_per_page;
        }
    
    }
}