<?php
/**
 * Display Post Types options home page
 *
 * @package Display Post Types
 * @since 2.5.0
 */

?>

<section class="space-y-6">
	<div class="rounded border border-slate-200 bg-slate-50 p-5 lg:p-7">
		<div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
			<div class="max-w-3xl space-y-3">
				<h2 class="m-0 text-2xl font-semibold tracking-tight text-slate-900"><?php esc_html_e( 'Build Better Content Layouts Faster', 'display-post-types' ); ?></h2>
				<p class="m-0 text-sm leading-6 text-slate-600 lg:text-base"><?php esc_html_e( 'Generate flexible list, grid, and slider layouts for posts, pages, and custom post types from one place.', 'display-post-types' ); ?></p>
			</div>
			<div class="flex w-full flex-col gap-2 lg:w-auto">
				<?php $this->mlink( 'https://www.youtube.com/watch?v=tTVGMylfBhU', 'Watch Getting Started', 'text-white bg-[#3b5998] hover:bg-[#3b5998]/90 hover:text-white box-border border border-transparent font-medium leading-5 rounded text-sm px-4 py-2.5 text-center inline-flex items-center' ); ?>
				<a class="text-body bg-white border border-gray-400 hover:bg-neutral-secondary-medium hover:text-heading font-medium leading-5 rounded text-sm px-4 py-2.5" href="<?php echo esc_url( admin_url( 'admin.php?page=dpt-shortcode' ) ); ?>"><?php esc_html_e( 'Open Shortcode Generator', 'display-post-types' ); ?></a>
			</div>
		</div>
	</div>

	<article class="rounded border border-slate-200 bg-white p-5">
		<h3 class="m-0 text-base font-semibold text-slate-900"><?php esc_html_e( 'Watch Tutorials', 'display-post-types' ); ?></h3>
		<div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
			<?php
			$videos = array(
				array(
					'id'    => 'tTVGMylfBhU',
					'title' => esc_html__( 'Getting Started Walkthrough', 'display-post-types' ),
					'tone'  => 'bg-pink-800',
				),
				array(
					'id'    => 'v6dD4Eurs_Q',
					'title' => esc_html__( 'DPT Pro Grid Layout Examples', 'display-post-types' ),
					'tone'  => 'bg-indigo-900',
				),
				array(
					'id'    => 'YDeygne5Kn4',
					'title' => esc_html__( 'Using ACF with DPT Pro', 'display-post-types' ),
					'tone'  => 'bg-teal-900',
				),
			);

			foreach ( $videos as $video ) {
				$youtube_link = 'https://www.youtube.com/watch?v=' . $video['id'];
				?>
			<a class="group overflow-hidden rounded-lg border border-slate-200 bg-white no-underline transition hover:border-slate-300 hover:shadow-sm" href="<?php echo esc_url( $youtube_link ); ?>" target="_blank" rel="noopener noreferrer">
				<div class="relative aspect-video overflow-hidden <?php echo esc_attr( $video['tone'] ); ?>">
					<span class="dpt-video-clip dpt-video-clip-one" aria-hidden="true"></span>
					<span class="dpt-video-clip dpt-video-clip-two" aria-hidden="true"></span>
					<div class="relative z-10 flex h-full justify-between p-4">
						<div class="flex items-end">
							<span class="text-xl text-white"><?php echo esc_html( $video['title'] ); ?><span class="dashicons dashicons-external mt-0 text-lg text-white/85"></span></span>
						</div>
					</div>
				</div>
			</a>
			<?php } ?>
		</div>
	</article>

	<?php $is_dpt_pro_active = defined( 'DPT_PRO_VERSION' ); ?>
	<div class="grid gap-4<?php echo $is_dpt_pro_active ? '' : ' xl:grid-cols-2'; ?>">
		<article class="rounded border border-slate-200 bg-white p-5">
			<h3 class="m-0 text-base font-semibold text-slate-900"><?php esc_html_e( 'Need Help?', 'display-post-types' ); ?></h3>
			<p class="mt-4 text-sm leading-6 text-slate-600"><?php esc_html_e( 'Open a support topic or contact the team directly for setup and troubleshooting help.', 'display-post-types' ); ?></p>
			<p class="mt-3 text-sm">
				<a class="text-blue-600" href="https://wordpress.org/support/plugin/display-post-types/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Open Support Ticket', 'display-post-types' ); ?></a>
				<span class="text-slate-400"> | </span>
				<a class="text-blue-600" href="https://easyprolabs.com/contact-us-2/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Contact Us', 'display-post-types' ); ?></a>
			</p>
		</article>

		<?php if ( ! $is_dpt_pro_active ) { ?>
		<article class="rounded border border-emerald-200 bg-emerald-50 p-5">
			<p class="m-0 text-xs font-semibold uppercase tracking-wide text-emerald-700"><?php esc_html_e( 'Upgrade Available', 'display-post-types' ); ?></p>
			<h3 class="mt-2 text-base font-semibold text-slate-900"><?php esc_html_e( 'Unlock Display Post Types Pro', 'display-post-types' ); ?></h3>
			<p class="mt-3 text-sm leading-6 text-slate-700"><?php esc_html_e( 'Get advanced controls to build richer and more dynamic post displays with less effort.', 'display-post-types' ); ?></p>
			<ul class="mt-4 list-disc space-y-2 pl-5 text-sm text-slate-700">
				<li><?php esc_html_e( 'Professional Layouts with powerful customizations', 'display-post-types' ); ?></li>
				<li><?php esc_html_e( 'Sort and Filter by custom fields', 'display-post-types' ); ?></li>
				<li><?php esc_html_e( 'Dynamic Frontend Filtering by Taxonomy', 'display-post-types' ); ?></li>
				<li><?php esc_html_e( 'Live Search for Instant Results', 'display-post-types' ); ?></li>
				<li><?php esc_html_e( 'Rearrange Post Content for a Truly Custom Layout', 'display-post-types' ); ?></li>
				<li><?php esc_html_e( 'Easily Display Custom Field Content', 'display-post-types' ); ?></li>
				<li><?php esc_html_e( 'Typography & Margin Control', 'display-post-types' ); ?></li>
			</ul>
			<div class="mt-5">
				<a class="inline-flex items-center rounded border border-transparent bg-emerald-600 px-4 py-2.5 text-sm font-medium text-white no-underline hover:bg-emerald-700 hover:text-white" href="https://easyprolabs.com/display-post-types/" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( 'Get Display Post Types Pro', 'display-post-types' ); ?>
					<span class="dashicons dashicons-external ml-2 text-base" aria-hidden="true"></span>
				</a>
			</div>
		</article>
		<?php } ?>
	</div>
</section>
