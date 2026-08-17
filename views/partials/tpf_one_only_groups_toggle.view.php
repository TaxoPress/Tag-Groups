<script>
jQuery(document).ready( function() {
<?php foreach ($groups as $group) : ?>
  jQuery('.tg_group_dpf_toggle_tr[data-groupid=<?php echo (int) $group ?>]').on('click', function(){
    var groupId = <?php echo (int) $group ?>;
    var termId = jQuery(this).attr('data-termid');
    if (jQuery('.tg_group_dpf_toggle_term[data-groupid='+groupId+'][data-termid='+termId+']').prop('checked')) {
      jQuery('.tg_group_dpf_toggle_term[data-groupid='+groupId+']').not('[data-termid='+termId+']').prop('checked', false);
    }
  });
<?php endforeach; ?>
});
</script>
