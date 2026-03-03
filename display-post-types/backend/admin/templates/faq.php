<?php
/**
 * Display Post Types options help and support page
 *
 * @package Display Post Types
 * @since 3.0.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<section class="space-y-6">
	<div class="space-y-2">
		<h2 class="m-0 text-2xl font-semibold tracking-tight text-slate-900 lg:text-3xl"><?php esc_html_e( 'Help, Docs, and Support', 'display-post-types' ); ?></h2>
		<p class="m-0 text-sm leading-6 text-slate-600 lg:text-base"><?php esc_html_e( 'Browse quick documentation links for setup, display options, and advanced Pro features.', 'display-post-types' ); ?></p>
	</div>

	<div class="grid gap-4 lg:grid-cols-3">
		<article class="rounded border border-slate-200 bg-white p-5">
			<h3 class="m-0 text-base font-semibold text-slate-900"><?php esc_html_e( 'Getting Started', 'display-post-types' ); ?></h3>
			<ul class="mt-4 space-y-3 text-sm">
				<li><a class="!underline" href="https://easyprolabs.com/display-post-types-docs/?easyDocId=178" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Display Post Types Overview', 'display-post-types' ); ?></a></li>
				<li><a class="!underline" href="https://easyprolabs.com/display-post-types-docs/?easyDocId=179" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Installation', 'display-post-types' ); ?></a></li>
			</ul>
		</article>

		<article class="rounded border border-slate-200 bg-white p-5">
			<h3 class="m-0 text-base font-semibold text-slate-900"><?php esc_html_e( 'Display Content', 'display-post-types' ); ?></h3>
			<ul class="mt-4 space-y-3 text-sm">
				<li><a class="!underline" href="https://easyprolabs.com/display-post-types-docs/?easyDocId=180" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Shortcode Generator', 'display-post-types' ); ?></a></li>
				<li><a class="!underline" href="https://easyprolabs.com/display-post-types-docs/?easyDocId=181" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Editor Block', 'display-post-types' ); ?></a></li>
				<li><a class="!underline" href="https://easyprolabs.com/display-post-types-docs/?easyDocId=182" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'WordPress Widget', 'display-post-types' ); ?></a></li>
				<li><a class="!underline" href="https://easyprolabs.com/display-post-types-docs/?easyDocId=196" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Post Meta Information', 'display-post-types' ); ?></a></li>
			</ul>
		</article>

		<article class="rounded border border-slate-200 bg-white p-5">
			<h3 class="m-0 text-base font-semibold text-slate-900"><?php esc_html_e( 'Pro Features', 'display-post-types' ); ?></h3>
			<ul class="mt-4 space-y-3 text-sm">
				<li><a class="!underline" href="https://easyprolabs.com/display-post-types-docs/?easyDocId=187" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Frontend Search & Filter', 'display-post-types' ); ?></a></li>
				<li><a class="!underline" href="https://easyprolabs.com/display-post-types-docs/?easyDocId=197" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Meta Info Pro Features', 'display-post-types' ); ?></a></li>
				<li><a class="!underline" href="https://easyprolabs.com/display-post-types-docs/?easyDocId=188" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Rearrange Post Elements', 'display-post-types' ); ?></a></li>
				<li><a class="!underline" href="https://easyprolabs.com/display-post-types-docs/?easyDocId=198" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Item Wrapper Design', 'display-post-types' ); ?></a></li>
				<li><a class="!underline" href="https://easyprolabs.com/display-post-types-docs/?easyDocId=199" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Additional Layout', 'display-post-types' ); ?></a></li>
			</ul>
		</article>
	</div>

	<div class="rounded border border-slate-200 bg-slate-50 p-5">
		<p class="m-0 text-sm text-slate-600"><?php esc_html_e( 'Still need help? Reach out and the team will assist you.', 'display-post-types' ); ?></p>
		<p class="mt-3 text-sm">
			<a class="text-blue-600" href="https://wordpress.org/support/plugin/display-post-types/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Open Support Ticket', 'display-post-types' ); ?></a>
			<span class="text-slate-400"> | </span>
			<a class="text-blue-600" href="https://easyprolabs.com/contact-us-2/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Contact Us', 'display-post-types' ); ?></a>
		</p>
	</div>
</section>
