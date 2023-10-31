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
 * The user data fetch and cache functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Api_Integration
 * @subpackage Api_Integration/public
 * @author     Creedally <pankj26@gmail.com>
 */
class Data_Fetch {
	/**
	 * Fetch and cache data
	 *
	 * @param  mixed $user_id The user id.
	 * @return array
	 */
	public static function fetch_and_cache_data( $user_id ) {

		$data = get_transient( 'custom_api_data' );

		if ( false === $data ) {

			$data = get_user_meta( $user_id, 'user_preferences', true );

			if ( ! is_array( $data ) ) {
				$data = array();
				set_transient( 'custom_api_data_' . $user_id, $data, 3600 );
			}
		}

		return $data;
	}
}

