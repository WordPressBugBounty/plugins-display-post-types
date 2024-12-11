<?php
/**
 * The admin-options page of the plugin.
 *
 * @link       https://www.easyprolabs.com
 * @since      1.0.0
 *
 * @package    Display_Post_Types
 * @subpackage Display_Post_Types/admin
 */

namespace Display_Post_Types\Backend\Admin;

use Display_Post_Types\Backend\Admin\ShortCodeGen;
use Display_Post_Types\Helper\Getters as Get_Fn;
use Display_Post_Types\Frontend\Inc\Instance_Counter;

/**
 * The admin options page of the plugin.
 *
 * @since 1.0.0
 */
class Options {

    /**
	 * Holds the instance of this class.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    object
	 */
	protected static $instance = null;

	/**
	 * Holds all modules of the admin page.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    array
	 */
	public $modules = array();

	/**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 */
	public function __construct() {
		$this->modules = array(
			'options'     => array(
				'label' => esc_html__( 'Home', 'display-post-types' ),
			),
			'shortcode' => array(
				'label' => esc_html__( 'Generate Shortcode', 'display-post-types' ),
			),
		);
	}

    /**
	 * Register hooked functions.
	 *
	 * @since 1.0.0
	 */
	public static function init() {
		$inst = self::get_instance();
        add_action( 'admin_menu', array( $inst, 'add_options_page' ) );
		// add_action( 'admin_init', array( $inst, 'add_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $inst, 'page_scripts' ) );
		add_action( 'dpt_options_page_content', array( $inst, 'display_content' ) );
	}

    /**
	 * Add plugin specific options page.
	 *
	 * @since    1.5
	 */
	public function add_options_page() {
		$suffix = add_menu_page(
			esc_html__( 'Display Post Types', 'display-post-types' ),
			esc_html__( 'Display Post Types', 'display-post-types' ),
			'manage_options',
			'dpt-options',
			array( $this, 'options_page' ),
			'data:image/svg+xml;base64,PCEtLSBHZW5lcmF0ZWQgYnkgSWNvTW9vbi5pbyAtLT4KPHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjMyIiBoZWlnaHQ9IjMyIiB2aWV3Qm94PSIwIDAgMzIgMzIiPgo8dGl0bGU+Z3JpZDwvdGl0bGU+CjxwYXRoIGZpbGw9IiNmZmYiIGQ9Ik03IDEyaC02Yy0wLjU1MyAwLTEgMC40NDctMSAxdjZjMCAwLjU1MyAwLjQ0NyAxIDEgMWg2YzAuNTUzIDAgMS0wLjQ0NyAxLTF2LTZjMC0wLjU1My0wLjQ0Ny0xLTEtMXpNNiAxN2MwIDAuNTUzLTAuNDQ3IDEtMSAxaC0yYy0wLjU1MyAwLTEtMC40NDctMS0xdi0yYzAtMC41NTMgMC40NDctMSAxLTFoMmMwLjU1MyAwIDEgMC40NDcgMSAxdjJ6TTcgMGgtNmMtMC41NTMgMC0xIDAuNDQ3LTEgMXY2YzAgMC41NTMgMC40NDcgMSAxIDFoNmMwLjU1MyAwIDEtMS40NDcgMS0ydi01YzAtMC41NTMtMC40NDctMS0xLTF6TTYgNWMwIDAuNTUzLTAuNDQ3IDEtMSAxaC0yYy0wLjU1MyAwLTEtMC40NDctMS0xdi0yYzAtMC41NTMgMC40NDctMSAxLTFoMmMwLjU1MyAwIDEgMC40NDcgMSAxdjJ6TTcgMjRoLTZjLTAuNTUzIDAtMSAwLjQ0Ny0xIDF2NmMwIDAuNTUzIDAuNDQ3IDEgMSAxaDZjMC41NTMgMCAxLTAuNDQ3IDEtMXYtNmMwLTAuNTUzLTAuNDQ3LTEtMS0xek02IDI5YzAgMC41NTMtMC40NDcgMS0xIDFoLTJjLTAuNTUzIDAtMS0wLjQ0Ny0xLTF2LTJjMC0wLjU1MyAwLjQ0Ny0xIDEtMWgyYzAuNTUzIDAgMSAwLjQ0NyAxIDF2MnpNMTkgMTJoLTZjLTAuNTUzIDAtMSAwLjQ0Ny0xIDF2NmMwIDAuNTUzIDAuNDQ3IDEgMSAxaDZjMC41NTMgMCAxLTAuNDQ3IDEtMXYtNmMwLTAuNTUzLTAuNDQ3LTEtMS0xek0xOCAxN2MwIDAuNTUzLTAuNDQ3IDEtMSAxaC0yYy0wLjU1MyAwLTEtMC40NDctMS0xdi0yYzAtMC41NTMgMC40NDctMSAxLTFoMmMwLjU1MyAwIDEgMC40NDcgMSAxdjJ6TTE5IDI0aC02Yy0wLjU1MyAwLTEgMC40NDctMSAxdjZjMCAwLjU1MyAwLjQ0NyAxIDEgMWg2YzAuNTUzIDAgMS0wLjQ0NyAxLTF2LTZjMC0wLjU1My0wLjQ0Ny0xLTEtMXpNMTggMjljMCAwLjU1My0wLjQ0NyAxLTEgMWgtMmMtMC41NTMgMC0xLTAuNDQ3LTEtMXYtMmMwLTAuNTUzIDAuNDQ3LTEgMS0xaDJjMC41NTMgMCAxIDAuNDQ3IDEgMXYyek0zMSAxMmgtNmMtMC41NTMgMC0xIDAuNDQ3LTEgMXY2YzAgMC41NTMgMC40NDcgMSAxIDFoNmMwLjU1MyAwIDEtMC40NDcgMS0xdi02YzAtMC41NTMtMC40NDctMS0xLTF6TTMwIDE3YzAgMC41NTMtMC40NDcgMS0xIDFoLTJjLTAuNTUzIDAtMS0wLjQ0Ny0xLTF2LTJjMC0wLjU1MyAwLjQ0Ny0xIDEtMWgyYzAuNTUzIDAgMSAwLjQ0NyAxIDF2MnpNMzEgMGgtNmMtMC41NTMgMC0xIDAuNDQ3LTEgMXY2YzAgMC41NTMgMC40NDcgMSAxIDFoNmMwLjU1MyAwIDEtMS40NDcgMS0ydi01YzAtMC41NTMtMC40NDctMS0xLTF6TTMwIDVjMCAwLjU1My0wLjQ0NyAxLTEgMWgtMmMtMC41NTMgMC0xLTAuNDQ3LTEtMXYtMmMwLTAuNTUzIDAuNDQ3LTEgMS0xaDJjMC41NTMgMCAxIDAuNDQ3IDEgMXYyek0xOSAwaC02Yy0wLjU1MyAwLTEgMC40NDctMSAxdjZjMCAwLjU1MyAwLjQ0NyAxIDEgMWg2YzAuNTUzIDAgMS0xLjQ0NyAxLTJ2LTVjMC0wLjU1My0wLjQ0Ny0xLTEtMXpNMTggNWMwIDAuNTUzLTAuNDQ3IDEtMSAxaC0yYy0wLjU1MyAwLTEtMC40NDctMS0xdi0yYzAtMC41NTMgMC40NDctMSAxLTFoMmMwLjU1MyAwIDEgMC40NDcgMSAxdjJ6TTMxIDI0aC02Yy0wLjU1MyAwLTEgMC40NDctMSAxdjZjMCAwLjU1MyAwLjQ0NyAxIDEgMWg2YzAuNTUzIDAgMS0wLjQ0NyAxLTF2LTZjMC0wLjU1My0wLjQ0Ny0xLTEtMXpNMzAgMjljMCAwLjU1My0wLjQ0NyAxLTEgMWgtMmMtMC41NTMgMC0xLTAuNDQ3LTEtMXYtMmMwLTAuNTUzIDAuNDQ3LTEgMS0xaDJjMC41NTMgMCAxIDAuNDQ3IDEgMXYyeiI+PC9wYXRoPgo8L3N2Zz4K'
		);

        // Sub Menu pages.
		$submenu_pages = array(
			'dpt-shortcode' => __( 'Shortcode', 'display-post-types' ),
		);

		foreach ( $submenu_pages as $key => $label ) {
			add_submenu_page(
				'dpt-options',
				$label,
				$label,
				'manage_options',
				$key,
				array( $this, 'options_page' )
			);
		}
	}

    /**
	 * Render Plus settings page.
	 *
	 * @since    1.0.0
	 */
	public function options_page() {
		do_action( 'dpt_options_page_content', 'dpt-options' );
	}

    /**
	 * Function to add options page content.
	 *
	 * @since    1.0.0
	 */
	public function display_content() {
		global $pagenow;
		$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
		if ( 'admin.php' === $pagenow && $page ) {
            $current_page = 'home';
			switch ( $page ) {
				case 'dpt-shortcode':
					$shcode_gen   = ShortCodeGen::get_instance();
					$current_page = 'shortcode';
					break;
				default:
					$current_page = 'home';
					break;
			}
			include_once DISPLAY_POST_TYPES_DIR . 'backend/admin/templates/main.php';
		}
	}

	/**
	 * Get properly framed A tag link to be used on documentation pages.
	 *
	 * @since 3.3.0
	 *
	 * @param str  $link URL to be used as href value.
	 * @param str  $text Link Text.
	 * @param str  $classes Link HTML classes.
	 * @param bool $echo Echo or return.
	 */
	public function mlink( $link, $text, $classes = '', $echo = true ) {
		$markup = '';
		if ( $link && $text ) {
			$text    = esc_html( $text ) . '<span class="dashicons dashicons-external"></span>';
			$classes = $classes ? 'class="' . esc_attr( $classes ) . '"' : '';
			$markup  = sprintf(
				'<a %s href="%s" rel="noopener noreferrer nofollow" target="_blank">%s</a>',
				$classes,
				esc_url( $link ),
				$text
			);
		}

		if ( $echo ) {
			echo $markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $markup;
		}
    }

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function page_scripts() {
		$current_screen = get_current_screen();
		$dpt = Instance_Counter::get_instance();
		$load_on = array(
			'toplevel_page_dpt-options',
			'display-post-types_page_dpt-shortcode',
		);
		if ( $current_screen && in_array( $current_screen->id, $load_on, true ) ) {

			$shortgen_data = apply_filters( 'dpt_shortgen_data', array(
				'styleSupport' => array_column( Get_Fn::styles(), 'support', 'style' ),
				'ajaxurl'      => admin_url( 'admin-ajax.php' ),
				'security'     => wp_create_nonce( 'dpt-admin-ajax-nonce' ),
			) );

			/**
			 * Enqueue admin scripts.
			 */
			wp_enqueue_script(
				'dptadminoptions',
				DISPLAY_POST_TYPES_URL . 'backend/js/admin.build.js',
				array( 'jquery-ui-tabs', 'wp-color-picker' ),
				DISPLAY_POST_TYPES_VERSION,
				true
			);
			wp_localize_script( 'dptadminoptions', 'dptShortgenData', $shortgen_data );
			wp_localize_script( 'dptadminoptions', 'dptScriptData', array(
				'security'  => wp_create_nonce( 'dpt-frontend-ajax-nonce' ),
				'ajaxurl'   => admin_url( 'admin-ajax.php' ),
				'instances' => $dpt->get_script_data(),
			) );

			/**
			 * Enqueue admin stylesheet.
			 */
			wp_enqueue_style(
				'dptadminoptions',
				DISPLAY_POST_TYPES_URL . 'backend/css/admin-options.css',
				array( 'wp-color-picker' ),
				DISPLAY_POST_TYPES_VERSION,
				'all'
			);

			$deps = array();

			// Load slider script only if there is at least one instance of DPT slider.
			$deps[] = 'dpt-flickity';
			wp_enqueue_script(
				'dpt-flickity',
				DISPLAY_POST_TYPES_URL . 'frontend/js/flickity.pkgd.min.js',
				array(),
				DISPLAY_POST_TYPES_VERSION,
				true
			);

			// Load Mason script only if there is at least one instance of DPT masonry layout.
			$deps[] = 'dpt-bricklayer';
			wp_enqueue_script(
				'dpt-bricklayer',
				DISPLAY_POST_TYPES_URL . 'frontend/js/bricklayer.build.js',
				array(),
				DISPLAY_POST_TYPES_VERSION,
				true
			);

			wp_enqueue_script(
				'dpt-scripts',
				DISPLAY_POST_TYPES_URL . 'frontend/js/scripts.build.js',
				$deps,
				DISPLAY_POST_TYPES_VERSION,
				true
			);

			wp_enqueue_style(
				'dpt-style',
				DISPLAY_POST_TYPES_URL . 'frontend/css/style.css',
				array(),
				DISPLAY_POST_TYPES_VERSION,
				'all'
			);
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

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}