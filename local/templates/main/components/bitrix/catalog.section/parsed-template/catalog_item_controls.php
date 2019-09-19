		<div class="bx_catalog_item_controls">
		
			<?
			if ($arItem['CAN_BUY'])
				{	//Если товар можно купить
					if ('Y' == $arParams['USE_PRODUCT_QUANTITY'])
						{	//Показывать количество товара
						?>
							<div class="bx_catalog_item_controls_blockone">
								<div style="display: inline-block;position: relative;">
									<a id="<? echo $arItemIDs['QUANTITY_DOWN']; ?>" href="javascript:void(0)" class="bx_bt_button_type_2 bx_small" rel="nofollow">-</a>
									<input type="text" class="bx_col_input" id="<? echo $arItemIDs['QUANTITY']; ?>" name="<? echo $arParams["PRODUCT_QUANTITY_VARIABLE"]; ?>" value="<? echo $arItem['CATALOG_MEASURE_RATIO']; ?>">
									<a id="<? echo $arItemIDs['QUANTITY_UP']; ?>" href="javascript:void(0)" class="bx_bt_button_type_2 bx_small" rel="nofollow">+</a>
									<span id="<? echo $arItemIDs['QUANTITY_MEASURE']; ?>"><? echo $arItem['CATALOG_MEASURE_NAME']; ?></span>
								</div>
							</div>
						<?
						}
					?>
					
				<!-- Кнопка купить -->	
				<div class="bx_catalog_item_controls_blocktwo">
					<a id="<? echo $arItemIDs['BUY_LINK']; ?>" class="bx_bt_button bx_medium" href="javascript:void(0)" rel="nofollow">
						<?
							// CT_BCS_TPL_MESS_BTN_BUY - Купить 
							// MESS_BTN_BUY - Задаётся пользователем в настройках компонента, вкладка "Дополнительно" параметр "Текст кнопки Купить"
							echo ('' != $arParams['MESS_BTN_BUY'] ? $arParams['MESS_BTN_BUY'] : GetMessage('CT_BCS_TPL_MESS_BTN_BUY'));
						?>
					</a>
				</div>
					<?
				}	
			else
			{	//Если товар нельзя купить
				?>
				<!-- Сообщение "ТОвара нет в наличии" -->
				<div class="bx_catalog_item_controls_blockone">
					<span class="bx_notavailable">
					<? echo ('' != $arParams['MESS_NOT_AVAILABLE'] ? $arParams['MESS_NOT_AVAILABLE'] : GetMessage('CT_BCS_TPL_MESS_PRODUCT_NOT_AVAILABLE'));?>	
					</span>
				</div>
				
				<?	//Если на отсутствующий товар можно подписаться, выводим соответсвубщую кнопку
				if ('Y' == $arParams['PRODUCT_SUBSCRIPTION'] && 'Y' == $arItem['CATALOG_SUBSCRIPTION'])
					{
					?>
						<div class="bx_catalog_item_controls_blocktwo">
							<a id="<? echo $arItemIDs['SUBSCRIBE_LINK']; ?>" class="bx_bt_button_type_2 bx_medium" href="javascript:void(0)">
								<?
								// CT_BCS_TPL_MESS_BTN_SUBSCRIBE - Подписаться
								// MESS_BTN_SUBSCRIBE -  Задаётся пользователем в настройках компонента, вкладка "Дополнительно" параметр "Текст кнопки Подписаться"
								echo ('' != $arParams['MESS_BTN_SUBSCRIBE'] ? $arParams['MESS_BTN_SUBSCRIBE'] : GetMessage('CT_BCS_TPL_MESS_BTN_SUBSCRIBE')); ?>
							</a>
						</div>
					<?
					}
			}
			?>
		<div style="clear: both;"></div>
		</div>	<!-- Конец bx_catalog_item_controls -->