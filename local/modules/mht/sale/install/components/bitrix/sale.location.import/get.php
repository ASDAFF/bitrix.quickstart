<?
define("NO_KEEP_STATISTIC", true);
define("NO_AGENT_STATISTIC", true);

use Bitrix\Main;
use Bitrix\Main\Loader;

$initialTime = time();

require_once($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/main/include/prolog_before.php');

Loader::includeModule('sale');

require_once(dirname(__FILE__).'/class.php');

CUtil::JSPostUnescape();

$result = true;
$errors = array();

// if we have an exception here, we got ajax parse error on client side.
// we must take care of it until we have better solution
$result = CBitrixCrmConfigLocationImport2Component::doAjaxStuff(array(
	'INITIAL_TIME' => $initialTime,
	'ONLY_DELETE_ALL' => isset($_REQUEST['ONLY_DELETE_ALL'])
));

header('Content-Type: application/x-javascript; charset='.LANG_CHARSET);
print(CUtil::PhpToJSObject(array(
	'result' => empty($result['ERRORS']),
	'errors' => $result['ERRORS'],
	'data' => $result['DATA']
), false, false, true));