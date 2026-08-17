<?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- Variables sanitized before passing to view ?>
<script>
  function tagGroupsTPFInit(){
    if ( tagGroupsTPFInitDone ) {
      return;
    }

    const options = {
      accordion: <?php echo (int) $accordion ?>,
      ajaxLink: '<?php echo $ajax_link ?>',
      cacheKey: '<?php echo $cache_key ?>',
      cachingTime: <?php echo isset($caching_time) ? $caching_time : 'null' ?>,
      debug: <?php echo ( defined('WP_DEBUG') && WP_DEBUG ) ? 'true' : 'false' ?>,
      defaultShowPosts: <?php echo $default_show_posts ? 'true' : 'false' ?>,
      displayAmount: <?php echo (int) $display_amount ?>,
      divId: '<?php echo $div_id ?>',
      groupIds: <?php echo wp_json_encode(array_values($term_groups_included)); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>,
      isSlider: <?php echo $is_slider ? 'true' : 'false' ?>,
      legacyMenu: <?php echo $legacy ? 'true' : 'false' ?>,
      messageAmountPl: '<?php echo htmlentities(str_replace("'", "\'", $message_amount_plural), ENT_QUOTES, "UTF-8") ?>',
      messageAmountSg: '<?php echo htmlentities(str_replace("'", "\'", $message_amount_singular), ENT_QUOTES, "UTF-8") ?>',
      messageGoBack: '<?php echo htmlentities(str_replace("'", "\'", $message_go_back), ENT_QUOTES, "UTF-8") ?>',
      messageLoadMore: '<?php echo htmlentities(str_replace("'", "\'", $message_load_more), ENT_QUOTES, "UTF-8") ?>',
      messageNothingFound: '<?php echo htmlentities(str_replace("'", "\'", $message_nothing_found), ENT_QUOTES, "UTF-8") ?>',
      operator: '<?php echo $operator ?>',
      order: '<?php echo $order ?>',
      orderBy: '<?php echo $orderby ?>',
      pager: <?php echo (int) $pager ?>,
      persistentFilter: <?php echo (int) $persistent_filter ?>,
      postsPerPage: <?php echo (int) $posts_per_page ?>,
      postsPlaceholder: '<?php echo htmlentities(str_replace("'", "\'", $posts_placeholder), ENT_QUOTES, "UTF-8") ?>',
      presetTermSlugs: <?php echo wp_json_encode($preset_term_slugs); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>,
      staticTaxonomy: '<?php echo str_replace("'", "\'", $static_taxonomy) ?>',
      staticTerms: '<?php echo str_replace("'", "\'", $static_terms) ?>',
      taxonomy: '<?php echo $taxonomy ?>',
      template: `<?php echo $template ?>`,
      textSearch: <?php echo $text_search ?>,
      timeout: <?php echo (int) $timeout ?>,
      transition: '<?php echo $transition ?>',
    };

    if (
      typeof TagGroupsTogglePostFilter !== 'undefined'
      && jQuery !== 'undefined'
      && (!options.accordion || typeof jQuery.ui.accordion !== 'undefined')
      && (typeof tagGroupsTPFBodyOptions !== 'undefined' &&typeof tagGroupsTPFBodyOptions.layout !== 'undefined' && !(tagGroupsTPFBodyOptions.layout === 'masonry' || tagGroupsTPFBodyOptions.layout === 'masonry-small' || tagGroupsTPFBodyOptions.layout === 'masonry-large') || typeof jQuery.fn.masonry !== 'undefined' && typeof jQuery.fn.imagesLoaded !== 'undefined')
      && (options.displayAmount < 2 || typeof jQuery.fn.jnoty !== 'undefined')
    ) {
      TagGroupsTogglePostFilter.load(options);
    } else {
      jQuery(document).ready(function(){
        setTimeout(function(){TagGroupsTogglePostFilter.load(options)}, 500);
      });
    }
    tagGroupsTPFInitDone = true;
  }
</script>
