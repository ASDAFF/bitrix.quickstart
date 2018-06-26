<?
define("NO_KEEP_STATISTIC", true);
define("NO_AGENT_STATISTIC", true);

use Bitrix\Main;
use Bitrix\Main\Loader;
use Bitrix\Sale\Location;
use Bitrix\Sale\Location\Admin\LocationHelper as Helper;

require_once($_SERVER["DOCUMENT_ROOT"].'/bitrix/modules/main/include/prolog_before.php');

Loader::includeModule('sale');

CUtil::JSPostUnescape();

$result = array(
	'ERRORS' => array(),
	'DATA' => array()
);

$item = Helper::getLocationsByZip($_REQUEST['ZIP'], array('limit' => 1))->fetch();

if(!isset($item['LOCATION_ID']))
	$result['ERRORS'] = array('Not found');
else
{
	$siteId = '';
	if(strlen($_REQUEST['SITE_ID']))
		$siteId = $_REQUEST['SITE_ID'];
	elseif(strlen(SITE_ID))
		$siteId = SITE_ID;

	$result['DATA']['ID'] = intval($item['LOCATION_ID']);

	if(strlen($siteId))
	{
		if(!Location\SiteLocationTable::checkConnectionExists($siteId, $result['DATA']['ID']))
			$result['ERRORS'] = array('Found, but not connected');
	}
}

header('Content-Type: application/x-javascript; charset='.LANG_CHARSET);
print(CUtil::PhpToJSObject(array(
	'result' => empty($result['ERRORS']),
	'errors' => $result['ERRORS'],
	'data' => $result['DATA']
), false, false, true));