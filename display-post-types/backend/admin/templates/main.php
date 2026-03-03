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
	<div class="min-h-[calc(100vh-32px)] bg-slate-50">
		<header class="border-b border-slate-200 bg-white">
			<div class="mx-auto w-full max-w-[1400px] px-3 py-5">
				<div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
					<div class="space-y-3">
						<h1 class="m-0 text-2xl tracking-tight text-slate-900 lg:text-4xl">
							<a class="text-slate-900 no-underline hover:text-slate-700" href="<?php echo esc_url( admin_url( 'admin.php?page=dpt-options' ) ); ?>">
								<?php esc_html_e( 'Display Post Types', 'display-post-types' ); ?>
							</a>
						</h1>
					</div>
					<nav aria-label="Display Post Types admin navigation">
						<ul class="m-0 flex flex-wrap justify-end gap-2 p-0">
							<?php
							foreach ( $this->modules as $key => $args ) {
								if ( 'options' === $key ) {
									continue;
								}
								$is_active = $key === $current_page;
								$classes   = 'inline-flex min-h-10 items-center rounded border px-4 text-sm font-medium no-underline transition';
								$classes  .= $is_active
									? ' border-slate-900 bg-slate-900 text-white hover:bg-slate-800 hover:text-white'
									: ' border-slate-300 bg-white text-slate-700 hover:border-slate-400 hover:text-slate-900';
								printf(
									'<li class="mb-0"><a href="%1$s" class="%2$s"><span>%3$s</span></a></li>',
									esc_url( admin_url( 'admin.php?page=dpt-' . $key ) ),
									esc_attr( $classes ),
									esc_html( $args['label'] )
								);
							}
							?>
						</ul>
					</nav>
				</div>
			</div>
		</header>

		<div class="mx-auto w-full max-w-[1400px] px-3 pt-5">
			<div class="flex items-center gap-2 border-b border-slate-200 pb-4 text-base">
				<a class="font-semibold text-blue-600 hover:text-blue-900 <?php echo 'home' !== $current_page ? '!underline' : ''; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=dpt-options' ) ); ?>"><?php esc_html_e( 'Home', 'display-post-types' ); ?></a>
				<?php if ( 'home' !== $current_page ) { ?>
					<span class="dashicons dashicons-arrow-right-alt2"></span>
					<span class="text-slate-500">
						<?php
						$crumb_label = 'shortcode' === $current_page ? esc_html__( 'Shortcode', 'display-post-types' ) : esc_html__( 'Support', 'display-post-types' );
						echo esc_html( $crumb_label );
						?>
					</span>
				<?php } ?>
			</div>
		</div>

		<div class="mx-auto flex w-full max-w-[1400px] flex-col gap-6 px-3 py-6 lg:flex-row lg:items-start lg:pb-10">
			<div id="dpt-options-content" class="dpt-options-content min-w-0 flex-1">
				<div class="dpt-options-content-wrapper">
					<div class="dpt-options-content-area rounded border border-slate-200 bg-white p-5 shadow-sm lg:p-8">
						<?php
						$located = Markup_Fn::locate_admin_template( $current_page );
						if ( $located ) {
							printf( '<div id="dpt-options-module-%s" class="dpt-module-content">', esc_attr( $current_page ) );
							include_once $located;
							echo '</div>';
						}
						?>
					</div>
					<div class="dpt-options-footer px-1 py-3 text-xs text-slate-500">
						<span><?php esc_html_e( 'Built by EasyPro Labs', 'display-post-types' ); ?> &copy; <?php echo esc_html( date_i18n( __( 'Y', 'display-post-types' ) ) ); ?></span>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="dpt-action-feedback" id="dpt-action-feedback">
		<span class="dashicons dashicons-update"></span>
		<span class="dashicons dashicons-no"></span>
		<span class="dashicons dashicons-yes"></span>
		<span class="dpt-feedback"></span>
		<span class="dpt-error-close"><span class="dashicons dashicons-no"></span></span>
	</div>
</div>
