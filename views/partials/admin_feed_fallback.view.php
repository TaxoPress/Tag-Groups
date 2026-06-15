<div style="padding:10px 10px 10px; text-align:center; float:left;">
    <?php echo wp_kses_post(sprintf(__('Please visit <a %s>%s</a>.', 'tag-groups'), ' href="' . esc_url($posts_url_campaign) . '" target="_blank" ', esc_url($posts_url))) ?>
</div>
