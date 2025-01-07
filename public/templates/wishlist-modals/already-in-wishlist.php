<?php
/**
 * Modal template - Already in wishlist.
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
	echo apply_filters( 'addonify_wishlist_added_to_wishlist_modal_icon', addonify_wishlist_get_wishlist_icons( 'heart-2' ) ); // phpcs:ignore
	?>
</div><!-- .adfy-wl-modal-content-icon -->
<div id="adfy-wl-modal-content-response">
	<p class="response-text">
		<?php echo wp_kses_post( $already_in_wishlist_text ); ?>
	</p><!-- .response-text -->
</div><!-- #adfy-wl-modal-content-response -->
<?php
if ( $wishlist_page_url && $view_wishlist_btn_label ) {
	?>
	<div class="adfy-wl-modal-content-btns">
		<a
			id="adfy-wl-modal-page-link"
			class="adfy-wl-btn adfy-wl-modal-btn"
			href="<?php echo esc_url( $wishlist_page_url ); ?>"
		>
			<?php echo esc_html( $view_wishlist_btn_label ); ?>
		</a><!-- #adfy-wl-modal-page-link.adfy-wl-modal-btn -->
	</div><!-- .adfy-wl-modal-content-btns -->
	<?php
}

do_action( 'addonify_wishlist_modal_wrapper_end' );
