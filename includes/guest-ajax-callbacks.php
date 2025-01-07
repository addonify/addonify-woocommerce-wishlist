<?php
/**
 * Collection of functions to handle AJAX request for guest user.
 *
 * @since 2.0.6
 *
 * @package    Addonify_Wishlist
 * @subpackage Addonify_Wishlist/includes
 */

if ( ! function_exists( 'addonify_wishlist_get_guest_wishlist_content' ) ) {
	/**
	 * Get's wishlist sidebar and table content.
	 *
	 * @since 1.0.0
	 */
	function addonify_wishlist_get_guest_wishlist_content() {

		if ( ! wp_doing_ajax() ) {
			return;
		}

		$response_data = array(
			'success' => false,
			'message' => '',
		);

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if (
			! $nonce ||
			! wp_verify_nonce( $nonce, 'addonify-wishlist' )
		) {
			$response_data['message'] = addonify_wishlist_get_option( 'security_token_error_message' );
			wp_send_json( $response_data );
		}

		$response_data['success'] = true;

		$wishlist_items = isset( $_POST['product_ids'] ) ? json_decode( sanitize_text_field( wp_unslash( $_POST['product_ids'] ) ), true ) : array();

		$has_wishlist_sidebar = isset( $_POST['has_wishlist_sidebar'] ) ? true : false;

		$has_wishlist_table = isset( $_POST['has_wishlist_table'] ) ? true : false;

		if ( addonify_wishlist_get_option( 'show_sidebar' ) === '1' && $has_wishlist_sidebar ) {
			ob_start();
			do_action( 'addonify_wishlist_sidebar_products', $wishlist_items );
			$response_data['sidebarContent'] = ob_get_clean();
		}

		if ( $has_wishlist_table ) {
			ob_start();
			do_action( 'addonify_wishlist_page_wishlist_products', $wishlist_items );
			$response_data['tableContent'] = ob_get_clean();
		}

		wp_send_json( $response_data );
	}
}


if ( ! function_exists( 'addonify_wishlist_get_guest_sidebar_table_product_row' ) ) {
	/**
	 * Get's wishlist sidebar and table product row content.
	 *
	 * @since 1.0.0
	 */
	function addonify_wishlist_get_guest_sidebar_table_product_row() {

		if ( ! wp_doing_ajax() ) {
			return;
		}

		$response_data = array(
			'success' => false,
			'message' => '',
		);

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if (
			! $nonce ||
			! wp_verify_nonce( $nonce, 'addonify-wishlist' )
		) {
			$response_data['message'] = addonify_wishlist_get_option( 'security_token_error_message' );
			wp_send_json( $response_data );
		}

		$product_id = isset( $_POST['product_id'] ) ? absint( wp_unslash( $_POST['product_id'] ) ) : 0;

		if ( ! $product_id ) {
			$response_data['message'] = addonify_wishlist_get_option( 'invalid_product_id_error_message' );
			wp_send_json( $response_data );
		}

		$product = wc_get_product( $product_id );

		if ( ! $product ) {
			$response_data['message'] = addonify_wishlist_get_option( 'invalid_product_error_message' );
			wp_send_json( $response_data );
		}

		$has_wishlist_sidebar = isset( $_POST['has_wishlist_sidebar'] ) ? true : false; // phpcs:ignore

		$has_wishlist_table = isset( $_POST['has_wishlist_table'] ) ? true : false; // phpcs:ignore

		$response_data['success'] = true;

		if ( $has_wishlist_sidebar ) {
			ob_start();
			do_action( 'addonify_wishlist_sidebar_product_row', $product );
			$response_data['sidebarProductRowContent'] = ob_get_clean();
		}

		if ( $has_wishlist_table ) {
			ob_start();
			do_action( 'addonify_wishlist_render_table_product_row', $product );
			$response_data['tableProductRowContent'] = ob_get_clean();
		}

		wp_send_json( $response_data );
	}
}
