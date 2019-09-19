	<div class="bx_catalog_item_price">	<!-- Контейнер  для отображения цен-->
		<div id="<? echo $arItemIDs['PRICE']; ?>" class="bx_price">
			<?

			
			if (!empty($arItem['MIN_PRICE']))
				{
					if ('N' == $arParams['PRODUCT_DISPLAY_MODE'] && isset($arItem['OFFERS']) && !empty($arItem['OFFERS']))
						{	//Цена для торговых предложений
							echo GetMessage(
								'CT_BCS_TPL_MESS_PRICE_SIMPLE_MODE',	//от #PRICE# за #MEASURE#
								array(
									'#PRICE#' => $arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE'],
									'#MEASURE#' => GetMessage(
										'CT_BCS_TPL_MESS_MEASURE_SIMPLE_MODE',	//#VALUE# #UNIT#
										array(
											'#VALUE#' => $arItem['MIN_PRICE']['CATALOG_MEASURE_RATIO'],
											'#UNIT#' => $arItem['MIN_PRICE']['CATALOG_MEASURE_NAME']
										)
									) 
								)
							);
						}
					else
						{
							//Цена для обычных товаров
							echo $arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE'];
						}
						
					//Показываем старую цену
					if ('Y' == $arParams['SHOW_OLD_PRICE'] && $arItem['MIN_PRICE']['DISCOUNT_VALUE'] < $arItem['MIN_PRICE']['VALUE'])
						{
							?> <span><? echo $arItem['MIN_PRICE']['PRINT_VALUE']; ?></span><?
						}
				}
			?>
		</div>
	</div>