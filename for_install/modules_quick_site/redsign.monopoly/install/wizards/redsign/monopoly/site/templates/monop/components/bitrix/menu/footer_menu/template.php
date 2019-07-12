<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if (!empty($arResult)) {
	?><h2 class="coolHeading"><span class="secondLine"><?=$arParams['FOOTER_MENU_TITLE']?></span></h2><?
	?><ul class="footer-menu"><?
			foreach ($arResult as $key => $arItem) {
				?><li><a href="<?=$arItem['LINK']?>"><?=$arItem['TEXT']?></a></li><?
			}
	?></ul><?
}