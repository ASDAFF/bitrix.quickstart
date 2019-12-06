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
					<option value=""><?=GetMessage("SERGELAND_EFFORTLESS_CATALOG_VIEW")?></option>
					<option <?if( (array_key_exists("view", $_REQUEST) && $_REQUEST["view"] == "price") || ($_SESSION["SERGELAND_EFFORTLESS_CATALOG_TEMPLATE"] == "price") ):?>selected<?endif?> value="<?=$APPLICATION->GetCurPageParam("view=price", array("view", "PHPSESSID", "clear_cache", "bitrix_include_areas"))?>"><?=GetMessage("SERGELAND_EFFORTLESS_CATALOG_PRICE")?></option>
					<option <?if( (array_key_exists("view", $_REQUEST) && $_REQUEST["view"] == "list") || ($_SESSION["SERGELAND_EFFORTLESS_CATALOG_TEMPLATE"] == "list") ):?>selected<?endif?> value="<?=$APPLICATION->GetCurPageParam("view=list", array("view", "PHPSESSID", "clear_cache", "bitrix_include_areas"))?>"><?=GetMessage("SERGELAND_EFFORTLESS_CATALOG_LIST")?></option>
					<option <?if( (array_key_exists("view", $_REQUEST) && $_REQUEST["view"] == "tile") || ($_SESSION["SERGELAND_EFFORTLESS_CATALOG_TEMPLATE"] == "tile") ):?>selected<?endif?> value="<?=$APPLICATION->GetCurPageParam("view=tile", array("view", "PHPSESSID", "clear_cache", "bitrix_include_areas"))?>"><?=GetMessage("SERGELAND_EFFORTLESS_CATALOG_TILE")?></option>	
				</select>
			</div>
		</form>
	</div>
	<div class="col-md-3 col-sm-12 col-xs-12 pull-right">
		<form>
			<div class="form-group">
				<select class="form-control">
					<option value=""><?=GetMessage("SERGELAND_EFFORTLESS_CATALOG_SORT")?></option>
					<option <?if( (array_key_exists("price", $_REQUEST) && array_key_exists("desc", $_REQUEST)) || ($_SESSION["SERGELAND_EFFORTLESS_CATALOG_SORT_FIELD"] == "property_PRICE" && $_SESSION["SERGELAND_EFFORTLESS_CATALOG_SORT_ORDER"] == "desc") ):?>selected<?endif?> value="<?=$APPLICATION->GetCurPageParam("price&desc", array("price", "popular", "name", "desc", "asc", "PHPSESSID", "clear_cache", "bitrix_include_areas"))?>"><?=GetMessage("SERGELAND_EFFORTLESS_CATALOG_PRICE_DESC")?></option>
					<option <?if( (array_key_exists("price", $_REQUEST) && array_key_exists("asc", $_REQUEST)) || ($_SESSION["SERGELAND_EFFORTLESS_CATALOG_SORT_FIELD"] == "property_PRICE" && $_SESSION["SERGELAND_EFFORTLESS_CATALOG_SORT_ORDER"] == "asc") ):?>selected<?endif?> value="<?=$APPLICATION->GetCurPageParam("price&asc", array("price", "popular", "name", "desc", "asc", "PHPSESSID", "clear_cache", "bitrix_include_areas"))?>"><?=GetMessage("SERGELAND_EFFORTLESS_CATALOG_PRICE_ASC")?></option>
					<option <?if( (array_key_exists("popular", $_REQUEST) && array_key_exists("asc", $_REQUEST)) || ($_SESSION["SERGELAND_EFFORTLESS_CATALOG_SORT_FIELD"] == "property_POPULAR" && $_SESSION["SERGELAND_EFFORTLESS_CATALOG_SORT_ORDER"] == "asc") ):?>selected<?endif?> value="<?=$APPLICATION->GetCurPageParam("popular&asc", array("price", "popular", "name", "desc", "asc", "PHPSESSID", "clear_cache", "bitrix_include_areas"))?>"><?=GetMessage("SERGELAND_EFFORTLESS_CATALOG_POPULAR_ASC")?></option>
					<option <?if( (array_key_exists("popular", $_REQUEST) && array_key_exists("desc", $_REQUEST)) || ($_SESSION["SERGELAND_EFFORTLESS_CATALOG_SORT_FIELD"] == "property_POPULAR" && $_SESSION["SERGELAND_EFFORTLESS_CATALOG_SORT_ORDER"] == "desc") ):?>selected<?endif?> value="<?=$APPLICATION->GetCurPageParam("popular&desc", array("price", "popular", "name", "desc", "asc", "PHPSESSID", "clear_cache", "bitrix_include_areas"))?>"><?=GetMessage("SERGELAND_EFFORTLESS_CATALOG_POPULAR_DESC")?></option>
					<option <?if( (array_key_exists("name", $_REQUEST) && array_key_exists("asc", $_REQUEST)) || ($_SESSION["SERGELAND_EFFORTLESS_CATALOG_SORT_FIELD"] == "name" && $_SESSION["SERGELAND_EFFORTLESS_CATALOG_SORT_ORDER"] == "asc") ):?>selected<?endif?> value="<?=$APPLICATION->GetCurPageParam("name&asc", array("price", "popular", "name", "desc", "asc", "PHPSESSID", "clear_cache", "bitrix_include_areas"))?>"><?=GetMessage("SERGELAND_EFFORTLESS_CATALOG_NAME_ASC")?></option>
					<option <?if( (array_key_exists("name", $_REQUEST) && array_key_exists("desc", $_REQUEST)) || ($_SESSION["SERGELAND_EFFORTLESS_CATALOG_SORT_FIELD"] == "name" && $_SESSION["SERGELAND_EFFORTLESS_CATALOG_SORT_ORDER"] == "desc") ):?>selected<?endif?> value="<?=$APPLICATION->GetCurPageParam("name&desc", array("price", "popular", "name", "desc", "asc", "PHPSESSID", "clear_cache", "bitrix_include_areas"))?>"><?=GetMessage("SERGELAND_EFFORTLESS_CATALOG_NAME_DESC")?></option>
				</select>
			</div>
		</form>
	</div>
</div>
<div class="catalog-price">
<?foreach($arResult["ITEMS"] as $arItem):
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
?>
	<div class="listing-item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
		<div class="col-lg-1 col-md-1 col-sm-1 col-xs-3">
			<div class="overlay-container pic">											
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>">
				<?if(!empty($arItem["PREVIEW_PICTURE"])):?>
					<img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arItem["NAME"]?>" class="img-responsive">
				<?else:?>
					<i class="fa fa-image pic">
				<?endif?>
				</a>
			</div>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-9">
			<div class="overlay-container">
				<div class="tags hidden-lg hidden-md hidden-sm">
					<?if(!empty($arItem["PROPERTIES"]["ACTION"]["VALUE"])):?><span class="badge default-bg"><?=GetMessage("SERGELAND_EFFORTLESS_CATALOG_ACTION")?> <?if(!empty($arItem["PROPERTIES"]["PERCENT"]["VALUE"])):?><?=$arItem["PROPERTIES"]["PERCENT"]["VALUE"]?><?endif?></span><?endif?>
					<?if(!empty($arItem["PROPERTIES"]["NEW"]["VALUE"])):?><span class="badge danger-bg"><?=GetMessage("SERGELAND_EFFORTLESS_CATALOG_NEW")?></span><?endif?>
					<?if(!empty($arItem["PROPERTIES"]["PRESENCE"]["VALUE"])):?><span class="badge success-bg"><?=GetMessage("SERGELAND_EFFORTLESS_CATALOG_PRESENCE")?></span>
					<?elseif(!empty($arItem["PROPERTIES"]["EXPECTED"]["VALUE"])):?><span class="badge warning-bg"><?=GetMessage("SERGELAND_EFFORTLESS_CATALOG_EXPECTED")?></span>
					<?elseif(!empty($arItem["PROPERTIES"]["UNDER"]["VALUE"])):?><span class="badge info-bg"><?=GetMessage("SERGELAND_EFFORTLESS_CATALOG_UNDER")?></span>
					<?elseif(!empty($arItem["PROPERTIES"]["UNAVAILABLE"]["VALUE"])):?><span class="badge gray-bg"><?=GetMessage("SERGELAND_EFFORTLESS_CATALOG_UNAVAILABLE")?></span><?endif?>
				</div>
				<h4><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></h4>
				<div class="hidden-lg hidden-md hidden-sm">
					<?if(!empty($arItem["PROPERTIES"]["PRICE"]["VALUE"])):?><span class="price"><?=$arItem["PROPERTIES"]["PRICE"]["VALUE"]?></span> <?=$arItem["PROPERTIES"]["CURRENCY"]["~VALUE"]?> <?if(!empty($arItem["PROPERTIES"]["PRICE_OLD"]["VALUE"])):?><del><?=$arItem["PROPERTIES"]["PRICE_OLD"]["VALUE"]?></del><?endif?><?endif?>
				</div>
			</div>
		</div>
		<div class="col-lg-3 col-md-3 col-sm-3 hidden-xs">
			<div class="overlay-container">
				<?if(!empty($arItem["PROPERTIES"]["PRICE"]["VALUE"])):?><span class="price"><?=$arItem["PROPERTIES"]["PRICE"]["VALUE"]?></span> <?=$arItem["PROPERTIES"]["CURRENCY"]["~VALUE"]?> <?if(!empty($arItem["PROPERTIES"]["PRICE_OLD"]["VALUE"])):?><del><?=$arItem["PROPERTIES"]["PRICE_OLD"]["VALUE"]?></del><?endif?><?endif?>
			</div>
		</div>
		<div class="col-lg-2 col-md-2 col-sm-2 hidden-xs">
			<div class="overlay-container">
				<div class="tags">
					<?if(!empty($arItem["PROPERTIES"]["ACTION"]["VALUE"])):?><span class="badge default-bg"><?=GetMessage("SERGELAND_EFFORTLESS_CATALOG_ACTION")?> <?if(!empty($arItem["PROPERTIES"]["PERCENT"]["VALUE"])):?><?=$arItem["PROPERTIES"]["PERCENT"]["VALUE"]?><?endif?></span><?endif?>
					<?if(!empty($arItem["PROPERTIES"]["NEW"]["VALUE"])):?><span class="badge danger-bg"><?=GetMessage("SERGELAND_EFFORTLESS_CATALOG_NEW")?></span><?endif?>
					<?if(!empty($arItem["PROPERTIES"]["PRESENCE"]["VALUE"])):?><span class="badge success-bg"><?=GetMessage("SERGELAND_EFFORTLESS_CATALOG_PRESENCE")?></span>
					<?elseif(!empty($arItem["PROPERTIES"]["EXPECTED"]["VALUE"])):?><span class="badge warning-bg"><?=GetMessage("SERGELAND_EFFORTLESS_CATALOG_EXPECTED")?></span>
					<?elseif(!empty($arItem["PROPERTIES"]["UNDER"]["VALUE"])):?><span class="badge info-bg"><?=GetMessage("SERGELAND_EFFORTLESS_CATALOG_UNDER")?></span>
					<?elseif(!empty($arItem["PROPERTIES"]["UNAVAILABLE"]["VALUE"])):?><span class="badge gray-bg"><?=GetMessage("SERGELAND_EFFORTLESS_CATALOG_UNAVAILABLE")?></span><?endif?>
				</div>
			</div>
		</div>
	</div>
<?endforeach?>
</div>
<?if($arResult["NAV_RESULT"]->NavRecordCount > $arParams["PAGE_ELEMENT_COUNT"]):?>
<div class="row shop-footer">
	<div class="col-md-2 hidden-xs hidden-sm pull-right">
		<form>
			<div class="form-group">
				<select class="form-control">
					<option <?if( (array_key_exists("page_element_count", $_REQUEST) && $_REQUEST["page_element_count"] == $arParams["PAGE_ELEMENT_COUNT_"]) || ($_SESSION["SERGELAND_EFFORTLESS_CATALOG_PAGE_COUNT"] == $arParams["PAGE_ELEMENT_COUNT_"]) ):?>selected<?endif?> value="<?=$APPLICATION->GetCurPageParam("page_element_count=".$arParams["PAGE_ELEMENT_COUNT_"], array("page_element_count", "PHPSESSID", "clear_cache", "bitrix_include_areas"))?>"><?=$arParams["PAGE_ELEMENT_COUNT_"]?></option>
					<option <?if( (array_key_exists("page_element_count", $_REQUEST) && $_REQUEST["page_element_count"] == ($arParams["PAGE_ELEMENT_COUNT_"]+$arParams["LINE_ELEMENT_COUNT"])) || ($_SESSION["SERGELAND_EFFORTLESS_CATALOG_PAGE_COUNT"] == ($arParams["PAGE_ELEMENT_COUNT_"]+$arParams["LINE_ELEMENT_COUNT"])) ):?>selected<?endif?> value="<?=$APPLICATION->GetCurPageParam("page_element_count=".($arParams["PAGE_ELEMENT_COUNT_"]+$arParams["LINE_ELEMENT_COUNT"]), array("page_element_count", "PHPSESSID", "clear_cache", "bitrix_include_areas"))?>"><?=$arParams["PAGE_ELEMENT_COUNT_"]+$arParams["LINE_ELEMENT_COUNT"]?></option>
					<option <?if( (array_key_exists("page_element_count", $_REQUEST) && $_REQUEST["page_element_count"] == ($arParams["PAGE_ELEMENT_COUNT_"]+2*$arParams["LINE_ELEMENT_COUNT"])) || ($_SESSION["SERGELAND_EFFORTLESS_CATALOG_PAGE_COUNT"] == ($arParams["PAGE_ELEMENT_COUNT_"]+2*$arParams["LINE_ELEMENT_COUNT"])) ):?>selected<?endif?> value="<?=$APPLICATION->GetCurPageParam("page_element_count=".($arParams["PAGE_ELEMENT_COUNT_"]+2*$arParams["LINE_ELEMENT_COUNT"]), array("page_element_count", "PHPSESSID", "clear_cache", "bitrix_include_areas"))?>"><?=$arParams["PAGE_ELEMENT_COUNT_"]+2*$arParams["LINE_ELEMENT_COUNT"]?></option>
					<option <?if( (array_key_exists("page_element_count", $_REQUEST) && $_REQUEST["page_element_count"] == ($arParams["PAGE_ELEMENT_COUNT_"]+3*$arParams["LINE_ELEMENT_COUNT"])) || ($_SESSION["SERGELAND_EFFORTLESS_CATALOG_PAGE_COUNT"] == ($arParams["PAGE_ELEMENT_COUNT_"]+3*$arParams["LINE_ELEMENT_COUNT"])) ):?>selected<?endif?> value="<?=$APPLICATION->GetCurPageParam("page_element_count=".($arParams["PAGE_ELEMENT_COUNT_"]+3*$arParams["LINE_ELEMENT_COUNT"]), array("page_element_count", "PHPSESSID", "clear_cache", "bitrix_include_areas"))?>"><?=$arParams["PAGE_ELEMENT_COUNT_"]+3*$arParams["LINE_ELEMENT_COUNT"]?></option>
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