<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>



<?if (!empty($arResult)){   ?>



<ul id="left-menu">
	<? $prevLevel = 1; $count = sizeof($arResult); foreach($arResult as $iInd=>$arItem): $class = '';
		if ($arItem['SELECTED'])
			$class = $arItem['DEPTH_LEVEL'] == 1 && $arItem['IS_PARENT'] ? ' class="act-opened"' : ' class="act"';
	 ?>
	<? if ($arItem['DEPTH_LEVEL'] < $prevLevel): ?></ul></li><? endif; ?>

		<li<?=$class;?>><? if ($arItem['IS_PARENT'] && $arItem['DEPTH_LEVEL'] == 1): ?><i></i><? endif; ?><a href="<?=$arItem["LINK"]?>"><?=$arItem["TEXT"]?></a><? if (!$arItem['IS_PARENT']): ?></li>

		<? else: ?>

		<ul><? endif; ?>


	<? $prevLevel = $arItem['DEPTH_LEVEL']; endforeach; ?>

</ul>

<?}?>