<?php
/**
 * UDP Agent collects non-sensitive data.
 *
 * @link       https://creamcode.org/user-data-processing/
 * @since      1.0.0
 * @author     CreamCode
 * @package    Udp_Agent
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * UDP Agent class.
 */
class Udp_Agent {

	/**
	 * Name of this agents parent.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $agent_name Name of this agents parent.
	 */
	private $agent_name;

	/**
	 * Engine URL.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $engine url URL to send wp rest api connection request.
	 */
	private $engine_url;

	/**
	 * Agent's parent folder location
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $agent_root_dir Path to this agents parent folder.
	 */
	private $agent_root_dir;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $ver            Version of this agent.
	 * @param      string $agent_root_dir Path to this agents parent folder.
	 * @param      string $engine_url     URL to Engine server.
	 */
	public function __construct( $ver, $agent_root_dir, $engine_url ) {

		$this->engine_url     = $engine_url;
		$this->agent_root_dir = $agent_root_dir;

		$this->hooks();
	}


	// ----------------------------------------------
	// Hooks.
	// ----------------------------------------------

	/**
	 * All hooks will be called from this method.
	 *
	 * @since    1.0.0
	 */
	private function hooks() {

		add_action( 'init', array( $this, 'on_init' ) );
		add_action( 'admin_init', array( $this, 'on_admin_init' ) );

		// Custom WP Schedule.
		add_action( 'init', array( $this, 'udp_schedule_cron' ) );
	}


	/**
	 * Action that needs to be run on "init" hook.
	 *
	 * @since    1.0.0
	 */
	public function on_init() {

		// Process user tracking actions.
		if ( isset( $_GET['udp-agent-allow-access'] ) ) { //phpcs:ignore
			$this->process_user_tracking_choice();
		}
	}

	/**
	 * Actions that needs to be run on "admin init" hook.
	 *
	 * @since    1.0.0
	 */
	public function on_admin_init() {

		// Register setting to get user's consent for data collection.
		register_setting(
			'general',
			'udp_agent_allow_tracking',
			array(
				'sanitize_callback' => array( $this, 'get_settings_field_val' ),
			)
		);

		// Render setting field to get user's consent for data collection.
		add_settings_field(
			'udp_agent_allow_tracking',
			esc_html__( 'Allow Anonymous Tracking', 'addonify-wishlist' ),
			array( $this, 'show_settings_ui' ),
			'general',
			'default',
			array(
				'label_for' => 'udp_agent_allow_tracking',
			)
		);
	}


	/**
	 * Sanitization callback function for sanitizing value of 'udp_agent_allow_tracking' setting.
	 *
	 * @since 1.0.0
	 *
	 * @param string $data Raw data.
	 * @return string
	 */
	public function get_settings_field_val( $data ) {
		if ( 1 === (int) $data ) {
			return 'yes';
		} else {
			return 'no';
		}

		return ( 'yes' === sanitize_text_field( $data ) ) ? 'yes' : 'no';
	}


	// ----------------------------------------------
	// Settings page UI.
	// ----------------------------------------------

	/**
	 * Generate markups for setting field, 'udp_agent_allow_tracking'.
	 *
	 * @since    1.0.0
	 */
	public function show_settings_ui() {

		echo '<p>';
		echo "<input type='checkbox' name='udp_agent_allow_tracking' id='udp_agent_allow_tracking' value='yes'";
		if ( 'yes' === get_option( 'udp_agent_allow_tracking' ) ) {
			echo ' checked';
		}
		echo '/>';
		echo esc_html__( 'Become a super contributor by sharing your non-sensitive WordPress data. We guarantee no sensitive data is collected.', 'addonify-wishlist' );
		echo wp_kses_data( '<a href="https://creamcode.org/user-data-processing/" target="_blank" > ' . esc_html__( ' What data do we collect?', 'addonify-wishlist' ) . '</a>' );
		echo ' </p>';
	}


	// ----------------------------------------------
	// Show admin notice, for collecting user data.
	// ----------------------------------------------

	/**
	 * User has decided to allow or not allow user tracking.
	 * save this value in database.
	 *
	 * @since    1.0.0
	 */
	private function process_user_tracking_choice() {

		$users_choice = isset( $_GET['udp-agent-allow-access'] ) ? sanitize_text_field( wp_unslash( $_GET['udp-agent-allow-access'] ) ) : ''; //phpcs:ignore

		if ( empty( $users_choice ) ) {
			return;
		}

		// Add data into database.
		update_option( 'udp_agent_allow_tracking', $users_choice );
		if ( 'yes' === $users_choice ) {
			$this->do_handshake();
		}
		update_option( 'udp_agent_tracking_msg_last_shown_at', time() );

		// Redirect back to dashboard.
		wp_safe_redirect( admin_url() );
		exit;
	}

	// ----------------------------------------------
	// Data collection and authentication with engine.
	// ----------------------------------------------

	/**
	 * Gather data to send to engine.
	 *
	 * @since    1.0.0
	 */
	private function get_data() {

		if ( ! class_exists( 'WP_Debug_Data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-debug-data.php';
			require_once ABSPATH . 'wp-includes/load.php';
			require_once ABSPATH . 'wp-admin/includes/update.php';
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
			require_once ABSPATH . 'wp-admin/includes/misc.php';
		}

		if ( ! class_exists( 'WP_Site_Health' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-site-health.php';
		}

		$data = array();

		$site        = wp_parse_url( get_site_url() );
		$site_scheme = array_key_exists( 'scheme', $site ) ? $site['scheme'] . '://' : '';
		$site_host   = array_key_exists( 'host', $site ) ? $site['host'] : '';
		$site_port   = array_key_exists( 'port', $site ) ? ':' . $site['port'] : '';

		$data['data']            = WP_Debug_Data::debug_data();
		$data['site_url']        = $site_scheme . $site_host . $site_port;
		$data['site_user_email'] = get_bloginfo( 'admin_email' );
		$plugin_directory        = untrailingslashit( dirname( __FILE__, 3 ) );
		$dir_names               = explode( '/', $plugin_directory );
		if ( strpos( $dir_names[ count( $dir_names ) - 1 ], '\\' ) ) {
			$dir_names = explode( '\\', $dir_names[ count( $dir_names ) - 1 ] );
		}
		$plugin_name = array_pop( $dir_names );
		if ( file_exists( $plugin_directory . '/' . $plugin_name . '.php' ) ) {
			$this_plugin_data      = get_plugin_data( $plugin_directory . '/' . $plugin_name . '.php' );
			$data['sender_client'] = $this_plugin_data['Name'];
		} else {
			$theme                 = wp_get_theme();
			$data['sender_client'] = $theme->name;
		}

		return $data;
	}



	/**
	 * Authorize this agent to send data to engine.
	 * get secret key from engine
	 * run on agent activation.
	 *
	 * @since    1.0.0
	 */
	public function do_handshake() {

		$track_user = get_option( 'udp_agent_allow_tracking' );

		if ( 'yes' !== $track_user ) {
			// Do not send data.
			return;
		}

		$data['agent_data'] = serialize( $this->get_data() ); //phpcs:ignore
		$url                = untrailingslashit( $this->engine_url ) . '/wp-json/udp-engine/v1/handshake';

		$this->do_curl( $url, $data );

		return true;
	}

	// ------------------------------------------------
	// Cron
	// ------------------------------------------------

	/**
	 * Custom cron job, runs daily
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function udp_schedule_cron() {

		$cron_hook_name = 'udp_agent_cron';
		add_action( $cron_hook_name, array( $this, 'send_data_to_engine' ) );

		if ( ! wp_next_scheduled( $cron_hook_name ) ) {
			wp_schedule_event( time(), 'daily', $cron_hook_name );
		}
	}

	/**
	 * Custom cron job callback function.
	 * Send data collected from agent to engine.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function send_data_to_engine() {

		$track_user = get_option( 'udp_agent_allow_tracking' );

		if ( 'yes' !== $track_user ) {
			// Do not send data.
			return;
		}

		$data_to_send['agent_data'] = serialize( $this->get_data() ); //phpcs:ignore
		$url                        = untrailingslashit( $this->engine_url ) . '/wp-json/udp-engine/v1/process-data';
		// phpcs:ignore $this->write_log( __FUNCTION__ . $this->do_curl( $url, $data_to_send ) );
		$this->do_curl( $url, $data_to_send );
		exit;
	}

	/**
	 * Function used for debugging.
	 * Writes in the log file.
	 *
	 * @param string $log Message to be logged.
	 */
	private function write_log( $log ) {
		if ( true === WP_DEBUG && true === WP_DEBUG_LOG ) {
			if ( is_array( $log ) || is_object( $log ) ) {
				error_log( print_r( $log, true ) ); //phpcs:ignore
			} else {
				error_log( $log ); //phpcs:ignore
			}
		}
	}

	/**
	 * A little helper function to do curl request.
	 *
	 * @since    1.0.0
	 *
	 * @param string $url URL.
	 * @param array  $data_to_send Data to send.
	 * @return mixed $response Response from curl request.
	 */
	private function do_curl( $url, $data_to_send ) {

		if ( empty( $url ) ) {
			return;
		}

		$args = array(
			'body' => $data_to_send,
		);

		$return_data = wp_remote_post( $url, $args );

		return wp_remote_retrieve_body( $return_data );
	}
}
