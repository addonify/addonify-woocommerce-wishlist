<?php
/**
 * Class definition - Addonify_Wishlist_Actions.
 *
 * User's wishlist CRUD operations.
 *
 * @since 1.0.0
 *
 * @package    Addonify_Wishlist
 * @subpackage Addonify_Wishlist/includes/classes
 */

if ( ! class_exists( 'Addonify_Wishlist_Actions' ) ) {
	/**
	 * Class - Addonify_Wishlist_Actions
	 *
	 * @since 1.0.0
	 */
	class Addonify_Wishlist_Actions {

		use Addonify_Wishlist_DB_Trait;

		/**
		 * Stores current user id.
		 *
		 * @access protected
		 *
		 * @var int $user_id
		 */
		protected $user_id;

		/**
		 * Stores current site url.
		 *
		 * @access protected
		 *
		 * @var string $site_url
		 */
		protected $site_url;

		/**
		 * Initializes class properties.
		 *
		 * @since 1.0.0
		 *
		 * @param array $args Arguments.
		 */
		public function __construct( $args ) {

			$this->user_id  = isset( $args['user_id'] ) && ! empty( $args['user_id'] ) ? $args['user_id'] : get_current_user_id();
			$this->site_url = isset( $args['site_url'] ) && ! empty( $args['site_url'] ) ? $args['site_url'] : get_site_url();
		}

		/**
		 * Gets user's wishlists.
		 *
		 * @since 1.0.0
		 */
		public function get_wishlists() {

			$wishlists = []; // phpcs:ignore

			$get_wishlists = $this->get_rows(
				array(
					'fields' => [ // phpcs:ignore
						'id',
						'wishlist_name',
						'wishlist_visibility',
						'share_key',
						'created_at',
					], // phpcs:ignore
					'where'  => [ // phpcs:ignore
						'user_id'            => $this->user_id,
						'site_url'           => $this->site_url,
						'parent_wishlist_id' => null,
					],
				)
			);

			if ( is_array( $get_wishlists ) && ! empty( $get_wishlists ) ) {

				$wishlist_items = []; // phpcs:ignore

				foreach ( $get_wishlists as $wishlist ) {
					$wishlists[ (int) $wishlist->id ] = [ // phpcs:ignore
						'id'         => (int) $wishlist->id,
						'name'       => $wishlist->wishlist_name,
						'visibility' => $wishlist->wishlist_visibility,
						'key'        => (int) $wishlist->share_key,
						'created'    => $wishlist->created_at,
						'products'   => $this->get_wishlist_items( (int) $wishlist->id ),
					];
				}
			}

			return ! empty( $wishlists ) ? $wishlists : false;
		}

		/**
		 * Gets user's default wishlist.
		 *
		 * @since 1.0.0
		 */
		public function get_default_wishlist() {

			$default_wishlist = null; // phpcs:ignore

			$wishlists = $this->get_wishlists();

			if ( is_array( $wishlists ) && ! empty( $wishlists ) ) {

				$wishlist_ids = array_keys( $wishlists );

				$default_wishlist_id = min( $wishlist_ids );

				$default_wishlist = $wishlists[ $default_wishlist_id ];
			}

			return $default_wishlist ? $default_wishlist : false;
		}

		/**
		 * Gets a wishlist.
		 *
		 * @since 2.0.14
		 *
		 * @param mixed  $value Value of 'id' or 'share_key'.
		 * @param string $key 'id' or 'share_key'.
		 */
		public function get_wishlist( $value, $key = 'id' ) {

			if ( ! in_array( $key, [ 'id', 'share_key' ], true ) ) { // phpcs:ignore
				return false;
			}

			$get_wishlist = $this->get_row(
				array(
					'fields' => [ // phpcs:ignore
						'id',
						'wishlist_name',
						'wishlist_visibility',
						'share_key',
						'created_at',
					], // phpcs:ignore
					'where'  => [ // phpcs:ignore
						$key                 => $value,
						'parent_wishlist_id' => null,
					],
				)
			);

			if ( ! is_object( $get_wishlist ) ) {
				return false;
			}

			$wishlist = [
				'id'         => (int) $get_wishlist->id,
				'name'       => $get_wishlist->wishlist_name,
				'visibility' => $get_wishlist->wishlist_visibility,
				'key'        => (int) $get_wishlist->share_key,
				'created'    => $get_wishlist->created_at,
				'products'   => $this->get_wishlist_items( (int) $get_wishlist->id ),
			];

			return $wishlist;
		}

		/**
		 * Gets wishlist items.
		 *
		 * @since 1.0.0
		 *
		 * @param int $wishlist_id Wishlist ID.
		 */
		public function get_wishlist_items( $wishlist_id = 0 ) {

			if ( ! $wishlist_id > 0 ) {
				$default_wishlist = $this->get_default_wishlist();
				if ( $default_wishlist ) {
					$wishlist_id = $default_wishlist['id'];
				}
			}

			$products = []; // phpcs:ignore

			if ( $wishlist_id > 0 ) {

				$get_products = $this->get_rows(
					[ // phpcs:ignore
						'fields' => [ 'product_id' ], // phpcs:ignore
						'where'  => [ // phpcs:ignore
							'parent_wishlist_id' => $wishlist_id,
						],
					]
				);

				if ( is_array( $get_products ) && ! empty( $get_products ) ) {
					foreach ( $get_products as $get_product ) {
						$products[] = (int) $get_product->product_id;
					}
				}
			}

			return $products;
		}

		/**
		 * Checks if user has wishlist.
		 *
		 * @since 1.0.0
		 */
		public function has_wishlist() {

			$default_wishlist = $this->get_default_wishlist();

			return $default_wishlist ? true : false;
		}

		/**
		 * Creates wishlist.
		 *
		 * @since 1.0.0
		 *
		 * @param array $args Arguments.
		 * @return array|false Wishlist id on success. Else false.
		 */
		public function create_wishlist( $args = array() ) {

			$defaults = [
				'wishlist_name'       => apply_filters( 'addonify_wishlist_default_wishlist_name', esc_html__( 'My Wishlist', 'addonify-wishlist' ) ),
				'wishlist_visibility' => 'private',
				'user_id'             => $this->user_id,
				'site_url'            => $this->site_url,
				'share_key'           => (int) strrev( (string) time() ),
			];

			$args = wp_parse_args( $args, $defaults );

			$wishlist_id = $this->insert_row( $args );

			if ( $wishlist_id ) {

				$args['id'] = $wishlist_id;

				do_action( 'addonify_wishlist_wishlist_created', $this->user_id, $wishlist_id, $args );

				return $args;
			}

			return false;
		}

		/**
		 * Adds item into the wishlist.
		 *
		 * @since 1.0.0
		 *
		 * @param int $product_id  Product ID.
		 * @param int $wishlist_id Wishlist ID.
		 * @return boolean
		 */
		public function add_to_wishlist( $product_id, $wishlist_id = 0 ) {

			if ( ! $product_id > 0 ) {
				return false;
			}

			if ( ! $wishlist_id > 0 ) {
				$default_wishlist = $this->get_default_wishlist();
				if ( $default_wishlist ) {
					$wishlist_id = $default_wishlist['id'];
				}
			}

			if ( ! $wishlist_id > 0 ) {
				return false;
			}

			$add_to_wishlist = $this->insert_row(
				[ // phpcs:ignore
					'product_id'         => (int) $product_id,
					'user_id'            => $this->user_id,
					'site_url'           => $this->site_url,
					'parent_wishlist_id' => (int) $wishlist_id,
				]
			);

			return $add_to_wishlist ? true : false;
		}

		/**
		 * Removes item from the wishlist.
		 *
		 * @since 1.0.0
		 *
		 * @param int $product_id  Product ID.
		 * @param int $wishlist_id Wishlist ID.
		 * @return boolean
		 */
		public function remove_from_wishlist( $product_id, $wishlist_id = 0 ) {

			if ( ! $product_id > 0 ) {
				return false;
			}

			if ( ! $wishlist_id > 0 ) {
				$default_wishlist = $this->get_default_wishlist();
				if ( $default_wishlist ) {
					$wishlist_id = $default_wishlist['id'];
				}
			}

			if ( ! $wishlist_id > 0 ) {
				return false;
			}

			$remove_from_wishlist = $this->delete_where(
				[ // phpcs:ignore
					'user_id'            => $this->user_id,
					'site_url'           => $this->site_url,
					'product_id'         => (int) $product_id,
					'parent_wishlist_id' => (int) $wishlist_id,
				]
			);

			return $remove_from_wishlist ? true : false;
		}

		/**
		 * Removes wishlist and associated products from the wishlist table.
		 *
		 * @since 1.0.0
		 *
		 * @param int $wishlist_id Wishlist ID.
		 * @return boolean
		 */
		public function remove_wishlist( $wishlist_id ) {

			if ( ! $wishlist_id > 0 ) {
				return false;
			}

			$remove_products = $this->empty_wishlist( $wishlist_id );

			if ( ! $remove_products ) {
				return false;
			}

			$remove_wishlist = $this->delete_where(
				[
					'id' => $wishlist_id,
				]
			);

			return $remove_wishlist ? true : false;
		}

		/**
		 * Empties the wishlist.
		 *
		 * @since 1.0.0
		 *
		 * @param int $wishlist_id Wishlist ID.
		 * @return boolean
		 */
		public function empty_wishlist( $wishlist_id = 0 ) {

			if ( ! $wishlist_id > 0 ) {
				$default_wishlist = $this->get_default_wishlist();
				if ( $default_wishlist ) {
					$wishlist_id = $default_wishlist['id'];
				}
			}

			if ( ! $wishlist_id > 0 ) {
				return false;
			}

			$empty_wishlist = $this->delete_where(
				[ // phpcs:ignore
					'user_id'            => $this->user_id,
					'site_url'           => $this->site_url,
					'parent_wishlist_id' => (int) $wishlist_id,
				]
			);

			return is_integer( $empty_wishlist ) ? true : false;
		}

		/**
		 * Moves products to another wishlist.
		 *
		 * @since 1.0.0
		 *
		 * @param int   $wishlist_id Wishlist ID.
		 * @param array $products_ids Products IDs.
		 * @return boolean
		 */
		public function bulk_move_to_wishlist( $wishlist_id = 0, $products_ids = [] ) {

			if ( ! $wishlist_id > 0 ) {
				$default_wishlist = $this->get_default_wishlist();
				if ( $default_wishlist ) {
					$wishlist_id = $default_wishlist['id'];
				}
			}

			if ( ! $wishlist_id > 0 ) {
				return false;
			}

			if ( ! is_array( $products_ids ) || empty( $products_ids ) ) {
				return false;
			}

			$sql_query = "UPDATE {table_name} SET parent_wishlist_id = '{$wishlist_id}' WHERE product_id IN (" . implode( ', ', $products_ids ) . ") AND user_id = '{$this->user_id}' AND site_url = '{$this->site_url}';";

			$remove_from_wishlist = $this->query( $sql_query );

			return $remove_from_wishlist ? true : false;
		}

		/**
		 * Deletes products from the wishlist.
		 *
		 * @since 1.0.0
		 *
		 * @param int   $wishlist_id Wishlist ID.
		 * @param array $products_ids Products IDs.
		 * @return boolean
		 */
		public function bulk_remove_from_wishlist( $wishlist_id = 0, $products_ids = [] ) {

			if ( ! $wishlist_id > 0 ) {
				$default_wishlist = $this->get_default_wishlist();
				if ( $default_wishlist ) {
					$wishlist_id = $default_wishlist['id'];
				}
			}

			if ( ! $wishlist_id > 0 ) {
				return false;
			}

			if ( ! is_array( $products_ids ) || empty( $products_ids ) ) {
				return false;
			}

			$sql_query = 'DELETE FROM {table_name} WHERE product_id IN (' . implode( ', ', $products_ids ) . ") AND user_id = '{$this->user_id}' AND site_url = '{$this->site_url}' AND parent_wishlist_id = '{$wishlist_id}';";

			$remove_from_wishlist = $this->query( $sql_query );

			return $remove_from_wishlist ? true : false;
		}
	}
}
