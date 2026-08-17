<?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- Variables sanitized before passing to view ?>
<div id="<?php echo $div_id ?>" <?php if (!empty($div_class)) :
    ?> class="<?php echo $div_class ?>" <?php
         endif; ?>>
  <div id="tg_filter_box_toggle_menu_wide" role="search">
    <?php if ($text_search) :
        ?>
      <div class="tg_dpf_menu_item tg_dpf_text_search_container_wide">
        <h3 class="tg_group_dpf_toggle_name"><?php echo $title_text_search ?></h3>
        <input class="tg_tpf_text_search_trigger tg_dpf_toggle_text_search_wide" autocomplete="<?php echo $autocomplete ?>" type="text" placeholder="<?php echo str_replace('"', '\”', $placeholder_text_search) ?>">
      </div>
        <?php
    endif; ?>
    <?php $tabindex = 0; ?>
    <?php foreach ($toggle_groups as $toggle_group) :
        ?>
      <div id="tg_group_dpf_toggle_group_container_<?php echo $toggle_group['group_id'] ?>" class="tg_group_dpf_toggle_group_container tg_dpf_menu_item">
        <h3 class="tg_group_dpf_toggle_name"><?php echo $toggle_group['label'] ?></h3>
        <div id="tg_group_dpf_toggle_term_container_<?php echo $toggle_group['group_id'] ?>" class="tg_group_dpf_toggle_term_container">
          <?php foreach ($toggle_group['terms'] as $term) :
                ?>
            <div class="tg_group_dpf_toggle_tr tg_pointer tg_tpf_trigger" data-groupid="<?php echo $toggle_group['group_id'] ?>" data-termid="<?php echo $term->term_id ?>">
              <div class="tg_group_dpf_toggle_label_td tg_pointer">
                <label class="tg_pointer tg_group_tpf_label" tabindex="<?php echo $tabindex ?>" aria-hidden="true"><?php echo $term->name ?></label>
              </div>
              <div class="tg_group_dpf_toggle_input_td tg_pointer">
                <div class="tag_groups_switch tg_pointer">
                  <input type="checkbox" autocomplete="<?php echo $autocomplete ?>" data-groupid="<?php echo $toggle_group['group_id'] ?>" data-termid="<?php echo $term->term_id ?>" data-slug="<?php echo $term->slug ?>" class="tg_group_dpf_toggle_term" id="tg_group_dpf_toggle_term_<?php echo $toggle_group['group_id'] ?>_<?php echo $term->term_id ?>" name="tg_group_dpf_toggle_term_<?php echo $term->term_id ?>" value="1" aria-label="<?php echo $term->name ?>"/>
                  <label class="tg_pointer" aria-hidden="true"><i></i></label>
                </div>
              </div>
            </div>
                <?php $tabindex++; ?>
                <?php
          endforeach; ?>
        </div>
      </div>
        <?php
    endforeach; ?>
    <div style="clear:both;"></div>
  </div>
</div>
