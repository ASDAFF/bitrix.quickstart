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

$bFirst = true;
?>
<div id="banner-carousel-1" class="carousel slide" data-ride="carousel" data-interval="20000">
	<!-- Indicators -->
	<ol class="carousel-indicators">
	<?
	for ($i=0; $i < $arResult["COUNT"]; $i++) { 
	?><li data-target="#banner-carousel-1" data-slide-to="<?=$i?>"<?=($i == 0 ? ' class="active"' : '')?>></li><?
	}
	?>
	</ol>
	<!-- Wrapper for slides -->
	<div class="carousel-inner">
	<?foreach ($arResult["ITEMS"] as $key => $arBanner) {
		switch ($arBanner["PROPERTIES"]["BANNER_TYPE"]["VALUE_XML_ID"]) {
			case 'IMG':
				?><a class='item<?=($bFirst ? " active" : "")?>' data-id="<?=$arBanner["ID"]?>" href="<?=$arBanner["PROPERTIES"]["LINK"]["VALUE"]?>" target="<?=$arBanner["PROPERTIES"]["LINK_TARGET"]["VALUE_XML_ID"]?>"><img class='img-responsive' alt="<?=$arBanner["PROPERTIES"]["LINK_ALT"]["VALUE"]?>"  title="<?=$arBanner["PROPERTIES"]["LINK_ALT"]["VALUE"]?>" src="<?=$arBanner["FIELDS"]["DETAIL_PICTURE"]["SRC"]?>" width="600" height="300" border="0" /></a><?
				break;

			/*case 'FLASH':
				# code...
				break;*/

			case 'HTML':
				?><a class='item<?=($bFirst ? " active" : "")?>' data-id="<?=$arBanner["ID"]?>" href="<?=$arBanner["PROPERTIES"]["LINK"]["VALUE"]?>" target="<?=$arBanner["PROPERTIES"]["LINK_TARGET"]["VALUE_XML_ID"]?>"><?=$arBanner["FIELDS"]["DETAIL_TEXT"]?></a><?
				break;			
		}
		$bFirst = false;?>
	<?}?>
	</div>
	<!-- Controls -->
	<a class="left carousel-control" href="#banner-carousel-1" role="button" data-slide="prev">
		<span class="chevron-left" aria-hidden="true"></span>
		<span class="sr-only">Previous</span>
	</a>
	<a class="right carousel-control" href="#banner-carousel-1" role="button" data-slide="next">
		<span class="chevron-right" aria-hidden="true"></span>
		<span class="sr-only">Next</span>
	</a>
</div>