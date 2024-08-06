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
						'key'        => $wishlist->share_key,
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
							'user_id'            => $this->user_id,
							'site_url'           => $this->site_url,
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
		 * @return int|false Wishlist id on success. Else false.
		 */
		public function create_wishlist() {

			$create_wishlist = $this->insert_row(
				[ // phpcs:ignore
					'wishlist_name'       => apply_filters( 'addonify_wishlist_default_wishlist_name', esc_html__( 'Default Wishlist', 'addonify-wishlist' ) ),
					'wishlist_visibility' => 'private',
					'user_id'             => $this->user_id,
					'site_url'            => $this->site_url,
					'share_key'           => (int) strrev( (string) time() ),
				]
			);

			if ( $create_wishlist ) {
				do_action( 'addonify_wishlist__wishlist_created', $this->user_id, $create_wishlist );
			}

			return $create_wishlist;
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

			return $empty_wishlist ? true : false;
		}
	}
}
