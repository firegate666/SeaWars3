<?
/**
 * print object to console
 *
 * @param mixed $array
 */
function print_a($array) {
	echo ("<pre>\n");
	print_r($array);
	echo ("</pre>\n");
}

/**
 * return value from global configs
 * 
 * @param	String	$name	name of config
 * @param	String	$default	if not found return this
 * @return	String	config value
 */
function get_config($name, $default = '') {
	global $_CONFIG;
	if(isset($_CONFIG[$name]))
		return $_CONFIG[$name];
	else
		return $default;
}
?>