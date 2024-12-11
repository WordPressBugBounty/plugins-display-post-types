<?php
/**
 * Misc actions for DPT admin section.
 *
 * @package Display_Post_Types
 * @since 2.7.0
 */

namespace Display_Post_Types\Backend\Inc;

use Display_Post_Types\Helper\Getters as Get_Fn;
use Display_Post_Types\Frontend\Inc\Display;
use Display_Post_Types\Helper\Security;
use Display_Post_Types\Helper\Markup as Markup_Fn;
use Display_Post_Types\Helper\Utility as Utility_Fn;
use Display_Post_Types\Backend\Admin\ShortCodeGen;

/**
 * DPT admin misc actions.
 *
 * @since 2.7.0
 */
class Misc {

	/**
	 * Holds the instance of this class.
	 * 
	 * @since  2.7.0
	 * @access private
	 * @var    object
	 */
	private static $instance = null;

	/**
	 * Class cannot be instantiated directly.
	 *
	 * @since  2.7.0
	 */
	private function __construct() {}

    /**
     * Add extra widget options to the DPT widget.
     *
     * @since 2.7.0
     *
     * @param array  $options Widget options
     * @param object $widget Current widget instance
     * @param array  $instance Current widget instance settings
     */
    public function extra_widget_options( $options, $widget, $instance ) {
        $extended_options = array();
		$components = array('wrapper', 'title', 'thumbnail', 'excerpt', 'meta1', 'meta2');
		foreach( $components as $component ) {
			$type_options    = $this->get_typography_widget_options($component);
            $class_options   = $this->get_class_widget_options( $component );
            $wrapper_options = $this->get_wrapper_widget_options( $component, $widget, $instance );
			if ( $type_options || ! empty( $type_options ) ) {
				$extended_options = array_merge( $extended_options, $type_options );
			}
            if ( $class_options || ! empty( $class_options ) ) {
				$extended_options = array_merge( $extended_options, $class_options );
			}
            if ( $wrapper_options || ! empty( $wrapper_options ) ) {
				$extended_options = array_merge( $extended_options, $wrapper_options );
			}
		}
		$options['style']['items'] = Utility_Fn::insert_array(
			$options['style']['items'],
            $extended_options
		);

		$options['style']['items'] = $this->move_general_styling_to_tabs( $options['style']['items'] );
		return $options;
    }

    /**
     * Move general styling options to tabs.
     *
     * @since  2.7.0
     *
     * @param array $options Styling options.
     */
    private function move_general_styling_to_tabs( $options ) {
        $settings = array( 'thumbnail', 'title', 'excerpt', 'meta', 'category' );
        foreach ( $options as $key => $args ) {
            $wrapper = isset( $args['wrapper'] ) ? $args['wrapper'] : '';
            $type = isset( $args['type'] ) ? $args['type'] : '';
            if ( in_array( $wrapper, $settings, true ) && 'spcheckbox' !== $type ) {
                $args['wrapper'] = $wrapper . '_general';
            }
            $options[$key] = $args;
        }
        return $options;
    }

    private function get_typography_widget_options( $set ) {
		$options = array();
		$supported_by = array('title', 'excerpt', 'meta1', 'meta2');
		if ( ! in_array( $set, $supported_by ) ) {
			return false;
		}
		$type_sets = array(
            'font_style' => array(
                'label' => esc_html__( 'Font Style', 'display-post-types' ),
                'type'  => 'select',
                'choices' => array(
                    'normal' => esc_html__( 'Normal', 'display-post-types' ),
                    'italic' => esc_html__( 'Italic', 'display-post-types' ),
                    'oblique' => esc_html__( 'Oblique', 'display-post-types' ),
                ),
            ),
            'font_weight' => array(
                'label' => esc_html__( 'Font Weight', 'display-post-types' ),
                'type'  => 'number',
                'input_attrs' => array(
                    'step' => 100,
                    'min'  => 100,
                    'size' => 3,
                ),
            ),
            'font_size' => array(
                'label' => esc_html__( 'Font Size (in px)', 'display-post-types' ),
                'type'  => 'number',
                'input_attrs' => array(
                    'step' => 1,
                    'min'  => 0,
                    'size' => 3,
                )
            ),
            'line_height' => array(
                'label' => esc_html__( 'Line Height', 'display-post-types' ),
                'type'  => 'number',
                'input_attrs' => array(
                    'step' => 0.1,
                    'min'  => 1,
                    'size' => 3,
                )
            ),
            'letter_spacing' => array(
                'label' => esc_html__( 'Letter Spacing (in em)', 'display-post-types' ),
                'type'  => 'number',
                'input_attrs' => array(
                    'step' => 0.01,
                    'min'  => -2,
                    'size' => 3,
                )
            ),
            'text_transform' => array(
                'label' => esc_html__( 'Text Transform', 'display-post-types' ),
                'type'  => 'select',
                'choices' => array(
                    'none' => esc_html__( 'None', 'display-post-types' ),
                    'uppercase' => esc_html__( 'Uppercase', 'display-post-types' ),
                    'lowercase' => esc_html__( 'Lowercase', 'display-post-types' ),
                    'capitalize' => esc_html__( 'Capitalize', 'display-post-types' ),
                )
            ),
            'text_decoration' => array(
                'label' => esc_html__( 'Text Decoration', 'display-post-types' ),
                'type'  => 'select',
                'choices' => array(
                    'none' => esc_html__( 'None', 'display-post-types' ),
                    'underline' => esc_html__( 'Underline', 'display-post-types' ),
                    'overline' => esc_html__( 'Overline', 'display-post-types' ),
                    'line-through' => esc_html__( 'Line Through', 'display-post-types' ),
                )
            ),
            'text_color' => array(
                'label' => esc_html__( 'Text Color', 'display-post-types' ),
                'type'  => 'color',
            ),
            'link_color' => array(
                'label' => esc_html__( 'Link Color', 'display-post-types' ),
                'type'  => 'color',
            ),
            'link_hover_color' => array(
                'label' => esc_html__( 'Link Hover Color', 'display-post-types' ),
                'type'  => 'color',
            ),
        );
		foreach ( $type_sets as $key => $setting ) {
			$set_name = $set . '_' . $key;
			$wrap_key = $set;
			if ( 'meta1' === $set ) {
				$wrap_key = 'meta';
			}

			if ( 'meta2' === $set ) {
				$wrap_key = 'category';
			}
			$setting['setting'] = $set_name;
			$setting['wrapper'] = $wrap_key . '_typography';
			$options[ $set_name ] = $setting;
		}
		return $options;
	}

    private function get_class_widget_options( $set ) {
		$options = array();
		$supported_by = array('thumbnail', 'title', 'excerpt', 'meta1', 'meta2');
		if ( ! in_array( $set, $supported_by ) ) {
			return false;
		}
		$general_sets = array(
            'class' => array(
                'label' => esc_html__( 'Class', 'display-post-types' ),
                'type'  => 'text',
            ),
        );
		foreach ( $general_sets as $key => $setting ) {
			$set_name = $set . '_' . $key;
			$wrap_key = $set;
			if ( 'meta1' === $set ) {
				$wrap_key = 'meta';
			}

			if ( 'meta2' === $set ) {
				$wrap_key = 'category';
			}
			$setting['setting'] = $set_name;
			$setting['wrapper'] = $wrap_key . '_general';
			$options[ $set_name ] = $setting;
		}
		return $options;
	}

    private function get_wrapper_widget_options( $set, $widget, $instance ) {
		$options = array();
		$supported_by = array('wrapper');
		if ( ! in_array( $set, $supported_by ) ) {
			return false;
		}
		$wrapper_sets = array(
            'type' => array(
                'label' => esc_html__( 'Wrapper Type', 'display-post-types' ),
                'type'  => 'select',
                'choices' => array(
                    '' => esc_html__( 'No Wrapper', 'display-post-types' ),
                    'contained' => esc_html__( 'Contained Wrap', 'display-post-types' ),
                    'content' => esc_html__( 'Content Wrap', 'display-post-types' ),
                ),
            ),
            'width' => array(
                'label' => esc_html__( 'Wrapper Width (in %)', 'display-post-types' ),
                'type'  => 'number',
                'input_attrs' => array(
                    'step' => 1,
                    'min'  => 0,
                    'max'  => 100,
                    'size' => 3,
                ),
                'hide_callback' => function() use ( $widget, $instance ) {
                    return ! $this->is_style_support($instance['styles'], 'overlay');
                },
            ),
            'height' => array(
                'label' => esc_html__( 'Wrapper Height (in px)', 'display-post-types' ),
                'type'  => 'number',
                'input_attrs' => array(
                    'step' => 1,
                    'min'  => 0,
                    'max'  => 1000,
                    'size' => 3,
                ),
                'hide_callback' => function() use ( $widget, $instance ) {
                    return ! $this->is_style_support($instance['styles'], 'overlay') || ( isset( $instance['wrapper_type'] ) && 'content' !== $instance['wrapper_type'] );
                },
            ),
            'padding' => array(
                'label' => esc_html__( 'Padding (in px)', 'display-post-types' ),
                'type'  => 'four',
            ),
            'br_width' => array(
                'label' => esc_html__( 'Border Width (in px)', 'display-post-types' ),
                'type'  => 'four',
            ),
            'br_radius' => array(
                'label' => esc_html__( 'Border Radius (in px)', 'display-post-types' ),
                'type'  => 'four',
            ),
            'br_color' => array(
                'label' => esc_html__( 'Wrapper Border Color', 'display-post-types' ),
                'type'  => 'color',
            ),
        );
		foreach ( $wrapper_sets as $key => $setting ) {
			$set_name = $set . '_' . $key;
			$setting['setting'] = $set_name;
			if ( 'type' === $key ) {
				$setting['wrapper'] = 'container';
			} else {
				$setting['wrapper'] = 'container_extra';
			}
			$options[ $set_name ] = $setting;
		}
		return $options;
	}

    /**
     * Extend widget wrappers.
     *
     * @since  1.0.0
     *
     * @param array $wrappers Wrappers.
     * @param object $widget Widget.
     * @param array $instance Instance.
     */
    public function extend_widget_wrappers( $wrappers, $widget, $instance ) {
        $settings = array( 'thumbnail', 'title', 'excerpt', 'meta', 'category' );
        $tabs = apply_filters( 'dpt_widget_options_tabs', array( 'general', 'typography' ) );
        foreach ( $settings as $setting ) {
            $children = array_filter( array_map( function($val) use ($setting) {
                if ( 'thumbnail' === $setting && 'typography' === $val ) {
                    return '';
                }
                return $setting . '_' . $val;
            }, $tabs ) );
            $wrappers[ $setting . '_tabs' ] = array(
                'id'       => $setting . '_tabs',
				'type'     => 'tabs',
				'label'    => ucwords( $setting ) . ' Options',
				'class'    => 'dpt-' . $setting . '-options',
				'children' => $children,
                'hide_callback' => function() use ( $setting, $instance ) {
                    return ! in_array( $setting, $instance['style_sup'], true );
                },
            );
            foreach( $children as $child ) {
                $wrappers[ $child ] = array(
                    'id'       => $child,
                    'type'     => 'genaral',
                    'label'    => ucwords( str_replace( '_', ' ', $child ) ),
                    'class'    => 'dpt-' . $child,
                    'children' => false,
                    'tab_header_content' => Markup_Fn::get_admin_icons( $child ),
                );
            }

            if ( isset( $wrappers[ $setting ] ) ) {
                $wrappers[ $setting ]['children'] = array( $setting . '_tabs' );
            }
        }

        $wrappers['container_extra'] = array(
            'id'       => 'container_ext',
			'type'     => 'normal',
			'label'    => esc_html__( 'Container Extra Options', 'display-post-types' ),
			'class'    => 'dpt-container-ext-section',
			'children' => false,
            'hide_callback' => function() use ( $widget, $instance ) {
                return isset( $instance['wrapper_type'] ) && '' === $instance['wrapper_type'];
            },
        );
        if ( isset( $wrappers['container'] ) ) {
            $wrappers['container']['children'] = array( 'container_extra' );
        }
        if ( isset( $wrappers['stylewrap'] ) ) {
            $wrappers['stylewrap']['sortchildren'] = 'style_sup';
        }
        return $wrappers;
    }

    /**
     * Extends widget data updation.
     *
     * @since 2.7.0
     *
     * @param array $data Widget data.
     * @param array $new_data New widget data.
     * @param object $widget Widget instance.
     */
    public function extend_widget_update( $data, $new_data, $widget ) {
        $spacing = ['thumbnail', 'title', 'excerpt', 'meta1', 'meta2'];
        $typography = ['title', 'excerpt', 'meta1', 'meta2'];
        $type_sets = array(
            'font_style'       => 'string',
            'font_weight'      => 'int',
            'font_size'        => 'int',
            'line_height'      => 'float',
            'letter_spacing'   => 'float',
            'text_decoration'  => 'string',
            'text_transform'   => 'string',
            'text_color'       => 'color',
            'link_color'       => 'color',
            'link_hover_color' => 'color',
        );
        $general_sets = array(
            'class' => 'string',
        );

        $wrapper_sets = array(
            'type'      => 'string',
            'padding'   => 'arrint',
            'width'     => 'int',
            'height'    => 'int',
            'br_width'  => 'arrint',
            'br_radius' => 'arrint',
            'br_color'  => 'color',
        );

        foreach ( $typography as $set ) {
            foreach ( $type_sets as $key => $type ) {
                $set_name = $set . '_' . $key;
                $set_val = isset( $new_data[ $set_name ] ) ? $new_data[ $set_name ] : '';
                switch ( $type ) {
                    case 'string':
                        $data[ $set_name ] = sanitize_text_field( $set_val );
                        break;
                    case 'int':
                        $data[ $set_name ] = intval( $set_val );
                        break;
                    case 'float':
                        $data[ $set_name ] = floatval( $set_val );
                        break;
                    case 'color':
                        $data[ $set_name ] = sanitize_hex_color( $set_val );
                        break;
                    default:
                        $data[ $set_name ] = $set_val;
                        break;
                }
            }
        }

        foreach ( $wrapper_sets as $key => $type ) {
            $set_name = 'wrapper_' . $key;
            $set_val = isset( $new_data[ $set_name ] ) ? $new_data[ $set_name ] : '';
            switch ( $type ) {
                case 'string':
                    $data[ $set_name ] = sanitize_text_field( $set_val );
                    break;
                case 'int':
                    $data[ $set_name ] = intval( $set_val );
                    break;
                case 'color':
                    $data[ $set_name ] = sanitize_hex_color( $set_val );
                    break;
                case 'arrint':
                    $set_val = isset( $new_data[ $set_name ] ) ? $new_data[ $set_name ] : array();
                    $set_val = ! is_array( $set_val ) ? explode( ',', $set_val ) : $set_val;
                    $old_val = isset( $data[ $set_name ] ) ? $data[ $set_name ] : array();
                    if ( ( empty( $old_val ) && empty( array_filter( $set_val ) ) ) || ! is_array( $set_val ) ) {
                        $data[ $set_name ] = array();
                    } else {
                        $data[ $set_name ] = array_map( 'intval', $set_val );
                    }
                    break;
                default:
                    $data[ $set_name ] = $set_val;
                    break;
            }
        }

        foreach ( $spacing as $set ) {
            foreach ( $general_sets as $key => $type ) {
                $set_name = $set . '_' . $key;
                $set_val = isset( $new_data[ $set_name ] ) ? $new_data[ $set_name ] : '';
                switch ( $type ) {
                    case 'string':
                        $data[ $set_name ] = sanitize_text_field( $set_val );
                        break;
                    default:
                        $data[ $set_name ] = $set_val;
                        break;
                }
            }
        }

        return $data;
    }

    /**
     * Extends setting type.
     *
     * @since 2.7.0
     *
     * @param array $data_type Setting type.
     */
    public function extend_setting_type( $data_type ) {
        $typography = ['title', 'excerpt', 'meta1', 'meta2'];
        $spacing = ['thumbnail', 'title', 'excerpt', 'meta1', 'meta2'];
        $type_sets = array(
            'font_style'       => 'string',
            'font_weight'      => 'int',
            'font_size'        => 'int',
            'line_height'      => 'float',
            'letter_spacing'   => 'float',
            'text_decoration'  => 'string',
            'text_transform'   => 'string',
            'text_color'       => 'color',
            'link_color'       => 'color',
            'link_hover_color' => 'color',
        );
        $wrapper_sets = array(
            'type'      => 'string',
            'padding'   => 'arrint',
            'width'     => 'int',
            'height'    => 'int',
            'br_width'  => 'arrint',
            'br_radius' => 'arrint',
            'br_color'  => 'color',
        );
        $general_sets = array(
            'class' => 'string',
        );

        foreach ( $typography as $set ) {
            foreach ( $type_sets as $key => $type ) {
                $set_name = $set . '_' . $key;
                $data_type[ $set_name ] = $type;
            }
        }

        foreach ( $spacing as $set ) {
            foreach ( $general_sets as $key => $type ) {
                $set_name = $set . '_' . $key;
                $data_type[ $set_name ] = $type;
            }
        }

        foreach ( $wrapper_sets as $key => $type ) {
            $data_type[ 'wrapper_' . $key ] = $type;
        }

        return $data_type;
    }

    /**
     * Extend inline styles to apply optional customizations.
     *
     * @since 1.0.0
     *
     * @param array $instance Widget instance options.
     * @param string $id Widget ID.
     */
    public function extend_inline_styles( $instance, $id ) {
        $args  = $instance['args'];
        $query = $instance['query'];
        $elems = ['title', 'excerpt', 'meta1', 'meta2'];
        $props = array(
            'font_style',
            'font_weight',
            'font_size',
            'line_height',
            'letter_spacing',
            'text_decoration',
            'text_transform',
            'text_color',
            'link_color',
            'link_hover_color',
        );
        $wrapper_props = array(
            'padding',
            'br_width',
            'width',
            'height',
            'br_radius',
            'br_color',
        );
        $css = '';
        foreach ( $elems as $elem ) {
            foreach ( $props as $prop ) {
                $setting = $elem . '_' . $prop;
                if ( isset( $args[ $setting ] ) ) {
                    $value = $args[ $setting ];
                    if ( 'font_size' === $prop && $value <= 0 ) {
                        continue;
                    }
					if ( 'meta1' === $elem ) {
						if ( ! $value ) {
							continue;
						}
						if ( 'link_color' === $prop ) {
							$css .= '.dpt-meta a { color: ' . $value . ' !important; }';
							continue;
						}
						if ( 'link_hover_color' === $prop ) {
							$css .= '.dpt-meta a:hover { color: ' . $value . ' !important; }';
							continue;
						}
					}
					if ( 'meta2' === $elem ) {
						if ( ! $value ) {
							continue;
						}
						if ( 'link_color' === $prop ) {
							$css .= '.dpt-meta2 a { color: ' . $value . ' !important; }';
							continue;
						}
						if ( 'link_hover_color' === $prop ) {
							$css .= '.dpt-meta2 a:hover { color: ' . $value . ' !important; }';
							continue;
						}
					}
                    $suffix = '';
                    if ( in_array( $prop, array( 'font_size' ), true ) ) {
                        $suffix = 'px';
                    } else if ( 'letter_spacing' === $prop ) {
                        $suffix = 'em';
                    }
                    if ( $value ) {
                        $value = $value . $suffix;
                        $css .= '--dpt-' . str_replace( '_', '-', $setting ) . ':' . $value . ';';
                    }
                }
            }
        }

        if ( isset( $args['wrapper_type'] ) && '' !== $args['wrapper_type'] ) {
            $type = $args['wrapper_type'];
            foreach ( $wrapper_props as $prop ) {
                $setting = 'wrapper_' . $prop;
                $suffix = '';
                if ( in_array( $prop, array('height' ), true ) ) {
                    $suffix = 'px';
                } elseif ( in_array( $prop, array('width' ), true ) ) {
                    $suffix = '%';
                }
                if ( isset( $args[ $setting ] ) ) {
                    $value = $args[ $setting ];
                    if ( in_array( $prop, array( 'padding', 'br_width', 'br_radius' ), true ) ) {
                        if ( ! is_array( $value ) ) {
                            $value = explode( ',', $value );
                            $value = array_map( 'absint', $value );
                        }
                        $value = array_map( function($val) { return $val . 'px'; }, $value );
                        $value = implode( ' ', $value );
                    }

                    if ( 'content' === $type ) {
                        if ( in_array( $prop, array( 'padding' ), true ) || $this->is_style_support($args['styles'], 'overlay') ) {
                            $setting = 'wrapper_entry_' . $prop;
                        }
                    } elseif ( 'width' === $prop ) {
                        $setting = 'wrapper_entry_' . $prop;
                    }

                    if ( in_array( $prop, array( 'height' ), true ) && ! $value ) {
                        continue;
                    }
                    $css .= '--dpt-' . str_replace( '_', '-', $setting ) . ':' . $value . $suffix . ';';
                }
            }
        }

        if ( $css ) {
            $css = sprintf( '#dpt-wrapper-%1$s { %2$s }', $id, $css );
            printf( '<style type="text/css">%1$s</style>', esc_html( wp_strip_all_tags( $css, true ) ) );
        }
    }

    /**
     * Add custom class to the entry title.
     *
     * @since 2.5.0
     *
     * @param array $attr HTML attributes.
     */
    public function extend_entry_title( $attr ) {
        if ( isset( $this->args['title_class'] ) && $this->args['title_class'] ) {
			$attr['class'] .= ' ' . $this->args['title_class'];
		}
        return $attr;
    }

    /**
     * Add custom class to the entry thumbnail.
     *
     * @since 2.5.0
     *
     * @param array $attr HTML attributes.
     */
    public function extend_entry_thumbnail( $attr ) {
        if ( isset( $this->args['thumbnail_class'] ) && $this->args['thumbnail_class'] ) {
			$attr['class'] .= ' ' . $this->args['thumbnail_class'];
		}
        return $attr;
    }

    /**
     * Add custom class to the entry excerpt.
     *
     * @since 2.5.0
     *
     * @param array $attr HTML attributes.
     */
    public function extend_entry_excerpt( $attr ) {
        if ( isset( $this->args['excerpt_class'] ) && $this->args['excerpt_class'] ) {
			$attr['class'] .= ' ' . $this->args['excerpt_class'];
		}
        return $attr;
    }

    /**
     * Add custom class to the entry meta.
     *
     * @since 2.5.0
     *
     * @param array $attr HTML attributes.
     */
    public function extend_entry_meta( $attr ) {
        if ( isset( $this->args['meta1_class'] ) && $this->args['meta1_class'] ) {
			$attr['class'] .= ' ' . $this->args['meta1_class'];
		}
        return $attr;
    }

    /**
     * Add custom class to the entry meta2.
     *
     * @since 2.5.0
     *
     * @param array $attr HTML attributes.
     */
    public function extend_entry_meta2( $attr ) {
        if ( isset( $this->args['meta2_class'] ) && $this->args['meta2_class'] ) {
			$attr['class'] .= ' ' . $this->args['meta2_class'];
		}
        return $attr;
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
	 * Returns the instance of this class.
	 *
	 * @since  2.7.0
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
