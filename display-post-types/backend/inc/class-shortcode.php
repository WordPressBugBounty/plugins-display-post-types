<?php
/**
 * Display post types shortcode.
 *
 * @package Display_Post_Types
 * @since 1.8.0
 */

namespace Display_Post_Types\Backend\Inc;

use Display_Post_Types\Helper\Getters as Get_Fn;
use Display_Post_Types\Frontend\Inc\Display;
use Display_Post_Types\Helper\Security;
use Display_Post_Types\Backend\Admin\ShortCodeGen;

/**
 * Display post types shortcode.
 *
 * @since 1.8.0
 */
class Shortcode {

	/**
	 * Holds the instance of this class.
	 * 
	 * @since  1.0.0
	 * @access private
	 * @var    object
	 */
	private static $instance = null;

	/**
	 * Class cannot be instantiated directly.
	 *
	 * @since  1.8.0
	 */
	private function __construct() {}

	/**
	 * Register hooked functions.
	 *
	 * @since 1.0.0
	 */
	public static function init() {
		$inst = self::get_instance();
		add_shortcode( 'dpt', array( $inst, 'render' ) );
		add_shortcode( 'showdpt', array( $inst, 'renderDpt' ) );
	}

	/**
	 * DPT shortcode function.
	 *
	 * @since 1.8.0
	 *
	 * @param array $atts User defined attributes in shortcode tag.
	 * @param str   $dpt_content Shortcode text content.
	 */
	public function render( $atts, $dpt_content = null ) {

		$defaults = Get_Fn::defaults();
		$atts     = shortcode_atts( $defaults, $atts, 'dpt' );

		$terms = array();
		if ( ! empty( $atts['terms'] ) ) {
			$terms = explode( ',', $atts['terms'] );
			$terms = array_map( 'trim', $terms );
		}

		$ids = array();
		if ( ! empty( $atts['post_ids'] ) ) {
			$ids = explode( ',', $atts['post_ids'] );
			$ids = array_map( 'trim', $ids );
		}

		$pages = array();
		if ( ! empty( $atts['pages'] ) ) {
			$pages = explode( ',', $atts['pages'] );
			$pages = array_map( 'trim', $ids );
		}

		// Check if all pages IDs are valid.
		if ( 'page' === $atts['post_type'] && ! empty( $pages ) ) {
			// Get list of all pages.
			$all_pages        = get_all_page_ids();
			$all_pages        = explode( ',', $all_pages );
			$valid_pages      = array_diff( $all_pages, array( get_option( 'page_for_posts' ) ) );
			$pages            = array_intersect( $pages, $valid_pages );
			$atts['taxonomy'] = array();
		}

		/**
		 * DPT display params from shortcode.
		 *
		 * @since 1.8.0
		 */
		$display_args = apply_filters(
			'dpt_shcode_display',
			array(
				'title'         => $atts['title'],
				'post_type'     => $atts['post_type'],
				'taxonomy'      => $atts['taxonomy'],
				'terms'         => $terms,
				'relation'      => $atts['relation'],
				'post_ids'      => $ids,
				'pages'         => $pages,
				'number'        => $atts['number'],
				'orderby'       => $atts['orderby'],
				'order'         => $atts['order'],
				'styles'        => $atts['styles'],
				'style_sup'     => $atts['style_sup'],
				'image_crop'    => isset( $atts['img_croppos'] ) ? $atts['img_croppos'] : 'centercrop',
				'img_aspect'    => $atts['img_aspect'],
				'custom_aspect' => $atts['custom_aspect'],
				'thumb_fetch'   => ( 'false' === $atts['thumb_fetch'] || ! $atts['thumb_fetch'] ) ? '' : 'yes',
				'text_pos_hor'  => $atts['text_pos_hor'],
				'text_pos_ver'  => $atts['text_pos_ver'],
				'img_align'     => $atts['img_align'],
				'br_radius'     => $atts['br_radius'],
				'col_narr'      => $atts['col_narr'],
				'pl_holder'     => ( 'false' === $atts['pl_holder'] || ! $atts['pl_holder'] ) ? '' : 'yes',
				'show_pgnation' => ( 'false' === $atts['show_pgnation'] || ! $atts['show_pgnation'] ) ? '' : 'yes',
				'title_shadow'  => ( 'false' === $atts['title_shadow'] || ! $atts['title_shadow'] ) ? '' : 'yes',
				'text_align'    => $atts['text_align'],
				'v_gutter'      => $atts['v_gutter'],
				'h_gutter'      => $atts['h_gutter'],
				'e_length'      => $atts['e_length'],
				'e_teaser'      => $atts['e_teaser'],
				'classes'       => $atts['classes'],
				'offset'        => $atts['offset'],
				'autotime'      => $atts['autotime'],
				'meta1'         => $atts['meta1'],
				'meta2'         => $atts['meta2'],
			),
			$atts
		);

		// Add all the remaining shortcode attributes as is.
		$display_args = apply_filters( 'dpt_shortcode_display_args', array_merge( $atts, $display_args ) );

		ob_start();
		Display::init( $display_args );
		$content = ob_get_clean();
		return $content;
	}

	/**
	 * DPT new shortcode function.
	 *
	 * @since 1.8.0
	 *
	 * @param array $atts User defined attributes in shortcode tag.
	 * @param str   $dpt_content Shortcode text content.
	 */
	public function renderDpt( $atts, $dpt_content = null ) {
		$instance = isset( $atts['instance'] ) ? $atts['instance'] : false;
		if ( false === $instance ) {
			return '';
		}

		$shcode_gen     = ShortCodeGen::get_instance();
		$shortcode_list = $shcode_gen->shortcode_settings;
		$args = isset( $shortcode_list[ $instance ] ) ? $shortcode_list[ $instance ] : array();
		$args = Security::escape_all( $args );
		ob_start();
		Display::init( $args );
		$content = ob_get_clean();
		return $content;
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
