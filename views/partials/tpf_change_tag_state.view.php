<script>
  jQuery('.tg_group_dpf_toggle_term').on('change', function(){
      tagGroupsTPFSetTagState(this);
      return false;
    });

    function tagGroupsTPFAllTagsSetTagState() {
      jQuery('.tg_group_dpf_toggle_term').each(function(){
        tagGroupsTPFSetTagState(this);
    });
    }

  function tagGroupsTPFSetTagState(element) {
    var termId = jQuery(element).attr('data-termid');
    var groupId = jQuery(element).attr('data-groupid');
    if (jQuery(element).prop('checked')) {
        jQuery('.tag_groups_tpf_tag[data-termid="'+termId+'"][data-groupid="'+groupId+'"]').addClass('tag_groups_tpf_tag_selected');
      } else {
        jQuery('.tag_groups_tpf_tag[data-termid="'+termId+'"][data-groupid="'+groupId+'"]').removeClass('tag_groups_tpf_tag_selected');
      }
  }

  // window.addEventListener('pageshow', tagGroupsTPFAllTagsSetTagState, false); // for Firefox when returning to a page
</script>