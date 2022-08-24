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

//add_action( 'nuki_cron_hook', 'nuki_cron_check');
add_action( 'admin_init', 'nuki_cron_check');
function nuki_cron_check() {
	$nuki = new \Nuki\API\api();
	$settings = $nuki->settings;
	$start = $settings['start-autolock'];
	$end = $settings['end-autolock'];

	$timezone = wp_timezone();
	$currentTime = wp_date( 'U', time(), $timezone ); // 1661355604 - 24/08/2022 17:40:04

	$start = DateTime::createFromFormat( 'G:i', $start, $timezone ); // $start->date = 2022-08-24 21:00:00.000000
	$start1 = $start->getTimestamp(); // 1661374800 - 24/08/2022 23:00:00
	$startTime = wp_date( 'U', strtotime( $start ), $timezone ); // 1661374800 - 24/08/2022 23:00:00

	$endTime = wp_date( 'U', strtotime( $end ), $timezone );

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
