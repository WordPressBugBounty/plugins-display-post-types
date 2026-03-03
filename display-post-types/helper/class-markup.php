<?php
/**
 * The front end specific functionality of the plugin.
 *
 * @package Display_Post_Types
 * @since 1.0.0
 */

namespace Display_Post_Types\Helper;

use Display_Post_Types\Helper\Icon_Loader as Icons;

/**
 * The front-end specific functionality of the plugin.
 *
 * @since 1.0.0
 */
class Markup {

	/**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 */
	public function __construct() {}

	/**
	 * Outputs a HTML element.
	 *
	 * @since  1.0.0
	 *
	 * @param string   $class     Markup HTML class(es).
	 * @param callable $callbacks Callback functions to echo content inside the wrapper.
	 * @param string   $open      Markup wrapper opening div.
	 * @param string   $close     Markup wrapper closing div.
	 * @return void
	 */
	public static function markup( $class = '', $callbacks = array(), $open = '<div%s>', $close = '</div>' ) {
		if ( ! $class ) {
			return;
		}

		if ( is_array( $class ) ) {
			// First HTML class will become context for the element.
			$context = array_shift( $class );
			// Remaining classes will simply be added to the element.
			$classes = join( ' ', array_map( 'esc_attr', $class ) );
		} else {
			$context = $class;
			$classes = '';
		}

		$hook = str_replace( '-', '_', $context );

		/**
		 * Filter array of all supplied callable functions for this context.
		 *
		 * @since 1.0.0
		 *
		 * @param arrray $callbacks Array of callback functions (may be with args).
		 */
		$callbacks = apply_filters( "dpt_markup_{$hook}", $callbacks );

		// Return if there are no display functions.
		if ( empty( $callbacks ) ) {
			return;
		}

		printf( $open, self::get_attr( $context, array( 'class' => $classes ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		foreach ( $callbacks as $callback ) {
			$callback = (array) $callback;
			$function = array_shift( $callback );

			// Display output of a function which returns the markup.
			if ( 'echo' === $function ) {
				$function = array_shift( $callback );

				if ( is_callable( $function ) ) {
					echo call_user_func_array( $function, $callback ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
			} else {
				if ( is_callable( $function ) ) {
					call_user_func_array( $function, $callback );
				}
			}
		}

		echo $close; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Outputs an HTML element's attributes.
	 *
	 * The purposes of this is to provide a way to hook into the attributes for specific
	 * HTML elements and create new or modify existing attributes, without modifying actual
	 * markup templates.
	 *
	 * @since  1.0.0
	 *
	 * @param  str   $slug The slug/ID of the element (e.g., 'sidebar').
	 * @param  array $attr Array of attributes to pass in (overwrites filters).
	 */
	public static function attr( $slug, $attr = array() ) {
		echo self::get_attr( $slug, $attr ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Gets an HTML element's attributes.
	 *
	 * This code is inspired (but totally modified) from Stargazer WordPress Theme,
	 * Copyright 2013 – 2018 Justin Tadlock. Stargazer is distributed
	 * under the terms of the GNU GPL.
	 *
	 * @since  1.0.0
	 *
	 * @param  str   $slug The slug/ID of the element (e.g., 'sidebar').
	 * @param  array $attr Array of attributes to pass in (overwrites filters).
	 * @return string
	 */
	public static function get_attr( $slug, $attr = array() ) {
		if ( ! $slug ) {
			return '';
		}

		$out = '';

		if ( false !== $attr ) {
			if ( isset( $attr['class'] ) ) {
				$attr['class'] .= ' ' . $slug;
			} else {
				$attr['class'] = $slug;
			}
		}

		$hook = str_replace( '-', '_', $slug );

		/**
		 * Filter element's attributes.
		 *
		 * @since 1.0.0
		 */
		$attr = apply_filters( "dpt_get_attr_{$hook}", $attr, $slug );

		if ( $attr ) {
			foreach ( $attr as $name => $value ) {
				$out .= sprintf( ' %s="%s"', esc_html( $name ), esc_attr( $value ) );
			}
		}

		return $out;
	}

	/**
	 * Display font icon SVG markup.
	 */

    /**
	 * Display font icon SVG markup.
	 *
	 * @param string $icon  SVG icon name.
	 * @param string $class CSS class name.
	 */
	public static function the_icon( $icon, $class = '' ) {
		$icon = is_array( $icon ) && isset( $icon['icon'] ) ? $icon['icon'] : $icon;
		echo self::get_icon( $icon, $class ); // get_icon() already return escaped HTML markup. phpcs:ignore
	}

	/**
	 * Return font icon SVG markup.
	 *
	 * @param string $icon  SVG icon name.
	 * @param string $class CSS class name.
	 * @return string Font icon SVG markup.
	 */
	public static function get_icon( $icon, $class = '' ) {
		$icon = is_array( $icon ) && isset( $icon['icon'] ) ? $icon['icon'] : $icon;
		// Add icon to icon loader array.
		$loader = Icons::get_instance();
		$loader->add( $icon );
		return sprintf( '<svg class="icon icon-%1$s %2$s" aria-hidden="true" role="img" focusable="false"><use href="#icon-%1$s" xlink:href="#icon-%1$s"></use></svg>', esc_attr( $icon ), esc_attr( $class ) );
	}

	/**
	 * Locate admin template part for display post types.
	 *
	 * Let pro override the core template.
	 *
	 * @since  1.0.0
	 *
	 * @param string $path  Template relative path.
	 */
	public static function locate_admin_template( $path ) {
		$located   = '';
		$templates = array();

		if (
			defined( 'DPT_PRO_DIR' ) &&
			defined( 'DPT_REQUIRED' ) &&
			defined( 'DPT_PRO_VERSION' ) &&
			version_compare( DPT_PRO_VERSION, DPT_REQUIRED, '>=')
		) {
			$templates = array(
				DPT_PRO_DIR . "admin/templates/{$path}.php",
				DISPLAY_POST_TYPES_DIR . "backend/admin/templates/{$path}.php",
			);
		} else {
			$templates = array( DISPLAY_POST_TYPES_DIR . "backend/admin/templates/{$path}.php" );
		}

		foreach ( $templates as $template ) {
			if ( file_exists( $template ) ) {
				$located = $template;
				break;
			}
		}

		/**
		 * Locate a template part for DPT.
		 *
		 * @since 2.5.0
		 *
		 * @param string $located Located template file.
		 * @param string $path Template relative path.
		 */
		return apply_filters( 'dpt_locate_admin_template', $located, $path );
	}

	/**
	 * Get admin icons for DPT backend.
	 *
	 * @since  2.7.0
	 */
	public static function get_admin_icons( $id ) {
        // Check if $id string contains 'typography' in it.
		if ( false !== strpos( $id, 'typography' ) ) {
		    return '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"><path fill="currentColor" d="M11 22v-6h2v2h8v2h-8v2zm-8-2v-2h6v2zm3.425-6H8.5l1.1-3.075h4.825L15.5 14h2.075l-4.5-12h-2.15zM10.2 9.2l1.75-4.975h.1L13.8 9.2z"/></svg>';
		}

		if ( false !== strpos( $id, 'spacing' ) ) {
			return '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"><path fill="currentColor" d="m7.825 19l.9.9q.275.275.275.688t-.3.712q-.275.275-.7.275t-.7-.275l-2.6-2.6q-.3-.3-.3-.7t.3-.7l2.6-2.6q.275-.275.688-.275t.712.275q.3.3.3.713t-.3.712L7.825 17h8.35l-.9-.9Q15 15.825 15 15.413t.3-.713q.275-.275.7-.275t.7.275l2.6 2.6q.3.3.3.7t-.3.7l-2.6 2.6q-.275.275-.687.275T15.3 21.3q-.3-.3-.3-.712t.3-.713l.875-.875zm-2.537-7.287Q5 11.425 5 11V3q0-.425.288-.712T6 2t.713.288T7 3v8q0 .425-.288.713T6 12t-.712-.288m6 0Q11 11.426 11 11V3q0-.425.288-.712T12 2t.713.288T13 3v8q0 .425-.288.713T12 12t-.712-.288m6 0Q17 11.426 17 11V3q0-.425.288-.712T18 2t.713.288T19 3v8q0 .425-.288.713T18 12t-.712-.288"/></svg>';
		}

		if ( false !== strpos( $id, 'general' ) ) {
			return '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"><path fill="currentColor" d="M10.825 22q-.675 0-1.162-.45t-.588-1.1L8.85 18.8q-.325-.125-.612-.3t-.563-.375l-1.55.65q-.625.275-1.25.05t-.975-.8l-1.175-2.05q-.35-.575-.2-1.225t.675-1.075l1.325-1Q4.5 12.5 4.5 12.337v-.675q0-.162.025-.337l-1.325-1Q2.675 9.9 2.525 9.25t.2-1.225L3.9 5.975q.35-.575.975-.8t1.25.05l1.55.65q.275-.2.575-.375t.6-.3l.225-1.65q.1-.65.588-1.1T10.825 2h2.35q.675 0 1.163.45t.587 1.1l.225 1.65q.325.125.613.3t.562.375l1.55-.65q.625-.275 1.25-.05t.975.8l1.175 2.05q.35.575.2 1.225t-.675 1.075l-1.325 1q.025.175.025.338v.674q0 .163-.05.338l1.325 1q.525.425.675 1.075t-.2 1.225l-1.2 2.05q-.35.575-.975.8t-1.25-.05l-1.5-.65q-.275.2-.575.375t-.6.3l-.225 1.65q-.1.65-.587 1.1t-1.163.45zM11 20h1.975l.35-2.65q.775-.2 1.438-.587t1.212-.938l2.475 1.025l.975-1.7l-2.15-1.625q.125-.35.175-.737T17.5 12t-.05-.787t-.175-.738l2.15-1.625l-.975-1.7l-2.475 1.05q-.55-.575-1.212-.962t-1.438-.588L13 4h-1.975l-.35 2.65q-.775.2-1.437.588t-1.213.937L5.55 7.15l-.975 1.7l2.15 1.6q-.125.375-.175.75t-.05.8q0 .4.05.775t.175.75l-2.15 1.625l.975 1.7l2.475-1.05q.55.575 1.213.963t1.437.587zm1.05-4.5q1.45 0 2.475-1.025T15.55 12t-1.025-2.475T12.05 8.5q-1.475 0-2.487 1.025T8.55 12t1.013 2.475T12.05 15.5M12 12"/></svg>';
		}
    }
}
