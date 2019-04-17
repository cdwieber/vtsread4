<?php

namespace VTS;

class VTSRead_Log {

	private $perm_file = WP_PLUGIN_DIR . '/vtsread/log/vts_import.log';
	private $tmp_file = WP_PLUGIN_DIR . '/vtsread/log/vts_import_tmp.log';

	/**
	 * Write a line to the log file. If the $tmp arg is set to false,
	 * skip writing to the temp file (which should be cleared after respective process).
	 * @param $message
	 * @param bool $tmp
	 * @return bool
	 */
	function write($message, $tmp = true) {
		$log_string = "[" . date("Y-m-d H:i:s") . "] -- " . $message . "\n";
		file_put_contents($this->perm_file, $log_string, FILE_APPEND);

		if(true == $tmp) {
			file_put_contents($this->tmp_file, $log_string, FILE_APPEND);
		}
		return true;
	}

	/**
	 * Read the contents of the temp file and return the whole thing. Useful for the AJAX
	 * polling method.
	 * @return string|bool
	 */
	function read_tmp() {
		return file_get_contents($this->tmp_file);
	}

	/**
	 * Clear the tmp file
	 * @return bool
	 */
	function clear_tmp() {
		file_put_contents($this->tmp_file, "");

		return true;
	}


}