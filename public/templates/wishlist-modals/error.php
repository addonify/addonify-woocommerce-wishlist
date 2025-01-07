<?php
/**
 * Modal template - Error.
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

do_action( 'addonify_wishlist_modal_wrapper_start', 'adfy-wl-modal-error' );
?>
<div class="adfy-wl-modal-content-icon">
	<?php echo apply_filters( 'addonify_wishlist_error_modal_icon', addonify_wishlist_get_wishlist_icons( 'error-1' ) ); // phpcs:ignore ?>
</div><!-- .adfy-wl-modal-content-icon -->
<div id="adfy-wl-modal-content-response">
	<p class="response-text">{error_message}</p><!-- .response-text -->
</div><!-- #adfy-wl-modal-content-response -->
<?php
do_action( 'addonify_wishlist_modal_wrapper_end' );
