<?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- Variables sanitized before passing to view ?>
<nav role="navigation" aria-label="Pagination Navigation">
  <h4 class="tg_pager">
  <?php if ($pager_data['down']) : ?>
        <a href="<?php echo add_query_arg('tg-list-paged', $paged - 1) ?>" id="tg_pager_down" class="tg_dpf_back tg_pointer tg_left" aria-label="previous"><span class="dashicons dashicons-arrow-left-alt"></span>&nbsp;<?php echo htmlentities(str_replace("'", "/'", $message_go_back), ENT_QUOTES, "UTF-8") ?></a>
  <?php endif; ?>
      <?php if ($pager_data['up']) : ?>
        <a href="<?php echo add_query_arg('tg-list-paged', $paged + 1) ?>" id="tg_pager_up" class="tg_dpf_more tg_pointer tg_right" aria-label="next"><?php echo htmlentities(str_replace("'", "/'", $message_load_more), ENT_QUOTES, "UTF-8") ?>&nbsp;<span class="dashicons dashicons-arrow-right-alt"></span></a>
      <?php endif; ?>
    </h4>
</nav>
<div style="clear:both;"></div>
