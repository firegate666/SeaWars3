<?
require_once dirname(__FILE__) . '/source/static.inc.php';
require_once dirname(__FILE__) . '/source/functions.inc.php';
require_once dirname(__FILE__) . '/config/config.inc.php';

session_save_path(get_config('cachepath','/tmp/'));
session_start();

require_once dirname(__FILE__) . '/source/All.inc.php';

//$xsl = new XSLTProcessor();
//$doc = new DOMDocument();
//
//$doc->load('./xslt/index.xsl');
//$xsl->importStyleSheet($doc);
//
//$doc->load('./objects/user.xml');
//echo $xsl->transformToXML($doc);

//print_a($mysql->getLastErrors());
//print_a($mysql->getQueries());
?>