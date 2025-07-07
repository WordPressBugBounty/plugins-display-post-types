<?php
/**
 * The data sanitization, validation and escaping functions.
 *
 * @package Display_Post_Types
 * @since 1.0.0
 */

namespace Display_Post_Types\Helper;

/**
 * The data sanitization, validation and escaping functions.
 *
 * @since 1.0.0
 */
class Security {

	/**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 */
	public function __construct() {}

    /**
	 * Get setting variable type.
	 *
	 * @since 2.6.0
	 */
	public static function setting_type() {
		return apply_filters(
			'dpt_setting_type',
			array(
				'title'         => 'string',
				'post_type'     => 'string',
				'taxonomy'      => 'string',
				'terms'         => 'arrstring',
				'relation'      => 'string',
				'post_ids'      => 'string',
				'pages'         => 'string',
				'number'        => 'int',
				'orderby'       => 'string',
				'order'         => 'string',
				'styles'        => 'string',
				'style_sup'     => 'arrstring',
				'image_crop'    => 'string',
				'img_aspect'    => 'string',
				'text_pos_hor'  => 'string',
				'text_pos_ver'  => 'string',
				'custom_aspect' => 'int',
				'img_align'     => 'string',
				'br_radius'     => 'int',
				'col_narr'      => 'int',
				'pl_holder'     => 'check',
				'show_pgnation' => 'check',
				'thumb_fetch'   => 'check',
				'title_shadow'  => 'check',
				'text_align'    => 'string',
				'v_gutter'      => 'int',
				'h_gutter'      => 'int',
				'e_length'      => 'int',
				'e_teaser'      => 'string',
				'classes'       => 'string',
				'offset'        => 'int',
				'autotime'      => 'int',
				'meta1'         => 'string',
				'meta2'         => 'string',
			)
		);
	}

    /**
	 * Sanitize all data before saving.
	 *
	 * @since 2.6.0
	 *
	 * @param array $data data to be sanitized.
	 */
	public static function sanitize_all( $data ) {
		if ( ! $data || ! is_array( $data ) ) {
			return false;
		}
        $setting_type = self::setting_type();
        foreach ( $data as $key => $value ) {
            $type = isset( $setting_type[ $key ] ) ? $setting_type[ $key ] : ( is_array( $value ) ? 'arrstring' : 'string' );
            $data[ $key ] = self::sanitize( $value, $type );
        }
        return $data;
    }

    /**
	 * Escape all data before saving.
	 *
	 * @since 2.6.0
	 *
	 * @param array $data data to be escaped.
	 */
	public static function escape_all( $data ) {
		if ( ! $data || ! is_array( $data ) ) {
			return false;
		}
        $setting_type = self::setting_type();
        foreach ( $data as $key => $value ) {
            $type = isset( $setting_type[ $key ] ) ? $setting_type[ $key ] : ( is_array( $value ) ? 'arrstring' : 'string' );
            $data[ $key ] = self::escape( $value, $type );
        }
        return $data;
    }

	/**
	 * Sanitize data before saving.
	 *
	 * @since 2.6.0
	 *
	 * @param mixed  $data      data to be sanitized.
     * @param string $data_type data type to apply correct sanitization.
	 */
	public static function sanitize( $data, $data_type ) {
		switch ( $data_type ) {
            case 'string':
                $data = sanitize_text_field( $data );
                break;
            case 'int':
                $data = intval( $data );
                break;
            case 'float':
                $data = floatval( $data );
                break;
            case 'color':
				$data = sanitize_text_field( $data );
                break;
            case 'arrint':
				if ( ! $data ) {
					$data = '';
				} else {
					$data = ! is_array( $data ) ? explode( ',', $data ) : $data;
	                $data = array_map( 'intval', $data );
				}
                break;
            case 'arrstring':
                $data = ! is_array( $data ) ? explode( ',', $data ) : $data;
                $data = array_map( 'sanitize_text_field', $data );
                break;
            case 'check':
                $data = 'yes' === $data ? 'yes' : '';
            default:
                $data = sanitize_text_field( $data );
                break;
        }

		return $data;
	}

    /**
	 * Sanitize data before saving.
	 *
	 * @since 2.6.0
	 *
	 * @param mixed  $data      data to be sanitized.
     * @param string $data_type data type to apply correct sanitization.
	 */
	public static function escape( $data, $data_type ) {
		switch ( $data_type ) {
            case 'string':
                $data = esc_html( $data );
                break;
            case 'int':
                $data = intval( $data );
                break;
            case 'float':
                $data = floatval( $data );
                break;
            case 'color':
				$data = sanitize_text_field( $data );
                break;
            case 'arrint':
				if ( ! $data ) {
					$data = '';
				} else {
					$data = ! is_array( $data ) ? explode( ',', $data ) : $data;
					$data = array_map( 'intval', $data );
				}
                break;
            case 'arrstring':
                $data = ! is_array( $data ) ? explode( ',', $data ) : $data;
                $data = array_map( 'esc_html', $data );
                break;
            case 'check':
                $data = 'yes' === $data ? 'yes' : '';
            default:
                $data = esc_html( $data );
                break;
        }

		return $data;
	}
}
