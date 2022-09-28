<?php

namespace Nuki\Dashboard;

use Nuki\API\api;

class Dashboard {

	public $smartlock_id;

	public function init(){
		add_action( 'wp_dashboard_setup', array( $this, 'dashboard_widget' ) );
	}

	public function dashboard_widget() {
		global $wp_meta_boxes;
		add_meta_box( 'nuki_smartlock', 'Nuki Smartlock', array( $this, 'render_dashboard'), 'dashboard', 'side', 'high' );
	}

	public function render_dashboard(){
		$nuki = new api();
		$data = $nuki->get_smartlock_details( $nuki->get_smartlock_id() );
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
            <p>
				<?php
				/* translators: %1$s is the level in digit 0->100 */
				printf( __( '<span>Battery level:</span> %1$s%2$s ', 'connect-your-nuki-smartlock' ), $data["state"]["batteryCharge"], '%' );
				?>
            </p>
            <p>
		        <?php
		        /* translators: %1$s is the smartlock state */
		        printf( __( '<span>State:</span> %1$s ', 'connect-your-nuki-smartlock' ), $nuki->state( $data["state"]["state"], $data["type"] ) );
		        ?>
            </p>
        </div>
<?php
	}
}
$dashboard = new Dashboard();
$dashboard->init();
