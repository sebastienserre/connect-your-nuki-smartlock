<?php
/**
 * Nuki API.
 *
 * @package Connect-your-nuki-smartlock
 * @see https://api.nuki.io/
 */

namespace Nuki\API;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

if ( ! class_exists( 'Api' ) ) {

	/**
	 * Easy manage Nuki Smartlock from WordPress
	 */
	class Api {

		/**
		 * NukiWeb API Key
		 *
		 * @var mixed
		 */
		private $apikey;

		/**
		 * WordPress Settings
		 *
		 * @var array
		 */
		private $settings;

		/**
		 * URL to connect to
		 *
		 * @var string
		 */
		public $remote_url;

		/**
		 * Managed Smartlock ID.
		 *
		 * @var false|mixed
		 */
		private $smartlock_id;

		/**
		 * PHP Constructor
		 */
		public function __construct() {
			$this->settings    = \get_option( 'nukiwp__settings' );
			$this->apikey      = $this->settings['apikey'];
			$this->remote_url  = 'https://api.nuki.io';
			$this->smartlock_id = $this->get_smartlock_id();
		}

		/**
		 * Get NUki API Key.
		 *
		 * @return mixed
		 */
		public function get_apikey() {
			return $this->apikey;
		}

		/**
		 * Get WP settings.
		 *
		 * @return array
		 */
		public function get_settings() {
			return $this->settings;
		}

		/**
		 * Methods to easily make call to Nuki API.
		 *
		 * @param string $url NukiWeb API URL.
		 * @param string $method can be 'get', 'post, 'put', 'delete'.
		 * @param array  $body request parameters.
		 *
		 * @return array|false|mixed|\WP_Error
		 */
		public function api_call( $url, $method = 'get', $body = array() ) {
			$args = array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $this->apikey,
				),
			);

			switch ( $method ) {
				case 'get':
				default:
					$result = wp_remote_get( $url, $args );
					if ( 200 === (int) wp_remote_retrieve_response_code( $result ) ) {
						$body   = wp_remote_retrieve_body( $result );
						$result = json_decode( $body, true );
					}
					break;
				case 'post':
					$result = wp_remote_post( $url, $args );
					break;
				case 'put':
					$arg                             = array(
						'body'   => wp_json_encode( $body ),
						'method' => 'PUT',
						'accept' => 'application/json',
					);
					$args['headers']['Content-Type'] = 'application/json';
					$args                            = wp_parse_args( $arg, $args );

					$result = wp_remote_request( $url, $args );
					break;
			}
			if ( ! empty( $result ) ) {
				return $result;
			} else {
				return false;
			}

		}

		/**
		 * Get Smartlock data.
		 *
		 * @return array|false|mixed|\WP_Error
		 */
		public function get_smartlock() {
			$url     = $this->remote_url . '/smartlock';
			$results = $this->api_call( $url, 'get' );

			return $results;
		}

		/**
		 * Get the smartlock ID.
		 *
		 * @return false|mixed
		 */
		public function get_smartlock_id() {
			$settings = $this->settings;
			if ( empty( $settings['smartlock-managed'] ) ) {
				return false;
			}
			$this->smartlock_id = $settings['smartlock-managed'];

			return $this->smartlock_id;
		}

		/**
		 * Get Smartlock detailled data.
		 *
		 * @param string $smartlock_id see get_smartlock_id().
		 *
		 * @return array|false|mixed|\WP_Error
		 */
		public function get_smartlock_details( $smartlock_id ) {
			$url     = $this->remote_url . '/smartlock/' . $smartlock_id;
			$results = $this->api_call( $url, 'get' );
			set_transient( 'nukiData[' . $smartlock_id . ']', $results, HOUR_IN_SECONDS * 24 );


			return $results;
		}

		/**
		 * Action to lock the smartlock.
		 *
		 * @return array|false|mixed|\WP_Error
		 */
		public function lock() {
			$args   = array(
				'url'    => $this->remote_url,
				'tool'   => 'smartlock',
				'id'     => $this->smartlock_id,
				'action' => 'action/lock',
			);
			$url    = implode( '/', $args );
			$result = $this->api_call( $url, 'post' );

			return $result;
		}

		/**
		 * Action to unlock the smartlock.
		 *
		 * @return array|false|mixed|\WP_Error
		 */
		public function unlock() {
			$args   = array(
				'url'    => $this->remote_url,
				'tool'   => 'smartlock',
				'id'     => $this->smartlock_id,
				'action' => 'action/unlock',
			);
			$url    = implode( '/', $args );
			$result = $this->api_call( $url, 'post' );

			return $result;
		}

		/**
		 * Get the smartlock state code by API.
		 *
		 * @return mixed
		 */
		public function get_state() {
			$smartlocks = $this->get_smartlock();
			foreach ( $smartlocks as $smartlock ) {
				$state = $smartlock['state']['state'];
			}

			return $state;
		}

		/**
		 * Generate a pincode. Must be 6 digits [1-9] and not starting by 12.
		 *
		 * @return string
		 * @see https://developer.nuki.io/t/web-api-example-manage-pin-codes-for-your-nuki-keypad/54
		 */
		public function generate_pin() {
			$size = 6;
			$i    = 1;
			$pin  = array(
				'1' => 0,
				'2' => 0,
			);
			while ( $i <= $size ) {
				if ( 1 === $i || 2 === $i ) {
					$pin[ $i ]   = random_int( 3, 9 );
					if ( $pin[1] === $pin[2] ) {
						// rand with exclusion
						in_array( ( $pin[2] = random_int( 3, 9 ) ), array( $pin[1] ) );
					}
				} else {

					$pin[ $i ] = random_int( 1, 9, );
					$b = $i - 1;
					$pin[ $i ] = $this->avoid_twice_same( $pin[ $b ], $pin[ $i ] );
				}
				$i ++;
			}
			ksort( $pin );
			return implode( '', $pin );
		}

		/**
		 * For security reason, avoide to have twice the same digit next to each other.
		 *
		 * @param int $first_pin first digit to compare.
		 * @param int $second_pin second digit to compare.
		 *
		 * @return int
		 */
		public function avoid_twice_same( $first_pin, $second_pin ) {
			if ( $first_pin === $second_pin ) {
				// rand with exclusion
				in_array( ( $second_pin = random_int( 1, 9 ) ), array( $first_pin ) );

				// check if we obtain 2 differents digits
				if ( $first_pin !== $second_pin ) {
					return $second_pin;
				} else {
					// same player play again.
					$this->avoid_twice_same( $first_pin, $second_pin );
				}
			}
			return $second_pin;
		}

		/**
		 * Send pin to keypad. A keypad must be paired with the Smartlock.
		 *
		 * @param array $pin_data array with data needed to create the pin.
		 *
		 * @return array
		 */
		public function send_pin_to_keypad( $pin_data ) {
			$args   = array(
				'url'    => $this->remote_url,
				'tool'   => 'smartlock',
				'id'     => $this->smartlock_id,
				'action' => 'auth',
			);
			$url    = implode( '/', $args );
			$body   = array(
				'name'             => $pin_data['name'],
				'allowedFromDate'  => $pin_data['start'],
				'allowedUntilDate' => $pin_data['end'],
				'allowedWeekDays'  => 127,
				'allowedFromTime'  => 0,
				'allowedUntilTime' => 0,
				'accountUserId'    => 0,
				'type'             => 13,
				'code'             => $pin_data['pincode'],
			);
			$result = $this->api_call( $url, 'put', $body );

			return $result;
		}

		/**
		 * Transform the numeric state code to an human readable state code.
		 *
		 * @param int $code state code sent by API.
		 * @param int $type Smartlock type sent by API.
		 *
		 * @return string|null
		 */
		public function state( $code, $type ) {
			$msg = '';
			switch ( $type ) {
				case 0:
				case 3:
				case 4:
					switch ( $code ) {
						case 0:
							$msg = __( 'uncalibrated', 'connect-your-nuki-smartlock' );
							break;
						case 1:
							$msg = __( 'locked', 'connect-your-nuki-smartlock' );
							break;
						case 2:
							$msg = __( 'unlocking', 'connect-your-nuki-smartlock' );
							break;
						case 3:
							$msg = __( 'unlocked', 'connect-your-nuki-smartlock' );
							break;
						case 4:
							$msg = __( 'locking', 'connect-your-nuki-smartlock' );
							break;
						case 5:
							$msg = __( 'unlatched', 'connect-your-nuki-smartlock' );
							break;
						case 6:
							$msg = __( "unlocked (lock 'n' go)", 'connect-your-nuki-smartlock' );
							break;
						case 7:
							$msg = __( 'unlatching', 'connect-your-nuki-smartlock' );
							break;
						case 254:
							$msg = __( 'motor blocked', 'connect-your-nuki-smartlock' );
							break;
						case 255:
							$msg = __( 'motor blocked', 'connect-your-nuki-smartlock' );
							break;
					}
					break;
				case 2:
					switch ( $code ) {
						case 0:
							$msg = __( 'untrained', 'connect-your-nuki-smartlock' );
							break;
						case 1:
							$msg = __( 'online', 'connect-your-nuki-smartlock' );
							break;
						case 3:
							$msg = __( 'ring to open active', 'connect-your-nuki-smartlock' );
							break;
						case 5:
							$msg = __( 'open', 'connect-your-nuki-smartlock' );
							break;
						case 7:
							$msg = __( 'opening', 'connect-your-nuki-smartlock' );
							break;
						case 253:
							$msg = __( 'boot run', 'connect-your-nuki-smartlock' );
							break;
						case 255:
							$msg = __( 'undefined', 'connect-your-nuki-smartlock' );
							break;
					}
					break;
			}
			return $msg;
		}
	}
}
