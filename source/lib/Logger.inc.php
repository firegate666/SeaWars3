<?php
abstract class Logger {
	protected $loglevel;
	
	abstract public function write($msg, $loglevel=LOGL_INFO);
}

class FileLogger extends Logger {

	/**
	 * write msg to logfile
	 *
	 * @param unknown_type $msg
	 * @param unknown_type $loglevel
	 */
	public function write($msg, $loglevel=LOGL_INFO) {
		if ($loglevel >= get_config('loglevel', LOGL_INFO)) // no logging
			return;
		$timestamp = Date::now();
		$userid = 0;
		$msg = "$timestamp ($userid): ".$msg."\n";
		$filename = get_config('logfilename', false);
		if ($filename === false)
			return;
		file_put_contents($filename, $msg, FILE_APPEND);
	}
	
}
?>