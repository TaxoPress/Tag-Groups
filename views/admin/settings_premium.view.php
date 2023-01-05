<div class="tg_premium_backend_main_box">
  <div class="tg_premium_backend_right_image_box">
    <a href="https://chattymango.com/tag-groups-premium/?pk_campaign=tg&pk_kwd=dashboard" target="_blank" >
      <img src="<?php echo TAG_GROUPS_PLUGIN_URL ?>/assets/images/logo-chatty-mango-200x200.png" alt="Chatty Mango Logo" class=""/>
    </a>
  </div>
  <h1>Get more features</h1>
  <p>Upgrade <b>Tag Groups</b> and take your tags to the next level!</p>
  <div style="clear:both;"></div>
  
  <?php if ( ! $tag_groups_premium_fs_sdk->is_paying() ): ?>
  <div class="tg_premium_backend_call_to_action">
    <span style="float:right; margin: 0 10px;"><a href="<?php echo admin_url( 'admin.php?page=tag-groups-settings-pricing&trial=true' ) ?>" class="tg_premium_backend_call_to_action_button">Try Premium</a></span>
    <h3>
      Start your 7-day free trial!<br/>
      All features. Cancel anytime.
    </h3>
  </div>
  <?php endif;?>

  <p>&nbsp;</p>

  <div id="tg_premium_presentation_tabs">
    <ul style="font-size: 1.2em">
      <li><a href="#tabs-1">Live Post Filter</a></li>
      <li><a href="#tabs-2">Searchable & Animated Tag Cloud</a></li>
      <li><a href="#tabs-3">Tag Meta Box</a></li>
      <li><a href="#tabs-4">Parent Level for Tag Groups</a></li>
      <li><a href="#tabs-5">Multiple Groups for One Tag</a></li>
      <li><a href="#tabs-6">And more</a></li>
    </ul>
    <div id="tabs-1">
      <p>Turn your tags into toggles and let your visitors search in real time for matching posts by any tag combination and by text. Organizing tags in groups makes it possible to apply different logic connections for the tags within a group and between groups.</p>
      <p>
        <a href="https://demo.chattymango.com/toggle-post-filter-demos/?pk_campaign=tg&pk_kwd=dashboard" target="_blank">
          <img src="<?php echo TAG_GROUPS_PLUGIN_URL ?>/assets/images/tgp-dpf-toggles.png" />
        </a>
      </p>
    </div>
    <div id="tabs-2">
      <p>The Shuffle Box is a tag cloud that can filter tags by name and by group. Tags rearrange with a nifty animation.</p>
      <p>
        <a href="https://demo.chattymango.com/tag-groups-premium-demo-page/?pk_campaign=tg&pk_kwd=dashboard" target="_blank">
          <img src="<?php echo TAG_GROUPS_PLUGIN_URL ?>/assets/images/tag-groups-premium-shuffle-box-animated.gif" />
        </a>
      </p>
    </div>
    <div id="tabs-3">
      <div class="tg_premium_backend_right_image_box">
        <img src="<?php echo TAG_GROUPS_PLUGIN_URL ?>/assets/images/tgp-meta-box.png" alt="Tag Groups Meta Box" title="Replace the default tag meta box with one that understands your tag groups!" class="tg_premium_backend_right_image"/>
      </div>
      <p>Replace the default tag meta box on the post edit screen with one that understands tag groups!</p>
      <p>The new tag box allows you to work with tags on two levels. Select all available tags from a menu or start typing to see all matching tags.</p>
      <ul style="list-style:disc;">
      <li style="padding:0 1em; margin-left:1em;"><b>Color coding</b> minimizes the risk of accidentally creating a new tag with a typo: New tags are green, tags that changed their groups are yellow.</li>
      <li style="padding:0 1em; margin-left:1em;"><b>Control new tags:</b> Optionally restrict the creation of new tags or prevent moving tags to another group on the post edit screen. These restrictions can be overridden per user role.</li>
      <li style="padding:0 1em; margin-left:1em;"><b>Bulk-add tags:</b> If you often need to insert the same set of tags, simply join them in one group and insert them with the push of a button.</li>
      </ul>
    </div>
    <div id="tabs-4">
      <p>With parent groups you can join multiple groups into one cluster that makes it easier to handle them on the backend.</p>
      <p>
        <a href="https://demo.chattymango.com/tag-groups-premium-demo-page/?pk_campaign=tg&pk_kwd=dashboard" target="_blank">
          <img src="<?php echo TAG_GROUPS_PLUGIN_URL ?>/assets/images/tgp-parent-groups.png" />
        </a>
      </p>
    </div>
    <div id="tabs-5">
      <p>The premium version allows you to add each tag to <b>multiple groups</b>.</p>
      <p>
        <a href="https://demo.chattymango.com/tag-groups-premium-demo-page/?pk_campaign=tg&pk_kwd=dashboard" target="_blank">
          <img src="<?php echo TAG_GROUPS_PLUGIN_URL ?>/assets/images/tgp-multiple-groups.png" />
        </a>
      </p>
    </div>
    <div id="tabs-6">
      <p></p>
      <p>
        <ul style="list-style:disc;">
          <li style="padding:0 1em; margin-left:1em;"><b>Filter posts</b> on the front end by tag group through a URL parameter.</li>
          <li style="padding:0 1em; margin-left:1em;">Display <b>post tags</b> segmented into groups under you posts.</li>
          <li style="padding:0 1em; margin-left:1em;"><b>More tag clouds:</b> Display your tags in a table or combine tags from multiple groups into one tag cloud.</li>
          <li style="padding:0 1em; margin-left:1em;"><b>Tag cloud search:</b> Let your visitors filter tags in tag clouds by parts of their names.</li>
        </ul>
      </p>
    </div>
  </div>
  <script>
  jQuery(document).ready(function(){
    if (typeof jQuery !== 'undefined' && typeof jQuery.ui !== 'undefined' && typeof jQuery.ui.tabs !== 'undefined' && typeof jQuery.widget !== 'undefined') {
      jQuery('#tg_premium_presentation_tabs').tabs();
    }
  });
  </script>

  <div style="clear:both;"></div>

  <p>The complete set of features is available with the "Premium" plan. <?php printf( 'See the <a %1$s>feature comparison and plans</a> or check out the <a %2$s>demos</a>.', 'href="https://chattymango.com/tag-groups-plans-and-pricing/?pk_campaign=tg&pk_kwd=dashboard" target="_blank"', 'href="https://demo.chattymango.com/tag-groups-premium-demo-page/?pk_campaign=tg&pk_kwd=dashboard" target="_blank"' )?></p>

  <?php if ( ! $tag_groups_premium_fs_sdk->is_paying() ): ?>
  <div class="tg_premium_backend_call_to_action">
    <span style="float:right; margin: 0 10px;"><a href="<?php echo admin_url( 'admin.php?page=tag-groups-settings-pricing&trial=true' ) ?>" class="tg_premium_backend_call_to_action_button">Try Premium</a></span>
    <h3>
      Start your 7-day free trial!<br/>
      All features. Cancel anytime.
    </h3>
  </div>
  <?php endif;?>

</div>