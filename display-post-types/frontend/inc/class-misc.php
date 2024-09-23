<?php
/**
 * Misc actions for DPT front section.
 *
 * @package Display_Post_Types
 * @since 2.7.0
 */

namespace Display_Post_Types\Frontend\Inc;

use Display_Post_Types\Helper\Getters as Get_Fn;
use Display_Post_Types\Helper\Markup as Markup_Fn;
use Display_Post_Types\Helper\Utility as Utility_Fn;

/**
 * DPT front misc actions.
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
     * Holds args for current dpt instance.
     *
     * @since 2.5.0
     * @access protected
     * @var array
     */
    protected $args = array();

    /**
     * Get Entry data and save it for further use.
     *
     * @since 2.5.0
     *
     * @param array $action_args Arguments.
     */
    public function loop_data( $action_args ) {
        $this->args = $action_args['args'];
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
