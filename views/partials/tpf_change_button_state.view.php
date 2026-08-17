<script>
  jQuery('.tg_group_dpf_toggle_term').on('change', function(){
      tagGroupsTPFButtonSetColor(this);
    });

    function tagGroupsTPFAllButtonsSetColor() {
      jQuery('.tg_group_dpf_toggle_term').each(function(){
      tagGroupsTPFButtonSetColor(this);
    });
    }

  function tagGroupsTPFButtonSetColor(element) {
      var termId = jQuery(element).attr('data-termid');
      var groupId = jQuery(element).attr('data-groupid');
    if (jQuery(element).prop('checked')) {
        jQuery('.tg_group_dpf_toggle_tr[data-termid="'+termId+'"][data-groupid="'+groupId+'"]').addClass('tag_groups_tr_selected');
      } else {
        jQuery('.tg_group_dpf_toggle_tr[data-termid="'+termId+'"][data-groupid="'+groupId+'"]').removeClass('tag_groups_tr_selected');
      }
  }
</script>