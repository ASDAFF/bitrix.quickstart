	<?
	if ($arItem['SECOND_PICT'])
		{	//Отображение второй картинки (при наведении мышки) - работает в адаптивном шаблоне магазина "из коробки"
			?>
			
			<a id="<? echo $arItemIDs['SECOND_PICT']; ?>"
				href="<? echo $arItem['DETAIL_PAGE_URL']; ?>"
				class="bx_catalog_item_images_double"
				style="background-image: url(<? echo (
					!empty($arItem['PREVIEW_PICTURE_SECOND'])
					? $arItem['PREVIEW_PICTURE_SECOND']['SRC']
					: $arItem['PREVIEW_PICTURE']['SRC']
				); ?>)"
				title="<? echo $strTitle; ?>"><?
			if ('Y' == $arParams['SHOW_DISCOUNT_PERCENT'])
			{	//Показывать процент скидки на второй картинке
			?>
				<div
					id="<? echo $arItemIDs['SECOND_DSC_PERC']; ?>"
					class="bx_stick_disc right bottom"
					style="display:<? echo (0 < $arItem['MIN_PRICE']['DISCOUNT_DIFF_PERCENT'] ? '' : 'none'); ?>;">-<? echo $arItem['MIN_PRICE']['DISCOUNT_DIFF_PERCENT']; ?>%</div>
			<?
			}
			if ($arItem['LABEL'])
			{	//Показывать эмблему (Новинка,Скидка) на второй картинке
			?>
				<div class="bx_stick average left top" title="<? echo $arItem['LABEL_VALUE']; ?>"><? echo $arItem['LABEL_VALUE']; ?></div>
			<?
			}
			?>
			</a><?
		}	//End if SECOND_PICT
	?>