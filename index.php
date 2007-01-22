<?
require_once dirname(__FILE__) . '/source/static.inc.php';
require_once dirname(__FILE__) . '/source/functions.inc.php';
require_once dirname(__FILE__) . '/config/config.inc.php';

session_save_path(get_config('cachepath','/tmp/'));
session_start();

require_once dirname(__FILE__) . '/source/All.inc.php';

$mysql = new MySQLInterface();
$user = new User(2);

$usergroup = new Usergroup(10);

$user->set('usergroupid', $usergroup);
$user->save();

//try {
//	$user->get('gibtsnicht');
//} catch(Exception $e) {
//	print_a($e);
//}

print_a($mysql->getLastErrors());
print_a($mysql->getQueries());
?>