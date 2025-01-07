<?php
/**
 * Wishlist page template functions.
 *
 * @since 2.0.14
 *
 * @package Addonify_Wishlist
 * @subpackage Addonify_Wishlist/includes/template-functions
 */

if ( ! function_exists( 'addonify_wishlist_page_wishlist_content_template' ) ) {
	/**
	 * Render wishlist content.
	 *
	 * @param array $products Wishlist product ids.
	 */
	function addonify_wishlist_page_wishlist_content_template( $products = array() ) {

		Addonify_Wishlist_Template_Loader::load_template(
			'wishlist-page/page-content.php',
			apply_filters(
				'addonify_wishlist_shortcode_contents_args',
				array(
					'wishlist_product_ids' => $products,
				)
			)
		);
	}
}


if ( ! function_exists( 'addonify_wishlist_page_wishlist_products_template' ) ) {
	/**
	 * Render wishlist content.
	 *
	 * @since 2.0.6
	 *
	 * @param array $products Product IDs in the wishlist.
	 */
	function addonify_wishlist_page_wishlist_products_template( $products = array() ) {

		Addonify_Wishlist_Template_Loader::load_template(
			'wishlist-page/wishlist-products.php',
			apply_filters(
				'addonify_wishlist_page_loop_args',
				array(
					'wishlist_product_ids' => $products,
				)
			)
		);
	}
}


if ( ! function_exists( 'addonify_wishlist_table_product_row_template' ) ) {
	/**
	 * Renders HTML template of product row displayed in table in the wishlist page.
	 *
	 * @since 2.0.6
	 *
	 * @param object $product WC_Product object.
	 */
	function addonify_wishlist_table_product_row_template( $product ) {

		if ( $product instanceof WC_Product ) {

			$product_id         = $product->get_id();
			$product_link       = $product->get_permalink();
			$product_name       = $product->get_name();
			$product_image      = $product->get_image( array( 60, 60 ) );
			$product_price_html = $product->get_price_html();
			$product_avaibility = addonify_wishlist_get_product_avaibility( $product );
			?>
			<tr
				id="adfy-wishlist-table-product-row-<?php echo esc_attr( $product_id ); ?>"
				class="addonify-wishlist-table-product-row"
				data-product_row="addonify-wishlist-table-product-row-<?php echo esc_attr( $product_id ); ?>"
				data-product_name="<?php echo esc_attr( $product_name ); ?>"
			>
				<td class="remove">
					<button 
						class="adfy-wl-btn addonify-wishlist-icon addonify-wishlist-ajax-remove-from-wishlist addonify-wishlist-table-button"
						name="addonify_wishlist_remove"
						data-product_id="<?php echo esc_attr( $product_id ); ?>"
						data-product_name="<?php echo esc_attr( $product_name ); ?>"
						data-source="wishlist-table"
					>
						<?php echo addonify_wishlist_escape_svg( addonify_wishlist_get_wishlist_icons( 'bin-5' ) ); // phpcs:ignore ?>
					</button>
				</td>
				<td class="image">
					<a href="<?php echo esc_url( $product_link ); ?>">
						<?php echo wp_kses_post( $product_image ); ?>
					</a>
				</td>
				<td class="name">
					<a href="<?php echo esc_url( $product_link ); ?>">
						<?php echo wp_kses_post( $product_name ); ?>
					</a>
				</td>
				<td class="price">
					<?php echo wp_kses_post( $product_price_html ); ?>
				</td>
				<td class="stock">
					<?php
					if (
						isset( $product_avaibility['class'] ) &&
						isset( $product_avaibility['avaibility'] )
					) {
						?>
						<span class="stock-label <?php echo esc_attr( $product_avaibility['class'] ); ?>">
							<?php echo esc_html( $product_avaibility['avaibility'] ); ?>
						</span>
						<?php
					}
					?>
				</td>
				<td class="actions">
					<?php
					do_action(
						'addonify_wishlist_add_to_cart_button',
						$product,
						array(
							'class' => 'adfy-wl-btn adfy-wl-table-btn adfy-wl-add-to-cart',
						)
					);
					?>
				</td>
			</tr>
			<?php
		}
	}

	add_action( 'addonify_wishlist_render_table_product_row', 'addonify_wishlist_table_product_row_template' );
}
