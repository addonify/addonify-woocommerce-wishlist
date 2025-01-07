<?php
/**
 * Modal template - Confirm empty wishlist.
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

do_action( 'addonify_wishlist_modal_wrapper_start', 'adfy-wl-modal-alert' );
?>
<div class="adfy-wl-modal-content-icon">
	<?php
	echo apply_filters( 'addonify_wishlist_alert_modal_icon', addonify_wishlist_get_wishlist_icons( 'warning-1' ) ); // phpcs:ignore
	?>
</div><!-- .adfy-wl-modal-content-icon -->
<div id="adfy-wl-modal-content-response">
	<p class="response-text">
		<?php echo wp_kses_post( $clear_wishlist_confirm_text ); ?>
	</p><!-- .response-text -->
</div><!-- #adfy-wl-modal-content-response -->
<?php
if ( $clear_wishlist_confirm_btn_label ) {
	?>
	<div class="adfy-wl-modal-content-btns">
		<button id="adfy-wl-modal-confirm-clear-wl-btn" class="adfy-wl-btn adfy-wl-modal-btn">
			<?php echo esc_html( $clear_wishlist_confirm_btn_label ); ?>
		</button>
	</div><!-- .adfy-wl-modal-content-btns -->
	<?php
}

do_action( 'addonify_wishlist_modal_wrapper_end' );
