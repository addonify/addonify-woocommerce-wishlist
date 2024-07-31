<?php
/**
 * Define general settings fields of plugin.
 *
 * @link       https://addonify.com/
 * @since      2.0.0
 *
 * @package    Addonify_Wishlist
 * @subpackage Addonify_Wishlist/includes/setting-functions/fields
 */

if ( ! function_exists( 'addonify_wishlist_general_v_2_options' ) ) {
	/**
	 * General options.
	 *
	 * @return array
	 */
	function addonify_wishlist_general_v_2_options() {
		return array(
			'general_options' => array(
				'title'        => __( 'General Options', 'addonify-wishlist' ),
				'type'         => 'sub_section',
				'sub_sections' => array(
					'enable_wishlist'                   => array(
						'label'       => __( 'Enable wishlist', 'addonify-wishlist' ),
						'description' => __( 'If Addonify WooCommerce Wishlist plugin is disabled, its functionality will not work.', 'addonify-wishlist' ),
						'type'        => 'switch',
						'className'   => '',
						'badge'       => 'Required',
						'value'       => addonify_wishlist_get_option( 'enable_wishlist' ),
					),
					'wishlist_page'                     => array(
						'type'        => 'select',
						'className'   => '',
						'label'       => __( 'Wishlist page', 'addonify-wishlist' ),
						'description' => __( 'Choose the page where you would like the wishlist table to be displayed.', 'addonify-wishlist' ),
						'dependent'   => array( 'enable_wishlist' ),
						'choices'     => addonify_wishlist_get_pages(),
						'value'       => addonify_wishlist_get_option( 'wishlist_page' ),
					),
					'require_login'                     => array(
						'type'        => 'switch',
						'label'       => __( 'Login required', 'addonify-wishlist' ),
						'description' => __( 'Set if a user is required to be logged in to add product into the wishlist.', 'addonify-wishlist' ),
						'dependent'   => array( 'enable_wishlist' ),
						'value'       => addonify_wishlist_get_option( 'require_login' ),
					),
					'if_not_login_action'               => array(
						'type'        => 'select',
						'className'   => '',
						'label'       => __( 'Action on add to wishlist button for guest user', 'addonify-wishlist' ),
						'description' => __( 'If user login is required, choose what action to take next when a guest user clicks on add to wishlist button.', 'addonify-wishlist' ),
						'dependent'   => array( 'enable_wishlist', 'require_login' ),
						'choices'     => array(
							'default'    => __( 'Redirect to login page', 'addonify-wishlist' ),
							'show_popup' => __( 'Display login popup notice', 'addonify-wishlist' ),
						),
						'value'       => addonify_wishlist_get_option( 'if_not_login_action' ),
					),
					'after_add_to_wishlist_action'      => array(
						'type'        => 'select',
						'className'   => '',
						'label'       => __( 'Action after added to wishlist', 'addonify-wishlist' ),
						'description' => __( 'After a product has been successfully added to the wishlist, choose what action to take next.', 'addonify-wishlist' ),
						'dependent'   => array( 'enable_wishlist' ),
						'value'       => addonify_wishlist_get_option( 'after_add_to_wishlist_action' ),
						'choices'     => array(
							'none'                      => __( 'None', 'addonify-wishlist' ),
							'show_popup_notice'         => __( 'Show Popup Notice', 'addonify-wishlist' ),
							'redirect_to_wishlist_page' => __( 'Redirect to Wishlist Page', 'addonify-wishlist' ),
						),
					),
					'remove_from_wishlist_if_added_to_cart' => array(
						'type'        => 'switch',
						'className'   => '',
						'label'       => __( 'Remove product from wishlist after added to cart', 'addonify-wishlist' ),
						'description' => __( 'Remove product from wishlist if the product is added to cart.', 'addonify-wishlist' ),
						'dependent'   => array( 'enable_wishlist' ),
						'value'       => addonify_wishlist_get_option( 'remove_from_wishlist_if_added_to_cart' ),
					),
					'enable_wishlist_on_myaccount_page' => array(
						'type'        => 'switch',
						'className'   => '',
						'label'       => __( 'Enable wishlist tab on My account page', 'addonify-wishlist' ),
						'description' => __( 'Adds Wishlist tab and wishlist page link in my account page.', 'addonify-wishlist' ),
						'dependent'   => array( 'enable_wishlist' ),
						'value'       => addonify_wishlist_get_option( 'enable_wishlist_on_myaccount_page' ),
					),
				),
			),
			'style_options'   => array(
				'title'        => __( 'Style Options', 'addonify-wishlist' ),
				'type'         => 'sub_section',
				'sub_sections' => array(
					'custom_css' => array(
						'type'           => 'textarea',
						'className'      => 'custom-css-box fullwidth',
						'inputClassName' => 'custom-css-textarea',
						'label'          => __( 'Custom CSS', 'addonify-wishlist' ),
						'description'    => __( 'If required, add your custom CSS code here.', 'addonify-wishlist' ),
						'placeholder'    => '#app { color: blue; }',
						'width'          => 'full',
						'value'          => addonify_wishlist_get_option( 'custom_css' ),
					),
				),
			),
			'product_options' => array(
				'title'        => __( 'Product Options', 'addonify-wishlist' ),
				'type'         => 'sub_section',
				'sub_sections' => array(
					'product_in_stock_label'     => array(
						'type'      => 'text',
						'className' => '',
						'label'     => __( 'Product in stock label', 'addonify-wishlist' ),
						'dependent' => array( 'enable_wishlist' ),
						'value'     => addonify_wishlist_get_option( 'product_in_stock_label' ),
					),
					'product_out_of_stock_label' => array(
						'type'      => 'text',
						'className' => '',
						'label'     => __( 'Product out of stock label', 'addonify-wishlist' ),
						'dependent' => array( 'enable_wishlist' ),
						'value'     => addonify_wishlist_get_option( 'product_out_of_stock_label' ),
					),
				),
			),
		);
	}
}
add_filter( 'addonify_wishlist_general_v_2_options', 'addonify_wishlist_general_v_2_options' );
