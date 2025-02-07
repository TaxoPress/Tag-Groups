<?php
/**
* Blocks Initializer
*
* Enqueue CSS/JS of all the blocks.
*
* @since 0.38
* @package Tag Groups
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
* Enqueue Gutenberg block assets for both frontend + backend.
*
* `wp-blocks`: includes block type registration and related functions.
*
* @since 1.0.0
*/

// We don't have any public Gutenberg styles
// function chatty_mango_tag_groups_block_assets() {
// 	// Styles.
// 	wp_enqueue_style(
// 		'chatty-mango_tag-groups-style-css', // Handle.
// 		plugins_url( 'dist/blocks.style.build.css', dirname( __FILE__ ) ), // Block style CSS.
// 		array( 'wp-blocks' ) // Dependency to include the CSS after it.
// 		// filemtime( plugin_dir_path( __FILE__ ) . 'editor.css' ) // Version: filemtime — Gets file modification time.
// 	);
// } // End function chatty_mango_tag_groups_block_assets().

// Hook: Frontend assets.
// add_action( 'enqueue_block_assets', 'chatty_mango_tag_groups_block_assets' );

/**
* Enqueue Gutenberg block assets for backend editor.
*
* `wp-blocks`: includes block type registration and related functions.
* `wp-element`: includes the WordPress Element abstraction for describing the structure of your blocks.
* `wp-i18n`: To internationalize the block's text.
*
* @since 1.0.0
*/
function chatty_mango_tag_groups_editor_assets() {

	$screen = get_current_screen();

	if ( is_object( $screen ) && property_exists( $screen, 'base' ) && 'post' != $screen->base ) {

		$server_side_render = false;

	} else {

		$server_side_render = !!TagGroups_Options::get_option( 'tag_group_server_side_render', 1 );

	}

	// make some data available
	$args = array(
		'siteUrl' 			=> get_option( 'siteurl' ),
		'siteLang'			=> '',	// for future use
		'pluginUrl'			=> TAG_GROUPS_PLUGIN_URL,
		'hasPremium'		=> TagGroups_Utilities::is_premium_plan(),
		'serverSideRender'	=> $server_side_render,
		'collapsible'		=> TagGroups_Options::get_option( 'tag_group_collapsible', 0 ),
		'mouseover'			=> TagGroups_Options::get_option( 'tag_group_mouseover', 0 ),
		'gutenbergSettings'	=> admin_url( 'admin.php?page=tag-groups-settings-back-end&active-tab=gutenberg' )
	);

	// Scripts.
	if ( ! empty( $screen->base ) && 'widgets' == $screen->base ) {

		// not yet implemented
		// wp_enqueue_script(
		// 	'chatty-mango_tag-groups-block-js', // Handle.
		// 	plugins_url( 'build/index.js', dirname( __FILE__ ) ),
		// 	array( 'lodash', 'react', 'react-dom', 'wp-api-fetch', 'wp-components', 'wp-element', 'wp-polyfill', 'wp-url', 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-edit-widgets' )
		// );

	} else {

		wp_enqueue_script(
			'chatty-mango_tag-groups-block-js', // Handle.
			plugins_url( 'build/index.js', dirname( __FILE__ ) ),
			array( 'lodash', 'react', 'react-dom', 'wp-api-fetch', 'wp-components', 'wp-element', 'wp-polyfill', 'wp-url', 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor' )
		);

	}

	wp_localize_script( 'chatty-mango_tag-groups-block-js', 'ChattyMangoTagGroupsGlobal', $args );

	// Styles.
	wp_enqueue_style(
		'chatty-mango_tag-groups-block-editor-css', // Handle.
		plugins_url( 'build/index.css', dirname( __FILE__ ) ), // Block editor CSS.
		array( 'wp-edit-blocks' )
		// , $dependencies['version']
	);

	if ( function_exists( 'wp_get_jed_locale_data' ) ) {
		$locale_data = wp_get_jed_locale_data( 'tag-groups' );
	} elseif ( function_exists( 'gutenberg_get_jed_locale_data' ) ) {
		$locale_data = gutenberg_get_jed_locale_data( 'tag-groups' );
	} else {
		$locale_data = []; // Ensure it doesn't break if neither function exists
	}
	
	wp_add_inline_script(
		'wp-i18n',
		'wp.i18n.setLocaleData( ' . json_encode( $locale_data ) . ' );'
	);

} // End function chatty_mango_tag_groups_editor_assets().

// Hook: Editor assets.
add_action( 'enqueue_block_editor_assets', 'chatty_mango_tag_groups_editor_assets' );
