<?php
/**
 * Modal template - Login required.
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
	<?php echo apply_filters( 'addonify_wishlist_error_modal_icon', addonify_wishlist_get_wishlist_icons( 'login-1' ) ); // phpcs:ignore ?>
</div><!-- .adfy-wl-modal-content-icon -->
<?php
if ( $login_required_message ) {
	?>
	<div id="adfy-wl-modal-content-response">
		<p class="response-text">
			<?php echo wp_kses_post( $login_required_message ); ?>
		</p><!-- .response-text -->
	</div><!-- #adfy-wl-modal-content-response -->
	<?php
}

if ( $login_url && $login_btn_label ) {
	?>
	<div class="adfy-wl-modal-content-btns">
		<a
			id="adfy-wl-modal-login-link"
			class="adfy-wl-btn adfy-wl-modal-btn"
			href="<?php echo esc_url( $login_url ); ?>"
		>
			<?php echo esc_html( $login_btn_label ); ?>
		</a><!-- .adfy-wl-modal-btn -->
	</div><!-- .adfy-wl-modal-content-btns -->
	<?php
}

do_action( 'addonify_wishlist_modal_wrapper_end' );
