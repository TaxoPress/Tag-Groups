<!-- begin Tag Groups plugin -->
<script>
  if (typeof jQuery !== 'undefined' && typeof jQuery.ui !== 'undefined' && typeof jQuery.ui.tabs !== 'undefined' && typeof jQuery.widget !== 'undefined' && typeof TagGroupsBase !== 'undefined') {
    TagGroupsBase.tabs('<?php echo esc_attr($id) ?>', <?php echo wp_json_encode($options_js_object) ?>, <?php echo esc_attr($delay ? 'true' : 'false') ?>);
  } else {
    jQuery(document).ready(function(){
      setTimeout(function(){
       if (typeof jQuery !== 'undefined' && typeof jQuery.ui !== 'undefined' && typeof jQuery.ui.tabs !== 'undefined' && typeof jQuery.widget !== 'undefined') {
        TagGroupsBase.tabs('<?php echo esc_attr($id) ?>', <?php echo wp_json_encode($options_js_object) ?>, <?php echo esc_attr($delay ? 'true' : 'false') ?>);
       } else {
         console.log('[Tag Groups] Error: jQuery UI Tabs is missing!');
       }
      }, 500);
    });
  }
</script>
<!-- end Tag Groups plugin -->
