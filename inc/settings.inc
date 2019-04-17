<div class="wrap">
	<h1>VTS Read Settings</h1>

	<form method="post" action="options.php">
		<?php settings_fields( 'vts-settings' ); ?>
		<?php do_settings_sections( 'vts-settings' ); ?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">API Key</th>
				<td><input type="text" name="api_key" value="<?php echo esc_attr( get_option('api_key') ); ?>" /></td>
			</tr>

			<tr valign="top">
				<th scope="row">API Secret</th>
				<td><input type="text" name="api_secret" value="<?php echo esc_attr( get_option('api_secret') ); ?>" /></td>
			</tr>

			<tr valign="top">
				<th scope="row">Google Maps Key</th>
				<td><input type="text" name="google_maps_key" value="<?php echo esc_attr( get_option('google_maps_key') ); ?>" /></td>
			</tr>
		</table>

		<?php submit_button(); ?>

	</form>
</div>