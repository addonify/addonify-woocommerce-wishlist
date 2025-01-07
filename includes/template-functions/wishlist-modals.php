<?php
/**
 * Wishlist modal template functions.
 *
 * @since 2.0.14
 *
 * @package Addonify_Wishlist
 * @subpackage Addonify_Wishlist/includes/template-functions
 */

if ( ! function_exists( 'addonify_wishlist_modal_close_button_template' ) ) {
	/**
	 * Renders HTML template for displaying modal close button.
	 *
	 * @since 2.0.6
	 */
	function addonify_wishlist_modal_close_button_template() {

		$close_button = '<button type="button" id="addonify-wishlist-close-modal-btn" class="adfy-wl-btn adfy-wl-clear-btn-style">' . addonify_wishlist_escape_svg( addonify_wishlist_get_wishlist_icons( 'close-1' ) ) . '</button>';

		echo apply_filters( 'addonify_wishlist_modal_close_button', $close_button ); // phpcs:ignore
	}

	add_action( 'addonify_wishlist_render_modal_close_button', 'addonify_wishlist_modal_close_button_template' );
}

if ( ! function_exists( 'addonify_wishlist_modal_content_template' ) ) {
	/**
	 * Renders HTML template of wishlist modal.
	 *
	 * @since 2.0.6
	 */
	function addonify_wishlist_modal_content_template() {

		Addonify_Wishlist_Template_Loader::load_template(
			'addonify-wishlist-modal-content.php',
			apply_filters(
				'addonify_wishlist_modal_content_template_args',
				array()
			)
		);
	}
}

if ( ! function_exists( 'addonify_wishlist_modal_template' ) ) {
	/**
	 * Renders HTML template for modal content.
	 *
	 * @since 2.0.13
	 *
	 * @param string $modal Modal.
	 */
	function addonify_wishlist_modal_template( $modal ) {

		$args = array();

		switch ( $modal ) {
			case 'added_to_wishlist':
				$args = array(
					'added_to_wishlist_text'  => addonify_wishlist_get_option( 'product_added_to_wishlist_text' ),
					'wishlist_page_url'       => addonify_wishlist_get_wishlist_page_url(),
					'view_wishlist_btn_label' => addonify_wishlist_get_option( 'view_wishlist_btn_text' ),
				);
				break;
			case 'already_in_wishlist':
				$args = array(
					'already_in_wishlist_text' => addonify_wishlist_get_option( 'product_already_in_wishlist_text' ),
					'wishlist_page_url'        => addonify_wishlist_get_wishlist_page_url(),
					'view_wishlist_btn_label'  => addonify_wishlist_get_option( 'view_wishlist_btn_text' ),
				);
				break;
			case 'removed_from_wishlist':
				$args = array(
					'removed_from_wishlist_message' => addonify_wishlist_get_option( 'product_removed_from_wishlist_text' ),
				);
				break;
			case 'confirm_clear_wishlist':
				$args = array(
					'clear_wishlist_confirm_text'      => addonify_wishlist_get_option( 'confirmation_message_for_emptying_wishlist' ),
					'clear_wishlist_confirm_btn_label' => addonify_wishlist_get_option( 'confirm_btn_label' ),
				);
				break;
			case 'login_required':
				$args = array(
					'login_required_message' => addonify_wishlist_get_option( 'login_required_message' ),
					'login_url'              => ( get_option( 'woocommerce_myaccount_page_id' ) ) ? get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) : wp_login_url(),
					'login_btn_label'        => addonify_wishlist_get_option( 'login_btn_label' ),
				);
				break;
			default:
				break;

		}

		$modal = str_replace( '_', '-', $modal );

		Addonify_Wishlist_Template_Loader::load_template( "wishlist-modals/{$modal}.php", $args );
	}
}


if ( ! function_exists( 'addonify_wishlist_render_popup_wishlist_link_button' ) ) {
	/**
	 * Render confirm button that is displayed in added to wishlist or already in the wishlist modal.
	 */
	function addonify_wishlist_render_popup_wishlist_link_button() {

		// If login is not required, display link button to wishlist page.
		if (
			(int) addonify_wishlist_get_option( 'require_login' ) === 1 &&
			! is_user_logged_in()
		) {

			return;
		}

		$wishlist_page_url = addonify_wishlist_get_option( 'wishlist_page' ) ? get_permalink( (int) addonify_wishlist_get_option( 'wishlist_page' ) ) : '';

		$view_wishlist_button_label = addonify_wishlist_get_option( 'view_wishlist_btn_text' );

		Addonify_Wishlist_Template_Loader::load_template(
			'addonify-popup-wishlist-link-button.php',
			apply_filters(
				'addonify_wishlist_popup_wishlist_link_button_args',
				array(
					'wishlist_page_url'          => $wishlist_page_url,
					'view_wishlist_button_label' => $view_wishlist_button_label,
				)
			)
		);
	}
}


if ( ! function_exists( 'addonify_wishlist_render_popup_login_link_button' ) ) {
	/**
	 * Render login link that is displayed in login modal.
	 */
	function addonify_wishlist_render_popup_login_link_button() {

		// If login is not required, display link button to wishlist page.
		if (
			(int) addonify_wishlist_get_option( 'require_login' ) === 0 ||
			is_user_logged_in()
		) {
			return;
		}

		global $wp;

		$redirect_url = add_query_arg(
			'addonify_wishlist_redirect',
			home_url( $wp->request ),
			( get_option( 'woocommerce_myaccount_page_id' ) ) ? get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) : wp_login_url()
		);

		Addonify_Wishlist_Template_Loader::load_template(
			'addonify-popup-login-link-button.php',
			apply_filters(
				'addonify_wishlist_popup_wishlist_link_button_args',
				array(
					'redirect_url'       => $redirect_url,
					'login_button_label' => apply_filters( 'addonify_wishlist_popup_login_button_label', addonify_wishlist_get_option( 'login_btn_label' ) ),
				)
			)
		);
	}
}


if ( ! function_exists( 'addonify_wishlist_render_popup_empty_wishlist_confirm_button' ) ) {
	/**
	 * Render confirm button that is displayed in empty wishlist confirmation modal.
	 *
	 * @since 2.0.6
	 */
	function addonify_wishlist_render_popup_empty_wishlist_confirm_button() {

		Addonify_Wishlist_Template_Loader::load_template(
			'addonify-popup-empty-wishlist-confirm-button.php',
			apply_filters(
				'addonify_wishlist_popup_empty_wishlist_confirm_button_args',
				array(
					'button_label' => apply_filters(
						'addonify_wishlist_popup_empty_wishlist_confirm_button_label',
						addonify_wishlist_get_option( 'confirm_btn_label' )
					),
				)
			)
		);
	}
}


if ( ! function_exists( 'addonify_wishlist_modal_wrapper_start_template' ) ) {
	/**
	 * Renders modal wrapper start.
	 *
	 * @since 2.0.14
	 *
	 * @param string $css_class CSS class.
	 */
	function addonify_wishlist_modal_wrapper_start_template( $css_class = 'adfy-wl-modal-info' ) {

		?>
		<div
			id="adfy-wl-modal-content-wrapper"
			class="<?php echo esc_attr( apply_filters( 'addonify_wishlist_modal_content_wrapper_css_class', $css_class ) ); ?>"
			data-modal_width="default"
			data-modal_display="open"
		>
			
			<?php do_action( 'addonify_wishlist_after_popup_opening_tag' ); ?>

			<div class="adfy-wl-modal-content">
				<div class="adfy-wl-modal-close">
					<?php do_action( 'addonify_wishlist_render_modal_close_button' ); ?>
				</div><!-- .adfy-modal-close -->
				<div id="adfy-wl-modal-body" class="adfy-wl-modal-body">
		<?php
	}
}

if ( ! function_exists( 'addonify_wishlist_modal_wrapper_end_template' ) ) {
	/**
	 * Renders modal wrapper end.
	 *
	 * @since 2.0.14
	 */
	function addonify_wishlist_modal_wrapper_end_template() {
		?>
				</div><!-- #adfy-wl-modal-body.adfy-wl-modal-body -->
			</div><!-- .adfy-wl-modal-content -->
			<?php do_action( 'addonify_wishlist_before_popup_closing_tag' ); ?>
		</div><!-- #adfy-wl-modal-content-wrapper -->
		<?php
	}
}
