<?
define("NO_KEEP_STATISTIC", true);
define("NO_AGENT_STATISTIC", true);
//define("NOT_CHECK_PERMISSIONS", true);

use Bitrix\Main;
use Bitrix\Main\Loader;

require_once($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/main/include/prolog_before.php');
require_once(dirname(__FILE__).'/class.php');

Loader::includeModule('sale');

$result = true;
$errors = array();
$data = array();

try
{
	CUtil::JSPostUnescape();
	$data = CBitrixLocationSelectorSearchComponent::processSearchRequest();
}
catch(Main\SystemException $e)
{
	$result = false;
	$errors[] = $e->getMessage();
}

header('Content-Type: application/x-javascript; charset='.LANG_CHARSET);
print(CUtil::PhpToJSObject(array(
	'result' => $result,
	'errors' => $errors,
	'data' => $data
), false, false, true));