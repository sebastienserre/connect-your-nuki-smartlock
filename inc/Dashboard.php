<?php
/**
 * Dashboard Widget.
 *
 * @package Connect-your-nuki-smartlock
 */

namespace Nuki\Dashboard;

use Nuki\API\api;
use function Nuki\Bookings\nukiwp_api;

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
		add_action( 'admin_init', array( $this, 'action' ) );
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

		$smartlocks = $nuki->get_smartlocks();
		$options = get_option( 'nukiwp__settings' );
		?>
		<div class="nuki_dashboard">
			<?php
			foreach ( $smartlocks as $smartlock ) {
				$data = $smartlock;
				// Generate classname for Battery level.
				$battery_state = 'ok';
				if ( $data['state']['batteryCritical'] ) {
					$battery_state = 'critical';
				}

				$generate_link = add_query_arg(
					array(
						'action'   => 'generate-pin',
						'_wpnonce' => wp_create_nonce( 'action' ),
						'id' => $smartlock['smartlockId'],

					),
					admin_url( '/' )
				);
				$action        = 'lock';
				$lock_state    = $nuki->state( $data['state']['state'], $data['type'] );
				if ( 1 === $data['state']['state'] &&  4 === $data['type'] ){
					$action = 'unlock';
				}
				$action_link = add_query_arg(
					array(
						'action'   => $action,
						'id' => $smartlock['smartlockId'],
						'_wpnonce' => wp_create_nonce( 'action' ),

					),
					admin_url( '/' )
				);
				$allowed_tags = array(
					'span' => array(),
					'a'    => array(
						'href' => array(),
					),
				);
				?>

			<h3>
				<?php
				/* translators: %1$s is the smartlock name defined by customer */
				printf( esc_attr__( 'Smartlock managed: %1$s ', 'connect-your-nuki-smartlock' ), esc_attr( $data['name'] ) );
				?>
			</h3>
			<p>
				<?php
				/* translators: %1$s is the smartlock ID */
				printf( wp_kses( __( '<span>Id:</span> %1$s ', 'connect-your-nuki-smartlock' ), $allowed_tags ), esc_attr( $smartlock['smartlockId'] ) );
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
				printf( wp_kses( __( '<a href="%2$s"><span>State:</span> %1$s </a>', 'connect-your-nuki-smartlock' ), $allowed_tags ), esc_attr( $lock_state ), esc_url( $action_link ) );
				?>
			</p>
				<?php
				if ( $data['config']['keypadPaired'] || $data['config']['keypad2Paired'] ) {
					?>
				<p>
					<a href="<?php echo esc_url( $generate_link ); ?>">
						<?php esc_html_e( 'Generate a pincode (valid 24h)', 'connect-your-nuki-smartlock' ); ?>
					</a>
					<?php
					if ( ! empty( $options['ondemand-pinlist'][ $smartlock['smartlockId'] ] ) ) {
						?>
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
						foreach ( $options['ondemand-pinlist'][ $smartlock['smartlockId'] ] as $name => $pin ) {
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
                                    'id' => $smartlock['smartlockId'],
								),
								admin_url( '/' )
							);
							?>
                            <td class="nuki-dashboard-actions">
                                <ul>
                                    <li>
                                        <a href="<?php echo esc_url( $delete_link ); ?>">
											<?php esc_html_e( 'Delete', 'connect-your-nuki-smartlock' ); ?>
                                        </a>
                                    </li>
                                </ul>
                            </td>
						</tr>
							<?php
						}
						?>
				</table>
						<?php
					}
					?>
				</p>
					<?php
				}
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
		if ( ! empty( $_GET['action'] ) && 'generate-pin' === $_GET['action'] && ! empty( $_GET['id'] ) ) {
			$user                                     = wp_get_current_user();
			$username                                 = $user->user_login;
			$options                                  = get_option( 'nukiwp__settings' );
			$pin_name                                 = $username . ' ' . wp_date( 'd/m/Y H\hi s', time() );
			$pin_code                                 = $nuki->generate_pin();
			$options['ondemand-pinlist'][ sanitize_key( $_GET['id'] ) ][ $pin_name ] = $pin_code;
			$pin_data = array(
				'name'    => $pin_name,
				'start'   => wp_date( 'c', time() ),
				'end'     => wp_date( 'c', time() + 24 * HOUR_IN_SECONDS ),
				'pincode' => $pin_code,
			);
			$nuki->send_pin_to_keypad( $pin_data, sanitize_key( $_GET['id'] ) );
			update_option( 'nukiwp__settings', $options );
            $this->redirect_admin( 0 );
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
		if ( isset( $options['ondemand-pinlist'][ sanitize_key( $_GET['id'] ) ][ sanitize_text_field( $_GET['pin_name'] ) ] ) ) {
			unset( $options['ondemand-pinlist'][ sanitize_key( $_GET['id'] ) ][ sanitize_text_field( $_GET['pin_name'] ) ] );
			update_option( 'nukiwp__settings', $options );
		}
        $this->redirect_admin( 0 );
	}

	/**
	 * Lock or unlock the smartlock from the Dashboard.
	 *
	 * @return bool
	 */
	public function action() {
		$nukiwp_api = new Api();
		if ( empty( $_GET['action'] ) ) {
			return false;
		}
		if ( ! empty( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'action' ) && empty( $_GET['action'] ) || 'unlock' === $_GET['action'] || 'lock' === $_GET['action'] && ! empty( $_GET['id'] ) ) {
			if ( 'lock' === $_GET['action'] ) {
				$nukiwp_api->lock( sanitize_key( $_GET['id'] ) );
			}
			if ( 'unlock' === $_GET['action'] ) {
				$nukiwp_api->unlock( sanitize_key( $_GET['id'] ) );
			}
            $this->redirect_admin();
		}
		return false;
	}

    public function redirect_admin( $delay=4, $url = '' ){
	    if ( empty( $url ) ){
            $url = admin_url();
	    }
	    header("Refresh: $delay; url=$url");
    }

}

$dashboard = new Dashboard();
$dashboard->init();
