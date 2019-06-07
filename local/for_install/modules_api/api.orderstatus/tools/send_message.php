<?
define('STOP_STATISTICS', true);
define('NO_AGENT_CHECK', true);

use Bitrix\Main\Loader;
use Bitrix\Main\Mail;
use Bitrix\Main\SiteTable;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Localization\Loc;

//use Bitrix\Main\Mail\Internal\EventTypeTable;
//use Bitrix\Main\Mail\Internal\EventMessageTable;
//use Bitrix\Main\Mail\Internal\EventMessageSiteTable;


require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
Loc::loadMessages(__FILE__);

global $USER, $APPLICATION;
CUtil::JSPostUnescape();

$MODULE_ID = 'api.orderstatus';
$arResult  = array();

$arModules = array(
	'api.orderstatus' => Loader::includeModule($MODULE_ID),
	'sale'            => Loader::includeModule('sale'),
);

if(!$arModules[ $MODULE_ID ])
{
	$arResult = array(
		'result'  => 'error',
		'message' => Loc::getMessage('AOS_TSM_MODULE_ERROR'),
	);
}

if(!$arModules['sale'])
{
	$arResult = array(
		'result'  => 'error',
		'message' => Loc::getMessage('AOS_TSM_SALE_MODULE_ERROR'),
	);
}

$MODULE_SALE_RIGHT = $APPLICATION->GetGroupRight('sale');
if($MODULE_SALE_RIGHT <= 'D')
{
	$arResult = array(
		'result'  => 'error',
		'message' => Loc::getMessage('AOS_TSM_ACCESS_DENIED'),
	);
}


use Api\OrderStatus\HistoryTable;

$context = \Bitrix\Main\Application::getInstance()->getContext();
$request = $context->getRequest();


$ORDER_ID  = $request->get('ORDER_ID');
$STATUS_ID = $request->get('STATUS_ID');
$MESSAGE   = $APPLICATION->ConvertCharset($request->get('MESSAGE'), 'UTF-8', LANG_CHARSET);


if(!$ORDER_ID)
{
	$arResult = array(
		'result'  => 'error',
		'message' => Loc::getMessage('ERROR_AJAX_VARS'),
	);
}

if(!$MESSAGE)
{
	$arResult = array(
		'result'  => 'error',
		'message' => Loc::getMessage('ERROR_EMPTY_COMMENT'),
	);
}


/////////////////////////////////////////////////////////////////
//                           EXEC
/////////////////////////////////////////////////////////////////
if(check_bitrix_sessid())
{
	if(empty($arResult))
	{
		$arOrderFields = CApiOrderStatus::getOrderFields($ORDER_ID);
		$LID           = $arOrderFields['SITE_ID'] ? $arOrderFields['SITE_ID'] : 's1';

		//StatusEX Add fields
		$arHistoryFields = array(
			'ORDER_ID'    => $arOrderFields['ID'],
			'USER_ID'     => intval($USER->GetID()),
			'DATE_CREATE' => DateTime::createFromTimestamp(time()),
			'STATUS'      => $STATUS_ID,
			'DESCRIPTION' => CApiOrderStatus::getFormatText($MESSAGE),
			'LID'         => $LID,
			'MAIL'        => 'Y',
			'FILES'       => 'N',
		);

		if($arHistoryFields['DESCRIPTION'])
			$arHistoryFields['DESCRIPTION'] = CApiOrderStatus::replaceMacros($arOrderFields, $arHistoryFields['DESCRIPTION']);

		$arOrderBlocks = CApiOrderStatus::getOrderBlocks($ORDER_ID);
		if($arHistoryFields['DESCRIPTION'])
			$arHistoryFields['DESCRIPTION'] = CApiOrderStatus::replaceMacros($arOrderBlocks, $arHistoryFields['DESCRIPTION']);


		$arOrderFields['ORDER_DESCRIPTION'] = $arHistoryFields['DESCRIPTION'];


		/////////////////////////////////////////////////////////////////
		//      Check event and event type, if not isset -> Add()
		/////////////////////////////////////////////////////////////////
		$eventType = new CEventType;
		$eventM    = new CEventMessage;

		$message_id = 0;
		$EVENT_NAME = 'API_ORDERSTATUS';
		$arET       = $arEM = array();

		$arEventTypeFields = Loc::getMessage('AOS_TSM_EVENT_TYPE');
		$arEventMessFields = Loc::getMessage('AOS_TSM_EVENT_MESSAGE');

		$arSiteID = array();
		$resSites = SiteTable::getList(array(
			'select' => array('LID', 'DEF'),
			'filter' => array('ACTIVE' => 'Y'),
		));
		while($arSite = $resSites->fetch())
		{
			$arSiteID[] = $arSite['LID'];
		}

		$arEventMessFields['LID'] = $arSiteID;

		$arFilter = array(
			"TYPE_ID" => $EVENT_NAME,
			"LID"     => LANGUAGE_ID,
		);
		$rsET     = $eventType->GetList($arFilter);
		if(!$rsET->Fetch())
		{
			foreach($arEventTypeFields as $arField)
			{
				$dbRes       = $eventType->GetByID($arField['EVENT_NAME'], $arField['LID']);
				$arEventType = $dbRes->Fetch();

				if(!$arEventType)
					$eventType->Add($arField);
				else
					$eventType->Update(array('ID' => $arEventType['ID']), $arField);
			}
		}
		unset($rsET, $dbRes, $arEventType, $arField);

		$rsET = $eventType->GetList($arFilter);
		if($arET = $rsET->Fetch())
		{
			$arFilter = array(
				'TYPE_ID' => $EVENT_NAME,
				'LID'     => $LID,
			);
			$rsMess   = $eventM->GetList($by = "id", $order = "desc", $arFilter);
			if($arEM = $rsMess->Fetch())
			{
				$message_id = $arEM['ID'];
			}
			else
			{
				//If not found LID mess, found other LID mess
				$dbRes = $eventM->GetList($by = "id", $order = "desc", array('TYPE_ID' => $EVENT_NAME));
				if($dbRes->Fetch())
					$arEventMessFields['LID'] = $LID;

				if($emID = $eventM->Add($arEventMessFields))
				{
					$message_id = $emID;
				}
				else
				{
					$arResult = array(
						'result'  => 'error',
						'message' => Loc::getMessage('AOS_TSM_EVENT_MESS_ADD_ERROR'),
					);
				}
			}

			unset($rsET, $arET, $arFilter, $rsMess, $arEM, $dbRes, $emID);
		}
		else
		{
			$arResult = array(
				'result'  => 'error',
				'message' => Loc::getMessage('AOS_TSM_EVENT_TYPE_ADD_ERROR'),
			);
		}



		/////////////////////////////////////////////////////////////////
		//                      Attachments
		/////////////////////////////////////////////////////////////////
		$files       = array();
		$bAttachFile = ($request->get('AOS_ATTACH_FILE') == 'Y');
		if($bAttachFile)
		{
			$row = Api\OrderStatus\FileTable::getList(array(
				'filter' => array('=ORDER_ID' => $ORDER_ID),
			))->fetch();

			$arHistoryFields['FILES'] = ($row['FILE_ID'] ? 'Y' : 'N');

			if($arFilesId = explode(',', $row['FILE_ID']))
				$files = $arFilesId;
		}



		/////////////////////////////////////////////////////////////////
		//                      Send message
		/////////////////////////////////////////////////////////////////
		foreach(GetModuleEvents('main', 'OnBeforeEventAdd', true) as $arEvent)
			if(ExecuteModuleEventEx($arEvent, array(&$EVENT_NAME, &$LID, &$arOrderFields, &$message_id, &$files)) === false)
				return false;

		$arLocalFields = array(
			'EVENT_NAME' => $EVENT_NAME,
			'C_FIELDS'   => $arOrderFields,
			'LID'        => is_array($LID) ? implode(',', $LID) : $LID,
			'DUPLICATE'  => 'Y',
			'FILE'       => $files,
		);
		if($message_id)
			$arLocalFields['MESSAGE_ID'] = intval($message_id);


		$result = Mail\Event::send($arLocalFields);
		Mail\EventManager::executeEvents();

		if($result->isSuccess())
		{
			//Add Status History
			HistoryTable::add($arHistoryFields);

			$arResult = array(
				'result'  => 'ok',
				'message' => Loc::getMessage('AOS_TSM_EVENT_MESSAGE_SEND'),
			);
		}
		else
		{
			$arResult = array(
				'result'  => 'error',
				'message' => Loc::getMessage('AOS_TSM_EVENT_MESSAGE_SEND_ERROR'),
			);
		}
	}
}
else
{
	$arResult = array(
		'result'  => 'error',
		'message' => Loc::getMessage('AOS_TSM_SESSION_EXPIRED'),
	);
}

$APPLICATION->RestartBuffer();
echo Bitrix\Main\Web\Json::encode($arResult);
die();
?>