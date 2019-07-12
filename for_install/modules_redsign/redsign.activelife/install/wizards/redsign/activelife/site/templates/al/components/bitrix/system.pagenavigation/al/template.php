<?

use Bitrix\Main\Web\Uri;


if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

/** @var array $arParams */
/** @var array $arResult */
/** @var CBitrixComponentTemplate $this */

$this->setFrameMode(true);

if(!$arResult["NavShowAlways"])
{
	if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false))
		return;
}

if ($arResult['NavQueryString'] != '') {
    $arResult['NavQueryString'] = preg_replace('/(\&amp;|\?)??(rs_ajax|catalog_refresh)\=[^\&]*/', '', $arResult['NavQueryString']);
}

$uri = new Uri($arResult['NavQueryString']);

$uri->deleteParams(array('rs_ajax', 'catalog_refresh'));

$arResult['NavQueryString'] = $uri->getUri();

$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");
?>

<nav class="pagination" itemscope itemtype="http://www.schema.org/SiteNavigationElement">
	<ul>
<?if($arResult["bDescPageNumbering"] === true):?>

	<?if ($arResult["NavPageNomer"] < $arResult["NavPageCount"]):?>
		<?if($arResult["bSavePage"]):?>
			<li class="pagination__prev"><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>"><?/*<span><?echo GetMessage("round_nav_back")?></span>*/?><svg class="pagination__icon icon-svg"><use xlink:href="#svg-left-quote"></use></svg></a></li>
			<li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>"><span>1</span></a></li>
		<?else:?>
			<?if (($arResult["NavPageNomer"]+1) == $arResult["NavPageCount"]):?>
				<li class="pagination__prev"><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><?/*<span><?echo GetMessage("round_nav_back")?></span>*/?><svg class="pagination__icon icon-svg"><use xlink:href="#svg-left-quote"></use></svg></a></li>
			<?else:?>
				<li class="pagination__prev"><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>"><?/*<span><?echo GetMessage("round_nav_back")?></span>*/?><svg class="pagination__icon icon-svg"><use xlink:href="#svg-left-quote"></use></svg></a></li>
			<?endif?>
			<li><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><span>1</span></a></li>
		<?endif?>
	<?else:?>
			<li class="active"><span>1</span></li>
	<?endif?>

	<?
	$arResult["nStartPage"]--;
	while($arResult["nStartPage"] >= $arResult["nEndPage"]+1):
	?>
		<?$NavRecordGroupPrint = $arResult["NavPageCount"] - $arResult["nStartPage"] + 1;?>

		<?if ($arResult["nStartPage"] == $arResult["NavPageNomer"]):?>
			<li class="active"><span><?=$NavRecordGroupPrint?></span></li>
		<?else:?>
			<li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["nStartPage"]?>"><span><?=$NavRecordGroupPrint?></span></a></li>
		<?endif?>

		<?$arResult["nStartPage"]--?>
	<?endwhile?>

	<?if ($arResult["NavPageNomer"] > 1):?>
		<?if($arResult["NavPageCount"] > 1):?>
			<li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=1"><span><?=$arResult["NavPageCount"]?></span></a></li>
		<?endif?>
			<li class="pagination__next"><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>"><?/*<span><?echo GetMessage("round_nav_forward")?></span>*/?><svg class="pagination__icon icon-svg"><use xlink:href="#svg-right-quote"></use></svg></a></li>
	<?else:?>
		<?if($arResult["NavPageCount"] > 1):?>
			<li class="active"><span><?=$arResult["NavPageCount"]?></span></li>
		<?endif?>
	<?endif?>

<?else:?>

	<?if ($arResult["NavPageNomer"] > 1):?>
		<?if($arResult["bSavePage"]):?>
			<li class="pagination__prev"><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>"><?/*<span><?echo GetMessage("round_nav_back")?></span>*/?><svg class="pagination__icon icon-svg"><use xlink:href="#svg-left-quote"></use></svg></a></li>
			<li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=1"><span>1</span></a></li>
		<?else:?>
			<?if ($arResult["NavPageNomer"] > 2):?>
				<li class="pagination__prev"><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]-1)?>"><?/*<span><?echo GetMessage("round_nav_back")?></span>*/?><svg class="pagination__icon icon-svg"><use xlink:href="#svg-left-quote"></use></svg></a></li>
			<?else:?>
				<li class="pagination__prev"><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><?/*<span><?echo GetMessage("round_nav_back")?></span>*/?><svg class="pagination__icon icon-svg"><use xlink:href="#svg-left-quote"></use></svg></a></li>
			<?endif?>
			<li><a href="<?=$arResult["sUrlPath"]?><?=$strNavQueryStringFull?>"><span>1</span></a></li>
		<?endif?>
	<?else:?>
			<li class="active"><span>1</span></li>
	<?endif?>

	<?
	$arResult["nStartPage"]++;
	while($arResult["nStartPage"] <= $arResult["nEndPage"]-1):
	?>
		<?if ($arResult["nStartPage"] == $arResult["NavPageNomer"]):?>
			<li class="active"><span><?=$arResult["nStartPage"]?></span></li>
		<?else:?>
			<li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["nStartPage"]?>"><span><?=$arResult["nStartPage"]?></span></a></li>
		<?endif?>
		<?$arResult["nStartPage"]++?>
	<?endwhile?>


	<?if($arResult["NavPageNomer"] < $arResult["NavPageCount"]):?>
		<?if($arResult["NavPageCount"] > 1):?>
			<li><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=$arResult["NavPageCount"]?>"><span><?=$arResult["NavPageCount"]?></span></a></li>
		<?endif?>
			<li class="pagination__next"><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>PAGEN_<?=$arResult["NavNum"]?>=<?=($arResult["NavPageNomer"]+1)?>"><?/*<span><?echo GetMessage("round_nav_forward")?></span>*/?><svg class="pagination__icon icon-svg"><use xlink:href="#svg-right-quote"></use></svg></a></li>
	<?else:?>
		<?if($arResult["NavPageCount"] > 1):?>
			<li class="active"><span><?=$arResult["NavPageCount"]?></span></li>
		<?endif?>
	<?endif?>
<?endif?>

<?if ($arResult["bShowAll"]):?>
	<?if ($arResult["NavShowAll"]):?>
			<li class="pagination__all"><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>SHOWALL_<?=$arResult["NavNum"]?>=0" rel="nofollow"><span><?echo GetMessage("round_nav_pages")?></span></a></li>
	<?else:?>
			<li class="pagination__all"><a href="<?=$arResult["sUrlPath"]?>?<?=$strNavQueryString?>SHOWALL_<?=$arResult["NavNum"]?>=1" rel="nofollow"><span><?echo GetMessage("round_nav_all")?></span></a></li>
	<?endif?>
<?endif?>
		</ul>
	<div style="clear:both"></div>
</nav>
