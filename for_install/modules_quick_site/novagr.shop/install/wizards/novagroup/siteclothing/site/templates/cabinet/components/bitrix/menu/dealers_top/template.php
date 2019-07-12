<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
//deb($arResult);


if (!empty($arResult)) {
?>
<ul class="nav">
<?
foreach($arResult as $arItem) {
	if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1) 
		continue;
	if ($arItem["SELECTED"]):?>
		<li class="active"><a href="<?=$arItem["LINK"]?>" ><?=$arItem["TEXT"]?></a></li>
	<?
	else:?>
		<li ><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a></li>
	<?
	endif?>	
<?
}
?>
</ul>
<?
}
?>