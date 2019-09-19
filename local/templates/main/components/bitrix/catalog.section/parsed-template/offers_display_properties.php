		<? //Отображение свойств из DISPLAY_PROPERTIES
		$boolShowOfferProps = ('Y' == $arParams['PRODUCT_DISPLAY_MODE'] && $arItem['OFFERS_PROPS_DISPLAY']);
		$boolShowProductProps = (isset($arItem['DISPLAY_PROPERTIES']) && !empty($arItem['DISPLAY_PROPERTIES']));
		if ($boolShowProductProps || $boolShowOfferProps)
			{
			?>
				<div class="bx_catalog_item_articul">
			<?
				if ($boolShowProductProps)
				{
					foreach ($arItem['DISPLAY_PROPERTIES'] as $arOneProp)
					{
					?><br><strong><? echo $arOneProp['NAME']; ?></strong> <?
						echo (
							is_array($arOneProp['DISPLAY_VALUE'])
							? implode(' / ', $arOneProp['DISPLAY_VALUE'])
							: $arOneProp['DISPLAY_VALUE']
						);
					}
				}
				if ($boolShowOfferProps)
				{
			?>
					<span id="<? echo $arItemIDs['DISPLAY_PROP_DIV']; ?>" style="display: none;"></span>
			<?
				}
			?>
				</div>
		<?	} ?> 