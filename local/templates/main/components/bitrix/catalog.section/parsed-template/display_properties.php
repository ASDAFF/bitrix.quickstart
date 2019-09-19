		<?
		//Если свойства для показа пользователю DISPLAY_PROPERTIES не пустые - выводим их
		if (isset($arItem['DISPLAY_PROPERTIES']) && !empty($arItem['DISPLAY_PROPERTIES']))
			{	
				
			?>
				<div class="bx_catalog_item_articul">
			<?
				foreach ($arItem['DISPLAY_PROPERTIES'] as $arOneProp)
					{
						?><br><strong><? echo $arOneProp['NAME']; ?></strong> <?
							echo (
								is_array($arOneProp['DISPLAY_VALUE'])
								? implode('<br>', $arOneProp['DISPLAY_VALUE'])
								: $arOneProp['DISPLAY_VALUE']
							);
					}
			?>
				</div>
		<? } ?>