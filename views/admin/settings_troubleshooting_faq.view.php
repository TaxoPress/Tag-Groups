<div class="tg_settings_tabs_content">
  <div class="chatty-mango-settings-columns">

    <div>
      <h3><?php _e('Where should I start?', 'tag-groups') ?></h3>
      <p><?php printf(__('The easiest way to get going is to launch the <a %s>Setup Wizard</a>.', 'tag-groups'), 'href="' . admin_url('admin.php?page=tag-groups-settings-first-steps') . '"') ?></p>
    </div>

    <div>
      <h3><?php _e('How can I use a tag cloud in a widget?', 'tag-groups') ?></h3>
      <p><?php _e('Please insert the shortcode into a text widget.', 'tag-groups') ?></p>
    </div>

    <div>
      <h3><?php _e('There is a gray box around the tag cloud or shortcode output', 'tag-groups') ?></h3>
      <p><?php _e('Please check your shortcode in the editor and make sure that it is formatted as “Paragraph”, not “Preformatted”.', 'tag-groups') ?></p>
    </div>

    <div>
      <h3><?php _e('One or more shortcode parameters are not effective', 'tag-groups') ?></h3>
      <p><?php _e('Please check your shortcode in the editor and make sure that quotes are not formatted, i.e. not tilted or curled (re-type all quotes) and that there is no invisible HTML code inside the shortcode.', 'tag-groups') ?></p>
    </div>

    <div>
      <h3><?php _e('Instead of the tag cloud I just see a white area', 'tag-groups') ?></h3>
      <p><?php _e('This might be a JavaScript error on that page. If you use Gutenberg, disable the option "Delay the display of the tabs until they are fully rendered". If you use a shortcode, add <i>delay=0</i> to the parameters.', 'tag-groups') ?> <?php _e('You can also try to activate "Always load shortcode scripts" in the Front End -> Shortcodes settings.', 'tag-groups') ?></p>
    </div>

    <div>
      <h3><?php _e("The list on the Tag Groups page doesn't load. I only see the wheel spinning forever", 'tag-groups') ?></h3>
      <p><?php printf(__('This usually means that somewhere your site outputs a warning or alert that interferes with the data transfer to your browser. In most cases it is caused by another plugin. Please try to find out the cause <a %s>according to these instructions</a>.', 'tag-groups'), 'href="https://documentation.chattymango.com/documentation/tag-groups/troubleshooting/the-list-on-the-tag-groups-page-doesnt-load-i-only-see-the-wheel-spinning-forever/?pk_campaign=tg&pk_kwd=dashboard" target="_blank"') ?></p>
    </div>

    <div>
      <h3><?php _e('More FAQ', 'tag-groups') ?></h3>
      <p><?php printf(__('Please continue <a %s>here</a>.', 'tag-groups'), 'href="https://documentation.chattymango.com/documentation/tag-groups/faq-and-troubleshooting-tag-groups/?pk_campaign=tg&pk_kwd=dashboard" target="_blank"') ?></p>
    </div>

  </div>
</div>