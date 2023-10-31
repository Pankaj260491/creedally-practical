<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://creedally.com
 * @since      1.0.0
 *
 * @package    Api_Integration
 * @subpackage Api_Integration/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Api_Integration
 * @subpackage Api_Integration/public
 * @author     Creedally <pankj26@gmail.com>
 */
class Api_Integration_Public {
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Api_Integration_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Api_Integration_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/api-integration-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Api_Integration_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Api_Integration_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/api-integration-public.js', array( 'jquery' ), $this->version, false );
		$localize_items = array(
			'root'  => esc_url_raw( rest_url() ),
			'nonce' => wp_create_nonce( 'wp_rest' ),
		);
		wp_localize_script(
			$this->plugin_name,
			'wpApiSettings',
			$localize_items
		);
	}

	/**
	 * Register new endpoint (URL) for My Account page.
	 *
	 * @return void
	 */
	public function custom_api_integration_endpoint() {
		add_rewrite_endpoint( 'custom-api-tab', EP_ROOT | EP_PAGES );
	}

	/**
	 * Add new query var.
	 *
	 * @param  mixed $vars query var.
	 * @return array
	 */
	public function custom_api_integration_query_vars( $vars ) {
		$vars[] = 'custom-api-tab';
		return $vars;
	}

	/**
	 * Add a new custom tab to My Account.
	 *
	 * @param  mixed $items An array of menu items.
	 * @return array
	 */
	public function custom_api_integration_add_my_account_tab( $items ) {
		$items['custom-api-tab'] = 'Custom API Data';
		return $items;
	}


	/**
	 * Display the custom tab content.
	 *
	 * @return void
	 */
	public function custom_api_integration_display_custom_tab() {

		if ( is_user_logged_in() ) {

			$user_id = get_current_user_id();
			echo '<div class="user-tab-content"><h2>' . esc_html__( 'Custom Integration Preferences', 'api-integration' ) . '</h2>';
			echo '<div class="api-message"></div>';
			echo '<form id="user-settings-form">';
			echo '<div class="form-label"><label for="user_preferences">Enter your preferences:</label></div>';
			echo '<div class="form-inner"><input type="text" id="user_preferences" name="user_preferences" value="">';
			echo '<div class="error"></div><input type="hidden" id="user_id" name="user_id" value="' . esc_attr( $user_id ) . '">';
			echo '<button id="fetch-data-button">' . esc_html__( 'Submit', 'api-integration' ) . '</button>';
			echo '<div id="loading"><img src="' . esc_url( plugin_dir_url( __FILE__ ) ) . '/images/ajax-loader.gif"></div></div></form>';

			$user_data = Data_Fetch::fetch_and_cache_data( $user_id );

			if ( ! empty( $user_data ) && is_array( $user_data ) ) {
				echo '<div class="userdata"><h3>' . esc_html__( 'User Preferences', 'api-integration' ) . '</h3><ul>';
				foreach ( $user_data as $value ) {
					echo '<li>' . esc_html( $value ) . '</li>';
				}
				echo '</ul></div>';
			}
			echo '</div>';
		} else {
			echo '<p>' . esc_html__( 'You must be logged in to access this page.', 'api-integration' ) . '</p>';
		}
	}

	/**
	 * Register custom api routes.
	 *
	 * @return void
	 */
	public function custom_api_routes() {
		register_rest_route(
			'customapi/v1',
			'/userdata',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'get_users_data_callback' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Get users data callback.
	 *
	 * This function handles the API request to update user preferences and
	 * responds with the result of the operation.
	 *
	 * @param  WP_REST_Request $request The API Request data.
	 * @return WP_REST_Response The response data.
	 */
	public function get_users_data_callback( $request ) {

		$data = $request->get_json_params();

		if ( isset( $data['nonce'] ) && wp_verify_nonce( $data['nonce'], 'wp_rest' ) ) {

			if ( isset( $data['user_preferences'] ) && ! empty( $data['user_preferences'] ) && isset( $data['user_id'] ) && ! empty( $data['user_id'] ) ) {

				$user_preferences       = sanitize_text_field( $data['user_preferences'] );
				$user_preferences_array = explode( ',', $user_preferences );
				$user_id                = sanitize_text_field( $data['user_id'] );

				$user_array = get_user_meta( $user_id, 'user_preferences', true );

				if ( ! is_array( $user_array ) ) {
					$user_array = array();
				}

				sort( $user_preferences_array );

				update_user_meta( $user_id, 'user_preferences', $user_preferences_array );

				set_transient( 'custom_api_data_' . $user_id, $user_preferences_array, 3600 );

				$response_data = array(
					'message'          => esc_html__( 'Data received and processed successfully.', 'api-integration' ),
					'user_preferences' => $user_preferences_array,
				);

			} else {

				$response_data = array(
					'message' => esc_html__( 'Invalid request data.', 'api-integration' ),
				);

			}
		} else {

			$response_data = array(
				'message' => esc_html__( 'Invalid nonce.', 'api-integration' ),
			);
		}

		return rest_ensure_response( $response_data );
	}

	/**
	 * Register User Widget.
	 *
	 * @return void
	 */
	public function user_register_widget() {
		register_widget( 'User_Widget' );
	}
}
require_once plugin_dir_path( __FILE__ ) . 'class-data-fetch.php';
require_once plugin_dir_path( __FILE__ ) . 'class-user-widget.php';
