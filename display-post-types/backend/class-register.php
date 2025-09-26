<?php
/**
 * The back-end specific functionality of the plugin.
 *
 * @package Display_Post_Types
 * @since 1.0.0
 */

namespace Display_Post_Types\Backend;

use Display_Post_Types\Helper\Getters as Get_Fn;
use Display_Post_Types\Helper\Security;
use Display_Post_Types\Backend\Inc\Block;
use Display_Post_Types\Backend\Inc\Shortcode;
use Display_Post_Types\Backend\Inc\Misc;
use Display_Post_Types\Backend\Admin\Options;
use Display_Post_Types\Frontend\Inc\Display;
use Display_Post_Types\Backend\Admin\ShortCodeGen;
use Display_Post_Types\Helper\Icon_Loader as Icons;
use Display_Post_Types\Frontend\Inc\Instance_Counter;

/**
 * The back-end specific functionality of the plugin.
 *
 * @since 1.0.0
 */
class Register {

	/**
	 * Holds the instance of this class.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    object
	 */
	protected static $instance = null;

	/**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 */
	public function __construct() {}

	/**
	 * Register hooked functions.
	 *
	 * @since 1.0.0
	 */
	public static function init() {
		$inst      = self::get_instance();
		$block     = Block::init();
		$shortcode = Shortcode::init();
		$options   = Options::init();
		$misc      = Misc::get_instance();
		$icons     = Icons::get_instance();
		add_action( 'widgets_init', array( $inst, 'register_custom_widget' ) );
		add_action( 'admin_enqueue_scripts', array( $inst, 'enqueue_admin_widgets' ) );
		add_action( 'admin_head', array( $inst, 'dismiss_notices' ) );
		add_action( 'admin_notices', array( $inst, 'admin_notices' ) );

		if (
			in_array(
				'elementor/elementor.php',
				apply_filters( 'active_plugins', get_option( 'active_plugins' ) ),
				true
			)
		) {
			add_action(
				'elementor/editor/before_enqueue_scripts',
				array( $inst, 'enqueue_admin' )
			);
		}

		// Handle Admin Ajax Requests.
		add_action( 'wp_ajax_dpt_render_preview', array( $inst, 'get_dpt_preview' ) );
		add_action( 'wp_ajax_dpt_blank_shortcode_template', array( $inst, 'get_shortcode_form' ) );
		add_action( 'wp_ajax_dpt_create_new_shortcode', array( $inst, 'create_new_shortcode' ) );
		add_action( 'wp_ajax_dpt_load_shortcode', array( $inst, 'load_shortcode' ) );
		add_action( 'wp_ajax_dpt_delete_shortcode', array( $inst, 'delete_shortcode' ) );
		add_action( 'wp_ajax_dpt_update_shortcode', array( $inst, 'update_shortcode' ) );

		// Extend DPT widget options.
		add_filter( 'dpt_widget_options', array( $misc, 'extra_widget_options' ), 10, 3 );
		add_filter( 'dpt_widget_wrappers', array( $misc, 'extend_widget_wrappers' ), 10, 3 );
		add_filter( 'dpt_widget_update', array( $misc, 'extend_widget_update' ), 10, 3 );
		add_filter( 'dpt_setting_type', array( $misc, 'extend_setting_type' ) );
		add_action( 'dpt_before_wrapper', array( $misc, 'extend_inline_styles' ), 0, 2 );

		// Load icon definitions.
		add_filter( 'admin_footer', array( $icons, 'add_admin_icons' ), 9999 );
	}

	/**
	 * Register the custom Widget.
	 *
	 * @since 1.0.0
	 */
	public function register_custom_widget() {
		require_once DISPLAY_POST_TYPES_DIR . '/backend/inc/class-widget.php';
		register_widget( 'Display_Post_Types\Backend\Inc\Widget' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_admin_widgets() {
		$screen = get_current_screen();
		if ( in_array( $screen->id, array( 'widgets', 'customize' ), true ) ) {
			$this->enqueue_admin();
		}
	}

	/**
	 * Register the Scripts and styles for the admin area.
	 *
	 * @since    1.8.0
	 */
	public function enqueue_admin() {
		$widget_data = apply_filters( 'dpt_widget_data', array(
			'styleSupport' => array_column( Get_Fn::styles(), 'support', 'style' ),
		) );

		wp_register_script(
			'wp-color-picker-alpha',
			plugin_dir_url( __FILE__ ) . 'js/wp-color-picker-alpha.min.js',
			array( 'wp-color-picker' ),
			DISPLAY_POST_TYPES_VERSION,
			true
		);
		$script_deps = apply_filters( 'dpt_widget_script_deps', array( 'jquery', 'wp-color-picker-alpha' ) );
		$style_deps  = apply_filters( 'dpt_widget_style_deps', array() );

		wp_enqueue_style(
			'dpt_widget_style',
			plugin_dir_url( __FILE__ ) . 'css/widgets.css',
			$style_deps,
			DISPLAY_POST_TYPES_VERSION,
			'all'
		);

		wp_enqueue_script(
			'dpt_widget_js',
			plugin_dir_url( __FILE__ ) . 'js/widgets.build.js',
			$script_deps,
			DISPLAY_POST_TYPES_VERSION,
			true
		);
		wp_localize_script( 'dpt_widget_js', 'dptWidgetData', $widget_data );
	}

	/**
	 * Display message on plugin activation.
	 *
	 * @since    1.0.0
	 */
	public function admin_notices() {
		// Check what admin page we are on.
		$current_screen = get_current_screen();

		// Screens on which notice is to be displayed.
		$enabled_screens = array( 'dashboard', 'themes', 'plugins', 'update-core.php' );

		if ( ! ( in_array( $current_screen->id, $enabled_screens, true ) || in_array( $current_screen->parent_file, $enabled_screens, true ) ) ) {
			return;
		}

		// Podcast Player Admin Notice.
		if ( DISPLAY_POST_TYPES_VERSION !== get_option( 'dpt-admin-notice' ) ) {
			// include_once DISPLAY_POST_TYPES_DIR . '/backend/inc/notifications.php';

			?>
			<style type="text/css" media="screen">

				.dpt-welcome-notice p {
					margin: 0.25em !important;
				}

				.common-links {
					padding: 5px 0;
				}

				.dpt-link {
					display: inline-block;
					line-height: 1;
				}

				.dpt-link a {
					padding: 0;
				}

				.dpt-link + .dpt-link {
					margin-left: 10px;
					padding: 0 0 0 10px !important;
					border-left: 2px solid #999;
				}

			</style>

			<?php
		}

		if ( defined( 'DPT_PRO_VERSION' ) && version_compare( DPT_PRO_VERSION, '1.4.6', '<' ) ) {
			?>
			<div class="notice-warning notice is-dismissible pp-welcome-notice">
				<p><?php esc_html_e( 'There is an update available to Display Post Types Pro. Please update to Display Post Types Pro v1.4.6.', 'display-post-types' ); ?></p>
			</div>
			<?php
		}
	}

	/**
	 * Display message on plugin activation.
	 *
	 * @since    1.0.0
	 */
	public function dismiss_notices() {
		if ( isset( $_GET['dpt-dismiss'] ) && check_admin_referer( 'dpt-dismiss-' . get_current_user_id() ) ) {
			update_option( 'dpt-admin-notice', DISPLAY_POST_TYPES_VERSION );
		}
	}

	/**
	 * Get DPT render for preview in admin page.
	 *
	 * @since 2.6.0
	 */
	public function get_dpt_preview() {
		check_ajax_referer( 'dpt-admin-ajax-nonce', 'security' );
		$args = isset( $_POST['data'] ) ? Security::escape_all( wp_unslash( $_POST['data'] ) ) : false;
		if ( false === $args || ! is_array( $args ) ) {
			echo wp_json_encode( array(
				'error' => __( 'Invalid data provided', 'display-post-types' ),
			) );
			wp_die();
		}
		ob_start();
		Display::init( $args );
		$content = ob_get_clean();
		$dpt = Instance_Counter::get_instance();
		echo wp_json_encode( array(
			'markup' => $content,
			'instances' => $dpt->get_script_data(),
		) );
		wp_die();
	}

	/**
	 * Get DPT form to generate the shortcode on the admin page.
	 *
	 * @since 2.6.0
	 */
	public function get_shortcode_form() {
		check_ajax_referer( 'dpt-admin-ajax-nonce', 'security' );
		$shcode_gen     = ShortCodeGen::get_instance();
		$shortcode_list = $shcode_gen->shortcode_settings;
		$instance       = empty( $shortcode_list ) || ! is_array( $shortcode_list ) ? 0 : max( array_keys( $shortcode_list ) ) + 1;
		ob_start();
		$shcode_gen->form( $instance );
		$form = ob_get_clean();
		echo wp_json_encode( array(
			'form'     => $form,
			'instance' => $instance,
		) );
		wp_die();
	}

	/**
	 * Get DPT form to generate the shortcode on the admin page.
	 *
	 * @since 2.6.0
	 */
	public function create_new_shortcode() {
		check_ajax_referer( 'dpt-admin-ajax-nonce', 'security' );
		$args = isset( $_POST['data'] ) ? Security::sanitize_all( wp_unslash( $_POST['data'] ) ) : false;
		$inst = isset( $_POST['instance'] ) ? absint(wp_unslash( $_POST['instance'] )) : false;
		if ( false === $args || false === $inst ) {
			echo wp_json_encode( array(
				'error'     => __( 'Shortcode data not provided correctly.', 'display-post-types' ),
			) );
			wp_die();
		}
		$shcode_gen     = ShortCodeGen::get_instance();
		$shortcode_list = $shcode_gen->shortcode_settings;
		$shortcode_list[ $inst ] = $args;
		$shcode_gen->shortcode_settings = $shortcode_list;
		$shcode_gen->save();
		echo wp_json_encode( array(
			'success' => __( 'Shortcode created successfully.', 'display-post-types' ),
		) );
		wp_die();
	}

	/**
	 * Get DPT form to generate the shortcode on the admin page.
	 *
	 * @since 2.6.0
	 */
	public function load_shortcode() {
		check_ajax_referer( 'dpt-admin-ajax-nonce', 'security' );
		$instance = isset( $_POST['instance'] ) ? absint( wp_unslash( $_POST['instance'] ) ) : false;
		if ( false === $instance ) {
			echo wp_json_encode( array(
				'error' => __( 'Invalid data provided', 'display-post-types' ),
			) );
			wp_die();
		}
		$shcode_gen     = ShortCodeGen::get_instance();
		$shortcode_list = $shcode_gen->shortcode_settings;
		$args = isset( $shortcode_list[ $instance ] ) ? $shortcode_list[ $instance ] : array();
		ob_start();
		Display::init( $args );
		$preview = ob_get_clean();
		ob_start();
		$shcode_gen->form( $instance );
		$form = ob_get_clean();
		$dpt = Instance_Counter::get_instance();
		echo wp_json_encode( array(
			'form'      => $form,
			'preview'   => $preview,
			'instance'  => $instance,
			'instances' => $dpt->get_script_data(),
		) );
		wp_die();
	}

	/**
	 * Delete already generated DPT shortcode from the admin page.
	 *
	 * @since 2.6.0
	 */
	public function delete_shortcode() {
		check_ajax_referer( 'dpt-admin-ajax-nonce', 'security' );
		$instance = isset( $_POST['instance'] ) ? absint( wp_unslash( $_POST['instance'] ) ) : false;
		if ( false === $instance ) {
			echo wp_json_encode( array(
				'error' => __( 'Invalid data provided', 'display-post-types' ),
			) );
			wp_die();
		}
		$shcode_gen     = ShortCodeGen::get_instance();
		$shortcode_list = $shcode_gen->shortcode_settings;
		if ( isset( $shortcode_list[ $instance ] ) ) {
			unset( $shortcode_list[ $instance ] );
			$shcode_gen->shortcode_settings = $shortcode_list;
			$shcode_gen->save();
		}
		echo wp_json_encode( array(
			'success' => true,
		) );
		wp_die();
	}

	/**
	 * Update already generated DPT shortcode from the admin page.
	 *
	 * @since 2.6.0
	 */
	public function update_shortcode() {
		check_ajax_referer( 'dpt-admin-ajax-nonce', 'security' );
		$args = isset( $_POST['data'] ) ? Security::sanitize_all( wp_unslash( $_POST['data'] ) ) : false;
		$inst = isset( $_POST['instance'] ) ? absint(wp_unslash( $_POST['instance'] )) : false;
		$shcode_gen     = ShortCodeGen::get_instance();
		$shortcode_list = $shcode_gen->shortcode_settings;
		if ( false === $args || false === $inst || ! isset( $shortcode_list[ $inst ] ) ) {
			echo wp_json_encode( array(
				'error'     => __( 'Shortcode data not provided correctly.', 'display-post-types' ),
			) );
			wp_die();
		}
		$shortcode_list[ $inst ] = $args;
		$shcode_gen->shortcode_settings = $shortcode_list;
		$shcode_gen->save();
		echo wp_json_encode( array(
			'success' => __( 'Shortcode updated successfully.', 'display-post-types' ),
		) );
		wp_die();
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
