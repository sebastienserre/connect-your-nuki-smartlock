<?php
/**
 * Dashboard Widget.
 *
 * @package Connect-your-nuki-smartlock
 */

namespace Nuki\Dashboard;

use Nuki\API\api;

/**
 * This class implement the Widget Dashboard.
 */
class Dashboard {

	/**
	 * @var $smartlock_id
	 */
	public $smartlock_id;

	/**
	 * Initialize Class.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'wp_dashboard_setup', array( $this, 'dashboard_widget' ) );
		add_action( 'admin_init', array( $this, 'generate_pincode' ) );
		add_action( 'admin_init', array( $this, 'delete_pincode' ) );
	}

	/**
	 * Initialize the Dashboard Widget.
	 *
	 * @return void
	 */
	public function dashboard_widget() {
		add_meta_box( 'nuki_smartlock', 'Nuki Smartlock', array( $this, 'render_dashboard' ), 'dashboard', 'side', 'high' );
	}

	/**
	 * Render the Dashboard Widget.
	 *
	 * @return void
	 */
	public function render_dashboard() {
		$nuki = new Api();
		$data = $nuki->get_smartlock_details( $nuki->get_smartlock_id() );
		$options = get_option( 'nukiwp__settings' );

		// Generate classname for Battery level.
		$battery_state = 'ok';
		if ( $data['state']['batteryCritical'] ) {
			$battery_state = 'critical';
		}

		$generate_link = add_query_arg(
			array(
				'action'   => 'generate-pin',
				'_wpnonce' => wp_create_nonce( 'action' ),

			),
			admin_url( '/' ),
		);

		?>
		<div class="nuki_dashboard">
			<h3>
				<?php
				/* translators: %1$s is the smartlock name defined by customer */
				printf( esc_attr__( 'Smartlock managed: %1$s ', 'connect-your-nuki-smartlock' ), esc_attr( $data['name'] ) );
				?>
			</h3>
			<p>
				<?php
				/* translators: %1$s is the smartlock ID */
				printf( wp_kses( '<span>Id:</span> %1$s ', 'connect-your-nuki-smartlock', array( 'span' ) ), esc_attr( $nuki->get_smartlock_id() ) );
				?>
			</p>
			<p class="battery-<?php echo esc_attr( $battery_state ); ?>">
				<?php
				/* translators: %1$s is the level in digit 0->100 */
				printf( wp_kses( '<span>Battery level:</span> %1$s%2$s ', 'connect-your-nuki-smartlock', array( 'span' ) ), esc_attr( $data['state']['batteryCharge'] ), '%' );
				?>
			</p>
			<p class="smartlock-<?php echo esc_attr( $nuki->state( $data['state']['state'], $data['type'] ) ); ?>">
				<?php
				/* translators: %1$s is the smartlock state */
				printf( wp_kses( '<span>State:</span> %1$s ', 'connect-your-nuki-smartlock', array( 'span' ) ), esc_attr( $nuki->state( $data['state']['state'], $data['type'] ) ) );
				?>
			</p>
			<?php
			if ( $data['config']['keypadPaired'] || $data['config']['keypad2Paired'] ) {
				?>
			<p>
				<a href="<?php echo esc_url( $generate_link ); ?>"><?php esc_html_e( 'Generate a pincode (valid 24h)', 'connect-your-nuki-smartlock' ); ?></a>
			<h4>
				<?php
				/* translators: %1$s is the smartlock name defined by customer */
				printf( esc_attr__( 'List of On-Demand Pin generated ', 'connect-your-nuki-smartlock' ), esc_attr( $data['name'] ) );
				?>
			</h4>
				<table>
				<tr>
					<th><?php esc_html_e( 'Name', 'connect-your-nuki-smartlock' ); ?></th>
					<th><?php esc_html_e( 'Pincode', 'connect-your-nuki-smartlock' ); ?></th>
					<th><?php esc_html_e( 'Actions', 'connect-your-nuki-smartlock' ); ?></th>
				</tr>
				<?php
				foreach ( $options['ondemand-pinlist'] as $name => $pin ) {
					?>
					<tr>
						<td><?php echo esc_html( $name ); ?></td>
						<td><?php echo esc_html( $pin ); ?></td>
						<?php
						$delete_link = add_query_arg(
							array(
								'action'   => 'delete-pin',
								'_wpnonce' => wp_create_nonce( 'action' ),
								'pin_name' => $name,
							),
							admin_url( '/' ),
						);
						?>
						<td><a href="<?php echo esc_url( $delete_link ); ?>"><?php esc_html_e( 'Delete', 'connect-your-nuki-smartlock' ); ?></a></td>
					</tr>
						<?php
				}
				?>
			</table>
			</p>
				<?php
			}
			?>
		</div>
		<?php
	}

	/**
	 * Generate a pincode.
	 *
	 * @return void
	 */
	public function generate_pincode() {
		$nuki = new Api();
		if ( empty( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'action' ) ) {
			return;
		}
		if ( ! empty( $_GET['action'] ) && 'generate-pin' === $_GET['action'] ) {
			$user                                     = wp_get_current_user();
			$username                                 = $user->user_login;
			$options                                  = get_option( 'nukiwp__settings' );
			$pin_name                                 = $username . ' ' . wp_date( 'd/m/Y H\hi s', time() );
			$pin_code                                 = $nuki->generate_pin();
			$options['ondemand-pinlist'][ $pin_name ] = $pin_code;
			$pin_data                                 = array(
				'name'    => $pin_name,
				'start'   => wp_date( 'c', time() ),
				'end'     => wp_date( 'c', time() + 24 * HOUR_IN_SECONDS ),
				'pincode' => $pin_code,
			);
			$nuki->send_pin_to_keypad( $pin_data );
			update_option( 'nukiwp__settings', $options );
		}

	}

	/**
	 * Delete a pincode.
	 *
	 * @return void
	 */
	public function delete_pincode() {
		if ( empty( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'action' ) ) {
			return;
		}
		if ( empty( $_GET['action'] ) || 'delete-pin' !== $_GET['action'] || empty( $_GET['pin_name'] ) ) {
			return;
		}
		$options = get_option( 'nukiwp__settings' );
		if ( isset( $options['ondemand-pinlist'][ sanitize_text_field( $_GET['pin_name'] ) ] ) ) {
			unset( $options['ondemand-pinlist'][ sanitize_text_field( $_GET['pin_name'] ) ] );
			update_option( 'nukiwp__settings', $options );
		}

	}
}
$dashboard = new Dashboard();
$dashboard->init();
