<?php

namespace Nuki\API;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

class api {

	private $apikey;
	private $settings;
	public $remote_url;
	private $smartlockID;

	public function init(){
		//$this->define_variables();
	}

	public function __construct(){
		$this->settings = \get_option( 'nukiwp__settings' );
		$this->apikey = $this->settings['apikey'];
		$this->remote_url = 'https://api.nuki.io';
		$this->smartlockID = $this->get_smartlock_id();
	}

	public function get_apikey(){
		return $this->apikey;
	}

	public function get_settings(){
		return $this->settings;
	}

	public function api_call( $url, $method='get' ){
		$args = array(
			'headers'     => array(
				'Authorization' => 'Bearer ' . $this->apikey,
			),
		);

		switch ( $method ) {
			case 'get':
			default:
			$result = wp_remote_get( $url, $args );
			if ( 200 === (int) wp_remote_retrieve_response_code( $result ) ) {
				$body = wp_remote_retrieve_body( $result );
				$result = json_decode( $body, true );
			}
			break;
			case 'post':
				$result = wp_remote_post( $url, $args );
				break;
		}
		if ( ! empty( $result ) ) {
			return $result;
		} else {
			return false;
		}

	}

	public function get_smartlock(){
		$url = $this->remote_url . '/smartlock';
		$results = $this->api_call( $url, 'get' );
		return $results;
	}

	public function get_smartlock_id(){
		$settings = $this->settings;
		$this->smartlockID = $settings['smartlock-managed'];
		return $this->smartlockID;
	}

	public function lock(){
		$args = array(
			'url' => $this->remote_url,
			'tool' => 'smartlock',
			'id' => $this->smartlockID,
			'action' => 'action/lock',
		);
		$url = implode( '/', $args );
		$result = $this->api_call( $url, 'post' );
		return $result;
	}

	public function unlock(){
		$args = array(
			'url' => $this->remote_url,
			'tool' => 'smartlock',
			'id' => $this->smartlockID,
			'action' => 'action/unlock',
		);
		$url = implode( '/', $args );
		$result = $this->api_call( $url, 'post' );
		return $result;
	}

	public function get_state(){
		$smartlocks = $this->get_smartlock();
		foreach ( $smartlocks as $smartlock ){
			$state = $smartlock['state']['state'];
		}
		return $state;
	}

	/**
	 * @see https://developer.nuki.io/t/web-api-example-manage-pin-codes-for-your-nuki-keypad/54
	 */
	public function generate_pin( $pin_name ){
		$size = 6;
		$i = 1;
		$pin = '';
		while ( $i <= $size ) {
			if ( 1 === $i || 2 === $i ) {
				$digit = rand( 3, 9, );
				$pin .= $digit;
			} else {
				$digit = rand( 1, 9, );
				$pin .= $digit;
			}
			$i++;
		}
		$pin = array( $pin_name => $pin );
		return $pin;
	}
}
