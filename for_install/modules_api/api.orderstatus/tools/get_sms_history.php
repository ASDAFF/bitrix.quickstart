<?
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
Loc::loadMessages(__FILE__);

global $APPLICATION;

$ORDER_ID  = intval($_REQUEST['ID']);

if(!$ORDER_ID)
	return;

$MODULE_SALE_RIGHT = $APPLICATION->GetGroupRight('sale');
if($MODULE_SALE_RIGHT <= 'D')
{
	return;
}

Loader::includeModule('api.orderstatus');
use Api\OrderStatus\SmsHistoryTable;

$sTableHistory = 'aos_sms_history_table';
$oSortHistory  = new CAdminSorting($sTableHistory);
$lAdminHistory = new CAdminList($sTableHistory, $oSortHistory);

$arHistoryData = SmsHistoryTable::getList(array(
	'order'  => array('ID' => 'DESC'),
	'filter' => array('=ORDER_ID' => $ORDER_ID),
	'select' => array('*'),
))->fetchAll();

$dbRes = new CDBResult;
$dbRes->InitFromArray($arHistoryData);
$dbRecords = new CAdminResult($dbRes, $sTableHistory);


$histHeader = array(
	array('id' => 'ID', 'content' => Loc::getMessage('AOS_TSMSH_ID'), 'sort' => '', 'default' => true),
	array('id' => 'DATE_CREATE', 'content' => Loc::getMessage('AOS_TSMSH_DATE_CREATE'), 'sort' => '', 'default' => true),
	array('id' => 'STATUS_ID', 'content' => Loc::getMessage('AOS_TSMSH_STATUS_ID'), 'sort' => '', 'default' => true),
	array('id' => 'SMS_ID', 'content' => Loc::getMessage('AOS_TSMSH_SMS_ID'), 'sort' => '', 'default' => true),
	array('id' => 'SMS_TEXT', 'content' => Loc::getMessage('AOS_TSMSH_SMS_TEXT'), 'sort' => '', 'default' => true),
	array('id' => 'USER_ID', 'content' => Loc::getMessage('AOS_TSMSH_USER_ID'), 'sort' => '', 'default' => true),
	array('id' => 'SMS_ERROR', 'content' => Loc::getMessage('AOS_TSMSH_SMS_ERROR'), 'sort' => '', 'align' => 'center', 'default' => true),
);
$lAdminHistory->AddHeaders($histHeader);


$arStatuses = array();
$res = CSaleStatus::GetList(
	array(),
	array('LID' => LANGUAGE_ID),
	false,
	false,
	array('ID','NAME')
);
while($arStatus = $res->Fetch())
{
	$arStatuses[ $arStatus['ID'] ] = $arStatus['NAME'];
}

while($arRecord = $dbRecords->Fetch())
{
	$row = &$lAdminHistory->AddRow($arRecord['ID'], $arRecord, '', '');

	$row->AddField('USER_ID', GetFormatedUserName($arRecord['USER_ID'], false));
	$row->AddField('STATUS_ID', $arStatuses[ $arRecord['STATUS_ID'] ]);

	$row->AddField('SMS_ID', $arRecord['SMS_ID']);
	$row->AddField('SMS_TEXT', $arRecord['SMS_TEXT']);
	$row->AddField('SMS_ERROR', $arRecord['SMS_ERROR']);
}

if($_REQUEST['table_id'] == $sTableHistory)
	$lAdminHistory->CheckListMode();
?>
<tr>
	<td id="aos-sms-history">
		<div id="aos-sms-history-table">
			<?
			$lAdminHistory->DisplayList(array("FIX_HEADER" => false, "FIX_FOOTER" => false));
			?>
		</div>
	</td>
</tr>