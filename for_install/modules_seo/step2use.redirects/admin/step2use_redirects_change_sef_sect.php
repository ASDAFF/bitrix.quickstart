<?php

//include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/mysql/database.php");
//include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/mysql/database_mysql.php");
//include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/classes/general/iblockresult.php");


require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/prolog.php");

$moduleID = 'step2use.redirects';

if(!CModule::IncludeModule($moduleID)) die('no module '.$moduleID);

// lang
IncludeModuleLangFile(__FILE__);

// check access
/*if (!$USER->CanDoOperation('edit_php') && !$USER->CanDoOperation('view_other_settings'))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
// is admin
$isAdmin = $USER->CanDoOperation('edit_php');*/

$isAdmin = S2uRedirects::canAdminThisModule() || $USER->CanDoOperation('edit_php');
if(!$isAdmin) {
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

if(isset($_REQUEST['Reindex']) && $_REQUEST['Reindex']=='Y') {
    
// ID инфоблока с товарами
$ATL_REDIRECTS_IBLOCK_ID = intval($_POST['iblockid']);
define("ADMIN_SECTION", true);

$tplFrom = htmlspecialcharsEx($_POST['tpl_from_sect']); // "#SITE_DIR#/catalog/#SECTION_CODE_PATH#/";
$tplTo = htmlspecialcharsEx($_POST['tpl_to_sect']); // "#SITE_DIR#/catalog/#SECTION_CODE_PATH#/";

if(!$ATL_REDIRECTS_IBLOCK_ID) {
    $message = new CAdminMessage(array(
            'MESSAGE' => GetMessage('ATL_IBLOCK_ERROR'), 
        	'TYPE' => 'ERROR',
        	'HTML' => true
		)
	);
    echo $message->Show();
    exit;
}
elseif(!$tplFrom) {
    $message = new CAdminMessage(array(
            'MESSAGE' => GetMessage('ATL_TPL_FROM_ERROR_SECT'),
        	'TYPE' => 'ERROR',
        	'HTML' => true
		)
	);
    echo $message->Show();
    exit;
}
elseif(!$tplTo) {
    $message = new CAdminMessage(array(
            'MESSAGE' => GetMessage('ATL_TPL_TO_ERROR_SECT'),
        	'TYPE' => 'ERROR',
        	'HTML' => true
		)
	);
    echo $message->Show();
    exit;
}

CModule::IncludeModule('iblock');
CModule::IncludeModule('step2use.redirects');

$repair_conflicts = COption::GetOptionString('step2use.redirects', 'REPAIR_CONFLICTS', 'N');

$arIblock = CIBlock::GetArrayByID($ATL_REDIRECTS_IBLOCK_ID);

include_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/classes/general/iblockresult.php");
            
// Получаем массив сайтов, которые привязаны к инфоблоку
$arIblockSites = array();
$rsSites = CIBlock::GetSite($ATL_REDIRECTS_IBLOCK_ID);
while($arSite = $rsSites->Fetch()) {
    $arIblockSites[] = $arSite["SITE_ID"];
}
// Список разделов
$i = 0;
$resSect = CIBlockSection::GetList(array(), array("IBLOCK_ID" => $ATL_REDIRECTS_IBLOCK_ID));
while ($arItem = $resSect->Fetch()) {
	foreach($arIblockSites as $siteID) {
		$arItem["LID"] = $siteID;
        $arItem["~LID"] = $siteID;
	    $arItem["EXTERNAL_ID"] = $arItem["XML_ID"];
		//с  #SITE_DIR#/catalog/#SECTION_CODE#/
	    $OLD_LINK = CIBlock::ReplaceSectionUrl($tplFrom, $arItem, $_SERVER["SERVER_NAME"], "S");
	    //на #SITE_DIR#/catalog/#SECTION_CODE_PATH#/
	    $NEW_LINK = CIBlock::ReplaceSectionUrl($tplTo, $arItem, $_SERVER["SERVER_NAME"], "S");
	    
	    $COMMENT = GetMessage('ATL_REDIRECT_COMMENT', array('#ID#' => $arItem["ID"]));
		$arrDbFields = array(
            'OLD_LINK' => trim($OLD_LINK),
			'NEW_LINK' => trim($NEW_LINK),
			'DATE_TIME_CREATE' => ConvertTimeStamp(time(), 'FULL'),
			'STATUS' => "301",
			'ACTIVE' => "Y",
			'COMMENT' => $COMMENT,
			'SITE_ID' => $siteID, 
			'WITH_INCLUDES' => "N",
			'USE_REGEXP' => "N",
		);	
		
		if($repair_conflicts){
			$Res__ = S2uRedirectsRulesDB::RepairConflicts($arrDbFields);
		} else {
			$Res__ = S2uRedirectsRulesDB::Add($arrDbFields);
		}	
	}
	$i++;
}

$message = new CAdminMessage(array(
		'MESSAGE' => GetMessage('ATL_RESULT_TITLE_END'),
		'TYPE' => 'OK',
		'DETAILS' => GetMessage('ATL_RESULT_NUMS', array('#CNT#' => $i, '#LAST#' => $url, '#PAGE#' => $page)),
		'HTML' => true
	));
echo $message->Show();

?>
<script>
    CloseWaitWindow();
</script>
        <?
    exit;
}

//--------PREPARE THE FORM DATA.
// browser's title
$APPLICATION->SetTitle(GetMessage("ATL_CHANGE_SEF_TITLE"));

// indlude admin core
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

$aTabs = array(
    array("DIV" => "edit1", "TAB" => GetMessage("ATL_CHANGE_SEF_TITLE"), "ICON" => ""),
);

$tabControl = new CAdminTabControl("tabControl", $aTabs);

?>

<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?=LANGUAGE_ID?>">

<? $tabControl->Begin(); ?>
<style>
    .field-name {
        width: 200px;
    }
</style>

<?
// основные настройки

// список инфоблоков
$arIblocks = array("");
$arIblocksTypes = array();
CModule::IncludeModule("iblock");
$db_iblock_type = CIBlockType::GetList();
while($ar_iblock_type = $db_iblock_type->Fetch())
{
   if($arIBType = CIBlockType::GetByIDLang($ar_iblock_type["ID"], LANG))
   {
      $arIblocksTypes[$ar_iblock_type["ID"]] = $arIBType["NAME"];
   }   
}

$res = CIBlock::GetList(Array("iblock_type"=>"asc"));
while($ar_res = $res->Fetch()) {
    $arIblocks[$ar_res['ID']] = "[".$arIblocksTypes[$ar_res['IBLOCK_TYPE_ID']]."] ".$ar_res['NAME'];
}

$arSiteDropdown = array("reference" => array_values($arIblocks), "reference_id" => array_keys($arIblocks));

$tabControl->BeginNextTab();
?>
<tr><td colspan="2" id="reindex_result_div"></td></tr>
<tr>
    <td class="field-name"><? echo GetMessage('ATL_CHANGE_SEF_IBLOCK'); ?></td>
    <td>
        <?echo SelectBoxFromArray("atl_iblock_id", $arSiteDropdown);?>
    </td>
</tr>

<tr>
    <td class="field-name"><? echo GetMessage('ATL_CHANGE_SEF_URL_FROM_TPL_SECT'); ?></td>
    <td>
        <input name="atl_page_tpl_from_sect" id="atl_page_tpl_from_sect" value="<? echo htmlspecialcharsEx($_POST['atl_page_tpl_from_sect']); ?>"> <span id="hint_tpl1_sect"></span>
    </td>
</tr>
<tr>
    <td class="field-name"><? echo GetMessage('ATL_CHANGE_SEF_URL_TO_TPL_SECT'); ?></td>
    <td>
        <input name="atl_page_tpl_to_sect" id="atl_page_tpl_to_sect" value="<? echo htmlspecialcharsEx($_POST['atl_page_tpl_to_sect']); ?>"> <span id="hint_tpl2_sect"></span>
    </td>
</tr>

<? $tabControl->Buttons();?>
<!--<input type="submit" <?if ($MODULE_RIGHT<"W") echo "disabled" ?> name="Update" value="<?echo GetMessage("MAIN_SAVE")?>">-->
    <?/*<input type="hidden" id="site_id" value="s1">*/ ?>
    <input type="button" id="start_button" value="<? echo GetMessage('ATL_CHANGE_SEF_START'); ?>" onclick="StartReindex();">
	<?/*<input type="button" id="stop_button" value="<? echo GetMessage('ATL_CHANGE_SEF_STOP'); ?>" onclick="StopReindex();" disabled="">
	<input type="button" id="continue_button" value="<? echo GetMessage('ATL_CHANGE_SEF_CONTINUE'); ?>" onclick="ContinueReindex();" disabled="">
	*/?>
<?=bitrix_sessid_post();?>
<?$tabControl->End();?>
</form>

<? echo BeginNote(); ?>
<? echo GetMessage("ATL_CHANGE_SEF_HELP") ?>
<? echo EndNote(); ?>

<script>

var hint_tpl = '<?=GetMessage("ATL_HINT_TPL")?><br/>#SITE_DIR#/catalog/#SECTION_CODE#/<br/>#SITE_DIR#/catalog/#SECTION_CODE_PATH#/<br/>#SITE_DIR#/catalog/#SECTION_ID#/<br/>#SITE_DIR#/catalog/#SECTION_CODE#/';

BX.hint_replace(BX('hint_tpl1_sect'), hint_tpl);
BX.hint_replace(BX('hint_tpl2_sect'), hint_tpl);

var savedNS;
var stop;
var interval = 0;
function StartReindex()
{
	stop=false;
	document.getElementById('reindex_result_div').innerHTML='';
	//document.getElementById('stop_button').disabled=false;
	document.getElementById('start_button').disabled=true;
	//document.getElementById('continue_button').disabled=true;
	DoNext();
}
function DoNext(NS)
{
	var queryString = 'Reindex=Y';
	if(!NS)
	{
        interval = 0;
		queryString += '&sessid=<?=bitrix_sessid();?>';
	}

	savedNS = NS;

	if(!stop)
	{
		ShowWaitWindow();
		BX.ajax.post(
			'step2use_redirects_change_sef_sect.php?'+queryString,
			{
			    iblockid: document.getElementById('atl_iblock_id').value,
			    tpl_from_sect: document.getElementById('atl_page_tpl_from_sect').value,
			    tpl_to_sect: document.getElementById('atl_page_tpl_to_sect').value
			},
			function(result){
				document.getElementById('reindex_result_div').innerHTML = result;
				var href = document.getElementById('continue_href');
				if(!href)
				{
					CloseWaitWindow();
					StopReindex();
				}
			}
		);
	}

	return false;
}
function StopReindex()
{
	stop=true;
	document.getElementById('start_button').disabled=false;
}
function ContinueReindex()
{
//
}
function EndReindex()
{
	stop=true;
	document.getElementById('start_button').disabled=false;
}
</script>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php"); ?>
