<h2 class="nav-tab-wrapper">
  <?php foreach ( $tabs as $slug => $label ) :

    $settings_url = add_query_arg( array( 'active-tab' => $slug ), menu_page_url( $page, false ) );

    ?>
    <a href="<?php echo esc_url( $settings_url ) ?>" class="nav-tab
      <?php if ( $slug == $active_tab) : ?>
        nav-tab-active
      <?php endif; ?>
      "><?php echo $label ?>
    </a>
  <?php endforeach; ?>
</h2>
