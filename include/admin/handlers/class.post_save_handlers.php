<?php

/**
 * @package     Tag Groups
 *
 * @author      Christoph Amthor
 * @copyright   2018 Christoph Amthor (@ Chatty Mango, chattymango.com)
 * @license     GPL-3.0+
 */

if (! class_exists('TagGroups_Post_Save_Handlers')) {
    class TagGroups_Post_Save_Handlers
    {
        /**
         * Schedules a cron job to execute fix_all_incorrect_post_terms().
         *
         * @return void
         */
        public static function schedule_fix_all_incorrect_post_terms()
        {
            TagGroups_Transients::delete_transient('tag_groups_all_term_ids');
            TagGroups_Cron::schedule_in_secs(20, 'tag_groups_run_fixing_post_meta');
        }

        /**
         * Clear the transient cache used by Post List metadata.
         *
         * @param  int|null $group_id Optional group identifier from hook context.
         * @return void
         */
        public static function clear_tag_groups_post_terms($group_id = null)
        {
            TagGroups_Transients::delete_transient('tag_groups_post_terms');
        }
    }
}
