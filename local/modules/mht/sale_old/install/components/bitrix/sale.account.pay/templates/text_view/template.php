<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if(strlen($arResult["errorMessage"]) > 0)
	ShowError($arResult["errorMessage"]);

?><h3><?=GetMessage("SAP_BUY_MONEY")?></h3><?
$adit="";
if(strlen($arResult["CURRENT_PAGE"]) > 0)
	$adit = "&CURRENT_PAGE=".$arResult["CURRENT_PAGE"];
foreach($arResult["AMOUNT_TO_SHOW"] as $v)
{

	?><a href="<?=$v["LINK"].$adit?>" title="<?=GetMessage("SAP_LINK_TITLE")." ".$v["NAME"]?>"><?=$v["NAME"]?></a><br /><?
}
?>