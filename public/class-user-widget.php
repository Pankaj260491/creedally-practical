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
 * The user widget functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Api_Integration
 * @subpackage Api_Integration/public
 * @author     Creedally <pankj26@gmail.com>
 */
class User_Widget extends WP_Widget {
	/**
	 * User_Widget constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct(
			'user_widget',
			'User Widget',
			array(
				'description' => esc_html__( 'A widget for displaying user data.', 'api-integration' ),
			)
		);
	}
	/**
	 * Widget.
	 *
	 * @param array $args     The Widget arguments.
	 * @param array $instance The Widget instance settings.
	 */
	public function widget( $args, $instance ) {

		$user_id   = get_current_user_id();
		$user_data = Data_Fetch::fetch_and_cache_data( $user_id );

		if ( ! empty( $user_data ) && is_array( $user_data ) ) {
			echo wp_kses_post( $args['before_widget'] );
			echo wp_kses_post( $args['before_title'] ) . esc_html( $instance['title'] ) . wp_kses_post( $args['after_title'] );
			echo '<div class="user_widget_data"><ul>';
			foreach ( $user_data as $value ) {
				echo '<li>' . esc_html( $value ) . '</li>';
			}
			echo '</ul></div>';
			echo wp_kses_post( $args['after_widget'] );
		}

	}

	/**
	 * Outputs the settings form for the Custom Widget.
	 *
	 * @param array $instance The widget settings.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Default Title', 'api-integration' );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php echo esc_html__( 'Title', 'api-integration' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php
	}

}
