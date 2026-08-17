<?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- Variables sanitized before passing to view ?>
<!-- begin Tag Groups plugin -->
<script>
  if (typeof tagGroupsMakeTableResponsive === 'undefined') {
    function tagGroupsMakeTableResponsive(tableId, responsiveBreakpoint) {
      jQuery("#"+tableId).basictable({
        breakpoint: responsiveBreakpoint,
        showEmptyCells: true
      });
    }
  }
  if (typeof TagGroupsDynamicPostFilter !== 'undefined' && jQuery !== 'undefined' && jQuery.basictable !== 'undefined') {
    <?php // We test if function is available because another plugin might have moved it to the end ?>
      tagGroupsMakeTableResponsive('<?php echo $table_id ?>','<?php echo $responsive_breakpoint ?>');
    } else {
      jQuery(document).ready(function(){
        setTimeout(function(){tagGroupsMakeTableResponsive('<?php echo $table_id ?>','<?php echo $responsive_breakpoint ?>');}, 500);
      });
    }
</script>
<!-- end Tag Groups plugin -->
