<div class="tg_settings_tabs_content">

  <p>&nbsp;</p>
  <form method="POST" action="">
    <input type="hidden" name="task-set-name" value="Migration" />
    <input type="hidden" name="process-tasks" value="<?php echo esc_attr($tasks_migration) ?>" />
    <?php wp_nonce_field('tag-groups-first-aid-nonce', 'nonce') ?>
    <p><?php _e( 'Migrate tags to the new format of Tag Groups.', 'tag-groups' ) ?><span class="dashicons dashicons-editor-help chatty-mango-help-icon" title="<?php _e( 'The plugin automatically migrates tags to the new format and tries to keep track of changes. There may, however, be cases where other components made unnoticed changes. You might also need to manually migrate tags after you enabled a taxonomy that already contained untracked tags.', 'tag-groups' ) ?>"></span></p>
    <input class="button-primary" type="submit" name="update" value="<?php _e( 'Migrate', 'tag-groups' ) ?>" id="submitbutton" />
  </form>

  <p>&nbsp;</p>
  <form method="POST" action="">
    <input type="hidden" name="task-set-name" value="Maintenance" />
    <input type="hidden" name="process-tasks" value="<?php echo esc_attr($tasks_maintenance) ?>" />
    <?php wp_nonce_field('tag-groups-first-aid-nonce', 'nonce') ?>
    <p><?php _e( 'Running the maintenance can solve issues that you encounter during filtering by tag groups.', 'tag-groups' ) ?></p>
    <input class="button-primary" type="submit" name="maintenance" value="<?php _e( 'Maintenance', 'tag-groups' ) ?>" id="submitbutton" />
  </form>

  <?php if ( $tag_group_show_filter_tags ): ?>
    <p>&nbsp;</p>
    <form method="POST" action="<?php echo esc_url( $_SERVER['REQUEST_URI'] ) ?>">
      <p><?php _e( "Reset the filter on the tags page to show all tags.", 'tag-groups' ) ?><span class="dashicons dashicons-editor-help chatty-mango-help-icon" title="<?php _e( 'Use it if a JavaScript error disables the filter menu. Note that it is still recommended to find out the cause of the error.', 'tag-groups' ) ?>"></span></p>
      <?php echo wp_nonce_field( 'tag-groups-reset-tag-filter', 'tag-groups-reset-tag-filter-nonce', true, false ) ?>
      <input type="hidden" name="tg_action" value="reset-tag-filter">
      <input class="button-primary" type="submit" name="reset" value="<?php _e( 'Reset Tag Filter', 'tag-groups' ) ?>" id="submitbutton" />
    </form>
  <?php endif; ?>
</div>
