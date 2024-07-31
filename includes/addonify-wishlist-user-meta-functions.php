<?php
/**
 * Definition of functions to store wishlist data in user's meta.
 *
 * @link       https://www.addonify.com
 * @since      1.0.0
 *
 * @package    Addonify_Wishlist
 * @subpackage Addonify_Wishlist/includes
 */

if ( ! function_exists( 'addonify_wishlist_get_user_default_wishlist_from_meta' ) ) {
	/**
	 * Gets user's default wishlist from the user meta.
	 *
	 * @since 2.0.6
	 *
	 * @param int $user_id User ID.
	 * @return int|boolean Wishlist ID if found. Else false.
	 */
	function addonify_wishlist_get_user_default_wishlist_from_meta( $user_id = 0 ) {

		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		if ( ! is_multisite() ) {
			return get_user_meta( $user_id, 'addonify_wishlist_default_wishlist', true );
		} else {
			$site_url = get_site_url();

			$user_wishlist_meta = wp_unslash( get_user_meta( $user_id, 'addonify_wishlist_default_wishlist', true ) );
			$user_wishlist_meta = json_decode( $user_wishlist_meta, true );

			if ( isset( $user_wishlist_meta[ $site_url ] ) && ! empty( $user_wishlist_meta[ $site_url ] ) ) {
				return $user_wishlist_meta[ $site_url ];
			}
		}

		return false;
	}
}


if ( ! function_exists( 'addonify_wishlist_set_user_default_wishlist_in_meta' ) ) {
	/**
	 * Sets user's default wishlist in the user meta.
	 *
	 * @since 2.0.6
	 *
	 * @param int $user_id User ID.
	 * @param int $wishlist_id Wishlist ID.
	 */
	function addonify_wishlist_set_user_default_wishlist_in_meta( $user_id = 0, $wishlist_id = 0 ) {

		if ( ! $wishlist_id ) {
			return;
		}

		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		$site_url = get_site_url();

		if ( ! is_multisite() ) {

			$user_default_wishlist = get_user_meta( $user_id, 'addonify_wishlist_default_wishlist', true );

			if ( ! $user_default_wishlist ) {
				update_user_meta( $user_id, 'addonify_wishlist_default_wishlist', $wishlist_id );
			}
		} else {

			$user_default_wishlist = wp_unslash( get_user_meta( $user_id, 'addonify_wishlist_default_wishlist', true ) );

			if ( ! $user_default_wishlist ) {
				$default_wishlists = array(
					$site_url => $wishlist_id,
				);

				update_user_meta( $user_id, 'addonify_wishlist_default_wishlist', wp_json_encode( $default_wishlists ) );
			} else {

				$user_default_wishlist = json_decode( $user_default_wishlist, true );

				if ( ! isset( $user_default_wishlist[ $site_url ] ) ) {
					$user_default_wishlist[ $site_url ] = $wishlist_id;
				}

				update_user_meta( $user_id, 'addonify_wishlist_default_wishlist', wp_json_encode( $user_default_wishlist ) );
			}
		}
	}

	add_action( 'addonify_wishlist__wishlist_created', 'addonify_wishlist_set_user_default_wishlist_in_meta', 10, 2 );
}


if ( ! function_exists( 'addonify_wishlist_update_user_default_wishlist_in_meta' ) ) {
	/**
	 * Gets user's default wishlist from the user meta.
	 *
	 * @since 2.0.6
	 *
	 * @param int $user_id User ID.
	 * @param int $wishlist_id Wishlist ID.
	 * @return boolean true if updated successfully. Else false.
	 */
	function addonify_wishlist_update_user_default_wishlist_in_meta( $user_id = 0, $wishlist_id = 0 ) {

		if ( ! $wishlist_id ) {
			return false;
		}

		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		if ( ! is_multisite() ) {
			return update_user_meta( $user_id, 'addonify_wishlist_default_wishlist', $wishlist_id );
		} else {
			$site_url = get_site_url();

			$user_wishlist_meta = wp_unslash( get_user_meta( $user_id, 'addonify_wishlist_default_wishlist', true ) );
			$user_wishlist_meta = json_decode( $user_wishlist_meta, true );

			$user_wishlist_meta[ $site_url ] = $wishlist_id;

			return update_user_meta( $user_id, 'addonify_wishlist_default_default', wp_json_encode( $user_wishlist_meta ) );
		}

		return false;
	}
}
