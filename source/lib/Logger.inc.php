<?php
abstract class Logger {
	protected $loglevel;
	
	
	abstract public function write($msg, $loglevel=0);
	
}

class FileLogger extends Logger {

	public function write($msg, $loglevel=0) {
		if ($loglevel > get_config('loglevel', 5)) // no logging
			return;
		$timestamp = Date::now();
		$userid = 0;
		$msg = "$timestamp ($userid): ".$msg."\n";
		$filename = 'cache/log.txt';
		file_put_contents($filename, $msg, FILE_APPEND);
	}
	
}
?>