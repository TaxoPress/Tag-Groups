<?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- Variables sanitized before passing to view ?>
<div id="<?php echo $div_id ?>"<?php if (! empty($div_class)) :
    ?> class="<?php echo $div_class ?>"<?php
         endif; ?>>

  <?php if (isset($message_amount)) :
        ?>
    <div id="tg_post_list_amount"><?php echo $message_amount ?></div>
        <?php
  endif; ?>

  <?php if (empty($result)) :
        ?>
        <?php echo $message_nothing_found ?>
        <?php
  else :
        ?>
      <?php echo $pager_top; ?>
    <div class="tg_post_list_posts content-area">
      <?php foreach ($result as $item) :
            ?>
        <article id="<?php echo $item['id'] ?>" class="<?php echo $article_class ?>">
            <?php echo $item['content']; ?>
        </article>
            <?php
      endforeach; ?>
      <div style="clear:both"></div>
    </div>
      <?php echo $pager_bottom; ?>
      <?php
  endif; ?>
</div>
