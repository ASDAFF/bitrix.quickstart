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

if (count($arResult["ITEMS"]) < 1)
	return;

?>
<?if(strlen($arResult["WIDE_BANNERS"][0]["FIELDS"]["DETAIL_PICTURE"]["SRC"]) > 0 && strlen($arResult["SQUARE_BANNERS"][0]["FIELDS"]["DETAIL_PICTURE"]["SRC"]) > 0):?>
<div class="bj-index-banners">
<div class="row">
<a href="<?=$arResult["WIDE_BANNERS"][0]["PROPERTIES"]["LINK"]["VALUE"]?>" title="<?=$arResult["WIDE_BANNERS"][0]["PROPERTIES"]["LINK_ALT"]["VALUE"]?>" target="<?=$arResult["WIDE_BANNERS"][0]["PROPERTIES"]["LINK_TARGET"]["VALUE_XML_ID"]?>" class="col-sm-8"><span style="background-image:url(<?=$arResult["WIDE_BANNERS"][0]["FIELDS"]["DETAIL_PICTURE"]["SRC"]?>); height: 200px; display: block;"></span></a>
<hr class="visible-xs-block">
<a href="<?=$arResult["SQUARE_BANNERS"][0]["PROPERTIES"]["LINK"]["VALUE"]?>" title="<?=$arResult["SQUARE_BANNERS"][0]["SQUARE_BANNERS"][0]["PROPERTIES"]["LINK_ALT"]["VALUE"]?>" target="<?=$arResult["SQUARE_BANNERS"][0]["PROPERTIES"]["LINK_TARGET"]["VALUE_XML_ID"]?>" class="col-sm-4"><span style="background-image:url(<?=$arResult["SQUARE_BANNERS"][0]["FIELDS"]["DETAIL_PICTURE"]["SRC"]?>); height: 200px; display: block;"></span></a>
</div>
<?if(strlen($arResult["WIDE_BANNERS"][1]["FIELDS"]["DETAIL_PICTURE"]["SRC"]) > 0 && strlen($arResult["SQUARE_BANNERS"][1]["FIELDS"]["DETAIL_PICTURE"]["SRC"]) > 0):?>
<hr>
<div class="row">
<a href="<?=$arResult["SQUARE_BANNERS"][1]["PROPERTIES"]["LINK"]["VALUE"]?>" title="<?=$arResult["SQUARE_BANNERS"][1]["SQUARE_BANNERS"][0]["PROPERTIES"]["LINK_ALT"]["VALUE"]?>" target="<?=$arResult["SQUARE_BANNERS"][1]["PROPERTIES"]["LINK_TARGET"]["VALUE_XML_ID"]?>" class="col-sm-4"><span style="background-image:url(<?=$arResult["SQUARE_BANNERS"][1]["FIELDS"]["DETAIL_PICTURE"]["SRC"]?>); height: 200px; display: block;"></span></a>
<hr class="visible-xs-block">
<a href="<?=$arResult["WIDE_BANNERS"][1]["PROPERTIES"]["LINK"]["VALUE"]?>" title="<?=$arResult["WIDE_BANNERS"][1]["PROPERTIES"]["LINK_ALT"]["VALUE"]?>" target="<?=$arResult["WIDE_BANNERS"][1]["PROPERTIES"]["LINK_TARGET"]["VALUE_XML_ID"]?>" class="col-sm-8"><span style="background-image:url(<?=$arResult["WIDE_BANNERS"][1]["FIELDS"]["DETAIL_PICTURE"]["SRC"]?>); height: 200px; display: block;"></span></a>
</div>
<?endif;?>
</div>
<?endif;?>