<?php
/**
 * Fired during plugin deactivation
 *
 * @link       https://www.addonify.com
 * @since      1.0.0
 *
 * @package    Addonify_Wishlist
 * @subpackage Addonify_Wishlist/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Addonify_Wishlist
 * @subpackage Addonify_Wishlist/includes
 * @author     Adodnify <contact@addonify.com>
 */
class Addonify_Wishlist_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since 1.0.0
	 */
	public static function deactivate() {

		if ( get_option( ADDONIFY_WISHLIST_DB_INITIALS . 'remove_all_plugin_data_on_uninstall', false ) ) {

			Addonify_Wishlist_DB::delete_table();
			Addonify_Wishlist_DB::remove_wishlist_options();

			delete_user_meta( get_current_user_id(), 'addonify_wishlist_default_wishlist' );
		}
	}
}
