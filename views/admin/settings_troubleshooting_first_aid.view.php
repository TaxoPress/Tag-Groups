<div class="tg_settings_tabs_content">

  <p>&nbsp;</p>
  <form method="POST" action="<?php echo esc_url( add_query_arg( array( 'process-tasks' => $tasks_maintenance, 'task-set-name' => 'Maintenance' ) ) ) ?>">
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
