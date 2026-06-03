<?php
/**
 * Display post types shortcode.
 *
 * @package Display_Post_Types
 * @since 6.8.0
 */

namespace Display_Post_Types\Backend\Admin;

use Display_Post_Types\Helper\Getters as Get_Fn;
use Display_Post_Types\Frontend\Inc\Display;
use Display_Post_Types\Helper\Security;

/**
 * Display post types shortcode.
 *
 * @since 6.8.0
 */
class ShortCodeGen {

    /**
	 * Holds the instance of this class.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    object
	 */
	protected static $instance = null;

    /**
     * Holds all shortcode settings.
     *
     * @since  6.8.0
     * @access public
     * @var    array
     */
    public $shortcode_settings = array();

    /**
	 * Holds all registered post type objects.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array
	 */
	protected $post_types = array();

	/**
	 * Holds sort orderby options.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array
	 */
	protected $orderby = array();

	/**
	 * Holds image cropping options.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array
	 */
	protected $imagecrop = array();

	/**
	 * Holds image aspect ratio options.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array
	 */
	protected $aspectratio = array();

	/**
	 * Holds widget settings defaults, populated in constructor.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var array
	 */
	protected $defaults = array();

	/**
	 * Holds all display styles.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var array
	 */
	protected $styles = array();

	/**
	 * Holds all display contents.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var array
	 */
	protected $contents = array();

	/**
	 * Holds all display styles supported items.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var array
	 */
	protected $style_supported = array();

	/**
	 * Initialize the instance of this class.
	 *
	 * @since  6.8.0
	 */
	private function __construct() {
        // Getting shortcode values from the database.
        $shortcode_settings = get_option( 'dpt_shortcode_options' );
        $this->shortcode_settings = $shortcode_settings ? $shortcode_settings : array();

        // Set widget instance settings default values.
		$this->defaults          = Get_Fn::defaults();
		$this->defaults['title'] = '';

		// Set the options for orderby.
		$this->orderby = array(
			'date'          => esc_html__( 'Publish Date', 'display-post-types' ),
			'modified'      => esc_html__( 'Modified Date', 'display-post-types' ),
			'title'         => esc_html__( 'Title', 'display-post-types' ),
			'author'        => esc_html__( 'Author', 'display-post-types' ),
			'comment_count' => esc_html__( 'Comment Count', 'display-post-types' ),
			'rand'          => esc_html__( 'Random', 'display-post-types' ),
		);

		$this->imagecrop = array(
			'topleftcrop'      => esc_html__( 'Top Left Cropping', 'display-post-types' ),
			'topcentercrop'    => esc_html__( 'Top Center Cropping', 'display-post-types' ),
			'centercrop'       => esc_html__( 'Center Cropping', 'display-post-types' ),
			'bottomleftcrop'   => esc_html__( 'Bottom Left Cropping', 'display-post-types' ),
			'bottomcentercrop' => esc_html__( 'Bottom Center Cropping', 'display-post-types' ),
		);

		$this->aspectratio = array(
			''       => esc_html__( 'No Cropping', 'display-post-types' ),
			'land1'  => esc_html__( 'Landscape (4:3)', 'display-post-types' ),
			'land2'  => esc_html__( 'Landscape (3:2)', 'display-post-types' ),
			'port1'  => esc_html__( 'Portrait (3:4)', 'display-post-types' ),
			'port2'  => esc_html__( 'Portrait (2:3)', 'display-post-types' ),
			'wdscrn' => esc_html__( 'Widescreen (16:9)', 'display-post-types' ),
			'squr'   => esc_html__( 'Square (1:1)', 'display-post-types' ),
			'custom' => esc_html__( 'Custom Aspect Ratio', 'display-post-types' ),
		);

		// Get list of all registered supported contents.
		$this->contents = array(
			'thumbnail' => esc_html__( 'Thumbnail', 'display-post-types' ),
			'title'     => esc_html__( 'Title', 'display-post-types' ),
			'meta'      => esc_html__( 'Meta Info 1', 'display-post-types' ),
			'category'  => esc_html__( 'Meta Info 2', 'display-post-types' ),
			'excerpt'   => esc_html__( 'Excerpt', 'display-post-types' ),
			'date'      => esc_html__( 'Date', 'display-post-types' ),
			'ago'       => esc_html__( 'Ago', 'display-post-types' ),
			'author'    => esc_html__( 'Author', 'display-post-types' ),
			'content'   => esc_html__( 'Content', 'display-post-types' ),
		);
    }

	/**
	 * Outputs the content for the current widget instance.
	 *
	 * @since 6.8.0
	 *
	 * @param array $instance Settings for the current widget instance.
     * @param bool $echo Echo or return the output.
	 */
	public function render( $instance, $echo ) {
		$instance = isset( $this->shortcode_settings[ $instance ] ) ? $this->shortcode_settings[ $instance ] : array();
        $instance = wp_parse_args( (array) $instance, $this->defaults );
        $instance = apply_filters( 'dpt_shortcode_display_instance', $instance );
		ob_start();
		Display::init( $instance );
		$content = ob_get_clean();
        if ( $echo ) {
            echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        } else {
            return $content;
        }
	}

	/**
	 * Get array of all widget options.
	 *
	 * @param array $instance Array of settings for current widget instance.
	 *
	 * @since 1.0.0
	 */
	public function get_widget_options( $instance ) {
		$widget    = $this;
		$post_type = Get_Fn::post_types();
		$post_type = array_merge( array( '' => esc_html__( 'None', 'display-post-types' ) ), $post_type );

		return apply_filters(
			'dpt_widget_options',
			array(
				'default' => array(
					'title'       => esc_html__( 'General Options', 'display-post-types' ),
					'op_callback' => function() use ( $widget, $instance ) {
						return '' !== $instance['post_type'];
					},
					'items'       => array(
						'title'     => array(
							'setting' => 'title',
							'label'   => esc_html__( 'Title', 'display-post-types' ),
							'type'    => 'text',
						),
						'post_type' => array(
							'setting' => 'post_type',
							'label'   => esc_html__( 'Select a Post Type', 'display-post-types' ),
							'type'    => 'select',
							'choices' => $post_type,
						),
					),
				),
				'fetch'   => array(
					'title'       => esc_html__( 'Get items to be displayed', 'display-post-types' ),
					'op_callback' => function() use ( $widget, $instance ) {
						return '' !== $instance['post_type'];
					},
					'items'       => array(
						'pages'         => array(
							'setting'       => 'pages',
							'type'          => 'pages',
							'hide_callback' => function() use ( $widget, $instance ) {
								return 'page' !== $instance['post_type'];
							},
						),
						'number'        => array(
							'setting'     => 'number',
							'label'       => esc_html__( 'Number of items to display', 'display-post-types' ),
							'type'        => 'number',
							'input_attrs' => array(
								'step' => 1,
								'min'  => 1,
								'size' => 3,
							),
						),
						'offset'        => array(
							'setting'       => 'offset',
							'label'         => esc_html__( 'Offset (number of posts to displace)', 'display-post-types' ),
							'type'          => 'number',
							'input_attrs'   => array(
								'step' => 1,
								'min'  => 0,
								'size' => 3,
							),
							'hide_callback' => function() use ( $widget, $instance ) {
								return 'page' === $instance['post_type'];
							},
						),
						'show_pgnation' => array(
							'setting'       => 'show_pgnation',
							'label'         => esc_html__( 'Show Pagination', 'display-post-types' ),
							'type'          => 'checkbox',
							'hide_callback' => function() use ( $widget, $instance ) {
								return ! $widget->is_style_support( $instance['styles'], 'pagination' );
							},
						),
					),
				),
				'filter'  => array(
					'title'       => esc_html__( 'Sort & Filter Items', 'display-post-types' ),
					'op_callback' => function() use ( $widget, $instance ) {
						return '' !== $instance['post_type'];
					},
					'items'       => array(
						'taxonomy'      => array(
							'setting'       => 'taxonomy',
							'type'          => 'taxonomy',
							'hide_callback' => function() use ( $widget, $instance ) {
								return 'page' === $instance['post_type'] || ! empty( $instance['query_tax_clauses'] );
							},
							'wrapper'       => 'filter_taxonomy',
						),
						'terms'         => array(
							'setting'       => 'terms',
							'type'          => 'terms',
							'hide_callback' => function() use ( $widget, $instance ) {
								return 'page' === $instance['post_type'] || ! empty( $instance['query_tax_clauses'] ) || '' === $instance['taxonomy'];
							},
							'wrapper'       => 'filter_taxonomy',
						),
						'relation'      => array(
							'setting'       => 'relation',
							'label'         => esc_html__( 'Terms Relationship', 'display-post-types' ),
							'type'          => 'select',
							'choices'       => array(
								'IN'  => esc_html__( 'OR - Show posts belong to any of the terms selected above.', 'display-post-types' ),
								'AND' => esc_html__( 'AND - Show posts only if they belong to all of the selected terms.', 'display-post-types' ),
							),
							'hide_callback' => function() use ( $widget, $instance ) {
								return 'page' === $instance['post_type'] || ! empty( $instance['query_tax_clauses'] ) || '' === $instance['taxonomy'];
							},
							'wrapper'       => 'filter_taxonomy',
						),
						'query_tax_clauses' => array(
							'setting'       => 'query_tax_clauses',
							'type'          => 'advanced_taxonomy',
							'hide_callback' => function() use ( $widget, $instance ) {
								return 'page' === $instance['post_type'];
							},
							'wrapper'       => 'filter_taxonomy',
						),
						'post_ids'      => array(
							'setting'       => 'post_ids',
							'label'         => esc_html__( 'Get items by Post IDs', 'display-post-types' ),
							'type'          => 'text',
							'input_attrs'   => array(
								'placeholder' => esc_attr_x( 'Comma separated ids, i.e. 230,300', 'Placeholder text for post ids', 'display-post-types' ),
							),
							'hide_callback' => function() use ( $widget, $instance ) {
								return 'page' === $instance['post_type'];
							},
							'wrapper'       => 'filter_postids',
						),
						'orderby'       => array(
							'setting'       => 'orderby',
							'label'         => esc_html__( 'Order By', 'display-post-types' ),
							'type'          => 'select',
							'choices'       => $this->orderby,
							'hide_callback' => function() use ( $widget, $instance ) {
								return 'page' === $instance['post_type'];
							},
							'wrapper'       => 'sort_items',
						),
						'order'         => array(
							'setting'       => 'order',
							'label'         => esc_html__( 'Sort Order', 'display-post-types' ),
							'type'          => 'select',
							'choices'       => array(
								'DESC' => esc_html__( 'Descending', 'display-post-types' ),
								'ASC'  => esc_html__( 'Ascending', 'display-post-types' ),
							),
							'hide_callback' => function() use ( $widget, $instance ) {
								return 'page' === $instance['post_type'];
							},
							'wrapper'       => 'sort_items',
						),
					),
				),
				'layout'  => array(
					'title'       => esc_html__( 'Layout & Styling', 'display-post-types' ),
					'op_callback' => function() use ( $widget, $instance ) {
						return '' !== $instance['post_type'];
					},
					'items'       => array(
						'styles'   => array(
							'setting' => 'styles',
							'label'   => esc_html__( 'Display Style', 'display-post-types' ),
							'type'    => 'select',
							'choices' => $this->get_display_styles(),
						),
						'col_narr' => array(
							'setting'       => 'col_narr',
							'label'         => esc_html__( 'Desktop grid columns', 'display-post-types' ),
							'type'          => 'number',
							'input_attrs'   => array(
								'step' => 1,
								'min'  => 1,
								'max'  => 8,
								'size' => 1,
							),
							'hide_callback' => function() use ( $widget, $instance ) {
								return ! $widget->is_style_support( $instance['styles'], 'multicol' );
							},
						),
						'col_narr_tab' => array(
							'setting'       => 'col_narr_tab',
							'label'         => esc_html__( 'Tablet grid columns', 'display-post-types' ),
							'type'          => 'number',
							'input_attrs'   => array(
								'step' => 1,
								'min'  => 0,
								'max'  => 8,
								'size' => 1,
							),
							'desc'          => esc_html__( 'Set to 0 to use the automatic tablet layout.', 'display-post-types' ),
							'hide_callback' => function() use ( $widget, $instance ) {
								return ! $widget->is_style_support( $instance['styles'], 'multicol' );
							},
						),
						'col_narr_mob' => array(
							'setting'       => 'col_narr_mob',
							'label'         => esc_html__( 'Mobile grid columns', 'display-post-types' ),
							'type'          => 'number',
							'input_attrs'   => array(
								'step' => 1,
								'min'  => 0,
								'max'  => 4,
								'size' => 1,
							),
							'desc'          => esc_html__( 'Set to 0 to use the automatic mobile layout.', 'display-post-types' ),
							'hide_callback' => function() use ( $widget, $instance ) {
								return ! $widget->is_style_support( $instance['styles'], 'multicol' );
							},
						),
						'h_gutter' => array(
							'setting'     => 'h_gutter',
							'label'       => esc_html__( 'Horizontal Gutter (in px)', 'display-post-types' ),
							'type'        => 'number',
							'input_attrs' => array(
								'step' => 1,
								'min'  => 0,
								'size' => 3,
							),
						),
						'v_gutter' => array(
							'setting'     => 'v_gutter',
							'label'       => esc_html__( 'Vertical Gutter (in px)', 'display-post-types' ),
							'type'        => 'number',
							'input_attrs' => array(
								'step' => 1,
								'min'  => 0,
								'size' => 3,
							),
						),
						'autotime' => array(
							'setting'       => 'autotime',
							'label'         => esc_html__( 'Auto slide timer (delay in ms)', 'display-post-types' ),
							'type'          => 'number',
							'input_attrs'   => array(
								'step' => 500,
								'min'  => 0,
								'max'  => 10000,
								'size' => 5,
							),
							'desc'          => esc_html__( 'Setting delay time to 0 will disable auto slide.', 'display-post-types' ),
							'hide_callback' => function() use ( $widget, $instance ) {
								return ! $widget->is_style_support( $instance['styles'], 'slider' );
							},
						),
					),
				),
				'style'   => array(
					'title'       => esc_html__( 'Manage Item Components', 'display-post-types' ),
					'op_callback' => function() use ( $widget, $instance ) {
						return '' !== $instance['post_type'];
					},
					'items'       => array(
						'style_sup' => array(
							'setting' => 'style_sup',
							'type'    => 'hiddenarray',
						),
						'text_align'   => array(
							'setting' => 'text_align',
							'label'   => esc_html__( 'Text Align', 'display-post-types' ),
							'type'    => 'select',
							'choices' => array(
								''       => esc_html__( 'Left Align', 'display-post-types' ),
								'r-text' => esc_html__( 'Right Align', 'display-post-types' ),
								'c-text' => esc_html__( 'Center Align', 'display-post-types' ),
							),
							'wrapper' => 'container',
						),
						'text_pos_hor'   => array(
							'setting' => 'text_pos_hor',
							'label'   => esc_html__( 'Horizontal Text Position on Image', 'display-post-types' ),
							'type'    => 'select',
							'choices' => array(
								''       => esc_html__( 'Left', 'display-post-types' ),
								'right'  => esc_html__( 'Right', 'display-post-types' ),
								'center' => esc_html__( 'Center', 'display-post-types' ),
							),
							'hide_callback' => function() use ( $widget, $instance ) {
								return ! $widget->is_style_support( $instance['styles'], 'overlay' );
							},
							'wrapper' => 'container',
						),
						'text_pos_ver'   => array(
							'setting' => 'text_pos_ver',
							'label'   => esc_html__( 'Verticle Text Position on Image', 'display-post-types' ),
							'type'    => 'select',
							'choices' => array(
								''       => esc_html__( 'Bottom', 'display-post-types' ),
								'top'    => esc_html__( 'Top', 'display-post-types' ),
								'middle' => esc_html__( 'Middle', 'display-post-types' ),
							),
							'hide_callback' => function() use ( $widget, $instance ) {
								return ! $widget->is_style_support( $instance['styles'], 'overlay' );
							},
							'wrapper' => 'container',
						),
						'show_thumb'   => array(
							'setting'    => 'style_sup',
							'label'      => esc_html__( 'Show Thumbnail', 'display-post-types' ),
							'value'      => 'thumbnail',
							'is_checked' => function() use ( $instance ) {
								return in_array( 'thumbnail', $instance['style_sup'], true ) ? 'checked' : '';
							},
							'type'       => 'spcheckbox',
							'wrapper'    => 'thumbnail',
						),
						'thumb_fetch'  => array(
							'setting'     => 'thumb_fetch',
							'label'       => esc_html__( 'Fetch Thumbnail from Content as fallback', 'display-post-types' ),
							'desc'        => esc_html__( 'If featured image is not set, fetch first image from post content as thumbnail', 'display-post-types' ),
							'type'        => 'checkbox',
							'hide_callback' => function() use ( $widget, $instance ) {
								return ! in_array( 'thumbnail', $instance['style_sup'], true );
							},
							'wrapper'     => 'thumbnail',
						),
						'br_radius'    => array(
							'setting'     => 'br_radius',
							'label'       => esc_html__( 'Thumbnail Border Radius (in px)', 'display-post-types' ),
							'type'        => 'number',
							'hide_callback' => function() use ( $widget, $instance ) {
								return ! in_array( 'thumbnail', $instance['style_sup'], true );
							},
							'input_attrs' => array(
								'step' => 1,
								'min'  => 0,
								'size' => 3,
							),
							'wrapper'     => 'thumbnail',
						),
						'pl_holder'    => array(
							'setting'       => 'pl_holder',
							'label'         => esc_html__( 'Thumbnail Placeholder.', 'display-post-types' ),
							'type'          => 'checkbox',
							'hide_callback' => function() use ( $widget, $instance ) {
								return ! in_array( 'thumbnail', $instance['style_sup'], true );
							},
							'wrapper'       => 'thumbnail',
						),
						'img_aspect'   => array(
							'setting'       => 'img_aspect',
							'label'         => esc_html__( 'Image Cropping', 'display-post-types' ),
							'type'          => 'select',
							'choices'       => $this->aspectratio,
							'hide_callback' => function() use ( $widget, $instance ) {
								return ! in_array( 'thumbnail', $instance['style_sup'], true );
							},
							'wrapper'       => 'thumbnail',
						),
						'custom_aspect' => array(
							'setting'       => 'custom_aspect',
							'label'         => esc_html( 'Thumbnail custom crop aspect ratio', 'display-post-types' ),
							'type'          => 'number',
							'input_attrs'   => array(
								'step' => 1,
								'min'  => 0,
								'size' => 3,
							),
							'hide_callback' => function() use ( $widget, $instance ) {
								return 'custom' !== $instance['img_aspect'] || ! in_array( 'thumbnail', $instance['style_sup'], true );
							},
							'wrapper'       => 'thumbnail',
						),
						'image_crop'   => array(
							'setting'       => 'image_crop',
							'label'         => esc_html__( 'Image Cropping Position', 'display-post-types' ),
							'type'          => 'select',
							'choices'       => $this->imagecrop,
							'hide_callback' => function() use ( $widget, $instance ) {
								return '' === $instance['img_aspect'] || ! in_array( 'thumbnail', $instance['style_sup'], true );
							},
							'wrapper'       => 'thumbnail',
						),
						'img_align'    => array(
							'setting'       => 'img_align',
							'label'         => esc_html__( 'Image Alignment', 'display-post-types' ),
							'type'          => 'select',
							'choices'       => array(
								''      => esc_html__( 'Left Aligned', 'display-post-types' ),
								'right' => esc_html__( 'Right Aligned', 'display-post-types' ),
							),
							'hide_callback' => function() use ( $widget, $instance ) {
								return ! $widget->is_style_support( $instance['styles'], 'ialign' ) || ! in_array( 'thumbnail', $instance['style_sup'], true );
							},
							'wrapper'       => 'thumbnail',
						),
						'show_title'   => array(
							'setting'    => 'style_sup',
							'label'      => esc_html__( 'Show Title', 'display-post-types' ),
							'value'      => 'title',
							'is_checked' => function() use ( $instance ) {
								return in_array( 'title', $instance['style_sup'], true ) ? 'checked' : '';
							},
							'type'       => 'spcheckbox',
							'wrapper'    => 'title',
						),
						'title_shadow' => array(
							'setting'       => 'title_shadow',
							'label'         => esc_html__( 'Show Title Shadow', 'display-post-types' ),
							'type'          => 'checkbox',
							'hide_callback' => function() use ( $widget, $instance ) {
								return ! $widget->is_style_support( $instance['styles'], 'overlay' );
							},
							'wrapper'       => 'title',
						),
						'show_excerpt' => array(
							'setting'    => 'style_sup',
							'label'      => esc_html__( 'Show Excerpt', 'display-post-types' ),
							'value'      => 'excerpt',
							'is_checked' => function() use ( $instance ) {
								return in_array( 'excerpt', $instance['style_sup'], true ) ? 'checked' : '';
							},
							'type'       => 'spcheckbox',
							'wrapper'    => 'excerpt',
						),
						'e_length'     => array(
							'setting'       => 'e_length',
							'label'         => esc_html__( 'Excerpt Length (in words)', 'display-post-types' ),
							'type'          => 'number',
							'input_attrs'   => array(
								'step' => 1,
								'min'  => 0,
								'size' => 3,
							),
							'hide_callback' => function() use ( $widget, $instance ) {
								return ! in_array( 'excerpt', $instance['style_sup'], true ) || ! $widget->is_style_support( $instance['styles'], 'excerpt' );
							},
							'wrapper'       => 'excerpt',
						),
						'e_teaser'     => array(
							'setting'       => 'e_teaser',
							'label'         => esc_html__( 'Excerpt Teaser Text', 'display-post-types' ),
							'type'          => 'text',
							'input_attrs'   => array(
								'placeholder' => esc_attr_x( 'i.e., Continue Reading, Read More', 'Placeholder text for excerpt teaser', 'display-post-types' ),
							),
							'hide_callback' => function() use ( $widget, $instance ) {
								return ! in_array( 'excerpt', $instance['style_sup'], true ) || ! $widget->is_style_support( $instance['styles'], 'excerpt' );
							},
							'wrapper'       => 'excerpt',
						),
						'show_meta1'   => array(
							'setting'    => 'style_sup',
							'label'      => esc_html__( 'Show Meta Info - 1', 'display-post-types' ),
							'value'      => 'meta',
							'is_checked' => function() use ( $instance ) {
								return in_array( 'meta', $instance['style_sup'], true ) ? 'checked' : '';
							},
							'type'       => 'spcheckbox',
							'wrapper'    => 'meta',
						),
						'meta1'        => array(
							'setting'       => 'meta1',
							'label'         => esc_html__( 'Meta Info to be displayed', 'display-post-types' ),
							'type'          => 'text',
							'hide_callback' => function() use ( $widget, $instance ) {
								return ! in_array( 'meta', $instance['style_sup'], true );
							},
							'wrapper'       => 'meta',
						),
						'show_meta2'   => array(
							'setting'    => 'style_sup',
							'label'      => esc_html__( 'Show Meta Info - 2', 'display-post-types' ),
							'value'      => 'category',
							'is_checked' => function() use ( $instance ) {
								return in_array( 'category', $instance['style_sup'], true ) ? 'checked' : '';
							},
							'type'       => 'spcheckbox',
							'wrapper'    => 'category',
						),
						'meta2'        => array(
							'setting'       => 'meta2',
							'label'         => esc_html__( 'Meta Info to be displayed', 'display-post-types' ),
							'type'          => 'text',
							'hide_callback' => function() use ( $widget, $instance ) {
								return ! in_array( 'category', $instance['style_sup'], true );
							},
							'wrapper'       => 'category',
						),
					),
				),
			),
			$widget,
			$instance
		);
	}

	public function get_wrappers( $instance ) {
		$widget = $this;
		return apply_filters( 'dpt_widget_wrappers',
			array(
				'header'    => array(
					'id'       => 'header',
					'type'     => 'toggle',
					'label'    => esc_html__( 'Title & Toolbar', 'display-post-types' ),
					'class'    => 'dpt-header-section',
					'children' => false,
					'hide_callback' => function() use ( $widget, $instance ) {
						return ! $instance['title'] && ( ! defined( 'DPT_PRO_VERSION' ) || ! $widget->is_style_support( $instance['styles'], 'hactions' ) );
					},
				),
				'container' => array(
					'id'       => 'container',
					'type'     => 'toggle',
					'label'    => esc_html__( 'Container', 'display-post-types' ),
					'class'    => 'dpt-container-section',
					'children' => false,
				),
				'stylewrap' => array(
					'id'           => 'stylewrap',
					'type'         => 'normal',
					'label'        => esc_html__( 'Style Wrapper Container', 'display-post-types' ),
					'class'        => 'dpt-style-wrapper',
					'children'     => array( 'thumbnail', 'meta', 'title', 'excerpt', 'category' ),
				),
				'thumbnail' => array(
					'id'            => 'thumbnail',
					'type'          => 'toggle',
					'label'         => esc_html__( 'Thumbnail', 'display-post-types' ),
					'class'         => 'dpt-thumbnail-section',
					'children'      => false,
					'hide_callback' => function() use ( $widget, $instance ) {
						return ! $widget->is_style_support( $instance['styles'], 'thumbnail' );
					},
				),
				'title'     => array(
					'id'            => 'title',
					'type'          => 'toggle',
					'label'         => esc_html__( 'Title', 'display-post-types' ),
					'class'         => 'dpt-title-section',
					'children'      => false,
					'hide_callback' => function() use ( $widget, $instance ) {
						return ! $widget->is_style_support( $instance['styles'], 'title' );
					},
				),
				'excerpt'   => array(
					'id'            => 'excerpt',
					'type'          => 'toggle',
					'label'         => esc_html__( 'Excerpt', 'display-post-types' ),
					'class'         => 'dpt-excerpt-section',
					'children'      => false,
					'hide_callback' => function() use ( $widget, $instance ) {
						return ! $widget->is_style_support( $instance['styles'], 'excerpt' );
					},
				),
				'meta'      => array(
					'id'            => 'meta',
					'type'          => 'toggle',
					'label'         => esc_html__( 'Meta Info 1', 'display-post-types' ),
					'class'         => 'dpt-meta-section',
					'children'      => false,
					'hide_callback' => function() use ( $widget, $instance ) {
						return ! $widget->is_style_support( $instance['styles'], 'meta' );
					},
				),
				'category'  => array(
					'id'            => 'category',
					'type'          => 'toggle',
					'label'         => esc_html__( 'Meta Info 2', 'display-post-types' ),
					'class'         => 'dpt-category-section',
					'children'      => false,
					'hide_callback' => function() use ( $widget, $instance ) {
						return ! $widget->is_style_support( $instance['styles'], 'category' );
					},
				),
				'filter_postids'  => array(
					'id'            => 'filter_postids',
					'type'          => 'toggle',
					'label'         => esc_html__( 'Filter By Post IDs', 'display-post-types' ),
					'class'         => 'dpt-filter-postids-section',
					'children'      => false,
					'hide_callback' => function() use ( $widget, $instance ) {
						return 'page' === $instance['post_type'];
					},
				),
				'filter_taxonomy'  => array(
					'id'            => 'filter_taxonomy',
					'type'          => 'toggle',
					'label'         => esc_html__( 'Filter By Taxonomy', 'display-post-types' ),
					'class'         => 'dpt-filter-taxonomy-section',
					'children'      => false,
					'hide_callback' => function() use ( $widget, $instance ) {
						return 'page' === $instance['post_type'];
					},
				),
				'filter_custom_fields'  => array(
					'id'       => 'filter_custom_fields',
					'type'     => 'toggle',
					'label'    => esc_html__( 'Filter By Custom Fields', 'display-post-types' ),
					'class'    => 'dpt-filter-cf-section',
					'children' => false,
				),
				'sort_items'  => array(
					'id'            => 'sort_items',
					'type'          => 'toggle',
					'label'         => esc_html__( 'Sort Items', 'display-post-types' ),
					'class'         => 'dpt-sort_posts-section',
					'children'      => false,
					'hide_callback' => function() use ( $widget, $instance ) {
						return 'page' === $instance['post_type'];
					},
				),
			),
			$widget,
			$instance
		);
	}

	/**
	 * Outputs the settings form for the widget.
	 *
	 * @since 1.0.0
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		// Merge with defaults.
        $instance  = isset( $this->shortcode_settings[ $instance ] ) ? $this->shortcode_settings[ $instance ] : array();
		$instance  = Security::escape_all( $instance );
        $instance = wp_parse_args( (array) $instance, $this->defaults );
		$options  = $this->get_widget_options( $instance );

		$default_markup = '';
		$options_markup = '';
		foreach ( $options as $option => $args ) {
			$items    = $args['items'];
			$showop   = isset( $args['op_callback'] ) && is_callable( $args['op_callback'] ) ? call_user_func( $args['op_callback'] ) : true;
			$markup   = array();
			$wrappers = array();
			foreach ( $items as $item => $attr ) {
				$dcall = isset( $attr['display_callback'] ) && is_callable( $attr['display_callback'] ) ? call_user_func( $attr['display_callback'] ) : true;
				if ( ! $dcall ) {
					continue;
				}

				$set   = $attr['setting'];
				$id    = esc_attr( $this->get_field_id( $set ) );
				$name  = esc_attr( $this->get_field_name( $set ) );
				$type  = $attr['type'];
				$label = isset( $attr['label'] ) ? $attr['label'] : '';
				$desc  = isset( $attr['desc'] ) ? $attr['desc'] : '';
				$iatt  = isset( $attr['input_attrs'] ) ? $attr['input_attrs'] : array();
				$hcal  = isset( $attr['hide_callback'] ) && is_callable( $attr['hide_callback'] ) ? call_user_func( $attr['hide_callback'] ) : false;
				$wrap  = isset( $attr['wrapper'] ) ? $attr['wrapper'] : false;

				$inputattr = '';
				foreach ( $iatt as $att => $val ) {
					$inputattr .= esc_html( $att ) . '="' . esc_attr( $val ) . '" ';
				}

				switch ( $type ) {
					case 'select':
						$optmar  = $this->label( $set, $label, false );
						$optmar .= $this->select( $set, $attr['choices'], $instance[ $set ], array(), false, $inputattr );
						break;
					case 'checkbox':
						$optmar  = sprintf( '<input class="dpt-getval" name="%s" id="%s" type="checkbox" value="yes" %s %s />', $name, $id, checked( $instance[ $set ], 'yes', false ), $instance[ $set ] );
						$optmar .= $this->label( $set, $label, false );
						$optmar .= $desc ? sprintf( '<div class="dpt-desc">%s</div>', $desc ) : '';
						break;
					case 'spcheckbox':
						$checked = isset( $attr['is_checked'] ) && is_callable( $attr['is_checked'] ) ? call_user_func( $attr['is_checked'] ) : '';
						$spid    = esc_attr( $this->get_field_id( $item ) );
						$optmar  = sprintf( '<input id="%1$s" type="checkbox" value="%2$s" %3$s data-id="%4$s" class="spcheckbox" />', $spid, esc_attr( $attr['value'] ), $checked, $id );
						$optmar .= $this->label( $item, $label, false );
						break;
					case 'hiddenarray':
						$optmar = sprintf( '<input class="dpt-getval" name="%1$s" id="%2$s" type="hidden" value="%3$s" />', $name, $id, is_array( $instance[ $set ] ) ? implode( ',', $instance[ $set ] ) : esc_attr( $instance[ $set ] ) );
						break;
					case 'text':
						$optmar  = $this->label( $set, $label, false );
						$optmar .= sprintf( '<input class="widefat dpt-getval dpt-%1$s" name="%2$s" id="%3$s" type="text" value="%4$s" %5$s />', str_replace( '_', '-', $set ), $name, $id, is_array( $instance[ $set ] ) ? implode( ',', $instance[ $set ] ) : esc_attr( $instance[ $set ] ), $inputattr );
						$optmar .= sprintf( '<div class="dpt-desc">%s</div>', $desc );
						break;
					case 'url':
						$optmar  = $this->label( $set, $label, false );
						$optmar .= sprintf( '<input class="widefat dpt-getval" name="%1$s" id="%2$s" type="url" value="%3$s" />', $name, $id, esc_attr( $instance[ $set ] ) );
						$optmar .= sprintf( '<div class="dpt-desc">%s</div>', $desc );
						break;
					case 'number':
						$optmar  = $this->label( $set, $label, false );

						$range_attrs = $inputattr;
						if ( false === strpos( $range_attrs, 'min=' ) ) {
							$range_attrs .= ' min="0"';
						}
						// Sensible default max if not defined
						if ( false === strpos( $range_attrs, 'max=' ) ) {
							$range_attrs .= ' max="200"';
						}
						if ( false === strpos( $range_attrs, 'step=' ) ) {
							$range_attrs .= ' step="1"';
						}
						$range_id = esc_attr( $id . '_range' );

						$optmar .= '<div class="dpt-slider-group">';
						$optmar .= sprintf( '<input class="dpt-range-slider" id="%1$s" type="range" value="%2$s" aria-label="%3$s" %4$s />', $range_id, esc_attr( $instance[ $set ] ), esc_attr( wp_strip_all_tags( $label ) ), $range_attrs );
						$optmar .= sprintf( '<input class="dpt-getval dpt-number-input" name="%1$s" id="%2$s" type="number" value="%3$s" aria-controls="%4$s" %5$s />', $name, $id, esc_attr( $instance[ $set ] ), $range_id, $inputattr );
						$optmar .= '</div>';

						$optmar .= sprintf( '<div class="dpt-desc">%s</div>', $desc );
						break;
					case 'textarea':
						$optmar  = $this->label( $set, $label, false );
						$optmar .= sprintf( '<textarea class="widefat dpt-getval" name="%1$s" id="%2$s" %3$s >%4$s</textarea>', $name, $id, $inputattr, esc_attr( $instance[ $set ] ) );
						break;
					case 'color':
						$optmar  = $this->label( $set, $label, false );
						$optmar .= sprintf( '<input class="dpt-color-picker dpt-getval" data-alpha-enabled="true" name="%1$s" id="%2$s" type="text" value="%3$s" />', $name, $id, esc_attr( sanitize_text_field( $instance[ $set ] ) ) );
						break;
					case 'taxonomy':
						$optmar = $this->taxonomies_select( $instance['post_type'], $instance['taxonomy'] );
						break;
					case 'terms':
						$optmar = $this->terms_checklist( $instance['taxonomy'], $instance['terms'] );
						break;
					case 'advanced_taxonomy':
						$optmar = $this->advanced_taxonomy_control( $instance );
						break;
					case 'pages':
						$optmar = $this->pages_checklist( $instance['pages'] );
						break;
					case 'cfields':
						$optmar = $this->custom_fields_select( $set, $instance['post_type'], $instance[ $set ] );
						break;
					case 'cfieldsoperators':
						$optmar = $this->custom_fields_operator_select( $instance['filter_custom_field_type'], $instance['filter_custom_field_operator'] );
						break;
					default:
						$optmar = apply_filters( 'dpt_custom_option_field', false, $item, $attr, $this, $instance );
						break;
				}
				$style = $hcal ? 'style="display: none;"' : '';
				$op    = $optmar ? sprintf( '<div class="%1$s dpt-widget-option" %2$s>%3$s</div>', $item, $style, $optmar ) : '';
				if ( $op ) {
					if ( ! $wrap ) {
						$markup['general'][] = $op;
					} else {
						$markup[ $wrap ][] = $op;
					}
				}
			}
			$markup = $this->get_options_markup( $markup, $instance );
			if ( 'default' === $option ) {
				$default_markup = $markup;
			} else {
				$opstyle         = $showop ? '' : 'style="display: none;"';
				$section_id      = esc_attr( $this->get_field_id( 'section_' . $option ) );
				$section         = sprintf( '<a class="dpt-settings-toggle dpt-%1$s-toggle" role="button" tabindex="0" aria-expanded="false" aria-controls="%4$s" %2$s>%3$s</a>', $option, $opstyle, $args['title'], $section_id );
				$section        .= sprintf( '<div class="dpt-settings-content dpt-%1$s-content" id="%3$s">%2$s</div>', $option, $markup, $section_id );
				$options_markup .= $section;
			}
		}
		printf( '%1$s<div class="dpt-options-wrapper">%2$s</div>', $default_markup, $options_markup ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Combine and properly format widget options markup.
	 *
	 * @param array $markup   Markup Array.
	 * @param array $instance All settings for this widget instance.
	 */
	public function get_options_markup( $markup, $instance ) {
		$content = isset( $markup['general'] ) && $markup['general'] ? implode( '', $markup['general'] ) : '';

		// Return content if there no wrapped content.
		if ( count( $markup ) === 1 && isset( $markup['general'] ) ) {
		    return $content;
		}

		$wrappers = $this->get_wrappers( $instance );
		$all_children = array();
		foreach ( $wrappers as $key => $wrap ) {
			if ( isset( $wrap['children'] ) && is_array( $wrap['children'] ) ) {
				if ( isset( $wrap['sortchildren'] ) && $wrap['sortchildren'] && isset( $instance[ $wrap['sortchildren'] ] ) && is_array( $instance[ $wrap['sortchildren'] ] ) ) {
					$common = array_intersect( $instance[ $wrap['sortchildren'] ], $wrap['children'] );
					$wrappers[$key]['children'] = array_unique( array_merge( $common, $wrap['children'] ) );
				}
				$all_children = array_merge( $all_children, $wrap['children'] );
			}
		}

		foreach ( $wrappers as $key => $wrap ) {
			if ( ! in_array( $key, $all_children ) ) {
				$content .= $this->get_wrapped_markup( $key, $markup, $wrappers );
			}
		}

		return $content;
	}

	/**
	 * Get properly wrapped form markup.
	 *
	 * @since 2.4.0
	 *
	 * @param string $key      Wrapper ID.
	 * @param array  $markup   Array of markup with their wrap ID.
	 * @param array  $wrappers Array of all wrappers.
	 */
	public function get_wrapped_markup( $key, $markup, $wrappers ) {
		$content = isset( $markup[ $key ] ) && $markup[ $key ] ? implode( '', $markup[ $key ] ) : '';
		$wrap = isset( $wrappers[ $key ] ) ? $wrappers[ $key ] : false;
		if ( ! $wrap ) {
			return '';
		}
		$label = $wrap['label'];
		$type  = $wrap['type'];
		$class = $wrap['class'];
		$hcal  = isset( $wrap['hide_callback'] ) && is_callable( $wrap['hide_callback'] ) ? call_user_func( $wrap['hide_callback'] ) : false;
		$style = $hcal ? 'style="display: none;"' : '';
		$header = '';
		
		if ( $wrap && isset( $wrap['children'] ) && is_array( $wrap['children'] ) ) {
			foreach ( $wrap['children'] as $child ) {
				$child_content = $this->get_wrapped_markup( $child, $markup, $wrappers );
				if ( ! $child_content ) {
					continue;
				}
				if ('tabs' === $type) {
					$child_wrap = isset( $wrappers[ $child ] ) ? $wrappers[ $child ] : false;
					if ($child_wrap) {
						if ( '' === $header ) {
							$tabclass = 'dpt-tab-index-item active-tab';
						} else {
							$tabclass = 'dpt-tab-index-item';
						}
						$header_content = isset( $child_wrap['tab_header_content'] ) ? $child_wrap['tab_header_content'] : '';
						$header .= $header_content ? sprintf( '<div class="%1$s" data-id="%2$s">%3$s</div>', $tabclass, 'tab-' . $child, $header_content ) : '';
						$content .= sprintf( '<div class="dpt-tab-content-item" data-attr="%1$s">%2$s</div>', 'tab-' . $child, $child_content );
					}
				} else {
					$content .= $child_content;
				}
			}
		}

		if ( ! $content ) {
			return '';
		}

		if ( 'toggle' === $type ) {
			$content_id = esc_attr( $this->get_field_id( 'toggle_' . $key ) );
			$content = sprintf(
				'<div class="dpt-wrapper-container dpt-toggle-container %1$s" %2$s><a class="dpt-settings-toggle" role="button" tabindex="0" aria-expanded="false" aria-controls="%5$s">%3$s</a><div class="dpt-settings-content" id="%5$s">%4$s</div></div>',
				$class,
				$style,
				$label,
				$content,
				$content_id
			);
		} elseif ( 'normal' === $type ) {
			$content = sprintf(
				'<div class="dpt-wrapper-continer dpt-normal-container %1$s" %2$s>%3$s</div>',
				$class,
				$style,
				$content
			);
		} elseif ( 'tabs' === $type ) {
			$content = sprintf(
				'<div class="dpt-wrapper-container dpt-tabs-container dpt-tabs %1$s" %2$s><div class="dpt-tab-index">%3$s</div><div class="dpt-tab-content">%4$s</div></div>',
				$class,
				$style,
				$header,
				$content
			);
		}
		return $content;
	}

    /**
     * Get field ID.
     *
     * @since 2.6.0
     */
    public function get_field_id( $id ) {
        return 'dpt_field_id_' . $id;
    }

    /**
     * Get field ID.
     *
     * @since 2.6.0
     */
    public function get_field_name( $id ) {
        return 'dpt_field_name_' . $id;
    }

	/**
	 * Prints a checkbox list of all pages.
	 *
	 * @param array $selected_pages Checked pages.
	 */
	public function pages_checklist( $selected_pages ) {
		$page_for_posts = (int) get_option( 'page_for_posts' );

		// Get list of all pages.
		$pages = get_pages();
		if ( $page_for_posts ) {
			$pages = array_filter( $pages, function( $page ) use ( $page_for_posts ) {
				return (int) $page->ID !== $page_for_posts;
			} );
		}

		$pages = wp_list_pluck( $pages, 'post_title', 'ID' );

		$markup  = $this->label( 'pages', esc_html__( 'Select Pages', 'display-post-types' ), false );
		$markup .= $this->mu_checkbox( 'pages', $pages, $selected_pages, array(), false );
		return $markup;
	}

	/**
	 * Prints a checkbox list of all terms for a taxonomy.
	 *
	 * @param str   $taxonomy       Selected Taxonomy.
	 * @param array $selected_terms Selected Terms.
	 */
	public function terms_checklist( $taxonomy, $selected_terms = array() ) {

		// Get list of all registered terms.
		$terms = get_terms(
			array(
				'hide_empty' => true,
			)
		);

		if ( is_wp_error( $terms ) ) {
			$terms = array();
		}

		// Terms Checkbox markup.
		$markup  = $this->label( 'terms', esc_html__( 'Select Terms', 'display-post-types' ), false );
		$markup .= $this->terms_mu_checkbox( $terms, $taxonomy, $selected_terms );
		return $markup;
	}

	/**
	 * Markup for term checkboxes.
	 *
	 * Terms are intentionally rendered from the term objects rather than an
	 * associative slug-indexed array. Different taxonomies can contain terms
	 * with the same slug, and using the slug as the array key drops duplicates.
	 *
	 * @param array  $terms          Term objects.
	 * @param string $taxonomy       Selected taxonomy.
	 * @param array  $selected_terms Selected term slugs.
	 * @return string
	 */
	public function terms_mu_checkbox( $terms, $taxonomy, $selected_terms = array() ) {
		$selected_terms = is_array( $selected_terms ) ? $selected_terms : explode( ',', $selected_terms );
		$selected_terms = array_map( 'strval', $selected_terms );
		$hidden_input   = sprintf(
			'<input class="dpt-getval" name="%1$s" id="%2$s" type="hidden" value="%3$s" />',
			esc_attr( $this->get_field_name( 'terms' ) ),
			esc_attr( $this->get_field_id( 'terms' ) ),
			implode( ',', array_map( 'esc_attr', $selected_terms ) )
		);
		$markup         = '<div class="terms-checklist dpt-mu-checklist">' . $hidden_input . '<ul id="' . esc_attr( $this->get_field_id( 'terms' ) ) . '">';

		foreach ( $terms as $term ) {
			$class      = $term->taxonomy;
			$is_current = ! $taxonomy || $taxonomy === $term->taxonomy;
			if ( ! $is_current ) {
				$class .= ' dpt-hidden';
			}
			$is_checked = $is_current && in_array( strval( $term->slug ), $selected_terms, true );
			$markup    .= sprintf(
				"\n<li class=\"%1\$s\"><label class=\"selectit\"><input value=\"%2\$s\" type=\"checkbox\"%3\$s /> %4\$s</label></li>\n",
				esc_attr( $class ),
				esc_attr( $term->slug ),
				checked( $is_checked, true, false ),
				esc_html( $term->name )
			);
		}

		$markup .= "</ul></div>\n";
		return $markup;
	}

	/**
	 * Render advanced taxonomy controls.
	 *
	 * @since 3.4.0
	 *
	 * @param array $instance Current settings.
	 * @return string
	 */
	public function advanced_taxonomy_control( $instance ) {
		$post_type = isset( $instance['post_type'] ) ? $instance['post_type'] : '';
		$relation  = isset( $instance['query_tax_relation'] ) ? \Display_Post_Types\Frontend\Inc\Query_Schema::normalize_relation( $instance['query_tax_relation'] ) : 'AND';
		$clauses   = isset( $instance['query_tax_clauses'] ) ? \Display_Post_Types\Frontend\Inc\Query_Schema::normalize_tax_clauses( $instance['query_tax_clauses'] ) : array();
		$enabled   = ! empty( $clauses );
		$rows      = $enabled ? $clauses : array( array( 'taxonomy' => '', 'terms' => array(), 'operator' => 'IN' ) );
		$rules     = '';

		foreach ( $rows as $index => $clause ) {
			$rules .= $this->advanced_taxonomy_rule( $post_type, $clause, $index );
		}

		$markup  = sprintf(
			'<input class="dpt-getval dpt-query-tax-clauses-value" name="%1$s" id="%2$s" type="hidden" value="%3$s" />',
			esc_attr( $this->get_field_name( 'query_tax_clauses' ) ),
			esc_attr( $this->get_field_id( 'query_tax_clauses' ) ),
			esc_attr( $enabled ? wp_json_encode( $clauses ) : '' )
		);
		$markup .= sprintf(
			'<label class="dpt-query-tax-toggle-label"><input type="checkbox" class="dpt-query-tax-toggle"%1$s /> %2$s</label>',
			checked( $enabled, true, false ),
			esc_html__( 'Use multiple taxonomy rules', 'display-post-types' )
		);
		$markup .= sprintf( '<div class="dpt-query-tax-builder"%s>', $enabled ? '' : ' style="display: none;"' );
		$markup .= '<div class="dpt-query-tax-relation-row">';
		$markup .= $this->label( 'query_tax_relation', esc_html__( 'Rules Relationship', 'display-post-types' ), false );
		$markup .= $this->select(
			'query_tax_relation',
			array(
				'AND' => esc_html__( 'Match all rules', 'display-post-types' ),
				'OR'  => esc_html__( 'Match any rule', 'display-post-types' ),
			),
			$relation,
			array(),
			false
		);
		$markup .= '</div>';
		$markup .= sprintf( '<div class="dpt-query-tax-rules">%s</div>', $rules );
		$markup .= sprintf( '<button type="button" class="button dpt-query-tax-add">%s</button>', esc_html__( 'Add Taxonomy Rule', 'display-post-types' ) );
		$markup .= '</div>';

		return sprintf( '<div class="dpt-query-tax-control">%s</div>', $markup );
	}

	/**
	 * Render one advanced taxonomy rule.
	 *
	 * @since 3.4.0
	 *
	 * @param string $post_type Selected post type.
	 * @param array  $clause    Taxonomy clause.
	 * @param int    $index     Rule index.
	 * @return string
	 */
	public function advanced_taxonomy_rule( $post_type, $clause, $index = 0 ) {
		$taxonomy = isset( $clause['taxonomy'] ) ? sanitize_key( $clause['taxonomy'] ) : '';
		$operator = isset( $clause['operator'] ) ? sanitize_text_field( $clause['operator'] ) : 'IN';
		$terms    = isset( $clause['terms'] ) && is_array( $clause['terms'] ) ? array_map( 'strval', $clause['terms'] ) : array();

		$markup  = sprintf( '<div class="dpt-query-tax-rule" data-index="%s">', absint( $index ) );
		$markup .= '<div class="dpt-query-tax-rule-head">';
		$markup .= sprintf( '<strong>%s</strong>', esc_html__( 'Taxonomy Rule', 'display-post-types' ) );
		$markup .= sprintf( '<button type="button" class="button-link dpt-query-tax-remove">%s</button>', esc_html__( 'Remove', 'display-post-types' ) );
		$markup .= '</div>';
		$markup .= sprintf( '<label>%s</label>', esc_html__( 'Taxonomy', 'display-post-types' ) );
		$markup .= $this->advanced_taxonomy_select( $post_type, $taxonomy );
		$markup .= sprintf( '<label>%s</label>', esc_html__( 'Rule Type', 'display-post-types' ) );
		$markup .= $this->advanced_taxonomy_operator_select( $operator );
		$markup .= sprintf( '<div class="dpt-query-tax-rule-terms-row"%s>', $taxonomy ? '' : ' style="display: none;"' );
		$markup .= sprintf( '<label>%s</label>', esc_html__( 'Terms', 'display-post-types' ) );
		$markup .= $this->advanced_taxonomy_terms( $taxonomy, $terms );
		$markup .= '</div>';
		$markup .= '</div>';

		return $markup;
	}

	/**
	 * Render taxonomy select for an advanced rule.
	 *
	 * @since 3.4.0
	 *
	 * @param string $post_type Selected post type.
	 * @param string $selected  Selected taxonomy.
	 * @return string
	 */
	private function advanced_taxonomy_select( $post_type, $selected ) {
		$options    = Get_Fn::taxonomies();
		$taxonomies = get_taxonomies( array(), 'objects' );
		$classes    = wp_list_pluck( $taxonomies, 'object_type', 'name' );

		if ( $post_type && 'page' !== $post_type ) {
			foreach ( $classes as $name => $type ) {
				$type = (array) $type;
				if ( ! in_array( $post_type, $type, true ) ) {
					$type[] = 'dpt-hidden';
				}
				$classes[ $name ] = $type;
			}
		}
		$classes[''] = 'always-visible';

		return $this->advanced_select_markup( 'dpt-query-tax-rule-taxonomy', $options, $selected, $classes );
	}

	/**
	 * Render rule operator select.
	 *
	 * @since 3.4.0
	 *
	 * @param string $selected Selected operator.
	 * @return string
	 */
	private function advanced_taxonomy_operator_select( $selected ) {
		return $this->advanced_select_markup(
			'dpt-query-tax-rule-operator',
			array(
				'IN'     => esc_html__( 'Match any selected term', 'display-post-types' ),
				'AND'    => esc_html__( 'Match all selected terms', 'display-post-types' ),
				'NOT IN' => esc_html__( 'Exclude selected terms', 'display-post-types' ),
			),
			$selected
		);
	}

	/**
	 * Render term checkboxes for an advanced rule.
	 *
	 * @since 3.4.0
	 *
	 * @param string $taxonomy Selected taxonomy.
	 * @param array  $selected Selected term slugs.
	 * @return string
	 */
	private function advanced_taxonomy_terms( $taxonomy, $selected ) {
		$terms = get_terms( array( 'hide_empty' => true ) );
		if ( is_wp_error( $terms ) ) {
			$terms = array();
		}

		$markup = '<div class="terms-checklist dpt-mu-checklist dpt-query-tax-rule-terms"><ul>';
		foreach ( $terms as $term ) {
			$is_current = $taxonomy && $taxonomy === $term->taxonomy;
			$classes    = array( $term->taxonomy );
			if ( ! $is_current ) {
				$classes[] = 'dpt-hidden';
			}
			$markup .= sprintf(
				'<li class="%1$s"%2$s><label class="selectit"><input value="%3$s" type="checkbox"%4$s /> %5$s</label></li>',
				esc_attr( join( ' ', $classes ) ),
				$is_current ? '' : ' style="display: none;"',
				esc_attr( $term->slug ),
				checked( $is_current && in_array( strval( $term->slug ), $selected, true ), true, false ),
				esc_html( $term->name )
			);
		}
		$markup .= '</ul></div>';

		return $markup;
	}

	/**
	 * Render a UI-only select for advanced taxonomy rules.
	 *
	 * @since 3.4.0
	 *
	 * @param string $class    Select class.
	 * @param array  $options  Options.
	 * @param string $selected Selected option.
	 * @param array  $classes  Option classes.
	 * @return string
	 */
	private function advanced_select_markup( $class, $options, $selected, $classes = array() ) {
		$output = '';
		foreach ( $options as $value => $label ) {
			$option_class = '';
			if ( isset( $classes[ $value ] ) ) {
				$option_class = ' class="' . join( ' ', array_map( 'esc_attr', (array) $classes[ $value ] ) ) . '"';
			}
			$output .= sprintf( '<option value="%1$s"%2$s %3$s>%4$s</option>', esc_attr( $value ), $option_class, selected( $value, $selected, false ), esc_html( $label ) );
		}

		return sprintf( '<select class="%1$s widefat">%2$s</select>', esc_attr( $class ), $output );
	}

	/**
	 * Prints select list of all taxonomies for a post type.
	 *
	 * @param str   $post_type Selected post type.
	 * @param array $selected  Selected taxonomy in widget form.
	 */
	public function taxonomies_select( $post_type, $selected = array() ) {

		$options = Get_Fn::taxonomies();

		// Get HTML classes for select options.
		$taxonomies = get_taxonomies( array(), 'objects' );
		$classes    = wp_list_pluck( $taxonomies, 'object_type', 'name' );
		if ( $post_type && 'page' !== $post_type ) {
			foreach ( $classes as $name => $type ) {
				$type = (array) $type;
				if ( ! in_array( $post_type, $type, true ) ) {
					$type[]           = 'dpt-hidden';
					$classes[ $name ] = $type;
				}
			}
		}
		$classes[''] = 'always-visible';

		// Taxonomy Select markup.
		$markup  = $this->label( 'taxonomy', esc_html__( 'Get items by Taxonomy', 'display-post-types' ), false );
		$markup .= $this->select( 'taxonomy', $options, $selected, $classes, false );
		return $markup;
	}

	/**
	 * Prints select list of all custom fields.
	 *
	 * @param str $for For Setting
	 * @param str $post_type Selected post type.
	 * @param str $custom_field Selected Custom Field.
	 */
	public function custom_fields_select( $for, $post_type, $custom_field ) {
		$custom_fields = Get_Fn::custom_fields();
		$options       = array_merge( array( '' => esc_html__( 'Select a Custom Field', 'display-post-types' ) ), array_combine( array_keys( $custom_fields ), array_keys( $custom_fields ) ) );
		if ( $post_type && 'page' !== $post_type ) {
			foreach ( $custom_fields as $name => $type ) {
				$type = (array) $type;
				if ( ! in_array( $post_type, $type, true ) ) {
					$type[]           = 'dpt-hidden';
					$custom_fields[ $name ] = $type;
				}
			}
		}
		$custom_fields[''] = 'always-visible';

		// Custom Field Select markup.
		$markup  = $this->label( $for, esc_html__( 'Select a Custom Field', 'display-post-types' ), false );
		$markup .= $this->select( $for, $options, $custom_field, $custom_fields, false );
		return $markup;
	}

	/**
	 * Prints select list of all custom fields comparison operators.
	 *
	 * @param str   $field_type Custom Field Data Type.
	 * @param str   $selected   Custom Field Operator.
	 */
	public function custom_fields_operator_select( $field_type, $selected ) {
		$operators  = Get_Fn::custom_field_operators();
		$field_type = '' === $field_type ? 'string' : $field_type;
		$options    = array();
		$classes    = array();
		foreach ( $operators as $operator => $args ) {
			$options[ $operator ] = $args['0'];
			$type = $args['1'];
			if ( ! in_array( $field_type, $type, true ) && ! in_array( 'always-visible', $type, true ) ) {
				$type[] = 'dpt-hidden';
			}
			$classes[ $operator ] = $type;
		}

		// Custom Field Select markup.
		$markup  = $this->label( 'filter_custom_field_operator', esc_html__( 'Operator for Comparison', 'display-post-types' ), false );
		$markup .= $this->select( 'filter_custom_field_operator', $options, $selected, $classes, false );
		return $markup;
	}

	/**
	 * Markup for 'label' for widget input options.
	 *
	 * @param str  $for  Label for which ID.
	 * @param str  $text Label text.
	 * @param bool $echo Display or Return.
	 * @return void|string
	 */
	public function label( $for, $text, $echo = true ) {
		$label = sprintf( '<label for="%s">%s</label>', esc_attr( $this->get_field_id( $for ) ), esc_html( $text ) );
		if ( $echo ) {
			echo $label; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $label;
		}
	}

	/**
	 * Markup for Select dropdown lists for widget options.
	 *
	 * @param string $for      Select for which ID.
	 * @param array  $options  Select options as 'value => label' pair.
	 * @param string $selected selected option.
	 * @param array  $classes  Options HTML classes.
	 * @param bool   $echo     Display or return.
	 * @param string $atts     HTML attributes.
	 * @return void|string
	 */
	public function select( $for, $options, $selected, $classes = array(), $echo = true, $atts = '' ) {
		$select      = '';
		$final_class = '';
		foreach ( $options as $value => $label ) {
			if ( isset( $classes[ $value ] ) ) {
				$option_classes = (array) $classes[ $value ];
				$option_classes = array_map( 'esc_attr', $option_classes );
				$final_class    = 'class="' . join( ' ', $option_classes ) . '"';
			}
			$select .= sprintf( '<option value="%1$s" %2$s %3$s>%4$s</option>', esc_attr( $value ), $final_class, selected( $value, $selected, false ), esc_html( $label ) );
		}

		$select = sprintf(
			'<select id="%1$s" name="%2$s" class="dpt-%3$s widefat dpt-getval" %5$s>%4$s</select>',
			esc_attr( $this->get_field_id( $for ) ),
			esc_attr( $this->get_field_name( $for ) ),
			esc_attr( str_replace( '_', '-', $for ) ),
			$select,
			$atts
		);

		if ( $echo ) {
			echo $select; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $select;
		}
	}

	/**
	 * Markup for multiple checkbox for widget options.
	 *
	 * @param str   $for      Select for which ID.
	 * @param array $options  Select options as 'value => label' pair.
	 * @param str   $selected selected option.
	 * @param array $classes  Checkbox input HTML classes.
	 * @param bool  $echo     Display or return.
	 * @return void|string
	 */
	public function mu_checkbox( $for, $options, $selected, $classes = array(), $echo = true ) {

		$final_class  = '';
		$selected     = is_array( $selected ) ? $selected : explode( ',', $selected );
		$selected     = array_map( 'strval', $selected );
		$hidden_input = sprintf( '<input class="dpt-getval" name="%1$s" id="%2$s" type="hidden" value="%3$s" />', esc_attr( $this->get_field_name( $for ) ), esc_attr( $this->get_field_id( $for ) ), is_array( $selected ) ? implode( ',', array_map( 'esc_attr', $selected ) ) : esc_attr( $selected ) );
		$mu_checkbox  = '<div class="' . esc_attr( $for ) . '-checklist dpt-mu-checklist">' . $hidden_input . '<ul id="' . esc_attr( $this->get_field_id( $for ) ) . '">';
		$rev_options  = $options;

		// Moving selected items on top of the array.
		foreach ( $options as $id => $label ) {
			if ( in_array( strval( $id ), $selected, true ) ) {
				$rev_options = array( $id => $label ) + $rev_options;
			}
		}

		foreach ( $rev_options as $id => $label ) {
			if ( isset( $classes[ $id ] ) ) {
				$final_class = ' class="' . esc_attr( $classes[ $id ] ) . '"';
			}
			$mu_checkbox .= "\n<li$final_class>" . '<label class="selectit"><input value="' . esc_attr( $id ) . '" type="checkbox"' . checked( in_array( strval( $id ), $selected, true ), true, false ) . ' /> ' . esc_html( $label ) . "</label></li>\n";
		}
		$mu_checkbox .= "</ul></div>\n";

		if ( $echo ) {
			echo $mu_checkbox; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $mu_checkbox;
		}
	}

	/**
	 * Get display styles.
	 *
	 * @return array
	 */
	public function get_display_styles() {
		if ( ! empty( $this->styles ) ) {
			return $this->styles;
		}

		$styles = Get_Fn::styles();
		foreach ( $styles as $style => $args ) {
			$this->styles[ $style ]          = $args['label'];
			$this->style_supported[ $style ] = $args['support'];
		}

		return $this->styles;
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

		if ( empty( $this->style_supported ) ) {
			$this->get_display_styles();
		}

		$sup_arr = isset( $this->style_supported[ $style ] ) ? $this->style_supported[ $style ] : array();
		return in_array( $item, $sup_arr, true );
	}

	/**
	 * Get dropdown list of all generated shortcodes.
	 *
	 * @since 2.6.0
	 */
	public function dropdown() {
		if ( empty( $this->shortcode_settings ) ) {
			return '';
		}
		$select = array( '' => esc_html__( 'Select a Shortcode to Edit', 'display-post-types' ) );
		foreach( $this->shortcode_settings as $id => $args ) {
			$select[ $id ] = $this->get_shortcode_label( $id, $args );
		}

		$markup   = '';
		$selected = '';
		$counter  = 0;
		foreach ( $select as $value => $label ) {
			$label = $label ? $label : esc_html__( 'DPT Shortcode', 'display-post-types' ) . ' ' . ++$counter;
			$markup .= sprintf( '<option value="%1$s" %2$s>%3$s</option>', esc_attr( $value ), selected( $value, $selected, false ), esc_html( $label ) );
		}

		$markup = sprintf(
			'<select class="widefat dpt-shortcode-dropdown">%1$s</select>',
			$markup
		);

		return $markup;
	}

	/**
	 * Get a stable human-readable shortcode label.
	 *
	 * @since 3.3.0
	 *
	 * @param int|string $id   Shortcode instance ID.
	 * @param array      $args Shortcode settings.
	 * @return string
	 */
	public function get_shortcode_label( $id, $args = array() ) {
		if ( isset( $args['title'] ) && '' !== trim( (string) $args['title'] ) ) {
			return $args['title'];
		}

		return sprintf(
			/* translators: %s: shortcode instance ID */
			esc_html__( 'DPT Shortcode %s', 'display-post-types' ),
			absint( $id ) + 1
		);
	}

	/**
	 * Get the next available stored shortcode instance ID.
	 *
	 * @since 3.3.0
	 *
	 * @return int
	 */
	public function next_instance_id() {
		if ( empty( $this->shortcode_settings ) || ! is_array( $this->shortcode_settings ) ) {
			return 0;
		}

		$ids = array_map( 'absint', array_keys( $this->shortcode_settings ) );
		$next = max( $ids ) + 1;
		while ( isset( $this->shortcode_settings[ $next ] ) ) {
			++$next;
		}

		return $next;
	}

	/**
	 * Get a table of saved shortcodes.
	 *
	 * @since 3.3.0
	 *
	 * @return string
	 */
	public function list_table() {
		if ( empty( $this->shortcode_settings ) || ! is_array( $this->shortcode_settings ) ) {
			return sprintf(
				'<p class="dpt-shortcode-empty">%s</p>',
				esc_html__( 'No saved shortcodes yet.', 'display-post-types' )
			);
		}

		$post_types = Get_Fn::post_types();
		$styles     = Get_Fn::styles();
		$rows       = '';
		foreach ( $this->shortcode_settings as $id => $args ) {
			$id          = absint( $id );
			$label       = $this->get_shortcode_label( $id, $args );
			$post_type   = isset( $args['post_type'], $post_types[ $args['post_type'] ] ) ? $post_types[ $args['post_type'] ] : ( isset( $args['post_type'] ) ? $args['post_type'] : '' );
			$style       = isset( $args['styles'], $styles[ $args['styles'] ]['label'] ) ? $styles[ $args['styles'] ]['label'] : ( isset( $args['styles'] ) ? $args['styles'] : '' );
			$shortcode   = sprintf( '[showdpt instance="%s"]', $id );
			$copy_label  = esc_attr__( 'Copy shortcode', 'display-post-types' );
			$edit_label  = esc_attr__( 'Edit shortcode', 'display-post-types' );
			$dupe_label  = esc_attr__( 'Duplicate shortcode', 'display-post-types' );
			$del_label   = esc_attr__( 'Delete shortcode', 'display-post-types' );
			$rows       .= sprintf(
				'<tr data-instance="%1$s"><td><strong class="dpt-shortcode-row-title">%2$s</strong></td><td class="dpt-shortcode-row-shortcode"><code class="dpt-shortcode-row-code">%3$s</code> <button type="button" class="button button-small dpt-icon-button dpt-shortcode-row-copy" data-shortcode="%6$s" aria-label="%7$s" title="%7$s"><span class="dashicons dashicons-clipboard" aria-hidden="true"></span></button></td><td>%4$s</td><td>%5$s</td><td class="dpt-shortcode-row-actions"><button type="button" class="button button-small dpt-icon-button dpt-shortcode-row-edit" data-instance="%1$s" aria-label="%8$s" title="%8$s"><span class="dashicons dashicons-edit" aria-hidden="true"></span></button> <button type="button" class="button button-small dpt-icon-button dpt-shortcode-row-duplicate" data-instance="%1$s" aria-label="%9$s" title="%9$s"><span class="dashicons dashicons-admin-page" aria-hidden="true"></span></button> <button type="button" class="button button-small dpt-icon-button dpt-shortcode-row-delete" data-instance="%1$s" aria-label="%10$s" title="%10$s"><span class="dashicons dashicons-trash" aria-hidden="true"></span></button></td></tr>',
				$id,
				esc_html( $label ),
				esc_html( $shortcode ),
				esc_html( $post_type ),
				esc_html( $style ),
				esc_attr( $shortcode ),
				$copy_label,
				$edit_label,
				$dupe_label,
				$del_label
			);
		}

		return sprintf(
			'<div class="dpt-shortcode-list-wrap"><table class="widefat striped dpt-shortcode-list"><thead><tr><th>%1$s</th><th>%2$s</th><th>%3$s</th><th>%4$s</th><th>%5$s</th></tr></thead><tbody>%6$s</tbody></table></div>',
			esc_html__( 'Name', 'display-post-types' ),
			esc_html__( 'Shortcode', 'display-post-types' ),
			esc_html__( 'Post Type', 'display-post-types' ),
			esc_html__( 'Layout', 'display-post-types' ),
			esc_html__( 'Actions', 'display-post-types' ),
			$rows
		);
	}

	/**
	 * Save shortcode settings to the database.
	 *
	 * @since 2.6.0
	 */
	public function save() {
		if ( empty( $this->shortcode_settings ) ) {
			delete_option( 'dpt_shortcode_options' );
			return;
		}
		update_option( 'dpt_shortcode_options', $this->shortcode_settings );
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
