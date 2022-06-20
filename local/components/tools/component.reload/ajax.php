<?
/**
 * @var CMain $APPLICATION
 */
define('STOP_STATISTICS', true);
define('NO_AGENT_CHECK', true);
define('NOT_CHECK_PERMISSIONS', true);

use Bitrix\Main\Loader;

if (isset($_REQUEST['site_id']) && is_string($_REQUEST['site_id']))
{
    $siteID = trim($_REQUEST['site_id']);
    if ($siteID !== '' && preg_match('/^[a-z0-9_]{2}$/i', $siteID) === 1)
    {
        define('SITE_ID', $siteID);
    }
}

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

$request = Bitrix\Main\Application::getInstance()->getContext()->getRequest();
$request->addFilter(new \Bitrix\Main\Web\PostDecodeFilter);

if (!Loader::includeModule('sale') || !Loader::includeModule('catalog'))
    return;

$_SERVER["REQUEST_URI"] = empty($request['requestUri']) ? $_SERVER["REQUEST_URI"] : $request['requestUri'];

$APPLICATION->IncludeComponent(
    $request['name'],
    $request['template'],
    $request['parameters'],
    (object)$request['parentComponent'],
    $request['arFunctionParams']
);
