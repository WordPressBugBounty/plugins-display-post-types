<?php
/**
 * Display post types block.
 *
 * @package Display_Post_Types
 * @since 1.0.0
 */

namespace Display_Post_Types\Backend\Inc;

use Display_Post_Types\Frontend\Inc\Display;

/**
 * The back-end specific functionality of the plugin.
 *
 * @since 1.0.0
 */
class Block {

	/**
	 * Holds the instance of this class.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    object
	 */
	protected static $instance = null;

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
		add_action( 'init', array( $inst, 'register_block' ) );
		add_action( 'rest_api_init', array( $inst, 'register_routes' ) );
		add_action( 'enqueue_block_editor_assets', array( $inst, 'block_assets' ) );
	}

	private function get_block_attributes() {
		$attributes = array(
			'title'        => array(
				'type'    => 'string',
				'default' => '',
			),
			'postType'     => array(
				'type'    => 'string',
				'default' => 'post',
			),
			'taxonomy'     => array(
				'type'    => 'string',
				'default' => '',
			),
			'terms'        => array(
				'type'    => 'array',
				'items'   => array(
					'type' => 'string',
				),
				'default' => array(),
			),
			'relation'     => array(
				'type'    => 'string',
				'default' => 'IN',
			),
			'postIds'      => array(
				'type'    => 'string',
				'default' => '',
			),
			'pages'        => array(
				'type'    => 'array',
				'items'   => array(
					'type' => 'string',
				),
				'default' => array(),
			),
			'number'       => array(
				'type'    => 'number',
				'default' => 9,
			),
			'orderBy'      => array(
				'type'    => 'string',
				'default' => 'date',
			),
			'order'        => array(
				'type'    => 'string',
				'default' => 'DESC',
			),
			'styles'       => array(
				'type'    => 'string',
				'default' => 'dpt-grid1',
			),
			'styleSup'     => array(
				'type'    => 'array',
				'items'   => array(
					'type' => 'string',
				),
				'default' => array( 'thumbnail', 'title' ),
			),
			'imageCrop'    => array(
				'type'    => 'string',
				'default' => 'centercrop',
			),
			'imgAspect'    => array(
				'type'    => 'string',
				'default' => '',
			),
			'customAspect' => array(
				'type'    => 'number',
				'default' => 60,
			),
			'imgAlign'     => array(
				'type'    => 'string',
				'default' => '',
			),
			'brRadius'     => array(
				'type'    => 'number',
				'default' => 5,
			),
			'colNarr'      => array(
				'type'    => 'number',
				'default' => 3,
			),
			'autoTime'     => array(
				'type'    => 'number',
				'default' => 0,
			),
			'textPosHor'  => array(
				'type'    => 'string',
				'default' => '',
			),
			'textPosVer'  => array(
				'type'    => 'string',
				'default' => '',
			),
			'plHolder'     => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'showPgnation' => array(
				'type'    => 'boolean',
				'default' => false,
			),
			'titleShadow'  => array(
				'type'    => 'boolean',
				'default' => true,
			),
			'textAlign'    => array(
				'type'    => 'string',
				'default' => '',
			),
			'vGutter'      => array(
				'type'    => 'number',
				'default' => 20,
			),
			'hGutter'      => array(
				'type'    => 'number',
				'default' => 20,
			),
			'eLength'      => array(
				'type'    => 'number',
				'default' => 20,
			),
			'eTeaser'      => array(
				'type'    => 'string',
				'default' => __( 'Continue reading', 'display-post-types' ),
			),
			'className'    => array(
				'type' => 'string',
			),
			'offset'       => array(
				'type'    => 'number',
				'default' => 0,
			),
			'meta1'        => array(
				'type'    => 'string',
				'default' => '[author] &middot; [date]',
			),
			'meta2'        => array(
				'type'    => 'string',
				'default' => '[category]',
			),
		);

		$typography = array(
			'font_style' => array(
				'type'    => 'string',
				'default' => 'normal',
			),
			'font_weight' => array(
				'type'    => 'number',
				'default' => 400,
			),
			'font_size' => array(
				'type'    => 'number',
				'default' => 0,
			),
			'line_height' => array(
				'type'    => 'number',
				'default' => 1.5,
			),
			'letter_spacing' => array(
				'type'    => 'number',
				'default' => 0,
			),
			'text_transform' => array(
				'type'    => 'string',
				'default' => 'none',
			),
			'text_decoration' => array(
				'type'    => 'string',
				'default' => 'none',
			),
			'text_color' => array(
				'type'    => 'string',
				'default' => '',
			),
			'link_color' => array(
				'type'    => 'string',
				'default' => '',
			),
			'link_hover_color' => array(
				'type'    => 'string',
				'default' => '',
			),
		);
		$space = array(
			'class' => array(
				'type'    => 'string',
				'default' => '',
			)
		);

		$wrapper = array(
            'type' => array(
                'type' => 'string',
                'default' => '',
            ),
            'padding' => array(
                'type'    => 'array',
                'items'   => array(
                    'type' => 'number',
                ),
                'default' => array(10, 10, 10, 10),
            ),
            'width' => array(
                'type' => 'number',
                'default' => 80,
            ),
            'height' => array(
                'type' => 'number',
                'default' => 0,
            ),
            'br_color' => array(
                'type' => 'string',
                'default' => '',
            ),
			'br_width' => array(
                'type'    => 'array',
                'items'   => array(
                    'type' => 'number',
                ),
                'default' => array(1, 1, 1, 1),
            ),
            'br_radius' => array(
                'type'    => 'array',
                'items'   => array(
                    'type' => 'number',
                ),
                'default' => array(5, 5, 5, 5),
            ),
        );

		$components = array('thumbnail', 'title', 'excerpt', 'meta1', 'meta2');
		foreach ( $components as $component ) {
			$typography['font_weight']['default'] = 'title' === $component ? 600 : 400;
			$typography['line_height']['default'] = in_array( $component, array( 'meta1', 'meta2' ) ) ? 1.9 : 1.5;
			foreach ( $typography as $key => $value ) {
				if ( 'thumbnail' === $component ) {
					continue;
				}
				$attributes[$component . '_' . $key] = $value;
			}
			foreach ( $space as $key => $value ) {
				$attributes[$component . '_' . $key] = $value;
			}
		}
		foreach ( $wrapper as $key => $value ) {
			$attributes['wrapper_' . $key] = $value;
		}

		return $attributes;
	}

	/**
	 * Register editor blocks.
	 *
	 * @since 1.0.0
	 */
	public function register_block() {
		// Check if the register function exists.
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		register_block_type(
			'dpt/display-post-types',
			apply_filters( 'dpt_block_settings',
				array(
					'render_callback' => array( $this, 'render_dpt' ),
					'attributes'      => apply_filters( 'dpt_block_attributes', $this->get_block_attributes() ),
				)
			)
		);
	}

	/**
	 * Render editor block for display posts.
	 *
	 * @since 1.0.0
	 *
	 * @param array $atts Display attributes.
	 */
	public function render_dpt( $atts ) {
		$classes = isset( $atts['className'] ) ? $atts['className'] : '';
		ob_start();
		$attributes = array(
			'title'         => $atts['title'],
			'post_type'     => $atts['postType'],
			'taxonomy'      => $atts['taxonomy'],
			'terms'         => $atts['terms'],
			'relation'      => $atts['relation'],
			'post_ids'      => $atts['postIds'],
			'pages'         => $atts['pages'],
			'number'        => $atts['number'],
			'orderby'       => $atts['orderBy'],
			'order'         => $atts['order'],
			'styles'        => $atts['styles'],
			'style_sup'     => $atts['styleSup'],
			'image_crop'    => $atts['imageCrop'],
			'img_aspect'    => $atts['imgAspect'],
			'custom_aspect' => $atts['customAspect'],
			'img_align'     => $atts['imgAlign'],
			'br_radius'     => $atts['brRadius'],
			'col_narr'      => $atts['colNarr'],
			'autotime'      => $atts['autoTime'],
			'text_pos_hor'  => $atts['textPosHor'],
			'text_pos_ver'  => $atts['textPosVer'],
			'text_align'    => $atts['textAlign'],
			'v_gutter'      => $atts['vGutter'],
			'h_gutter'      => $atts['hGutter'],
			'e_length'      => $atts['eLength'],
			'e_teaser'      => $atts['eTeaser'],
			'meta1'         => $atts['meta1'],
			'meta2'         => $atts['meta2'],
			'classes'       => $classes,
			'offset'        => $atts['offset'],
			'pl_holder'     => ( 'false' === $atts['plHolder'] || ! $atts['plHolder'] ) ? '' : 'yes',
			'show_pgnation' => ( 'false' === $atts['showPgnation'] || ! $atts['showPgnation'] ) ? '' : 'yes',
			'title_shadow'  => ( 'false' === $atts['titleShadow'] || ! $atts['titleShadow'] ) ? '' : 'yes',
		);

		$components = array('thumbnail', 'title', 'excerpt', 'meta1', 'meta2');
		$typography = array(
			'font_style',
			'font_weight',
			'font_size',
			'line_height',
			'letter_spacing',
			'text_transform',
			'text_decoration',
			'text_color',
			'link_color',
			'link_hover_color',
		);
		$space = array(
			'class',
		);
		$wrapper = array(
			'type',
			'padding',
			'width',
			'height',
			'br_color',
			'br_width',
			'br_radius',
		);
		foreach( $components as $component ) {
			foreach ( $typography as $typo ) {
				if ( 'thumbnail' === $component ) {
					continue;
				}
				$attributes[ $component . '_' . $typo ] = $atts[ $component . '_' . $typo ];
			}
			foreach ( $space as $key ) {
				$attributes[ $component . '_' . $key ] = $atts[ $component . '_' . $key ];
			}
		}
		foreach ( $wrapper as $key ) {
			$attributes['wrapper_' . $key] = $atts['wrapper_' . $key];
		}
		$attributes = apply_filters( 'dpt_render_attributes', $attributes, $atts, 'block' );
		$all_attrs = $this->get_block_attributes();
		$extra = array_diff_key( $atts, $all_attrs );
		$args = apply_filters( 'dpt_block_display_args', array_merge( $attributes, $extra ), $atts );
		ob_start();
		Display::init( $args );
		$content = ob_get_clean();
		return $content;
	}

	/**
	 * Create REST API endpoints to get all pages list.
	 *
	 * @since 1.0.0
	 */
	public function register_routes() {

		register_rest_route(
			'dpt/v1',
			'posttypes',
			array(
				'methods'             => 'GET',
				'callback'            => array( 'Display_Post_Types\Helper\Getters', 'post_types' ),
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);

		register_rest_route(
			'dpt/v1',
			'pagelist',
			array(
				'methods'             => 'GET',
				'callback'            => array( 'Display_Post_Types\Helper\Getters', 'pagelist' ),
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);

		register_rest_route(
			'dpt/v1',
			'stylelist',
			array(
				'methods'             => 'GET',
				'callback'            => array( 'Display_Post_Types\Helper\Getters', 'styles' ),
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);

		register_rest_route(
			'dpt/v1',
			'/taxonomies/(?P<post_type>[\w-]+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( 'Display_Post_Types\Helper\Getters', 'object_taxonomies' ),
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
				'args'                => array(
					'post_type' => array(
						'description' => esc_html__( 'Post Type', 'display-post-types' ),
						'type'        => 'string',
					),
				),
			)
		);

		register_rest_route(
			'dpt/v1',
			'/terms/(?P<taxonomy>[\w-]+)',
			array(
				'methods'             => 'GET',
				'callback'            => array( 'Display_Post_Types\Helper\Getters', 'terms' ),
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
				'args'                => array(
					'taxonomy' => array(
						'description' => esc_html__( 'Taxonomy', 'display-post-types' ),
						'type'        => 'string',
					),
				),
			)
		);
	}

	/**
	 * Register block assets.
	 *
	 * @since    1.0.0
	 */
	public function block_assets() {
		wp_enqueue_script(
			'dpt-flickity',
			plugins_url( '/frontend/js/flickity.pkgd.min.js', dirname( dirname( __FILE__ ) ) ),
			array(),
			DISPLAY_POST_TYPES_VERSION,
			true
		);

		wp_enqueue_script(
			'dpt-bricklayer',
			plugins_url( '/frontend/js/bricklayer.build.js', dirname( dirname( __FILE__ ) ) ),
			array(),
			DISPLAY_POST_TYPES_VERSION,
			true
		);

		wp_enqueue_script(
			'dpt-scripts',
			plugins_url( '/frontend/js/scripts.build.js', dirname( dirname( __FILE__ ) ) ),
			array( 'dpt-bricklayer', 'dpt-flickity' ),
			DISPLAY_POST_TYPES_VERSION,
			true
		);

		wp_enqueue_style(
			'dpt-blocks-css',
			plugins_url( '/css/blocks.css', dirname( __FILE__ ) ),
			array(),
			DISPLAY_POST_TYPES_VERSION,
			'all'
		);

		$dpt_block_data = apply_filters( 'dpt_block_script_data', array() );
		wp_enqueue_script(
			'dpt-blocks-js',
			plugins_url( '/js/blocks.build.js', dirname( __FILE__ ) ),
			array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor', 'wp-api-fetch', 'wp-block-editor', 'wp-server-side-render', 'jquery' ),
			DISPLAY_POST_TYPES_VERSION,
			true
		);
		wp_localize_script( 'dpt-blocks-js', 'dptBlockData', $dpt_block_data );
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

Block::init();
