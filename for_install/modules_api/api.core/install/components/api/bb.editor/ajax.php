<?php
/**
 * Bitrix vars
 *
 * @var CDatabase $DB
 * @var CUser     $USER
 * @var CMain     $APPLICATION
 *
 */

define('PUBLIC_AJAX_MODE', true);
define('STOP_STATISTICS', true);
define('NO_KEEP_STATISTIC', 'Y');
define('NO_AGENT_STATISTIC', 'Y');
define('DisableEventsCheck', true);
define('BX_SECURITY_SHOW_MESSAGE', true);

use Bitrix\Main\Loader,
	 Bitrix\Main\Application,
	 Bitrix\Main\Web\Json,
	 Bitrix\Main\Localization\Loc,
	 Bitrix\Main\Text\Encoding;

if(!$_SERVER['REQUEST_METHOD'] || !preg_match('/^[A-Za-z0-9_]{2}$/', $_REQUEST['SITE_ID']))
	die();

if($_REQUEST['SITE_ID']) {
	define('SITE_ID', htmlspecialchars($_REQUEST['SITE_ID']));
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

global $APPLICATION;

//Loc::loadMessages(dirname(__FILE__) . '/template.php');

$context = Application::getInstance()->getContext();
$server  = $context->getServer();
$request = $context->getRequest();

$request->addFilter(new \Bitrix\Main\Web\PostDecodeFilter);

$post = $request->getPostList()->toArray();

if($post['API_BB_EDITOR_AJAX'] != 'Y')
	die();

if(!Loader::includeModule('api.core'))
	die('api.core required');


$html = Loc::getMessage('API_BB_EDITOR_HTML');


$APPLICATION->RestartBuffer();
echo $html;
//header('Content-Type: application/json');
//echo Json::encode($return);
die();