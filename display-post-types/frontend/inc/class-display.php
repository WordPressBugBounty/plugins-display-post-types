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
	 * Constructor method.
	 *
	 * @since  1.0.0
	 */
	public function __construct() {}

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

		// If pagination is to be displayed.
		if ( self::is_style_support( $args['styles'], 'pagination' ) && isset( $args['show_pgnation'] ) && $args['show_pgnation'] ) {
			$inst_id    = $inst_class->count();
			$pagination = 'paged' . $inst_id;
			$paged      = isset( $_GET[ $pagination ] ) ? (int) $_GET[ $pagination ] : 1;
		}

		// Prepare the query.
		$query_args = array();
		if ( ! $args['post_type'] ) {
			return;
		} elseif ( 'page' === $args['post_type'] ) {
			$query_args = array(
				'post_type'           => 'page',
				'post__in'            => $args['pages'],
				'post_status'         => 'publish',
				'ignore_sticky_posts' => true,
				'posts_per_page'      => $args['number'],
			);
		} else {
			$query_args = array(
				'post_type'           => $args['post_type'],
				'post_status'         => 'publish',
				'ignore_sticky_posts' => true,
				'posts_per_page'      => $args['number'],
				'orderby'             => $args['orderby'],
				'order'               => $args['order'],
			);

			if ( $args['taxonomy'] && ! empty( $args['terms'] ) ) {
				$taxargs = array(
					'taxonomy' => $args['taxonomy'],
					'field'    => 'slug',
					'terms'    => $args['terms'],
				);
				// Add relationship if there are more than one terms selected.
				if ( $args['relation'] && 1 < count( $args['terms'] ) ) {
					$taxargs['operator'] = $args['relation'];
				}
				$query_args['tax_query'] = array( $taxargs );
			}

			if ( $args['offset'] ) {
				$query_args['offset'] = $args['offset'];
			}

			if ( $args['post_ids'] ) {
				$query_args['post__in'] = explode( ',', $args['post_ids'] );
			}
		}

		// If pagination is to be displayed.
		if ( self::is_style_support( $args['styles'], 'pagination' ) && isset( $args['show_pgnation'] ) && $args['show_pgnation'] ) {
			$query_args['paged'] = $paged;
		} else {
			$query_args['no_found_rows'] = true;
		}

		$current_id = get_the_ID();
		if ( $current_id && ! is_home() ) {
			$exclude                    = (array) $current_id;
			$query_args['post__not_in'] = $exclude;
		}

		$query_args = apply_filters( 'dpt_display_posts_args', $query_args, $args );

		$all_post_ids           = self::get_all_post_ids( $query_args );
		$query_args['post__in'] = $all_post_ids;
		unset( $query_args['tax_query'] );
		$post_query     = new \WP_Query( $query_args );
		if ( $post_query->have_posts() ) :
			$action_args = array(
				'args'  => $args,
				'query' => $post_query,
			);

			$all_taxonomies = get_object_taxonomies( $args['post_type'] );
			$taxonomies     = array();
			$post_ids       = array();
			?>

			<div class="display-post-types">
				
				<?php
				/**
				 * Fires before display posts wrapper.
				 *
				 * @since 1.0.0
				 *
				 * @param array $action_args Settings & args for the current widget instance..
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
						$post_ids[]  = get_the_ID();
						$attributes  = '';
						foreach ( $all_taxonomies as $tax ) {
							$terms = wp_get_object_terms( get_the_ID(), $tax );
							if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
								$post_tax = wp_list_pluck( $terms, 'term_id', 'name' );
								$taxonomies[ $tax ] = isset( $taxonomies[ $tax ] ) ? array_merge( $taxonomies[ $tax ], $post_tax ) : $post_tax;
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
			$current_page = $post_query->get('paged') ? $post_query->get('paged') : 1;
			$posts_per_page = $post_query->get('posts_per_page');
			$total = count( $all_post_ids );
			$offset = min( $total, $posts_per_page * ( $current_page - 1 ) );
			
			// Send query args and instance settings to the frontend as script data.
			$inst_class->add_script_data( $instance, array(
				'query_args' => $query_args,
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

		if ( isset( $query_args['post_ids'] ) && ! empty( $query_args['post_ids'] ) ) {
			return $query_args['post_ids'];
		}

		$query_args['posts_per_page'] = -1;
		$query_args['fields']         = 'ids';
		return get_posts( $query_args );
	}

	/**
	 * Returns the instance of this class.
	 *
	 * @since  1.0.0
	 *
	 * @return object Instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}
