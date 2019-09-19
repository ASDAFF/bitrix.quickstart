<!-- Отображение основной картинки -->
<a id="<? echo $arItemIDs['PICT']; ?>"	
			href="<? echo $arItem['DETAIL_PAGE_URL']; ?>"
			class="bx_catalog_item_images"
			style="background-image: url(<? echo $arItem['PREVIEW_PICTURE']['SRC']; ?>)"
			title="<? echo $strTitle; ?>"><?
	if ('Y' == $arParams['SHOW_DISCOUNT_PERCENT'])
		{	//Показываем процент скидки
		?>
				<div id="<? echo $arItemIDs['DSC_PERC']; ?>" class="bx_stick_disc right bottom" style="display:<? echo (0 < $arItem['MIN_PRICE']['DISCOUNT_DIFF_PERCENT'] ? '' : 'none'); ?>;">-<? echo $arItem['MIN_PRICE']['DISCOUNT_DIFF_PERCENT']; ?>%</div>
		<?
		}
	if ($arItem['LABEL'])
		{	//Показываем эмблему (Новинка,Скидка и т.п.)
		?>
				<div class="bx_stick average left top" title="<? echo $arItem['LABEL_VALUE']; ?>"><? echo $arItem['LABEL_VALUE']; ?></div>
		<?
		}
	?>
</a>