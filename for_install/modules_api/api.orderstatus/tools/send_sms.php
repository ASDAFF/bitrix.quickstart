<?
define('STOP_STATISTICS', true);
define('NO_AGENT_CHECK', true);

use Bitrix\Main;
use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Localization\Loc;
use Api\OrderStatus\SMS;
use Api\OrderStatus\SmsHistoryTable;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
Loc::loadMessages(__FILE__);

global $USER, $APPLICATION;
CUtil::JSPostUnescape();

$moduleId = 'api.orderstatus';
$arResult  = array();

$arModules = array(
	'api.orderstatus' => Loader::includeModule($moduleId),
	'sale'            => Loader::includeModule('sale'),
);

if(!$arModules[ $moduleId ])
{
	$arResult = array(
		'result'  => 'error',
		'message' => Loc::getMessage('AOS_TSS_MODULE_ERROR'),
	);
}

if(!$arModules['sale'])
{
	$arResult = array(
		'result'  => 'error',
		'message' => Loc::getMessage('AOS_TSS_SALE_MODULE_ERROR'),
	);
}

$MODULE_SALE_RIGHT = $APPLICATION->GetGroupRight('sale');
if($MODULE_SALE_RIGHT <= 'D')
{
	$arResult = array(
		'result'  => 'error',
		'message' => Loc::getMessage('AOS_TSS_ACCESS_DENIED'),
	);
}


$context = Application::getInstance()->getContext();
$request = $context->getRequest();


$orderId  = $request->get('orderId');
$statusId = $request->get('statusId');
$siteId   = $request->get('siteId');
$phone    = $request->get('phone');
$message  = $request->get('message');


if(!Application::isUtfMode())
	$message = Main\Text\Encoding::convertEncoding($message, 'UTF-8', $context->getCulture()->getCharset());

if(!$orderId || !$statusId || !$siteId || !$phone)
{
	$arResult = array(
		'result'  => 'error',
		'message' => Loc::getMessage('AOS_TSS_ERROR_AJAX_VARS'),
	);
}

if(strlen($message) == 0)
{
	$arResult = array(
		'result'  => 'error',
		'message' => Loc::getMessage('AOS_TSS_ERROR_EMPTY_MESSAGE'),
	);
}

$arOrderFields = \CApiOrderStatus::getOrderFields($orderId);
if($message)
	$message = \CApiOrderStatus::replaceMacros($arOrderFields, $message);


/////////////////////////////////////////////////////////////////
//                           EXEC
/////////////////////////////////////////////////////////////////
if(check_bitrix_sessid())
{
	if(empty($arResult))
	{
		$data = array(
			'GATEWAY_ID' => null,
			'SMS_ID' => null,
			'SMS_ERROR' => null,
		);

		$result = SMS::send($phone,$message,$siteId);

		if($result->isSuccess())
		{
			$data = $result->getData();

			$arResult = array(
				'result'  => 'ok',
				'message' => Loc::getMessage('AOS_TSS_SMS_SEND'),
			);
		}
		else
		{
			$errors = join("<br>", $result->getErrorMessages());
			$data['SMS_ERROR'] = $errors;

			$arResult = array(
				'result'  => 'error',
				'message' => $errors,
			);
		}

		//Add Status History
		SmsHistoryTable::add(array(
			'ORDER_ID'    => $orderId,
			'USER_ID'     => intval($USER->GetID()),
			'SITE_ID'     => $siteId,
			'STATUS_ID'   => $statusId,
			'DATE_CREATE' => DateTime::createFromTimestamp(time()),
			'GATEWAY_ID'  => $data['GATEWAY_ID'],
			'SMS_ID'      => $data['SMS_ID'],
			'SMS_ERROR'   => $data['SMS_ERROR'],
			'SMS_TEXT'    => CApiOrderStatus::getFormatText($message),
		));
	}
}
else
{
	$arResult = array(
		'result'  => 'error',
		'message' => Loc::getMessage('AOS_TSS_SESSION_EXPIRED'),
	);
}

$APPLICATION->RestartBuffer();
echo Bitrix\Main\Web\Json::encode($arResult);
die();
?>