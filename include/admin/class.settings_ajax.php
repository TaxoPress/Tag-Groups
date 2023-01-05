<?php

/**
* @package     Tag Groups
* @author      Christoph Amthor
* @copyright   2018 Christoph Amthor (@ Chatty Mango, chattymango.com)
* @license     GPL-3.0+
*/
if ( !class_exists( 'TagGroups_Settings_Ajax' ) ) {
    /**
     *
     */
    class TagGroups_Settings_Ajax
    {
        /**
         * Runs selected routines for benchmarking
         *
         * @param void
         * @return string HTML
         * @since 1.23.0
         */
        public static function ajax_benchmark()
        {
            if ( !isset( $_POST['task'] ) ) {
                return;
            }
            global  $tag_groups_premium_fs_sdk ;
            $benchmark = array();
            switch ( $_POST['task'] ) {
                case "cache":
                    break;
            }
            echo  json_encode( $benchmark ) ;
            wp_die();
        }
    
    }
}