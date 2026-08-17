<script>
jQuery(document).ready( function() {
<?php foreach ($groups as $group) : ?>
  jQuery('.tag_groups_tpf_tag[data-groupid=<?php echo (int) $group ?>]').on('click', function(){
    var groupId = <?php echo (int) $group ?>;
    var termId = jQuery(this).attr('data-termid');
    if (jQuery('.tg_group_dpf_toggle_term[data-groupid='+groupId+'][data-termid='+termId+']').prop('checked')) {
      jQuery('.tg_group_dpf_toggle_term[data-groupid='+groupId+']').not('[data-termid='+termId+']').prop('checked', false);
      jQuery('.tag_groups_tpf_tag[data-groupid='+groupId+']').not('[data-termid='+termId+']').removeClass('tag_groups_tpf_tag_selected');
    }
  });
<?php endforeach; ?>
});
</script>
