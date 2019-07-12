<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (empty($arResult))
	return;
?>
<div id="fullmenu">
	<ul>
	<?
	$previousLevel = 0;
	foreach($arResult as $key => $arItem):

		if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):
			echo str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));
		endif;

		if ($arItem["IS_PARENT"]):
			?>
			<? if ($arItem['DEPTH_LEVEL'] > 1 && !$childSelected && $bHasSelected) :?>
			<li>
				<a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
				<ul><?if ($arItem['DEPTH_LEVEL'] == 1 ){ ?><li class="ecke"></li><?}?>
			<? else:?>
			<li>
				<a href="<?=$arItem["LINK"]?>"  ><?=$arItem["TEXT"]?></a>
				<ul><?if ($arItem['DEPTH_LEVEL'] == 1 ){ ?><li class="ecke"></li><?}?>
			<? endif?>
		<?else:
			if ($arItem["PERMISSION"] > "D"):
				?>
				<li>
					<a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a>
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
</div>