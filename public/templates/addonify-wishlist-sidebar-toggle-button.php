<?php
    // direct access is disabled
    defined( 'ABSPATH' ) || exit;

?>

<a id="addonify-wishlist-show-sidebar-btn" class="<?php echo $css_classes; ?>">
    <?php if( $show_icon ) :?>
        <span class="button-icon"><i class="adfy-wishlist-icon settings"></i> 
    <?php endif;?>

    </span> <span class="button-label"><?php echo $label; ?></span>
</a>