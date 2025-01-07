<?php
/**
 * Template for displaying wishlist items in wishlist page.
 *
 * @link       https://www.addonify.com
 * @since      1.0.0
 *
 * @package    Addonify_Wishlist
 * @subpackage Addonify_Wishlist/public/templates
 */

$display_clear_wishlist_btn = addonify_wishlist_get_option( 'show_wishlist_emptying_button' );
$clear_wishlist_btn_label   = addonify_wishlist_get_option( 'clear_wishlist_label' );

$no_items_css_class = 'adfy-wl-show';
$form_css_class     = 'adfy-wl-hide';

if ( is_array( $wishlist_product_ids ) && ! empty( $wishlist_product_ids ) ) {
	$no_items_css_class = 'adfy-wl-hide';
	$form_css_class     = 'adfy-wl-show';
}

do_action( 'addonify_wishlist_before_wishlist_form' );
?>
<form
	action="#"
	method="POST"
	id="adfy-wl-form"
	class="<?php echo esc_attr( $form_css_class ); ?>"
>
	<?php do_action( 'addonify_wishlist_before_wishlist_form_table' ); ?>
	<table id="addonify-wishlist-table">
		<thead class="adfy-wl-table-head">
			<tr>
				<th class="remove"></th>
				<th class="image"><?php echo esc_html__( 'Product Image', 'addonify-wishlist' ); ?></th>
				<th class="name"><?php echo esc_html__( 'Product Name', 'addonify-wishlist' ); ?></th>
				<th class="price"><?php echo esc_html__( 'Unit Price', 'addonify-wishlist' ); ?></th>
				<th class="stock"><?php echo esc_html__( 'Stock Status', 'addonify-wishlist' ); ?></th>
				<th class="actions"></th>
			</tr>
		</thead>
		<tbody id="adfy-wl-table-body">
			<?php
			foreach ( $wishlist_product_ids as $product_id ) {

				$product = wc_get_product( (int) $product_id );

				do_action( 'addonify_wishlist_render_table_product_row', $product );
			}
			?>
		</tbody>
		<?php
		if ( '1' === $display_clear_wishlist_btn && ! empty( $clear_wishlist_btn_label ) ) {
			?>
			<tfoot id="adfy-wl-table-foot">
				<tr>
					<td colspan="6">
						<div class="adfy-wl-table-toolbars">
							<button type="button" id="addonify-wishlist__clear-all" class="adfy-wl-btn adfy-wl-table-btn">
								<?php echo esc_html( $clear_wishlist_btn_label ); ?>
							</button>
						</div>
					</td>
				</tr>
			</tfoot>
			<?php
		}
		?>
	</table>
	<?php do_action( 'addonify_wishlist_after_wishlist_form_table' ); ?>
</form>
<?php
do_action( 'addonify_wishlist_no_wishlist_products_content', $no_items_css_class );

do_action( 'addonify_wishlist_after_wishlist_form' );
