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
	 *
	 * @param array $args {
	 *     Parameters needed to display an SVG.
	 *
	 *     @type string $icon  Required SVG icon filename.
	 *     @type string $title Optional SVG title.
	 *     @type string $desc  Optional SVG description.
	 * }
	 */
	public static function the_icon( $args = array() ) {
		echo self::get_icon( $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Return font icon SVG markup.
	 *
	 * This function incorporates code from Twenty Seventeen WordPress Theme,
	 * Copyright 2016-2017 WordPress.org. Twenty Seventeen is distributed
	 * under the terms of the GNU GPL.
	 *
	 * @param array $args {
	 *     Parameters needed to display an SVG.
	 *
	 *     @type string $icon  Required SVG icon filename.
	 *     @type string $title Optional SVG title.
	 *     @type string $desc  Optional SVG description.
	 * }
	 * @return string Font icon SVG markup.
	 */
	public static function get_icon( $args = array() ) {
		// Make sure $args are an array.
		if ( empty( $args ) ) {
			return esc_html__( 'Please define default parameters in the form of an array.', 'display-post-types' );
		}

		// Define an icon.
		if ( false === array_key_exists( 'icon', $args ) ) {
			return esc_html__( 'Please define an SVG icon filename.', 'display-post-types' );
		}

		// Set defaults.
		$defaults = array(
			'icon'     => '',
			'title'    => '',
			'desc'     => '',
			'fallback' => false,
		);

		// Parse args.
		$args = wp_parse_args( $args, $defaults );

		// Set aria hidden.
		$aria_hidden = ' aria-hidden="true"';

		// Set ARIA.
		$aria_labelledby = '';

		/*
		* DPT doesn't use the SVG title or description attributes; non-decorative icons are
		* described with .ppjs__offscreen. However, child themes can use the title and description
		* to add information to non-decorative SVG icons to improve accessibility.
		* See https://www.paciellogroup.com/blog/2013/12/using-aria-enhance-svg-accessibility/.
		*/
		if ( $args['title'] ) {
			$aria_hidden     = '';
			$unique_id       = uniqid();
			$aria_labelledby = ' aria-labelledby="title-' . $unique_id . '"';

			if ( $args['desc'] ) {
				$aria_labelledby = ' aria-labelledby="title-' . $unique_id . ' desc-' . $unique_id . '"';
			}
		}

		// Begin SVG markup.
		$svg = '<svg class="icon icon-' . esc_attr( $args['icon'] ) . '"' . $aria_hidden . $aria_labelledby . ' role="img" focusable="false">';

		// Display the title.
		if ( $args['title'] ) {
			$svg .= '<title id="title-' . $unique_id . '">' . esc_html( $args['title'] ) . '</title>';

			// Display the desc only if the title is already set.
			if ( $args['desc'] ) {
				$svg .= '<desc id="desc-' . $unique_id . '">' . esc_html( $args['desc'] ) . '</desc>';
			}
		}

		/*
		* Display the icon.
		*
		* The whitespace around `<use>` is intentional - it is a work around to a keyboard navigation bug in Safari 10.
		*
		* See https://core.trac.wordpress.org/ticket/38387.
		*/
		$svg .= ' <use href="#icon-' . esc_attr( $args['icon'] ) . '" xlink:href="#icon-' . esc_attr( $args['icon'] ) . '"></use> ';

		// Add some markup to use as a fallback for browsers that do not support SVGs.
		if ( $args['fallback'] ) {
			$svg .= '<span class="svg-fallback icon-' . esc_attr( $args['icon'] ) . '"></span>';
		}

		$svg .= '</svg>';

		return $svg;
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
		 * @param string $name Template file name.
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
		    return '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><path d="M28.688 12v4h-4v9.313h-4v-9.313h-4v-4h12zM3.313 5.313h17.375v4h-6.688v16h-4v-16h-6.688v-4z"></path></svg>';
		}

		if ( false !== strpos( $id, 'spacing' ) ) {
			return '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><path d="M13.313 17.313v-2.625h16v2.625h-16zM13.313 25.313v-2.625h16v2.625h-16zM13.313 6.688h16v2.625h-16v-2.625zM8 9.313v13.375h3.313l-4.625 4.625-4.688-4.625h3.313v-13.375h-3.313l4.688-4.625 4.625 4.625h-3.313z"></path></svg>';
		}

		if ( false !== strpos( $id, 'general' ) ) {
			return '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32"><path d="M29.181 19.070c-1.679-2.908-0.669-6.634 2.255-8.328l-3.145-5.447c-0.898 0.527-1.943 0.829-3.058 0.829-3.361 0-6.085-2.742-6.085-6.125h-6.289c0.008 1.044-0.252 2.103-0.811 3.070-1.679 2.908-5.411 3.897-8.339 2.211l-3.144 5.447c0.905 0.515 1.689 1.268 2.246 2.234 1.676 2.903 0.672 6.623-2.241 8.319l3.145 5.447c0.895-0.522 1.935-0.82 3.044-0.82 3.35 0 6.067 2.725 6.084 6.092h6.289c-0.003-1.034 0.259-2.080 0.811-3.038 1.676-2.903 5.399-3.894 8.325-2.219l3.145-5.447c-0.899-0.515-1.678-1.266-2.232-2.226zM16 22.479c-3.578 0-6.479-2.901-6.479-6.479s2.901-6.479 6.479-6.479c3.578 0 6.479 2.901 6.479 6.479s-2.901 6.479-6.479 6.479z"></path></svg>';
		}
    }
}