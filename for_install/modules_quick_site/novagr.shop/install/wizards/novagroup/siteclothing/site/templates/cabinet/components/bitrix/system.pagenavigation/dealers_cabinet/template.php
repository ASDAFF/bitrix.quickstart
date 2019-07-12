<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!$arResult["NavShowAlways"])
{
	if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false))
		return;
}
$addParams = "";
if (isset($_REQUEST["secid"])) $addParams .= "&secid=" .$_REQUEST["secid"];
//echo "<pre>"; print_r($arResult);echo "</pre>";

//$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
//$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");
//echo ' $strNavQueryString ' . $strNavQueryString;
?>
<? //=$arResult["NavTitle"]?>

<?if($arResult["bDescPageNumbering"] === true):?>

	<?=$arResult["NavFirstRecordShow"]?> <?=GetMessage("nav_to")?> <?=$arResult["NavLastRecordShow"]?> <?=GetMessage("nav_of")?> <?=$arResult["NavRecordCount"]?><br />

	<?if ($arResult["NavPageNomer"] < $arResult["NavPageCount"]):?>
		<?if($arResult["bSavePage"]):?>
			<a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["NavPageCount"]?>"><?=GetMessage("nav_begin")?></a>
			|
			<a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>"><?=GetMessage("nav_prev")?></a>
			|
		<?else:?>
			<a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><?=GetMessage("nav_begin")?></a>
			|
			<?if ($arResult["NavPageCount"] == ($arResult["NavPageNomer"]+1) ):?>
				<a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><?=GetMessage("nav_prev")?></a>
				|
			<?else:?>
				<a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>"><?=GetMessage("nav_prev")?></a>
				|
			<?endif?>
		<?endif?>
	<?else:?>
		<?=GetMessage("nav_begin")?>&nbsp;|&nbsp;<?=GetMessage("nav_prev")?>&nbsp;|
	<?endif?>

	<?while($arResult["nStartPage"] >= $arResult["nEndPage"]):?>
		<?$NavRecordGroupPrint = $arResult["NavPageCount"] - $arResult["nStartPage"] + 1;?>

		<?if ($arResult["nStartPage"] == $arResult["NavPageNomer"]):?>
			<b><?=$NavRecordGroupPrint?></b>
		<?elseif($arResult["nStartPage"] == $arResult["NavPageCount"] && $arResult["bSavePage"] == false):?>
			<a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><?=$NavRecordGroupPrint?></a>
		<?else:?>
			<a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["nStartPage"]?>"><?=$NavRecordGroupPrint?></a>
		<?endif?>

		<?$arResult["nStartPage"]--?>
	<?endwhile?>

	|

	<?if ($arResult["NavPageNomer"] > 1):?>
		<a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>"><?=GetMessage("nav_next")?></a>
		|
		<a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=1"><?=GetMessage("nav_end")?></a>
	<?else:?>
		<?=GetMessage("nav_next")?>&nbsp;|&nbsp;<?=GetMessage("nav_end")?>
	<?endif?>

<?else:
/*
?>
	
	<div class="product-count-bottom"><span class="total-items"><?=$arResult["NavFirstRecordShow"]?> - <?=$arResult["NavLastRecordShow"]?> из <?=$arResult["NavRecordCount"]?></span></div>
	<?
	*/?>
	
	<div class="pagination">
		<ul>
	<?if ($arResult["NavPageNomer"] > 1):?>

			<li ><a inumpage="1" href="?iNumPage=1&nPageSize=<?=$arResult["NavPageSize"].$addParams;?>"><?=GetMessage("nav_begin")?></a></li>
			<li ><a inumpage="<?=($arResult["NavPageNomer"]-1)?>" href="?iNumPage=<?=($arResult["NavPageNomer"]-1)?>&nPageSize=<?=$arResult["NavPageSize"].$addParams;?>">&larr;</a></li>
			
	<?else:?>
		<li class="disabled" ><a href="#"><?=GetMessage("nav_begin")?></a></li>
 		<li class="disabled" ><a href="#">&larr;</a></li>
	<?endif?>

	<?while($arResult["nStartPage"] <= $arResult["nEndPage"]):?>

		<?if ($arResult["nStartPage"] == $arResult["NavPageNomer"]):?>
			
			<li class="active"><a href="#"><?=$arResult["nStartPage"]?></a></li>

		<?else:?>
			
			<li>
			<a numpage="<?=$arResult["nStartPage"]?>" href="?iNumPage=<?=$arResult["nStartPage"]?>&nPageSize=<?=$arResult["NavPageSize"].$addParams;?>"><?=$arResult["nStartPage"]?></a>
						
		<?endif?>
		<?$arResult["nStartPage"]++?>
	<?endwhile?>

	<?if($arResult["NavPageNomer"] < $arResult["NavPageCount"]):?>
		
 		<li ><a inumpage="<?=($arResult["NavPageNomer"]+1)?>" href="?iNumPage=<?=($arResult["NavPageNomer"]+1)?>&nPageSize=<?=$arResult["NavPageSize"].$addParams;?>">&rarr;</a></li>
 		<li ><a inumpage="<?=$arResult["NavPageCount"]?>" href="?iNumPage=<?=$arResult["NavPageCount"]?>&nPageSize=<?=$arResult['NavPageSize'].$addParams;?>"><?=GetMessage("nav_end")?></a></li>
 		
	<?else:?>
		<li class="disabled" ><a href="#">&rarr;</a></li>
 		<li class="disabled" ><a href="#"><?=GetMessage("nav_end")?></a></li>
	<?endif?>
	<ul>
	</div>
<?endif?>


