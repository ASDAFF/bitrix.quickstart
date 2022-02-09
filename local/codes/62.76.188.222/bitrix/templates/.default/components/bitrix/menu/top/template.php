<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?if (!empty($arResult)):?>

<ul class="b-menu_top">
    <?
foreach($arResult as $arItem):
	if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1) 
		continue;
?>
  <li class="b-menu_top__item"><a href="<?=$arItem["LINK"]?>" class="b-topline__link"><span><?=$arItem["TEXT"]?></span></a></li>
 
<?endforeach?>
 
</ul>
<?endif?>
 