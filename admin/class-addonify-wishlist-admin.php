<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.addonify.com
 * @since      1.0.0
 *
 * @package    Addonify_Wishlist
 * @subpackage Addonify_Wishlist/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and other required variables.
 *
 * @package    Addonify_Wishlist
 * @subpackage Addonify_Wishlist/admin
 * @author     Adodnify <contact@addonify.com>
 */
class Addonify_Wishlist_Admin {

	/**
	 * Settings page slug
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $settings_page_slug    Default settings page slug for this plugin
	 */
	private $settings_page_slug = 'addonify_wishlist';


	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		// load styles in this plugin page only.
		if ( isset( $_GET['page'] ) && $_GET['page'] == $this->settings_page_slug ) { // phpcs:ignore

			wp_enqueue_style(
				"{$this->plugin_name}-icon",
				plugin_dir_url( __FILE__ ) . 'app/fonts/addonify-wishlist/icon.css',
				array(),
				$this->version,
				'all'
			);
		}

		// Load global admin styles.
		wp_enqueue_style(
			"{$this->plugin_name}-global-admin",
			plugin_dir_url( __FILE__ ) . 'resources/global.css',
			array(),
			$this->version,
			'all'
		);
	}


	/**
	 * Admin initial functions.
	 */
	public function admin_init() {

		$this->maybe_update_user_review_status();
	}


	/**
	 * Generate admin menu for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function add_menu_callback() {

		// do not show menu if woocommerce is not active.
		if ( ! class_exists( 'woocommerce' ) ) {
			return;
		}

		global $admin_page_hooks;

		$parent_menu_slug = array_search( 'addonify', $admin_page_hooks, true );

		if ( ! $parent_menu_slug ) {

			add_menu_page(
				'Addonify Settings',
				'Addonify',
				'manage_options',
				$this->settings_page_slug,
				array( $this, 'get_settings_screen_contents' ),
				'dashicons-superhero',
				70
			);

			add_submenu_page(
				$this->settings_page_slug,
				'Wishlist Settings',
				'Wishlist',
				'manage_options',
				$this->settings_page_slug,
				array( $this, 'get_settings_screen_contents' ),
				1
			);

		} else {
			add_submenu_page(
				$parent_menu_slug,
				'Wishlist Settings',
				'Wishlist',
				'manage_options',
				$this->settings_page_slug,
				array( $this, 'get_settings_screen_contents' ),
				1
			);
		}
	}



	/**
	 * Show action links on the plugin screen.
	 *
	 * @param mixed $links Plugin Action links.
	 * @return array
	 */
	public function plugin_action_links( $links ) {

		$action_links = array(
			'settings' => '<a href="admin.php?page=' . esc_attr( $this->settings_page_slug ) . '">' . __( 'Settings', 'addonify-wishlist' ) . '</a>',
		);

		return array_merge( $action_links, $links );
	}

	/**
	 * Show row meta on the plugin screen.
	 *
	 * @param mixed $links Plugin Row Meta.
	 * @param mixed $file  Plugin Base file.
	 *
	 * @return array
	 */
	public function plugin_row_meta( $links, $file ) {

		if ( ADDONIFY_WISHLIST_BASENAME !== $file ) {
			return $links;
		}

		$row_meta = array(
			'docs'    => '<a href="https://docs.addonify.com/kb/woocommerce-wishlist/" aria-label="' . esc_attr__( 'View Addonify Wishlist documentation', 'addonify-wishlist' ) . '" target="_blank">' . esc_html__( 'Documentation', 'addonify-wishlist' ) . '</a>',
			'github'  => '<a href="https://github.com/addonify/addonify-wishlist/" aria-label="' . esc_attr__( 'View Addonify Wishlist GitHub link', 'addonify-wishlist' ) . '" target="_blank">' . esc_html__( 'GitHub', 'addonify-wishlist' ) . '</a>',
			'support' => '<a href="https://wordpress.org/support/plugin/addonify-wishlist/" aria-label="' . esc_attr__( 'Visit community forums', 'addonify-wishlist' ) . '" target="_blank">' . esc_html__( 'Community support', 'addonify-wishlist' ) . '</a>',
		);

		return array_merge( $links, $row_meta );
	}


	/**
	 * Get contents from settings page templates and print it
	 * Called from "add_menu_callback".
	 *
	 * @since    1.0.0
	 */
	public function get_settings_screen_contents() {
		?>
		<div id="addonify-wishlist-app"></div>
		<?php
	}


	/**
	 * Show custom "post status" label after page title in "Pages" admin menu.
	 * shows "Addonify Wishlist Page" label after the page title.
	 *
	 * @since    1.0.0
	 * @param array  $states Post Status.
	 * @param object $post  Post object.
	 */
	public function display_custom_post_states_after_page_title( $states, $post ) {

		$wishlist_page_id = addonify_wishlist_get_option( 'wishlist_page' ) ? (int) addonify_wishlist_get_option( 'wishlist_page' ) : '';

		if (
			get_post_type( $post->ID ) === 'page' &&
			(int) $post->ID === (int) $wishlist_page_id
		) {
			$states[] = __( 'Addonify Wishlist Page', 'addonify-wishlist' );
		}

		return $states;
	}

	/**
	 * Review notice update on user choice.
	 */
	public function maybe_update_user_review_status() {

		if ( isset( $_GET['addonify-Wishlist-review-notice-already-did'] ) ) { //phpcs:ignore
			update_option( 'addonify_wishlist_plugin_review_status', 'reviewed' );
			wp_safe_redirect( esc_url( admin_url() ) );
		}

		if ( isset( $_GET['addonify-Wishlist-review-notice-maybe-later'] ) ) { //phpcs:ignore
			set_transient( 'addonify_wishlist_ask_for_review_transient', '1', 3 * DAY_IN_SECONDS );
			wp_safe_redirect( esc_url( admin_url() ) );
		}
	}

	/**
	 * Show add a review admin notice.
	 *
	 * @since 2.0.0
	 */
	public function show_add_a_review_notice() {

		global $pagenow;

		if ( 'index.php' !== $pagenow ) {
			return;
		}
		?>
		<div class="addonify-wishlist-wp-notice notice notice-info" id="addonify-wishlist-review-notice">
			<h3 class="notice-heading">
				<?php esc_html_e( 'Thank you for choosing Addonify WooCommerce Wishlist!', 'addonify-wishlist' ); ?>
			</h3>
			<p class="notice-description">
				<?php esc_html_e( 'We hope you are enjoying the plugin. If you have a few minutes to spare, please consider leaving a review. We would deeply appreciate it and be grateful to you. It would encourage and help us improve our plugin and make it better.', 'addonify-wishlist' ); ?>
			</p>
			<div class="action-buttons">
				<a target="_blank" href="<?php echo esc_html( 'https://wordpress.org/plugins/addonify-wishlist/#reviews' ); ?>" class="button button-primary">
					<?php esc_html_e( 'Okay, You got it!', 'addonify-wishlist' ); ?>
					<i class="dashicons dashicons-smiley"></i>
				</a>
				<a href="<?php echo esc_html( add_query_arg( 'addonify-Wishlist-review-notice-already-did', true, admin_url() ) ); ?>" class="button button-secondary">
					<?php esc_html_e( 'I already did', 'addonify-wishlist' ); ?>
				</a>
				<a href="<?php echo esc_html( add_query_arg( 'addonify-Wishlist-review-notice-maybe-later', true, admin_url() ) ); ?>" class="button button-secondary">
					<?php esc_html_e( 'Maybe later', 'addonify-wishlist' ); ?>
				</a>
			</div>
		</div>
		<?php
	}
}
