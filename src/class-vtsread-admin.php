<?php
namespace VTS;

class VTSRead_Admin {

	/**
	 * VTSRead_Admin constructor.
	 */
	public function __construct() {

		//$this->vts_menus();
		$this->register_vts_settings();

	}

	/**
	 * Add appropriate submenus to listing menu
	 */
	public function vts_menus() {
		//create new top-level menu
		add_submenu_page('edit.php?post_type=listing',
			'Manual Refresh',
			'Manual Refresh',
			'manage_options',
			'vts_refresh',
			array($this, 'import_page')
			);
		add_submenu_page('edit.php?post_type=listing',
			'Settings',
			'Settings',
			'manage_options',
			'vts_settings',
			array($this, 'settings_page')
		);
	}

	/**
	 * Register our plugin-specific settings
	 */
	public function register_vts_settings() {
		//register our settings
		register_setting( 'vts-settings', 'api_key' );
		register_setting( 'vts-settings', 'api_secret' );
		register_setting( 'vts-settings', 'google_maps_key' );
	}

	/**
	 * Register import admin page.
	 */
	public function import_page() {
		require_once WP_PLUGIN_DIR . '/vtsread/inc/import.inc';
	}

	/**
	 * Register plugin admin settings page.
	 */
	public function settings_page() {
		require_once WP_PLUGIN_DIR . '/vtsread/inc/settings.inc';
	}
}