<?php
/**
 * @package     Tag Groups
 *
 * @author      Christoph Amthor
 * @copyright   2018 Christoph Amthor (@ Chatty Mango, chattymango.com)
 * @license     GPL-3.0+
 */

if ( ! class_exists( 'TagGroups_Freemius' ) ) {

  /**
   *
   */
  class TagGroups_Freemius {

    /**
     * Change the time until the first trial encouragement appears
     *
     * @since 1.19.1
     *
     * @param  int   $sec Default is 24 hours.
     * @return int
     */
    public static function change_time_show_first_trial( $sec ) {

      return 3 * DAY_IN_SECONDS;

    }

    /**
     * Change the time between trial encouragements
     *
     *
     * @since 1.19.1
     *
     * @param  int   $sec Default is 30 days.
     * @return int
     */
    public static function change_time_reshow_trial( $sec ) {

      // These messages appear only on pages where users go to set up tag groups, not for daily work, that means only a few times per year -> can reshow after 30 days, it effectively will be less

      return 30 * DAY_IN_SECONDS;

    }

    /**
     * Show Freemius admin notice of trial promotion only in Tag Groups own settings or Tag Groups Admin page
     * ("page" parameter starts with tag-groups)
     *
     * @since 1.19.2
     *
     * @param  mixed     $show
     * @param  array     $msg
     * @return boolean
     */
    public static function change_show_admin_notice( $show, $msg ) {

      if (
        'trial_promotion' == $msg['id']
        && ( empty( $_GET['page'] ) || strpos( $_GET['page'], 'tag-groups' ) !== 0 )
      ) {

        // Don't show the trial promotional admin notice.
        return false;

      }

      return true;

    }

    /**
     * Remove the feedback form 
     *
     * @return void
     */
    public static function remove_deactivation_feedback_form() {

      global $tag_groups_premium_fs_sdk;

      $tag_groups_premium_fs_sdk->add_filter(
        'show_deactivation_feedback_form', '__return_false'
      );
    }

  }

}
