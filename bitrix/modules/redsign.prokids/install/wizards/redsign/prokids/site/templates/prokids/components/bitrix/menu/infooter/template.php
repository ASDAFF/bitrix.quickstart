<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);

if(!empty($arResult))
{
	?><div class="footmenu clearfix"><?
		?><div class="title"><?=$arParams['BLOCK_TITLE']?></div><?
		foreach($arResult as $arMenu){
			?><div class="item"><?
				?><a href="<?=$arMenu['LINK']?>"><span><?=$arMenu['TEXT']?></span></a><?
			?></div><?
		}
	?></div><?
}