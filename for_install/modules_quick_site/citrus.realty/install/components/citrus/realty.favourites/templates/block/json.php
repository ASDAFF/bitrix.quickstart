<?
define("NO_KEEP_STATISTIC", "Y");
define("NO_AGENT_STATISTIC","Y");
define("NO_AGENT_CHECK", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$response = array();
try
{
	if (!\Bitrix\Main\Loader::includeModule("iblock"))
		throw new \Exception("iblock module not found");

	if (!\Bitrix\Main\Loader::includeModule("citrus.realty"))
		throw new \Exception("citrus.realty module not found");

	if ($_REQUEST['type'] == "add")
		\Citrus\Realty\Favourites::add($_REQUEST['id']);
	elseif ($_REQUEST['type'] == "remove")
		\Citrus\Realty\Favourites::remove($_REQUEST['id']);

	$response['count'] = \Citrus\Realty\Favourites::getCount();
	$response['type'] = $_REQUEST['type'];
}
catch (Exception $e)
{
	$response['error'] = $e->getMessage();
}

$APPLICATION->RestartBuffer();
echo \Bitrix\Main\Web\Json::encode($response);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");