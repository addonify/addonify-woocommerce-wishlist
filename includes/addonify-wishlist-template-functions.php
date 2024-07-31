<?php
/**
 * The file that defines the template functions.
 *
 * @link       https://www.addonify.com
 * @since      1.0.0
 *
 * @package    Addonify_Wishlist
 * @subpackage Addonify_Wishlist/includes
 */

if ( ! function_exists( 'addonify_wishlist_locate_template' ) ) {
	/**
	 * Locates template.
	 *
	 * @param string $template_name Template name.
	 * @param string $template_path Template path.
	 * @param string $default_path Default path.
	 */
	function addonify_wishlist_locate_template( $template_name, $template_path = '', $default_path = '' ) {

		// Set template location for theme.
		if ( empty( $template_path ) ) :
			$template_path = 'addonify/';
		endif;

		// Set default plugin templates path.
		if ( ! $default_path ) :
			$default_path = plugin_dir_path( __DIR__ ) . 'public/templates/'; // Path to the template folder.
		endif;

		// Search template file in theme folder.
		$template = locate_template(
			array(
				$template_path . $template_name,
				$template_name,
			)
		);

		// Get plugins template file.
		if ( ! $template ) :
			$template = $default_path . $template_name;
		endif;

		return apply_filters( 'addonify_wishlist_locate_template', $template, $template_name, $template_path, $default_path );
	}
}


if ( ! function_exists( 'addonify_wishlist_get_template' ) ) {
	/**
	 * Return template content if found.
	 *
	 * @param string $template_name Template name.
	 * @param array  $args Arguments.
	 * @param string $tempate_path Template path.
	 * @param string $default_path Default path.
	 */
	function addonify_wishlist_get_template( $template_name, $args = array(), $tempate_path = '', $default_path = '' ) {

		if ( is_array( $args ) && isset( $args ) ) :
			extract( $args ); //phpcs:ignore
		endif;

		$template_file = addonify_wishlist_locate_template( $template_name, $tempate_path, $default_path );

		if ( ! file_exists( $template_file ) ) :
			_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $template_file ), '1.0.0' ); //phpcs:ignore
			return;
		endif;

		require $template_file;
	}
}


if ( ! function_exists( 'addonify_wishlist_render_add_to_wishlist_button' ) ) {
	/**
	 * Render add to wishlist button.
	 *
	 * @param array $button_template_args  Button template args.
	 */
	function addonify_wishlist_render_add_to_wishlist_button( $button_template_args ) {

		addonify_wishlist_get_template(
			'addonify-add-to-wishlist-button.php',
			apply_filters(
				'addonify_wishlist_add_to_wishlist_button_args',
				$button_template_args
			)
		);
	}
}


if ( ! function_exists( 'addonify_wishlist_render_wishlist_content' ) ) {
	/**
	 * Render wishlist content.
	 *
	 * @param array $wishlist_product_ids Wishlist product ids.
	 */
	function addonify_wishlist_render_wishlist_content( $wishlist_product_ids = array() ) {

		addonify_wishlist_get_template(
			'addonify-wishlist-shortcode-contents.php',
			apply_filters(
				'addonify_wishlist_shortcode_contents_args',
				array(
					'wishlist_product_ids' => $wishlist_product_ids,
				)
			)
		);
	}
}


if ( ! function_exists( 'addonify_wishlist_render_wishlist_page_loop' ) ) {
	/**
	 * Render wishlist content.
	 *
	 * @since 2.0.6
	 *
	 * @param array $wishlist_product_ids Product IDs in the wishlist.
	 */
	function addonify_wishlist_render_wishlist_page_loop( $wishlist_product_ids = array() ) {

		addonify_wishlist_get_template(
			'addonify-wishlist-page-loop.php',
			apply_filters(
				'addonify_wishlist_page_loop_args',
				array(
					'wishlist_product_ids' => $wishlist_product_ids,
				)
			)
		);
	}
}


if ( ! function_exists( 'addonify_wishlist_render_modal_wrapper' ) ) {
	/**
	 * Render modal wrapper.
	 */
	function addonify_wishlist_render_modal_wrapper() {

		$css_class = ( (int) addonify_wishlist_get_option( 'require_login' ) && ! is_user_logged_in() ) ? 'require-login' : '';

		addonify_wishlist_get_template(
			'addonify-wishlist-modal-wrapper.php',
			apply_filters(
				'addonify_wishlist_modal_wrapper_args',
				array(
					'css_classes' => $css_class,
				)
			)
		);
	}
}


if ( ! function_exists( 'addonify_wishlist_render_sidebar_toggle_button' ) ) {
	/**
	 * Render sidebar toggle button.
	 *
	 * @param array $wishlist_product_ids Wishlist product ids.
	 */
	function addonify_wishlist_render_sidebar_toggle_button( $wishlist_product_ids = array() ) {

		$alignment = 'addonify-align-' . addonify_wishlist_get_option( 'sidebar_position' );

		$css_classes = array( $alignment );

		$total_items = count( $wishlist_product_ids );

		$css_classes[] = ( $total_items < 1 && empty( $product_ids ) ) ? 'hidden' : '';

		addonify_wishlist_get_template(
			'addonify-wishlist-sidebar-toggle-button.php',
			apply_filters(
				'addonify_wishlist_sidebar_toggle_button_args',
				array(
					'css_classes' => implode( ' ', $css_classes ),
					'label'       => addonify_wishlist_get_option( 'sidebar_btn_label' ),
					'show_icon'   => (int) addonify_wishlist_get_option( 'sidebar_show_icon' ),
					'icon'        => addonify_wishlist_get_wishlist_icons( addonify_wishlist_get_option( 'sidebar_toggle_btn_icon' ) ),
				)
			)
		);
	}
}


if ( ! function_exists( 'addonify_wishlist_render_sidebar' ) ) {
	/**
	 * Render sidebar.
	 *
	 * @param array $wishlist_product_ids Wishlist product ids.
	 */
	function addonify_wishlist_render_sidebar( $wishlist_product_ids = array() ) {

		if ( get_the_ID() === (int) addonify_wishlist_get_option( 'wishlist_page' ) ) {
			// do not show sidebar in wishlist page.
			return;
		}

		if ( (int) addonify_wishlist_get_option( 'show_sidebar' ) !== 1 ) {
			return;
		}

		addonify_wishlist_get_template(
			'addonify-wishlist-sidebar.php',
			apply_filters(
				'addonify_wishlist_sidebar_args',
				array(
					'total_items'                     => count( $wishlist_product_ids ),
					'css_class'                       => 'addonify-align-' . addonify_wishlist_get_option( 'sidebar_position' ),
					'title'                           => addonify_wishlist_get_option( 'sidebar_title' ),
					'wishlist_url'                    => ( addonify_wishlist_get_option( 'wishlist_page' ) ) ? get_permalink( addonify_wishlist_get_option( 'wishlist_page' ) ) : '',
					'alignment'                       => 'addonify-align-' . addonify_wishlist_get_option( 'sidebar_position' ),
					'view_wishlist_page_button_label' => addonify_wishlist_get_option( 'view_wishlist_page_button_label' ),
					'product_ids'                     => $wishlist_product_ids,
				)
			)
		);
	}
}


if ( ! function_exists( 'addonify_wishlist_render_sidebar_loop' ) ) {
	/**
	 * Render sidebar loop.
	 *
	 * @param array $wishlist_product_ids Product ids.
	 */
	function addonify_wishlist_render_sidebar_loop( $wishlist_product_ids = array() ) {

		addonify_wishlist_get_template(
			'addonify-wishlist-sidebar-loop.php',
			apply_filters(
				'addonify_wishlist_sidebar_loop_args',
				array(
					'wishlist_product_ids' => $wishlist_product_ids,
				)
			)
		);
	}
}


if ( ! function_exists( 'addonify_wishlist_render_single_wishlist_button' ) ) {
	/**
	 * Render single wishlist button.
	 */
	function addonify_wishlist_render_single_wishlist_button() {

		addonify_wishlist_get_template(
			'addonify-wishlist-sidebar-loop.php',
			apply_filters(
				'addonify_wishlist_sidebar_loop_args',
				array(
					'wishlist_product_ids' => isset( $wishlist_product_ids ) ? $wishlist_product_ids : array(),
				)
			)
		);
	}
}


if ( ! function_exists( 'addonify_wishlist_render_sidebar_product' ) ) {
	/**
	 * Render single wishlist button.
	 *
	 * @param string $product_id Product id.
	 * @param bool   $guest False if user is logged in, true otherwise.
	 */
	function addonify_wishlist_render_sidebar_product( $product_id, $guest = false ) {

		$product            = wc_get_product( $product_id );
		$product_avaibility = addonify_wishlist_get_product_avaibility( $product );

		ob_start();
		?>
		<li
			class="addonify-wishlist-sidebar-item"
			data-product_row="addonify-wishlist-sidebar-product-row-<?php echo esc_attr( $product_id ); ?>"
			data-product_name="<?php echo esc_attr( $product->get_name() ); ?>"
		>
			<div class="adfy-wishlist-row">
				<div class="adfy-wishlist-col image-column">
					<div class="adfy-wishlist-woo-image">
						<?php
						$product_post_thumbnail_id = $product->get_image_id();
						if ( $product->get_image() ) {
							?>
							<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
								<?php echo wp_kses_post( $product->get_image( array( 72, 72 ) ) ); ?>
							</a>
							<?php
						}
						?>
					</div>
				</div>
				<div class="adfy-wishlist-col title-price-column">
					<div class="adfy-wishlist-woo-title">
						<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
							<?php echo wp_kses_post( $product->get_title() ); ?>
						</a>
					</div>
					<div class="adfy-wishlist-woo-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
					<?php
					if ( $product_avaibility ) {
						?>
						<div class="adfy-wishlist-woo-stock">
							<span class="stock-label <?php echo esc_attr( $product_avaibility['class'] ); ?>">
								<?php echo esc_html( $product_avaibility['avaibility'] ); ?>
							</span>
						</div>
						<?php
					}
					?>
				</div>
			</div>

			<div class="adfy-wishlist-woo-action">
				<div class="adfy-wishlist-row">
					<div class="adfy-wishlist-col cart-column">
						<?php
						echo do_shortcode( '[add_to_cart id=' . $product->get_id() . ' show_price=false style="" class="adfy-wishlist-clear-shortcode-button-style adfy-wishlist-btn addonify-wishlist-add-to-cart addonify-wishlist-sidebar-button"]' );
						?>
					</div>
					<div class="adfy-wishlist-col remove-item-column">
						<?php
						$remove_from_wishlist_class = $guest ? ' addonify-wishlist-remove-from-wishlist ' : ' adfy-wishlist-remove-btn addonify-wishlist-ajax-remove-from-wishlist ';
						?>
						<button
							class="adfy-wishlist-btn adfy-wishlist-remove-btn addonify-wishlist-icon <?php echo esc_attr( $remove_from_wishlist_class ); ?> addonify-wishlist-sidebar-button"
							name="addonify_wishlist_remove"
							data-product_name="<?php echo wp_kses_post( $product->get_title() ); ?>"
							value="<?php echo esc_attr( $product->get_id() ); ?>"
						>
							<i class="adfy-wishlist-icon trash-2"></i>
						</button>
					</div>
				</div>
			</div>
		</li>
		<?php
		return ob_get_clean();
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

		addonify_wishlist_get_template(
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

		addonify_wishlist_get_template(
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

		addonify_wishlist_get_template(
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



if ( ! function_exists( 'addonify_wishlist_render_empty_wishlist_content_template' ) ) {
	/**
	 * Renders HTML template for displaying text when wishlist is empty.
	 *
	 * @since 2.0.6
	 */
	function addonify_wishlist_render_empty_wishlist_content_template() {

		$template  = '<p id="addonify-empty-wishlist-para">';
		$template .= esc_html( addonify_wishlist_get_option( 'empty_wishlist_label' ) );

		if (
			addonify_wishlist_get_option( 'show_empty_wishlist_navigation_link' ) === '1' &&
			! empty( addonify_wishlist_get_option( 'empty_wishlist_navigation_link' ) )
		) {
			$page_link = get_permalink( addonify_wishlist_get_option( 'empty_wishlist_navigation_link' ) );

			$template .= " <a href='" . esc_url( $page_link ) . "'>" . esc_html( addonify_wishlist_get_option( 'empty_wishlist_navigation_link_label' ) ) . '</a>';
		}

		$template .= '</p>';

		echo wp_kses_post( $template );
	}

	add_action( 'addonify_wishlist_empty_wishlist_content', 'addonify_wishlist_render_empty_wishlist_content_template' );
}


if ( ! function_exists( 'addonify_wishlist_modal_close_button_template' ) ) {
	/**
	 * Renders HTML template for displaying modal close button.
	 *
	 * @since 2.0.6
	 */
	function addonify_wishlist_modal_close_button_template() {

		$close_button = '<button type="button" id="addonify-wishlist-close-modal-btn" class="adfy-wishlist-btn adfy-wishlist-clear-button-style">' . addonify_wishlist_escape_svg( addonify_wishlist_get_wishlist_icons( 'close-1' ) ) . '</button>';

		echo apply_filters( 'addonify_wishlist_modal_close_button', $close_button ); // phpcs:ignore
	}

	add_action( 'addonify_wishlist_render_modal_close_button', 'addonify_wishlist_modal_close_button_template' );
}


if ( ! function_exists( 'addonify_wishlist_loader_template' ) ) {
	/**
	 * Renders HTML template for loader/spinner in wishlist sidebar and table.
	 *
	 * @since 2.0.6
	 */
	function addonify_wishlist_loader_template() {
		?>
		<div id="addonify-wishlist_spinner">
			<?php echo apply_filters( 'addonify_wishlist_loader', '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M2 11h5v2H2zm15 0h5v2h-5zm-6 6h2v5h-2zm0-15h2v5h-2zM4.222 5.636l1.414-1.414 3.536 3.536-1.414 1.414zm15.556 12.728-1.414 1.414-3.536-3.536 1.414-1.414zm-12.02-3.536 1.414 1.414-3.536 3.536-1.414-1.414zm7.07-7.071 3.536-3.535 1.414 1.415-3.536 3.535z"></path></svg>' ); // phpcs:ignore ?>
		</div>
		<?php
	}

	add_action( 'addonify_wishlist_render_loader', 'addonify_wishlist_loader_template' );
}


if ( ! function_exists( 'addonify_wishlist_modal_content_template' ) ) {
	/**
	 * Renders HTML template of wishlist modal.
	 *
	 * @since 2.0.6
	 */
	function addonify_wishlist_modal_content_template() {

		addonify_wishlist_get_template(
			'addonify-wishlist-modal-content.php',
			apply_filters(
				'addonify_wishlist_modal_content_template_args',
				array()
			)
		);
	}
}



if ( ! function_exists( 'addonify_wishlist_sidebar_product_row_template' ) ) {
	/**
	 * Renders HTML template of product row displayed in wishlist sidebar.
	 *
	 * @since 2.0.6
	 *
	 * @param object $product WC_Product object.
	 */
	function addonify_wishlist_sidebar_product_row_template( $product ) {
		?>
		<li
			id="adfy-wishlist-sidebar-product-row-<?php echo esc_attr( $product->get_id() ); ?>"
			class="addonify-wishlist-sidebar-item"
			data-product_row="addonify-wishlist-sidebar-product-row-<?php echo esc_attr( $product->get_id() ); ?>"
			data-product_name="<?php echo esc_attr( $product->get_name() ); ?>"
		>
			<div class="adfy-wishlist-row">
				<div class="adfy-wishlist-col image-column">
					<div class="adfy-wishlist-woo-image">
						<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
							<?php echo wp_kses_post( $product->get_image( array( 82, 82 ) ) ); ?>
						</a>
					</div>
				</div>
				<div class="adfy-wishlist-col title-price-column">
					<div class="adfy-wishlist-woo-title">
						<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
							<?php echo wp_kses_post( $product->get_name() ); ?>
						</a>
					</div>
					<div class="adfy-wishlist-woo-price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
					<?php
					$product_avaibility = addonify_wishlist_get_product_avaibility( $product );
					if (
						isset( $product_avaibility['class'] ) &&
						isset( $product_avaibility['avaibility'] )
					) {
						?>
						<div class="adfy-wishlist-woo-stock">
							<span class="stock-label <?php echo esc_attr( $product_avaibility['class'] ); ?>">
								<?php echo esc_html( $product_avaibility['avaibility'] ); ?>
							</span>
						</div>
						<?php
					}
					?>
				</div>
			</div>

			<div class="adfy-wishlist-woo-action">
				<div class="adfy-wishlist-row">
					<div class="adfy-wishlist-col cart-column">
						<?php
						echo do_shortcode( '[add_to_cart id=' . $product->get_id() . ' show_price=false style="" class="adfy-wishlist-clear-shortcode-button-style adfy-wishlist-btn addonify-wishlist-add-to-cart addonify-wishlist-sidebar-button"]' );
						?>
					</div>
					<div class="adfy-wishlist-col remove-item-column">
						<button
							class="adfy-wishlist-btn addonify-wishlist-icon addonify-wishlist-ajax-remove-from-wishlist addonify-wishlist-sidebar-button"
							name="addonify_wishlist_remove"
							data-product_id="<?php echo esc_attr( $product->get_id() ); ?>"
							data-product_name="<?php echo esc_attr( $product->get_name() ); ?>"
							data-source="wishlist-sidebar"
						>
							<?php echo addonify_wishlist_escape_svg( addonify_wishlist_get_wishlist_icons( 'bin-5' ) ); // phpcs:ignore ?>
						</button>
					</div>
				</div>
			</div>
		</li>
		<?php
	}

	add_action( 'addonify_wishlist_render_sidebar_product_row', 'addonify_wishlist_sidebar_product_row_template' );
}


if ( ! function_exists( 'addonify_wishlist_table_product_row_template' ) ) {
	/**
	 * Renders HTML template of product row displayed in table in the wishlist page.
	 *
	 * @since 2.0.6
	 *
	 * @param object $product WC_Product object.
	 */
	function addonify_wishlist_table_product_row_template( $product ) {
		?>
		<tr
			id="adfy-wishlist-table-product-row-<?php echo esc_attr( $product->get_id() ); ?>"
			class="addonify-wishlist-table-product-row"
			data-product_row="addonify-wishlist-table-product-row-<?php echo esc_attr( $product->get_id() ); ?>"
			data-product_name="<?php echo esc_attr( $product->get_name() ); ?>"
		>
			<td class="remove">
				<button 
					class="adfy-wishlist-btn addonify-wishlist-icon addonify-wishlist-ajax-remove-from-wishlist addonify-wishlist-table-button"
					name="addonify_wishlist_remove"
					data-product_id="<?php echo esc_attr( $product->get_id() ); ?>"
					data-product_name="<?php echo esc_attr( $product->get_name() ); ?>"
					data-source="wishlist-table"
				>
					<?php echo addonify_wishlist_escape_svg( addonify_wishlist_get_wishlist_icons( 'bin-5' ) ); // phpcs:ignore ?>
				</button>
			</td>
			<td class="image">
				<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
					<?php echo wp_kses_post( $product->get_image( array( 128, 128 ) ) ); ?>
				</a>
			</td>
			<td class="name">
				<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
					<?php echo wp_kses_post( $product->get_name() ); ?>
				</a>
			</td>
			<td class="price">
				<?php echo wp_kses_post( $product->get_price_html() ); ?>
			</td>
			<td class="stock">
				<?php
				$product_avaibility = addonify_wishlist_get_product_avaibility( $product );
				if (
					isset( $product_avaibility['class'] ) &&
					isset( $product_avaibility['avaibility'] )
				) {
					?>
					<span class="stock-label <?php echo esc_attr( $product_avaibility['class'] ); ?>">
						<?php echo esc_html( $product_avaibility['avaibility'] ); ?>
					</span>
					<?php
				}
				?>
			</td>
			<td class="actions">
				<?php
				echo do_shortcode( '[add_to_cart id=' . $product->get_id() . ' show_price=false style="" class="adfy-wishlist-clear-shortcode-button-style adfy-wishlist-btn addonify-wishlist-add-to-cart addonify-wishlist-table-button"]' );
				?>
			</td>
		</tr>
		<?php
	}

	add_action( 'addonify_wishlist_render_table_product_row', 'addonify_wishlist_table_product_row_template' );
}



if ( ! function_exists( 'addonify_wishlist_product_removal_undo_notice_template' ) ) {
	/**
	 * Renders HTML template of product removal undo notice.
	 *
	 * @since 2.0.6
	 */
	function addonify_wishlist_product_removal_undo_notice_template() {
		?>
		<p>
			<?php echo esc_html( addonify_wishlist_get_option( 'undo_action_prelabel_text' ) ); ?>
			<?php
			if ( ! empty( addonify_wishlist_get_option( 'undo_action_label' ) ) ) {
				?>
				<a
					href="#"
					id="addonify-wishlist-undo-deleted-product-link"
				><?php echo esc_html( addonify_wishlist_get_option( 'undo_action_label' ) ); ?></a>
				<?php
			}
			?>
		</p>
		<?php
	}

	add_action(
		'addonify_wishlist_render_product_removal_undo_notice',
		'addonify_wishlist_product_removal_undo_notice_template'
	);
}
