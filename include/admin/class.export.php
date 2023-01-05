<?php

/**
* @package     Tag Groups
* @author      Christoph Amthor
* @copyright   2020 Christoph Amthor (@ Chatty Mango, chattymango.com)
* @license     GPL-3.0+
*/
if ( !class_exists( 'TagGroups_Export' ) ) {
    /**
     *
     * @since 1.38.0
     */
    class TagGroups_Export
    {
        /**
         * Options to be exported
         *
         * @var array
         */
        private  $options ;
        /**
         * Terms to be exported
         *
         * @var array
         */
        private  $terms ;
        /**
         * pseudo-random hash to cloak the exported files
         *
         * @var string
         */
        private  $hash ;
        /**
         * whether an error occured
         *
         * @var integer
         */
        private  $error ;
        function __construct()
        {
            $this->error = false;
        }
        
        /**
         * Create an array of all options that should be exported
         *
         * @return void
         */
        function process_options_for_export()
        {
            global  $tag_groups_premium_fs_sdk ;
            $this->options = array(
                'name'    => 'tag_groups_options',
                'version' => TAG_GROUPS_VERSION,
                'date'    => current_time( 'mysql' ),
            );
            $available_options = TagGroups_Options::get_available_options();
            foreach ( $available_options as $key => $value ) {
                if ( $available_options[$key]['export'] ) {
                    if ( TagGroups_Options::TAG_GROUPS_PLUGIN == $available_options[$key]['origin'] ) {
                        $this->options[$key] = TagGroups_Options::get_option( $key );
                    }
                }
            }
        }
        
        /**
         * Create an array of all terms that should be exported
         *
         * @return void
         */
        function process_terms_for_export()
        {
            // generate array of all terms
            $wp_terms = get_terms( array(
                'hide_empty' => false,
                'taxonomy'   => TagGroups_Taxonomy::get_enabled_taxonomies(),
            ) );
            $this->terms = array(
                'name'    => 'tag_groups_terms',
                'version' => TAG_GROUPS_VERSION,
                'date'    => current_time( 'mysql' ),
            );
            $this->terms['terms'] = array();
            foreach ( $wp_terms as $term ) {
                $tg_term = new TagGroups_Term( $term->term_id );
                // We export only fields that later can be updated with wp_update_term()
                $this->terms['terms'][] = array(
                    'term_id'     => $term->term_id,
                    'name'        => $term->name,
                    'slug'        => $term->slug,
                    'term_group'  => $tg_term->get_groups(),
                    'taxonomy'    => $term->taxonomy,
                    'description' => $term->description,
                    'parent'      => $term->parent,
                );
            }
        }
        
        /**
         * Writes options and terms into files
         *
         * @return void
         */
        function write_files()
        {
            try {
                // misusing the password generator to get a hash
                $this->hash = wp_generate_password( 10, false );
                /*
                 * Write settings/groups and tags separately
                 */
                $fp = fopen( WP_CONTENT_DIR . '/uploads/tag_groups_settings-' . $this->hash . '.json', 'w' );
                fwrite( $fp, json_encode( $this->options ) );
                fclose( $fp );
                $fp = fopen( WP_CONTENT_DIR . '/uploads/tag_groups_terms-' . $this->hash . '.json', 'w' );
                fwrite( $fp, json_encode( $this->terms ) );
                fclose( $fp );
            } catch ( Exception $e ) {
                $this->error = true;
            }
        }
        
        /**
         * Displays the links to download the exported files, or an error message
         *
         * @return void
         */
        function show_download_links()
        {
            
            if ( !$this->error ) {
                TagGroups_Admin_Notice::add( 'success', __( 'Your settings/groups and your terms have been exported. Please download the resulting files with right-click or ctrl-click:', 'tag-groups' ) . '  <p>
        <a href="' . get_bloginfo( 'wpurl' ) . '/wp-content/uploads/tag_groups_settings-' . $this->hash . '.json" target="_blank">tag_groups_settings-' . $this->hash . '.json</a>
        </p>' . '  <p>
        <a href="' . get_bloginfo( 'wpurl' ) . '/wp-content/uploads/tag_groups_terms-' . $this->hash . '.json" target="_blank">tag_groups_terms-' . $this->hash . '.json</a>
        </p>' );
            } else {
                TagGroups_Error::log( '[Tag Groups] Error writing files' );
                TagGroups_Admin_Notice::add( 'error', __( 'Writing of the exported settings failed.', 'tag-groups' ) );
            }
        
        }
    
    }
}