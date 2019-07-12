<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
<ul>
<?
foreach($arResult as $arItem):
	if($arParams["MAX_LEVEL"] == 1 && $arItem["DEPTH_LEVEL"] > 1) 
		continue;
?>
	<?
	//deb($arItem);
	$class = '';
	$dataToggle = '';
	if ($arItem["PARAMS"]["DATA_TOGGLE"]) {
		$dataToggle = 'data-toggle="' . $arItem["PARAMS"]["DATA_TOGGLE"] . '"';
	}
	if ($arItem["PARAMS"]["CLASS"]) {
		$class = 'class="' . $arItem["PARAMS"]["CLASS"] . '"';
	}
	
	if($arItem["SELECTED"]):?>
		<li><strong><a href="<?=$arItem["LINK"]?>" <?=$class?> <?=$dataToggle?>><?=$arItem["TEXT"]?></a></strong></li>
	<?else:?>
		<li><a href="<?=$arItem["LINK"]?>" <?=$class?> <?=$dataToggle?>><?=$arItem["TEXT"]?></a></li>
	<?endif?>
	
<?endforeach?>

</ul>
<?endif?>