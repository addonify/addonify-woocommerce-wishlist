<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.addonify.com
 * @since      1.0.0
 *
 * @package    Addonify_Wishlist
 * @subpackage Addonify_Wishlist/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and other required stuff.
 *
 * @package    Addonify_Wishlist
 * @subpackage Addonify_Wishlist/public
 * @author     Adodnify <contact@addonify.com>
 */
class Addonify_Wishlist_Public {

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
	 * True if current user is logged in. Else false.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      boolean    $is_user_logged_in    True if current user is logged in. Else false.
	 */
	private $is_user_logged_in;

	/**
	 * The value of initial button label for wishlist button from the setting.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $add_to_wishlist_button_label    The value of initial button label for wishlist button from the setting.
	 */
	private $add_to_wishlist_button_label;

	/**
	 * The button label for wishlist button if added into the wishlist from the setting.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $added_to_wishlist_button_label The button label for wishlist button if added into the wishlist from the setting.setting.
	 */
	private $added_to_wishlist_button_label;

	/**
	 * The button label for wishlist button if already in the wishlist from the setting.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $already_in_wishlist_button_label The button label for wishlist button if already in the wishlist from the setting.
	 */
	private $already_in_wishlist_button_label;

	/**
	 * The boolean value if icon to be displayed in the wishlist button from the setting.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $show_wishlist_button_icon The boolean value if icon to be displayed in the wishlist button from the setting.
	 */
	private $show_wishlist_button_icon;

	/**
	 * The value of icon position in wishlist button from the setting.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $wishlist_button_icon_position    The value of icon position in wishlist button from the setting.
	 */
	private $wishlist_button_icon_position;

	/**
	 * The value custom classes for wishlist button from the setting.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $wishlist_button_custom_css_classes    The value custom classes for wishlist button from the setting
	 */
	private $wishlist_button_custom_css_classes;

	/**
	 * The current user's wishlist items.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $wishlist_items    The current user's wishlist items.
	 */
	private $wishlist_items = []; // phpcs:ignore

	/**
	 * The current user ID.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      int    $current_user_id    The current user ID.
	 */
	private $current_user_id = 0;

	/**
	 * Site URL.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string  $site_url Site URL.
	 */
	private $site_url = null;

	/**
	 * Wishlist object.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    object  $wishlist Wishlist object.
	 */
	private $wishlist = null;

	/**
	 * Wishlist handler.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    object  $wishlist_handler Wishlist handler.
	 */
	private $wishlist_handler = null;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param string $plugin_name       The name of the plugin.
	 * @param string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Initialize wishlist, and actions.
	 *
	 * @since 1.0.0
	 */
	public function init() {

		if ( addonify_wishlist_get_option( 'enable_wishlist' ) !== '1' ) {
			return;
		}

		$this->is_user_logged_in = is_user_logged_in();

		$this->current_user_id = get_current_user_id();

		$this->site_url = get_site_url();

		$this->wishlist_handler = new Addonify_Wishlist_Actions(
			[ // phpcs:ignore
				'user_id'  => $this->current_user_id,
				'site_url' => $this->site_url,
			]
		);

		$this->wishlist_items = $this->wishlist_handler->get_wishlist_items();

		if ( addonify_wishlist_get_option( 'enable_wishlist_on_myaccount_page' ) === '1' ) {

			add_rewrite_endpoint( 'addonify-wishlist', EP_ROOT | EP_PAGES );
		}

		$this->add_to_wishlist_button_label       = addonify_wishlist_get_option( 'btn_label' );
		$this->added_to_wishlist_button_label     = addonify_wishlist_get_option( 'btn_label_when_added_to_wishlist' );
		$this->already_in_wishlist_button_label   = addonify_wishlist_get_option( 'btn_label_if_added_to_wishlist' );
		$this->show_wishlist_button_icon          = addonify_wishlist_get_option( 'show_icon' );
		$this->wishlist_button_icon_position      = addonify_wishlist_get_option( 'icon_position' );
		$this->wishlist_button_custom_css_classes = addonify_wishlist_get_option( 'btn_custom_class' );

		$this->add_actions_and_filters();

		$this->register_ajax_actions();
	}


	/**
	 * Register all actions required.
	 *
	 * @since 1.0.0
	 */
	public function add_actions_and_filters() {

		$wishlist_btn_position_in_product_loop = addonify_wishlist_get_option( 'btn_position' );

		if ( 'none' !== $wishlist_btn_position_in_product_loop ) {
			switch ( $wishlist_btn_position_in_product_loop ) {
				case 'after_add_to_cart':
					add_action(
						'woocommerce_after_shop_loop_item',
						array( $this, 'render_add_to_wishlist_button' ),
						15
					);
					break;
				case 'before_add_to_cart':
					add_action(
						'woocommerce_after_shop_loop_item',
						array( $this, 'render_add_to_wishlist_button' ),
						5
					);
					break;
				default:
					add_action(
						'body_class',
						function ( $classes ) {
							$classes[] = 'addonify-wishlist-btn-over-image';
							return $classes;
						}
					);

					add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'render_add_to_wishlist_button' ), 15 );
			}
		}

		add_action( 'woocommerce_add_to_cart', array( $this, 'remove_item_from_wishlist' ), 10, 2 );

		// Displaying add to wishlist button in product single before cart form.
		add_action(
			'woocommerce_before_add_to_cart_form',
			array( $this, 'render_add_to_wishlist_button_before_single_cart_form' )
		);
		// Displaying add to wishlist button in product single after cart form.
		add_action(
			'woocommerce_after_add_to_cart_form',
			array( $this, 'render_add_to_wishlist_button_after_single_cart_form' )
		);
		// Displaying add to wishlist button in product single after cart quantity field.
		add_action(
			'woocommerce_after_add_to_cart_quantity',
			array( $this, 'render_add_to_wishlist_button_after_single_add_to_cart_quantity' )
		);
		// Displaying add to wishlist button in product single before add to cart button.
		add_action(
			'woocommerce_before_add_to_cart_button',
			array( $this, 'render_add_to_wishlist_button_before_single_add_to_cart_button' )
		);
		// Displaying add to wishlist button in product single after add to cart button.
		add_action(
			'woocommerce_after_add_to_cart_button',
			array( $this, 'render_add_to_wishlist_button_after_single_add_to_cart_button' )
		);

		add_action( 'wp_footer', array( $this, 'wishlist_modal_wrapper' ) );
		add_action( 'wp_footer', array( $this, 'wishlist_sidebar_template' ) );

		add_filter( 'woocommerce_login_redirect', array( $this, 'myaccount_login' ) );

		add_shortcode( 'addonify_wishlist', array( $this, 'get_shortcode_contents' ) );

		add_shortcode( 'addonify_wishlist_button', array( $this, 'add_to_wishlist_button_shortcode_callback' ) );

		add_shortcode( 'addonify_wishlist_icon', array( $this, 'wishlist_icon_shortcode_callback' ) );

		if ( addonify_wishlist_get_option( 'enable_save_for_later' ) ) {
			if ( 'after_product_name' === addonify_wishlist_get_option( 'save_for_later_btn_position' ) ) {
				add_action( 'woocommerce_after_cart_item_name', array( $this, 'render_add_to_wishlist_button_in_cart_page_items_after_name' ), 10 );
			} else {
				add_filter( 'woocommerce_cart_item_subtotal', array( $this, 'render_add_to_wishlist_button_in_cart_page_items_after_subtotal' ), 10, 2 );
			}
		}

		add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'add_to_cart_fragments' ) );

		add_filter(
			'woocommerce_loop_add_to_cart_args',
			array( $this, 'add_to_cart_args' ),
			15,
			2
		);

		if ( addonify_wishlist_get_option( 'enable_wishlist_on_myaccount_page' ) === '1' ) {

			add_filter(
				'query_vars',
				function ( $vars ) {
					$vars[] = 'addonify-wishlist';
					return $vars;
				}
			);

			add_filter(
				'woocommerce_account_menu_items',
				function ( $items ) {
					$new_item = [ 'addonify-wishlist' => esc_html__( 'Wishlist', 'addonify-wishlist' ) ]; // phpcs:ignore
					$items    = array_slice( $items, 0, 1, true ) + $new_item + array_slice( $items, 1, null, true );
					return $items;
				}
			);

			add_filter(
				'woocommerce_get_endpoint_url',
				function ( $url, $endpoint ) {
					if ( 'addonify-wishlist' === $endpoint ) {
						$wishlist_page_id = addonify_wishlist_get_option( 'wishlist_page' );
						$url              = get_permalink( (int) $wishlist_page_id );
					}
					return $url;
				},
				10,
				2
			);
		}
	}


	/**
	 * Register ajax call functions.
	 *
	 * @since 1.0.0
	 */
	private function register_ajax_actions() {

		// Hooked AJAX endpoint to handle action for adding product into the wishlist.
		add_action(
			'wp_ajax_addonify_wishlist_add_to_wishlist',
			array( $this, 'add_to_wishlist_ajax_handler' )
		);
		// Hooked AJAX endpoint to handle action for removing product from the wishlist.
		add_action(
			'wp_ajax_addonify_wishlist_remove_from_wishlist',
			array( $this, 'remove_from_wishlist_ajax_handler' )
		);
		// Hooked AJAX endpoint to handle action for emptying the wishlist.
		add_action(
			'wp_ajax_addonify_wsihlist_empty_wishlist',
			array( $this, 'empty_wishlist_ajax_handler' )
		);

		// Hooked AJAX endpoint to handle action for getting sidebar and wishlist page content.
		add_action(
			'wp_ajax_nopriv_addonify_wishlist_guest_get_wishlist_content',
			'addonify_wishlist_get_guest_wishlist_content'
		);
		// Hooked AJAX endpoint to handle action for getting product row for sidebar and wishlist page table.
		add_action(
			'wp_ajax_nopriv_addonify_wishlist_guest_get_sidebar_table_product_row',
			'addonify_wishlist_get_guest_sidebar_table_product_row'
		);
	}


	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		if ( ( addonify_wishlist_get_option( 'enable_save_for_later' ) !== '1' && is_cart() ) || is_checkout() ) {
			return;
		}

		wp_enqueue_style(
			'perfect-scrollbar',
			plugin_dir_url( __FILE__ ) . 'assets/build/css/conditional/perfect-scrollbar.css',
			array(),
			$this->version,
			'all'
		);

		wp_enqueue_style(
			'addonify-wishlist-icon',
			plugin_dir_url( __FILE__ ) . 'assets/fonts/addonify-wishlist-icon.min.css',
			array(),
			$this->version,
			'all'
		);

		if ( is_rtl() ) {
			wp_enqueue_style(
				$this->plugin_name,
				plugin_dir_url( __FILE__ ) . 'assets/build/css/addonify-wishlist-public-rtl.css',
				array(),
				$this->version,
				'all'
			);
		} else {
			wp_enqueue_style(
				$this->plugin_name,
				plugin_dir_url( __FILE__ ) . 'assets/build/css/addonify-wishlist-public.css',
				array(),
				$this->version,
				'all'
			);
		}

		$css  = ':root {';
		$css .= '--adfy_wishlist_sidebar_btn_position_offset: ' . addonify_wishlist_get_option( 'sidebar_btn_position_offset' ) . ';';
		$css .= '}';
		wp_add_inline_style( $this->plugin_name, $css );

		$inline_css = $this->dynamic_css();

		$custom_css = addonify_wishlist_get_option( 'custom_css' );

		if ( $custom_css ) {
			$inline_css .= $custom_css;
		}

		$inline_css = $this->minify_css( $inline_css );

		wp_add_inline_style(
			$this->plugin_name,
			$inline_css
		);
	}


	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		if ( ( addonify_wishlist_get_option( 'enable_save_for_later' ) !== '1' && is_cart() ) || is_checkout() ) {
			return;
		}

		wp_enqueue_script(
			'perfect-scrollbar',
			plugin_dir_url( __FILE__ ) . 'assets/build/js/conditional/perfect-scrollbar.min.js',
			null,
			$this->version,
			true
		);

		wp_enqueue_script(
			$this->plugin_name . '-global',
			plugin_dir_url( __FILE__ ) . 'assets/build/js/addonify-wishlist-global-public.min.js',
			null,
			$this->version,
			true
		);

		wp_enqueue_script(
			$this->plugin_name . '-common',
			plugin_dir_url( __FILE__ ) . 'assets/build/js/conditional/common.min.js',
			array( 'jquery' ),
			$this->version,
			true
		);

		$script_object = apply_filters(
			'addonify_wishlist_js_object',
			array(
				'ajaxURL'                                  => esc_url( admin_url( 'admin-ajax.php' ) ),
				'nonce'                                    => wp_create_nonce( $this->plugin_name ),
				'removeAlreadyAddedProductFromWishlist'    => (bool) addonify_wishlist_get_option( 'remove_already_added_product_from_wishlist' ),
				'removeFromWishlistAfterAddedToCart'       => addonify_wishlist_get_option( 'remove_from_wishlist_if_added_to_cart' ),
				'afterAddToWishlistAction'                 => addonify_wishlist_get_option( 'after_add_to_wishlist_action' ),
				'wishlistPageURL'                          => addonify_wishlist_get_wishlist_page_url(),
				'undoNoticeTimeout'                        => addonify_wishlist_get_option( 'undo_notice_timeout' ),
				'addedToWishlistButtonLabel'               => addonify_wishlist_get_option( 'btn_label_when_added_to_wishlist' ),
				'alreadyInWishlistButtonLabel'             => $this->already_in_wishlist_button_label,
				'initialAddToWishlistButtonLabel'          => $this->add_to_wishlist_button_label,
				'loader'                                   => $this->get_loader(),
				'productRemovalUndoNotice'                 => $this->get_product_removal_undo_notice(),
				'enabledMultiWishlist'                     => false,
				// Modal Template.
				'modalTemplate'                            => $this->get_modal_template(),
				// Modal Container Classes.
				'successModalClasses'                      => apply_filters(
					'addonify_wishlist_success_modal_classes',
					'adfy-success-modal'
				),
				'alertModalClasses'                        => apply_filters(
					'addonify_wishlist_alert_modal_classes',
					'adfy-warning-modal'
				),
				'errorModalClasses'                        => apply_filters(
					'addonify_wishlist_error_modal_classes',
					'adfy-error-modal'
				),
				'infoModalClasses'                         => apply_filters(
					'addonify_wishlist_info_modal_classes',
					'adfy-info-modal'
				),
				// Modal Icons.
				'addedToWishlistModalIcon'                 => apply_filters(
					'addonify_wishlist_added_to_wishlist_modal_icon',
					addonify_wishlist_get_wishlist_icons( 'heart-2' )
				),
				'removedFromWishlistModalIcon'             => apply_filters(
					'addonify_wishlist_removed_from_wishlist_modal_icon',
					addonify_wishlist_get_wishlist_icons( 'heart-1' )
				),
				'successModalIcon'                         => apply_filters(
					'addonify_wishlist_success_modal_icon',
					addonify_wishlist_get_wishlist_icons( 'check-1' )
				),
				'alertModalIcon'                           => apply_filters(
					'addonify_wishlist_alert_modal_icon',
					addonify_wishlist_get_wishlist_icons( 'warning-1' )
				),
				'errorModalIcon'                           => apply_filters(
					'addonify_wishlist_error_modal_icon',
					addonify_wishlist_get_wishlist_icons( 'error-1' )
				),
				// Wishlist Button Icons.
				'addToWishlistButtonIcon'                  => apply_filters(
					'addonify_wishlist_add_to_wishlist_btn_icon',
					addonify_wishlist_get_wishlist_icons( 'heart-1' )
				),
				'addedToWishlistButtonIcon'                => apply_filters(
					'addonify_wishlist_added_to_wishlist_btn_icon',
					addonify_wishlist_get_wishlist_icons( 'heart-2' )
				),
				'loadingWishlistButtonIcon'                => apply_filters(
					'addonify_wishlist_loading_wishlist_btn_icon',
					addonify_wishlist_get_wishlist_icons( 'spinner-1' )
				),
				// Modal Messages.
				'addedToWishlistModalMessage'              => addonify_wishlist_get_option( 'product_added_to_wishlist_text' ),
				'alreadyInWishlistModalMessage'            => addonify_wishlist_get_option( 'product_already_in_wishlist_text' ),
				'wishlistEmptyingConfirmationModalMessage' => addonify_wishlist_get_option( 'confirmation_message_for_emptying_wishlist' ),
				'productRemovedFormWishlistModalMessage'   => addonify_wishlist_get_option( 'product_removed_from_wishlist_text' ),
				// Modal Buttons.
				'wishlistLinkModalButton'                  => $this->get_wishlist_link_modal_button(),
				'emptyWishlistConfirmModalButton'          => $this->get_wishlist_empty_confirm_modal_button(),
			)
		);

		if ( addonify_wishlist_get_option( 'enable_save_for_later' ) === '1' ) {
			$script_object['saveForLaterButtonLabel']  = addonify_wishlist_get_option( 'save_for_later_btn_label' );
			$script_object['savedForLaterButtonLabel'] = addonify_wishlist_get_option( 'save_for_later_btn_label_after_added_to_wishlist' );
		}

		if ( ! $this->is_user_logged_in ) {

			$login_url = ( get_option( 'woocommerce_myaccount_page_id' ) ) ? get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) : wp_login_url();

			wp_enqueue_script(
				$this->plugin_name,
				plugin_dir_url( __FILE__ ) . 'assets/build/js/conditional/addonify-wishlist-public-guest.min.js',
				array( 'jquery', 'addonify-wishlist-common' ),
				$this->version,
				true
			);

			$script_object['requireLogin']                         = (bool) addonify_wishlist_get_option( 'require_login' );
			$script_object['thisSiteUrl']                          = get_site_url();
			$script_object['isLoginRequired']                      = addonify_wishlist_get_option( 'require_login' );
			$script_object['ifNotLoginAction']                     = addonify_wishlist_get_option( 'if_not_login_action' );
			$script_object['loginURL']                             = $login_url;
			$script_object['defaultWishlistName']                  = apply_filters( 'addonify_wishist_default_wishlist_name', esc_html__( 'Default Wishlist', 'addonify-wishlist' ) );
			$script_object['getGuestWishlistContent']              = 'addonify_wishlist_guest_get_wishlist_content';
			$script_object['getGuestSidebarTableProductRowAction'] = 'addonify_wishlist_guest_get_sidebar_table_product_row';
			// Modal Messages.
			$script_object['wishlistEmptiedModalMessage']                  = addonify_wishlist_get_option( 'success_emptying_wishlist_message' );
			$script_object['errorEmptyingWishlistModalMessage']            = addonify_wishlist_get_option( 'error_emptying_wishlist_message' );
			$script_object['errorAddingProductToWishlistModalMessage']     = addonify_wishlist_get_option( 'could_not_add_to_wishlist_error_description' );
			$script_object['errorRemovingProductFromWishlistModalMessage'] = addonify_wishlist_get_option( 'could_not_remove_from_wishlist_error_description' );
			$script_object['loginRequiredModalMessage']                    = addonify_wishlist_get_option( 'login_required_message' );
			// Modal Icon.
			$script_object['loginRequiredModalIcon'] = apply_filters(
				'addonify_wishlist_login_required_modal_icon',
				addonify_wishlist_get_wishlist_icons( 'login-1' )
			);
			// Modal Button.
			$script_object['loginLinkModalButton'] = $this->get_login_link_modal_button();
		} else {
			wp_enqueue_script(
				$this->plugin_name,
				plugin_dir_url( __FILE__ ) . 'assets/build/js/conditional/addonify-wishlist-public.min.js',
				array( 'jquery' ),
				$this->version,
				true
			);

			$script_object['addToWishlistAction']      = 'addonify_wishlist_add_to_wishlist';
			$script_object['removeFromWishlistAction'] = 'addonify_wishlist_remove_from_wishlist';
			$script_object['emptyWishlistAction']      = 'addonify_wsihlist_empty_wishlist';
		}

		wp_localize_script(
			$this->plugin_name,
			'addonifyWishlistJSObject',
			$script_object
		);
	}


	/**
	 * Callback function to handle AJAX request to add product into the wishlist.
	 *
	 * @since 1.0.0
	 */
	public function add_to_wishlist_ajax_handler() {

		if ( ! wp_doing_ajax() ) {
			return;
		}

		$response_data = array(
			'success' => false,
			'message' => '',
		);

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if (
			! $nonce ||
			! wp_verify_nonce( $nonce, 'addonify-wishlist' )
		) {
			$response_data['message'] = esc_html( addonify_wishlist_get_option( 'security_token_error_message' ) );
			wp_send_json( $response_data );
		}

		$product_id = isset( $_POST['product_id'] ) ? absint( wp_unslash( $_POST['product_id'] ) ) : 0;

		if ( ! $product_id ) {
			$response_data['message'] = esc_html( addonify_wishlist_get_option( 'invalid_product_id_error_message' ) );
			wp_send_json( $response_data );
		}

		$product = wc_get_product( $product_id );

		if ( ! $product ) {
			$response_data['message'] = esc_html( addonify_wishlist_get_option( 'invalid_product_error_message' ) );
			wp_send_json( $response_data );
		}

		$has_wishlist = $this->wishlist_handler->has_wishlist();

		if ( ! $has_wishlist ) {
			$created      = $this->wishlist_handler->create_wishlist();
			$has_wishlist = $created ? true : false;
		}

		if ( ! $has_wishlist ) {
			$response_data['message'] = esc_html( addonify_wishlist_get_option( 'could_not_add_to_wishlist_error_description' ) );
			wp_send_json( $response_data );
		}

		if ( in_array( $product_id, $this->wishlist_items, true ) ) {
			$response_data['message'] = esc_html( addonify_wishlist_get_option( 'product_already_in_wishlist_text' ) );
			wp_send_json( $response_data );
		}

		$add_to_wishlist = $this->wishlist_handler->add_to_wishlist( $product_id );

		if ( $add_to_wishlist ) {

			$this->wishlist_items[] = $product_id;

			$response_data['success']    = true;
			$response_data['itemsCount'] = count( $this->wishlist_items );

			$has_wishlist_sidebar = isset( $_POST['has_wishlist_sidebar'] ) ? true : false; // phpcs:ignore

			$has_wishlist_table = isset( $_POST['has_wishlist_table'] ) ? true : false; // phpcs:ignore

			$response_data['success'] = true;

			if ( $has_wishlist_sidebar ) {
				ob_start();
				do_action( 'addonify_wishlist_render_sidebar_product_row', $product );
				$response_data['sidebarProductRowContent'] = ob_get_clean();
			}

			if ( $has_wishlist_table ) {
				ob_start();
				do_action( 'addonify_wishlist_render_table_product_row', $product );
				$response_data['tableProductRowContent'] = ob_get_clean();
			}

			wp_send_json( $response_data );
		} else {
			$response_data['message'] = esc_html( addonify_wishlist_get_option( 'could_not_add_to_wishlist_error_description' ) );
			wp_send_json( $response_data );
		}
	}


	/**
	 * Callback function to handle AJAX request to remove product from the wishlist.
	 *
	 * @since 1.0.0
	 */
	public function remove_from_wishlist_ajax_handler() {

		if ( ! wp_doing_ajax() ) {
			return;
		}

		$response_data = array(
			'success' => false,
			'message' => '',
		);

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if (
			! $nonce ||
			! wp_verify_nonce( $nonce, 'addonify-wishlist' )
		) {
			$response_data['message'] = esc_html( addonify_wishlist_get_option( 'security_token_error_message' ) );
			wp_send_json( $response_data );
		}

		$product_id = isset( $_POST['product_id'] ) ? absint( wp_unslash( $_POST['product_id'] ) ) : 0;

		if ( ! $product_id ) {
			$response_data['message'] = esc_html( addonify_wishlist_get_option( 'invalid_product_id_error_message' ) );
			wp_send_json( $response_data );
		}

		$product = wc_get_product( $product_id );

		if ( ! $product ) {
			$response_data['message'] = esc_html( addonify_wishlist_get_option( 'invalid_product_error_message' ) );
			wp_send_json( $response_data );
		}

		if ( ! in_array( $product_id, $this->wishlist_items, true ) ) {
			$response_data['message'] = esc_html( addonify_wishlist_get_option( 'product_not_in_wishlist_error_message' ) );
			wp_send_json( $response_data );
		}

		if ( $this->wishlist_handler->remove_from_wishlist( $product_id ) ) {

			$wishlist_items = $this->wishlist_items;

			$item_index = array_search( $product_id, $wishlist_items, true );
			array_splice( $wishlist_items, $item_index, 1 );

			$this->wishlist_items = $wishlist_items;

			$response_data['success']    = true;
			$response_data['itemsCount'] = count( $this->wishlist_items );
			$response_data['message']    = esc_html( addonify_wishlist_get_option( 'product_removed_from_wishlist_text' ) );
			wp_send_json( $response_data );
		} else {
			$response_data['message'] = esc_html( addonify_wishlist_get_option( 'could_not_remove_from_wishlist_error_description' ) );
			wp_send_json( $response_data );
		}
	}


	/**
	 * Callback function to handle AJAX request to empty the wishlist.
	 *
	 * @since 1.0.0
	 */
	public function empty_wishlist_ajax_handler() {

		if ( ! wp_doing_ajax() ) {
			return;
		}

		$response_data = array(
			'success' => false,
			'message' => '',
		);

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if (
			! $nonce ||
			! wp_verify_nonce( $nonce, 'addonify-wishlist' )
		) {
			$response_data['message'] = esc_html( addonify_wishlist_get_option( 'security_token_error_message' ) );
			wp_send_json( $response_data );
		}

		if ( $this->wishlist_handler->empty_wishlist() ) {
			$response_data['success'] = true;
			$response_data['message'] = esc_html( addonify_wishlist_get_option( 'success_emptying_wishlist_message' ) );
			wp_send_json( $response_data );
		} else {
			$response_data['message'] = esc_html( addonify_wishlist_get_option( 'error_emptying_wishlist_message' ) );
			wp_send_json( $response_data );
		}
	}


	/**
	 * Functions to add product name data attribute in add to cart button.
	 *
	 * @since    2.0.3
	 *
	 * @param array $args Arguments.
	 * @param array $product Product object.
	 */
	public function add_to_cart_args( $args, $product ) {

		$args['attributes']['data-product_name'] = $product->get_title();

		return $args;
	}


	/**
	 * Add wishlist table and sidebar content in cart fragments.
	 *
	 * @since 2.0.6
	 *
	 * @param array $fragments Cart fragments.
	 */
	public function add_to_cart_fragments( $fragments ) {

		if ( $this->is_user_logged_in ) {

			$fragments['itemsCount'] = count( $this->wishlist_items );
		}

		return $fragments;
	}


	/**
	 * Prepares and template arguments for wishlist button.
	 *
	 * @since 2.0.6
	 *
	 * @param array $args Wishlist button template arguments.
	 */
	public function prepare_wishlist_button_template_args( $args = array() ) {

		if ( ! isset( $args['product'] ) ) {
			return;
		}

		$product = $args['product'];

		$button_args = array(
			'product'      => $product,
			'button_label' => isset( $args['button_label'] ) ? $args['button_label'] : $this->add_to_wishlist_button_label,
			'classes'      => isset( $args['classes'] ) ? array( $args['classes'] ) : array(),
			'data_attrs'   => isset( $args['data_attrs'] ) ? $args['data_attrs'] : array(),
		);

		if ( in_array( $product->get_id(), $this->wishlist_items, true ) ) {
			$button_args['button_label'] = isset( $args['already_in_wishlist_button_label'] ) ? $args['already_in_wishlist_button_label'] : $this->already_in_wishlist_button_label;
			$button_args['classes'][]    = 'added-to-wishlist';
		}

		if ( isset( $args['button_icon_position'] ) && 'none' !== $args['button_icon_position'] ) {

			if ( in_array( $product->get_id(), $this->wishlist_items, true ) ) {
				$button_args['button_icon'] = addonify_wishlist_get_wishlist_icons( 'heart-2' );
			} else {
				$button_args['button_icon'] = addonify_wishlist_get_wishlist_icons( 'heart-1' );
			}

			$button_args['classes'][] = ( 'left' === $args['button_icon_position'] ) ? 'adfy-icon-before-label' : 'adfy-icon-after-label';
		}

		if ( ! empty( $this->wishlist_button_custom_css_classes ) ) {
			$button_args['classes'][] = $this->wishlist_button_custom_css_classes;
		}

		return $button_args;
	}


	/**
	 * Render add to wishlist button in products loop.
	 *
	 * @since 1.0.0
	 */
	public function render_add_to_wishlist_button() {

		global $product;

		$args = array();

		if ( '1' === $this->show_wishlist_button_icon ) {
			$args['button_icon_position'] = $this->wishlist_button_icon_position;
		}

		$args['product'] = $product;

		do_action( 'addonify_wishlist_render_wishlist_button', $this->prepare_wishlist_button_template_args( $args ) );
	}


	/**
	 * Render add to wishlist button in product single before cart form.
	 *
	 * @since 2.0.2
	 */
	public function render_add_to_wishlist_button_before_single_cart_form() {

		$add_to_wishlist_button_position = addonify_wishlist_get_option( 'btn_position_on_single' );

		if ( 'before_add_to_cart_form' === $add_to_wishlist_button_position ) {
			echo '<div class="addonify-add-to-wishlist-btn-wrapper">';
			$this->render_add_to_wishlist_button();
			echo '</div>';
		}
	}


	/**
	 * Render add to wishlist button in product single after cart form.
	 *
	 * @since 2.0.2
	 */
	public function render_add_to_wishlist_button_after_single_cart_form() {

		$add_to_wishlist_button_position = addonify_wishlist_get_option( 'btn_position_on_single' );

		if ( 'after_add_to_cart_form' === $add_to_wishlist_button_position ) {
			echo '<div class="addonify-add-to-wishlist-btn-wrapper">';
			$this->render_add_to_wishlist_button();
			echo '</div>';
		}
	}


	/**
	 * Render add to wishlist button in product single before add to cart button.
	 *
	 * @since 2.0.2
	 */
	public function render_add_to_wishlist_button_before_single_add_to_cart_button() {

		global $product;

		$add_to_wishlist_button_position = addonify_wishlist_get_option( 'btn_position_on_single' );

		if (
			(
				'simple' !== $product->get_type() &&
				'variable' !== $product->get_type()
			) &&
			'before_add_to_cart_button' === $add_to_wishlist_button_position
		) {
			$this->render_add_to_wishlist_button();
		}
	}


	/**
	 * Render add to wishlist button in product single before cart quantity.
	 *
	 * @since 2.0.2
	 */
	public function render_add_to_wishlist_button_after_single_add_to_cart_quantity() {

		global $product;

		$add_to_wishlist_button_position = addonify_wishlist_get_option( 'btn_position_on_single' );

		if (
			(
				'simple' === $product->get_type() ||
				'variable' === $product->get_type()
			) &&
			'before_add_to_cart_button' === $add_to_wishlist_button_position
		) {
			$this->render_add_to_wishlist_button();
		}
	}


	/**
	 * Render add to wishlist button in product single after add to cart button.
	 *
	 * @since 2.0.2
	 */
	public function render_add_to_wishlist_button_after_single_add_to_cart_button() {

		$add_to_wishlist_button_position = addonify_wishlist_get_option( 'btn_position_on_single' );

		if ( 'after_add_to_cart_button' === $add_to_wishlist_button_position ) {
			$this->render_add_to_wishlist_button();
		}
	}


	/**
	 * Add product into the cart.
	 *
	 * @since 1.0.0
	 * @param int $product_id Product ID.
	 * @return boolean true if added successfully otherwise false.
	 */
	public function add_to_cart( $product_id ) {

		global $woocommerce;

		if ( $woocommerce->cart->add_to_cart( $product_id ) ) {

			return true;
		}

		return false;
	}


	/**
	 * Gather shortcode contents and display template
	 *
	 * @since    1.0.0
	 */
	public function get_shortcode_contents() {

		ob_start();
		do_action( 'addonify_wishlist_render_shortcode_content', $this->wishlist_items );
		return ob_get_clean();
	}


	/**
	 * Add add-to-wishlist button in cart page items.
	 *
	 * @param object|array $cart_item Cart item.
	 */
	public function render_add_to_wishlist_button_in_cart_page_items_after_name( $cart_item ) {

		echo $this->render_add_to_wishlist_button_in_cart_page_items( $cart_item['product_id'] ); // phpcs:ignore
	}


	/**
	 * Add add-to-wishlist button in cart page items.
	 *
	 * @param string|int   $item_subtotal Item subtotal.
	 * @param object|array $cart_item Cart item.
	 */
	public function render_add_to_wishlist_button_in_cart_page_items_after_subtotal( $item_subtotal, $cart_item ) {

		return $item_subtotal . $this->render_add_to_wishlist_button_in_cart_page_items( $cart_item['product_id'] );
	}


	/**
	 * Add add-to-wishlist button in cart page items.
	 *
	 * @param int $product_id Cart item.
	 */
	public function render_add_to_wishlist_button_in_cart_page_items( $product_id ) {

		if ( ! ( is_cart() || is_page( 'cart' ) ) ) {
			return;
		}

		$product = '';

		if ( isset( $product_id ) ) {
			$product = wc_get_product( (int) $product_id );
		}

		if ( ! isset( $product ) || ! ( $product instanceof WC_Product ) ) {
			echo esc_html__( 'Invalid product!', 'addonify-wishlist' );
			return;
		}

		$button_args = array(
			'product'                          => $product,
			'classes'                          => 'addonify_wishlist-cart-item-add-to-wishlist addonify-wishlist-save-for-later',
			'button_label'                     => addonify_wishlist_get_option( 'save_for_later_btn_label' ),
			'already_in_wishlist_button_label' => addonify_wishlist_get_option( 'save_for_later_btn_label_after_added_to_wishlist' ),
		);

		if ( '1' === $this->show_wishlist_button_icon ) {
			$button_args['button_icon_position'] = $this->wishlist_button_icon_position;
		}
		?>
		<div class="addonify_wishlist-save-for-later">
			<?php do_action( 'addonify_wishlist_render_wishlist_button', $this->prepare_wishlist_button_template_args( $button_args ) ); ?>
		</div>
		<?php
	}


	/**
	 * Callback function for `add_shortcode('addonify_wishlist_icon')`.
	 *
	 * Renders wishlist icon.
	 *
	 * @since 2.0.12
	 *
	 * @param array $attrs Shortcode attributes.
	 */
	public function wishlist_icon_shortcode_callback( $attrs ) {

		if ( is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			return;
		}

		$default_attrs = array(
			'id'             => '',
			'class'          => '',
			'display_badge'  => 'yes',
			'badge_position' => 'top-right',
		);

		$shortcode_attrs = wp_parse_args( $attrs, $default_attrs );

		$button_attributes = array(
			'class' => 'adfy_wishlist-icon-shortcode-btn',
		);

		if ( isset( $shortcode_attrs['id'] ) && $shortcode_attrs['id'] ) {
			$button_attributes['id'] = esc_attr( $shortcode_attrs['id'] );
		}

		if ( isset( $shortcode_attrs['class'] ) && $shortcode_attrs['class'] ) {
			$button_attributes['class'] .= ' ' . esc_attr( $shortcode_attrs['class'] );
		}

		$wishlist_page_id  = addonify_wishlist_get_option( 'wishlist_page' );
		$wishlist_page_url = get_permalink( (int) $wishlist_page_id );

		ob_start();
		?>
		<a href="<?php echo esc_url( $wishlist_page_url ); ?>" <?php echo wc_implode_html_attributes( $button_attributes ); // phpcs:ignore ?>>
			<span class="icon">
				<?php echo addonify_wishlist_get_wishlist_icons( 'heart-1' ); //phpcs:ignore ?>
			</span>

			<?php
			if ( isset( $shortcode_attrs['display_badge'] ) && 'yes' === $shortcode_attrs['display_badge'] ) {

				$count_positions = array(
					'top-right',
					'top-left',
				);

				$count_position = (
					isset( $shortcode_attrs['badge_position'] ) &&
					$shortcode_attrs['badge_position'] &&
					in_array( $shortcode_attrs['badge_position'], $count_positions, true )
				) ?
				$shortcode_attrs['badge_position'] :
				'top-right';
				?>
				<span class="badge <?php echo esc_attr( $count_position ); ?>">
					<span class="adfy_wishlist-icon-items-count"><?php echo esc_html( count( $this->wishlist_items ) ); ?></span>
				</span>
			<?php } ?>
		</a>
		<?php
		return ob_get_clean();
	}


	/**
	 * Get wishlist button shortcode.
	 *
	 * @param array $atts Attributes.
	 */
	public function add_to_wishlist_button_shortcode_callback( $atts ) {

		global $product;

		if ( isset( $atts['product_id'] ) ) {
			$product = wc_get_product( (int) $atts['product_id'] );
		}

		if ( ! isset( $product ) || ! ( $product instanceof WC_Product ) ) {
			ob_start();
			echo esc_html__( 'Invalid product!', 'addonify-wishlist' );
			return ob_get_clean();
		}

		$shortcode_atts = shortcode_atts(
			array(
				'product_id'                       => '',
				'button_label'                     => $this->add_to_wishlist_button_label,
				'added_to_wishlist_button_label'   => $this->added_to_wishlist_button_label,
				'already_in_wishlist_button_label' => $this->already_in_wishlist_button_label,
				'classes'                          => '',
				'button_icon_position'             => 'none',
			),
			$atts,
			'addonify_wishlist_button'
		);

		$button_args = array(
			'product'                          => $product,
			'button_label'                     => $shortcode_atts['button_label'],
			'added_to_wishlist_button_label'   => $shortcode_atts['added_to_wishlist_button_label'],
			'already_in_wishlist_button_label' => $shortcode_atts['already_in_wishlist_button_label'],
			'data_attrs'                       => array(
				'button_label'                     => $shortcode_atts['button_label'],
				'added_to_wishlist_button_label'   => $shortcode_atts['added_to_wishlist_button_label'],
				'already_in_wishlist_button_label' => $shortcode_atts['already_in_wishlist_button_label'],
			),
			'classes'                          => $shortcode_atts['classes'] . ' adfy-wishlist-shortcode-btn',
			'button_icon_position'             => $shortcode_atts['button_icon_position'],
		);

		ob_start();
		do_action( 'addonify_wishlist_render_wishlist_button', $this->prepare_wishlist_button_template_args( $button_args ) );
		return ob_get_clean();
	}


	/**
	 * Render template for showing "added to wishlist" modal
	 *
	 * @since    1.0.0
	 */
	public function wishlist_modal_wrapper() {

		if ( ( addonify_wishlist_get_option( 'enable_save_for_later' ) !== '1' && is_cart() ) || is_checkout() ) {
			return;
		}

		do_action( 'addonify_wishlist_render_modal_wrapper' );
	}


	/**
	 * Render template for showing sticky sidebar
	 *
	 * @since    1.0.0
	 */
	public function wishlist_sidebar_template() {

		if (
			addonify_wishlist_get_option( 'show_sidebar' ) !== '1' ||
			get_the_ID() === (int) addonify_wishlist_get_option( 'wishlist_page' ) ||
			is_cart() ||
			is_checkout()
		) {
			return;
		}

		do_action( 'addonify_wishlist_render_sidebar_toggle_button', $this->wishlist_items );

		do_action( 'addonify_wishlist_render_sidebar', $this->wishlist_items );
	}


	/**
	 * Change redirect url after login.
	 *
	 * @since    1.0.0
	 * @param    string $redirect Redirect url.
	 */
	public function myaccount_login( $redirect ) {

		if ( isset( $_GET['addonify_wishlist_redirect'] ) && ! empty( $_GET['addonify_wishlist_redirect'] ) ) { //phpcs:ignore
			$redirect = sanitize_text_field( wp_unslash( $_GET['addonify_wishlist_redirect'] ) ); //phpcs:ignore
		}

		return $redirect;
	}


	/**
	 * Removes product from wishlist when product is added into the cart.
	 *
	 * @since 1.0.0
	 *
	 * @param string $cart_item_key Cart item key.
	 * @param int    $product_id Product id.
	 */
	public function remove_item_from_wishlist( $cart_item_key, $product_id ) {

		if ( addonify_wishlist_get_option( 'remove_from_wishlist_if_added_to_cart' ) === '1' ) {

			$this->wishlist_handler->remove_from_wishlist( $product_id );
		}
	}


	/**
	 * Returns the HTML for product removal undo notice.
	 *
	 * @since 2.0.6
	 */
	public function get_product_removal_undo_notice() {

		ob_start();
		do_action( 'addonify_wishlist_render_product_removal_undo_notice' );
		return ob_get_clean();
	}


	/**
	 * Returns the HTML for loader.
	 *
	 * @since 2.0.6
	 */
	public function get_loader() {
		ob_start();
		do_action( 'addonify_wishlist_render_loader' );
		return ob_get_clean();
	}


	/**
	 * Returns the HTML for wishlist modal template.
	 *
	 * @since 2.0.6
	 */
	public function get_modal_template() {
		ob_start();
		do_action( 'addonify_wishlist_render_modal_template' );
		return ob_get_clean();
	}


	/**
	 * Returns the HTML of wishlist link displayed in the added to wishlist or already in the wishlist modal.
	 *
	 * @since 2.0.6
	 */
	public function get_wishlist_link_modal_button() {
		ob_start();
		do_action( 'addonify_wishlist_modal_wishlist_link' );
		return ob_get_clean();
	}


	/**
	 * Returns the HTML of login link displayed in the login wishlist modal.
	 *
	 * @since 2.0.6
	 */
	public function get_login_link_modal_button() {
		ob_start();
		do_action( 'addonify_wishlist_modal_login_link' );
		return ob_get_clean();
	}


	/**
	 * Returns the HTML of confirm button displayed in the confirmation of action in the wishlist modal.
	 *
	 * @since 2.0.6
	 */
	public function get_wishlist_empty_confirm_modal_button() {
		ob_start();
		do_action( 'addonify_wishlist_modal_empty_wishlist_confirm_button' );
		return ob_get_clean();
	}


	/**
	 * Print dynamic CSS generated from settings page.
	 */
	public function dynamic_css() {

		$css_values = array(
			'--adfy_wishlist_wishlist_btn_text_color'      => addonify_wishlist_get_option( 'wishlist_btn_text_color' ),
			'--adfy_wishlist_wishlist_btn_text_color_hover' => addonify_wishlist_get_option( 'wishlist_btn_text_color_hover' ),
			'--adfy_wishlist_wishlist_btn_bg_color'        => addonify_wishlist_get_option( 'wishlist_btn_bg_color' ),
			'--adfy_wishlist_wishlist_btn_bg_color_hover'  => addonify_wishlist_get_option( 'wishlist_btn_bg_color_hover' ),
			'--adfy_wishlist_sidebar_modal_overlay_bg_color' => addonify_wishlist_get_option( 'sidebar_modal_overlay_bg_color' ),
			'--adfy_wishlist_popup_modal_overlay_bg_color' => addonify_wishlist_get_option( 'popup_modal_overlay_bg_color' ),
			'--adfy_wishlist_popup_modal_bg_color'         => addonify_wishlist_get_option( 'popup_modal_bg_color' ),
			'--adfy_wishlist_border_color'                 => addonify_wishlist_get_option( 'sidebar_modal_general_border_color' ),
			'--adfy_wishlist_popup_modal_close_btn_icon_color' => addonify_wishlist_get_option( 'popup_close_btn_icon_color' ),
			'--adfy_wishlist_popup_modal_close_btn_icon_color_hover' => addonify_wishlist_get_option( 'popup_close_btn_icon_color_on_hover' ),
			'--adfy_wishlist_popup_modal_icon_color'       => addonify_wishlist_get_option( 'popup_modal_icon_color' ),
			'--adfy_wishlist_success_icon_color'           => addonify_wishlist_get_option( 'popup_modal_success_icon_color' ),
			'--adfy_wishlist_alert_icon_color'             => addonify_wishlist_get_option( 'popup_modal_alert_icon_color' ),
			'--adfy_wishlist_error_icon_color'             => addonify_wishlist_get_option( 'popup_modal_error_icon_color' ),
			'--adfy_wishlist_info_icon_color'              => addonify_wishlist_get_option( 'popup_modal_info_icon_color' ),
			'--adfy_wishlist_popup_modal_text_color'       => addonify_wishlist_get_option( 'popup_modal_text_color' ),
			'--adfy_wishlist_popup_modal_btn_text_color'   => addonify_wishlist_get_option( 'popup_modal_btn_text_color' ),
			'--adfy_wishlist_popup_modal_btn_text_color_hover' => addonify_wishlist_get_option( 'popup_modal_btn_text_color_hover' ),
			'--adfy_wishlist_popup_modal_btn_bg_color'     => addonify_wishlist_get_option( 'popup_modal_btn_bg_color' ),
			'--adfy_wishlist_popup_modal_btn_bg_color_hover' => addonify_wishlist_get_option( 'popup_modal_btn_bg_color_hover' ),
			'--adfy_wishlist_sidebar_modal_toggle_btn_label_color' => addonify_wishlist_get_option( 'sidebar_modal_toggle_btn_label_color' ),
			'--adfy_wishlist_sidebar_modal_toggle_btn_label_color_hover' => addonify_wishlist_get_option( 'sidebar_modal_toggle_btn_label_color_hover' ),
			'--adfy_wishlist_sidebar_modal_toggle_btn_bg_color' => addonify_wishlist_get_option( 'sidebar_modal_toggle_btn_bg_color' ),
			'--adfy_wishlist_sidebar_modal_toggle_btn_bg_color_hover' => addonify_wishlist_get_option( 'sidebar_modal_toggle_btn_bg_color_hover' ),
			'--adfy_wishlist_sidebar_modal_bg_color'       => addonify_wishlist_get_option( 'sidebar_modal_bg_color' ),
			'--adfy_wishlist_sidebar_modal_title_color'    => addonify_wishlist_get_option( 'sidebar_modal_title_color' ),
			'--adfy_wishlist_sidebar_modal_empty_text_color' => addonify_wishlist_get_option( 'sidebar_modal_empty_text_color' ),
			'--adfy_wishlist_sidebar_modal_close_icon_color' => addonify_wishlist_get_option( 'sidebar_modal_close_icon_color' ),
			'--adfy_wishlist_sidebar_modal_close_icon_color_hover' => addonify_wishlist_get_option( 'sidebar_modal_close_icon_color_hover' ),
			'--adfy_wishlist_sidebar_modal_product_title_color' => addonify_wishlist_get_option( 'sidebar_modal_product_title_color' ),
			'--adfy_wishlist_sidebar_modal_product_title_color_hover' => addonify_wishlist_get_option( 'sidebar_modal_product_title_color_hover' ),
			'--adfy_wishlist_sidebar_modal_product_regular_price_color' => addonify_wishlist_get_option( 'sidebar_modal_product_regular_price_color' ),
			'--adfy_wishlist_sidebar_modal_product_sale_price_color' => addonify_wishlist_get_option( 'sidebar_modal_product_sale_price_color' ),
			'--adfy_wishlist_sidebar_modal_product_add_to_cart_label_color' => addonify_wishlist_get_option( 'sidebar_modal_product_add_to_cart_label_color' ),
			'--adfy_wishlist_sidebar_modal_product_add_to_cart_label_color_hover' => addonify_wishlist_get_option( 'sidebar_modal_product_add_to_cart_label_color_hover' ),
			'--adfy_wishlist_sidebar_modal_product_add_to_cart_bg_color' => addonify_wishlist_get_option( 'sidebar_modal_product_add_to_cart_bg_color' ),
			'--adfy_wishlist_sidebar_modal_product_add_to_cart_bg_color_hover' => addonify_wishlist_get_option( 'sidebar_modal_product_add_to_cart_bg_color_hover' ),
			'--adfy_wishlist_sidebar_modal_product_remove_from_wishlist_icon_color' => addonify_wishlist_get_option( 'sidebar_modal_product_remove_from_wishlist_icon_color' ),
			'--adfy_wishlist_sidebar_modal_product_remove_from_wishlist_icon_color_hover' => addonify_wishlist_get_option( 'sidebar_modal_product_remove_from_wishlist_icon_color_hover' ),
			'--adfy_wishlist_sidebar_modal_view_wishlist_btn_label_color' => addonify_wishlist_get_option( 'sidebar_modal_view_wishlist_btn_label_color' ),
			'--adfy_wishlist_sidebar_modal_view_wishlist_btn_label_color_hover' => addonify_wishlist_get_option( 'sidebar_modal_view_wishlist_btn_label_color_hover' ),
			'--adfy_wishlist_sidebar_modal_view_wishlist_btn_bg_color' => addonify_wishlist_get_option( 'sidebar_modal_view_wishlist_btn_bg_color' ),
			'--adfy_wishlist_sidebar_modal_view_wishlist_btn_bg_color_hover' => addonify_wishlist_get_option( 'sidebar_modal_view_wishlist_btn_bg_color_hover' ),
			'--adfy_wishlist_sidebar_modal_in_stock_text_color' => addonify_wishlist_get_option( 'sidebar_modal_in_stock_text_color' ),
			'--adfy_wishlist_sidebar_modal_out_of_stock_text_color' => addonify_wishlist_get_option( 'sidebar_modal_out_of_stock_text_color' ),
			'--adfy_wishlist_product_removed_notice_bg_color' => addonify_wishlist_get_option( 'notice_background_color' ),
			'--adfy_wishlist_product_removed_notice_text_color' => addonify_wishlist_get_option( 'notice_text_color' ),
			'--adfy_wishlist_product_removed_notice_undo_btn_text_color' => addonify_wishlist_get_option( 'undo_button_label_color' ),
			'--adfy_wishlist_product_removed_notice_undo_btn_hover_text_color' => addonify_wishlist_get_option( 'undo_button_label_color_hover' ),
			'--adfy_wishlist_product_removed_notice_undo_btn_bg_color' => addonify_wishlist_get_option( 'undo_button_background_color' ),
			'--adfy_wishlist_product_removed_notice_undo_btn_hover_bg_color' => addonify_wishlist_get_option( 'undo_button_background_color_hover' ),
		);

		$css = ':root {';

		foreach ( $css_values as $key => $value ) {

			if ( ! is_array( $value ) ) {
				$css .= $key . ': ' . $value . ';';
			}
		}

		$css .= '}';

		return $css;
	}


	/**
	 * Minify the dynamic css.
	 *
	 * @param string $css css to minify.
	 * @return string minified css.
	 */
	public function minify_css( $css ) {

		$css = preg_replace( '/\s+/', ' ', $css );
		$css = preg_replace( '/\/\*[^\!](.*?)\*\//', '', $css );
		$css = preg_replace( '/(,|:|;|\{|}) /', '$1', $css );
		$css = preg_replace( '/ (,|;|\{|})/', '$1', $css );
		$css = preg_replace( '/(:| )0\.([0-9]+)(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}.${2}${3}', $css );
		$css = preg_replace( '/(:| )(\.?)0(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}0', $css );

		return trim( $css );
	}
}
