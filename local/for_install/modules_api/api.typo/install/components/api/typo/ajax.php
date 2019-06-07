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

if($_SERVER['REQUEST_METHOD'] != 'POST' || !preg_match('/^[A-Za-z0-9_]{2}$/', $_POST['SITE_ID']))
	 die();

define('SITE_ID', htmlspecialchars($_POST['SITE_ID']));
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
if(!check_bitrix_sessid())
	die();

use \Bitrix\Main\Application;
use \Bitrix\Main\Mail\Event;
use \Bitrix\Main\SiteTable;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Text\Encoding;
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

global $APPLICATION;

$context = Application::getInstance()->getContext();
$request = $context->getRequest();
$server  = $context->getServer();
//$post   = $request->getPostList()->toArray();
//$request->addFilter(new \Bitrix\Main\Web\PostDecodeFilter);


//if(!Bitrix\Main\Loader::includeModule('sale')) // || !$request->get('action')
//	return;

//Bitrix\Main\Localization\Loc::loadMessages(dirname(__FILE__).'/class.php');

$url      = $request->get('URL');
$siteId   = $request->get('SITE_ID');
$arParams = $request->get('OPTIONS');
$strForm  = $request->get('FORM');
parse_str($strForm, $arPost);
$post = $arPost['TYPO'];


if(!Application::isUtfMode())
	$post = Encoding::convertEncoding($post, 'UTF-8', $context->getCulture()->getCharset());


if($post)
{
	foreach($post as $key => $val)
		$post[ $key ] = nl2br($val);
}

//$tttfile = dirname(__FILE__) . '/1_txt.php';
//file_put_contents($tttfile, '<pre>' . print_r($post, 1) . '</pre>\n');

$arSite = SiteTable::getList(array(
	 'select' => array('EMAIL', 'SITE_NAME', 'SERVER_NAME'),
	 'filter' => array('=LID' => $siteId),
))->fetch();

$siteName   = ($arSite['SITE_NAME'] ? $arSite['SITE_NAME'] : Option::get('main', 'site_name', $server->getServerName()));
$serverName = ($arSite['SERVER_NAME'] ? $arSite['SERVER_NAME'] : Option::get('main', 'server_name', $server->getServerName()));
$emailFrom   = $arSite['EMAIL'] ? $arSite['EMAIL'] : Option::get('main', 'email_from', 'info@' . $server->getServerName());
$emailTo     = $emailFrom;

if($arParams['EMAIL_FROM'])
	$emailFrom = base64_decode($arParams['EMAIL_FROM']);

if($arParams['EMAIL_TO'])
	$emailTo = base64_decode($arParams['EMAIL_TO']);

//$subject = str_replace(array('#SITE_NAME#', '#SERVER_NAME#'), array($sSiteName, $sServerName), $arFields['SUBJECT']);

$event    = 'API_TYPO';
$arFields = array(
	 'EMAIL_FROM'  => $emailFrom,
	 'EMAIL_TO'    => $emailTo,
	 'SITE_NAME'   => $siteName,
	 'SERVER_NAME' => $serverName,
	 'ERROR'       => $post['ERROR'],
	 'COMMENT'     => $post['COMMENT'],
	 'URL'         => $url,
	 'IP'          => $server->getServerAddr(),
	 'MESSAGE'     => Loc::getMessage(
			(strlen($post['COMMENT']) > 0 ? 'API_TYPO_AJAX_MESSAGE' : 'API_TYPO_AJAX_MESSAGE_SHORT'),
			array(
				 '#ERROR#'   => $post['ERROR'],
				 '#COMMENT#' => $post['COMMENT'],
				 '#URL#'     => $url,
			)
	 ),
);


foreach(GetModuleEvents('main', 'OnBeforeEventAdd', true) as $arEvent)
	if(ExecuteModuleEventEx($arEvent, array(&$event, &$siteId, &$arFields)) === false)
		return false;

$result = Event::send(array(
	 'EVENT_NAME' => $event,
	 'C_FIELDS'   => $arFields,
	 'LID'        => $siteId,
	 'DUPLICATE'  => 'Y',
));


if($result->isSuccess())
{
	$sendResult = array(
		 'status'  => 'ok',
		 'message' => ($arParams['MESS_ALERT_SEND_OK'] ? $arParams['MESS_ALERT_SEND_OK'] : Loc::getMessage('API_TYPO_AJAX_SEND_OK')),
	);
}
else
{
	$sendResult = array(
		 'status'  => 'error',
		 'message' => join('<br>', $result->getErrorMessages()),
	);
}

if(!Application::isUtfMode())
	$sendResult = Encoding::convertEncoding($sendResult, 'UTF-8', $context->getCulture()->getCharset());


$APPLICATION->RestartBuffer();
echo \Bitrix\Main\Web\Json::encode($sendResult);
CMain::FinalActions();
die();