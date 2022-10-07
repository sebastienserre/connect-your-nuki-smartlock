<?php
/** Actions.
 *
 * @package Connect-your-nuki-smartlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

/**
 * @see https://thevaluable.dev/php-datetime-create-compare-format/#comparing-datetimes
 */
add_action( 'nuki_cron_hook', 'nukiwp_cron_check' );

if ( ! function_exists( 'nukiwp_cron_check' ) ) {
	/**
	 * Will check on every interval if the door is locked.
	 *
	 * @return void
	 */
	function nukiwp_cron_check() {
		$nuki     = new \Nuki\API\Api();
		$settings = $nuki->get_settings();

		// bail if feature not activated.
		if ( empty( $settings['autolock_activated'] ) || '1' !== $settings['autolock_activated'] || empty( $settings['smartlock-managed'] ) ) {
			return;
		}

		$start = $settings['start-autolock'];
		$end   = $settings['end-autolock'];

		$timezone = wp_timezone();

		$start_time   = DateTime::createFromFormat( 'G:i', $start, $timezone );
		$end_time     = DateTime::createFromFormat( 'G:i', $end, $timezone );
		$current_time = new DateTime( 'now', $timezone );

		// to avoid difficulties with day change. I'm checking if I'm outside the day part.
		if ( $current_time >= $start_time || $current_time <= $end_time ) {
			$state = $nuki->get_state( $settings['smartlock-managed'] );
			if ( 3 === $state ) {
				$nuki->lock( $settings['smartlock-managed'] );
			}
		}
	}
}
