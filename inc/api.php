<?php

namespace Nuki\API;

class Api {
	private $apiKey;

	public function constructor(){
		$this->apiKey = get_option( 'nukiwp__text_field_0' );
	}

	public function init(){
		var_dump( $this->apiKey );
	}

}
$api = new Api();
$api->init();