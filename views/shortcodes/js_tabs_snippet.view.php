<!-- begin Tag Groups plugin -->
<script>
  if (typeof jQuery !== 'undefined' && typeof jQuery.ui !== 'undefined' && typeof jQuery.ui.tabs !== 'undefined' && typeof jQuery.widget !== 'undefined' && typeof TagGroupsBase !== 'undefined') {
    TagGroupsBase.tabs('<?php echo esc_js($id) ?>', <?php echo $options_js_object; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already JSON encoded. ?>, <?php echo $delay ? 'true' : 'false' ?>);
  } else {
    jQuery(document).ready(function(){
      setTimeout(function(){
       if (typeof jQuery !== 'undefined' && typeof jQuery.ui !== 'undefined' && typeof jQuery.ui.tabs !== 'undefined' && typeof jQuery.widget !== 'undefined') {
        TagGroupsBase.tabs('<?php echo esc_js($id) ?>', <?php echo $options_js_object; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already JSON encoded. ?>, <?php echo $delay ? 'true' : 'false' ?>);
       } else {
         console.log('[Tag Groups] Error: jQuery UI Tabs is missing!');
       }
      }, 500);
    });
  }
</script>
<!-- end Tag Groups plugin -->
