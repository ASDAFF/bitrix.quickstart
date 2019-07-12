<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$sAnchor = '#objectList';
echo $strNavQueryString;
if(!$arResult["NavShowAlways"])
{
	if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false))
		return;
}
?>
<?
$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");

$iInterval = 7;

?>
<div class="navigation">
    <? if ($arResult["NavPageNomer"] > 1) { ?>
        <a href="<?=$arResult["sUrlPath"].'?PAGEN_'.$arResult["NavNum"].'=1&'.$strNavQueryString?><?=$sAnchor?>">1</a>
        <? if ($arResult["NavPageNomer"] > $iInterval + 2) { ?>
        ...
        <? } ?>
    <? } ?>
    <?
        if ($arResult["NavPageNomer"] > 2) { 
            $iStartPos = $arResult["NavPageNomer"]-$iInterval;
            $iStartPos = $iStartPos > 1?$iStartPos:2;
            for ($i=$iStartPos;$i<$arResult["NavPageNomer"];$i++) {
    ?>
        <a href="<?=$arResult["sUrlPath"].'?PAGEN_'.$arResult["NavNum"].'='.($i).'&'.$strNavQueryString?><?=$sAnchor?>"><?=intval($i)?></a>
    <? } } ?>
    <a href="<?=$arResult["sUrlPath"].'?PAGEN_'.$arResult["NavNum"].'='.($arResult["NavPageNomer"]).'&'.$strNavQueryString?><?=$sAnchor?>" class="sel">&nbsp;<?=intval($arResult["NavPageNomer"])?>&nbsp;</a>
    <?
        if ($arResult["NavPageNomer"] < $arResult["NavPageCount"]) {
            $iEndPos = $arResult["NavPageNomer"]+$iInterval;
            $iEndPos = $iEndPos < $arResult["NavPageCount"]?$iEndPos:$arResult["NavPageCount"]-1;
            for ($i=$arResult["NavPageNomer"]+1;$i<=$iEndPos;$i++) {
    ?>
        <a href="<?=$arResult["sUrlPath"].'?PAGEN_'.$arResult["NavNum"].'='.($i).'&'.$strNavQueryString?><?=$sAnchor?>"><?=intval($i)?></a>
    <? } } ?>

    <? if ($arResult["NavPageNomer"] < $arResult["NavPageCount"]) { ?>
        <? if ($arResult["NavPageNomer"] < $arResult["NavPageCount"] - $iInterval - 1) {?>
        ...
        <? } ?>
        <a href="<?=$arResult["sUrlPath"].'?PAGEN_'.$arResult["NavNum"].'='.$arResult["NavPageCount"].'&'.$strNavQueryString?><?=$sAnchor?>"><?=intval($arResult["NavPageCount"])?></a> 
    <? } ?>
</div>