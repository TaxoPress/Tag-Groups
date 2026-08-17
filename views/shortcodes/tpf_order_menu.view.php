<?php // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- Variables sanitized before passing to view ?>
<div <?php if (! empty($div_class)) :
    ?> class="<?php echo $div_class ?>" <?php
     endif;?>>
  <label for="<?php echo $select_id_1 ?>" class="tg_tpf_select_label"><?php echo $orderby_text ?></label>
  <select name="orderby" class="tg_tpf_orderby_select" id="<?php echo $select_id_1 ?>" autocomplete="off">
  <?php foreach ($orderby_options as $orderby) :
        $orderby_a = explode(':', $orderby); ?>
    <option value="<?php echo $orderby_a[0]; ?>">
        <?php echo htmlentities($orderby_a[1], ENT_QUOTES, "UTF-8"); ?>
    </option>
        <?php
  endforeach; ?>
  </select>

  <label for="<?php echo $select_id_2 ?>" class="tg_tpf_select_label"><?php echo $order_text ?></label>
  <select name="order" class="tg_tpf_order_select" id="<?php echo $select_id_1 ?>" autocomplete="off">
  <?php foreach ($order_options as $order) :
        $order_a = explode(':', $order);
        if ('asc' == strtolower($order_a[0]) || 'desc' == strtolower($order_a[0])) :
            ?>
    <option value="<?php echo $order_a[0]; ?>">
            <?php echo htmlentities($order_a[1], ENT_QUOTES, "UTF-8"); ?>
    </option>
            <?php
        endif; ?>
        <?php
  endforeach; ?>
  </select>

  <?php if (! empty($sumoselect)) :
        ?>
    <script>
      if (typeof jQuery !== 'undefined' && typeof SumoSelect !== 'undefined') {
        jQuery('.tg_tpf_orderby_select,.tg_tpf_order_select').SumoSelect();
      } else {
        jQuery(document).ready(function(){
          setTimeout(function(){jQuery('.tg_tpf_orderby_select,.tg_tpf_order_select').SumoSelect();}, 500);
        });
      }
    </script>
        <?php
  endif;?>

  <style>
    .SumoSelect {
      width: auto;
    }
  </style>

</div>
