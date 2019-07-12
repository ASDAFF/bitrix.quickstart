<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
$this->setFrameMode(true);

if(is_array($arResult['SECTIONS']) && count($arResult['SECTIONS'])>0) {
	$index = 1;
	?><div class="brandbig clearfix"><?
		foreach($arResult['SECTIONS'] as $arSection) {
			?><div class="item"><?
				?><a class="img" href="<?=$arSection['SECTION_PAGE_URL']?>"><?
					if(isset($arSection['PICTURE']['RESIZE']['src'])) {
						?><img src="<?=$arSection['PICTURE']['RESIZE']['src']?>" alt="" title="" /><?
					} else {
						?><img src="<?=$arResult['NO_PHOTO']['src']?>" alt="" title="" /><?
					}
				?></a><?
				?><a class="name" href="<?=$arSection['SECTION_PAGE_URL']?>"><?=$arSection['NAME']?></a><?
			?></div><?
			?><div class="separator x<?=$index?>"></div><?
			$index++;
			if($index>4){$index=0;}
		}
	?></div><?
}