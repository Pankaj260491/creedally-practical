<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://creedally.com
 * @since             1.0.0
 * @package           Api_Integration
 *
 * @wordpress-plugin
 * Plugin Name:       Api Integration
 * Plugin URI:        https://creedally.com
 * Description:       Fetch elements from an API based on user preferences.
 * Version:           1.0.0
 * Author:            Creedally
 * Author URI:        https://creedally.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       api-integration
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Check if WooCommerce is active.
 */
function is_woocommerce_active() {
	return class_exists( 'WooCommerce' );
}

/**
 * Currently plugin version.
 */
define( 'API_INTEGRATION_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-api-integration-activator.php
 */
function activate_api_integration() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-api-integration-activator.php';
	Api_Integration_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-api-integration-deactivator.php
 */
function deactivate_api_integration() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-api-integration-deactivator.php';
	Api_Integration_Deactivator::deactivate();
}

if ( is_woocommerce_active() ) {
	register_activation_hook( __FILE__, 'activate_api_integration' );
	register_deactivation_hook( __FILE__, 'deactivate_api_integration' );
} else {
	add_action( 'admin_notices', 'woocommerce_not_found_notice' );
}

/**
 * Admin notice for WooCommerce not found
 */
function woocommerce_not_found_notice() {
	echo '<div class="error"><p>' . esc_html__( 'Api Integration plugin requires WooCommerce to be installed and active. Please install and activate WooCommerce.', 'api-integration' ) . '</p></div>';
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-api-integration.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_api_integration() {

	$plugin = new Api_Integration();
	$plugin->run();

}
run_api_integration();
