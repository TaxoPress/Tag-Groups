<?php
/**
* @package     Tag Groups
* @author      Christoph Amthor
* @copyright   2019 Christoph Amthor (@ Chatty Mango, chattymango.com)
* @license     GPL-3.0+
*/

if ( ! class_exists('TagGroups_Activation_Deactivation') ) {

  /**
  *
  */
  class TagGroups_Activation_Deactivation {


    /**
    *   Initializes values and prevents errors that stem from wrong values, e.g. based on earlier bugs.
    *   Runs when plugin is activated.
    *
    * @param void
    * @return void
    */
    static function on_activation() {

      if ( ! current_user_can( 'activate_plugins' ) ) {

        TagGroups_Error::log( '[Tag Groups] Insufficient permissions to activate plugin.' );

        return;

      }

      if ( TAG_GROUPS_PLUGIN_IS_KERNL ) {

        register_uninstall_hook( TAG_GROUPS_PLUGIN_ABSOLUTE_PATH, array( 'TagGroups_Activation_Deactivation', 'on_uninstall' ) );

      }


      $tag_groups_loader = new TagGroups_Loader( __FILE__ );


      $tag_groups_loader->set_version();

      $tag_groups_loader->register_CRON();

      if ( $tag_groups_loader->is_version_update() ) {

        TagGroups_Error::verbose_log( '[Tag Groups] Version update from %s to %s detected', $tag_groups_loader->get_saved_version(), $tag_groups_loader->get_version() );

        $update_scripts = new TagGroups_Update( $tag_groups_loader->get_saved_version(), $tag_groups_loader->get_version() );
        
        $update_scripts->update_version_number(); // update early so that parallel thread will not run this again (anyway cron jobs avoid overlaps)

        $update_scripts->run_specific_scripts();

        $update_scripts->run_general_scripts();

      }

    }


    /**
    * This script is executed when the (inactive) plugin is deleted through the admin backend.
    *
    *It removes the plugin settings from the option table and all tag groups. It does not change the term_group field of the taxonomies.
    *
    * @param void
    * @return void
    */
    public static function on_uninstall() {

      if ( ! current_user_can( 'install_plugins' ) ) {

        TagGroups_Error::log( '[Tag Groups] Insufficient permissions to uninstall plugin.' );

        return;

      }

      // Referrer is wrong when triggered via Freemius
      // check_admin_referer( 'bulk-plugins' );

      /**
      * Delete options only if requested
      */
      // Note: WP_UNINSTALL_PLUGIN is not defined when using the deinstallation hook

      TagGroups_Error::log( '[Tag Groups] Starting uninstall routine.' );

      if ( ! file_exists( dirname( __FILE__ ) . '/class.options.php' ) ) {

        TagGroups_Error::log( '[Tag Groups] Options class not available.' );

        return;

      }

      require_once dirname( __FILE__ ) . '/class.options.php';


      if ( ! file_exists( dirname( __FILE__ ) . '/cache/class.object_cache.php' ) ) {

        TagGroups_Error::log( '[Tag Groups] Cache class not available.' );

        return;

      }

      require_once dirname( __FILE__ ) . '/cache/class.object_cache.php';

      /**
      * Purge cache
      */
      if ( class_exists( 'TagGroups_Object_Cache' ) ) {
        $cache = new TagGroups_Object_Cache();
        $cache
        ->type( TagGroups_Options::get_option( 'tag_group_object_cache', TagGroups_Object_Cache::WP_TRANSIENTS ) )
        ->path( WP_CONTENT_DIR . '/chatty-mango/cache/' )
        ->purge_all();
      }

      /**
      * Erase /chatty-mango/cache/ directory
      */
      if ( file_exists( WP_CONTENT_DIR . '/chatty-mango/cache' ) && is_dir( WP_CONTENT_DIR . '/chatty-mango/cache' ) ) {
        /**
        * Attempt to empty and remove chatty-mango/cache directory
        * (Different from purging cache because the previous one can be database.)
        */
        foreach ( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( WP_CONTENT_DIR . '/chatty-mango/cache/' ) ) as $file) {

          // filter out "." and ".."
          if ( $file->isDir() ) continue;

          @unlink( $file->getPathname() );

        }

        @rmdir( WP_CONTENT_DIR . '/chatty-mango/cache' );

      }


      /**
      * Remove transients
      *
      * Do this before deleting options, because we need to know the array in 'tag_group_used_transient_names'
      * Don't call the method clear_term_cache since we don't know if it is still available.
      */
      TagGroups_Error::log( '[Tag Groups] Removing transients.' );

      TagGroups_Transients::delete_all_transients();


      /**
       * Maybe delete options
       */
      $tag_group_reset_when_uninstall = TagGroups_Options::get_option( 'tag_group_reset_when_uninstall', 0 );

      $option_count = 0;

      if ( $tag_group_reset_when_uninstall ) {

        $option_count = TagGroups_Options::delete_all();

        TagGroups_Error::log( '[Tag Groups] %d options deleted.', $option_count );

      }


      /**
      * Remove regular crons
      */
      wp_clear_scheduled_hook( 'tag_groups_check_tag_migration' );

      wp_clear_scheduled_hook( 'tag_groups_purge_expired_transients' );

      TagGroups_Error::log( '[Tag Groups] Finished uninstall routine.' );

    }

  }

}
