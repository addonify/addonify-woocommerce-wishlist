<?php
/**
 * Define wishlist button settings fields of plugin.
 *
 * @link       https://addonify.com/
 * @since      2.0.0
 *
 * @package    Addonify_Wishlist
 * @subpackage Addonify_Wishlist/includes/setting-functions/fields
 */

if ( ! function_exists( 'addonify_wishlist_wishlist_button_v_2_options' ) ) {
	/**
	 * Wishlist button options.
	 *
	 * @return array
	 */
	function addonify_wishlist_wishlist_button_v_2_options() {
		return array(
			'wishlist_button_options' => array(
				'title'        => __( 'Wishlist Button Options', 'addonify-wishlist' ),
				'type'         => 'sub_section',
				'sub_sections' => array(
					'btn_position'                => array(
						'type'        => 'select',
						'className'   => '',
						'label'       => __( 'Add to Wishlist button position on product loop', 'addonify-wishlist' ),
						'description' => __( 'Choose where in the product loop of the product catalog you would like the "Add to Wishlist" button to be displayed.', 'addonify-wishlist' ),
						'choices'     => array(
							'none'               => __( 'None', 'addonify-wishlist' ),
							'after_add_to_cart'  => __( 'After Add to Cart Button', 'addonify-wishlist' ),
							'before_add_to_cart' => __( 'Before Add to Cart Button', 'addonify-wishlist' ),
							'over_image'         => esc_html__( 'Over Product Image', 'addonify-wishlist' ),
						),
						'dependent'   => array( 'enable_wishlist' ),
						'value'       => addonify_wishlist_get_option( 'btn_position' ),
					),
					'btn_position_on_single'      => array(
						'type'        => 'select',
						'className'   => '',
						'label'       => __( 'Add to Wishlist button position on product single', 'addonify-wishlist' ),
						'description' => __( 'Choose where in the add to cart form of product singble you would like the "Add to Wishlist" button to be displayed.', 'addonify-wishlist' ),
						'choices'     => array(
							'none'                      => __( 'None', 'addonify-wishlist' ),
							'before_add_to_cart_form'   => __( 'Before Add to Cart Form', 'addonify-wishlist' ),
							'before_add_to_cart_button' => __( 'Before Add to Cart Button', 'addonify-wishlist' ),
							'after_add_to_cart_button'  => __( 'After Add to Cart Button', 'addonify-wishlist' ),
							'after_add_to_cart_form'    => __( 'After Add to Cart Form', 'addonify-wishlist' ),
						),
						'dependent'   => array( 'enable_wishlist' ),
						'value'       => addonify_wishlist_get_option( 'btn_position_on_single' ),
					),
					'remove_already_added_product_from_wishlist' => array(
						'type'        => 'switch',
						'className'   => '',
						'label'       => __( 'Remove product from the wishlist on double click', 'addonify-wishlist' ),
						'description' => __( 'Enable the option to remove a product from the wishlist on double click on "Added to Wishlist" or "Already in Wishlist" buttons.', 'addonify-wishlist' ),
						'dependent'   => array( 'enable_wishlist' ),
						'value'       => addonify_wishlist_get_option( 'remove_already_added_product_from_wishlist' ),
					),
					'btn_custom_class'            => array(
						'type'        => 'text',
						'className'   => '',
						'placeholder' => 'my_button rounded_button',
						'label'       => __( 'Custom CSS class', 'addonify-wishlist' ),
						'badge'       => __( 'Optional', 'addonify-wishlist' ),
						'description' => __( 'Add custom CSS class or classes to "Add to Wishlist" button. If more than one CSS classes are to be added, separate CSS classes with a space.', 'addonify-wishlist' ),
						'dependent'   => array( 'enable_wishlist' ),
						'value'       => addonify_wishlist_get_option( 'btn_custom_class' ),
					),
					'wishlist_btn_bg_color'       => array(
						'type'          => 'color',
						'label'         => __( 'Background color', 'addonify-wishlist' ),
						'isAlphaPicker' => true,
						'className'     => '',
						'value'         => addonify_wishlist_get_option( 'wishlist_btn_bg_color' ),
					),
					'wishlist_btn_bg_color_hover' => array(
						'type'          => 'color',
						'label'         => __( 'On hover background color', 'addonify-wishlist' ),
						'isAlphaPicker' => true,
						'className'     => '',
						'value'         => addonify_wishlist_get_option( 'wishlist_btn_bg_color_hover' ),
					),
				),
			),
			'save_for_later'          => array(
				'title'        => __( 'Save for Later Button Options', 'addonify-wishlist' ),
				'type'         => 'sub_section',
				'sub_sections' => array(
					'enable_save_for_later'       => array(
						'label'       => __( 'Enable save for later button', 'addonify-wishlist' ),
						'description' => __( 'Enable the option to display the "Save for Later" button for products on the cart page.', 'addonify-wishlist' ),
						'type'        => 'switch',
						'className'   => '',
						'value'       => addonify_wishlist_get_option( 'enable_save_for_later' ),
					),
					'save_for_later_btn_label'    => array(
						'type'        => 'text',
						'className'   => '',
						'label'       => __( 'Button label', 'addonify-wishlist' ),
						'description' => __( 'Set the label for "Save for Later" button.', 'addonify-wishlist' ),
						'placeholder' => __( 'Save for Later', 'addonify-wishlist' ),
						'dependent'   => array( 'enable_wishlist' ),
						'value'       => addonify_wishlist_get_option( 'save_for_later_btn_label' ),
					),
					'save_for_later_btn_label_after_added_to_wishlist' => array(
						'type'        => 'text',
						'className'   => '',
						'label'       => __( 'Button label after added to wishlist', 'addonify-wishlist' ),
						'description' => __( 'Set the label for "Save for Later" button after the product is added to wishlist.', 'addonify-wishlist' ),
						'placeholder' => __( 'Saved for later', 'addonify-wishlist' ),
						'dependent'   => array( 'enable_wishlist' ),
						'value'       => addonify_wishlist_get_option( 'save_for_later_btn_label_after_added_to_wishlist' ),
					),
					'save_for_later_btn_position' => array(
						'type'        => 'select',
						'className'   => '',
						'label'       => __( 'Button position', 'addonify-wishlist' ),
						'description' => __( 'Choose where you want the "Save for Later" button to be displayed on the cart page.', 'addonify-wishlist' ),
						'choices'     => array(
							'after_product_name'     => __( 'After Product Name', 'addonify-wishlist' ),
							'after_product_subtotal' => __( 'After Product Subtotal', 'addonify-wishlist' ),
						),
						'dependent'   => array( 'enable_wishlist' ),
						'value'       => addonify_wishlist_get_option( 'save_for_later_btn_position' ),
					),
				),
			),
			'button_labels'           => array(
				'title'        => __( 'Button Label Options', 'addonify-wishlist' ),
				'type'         => 'sub_section',
				'sub_sections' => array(
					'btn_label'                        => array(
						'type'        => 'text',
						'className'   => '',
						'label'       => __( 'Add to Wishlist Button label', 'addonify-wishlist' ),
						'description' => __( 'Set the label for "Add to Wishlist" button.', 'addonify-wishlist' ),
						'dependent'   => array( 'enable_wishlist' ),
						'value'       => addonify_wishlist_get_option( 'btn_label' ),
					),
					'btn_label_when_added_to_wishlist' => array(
						'type'        => 'text',
						'className'   => '',
						'label'       => __( 'Added to Wishlist Button Label', 'addonify-wishlist' ),
						'description' => __( 'Set the label for "Added to Wishlist" button.', 'addonify-wishlist' ),
						'dependent'   => array( 'enable_wishlist' ),
						'value'       => addonify_wishlist_get_option( 'btn_label_when_added_to_wishlist' ),
					),
					'btn_label_if_added_to_wishlist'   => array(
						'type'        => 'text',
						'className'   => '',
						'label'       => __( 'Already in Wishlist Button Label', 'addonify-wishlist' ),
						'description' => __( 'Set the label for "Already in Wishlist" button.', 'addonify-wishlist' ),
						'dependent'   => array( 'enable_wishlist' ),
						'value'       => addonify_wishlist_get_option( 'btn_label_if_added_to_wishlist' ),
					),
					'wishlist_btn_text_color'          => array(
						'type'          => 'color',
						'label'         => __( 'Label color', 'addonify-wishlist' ),
						'isAlphaPicker' => true,
						'className'     => '',
						'value'         => addonify_wishlist_get_option( 'wishlist_btn_text_color' ),
					),
					'wishlist_btn_text_color_hover'    => array(
						'type'          => 'color',
						'label'         => __( 'On hover label color', 'addonify-wishlist' ),
						'isAlphaPicker' => true,
						'className'     => '',
						'value'         => addonify_wishlist_get_option( 'wishlist_btn_text_color_hover' ),
					),
				),
			),
			'button_icon'             => array(
				'title'        => __( 'Button Icon Options', 'addonify-wishlist' ),
				'type'         => 'sub_section',
				'sub_sections' => array(
					'show_icon'     => array(
						'type'        => 'switch',
						'className'   => '',
						'label'       => __( 'Display icon in button', 'addonify-wishlist' ),
						'description' => __( 'Enable the option to display icon on the "Add to Wishlist" button.', 'addonify-wishlist' ),
						'dependent'   => array( 'enable_wishlist' ),
						'value'       => addonify_wishlist_get_option( 'show_icon' ),
					),
					'icon_position' => array(
						'type'        => 'select',
						'className'   => '',
						'label'       => __( 'Button Icon Position', 'addonify-wishlist' ),
						'description' => __( 'Choose where to display the icon on the "Add to Wishlist" button.', 'addonify-wishlist' ),
						'choices'     => array(
							'left'  => __( 'Before Button Label', 'addonify-wishlist' ),
							'right' => __( 'After Button Label', 'addonify-wishlist' ),
						),
						'dependent'   => array( 'enable_wishlist', 'show_icon' ),
						'value'       => addonify_wishlist_get_option( 'icon_position' ),
					),
				),
			),
		);
	}
}

add_filter( 'addonify_wishlist_wishlist_button_v_2_options', 'addonify_wishlist_wishlist_button_v_2_options' );
