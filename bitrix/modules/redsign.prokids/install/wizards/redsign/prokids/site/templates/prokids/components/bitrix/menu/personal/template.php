<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

if(!empty($arResult))
{
	?><div class="in"><?
	
		$index = 1;
		foreach($arResult as $arItem)
		{
			if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1) 
				continue;
			
			if($arItem["SELECTED"])
			{
				?><a class="selected" href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a><?
			} else {
				?><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a><?
			}
			
			if(is_array($arParams['SEPARATORS_PLACE']) && count($arParams['SEPARATORS_PLACE'])>0 && in_array($index,$arParams['SEPARATORS_PLACE']))
			{
				?><div class="separator"></div><?
			}
			
			$index++;
		}
		
	?></div><?
}
