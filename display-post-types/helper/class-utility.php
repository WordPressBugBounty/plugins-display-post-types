<?php
/**
 * DPT Utility Functions.
 *
 * @package Display_Post_Types
 * @since 2.7.0
 */

namespace Display_Post_Types\Helper;

/**
 * DPT Utility Functions.
 *
 * @since 2.7.0
 */
class Utility {

	/**
	 * Constructor method.
	 *
	 * @since  2.7.0
	 */
	public function __construct() {}

	/**
	 * Add an element to an array after certain associative key.
	 *
	 * @param array $array Main array.
	 * @param array $added Array items to be added.
	 * @param str   $key   Array key after which items to be added.
	 *
	 * @since  1.0.0
	 */
	public static function insert_array( $array, $added, $key = false ) {

		if ( is_int( $key ) ) {
			$pos = $key;
		} else {
			$pos = $key ? array_search( $key, array_keys( $array ), true ) : false;
			$pos = ( false !== $pos ) ? $pos + 1 : $pos;
		}

		if ( false !== $pos ) {
			return array_merge(
				array_slice( $array, 0, $pos ),
				$added,
				array_slice( $array, $pos )
			);
		}

		return array_merge( $array, $added );
	}
}
