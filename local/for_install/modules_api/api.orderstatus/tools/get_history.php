<?
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
Loc::loadMessages(__FILE__);

global $APPLICATION;

$MODULE_ID = 'api.orderstatus';
$ORDER_ID  = intval($_REQUEST['ID']);

if(!$ORDER_ID)
	return;

$MODULE_SALE_RIGHT = $APPLICATION->GetGroupRight('sale');
if($MODULE_SALE_RIGHT <= 'D')
{
	return;
}

Loader::includeModule($MODULE_ID);
use Api\OrderStatus\HistoryTable;

$sTableHistory = 'aos_history_table';
$oSortHistory  = new CAdminSorting($sTableHistory);
$lAdminHistory = new CAdminList($sTableHistory, $oSortHistory);

$arHistoryData = HistoryTable::getList(array(
	'order'  => array('ID' => 'DESC'),
	'filter' => array('=ORDER_ID' => $ORDER_ID),
	'select' => array('*'),
))->fetchAll();

$dbRes = new CDBResult;
$dbRes->InitFromArray($arHistoryData);
$dbRecords = new CAdminResult($dbRes, $sTableHistory);


$histHeader = array(
	array('id' => 'ID', 'content' => Loc::getMessage('AOS_HISTORY_ID'), 'sort' => '', 'default' => true),
	array('id' => 'DATE_CREATE', 'content' => Loc::getMessage('AOS_HISTORY_DATE_CREATE'), 'sort' => '', 'default' => true),
	array('id' => 'STATUS', 'content' => Loc::getMessage('AOS_HISTORY_STATUS'), 'sort' => '', 'default' => true),
	array('id' => 'DESCRIPTION', 'content' => Loc::getMessage('AOS_HISTORY_DESCRIPTION'), 'sort' => '', 'default' => true),
	array('id' => 'USER_ID', 'content' => Loc::getMessage('AOS_HISTORY_USER_ID'), 'sort' => '', 'default' => true),
	array('id' => 'MAIL', 'content' => Loc::getMessage('AOS_HISTORY_MAIL'), 'sort' => '', 'align' => 'center', 'default' => true),
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

$strOrderFiles = '';
if($arOrderFiles = Api\OrderStatus\FileTable::getOrderFiles($ORDER_ID))
{
	$strOrderFiles .= '<div class="history-files">';

	foreach($arOrderFiles as $arFile)
	{
		$fileExt  = pathinfo($arFile['FILE_NAME'], PATHINFO_EXTENSION);
		$strOrderFiles .= '<div>
									<span class="api-file-ext-'. $fileExt .'"></span>
									<a target="_blank" href="/upload/'. $arFile['SUBDIR'] .'/'. $arFile['FILE_NAME'] .'">'. $arFile['ORIGINAL_NAME'] .'</a>
								</div>';
	}

	$strOrderFiles .= '</div>';
}

while($arRecord = $dbRecords->Fetch())
{
	$row = &$lAdminHistory->AddRow($arRecord['ID'], $arRecord, '', '');

	$row->AddField('USER_ID', GetFormatedUserName($arRecord['USER_ID'],false));
	$row->AddField('STATUS', $arStatuses[ $arRecord['STATUS'] ]);

	if($arRecord['FILES'] == 'Y' && $strOrderFiles)
	{
		$arRecord['DESCRIPTION'] .= $strOrderFiles;
	}

	$row->AddField('DESCRIPTION', $arRecord['DESCRIPTION']);


	if($arRecord['MAIL'] == 'N')
		$row->AddField('MAIL', '<span style="color:red">'. Loc::getMessage('AOS_HISTORY_FLAG_'. $arRecord['MAIL']) .'</span>');
	else
		$row->AddField('MAIL', '<span style="color:green">'. Loc::getMessage('AOS_HISTORY_FLAG_'. $arRecord['MAIL']) .'</span>');

}

if($_REQUEST['table_id'] == $sTableHistory)
	$lAdminHistory->CheckListMode();
?>
<tr>
	<td id="aos-history">
		<div id="aos-history-table">
			<?
			$lAdminHistory->DisplayList(array("FIX_HEADER" => false, "FIX_FOOTER" => false));
			?>
		</div>
	</td>
</tr>