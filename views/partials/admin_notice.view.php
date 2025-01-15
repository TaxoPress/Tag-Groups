<div class="notice notice-<?php echo $notice['type'] ?> is-dismissible" style="clear:both;">
  <?php echo $notice['content'] ?>
  <div style="clear:both;"></div>
</div>
<?php if ( '' != $ajax_link ) : ?>
  <script>
  jQuery(document).ready(function(){
    jQuery("#tag_groups_premium_clear_cache").on('click', function(){
      jQuery("#tag_groups_premium_clear_cache").attr("disabled", "disabled");
      jQuery.ajax({
        url: "<?php echo $ajax_link ?>",
        data: {
          action: "tg_ajax_clear_object_cache",
        },
        success: function( data ) {
          jQuery("#tag_groups_premium_clear_cache").replaceWith("<span class=\'dashicons dashicons-yes\'></span>");
        }
      });
    });
  });
</script>
<?php endif; ?>
