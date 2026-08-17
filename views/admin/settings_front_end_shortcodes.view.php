<?php
$shortcode_features = array(
    array(
        'title'         => __('Tabbed Tag Cloud', 'tag-groups'),
        'description'   => __('Display the tags in a tabbed tag cloud.', 'tag-groups'),
        'requires_groups' => true,
        'shortcode'     => '[tag_groups_cloud]',
        'documentation' => 'https://taxopress.com/docs/tabbed-tag-cloud-shortcode-parameters/',
    ),
    array(
        'title'         => __('Accordion', 'tag-groups'),
        'description'   => __('Display the tags in an accordion.', 'tag-groups'),
        'requires_groups' => true,
        'shortcode'     => '[tag_groups_accordion]',
        'documentation' => 'https://taxopress.com/docs/accordion-tag-cloud-shortcode-parameters/',
    ),
    array(
        'title'         => __('Tag List', 'tag-groups'),
        'description'   => __('Display the tags in lists under tag groups.', 'tag-groups'),
        'requires_groups' => true,
        'shortcode'     => '[tag_groups_tag_list]',
        'documentation' => 'https://taxopress.com/docs/tag-list-shortcode-parameters/',
    ),
    array(
        'title'         => __('Alphabetical Tag Cloud', 'tag-groups'),
        'description'   => __('Display the tags in tabbed tag cloud with first letters as tabs.', 'tag-groups'),
        'requires_groups' => false,
        'shortcode'     => '[tag_groups_alphabet_tabs]',
        'documentation' => 'https://taxopress.com/docs/alphabetical-tag-cloud-shortcode-parameters/',
    ),
    array(
        'title'         => __('Alphabetical Tag Index', 'tag-groups'),
        'description'   => __('Display the tags in a list with first letters as heading.', 'tag-groups'),
        'requires_groups' => false,
        'shortcode'     => '[tag_groups_alphabetical_index]',
        'documentation' => 'https://taxopress.com/docs/alphabetical-tag-cloud-shortcode-parameters/',
    ),
);

if (!TagGroups_Utilities::is_premium_plan()) {
    $shortcode_features[] = array(
        'title'         => __('Tag Cloud', 'tag-groups'),
        'description'   => __('Display the tags from multiple groups in one tag cloud.', 'tag-groups'),
        'requires_groups' => false,
        'shortcode'     => '[tag_groups_simple_cloud]',
        'documentation' => 'https://taxopress.com/docs/combined-tag-cloud-parameters/',
    );
    $shortcode_features[] = array(
        'title'         => __('Table Tag Cloud', 'tag-groups'),
        'description'   => __('Display the tags in a table, sorted into columns under the groups.', 'tag-groups'),
        'requires_groups' => true,
        'shortcode'     => '[tag_groups_table]',
        'documentation' => 'https://taxopress.com/docs/table-tag-cloud-parameters/',
    );
    $shortcode_features[] = array(
        'title'         => __('Shuffle Box', 'tag-groups'),
        'description'   => __('Display a tag cloud that visitors can filter by group and by name.', 'tag-groups'),
        'requires_groups' => false,
        'shortcode'     => '[tag_groups_shuffle_box]',
        'documentation' => 'https://taxopress.com/docs/shuffle-box-parameters/',
    );
    $shortcode_features[] = array(
        'title'         => __('Post List', 'tag-groups'),
        'description'   => __('Display a static list of posts that matches the selected tag groups.', 'tag-groups'),
        'requires_groups' => false,
        'shortcode'     => '[tag_groups_post_list]',
        'documentation' => 'https://taxopress.com/docs/post-list-parameters/',
    );
    $toggle_post_filter_features = array(
        array(
        'title'         => __('Toggle Post Filter: Menu', 'tag-groups'),
        'description'   => __('Display a horizontal tag menu for building the post filter.', 'tag-groups'),
        'requires_groups' => false,
        'shortcode'     => '[tag_groups_tpf_menu layout=wide]',
        'documentation' => 'https://taxopress.com/docs/toggle-post-filter-parameters/',
        ),
        array(
        'title'         => __('Toggle Post Filter: Vertical Menu', 'tag-groups'),
        'description'   => __('Display a vertical checkbox-style menu for building the post filter.', 'tag-groups'),
        'requires_groups' => false,
        'shortcode'     => '[tag_groups_tpf_menu layout=plain]',
        'documentation' => 'https://taxopress.com/docs/toggle-post-filter-parameters/',
        ),
        array(
        'title'         => __('Toggle Post Filter: Buttons', 'tag-groups'),
        'description'   => __('Display the filter controls as horizontal buttons.', 'tag-groups'),
        'requires_groups' => false,
        'shortcode'     => '[tag_groups_tpf_menu layout=wide_button]',
        'documentation' => 'https://taxopress.com/docs/toggle-post-filter-parameters/',
        ),
        array(
        'title'         => __('Toggle Post Filter: Vertical Buttons', 'tag-groups'),
        'description'   => __('Display the filter controls as vertical buttons.', 'tag-groups'),
        'requires_groups' => false,
        'shortcode'     => '[tag_groups_tpf_menu layout=button]',
        'documentation' => 'https://taxopress.com/docs/toggle-post-filter-parameters/',
        ),
        array(
        'title'         => __('Toggle Post Filter: Vertical Toggles', 'tag-groups'),
        'description'   => __('Display the filter controls as vertical toggle switches.', 'tag-groups'),
        'requires_groups' => false,
        'shortcode'     => '[tag_groups_tpf_menu layout=classic]',
        'documentation' => 'https://taxopress.com/docs/toggle-post-filter-parameters/',
        ),
        array(
        'title'         => __('Toggle Post Filter: Slider Menu', 'tag-groups'),
        'description'   => __('Display the filter controls in a slider menu. Use slider_right for a right-side slider.', 'tag-groups'),
        'requires_groups' => false,
        'shortcode'     => '[tag_groups_tpf_menu layout=slider_left]',
        'documentation' => 'https://taxopress.com/docs/toggle-post-filter-parameters/',
        ),
        array(
        'title'         => __('Toggle Post Filter: Slider With Buttons', 'tag-groups'),
        'description'   => __('Display the slider menu with tag-style buttons. Use slider_right_tags for a right-side slider.', 'tag-groups'),
        'requires_groups' => false,
        'shortcode'     => '[tag_groups_tpf_menu layout=slider_left_tags]',
        'documentation' => 'https://taxopress.com/docs/toggle-post-filter-parameters/',
        ),
        array(
        'title'         => __('Toggle Post Filter - Posts', 'tag-groups'),
        'description'   => __('Display the posts that match the selected tags in the filter.', 'tag-groups'),
        'requires_groups' => false,
        'shortcode'     => '[tag_groups_tpf_body]',
        'documentation' => 'https://taxopress.com/docs/toggle-post-filter-parameters/',
        ),
        array(
        'title'         => __('Toggle Post Filter - Message Field', 'tag-groups'),
        'description'   => __('Display the result count and filter messages for the post filter.', 'tag-groups'),
        'requires_groups' => false,
        'shortcode'     => '[tag_groups_tpf_messages]',
        'documentation' => 'https://taxopress.com/docs/toggle-post-filter-parameters/',
        ),
        array(
        'title'         => __('Toggle Post Filter - Reset Button', 'tag-groups'),
        'description'   => __('Display a button that clears the selected tags in the filter.', 'tag-groups'),
        'requires_groups' => false,
        'shortcode'     => '[tag_groups_tpf_reset]',
        'documentation' => 'https://taxopress.com/docs/toggle-post-filter-parameters/',
        ),
        array(
        'title'         => __('Toggle Post Filter - Text Search', 'tag-groups'),
        'description'   => __('Display a separate text search field for the toggle post filter.', 'tag-groups'),
        'requires_groups' => false,
        'shortcode'     => '[tag_groups_tpf_text_search]',
        'documentation' => 'https://taxopress.com/docs/toggle-post-filter-parameters/',
        ),
        array(
        'title'         => __('Toggle Post Filter - Slider Button', 'tag-groups'),
        'description'   => __('Display the button that opens slider-based filter layouts.', 'tag-groups'),
        'requires_groups' => false,
        'shortcode'     => '[tag_groups_tpf_slider_button]',
        'documentation' => 'https://taxopress.com/docs/toggle-post-filter-parameters/',
        ),
        array(
        'title'         => __('Toggle Post Filter - Order Menu', 'tag-groups'),
        'description'   => __('Display menus that let visitors reorder the filtered posts.', 'tag-groups'),
        'requires_groups' => false,
        'shortcode'     => '[tag_groups_tpf_order_menu]',
        'documentation' => 'https://taxopress.com/docs/toggle-post-filter-parameters/',
        ),
    );
} else {
    $toggle_post_filter_features = array();
}

$render_shortcode_table = function ($features) {
    if (empty($features)) {
        return;
    }
    ?>
    <table class="widefat fixed striped">
      <thead>
        <tr>
          <th style="width: 18%;"><?php _e('Title', 'tag-groups') ?></th>
          <th style="width: 31%;"><?php _e('Description', 'tag-groups') ?></th>
          <th style="width: 7%; text-align: center;"><?php _e('Groups', 'tag-groups') ?></th>
          <th style="width: 30%;"><?php _e('Shortcode', 'tag-groups') ?></th>
          <th style="width: 14%;"><?php _e('Find Out More', 'tag-groups') ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($features as $shortcode_feature) : ?>
          <tr>
            <td><?php echo esc_html($shortcode_feature['title']) ?></td>
            <td><?php echo esc_html($shortcode_feature['description']) ?></td>
            <td style="text-align: center;">
              <?php if (!empty($shortcode_feature['requires_groups'])) : ?>
                <span class="dashicons dashicons-yes-alt" aria-hidden="true"></span>
                <span class="screen-reader-text"><?php _e('Requires groups', 'tag-groups') ?></span>
              <?php endif; ?>
            </td>
            <td>
              <input
                type="text"
                class="tg-shortcode-input"
                readonly="readonly"
                value="<?php echo esc_attr($shortcode_feature['shortcode']) ?>"
                onclick="this.select();"
                onfocus="this.select();"
              />
            </td>
            <td>
              <a href="<?php echo esc_url($shortcode_feature['documentation']) ?>" target="_blank" rel="noopener noreferrer">
                <?php _e('Documentation', 'tag-groups') ?>
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php
};
?>
<div class="tg_settings_tabs_content box-tg-accordion">
  <style>
    .tg-shortcode-input {
      width: 100%;
      min-height: 38px;
      padding: 8px 12px;
      border: 1px solid #8c8f94;
      border-radius: 0;
      background: #f6f7f7;
      box-shadow: none;
      font-family: Consolas, Monaco, monospace;
      font-size: 13px;
      line-height: 1.4;
    }

    .tg-shortcode-input[readonly] {
      color: #1d2327;
      cursor: text;
    }
  </style>

  <p>
    <?php _e('All Tag Groups features are available as Gutenberg blocks. You can also use shortcodes.', 'tag-groups') ?>
  </p>

  <p>&nbsp;</p>
  <h3><?php _e('Shortcodes', 'tag-groups') ?></h3>
  <?php $render_shortcode_table($shortcode_features); ?>

  <?php if (!empty($toggle_post_filter_features)) : ?>
    <p>&nbsp;</p>
    <h3><?php _e('Toggle Post Filter', 'tag-groups') ?></h3>
    <?php $render_shortcode_table($toggle_post_filter_features); ?>
  <?php endif; ?>

  <?php if (!empty($premium_shortcode_info)) : ?>
    <p>&nbsp;</p>
    <div class="tg_admin_accordion">
      <?php echo wp_kses_post($premium_shortcode_info) ?>
    </div>
  <?php endif; ?>

  <!-- begin Tag Groups plugin -->
  <script>
    jQuery(function() {
      var icons = {
        header: "dashicons dashicons-arrow-right",
        activeHeader: "dashicons dashicons-arrow-down"
      };
      jQuery(".box-tg-accordion .tg_admin_accordion").accordion({
        icons: icons,
        collapsible: true,
        active: false,
        heightStyle: "content"
      });
    });
  </script>
  <!-- end Tag Groups plugin -->

</div>
