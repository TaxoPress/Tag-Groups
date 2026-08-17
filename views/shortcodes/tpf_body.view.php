<?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- Variables sanitized before passing to view ?>
<div <?php if (! empty($div_id)) :
    ?> id="<?php echo $div_id ?>"<?php
     endif; ?><?php if (! empty($div_class)) :
    ?> class="<?php echo $div_class ?>"<?php
     endif; ?>>
  <div id="tg_tpf_pager_wrapper_top" style="display:none"></div>
  <div style="clear:both;"></div>
  <div id="tg_filter_dpf_toggle_box_posts" class="content-area" aria-live="assertive">
  </div>
  <div style="clear:both;"></div>
  <div id="tg_tpf_pager_wrapper_bottom" style="display:none"></div>
  <div style="clear:both;"></div>
</div>

<script>
  var tagGroupsTPFBodyOptions = {
    defaultImageSrc: '<?php echo htmlentities(str_replace("'", "\'", $default_image_src), ENT_QUOTES, "UTF-8") ?>',
    defaultShowPosts: <?php echo $default_show_posts ? 'true' : 'false' ?>,
    displayAmount: <?php echo $display_amount ?>,
    layout: '<?php echo $layout ?>',
    legacyBody: <?php echo $legacy ? 'true' : 'false' ?>,
    messageAmountPl: '<?php echo htmlentities(str_replace("'", "\'", $message_amount_plural), ENT_QUOTES, "UTF-8") ?>',
    messageAmountSg: '<?php echo htmlentities(str_replace("'", "\'", $message_amount_singular), ENT_QUOTES, "UTF-8") ?>',
    messageGoBack: '<?php echo htmlentities(str_replace("'", "\'", $message_go_back), ENT_QUOTES, "UTF-8") ?>',
    messageLoadMore: '<?php echo htmlentities(str_replace("'", "\'", $message_load_more), ENT_QUOTES, "UTF-8") ?>',
    messageNothingFound: '<?php echo htmlentities(str_replace("'", "\'", $message_nothing_found), ENT_QUOTES, "UTF-8") ?>',
    order: '<?php echo $order ?>',
    orderBy: '<?php echo $orderby ?>',
    pager: <?php echo (int) $pager ?>,
    pagerPosition: '<?php echo $pager_position ?>',
    postsPerPage: <?php echo $posts_per_page ?>,
    postsPlaceholder: '<?php echo htmlentities(str_replace("'", "\'", $posts_placeholder), ENT_QUOTES, "UTF-8") ?>',
    template: `<?php echo $template ?>`,
    transition: '<?php echo $transition ?>',
  }
</script>
