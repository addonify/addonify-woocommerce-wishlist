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
<div id="adfy-wl-content-wrapper">

	<div id="adfy-wl-notice" class="adfy-wl-notice"></div>

	<div id="adfy-wl-content">
		<?php
		if ( is_user_logged_in() ) {
			do_action( 'addonify_wishlist_page_wishlist_products', $wishlist_product_ids );
		} else {

			$require_login = addonify_wishlist_get_option( 'require_login' );

			if ( '1' === $require_login ) {
				do_action( 'addonify_wishlist_login_required_content' );
			} else {
				do_action( 'addonify_wishlist_page_wishlist_products', $wishlist_product_ids );
			}
		}
		?>
	</div>
</div>