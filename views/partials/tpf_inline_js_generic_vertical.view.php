<?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- Variables sanitized before passing to view ?>
<script>
    var tagGroupsTPFInitDone = false;

    jQuery(document).ready(function() {
        tagGroupsTPFInit();
        jQuery('.tag_groups_dpf_toggle_body').css('min-height', jQuery('#<?php echo $div_id ?>').height());
    });

    window.addEventListener( 'pageshow', tagGroupsTPFInit, false );
</script>
