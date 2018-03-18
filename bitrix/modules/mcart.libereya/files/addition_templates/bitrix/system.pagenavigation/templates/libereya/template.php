<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!$arResult["NavShowAlways"])
{
	if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false))
		return;
}

//echo "<pre>"; print_r($arResult);echo "</pre>";

$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");

?>


<?if($arResult["bDescPageNumbering"] === true):?>
					
	<b class="curr-page"><?=$arResult["NavPageNomer"]?></b>&nbsp;/&nbsp;<b class="max-page"><?=$arResult["NavPageCount"]?></b> 

	<?if ($arResult["NavPageNomer"] < $arResult["NavPageCount"]):?>
		<?if($arResult["bSavePage"]):?>
			
			<a  class="prev" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>"></a>
			
		<?else:?>
			
			
			<?if ($arResult["NavPageCount"] == ($arResult["NavPageNomer"]+1) ):?>
				<a class="prev" href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"></a>
				
			<?else:?>
				<a  class="prev" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>"></a>
				
			<?endif?>
		<?endif?>
	<?else:?>
			<a href="#" class="prev"></a>
	<?endif?>

	<?if ($arResult["NavPageNomer"] > 1):?>
		<a  class="next" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>"></a>
		
	<?else:?>
		<a class="next" href="#"></a>&nbsp;
	<?endif?>

<?else:?>

	<b class="curr-page"><?=$arResult["NavPageNomer"]?></b>&nbsp;/&nbsp;<b class="max-page"><?=$arResult["NavPageCount"]?></b>



	<?if ($arResult["NavPageNomer"] > 1):?>

		<?if($arResult["bSavePage"]):?>
			
			<a class="prev" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>"></a>
			|
		<?else:?>
			
			<?if ($arResult["NavPageNomer"] > 2):?>
				<a class="prev" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>"></a>
			<?else:?>
				<a class="prev" href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"></a>
			<?endif?>
			
		<?endif?>

	<?else:?>
		<a href="#" class="prev"></a>
	<?endif?>


	

	<?if($arResult["NavPageNomer"] < $arResult["NavPageCount"]):?>
		<a class="next" href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>"></a>&nbsp;
		
	<?else:?>
		<a href="#" class="next"></a>
	<?endif?>

<?endif?>
