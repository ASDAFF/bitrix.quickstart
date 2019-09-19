	<?
		if ('Y' == $arParams['PRODUCT_DISPLAY_MODE'])	//Подробная схема отображения (Книпки купить,количество +/- и пр.)
		{
			?>
			
		<div class="bx_catalog_item_controls no_touch">
			<?
			if ('Y' == $arParams['USE_PRODUCT_QUANTITY'])
				{	//Показывать количество товара
				?>
					<div class="bx_catalog_item_controls_blockone">
						<a id="<? echo $arItemIDs['QUANTITY_DOWN']; ?>" href="javascript:void(0)" class="bx_bt_button_type_2 bx_small" rel="nofollow">-</a>
						<input type="text" class="bx_col_input" id="<? echo $arItemIDs['QUANTITY']; ?>" name="<? echo $arParams["PRODUCT_QUANTITY_VARIABLE"]; ?>" value="<? echo $arItem['CATALOG_MEASURE_RATIO']; ?>">
						<a id="<? echo $arItemIDs['QUANTITY_UP']; ?>" href="javascript:void(0)" class="bx_bt_button_type_2 bx_small" rel="nofollow">+</a>
						<span id="<? echo $arItemIDs['QUANTITY_MEASURE']; ?>"></span>
					</div>
				<?
				}
			?>
			
			<!-- Кнопка "Купить" -->	
			<div class="bx_catalog_item_controls_blocktwo">
				<a id="<? echo $arItemIDs['BUY_LINK']; ?>" class="bx_bt_button bx_medium" href="javascript:void(0)" rel="nofollow">
					<? echo ('' != $arParams['MESS_BTN_BUY'] ? $arParams['MESS_BTN_BUY'] : GetMessage('CT_BCS_TPL_MESS_BTN_BUY')); ?>
				</a>
			</div>
		<div style="clear: both;"></div>
		</div>
			<?
		}
		else
		{	//Простая схема отображения (кнопка "Подробнее")
			?>
		<div class="bx_catalog_item_controls no_touch">
			<a class="bx_bt_button_type_2 bx_medium" href="<? echo $arItem['DETAIL_PAGE_URL']; ?>">
				<? echo ('' != $arParams['MESS_BTN_DETAIL'] ? $arParams['MESS_BTN_DETAIL'] : GetMessage('CT_BCS_TPL_MESS_BTN_DETAIL'));?>
			</a>
		</div>
			<?
		}
		?>
		
		
		<!-- Для мобильных устройств (класс touch) (не видна на обычном ПК) -->
		<div class="bx_catalog_item_controls touch">
			<a class="bx_bt_button_type_2 bx_medium" href="<? echo $arItem['DETAIL_PAGE_URL']; ?>">
				<? echo ('' != $arParams['MESS_BTN_DETAIL'] ? $arParams['MESS_BTN_DETAIL'] : GetMessage('CT_BCS_TPL_MESS_BTN_DETAIL')); ?>
			</a>
		</div>
		