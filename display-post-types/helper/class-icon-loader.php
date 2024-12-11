<?php
/**
 * The front end specific functionality of the plugin.
 *
 * @package Display_Post_Types
 * @since 2.8.4
 */

namespace Display_Post_Types\Helper;

/**
 * Load required font icons.
 *
 * @since 2.8.4
 */
class Icon_Loader extends Singleton {
	/**
	 * Holds all required font icons.
	 *
	 * @since 2.8.4
	 * @access private
	 * @var    array
	 */
	private $icons = array();

	/**
	 * Adds a font icon to icons array.
	 *
	 * @since 2.8.4
	 *
	 * @param str $icon Icon to be added.
	 */
	public function add( $icon ) {
		if ( ! in_array( $icon, $this->icons, true ) ) {
			$this->icons[] = $icon;
		}
	}

	/**
	 * Adds a font icon to footer the web page.
	 *
	 * @since 2.8.4
	 */
	public function add_icons() {
		if ( empty( $this->icons ) ) {
			return;
		}

		$icons = '<svg style="position: absolute; width: 0; height: 0; overflow: hidden;" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><defs>';

		$icons_def = $this->get_font_icons_def();
		foreach ( $this->icons as $icon ) {
			if ( isset( $icons_def[ $icon ] ) ) {
				$icons .= $icons_def[ $icon ];
			}
		}

		$icons .= '</defs></svg>';
		echo $icons; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Adds a font icon to footer the web page.
	 *
	 * @since 2.8.4
	 */
	public function add_admin_icons() {
		$icons = '<svg style="position: absolute; width: 0; height: 0; overflow: hidden;" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><defs>';

		$icons_def = $this->get_font_icons_def();
		foreach ( $icons_def as $key => $icon ) {
			$icons .= $icon;
		}

		$icons .= '</defs></svg>';
		echo $icons; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * SVG icons definition.
	 *
	 * @since 2.8.4
	 */
	public function get_font_icons_def() {
		return apply_filters(
			'pp_icon_fonts_def',
			array(
				'dpt-search'   => '<symbol id="icon-dpt-search" viewBox="0 0 30 32"><path d="M20.571 14.857c0-4.411-3.589-8-8-8s-8 3.589-8 8 3.589 8 8 8 8-3.589 8-8zM29.714 29.714c0 1.25-1.036 2.286-2.286 2.286-0.607 0-1.196-0.25-1.607-0.679l-6.125-6.107c-2.089 1.446-4.589 2.214-7.125 2.214-6.946 0-12.571-5.625-12.571-12.571s5.625-12.571 12.571-12.571 12.571 5.625 12.571 12.571c0 2.536-0.768 5.036-2.214 7.125l6.125 6.125c0.411 0.411 0.661 1 0.661 1.607z"></path></symbol>',
				'dpt-previous' => '<symbol id="icon-dpt-previous" viewBox="0 0 24 32"><path d="M20.911 5.375l-9.482 9.482 9.482 9.482c0.446 0.446 0.446 1.161 0 1.607l-2.964 2.964c-0.446 0.446-1.161 0.446-1.607 0l-13.25-13.25c-0.446-0.446-0.446-1.161 0-1.607l13.25-13.25c0.446-0.446 1.161-0.446 1.607 0l2.964 2.964c0.446 0.446 0.446 1.161 0 1.607z"></path></symbol>',
				'dpt-next'     => '<symbol id="icon-dpt-next" viewBox="0 0 22 32"><path d="M19.768 15.661l-13.25 13.25c-0.446 0.446-1.161 0.446-1.607 0l-2.964-2.964c-0.446-0.446-0.446-1.161 0-1.607l9.482-9.482-9.482-9.482c-0.446-0.446-0.446-1.161 0-1.607l2.964-2.964c0.446-0.446 1.161-0.446 1.607 0l13.25 13.25c0.446 0.446 0.446 1.161 0 1.607z"></path></symbol>',
				'dpt-filter'   => '<symbol id="icon-dpt-filter" viewBox="0 0 25 32"><path d="M25.054 5.268c0.179 0.429 0.089 0.929-0.25 1.25l-8.804 8.804v13.25c0 0.464-0.286 0.875-0.696 1.054-0.143 0.054-0.304 0.089-0.446 0.089-0.304 0-0.589-0.107-0.804-0.339l-4.571-4.571c-0.214-0.214-0.339-0.5-0.339-0.804v-8.679l-8.804-8.804c-0.339-0.321-0.429-0.821-0.25-1.25 0.179-0.411 0.589-0.696 1.054-0.696h22.857c0.464 0 0.875 0.286 1.054 0.696z"></path></symbol>',
				'dpt-close'    => '<symbol id="icon-dpt-close" viewBox="0 0 25 32"><path d="M23.179 23.607c0 0.446-0.179 0.893-0.5 1.214l-2.429 2.429c-0.321 0.321-0.768 0.5-1.214 0.5s-0.893-0.179-1.214-0.5l-5.25-5.25-5.25 5.25c-0.321 0.321-0.768 0.5-1.214 0.5s-0.893-0.179-1.214-0.5l-2.429-2.429c-0.321-0.321-0.5-0.768-0.5-1.214s0.179-0.893 0.5-1.214l5.25-5.25-5.25-5.25c-0.321-0.321-0.5-0.768-0.5-1.214s0.179-0.893 0.5-1.214l2.429-2.429c0.321-0.321 0.768-0.5 1.214-0.5s0.893 0.179 1.214 0.5l5.25 5.25 5.25-5.25c0.321-0.321 0.768-0.5 1.214-0.5s0.893 0.179 1.214 0.5l2.429 2.429c0.321 0.321 0.5 0.768 0.5 1.214s-0.179 0.893-0.5 1.214l-5.25 5.25 5.25 5.25c0.321 0.321 0.5 0.768 0.5 1.214z"></path></symbol>',
				'dpt-spin'     => '<symbol id="icon-dpt-spin" viewBox="0 0 24 24"><path fill="currentColor" d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z" opacity="0.25"/><path fill="currentColor" d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z"><animateTransform attributeName="transform" dur="0.75s" repeatCount="indefinite" type="rotate" values="0 12 12;360 12 12"/></path></symbol>',
			)
		);
	}
}
