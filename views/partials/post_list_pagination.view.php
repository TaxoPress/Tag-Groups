<?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- Variables sanitized before passing to view ?>
<nav role="navigation" aria-label="Pagination Navigation">
  <div class="tg_pager tg_pager_pages">
  <?php if ($pager_data['page'] > 1) : ?>
    <a href="<?php echo add_query_arg('tg-list-paged', $pager_data['page'] - 2) ?>" class="tg_">
      <div class="tg_pager_number tg_pointer" aria-label="previous">&laquo;</div>
    </a>
  <?php endif; ?>

    <?php
      $dots_below_set = false;
      $dots_above_set = false;
    ?>
    <?php if ($pager_data['total_pages'] > 1) : ?>
        <?php for ($i = 1; $i <= $pager_data['total_pages']; $i++) : ?>
            <?php if ($pager_data['total_pages'] < 6 || abs($pager_data['page'] - $i) < 3 || 1 == $i || $i == $pager_data['total_pages']) : ?>
                <?php if ($i === $pager_data['page']) : ?>
          <div class="tg_pager_number tg_pager_number_active" aria-label="Current Page, Page <?php echo $i ?>" aria-current="true"><?php echo $i ?></div>
                <?php else : ?>
          <a href="<?php echo add_query_arg('tg-list-paged', $i - 1) ?>">
            <div class="tg_pager_number tg_pointer" aria-label="Goto Page <?php echo $i ?>"><?php echo $i ?></div>
          </a>
                <?php endif; ?>
            <?php elseif ($i < $pager_data['page'] && ! $dots_below_set) : ?>
          <div class="tg_pager_number tg_pager_number_inactive" >...</div>
                <?php $dots_below_set = true; ?>
            <?php elseif ($i > $pager_data['page'] && ! $dots_above_set) : ?>
          <div class="tg_pager_number tg_pager_number_inactive" >...</div>
                <?php $dots_above_set = true; ?>
            <?php endif; ?>
        <?php endfor; ?>
    <?php else : ?>
      <div class="tg_pager_number tg_pager_number_active" aria-label="Current Page, Page <?php echo $pager_data['page'] ?>" aria-current="true"><?php echo $pager_data['page'] ?></div>
    <?php endif; ?>

    <?php if ($pager_data['page'] < $pager_data['total_pages']) : ?>
    <a href="<?php echo add_query_arg('tg-list-paged', $pager_data['page']) ?>">
      <div class="tg_pager_number tg_pointer" aria-label="next">&raquo;</div>
    </a>
    <?php endif; ?>
  </div>
</nav>
<div style="clear:both;"></div>
