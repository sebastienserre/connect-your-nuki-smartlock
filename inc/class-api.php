<?php

namespace Nuki\API;
class api {

	public $apikey;
	public $settings;
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
}
