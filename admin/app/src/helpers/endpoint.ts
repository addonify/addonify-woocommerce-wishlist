/**
 *
 * Base REST API endpoint for fetching and saving settings.
 * Receiving the locoalized data from WordPress.
 *
 * @since 2.0.0
 */

export const apiEndpoint: string = "https://addonify.com/wp-json/addonify/v1";

/**
 *
 * Endpoint: Get sale notification.
 * Used to display sale badge in sidebar.
 *
 * @since 2.0.0
 */

export const saleEndpoint: string =
	"https://creamcode.org/upsell/v1/sale-notification.json";

/**
 *
 * Upgrade to pro landing page.
 * Used in upsell buttons.
 *
 * @since 2.0.0
 */

export const upgradeLanding: string =
	"https://addonify.com/downloads/addonify-wishlist-pro/";

/**
 *
 * Global Documentation site guide link.
 * Used section tiles in dashboard.
 *
 * @since 2.0.0
 */

export const docsLanding: string = "https://docs.addonify.com/";
