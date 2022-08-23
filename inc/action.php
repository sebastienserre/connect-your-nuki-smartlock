<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

add_action( 'admin_init', 'nuki_action' );
function nuki_action() {

	if ( ! empty( $_GET['nuki-action'] ) ) {
		$nuki = new \Nuki\API\api();
		switch ( $_GET['nuki-action'] ) {
			case 'lock':
				defaut:
				$nuki->lock();
				break;
			case 'unlock':
				$nuki->unlock();
				break;
		}
	}
}

// https://stackoverflow.com/questions/26372487/php-if-an-hour-is-between-two-other-hours