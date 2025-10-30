<?php

use Nuki\API\Api;

class testDashboard extends WP_UnitTestCase {

	public $api;
	public $dashboard;

	public function set_up(){
		parent::set_up();
		$options = get_option( 'nukiwp__settings' );
		$options = array(
			'apikey' => '',
			'smartlock-managed' => '17985114732',

		);
		update_option( 'nukiwp__settings', $options );
		$this->api = new Nuki\API\Api();
		$this->dashboard = new Nuki\Dashboard\Dashboard();

	}
	function nukiwp_api() {
		$api = new Api();
		return $api;
	}

	public function test_action(){
		$_GET['_wpnonce'] = wp_create_nonce( 'action' );
		$_GET['action'] = 'lock';
		$api = $this->api;
		$settings = $api->settings;
		$_GET['id'] = $settings['smartlock-managed'];
		$dashboard = $this->dashboard;
		$result = $dashboard->action();
		\PHPUnit\Framework\assertTrue( is_bool( $result ) );
	}
}
