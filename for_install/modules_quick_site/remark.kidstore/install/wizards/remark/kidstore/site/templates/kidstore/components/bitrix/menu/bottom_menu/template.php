<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (empty($arResult))
	return;
?>

<ul>
	<?foreach($arResult as $itemIdex => $arItem):?>
		<?if ($arItem["DEPTH_LEVEL"] == "1"):?>
			<li><a href="<?=$arItem["LINK"]?>"><?=htmlspecialcharsbx($arItem["TEXT"])?></a></li>
		<?endif?>
	<?endforeach;?>
</ul>