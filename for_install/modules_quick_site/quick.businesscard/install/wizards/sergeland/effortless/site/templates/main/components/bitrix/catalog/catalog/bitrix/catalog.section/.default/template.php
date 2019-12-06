<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

switch($arParams["LINE_ELEMENT_COUNT"])
{
	case 1: $span = 12; break;
	case 2: $span = 6; break;	
	case 3: $span = 4; break;
	case 4: $span = 3; break; 
	case 5: case 6: case 7: $span = 2; break;			
    default: $span = 4;
}
?>
<?if(!empty($arResult["UF_SEO_DESCRIPTION"])):?>
<div class="block text-muted"><?=$arResult["UF_SEO_DESCRIPTION"]?></div>
<?endif?>
<?if(!empty($arResult["ITEMS"])):?>
<div class="row shop-header mb-10">
	<div class="col-md-2 hidden-xs hidden-sm pull-right">
		<form>
			<div class="form-group">
				<select class="form-control">
					<option value=""><?=GetMessage("QUICK_EFFORTLESS_CATALOG_VIEW")?></option>
					<option <?if( (array_key_exists("view", $_REQUEST) && $_REQUEST["view"] == "price") || ($_SESSION["QUICK_EFFORTLESS_CATALOG_TEMPLATE"] == "price") ):?>selected<?endif?> value="<?=$APPLICATION->GetCurPageParam("view=price", array("view", "PHPSESSID", "clear_cache", "bitrix_include_areas"))?>"><?=GetMessage("QUICK_EFFORTLESS_CATALOG_PRICE")?></option>
					<option <?if( (array_key_exists("view", $_REQUEST) && $_REQUEST["view"] == "list") || ($_SESSION["QUICK_EFFORTLESS_CATALOG_TEMPLATE"] == "list") ):?>selected<?endif?> value="<?=$APPLICATION->GetCurPageParam("view=list", array("view", "PHPSESSID", "clear_cache", "bitrix_include_areas"))?>"><?=GetMessage("QUICK_EFFORTLESS_CATALOG_LIST")?></option>
					<option <?if( (array_key_exists("view", $_REQUEST) && $_REQUEST["view"] == "tile") || ($_SESSION["QUICK_EFFORTLESS_CATALOG_TEMPLATE"] == "tile") ):?>selected<?endif?> value="<?=$APPLICATION->GetCurPageParam("view=tile", array("view", "PHPSESSID", "clear_cache", "bitrix_include_areas"))?>"><?=GetMessage("QUICK_EFFORTLESS_CATALOG_TILE")?></option>
				</select>
			</div>
		</form>
	</div>
	<div class="col-md-3 col-sm-12 col-xs-12 pull-right">
		<form>
			<div class="form-group">
				<select class="form-control">
					<option value=""><?=GetMessage("QUICK_EFFORTLESS_CATALOG_SORT")?></option>
					<option <?if( (array_key_exists("price", $_REQUEST) && array_key_exists("desc", $_REQUEST)) || ($_SESSION["QUICK_EFFORTLESS_CATALOG_SORT_FIELD"] == "property_PRICE" && $_SESSION["QUICK_EFFORTLESS_CATALOG_SORT_ORDER"] == "desc") ):?>selected<?endif?> value="<?=$APPLICATION->GetCurPageParam("price&desc", array("price", "popular", "name", "desc", "asc", "PHPSESSID", "clear_cache", "bitrix_include_areas"))?>"><?=GetMessage("QUICK_EFFORTLESS_CATALOG_PRICE_DESC")?></option>
					<option <?if( (array_key_exists("price", $_REQUEST) && array_key_exists("asc", $_REQUEST)) || ($_SESSION["QUICK_EFFORTLESS_CATALOG_SORT_FIELD"] == "property_PRICE" && $_SESSION["QUICK_EFFORTLESS_CATALOG_SORT_ORDER"] == "asc") ):?>selected<?endif?> value="<?=$APPLICATION->GetCurPageParam("price&asc", array("price", "popular", "name", "desc", "asc", "PHPSESSID", "clear_cache", "bitrix_include_areas"))?>"><?=GetMessage("QUICK_EFFORTLESS_CATALOG_PRICE_ASC")?></option>
					<option <?if( (array_key_exists("popular", $_REQUEST) && array_key_exists("asc", $_REQUEST)) || ($_SESSION["QUICK_EFFORTLESS_CATALOG_SORT_FIELD"] == "property_POPULAR" && $_SESSION["QUICK_EFFORTLESS_CATALOG_SORT_ORDER"] == "asc") ):?>selected<?endif?> value="<?=$APPLICATION->GetCurPageParam("popular&asc", array("price", "popular", "name", "desc", "asc", "PHPSESSID", "clear_cache", "bitrix_include_areas"))?>"><?=GetMessage("QUICK_EFFORTLESS_CATALOG_POPULAR_ASC")?></option>
					<option <?if( (array_key_exists("popular", $_REQUEST) && array_key_exists("desc", $_REQUEST)) || ($_SESSION["QUICK_EFFORTLESS_CATALOG_SORT_FIELD"] == "property_POPULAR" && $_SESSION["QUICK_EFFORTLESS_CATALOG_SORT_ORDER"] == "desc") ):?>selected<?endif?> value="<?=$APPLICATION->GetCurPageParam("popular&desc", array("price", "popular", "name", "desc", "asc", "PHPSESSID", "clear_cache", "bitrix_include_areas"))?>"><?=GetMessage("QUICK_EFFORTLESS_CATALOG_POPULAR_DESC")?></option>
					<option <?if( (array_key_exists("name", $_REQUEST) && array_key_exists("asc", $_REQUEST)) || ($_SESSION["QUICK_EFFORTLESS_CATALOG_SORT_FIELD"] == "name" && $_SESSION["QUICK_EFFORTLESS_CATALOG_SORT_ORDER"] == "asc") ):?>selected<?endif?> value="<?=$APPLICATION->GetCurPageParam("name&asc", array("price", "popular", "name", "desc", "asc", "PHPSESSID", "clear_cache", "bitrix_include_areas"))?>"><?=GetMessage("QUICK_EFFORTLESS_CATALOG_NAME_ASC")?></option>
					<option <?if( (array_key_exists("name", $_REQUEST) && array_key_exists("desc", $_REQUEST)) || ($_SESSION["QUICK_EFFORTLESS_CATALOG_SORT_FIELD"] == "name" && $_SESSION["QUICK_EFFORTLESS_CATALOG_SORT_ORDER"] == "desc") ):?>selected<?endif?> value="<?=$APPLICATION->GetCurPageParam("name&desc", array("price", "popular", "name", "desc", "asc", "PHPSESSID", "clear_cache", "bitrix_include_areas"))?>"><?=GetMessage("QUICK_EFFORTLESS_CATALOG_NAME_DESC")?></option>
				</select>
			</div>
		</form>
	</div>
</div>
<div class="catalog-grid">
<?
$count = count($arResult["ITEMS"]);
foreach($arResult["ITEMS"] as $arItem):
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
?>
	<?if($cell%$arParams["LINE_ELEMENT_COUNT"] == 0):?>
	<div class="row">
	<?endif?>
	<div class="col-md-<?=$span?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
		<div class="listing-item">
			<div class="overlay-container pic">
				<?if(!empty($arItem["PREVIEW_PICTURE"])):?>
				<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["NAME"]?>">
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="overlay small">
					<i class="fa fa-plus"></i>
				</a>
				<?else:?>
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><i class="fa fa-image pic"></i></a>
				<?endif?>
				<?if(!empty($arItem["PROPERTIES"]["ACTION"]["VALUE"]) || !empty($arItem["PROPERTIES"]["NEW"]["VALUE"])):?>
				<div class="tags">
					<?if(!empty($arItem["PROPERTIES"]["ACTION"]["VALUE"])):?><span class="badge default-bg"><?=GetMessage("QUICK_EFFORTLESS_CATALOG_ACTION")?> <?if(!empty($arItem["PROPERTIES"]["PERCENT"]["VALUE"])):?><?=$arItem["PROPERTIES"]["PERCENT"]["VALUE"]?><?endif?></span><?endif?>
					<?if(!empty($arItem["PROPERTIES"]["NEW"]["VALUE"])):?><span class="badge danger-bg"><?=GetMessage("QUICK_EFFORTLESS_CATALOG_NEW")?></span><?endif?>
				</div>
				<?endif?>
				<div class="status">
					<?if(!empty($arItem["PROPERTIES"]["PRESENCE"]["VALUE"])):?><span class="badge success-bg"><?=GetMessage("QUICK_EFFORTLESS_CATALOG_PRESENCE")?></span>
					<?elseif(!empty($arItem["PROPERTIES"]["EXPECTED"]["VALUE"])):?><span class="badge warning-bg"><?=GetMessage("QUICK_EFFORTLESS_CATALOG_EXPECTED")?></span>
					<?elseif(!empty($arItem["PROPERTIES"]["UNDER"]["VALUE"])):?><span class="badge info-bg"><?=GetMessage("QUICK_EFFORTLESS_CATALOG_UNDER")?></span>
					<?elseif(!empty($arItem["PROPERTIES"]["UNAVAILABLE"]["VALUE"])):?><span class="badge gray-bg"><?=GetMessage("QUICK_EFFORTLESS_CATALOG_UNAVAILABLE")?></span><?endif?>
				</div>
			</div>
			<hr class="hidden-md hidden-lg pic">
			<div class="listing-item-body clearfix">
				<h3 class="title <?if(empty($arItem["PREVIEW_TEXT"])):?>mb-15<?endif?>"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></h3>
				<?if(!empty($arItem["PREVIEW_TEXT"])):?><div class="preview-text"><?=$arItem["PREVIEW_TEXT"]?></div><?endif?>
				<?if(!empty($arItem["PROPERTIES"]["PRICE"]["VALUE"])):?><span class="price"><?=$arItem["PROPERTIES"]["PRICE"]["VALUE"]?></span> <?=$arItem["PROPERTIES"]["CURRENCY"]["~VALUE"]?> <?if(!empty($arItem["PROPERTIES"]["PRICE_OLD"]["VALUE"])):?><del><?=$arItem["PROPERTIES"]["PRICE_OLD"]["VALUE"]?></del><?endif?><?endif?>
				<div class="pull-right">
					<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="btn btn-white"><i class="fa fa-shopping-cart"></i></a>
				</div>
			</div>		
		</div>
	</div>
	<?$cell++;
	if($cell%$arParams["LINE_ELEMENT_COUNT"] == 0 || $count == $cell):?>
	</div>
	<?endif?>
<?endforeach?>
</div>
<?if($arResult["NAV_RESULT"]->NavRecordCount > $arParams["PAGE_ELEMENT_COUNT"]):?>
<div class="row shop-footer">
	<div class="col-md-2 hidden-xs hidden-sm pull-right">
		<form>
			<div class="form-group">
				<select class="form-control">
					<option <?if( (array_key_exists("page_element_count", $_REQUEST) && $_REQUEST["page_element_count"] == $arParams["PAGE_ELEMENT_COUNT_"]) || ($_SESSION["QUICK_EFFORTLESS_CATALOG_PAGE_COUNT"] == $arParams["PAGE_ELEMENT_COUNT_"]) ):?>selected<?endif?> value="<?=$APPLICATION->GetCurPageParam("page_element_count=".$arParams["PAGE_ELEMENT_COUNT_"], array("page_element_count", "PHPSESSID", "clear_cache", "bitrix_include_areas"))?>"><?=$arParams["PAGE_ELEMENT_COUNT_"]?></option>
					<option <?if( (array_key_exists("page_element_count", $_REQUEST) && $_REQUEST["page_element_count"] == ($arParams["PAGE_ELEMENT_COUNT_"]+$arParams["LINE_ELEMENT_COUNT"])) || ($_SESSION["QUICK_EFFORTLESS_CATALOG_PAGE_COUNT"] == ($arParams["PAGE_ELEMENT_COUNT_"]+$arParams["LINE_ELEMENT_COUNT"])) ):?>selected<?endif?> value="<?=$APPLICATION->GetCurPageParam("page_element_count=".($arParams["PAGE_ELEMENT_COUNT_"]+$arParams["LINE_ELEMENT_COUNT"]), array("page_element_count", "PHPSESSID", "clear_cache", "bitrix_include_areas"))?>"><?=$arParams["PAGE_ELEMENT_COUNT_"]+$arParams["LINE_ELEMENT_COUNT"]?></option>
					<option <?if( (array_key_exists("page_element_count", $_REQUEST) && $_REQUEST["page_element_count"] == ($arParams["PAGE_ELEMENT_COUNT_"]+2*$arParams["LINE_ELEMENT_COUNT"])) || ($_SESSION["QUICK_EFFORTLESS_CATALOG_PAGE_COUNT"] == ($arParams["PAGE_ELEMENT_COUNT_"]+2*$arParams["LINE_ELEMENT_COUNT"])) ):?>selected<?endif?> value="<?=$APPLICATION->GetCurPageParam("page_element_count=".($arParams["PAGE_ELEMENT_COUNT_"]+2*$arParams["LINE_ELEMENT_COUNT"]), array("page_element_count", "PHPSESSID", "clear_cache", "bitrix_include_areas"))?>"><?=$arParams["PAGE_ELEMENT_COUNT_"]+2*$arParams["LINE_ELEMENT_COUNT"]?></option>
					<option <?if( (array_key_exists("page_element_count", $_REQUEST) && $_REQUEST["page_element_count"] == ($arParams["PAGE_ELEMENT_COUNT_"]+3*$arParams["LINE_ELEMENT_COUNT"])) || ($_SESSION["QUICK_EFFORTLESS_CATALOG_PAGE_COUNT"] == ($arParams["PAGE_ELEMENT_COUNT_"]+3*$arParams["LINE_ELEMENT_COUNT"])) ):?>selected<?endif?> value="<?=$APPLICATION->GetCurPageParam("page_element_count=".($arParams["PAGE_ELEMENT_COUNT_"]+3*$arParams["LINE_ELEMENT_COUNT"]), array("page_element_count", "PHPSESSID", "clear_cache", "bitrix_include_areas"))?>"><?=$arParams["PAGE_ELEMENT_COUNT_"]+3*$arParams["LINE_ELEMENT_COUNT"]?></option>
				</select>
			</div>
		</form>
	</div>
	<div class="col-md-10">
		<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
			<?=$arResult["NAV_STRING"]?>
		<?endif?>
	</div>
</div>
<?endif?>
<?endif?>