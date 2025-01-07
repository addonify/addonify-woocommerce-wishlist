<?php
/**
 * Frontend template loader class.
 *
 * @link       https://www.addonify.com
 * @since      2.0.14
 *
 * @package    Addonify_Wishlist
 * @subpackage Addonify_Wishlist/includes
 */

/**
 * Class - Addonify_Wishlist_Template_Loader.
 *
 * Template loader.
 *
 * @since 2.0.14
 */
class Addonify_Wishlist_Template_Loader {

	/**
	 * Load a template, allowing overrides via themes or other plugins.
	 *
	 * @param string $template_name The relative path to the template file.
	 * @param array  $args Optional. Arguments to pass to the template.
	 */
	public static function load_template( $template_name, $args = array() ) {

		// Look for the template in theme directories first.
		$theme_template = self::locate_template( $template_name );

		// Use the overridden template if found, otherwise use the plugin's default.
		$template_path = $theme_template ? $theme_template : ADDONIFY_WISHLIST_PLUGIN_PATH . '/public/templates/' . $template_name;

		// Pass arguments to the template.
		self::include_template( $template_path, $args );
	}

	/**
	 * Locate a template in the theme or child theme, supporting subfolders.
	 *
	 * @param string $template_name The relative path to the template file.
	 * @return string|false The path to the template if found, or false.
	 */
	public static function locate_template( $template_name ) {

		// Check in child theme and parent theme directories.
		$theme_paths = array(
			get_stylesheet_directory() . '/addonify/' . $template_name, // Child theme.
			get_template_directory() . '/addonify/' . $template_name,  // Parent theme.
		);

		foreach ( $theme_paths as $path ) {
			if ( file_exists( $path ) ) {
				return $path; // Return the first match found.
			}
		}

		return false; // No overrides found.
	}

	/**
	 * Include the template file with arguments.
	 *
	 * @param string $template_path The path to the template file.
	 * @param array  $args Optional. Arguments to pass to the template.
	 */
	private static function include_template( $template_path, $args = array() ) {

		if ( ! empty( $args ) && is_array( $args ) ) {
			extract( $args, EXTR_SKIP ); // phpcs:ignore
		}

		if ( file_exists( $template_path ) ) {
			include $template_path; // Include the template file.
		}
	}
}
