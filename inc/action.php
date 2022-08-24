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

add_action( 'nuki_cron_hook', 'nuki_cron_check');
//add_action( 'admin_init', 'nuki_cron_check');
function nuki_cron_check() {

	$wp_timezone = get_option('timezone_string' );
	$currentTime = wp_date( 'U', time() ); // 24/08/2022 07:39:55
	$startTime = wp_date( 'U', strtotime( '21:00' )); // 24/08/2022 23:00:00
	$endTime = wp_date( 'U', strtotime( '08:00' )); //Résultat en date : 24/08/2022 10:00:00

	//to avoid difficulties with day change. I'm checking if I'm outside the day part.
	if ( $currentTime >= $startTime || $currentTime <= $endTime ) {
		$nuki  = new \Nuki\API\api();
		$state = $nuki->get_state();
		if ( 3 === $state) {
				$nuki->lock();
				error_log( wp_date( 'j F Y, G:i') . ': Door locked');
		} else {
			error_log( wp_date( 'j F Y, G:i') . ': Door already locked');
		}
	}
}
