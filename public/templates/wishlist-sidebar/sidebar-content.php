<?php
/**
 * Public template.
 *
 * @link       https://www.addonify.com
 * @since      1.0.0
 *
 * @package    Addonify_Wishlist
 * @subpackage Addonify_Wishlist/public/templates
 */

// direct access is disabled.
defined( 'ABSPATH' ) || exit;

?>
<div id="adfy-wl-sidebar-overlay"></div>
<div id="adfy-wl-sidebar-container" class="<?php echo esc_attr( $css_class ); ?>">
	<?php do_action( 'addonify_wishlist_after_sidebar_opening_tag' ); ?>
	<div class="adfy-wl-ssc-body">
		<div class="adfy-wl-ssc-header">
			<h3 id="adfy-wl-sidebar-wl-title" class="adfy-wl-sidebar-title">
				<?php echo esc_html( $title ); ?>
			</h3>
			<?php do_action( 'addonify_wishlist_after_sidebar_title' ); ?>
			<button id="adfy-wl-sidebar-close-btn">	
				<span class="screen-reader-text"><?php esc_html_e( 'Close wishlist sidebar', 'addonify-wishlist' ); ?></span>
				<?php echo addonify_wishlist_escape_svg( addonify_wishlist_get_wishlist_icons( 'close-2' ) ); // phpcs:ignore ?>
			</button>
		</div>

		<div id="adfy-wl-notice" class="adfy-wl-notice"></div>

		<form action="" method="POST" id="adfy-wl-sidebar-form">
			<?php do_action( 'addonify_wishlist_before_sidebar_wishlist_form_table' ); ?>
			<div id="adfy-wl-sidebar-items">
				<?php do_action( 'addonify_wishlist_sidebar_products', $product_ids ); ?>
			</div>
			<?php do_action( 'addonify_wishlist_after_sidebar_wishlist_form_table' ); ?>
		</form>
	</div>
	<?php
	if ( $view_wishlist_page_button_label ) {
		?>
		<div class="adfy-wl-ssc-footer">
			<a id="adfy-wl-wishlist-page-link" href="<?php echo esc_url( apply_filters( 'addonify_wishlist_sidebar_wishlist_page_url', $wishlist_url ) ); ?>" class="adfy-wl-wishlist-page-link-btn">
				<?php echo esc_html( $view_wishlist_page_button_label ); ?>
				<span class="icon">
					<svg viewBox="0 0 64 64" xml:space="preserve">
						<path d="M61.5,28.5l-6.9-8.2c-0.6-0.7-1.7-0.8-2.5-0.2c-0.7,0.6-0.8,1.7-0.2,2.5l6.5,7.7H3c-1,0-1.8,0.8-1.8,1.8c0,1,0.8,1.8,1.8,1.8h55.4l-6.5,7.7c-0.6,0.7-0.5,1.8,0.2,2.5c0.3,0.3,0.7,0.4,1.1,0.4c0.5,0,1-0.2,1.3-0.6l6.9-8.2C63.2,33.5,63.2,30.5,61.5,28.5z"/>
					</svg>
				</span>
			</a>
		</div>
		<?php
	}
	?>
	<?php do_action( 'addonify_wishlist_before_sidebar_closing_tag' ); ?>
</div>
