<?php
/**
 * Wishlist button template functions.
 *
 * @since 2.0.14
 *
 * @package Addonify_Wishlist
 * @subpackage Addonify_Wishlist/includes/template-functions
 */

if ( ! function_exists( 'addonify_wishlist_add_to_wishlist_button_template' ) ) {
	/**
	 * Render add to wishlist button.
	 *
	 * @param array $button_template_args  Button template args.
	 */
	function addonify_wishlist_add_to_wishlist_button_template( $button_template_args ) {

		Addonify_Wishlist_Template_Loader::load_template(
			'wishlist-button/add-to-wishlist-button.php',
			apply_filters(
				'addonify_wishlist_add_to_wishlist_button_args',
				$button_template_args
			)
		);
	}
}
