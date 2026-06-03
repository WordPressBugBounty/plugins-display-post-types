<?php
/**
 * The front end specific functionality of the plugin.
 *
 * @package Display_Post_Types
 * @since 1.0.0
 */

namespace Display_Post_Types\Frontend\Inc;

use Display_Post_Types\Frontend\Inc\Instance_Counter;
use Display_Post_Types\Helper\Getters as Get_Fn;

/**
 * The front-end specific functionality of the plugin.
 *
 * @since 1.0.0
 */
class Display {

	/**
	 * Fecilitate display post types markup rendering.
	 *
	 * @since  1.0.0
	 *
	 * @param array $args Display post types markup args.
	 * @return void
	 */
	public static function init( $args ) {

		// Set widget instance settings default values.
		$defaults = Get_Fn::defaults();

		// Merge with defaults.
		$args = wp_parse_args( (array) $args, $defaults );

		$wrapper_class = apply_filters( 'dpt_wrapper_classes', array( $args['styles'], $args['classes'] ), $args );
		$wrapper_class = array_map( 'esc_attr', $wrapper_class );

		// Add attributes to the wrapper.
		$out  = '';
		$attr = apply_filters( 'dpt_html_attributes', array(), $args );
		if ( ! empty( $attr ) ) {
			foreach ( $attr as $name => $value ) {
				$out .= sprintf( ' %s="%s"', esc_html( $name ), esc_attr( $value ) );
			}
		}

		// Get current DPT instance.
		$inst_class = Instance_Counter::get_instance();
		$instance   = $inst_class->get();

		// Set if current instance support slider or Mason styles.
		if ( self::has_slider( $args ) ) {
			$inst_class->set_slider();
		} elseif ( self::has_mason( $args ) ) {
			$inst_class->set_mason();
		}

		$pagination_enabled = self::is_style_support( $args['styles'], 'pagination' ) && isset( $args['show_pgnation'] ) && $args['show_pgnation'];
		if ( $pagination_enabled ) {
			$inst_id    = $inst_class->count();
			$pagination = 'paged' . $inst_id;
			$paged      = isset( $_GET[ $pagination ] ) ? absint( wp_unslash( $_GET[ $pagination ] ) ) : 1;
		}

		// Prepare the query.
		$query_context = array(
			'no_found_rows' => ! $pagination_enabled,
			'current_id'    => get_the_ID(),
			'is_home'       => is_home(),
		);
		if ( $pagination_enabled ) {
			$query_context['paged'] = $paged;
		}

		$query_args = Query_Builder::build( $args, $query_context );
		if ( empty( $query_args ) ) {
			return;
		}

		$query_args = apply_filters( 'dpt_display_posts_args', $query_args, $args );

		$needs_full_index = self::needs_full_index( $args );
		$script_query_args = $query_args;
		$all_post_ids = array();
		if ( $needs_full_index ) {
			$all_post_ids = self::get_all_post_ids( $query_args );
			$script_query_args['post__in'] = ! empty( $all_post_ids ) ? $all_post_ids : array( 0 );
			unset( $script_query_args['tax_query'] );
		}

		$post_query = new \WP_Query( $query_args );
		if ( $post_query->have_posts() ) :
			$current_page   = $post_query->get( 'paged' ) ? absint( $post_query->get( 'paged' ) ) : 1;
			$posts_per_page = absint( $post_query->get( 'posts_per_page' ) );
			$total          = $needs_full_index ? count( $all_post_ids ) : (int) $post_query->post_count;
			$offset         = min( $total, $posts_per_page * ( $current_page - 1 ) );

			$action_args = array(
				'args'       => $args,
				'query'      => $post_query,
				'query_meta' => array(
					'current_page'   => $current_page,
					'posts_per_page' => $posts_per_page,
					'total'          => $total,
					'has_more_posts' => 0 < $posts_per_page && $total > $posts_per_page,
				),
			);

			$all_taxonomies = get_object_taxonomies( $args['post_type'] );
			$taxonomies     = $needs_full_index ? self::get_taxonomy_index( $all_post_ids, $all_taxonomies ) : array();
			?>

			<div class="display-post-types">

				<?php
				/**
				 * Fires before display posts wrapper.
				 *
				 * @since 1.0.0
				 *
				 * @param array $action_args Settings & args for the current widget instance.
				 * @param int   $instance    Current instance counter.
				 */
				do_action( 'dpt_before_wrapper', $action_args, $instance );
				?>
			
				<div id="dpt-wrapper-<?php echo absint( $instance ); ?>" class="dpt-wrapper <?php echo join( ' ', $wrapper_class ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" <?php echo $out; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>

					<?php
					/**
					 * Fires before custom loop starts.
					 *
					 * @since 1.0.0
					 *
					 * @param array $action_args Settings & args for the current widget instance..
					 */
					do_action( 'dpt_before_loop', $action_args );

					while ( $post_query->have_posts() ) :
						$post_query->the_post();
						$entry_class = apply_filters( 'dpt_entry_classes', array(), $action_args );
						$entry_class = array_map( 'esc_attr', $entry_class );
						$attributes  = '';
						foreach ( $all_taxonomies as $tax ) {
							$terms = wp_get_object_terms( get_the_ID(), $tax );
							if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
								$post_tax = wp_list_pluck( $terms, 'term_id', 'name' );
								$attributes .= ' data-' . $tax . '="' . strtolower( esc_attr( join( ' ', array_keys( $post_tax ) ) ) ) . '"';
							}
						}
						?>
						<div class="dpt-entry <?php echo join( ' ', $entry_class );?>" data-title="<?php echo esc_attr( strtolower( get_the_title() ) );?>" data-id="<?php echo absint( get_the_ID() );?>" <?php echo $attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
							<div class="dpt-entry-wrapper"><?php do_action( 'dpt_entry', $action_args ); ?></div>
						</div><!-- .dpt-entry -->
						<?php
					endwhile;

					/**
					 * Fires after custom loop starts.
					 *
					 * @since 1.0.0
					 *
					 * @param array $action_args Settings & args for the current widget instance..
					 */
					do_action( 'dpt_after_loop', $action_args );
					?>

				</div>
			<?php
			// If pagination is to be displayed.
			if ( self::is_style_support( $args['styles'], 'pagination' ) && isset( $args['show_pgnation'] ) && $args['show_pgnation'] ) {
				$pag_args1 = array(
					'format'  => '?paged' . $inst_id . '=%#%',
					'current' => $paged,
					'total'   => $post_query->max_num_pages,
				);
				printf( '<div class="dp-pagination">%s</div>', paginate_links( $pag_args1 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			/**
			 * Fires after display posts wrapper.
			 *
			 * @since 1.0.0
			 *
			 * @param array $action_args Settings & args for the current widget instance..
			 */
			do_action( 'dpt_after_wrapper', $action_args );
			?>
			</div>
			<?php

			// Send query args and instance settings to the frontend as script data.
			$inst_class->add_script_data( $instance, array(
				'query_args'  => $script_query_args,
				'query_token' => self::get_query_token( $script_query_args, $args ),
				'args'       => $args,
				'offset'     => $offset,
				'lot_size'   => $posts_per_page,
				'total'      => $total,
				'taxonomies' => $taxonomies,
			));

			// Reset the global $the_post as this query will have stomped on it.
			wp_reset_postdata();
		endif;
	}

	/**
	 * Check if current instance have slider style.
	 *
	 * @since  2.0.0
	 *
	 * @param array $args Settings for current DPT instance.
	 * @return bool
	 */
	public static function has_slider( $args ) {
		if ( self::is_style_support( $args['styles'], 'slider' ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Check if current instance have Masonry style.
	 *
	 * @since  2.0.0
	 *
	 * @param array $args Settings for current DPT instance.
	 * @return bool
	 */
	public static function has_mason( $args ) {
		// Return false if image is already cropped.
		if ( '' !== $args['img_aspect'] ) {
			return false;
		}

		// Return false if instance does not have multiple columns.
		if ( ! self::is_style_support( $args['styles'], 'multicol' ) || 1 === $args['col_narr'] ) {
			return false;
		}
		return true;
	}

	/**
	 * Check if item is supported by the style.
	 *
	 * @param string $style Current display style.
	 * @param string $item  item to be checked for support.
	 * @return bool
	 */
	public static function is_style_support( $style, $item ) {
		if ( ! $style ) {
			return false;
		}

		$all = Get_Fn::styles();
		if ( ! isset( $all[ $style ]['support'] ) || ! $all[ $style ]['support'] ) {
			return false;
		}
		$sup_arr = $all[ $style ]['support'];

		return in_array( $item, $sup_arr, true );
	}

	/**
	 * Get all post ids for the current query.
	 *
	 * @since  2.8.4
	 *
	 * @param array $query_args Query arguments.
	 */
	public static function get_all_post_ids( $query_args ) {
		if ( ! $query_args || ! is_array( $query_args ) ) {
			return array();
		}

		$query_args['posts_per_page'] = -1;
		$query_args['fields']         = 'ids';
		$query_args['no_found_rows']  = true;
		unset( $query_args['paged'] );

		$cache_key = self::get_cache_key( 'ids', $query_args );
		$cached    = wp_cache_get( $cache_key, 'display-post-types' );
		if ( false !== $cached ) {
			return $cached;
		}

		$post_ids = get_posts( $query_args );
		wp_cache_set( $cache_key, $post_ids, 'display-post-types' );
		return $post_ids;
	}

	/**
	 * Get a cached taxonomy map for the full result set.
	 *
	 * @since 3.3.0
	 *
	 * @param array $post_ids   Post IDs.
	 * @param array $taxonomies Taxonomy names.
	 * @return array
	 */
	public static function get_taxonomy_index( $post_ids, $taxonomies ) {
		if ( empty( $post_ids ) || empty( $taxonomies ) ) {
			return array();
		}

		$post_ids   = self::normalize_id_list( $post_ids );
		$taxonomies = array_map( 'sanitize_key', (array) $taxonomies );
		$cache_key  = self::get_cache_key(
			'taxonomies',
			array(
				'post_ids'   => $post_ids,
				'taxonomies' => $taxonomies,
			)
		);
		$cached     = wp_cache_get( $cache_key, 'display-post-types' );
		if ( false !== $cached ) {
			return $cached;
		}

		$tax_index = array();
		$terms     = wp_get_object_terms( $post_ids, $taxonomies );
		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$tax_index[ $term->taxonomy ][ $term->name ] = $term->term_id;
			}
		}

		wp_cache_set( $cache_key, $tax_index, 'display-post-types' );
		return $tax_index;
	}

	/**
	 * Build a cache key that follows WordPress post and term invalidation.
	 *
	 * @since 3.3.0
	 *
	 * @param string $prefix Cache key prefix.
	 * @param array  $data   Cache key data.
	 * @return string
	 */
	public static function get_cache_key( $prefix, $data ) {
		$last_changed = function_exists( 'wp_cache_get_last_changed' )
			? wp_cache_get_last_changed( 'posts' ) . ':' . wp_cache_get_last_changed( 'terms' )
			: '';

		return 'dpt_' . sanitize_key( $prefix ) . '_' . md5( wp_json_encode( $data ) . '|' . $last_changed );
	}

	/**
	 * Normalize an ID list received from block, widget or shortcode settings.
	 *
	 * @since 3.3.0
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
	 * Check whether the current instance needs a full result index.
	 *
	 * This is only needed for Pro header actions that search, filter or page
	 * through the whole matching result set via AJAX.
	 *
	 * @since 3.3.0
	 *
	 * @param array $args Settings for current DPT instance.
	 * @return bool
	 */
	public static function needs_full_index( $args ) {
		if ( empty( $args['styles'] ) || ! self::is_style_support( $args['styles'], 'hactions' ) ) {
			return false;
		}

		foreach ( array( 'hsearch', 'hfilter', 'hnext' ) as $setting ) {
			if ( ! empty( $args[ $setting ] ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Sign frontend query data so AJAX requests can verify the original query.
	 *
	 * @since 3.3.0
	 *
	 * @param array $query_args Query arguments.
	 * @param array $args       Display settings.
	 * @return string
	 */
	public static function get_query_token( $query_args, $args ) {
		return wp_hash( wp_json_encode( $query_args ) . '|' . wp_json_encode( $args ) );
	}
}
