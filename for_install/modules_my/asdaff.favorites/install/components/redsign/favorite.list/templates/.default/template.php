<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if($arResult["COUNT"]>0)
{
	?><div class="favorite"><?=GetMessage("FAVORITE_COUNT")?>: <?=$arResult["COUNT"]?></div><?
}
