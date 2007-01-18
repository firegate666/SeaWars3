<?php
class Date {
	
	/**
	* get actual date formatted
	*
	* @param	String	$formatstring	format date, see php doc
	* @return	String	formatted date
	*/
	public function now($formatstring = '%Y-%m-%d %H:%M:%S') {
		return strftime("$formatstring", time());
	}
	
}
?>