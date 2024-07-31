<?php
/**
 * Fired during plugin activation
 *
 * @link       https://www.addonify.com
 * @since      1.0.0
 *
 * @package    Addonify_Wishlist
 * @subpackage Addonify_Wishlist/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Addonify_Wishlist
 * @subpackage Addonify_Wishlist/includes
 * @author     Adodnify <contact@addonify.com>
 */
class Addonify_Wishlist_Activator {

	/**
	 * Tasks that needs to be done during plugin activation.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		require_once ADDONIFY_WISHLIST_PLUGIN_PATH . '/includes/classes/addonify-wishlist-db-trait.php';

		require_once ADDONIFY_WISHLIST_PLUGIN_PATH . '/includes/classes/class-addonify-wishlist-db.php';

		if ( ! Addonify_Wishlist_DB::table_exists() ) {
			Addonify_Wishlist_DB::create_table();
		}

		self::create_wishlist_page();

		set_transient( 'addonify_wishlist_ask_for_review_transient', '1', 5 * DAY_IN_SECONDS );
	}

	/**
	 * Create wishlist page.
	 */
	private static function create_wishlist_page() {

		// create page only once.
		// do not regenerate even if plugin is deleted by user.

		if ( get_option( ADDONIFY_WISHLIST_DB_INITIALS . 'wishlist_page' ) ) {
			return;
		}

		$args = array(
			'pagename' => esc_html__( 'Wishlist', 'addonify-wishlist' ),
		);

		$query = new WP_Query( $args );
		if ( $query->have_posts() ) {
			update_option( ADDONIFY_WISHLIST_DB_INITIALS . 'wishlist_page', $query->post->ID );
			return;
		}

		// Create page object.
		$new_page = array(
			'post_title'   => esc_html__( 'Wishlist', 'addonify-wishlist' ),
			'post_content' => '[addonify_wishlist]',
			'post_status'  => 'publish',
			'post_author'  => get_current_user_id(),
			'post_type'    => 'page',
		);

		// Insert the post into the database.
		$page_id = wp_insert_post( $new_page );

		update_option( ADDONIFY_WISHLIST_DB_INITIALS . 'wishlist_page', $page_id );
	}
}
