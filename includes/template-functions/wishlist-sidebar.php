<?php
/**
 * Wishlist sidebar template functions.
 *
 * @since 2.0.14
 *
 * @package Addonify_Wishlist
 * @subpackage Addonify_Wishlist/includes/template-functions
 */

if ( ! function_exists( 'addonify_wishlist_sidebar_wishlist_toggle_button_template' ) ) {
	/**
	 * Render sidebar toggle button.
	 *
	 * @param array $products Wishlist product ids.
	 */
	function addonify_wishlist_sidebar_wishlist_toggle_button_template( $products = array() ) {

		$alignment = 'addonify-align-' . addonify_wishlist_get_option( 'sidebar_position' );

		$css_classes = array( $alignment );

		$total_items = count( $products );

		$css_classes[] = ( $total_items < 1 && empty( $products ) ) ? 'hidden' : '';

		Addonify_Wishlist_Template_Loader::load_template(
			'wishlist-sidebar/sidebar-toggle-button.php',
			apply_filters(
				'addonify_wishlist_sidebar_toggle_button_args',
				array(
					'css_classes' => implode( ' ', $css_classes ),
					'label'       => addonify_wishlist_get_option( 'sidebar_btn_label' ),
					'show_icon'   => (int) addonify_wishlist_get_option( 'sidebar_show_icon' ),
					'icon'        => addonify_wishlist_get_wishlist_icons( addonify_wishlist_get_option( 'sidebar_toggle_btn_icon' ) ),
				)
			)
		);
	}
}


if ( ! function_exists( 'addonify_wishlist_sidebar_wishlist_content_template' ) ) {
	/**
	 * Render sidebar.
	 *
	 * @param array $products Wishlist product ids.
	 */
	function addonify_wishlist_sidebar_wishlist_content_template( $products = array() ) {

		if ( get_the_ID() === (int) addonify_wishlist_get_option( 'wishlist_page' ) ) {
			// do not show sidebar in wishlist page.
			return;
		}

		if ( (int) addonify_wishlist_get_option( 'show_sidebar' ) !== 1 ) {
			return;
		}

		$sidebar_position = addonify_wishlist_get_option( 'sidebar_position' );
		if ( empty( $sidebar_position ) ) {
			$sidebar_position = 'right';
		}

		$wishlist_title           = addonify_wishlist_get_option( 'sidebar_title' );
		$wishlist_page            = addonify_wishlist_get_option( 'wishlist_page' );
		$wishlist_page_url        = $wishlist_page ? get_permalink( $wishlist_page ) : '';
		$wishlist_page_link_label = addonify_wishlist_get_option( 'view_wishlist_page_button_label' );

		Addonify_Wishlist_Template_Loader::load_template(
			'wishlist-sidebar/sidebar-content.php',
			apply_filters(
				'addonify_wishlist_sidebar_args',
				array(
					'total_items'                     => count( $products ),
					'css_class'                       => 'addonify-align-' . $sidebar_position,
					'title'                           => $wishlist_title,
					'wishlist_url'                    => $wishlist_page_url,
					'alignment'                       => 'addonify-align-' . $sidebar_position,
					'view_wishlist_page_button_label' => $wishlist_page_link_label,
					'product_ids'                     => $products,
				)
			)
		);
	}
}


if ( ! function_exists( 'addonify_wishlist_sidebar_wishlist_products_template' ) ) {
	/**
	 * Render sidebar loop.
	 *
	 * @param array $products Product ids.
	 */
	function addonify_wishlist_sidebar_wishlist_products_template( $products = array() ) {

		Addonify_Wishlist_Template_Loader::load_template(
			'wishlist-sidebar/wishlist-products.php',
			apply_filters(
				'addonify_wishlist_sidebar_loop_args',
				array(
					'wishlist_product_ids' => $products,
				)
			)
		);
	}
}


if ( ! function_exists( 'addonify_wishlist_sidebar_product_template' ) ) {
	/**
	 * Renders HTML template of product row displayed in wishlist sidebar.
	 *
	 * @since 2.0.6
	 *
	 * @param object $product WC_Product object.
	 */
	function addonify_wishlist_sidebar_product_template( $product ) {

		if ( $product instanceof WC_Product ) {

			$product_id         = $product->get_id();
			$product_link       = $product->get_permalink();
			$product_name       = $product->get_name();
			$product_image      = $product->get_image( array( 82, 82 ) );
			$product_price_html = $product->get_price_html();
			$product_avaibility = addonify_wishlist_get_product_avaibility( $product );
			?>
			<li
				id="adfy-wishlist-sidebar-product-row-<?php echo esc_attr( $product_id ); ?>"
				class="addonify-wishlist-sidebar-item"
				data-product_row="addonify-wishlist-sidebar-product-row-<?php echo esc_attr( $product_id ); ?>"
				data-product_name="<?php echo esc_attr( $product_name ); ?>"
			>
				<div class="adfy-wishlist-row">
					<div class="adfy-wishlist-col image-column">
						<div class="adfy-wishlist-woo-image">
							<a href="<?php echo esc_url( $product_link ); ?>">
								<?php echo wp_kses_post( $product_image ); ?>
							</a>
						</div>
					</div>
					<div class="adfy-wishlist-col title-price-column">
						<div class="adfy-wishlist-woo-title">
							<a href="<?php echo esc_url( $product_link ); ?>">
								<?php echo wp_kses_post( $product_name ); ?>
							</a>
						</div>
						<div class="adfy-wishlist-woo-price"><?php echo wp_kses_post( $product_price_html ); ?></div>
						<?php
						if (
							isset( $product_avaibility['class'] ) &&
							isset( $product_avaibility['avaibility'] )
						) {
							?>
							<div class="adfy-wishlist-woo-stock">
								<span class="stock-label <?php echo esc_attr( $product_avaibility['class'] ); ?>">
									<?php echo esc_html( $product_avaibility['avaibility'] ); ?>
								</span>
							</div>
							<?php
						}
						?>
					</div>
				</div>

				<div class="adfy-wishlist-woo-action">
					<div class="adfy-wishlist-row">
						<div class="adfy-wishlist-col cart-column">
							<?php
							do_action(
								'addonify_wishlist_add_to_cart_button',
								$product,
								array(
									'class' => 'adfy-wl-btn adfy-wl-table-btn adfy-wl-add-to-cart',
								)
							);
							?>
						</div>
						<div class="adfy-wishlist-col remove-item-column">
							<button
								class="adfy-wl-btn addonify-wishlist-icon addonify-wishlist-ajax-remove-from-wishlist adfy-wl-sidebar-btn"
								name="addonify_wishlist_remove"
								data-product_id="<?php echo esc_attr( $product_id ); ?>"
								data-product_name="<?php echo esc_attr( $product_name ); ?>"
								data-source="wishlist-sidebar"
							>
								<span class="screen-reader-text"><?php esc_html_e( 'Remove from wishlist', 'addonify-wishlist' ); ?></span>
								<?php echo addonify_wishlist_escape_svg( addonify_wishlist_get_wishlist_icons( 'bin-5' ) ); // phpcs:ignore ?>
							</button>
						</div>
					</div>
				</div>
			</li>
			<?php
		}
	}

	add_action( 'addonify_wishlist_sidebar_product_row', 'addonify_wishlist_sidebar_product_template' );
}
