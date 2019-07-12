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
<?foreach ($arResult["ITEMS"] as $key => $arBanner) {
	switch ($arBanner["PROPERTIES"]["BANNER_TYPE"]["VALUE_XML_ID"]) {
		case 'IMG':
			?><a href="<?=$arBanner["PROPERTIES"]["LINK"]["VALUE"]?>" title="<?=$arBanner["PROPERTIES"]["LINK_ALT"]["VALUE"]?>" target="<?=$arBanner["PROPERTIES"]["LINK_TARGET"]["VALUE_XML_ID"]?>" class="col-sm-8"><span style="background-image:url(<?=$arBanner["FIELDS"]["DETAIL_PICTURE"]["SRC"]?>); height: 200px; display: block;"></span></a><?
			break;

		/*case 'FLASH':
			# code...
			break;*/
	}
	?>
<?}?>