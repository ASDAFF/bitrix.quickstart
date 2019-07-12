<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
<ul id="top-links">

<?
foreach($arResult as $arItem):
	if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1)
		continue;
?>
	<?if($arItem["SELECTED"]):?>
		<li><a href="<?=$arItem["LINK"]?>"><b><?=$arItem["TEXT"]?></b><?if ($arItem['LINK'] == '/about/contacts/'):?><i></i><? endif; ?></a></li>
	<?else:?>
		<li><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?><?if ($arItem['LINK'] == '/about/contacts/'):?><i></i><? endif; ?></a></li>
	<?endif?>

<?endforeach?>

</ul>
<?endif?>