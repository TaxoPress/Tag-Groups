<!-- begin Tag Groups plugin -->
<script>
  if (typeof jQuery !== 'undefined' && typeof jQuery.ui !== 'undefined' && typeof jQuery.ui.tabs !== 'undefined' && typeof jQuery.widget !== 'undefined' && typeof TagGroupsBase !== 'undefined') {
    TagGroupsBase.tabs('<?php echo $id ?>', <?php echo $options_js_object ?>, <?php echo $delay ? 'true' : 'false' ?>);
  } else {
    jQuery(document).ready(function(){
      setTimeout(function(){
       if (typeof jQuery !== 'undefined' && typeof jQuery.ui !== 'undefined' && typeof jQuery.ui.tabs !== 'undefined' && typeof jQuery.widget !== 'undefined') {
        TagGroupsBase.tabs('<?php echo $id ?>', <?php echo $options_js_object ?>, <?php echo $delay ? 'true' : 'false' ?>);
       } else {
         console.log('[Tag Groups] Error: jQuery UI Tabs is missing!');
       }
      }, 500);
    });
  }
</script>
<!-- end Tag Groups plugin -->