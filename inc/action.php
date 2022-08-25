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

/**
 * @see https://thevaluable.dev/php-datetime-create-compare-format/#comparing-datetimes
 */
add_action( 'nuki_cron_hook', 'nuki_cron_check');
//add_action( 'admin_init', 'nuki_cron_check');
function nuki_cron_check() {
	$nuki = new \Nuki\API\api();
	$settings = $nuki->settings;
	$start = $settings['start-autolock'];
	$end = $settings['end-autolock'];

	$timezone = wp_timezone();

	$startTime = DateTime::createFromFormat( 'G:i', $start, $timezone );
	$endTime = DateTime::createFromFormat( 'G:i', $end, $timezone );
	$currentTime = new DateTime( 'now', $timezone );

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
