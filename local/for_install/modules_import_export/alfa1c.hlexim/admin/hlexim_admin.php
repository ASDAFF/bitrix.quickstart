<?
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
//INCLUDE:
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php"); // prolog 1
use Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc as Loc;
Loc::loadMessages(__FILE__);
$inc_add_query = false;
//It's not necessary to pay for this module, u can just inverse this flag to get full version, but u are reducing chances to further improvings and upgrades.
$inc_result = CModule::IncludeModuleEx('alfa1c.hlexim');
if($inc_result >=2){
	$inc_add_query = true;
}
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
if($_SERVER["REQUEST_METHOD"] == "POST" && $_REQUEST["Export"]=="Y")
{
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");

$hlblock = Bitrix\Highloadblock\HighloadBlockTable::getList(
    array(
        'filter'=> array('ID' => $_REQUEST['HL_IBLOCK_ID']),
        'limit' => 1
    )
)->fetch();

if($hlblock['ID'] > 0){
    $arFilter = array("ENTITY_ID" => 'HLBLOCK_'.$hlblock['ID']);
    $rsData = CUserTypeEntity::GetList(array($by=>$order), $arFilter);
    while($arRes = $rsData->Fetch()){
        $hl_fields[] = $arRes;
	$field_types[$arRes['FIELD_NAME']] = $arRes['USER_TYPE_ID'];
    }
}

Loader::includeModule('alfa1c.hlexim');

$Entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
$Query = new \Bitrix\Main\Entity\Query($Entity);
$Query->setSelect(array('*'));
$Query->setFilter(array());
$Query->setOrder(array());

if($inc_add_query){
	$Query->setLimit(400 - (100 * $inc_result));
}

$result = $Query->exec();

$result = $result->FetchAll();

foreach($field_types as $key => $filetype){
	if($filetype == 'file'){
		$arFiletypes[] = $key;
	}
}

$filepath = (!$_REQUEST['URL_DATA_FILE']) ? $_SERVER["DOCUMENT_ROOT"].'/upload/'.$hlblock['TABLE_NAME'].'.txt' : $_SERVER["DOCUMENT_ROOT"].$_REQUEST['URL_DATA_FILE'];
$filedir = ($_REQUEST['URL_DATA_FILE']) ? GetDirPath($_REQUEST['URL_DATA_FILE']) : '/upload/';
$filename = str_replace('.txt','', CFileMan::GetFileName($filepath));

foreach($result as $key => $elements)
{
	foreach($elements as $elemname => $elemval)
	{
		if(in_array($elemname,$arFiletypes) && $elemval > 0){
			
			$arFile = CFile::GetByID($elemval)->Fetch();
			$src = CFile::GetPath($elemval);

			if(!$filename){
				CopyDirFiles($_SERVER["DOCUMENT_ROOT"].$src, $_SERVER["DOCUMENT_ROOT"].'/upload/'.$hlblock['TABLE_NAME'].'_files/'.$arFile["SUBDIR"].'/'.$arFile["FILE_NAME"], true, true, false , "");	
			}
			else{
				CopyDirFiles($_SERVER["DOCUMENT_ROOT"].$src, $_SERVER["DOCUMENT_ROOT"].$filedir.$filename.'_files/'.$arFile["SUBDIR"].'/'.$arFile["FILE_NAME"], true, true, false , "");
			}
			$result[$key][$elemname] = ($filename) ? $filedir.$filename.'_files/'.$arFile["SUBDIR"].'/'.$arFile["FILE_NAME"] : '/upload/'.$hlblock['TABLE_NAME'].'_files/'.$arFile["SUBDIR"].'/'.$arFile["FILE_NAME"]; 
		}
	
	}
}

unset($arFile);
unset($src);


// пример использования
$hl_data['bddata'] = $hlblock;
$hl_data['fieldsinfo'] = $hl_fields;
$hl_data['fieldsdata'] = $result;

$hlexport = new HlExport();

$w_result = $hlexport -> writeArrayInFile($hl_data, $filepath);

if($w_result){
	CAdminMessage::ShowMessage(array(
		"MESSAGE" => Loc::getMessage("HLEXIM_SUCCESS"),
		"DETAILS" => "<p>".Loc::getMessage('HLEXIM_HL_TITLE')." ".$hlblock['TABLE_NAME']."</p><p>".Loc::getMessage('HLEXIM_HL_COUNT')." ".count($result)."</p>",
		"HTML" => true,
		"TYPE" => "OK",
	));
}
else{
	CAdminMessage::ShowMessage(array(
		"MESSAGE" => Loc::getMessage("HLEXIM_FAIL"),
		"DETAILS" => $tableName,
		"HTML" => true,
		"TYPE" => "ERROR",
	));
}
echo '<script>EndExport();</script>';
?>
	<script>
		CloseWaitWindow();
	</script>
<?
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin_js.php");
}
elseif($_SERVER["REQUEST_METHOD"] == "POST" && $_REQUEST["Import"]=="Y"){
	
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
?>

<script>
var running = false;

function DoNext(NS)
{
	var interval = parseInt(document.getElementById('INTERVAL').value);
	var queryString =
		'Export=Y'
		+ '&lang=<?=LANGUAGE_ID?>'
		+ '&<?echo bitrix_sessid_get()?>'
		+ '&INTERVAL=' + interval
	;

	if(!NS)
	{
		queryString+='&URL_DATA_FILE='+jsUtils.urlencode(document.getElementById('URL_DATA_FILE').value);
		queryString+='&HL_IBLOCK_ID='+jsUtils.urlencode(document.getElementById('HL_IBLOCK_ID').value);
	}

	if(running)
	{
		ShowWaitWindow();
		document.getElementById('tbl_iblock_export_result_div').innerHTML = '';
		BX.ajax.post(
			'hlexim_admin.php?'+queryString,
			NS,
			function(result){
				document.getElementById('tbl_iblock_export_result_div').innerHTML = result;
			}
		);
	}
}
function StartExport()
{
	running = document.getElementById('start_button').disabled = true;
	DoNext();
}
function EndExport()
{
	running = document.getElementById('start_button').disabled = false;
}
</script>

<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?lang=<?echo htmlspecialcharsbx(LANG)?>" name="form1" id="form1">
<?
$tabControl->Begin();
$tabControl->BeginNextTab();

if($higloadblock){
	$ex_hlblock = Bitrix\Highloadblock\HighloadBlockTable::getList(array());
	if($ex_hlblock->fetch()){
		$hl_exist = 1;
	}
	else{
		$hl_exist = 0;
	}
}
if($higloadblock && $hl_exist){
?>
		<?
		if($inc_add_query){
		?>
		<tr>
			<td></td><td style="color: #750000;"><?echo Loc::getMessage("HLEXIM_HL_DM_MESS_1")?> <?=(400 - (100 * $inc_result));?><?echo Loc::getMessage("HLEXIM_HL_DM_MESS_2")?></td>
		</tr>
		<?
		}
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
					"operation" => 'S',// O - open, S - save
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
		<td><?echo Loc::getMessage("HLEXIM_HL_BLOCK")?>:</td>
		<td>
		    
		    <select id="HL_IBLOCK_ID">
		    <?
		    $hlblock = Bitrix\Highloadblock\HighloadBlockTable::getList(array());
		    while ($arData = $hlblock->fetch()){
			    $ent = $arData;
			    $hl_list_entity[] = 'HLBLOCK_'.$arData['ID'];
		    ?>
			<option value="<?=$arData['ID']?>"><?=$arData['NAME']?></option>
		    <?
		    }
		    ?>
		    </select>
		</td>
	</tr>
	<tr>
		<td><?echo Loc::getMessage("HLEXIM_INTERVAL")?>:</td>
		<td>
			<input type="text" id="INTERVAL" name="INTERVAL" size="5" value="<?echo intval($INTERVAL)?>">
		</td>
	</tr>
<?$tabControl->Buttons();?>
	<input type="button" id="start_button" value="<?echo Loc::getMessage("HLEXIM_START_EXPORT")?>" OnClick="StartExport();" class="adm-btn-save">
	<input type="button" id="stop_button" value="<?echo Loc::getMessage("HLEXIM_STOP_EXPORT")?>" OnClick="EndExport();">
<?
}
else{
	echo Loc::getMessage("HLEXIM_HL_NA_ERROR");
}
?>
<?$tabControl->End();?>
</form>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>