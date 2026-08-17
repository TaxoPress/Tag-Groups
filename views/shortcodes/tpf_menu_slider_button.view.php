<?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- Variables sanitized before passing to view ?>
<button class="tg_dpf_slider_toggle_button tg_pointer <?php echo $button_class ?>" autocomplete="off" aria-pressed="false">
  <?php echo htmlentities($button_text, ENT_QUOTES, "UTF-8") ?>
</button>
