<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!empty($arResult)):

	?>
	<ul id="horizontal-multilevel-menu">
	<?
	$previousLevel = 0;
	foreach($arResult as $arItem)
	{
		
		if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel)
		{
			echo str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));
		}

		if ($arItem["LINK"] == '#')
		{
			echo "<li class=\"divider\"><span>{$arItem['TEXT']}</span>";
		}
		else
		{
			?>
			<li><a href="<?= $arItem["LINK"] ?>"<?= ($arItem["SELECTED"] ? ' class="selected"' : '') ?>><?= $arItem["TEXT"] ?></a><?
		}

		if ($arItem["IS_PARENT"])
		{
			echo '<ul>';
		}
		else
		{
			echo '</li>';
		}
		
		$previousLevel = $arItem["DEPTH_LEVEL"];
	}

	if ($previousLevel > 1)
		echo str_repeat("</ul></li>", ($previousLevel-1) );
	
?>
</ul>
<?endif?>