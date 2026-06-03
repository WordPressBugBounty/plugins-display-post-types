<?php
/**
 * WP_Query argument builder.
 *
 * @package Display_Post_Types
 * @since 3.4.0
 */

namespace Display_Post_Types\Frontend\Inc;

/**
 * Builds WP_Query arguments from a normalized query schema.
 *
 * @since 3.4.0
 */
class Query_Builder {

	/**
	 * Build WP_Query arguments.
	 *
	 * @since 3.4.0
	 *
	 * @param array $settings Display settings.
	 * @param array $context  Runtime query context.
	 * @return array
	 */
	public static function build( $settings, $context = array() ) {
		$settings = is_array( $settings ) ? $settings : array();
		$context  = is_array( $context ) ? $context : array();
		$schema   = Query_Schema::normalize( $settings );

		if ( empty( $schema['post_type'] ) ) {
			return array();
		}

		if ( 'page' === $schema['post_type'] ) {
			$query_args = self::build_page_query( $schema );
		} else {
			$query_args = self::build_post_type_query( $schema );
		}

		$query_args = self::apply_runtime_context( $query_args, $context );

		/**
		 * Filters query arguments produced by the DPT query builder.
		 *
		 * This runs before the existing dpt_display_posts_args filter, so
		 * existing integrations keep their current extension point.
		 *
		 * @since 3.4.0
		 *
		 * @param array $query_args WP_Query arguments.
		 * @param array $schema     Normalized query schema.
		 * @param array $settings   Original display settings.
		 * @param array $context    Runtime query context.
		 */
		return apply_filters( 'dpt_query_builder_args', $query_args, $schema, $settings, $context );
	}

	/**
	 * Build a page query.
	 *
	 * @since 3.4.0
	 *
	 * @param array $schema Normalized query schema.
	 * @return array
	 */
	private static function build_page_query( $schema ) {
		return array(
			'post_type'           => 'page',
			'post__in'            => $schema['pages'],
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'posts_per_page'      => $schema['number'],
		);
	}

	/**
	 * Build a public post type query.
	 *
	 * @since 3.4.0
	 *
	 * @param array $schema Normalized query schema.
	 * @return array
	 */
	private static function build_post_type_query( $schema ) {
		$query_args = array(
			'post_type'           => $schema['post_type'],
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'posts_per_page'      => $schema['number'],
			'orderby'             => $schema['orderby'],
			'order'               => $schema['order'],
		);

		$query_args = self::apply_tax_query( $query_args, $schema );
		$query_args = self::apply_meta_query( $query_args, $schema );
		$query_args = self::apply_date_query( $query_args, $schema );

		if ( $schema['offset'] ) {
			$query_args['offset'] = $schema['offset'];
		}

		if ( ! empty( $schema['post_ids'] ) ) {
			$query_args['post__in'] = $schema['post_ids'];
		}

		return $query_args;
	}

	/**
	 * Apply taxonomy query settings.
	 *
	 * @since 3.4.0
	 *
	 * @param array $query_args WP_Query arguments.
	 * @param array $schema     Normalized query schema.
	 * @return array
	 */
	private static function apply_tax_query( $query_args, $schema ) {
		if ( ! empty( $schema['tax_clauses'] ) ) {
			$tax_query = array();
			if ( 1 < count( $schema['tax_clauses'] ) ) {
				$tax_query['relation'] = $schema['tax_relation'];
			}

			foreach ( $schema['tax_clauses'] as $clause ) {
				$tax_query[] = array_filter(
					$clause,
					function( $value ) {
						return array() !== $value && '' !== $value && null !== $value;
					}
				);
			}

			$query_args['tax_query'] = $tax_query;
			return $query_args;
		}

		if ( empty( $schema['taxonomy'] ) || empty( $schema['terms'] ) ) {
			return $query_args;
		}

		$tax_args = array(
			'taxonomy' => $schema['taxonomy'],
			'field'    => 'slug',
			'terms'    => $schema['terms'],
		);

		if ( $schema['relation'] && 1 < count( $schema['terms'] ) ) {
			$tax_args['operator'] = $schema['relation'];
		}

		$query_args['tax_query'] = array( $tax_args );
		return $query_args;
	}

	/**
	 * Apply optional meta query clauses.
	 *
	 * @since 3.4.0
	 *
	 * @param array $query_args WP_Query arguments.
	 * @param array $schema     Normalized query schema.
	 * @return array
	 */
	private static function apply_meta_query( $query_args, $schema ) {
		if ( empty( $schema['meta_clauses'] ) ) {
			return $query_args;
		}

		$meta_query = array();
		if ( 1 < count( $schema['meta_clauses'] ) ) {
			$meta_query['relation'] = $schema['meta_relation'];
		}

		foreach ( $schema['meta_clauses'] as $clause ) {
			$meta_query[] = $clause;
		}

		$query_args['meta_query'] = $meta_query;
		return $query_args;
	}

	/**
	 * Apply optional date range settings.
	 *
	 * @since 3.4.0
	 *
	 * @param array $query_args WP_Query arguments.
	 * @param array $schema     Normalized query schema.
	 * @return array
	 */
	private static function apply_date_query( $query_args, $schema ) {
		if ( empty( $schema['date_after'] ) && empty( $schema['date_before'] ) ) {
			return $query_args;
		}

		$date_query = array( 'inclusive' => true );
		if ( ! empty( $schema['date_after'] ) ) {
			$date_query['after'] = $schema['date_after'];
		}
		if ( ! empty( $schema['date_before'] ) ) {
			$date_query['before'] = $schema['date_before'];
		}

		$query_args['date_query'] = array( $date_query );
		return $query_args;
	}

	/**
	 * Apply runtime context such as pagination and current post exclusion.
	 *
	 * @since 3.4.0
	 *
	 * @param array $query_args WP_Query arguments.
	 * @param array $context    Runtime context.
	 * @return array
	 */
	private static function apply_runtime_context( $query_args, $context ) {
		if ( array_key_exists( 'paged', $context ) ) {
			$query_args['paged'] = absint( $context['paged'] );
		} elseif ( array_key_exists( 'no_found_rows', $context ) ) {
			$query_args['no_found_rows'] = (bool) $context['no_found_rows'];
		}

		$current_id = isset( $context['current_id'] ) ? absint( $context['current_id'] ) : 0;
		$is_home    = ! empty( $context['is_home'] );
		if ( $current_id && ! $is_home ) {
			$query_args['post__not_in'] = array( $current_id );
		}

		return $query_args;
	}
}
