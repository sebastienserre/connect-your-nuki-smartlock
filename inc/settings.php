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
		'apikey',
		__( 'API Key', 'connect-nuki-smartlock' ),
		'apikey_render',
		'pluginPage',
		'nukiwp__pluginPage_section'
	);

	add_settings_field(
		'nuki-open-auto-lock',
		__( 'Autolock between', 'connect-nuki-smartlock' ),
		'nukiwp__open_action',
		'pluginPage',
		'nukiwp__pluginPage_section'
	);


}

function apikey_render(  ) {

	$options = get_option( 'nukiwp__settings' );
	?>
	<input type='text' name='nukiwp__settings[apikey]' value='<?php echo $options['apikey']; ?>'>
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
    $options = nukiwp_time_selector();
?>
    <select name='nukiwp__settings[start-autolock]'>
        <?php
        foreach ( $options as $option ){
            echo $option;
        }
        ?>
    </select>
<?php
    $options = '';
    _e( 'and', 'connect-nuki-smartlock');
	$options = nukiwp_time_selector( 'end' );
	?>
    <select name='nukiwp__settings[end-autolock]'>
		<?php
		foreach ( $options as $option ){
			echo $option;
		}
		?>
    </select>
	<?php
}

function nukiwp_time_selector( $hour = 'start') {
	$hours   = array( '00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23' );
	$minutes = array( '00', '05', '10', '15', '20', '25', '30', '35', '40', '45', '50', '55' );
	$nuki = new \Nuki\API\api();
	$settings = $nuki->settings;
    $selected_hour = $settings[$hour . '-autolock'];
	foreach ( $hours as $hour ) {
		foreach ( $minutes as $minute ) {
            $formatted_hour = $hour . ':' . $minute;
			$options[] = '<option value="'. $formatted_hour . '"' . selected( $selected_hour, $formatted_hour, false ). '>' . $formatted_hour . '</option>';
		}
	}
    return $options;
}
