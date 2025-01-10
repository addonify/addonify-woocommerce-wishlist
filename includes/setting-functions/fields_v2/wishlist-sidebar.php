<?php
/**
 * Define wishlist sidebar settings fields of plugin.
 *
 * @link       https://addonify.com/
 * @since      2.0.0
 *
 * @package    Addonify_Wishlist
 * @subpackage Addonify_Wishlist/includes/setting-functions/fields
 */

if ( ! function_exists( 'addonify_wishlist_wishlist_sidebar_v_2_options' ) ) {
	/**
	 * Wishlist sidebar options.
	 *
	 * @return array
	 */
	function addonify_wishlist_wishlist_sidebar_v_2_options() {
		return array(
			'wishlist_sidebar_options' => array(
				'title'        => __( 'Sidebar Options', 'addonify-wishlist' ),
				'type'         => 'sub_section',
				'sub_sections' => array(
					'show_sidebar'                         => array(
						'type'        => 'switch',
						'className'   => '',
						'label'       => __( 'Display sidebar', 'addonify-wishlist' ),
						'description' => __( 'Enable this option to display the sidebar where the wishlist is displayed.', 'addonify-wishlist' ),
						'dependent'   => array( 'enable_wishlist' ),
						'value'       => addonify_wishlist_get_option( 'show_sidebar' ),
					),
					'sidebar_position'                     => array(
						'type'        => 'select',
						'dependent'   => array( 'show_sidebar' ),
						'className'   => '',
						'label'       => __( 'Sidebar position', 'addonify-wishlist' ),
						'description' => __( 'Choose the position where the sidebar should be displayed.', 'addonify-wishlist' ),
						'choices'     => array(
							'left'  => __( 'Left', 'addonify-wishlist' ),
							'right' => __( 'Right', 'addonify-wishlist' ),
						),
						'dependent'   => array( 'enable_wishlist', 'show_sidebar' ),
						'value'       => addonify_wishlist_get_option( 'sidebar_position' ),
					),
					'sidebar_title'                        => array(
						'type'        => 'text',
						'className'   => '',
						'label'       => __( 'Sidebar title', 'addonify-wishlist' ),
						'description' => __( 'Set the title of the wishlist that is displayed inside the sidebar.', 'addonify-wishlist' ),
						'dependent'   => array( 'enable_wishlist', 'show_sidebar' ),
						'value'       => addonify_wishlist_get_option( 'sidebar_title' ),
					),
					'view_wishlist_page_button_label'      => array(
						'type'        => 'text',
						'className'   => '',
						'label'       => __( 'Wishlist page button link label', 'addonify-wishlist' ),
						'description' => __( 'Set the label for the button that links to the wishlist page.', 'addonify-wishlist' ),
						'dependent'   => array( 'enable_wishlist', 'show_sidebar' ),
						'value'       => addonify_wishlist_get_option( 'view_wishlist_page_button_label' ),
					),
					'sidebar_modal_view_wishlist_btn_label_color' => array(
						'type'          => 'color',
						'className'     => '',
						'label'         => __( 'Wishlist page button link label color', 'addonify-wishlist' ),
						'description'   => __( 'Set the color for label for the button that links to the wishlist page.', 'addonify-wishlist' ),
						'isAlphaPicker' => true,
						'dependent'     => array( 'enable_wishlist', 'show_sidebar' ),
						'value'         => addonify_wishlist_get_option( 'sidebar_modal_view_wishlist_btn_label_color' ),
					),
					'sidebar_modal_view_wishlist_btn_label_color_hover' => array(
						'type'          => 'color',
						'className'     => '',
						'label'         => __( 'Wishlist page button link label color on hover', 'addonify-wishlist' ),
						'description'   => __( 'Set the color for label on hover for the button that links to the wishlist page.', 'addonify-wishlist' ),
						'isAlphaPicker' => true,
						'dependent'     => array( 'enable_wishlist', 'show_sidebar' ),
						'value'         => addonify_wishlist_get_option( 'sidebar_modal_view_wishlist_btn_label_color_hover' ),
					),
					'sidebar_modal_view_wishlist_btn_bg_color' => array(
						'type'          => 'color',
						'className'     => '',
						'label'         => __( 'Wishlist page button link background color', 'addonify-wishlist' ),
						'description'   => __( 'Set the color for label for the button that links to the wishlist page.', 'addonify-wishlist' ),
						'isAlphaPicker' => true,
						'dependent'     => array( 'enable_wishlist', 'show_sidebar' ),
						'value'         => addonify_wishlist_get_option( 'sidebar_modal_view_wishlist_btn_bg_color' ),
					),
					'sidebar_modal_view_wishlist_btn_bg_color_hover' => array(
						'type'          => 'color',
						'className'     => '',
						'label'         => __( 'Wishlist page button link background color on hover', 'addonify-wishlist' ),
						'description'   => __( 'Set the color for label on hover for the button that links to the wishlist page.', 'addonify-wishlist' ),
						'isAlphaPicker' => true,
						'dependent'     => array( 'enable_wishlist', 'show_sidebar' ),
						'value'         => addonify_wishlist_get_option( 'sidebar_modal_view_wishlist_btn_bg_color_hover' ),
					),
					'sidebar_empty_wishlist_label'         => array(
						'type'        => 'text',
						'className'   => '',
						'label'       => __( 'Empty wishlist text', 'addonify-wishlist' ),
						'description' => __( 'Set the text to be displayed when there are no products in the wishlist.', 'addonify-wishlist' ),
						'dependent'   => array( 'enable_wishlist', 'show_sidebar' ),
						'value'       => addonify_wishlist_get_option( 'sidebar_empty_wishlist_label' ),
					),
					'sidebar_modal_overlay_bg_color'       => array(
						'type'          => 'color',
						'label'         => __( 'Sidebar overlay background color', 'addonify-wishlist' ),
						'isAlphaPicker' => true,
						'className'     => '',
						'value'         => addonify_wishlist_get_option( 'sidebar_modal_bg_color' ),
					),
					'sidebar_modal_bg_color'               => array(
						'type'          => 'color',
						'label'         => __( 'Sidebar background color', 'addonify-wishlist' ),
						'isAlphaPicker' => true,
						'className'     => '',
						'value'         => addonify_wishlist_get_option( 'sidebar_modal_bg_color' ),
					),
					'sidebar_modal_general_border_color'   => array(
						'type'          => 'color',
						'label'         => __( 'General border color', 'addonify-wishlist' ),
						'isAlphaPicker' => true,
						'className'     => '',
						'value'         => addonify_wishlist_get_option( 'sidebar_modal_bg_color' ),
					),
					'sidebar_modal_title_color'            => array(
						'type'          => 'color',
						'label'         => __( 'Title color', 'addonify-wishlist' ),
						'isAlphaPicker' => true,
						'className'     => '',
						'value'         => addonify_wishlist_get_option( 'sidebar_modal_title_color' ),
					),
					'sidebar_modal_empty_text_color'       => array(
						'type'          => 'color',
						'label'         => __( 'Empty wishlist text color', 'addonify-wishlist' ),
						'isAlphaPicker' => true,
						'className'     => '',
						'value'         => addonify_wishlist_get_option( 'sidebar_modal_empty_text_color' ),
					),
					'sidebar_modal_close_icon_color'       => array(
						'type'          => 'color',
						'label'         => __( 'Close icon color', 'addonify-wishlist' ),
						'isAlphaPicker' => true,
						'className'     => '',
						'value'         => addonify_wishlist_get_option( 'sidebar_modal_close_icon_color' ),
					),
					'sidebar_modal_close_icon_color_hover' => array(
						'type'          => 'color',
						'label'         => __( 'On hover close icon color', 'addonify-wishlist' ),
						'isAlphaPicker' => true,
						'className'     => '',
						'value'         => addonify_wishlist_get_option( 'sidebar_modal_close_icon_color_hover' ),
					),
				),
			),
			'sidebar_toggle_button'    => array(
				'title'        => __( 'Sidebar Toggle Button Options', 'addonify-wishlist' ),
				'type'         => 'sub_section',
				'sub_sections' => array(
					'sidebar_btn_label'                    => array(
						'type'        => 'text',
						'className'   => '',
						'label'       => __( 'Button label', 'addonify-wishlist' ),
						'description' => __( 'Set the label for the sidebar toggle button.', 'addonify-wishlist' ),
						'dependent'   => array( 'enable_wishlist', 'show_sidebar' ),
						'value'       => addonify_wishlist_get_option( 'sidebar_btn_label' ),
					),
					'sidebar_btn_position_offset'          => array(
						'type'          => 'number',
						'design'        => 'slider', // arrow, plus-minus & slider.
						'sliderInput'   => true, // Optional.
						'min'           => -300, // Optional.
						'max'           => 300, // Optional.
						'step'          => 1, // Optional.
						'sliderTipText' => 'px', // Optional.
						'width'         => 'full',
						'className'     => 'fullwidth', // Optional.
						'label'         => __( 'Button position offset', 'addonify-wishlist' ),
						'description'   => __( 'Set left and right position offset to the sidebar toggle button. The value is in px', 'addonify-wishlist' ),
						'dependent'     => array( 'enable_wishlist', 'show_sidebar' ),
						'value'         => addonify_wishlist_get_option( 'sidebar_btn_position_offset' ),
					),
					'sidebar_show_icon'                    => array(
						'type'        => 'switch',
						'label'       => __( 'Display button icon', 'addonify-wishlist' ),
						'description' => __( 'Enable this option to display an icon on the sidebar toggle button.', 'addonify-wishlist' ),
						'dependent'   => array( 'enable_wishlist', 'show_sidebar' ),
						'value'       => addonify_wishlist_get_option( 'sidebar_show_icon' ),
					),
					'sidebar_toggle_btn_icon'              => array(
						'type'        => 'radio',
						'design'      => 'radioIcons',
						'width'       => 'full',
						'className'   => 'radio-input-group',
						'label'       => __( 'Button icon', 'addonify-wishlist' ),
						'description' => __( 'Choose the icon to be displayed on the sidebar toggle button.', 'addonify-wishlist' ),
						'choices'     => addonify_wishlist_get_sidebar_icons(),
						'dependent'   => array( 'enable_wishlist', 'show_sidebar', 'sidebar_show_icon' ),
						'value'       => addonify_wishlist_get_option( 'sidebar_btn_icon' ),
					),
					'sidebar_modal_toggle_btn_label_color' => array(
						'type'          => 'color',
						'label'         => __( 'Button label color', 'addonify-wishlist' ),
						'isAlphaPicker' => true,
						'className'     => '',
						'value'         => addonify_wishlist_get_option( 'sidebar_modal_toggle_btn_label_color' ),
					),
					'sidebar_modal_toggle_btn_label_color_hover' => array(
						'type'          => 'color',
						'label'         => __( 'On hover button label color', 'addonify-wishlist' ),
						'isAlphaPicker' => true,
						'className'     => '',
						'value'         => addonify_wishlist_get_option( 'sidebar_modal_toggle_btn_label_color_hover' ),
					),
					'sidebar_modal_toggle_btn_bg_color'    => array(
						'type'          => 'color',
						'label'         => __( 'Button background color', 'addonify-wishlist' ),
						'isAlphaPicker' => true,
						'className'     => '',
						'value'         => addonify_wishlist_get_option( 'sidebar_modal_toggle_btn_bg_color' ),
					),
					'sidebar_modal_toggle_btn_bg_color_hover' => array(
						'type'          => 'color',
						'label'         => __( 'On hover button background color', 'addonify-wishlist' ),
						'isAlphaPicker' => true,
						'className'     => '',
						'value'         => addonify_wishlist_get_option( 'sidebar_modal_toggle_btn_bg_color_hover' ),
					),
				),
			),
			'sidebar_product_colors'   => array(
				'title'        => __( 'Sidebar Product Color Options', 'addonify-wishlist' ),
				'type'         => 'sub_section',
				'sub_sections' => array(
					'sidebar_modal_product_title_color' => array(
						'type'          => 'color',
						'label'         => __( 'Product name color', 'addonify-wishlist' ),
						'isAlphaPicker' => true,
						'className'     => '',
						'value'         => addonify_wishlist_get_option( 'sidebar_modal_product_title_color' ),
					),
					'sidebar_modal_product_title_color_hover' => array(
						'type'          => 'color',
						'label'         => __( 'On hover product name color', 'addonify-wishlist' ),
						'isAlphaPicker' => true,
						'className'     => '',
						'value'         => addonify_wishlist_get_option( 'sidebar_modal_product_title_color' ),
					),
					'sidebar_modal_product_regular_price_color' => array(
						'type'          => 'color',
						'label'         => __( 'Product regular price color', 'addonify-wishlist' ),
						'isAlphaPicker' => true,
						'className'     => '',
						'value'         => addonify_wishlist_get_option( 'sidebar_modal_product_regular_price_color' ),
					),
					'sidebar_modal_product_sale_price_color' => array(
						'type'          => 'color',
						'label'         => __( 'Product sale price color', 'addonify-wishlist' ),
						'isAlphaPicker' => true,
						'className'     => '',
						'value'         => addonify_wishlist_get_option( 'sidebar_modal_product_sale_price_color' ),
					),
					'sidebar_modal_in_stock_text_color' => array(
						'type'          => 'color',
						'label'         => __( 'Product in stock text color', 'addonify-wishlist' ),
						'isAlphaPicker' => true,
						'className'     => '',
						'value'         => addonify_wishlist_get_option( 'sidebar_modal_in_stock_text_color' ),
					),
					'sidebar_modal_out_of_stock_text_color' => array(
						'type'          => 'color',
						'label'         => __( 'Product out of stock text color', 'addonify-wishlist' ),
						'isAlphaPicker' => true,
						'className'     => '',
						'value'         => addonify_wishlist_get_option( 'sidebar_modal_out_of_stock_text_color' ),
					),
					'sidebar_modal_product_add_to_cart_label_color' => array(
						'type'          => 'color',
						'label'         => __( 'Add to Cart Button label color', 'addonify-wishlist' ),
						'isAlphaPicker' => true,
						'className'     => '',
						'value'         => addonify_wishlist_get_option( 'sidebar_modal_product_add_to_cart_label_color' ),
					),
					'sidebar_modal_product_add_to_cart_label_color_hover' => array(
						'type'          => 'color',
						'label'         => __( 'On hover Add to Cart Button label color', 'addonify-wishlist' ),
						'isAlphaPicker' => true,
						'className'     => '',
						'value'         => addonify_wishlist_get_option( 'sidebar_modal_product_add_to_cart_label_color_hover' ),
					),
					'sidebar_modal_product_add_to_cart_bg_color' => array(
						'type'          => 'color',
						'label'         => __( 'Add to Cart button background color', 'addonify-wishlist' ),
						'isAlphaPicker' => true,
						'className'     => '',
						'value'         => addonify_wishlist_get_option( 'sidebar_modal_product_add_to_cart_bg_color' ),
					),
					'sidebar_modal_product_add_to_cart_bg_color_hover' => array(
						'type'          => 'color',
						'label'         => __( 'On hover Add to Cart button background color', 'addonify-wishlist' ),
						'isAlphaPicker' => true,
						'className'     => '',
						'value'         => addonify_wishlist_get_option( 'sidebar_modal_product_add_to_cart_bg_color_hover' ),
					),
					'sidebar_modal_product_remove_from_wishlist_icon_color' => array(
						'type'          => 'color',
						'label'         => __( 'Remove icon color', 'addonify-wishlist' ),
						'isAlphaPicker' => true,
						'className'     => '',
						'value'         => addonify_wishlist_get_option( 'sidebar_modal_product_remove_from_wishlist_icon_color' ),
					),
					'sidebar_modal_product_remove_from_wishlist_icon_color_hover' => array(
						'type'          => 'color',
						'label'         => __( 'On hover remove icon color', 'addonify-wishlist' ),
						'isAlphaPicker' => true,
						'className'     => '',
						'value'         => addonify_wishlist_get_option( 'sidebar_modal_product_remove_from_wishlist_icon_color' ),
					),
				),
			),
		);
	}
}

add_filter( 'addonify_wishlist_wishlist_sidebar_v_2_options', 'addonify_wishlist_wishlist_sidebar_v_2_options' );
