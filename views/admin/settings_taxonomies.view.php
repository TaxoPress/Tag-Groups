<div class="tg_settings_tabs_content">
  <p><?php _e('Choose the taxonomies for which you want to use tag groups.', 'tag-groups') ?><span class="dashicons dashicons-editor-help chatty-mango-help-icon" data-topic="taxonomies" title="<?php _e('Click for more information', 'tag-groups-premium') ?>"></span></p>

  <div class="chatty-mango-help-container chatty-mango-help-container-taxonomies" style="display:none;">
    <p><?php _e("The default texonomy is <b>Tags (post_tag)</b>. Please note that the tag clouds might not work with all taxonomies and that some taxonomies listed here may not be accessible in the admin backend. If you don't understand what is going on here, just leave the default.", 'tag-groups') ?></p>
    <p><?php _e("<b>Please deselect taxonomies that you don't use. Using several taxonomies for the same post type or hierarchical taxonomies (like categories) is experimental and not supported.</b>", 'tag-groups') ?></p>
    <p><?php _e('To see the post type, hover your mouse over the option.', 'tag-groups') ?></p>
    <p><?php _e('If you use a custom taxonomy, make sure that the attribute "Public" is set to "true" and "Hierarchical" to "false".', 'tag-groups') ?></p>
  </div>
  <div class="chatty-mango-settings-container">
    <form method="POST" action="<?php echo esc_url($_SERVER['REQUEST_URI']) ?>">
      <?php echo wp_nonce_field('tag-groups-taxonomy', 'tag-groups-taxonomy-nonce', true, false) ?>
      <ul>
        <p><input id="tg_advanced_options_checkbox" type="checkbox" value=1 autocomplete="off" />
          <label for="tg_advanced_options_checkbox"><?php _e('Show hierarchical taxonomies', 'tag-groups') ?></label></p>
        <script>
          jQuery(document).ready(function() {
            jQuery("#tg_advanced_options_checkbox").on('change', function() {
              if (jQuery("#tg_advanced_options_checkbox").is(":checked")) {
                jQuery(".tg_advanced_options_items").slideDown();
              } else {
                jQuery(".tg_advanced_options_items").slideUp();
              }
            });
          });
        </script>
        <p>&nbsp;</p>
        <?php foreach ($public_taxonomies as $taxonomy) : ?>
          <li<?php if (is_taxonomy_hierarchical($taxonomy)) : ?> class="tg_advanced_options_items" style="display:none;" <?php endif; ?>>
            <input type="checkbox" name="taxonomies[]" autocomplete="off" id="<?php echo $taxonomy ?>" value="<?php echo $taxonomy ?>" <?php if (in_array($taxonomy, $enabled_taxonomies)) : ?> checked />&nbsp;<a href="<?php echo TagGroups_Taxonomy::get_tag_group_admin_url($taxonomy) ?>" title="<?php _e('go to tag group administration', 'tag-groups') ?>"><span class="dashicons dashicons-index-card tg_no_underline"></span></a>
          <?php else : ?>
            />&nbsp;<span class="dashicons dashicons-index-card tg_no_underline tg_faded"></span>
          <?php endif; ?>
          <label for="<?php echo $taxonomy ?>" class="tg_unhide_trigger" title="<?php _e( 'post type', 'tag-groups') ?>: <?php echo implode( ', ', TagGroups_Taxonomy::post_types_from_taxonomies( $taxonomy ) ) ?>">
              <?php echo TagGroups_Taxonomy::get_name_from_slug( $taxonomy ) ?> (<?php echo $taxonomy ?>)
            </label>
          </li>
        <?php endforeach; ?>
      </ul>
      <script>
        jQuery(document).ready(function() {
          jQuery(".tg_unhide_trigger").on('mouseover', function() {
            jQuery(this).find("span").show();
          });
          jQuery(".tg_unhide_trigger").on('mouseout', function() {
            jQuery(this).find("span").hide();
          });
        });
      </script>
      <input type="hidden" name="tg_action" value="taxonomy">
      <input class="button-primary" type="submit" name="Save" value="<?php _e('Save Taxonomies', 'tag-groups') ?>" id="submitbutton" />
    </form>
  </div>
</div>