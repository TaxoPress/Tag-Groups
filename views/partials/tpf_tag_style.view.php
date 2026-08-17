<?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- Variables sanitized before passing to view ?>
<style>
.tag_groups_tpf_tag {
  background: <?php echo $tag_color ?>;
}
.tag_groups_tpf_tag:before {
  border-color: transparent <?php echo $tag_color ?> transparent transparent;
}
.tag_groups_tpf_tag_selected {
  background: <?php echo $selected_tag_color ?>;
  color: #fff;
}
.tag_groups_tpf_tag_selected:before {
  border-color: transparent <?php echo $selected_tag_color ?> transparent transparent;
}
.tag_groups_tpf_tag_selected:after {
  -moz-box-shadow: -1px -1px 2px #333;
  -webkit-box-shadow: -1px -1px 2px #333;
  box-shadow: -1px -1px 2px #333;
}
</style>
