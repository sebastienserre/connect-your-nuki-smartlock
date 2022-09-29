<?php

namespace Nuki\Dashboard;

use Nuki\API\api;

class Dashboard {

	public $smartlock_id;

	public function init(){
		add_action( 'wp_dashboard_setup', array( $this, 'dashboard_widget' ) );
        add_action( 'admin_init', array( $this, 'generate_pincode' ) );
        add_action( 'admin_init', array( $this, 'delete_pincode' ) );
	}

	public function dashboard_widget() {
		global $wp_meta_boxes;
		add_meta_box( 'nuki_smartlock', 'Nuki Smartlock', array( $this, 'render_dashboard'), 'dashboard', 'side', 'high' );
	}

	public function render_dashboard(){
		$nuki = new api();
		$data = $nuki->get_smartlock_details( $nuki->get_smartlock_id() );
		$options = get_option( 'nukiwp__settings' );

        // Generate classname for Battery level.
        $batteryState = 'ok';
        if ( $data["state"]["batteryCritical"] ){
            $batteryState = 'critical';
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
				printf( __( 'Smartlock managed: %1$s ', 'connect-your-nuki-smartlock' ), $data["name"] );
				?>
            </h3>
            <p>
				<?php
				/* translators: %1$s is the smartlock ID */
				printf( __( '<span>Id:</span> %1$s ', 'connect-your-nuki-smartlock' ), $nuki->get_smartlock_id() );
				?>
            </p>
            <p class="battery-<?php echo $batteryState ?>">
				<?php
				/* translators: %1$s is the level in digit 0->100 */
				printf( __( '<span>Battery level:</span> %1$s%2$s ', 'connect-your-nuki-smartlock' ), $data["state"]["batteryCharge"], '%' );
				?>
            </p>
            <p class="smartlock-<?php echo $nuki->state( $data["state"]["state"], $data["type"] );?>">
		        <?php
		        /* translators: %1$s is the smartlock state */
		        printf( __( '<span>State:</span> %1$s ', 'connect-your-nuki-smartlock' ), $nuki->state( $data["state"]["state"], $data["type"] ) );
		        ?>
            </p>
            <p>
                <a href="<?php echo $generate_link; ?>"><?php _e( 'Generate a pincode', 'connect-your-nuki-smartlock' ); ?></a>
            <h4>
		        <?php
		        /* translators: %1$s is the smartlock name defined by customer */
		        printf( __( 'List of On-Demand Pin generated ', 'connect-your-nuki-smartlock' ), $data["name"] );
		        ?>
            </h4>
                <table>
                <tr>
                    <th><?php _e('Name', 'connect-your-nuki-smartlock' )?></th>
                    <th><?php _e('Pincode', 'connect-your-nuki-smartlock' )?></th>
                    <th><?php _e('Actions', 'connect-your-nuki-smartlock' )?></th>
                </tr>
                <?php
                foreach ( $options['ondemand-pinlist'] as $name => $pin ){
                 ?>
                    <tr>
                        <td><?php echo $name; ?></td>
                        <td><?php echo $pin; ?></td>
                        <?php
                        $delete_link = add_query_arg( array(
	                        'action'   => 'delete-pin',
	                        '_wpnonce' => wp_create_nonce( 'action' ),
                            'pin_name' => $name,
                        ),
	                        admin_url( '/' ),
                        );
                        ?>
                        <td><a href="<?php echo $delete_link ?>"><?php _e('Delete', 'connect-your-nuki-smartlock' )?></a></td>
                    </tr>
                        <?php
                }
                ?>
            </table>
            </p>
        </div>
<?php
	}

	function generate_pincode() {
		$nuki = new api();
		if ( empty( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'action' ) ) {
			return;
		}
		if ( ! empty( $_GET['action'] ) && 'generate-pin' === $_GET['action'] ) {
			$user                                                                               = wp_get_current_user();
			$username                                                                           = $user->user_login;
			$options                                                                            = get_option( 'nukiwp__settings' );
			$options['ondemand-pinlist'][ $username . ' ' . wp_date( 'd/m/Y H\hi s', time() ) ] = $nuki->generate_pin();
			update_option( 'nukiwp__settings', $options );
		}

	}

	function delete_pincode() {
		if ( empty( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'action' ) ) {
			return;
		}
		if ( empty( $_GET['action'] ) || 'delete-pin' !== $_GET['action'] || empty( $_GET['pin_name'] )  ) {
			return;
		}
		$options = get_option( 'nukiwp__settings' );
        if ( isset( $options['ondemand-pinlist'][esc_attr( $_GET['pin_name'])] ) ) {
	        unset( $options['ondemand-pinlist'][ esc_attr( $_GET['pin_name'] ) ] );
	        update_option( 'nukiwp__settings', $options );
        }

	}
}
$dashboard = new Dashboard();
$dashboard->init();
