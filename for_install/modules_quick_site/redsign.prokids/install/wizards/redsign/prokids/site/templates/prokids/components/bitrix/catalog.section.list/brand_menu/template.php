<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$this->setFrameMode(true);

if(is_array($arResult['SECTIONS']) && count($arResult['SECTIONS'])>0) {
	?><div class="pmenu"><?
		?><div class="brandmenu"><?
			foreach($arResult['SECTIONS'] as $arSection) {
				?><a href="<?=$arSection['SECTION_PAGE_URL']?>"><?=$arSection['NAME']?></a><?
			}
		?></div><?
	?></div><?
}