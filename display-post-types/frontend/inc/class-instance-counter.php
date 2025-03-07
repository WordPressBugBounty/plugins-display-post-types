<?php
/**
 * Instance counter class.
 *
 * @since      1.0.0
 *
 * @package    Display_Post_Types
 */

namespace Display_Post_Types\Frontend\Inc;

/**
 * Instance counter.
 *
 * @package    Display_Post_Types
 * @author     easyprolabs <contact@easyprolabs.com>
 */
class Instance_Counter {

	/**
	 * Holds the instance of this class.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    object
	 */
	protected static $instance = null;

	/**
	 * DPT instance counter.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    int
	 */
	private $counter = null;

	/**
	 * Check if there is at least one instance of DPT.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    bool
	 */
	private $has_dpt = false;

	/**
	 * Check if there is at least one instance with slider style.
	 *
	 * @since  2.0.0
	 * @access private
	 * @var    bool
	 */
	private $has_slider = false;

	/**
	 * Check if there is at least one instance with masonry style.
	 *
	 * @since  2.0.0
	 * @access private
	 * @var    bool
	 */
	private $has_mason = false;

	/**
	 * Check number of instances of DPT.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    int
	 */
	private $dpt_count = 0;

	/**
	 * DPT frontend script data.
	 *
	 * @since  2.8.4
	 * @access private
	 * @var    array
	 */
	private $script_data = array();

	/**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 */
	public function __construct() {
		$this->counter = wp_rand( 1, 1000 );
	}

	/**
	 * Return current instance of a key.
	 *
	 * @since  1.0.0
	 *
	 * @return int
	 */
	public function get() {
		$this->has_dpt         = true;
		return $this->counter += 1;
	}

	/**
	 * Return total number of DPT instances.
	 *
	 * @since  1.8.0
	 *
	 * @return int
	 */
	public function count() {
		return $this->dpt_count += 1;
	}

	/**
	 * Check if there is at least one instance of DPT.
	 *
	 * @since 1.7.0
	 *
	 * @return bool
	 */
	public function has_dpt() {
		return $this->has_dpt;
	}

	/**
	 * Check if there is at least one instance of DPT with Slider style.
	 *
	 * @since 2.0.0
	 */
	public function has_slider() {
		return $this->has_slider;
	}

	/**
	 * Check if there is at least one instance of DPT with Masonry style.
	 *
	 * @since 2.0.0
	 */
	public function has_mason() {
		return $this->has_mason;
	}

	/**
	 * Set if there is at least one instance of DPT with Slider style.
	 *
	 * @since 2.0.0
	 */
	public function set_slider() {
		$this->has_slider = true;
	}

	/**
	 * Unset if there are no more instances of DPT with Slider style.
	 *
	 * @since 2.0.0
	 */
	public function unset_slider() {
		$this->has_slider = false;
	}

	/**
	 * Set if there is at least one instance of DPT with Masonry style.
	 *
	 * @since 2.0.0
	 */
	public function set_mason() {
		$this->has_mason = true;
	}

	/**
	 * Unset if there are no more instances of DPT with Mason style.
	 *
	 * @since 2.0.0
	 */
	public function unset_mason() {
		$this->has_mason = false;
	}

	/**
	 * Add script data for a specific DPT instance.
	 *
	 * @since 2.8.4
	 *
	 * @param int   $instance DPT instance.
	 * @param array $data     Script data.
	 */
	public function add_script_data( $instance, $data ) {
		$this->script_data[ $instance ] = $data;
	}

	/**
	 * Get script data for a specific DPT instance.
	 *
	 * @since 2.8.4
	 *
	 * @param int $instance DPT instance.
	 */
	public function get_script_data( $instance = false ) {
		if ( ! $instance ) {
			return $this->script_data;
		} else if ( ! isset( $this->script_data[ $instance ] ) ) {
			return array();
		} else {
			return $this->script_data[ $instance ];
		}
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
