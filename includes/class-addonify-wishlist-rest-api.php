<?php
/**
 * The class to define REST API endpoints used in settings page.
 *
 * This is used to define REST API endpoints used in admin settings page to get and update settings values.
 *
 * @since      1.0.7
 * @package    Addonify_Wishlist
 * @subpackage Addonify_Wishlist/includes
 * @author     Addonify <contact@addonify.com>
 */

if ( ! class_exists( 'Addonify_Wishlist_Rest_API' ) ) {
	/**
	 * Register rest api.
	 *
	 * @package    Addonify_Wishlist
	 * @subpackage Addonify_Wishlist/includes
	 * @author     Adodnify <contact@addonify.com>
	 */
	class Addonify_Wishlist_Rest_API {

		/**
		 * The namespace of the Rest API.
		 *
		 * @since    1.0.7
		 * @access   protected
		 * @var      string    $rest_namespace.
		 */
		protected $rest_namespace = 'addonify_wishlist_options_api';

		/**
		 * The new namespace of the Rest API.
		 *
		 * @since    1.0.7
		 * @access   protected
		 * @var      string    $rest_namespace.
		 */
		protected $rest_namespace_v2 = 'addonify_wishlist_options_api/v2';


		/**
		 * Register new REST API endpoints.
		 *
		 * @since    1.0.7
		 */
		public function __construct() {

			add_action( 'rest_api_init', array( $this, 'register_rest_endpoints' ) );
		}


		/**
		 * Define the REST API endpoints to get all setting options and update all setting options.
		 *
		 * @since    1.0.7
		 * @access   public
		 */
		public function register_rest_endpoints() {

			register_rest_route( // get wishlist data.
				$this->rest_namespace,
				'/stats/get_wishlist_number',
				array(
					'methods'             => 'Get',
					'callback'            => array( $this, 'rest_handler_get_number_of_wishlists' ),
					'permission_callback' => '__return_true',//array( $this, 'permission_callback' ),
				)
			);

			register_rest_route( // get wishlist data.
				$this->rest_namespace,
				'/stats/get_popular_products',
				array(
					'methods'             => 'Get',
					'callback'            => array( $this, 'rest_handler_get_popular_products' ),
					'permission_callback' => '__return_true',//array( $this, 'permission_callback' ),
				)
			);

			register_rest_route( // Get options.
				$this->rest_namespace,
				'/get_options',
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'rest_handler_get_settings_fields' ),
					'permission_callback' => array( $this, 'permission_callback' ),
				)
			);

			register_rest_route( // Get options version 2.
				$this->rest_namespace_v2,
				'/get_options',
				array(
					'methods'             => 'GET',
					'callback'            => array( $this, 'rest_handler_get_settings_fields_v2' ),
					'permission_callback' => array( $this, 'permission_callback' ),
				)
			);

			register_rest_route( // Update options.
				$this->rest_namespace_v2,
				'/update_options',
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'rest_handler_update_options' ),
					'permission_callback' => array( $this, 'permission_callback' ),
				)
			);

			register_rest_route( // Reset options.
				$this->rest_namespace_v2,
				'/reset_options',
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'rest_handler_reset_options' ),
					'permission_callback' => array( $this, 'permission_callback' ),
				)
			);

			register_rest_route( // Export options.
				$this->rest_namespace_v2,
				'/export_options',
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'rest_handler_export_options' ),
					'permission_callback' => array( $this, 'permission_callback' ),
				)
			);

			register_rest_route( // Import options.
				$this->rest_namespace_v2,
				'/import_options',
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'rest_handler_import_options' ),
					'permission_callback' => array( $this, 'permission_callback' ),
				)
			);

			register_rest_route( // Import options.
				$this->rest_namespace_v2,
				'/create_wishlist_page',
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'rest_handler_create_wishlist_page' ),
					'permission_callback' => array( $this, 'permission_callback' ),
				)
			);
		}


		/**
		 * Callback function to get all settings options values.
		 *
		 * @since    1.0.7
		 */
		public function rest_handler_get_settings_fields() {

			return addonify_wishlist_get_settings_fields();
		}

		/**
		 * Callback function to get all settings options values.
		 *
		 * @since    2.0.0
		 * @return array
		 */
		public function rest_handler_get_settings_fields_v2() {

			return addonify_wishlist_v_2_get_settings_fields();
		}

		/**
		 * REST API endpoint handler to get wishlist data.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param \WP_REST_Request $request The request object.
		 * @return \WP_REST_Response
		 */
		public function rest_handler_get_number_of_wishlists( $request ) {

			$query_args = array();

			$from = $request->get_param( 'start' );

			$to = $request->get_param( 'end' );

			if ( empty( $from ) && ! empty( $to ) ) {
				$date = new DateTime( $to ); // If from date is not set then set 5 days todays date minus 5 days.
				$date->modify( '-5 days' );
				$from = $date->format( 'Y-m-d' );
			}

			if ( empty( $to ) && ! empty( $from ) ) {
				$date = new DateTime( $from ); // If from date is not set then set 5 days todays date minus 5 days.
				$date->modify( '5 days' );
				$to = $date->format( 'Y-m-d' );
			}

			if ( empty( $from ) && empty( $to ) ) {
				$date = new DateTime(); // If from date is not set then set 5 days todays date minus 5 days.
				$date->modify( '-5 days' );
				$from = $date->format( 'Y-m-d' );
				$to   = gmdate( 'Y-m-d' );
			}

			if ( $from && $this->validate_date_format( $from ) ) {
				$query_args['from'] = $from;
			}

			if ( $to && $this->validate_date_format( $to ) ) {
				$query_args['to'] = $to;
			}

			$visibility = $request->get_param( 'visibility' ) ? sanitize_text_field( $request->get_param( 'visibility' ) ) : 'NULL';

			$query_args['visibility'] = $visibility;

			$results = Addonify_Wishlist_DB::count_all_wishlists( $query_args );

			if ( empty( $results ) ) {
				return new WP_REST_Response(
					array(
						'success' => false,
						'message' => esc_html__( 'No data found.', 'addonify-wishlist' ),
					)
				);
			}

			$response['success'] = true;
			$response['message'] = esc_html__( 'Data fetched successfully.', 'addonify-wishlist' );
			$response['data']    = array(
				'Number of Wishlists' => $results,
			);

			return new WP_REST_Response( $response );
		}

		/**
		 * REST API endpoint handler to get popular products among the wishlists.
		 *
		 * @since 1.0.0
		 * @access public
		 *
		 * @param \WP_REST_Request $request The request object.
		 * @return \WP_REST_Response
		 */
		public function rest_handler_get_popular_products( $request ) {

			$query_args = array();

			$order_by = ( $request->get_param( 'order_by' ) ) ? strtoupper( sanitize_text_field( $request->get_param( 'order_by' ) ) ) : 'DESC';

			$visibility = ( $request->get_param( 'visibility' ) ) ? sanitize_text_field( $request->get_param( 'visibility' ) ) : 'NULL';

			$limit = ( $request->get_param( 'limit' ) ) ? absint( $request->get_param( 'limit' ) ) : '2';

			$offset = ( $request->get_param( 'offset' ) ) ? absint( $request->get_param( 'offset' ) ) : '0';

			if ( ! in_array( $order_by, array( 'ASC', 'DESC' ), true ) ) {
				$order_by = 'DESC';
			}

			$from = $request->get_param( 'start' );

			$to = $request->get_param( 'end' );

			if ( empty( $from ) && ! empty( $to ) ) {
				$date = new DateTime( $to ); // If from date is not set then set 5 days todays date minus 15 days.
				$date->modify( '-5 days' );
				$from = $date->format( 'Y-m-d' );
			}

			if ( empty( $to ) && ! empty( $from ) ) {
				$date = new DateTime( $from ); // If from date is not set then set 5 days todays date minus 15 days.
				$date->modify( '5 days' );
				$to = $date->format( 'Y-m-d' );
			}

			if ( empty( $from ) && empty( $to ) ) {
				$date = new DateTime(); // If from date is not set then set 5 days todays date minus 15 days.
				$date->modify( '-5 days' );
				$from = $date->format( 'Y-m-d' );
				$to   = gmdate( 'Y-m-d' );
			}

			if ( $from && $this->validate_date_format( $from ) ) {
				$query_args['from'] = $from;
			}

			if ( $to && $this->validate_date_format( $to ) ) {
				$query_args['to'] = $to;
			}

			$query_args['visibility'] = $visibility;

			$query_args['order_by'] = $order_by;

			$query_args['limit'] = $limit;

			$query_args['offset'] = $offset;

			$results = Addonify_Wishlist_DB::get_most_repeated_item( $query_args );

			if ( is_array( $results ) ) {

				if ( empty( $results ) ) {
					return new WP_REST_Response(
						array(
							'success' => false,
							'message' => esc_html__( 'No products found.', 'addonify-wishlist' ),
						)
					);
				}

				$popular_products = array_map(
					function ( $popular_products ) {
						$priduct_id = $popular_products['product_id'];
						$count      = $popular_products['total'];
						return array(
							'product_id' => $priduct_id,
							'count'      => $count,
						);
					},
					$results,
				);

				$response['success'] = true;
				$response['message'] = esc_html__( 'Products fetched successfully.', 'addonify-wishlist' );
				$response['data']    = array(
					'popular_products' => $popular_products,
				);

				return new WP_REST_Response( $response );
			} else {
				return new WP_REST_Response(
					array(
						'success' => false,
						'message' => esc_html__( 'Error fetching data.', 'addonify-wishlist' ),
					)
				);
			}
		}

		/**
		 * Callback function to update all settings options values.
		 *
		 * @since    1.0.7
		 * @param    \WP_REST_Request $request    The request object.
		 * @return   \WP_REST_Response   $return_data   The response object.
		 */
		public function rest_handler_update_options( $request ) {

			$return_data = array(
				'success' => false,
				'message' => __( 'Ooops, error saving settings!!!', 'addonify-wishlist' ),
			);

			$params = $request->get_params();

			if ( ! isset( $params['settings_values'] ) ) {

				$return_data['message'] = __( 'No settings values to update!!!', 'addonify-wishlist' );
				return $return_data;
			}

			if ( addonify_wishlist_v_2_update_settings( $params['settings_values'] ) === true ) {

				$return_data['success'] = true;
				$return_data['message'] = __( 'Settings saved successfully', 'addonify-wishlist' );
			}

			return rest_ensure_response( $return_data );
		}

		/**
		 * Define callback function that handles request coming to /reset_options endpoint.
		 *
		 * @since    1.0.5
		 * @param object $request  \WP_REST_Request The request object.
		 * @return json $return_data   \WP_REST_Response The response object.
		 */
		public function rest_handler_reset_options( $request ) {

			global $wpdb;

			$option_keys = array_keys( addonify_wishlist_v_2_settings_fields() );

			$where  = '';
			$first  = true;
			$values = array();
			if ( ! empty( $option_keys ) ) {
				foreach ( $option_keys as $key ) {
					if ( ! $first ) {
						$where .= ' OR ';
					}
					$where   .= ' option_name = %s';
					$values[] = ADDONIFY_WISHLIST_DB_INITIALS . $key;
					$first    = false;
				}
			}

			$query  = 'DELETE FROM ' . $wpdb->options . ' WHERE ' . $where;
			$result = $wpdb->query( $wpdb->prepare( $query, $values ) ); //phpcs:ignore
			if ( $result ) {
				$return_data = array(
					'success' => true,
					'message' => esc_html__( 'Options reset success.', 'addonify-wishlist' ),
				);
			} elseif ( 0 === $result ) {
				$return_data = array(
					'success' => true,
					'message' => esc_html__( 'Options reset success.', 'addonify-wishlist' ),
				);
			} else {
				$return_data = array(
					'success' => false,
					'message' => esc_html__( 'Error! Options reset unsuccessful.', 'addonify-wishlist' ),
				);
			}

			return rest_ensure_response( $return_data );
		}

		/**
		 * REST handler function for creating wishlist page.
		 */
		public function rest_handler_create_wishlist_page() {

			// Create page object.
			$new_page = array(
				'post_title'   => __( 'Wishlist', 'addonify-wishlist' ),
				'post_content' => '[addonify_wishlist]',
				'post_status'  => 'publish',
				'post_author'  => get_current_user_id(),
				'post_type'    => 'page',
			);

			// Insert the post into the database.
			$page_id = wp_insert_post( $new_page );

			if ( ! ( $page_id instanceof WP_Error || 0 === $page_id ) ) {
				update_option( ADDONIFY_WISHLIST_DB_INITIALS . 'wishlist_page', $page_id );
				$return_data = array(
					'success' => true,
					'message' => esc_html__( 'Wishlist page generate success.', 'addonify-wishlist' ),
				);
			} else {
				$return_data = array(
					'success' => false,
					'message' => esc_html__( 'Error! Wishlist page generate unsuccessful.', 'addonify-wishlist' ),
				);
			}

			return rest_ensure_response( $return_data );
		}

		/**
		 * Define callback function that handles request coming to /reset_options endpoint.
		 *
		 * @since    1.0.5
		 */
		public function rest_handler_export_options() {
			global $wpdb;

			$query = 'SELECT option_name, option_value FROM ' . $wpdb->options . ' WHERE option_name LIKE %s';

			$results = $wpdb->get_results( $wpdb->prepare( $query, '%' . ADDONIFY_WISHLIST_DB_INITIALS . '%' ), ARRAY_A ); //phpcs:ignore

			$content = wp_json_encode( $results );

			$file_name = time() . wp_rand( 100000, 999999 ) . '.json';

			if ( file_put_contents( trailingslashit( wp_upload_dir()['path'] ) . $file_name, $content ) ) { //phpcs:ignore
				$message = array(
					'success' => true,
					'url'     => trailingslashit( wp_upload_dir()['url'] ) . $file_name,
				);
			} else {
				$message = array(
					'success' => false,
					'message' => esc_html__( 'Error! Options export failed!.', 'addonify-wishlist' ),
				);
			}

			return rest_ensure_response( $message );
		}

		/**
		 * Callback function to update all settings options values.
		 *
		 * @since    1.0.7
		 * @param object $request  \WP_REST_Request The request object.
		 */
		public function rest_handler_import_options( $request ) {
			if ( empty( $_FILES ) ) {
				return rest_ensure_response(
					array(
						'success' => false,
						'message' => esc_html__( 'No file provided.', 'addonify-wishlist' ),
					)
				);
			}
			$file_contents = file_get_contents( $_FILES['addonify_wishlist_import_file']['tmp_name'] ); //phpcs:ignore

			if ( isset( $_FILES['addonify_wishlist_import_file']['type'] ) && 'application/json' !== $_FILES['addonify_wishlist_import_file']['type'] ) {
				return rest_ensure_response(
					array(
						'success' => false,
						'message' => esc_html__( 'File format not supported.', 'addonify-wishlist' ),
					)
				);
			}

			$uploaded_options_array = $this->json_to_array( $file_contents );

			if ( ! is_array( $uploaded_options_array ) ) {
				return rest_ensure_response(
					array(
						'success' => false,
						'message' => esc_html__( 'Ooops, error saving settings!!! File does not contain supported json.', 'addonify-wishlist' ),
					)
				);
			}

			foreach ( $uploaded_options_array as $option ) {
				update_option( $option->option_name, $option->option_value );
			}

			return rest_ensure_response(
				array(
					'success' => true,
					'message' => esc_html__( 'Addonify Wishlist options has been imported successfully.', 'addonify-wishlist' ),
				)
			);
		}



		/**
		 * Permission callback function to check if current user can access the rest api route.
		 *
		 * @since    1.0.7
		 */
		public function permission_callback() {

			if ( ! current_user_can( 'manage_options' ) ) {

				return new WP_Error( 'rest_forbidden', esc_html__( 'Ooops, you are allowed to manage options.', 'addonify-wishlist' ), array( 'status' => 401 ) );
			}
			return true;
		}

		/**
		 * Converts json data to array.
		 *
		 * @param mixed $data JSON Data to convert to array format.
		 * @return array|false Array if correct json format, false otherwise
		 */
		private function json_to_array( $data ) {
			if ( ! is_string( $data ) ) {
				return false;
			}
			try {
				$return_data = json_decode( $data );
				if ( JSON_ERROR_NONE === json_last_error() ) {
					if ( gettype( $return_data ) === 'array' ) {
						return $return_data;
					} elseif ( gettype( $return_data ) === 'object' ) {
						return (array) $return_data;
					}
				} else {
					return false;
				}
			} catch ( Exception $e ) {
				error_log( $e->getMessage() ); //phpcs:ignore
			}
		}

		/**
		 * Checks if given date and format is valid.
		 *
		 * @since 1.0.0
		 *
		 * @param string $date Date.
		 * @param string $format Date format.
		 */
		public function validate_date_format( $date, $format = 'Y-m-d' ) {

			$d = DateTime::createFromFormat( 'Y-m-d', $date );
			return $d && $d->format( $format ) === $date;
		}
	}
}
