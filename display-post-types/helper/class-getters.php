<?php
/**
 * The front end specific functionality of the plugin.
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
class Getters {

	/**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 */
	public function __construct() {}

	/**
	 * Get list of all registered post types.
	 *
	 * @return array
	 */
	public static function post_types() {

		// Default Post and Pages post types.
		$default = array(
			'post' => esc_html__( 'Posts', 'display-post-types' ),
			'page' => esc_html__( 'Pages', 'display-post-types' ),
		);

		// Get the registered post types.
		$post_types = get_post_types(
			array(
				'public'   => true,
				'_builtin' => false,
			),
			'objects'
		);
		$post_types = wp_list_pluck( $post_types, 'label', 'name' );
		$post_types = array_merge( $default, $post_types );

		return $post_types;
	}

	/**
	 * Get list of taxonomies.
	 *
	 * @return array
	 */
	public static function taxonomies() {

		// Default taxonomies.
		$default = array(
			''         => esc_html__( 'Ignore Taxonomy', 'display-post-types' ),
			'category' => esc_html__( 'Categories', 'display-post-types' ),
			'post_tag' => esc_html__( 'Tags', 'display-post-types' ),
		);

		// Get list of all registered taxonomies.
		$taxonomies = get_taxonomies(
			array(
				'public'   => true,
				'_builtin' => false,
			),
			'objects'
		);

		// Get 'select' options as value => label.
		$options = wp_list_pluck( $taxonomies, 'label', 'name' );
		$options = array_merge( $default, $options );

		return $options;
	}

	/**
	 * Get list of taxonomies.
	 *
	 * @param  WP_REST_Request $request Request data.
	 *
	 * @return array
	 */
	public static function object_taxonomies( $request ) {

		$taxs = array();
		if ( isset( $request['post_type'] ) ) {
			// Get list of all registered taxonomies.
			$taxs = get_object_taxonomies( sanitize_text_field( $request['post_type'] ), 'objects' );
		}

		if ( empty( $taxs ) ) {
			return $taxs;
		}

		// Get 'select' options as value => label.
		$taxonomies = wp_list_pluck( $taxs, 'label', 'name' );

		if ( isset( $taxonomies['post_format'] ) ) {
			unset( $taxonomies['post_format'] );
		}

		$taxonomies[''] = esc_html__( '- Ignore Taxonomy -', 'display-post-types' );

		return $taxonomies;
	}

	/**
	 * Get list of taxonomies.
	 *
	 * @return array
	 */
	public static function pagelist() {

		// Get list of all pages.
		$pages = get_pages( array( 'exclude' => get_option( 'page_for_posts' ) ) );
		$pages = wp_list_pluck( $pages, 'post_title', 'ID' );

		return $pages;
	}

	/**
	 * Get list of taxonomies.
	 *
	 * @param  WP_REST_Request $request Request data.
	 *
	 * @return array
	 */
	public static function terms( $request ) {

		$terms = array();
		if ( isset( $request['taxonomy'] ) ) {
			$terms = get_terms(
				array(
					'taxonomy'   => sanitize_text_field( $request['taxonomy'] ),
					'hide_empty' => true,
				)
			);
			if ( is_wp_error( $terms ) ) {
				$terms = array();
			}
		}

		if ( empty( $terms ) ) {
			return $terms;
		}

		// Get 'select' options as value => label.
		$termlist = wp_list_pluck( $terms, 'name', 'slug' );
		return $termlist;
	}

	/**
	 * Get options default values.
	 *
	 * @return array
	 */
	public static function defaults() {
		$defaults = array(
			'title'         => '',
			'post_type'     => '',
			'taxonomy'      => '',
			'terms'         => array(),
			'relation'      => 'IN',
			'post_ids'      => '',
			'pages'         => array(),
			'number'        => 9,
			'orderby'       => 'date',
			'order'         => 'DESC',
			'styles'        => 'dpt-grid1',
			'style_sup'     => array( 'thumbnail', 'title' ),
			'image_crop'    => 'centercrop',
			'img_aspect'    => '',
			'text_pos_hor'  => '',
			'text_pos_ver'  => '',
			'custom_aspect' => 60,
			'img_align'     => '',
			'br_radius'     => 5,
			'col_narr'      => 3,
			'pl_holder'     => 'yes',
			'show_pgnation' => '',
			'title_shadow'  => 'yes',
			'text_align'    => '',
			'v_gutter'      => 20,
			'h_gutter'      => 20,
			'e_length'      => 35,
			'e_teaser'      => __( 'Continue reading', 'display-post-types' ),
			'classes'       => '',
			'offset'        => 0,
			'autotime'      => 0,
			'meta1'         => '[author] &middot; [date]',
			'meta2'         => '[category]',
		);
		$components = array( 'thumbnail', 'title', 'excerpt', 'meta1', 'meta2' );
		foreach ( $components as $component ) {
			$default_font_weight = 'title' === $component ? 600 : 400;
			$default_line_height = in_array( $component, array( 'meta1', 'meta2' ) ) ? 1.9 : 1.5;
			$type_sets = array(
				'font_style' => 'normal',
				'font_weight' => $default_font_weight,
				'font_size' => 0,
				'line_height' => $default_line_height,
				'letter_spacing' => 0,
				'text_decoration' => 'none',
				'text_transform' => 'none',
				'text_color' => '',
				'link_color' => '',
				'link_hover_color' => '',
			);
			$class = array(
				'class' => '',
			);
			foreach ( $type_sets as $key => $value ) {
				if ( 'thumbnail' === $component ) {
					continue;
				}
				$defaults[ $component . '_' . $key ] = $value;
			}
			foreach ($class as $key => $value) {
				$defaults[ $component . '_' . $key ] = $value;
			}
		}

		$wrapper_sets = array(
			'type'                   => '',
            'padding'                => array(10, 10, 10, 10),
            'br_width'               => array(1, 1, 1, 1),
            'br_radius'              => array(5, 5, 5, 5),
            'width'                  => 80,
            'height'                 => 0,
            'br_color'               => '',
		);
		foreach ( $wrapper_sets as $key => $value ) {
			$defaults[ 'wrapper_' . $key ] = $value;
		}

		return apply_filters( 'dpt_defaults', $defaults );
	}

	/**
	 * Register widget display styles.
	 *
	 * @return array Array of supported display styles.
	 */
	public static function styles() {
		return apply_filters(
			'dpt_styles',
			array(
				'dpt-list1'   => array(
					'style'   => 'dpt-list1',
					'label'   => esc_html__( 'List - Full', 'display-post-types' ),
					'support' => array( 'thumbnail', 'meta', 'title', 'excerpt', 'category', 'ialign', 'pagination' ),
				),
				'dpt-list2'   => array(
					'style'   => 'dpt-list2',
					'label'   => esc_html__( 'List - Mini', 'display-post-types' ),
					'support' => array( 'thumbnail', 'meta', 'title', 'category', 'ialign', 'pagination' ),
				),
				'dpt-grid1'   => array(
					'style'   => 'dpt-grid1',
					'label'   => esc_html__( 'Grid - Normal', 'display-post-types' ),
					'support' => array( 'thumbnail', 'meta', 'title', 'excerpt', 'category', 'multicol', 'pagination' ),
				),
				'dpt-grid2'   => array(
					'style'   => 'dpt-grid2',
					'label'   => esc_html__( 'Grid - Overlay', 'display-post-types' ),
					'support' => array( 'thumbnail', 'meta', 'title', 'category', 'multicol', 'pagination', 'overlay' ),
				),
				'dpt-slider1' => array(
					'style'   => 'dpt-slider1',
					'label'   => esc_html__( 'Slider - Normal', 'display-post-types' ),
					'support' => array( 'thumbnail', 'meta', 'title', 'category', 'multicol', 'slider', 'overlay' ),
				),
				'dpt-mag1'    => array(
					'style'   => 'dpt-mag1',
					'label'   => esc_html__( 'Magazine Layout 1', 'display-post-types' ),
					'support' => array( 'thumbnail', 'meta', 'title', 'excerpt', 'pagination' ),
				)
			)
		);
	}

	/**
	 * Get aspect ratio for cropped images.
	 *
	 * @since 2.4.0
	 *
	 * @param string $size Image crop name.
	 */
	public static function  crop_ratio( $size ) {
		$sizes = array(
			'land1'  => '75%',
			'land2'  => '66.66%',
			'port1'  => '133%',
			'port2'  => '150%',
			'wdscrn' => '56.25%',
			'squr'   => '100%',
		);

		if ( isset( $sizes[ $size ] ) ) {
			return $sizes[ $size ];
		}

		return '100%';
	}
}
