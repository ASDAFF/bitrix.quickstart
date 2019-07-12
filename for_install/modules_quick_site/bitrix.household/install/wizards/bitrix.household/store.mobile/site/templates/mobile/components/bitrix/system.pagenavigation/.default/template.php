<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$prev = "";
$next = "";
$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"] : "");
if($arResult["bDescPageNumbering"] === true):
	if ($arResult["NavPageNomer"] < $arResult["NavPageCount"]):
		if($arResult["bSavePage"]):
			$prev = $strNavQueryString."PAGEN_".$arResult["NavNum"]."=".($arResult["NavPageNomer"]+1);
		else:
			$prev = $strNavQueryString."PAGEN_".$arResult["NavNum"]."=".($arResult["NavPageNomer"]+1);
		endif;
	endif;
	if ($arResult["NavPageNomer"] > 1):
		$next = $strNavQueryString."PAGEN_".$arResult["NavNum"]."=".($arResult["NavPageNomer"]-1);
	endif; 

else:
	if ($arResult["NavPageNomer"] > 1):
		if($arResult["bSavePage"]):
			$prev = $strNavQueryString."PAGEN_".$arResult["NavNum"]."=".($arResult["NavPageNomer"]-1);
		else:
			$prev = $strNavQueryString."PAGEN_".$arResult["NavNum"]."=".($arResult["NavPageNomer"]-1);
		endif;
	endif;
	
	if($arResult["NavPageNomer"] < $arResult["NavPageCount"]):
		$next = $strNavQueryString."PAGEN_".$arResult["NavNum"]."=".($arResult["NavPageNomer"]+1);
	endif;
endif;
?>
<p>
<?if(strlen($prev) > 0):?>
<a href="<?=$arResult["sUrlPath"]."?".$prev?>" data-role="button" data-icon="arrow-l" data-inline="true" data-transition="slidedown"><?=GetMessage("SP_PREV")?></a>
<?endif;?>
<?if(strlen($next) > 0):?>
<a href="<?=$arResult["sUrlPath"]."?".$next?>" data-role="button" data-icon="arrow-r" data-inline="true" data-transition="slideup" data-iconpos="right"><?=GetMessage("SP_PREV")?></a>
<?endif;?>
</p>