<?php
/**
 * The front end specific functionality of the plugin.
 *
 * @package Display_Post_Types
 * @since 1.0.0
 */

namespace Display_Post_Types\Frontend;

use Display_Post_Types\Helper\Getters as Get_Fn;
use Display_Post_Types\Helper\Validation as Validation_Fn;
use Display_Post_Types\Frontend\Inc\Loader;
use Display_Post_Types\Helper\Markup;
use Display_Post_Types\Frontend\Inc\Misc as Misc_Fn;
use Display_Post_Types\Helper\Icon_Loader as Icons;

/**
 * The front-end specific functionality of the plugin.
 *
 * @since 1.0.0
 */
class Register {

	/**
	 * Holds the instance of this class.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    object
	 */
	protected static $instance = null;

	/**
	 * Holds Pro Status.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var    bool
	 */
	protected $is_pro = false;

	/**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 */
	public function __construct() {}

	/**
	 * Register hooked functions.
	 *
	 * @since 1.0.0
	 */
	public static function init() {
		$inst = self::get_instance();
		$inst->is_pro = Validation_Fn::is_pro();

		add_filter( 'dpt_wrapper_classes', array( $inst, 'wrapper_classes' ), 10, 2 );
		add_filter( 'dpt_html_attributes', array( $inst, 'html_attr' ), 10, 2 );
		add_filter( 'dpt_entry_classes', array( $inst, 'entry_classes' ), 10, 2 );
		add_action( 'dpt_entry', array( $inst, 'entry' ) );
		add_action( 'dpt_before_wrapper', array( $inst, 'inline_css' ), 0, 2 );
		add_action( 'dpt_before_wrapper', array( $inst, 'dpt_header' ), 10, 2 );
		add_filter( 'body_class', array( $inst, 'add_body_classes' ) );
		add_filter( 'wp_get_attachment_image_attributes', array( $inst, 'img_attrs' ), 9999, 3 );

		$misc = Misc_Fn::get_instance();
		add_action( 'dpt_before_loop', array( $misc, 'loop_data' ), 0 );
		add_filter( 'dpt_get_attr_dpt_title', array( $misc, 'extend_entry_title' ) );
		add_filter( 'dpt_get_attr_dpt_featured_content', array( $misc, 'extend_entry_thumbnail' ) );
		add_filter( 'dpt_get_attr_dpt_excerpt', array( $misc, 'extend_entry_excerpt' ) );
		add_filter( 'dpt_get_attr_dpt_meta', array( $misc, 'extend_entry_meta' ) );
		add_filter( 'dpt_get_attr_dpt_meta2', array( $misc, 'extend_entry_meta2' ) );

		// Loader Instance.
		$loader = Loader::get_instance();

		// Load front end scripts.
		self::load_scripts( $loader );

		// Elementor Support.
		self::elementor_support( $loader );

		// Load icon definitions.
		$icons = Icons::get_instance();
		self::add_icons_definitions( $icons );
	}

	/**
	 * Load front-end specific scripts for DPT.
	 *
	 * @since 2.0.0
	 *
	 * @param object $instance DPT script loader instance.
	 */
	public static function load_scripts( $instance ) {
		add_action( 'wp_footer', array( $instance, 'enqueue_scripts' ) );
	}

	/**
	 * Scripts to support elementor functionality in preview screen.
	 *
	 * @since 2.0.0
	 *
	 * @param object $instance DPT script loader instance.
	 */
	public static function elementor_support( $instance ) {
		if (
			in_array(
				'elementor/elementor.php',
				apply_filters( 'active_plugins', get_option( 'active_plugins' ) ),
				true
			)
		) {
			add_action(
				'elementor/preview/enqueue_scripts',
				array( $instance, 'enqueue_scripts' )
			);
		}
	}

	/**
	 * Display DPT header.
	 *
	 * @param array $instance display args.
	 * @param int   $id Instance ID.
	 */
	public function dpt_header( $instance, $id ) {
		$args  = $instance['args'];
		$query = $instance['query'];
		$title = isset( $args[ 'title' ] ) ? esc_html( $args[ 'title' ] ) : false;

		if ( ! $title ) {
			return;
		}

		$is_slider    = $this->is_style_support( $args['styles'], 'slider' );
		$header_style = $this->is_pro && isset( $args[ 'hstyle' ] ) ? $args[ 'hstyle' ] : '';
		$current_page = $this->is_pro && $query->get('paged') ? $query->get('paged') : 1;
		$is_search    = $this->is_pro && ! $is_slider && isset( $args[ 'hsearch' ] ) && ! empty( $args[ 'hsearch' ] ) ? $args[ 'hsearch' ] : false;
		$is_filter    = $this->is_pro && ! $is_slider && isset( $args[ 'hfilter' ] ) && ! empty( $args[ 'hfilter' ] ) ? $args[ 'hfilter' ] : false;
		$is_hnext     = $this->is_pro && ! $is_slider && isset( $args[ 'hnext' ] ) && ! empty( $args[ 'hnext' ] ) ? $args[ 'hnext' ] : false;

		Markup::markup(
			array( 'dpt-main-header', $header_style ),
			array(
				array(
					function( $title, $header_style, $is_search, $is_filter, $is_hnext, $current_page ) {
						$search_markup = '';
						$filter_markup = '';
						$hnext_markup  = '';
						if ( $is_search ) {
							$search_markup = '
							<div class="dpt-header-search-btn">
								<button type="submit" class="dpt-hsearch-btn dpt-header-btn">' . Markup::get_icon( array( 'icon' => 'dpt-search' ) ) . '</button>
							</div>
							';
						}
						if ( $is_hnext ) {
							$disabled = 1 === $current_page ? 'disabled' : '';
							$class    = 1 === $current_page ? 'is-disabled' : '';
							$hnext_markup = '
							<div class="dpt-header-posts-btn">
								<button type="submit"'  . $disabled . ' class="dpt-hprev-btn dpt-header-btn ' . $class . '">' . Markup::get_icon( array( 'icon' => 'dpt-previous' ) ) . '</button><button type="submit" class="dpt-hnext-btn dpt-header-btn">' . Markup::get_icon( array( 'icon' => 'dpt-next' ) ) . '</button>
							</div>
							';
						}
						if ( $is_filter ) {
							$filter_markup = '
							<div class="dpt-header-filter-btn">
								<button type="submit" class="dpt-hfilter-btn dpt-header-btn">' . Markup::get_icon( array( 'icon' => 'dpt-filter' ) ) . '</button>
							</div>
							';
						}
						$markup_string = '
						<div class="dpt-main-title">
							<span class="dpt-main-title-text">%1$s</span>
						</div>
						%2$s
						%3$s
						%4$s
						';
						$markup = sprintf( $markup_string, $title, $search_markup, $filter_markup, $hnext_markup );
						echo $markup;
					},
					$title, $header_style, $is_search, $is_filter, $is_hnext, $current_page
				),
			)
		);

		if ( $is_search ) {
			Markup::markup(
				array( 'dpt-header-search' ),
				array(
					function() {
						$markup_string = '%1$s<input type="text" class="dpt-hsearch-input" placeholder="%2$s"><button class="dpt-hsearch-close dpt-header-btn">%3$s</button>';
						$markup = sprintf(
							$markup_string,
							Markup::get_icon( array( 'icon' => 'dpt-search' ) ),
							esc_html__( 'Search', 'display-post-types' ),
							Markup::get_icon( array( 'icon' => 'dpt-close' ) )
						);
						echo $markup;
					}
				)
			);
		}

		if ( $is_filter ) {
			Markup::markup(
				array( 'dpt-header-filter' ),
				array(
					function() {
						$markup_string = '<div class="dpt-filter-title">%1$s</div><div class="dpt-filter-menu"></div><button class="dpt-hfilter-close dpt-header-btn">%2$s</button>';
						$markup = sprintf(
							$markup_string,
							esc_html__( 'Filter By', 'display-post-types' ),
							Markup::get_icon( array( 'icon' => 'dpt-close' ) )
						);
						echo $markup;
					}
				)
			);
		}
	}

	/**
	 * Display widget content to front-end.
	 *
	 * @param array $action_args Widget display arguments.
	 */
	public function entry( $action_args ) {
		$args = $action_args['args'];
		$display = $this->get_display_map( $args );
		$this->render_entry( $display, $args );
	}

	/**
	 * Display widget content to front-end.
	 *
	 * @param array $items content to be displayed.
	 * @param array $args Widget display arguments.
	 */
	public function get_display_map( $args ) {
		$style = isset( $args['styles'] ) ? $args['styles'] : false;
		$map = isset( $args['style_sup'] ) ? $args['style_sup'] : false;
		if ( ! $map || ! $style ) {
			return array();
		}
		$map = ! is_array( $map ) ? explode(',', $map) : $map;

		$style_supported = Get_Fn::styles();
		if ( ! isset( $style_supported[ $style ]['support'] ) || ! $style_supported[ $style ]['support'] ) {
			return array();
		}

		$supported_elems = $style_supported[ $style ]['support'];
		$final_map = array_values( array_intersect( $map, $supported_elems ) );

		$key = array_search( 'thumbnail', $final_map );
		if ( false !== $key ) {
			$before_thumb = array_slice( $final_map, 0, $key );
			$after_thumb  = array_slice( $final_map, $key + 1 );
			$result = array();
			if ( ! empty( $before_thumb ) ) {
				$result[] = $before_thumb;
			}
			$result[] = 'thumbnail';
			if ( ! empty( $after_thumb ) ) {
				$result[] = $after_thumb;
			}
			return $result;
		} else {
			return $final_map;
		}
	}

	/**
	 * Display widget content to front-end.
	 *
	 * @param array $instance display args.
	 * @param int   $id Instance ID.
	 */
	public function inline_css( $instance, $id ) {
		$style  = '';
		$vars   = '';
		$args   = $instance['args'];

		$crop_position = array(
			'topleftcrop'     => 'top left',
			'toprightcrop'    => 'top right',
			'bottomleftcrop'  => 'bottom left',
			'bottomrightcrop' => 'bottom right',
			'centercrop'      => 'center',
		);
		
		// Inline style for the text alignment.
		$text_align = 'r-text' === $args['text_align'] ? 'right' : ( 'c-text' === $args['text_align'] ? 'center' : 'left' );
		$vars .= '--dpt-text-align: ' . $text_align . ';';
		if ('center' === $text_align) {
			$vars .= '--dpt-excerpt-align-margin-right: auto; --dpt-excerpt-align-margin-left: auto;';
		}

		// Inline style for the image crop position.
		$croppos = isset( $crop_position[ $args['image_crop'] ] ) ? $crop_position[ $args['image_crop'] ] : 'center';
		$vars .= '--dpt-image-crop: ' . $croppos . ';';

		// Inline style for the border radius.
		$br_radius = isset( $args['br_radius'] ) ? $args['br_radius'] : 0;
		$vars .= '--dpt-border-radius: ' . absint( $br_radius ) . 'px;';

		// Inline style for the grid columns.
		if ( isset( $args['styles'] ) && $this->is_style_support( $args['styles'], 'multicol' ) ) {
			$columns    = isset( $args['col_narr'] ) ? absint( $args['col_narr'] ) : 1;
			$sml_grid   = 2 < $columns ? '33.33%' : '50%';
			$vars .= '--dpt-small-grid-column: ' . $sml_grid . ';';
			$large_grid = (100 / $columns) . '%';
			$vars .= '--dpt-large-grid-column: ' . $large_grid . ';';
		}

		if ( isset( $args['h_gutter'] ) ) {
			$vars .= '--dpt-h-gutter: ' . ( absint( $args['h_gutter'] ) / 2 ) . 'px;';
		}

		if ( isset( $args['v_gutter'] ) ) {
			$vars .= '--dpt-v-gutter: ' . ( absint( $args['v_gutter'] ) / 2 ) . 'px;';
		}

		if ( isset( $args['title_shadow'] ) && 'yes' !== $args['title_shadow'] ) {
			$vars .= '--dpt-title-text-shadow: none;';
		}

		if ( isset( $args['styles'] ) && $this->is_style_support( $args['styles'], 'overlay' ) ) {
			if ( isset( $args['text_pos_hor'] ) && isset( $args['text_pos_ver'] ) ) {
				switch ( $args['text_pos_ver'] ) {
					case 'top':
						$vars .= '--dpt-text_pos_top: 0;';
						$vars .= '--dpt-text_pos_bottom: auto;';
						break;
					case 'middle':
						$vars .= '--dpt-text_pos_top: 50%;';
						$vars .= '--dpt-text_pos_bottom: auto;';
						break;
					default:
						$vars .= '--dpt-text_pos_top: auto;';
						$vars .= '--dpt-text_pos_bottom: 0;';
						break;
				}

				switch ( $args['text_pos_hor'] ) {
					case 'right':
						$vars .= '--dpt-text_pos_right: 5%;';
						$vars .= '--dpt-text_pos_left: auto;';
						break;
					case 'center':
						$vars .= '--dpt-text_pos_left: 50%;';
						$vars .= '--dpt-text_pos_right: auto;';
						break;
					default:
						$vars .= '--dpt-text_pos_right: auto;';
						$vars .= '--dpt-text_pos_left: 5%;';
						break;
				}

				if ( 'middle' === $args['text_pos_ver'] ) {
					if ( 'center' === $args['text_pos_hor'] ) {
						$vars .= '--dpt-text_pos_transform: translate(-50%, -50%);';
					} else {
						$vars .= '--dpt-text_pos_transform: translateY(-50%);';
					}
				} elseif ( 'center' === $args['text_pos_hor'] ) {
					$vars .= '--dpt-text_pos_transform: translateX(-50%);';
				}
			}
		}

		// Apply all variable styling to the current DPT instance.
		$style = sprintf( '#dpt-wrapper-%1$s { %2$s }', absint( $id ), $vars );

		if ( $style ) {
			?>
			<style type="text/css">
			<?php echo wp_strip_all_tags( $style, true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</style>
			<?php
		}
	}

	/**
	 * Add wrapper classes.
	 *
	 * @param str   $classes  Comma separated wrapper classes.
	 * @param array $args Settings for the current instance.
	 *
	 * @return array Array of supported display styles.
	 */
	public function wrapper_classes( $classes, $args ) {
		$classes[] = $args['img_aspect'];

		if ( $this->is_style_support( $args['styles'], 'multicol' ) ) {
			if ( 1 <= $args['col_narr'] ) {
				$classes[] = 'multi-col';
			}
		}

		if ( $this->is_style_support( $args['styles'], 'overlay' ) ) {
			$classes[] = 'dpt-image-overlay';
		}

		$wrap = 'dpt-flex-wrap';
		if ( '' !== $args['img_aspect'] ) {
			$classes[] = 'dpt-cropped';
		} elseif ( $this->is_style_support( $args['styles'], 'multicol' ) && 1 !== $args['col_narr'] ) {
			$wrap = 'dpt-mason-wrap';
		}

		if ( $this->is_style_support( $args['styles'], 'slider' ) ) {
			if ( $this->is_style_support( $args['styles'], 'customslider' ) ) {
				$classes[] = 'dpt-custom-slider';
			} else {
				$classes[] = 'dpt-slider';
				$wrap      = '';
				if ( ! $args['img_aspect'] ) {
					$wrap = 'dpt-mason-slider';
				}
			}
		}
		$classes[] = $wrap;

		if ( $this->is_style_support( $args['styles'], 'ialign' ) ) {
			if ( 'right' === $args['img_align'] ) {
				$classes[] = 'right-al';
			}
		}
		
		if ( isset( $args['wrapper_type'] ) && '' !== $args['wrapper_type'] ) {
            $classes[] = 'dpt-' . esc_attr( $args['wrapper_type'] ) . '-wraptype';
        }

		return array_filter( $classes );
	}

	/**
	 * Add html attributes to DPT wrapper.
	 *
	 * @param array $attr HTML attributes associative array.
	 * @param array $args Settings for the current instance.
	 */
	public function html_attr( $attr, $args ) {
		if ( isset( $args['autotime'] ) && $args['autotime'] ) {
			$attr['data-autotime'] = $args['autotime'];
		}

		return $attr;
	}

	/**
	 * Add entry classes.
	 *
	 * @param str   $classes     Comma separated entry posts classes.
	 * @param array $action_args Settings for the current instance.
	 *
	 * @return array Array of supported display styles.
	 */
	public function entry_classes( $classes, $action_args ) {
		if ( has_post_thumbnail() ) {
			$classes[] = 'has-thumbnail';
		} else {
			$classes[] = 'no-thumbnail';
		}

		return array_filter( $classes );
	}

	/**
	 * Display entry content to front-end.
	 *
	 * @param array $items Content display arguments.
	 * @param str   $args  Current display post style.
	 */
	public function render_entry( $items, $args ) {
		foreach ( $items as $item ) {
			if ( is_array( $item ) ) {
				Markup::markup( 'sub-entry', array( array( array( $this, 'render_entry' ), $item, $args ) ) );
			} else {
				switch ( $item ) {
					case 'title':
						$this->title();
						break;
					case 'date':
						$this->date();
						break;
					case 'ago':
						$this->ago();
						break;
					case 'author':
						$this->author();
						break;
					case 'content':
						$this->content();
						break;
					case 'excerpt':
						$this->excerpt( $args );
						break;
					case 'category':
						$this->meta2( $args );
						break;
					case 'meta':
						$this->meta( $args );
						break;
					case 'thumbnail':
						$this->featured( 'full', $args );
						break;
					case 'no-thumb':
						$this->featured( false, $args );
						break;
					default:
						do_action( 'display_dpt_item', $item, $args );
						break;
				}
			}
		}
	}

	/**
	 * Display post entry title.
	 *
	 * @since 1.0.0
	 */
	public function title() {
		Markup::markup(
			'dpt-title',
			array(
				function() {
					if ( get_the_title() ) {
						the_title(
							sprintf(
								'<a class="dpt-title-link" href="%s" rel="bookmark">',
								esc_url( get_permalink() )
							),
							'</a>'
						);
					}
				}
			),
			'<h3%s>',
			'</h3>'
		);
	}

	/**
	 * Display post entry date.
	 *
	 * @since 1.0.0
	 */
	public function date() {
		Markup::markup(
			'dpt-date',
			array(
				function() {
					printf(
						'<time datetime="%s">%s</time>',
						esc_attr( get_the_date( DATE_W3C ) ),
						esc_html( get_the_date() )
					);
				}
			)
		);
	}

	/**
	 * Display human readable post entry date.
	 *
	 * @since 1.0.0
	 */
	public function ago() {

		$time = sprintf(
			/* translators: %s: human-readable time difference */
			esc_html_x( '%s ago', 'human-readable time difference', 'display-post-types' ),
			esc_html( human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) )
		);

		printf( '<div class="dpt-date">%s</div>', $time ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Display post entry author.
	 *
	 * @since 1.0.0
	 */
	public function author() {
		Markup::markup(
			'dpt-author',
			array(
				function() {
					printf(
						'<a href="%s"><span>%s</span></a>',
						esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
						esc_html( get_the_author_meta( 'display_name' ) )
					);
				}
			)
		);
	}

	/**
	 * Display post entry author.
	 *
	 * @since 1.0.0
	 */
	public function permalink() {
		Markup::markup(
			'dpt-permalink',
			array(
				function() {
					printf(
						'<a href="%s" class="dpt-permalink"><span class="screen-reader-text">%s</span></a>',
						esc_url( get_permalink() ),
						the_title( '', '', false ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					);
				}
			)
		);
	}

	/**
	 * Display post featured content.
	 *
	 * @since 1.0.0
	 *
	 * @param str   $size Thumbanil Size.
	 * @param array $args Current instance settings.
	 */
	public function featured( $size, $args ) {
		$style = $args['styles'];

		if ( has_post_thumbnail() || ( isset( $args['pl_holder'] ) && 'yes' === $args['pl_holder'] ) ) {

			Markup::markup(
				'dpt-featured-content',
				array(
					array( array( $this, 'permalink' ) ),
					array( array( $this, 'thumbnail' ), $size, $args ),
				)
			);
		}
	}

	/**
	 * Display post entry thumbnail.
	 *
	 * @since 1.0.0
	 *
	 * @param string $size Thumbanil Size.
	 * @param array  $args Current instance settings.
	 */
	public function thumbnail( $size = 'full', $args = array() ) {
		$aspect_ratio = '100%';
		$id      = get_post_thumbnail_id();
		$imgmeta = wp_get_attachment_metadata( $id );
		if ( isset( $imgmeta['width'] ) && isset( $imgmeta['height'] ) && $imgmeta['width'] ) {
			$aspect_ratio = round($imgmeta['height'] / $imgmeta['width'] * 100) . '%';
		}

		if ( isset( $args['img_aspect'] ) && '' !== $args['img_aspect'] ) {
			if ( 'custom' === $args['img_aspect'] && isset( $args['custom_aspect'] ) && $args['custom_aspect'] ) {
				$aspect_ratio = $args['custom_aspect'] . '%';
			} else {
				$aspect_ratio = Get_Fn::crop_ratio( $args['img_aspect'] );
			}
		}

		Markup::markup(
			'dpt-thumbnail',
			array(
				function() use ( $size, $aspect_ratio ) {
					echo '<div class="dpt-thumbnail-inner">';
					the_post_thumbnail( $size, array( 'context' => 'dpt' ) );
					echo '</div>';
					echo '<span class="dpt-thumbnail-aspect-ratio" style="padding-top: ' . esc_attr( $aspect_ratio ) . '"></span>';
				}
			)
		);
	}

	/**
	 * Display post content.
	 *
	 * @since 1.0.0
	 */
	public function content() {
		Markup::markup( 'dpt-content', array( 'the_content' ) );
	}

	/**
	 * Display post content.
	 *
	 * @since 1.0.0
	 *
	 * @param str $args Current display post args.
	 */
	public function excerpt( $args ) {

		$text = has_excerpt() ? get_the_excerpt() : get_the_content( '' );
		$text = wp_strip_all_tags( strip_shortcodes( $text ) );
		$text = str_replace( ']]>', ']]&gt;', $text );

		$excerpt_length = isset( $args['e_length'] ) ? absint( $args['e_length'] ) : 35;
		$teaser_button = isset( $args['excerpt_teaser_btn'] ) && $args['excerpt_teaser_btn'] ? true : false;

		// Generate excerpt teaser text and link.
		$exrpt_text  = $args['e_teaser'] ? esc_html( $args['e_teaser'] ) : ( $teaser_button ? esc_html__( 'Continue Reading', 'dpt-pro' ) : '' );
		$excerpt_teaser = '';
		$teaser_class = $excerpt_length > 0 ? 'dpt-has-teaser dpt-link-more' : 'dpt-link-more';
		if ( $exrpt_text ) {
			$excerpt_teaser = sprintf( '<p class="%1$s"><a class="dpt-more-link" href="%2$s"><span>%3$s</span> <span class="screen-reader-text">%4$s</span></a></p>', $teaser_class, esc_url( get_permalink() ), $exrpt_text, get_the_title() );
		}
		$more = $teaser_button ? '' : $excerpt_teaser;
		$text = wp_trim_words( $text, $excerpt_length, $more );

		$text = $teaser_button ? $text . $excerpt_teaser : $text;
		
		Markup::markup(
			'dpt-excerpt',
			array(
				function() use ( $text ) {
					echo $text;
				}
			)
		);
	}

	/**
	 * Display post categories.
	 *
	 * @since 1.0.0
	 */
	public function category() {
		Markup::markup( 'dpt-categories', array( array( 'the_category', ', ' ) ) );
	}

	/**
	 * Display post categories.
	 *
	 * @since 1.0.0
	 */
	public function tags() {
		Markup::markup( 'dpt-tags', array( array( 'the_tags', '', ', ' ) ) );
	}

	/**
	 * Display post meta.
	 *
	 * @since 2.1.0
	 *
	 * @param array $args Settings for DPT display instance.
	 */
	public function meta( $args ) {
		Markup::markup(
			array( 'dpt-meta', 'dpt-meta1' ),
			array(
				function() use ( $args ) {
					$meta = isset( $args['meta1'] ) && $args['meta1'] ? $args['meta1'] : false;
					$content = $meta ? $this->get_converted_meta( $meta ) : false;
					if ( $content ) {
						echo $content;
					} else {
						$this->author();
						$this->date();
					}
				}
			)
		);
	}

	/**
	 * Display post meta.
	 *
	 * @since 2.1.0
	 *
	 * @param array $args Settings for DPT display instance.
	 */
	public function meta2( $args ) {
		Markup::markup(
			'dpt-meta2',
			array(
				function() use ( $args ) {
					$meta = isset( $args['meta2'] ) && $args['meta2'] ? $args['meta2'] : false;
					$content = $meta ? $this->get_converted_meta( $meta ) : false;
					if ( $content ) {
						echo $content;
					} else {
						$this->category();
					}
				}
			)
		);
	}

	/**
	 * Convert and return post meta symbols to relevant HTML.
	 *
	 * @since 2.1.0
	 *
	 * @param string $text Text to fetch post meta.
	 */
	public function get_converted_meta( $text ) {
		$text = esc_html( $text );
		if ( ! $text ) {
			return '';
		}

		if ( false !== strpos( $text, '[author]' ) ) {
			ob_start();
			$this->author();
			$content = ob_get_clean();
			$text    = str_replace( '[author]', $content, $text );
		}

		if ( false !== strpos( $text, '[date]' ) ) {
			ob_start();
			$this->date();
			$content = ob_get_clean();
			$text    = str_replace( '[date]', $content, $text );
		}

		if ( false !== strpos( $text, '[category]' ) ) {
			ob_start();
			$this->category();
			$content = ob_get_clean();
			$text    = str_replace( '[category]', $content, $text );
		}

		if ( false !== strpos( $text, '[tags]' ) ) {
			ob_start();
			$this->tags();
			$content = ob_get_clean();
			$text    = str_replace( '[tags]', $content, $text );
		}

		return apply_filters( 'dpt_get_converted_meta', $text );
	}

	/**
	 * Check if item is supported by the style.
	 *
	 * @param string $style Current display style.
	 * @param string $item  item to be checked for support.
	 * @return bool
	 */
	public function is_style_support( $style, $item ) {
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
	 * Extend the default WordPress body classes.
	 *
	 * @since 1.0.0
	 *
	 * @param array $classes Classes for the body element.
	 * @return array
	 */
	public function add_body_classes( $classes ) {
		$classes[] = 'dpt';
		return $classes;
	}

	public function img_attrs( $attr, $attachment, $size ) {
		if ( ! isset( $attr['context'] ) || 'dpt' !== $attr['context'] || ! isset( $attr['srcset'] ) ||  empty( $attr['srcset'] ) ) {
			return $attr;
		}

		if ( isset( $attr['src'] ) && ! empty( $attr['src'] ) ) {
			$is_internal = Validation_Fn::is_internal_link( $attr['src'] );
			if ( ! $is_internal ) {
				return $attr;
			}
			$attr['data-dpt-src'] = $attr['src'];
			unset( $attr['src'] );
		} else {
			return $attr;
		}

		if ( isset( $attr['sizes'] ) ) {
			$attr['data-dpt-sizes'] = $attr['sizes'];
			unset( $attr['sizes'] );
		}

		if ( isset( $attr['srcset'] ) ) {
			$attr['data-dpt-srcset'] = $attr['srcset'];
			unset( $attr['srcset'] );
		}

		return $attr;
	}

	/**
	 * Add icons definitions.
	 *
	 * @since 2.8.4
	 *
	 * @param object $icons Icon loader instance.
	 */
	public static function add_icons_definitions( $icons ) {
		add_filter( 'wp_footer', array( $icons, 'add_icons' ), 9999 );
	}

	/**
	 * Returns the instance of this class.
	 *
	 * @since  1.0.0
	 *
	 * @return object Instance of this class.
	 */
	public static function get_instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
