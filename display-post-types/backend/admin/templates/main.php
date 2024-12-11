<?php
/**
 * Display Post Types options page
 *
 * @package Display Post Types
 * @since 2.5.0
 */

use Display_Post_Types\Helper\Markup as Markup_Fn;

?>

<div id="dpt-options-page" class="dpt-options-page">
	<div class="dpt-options-header">
		<div class="dpt-options-title">
			<h3><a class="dpt-options-title-link" href="https://easyprolabs.com/display-post-types/" target="_blank"><?php esc_html_e( 'Display Post Types', 'display-post-types' ); ?></a></h3>
		</div>
		<div class="dpt-options-links">
			<a class="dpt-options-link" href="https://wordpress.org/support/plugin/display-post-types/" target="_blank"></a>
		</div>
	</div>
	<div class="dpt-options-main">
		<div id="dpt-options-content" class="dpt-options-content">
			<ul class="dpt-options-menu">
				<?php
				foreach ( $this->modules as $key => $args ) {
					printf(
						'<li class="dpt-module-item"><a href="%1$s" class="dpt-module-item-link"><span class="dpt-module-text">%2$s</span></a></li>',
						esc_url( admin_url( 'admin.php?page=dpt-' . $key ) ),
						esc_html( $args['label'] )
					);
				}
				?>
			</ul>
			<div class="dpt-options-content-wrapper">
				<div class="dpt-options-content-area">
					<?php
					$located = Markup_Fn::locate_admin_template( $current_page );
					if ( $located ) {
						printf( '<div id="dpt-options-module-%s" class="dpt-module-content">', esc_attr( $current_page ) );
						include_once $located;
						echo '</div>';
					}
					?>
				</div>
				<div class="dpt-options-footer">
					<div class="dpt-options-copyright"><span><?php esc_html_e( 'EasyPro Labs', 'display-post-types' ); ?> &copy; <?php echo esc_html( date_i18n( __( 'Y', 'display-post-types' ) ) ); ?></span></div>
				</div>
			</div>
		</div>
		<?php if ( $current_page === 'home' && ! defined( 'DPT_PRO_VERSION' ) ) { ?>
		<div class="dpt-options-sidebar">
			<?php require DISPLAY_POST_TYPES_DIR . '/backend/admin/templates/sidebar.php'; ?>
		</div>
		<?php } ?>
	</div>
	<div class="dpt-action-feedback" id="dpt-action-feedback">
		<span class="dashicons dashicons-update"></span>
		<span class="dashicons dashicons-no"></span>
		<span class="dashicons dashicons-yes"></span>
		<span class="dpt-feedback"></span>
		<span class="dpt-error-close"><span class="dashicons dashicons-no"></span></span>
	</div>
</div>
