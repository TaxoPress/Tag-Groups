<?php
/**
*   Using .html() instead of .text() to avoid ampersands displaying
*/
?>
<script>
jQuery(document).ready(function () {
  var sel_filter;
  // check if we have an bulk action menu
  if (jQuery("select[name='term-group-top']").length){
    sel_filter = jQuery("<select id='tag_filter' name='term-filter' style='margin-left: 20px;'>").insertAfter("select[name='term-group-top']");
  } else {
    sel_filter = jQuery("<select id='tag_filter' name='term-filter' style='margin-left: 20px;'>").insertAfter("select[name='action']");
  }
  sel_filter.append(jQuery("<option>").attr("value", "-1").html("<?php
  _e( 'Filter off', 'tag-groups' )
  ?>"));
  <?php foreach ( $term_groups as $term_group ) :
    $bg_color = in_array( $term_group['term_group'], $parents ) ? '#dfdfdf' : 'transparent';
    $prefix = empty( $parents ) || in_array( $term_group['term_group'], $parents ) ? '' : '&nbsp;&nbsp;'; ?>
  sel_filter.append(jQuery("<option>").attr("value", "<?php echo $term_group['term_group'] ?>").css('background-color', '<?php echo $bg_color ?>').html("<?php echo $prefix . htmlentities( $term_group['label'], ENT_QUOTES, "UTF-8" )?>"));
  <?php endforeach; ?>
  jQuery("#tag_filter option[value=<?php echo $tag_filter ?>]").prop('selected', true);

  jQuery('#tg_reset_filter_button').hide();
});
</script>
