<div class="tg_settings_tabs_content">

  <h2><?php _e( 'Newsletter', 'tag-groups' ) ?></h2>
  <p>
    <?php printf( __( '<a %s>Sign up for our newsletter</a> to receive updates about new versions and related tips and news.', 'tag-groups' ), 'href="http://eepurl.com/c6AeK1" target="_blank"' ) ?>
  </p>
  <p>&nbsp;</p>

  <h2><?php _e( 'Latest Posts', 'tag-groups' ) ?></h2>
  <div style="display:block; background-color:white; border:1px solid #ccc; padding:10px;">
    <div id="tg_feed_container">
        <div style="text-align: center;">
          <?php _e( 'Loading...', 'tag-groups-premium' ) ?>
        </div>
    </div>
    <div style="clear:both"></div>
  </div>
</div>

<script>
jQuery(document).ready(function(){
  var tg_feed_amount = jQuery("#tg_feed_amount").val();
  var data = {
    action: "tg_ajax_get_feed",
    url: "<?php echo TAG_GROUPS_UPDATES_RSS_URL ?>",
    amount: 5
  };

  jQuery.post("<?php echo $admin_url ?>", data, function (data) {
    jQuery("#tg_feed_container").html(JSON.parse(data));
  });
});
</script>
