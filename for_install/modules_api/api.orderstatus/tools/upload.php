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
$arResult = array();

$arModules = array(
	'api.orderstatus' => Loader::includeModule($MODULE_ID),
	'sale'            => Loader::includeModule('sale'),
);

if(!$arModules[ $MODULE_ID ])
{
	$arResult = array(
		'result'  => 'error',
		'message' => Loc::getMessage('AOS_TU_MODULE_ERROR'),
	);
}

if(!$arModules['sale'])
{
	$arResult = array(
		'result'  => 'error',
		'message' => Loc::getMessage('AOS_TU_SALE_MODULE_ERROR'),
	);
}

$MODULE_SALE_RIGHT = $APPLICATION->GetGroupRight('sale');
if($MODULE_SALE_RIGHT <= 'D')
{
	$arResult = array(
		'result'  => 'error',
		'message' => Loc::getMessage('AOS_TU_ACCESS_DENIED'),
	);
}

use Api\OrderStatus\FileTable;
$context = \Bitrix\Main\Application::getInstance()->getContext();
$request = $context->getRequest();

/////////////////////////////////////////////////////////////////
//                           EXEC
/////////////////////////////////////////////////////////////////

if(check_bitrix_sessid())
{
	$action    = $request->get('action');
	$ORDER_ID  = $request->get('ORDER_ID');
	$STATUS_ID = $request->get('STATUS_ID');
	$FILE_ID   = $request->get('FILE_ID');

	if($ORDER_ID)
	{
		if($action == 'delete')
		{
			if($FILE_ID)
			{
				$dbFiles = FileTable::getList(array(
					'filter' => array('=ORDER_ID' => $ORDER_ID)
				));

				if($arOrderFiles = $dbFiles->fetch())
				{
					$arExpFiles = explode(',',$arOrderFiles['FILE_ID']);
					if($arExpFiles)
					{
						foreach($arExpFiles as $key => $val)
						{
							if($val == $FILE_ID)
								unset($arExpFiles[$key]);
						}
					}

					$strFiles = implode(',',$arExpFiles);
					FileTable::update(
						$arOrderFiles['ID'],
						array(
							'ORDER_ID' => $ORDER_ID,
							'FILE_ID'  => TrimExAll($strFiles,',')
						)
					);
				}

				CFile::Delete($FILE_ID);
			}

			$arResult = array(
				'result'  => 'ok',
				'message' => '',
			);
		}
		else
		{
			$arFile = $_FILES['file'];
			$arFile['name'] = $APPLICATION->ConvertCharset($arFile['name'], 'UTF-8', LANG_CHARSET);
			$arFile['MODULE_ID'] = $MODULE_ID;
			$FILE_ID = CFile::SaveFile($arFile,$MODULE_ID);

			$dbFiles = FileTable::getList(array(
				'filter' => array('=ORDER_ID' => $ORDER_ID)
			));

			if($arOrderFiles = $dbFiles->fetch())
			{
				FileTable::update(
					$arOrderFiles['ID'],
					array(
						'ORDER_ID' => $ORDER_ID,
						'FILE_ID'  => ($arOrderFiles['FILE_ID'] ? $arOrderFiles['FILE_ID'] . ','. $FILE_ID : $FILE_ID),
					));
			}
			else
			{
				FileTable::add(array(
					'ORDER_ID'  => $ORDER_ID,
					'FILE_ID'   => $FILE_ID,
				));
			}

			$arResult = array(
				'result'  => 'ok',
				'message' => '',
				'id' => $FILE_ID,
			);
		}
	}
}
else
{
	$arResult = array(
		'result'  => 'error',
		'message' => Loc::getMessage('AOS_TU_SESSION_EXPIRED'),
	);
}


$APPLICATION->RestartBuffer();
echo Bitrix\Main\Web\Json::encode($arResult);
die();
?>