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

$no_items_css_class         = 'adfy-wl-show';
$wishlist_content_css_class = 'adfy-wl-hide';

if ( is_array( $wishlist_product_ids ) && ! empty( $wishlist_product_ids ) ) {
	$no_items_css_class         = 'adfy-wl-hide';
	$wishlist_content_css_class = 'adfy-wl-show';
}
?>
<ul
	id="adfy-wl-sidebar-content"
	class="adfy-wl-sidebar-wl-items <?php echo esc_attr( $wishlist_content_css_class ); ?>"
>
	<?php
	foreach ( $wishlist_product_ids as $product_id ) {

		$product = wc_get_product( (int) $product_id );

		do_action( 'addonify_wishlist_sidebar_product_row', $product );
	}
	?>
</ul>
<?php
do_action( 'addonify_wishlist_no_wishlist_products_content', $no_items_css_class );
