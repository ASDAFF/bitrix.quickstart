<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>

<?foreach($arResult as $arItem):?>
	<?if ($arItem["PERMISSION"] > "D"):?>
		<?if ($arItem["SELECTED"]):?>
			<span><?=$arItem["TEXT"]?></span>&nbsp;&nbsp;
		<?else:?>
			<a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>&nbsp;&nbsp;
		<?endif?>
	<?endif?>
<?endforeach?>

<?endif?>