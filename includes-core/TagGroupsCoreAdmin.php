<?php
namespace TaxoPress\TagGroups;

class TagGroupsCoreAdmin {
    function __construct() {

        if ( current_user_can( 'manage_options' ) ) {
        if (is_admin()) {

            require_once TAG_GROUPS_PLUGIN_ABSOLUTE_PATH . '/vendor/publishpress/wordpress-version-notices/includes.php';
            add_filter(
                        \PPVersionNotices\Module\TopNotice\Module::SETTINGS_FILTER,
                        function ($settings) {
                            $settings['taxopress-tag_groups'] = [
                                'message' => __('You\'re using Tag Groups Free. The Pro version has more features and support. %sUpgrade to Pro%s', 'tag-groups'),
                                'link'    => 'https://taxopress.com/tag-groups/',
                                'screens' => [
                                    ['base' => 'toplevel_page_tag-groups-settings', 'id'   => 'toplevel_page_tag-groups-settings'],
                                    ['base' => 'tag-groups_page_tag-groups-settings-taxonomies', 'id'   => 'tag-groups_page_tag-groups-settings-taxonomies'],
                                    ['base' => 'tag-groups_page_tag-groups-settings-front-end',     'id'   => 'tag-groups_page_tag-groups-settings-front-end'],
                                    ['base' => 'tag-groups_page_tag-groups-settings-back-end',       'id'   => 'tag-groups_page_tag-groups-settings-back-end'],
                                    ['base' => 'tag-groups_page_tag-groups-settings-tools',     'id'   => 'tag-groups_page_tag-groups-settings-tools'],
                                    ['base' => 'tag-groups_page_tag-groups-settings-about',   'id'  => 'tag-groups_page_tag-groups-settings-about'],
                                    ['base' => 'posts_page_tag-groups_post','id' => 'posts_page_tag-groups_post'],
                                    ['base' => 'admin_page_tag-groups-settings-first-steps',   'id'  => 'admin_page_tag-groups-settings-first-steps'],
                                    ['base' => 'admin_page_tag-groups-settings-setup-wizard',   'id'  => 'admin_page_tag-groups-settings-setup-wizard'],
                                    ['base' => 'taxopress_page_st_suggestterms','id'  => 'taxopress_page_st_suggestterms'],
                                    ['base' => 'taxopress_page_st_terms',       'id'  => 'taxopress_page_st_terms']
                                ]
                            ];

                            return $settings;
                        }
                    );


        }
                    add_filter(
                        \PPVersionNotices\Module\MenuLink\Module::SETTINGS_FILTER,
                        function ($settings) {
                            $settings['taxopress-tag_groups'] = [
                                'parent' => 'tag-groups-settings',
                                'label'  => __('Upgrade to Pro', 'tag-groups'),
                                'link'   => 'https://taxopress.com/tag-groups/',
                            ];

                            return $settings;
                        });

        }

        add_action('tag_groups_settings_right_sidebar', [$this, 'tag_groups_admin_advertising_sidebar_banner']);
    }


    function tag_groups_admin_advertising_sidebar_banner(){
        ?>

        <div class="tag-groups-advertisement-right-sidebar">
            <div id="postbox-container-1" class="postbox-container">
            <div class="meta-box-sortables">
                <div class="advertisement-box-content postbox">
                    <div class="postbox-header">
                        <h3 class="advertisement-box-header hndle is-non-sortable">
                            <span><?php echo esc_html__('Upgrade to Tag Groups Pro', 'tag-groups'); ?></span>
                        </h3>
                    </div>

                    <div class="inside">
                        <p><?php echo esc_html__('Enhance the power of Tag Groups with the Pro version:', 'tag-groups'); ?>
                        </p>
                        <ul>
                            <li><?php echo esc_html__('21 Shortcodes', 'tag-groups'); ?></li>
                            <li><?php echo esc_html__('18 Gutenberg Blocks', 'tag-groups'); ?></li>
                            <li><?php echo esc_html__('Table Tag Cloud', 'tag-groups'); ?></li>
                            <li><?php echo esc_html__('Simple Tag Cloud', 'tag-groups'); ?></li>
                            <li><?php echo esc_html__('Shuffle Box', 'tag-groups'); ?></li>
                            <li><?php echo esc_html__('Caching', 'tag-groups'); ?></li>
                            <li><?php echo esc_html__('Multiple Groups Per Tag', 'tag-groups'); ?></li>
                            <li><?php echo esc_html__('Tag Cloud Search', 'tag-groups'); ?></li>
                            <li><?php echo esc_html__('Toggle Post Filter', 'tag-groups'); ?></li>
                            <li><?php echo esc_html__('Dynamic Post Filter', 'tag-groups'); ?></li>
                            <li><?php echo esc_html__('Post List', 'tag-groups'); ?></li>
                            <li><?php echo esc_html__('Tag Meta Box', 'tag-groups'); ?></li>
                            <li><?php echo esc_html__('Post Tags in Groups', 'tag-groups'); ?></li>
                            <li><?php echo esc_html__('Permissions', 'tag-groups'); ?></li>
                            <li><?php echo esc_html__('Default Groups', 'tag-groups'); ?></li>
                            <li><?php echo esc_html__('WooCommerce Attributes', 'tag-groups'); ?></li>
                            <li><?php echo esc_html__('Parent Group Level', 'tag-groups'); ?></li>
                        </ul>
                        <div class="upgrade-btn">
                            <a href="https://taxopress.com/tag-groups/" target="__blank"><?php echo esc_html__('Upgrade to Pro', 'tag-groups'); ?></a>
                        </div>
                    </div>
                </div>
                <div class="advertisement-box-content postbox">
                    <div class="postbox-header">
                        <h3 class="advertisement-box-header hndle is-non-sortable">
                            <span><?php echo esc_html__('Need Tag Groups Support?', 'tag-groups'); ?></span>
                        </h3>
                    </div>

                    <div class="inside">
                        <p><?php echo esc_html__('If you need help or have a new feature request, let us know.', 'tag-groups'); ?>
                            <a class="advert-link" href="https://wordpress.org/support/plugin/tag-groups/" target="_blank">
                            <?php echo esc_html__('Request Support', 'tag-groups'); ?>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" class="linkIcon">
                                    <path
                                        d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"
                                    ></path>
                                </svg>
                            </a>
                        </p>
                        <p>
                        <?php echo esc_html__('Detailed documentation is also available on the plugin website.', 'tag-groups'); ?>
                            <a class="advert-link" href="https://taxopress.com/docs/" target="_blank">
                            <?php echo esc_html__('View Knowledge Base', 'tag-groups'); ?>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" class="linkIcon">
                                    <path
                                        d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"
                                    ></path>
                                </svg>
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <?php
    }


}
