<?php
/**
 * Public template.
 *
 * @link       https://www.addonify.com
 * @since      1.0.0
 *
 * @package    Addonify_Wishlist
 * @subpackage Addonify_Wishlist/public/templates
 */

// direct access is disabled.
defined( 'ABSPATH' ) || exit;
?>

<button id="adfy-wl-sidebar-toggle-btn" class="adfy-wl-clear-btn-style <?php echo esc_attr( $css_classes ); ?>">
	<?php
	if ( $show_icon ) {
		?>
		<span class="button-icon">
			<?php echo addonify_wishlist_escape_svg( $icon ); // phpcs:ignore ?>
		</span>
		<?php
	}
	?>
	<span class="button-label"><?php echo esc_html( $label ); ?></span>
</button>

