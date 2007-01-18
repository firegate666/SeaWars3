<?
error_reporting(E_ALL);
session_save_path('./cache/');
session_start();

require_once dirname(__FILE__) . '/config/config.inc.php';
require_once dirname(__FILE__) . '/source/All.inc.php';

$mysql = new MySQLInterface();
$user = new User(2);
$user->set('login', 'matsche');
$user->save();
print_a($mysql->getLastErrors());
print_a($mysql->getQueries());
?>