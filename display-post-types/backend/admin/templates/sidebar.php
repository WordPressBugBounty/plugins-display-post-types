<?php
/**
 * Display Post Types sidebar
 *
 * @package Display Post Types
 * @since 2.5.0
 */

?>

<div class="dpt-sidebar-section">
	<?php
	if ( function_exists( 'dpt_pro_license_options' ) ) {
		dpt_pro_license_options();
	} else {
		?>
		<h3 class="dpt-pro-title"><?php esc_html_e( 'Upgrade to Display Post Types Pro', 'display-post-types' ); ?></h3>
		<ul class="dpt-pro-features">
			<li>Better looking <?php $this->mlink( 'https://vedathemes.com/display-post-types-demo/', 'professional layouts', 'dpt-pro-link' ); ?>.</li>
			<li>Layout customization options.</li>
			<li>Typography and color customization options.</li>
			<li>Support for displaying custom fields and shortcodes.</li>
			<li>Priority Email Support</li>
			<li>And much more</li>
		</ul>
		<?php $this->mlink( 'https://vedathemes.com/display-post-types', 'Buy Now', 'button dpt-pro-more' ); ?>
		<?php
	}
	?>
</div>