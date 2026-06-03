<?php
/**
 * Query schema normalization.
 *
 * @package Display_Post_Types
 * @since 3.4.0
 */

namespace Display_Post_Types\Frontend\Inc;

/**
 * Normalizes display settings into a stable query schema.
 *
 * This class intentionally accepts the current legacy settings first. Optional
 * expanded clauses are normalized here too, but they are not exposed in the UI
 * until the controls are implemented separately.
 *
 * @since 3.4.0
 */
class Query_Schema {

	/**
	 * Normalize display settings.
	 *
	 * @since 3.4.0
	 *
	 * @param array $settings Display settings.
	 * @return array
	 */
	public static function normalize( $settings ) {
		$settings = is_array( $settings ) ? $settings : array();

		$schema = array(
			'post_type'     => self::normalize_slug( isset( $settings['post_type'] ) ? $settings['post_type'] : '' ),
			'pages'         => self::normalize_id_list( isset( $settings['pages'] ) ? $settings['pages'] : array() ),
			'number'        => self::normalize_int( isset( $settings['number'] ) ? $settings['number'] : 0 ),
			'orderby'       => self::normalize_orderby( isset( $settings['orderby'] ) ? $settings['orderby'] : 'date' ),
			'order'         => self::normalize_order( isset( $settings['order'] ) ? $settings['order'] : 'DESC' ),
			'taxonomy'      => self::normalize_slug( isset( $settings['taxonomy'] ) ? $settings['taxonomy'] : '' ),
			'terms'         => self::normalize_string_list( isset( $settings['terms'] ) ? $settings['terms'] : array() ),
			'relation'      => self::normalize_operator( isset( $settings['relation'] ) ? $settings['relation'] : 'IN', array( 'IN', 'AND' ), 'IN' ),
			'offset'        => self::normalize_int( isset( $settings['offset'] ) ? $settings['offset'] : 0 ),
			'post_ids'      => self::normalize_id_list( isset( $settings['post_ids'] ) ? $settings['post_ids'] : array() ),
			'tax_relation'  => self::normalize_relation( isset( $settings['query_tax_relation'] ) ? $settings['query_tax_relation'] : 'AND' ),
			'tax_clauses'   => self::normalize_tax_clauses( isset( $settings['query_tax_clauses'] ) ? $settings['query_tax_clauses'] : array() ),
			'meta_relation' => self::normalize_relation( isset( $settings['query_meta_relation'] ) ? $settings['query_meta_relation'] : 'AND' ),
			'meta_clauses'  => self::normalize_meta_clauses( isset( $settings['query_meta_clauses'] ) ? $settings['query_meta_clauses'] : array() ),
			'date_after'    => self::normalize_date( isset( $settings['query_date_after'] ) ? $settings['query_date_after'] : '' ),
			'date_before'   => self::normalize_date( isset( $settings['query_date_before'] ) ? $settings['query_date_before'] : '' ),
		);

		/**
		 * Filters the normalized DPT query schema.
		 *
		 * @since 3.4.0
		 *
		 * @param array $schema   Normalized query schema.
		 * @param array $settings Original display settings.
		 */
		return apply_filters( 'dpt_query_schema', $schema, $settings );
	}

	/**
	 * Normalize an ID list.
	 *
	 * @since 3.4.0
	 *
	 * @param string|array $ids Comma separated IDs or ID array.
	 * @return array
	 */
	public static function normalize_id_list( $ids ) {
		if ( empty( $ids ) ) {
			return array();
		}

		if ( ! is_array( $ids ) ) {
			$ids = explode( ',', $ids );
		}

		$ids = array_filter( array_map( 'absint', $ids ) );
		return array_values( array_unique( $ids ) );
	}

	/**
	 * Normalize taxonomy clauses.
	 *
	 * @since 3.4.0
	 *
	 * @param array|string $clauses Raw clauses.
	 * @return array
	 */
	public static function normalize_tax_clauses( $clauses ) {
		$clauses = self::decode_clauses( $clauses );
		if ( empty( $clauses ) ) {
			return array();
		}

		$normalized = array();
		foreach ( $clauses as $clause ) {
			if ( ! is_array( $clause ) ) {
				continue;
			}

			$taxonomy = self::normalize_slug( isset( $clause['taxonomy'] ) ? $clause['taxonomy'] : '' );
			if ( '' === $taxonomy ) {
				continue;
			}

			$operator = self::normalize_operator( isset( $clause['operator'] ) ? $clause['operator'] : 'IN', array( 'IN', 'NOT IN', 'AND', 'EXISTS', 'NOT EXISTS' ), 'IN' );
			$terms    = self::normalize_string_list( isset( $clause['terms'] ) ? $clause['terms'] : array() );
			if ( empty( $terms ) && ! in_array( $operator, array( 'EXISTS', 'NOT EXISTS' ), true ) ) {
				continue;
			}

			$field = isset( $clause['field'] ) ? sanitize_key( $clause['field'] ) : 'slug';
			$field = in_array( $field, array( 'term_id', 'name', 'slug', 'term_taxonomy_id' ), true ) ? $field : 'slug';

			$normalized[] = array(
				'taxonomy' => $taxonomy,
				'field'    => $field,
				'terms'    => $terms,
				'operator' => $operator,
			);
		}

		return $normalized;
	}

	/**
	 * Normalize meta clauses.
	 *
	 * @since 3.4.0
	 *
	 * @param array|string $clauses Raw clauses.
	 * @return array
	 */
	public static function normalize_meta_clauses( $clauses ) {
		$clauses = self::decode_clauses( $clauses );
		if ( empty( $clauses ) ) {
			return array();
		}

		$normalized = array();
		foreach ( $clauses as $clause ) {
			if ( ! is_array( $clause ) ) {
				continue;
			}

			$key = isset( $clause['key'] ) ? sanitize_text_field( $clause['key'] ) : '';
			if ( '' === $key ) {
				continue;
			}

			$type     = self::normalize_meta_type( isset( $clause['type'] ) ? $clause['type'] : '' );
			$compare  = self::normalize_meta_compare( isset( $clause['compare'] ) ? $clause['compare'] : '=' );
			$raw_value = isset( $clause['value'] ) ? $clause['value'] : '';
			$value    = is_array( $raw_value ) ? array_map( 'sanitize_text_field', $raw_value ) : sanitize_text_field( $raw_value );
			$value    = self::normalize_meta_value( $value, $type );

			if ( ( '' === $value || array() === $value ) && ! in_array( $compare, array( 'EXISTS', 'NOT EXISTS' ), true ) ) {
				continue;
			}

			$item = array(
				'key'     => $key,
				'compare' => $compare,
				'value'   => $value,
			);

			if ( '' !== $type ) {
				$item['type'] = $type;
			}

			$normalized[] = $item;
		}

		return $normalized;
	}

	/**
	 * Normalize a date value to YYYY-MM-DD.
	 *
	 * @since 3.4.0
	 *
	 * @param string $date Raw date.
	 * @return string
	 */
	public static function normalize_date( $date ) {
		$date = trim( sanitize_text_field( $date ) );
		if ( '' === $date ) {
			return '';
		}

		try {
			$dt = new \DateTime( $date );
			return $dt->format( 'Y-m-d' );
		} catch ( \Exception $exception ) {
			return '';
		}
	}

	/**
	 * Normalize an AND/OR relation.
	 *
	 * @since 3.4.0
	 *
	 * @param string $relation Raw relation.
	 * @return string
	 */
	public static function normalize_relation( $relation ) {
		return 'OR' === strtoupper( sanitize_text_field( $relation ) ) ? 'OR' : 'AND';
	}

	/**
	 * Decode clause JSON from classic form style payloads.
	 *
	 * @since 3.4.0
	 *
	 * @param array|string $clauses Raw clauses.
	 * @return array
	 */
	private static function decode_clauses( $clauses ) {
		if ( is_string( $clauses ) ) {
			$clauses = trim( wp_unslash( $clauses ) );
			if ( '' === $clauses ) {
				return array();
			}

			$decoded = json_decode( $clauses, true );
			return is_array( $decoded ) ? $decoded : array();
		}

		return is_array( $clauses ) ? $clauses : array();
	}

	/**
	 * Normalize a slug-like value.
	 *
	 * @since 3.4.0
	 *
	 * @param string $value Raw value.
	 * @return string
	 */
	private static function normalize_slug( $value ) {
		return sanitize_key( $value );
	}

	/**
	 * Normalize a text list.
	 *
	 * @since 3.4.0
	 *
	 * @param string|array $values Raw list.
	 * @return array
	 */
	private static function normalize_string_list( $values ) {
		if ( empty( $values ) ) {
			return array();
		}

		if ( ! is_array( $values ) ) {
			$values = explode( ',', $values );
		}

		$values = array_map( 'trim', $values );
		$values = array_filter( array_map( 'sanitize_text_field', $values ), 'strlen' );
		return array_values( array_unique( $values ) );
	}

	/**
	 * Normalize an integer.
	 *
	 * @since 3.4.0
	 *
	 * @param mixed $value Raw value.
	 * @return int
	 */
	private static function normalize_int( $value ) {
		return absint( $value );
	}

	/**
	 * Normalize orderby.
	 *
	 * @since 3.4.0
	 *
	 * @param string $orderby Raw orderby.
	 * @return string
	 */
	private static function normalize_orderby( $orderby ) {
		return sanitize_text_field( $orderby );
	}

	/**
	 * Normalize order direction.
	 *
	 * @since 3.4.0
	 *
	 * @param string $order Raw order.
	 * @return string
	 */
	private static function normalize_order( $order ) {
		return 'ASC' === strtoupper( sanitize_text_field( $order ) ) ? 'ASC' : 'DESC';
	}

	/**
	 * Normalize an operator from an allowed list.
	 *
	 * @since 3.4.0
	 *
	 * @param string $operator Raw operator.
	 * @param array  $allowed  Allowed operators.
	 * @param string $fallback Fallback operator.
	 * @return string
	 */
	private static function normalize_operator( $operator, $allowed, $fallback ) {
		$operator = strtoupper( sanitize_text_field( $operator ) );
		return in_array( $operator, $allowed, true ) ? $operator : $fallback;
	}

	/**
	 * Normalize meta type.
	 *
	 * @since 3.4.0
	 *
	 * @param string $type Raw type.
	 * @return string
	 */
	private static function normalize_meta_type( $type ) {
		$type = strtolower( sanitize_text_field( $type ) );
		$map  = array(
			'number'  => 'NUMERIC',
			'numeric' => 'NUMERIC',
			'date'    => 'DATE',
			'bool'    => 'BINARY',
			'boolean' => 'BINARY',
			'binary'  => 'BINARY',
		);

		return isset( $map[ $type ] ) ? $map[ $type ] : '';
	}

	/**
	 * Normalize meta compare operator.
	 *
	 * @since 3.4.0
	 *
	 * @param string $compare Raw compare.
	 * @return string
	 */
	private static function normalize_meta_compare( $compare ) {
		$operators = array(
			''           => '=',
			'='          => '=',
			'ne'         => '!=',
			'!='         => '!=',
			'gt'         => '>',
			'>'          => '>',
			'gte'        => '>=',
			'>='         => '>=',
			'lt'         => '<',
			'<'          => '<',
			'lte'        => '<=',
			'<='         => '<=',
			'like'       => 'LIKE',
			'LIKE'       => 'LIKE',
			'nlike'      => 'NOT LIKE',
			'not like'   => 'NOT LIKE',
			'NOT LIKE'   => 'NOT LIKE',
			'exists'     => 'EXISTS',
			'EXISTS'     => 'EXISTS',
			'nexists'    => 'NOT EXISTS',
			'not exists' => 'NOT EXISTS',
			'NOT EXISTS' => 'NOT EXISTS',
		);

		$compare = sanitize_text_field( $compare );
		return isset( $operators[ $compare ] ) ? $operators[ $compare ] : '=';
	}

	/**
	 * Normalize meta query value for known types.
	 *
	 * @since 3.4.0
	 *
	 * @param string|array $value Raw value.
	 * @param string       $type  Normalized meta type.
	 * @return string|array
	 */
	private static function normalize_meta_value( $value, $type ) {
		if ( 'NUMERIC' === $type ) {
			return is_array( $value ) ? array_values( array_filter( $value, 'is_numeric' ) ) : ( is_numeric( $value ) ? $value : '' );
		}

		if ( 'DATE' === $type && ! is_array( $value ) ) {
			$date = self::normalize_date( $value );
			return '' !== $date ? $date : $value;
		}

		return $value;
	}
}
