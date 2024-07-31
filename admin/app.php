<?php
/**
 *
 * Check if the current page is udp engine page.
 *
 * @package Addonify_Wishlist
 * @since 1.0.1
 */

use Kucrut\Vite;


/**
*
* Enqueue admin scripts if the current page is addonify wishlist page.
*
* @since 2.0.0
*/

if ( isset( $_GET['page'] ) && 'addonify_wishlist' === $_GET['page'] ) { //phpcs:ignore
	$handle = 'addonify-wishlist-admin';
	add_action(
		'admin_enqueue_scripts',
		function () use ( $handle ): void {
			Vite\enqueue_asset(
				__DIR__ . '/app/dist',
				'admin/app/src/main.js',
				array(
					'handle'           => $handle,
					'dependencies'     => array( 'lodash', 'wp-api-fetch', 'wp-i18n' ), // Dependencies.
					'css-dependencies' => array(), // Optional style dependencies. Defaults to empty array.
					'css-media'        => 'all', // Optional.
					'css-only'         => false, // Optional. Set to true to only load style assets in production mode.
					'in-footer'        => true, // Optional. Defaults to false.
				)
			);

			wp_localize_script(
				$handle,
				'addonify_wishlist_localizer',
				array(
					'admin_url'      => admin_url( '/' ),
					'ajax_url'       => admin_url( 'admin-ajax.php' ),
					'site_url'       => site_url( '/' ),
					'rest_namespace' => 'addonify_wishlist_options_api/v2',
					'version_number' => ADDONIFY_WISHLIST_VERSION,
				)
			);
		}
	);
}
