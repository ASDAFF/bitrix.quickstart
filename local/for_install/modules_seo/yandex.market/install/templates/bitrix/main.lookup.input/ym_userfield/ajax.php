<?php

use Bitrix\Main;
use Yandex\Market;

if (empty($_REQUEST['site']) || !is_string($_REQUEST['site']) || !preg_match('/^[a-z0-9_]{2}$/i', $_REQUEST['site'])) { die(); }

define('ADMIN_SECTION', true);
define('STOP_STATISTICS', true);
define('BX_SECURITY_SHOW_MESSAGE', true);
define('SITE_ID', $_REQUEST['site']);

require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php';

global $APPLICATION;

Main\Localization\Loc::loadMessages(__FILE__);

if (!Main\Loader::includeModule('yandex.market'))
{
	echo Main\Localization\Loc::getMessage('YANDEX_MARKET_T_BITRIX_MLI_SERVER_CATEGORY_REQUIRE_MODULE');
	die();
}

/** @var Yandex\Market\Ui\UserField\Autocomplete\Provider $providerName */
$providerClassName = isset($_REQUEST['provider']) ? trim($_REQUEST['provider']) : '';

if (
	$providerClassName === ''
	|| !class_exists($providerClassName)
	|| !is_subclass_of($providerClassName, '\Yandex\Market\Ui\UserField\Autocomplete\Provider')
)
{
	echo Main\Localization\Loc::getMessage('YANDEX_MARKET_T_BITRIX_MLI_SERVER_CATEGORY_INVALID_PROVIDER');
	die();
}

CUtil::JSPostUnescape();

if (isset($_REQUEST['MODE']) && $_REQUEST['MODE'] == 'SEARCH')
{
	$APPLICATION->RestartBuffer();

	$query = trim($_REQUEST['search']);
	$result = $providerClassName::searchByName($query);

	header('Content-Type: application/json');
	echo Main\Web\Json::encode($result);
	die();
}