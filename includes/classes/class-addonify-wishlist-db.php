<?php
/**
 * Class definition - Addonify_Wishlist_DB.
 *
 * Handles wishlist table existence check, table creation and deletion, and plugin options removal.
 *
 * @since 1.0.0
 *
 * @package    Addonify_Wishlist
 * @subpackage Addonify_Wishlist/includes/classes
 */

if ( ! class_exists( 'Addonify_Wishlist_DB' ) ) {
	/**
	 * Class - Addonify_Wishlist_DB
	 *
	 * @since 1.0.0
	 */
	class Addonify_Wishlist_DB {

		use Addonify_Wishlist_DB_Trait;

		/**
		 * Checks if wishlist table exists in the database.
		 *
		 * @since 1.0.0
		 */
		public static function table_exists() {

			global $wpdb;

			$table_name = self::get_table_name();
			$exists     = false;

			$wp_tables = $wpdb->get_results( 'show tables', ARRAY_A ); //phpcs:ignore

			foreach ( $wp_tables as $wp_table ) {
				foreach ( $wp_table as $key => $value ) {
					if ( $table_name === $value ) {
						$exists = true;
						break;
					}
				}
			}

			return $exists;
		}

		/**
		 * Creates wishlist table.
		 *
		 * @since 1.0.0
		 */
		public static function create_table() {

			global $wpdb;

			$table_name      = self::get_table_name();
			$charset_collate = $wpdb->get_charset_collate();

			$sql = "CREATE TABLE {$table_name}(
				id BIGINT NOT NULL AUTO_INCREMENT,
				user_id BIGINT NOT NULL,
				site_url VARCHAR(250) NOT NULL,
				wishlist_name VARCHAR(100) NULL,
				wishlist_visibility ENUM('public','shared','private') NULL,
				created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				parent_wishlist_id BIGINT NULL,
				product_id BIGINT NULL,
				share_key BIGINT NULL,
				PRIMARY KEY (id)
			) {$charset_collate};";

			if ( ! function_exists( 'dbDelta' ) ) {
				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			}

			dbDelta( $sql );
		}

		/**
		 * Deletes wishlist table.
		 *
		 * @since 1.0.0
		 */
		public static function delete_table() {

			global $wpdb;

			$table_name = self::get_table_name();

			$wpdb->query( "DROP TABLE IF EXISTS {$table_name}" ); // phpcs:ignore
		}

		/**
		 * Removes plugin's settings.
		 *
		 * @since 1.0.0
		 */
		public static function remove_settings() {

			global $wpdb;

			$wpdb->query( "delete from $wpdb->options where option_name regexp '" . ADDONIFY_WISHLIST_DB_INITIALS . ".*'" ); // phpcs:ignore
		}
	}
}
