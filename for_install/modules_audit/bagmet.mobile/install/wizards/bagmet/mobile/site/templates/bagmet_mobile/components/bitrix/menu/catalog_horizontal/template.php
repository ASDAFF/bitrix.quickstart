<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
$APPLICATION->SetAdditionalCSS('/bitrix/templates/'.SITE_TEMPLATE_ID.'/components/bitrix/menu/catalog_horizontal/default_style.css');
//$APPLICATION->SetAdditionalCSS('/bitrix/templates/'.SITE_TEMPLATE_ID.'/components/bitrix/menu/catalog_horizontal/special_style.css');

if (count($arResult) < 1)
	return;
?>
<ul class="dropdown">
	<?
	$previousLevel = 0;
	foreach ($arResult as $key => $arItem)
	{
		if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):
			echo str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));
		endif;

		if ($arItem["IS_PARENT"]):
		$className = ($arItem['DEPTH_LEVEL'] == 1) ? "dir menu_title" : /*($arItem['DEPTH_LEVEL'] == 2 ? "dir menu_folder" :*/ "dir"/*)*/;
		if ($arItem['SELECTED'] && $arItem['DEPTH_LEVEL'] == 1) $className.=" active";
		?>
		<li<?=$className ? ' class="'.$className.'"' : ''?>>
			<a href="<?=$arItem["LINK"]?>" class="<?/*if ($arItem['SELECTED']):?> active<?endif*/?>"><?=$arItem["TEXT"]?></a>
			<ul>
		<?
		else:
			if ($arItem["PERMISSION"] > "D"):
				$className= $arItem['DEPTH_LEVEL'] == 1 ? " menu_title" : /*($arItem['DEPTH_LEVEL'] == 2 ? "menu_folder" : */""/*)*/;
				if ($arItem['SELECTED']) $className.=" active";
				?>
			<li<?=$className ? ' class="'.$className.'"' : ''?>>
				<a href="<?=$arItem["LINK"]?>" <?/*if ($arItem['SELECTED']):?>class="active"<?endif*/?>><?=$arItem["TEXT"]?></a>
				</li><?
			endif;
		endif;

		$previousLevel = $arItem["DEPTH_LEVEL"];
	}

	if ($previousLevel > 1):
		echo str_repeat("</ul></li>", ($previousLevel-1) );
	endif;
?>
</ul>