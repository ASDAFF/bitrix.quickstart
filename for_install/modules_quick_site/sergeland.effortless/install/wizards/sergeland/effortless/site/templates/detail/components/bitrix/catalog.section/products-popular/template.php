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
$this->setFrameMode(true);?>
<?if(!empty($arResult["ITEMS"])):?>
<div class="owl-carousel <?=$arParams["AUTOPLAY"]?> catalog-grid">
<?foreach($arResult["ITEMS"] as $cell=>$arItem):
	$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
	$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
?>
	<div class="listing-item <?if($cell<4 && $arParams["AUTOPLAY"] == "carousel"):?>object-non-visible<?endif?>" id="<?=$this->GetEditAreaId($arItem['ID']);?>" <?if($cell<4 && $arParams["AUTOPLAY"] == "carousel"):?>data-animation-effect="fadeInLeft" data-effect-delay="<?=(300-$cell*100)?>"<?endif?>>
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
				<?if(!empty($arItem["PROPERTIES"]["ACTION"]["VALUE"])):?><span class="badge default-bg"><?=GetMessage("SERGELAND_EFFORTLESS_CATALOG_ACTION")?> <?if(!empty($arItem["PROPERTIES"]["PERCENT"]["VALUE"])):?><?=$arItem["PROPERTIES"]["PERCENT"]["VALUE"]?><?endif?></span><?endif?>
				<?if(!empty($arItem["PROPERTIES"]["NEW"]["VALUE"])):?><span class="badge danger-bg"><?=GetMessage("SERGELAND_EFFORTLESS_CATALOG_NEW")?></span><?endif?>
			</div>
			<?endif?>
			<div class="status">
				<?if(!empty($arItem["PROPERTIES"]["PRESENCE"]["VALUE"])):?><span class="badge success-bg"><?=GetMessage("SERGELAND_EFFORTLESS_CATALOG_PRESENCE")?></span>
				<?elseif(!empty($arItem["PROPERTIES"]["EXPECTED"]["VALUE"])):?><span class="badge warning-bg"><?=GetMessage("SERGELAND_EFFORTLESS_CATALOG_EXPECTED")?></span>
				<?elseif(!empty($arItem["PROPERTIES"]["UNDER"]["VALUE"])):?><span class="badge info-bg"><?=GetMessage("SERGELAND_EFFORTLESS_CATALOG_UNDER")?></span>
				<?elseif(!empty($arItem["PROPERTIES"]["UNAVAILABLE"]["VALUE"])):?><span class="badge gray-bg"><?=GetMessage("SERGELAND_EFFORTLESS_CATALOG_UNAVAILABLE")?></span><?endif?>
			</div>
		</div>
		<div class="listing-item-body clearfix">
			<h3 class="title <?if(empty($arItem["PREVIEW_TEXT"])):?>mb-15<?endif?>"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>"><?=$arItem["NAME"]?></a></h3>
			<?if(!empty($arItem["PREVIEW_TEXT"])):?><div class="preview-text"><?=$arItem["PREVIEW_TEXT"]?></div><?endif?>
			<?if(!empty($arItem["PROPERTIES"]["PRICE"]["VALUE"])):?><span class="price"><?=$arItem["PROPERTIES"]["PRICE"]["VALUE"]?></span> <?=$arItem["PROPERTIES"]["CURRENCY"]["~VALUE"]?> <?if(!empty($arItem["PROPERTIES"]["PRICE_OLD"]["VALUE"])):?><del><?=$arItem["PROPERTIES"]["PRICE_OLD"]["VALUE"]?></del><?endif?><?endif?>
			<div class="pull-right">
				<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="btn btn-white"><i class="fa fa-shopping-cart"></i></a>
			</div>
		</div>
	</div>
<?endforeach?>
</div>
<?endif?>