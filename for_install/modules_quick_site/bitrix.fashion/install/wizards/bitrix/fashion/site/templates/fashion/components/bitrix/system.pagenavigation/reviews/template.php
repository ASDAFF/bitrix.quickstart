<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!$arResult["NavShowAlways"])
{
    if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false))
        return;
}
?>
<div class="pagination">
    <ul>
<?

$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");

$bFirst = true;

if ($arResult["NavPageNomer"] > 1):
    if ($arResult["nStartPage"] > 1):
        $bFirst = false;
        if($arResult["bSavePage"]):
?>
        <li>[ <a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=1#reviews">1</a> ]</li>
<?
        else:
?>
        <li>[ <a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>#reviews">1</a> ]</li>
<?
        endif;
        if ($arResult["nStartPage"] > 2):
?>
        <li>[ <a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=round($arResult["nStartPage"] / 2)?>#reviews">&hellip;</a> ]</li>
<?
        endif;
    endif;
endif;

do
{
    if ($arResult["nStartPage"] == $arResult["NavPageNomer"]):
?>
    <li class="current">[ <span><?=$arResult["nStartPage"]?></span> ]</li>
<?
    elseif($arResult["nStartPage"] == 1 && $arResult["bSavePage"] == false):
?>
    <li>[ <a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>#reviews"><?=$arResult["nStartPage"]?></a> ]</li>
<?
    else:
?>
    <li>[ <a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["nStartPage"]?>#reviews"><?=$arResult["nStartPage"]?></a> ]</li>
<?
    endif;
    $arResult["nStartPage"]++;
    $bFirst = false;
} while($arResult["nStartPage"] <= $arResult["nEndPage"]);

if($arResult["NavPageNomer"] < $arResult["NavPageCount"]):
    if ($arResult["nEndPage"] < $arResult["NavPageCount"]):
        if ($arResult["nEndPage"] < ($arResult["NavPageCount"] - 1)):
?>
    <li>[ <a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=round($arResult["nEndPage"] + ($arResult["NavPageCount"] - $arResult["nEndPage"]) / 2)?>#reviews">&hellip;</a> ]</li>
<?
        endif;
?>
    <li>[ <a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["NavPageCount"]?>#reviews"><?=$arResult["NavPageCount"]?></a> ]</li>
<?
    endif;
endif;
?>
    </ul>
</div>