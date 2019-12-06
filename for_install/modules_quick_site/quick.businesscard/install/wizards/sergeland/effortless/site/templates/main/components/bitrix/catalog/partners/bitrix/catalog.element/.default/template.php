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

$strTitle = (
	isset($arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"]) && $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"] != ''
	? $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"]
	: $arResult['NAME']
);
$strAlt = (
	isset($arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"]) && $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"] != ''
	? $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"]
	: $arResult['NAME']
);
?>
<div class="row" id="<?=$this->GetEditAreaId($arResult['ID'])?>">
	<div class="col-md-12">
		<h2 class="page-title"><?=$arResult["NAME"]?></h2>
		<?if(!empty($arResult["SECTION"]["NAME"])):?><span class="badge default-bg mb-15"><?=$arResult["SECTION"]["NAME"]?></span><?endif?>
	</div>
	<div class="col-md-12">
		<?if(!empty($arResult["PREVIEW_PICTURE"])):?><img alt="<?=$strAlt?>" class="img-responsive col-sm-5 pull-right mb-20" src="<?=$arResult["PREVIEW_PICTURE"]["SRC"]?>"><?endif?>
		<?=$arResult["PREVIEW_TEXT"]?>
	</div>
</div>
<hr>
<ul class="list-unstyled team no-underline">
	<?if(!empty($arResult["PROPERTIES"]["HREF"]["VALUE"])):?><li><i class="fa fa-external-link"></i> <a href="<?=$arResult["PROPERTIES"]["HREF"]["VALUE"]?>"><?=$arResult["PROPERTIES"]["HREF"]["VALUE"]?></a></li><?endif?>
	<?if(!empty($arResult["PROPERTIES"]["PHONE"]["VALUE"])):?><li><i class="fa fa-phone"></i> <?=$arResult["PROPERTIES"]["PHONE"]["VALUE"]?></li><?endif?>
	<?if(!empty($arResult["PROPERTIES"]["ADDRESS"]["VALUE"])):?><li><i class="fa fa-globe"></i> <?=$arResult["PROPERTIES"]["ADDRESS"]["VALUE"]?></li><?endif?>
</ul>