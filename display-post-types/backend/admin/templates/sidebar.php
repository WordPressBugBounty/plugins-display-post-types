<?php
/**
 * Display Post Types sidebar
 *
 * @package Display Post Types
 * @since 2.5.0
 */

?>

<div class="rounded border border-slate-200 bg-white p-5 shadow-sm lg:p-6">
	<p class="m-0 text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-500"><?php esc_html_e( 'Upgrade Available', 'display-post-types' ); ?></p>
	<h3 class="mt-2 text-xl font-semibold text-slate-900"><?php esc_html_e( 'Display Post Types Pro', 'display-post-types' ); ?></h3>
	<p class="mt-3 text-sm leading-6 text-slate-600"><?php esc_html_e( 'Get advanced layouts, deeper visual controls, smart filtering, and priority support.', 'display-post-types' ); ?></p>
	<ul class="mt-4 space-y-2 text-sm text-slate-600">
		<li><?php esc_html_e( 'Professional layout presets', 'display-post-types' ); ?></li>
		<li><?php esc_html_e( 'Advanced spacing and typography options', 'display-post-types' ); ?></li>
		<li><?php esc_html_e( 'Smart search, filter, and sort tools', 'display-post-types' ); ?></li>
		<li><?php esc_html_e( 'Template support for meta fields', 'display-post-types' ); ?></li>
		<li><?php esc_html_e( 'Priority email support', 'display-post-types' ); ?></li>
	</ul>
	<div class="mt-5 flex flex-col gap-2">
		<?php $this->mlink( 'https://easyprolabs.com/dpt-demo-page/', 'Explore Live Demos', 'button' ); ?>
		<?php $this->mlink( 'https://easyprolabs.com/display-post-types', 'Upgrade to Pro', 'button button-primary' ); ?>
	</div>
</div>
