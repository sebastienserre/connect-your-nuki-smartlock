<?php
class testApi extends WP_UnitTestCase {

	public $api;

	public function set_up(){
		parent::set_up();
		$options = get_option( 'nukiwp__settings' );
		$options = array(
			'apikey' => 'a8ca802281b6d89bcef356f2b5748801948d1e916a58e9c096c4420db226693d5a8206cdb90d3683',
			'smartlock-managed' => '17985114732',

		);
		update_option( 'nukiwp__settings', $options );
		$this->api = new Nuki\API\Api();

	}

	public function test_get_apikey(){

		$apikey = $this->api->get_apikey();

		\PHPUnit\Framework\assertIsString($apikey );

	}

	public function  test_get_smartlock_id(){
		$smartlock_id = $this->api->get_smartlock_id();
		\PHPUnit\Framework\assertIsString( $smartlock_id );
	}

	public function test_get_smartlcok_detail(){
		$smartlock_id = $this->api->get_smartlock_id();
		$details = $this->api->get_smartlock_details( $smartlock_id );

		\PHPUnit\Framework\assertIsArray( $details );
		\PHPUnit\Framework\assertArrayHasKey( 'smartlockId', $details );
	}

	Public function test_api_call(){
		$args = array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $this->api->get_apikey(),
			),
		);

		$smartlock_id = $this->api->get_smartlock_id();
		$url     = $this->api->remote_url . '/smartlock/' . $smartlock_id;

		//case get
		$result = wp_remote_get( $url, $args );
		$return_code = (int) wp_remote_retrieve_response_code( $result );

		\PHPUnit\Framework\assertIsInt( $return_code );
		\PHPUnit\Framework\assertSame( 200, $return_code );

		//case post
		$url_args   = array(
			'url'    => $this->api->remote_url,
			'tool'   => 'smartlock',
			'id'     => $this->api->get_smartlock_id(),
			'action' => 'action/lock',
		);
		$url    = implode( '/', $url_args );
		$result = wp_remote_request( $url, $args );
		$return_code = (int) wp_remote_retrieve_response_code( $result );
		error_log( $return_code);
		\PHPUnit\Framework\assertIsArray( $result );
		\PHPUnit\Framework\assertSame( 200, $return_code );
	}
}