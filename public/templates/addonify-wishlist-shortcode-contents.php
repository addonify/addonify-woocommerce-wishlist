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
<?php if ( isset( $data['wishlist_name'] ) && $data['wishlist_name'] ) : ?>
	<h2><?php echo esc_html( $data['wishlist_name'] ); ?></h2>
<?php endif; ?>

<div id="addonify-wishlist-page-container">
	<?php
	if ( 
		isset( $data['wishlist_product_ids'] ) &&
		is_array( $data['wishlist_product_ids'] ) && 
		count( $data['wishlist_product_ids'] ) > 0 
	) {
		?>
		<form action="" method="POST" >

			<input type="hidden" name="nonce" value="<?php echo esc_html( $data['nonce'] ); ?>" >
			<input type="hidden" name="process_addonify_wishlist_form" value="1" >

			<table id="addonify-wishlist-table">
				<thead class="addonify-wishlist-items-heading">
					<tr>
						<th class="remove"></th>
						<th class="image"><?php echo esc_html__( 'Product Image', 'addonify-wishlist' ); ?></th>
						<th class="name"><?php echo esc_html__( 'Product Name', 'addonify-wishlist' ); ?></th>
						<th class="price"><?php echo esc_html__( 'Unit Price', 'addonify-wishlist' ); ?></th>
						<th class="stock"><?php echo esc_html__( 'Stock Status', 'addonify-wishlist' ); ?></th>
						<th class="cart"><?php echo esc_html__( 'Actions', 'addonify-wishlist' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ( $data['wishlist_product_ids'] as $product_id ) {

						$product = wc_get_product( $product_id );
						?>
						<tr>
							<td class="remove">
								<button type="submit" class="adfy-wishlist-btn addonify-wishlist-icon" name="addonify_wishlist_remove" value="<?php echo esc_attr( $product_id ); ?>"><i class="adfy-wishlist-icon trash-2"></i></button>
							</td>
							<td class="image">
								<?php
								$product_post_thumbnail_id = $product->get_image_id();
								if ( $product->get_image() ) {
									?>
									<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
										<?php echo $product->get_image(array(72, 72)); ?>
									</a>
									<?php
								}
								?>
							</td>
							<td class="name">
								<?php echo $product->get_title(); ?>
							</td>
							<td class="price">
								<?php echo $product->get_price_html(); ?>
							</td>
							<td class="stock">
								<?php echo $product->get_stock_status(); ?>
							</td>
							<td class="actions">
								<?php
								if ( $product->is_purchasable() && $product->is_in_stock() ) {

									if ( in_array( $product->get_type(), array( 'simple', 'external' ) ) ) {
										?>
										<button type="submit" class="button adfy-wishlist-btn" name="addonify_wishlist_add_to_cart" value="<?php echo esc_attr( $product_id ); ?>"><?php echo esc_html( $product->add_to_cart_text() ); ?></button>
										<?php
									} else {
										$add_to_cart_button_classes = array(
											'button',
											'adfy-wishlist-btn',
											'product_type_' . $product->get_type(),
											$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
											$product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
										);

										$add_to_cart_button_attributes = array(
											'data-product_id'  => $product->get_id(),
											'data-product_sku' => $product->get_sku(),
											'aria-label'       => $product->add_to_cart_description(),
											'rel'              => 'nofollow',
										);
										?>
										<a href="<?php echo esc_url( $product->add_to_cart_url() ) ?>" class="<?php echo esc_attr( implode( ' ', $add_to_cart_button_classes ) ); ?>" <?php echo wc_implode_html_attributes( $add_to_cart_button_attributes ); ?>>
											<?php echo esc_html( $product->add_to_cart_text() ); ?>
										</a>
										<?php
									}										
								}
								?>
							</td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>		
		</form>
		<?php 
	} else {
		echo esc_html__( 'Your wishlist is empty.', 'addonify-wishlist' );
	}
	?>
</div>
