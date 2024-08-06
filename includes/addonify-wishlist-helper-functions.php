<?php
/**
 * The file that defines the helper functions.
 *
 * @link       https://www.addonify.com
 * @since      1.0.0
 *
 * @package    Addonify_Wishlist
 * @subpackage Addonify_Wishlist/includes
 */

if ( ! function_exists( 'addonify_wishlist_get_button_label' ) ) {

	/**
	 * Get wishlist button label.
	 * If $in_wishlist is true, then already in wishlist button label will be returned. Else, active wishlist button label  * will be returned.
	 *
	 * @since 1.0.0
	 *
	 * @param boolean $in_wishlist If the item is in wishlist.
	 * @return string
	 */
	function addonify_wishlist_get_button_label( $in_wishlist = false ) {

		return ( ! $in_wishlist ) ? addonify_wishlist_get_option( 'btn_label' ) : addonify_wishlist_get_option( 'btn_label_if_added_to_wishlist' );
	}
}


if ( ! function_exists( 'addonify_wishlist_get_wishlist_page_url' ) ) {

	/**
	 * Get wishlist page url.
	 *
	 * @since 1.0.0
	 *
	 * @return string|boolean $wishlist_page_url Wishlist page url if found else false.
	 */
	function addonify_wishlist_get_wishlist_page_url() {

		$wislist_page = get_option( ADDONIFY_WISHLIST_DB_INITIALS . 'wishlist_page' );

		if ( $wislist_page ) {
			return get_permalink( $wislist_page );
		} else {
			return false;
		}
	}
}


if ( ! function_exists( 'addonify_wishlist_reverse_num' ) ) {
	/**
	 * Get number reversed.
	 *
	 * @since 1.0.0
	 *
	 * @param int $num Any number.
	 *
	 * @return int Number reversed.
	 */
	function addonify_wishlist_reverse_num( $num ) {

		$num    = absint( $num );
		$revnum = 0;
		while ( $num > 1 ) {
			$rem    = $num % 10;
			$revnum = ( $revnum * 10 ) + $rem;
			$num    = ( $num / 10 );
		}
		return $revnum;
	}
}


if ( ! function_exists( 'addonify_wishlist_get_product_avaibility' ) ) {
	/**
	 * Get product's avaibility.
	 *
	 * @since 2.0.2
	 *
	 * @param object $product Product.
	 * @return array $product_avaibility Array containing avaibility class and label.
	 */
	function addonify_wishlist_get_product_avaibility( $product ) {

		if ( ! $product ) {
			return false;
		}

		$product_status     = $product->get_stock_status();
		$product_avaibility = array();

		switch ( $product_status ) {
			case 'instock':
				$product_avaibility['class']      = 'in-stock';
				$product_avaibility['avaibility'] = apply_filters(
					'addonify_wishlist_product_in_stock_label',
					addonify_wishlist_get_option( 'product_in_stock_label' )
				);
				break;
			case 'outofstock':
				$product_avaibility['class']      = 'out-of-stock';
				$product_avaibility['avaibility'] = apply_filters(
					'addonify_wishlist_product_out_of_stock_label',
					addonify_wishlist_get_option( 'product_out_of_stock_label' )
				);
				break;
			default:
		}

		return $product_avaibility;
	}
}


if ( ! function_exists( 'addonify_wishlist_get_wishlist_items' ) ) {
	/**
	 * Get items in the wishlist.
	 *
	 * @since 2.0.6
	 *
	 * @return array
	 */
	function addonify_wishlist_get_wishlist_items() {

		$wishlist_handler = new Addonify_Wishlist_Actions(
			[ // phpcs:ignore
				'user_id'  => get_current_user_id(),
				'site_url' => get_site_url(),
			]
		);

		return $wishlist_handler->get_wishlist_items();
	}
}


if ( ! function_exists( 'addonify_wishlist_get_wishlist_items_count' ) ) {
	/**
	 * Get the count of items in the wishlist.
	 *
	 * @since 1.0.0
	 *
	 * @return int Count of items.
	 */
	function addonify_wishlist_get_wishlist_items_count() {

		return count( addonify_wishlist_get_wishlist_items() );
	}
}


if ( ! function_exists( 'addonify_wishlist_is_product_in_wishlist' ) ) {
	/**
	 * Check if product is in wishlist.
	 *
	 * @since 1.0.0
	 *
	 * @param int $product_id Product ID.
	 * @return boolean True if product is in wishlist, false otherwise.
	 */
	function addonify_wishlist_is_product_in_wishlist( $product_id ) {

		$wishlist_items = addonify_wishlist_get_wishlist_items();

		return in_array( $product_id, $wishlist_items, true );
	}
}


if ( ! function_exists( 'addonify_wishlist_escape_svg' ) ) {
	/**
	 * Sanitizes SVG when rendering in the frontend.
	 *
	 * @since 2.0.6
	 *
	 * @param string $svg SVG code.
	 * @return string $svg Sanitized SVG code.
	 */
	function addonify_wishlist_escape_svg( $svg ) {

		$allowed_html = array(
			'svg'   => array(
				'class'           => true,
				'aria-hidden'     => true,
				'aria-labelledby' => true,
				'role'            => true,
				'xmlns'           => true,
				'width'           => true,
				'height'          => true,
				'viewbox'         => true,
			),
			'g'     => array(
				'fill' => true,
			),
			'title' => array(
				'title' => true,
			),
			'path'  => array(
				'd'    => true,
				'fill' => true,
			),
		);

		return wp_kses( $svg, $allowed_html );
	}
}


if ( ! function_exists( 'addonify_wishlist_get_wishlist_icons' ) ) {
	/**
	 * Gets and returns wishlist icon.
	 *
	 * @since 2.0.6.
	 *
	 * @param array $key Icon key.
	 */
	function addonify_wishlist_get_wishlist_icons( $key ) {

		$icons = array(
			// Heart icons (6 icons).
			'heart-1'   => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-heart-1" viewBox="0 0 24 24" width="24" height="24"><path d="M17.5,1.917a6.4,6.4,0,0,0-5.5,3.3,6.4,6.4,0,0,0-5.5-3.3A6.8,6.8,0,0,0,0,8.967c0,4.547,4.786,9.513,8.8,12.88a4.974,4.974,0,0,0,6.4,0C19.214,18.48,24,13.514,24,8.967A6.8,6.8,0,0,0,17.5,1.917Zm-3.585,18.4a2.973,2.973,0,0,1-3.83,0C4.947,16.006,2,11.87,2,8.967a4.8,4.8,0,0,1,4.5-5.05A4.8,4.8,0,0,1,11,8.967a1,1,0,0,0,2,0,4.8,4.8,0,0,1,4.5-5.05A4.8,4.8,0,0,1,22,8.967C22,11.87,19.053,16.006,13.915,20.313Z"/></svg>',
			'heart-2'   => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-heart-2" viewBox="0 0 24 24" width="24" height="24"><path d="M17.5,1.917a6.4,6.4,0,0,0-5.5,3.3,6.4,6.4,0,0,0-5.5-3.3A6.8,6.8,0,0,0,0,8.967c0,4.547,4.786,9.513,8.8,12.88a4.974,4.974,0,0,0,6.4,0C19.214,18.48,24,13.514,24,8.967A6.8,6.8,0,0,0,17.5,1.917Z"/></svg>',
			'heart-3'   => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-heart-3" width="24" height="24" viewBox="0 0 16 16"><path fill="currentColor" d="m8 6.236l-.894-1.789c-.222-.443-.607-1.08-1.152-1.595C5.418 2.345 4.776 2 4 2C2.324 2 1 3.326 1 4.92c0 1.211.554 2.066 1.868 3.37c.337.334.721.695 1.146 1.093C5.122 10.423 6.5 11.717 8 13.447c1.5-1.73 2.878-3.024 3.986-4.064c.425-.398.81-.76 1.146-1.093C14.446 6.986 15 6.131 15 4.92C15 3.326 13.676 2 12 2c-.777 0-1.418.345-1.954.852c-.545.515-.93 1.152-1.152 1.595L8 6.236zm.392 8.292a.513.513 0 0 1-.784 0c-1.601-1.902-3.05-3.262-4.243-4.381C1.3 8.208 0 6.989 0 4.92C0 2.755 1.79 1 4 1c1.6 0 2.719 1.05 3.404 2.008c.26.365.458.716.596.992a7.55 7.55 0 0 1 .596-.992C9.281 2.049 10.4 1 12 1c2.21 0 4 1.755 4 3.92c0 2.069-1.3 3.288-3.365 5.227c-1.193 1.12-2.642 2.48-4.243 4.38z"/></svg>',
			'heart-4'   => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-heart-4" width="24" height="24" viewBox="0 0 16 16"><path fill="currentColor" d="M4 1c2.21 0 4 1.755 4 3.92C8 2.755 9.79 1 12 1s4 1.755 4 3.92c0 3.263-3.234 4.414-7.608 9.608a.513.513 0 0 1-.784 0C3.234 9.334 0 8.183 0 4.92C0 2.755 1.79 1 4 1z"/></svg>',
			'heart-5'   => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-heart-5" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="m8.962 18.91l.464-.588l-.464.589ZM12 5.5l-.54.52l.01.011l.53-.53Zm3.038 13.41l.465.59l-.465-.59ZM13.47 8.03a.75.75 0 1 0 1.06-1.06l-1.06 1.06ZM9.426 18.322C7.91 17.127 6.253 15.96 4.938 14.48C3.65 13.028 2.75 11.334 2.75 9.137h-1.5c0 2.666 1.11 4.7 2.567 6.339c1.43 1.61 3.254 2.9 4.68 4.024l.93-1.178ZM2.75 9.137c0-2.15 1.215-3.954 2.874-4.713c1.612-.737 3.778-.541 5.836 1.597l1.08-1.04C10.1 2.444 7.264 2.025 5 3.06C2.786 4.073 1.25 6.425 1.25 9.137h1.5ZM8.497 19.5c.513.404 1.063.834 1.62 1.16c.557.325 1.193.59 1.883.59v-1.5c-.31 0-.674-.12-1.126-.385c-.453-.264-.922-.628-1.448-1.043L8.497 19.5Zm7.006 0c1.426-1.125 3.25-2.413 4.68-4.024c1.457-1.64 2.567-3.673 2.567-6.339h-1.5c0 2.197-.9 3.891-2.188 5.343c-1.315 1.48-2.972 2.647-4.488 3.842l.929 1.178ZM22.75 9.137c0-2.712-1.535-5.064-3.75-6.077c-2.264-1.035-5.098-.616-7.54 1.92l1.08 1.04c2.058-2.137 4.224-2.333 5.836-1.596c1.659.759 2.874 2.562 2.874 4.713h1.5Zm-8.176 9.185c-.526.415-.995.779-1.448 1.043c-.452.264-.816.385-1.126.385v1.5c.69 0 1.326-.265 1.883-.59c.558-.326 1.107-.756 1.62-1.16l-.929-1.178ZM11.47 6.032l2 1.998l1.06-1.06l-2-2l-1.06 1.061Z"/></svg>',
			'heart-6'   => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-heart-6" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M8.106 18.247C5.298 16.083 2 13.542 2 9.137C2 4.274 7.5.825 12 5.501l2 1.998a.75.75 0 0 0 1.06-1.06l-1.93-1.933C17.369 1.403 22 4.675 22 9.137c0 4.405-3.298 6.946-6.106 9.11c-.292.225-.579.445-.856.664C14 19.729 13 20.5 12 20.5s-2-.77-3.038-1.59c-.277-.218-.564-.438-.856-.663Z"/></svg>',
			// Check icons (4 icons).
			'check-1'   => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-check-1" width="24" height="24" viewBox="0 0 256 256"><path fill="currentColor" d="M172.24 99.76a6 6 0 0 1 0 8.48l-56 56a6 6 0 0 1-8.48 0l-24-24a6 6 0 0 1 8.48-8.48L112 151.51l51.76-51.75a6 6 0 0 1 8.48 0ZM230 128A102 102 0 1 1 128 26a102.12 102.12 0 0 1 102 102Zm-12 0a90 90 0 1 0-90 90a90.1 90.1 0 0 0 90-90Z"/></svg>',
			'check-2'   => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-check-2" width="24" height="24" viewBox="0 0 256 256"><path fill="currentColor" d="M128 24a104 104 0 1 0 104 104A104.11 104.11 0 0 0 128 24Zm45.66 85.66l-56 56a8 8 0 0 1-11.32 0l-24-24a8 8 0 0 1 11.32-11.32L112 148.69l50.34-50.35a8 8 0 0 1 11.32 11.32Z"/></svg>',
			'check-3'   => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-check-3" width="24" height="24" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5"><path stroke-linejoin="round" d="m8.5 12.5l2 2l5-5"/><path d="M7 3.338A9.954 9.954 0 0 1 12 2c5.523 0 10 4.477 10 10s-4.477 10-10 10S2 17.523 2 12c0-1.821.487-3.53 1.338-5"/></g></svg>',
			'check-4'   => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-check-4" width="24" height="24" viewBox="0 0 256 256"><path fill="currentColor" d="m148.2 84.28l-89.6 88a6 6 0 0 1-8.4 0l-38.4-37.71a6 6 0 1 1 8.4-8.57l34.2 33.58l85.4-83.87a6 6 0 1 1 8.4 8.56Zm96.08-8.48a6 6 0 0 0-8.48-.08l-85.4 83.87l-20.23-19.87a6 6 0 1 0-8.41 8.56l24.44 24a6 6 0 0 0 8.4 0l89.6-88a6 6 0 0 0 .08-8.48Z"/></svg>',
			// Close icons (3 icons).
			'close-1'   => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-close-1" width="24" height="24" viewBox="0 0 24 24"><g id="feClose0" fill="none" fill-rule="evenodd" stroke="none" stroke-width="1"><g id="feClose1" fill="currentColor"><path id="feClose2" d="M10.657 12.071L5 6.414L6.414 5l5.657 5.657L17.728 5l1.414 1.414l-5.657 5.657l5.657 5.657l-1.414 1.414l-5.657-5.657l-5.657 5.657L5 17.728z"/></g></g></svg>',
			'close-2'   => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-close-2" width="24" height="24" viewBox="0 0 20 20"><path fill="currentColor" d="M10 0c5.523 0 10 4.477 10 10s-4.477 10-10 10S0 15.523 0 10S4.477 0 10 0Zm0 1.395a8.605 8.605 0 1 0 0 17.21a8.605 8.605 0 0 0 0-17.21Zm2.207 5.442a.682.682 0 0 1 .963.964l-2.195 2.193l2.195 2.193a.682.682 0 0 1-.963.965l-2.197-2.195l-2.195 2.195a.682.682 0 0 1-.88.071l-.084-.072a.682.682 0 0 1 0-.964l2.195-2.193l-2.195-2.193a.682.682 0 1 1 .964-.964L10.01 9.03Z"/></svg>',
			'close-3'   => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-close-3" width="24" height="24" viewBox="0 0 20 20"><path fill="currentColor" d="M10 0c5.523 0 10 4.477 10 10s-4.477 10-10 10S0 15.523 0 10S4.477 0 10 0Zm2.207 6.837L10.01 9.03L7.815 6.837a.682.682 0 0 0-.88-.072l-.084.072a.682.682 0 0 0 0 .964l2.195 2.193l-2.195 2.193a.682.682 0 1 0 .964.965l2.195-2.195l2.197 2.195c.24.24.613.263.88.071l.084-.072a.682.682 0 0 0 0-.964l-2.196-2.193l2.195-2.193a.682.682 0 0 0-.963-.964Z"/></svg>',
			// Warning icons (2 icons).
			'warning-1' => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-warning-1" data-name="Layer 1" viewBox="0 0 24 24" width="24" height="24"><path d="M11,13V7c0-.55,.45-1,1-1s1,.45,1,1v6c0,.55-.45,1-1,1s-1-.45-1-1Zm1,2c-.83,0-1.5,.67-1.5,1.5s.67,1.5,1.5,1.5,1.5-.67,1.5-1.5-.67-1.5-1.5-1.5Zm11.58,4.88c-.7,1.35-2.17,2.12-4.01,2.12H4.44c-1.85,0-3.31-.77-4.01-2.12-.71-1.36-.51-3.1,.5-4.56L8.97,2.6c.71-1.02,1.83-1.6,3.03-1.6s2.32,.58,3,1.57l8.08,12.77c1.01,1.46,1.2,3.19,.49,4.54Zm-2.15-3.42s-.02-.02-.02-.04L13.34,3.67c-.29-.41-.79-.67-1.34-.67s-1.05,.26-1.36,.71L2.59,16.42c-.62,.88-.76,1.84-.4,2.53,.35,.68,1.15,1.05,2.24,1.05h15.12c1.09,0,1.89-.37,2.24-1.05,.36-.69,.22-1.65-.37-2.49Z"/></svg>',
			'warning-2' => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-warning-2" data-name="Layer 1" viewBox="0 0 24 24" width="24" height="24"><path d="M23.08,15.33L15,2.57c-.68-.98-1.81-1.57-3-1.57s-2.32,.58-3.03,1.6L.93,15.31c-1.02,1.46-1.21,3.21-.5,4.56,.7,1.35,2.17,2.12,4.01,2.12h15.12c1.85,0,3.31-.77,4.01-2.12,.7-1.35,.51-3.09-.49-4.54ZM11,7c0-.55,.45-1,1-1s1,.45,1,1v6c0,.55-.45,1-1,1s-1-.45-1-1V7Zm1,12c-.83,0-1.5-.67-1.5-1.5s.67-1.5,1.5-1.5,1.5,.67,1.5,1.5-.67,1.5-1.5,1.5Z"/></svg>',
			// Error icons (2 icons).
			'error-1'   => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-error-1" viewBox="0 0 24 24" width="24" height="24"><path d="M12,0A12,12,0,1,0,24,12,12.013,12.013,0,0,0,12,0Zm0,22A10,10,0,1,1,22,12,10.011,10.011,0,0,1,12,22Z"/><path d="M12,5a1,1,0,0,0-1,1v8a1,1,0,0,0,2,0V6A1,1,0,0,0,12,5Z"/><rect x="11" y="17" width="2" height="2" rx="1"/></svg>',
			'error-2'   => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-error-2" x="0px" y="0px" viewBox="0 0 512 512" xml:space="preserve" width="24" height="24"><g><path d="M256,512c141.385,0,256-114.615,256-256S397.385,0,256,0S0,114.615,0,256C0.153,397.322,114.678,511.847,256,512z    M234.667,128c0-11.782,9.551-21.333,21.333-21.333c11.782,0,21.333,9.551,21.333,21.333v170.667   c0,11.782-9.551,21.333-21.333,21.333c-11.782,0-21.333-9.551-21.333-21.333V128z M256,384c11.782,0,21.333,9.551,21.333,21.333   s-9.551,21.333-21.333,21.333c-11.782,0-21.333-9.551-21.333-21.333S244.218,384,256,384z"/></g></svg>',
			// Gear icon (3 icons).
			'gear-1'    => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-gear-1" width="24" height="24" viewBox="0 0 16 16"><g fill="currentColor"><path d="M8 4.754a3.246 3.246 0 1 0 0 6.492a3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0a2.246 2.246 0 0 1-4.492 0z"/><path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115l.094-.319z"/></g></svg>',
			'gear-2'    => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-gear-2" width="24" height="24" viewBox="0 0 16 16"><path fill="currentColor" d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872l-.1-.34zM8 10.93a2.929 2.929 0 1 1 0-5.86a2.929 2.929 0 0 1 0 5.858z"/></svg>',
			'gear-3'    => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-gear-3" width="24" height="24" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"><path d="M12 15a3 3 0 1 0 0-6a3 3 0 0 0 0 6Z"/><path d="m19.622 10.395l-1.097-2.65L20 6l-2-2l-1.735 1.483l-2.707-1.113L12.935 2h-1.954l-.632 2.401l-2.645 1.115L6 4L4 6l1.453 1.789l-1.08 2.657L2 11v2l2.401.655L5.516 16.3L4 18l2 2l1.791-1.46l2.606 1.072L11 22h2l.604-2.387l2.651-1.098C16.697 18.831 18 20 18 20l2-2l-1.484-1.75l1.098-2.652l2.386-.62V11l-2.378-.605Z"/></g></svg>',
			// Setting icon (2 icons).
			'setting-1' => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-setting-1" width="24" height="24" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="2"><path d="M19 3v4m0 14V11m-7-8v12m0 6v-2M5 3v2m0 16V9"/><circle cx="19" cy="9" r="2" transform="rotate(90 19 9)"/><circle cx="12" cy="17" r="2" transform="rotate(90 12 17)"/><circle cx="5" cy="7" r="2" transform="rotate(90 5 7)"/></g></svg>',
			'setting-2' => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-setting-2" width="24" height="24" viewBox="0 0 14 14"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><circle cx="2" cy="2" r="1.5"/><path d="M3.5 2h10"/><circle cx="7" cy="7" r="1.5"/><path d="M.5 7h5m3 0h5"/><circle cx="12" cy="12" r="1.5"/><path d="M10.5 12H.5"/></g></svg>',
			// Bell icon (4 icons).
			'bell-1'    => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-bell-1" width="24" height="24" viewBox="0 0 256 256"><path fill="currentColor" d="M224 71.1a8 8 0 0 1-10.78-3.42a94.13 94.13 0 0 0-33.46-36.91a8 8 0 1 1 8.54-13.54a111.46 111.46 0 0 1 39.12 43.09A8 8 0 0 1 224 71.1ZM35.71 72a8 8 0 0 0 7.1-4.32a94.13 94.13 0 0 1 33.46-36.91a8 8 0 1 0-8.54-13.54a111.46 111.46 0 0 0-39.12 43.09A8 8 0 0 0 35.71 72Zm186.1 103.94A16 16 0 0 1 208 200h-40.8a40 40 0 0 1-78.4 0H48a16 16 0 0 1-13.79-24.06C43.22 160.39 48 138.28 48 112a80 80 0 0 1 160 0c0 26.27 4.78 48.38 13.81 63.94ZM150.62 200h-45.24a24 24 0 0 0 45.24 0ZM208 184c-10.64-18.27-16-42.49-16-72a64 64 0 0 0-128 0c0 29.52-5.38 53.74-16 72Z"/></svg>',
			'bell-2'    => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-bell-2" width="24" height="24" viewBox="0 0 256 256"><path fill="currentColor" d="M224 71.1a8 8 0 0 1-10.78-3.42a94.13 94.13 0 0 0-33.46-36.91a8 8 0 1 1 8.54-13.54a111.46 111.46 0 0 1 39.12 43.09A8 8 0 0 1 224 71.1ZM35.71 72a8 8 0 0 0 7.1-4.32a94.13 94.13 0 0 1 33.46-36.91a8 8 0 1 0-8.54-13.54a111.46 111.46 0 0 0-39.12 43.09A8 8 0 0 0 35.71 72Zm186.1 103.94A16 16 0 0 1 208 200h-40.8a40 40 0 0 1-78.4 0H48a16 16 0 0 1-13.79-24.06C43.22 160.39 48 138.28 48 112a80 80 0 0 1 160 0c0 26.27 4.78 48.38 13.81 63.94ZM150.62 200h-45.24a24 24 0 0 0 45.24 0Z"/></svg>',
			'bell-3'    => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-bell-3" width="24" height="24" viewBox="0 0 16 16"><path fill="currentColor" d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zM8 1.918l-.797.161A4.002 4.002 0 0 0 4 6c0 .628-.134 2.197-.459 3.742c-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4.002 4.002 0 0 0-3.203-3.92L8 1.917zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5.002 5.002 0 0 1 13 6c0 .88.32 4.2 1.22 6z"/></svg>',
			'bell-4'    => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-bell-4" width="24" height="24" viewBox="0 0 16 16"><path fill="currentColor" d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zm.995-14.901a1 1 0 1 0-1.99 0A5.002 5.002 0 0 0 3 6c0 1.098-.5 6-2 7h14c-1.5-1-2-5.902-2-7c0-2.42-1.72-4.44-4.005-4.901z"/></svg>',
			// Bell icon (4 icons).
			'bin-1'     => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-bell-1" width="24" height="24" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="1.5" d="M20.5 6h-17m15.333 2.5l-.46 6.9c-.177 2.654-.265 3.981-1.13 4.79c-.865.81-2.196.81-4.856.81h-.774c-2.66 0-3.991 0-4.856-.81c-.865-.809-.954-2.136-1.13-4.79l-.46-6.9M9.17 4a3.001 3.001 0 0 1 5.66 0"/></svg>',
			'bin-2'     => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-bin-2" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M3 6.524c0-.395.327-.714.73-.714h4.788c.006-.842.098-1.995.932-2.793A3.68 3.68 0 0 1 12 2a3.68 3.68 0 0 1 2.55 1.017c.834.798.926 1.951.932 2.793h4.788c.403 0 .73.32.73.714a.722.722 0 0 1-.73.714H3.73A.722.722 0 0 1 3 6.524ZM11.607 22h.787c2.707 0 4.06 0 4.94-.863c.881-.863.971-2.28 1.151-5.111l.26-4.08c.098-1.537.146-2.306-.295-2.792c-.442-.487-1.188-.487-2.679-.487H8.23c-1.491 0-2.237 0-2.679.487c-.441.486-.392 1.255-.295 2.791l.26 4.08c.18 2.833.27 4.249 1.15 5.112C7.545 22 8.9 22 11.607 22Z"/></svg>',
			'bin-3'     => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-bin-3" width="24" height="24" viewBox="0 0 256 256"><path fill="currentColor" d="M216 50H40a6 6 0 0 0 0 12h10v146a14 14 0 0 0 14 14h128a14 14 0 0 0 14-14V62h10a6 6 0 0 0 0-12Zm-22 158a2 2 0 0 1-2 2H64a2 2 0 0 1-2-2V62h132ZM82 24a6 6 0 0 1 6-6h80a6 6 0 0 1 0 12H88a6 6 0 0 1-6-6Z"/></svg>',
			'bin-4'     => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-bin-4" width="24" height="24" viewBox="0 0 256 256"><path fill="currentColor" d="M224 56a8 8 0 0 1-8 8h-8v144a16 16 0 0 1-16 16H64a16 16 0 0 1-16-16V64h-8a8 8 0 0 1 0-16h176a8 8 0 0 1 8 8ZM88 32h80a8 8 0 0 0 0-16H88a8 8 0 0 0 0 16Z"/></svg>',
			'bin-5'     => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-bin-5" width="24" height="24" viewBox="0 0 256 256"><path fill="currentColor" d="M216 50h-42V40a22 22 0 0 0-22-22h-48a22 22 0 0 0-22 22v10H40a6 6 0 0 0 0 12h10v146a14 14 0 0 0 14 14h128a14 14 0 0 0 14-14V62h10a6 6 0 0 0 0-12ZM94 40a10 10 0 0 1 10-10h48a10 10 0 0 1 10 10v10H94Zm100 168a2 2 0 0 1-2 2H64a2 2 0 0 1-2-2V62h132Zm-84-104v64a6 6 0 0 1-12 0v-64a6 6 0 0 1 12 0Zm48 0v64a6 6 0 0 1-12 0v-64a6 6 0 0 1 12 0Z"/></svg>',
			'bin-6'     => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-bin-6" width="24" height="24" viewBox="0 0 256 256"><path fill="currentColor" d="M216 48h-40v-8a24 24 0 0 0-24-24h-48a24 24 0 0 0-24 24v8H40a8 8 0 0 0 0 16h8v144a16 16 0 0 0 16 16h128a16 16 0 0 0 16-16V64h8a8 8 0 0 0 0-16ZM112 168a8 8 0 0 1-16 0v-64a8 8 0 0 1 16 0Zm48 0a8 8 0 0 1-16 0v-64a8 8 0 0 1 16 0Zm0-120H96v-8a8 8 0 0 1 8-8h48a8 8 0 0 1 8 8Z"/></svg>',
			// Spinner icon (2 icons).
			'spinner-1' => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-spinner-1" width="24" height="24" viewBox="0 0 256 256"><path fill="currentColor" d="M136 32v32a8 8 0 0 1-16 0V32a8 8 0 0 1 16 0Zm37.25 58.75a8 8 0 0 0 5.66-2.35l22.63-22.62a8 8 0 0 0-11.32-11.32L167.6 77.09a8 8 0 0 0 5.65 13.66ZM224 120h-32a8 8 0 0 0 0 16h32a8 8 0 0 0 0-16Zm-45.09 47.6a8 8 0 0 0-11.31 11.31l22.62 22.63a8 8 0 0 0 11.32-11.32ZM128 184a8 8 0 0 0-8 8v32a8 8 0 0 0 16 0v-32a8 8 0 0 0-8-8Zm-50.91-16.4l-22.63 22.62a8 8 0 0 0 11.32 11.32l22.62-22.63a8 8 0 0 0-11.31-11.31ZM72 128a8 8 0 0 0-8-8H32a8 8 0 0 0 0 16h32a8 8 0 0 0 8-8Zm-6.22-73.54a8 8 0 0 0-11.32 11.32L77.09 88.4A8 8 0 0 0 88.4 77.09Z"/></svg>',
			'spinner-2' => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-spinner-2" width="24" height="24" viewBox="0 0 256 256"><path fill="currentColor" d="M140 32v32a12 12 0 0 1-24 0V32a12 12 0 0 1 24 0Zm33.25 62.75a12 12 0 0 0 8.49-3.52l22.63-22.63a12 12 0 0 0-17-17l-22.6 22.66a12 12 0 0 0 8.48 20.49ZM224 116h-32a12 12 0 0 0 0 24h32a12 12 0 0 0 0-24Zm-42.26 48.77a12 12 0 1 0-17 17l22.63 22.63a12 12 0 0 0 17-17ZM128 180a12 12 0 0 0-12 12v32a12 12 0 0 0 24 0v-32a12 12 0 0 0-12-12Zm-53.74-15.23L51.63 187.4a12 12 0 0 0 17 17l22.63-22.63a12 12 0 1 0-17-17ZM76 128a12 12 0 0 0-12-12H32a12 12 0 0 0 0 24h32a12 12 0 0 0 12-12Zm-7.4-76.37a12 12 0 1 0-17 17l22.66 22.6a12 12 0 0 0 17-17Z"/></svg>',
			// Edit icon (2 icons).
			'edit-1'    => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-edit-1" width="24" height="24" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" d="M22 10.5V12c0 4.714 0 7.071-1.465 8.535C19.072 22 16.714 22 12 22s-7.071 0-8.536-1.465C2 19.072 2 16.714 2 12s0-7.071 1.464-8.536C4.93 2 7.286 2 12 2h1.5" opacity=".5"/><path d="m17.3 2.806l-.648.65l-5.965 5.964c-.404.404-.606.606-.78.829c-.205.262-.38.547-.524.848c-.121.255-.211.526-.392 1.068L8.412 13.9l-.374 1.123a.742.742 0 0 0 .94.939l1.122-.374l1.735-.579c.542-.18.813-.27 1.068-.392c.301-.144.586-.32.848-.524c.223-.174.425-.376.83-.78l5.964-5.965l.649-.649A2.753 2.753 0 0 0 17.3 2.806Z"/><path d="M16.652 3.455s.081 1.379 1.298 2.595c1.216 1.217 2.595 1.298 2.595 1.298M10.1 15.588L8.413 13.9" opacity=".5"/></g></svg>',
			'edit-2'    => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-edit-2" width="24" height="24" viewBox="0 0 24 24"><g fill="currentColor"><path d="M21.194 2.806a2.753 2.753 0 0 1 0 3.893l-.496.496a4.61 4.61 0 0 1-.533-.151a5.19 5.19 0 0 1-1.968-1.241a5.19 5.19 0 0 1-1.241-1.968a4.613 4.613 0 0 1-.15-.533l.495-.496a2.753 2.753 0 0 1 3.893 0ZM14.58 13.313c-.404.404-.606.606-.829.78a4.59 4.59 0 0 1-.848.524c-.255.121-.526.211-1.068.392l-2.858.953a.742.742 0 0 1-.939-.94l.953-2.857c.18-.542.27-.813.392-1.068c.144-.301.32-.586.524-.848c.174-.223.376-.425.78-.83l4.916-4.915a6.7 6.7 0 0 0 1.533 2.36a6.702 6.702 0 0 0 2.36 1.533l-4.916 4.916Z"/><path d="M20.535 20.535C22 19.072 22 16.714 22 12c0-1.548 0-2.842-.052-3.934l-6.362 6.362c-.351.352-.615.616-.912.847a6.08 6.08 0 0 1-1.125.696c-.34.162-.694.28-1.166.437l-2.932.977a2.242 2.242 0 0 1-2.836-2.836l.977-2.932c.157-.472.275-.826.437-1.166c.19-.399.424-.776.696-1.125c.231-.297.495-.56.847-.912l6.362-6.362C14.842 2 13.548 2 12 2C7.286 2 4.929 2 3.464 3.464C2 4.93 2 7.286 2 12c0 4.714 0 7.071 1.464 8.535C4.93 22 7.286 22 12 22c4.714 0 7.071 0 8.535-1.465Z"/></g></svg>',
			// Eye icon (4 icons).
			'eye-1'     => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-eye-1" width="24" height="24" viewBox="0 0 32 32"><path fill="currentColor" d="M30.94 15.66A16.69 16.69 0 0 0 16 5A16.69 16.69 0 0 0 1.06 15.66a1 1 0 0 0 0 .68A16.69 16.69 0 0 0 16 27a16.69 16.69 0 0 0 14.94-10.66a1 1 0 0 0 0-.68ZM16 25c-5.3 0-10.9-3.93-12.93-9C5.1 10.93 10.7 7 16 7s10.9 3.93 12.93 9C26.9 21.07 21.3 25 16 25Z"/><path fill="currentColor" d="M16 10a6 6 0 1 0 6 6a6 6 0 0 0-6-6Zm0 10a4 4 0 1 1 4-4a4 4 0 0 1-4 4Z"/></svg>',
			'eye-2'     => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-eye-2" width="24" height="24" viewBox="0 0 32 32"><circle cx="16" cy="16" r="4" fill="currentColor"/><path fill="currentColor" d="M30.94 15.66A16.69 16.69 0 0 0 16 5A16.69 16.69 0 0 0 1.06 15.66a1 1 0 0 0 0 .68A16.69 16.69 0 0 0 16 27a16.69 16.69 0 0 0 14.94-10.66a1 1 0 0 0 0-.68ZM16 22.5a6.5 6.5 0 1 1 6.5-6.5a6.51 6.51 0 0 1-6.5 6.5Z"/></svg>',
			'eye-3'     => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-eye-3" width="24" height="24" viewBox="0 0 32 32"><path fill="currentColor" d="m5.24 22.51l1.43-1.42A14.06 14.06 0 0 1 3.07 16C5.1 10.93 10.7 7 16 7a12.38 12.38 0 0 1 4 .72l1.55-1.56A14.72 14.72 0 0 0 16 5A16.69 16.69 0 0 0 1.06 15.66a1 1 0 0 0 0 .68a16 16 0 0 0 4.18 6.17Z"/><path fill="currentColor" d="M12 15.73a4 4 0 0 1 3.7-3.7l1.81-1.82a6 6 0 0 0-7.33 7.33zm18.94-.07a16.4 16.4 0 0 0-5.74-7.44L30 3.41L28.59 2L2 28.59L3.41 30l5.1-5.1A15.29 15.29 0 0 0 16 27a16.69 16.69 0 0 0 14.94-10.66a1 1 0 0 0 0-.68zM20 16a4 4 0 0 1-6 3.44L19.44 14a4 4 0 0 1 .56 2zm-4 9a13.05 13.05 0 0 1-6-1.58l2.54-2.54a6 6 0 0 0 8.35-8.35l2.87-2.87A14.54 14.54 0 0 1 28.93 16C26.9 21.07 21.3 25 16 25z"/></svg>',
			'eye-4'     => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-eye-4" width="24" height="24" viewBox="0 0 32 32"><path fill="currentColor" d="M30.94 15.66a16.4 16.4 0 0 0-5.73-7.45L30 3.41L28.59 2L2 28.59L3.41 30l5.1-5.09A15.38 15.38 0 0 0 16 27a16.69 16.69 0 0 0 14.94-10.66a1 1 0 0 0 0-.68zM16 22.5a6.46 6.46 0 0 1-3.83-1.26L14 19.43A4 4 0 0 0 19.43 14l1.81-1.81A6.49 6.49 0 0 1 16 22.5zm-11.47-.69l5-5A6.84 6.84 0 0 1 9.5 16A6.51 6.51 0 0 1 16 9.5a6.84 6.84 0 0 1 .79.05l3.78-3.77A14.39 14.39 0 0 0 16 5A16.69 16.69 0 0 0 1.06 15.66a1 1 0 0 0 0 .68a15.86 15.86 0 0 0 3.47 5.47z"/></svg>',
			// Bolt/Flash icon (2 icons).
			'bolt-1'    => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-bolt-1" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M14.5 4h.005M14.5 4L12 10l5 2.898L9.5 20l2.5-6l-5-2.9L14.5 4m0-2a2.024 2.024 0 0 0-1.379.551L5.624 9.646a1.998 1.998 0 0 0-.61 1.686c.072.626.437 1.182.982 1.498l3.482 2.021l-1.826 4.381a2.003 2.003 0 0 0 1.847 2.77c.498 0 .993-.186 1.375-.548l7.5-7.103a1.995 1.995 0 0 0 .61-1.685a1.999 1.999 0 0 0-.982-1.498L14.52 9.15l1.789-4.293A2 2 0 0 0 14.5 2z"/></svg>',
			'bolt-2'    => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-bolt-2" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="m17.502 12.033l-4.241-2.458l2.138-5.131A1.003 1.003 0 0 0 14.505 3a1.004 1.004 0 0 0-.622.214l-.07.06l-7.5 7.1a1.002 1.002 0 0 0 .185 1.592l4.242 2.46l-2.163 5.19a.999.999 0 0 0 1.611 1.11l7.5-7.102a1.002 1.002 0 0 0-.186-1.591z"/></svg>',
			// Login icon (2 icons).
			'login-1'   => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-login-1" width="24" height="24" viewBox="0 0 20 20"><path fill="currentColor" d="M9.76 0C15.417 0 20 4.477 20 10S15.416 20 9.76 20c-3.191 0-6.142-1.437-8.07-3.846a.644.644 0 0 1 .115-.918a.68.68 0 0 1 .94.113a8.96 8.96 0 0 0 7.016 3.343c4.915 0 8.9-3.892 8.9-8.692c0-4.8-3.985-8.692-8.9-8.692a8.961 8.961 0 0 0-6.944 3.255a.68.68 0 0 1-.942.101a.644.644 0 0 1-.103-.92C3.703 1.394 6.615 0 9.761 0Zm.545 6.862l2.707 2.707c.262.262.267.68.011.936L10.38 13.15a.662.662 0 0 1-.937-.011a.662.662 0 0 1-.01-.937l1.547-1.548l-10.31.001A.662.662 0 0 1 0 10c0-.361.3-.654.67-.654h10.268L9.38 7.787a.662.662 0 0 1-.01-.937a.662.662 0 0 1 .935.011Z"/></svg>',
			'login-2'   => '<svg xmlns="http://www.w3.org/2000/svg" class="adfy-wishlist-icon addonify-wishlist-icon icon-login-2" width="24" height="24" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4m-5-4l5-5l-5-5m5 5H3"/></svg>',
		);

		return isset( $icons[ $key ] ) ? $icons[ $key ] : false;
	}
}
