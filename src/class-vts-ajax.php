<?php

namespace VTS;

class VTS_Ajax {
	/**
	 * VTS_Ajax constructor.
	 */
	public function __construct() {
		add_action('wp_ajax_import', array($this, 'import_listings'));
		add_action('wp_ajax_poll', array($this, 'poll_tmp_log'));
	}

	/**
	 * Called by AJAX frontend. User-initiated import of listings.
	 */
	public function import_listings()
	{
		$log = new VTSRead_Log();
		$log->write("*****USER INITIATED IMPORT*****");

		$import = new VTS_Import();
		$import->vtsread_import();
		echo "Stuff";

		die();
	}

	/**
	 * Called by AJAX front-end. Polls the temporary log for progress
	 * information echos to front-end.
	 */
	public function poll_tmp_log() {
		$log = new VTSRead_Log();
		echo $log->read_tmp();
		die();
	}
}