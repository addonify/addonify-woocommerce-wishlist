<?php
/**
 * The file that defines the template functions.
 *
 * @link       https://www.addonify.com
 * @since      1.0.0
 *
 * @package    Addonify_Wishlist
 * @subpackage Addonify_Wishlist/includes
 */

/**
 * Load wishlist button template functions.
 */
require_once ADDONIFY_WISHLIST_PLUGIN_PATH . '/includes/template-functions/wishlist-button.php';
/**
 * Load wishlist modals template functions.
 */
require_once ADDONIFY_WISHLIST_PLUGIN_PATH . '/includes/template-functions/wishlist-modals.php';
/**
 * Load wishlist page template functions.
 */
require_once ADDONIFY_WISHLIST_PLUGIN_PATH . '/includes/template-functions/wishlist-page.php';
/**
 * Load wishlist sidebar template functions.
 */
require_once ADDONIFY_WISHLIST_PLUGIN_PATH . '/includes/template-functions/wishlist-sidebar.php';


if ( ! function_exists( 'addonify_wishlist_loader_template' ) ) {
	/**
	 * Renders HTML template for loader/spinner in wishlist sidebar and table.
	 *
	 * @since 2.0.6
	 */
	function addonify_wishlist_loader_template() {
		?>
		<div id="addonify-wishlist_spinner">
			<?php echo apply_filters( 'addonify_wishlist_loader', '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M2 11h5v2H2zm15 0h5v2h-5zm-6 6h2v5h-2zm0-15h2v5h-2zM4.222 5.636l1.414-1.414 3.536 3.536-1.414 1.414zm15.556 12.728-1.414 1.414-3.536-3.536 1.414-1.414zm-12.02-3.536 1.414 1.414-3.536 3.536-1.414-1.414zm7.07-7.071 3.536-3.535 1.414 1.415-3.536 3.535z"></path></svg>' ); // phpcs:ignore ?>
		</div>
		<?php
	}

	add_action( 'addonify_wishlist_render_loader', 'addonify_wishlist_loader_template' );
}


if ( ! function_exists( 'addonify_wishlist_product_removal_undo_notice_template' ) ) {
	/**
	 * Renders HTML template of product removal undo notice.
	 *
	 * @since 2.0.6
	 */
	function addonify_wishlist_product_removal_undo_notice_template() {
		?>
		<p>
			<?php echo esc_html( addonify_wishlist_get_option( 'undo_action_prelabel_text' ) ); ?>
			<?php
			if ( ! empty( addonify_wishlist_get_option( 'undo_action_label' ) ) ) {
				?>
				<a
					href="#"
					id="addonify-wishlist-undo-deleted-product-link"
				><?php echo esc_html( addonify_wishlist_get_option( 'undo_action_label' ) ); ?></a>
				<?php
			}
			?>
		</p>
		<?php
	}

	add_action(
		'addonify_wishlist_render_product_removal_undo_notice',
		'addonify_wishlist_product_removal_undo_notice_template'
	);
}


if ( ! function_exists( 'addonify_wishlist_login_required_content_content_template' ) ) {
	/**
	 * Renders HTML template for login required.
	 *
	 * @since 2.0.14
	 */
	function addonify_wishlist_login_required_content_content_template() {

		global $wp;

		$login_required_message = addonify_wishlist_get_option( 'login_required_message' );

		$login_url = add_query_arg(
			'addonify_wishlist_redirect',
			home_url( $wp->request ),
			( get_option( 'woocommerce_myaccount_page_id' ) ) ? get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) : wp_login_url()
		);

		$login_btn_label = addonify_wishlist_get_option( 'login_btn_label' );
		$login_btn_label = $login_btn_label ? $login_btn_label : esc_html__( 'Login', 'addonify-wishlist' );
		?>
		<div id="adfy-wl-login-required" class="adfy-wl-page-section">
			<div class="adfy-wl-section-icon">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
					<path fill="currentColor" d="M9.76 0C15.417 0 20 4.477 20 10S15.416 20 9.76 20c-3.191 0-6.142-1.437-8.07-3.846a.644.644 0 0 1 .115-.918a.68.68 0 0 1 .94.113a8.96 8.96 0 0 0 7.016 3.343c4.915 0 8.9-3.892 8.9-8.692c0-4.8-3.985-8.692-8.9-8.692a8.961 8.961 0 0 0-6.944 3.255a.68.68 0 0 1-.942.101a.644.644 0 0 1-.103-.92C3.703 1.394 6.615 0 9.761 0Zm.545 6.862l2.707 2.707c.262.262.267.68.011.936L10.38 13.15a.662.662 0 0 1-.937-.011a.662.662 0 0 1-.01-.937l1.547-1.548l-10.31.001A.662.662 0 0 1 0 10c0-.361.3-.654.67-.654h10.268L9.38 7.787a.662.662 0 0 1-.01-.937a.662.662 0 0 1 .935.011Z"/>
				</svg>
			</div>
			<div class="adfy-wl-section-content">
				<h3 class="adfy-wl-section-header"><?php echo esc_html( $login_required_message ); ?></h3>
				<p>
					<a href="<?php echo esc_url( $login_url ); ?>" class="adfy-wl-btn adfy-wl-page-btn">
						<?php echo esc_html( $login_btn_label ); ?>
					</a>
				</p>
			</div>
		</div>
		<?php
	}

	add_action( 'addonify_wishlist_login_required_content', 'addonify_wishlist_login_required_content_content_template' );
}


if ( ! function_exists( 'addonify_wishlist_no_wishlist_products_content_template' ) ) {
	/**
	 * Renders HTML template for no items in the wishlist.
	 *
	 * @since 2.0.14
	 *
	 * @param string $css_class Section CSS class.
	 */
	function addonify_wishlist_no_wishlist_products_content_template( $css_class = 'adfy-wl-hide' ) {

		$empty_wishlist_label = addonify_wishlist_get_option( 'empty_wishlist_label' );

		$nav_url          = '';
		$display_nav_link = addonify_wishlist_get_option( 'show_empty_wishlist_navigation_link' );
		$nav_page         = addonify_wishlist_get_option( 'empty_wishlist_navigation_link' );
		$nav_link_label   = addonify_wishlist_get_option( 'empty_wishlist_navigation_link_label' );

		if ( '1' === $display_nav_link && ! empty( $nav_page ) && ! empty( $nav_link_label ) ) {
			$nav_url = get_permalink( (int) $nav_page );
		}
		?>
		<div id="adfy-wl-no-items" class="adfy-wl-page-section <?php echo esc_attr( $css_class ); ?>">
			<div class="adfy-wl-section-icon">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
					<path fill="currentColor" fill-rule="evenodd" d="M1.633 2.796c.762-.837 1.85-1.297 3.127-1.297c1.164 0 2.407.55 3.24 1.626c.828-1.075 2.066-1.626 3.24-1.626c1.274 0 2.36.458 3.124 1.293c.756.828 1.136 1.962 1.136 3.22c0 2.166-1.113 3.909-2.522 5.264c-1.405 1.352-3.17 2.383-4.633 3.14a.75.75 0 0 1-.693-.002c-1.463-.765-3.228-1.788-4.633-3.133C1.61 9.93.5 8.193.5 6.013c0-1.255.378-2.389 1.133-3.217m1.109 1.01C2.287 4.306 2 5.053 2 6.013c0 1.624.816 2.996 2.057 4.184c1.146 1.098 2.6 1.985 3.945 2.705c1.335-.71 2.79-1.604 3.937-2.707C13.182 8.998 14 7.62 14 6.013c0-.963-.288-1.71-.744-2.21C12.808 3.314 12.14 3 11.24 3c-.976 0-2.093.628-2.527 1.95a.75.75 0 0 1-1.426 0C6.854 3.63 5.725 3 4.76 3c-.903 0-1.57.315-2.018.807Z" clip-rule="evenodd"/>
				</svg>
			</div>
			<div class="adfy-wl-section-content">
				<h3 class="adfy-wl-section-header"><?php echo esc_html( $empty_wishlist_label ); ?></h3>
				<?php
				if ( $nav_url ) {
					?>
					<p>
						<a href="<?php echo esc_url( $nav_url ); ?>" class="adfy-wl-btn adfy-wl-page-btn">
							<?php echo esc_html( $nav_link_label ); ?>
						</a>
					</p>
					<?php
				}
				?>
			</div>
		</div>
		<?php
	}
	add_action( 'addonify_wishlist_no_wishlist_products_content', 'addonify_wishlist_no_wishlist_products_content_template' );
}


if ( ! function_exists( 'addonify_wishlist_add_to_cart_button_template' ) ) {
	/**
	 * Renders add to cart button.
	 *
	 * @since 2.0.14
	 *
	 * @param WC_Product $product WC_Product.
	 * @param array      $args    Arguments.
	 */
	function addonify_wishlist_add_to_cart_button_template( $product, $args = array() ) {

		$product_id = $product->get_id();

		$defaults = array(
			'quantity'              => 1,
			'aria-describedby_text' => $product->add_to_cart_aria_describedby(),
			'attributes'            => array(
				'data-product_id'   => $product_id,
				'data-product_name' => $product->get_name(),
				'data-product_sku'  => $product->get_sku(),
				'aria-label'        => $product->add_to_cart_description(),
				'rel'               => 'nofollow',
			),
		);

		if ( is_a( $product, 'WC_Product_Simple' ) ) {
			$defaults['attributes']['data-success_message'] = $product->add_to_cart_success_message();
		}

		$args = apply_filters( 'addonify_wishlist_add_to_cart_args', wp_parse_args( $args, $defaults ), $product );

		if ( ! empty( $args['attributes']['aria-describedby'] ) ) {
			$args['attributes']['aria-describedby'] = wp_strip_all_tags( $args['attributes']['aria-describedby'] );
		}

		if ( isset( $args['attributes']['aria-label'] ) ) {
			$args['attributes']['aria-label'] = wp_strip_all_tags( $args['attributes']['aria-label'] );
		}

		$aria_describedby = isset( $args['aria-describedby_text'] ) ? sprintf( 'aria-describedby="woocommerce_loop_add_to_cart_link_describedby_%s"', esc_attr( $product_id ) ) : '';

		$class = implode(
			' ',
			array_filter(
				array(
					'button',
					wc_wp_theme_get_element_class_name( 'button' ), // escaped in the template.
					'product_type_' . $product->get_type(),
					$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
					$product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
				)
			)
		);

		if ( isset( $args['class'] ) && ! empty( $args['class'] ) ) {
			$class .= ' ' . $args['class'];
		}

		echo apply_filters( // phpcs:ignore
			'addonify_wishlist_add_to_cart_link',
			sprintf(
				'<a href="%s" %s data-quantity="%s" class="%s" %s>%s</a>',
				esc_url( $product->add_to_cart_url() ),
				$aria_describedby,
				esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
				esc_attr( isset( $class ) ? $class : 'button' ),
				isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
				esc_html( $product->add_to_cart_text() )
			),
			$product,
			$args
		);

		if ( isset( $args['aria-describedby_text'] ) ) {
			?>
			<span id="woocommerce_loop_add_to_cart_link_describedby_<?php echo esc_attr( $product_id ); ?>" class="screen-reader-text">
				<?php echo esc_html( $args['aria-describedby_text'] ); ?>
			</span>
			<?php
		}
	}

	add_action( 'addonify_wishlist_add_to_cart_button', 'addonify_wishlist_add_to_cart_button_template', 10, 2 );
}