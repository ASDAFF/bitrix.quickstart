<?
//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//INCLUDE:
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php"); // prolog 1
use Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc as Loc;
Loc::loadMessages(__FILE__);
?>
<?
//$APPLICATION->AddHeadScript('/bitrix/js/alfa1c.hlexim/scripts.js');
$APPLICATION->AddHeadScript('/bitrix/js/alfa1c.hlexim/jquery-1.9.1.min.js');

// check permissions
$POST_RIGHT = $APPLICATION->GetGroupRight("alfa1c.hlexim");
// got auth if no permissions
if ($POST_RIGHT == "D")
    $APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));

$higloadblock = Loader::includeModule('highloadblock');
?>
<?
if($_SERVER["REQUEST_METHOD"] == "POST" && $_REQUEST["Import"]=="Y")
{
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");
	Loader::includeModule('alfa1c.hlexim');
	$hlimport = new HlExport();
	$cnt = 0;

	$import_data = $hlimport -> readFileInArray($_SERVER["DOCUMENT_ROOT"].$_REQUEST["URL_DATA_FILE"]);
	
	if(empty($import_data['bddata'])){
		$res = array('type' => 'failure','text' => 'Нет данных');
		echo \Bitrix\Main\Web\Json::encode($res);
		die();
	}

	$hlblock = Bitrix\Highloadblock\HighloadBlockTable::getList(
	    array(
	        'filter' => array('=NAME' => $import_data['bddata']['NAME']),
	        'limit' => 1,
	        'select' => array('ID')
	    )
	)->fetch();
	
	
	if($hlblock['ID'] > 0){
	    Bitrix\Highloadblock\HighloadBlockTable::delete($hlblock['ID']);
	}
	
	$result = Bitrix\Highloadblock\HighloadBlockTable::add(array(
	    'NAME' => $import_data['bddata']['NAME'],
	    'TABLE_NAME' => strtolower($import_data['bddata']['NAME']),
	));
	
	$hlblock['ID'] = $result->getId();

	$arFieldsName = $import_data['fieldsinfo'];
	$obUserField = new CUserTypeEntity();
	$sort = 100;
	foreach($arFieldsName as $fieldName => $fieldValue)
	{
	    $fieldValue['ENTITY_ID'] = "HLBLOCK_".$hlblock['ID'];
	    $arUserField = $fieldValue;
	    $res = $obUserField->Add($arUserField);
	    if ($res)
	    {
	        $sort += 100;
	    }
	    else
	    {
	        return 0;
	    }

	    if($fieldValue['USER_TYPE_ID'] == 'file'){
		$arFiletypes[] = $fieldValue['FIELD_NAME'];
	    }

	}
	
	$hlblock = Bitrix\Highloadblock\HighloadBlockTable::getList(array(
	    "filter" => array(
	        "=ID" => $hlblock['ID'],
	    )))->fetch();
	$entity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);

	$entity_data_class = $entity->getDataClass();

	foreach($import_data['fieldsdata'] as $key => $arFields)
	{
			foreach($arFields as $fieldName => $fieldVal)
			{
				if(in_array($fieldName,$arFiletypes) && $fieldVal){
					$arFields[$fieldName] = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].$fieldVal);
				}
			}
	    $entity_data_class::add($arFields);
	    $cnt++;
	}
	if($cnt > 0){
		$res = array('type' => 'success','count' => $cnt, 'text' => 'Импорт успешно завершен');
		echo \Bitrix\Main\Web\Json::encode($res);
	}
	else{
		$res = array('type' => 'failure','text' => 'Нет элементов для импорта');
		echo \Bitrix\Main\Web\Json::encode($res);
	}
?>
<?
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin_js.php");
}
?>
<?
$APPLICATION->SetTitle(Loc::getMessage("HLEXIM_MODULE_NAME"));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php"); // prolog 2
include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fileman/classes/general/fileman_utils.php");
?>
<?
$site = (SITE_ID) ? SITE_ID : 's1';
$title = Loc::getMessage("HLEXIM_MODULE_NAME");
$charset = strtolower(LANG_CHARSET);
?>
<div id="tbl_iblock_export_result_div"></div>
<?
$aTabs = array(
	array(
		"DIV" => "edit1",
		"TAB" => Loc::getMessage("HLEXIM_TAB"),
		"ICON" => "main_user_edit",
		"TITLE" => Loc::getMessage("HLEXIM_TAB_TITLE"),
	),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs, true, true);
unset($_SESSION["BX_HL_IMPORT"]);
?>

<script>
var running = false;

function DoNext(NS)
{
	var interval = parseInt(document.getElementById('INTERVAL').value);

	var queryString =
		'Import=Y'
		+ '&lang=<?=LANGUAGE_ID?>'
		+ '&<?echo bitrix_sessid_get()?>'
		+ '&INTERVAL=' + interval
	;

	if(!NS)
	{
		queryString+='&URL_DATA_FILE='+jsUtils.urlencode(document.getElementById('URL_DATA_FILE').value);
	}
	if(running)
	{
		ShowWaitWindow();
		BX.ajax.post(
			'hlexim_admin_import.php?'+queryString,
			NS,
			function(result){
				var result = jQuery.parseJSON(result);
				if(result.type == 'progress'){
					console.log(result.text);
					//document.getElementById('tbl_iblock_export_result_div').innerHTML = '<div class="adm-info-message-wrap adm-info-message-gray"><div class="adm-info-message"><div class="adm-info-message-title">Импорт</div><p>'+result.text+'</p><div class="adm-info-message-buttons"></div></div></div>';
					DoNext();
				}
				else if(result.type == 'failure'){
					console.log(result.text);
					document.getElementById('tbl_iblock_export_result_div').innerHTML = '';
					document.getElementById('tbl_iblock_export_result_div').innerHTML = '<div class="adm-info-message-wrap adm-info-message-red"><div class="adm-info-message"><div class="adm-info-message-title">'+result.text+'</div><div class="adm-info-message-icon"></div></div></div>';
					EndImport();
					CloseWaitWindow();
				}
				else{
					console.log(result.text);
					document.getElementById('tbl_iblock_export_result_div').innerHTML = '<div class="adm-info-message-wrap adm-info-message-green"><div class="adm-info-message"><div class="adm-info-message-title">'+result.text+'</div><p><?=Loc::getMessage("HLEXIM_HL_COUNT");?> '+result.count+'</p><div class="adm-info-message-icon"></div></div></div>';
					EndImport();
					CloseWaitWindow();	
				}
			}
		);
	}
}
function StartImport()
{
	running = document.getElementById('start_button').disabled = true;
	DoNext();
}
function EndImport()
{
	running = document.getElementById('start_button').disabled = false;
}
</script>

<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?lang=<?echo htmlspecialcharsbx(LANG)?>" name="form1" id="form1">
<?
$tabControl->Begin();
$tabControl->BeginNextTab();
if($higloadblock){
?>
	<tr>
		<td width="40%"><?echo Loc::getMessage("HLEXIM_URL_DATA_FILE")?>:</td>
		<td width="60%">
			<input type="text" id="URL_DATA_FILE" name="URL_DATA_FILE" size="30" value="<?=htmlspecialcharsbx($URL_DATA_FILE)?>">
			<input type="button" value="<?echo Loc::getMessage("HLEXIM_URL_DATA_FILE_OPEN")?>" OnClick="BtnClick()">
			<?
			CAdminFileDialog::ShowScript
			(
				Array(
					"event" => "BtnClick",
					"arResultDest" => array("FORM_NAME" => "form1", "FORM_ELEMENT_NAME" => "URL_DATA_FILE"),
					"arPath" => array("SITE" => SITE_ID, "PATH" =>"/upload"),
					"select" => 'F',// F - file only, D - folder only
					"operation" => 'O',// O - open, S - save
					"showUploadTab" => true,
					"showAddToMenuTab" => false,
					"fileFilter" => 'txt',
					"allowAllFiles" => true,
					"SaveConfig" => true,
				)
			);
			?>
		</td>
	</tr>
	<tr>
		<td><?echo Loc::getMessage("HLEXIM_INTERVAL")?></td>
		<td>
			<input type="text" id="INTERVAL" name="INTERVAL" size="5" value="10">
		</td>
	</tr>
<?$tabControl->Buttons();?>
	<input type="button" id="start_button" value="<?echo Loc::getMessage("HLEXIM_START_IMPORT")?>" OnClick="StartImport();" class="adm-btn-save">
	<input type="button" id="stop_button" value="<?echo Loc::getMessage("HLEXIM_STOP_IMPORT")?>" OnClick="EndImport();">
<?
}
else{
	echo Loc::getMessage("HLEXIM_HL_NA_ERROR");
}
?>
<?$tabControl->End();?>
</form>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>