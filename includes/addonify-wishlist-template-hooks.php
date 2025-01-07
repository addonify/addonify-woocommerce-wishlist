<?php
/**
 * The file that adds the template actions.
 *
 * @link       https://www.addonify.com
 * @since      1.0.0
 *
 * @package    Addonify_Wishlist
 * @subpackage Addonify_Wishlist/includes
 */

/**
 * Add to wishlist button.
 *
 * @see addonify_wishlist_add_to_wishlist_button_template()
 */
add_action( 'addonify_wishlist_add_to_wishlist_button', 'addonify_wishlist_add_to_wishlist_button_template' );

/**
 * Wishlist sidebar.
 *
 * @see addonify_wishlist_sidebar_wishlist_toggle_button_template()
 * @see addonify_wishlist_sidebar_wishlist_content_template()
 * @see addonify_wishlist_sidebar_wishlist_products_template()
 */
add_action( 'addonify_wishlist_sidebar_wishlist_toggle_button', 'addonify_wishlist_sidebar_wishlist_toggle_button_template' );
add_action( 'addonify_wishlist_sidebar_wishlist_content', 'addonify_wishlist_sidebar_wishlist_content_template' );
add_action( 'addonify_wishlist_sidebar_products', 'addonify_wishlist_sidebar_wishlist_products_template' );

/**
 * Wishlist page.
 *
 * @see addonify_wishlist_page_wishlist_content_template()
 * @see addonify_wishlist_page_wishlist_products_template()
 */
add_action( 'addonify_wishlist_page_wishlist_content', 'addonify_wishlist_page_wishlist_content_template' );
add_action( 'addonify_wishlist_page_wishlist_products', 'addonify_wishlist_page_wishlist_products_template' );

/**
 * Wishlist modals.
 *
 * @see addonify_wishlist_modal_wrapper_start_template()
 * @see addonify_wishlist_modal_wrapper_end_template()
 * @see addonify_wishlist_modal_template()
 */
add_action( 'addonify_wishlist_modal_wrapper_start', 'addonify_wishlist_modal_wrapper_start_template' );
add_action( 'addonify_wishlist_modal_wrapper_end', 'addonify_wishlist_modal_wrapper_end_template' );

add_action( 'addonify_wishlist_added_to_wishlist_modal', 'addonify_wishlist_modal_template' );
add_action( 'addonify_wishlist_already_in_wishlist_modal', 'addonify_wishlist_modal_template' );
add_action( 'addonify_wishlist_removed_from_wishlist_modal', 'addonify_wishlist_modal_template' );
add_action( 'addonify_wishlist_confirm_clear_wishlist_modal', 'addonify_wishlist_modal_template' );
add_action( 'addonify_wishlist_success_modal', 'addonify_wishlist_modal_template' );
add_action( 'addonify_wishlist_error_modal', 'addonify_wishlist_modal_template' );
add_action( 'addonify_wishlist_login_required_modal', 'addonify_wishlist_modal_template' );
