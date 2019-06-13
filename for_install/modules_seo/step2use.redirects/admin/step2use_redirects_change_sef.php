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
/*if (!$USER->CanDoOperation('edit_php') && !$USER->CanDoOperation('view_other_settings')) {
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}
// is admin
$isAdmin = $USER->CanDoOperation('edit_php');*/

$isAdmin = S2uRedirects::canAdminThisModule() || $USER->CanDoOperation('edit_php');
if(!$isAdmin) {
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

$iNumPage = 1;
if (isset($_REQUEST['Reindex']) && $_REQUEST['Reindex']=='Y') {
    
    // ID инфоблока с товарами
	$ATL_REDIRECTS_IBLOCK_ID = intval($_POST['iblockid']);
	define("ADMIN_SECTION", true);

	$iNumPage = (int) $_POST["iNumPage"];
	$nPageSize = ((int) $_POST["nPageSize"]) ?: 20;
	$tplFrom = htmlspecialcharsEx($_POST['tpl_from']); // "#SITE_DIR#/catalog/#SECTION_CODE_PATH#/#ELEMENT_ID#/";
	$tplTo = htmlspecialcharsEx($_POST['tpl_to']); // "#SITE_DIR#/catalog/#SECTION_CODE_PATH#/#ELEMENT_CODE#/";

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
				'MESSAGE' => GetMessage('ATL_TPL_FROM_ERROR'), 
				'TYPE' => 'ERROR',
				'HTML' => true
			)
		);
		echo $message->Show();
		exit;
	}
	elseif(!$tplTo) {
		$message = new CAdminMessage(array(
				'MESSAGE' => GetMessage('ATL_TPL_To_ERROR'),
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
	while ($arSite = $rsSites->Fetch()) {
		$arIblockSites[] = $arSite["SITE_ID"];
	}
	
	$res = CIBlockElement::GetList(
		array("SORT" => "ASC"), 
		array("IBLOCK_ID" => $ATL_REDIRECTS_IBLOCK_ID), 
		false, 
		array("iNumPage" => $iNumPage, "nPageSize" => $nPageSize, "checkOutOfRange" => true), 
		array()
	);

	$arFields = array();
	while ($ob = $res->GetNextElement()) {
		$arFields = $ob->GetFields();
		
		foreach ($arIblockSites as $siteID) {
			$arFields["LID"] = $siteID;
			$arFields["~LID"] = $siteID;
			$arFields["EXTERNAL_ID"] = $arFields["XML_ID"];
			
			//с  #SITE_DIR#/catalog/#SECTION_CODE_PATH#/#ELEMENT_ID#/
			$OLD_LINK = CIBlock::ReplaceDetailUrl($tplFrom, $arFields, $_SERVER["SERVER_NAME"], "E");
			//на #SITE_DIR#/catalog/#SECTION_CODE_PATH#/#ELEMENT_CODE#/
			$NEW_LINK = CIBlock::ReplaceDetailUrl($tplTo, $arFields, $_SERVER["SERVER_NAME"], "E");
			
			$COMMENT = GetMessage('ATL_REDIRECT_COMMENT', array('#ID#'=>$arFields["ID"]));
			
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
	}

	sleep(2);

	if (!empty($arFields)) {
		$_SESSION["ATL_COMPLETED_CNT"] = $nPageSize * $iNumPage;
		echo $iNumPage;
	} else {
		$message = new CAdminMessage(array(
				'MESSAGE' => GetMessage('ATL_RESULT_TITLE_END'),
				'TYPE' => 'OK',
				'DETAILS' => GetMessage('ATL_RESULT_NUMS', array('#CNT#' => $_SESSION["ATL_COMPLETED_CNT"], '#LAST#' => $url, '#PAGE#' => $page)),
				'HTML' => true
			)
		);
		echo $message->Show();
	}
	
	exit();
}
?>

<?
if ($_REQUEST["completed"] === "Y") {
	$message = new CAdminMessage(array(
            'MESSAGE' => GetMessage('ATL_RESULT_TITLE_END'),
        	'TYPE' => 'OK',
        	'DETAILS' => GetMessage('ATL_RESULT_NUMS', array('#CNT#' => $_SESSION["ATL_COMPLETED_CNT"], '#LAST#' => $url, '#PAGE#' => $page)),
        	'HTML' => true
        )
	);
    echo $message->Show();
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
while ($ar_res = $res->Fetch()) {
    $arIblocks[$ar_res['ID']] = "[".$arIblocksTypes[$ar_res['IBLOCK_TYPE_ID']]."] ".$ar_res['NAME'];
}

$arSiteDropdown = array("reference" => array_values($arIblocks), "reference_id" => array_keys($arIblocks));

$tabControl->BeginNextTab();
?>

<tr><td colspan="2" id="reindex_result_div"></td><td></td></tr>
<tr>
    <td class="field-name"><? echo GetMessage('ATL_CHANGE_SEF_IBLOCK'); ?></td>
    <td>
        <?echo SelectBoxFromArray("atl_iblock_id", $arSiteDropdown);?>
    </td>
</tr>
<tr>
    <td class="field-name"><? echo GetMessage('ATL_CHANGE_SEF_URL_FROM_TPL'); ?></td>
    <td>
        <input name="atl_page_tpl_from" id="atl_page_tpl_from" value="<? echo htmlspecialcharsEx($_POST['atl_page_tpl_from']); ?>"> <span id="hint_tpl1"></span>
    </td>
</tr>
<tr>
    <td class="field-name"><? echo GetMessage('ATL_CHANGE_SEF_URL_TO_TPL'); ?></td>
    <td>
        <input name="atl_page_tpl_to" id="atl_page_tpl_to" value="<? echo htmlspecialcharsEx($_POST['atl_page_tpl_to']); ?>"> <span id="hint_tpl2"></span>
    </td>
</tr>
<tr>
    <td class="field-name"><? echo GetMessage('ATL_CHANGE_SEF_N_PAGE_SIZE'); ?></td>
    <td>
        <input name="atl_nPageSize" id="atl_nPageSize" value="<? echo htmlspecialcharsEx($_POST['atl_nPageSize']) ?: "20"; ?>">
    </td>
</tr>

<? $tabControl->Buttons();?>

    <input type="button" id="start_button" value="<? echo GetMessage('ATL_CHANGE_SEF_START'); ?>" onclick="StartReindex();">

<?=bitrix_sessid_post();?>
<?$tabControl->End();?>
</form>

<? echo BeginNote(); ?>
<? echo GetMessage("ATL_CHANGE_SEF_HELP") ?>
<? echo EndNote(); ?>

<script>

var hint_tpl = '<?=GetMessage("ATL_HINT_TPL")?><br/>#SITE_DIR#/catalog/#SECTION_CODE_PATH#/#ELEMENT_ID#/<br/>#SITE_DIR#/catalog/#SECTION_CODE_PATH#/#ELEMENT_CODE#/<br/>#SITE_DIR#/catalog/#SECTION_ID#/#ELEMENT_ID#/<br/>#SITE_DIR#/catalog/#SECTION_CODE#/#ELEMENT_CODE#/';

BX.hint_replace(BX('hint_tpl1'), hint_tpl);
BX.hint_replace(BX('hint_tpl2'), hint_tpl);

var savedNS;
var stop;
var interval = 0;
function StartReindex()
{
	stop=false;
	document.getElementById('reindex_result_div').innerHTML='';
	document.getElementById('start_button').disabled=true;
	DoNext('<?=$iNumPage?>');
}
function DoNext(iNumPage, NS)
{
	var queryString = 'Reindex=Y';
	if (!NS)
	{
        interval = 0;
		queryString += '&sessid=<?=bitrix_sessid();?>';
	}

	savedNS = NS;

	if (!stop)
	{
		ShowWaitWindow();
		BX.ajax.post(
			'step2use_redirects_change_sef.php?'+queryString,
			{
			    iblockid: document.getElementById('atl_iblock_id').value,
			    tpl_from: document.getElementById('atl_page_tpl_from').value,
			    tpl_to: document.getElementById('atl_page_tpl_to').value,
				nPageSize: document.getElementById('atl_nPageSize').value,
				iNumPage: iNumPage
			},
			function(result) {
				console.log(result);
				if (!isNaN(parseInt(result))) {
					if (parseInt(result) == 0) {
						window.location = 'step2use_redirects_change_sef.php?lang=<?=LANGUAGE_ID?>&completed=Y';
					} else {
						var countElement = parseInt(result) * parseInt(document.getElementById('atl_nPageSize').value);
						document.getElementById('reindex_result_div').innerHTML = '<p style="padding: 15px; background-color:#FFFFCC; text-align: left; border: 1px solid #FFCC66;">' + '<?=GetMessage("ATL_COUNT_ELEMENT_STEP")?> ' +  '<b>' + countElement + '</b>' + '</p>';
						DoNext(parseInt(result)+1);
					}	
				} else {
					document.getElementById('reindex_result_div').innerHTML = result;
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
	
}
function EndReindex()
{
	stop=true;
	document.getElementById('start_button').disabled=false;
}
</script>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php"); ?>
