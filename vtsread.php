<?php
/*
* Plugin Name:       VTSRead
* Description:       Plugin which reads and imports properties from VTS
* Version:           0.0.2
* Author:            Chris Wieber
* Author URI:        http://chriswieber.com
* Text Domain:       vtsread
*/

/**
 * This file acts as the bootstrapper for VTSRead.
 */

require_once 'vendor/autoload.php';

use VTS\VTSRead;

if ( ! defined( 'WPINC' ) ) {
	die;
}

//Define VTSRead run function
function run_vtsread() {
	$vtsread = new VTSRead;
	$vtsread->run();
}
//Get this party started.
run_vtsread();