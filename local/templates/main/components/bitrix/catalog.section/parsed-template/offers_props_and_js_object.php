<?
/**
 * Copyright (c) 19/9/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if ('Y' == $arParams['PRODUCT_DISPLAY_MODE'])
		{
			if (!empty($arItem['OFFERS_PROP']))
			{	//При расширенной схеме отображения и не пустых свойствах торгового предложения (OFFERS_PROP)
				//показываем контейнер свойств составляющих торговое предложение. $arSkuTemplate - формируется в начале основного файла шаблона.
				$arSkuProps = array();
				?>
				
				<div class="bx_catalog_item_scu" id="<? echo $arItemIDs['PROP_DIV']; ?>">
					<?
						foreach ($arSkuTemplate as $code => $strTemplate)
							{
								if (!isset($arItem['OFFERS_PROP'][$code]))
									continue;
								echo '<div>', str_replace('#ITEM#_prop_', $arItemIDs['PROP'], $strTemplate), '</div>';
							}
						foreach ($arResult['SKU_PROPS'] as $arOneProp)
							{
								if (!isset($arItem['OFFERS_PROP'][$arOneProp['CODE']]))
									continue;
								$arSkuProps[] = array(
									'ID' => $arOneProp['ID'],
									'SHOW_MODE' => $arOneProp['SHOW_MODE'],
									'VALUES_COUNT' => $arOneProp['VALUES_COUNT']
								);
							}
						foreach ($arItem['JS_OFFERS'] as &$arOneJs)
							{
								if (0 < $arOneJs['PRICE']['DISCOUNT_DIFF_PERCENT'])
									$arOneJs['PRICE']['DISCOUNT_DIFF_PERCENT'] = '-'.$arOneJs['PRICE']['DISCOUNT_DIFF_PERCENT'].'%';
							}
						unset($arOneJs);
					?>
				</div>
			
			<!-- Отображение обычных (общих) свойств торгового предложения -->	
			<? if ($arItem['OFFERS_PROPS_DISPLAY'])
				{
					foreach ($arItem['JS_OFFERS'] as $keyOffer => $arJSOffer)
					{
						$strProps = '';
						if (!empty($arJSOffer['DISPLAY_PROPERTIES']))
						{
							foreach ($arJSOffer['DISPLAY_PROPERTIES'] as $arOneProp)
							{
								$strProps .= '<br>'.$arOneProp['NAME'].' <strong>'.(
									is_array($arOneProp['VALUE'])
									? implode(' / ', $arOneProp['VALUE'])
									: $arOneProp['VALUE']
								).'</strong>';
							}
						}
						$arItem['JS_OFFERS'][$keyOffer]['DISPLAY_PROPERTIES'] = $strProps;
					}
				}
				
				//Массив параметроф объекта
				$arJSParams = array(
					'PRODUCT_TYPE' => $arItem['CATALOG_TYPE'],
					'SHOW_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
					'SHOW_ADD_BASKET_BTN' => false,
					'SHOW_BUY_BTN' => true,
					'SHOW_ABSENT' => true,
					'SHOW_SKU_PROPS' => $arItem['OFFERS_PROPS_DISPLAY'],
					'SECOND_PICT' => $arItem['SECOND_PICT'],
					'SHOW_OLD_PRICE' => ('Y' == $arParams['SHOW_OLD_PRICE']),
					'SHOW_DISCOUNT_PERCENT' => ('Y' == $arParams['SHOW_DISCOUNT_PERCENT']),
					'DEFAULT_PICTURE' => array(
						'PICTURE' => $arItem['PRODUCT_PREVIEW'],
						'PICTURE_SECOND' => $arItem['PRODUCT_PREVIEW_SECOND']
					),
					'VISUAL' => array(
						'ID' => $arItemIDs['ID'],
						'PICT_ID' => $arItemIDs['PICT'],
						'SECOND_PICT_ID' => $arItemIDs['SECOND_PICT'],
						'QUANTITY_ID' => $arItemIDs['QUANTITY'],
						'QUANTITY_UP_ID' => $arItemIDs['QUANTITY_UP'],
						'QUANTITY_DOWN_ID' => $arItemIDs['QUANTITY_DOWN'],
						'QUANTITY_MEASURE' => $arItemIDs['QUANTITY_MEASURE'],
						'PRICE_ID' => $arItemIDs['PRICE'],
						'TREE_ID' => $arItemIDs['PROP_DIV'],
						'TREE_ITEM_ID' => $arItemIDs['PROP'],
						'BUY_ID' => $arItemIDs['BUY_LINK'],
						'ADD_BASKET_ID' => $arItemIDs['ADD_BASKET_ID'],
						'DSC_PERC' => $arItemIDs['DSC_PERC'],
						'SECOND_DSC_PERC' => $arItemIDs['SECOND_DSC_PERC'],
						'DISPLAY_PROP_DIV' => $arItemIDs['DISPLAY_PROP_DIV'],
					),
					'BASKET' => array(
						'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'],
						'PROPS' => $arParams['PRODUCT_PROPS_VARIABLE'],
						'SKU_PROPS' => $arItem['OFFERS_PROP_CODES']
					),
					'PRODUCT' => array(
						'ID' => $arItem['ID'],
						'NAME' => $arItem['~NAME']
					),
					'OFFERS' => $arItem['JS_OFFERS'],
					'OFFER_SELECTED' => $arItem['OFFERS_SELECTED'],
					'TREE_PROPS' => $arSkuProps,
					'LAST_ELEMENT' => $arItem['LAST_ELEMENT']
				);
				?>
				
<!-- Создание объекта -->				
<script type="text/javascript">
var <? echo $strObName; ?> = new JCCatalogSection(<? echo CUtil::PhpToJSObject($arJSParams, false, true); ?>);
</script>
				<?
			}	//Конец if !empty OFFERS_PROP
		}	//Конец if PRODUCT_DISPLAY_MODE (Расширенная схема показа)
?>