<?php
class testApi extends WP_UnitTestCase {

	public $api;
	public $options;
	public $smartlock_id;
	public $apikey;

	public function set_up(){
		parent::set_up();
		$this->options = array(
			'apikey' => 'a8ca802281b6d89bcef356f2b5748801948d1e916a58e9c096c4420db226693d5a8206cdb90d3683',
			'smartlock-managed' => '17985114732',

		);
		update_option( 'nukiwp__settings', $this->options );
		$this->api = new Nuki\API\Api();
		$this->smartlock_id = '17985114732';

	}

	public function test_get_apikey(){

		$apikey = $this->api->get_apikey();

		\PHPUnit\Framework\assertIsString($apikey );
		\PHPUnit\Framework\assertSame('a8ca802281b6d89bcef356f2b5748801948d1e916a58e9c096c4420db226693d5a8206cdb90d3683', $apikey );

	}

	public function  test_get_smartlock_id(){
		$smartlock_id = $this->api->get_smartlock_id();
		\PHPUnit\Framework\assertIsString( $smartlock_id );
	}

	public function test_get_smartlcok_detail(){
		$details = $this->api->get_smartlock_details( $this->smartlock_id );
		\PHPUnit\Framework\assertIsArray( $details );
		\PHPUnit\Framework\assertArrayHasKey( 'smartlockId', $details );
	}

	public function test_api_call_wrong_apikey(){
		$args = array(
			'headers' => array(
				'Authorization' => 'Bearer ' . 'wrong-apikey',
			),
		);

		$url     = $this->api->remote_url . '/smartlock/' . $this->smartlock_id;

		//case get
		$result = wp_remote_get( $url, $args );
		$return_code = (int) wp_remote_retrieve_response_code( $result );

		\PHPUnit\Framework\assertIsInt( $return_code );
		\PHPUnit\Framework\assertIsBool( ! is_bool( $return_code ) );
	}

	public function test_api_call(){
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
		$result = wp_remote_post( $url, $args );
		$return_code = (int) wp_remote_retrieve_response_code( $result );
		\PHPUnit\Framework\assertIsArray( $result );
		\PHPUnit\Framework\assertSame( 204, $return_code );

		//case put
		$url = $this->api->remote_url . '/smartlock/' . $this->api->get_smartlock_id() . '/auth';
		$body   = array(
			'name'             => 'pintest',
			'allowedFromDate'  => time(),
			'allowedUntilDate' => time(),
			'allowedWeekDays'  => 127,
			'allowedFromTime'  => 0,
			'allowedUntilTime' => 0,
			'accountUserId'    => 0,
			'type'             => 13,
			'code'             => '541236',
		);
		$arg                             = array(
			'body'   => wp_json_encode( $body ),
			'method' => 'PUT',
			'accept' => 'application/json',
		);
		$args['headers']['Content-Type'] = 'application/json';
		$args = wp_parse_args( $arg, $args );
		$result = wp_remote_request( $url, $args );
		\PHPUnit\Framework\assertIsArray( $result );
		\PHPUnit\Framework\assertSame( 204, $return_code );
	}

	public function test_save_settings( ){
		$options = get_option( 'nukiwp__settings' );
		$options['test'] = '1';
		$result = $this->api->save_settings( $options );

		\PHPUnit\Framework\assertTrue( is_bool( $result ) );
	}

	public function test_state(){
		$result = $this->api->get_state( $this->api->get_smartlock_id());
		\PHPUnit\Framework\assertIsInt( $result );
	}

	public function test_state_msg(){
		$result = $this->api->state( 0, 0 );
		\PHPUnit\Framework\assertIsString( $result );
		\PHPUnit\Framework\assertNotEmpty( $result );
	}
}