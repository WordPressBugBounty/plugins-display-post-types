<?php
/**
 * Display Post Types Admin Notifications
 *
 * @package Display post Types
 * @since 1.0.0
 */

?>

<div class="updated notice is-dismissible dpt-welcome-notice">
	<p class="intro-msg">
		<?php esc_html_e( 'Thanks for trying/updating Display Post Types.', 'display-post-types' ); ?>
	</p>
	<p>
		<?php esc_html_e( 'New: Various options added to customize typography of the content. In "Manage Item Components" section of the DPT block, you can customize typography.', 'display-post-types' ); ?>
    </p>
	<div class="common-links">
		<p class="dpt-link">
			<a href="https://wordpress.org/support/plugin/display-post-types/" target="_blank">
				<?php esc_html_e( 'Raise a support request', 'display-post-types' ); ?>
			</a>
		</p>
		<p class="dpt-link">
			<a href="https://wordpress.org/support/plugin/display-post-types/reviews/" target="_blank">
				<?php esc_html_e( 'Give us 5 stars rating', 'display-post-types' ); ?>
			</a>
		</p>
		<p class="dpt-link">
			<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'dpt-dismiss', 'dismiss_admin_notices' ), 'dpt-dismiss-' . get_current_user_id() ) ); ?>" target="_parent" style="color: red;">
				<?php esc_html_e( 'Dismiss this notice', 'display-post-types' ); ?>
			</a>
		</p>
	</div>
</div>
