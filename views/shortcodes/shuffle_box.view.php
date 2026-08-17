<?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- Variables sanitized before passing to view ?>
<div id="<?php echo $div_id_inner ?>">
  <?php echo implode("\n", $html_header) ?>
  <div id="<?php echo $div_id_inner ?>_container" class="cm-shuffle-box-tag-container" aria-live="assertive">
    <?php echo implode("\n", $html_div) ?>
  </div>
</div>
<?php if ('serverSideRender' != $source) : ?>
<script>
  (function(){
    const options = {
      divIdInner: '<?php echo $div_id_inner ?>',
      addPremiumFilter: <?php echo $add_premium_filter ? 'true' : 'false' ?>,
      timeoutMilliSecs: <?php echo $timeout ?>,
      initialGroup: <?php echo $initial_group ?>,
      layoutMode: '<?php echo $layout_mode ?>'
    };

    if (typeof TagGroupsShuffleBox !== 'undefined' && jQuery !== 'undefined') {
      <?php // We test if function is available because another plugin might have moved it to the end ?>
      const obj = Object.create( TagGroupsShuffleBox );
      obj.init(options);
    } else {
      jQuery(document).ready(function(){
        setTimeout(function(){
          const obj = Object.create( TagGroupsShuffleBox );
          obj.init(options);
        }, 500);
      });
    }
})()
</script>
<?php endif; ?>
