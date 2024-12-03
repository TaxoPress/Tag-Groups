<div style="margin: 50px 0 0;">
  <p><?php _e( 'Choose the taxonomies for which you want to use tag groups.', 'tag-groups' ) ?> <?php _e( "In most cases the default taxonomy <b>Tags (post_tag)</b> will do the job.", 'tag-groups' ) ?><span class="dashicons dashicons-editor-help chatty-mango-help-icon" data-topic="taxonomies" title="<?php _e('Click for more information', 'tag-groups') ?>"></span></p>

  <div class="chatty-mango-help-container chatty-mango-help-container-taxonomies" style="display:none;">
    <p><?php _e( "The default texonomy is <b>Tags (post_tag)</b>. Please note that the tag clouds might not work with all taxonomies and that some taxonomies listed here may not be accessible in the admin backend. If you don't understand what is going on here, just leave the default.", 'tag-groups' ) ?></p>
  </div>
  <div class="chatty-mango-settings-container">
    <form method="POST" action="<?php echo $setup_wizard_next_link ?>">
      <ul>
        <p>&nbsp;</p>
        <?php foreach ( $public_taxonomies as $taxonomy ) : ?>
          <?php if ( is_taxonomy_hierarchical( $taxonomy ) ) { continue; } ?>
          <li class="tg_advanced_options_items">
            <input type="checkbox" name="taxonomies[]" autocomplete="off" id="<?php echo $taxonomy ?>" value="<?php echo $taxonomy ?>"<?php if ( in_array( $taxonomy, $enabled_taxonomies ) ) : ?>
              checked
            <?php endif; ?>
            />&nbsp;<span class="dashicons dashicons-index-card tg_no_underline<?php if ( strpos( $taxonomy, '_tag' ) === false ) :?> tg_faded<?php endif; ?>"></span>
              <label for="<?php echo $taxonomy ?>" class="tg_unhide_trigger" title="<?php _e( 'post type', 'tag-groups') ?>: <?php echo implode( ', ', TagGroups_Taxonomy::post_types_from_taxonomies( $taxonomy ) ) ?>">
              <?php echo TagGroups_Taxonomy::get_name_from_slug( $taxonomy ) ?> (<?php echo $taxonomy ?>)
            </label>
          </li>
        <?php endforeach; ?>
      </ul>

      <p>&nbsp;</p>
      <h4><span class="dashicons dashicons-lightbulb"></span>&nbsp;<?php _e( 'You will find more options in the Tag Groups settings.', 'tag-groups' ) ?></h4>

      <input type="hidden" name="tg_action_wizard" value="taxonomy">
      <input type="submit" value="<?php _e( 'Next Step', 'tag-groups' ) ?>" class="button button-primary tag-groups-wizard-submit">
      <input type="hidden" name="tag-groups-setup-wizard-nonce" id="tag-groups-setup-wizard-nonce" value="<?php echo wp_create_nonce( 'tag-groups-setup-wizard-nonce' ) ?>" />
    </form>
  </div>
</div>

<script>
jQuery(document).ready(function () {
  jQuery(".tg_unhide_trigger").on('mouseover', function () {
    jQuery(this).find("span").show();
  });
  jQuery(".tg_unhide_trigger").on('mouseout', function () {
    jQuery(this).find("span").hide();
  });
});
</script>
