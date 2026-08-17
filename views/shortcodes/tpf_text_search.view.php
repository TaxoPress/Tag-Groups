<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Placeholder sanitized before passing to view ?>
<input class="tg_tpf_text_search_trigger tg_dpf_toggle_text_search" autocomplete="off" type="text" placeholder="<?php echo str_replace('"', '\”', $placeholder) ?>">

<script>
if ( typeof tagGroupsSeparateTextSearch === 'undefined' ) {
  var tagGroupsSeparateTextSearch = <?php echo (int) $search_trigger ?>;
} else {
  tagGroupsSeparateTextSearch = Math.max( <?php echo (int) $search_trigger ?>, tagGroupsSeparateTextSearch );
}
</script>
