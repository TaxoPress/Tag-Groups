<?php
/**
 * @package     Tag Groups
 *
 * @author      Christoph Amthor
 * @copyright   2018 Christoph Amthor (@ Chatty Mango, chattymango.com)
 * @license     GPL-3.0+
 */

if ( ! class_exists( 'TagGroups_Utilities' ) ) {

    /**
     *
     */
    class TagGroups_Utilities {

        /**
         * Returns the first element of an array without changing the original array
         *
         * @param  array   $array
         * @return mixed
         */
        public static function get_first_element( $array = array() ) {

            if ( ! is_array( $array ) ) {

                TagGroups_Error::log('[Tag Groups] Parameter supplied to get_first_element() must be an array.');

                return;

            }

            return reset( $array );

        }


        /**
         * Turns a string into a valid JS function name, preserving as much as possible uniqueness
         *
         * @since 1.26.1
         *
         * @param  string   $raw
         * @return string
         */
        static function create_js_fn_name( $raw ) {

            return str_replace( '-', '', sanitize_html_class( $raw ) );

        }


        /**
         * Execute wp_die() or die(), depending whether we are running tests
         * That way we prevent that other plugins add own output after AJAX responses
         *
         * @return void
         */
        static function die() {

            if ( defined( 'CM_UNIT_TESTING' ) ) {
      
                wp_die();
            
            } else {
    
                die();
    
            }

        }


    } // class

}
