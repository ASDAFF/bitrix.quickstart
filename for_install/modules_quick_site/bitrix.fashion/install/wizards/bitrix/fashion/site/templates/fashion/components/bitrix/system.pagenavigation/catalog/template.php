<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!$arResult["NavShowAlways"])
{
    if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false))
        return;
}
?>
<ul class="pagination">
<?

$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");
?>
    <li><?=GetMessage("pages")?></li>
<?
if($arResult["bDescPageNumbering"] === true):
    $bFirst = true;
    if ($arResult["NavPageNomer"] < $arResult["NavPageCount"]):
        if($arResult["bSavePage"]):
?>
            <li class="prev"><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>"><?=GetMessage("nav_prev")?></a></li>
<?
        else:
            if ($arResult["NavPageCount"] == ($arResult["NavPageNomer"]+1) ):
?>
            <li class="prev"><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><?=GetMessage("nav_prev")?></a></li>
<?
            else:
?>
            <li class="prev"><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>"><?=GetMessage("nav_prev")?></a></li>
<?
            endif;
        endif;
        
        if ($arResult["nStartPage"] < $arResult["NavPageCount"]):
            $bFirst = false;
            if($arResult["bSavePage"]):
?>
            <li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["NavPageCount"]?>">1</a></li>
<?
            else:
?>
            <li><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>">1</a></li>
<?
            endif;
            if ($arResult["nStartPage"] < ($arResult["NavPageCount"] - 1)):
?>    
            <li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=intVal($arResult["nStartPage"] + ($arResult["NavPageCount"] - $arResult["nStartPage"]) / 2)?>">&hellip;</a></li>
<?
            endif;
        endif;
    endif;
    do
    {
        $NavRecordGroupPrint = $arResult["NavPageCount"] - $arResult["nStartPage"] + 1;
        
        if ($arResult["nStartPage"] == $arResult["NavPageNomer"]):
?>
        <li><span><?=$NavRecordGroupPrint?></span></li>
<?
        elseif($arResult["nStartPage"] == $arResult["NavPageCount"] && $arResult["bSavePage"] == false):
?>
        <li><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><?=$NavRecordGroupPrint?></a></li>
<?
        else:
?>
        <li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["nStartPage"]?>"><?=$NavRecordGroupPrint?></a></li>
<?
        endif;
        
        $arResult["nStartPage"]--;
        $bFirst = false;
    } while($arResult["nStartPage"] >= $arResult["nEndPage"]);
    
    if ($arResult["NavPageNomer"] > 1):
        if ($arResult["nEndPage"] > 1):
            if ($arResult["nEndPage"] > 2):
?>
        <li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=round($arResult["nEndPage"] / 2)?>">&hellip;</a></li>
<?
            endif;
?>
        <li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=1"><?=$arResult["NavPageCount"]?></a></li>
<?
        endif;
    
?>
        <li class="next"><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>"><?=GetMessage("nav_next")?></a></li>
<?
    endif; 

else:
    $bFirst = true;

    if ($arResult["NavPageNomer"] > 1):
        if($arResult["bSavePage"]):
?>
            <li class="prev"><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>"><?=GetMessage("nav_prev")?></a></li>
<?
        else:
            if ($arResult["NavPageNomer"] > 2):
?>
            <li class="prev"><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>"><?=GetMessage("nav_prev")?></a></li>
<?
            else:
?>
            <li class="prev"><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><?=GetMessage("nav_prev")?></a></li>
<?
            endif;
        
        endif;
        
        if ($arResult["nStartPage"] > 1):
            $bFirst = false;
            if($arResult["bSavePage"]):
?>
            <li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=1">1</a></li>
<?
            else:
?>
            <li><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>">1</a></li>
<?
            endif;
            if ($arResult["nStartPage"] > 2):
/*?>
            <li><span class="modern-page-dots">&hellip;</span></li>
<?*/
?>
            <li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=round($arResult["nStartPage"] / 2)?>">&hellip;</a></li>
<?
            endif;
        endif;
    endif;

    do
    {
        if ($arResult["nStartPage"] == $arResult["NavPageNomer"]):
?>
        <li><span><?=$arResult["nStartPage"]?></span></li>
<?
        elseif($arResult["nStartPage"] == 1 && $arResult["bSavePage"] == false):
?>
        <li><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><?=$arResult["nStartPage"]?></a></li>
<?
        else:
?>
        <li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["nStartPage"]?>"><?=$arResult["nStartPage"]?></a></li>
<?
        endif;
        $arResult["nStartPage"]++;
        $bFirst = false;
    } while($arResult["nStartPage"] <= $arResult["nEndPage"]);
    
    if($arResult["NavPageNomer"] < $arResult["NavPageCount"]):
        if ($arResult["nEndPage"] < $arResult["NavPageCount"]):
            if ($arResult["nEndPage"] < ($arResult["NavPageCount"] - 1)):
/*?>
        <li><span class="modern-page-dots">&hellip;</span></li>
<?*/
?>
        <li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=round($arResult["nEndPage"] + ($arResult["NavPageCount"] - $arResult["nEndPage"]) / 2)?>">&hellip;</a></li>
<?
            endif;
?>
        <li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["NavPageCount"]?>"><?=$arResult["NavPageCount"]?></a></li>
<?
        endif;
?>
        <li class="next"><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>"><?=GetMessage("nav_next")?></a></li>
<?
    endif;
endif;

if ($arResult["bShowAll"]):
    if ($arResult["NavShowAll"]):
?>
        <li><a class="modern-page-pagen" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>SHOWALL_<?=$arResult["NavNum"]?>=0"><?=GetMessage("nav_paged")?></a></li>
<?
    else:
?>
        <li><a class="modern-page-all" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>SHOWALL_<?=$arResult["NavNum"]?>=1"><?=GetMessage("nav_all")?></a></li>
<?
    endif;
endif
?>
</ul>