<?php
/**
 * Modal template - Removed from wishlist.
 *
 * @link       https://www.addonify.com
 * @since      1.0.0
 *
 * @package    Addonify_Wishlist
 * @subpackage Addonify_Wishlist/public/templates/modals
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

do_action( 'addonify_wishlist_modal_wrapper_start', 'adfy-wl-modal-success' );
?>
<div class="adfy-wl-modal-content-icon">
	<?php
	echo apply_filters( 'addonify_wishlist_removed_from_wishlist_modal_icon', addonify_wishlist_get_wishlist_icons( 'heart-1' ) ); // phpcs:ignore
	?>
</div><!-- .adfy-wl-modal-content-icon -->
<div id="adfy-wl-modal-content-response">
	<p class="response-text">
		<?php echo wp_kses_post( $removed_from_wishlist_message ); ?>
	</p><!-- .response-text -->
</div><!-- #adfy-wl-modal-content-response -->
<?php
do_action( 'addonify_wishlist_modal_wrapper_end' );