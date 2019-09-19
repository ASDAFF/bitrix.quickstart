<?
/**
 * Copyright (c) 19/9/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

	//Формирование вёрстки блоков выбора свойств торговых предложений
	if (!empty($arResult['SKU_PROPS']))
	{
		foreach ($arResult['SKU_PROPS'] as &$arProp)
		{
			ob_start();
			
			//Если показывать как текст
			if ('TEXT' == $arProp['SHOW_MODE'])
			{
				if (5 < $arProp['VALUES_COUNT'])
				{
					$strClass = 'bx_item_detail_size full';
					$strWidth = ($arProp['VALUES_COUNT']*20).'%';
					$strOneWidth = (100/$arProp['VALUES_COUNT']).'%';
					$strSlideStyle = '';
				}
				else
				{
					$strClass = 'bx_item_detail_size';
					$strWidth = '100%';
					$strOneWidth = '20%';
					$strSlideStyle = 'display: none;';
				}
				?>
				
				<div class="<? echo $strClass; ?>" id="#ITEM#_prop_<? echo $arProp['ID']; ?>_cont">
					<span class="bx_item_section_name_gray">
						<? echo htmlspecialcharsex($arProp['NAME']); ?>
					</span>
				<div class="bx_size_scroller_container">
					<div class="bx_size">
						<ul id="#ITEM#_prop_<? echo $arProp['ID']; ?>_list" style="width: <? echo $strWidth; ?>;">
						<?
							foreach ($arProp['VALUES'] as $arOneValue)
							{
								?><li
									data-treevalue="<? echo $arProp['ID'].'_'.$arOneValue['ID']; ?>"
									data-onevalue="<? echo $arOneValue['ID']; ?>"
									style="width: <? echo $strOneWidth; ?>;"><i></i>
										<span class="cnt">
											<? echo htmlspecialcharsex($arOneValue['NAME']); ?>
										</span>
									</li><?
							}
						?></ul>
					</div>
		<div class="bx_slide_left" id="#ITEM#_prop_<? echo $arProp['ID']; ?>_left" data-treevalue="<? echo $arProp['ID']; ?>" style="<? echo $strSlideStyle; ?>"></div>
		<div class="bx_slide_right" id="#ITEM#_prop_<? echo $arProp['ID']; ?>_right" data-treevalue="<? echo $arProp['ID']; ?>" style="<? echo $strSlideStyle; ?>"></div>
	</div>
</div><?
			}
			elseif ('PICT' == $arProp['SHOW_MODE'])	//Иначе если показывать как картинку
			{
				if (5 < $arProp['VALUES_COUNT'])
				{
					$strClass = 'bx_item_detail_scu full';
					$strWidth = ($arProp['VALUES_COUNT']*20).'%';
					$strOneWidth = (100/$arProp['VALUES_COUNT']).'%';
					$strSlideStyle = '';
				}
				else
				{
					$strClass = 'bx_item_detail_scu';
					$strWidth = '100%';
					$strOneWidth = '20%';
					$strSlideStyle = 'display: none;';
				}
				?><div class="<? echo $strClass; ?>" id="#ITEM#_prop_<? echo $arProp['ID']; ?>_cont">
<span class="bx_item_section_name_gray"><? echo htmlspecialcharsex($arProp['NAME']); ?></span>
<div class="bx_scu_scroller_container"><div class="bx_scu"><ul id="#ITEM#_prop_<? echo $arProp['ID']; ?>_list" style="width: <? echo $strWidth; ?>;"><?
				foreach ($arProp['VALUES'] as $arOneValue)
				{
					?><li
						data-treevalue="<? echo $arProp['ID'].'_'.$arOneValue['ID'] ?>"
						data-onevalue="<? echo $arOneValue['ID']; ?>"
						style="width: <? echo $strOneWidth; ?>; padding-top: <? echo $strOneWidth; ?>;"
						><i title="<? echo htmlspecialcharsbx($arOneValue['NAME']); ?>"></i>
						<span class="cnt"><span class="cnt_item"
						style="background-image:url('<? echo $arOneValue['PICT']['SRC']; ?>');"
						title="<? echo htmlspecialcharsbx($arOneValue['NAME']); ?>"
					></span></span></li><?
				}
?></ul></div>
		<div class="bx_slide_left" id="#ITEM#_prop_<? echo $arProp['ID']; ?>_left" data-treevalue="<? echo $arProp['ID']; ?>" style="<? echo $strSlideStyle; ?>"></div>
		<div class="bx_slide_right" id="#ITEM#_prop_<? echo $arProp['ID']; ?>_right" data-treevalue="<? echo $arProp['ID']; ?>" style="<? echo $strSlideStyle; ?>"></div>
	</div>
</div>
<?
			}
			$arSkuTemplate[$arProp['CODE']] = ob_get_contents();
			ob_end_clean();
		}
		unset($arProp);
	} ?> 