<?php
/**
 * New-purchase promotion for administrators without Display Post Types Pro.
 *
 * @package Display_Post_Types
 */

namespace Display_Post_Types\Backend\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Pro_Offer {

	const CAMPAIGN_ID  = 'dpt20-new-purchases-v1';
	const COUPON_CODE  = 'dpt20';
	const PRODUCT_ID   = '17327';
	const DISMISS_KEY  = 'display_post_types_dismissed_pro_offer';
	const END_DATE     = '2026-10-31';

	/**
	 * Detect active Pro functionality in either edition.
	 *
	 * @return bool
	 */
	public static function is_pro_active() {
		return defined( 'DPT_PRO_VERSION' ) || function_exists( 'dp_fs' ) || apply_filters( 'display_post_types_is_premium', false );
	}

	/**
	 * Whether the current administrator should see this campaign.
	 *
	 * @return bool
	 */
	public static function should_show() {
		return current_user_can( 'manage_options' )
			// Include the entire last day, using the WordPress site's timezone.
			&& current_time( 'Y-m-d' ) <= self::END_DATE
			&& ! self::is_pro_active()
			&& ! self::is_closed_for_version();
	}

	/**
	 * Dismissals apply only to this campaign and this installed plugin version.
	 *
	 * @return bool
	 */
	private static function is_closed_for_version() {
		$closed = get_user_meta( get_current_user_id(), self::DISMISS_KEY, true );
		return is_array( $closed )
			&& isset( $closed['campaign'], $closed['version'] )
			&& self::CAMPAIGN_ID === $closed['campaign']
			&& DISPLAY_POST_TYPES_VERSION === $closed['version'];
	}

	/**
	 * Hosted checkout with the discount prefilled.
	 *
	 * @return string
	 */
	public static function checkout_url() {
		return add_query_arg( 'coupon', self::COUPON_CODE, 'https://checkout.freemius.com/product/' . self::PRODUCT_ID . '/' );
	}

	/**
	 * Remember this offer's dismissal until the installed plugin version changes.
	 */
	public static function dismiss() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to dismiss this offer.', 'display-post-types' ), '', array( 'response' => 403 ) );
		}

		check_admin_referer( 'dpt_dismiss_pro_offer' );
		update_user_meta(
			get_current_user_id(),
			self::DISMISS_KEY,
			array(
				'campaign' => self::CAMPAIGN_ID,
				'version'  => DISPLAY_POST_TYPES_VERSION,
			)
		);
		wp_safe_redirect( wp_get_referer() ?: admin_url( 'admin.php?page=dpt-options' ) );
		exit;
	}
}
