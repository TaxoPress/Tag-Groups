<?php
/**
 *
 * Plugin Name: Tag Groups
 * Plugin URI: https://wordpress.org/plugins/tag-groups/
 * Description: Tag Groups allows you to organize your WordPress taxonomy terms and show them in clouds, tabs, accordions, tables, lists and much more.
 * Author: TaxoPress
 * Author URI: https://taxopress.com
 * Version: 2.1.1
 * License: GNU GENERAL PUBLIC LICENSE, Version 3
 * Text Domain: tag-groups
 * Domain Path: /languages
 */
// keep the following line for automatic processing
// define( "CM_TGP_KERNL_UUID", '' );
// Don't call this file directly
if ( !defined( 'ABSPATH' ) ) {
    die;
}

if ( !defined( 'TAG_GROUPS_PLUGIN_IS_FREE' ) ) {

    if ( plugin_basename( __FILE__ ) == 'tag-groups/tag-groups.php' ) {
        define( 'TAG_GROUPS_PLUGIN_IS_FREE', true );
    } else {
        // Don't define the constant! If the premium plugin runs earlier, the free plugin still needs to define it.
    }

}
if ( !defined( 'TAG_GROUPS_PLUGIN_IS_KERNL' ) ) {

    if ( defined( 'CM_TGP_KERNL_UUID' ) && CM_TGP_KERNL_UUID != '' || defined( 'CM_TGP_BETA_PLUGIN_UUID' ) && CM_TGP_BETA_PLUGIN_UUID != '' ) {
        define( 'TAG_GROUPS_PLUGIN_IS_KERNL', true );
    } else {
        define( 'TAG_GROUPS_PLUGIN_IS_KERNL', false );
    }

}

if ( !defined( 'TAG_GROUPS_PLUGIN_BASENAME' ) ) {

    define ('TAG_GROUPS_FILE', __FILE__);
    
    /**
     * The plugin's relative path (starting below the plugin directory), including the name of this file.
     */
    define( "TAG_GROUPS_PLUGIN_BASENAME", plugin_basename( __FILE__ ) );
    /**
     * The required minimum version of WordPress.
     */
    define( "TAG_GROUPS_MINIMUM_VERSION_WP", "4.9" );
    /**
     * Comma-separated list of default themes that come bundled with this plugin.
     */
    define( "TAG_GROUPS_BUILT_IN_THEMES", "delta,base,ui-gray,ui-lightness,ui-darkness,blitzer,aristo" );
    /**
     * The theme that is selected by default. Must be among TAG_GROUPS_BUILT_IN_THEMES.
     */
    define( "TAG_GROUPS_STANDARD_THEME", "delta" );
    /**
     * The default number of groups on one page on the edit group screen.
     */
    define( "TAG_GROUPS_ITEMS_PER_PAGE", 20 );
    /**
     * This plugin's last piece of the path, i.e. basically the plugin's name
     */
    define( "TAG_GROUPS_PLUGIN_RELATIVE_PATH", basename( dirname( __FILE__ ) ) );
    /**
     * This plugin's absolute path on this server - starting from root.
     */
    define( "TAG_GROUPS_PLUGIN_ABSOLUTE_PATH", dirname( __FILE__ ) );
}

$autoloadPath = TAG_GROUPS_PLUGIN_ABSOLUTE_PATH . '/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

/**
 * Make scope of $tag_groups_loader global for wp-cli
 */
global  $tag_groups_loader ;
require_once dirname( __FILE__ ) . '/include/class.loader.php';
$tag_groups_loader = new TagGroups_Loader( TAG_GROUPS_PLUGIN_ABSOLUTE_PATH );
$tag_groups_loader->require_classes();

if ( !function_exists( 'tag_groups_init' ) ) {
    /**
     * Do all initial stuff: register hooks, check dependencies
     *
     *
     * @param  void
     * @return void
     */
    function tag_groups_init()
    {
        global $tag_groups_loader ;
        if ( plugin_basename( __FILE__ ) != 'tag-groups/tag-groups.php' ) {
            /**
             *  TGP-Codester or TGP-Freemius
             */

            if ( defined( 'TAG_GROUPS_PLUGIN_IS_FREE' ) && TAG_GROUPS_PLUGIN_IS_FREE ) {
                /**
                 * The free version is also active.
                 */
                /**
                 * Make sure we don't delete data by removing the base plugin by returning data removal to opt-in:
                 * Set the option to OFF and keep, because removing the plugin might only happen later.
                 */
                update_option( 'tag_group_reset_when_uninstall', 0 );
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
                deactivate_plugins( 'tag-groups/tag-groups.php', true );
                // add the hook directly
                add_action( 'admin_notices', function () {
                    echo  '<div class="notice notice-info is-dismissible"><p>' . __( 'The free Tag Groups plugin cannot be active together with Tag Groups Pro.', 'tag-groups' ) . ' <a href="https://taxopress.com/docs/tag-groups/" target="_blank" style="text-decoration: none;" title="' . __( 'more information', 'tag-groups' ) . '"><span class="dashicons dashicons-editor-help"></span></a></p></div><div clear="all" /></div>' ;
                } );
                /**
                 * Remove the misleading "Plugin activated" messaage
                 */
                unset( $_GET['activate'] );
            }

        }
        // URL must be defined after WP has finished loading its settings

        if ( !defined( 'TAG_GROUPS_PLUGIN_URL' ) ) {
            define( "TAG_GROUPS_PLUGIN_URL", plugins_url( '', __FILE__ ) );
            // start all initializations, registration of hooks, housekeeping, menus, ...
            $tag_groups_loader->set_version();
            $tag_groups_loader->check_preconditions();
            $tag_groups_loader->provide_globals();
            $tag_groups_loader->add_hooks();
            $tag_groups_loader->register_shortcodes_and_blocks();
            $tag_groups_loader->register_REST_API();
            $tag_groups_loader->register_CRON();

            if (is_admin()) {
                require_once(TAG_GROUPS_PLUGIN_ABSOLUTE_PATH . '/includes-core/TagGroupsCoreAdmin.php');
                new \TaxoPress\TagGroups\TagGroupsCoreAdmin(); 
            }
        }

    }

    add_action( 'plugins_loaded', 'tag_groups_init' );
    register_activation_hook( __FILE__, array( 'TagGroups_Activation_Deactivation', 'on_activation' ) );
}

/**
 * aliases for common functions, for backwards compatibility
 */
require_once 'aliases.php';
/**
 * guess what - the end
 */
