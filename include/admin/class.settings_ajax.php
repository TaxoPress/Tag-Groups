<?php

/**
* @package     Tag Groups
* @author      Christoph Amthor
* @copyright   2018 Christoph Amthor (@ Chatty Mango, chattymango.com)
* @license     GPL-3.0+
*/

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ValidClassName.NotCamelCaps, PSR1.Methods.CamelCapsMethodName.NotCamelCaps
if (!class_exists('TagGroups_Settings_Ajax')) {
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
            // phpcs:ignore WordPress.Security.NonceVerification.Missing -- AJAX handler
            if (!isset($_POST['task'])) {
                return;
            }
            
            $benchmark = array();
            // phpcs:ignore WordPress.Security.NonceVerification.Missing -- AJAX handler
            switch ($_POST['task']) {
                case "cache":
                    break;
            }
            echo  wp_json_encode($benchmark) ;
            wp_die();
        }
    }
}
