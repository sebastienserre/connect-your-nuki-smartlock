<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

add_action( 'admin_menu', 'nukiwp__add_admin_menu' );
add_action( 'admin_init', 'nukiwp__settings_init' );

if ( ! function_exists( 'nukiwp__add_admin_menu' ) ) {
	function nukiwp__add_admin_menu() {

		add_options_page( 'Connect your Nuki SmartLock', 'Connect your Nuki SmartLock', 'manage_options', 'connect_your_nuki_smartlock', 'nukiwp__options_page' );

	}
}

if ( ! function_exists( 'nukiwp__settings_init' ) ) {
	function nukiwp__settings_init() {

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
			'nukiwp_apikey_render',
			'pluginPage',
			'nukiwp__pluginPage_section'
		);

		add_settings_field(
			'nuki-smartlcok-managed',
			__( 'Smartlock to manage', 'connect-nuki-smartlock' ),
			'nukiwp__manage_smartlock',
			'pluginPage',
			'nukiwp__pluginPage_section'
		);

		add_settings_field(
			'nuki-smartlovk-autolock-activated',
			__( 'Enable autolock', 'connect-nuki-smartlock' ),
			'nukiwp_enable_autolock',
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
}

if ( ! function_exists( 'nukiwp_apikey_render' ) ) {
	function nukiwp_apikey_render() {

		$options = get_option( 'nukiwp__settings' );
		?>
        <input type='text' name='nukiwp__settings[apikey]' value='<?php echo esc_attr( $options['apikey'] ); ?>'>
		<?php

	}
}

if ( ! function_exists( 'nukiwp_enable_autolock' ) ) {
	function nukiwp_enable_autolock() {

		$options = get_option( 'nukiwp__settings' );
		if ( empty( $options['autolock_activated'] ) ) {
			$options['autolock_activated'] = '0';
		}
		?>
        <input type='checkbox' name='nukiwp__settings[autolock_activated]' value='1' <?php checked( $options['autolock_activated'], '1' ) ?>>
		<?php

	}
}

if ( ! function_exists( 'nukiwp__options_page' ) ) {
	function nukiwp__options_page() {

		?>
        <form action='options.php' method='post'>

            <h2><?php _e( 'Connect your Nuki SmartLock', 'connect-nuki-smartlock' ); ?></h2>

			<?php
			settings_fields( 'pluginPage' );
			do_settings_sections( 'pluginPage' );
			submit_button();
			?>

        </form>
		<?php

	}
}

if ( ! function_exists( 'nukiwp__open_action' ) ) {
	function nukiwp__open_action() {
		$options = nukiwp_time_selector();
		?>
        <select name='nukiwp__settings[start-autolock]'>
			<?php
			foreach ( $options as $option ) {
				echo $option; // value escaped in nukiwp_time_selector()
			}
			?>
        </select>
		<?php
		$options = '';
		esc_attr_e( 'and', 'connect-nuki-smartlock' );
		$options = nukiwp_time_selector( 'end' );
		?>
        <select name='nukiwp__settings[end-autolock]'>
			<?php
			foreach ( $options as $option ) {
				echo $option; // value escaped in nukiwp_time_selector()
			}
			?>
        </select>
		<?php
	}
}

if ( ! function_exists( 'nukiwp_time_selector' ) ) {
	function nukiwp_time_selector( $hour = 'start' ) {
		$hours         = array(
			'00',
			'01',
			'02',
			'03',
			'04',
			'05',
			'06',
			'07',
			'08',
			'09',
			'10',
			'11',
			'12',
			'13',
			'14',
			'15',
			'16',
			'17',
			'18',
			'19',
			'20',
			'21',
			'22',
			'23'
		);
		$minutes       = array( '00', '05', '10', '15', '20', '25', '30', '35', '40', '45', '50', '55' );
		$nuki          = new \Nuki\API\api();
		$settings      = $nuki->get_settings();
		$selected_hour = $settings[ $hour . '-autolock' ];
		foreach ( $hours as $hour ) {
			foreach ( $minutes as $minute ) {
				$formatted_hour = $hour . ':' . $minute;
				$options[]      = '<option value="' . esc_attr( $formatted_hour ) . '"' . selected( $selected_hour, $formatted_hour, false ) . '>' . $formatted_hour . '</option>';
			}
		}

		return $options;
	}
}

if ( ! function_exists( 'nukiwp__manage_smartlock' ) ) {
	function nukiwp__manage_smartlock() {
		$nuki               = new \Nuki\API\api();
		$smartlocks         = $nuki->get_smartlock();
		$settings           = $nuki->get_settings();
		$selected_smartlock = $settings['smartlock-managed'];
		?>
        <select name='nukiwp__settings[smartlock-managed]'>
			<?php
			foreach ( $smartlocks as $smartlock ) {
				echo '<option value="' . esc_attr( $smartlock['smartlockId'] ) . '"' . selected( $selected_smartlock, $smartlock['smartlockId'], false ) . '>' . $smartlock['name'] . '</option>';
			}
			?>
        </select>
		<?php
	}
}