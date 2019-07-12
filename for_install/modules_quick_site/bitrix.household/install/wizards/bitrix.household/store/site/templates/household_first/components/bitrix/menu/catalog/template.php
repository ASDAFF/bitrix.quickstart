<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
//echo '<pre>'; print_r($arResult); echo '</pre>';
if (count($arResult) < 1)
	return;

$bManyIblock = array_key_exists("IBLOCK_ROOT_ITEM", $arResult[0]["PARAMS"]);
?>

	<ul class="menu">
<?
	$previousLevel = 0;
	foreach($arResult as $key => $arItem):

		if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):
			echo str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));
		endif;

		if ($arItem["IS_PARENT"]):
			$i = $key;
			$bHasSelected = $arItem['SELECTED'];
			$childSelected = false;
			if (!$bHasSelected)
			{
				while ($arResult[++$i]['DEPTH_LEVEL'] > $arItem['DEPTH_LEVEL'])
				{
					if ($arResult[$i]['SELECTED'])
					{
						$bHasSelected = $childSelected = true; break;
					}
				}
			}
			
			$className = $nHasSelected ? 'selected' : '';//($bHasSelected ? 'selected' : '');
?>
		<? if ($arItem['DEPTH_LEVEL'] > 1 && !$childSelected && $bHasSelected) :?>
			<li class="active">
			<a class="selected" href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
			<ul>

		<? else:?>
			<li<?=$bHasSelected ? ' class="selected"' : ''?>>
			<a<?=$bHasSelected ? ' class="selected"' : ''?> href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
			<ul<?=$bHasSelected || ($bManyIblock && $arItem['DEPTH_LEVEL'] <= 1) ? '' : ' style="display: none;"'?>>
		<? endif?>


<?
		else:
			if ($arItem["PERMISSION"] > "D"):
				$className = $arItem['SELECTED'] ? $arItem['DEPTH_LEVEL'] > 1 ? 'active' : "selected" : '';
?>
		<li<?=$className ? ' class="'.$className.'"' : ''?>>

			<a<?if ($arItem['SELECTED']):?> class="selected"<?endif?> href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>

		</li>
<?
			endif;
		endif;

		$previousLevel = $arItem["DEPTH_LEVEL"];
	endforeach;

	if ($previousLevel > 1)://close last item tags
		echo str_repeat("</ul></li>", ($previousLevel-1) );
	endif;
?>
	</ul>