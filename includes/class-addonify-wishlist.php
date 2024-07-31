<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.addonify.com
 * @since      1.0.0
 *
 * @package    Addonify_Wishlist
 * @subpackage Addonify_Wishlist/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Addonify_Wishlist
 * @subpackage Addonify_Wishlist/includes
 * @author     Adodnify <contact@addonify.com>
 */
class Addonify_Wishlist {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Addonify_Wishlist_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		if ( defined( 'ADDONIFY_WISHLIST_VERSION' ) ) {
			$this->version = ADDONIFY_WISHLIST_VERSION;
		} else {
			$this->version = '1.0.0';
		}

		$this->plugin_name = 'addonify-wishlist';

		$this->load_dependencies();
		$this->set_locale();
		$this->maybe_create_table();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->rest_api();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Addonify_Wishlist_Loader. Orchestrates the hooks of the plugin.
	 * - Addonify_Wishlist_I18n. Defines internationalization functionality.
	 * - Addonify_Wishlist_Admin. Defines all hooks for the admin area.
	 * - Addonify_Wishlist_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once ADDONIFY_WISHLIST_PLUGIN_PATH . '/includes/class-addonify-wishlist-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once ADDONIFY_WISHLIST_PLUGIN_PATH . '/includes/class-addonify-wishlist-i18n.php';

		require_once ADDONIFY_WISHLIST_PLUGIN_PATH . '/includes/addonify-wishlist-user-meta-functions.php';

		require_once ADDONIFY_WISHLIST_PLUGIN_PATH . '/includes/classes/addonify-wishlist-db-trait.php';

		require_once ADDONIFY_WISHLIST_PLUGIN_PATH . '/includes/classes/class-addonify-wishlist-db.php';

		require_once ADDONIFY_WISHLIST_PLUGIN_PATH . '/includes/classes/class-addonify-wishlist-actions.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once ADDONIFY_WISHLIST_PLUGIN_PATH . '/admin/class-addonify-wishlist-admin.php';

		require_once ADDONIFY_WISHLIST_PLUGIN_PATH . '/includes/setting-functions/helpers.php';

		require_once ADDONIFY_WISHLIST_PLUGIN_PATH . '/includes/setting-functions/settings-v2.php';

		require_once ADDONIFY_WISHLIST_PLUGIN_PATH . '/includes/class-addonify-wishlist-rest-api.php';

		require_once ADDONIFY_WISHLIST_PLUGIN_PATH . '/includes/addonify-wishlist-helper-functions.php';

		require_once ADDONIFY_WISHLIST_PLUGIN_PATH . '/includes/addonify-wishlist-template-functions.php';

		require_once ADDONIFY_WISHLIST_PLUGIN_PATH . '/includes/addonify-wishlist-template-hooks.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once ADDONIFY_WISHLIST_PLUGIN_PATH . '/public/class-addonify-wishlist-public.php';

		require_once ADDONIFY_WISHLIST_PLUGIN_PATH . '/includes/guest-ajax-callbacks.php';

		/**
		 * User data processing functions.
		 */
		require_once ADDONIFY_WISHLIST_PLUGIN_PATH . '/includes/udp/init.php';

		$this->loader = new Addonify_Wishlist_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Addonify_Wishlist_I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Addonify_Wishlist_I18n();

		$this->loader->add_action( 'init', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Check for wishlist table and create if it does not exist.
	 *
	 * @since 1.0.0
	 */
	private function maybe_create_table() {

		$table_exists = Addonify_Wishlist_DB::table_exists();

		if ( ! $table_exists ) {
			Addonify_Wishlist_DB::create_table();
		}
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Addonify_Wishlist_Admin(
			$this->get_plugin_name(),
			$this->get_version()
		);

		// Display review message on certain time interval if not already reviewed.
		if ( ! get_transient( 'addonify_wishlist_ask_for_review_transient' ) ) {
			$review_status = get_option( 'addonify_wishlist_plugin_review_status' );
			if ( ! $review_status ) {
				update_option( 'addonify_wishlist_plugin_review_status', 'later' );
				set_transient( 'addonify_wishlist_ask_for_review_transient', '1', 3 * DAY_IN_SECONDS );
			} elseif ( 'reviewed' !== $review_status ) {
				$this->loader->add_action( 'admin_notices', $plugin_admin, 'show_add_a_review_notice' );
			}
		}

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );

		$this->loader->add_action( 'admin_init', $plugin_admin, 'admin_init' );

		// admin menu.
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_menu_callback', 20 );

		// custom link in plugins.php page in wp-admin.
		$this->loader->add_filter( 'plugin_action_links_' . ADDONIFY_WISHLIST_BASENAME, $plugin_admin, 'plugin_action_links', 10, 2 );

		$this->loader->add_filter( 'plugin_row_meta', $plugin_admin, 'plugin_row_meta', 10, 2 );

		// add custom post status "Addonify Wishlist Page" after page name.
		$this->loader->add_filter( 'display_post_states', $plugin_admin, 'display_custom_post_states_after_page_title', 10, 2 );
	}

	/**
	 * Register rest api endpoints for admin settings page.
	 *
	 * @since    1.0.7
	 * @access   private
	 */
	private function rest_api() {

		$plugin_rest = new Addonify_Wishlist_Rest_API();
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Addonify_Wishlist_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'init', $plugin_public, 'init' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts', 15 );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles', 15 );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {

		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {

		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Addonify_Wishlist_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {

		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {

		return $this->version;
	}
}
