<?php
/**
 * The validation functions for the DPT.
 *
 * @package Display_Post_Types
 * @since 1.0.0
 */

namespace Display_Post_Types\Helper;

/**
 * The front-end specific functionality of the plugin.
 *
 * @since 1.0.0
 */
class Validation {

	/**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 */
	public function __construct() {}

	/**
	 * Check if url is an internal link.
	 *
	 * @since 3.3.0
	 *
	 * @param string $link Link to be checked.
	 */
	public static function is_internal_link( $link ) {
		$host = wp_parse_url( $link, PHP_URL_HOST );

		// Check if relative link without a host.
		if ( empty( $host ) ) {
			return true;
		}

		// Check if host is same as home_url.
		if ( strtolower( $host ) === strtolower( wp_parse_url( home_url(), PHP_URL_HOST ) ) ) {
			return true;
		}

		return false;
	}
}
