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
                                'message' => 'You\'re using Tag Groups Free. The Pro version has more features and support. %sUpgrade to Pro%s',
                                'link'    => 'https://taxopress.com/pro',
                                'screens' => [
                                    ['base' => 'toplevel_page_tag-groups-settings', 'id'   => 'toplevel_page_tag-groups-settings'],
                                    ['base' => 'tag-groups_page_tag-groups-settings-taxonomies', 'id'   => 'tag-groups_page_tag-groups-settings-taxonomies'],
                                    ['base' => 'tag-groups_page_tag-groups-settings-front-end',     'id'   => 'tag-groups_page_tag-groups-settings-front-end'],
                                    ['base' => 'tag-groups_page_tag-groups-settings-back-end',       'id'   => 'tag-groups_page_tag-groups-settings-back-end'],
                                    ['base' => 'tag-groups_page_tag-groups-settings-tools',     'id'   => 'tag-groups_page_tag-groups-settings-tools'],
                                    ['base' => 'tag-groups_page_tag-groups-settings-troubleshooting','id' => 'tag-groups_page_tag-groups-settings-troubleshooting'],
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
                                'label'  => 'Upgrade to Pro',
                                'link'   => 'https://taxopress.com/pro',
                            ];

                            return $settings;
                        });

        }
    }
}
