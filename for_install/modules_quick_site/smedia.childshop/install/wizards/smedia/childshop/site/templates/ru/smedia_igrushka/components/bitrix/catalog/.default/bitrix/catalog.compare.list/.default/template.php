<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
$compareUrl = str_replace("#IBLOCK_ID#", $arParams['IBLOCK_ID'], $arParams["COMPARE_URL"]);
$compareDeleteUrl = $compareUrl."?action=DELETE_FROM_COMPARE_RESULT&IBLOCK_ID=".$arParams['IBLOCK_ID'];
foreach ($arResult as $id => $arProduct)
	$compareDeleteUrl .= "&ID[]=".$arProduct["ID"];

$backurl = "";
if (isset($_REQUEST["backurl"]) && strlen($_REQUEST["backurl"]) > 0)
	$backurl = $_REQUEST["backurl"];
else
	$backurl = $APPLICATION->GetCurPageParam("",Array("action", "backurl", "ajax_compare", "id"));

$compareDeleteUrl .= "&backurl=".urlencode($backurl);
?>

 
<a href="<?=$compareUrl?>" title="<?=htmlspecialchars(GetMessage("CATALOG_LINK_TITLE"));?>" <?if(count($arResult) < 1 ):?> style="display:none;"<?endif?>>
	<img src="<?=SITE_DIR?>images/button_sr.gif" width="128px" height="24px" alt="<?=htmlspecialchars(GetMessage("CATALOG_LINK_TITLE"));?>" />
</a>

