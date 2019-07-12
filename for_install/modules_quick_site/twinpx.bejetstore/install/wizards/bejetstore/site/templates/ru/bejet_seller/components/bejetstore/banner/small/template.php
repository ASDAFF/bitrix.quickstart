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

if($arResult["COUNT"] <= 0)
	return;

?>
<div class="row bj-index-banners">
	<?foreach ($arResult["ITEMS"] as $key => $arBanner) {?>
		<?if($key != 0 && $arResult["COUNT"] > 1):?><hr class="visible-xs-block"><?endif;?>
		<?switch ($arBanner["PROPERTIES"]["BANNER_TYPE"]["VALUE_XML_ID"]) {
			case 'IMG':
				?><a class='col-sm-6' href="<?=$arBanner["PROPERTIES"]["LINK"]["VALUE"]?>" target="<?=$arBanner["PROPERTIES"]["LINK_TARGET"]["VALUE_XML_ID"]?>"><img class='img-responsive' alt="<?=$arBanner["PROPERTIES"]["LINK_ALT"]["VALUE"]?>"  title="<?=$arBanner["PROPERTIES"]["LINK_ALT"]["VALUE"]?>" src="<?=$arBanner["FIELDS"]["DETAIL_PICTURE"]["SRC"]?>" width="600" height="300" border="0" /></a><?
				break;

			/*case 'FLASH':
				# code...
				break;*/

			case 'HTML':
				?><a class='col-sm-6' href="<?=$arBanner["PROPERTIES"]["LINK"]["VALUE"]?>" target="<?=$arBanner["PROPERTIES"]["LINK_TARGET"]["VALUE_XML_ID"]?>"><?=$arBanner["FIELDS"]["DETAIL_TEXT"]?></a><?
				break;			
		}
		?>
	<?}?>
</div>