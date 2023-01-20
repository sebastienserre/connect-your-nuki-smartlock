<?php
/**
 * Nuki Smartlock Settings.
 *
 * @package Connect-your-nuki-smartlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

add_action( 'admin_menu', 'nukiwp__add_admin_menu' );
add_action( 'admin_init', 'nukiwp__settings_init' );

/**
 * Add admin menu.
 *
 * @return void
 */
function nukiwp__add_admin_menu() {

	add_menu_page( __( 'Nuki Settings', 'connect-your-nuki-smartlock' ), __( 'Nuki Settings', 'connect-your-nuki-smartlock' ), 'manage_options', 'connect_your_nuki_smartlock', 'nukiwp__options_page' );

}

/**
 * Init settings.
 *
 * @return void
 */
function nukiwp__settings_init() {

	$options = get_option( 'nukiwp__settings' );
	if ( ! empty( $options['apikey'] ) ) {
		$token = $options['apikey'];
	}
	register_setting( 'pluginPage', 'nukiwp__settings' );


	add_settings_section(
		'nukiwp__pluginPage_section',
		'',
		'',
		'pluginPage'
	);

	add_settings_field(
		'apikey',
		__( 'Token', 'connect-your-nuki-smartlock' ),
		'nukiwp_apikey_render',
		'pluginPage',
		'nukiwp__pluginPage_section'
	);

	if ( ! empty( $token ) ) {
		add_settings_field(
			'nuki-smartlcok-managed',
			__( 'Smartlock to manage', 'connect-your-nuki-smartlock' ),
			'nukiwp__manage_smartlock',
			'pluginPage',
			'nukiwp__pluginPage_section'
		);

		add_settings_field(
			'nuki-smartlovk-autolock-activated',
			__( 'Enable autolock', 'connect-your-nuki-smartlock' ),
			'nukiwp_enable_autolock',
			'pluginPage',
			'nukiwp__pluginPage_section'
		);

		add_settings_field(
			'nuki-open-auto-lock',
			__( 'Autolock between', 'connect-your-nuki-smartlock' ),
			'nukiwp__open_action',
			'pluginPage',
			'nukiwp__pluginPage_section'
		);
	}
}

/**
 * API Key field render.
 *
 * @return void
 */
function nukiwp_apikey_render() {

	$options = get_option( 'nukiwp__settings' );
	?>
	<input type='text' name='nukiwp__settings[apikey]' value='<?php echo esc_attr( $options['apikey'] ); ?>'>
	<?php

}

/**
 * Enable Autlock setting.
 *
 * @return void
 */
function nukiwp_enable_autolock() {

	$options = get_option( 'nukiwp__settings' );
	if ( empty( $options['autolock_activated'] ) ) {
		$options['autolock_activated'] = '0';
	}
	?>
	<input type='checkbox' name='nukiwp__settings[autolock_activated]' value='1' <?php checked( $options['autolock_activated'], '1' ); ?>>
	<?php

}

/**
 * Render option page.
 *
 * @return void
 */
function nukiwp__options_page() {

	?>
	<form action='options.php' method='post'>

		<h2><?php esc_html_e( 'Connect your Nuki SmartLock', 'connect-your-nuki-smartlock' ); ?></h2>

		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>

	</form>
	<?php

}

/**
 * Display time to run.
 *
 * @return void
 */
function nukiwp__open_action() {
	$options = nukiwp_time_selector();
	?>
	<select name='nukiwp__settings[start-autolock]'>
		<?php
		foreach ( $options as $option ) {
			// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $option; // value escaped in nukiwp_time_selector()
		}
		?>
	</select>
	<?php
	$options = '';
	esc_attr_e( 'and', 'connect-your-nuki-smartlock' );
	$options = nukiwp_time_selector( 'end' );
	?>
	<select name='nukiwp__settings[end-autolock]'>
		<?php
		foreach ( $options as $option ) {

			// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $option; // value escaped in nukiwp_time_selector()
		}
		?>
	</select>
	<?php
}

/**
 * Create the time selector.
 *
 * @param string $hour 'start' or  'end' time selector.
 *
 * @return array
 */
function nukiwp_time_selector( $hour = 'start' ) {
	$hours   = array( '00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23' );
	$minutes = array( '00', '05', '10', '15', '20', '25', '30', '35', '40', '45', '50', '55' );
	$nuki = new \Nuki\API\Api();
	$settings = $nuki->get_settings();
	$options = array();
	if ( ! empty( $settings['smartlock-managed'] ) ) {
		$selected_hour = $settings[ $hour . '-autolock' ];
		foreach ( $hours as $hour ) {
			foreach ( $minutes as $minute ) {
				$formatted_hour = $hour . ':' . $minute;
				$options[]      = '<option value="' . esc_attr( $formatted_hour ) . '"' . selected( $selected_hour, $formatted_hour, false ) . '>' . $formatted_hour . '</option>';
			}
		}
	}
	return $options;
}

/**
 * Display which Smartllock will be managed.
 *
 * @return void
 */
function nukiwp__manage_smartlock() {
	$nuki = new \Nuki\API\Api();
	$smartlocks = $nuki->get_smartlock();
	if ( empty( $smartlocks ) ) {
		echo '<span class="nuki-error">' . esc_html__( 'Wrong or empty API key', 'connect-your-nuki-smartlock' ) . '</span>';
		return;
	}
	$settings = $nuki->get_settings();

	$selected_smartlock = '';
	if ( ! empty( $settings['smartlock-managed'] ) ) {
		$selected_smartlock = $settings['smartlock-managed'];
	}
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

/**
 * Add admin menu.
 *
 * @return void
 */
function nuki_wp_add_admin_menu() {
	add_submenu_page( 'connect_your_nuki_smartlock', __( 'Licenses', 'connect-your-nuki-smartlock' ), __( 'Licenses', 'connect-your-nuki-smartlock' ), 'manage_options', 'connect_nuki_licenses', 'nuki_wp_licences_page' );
}

add_action( 'admin_menu', 'nuki_wp_add_admin_menu', 15 );

/**
 * Create the option page.
 *
 * @return void
 */
function nuki_wp_licences_page() {
	?>
    <form action='options.php' method='post'>

        <h2><?php esc_html_e( 'Connect Nuki Licenses', 'connect-your-nuki-smartlock' ); ?></h2>

		<?php
		if ( defined( 'NUKIGF_VERSION' ) || defined( 'NUKIWC_VERSION' ) ) {
			settings_fields( 'License-form' );
			do_settings_sections( 'License-form' );
			submit_button();
		} else {
			?>
            <div class="nuki-ads">
                <a href="https://nuki-smartlock-for-wp.com/">
					<?php
					esc_html_e( 'Visit our shop and get WooCommerce add-on', 'connect-your-nuki-smartlock' );
					?>
                </a>
            </div>
			<?php
		}
		?>

    </form>
	<?php
}

add_action( 'admin_init', 'nuki_wp_settings_init' );
/**
 * Register settings.
 *
 * @return void
 */
function nuki_wp_settings_init() {

	add_settings_section(
		'nuki_wp__License-form_section',
		'',
		'',
		'License-form'
	);


}
