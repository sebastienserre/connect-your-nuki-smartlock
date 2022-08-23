<?php
/**
 * Plugin Name: Connect your Nuki Smartlock !
 * Plugin URI: https://www.thivinfo.com
 * Description:
 * Author: Sébastien SERRE
 * Author URI: https://thivinfo.com
 * Text Domain: connect-nuki-smartlock
 * Domain Path: /languages/
 * Version: 0.1.0
 **/

namespace Nuki;

class Connect_Nuki_Smartlock {
	public function init(){
		add_action( 'plugins_loaded', array( $this, 'define_constantes' ) );
		add_action( 'plugins_loaded', array( $this, 'load_files' ) );
	}

	public function define_constantes(){
		define( 'NUKIWP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		define( 'NUKIWP_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
		define( 'NUKIWP_PLUGIN_DIR', untrailingslashit( NUKIWP_PLUGIN_PATH ) );
	}

	public function load_files(){
		$files = scandir( NUKIWP_PLUGIN_PATH . 'inc/' );
		foreach ( $files as $file ) {
			if ( is_file( NUKIWP_PLUGIN_PATH . 'inc/' . $file ) ) {
				require NUKIWP_PLUGIN_PATH . 'inc/' . $file;
			}
		}
	}
}
$nuki = new Connect_Nuki_Smartlock();
$nuki->init();