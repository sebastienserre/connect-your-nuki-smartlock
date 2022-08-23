<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

add_action( 'admin_menu', 'nukiwp__add_admin_menu' );
add_action( 'admin_init', 'nukiwp__settings_init' );

function nukiwp__add_admin_menu(  ) {

	add_options_page( 'Connect your Nuki SmartLock', 'Connect your Nuki SmartLock', 'manage_options', 'connect_your_nuki_smartlock', 'nukiwp__options_page' );

}


function nukiwp__settings_init(  ) {

	register_setting( 'pluginPage', 'nukiwp__settings' );

	add_settings_section(
		'nukiwp__pluginPage_section',
		/*__( 'Your section description', 'connect-nuki-smartlock' ),*/
        '',
		/*'nukiwp__settings_section_callback',*/
        '',
		'pluginPage'
	);

	add_settings_field(
		'nukiwp__text_field_0',
		__( 'API Key', 'connect-nuki-smartlock' ),
		'nukiwp__text_field_0_render',
		'pluginPage',
		'nukiwp__pluginPage_section'
	);

	add_settings_field(
		'nuki-open-acion',
		__( 'Open', 'connect-nuki-smartlock' ),
		'nukiwp__open_action',
		'pluginPage',
		'nukiwp__pluginPage_section'
	);


}


function nukiwp__text_field_0_render(  ) {

	$options = get_option( 'nukiwp__settings' );
	?>
	<input type='text' name='nukiwp__settings[nukiwp__text_field_0]' value='<?php echo $options['nukiwp__text_field_0']; ?>'>
	<?php

}


function nukiwp__settings_section_callback(  ) {

	echo __( 'This section description', 'connect-nuki-smartlock' );

}


function nukiwp__options_page(  ) {

	?>
	<form action='options.php' method='post'>

		<h2><?php _e( 'Connect your Nuki SmartLock', 'connect-nuki-smartlock'); ?></h2>

		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>

	</form>
	<?php

}

function nukiwp__open_action(){
    ?>
    <button>Open the Door</button>
<?php
}
