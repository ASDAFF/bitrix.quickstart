<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

?><div class="orderdetail clearfix"><?

	if(strlen($arResult['ERROR_MESSAGE']))
	{
		?><?=ShowError($arResult['ERROR_MESSAGE']);?><?
	} else {
	
		?><div class="table"><?
			?><div class="full name"><?
				?><h2><?=GetMessage('SPOD_ORDER')?> <?=GetMessage('SPOD_NUM_SIGN')?><?=$arResult["ACCOUNT_NUMBER"]?> <?=GetMessage("SPOD_FROM")?> <?=$arResult["DATE_INSERT_FORMATED"]?></h2><?
			?></div><?
			
			?><div class="tr"><?
				?><div class="td"><?=GetMessage('SPOD_ORDER_STATUS')?>:</div><?
				?><div class="td"><?=$arResult['STATUS']['NAME']?> (<?=GetMessage('SPOD_FROM')?> <?=$arResult['DATE_STATUS_FORMATED']?>)</div><?
			?></div><?
			
			?><div class="tr"><?
				?><div class="td"><?=GetMessage('SPOD_ORDER_PRICE')?>:</div><?
				?><div class="td"><?
					?><?=$arResult['PRICE_FORMATED']?><?
					if(floatval($arResult['SUM_PAID']))
					{
						?>(<?=GetMessage('SPOD_ALREADY_PAID')?>:&nbsp;<?=$arResult['SUM_PAID_FORMATED']?>)<?
					}
				?></div><?
			?></div><?
			
			if($arResult['CANCELED']=='Y' || $arResult['CAN_CANCEL']=='Y')
			{
				?><div class="tr"><?
					?><div class="td"><?=GetMessage('SPOD_ORDER_CANCELED')?>:</div><?
					?><div class="td"><?
						if($arResult['CANCELED']=='Y')
						{
							?><?=GetMessage('SPOD_YES')?> (<?=GetMessage('SPOD_FROM')?> <?=$arResult['DATE_CANCELED_FORMATED']?>)<?
						} elseif($arResult['CAN_CANCEL']=='Y')
						{
							?><?=GetMessage('SPOD_NO')?>&nbsp;&nbsp;&nbsp;<a class="btn btn3" href="<?=$arResult['URL_TO_CANCEL']?>"><?=GetMessage('SPOD_ORDER_CANCEL')?></a><?
						}
					?></div><?
				?></div><?
			}
			
			if(IntVal($arResult['USER_ID']))
			{
				?><div class="full header"><?=GetMessage('SPOD_ACCOUNT_DATA')?></div><?
				if(strlen($arResult['USER_NAME']))
				{
					?><div class="tr"><?
						?><div class="td"><?=GetMessage('SPOD_ACCOUNT')?>:</div><?
						?><div class="td"><?=$arResult['USER_NAME']?></div><?
					?></div><?
				}
				
				?><div class="tr"><?
					?><div class="td"><?=GetMessage('SPOD_LOGIN')?>:</div><?
					?><div class="td"><?=$arResult['USER']['LOGIN']?></div><?
				?></div><?
				
				?><div class="tr"><?
					?><div class="td"><?=GetMessage('SPOD_EMAIL')?>:</div><?
					?><div class="td"><a href="mailto:<?=$arResult['USER']['EMAIL']?>"><?=$arResult['USER']['EMAIL']?></a></div><?
				?></div><?
			}
			
			?><div class="full header"><?=GetMessage('SPOD_ORDER_PROPERTIES')?></div><?
			
			?><div class="tr"><?
				?><div class="td"><?=GetMessage('SPOD_ORDER_PERS_TYPE')?>:</div><?
				?><div class="td"><?=$arResult['PERSON_TYPE']['NAME']?></div><?
			?></div><?
			
			foreach($arResult['ORDER_PROPS'] as $prop)
			{
				if($prop['SHOW_GROUP_NAME']=='Y')
				{
					?><div class="full header"><?=$prop['GROUP_NAME']?></div><?
				}
				?><div class="tr"><?
					?><div class="td"><?=$prop['NAME']?>:</div><?
					?><div class="td"><?
						if($prop['TYPE']=='CHECKBOX')
						{
							?><?=GetMessage('SPOD_'.($prop['VALUE']=='Y'?'YES':'NO'))?><?
						} else {
							?><?=$prop['VALUE']?><?
						}
					?></div><?
				?></div><?
			}
			
			if(!empty($arResult['USER_DESCRIPTION']))
			{
				?><div class="tr"><?
					?><div class="td"><?=GetMessage('SPOD_ORDER_USER_COMMENT')?>:</div><?
					?><div class="td"><?=$arResult['USER_DESCRIPTION']?></div><?
				?></div><?
			}
			
			?><div class="full header"><?=GetMessage('SPOD_ORDER_PAYMENT')?></div><?
			
			?><div class="tr"><?
				?><div class="td"><?=GetMessage('SPOD_PAY_SYSTEM')?>:</div><?
				?><div class="td"><?
					if(IntVal($arResult['PAY_SYSTEM_ID']))
					{
						?><?=$arResult['PAY_SYSTEM']['NAME']?><?
					} else {
						?><?=GetMessage('SPOD_NONE')?><?
					}
				?></div><?
			?></div><?
			
			?><div class="tr"><?
				?><div class="td"><?=GetMessage('SPOD_ORDER_PAYED')?>:</div><?
				?><div class="td"><?
					if($arResult['PAYED']=='Y')
					{
						?><?=GetMessage('SPOD_YES')?><?
						?>(<?=GetMessage('SPOD_FROM')?> <?=$arResult['DATE_PAYED_FORMATED']?>)<?
					} else {
						?><?=GetMessage('SPOD_NO')?><?
						if($arResult['CAN_REPAY']=='Y' && $arResult['PAY_SYSTEM']['PSA_NEW_WINDOW']=='Y')
						{
							?>&nbsp;&nbsp;&nbsp;<a class="btn btn3" href="<?=$arResult['PAY_SYSTEM']['PSA_ACTION_FILE']?>" target="_blank"><?=GetMessage('SPOD_REPEAT_PAY')?></a><?
						}
					}
				?></div><?
			?></div><?
			
			?><div class="tr"><?
				?><div class="td"><?=GetMessage('SPOD_ORDER_DELIVERY')?>:</div><?
				?><div class="td"><?
					if(strpos($arResult['DELIVERY_ID'],':') !== false || IntVal($arResult['DELIVERY_ID']))
					{
						?><?=$arResult['DELIVERY']['NAME']?><?
						if(IntVal($arResult['STORE_ID']) && !empty($arResult['DELIVERY']['STORE_LIST'][$arResult['STORE_ID']]))
						{
							$store = $arResult['DELIVERY']['STORE_LIST'][$arResult['STORE_ID']];
							?><div><?
								?><div><?
									?><?=GetMessage('SPOD_TAKE_FROM_STORE')?>: <b><?=$store['TITLE']?></b><?
									if(!empty($store['DESCRIPTION']))
									{
										?><div><?=$store['DESCRIPTION']?></div><?
									}
								?></div><?
								if(!empty($store['ADDRESS']))
								{
									?><div><b><?=GetMessage('SPOD_STORE_ADDRESS')?></b>: <?=$store['ADDRESS']?></div><?
								}
								if(!empty($store['SCHEDULE']))
								{
									?><div><b><?=GetMessage('SPOD_STORE_WORKTIME')?></b>: <?=$store['SCHEDULE']?></div><?
								}
								if(!empty($store['PHONE']))
								{
									?><div><b><?=GetMessage('SPOD_STORE_PHONE')?></b>: <?=$store['PHONE']?></div><?
								}
								if(!empty($store['EMAIL']))
								{
									?><div><b><?=GetMessage('SPOD_STORE_EMAIL')?></b>: <a href="mailto:<?=$store['EMAIL']?>"><?=$store['EMAIL']?></a></div><?
								}
								if(($store['GPS_N'] = floatval($store['GPS_N'])) && ($store['GPS_S'] = floatval($store['GPS_S'])))
								{
									?><div id="bx_old_s_map"><?
										?><div><?
											?><a href="javascript:void(0)" class="bx_big bx_bt_button_type_2 bx_cart" id="map-show"><?=GetMessage('SPOD_SHOW_MAP')?></a><?
											?><a href="javascript:void(0)" class="bx_big bx_bt_button_type_2 bx_cart" id="map-hide"><?=GetMessage('SPOD_HIDE_MAP')?></a><?
										?></div><?
										ob_start();
										?><div><?
											$mg = $arResult["DELIVERY"]["STORE_LIST"][$arResult['STORE_ID']]['IMAGE'];
											if(!empty($mg['SRC']))
											{
												?><img src="<?=$mg['SRC']?>" width="<?=$mg['WIDTH']?>" height="<?=$mg['HEIGHT']?>"><br /><br /><?
											}
											?><?=$store['TITLE']?><?
										?></div><?
										$ballon = ob_get_contents();
										ob_end_clean();
											$mapId = '__store_map';
											$mapParams = array(
											'yandex_lat' => $store['GPS_N'],
											'yandex_lon' => $store['GPS_S'],
											'yandex_scale' => 16,
											'PLACEMARKS' => array(
												array(
													'LON' => $store['GPS_S'],
													'LAT' => $store['GPS_N'],
													'TEXT' => $ballon
												)
											));
										?><div id="map-container"><?
											?><?$APPLICATION->IncludeComponent("bitrix:map.yandex.view", ".default", array(
												"INIT_MAP_TYPE" => "MAP",
												"MAP_DATA" => serialize($mapParams),
												"MAP_WIDTH" => "100%",
												"MAP_HEIGHT" => "200",
												"CONTROLS" => array(
													0 => "SMALLZOOM",
												),
												"OPTIONS" => array(
													0 => "ENABLE_SCROLL_ZOOM",
													1 => "ENABLE_DBLCLICK_ZOOM",
													2 => "ENABLE_DRAGGING",
												),
												"MAP_ID" => $mapId
												),
												false
											);?><?
										?></div><?
										CJSCore::Init();
										?><script>new CStoreMap({mapId:"<?=$mapId?>", area: '.bx_old_s_map'});</script><?
									?></div><?
								}
							?></div><?
						}
					} else {
						?><?=GetMessage('SPOD_NONE')?><?
					}
				?></div><?
			?></div><?
			
			if($arResult['TRACKING_NUMBER'])
			{
				?><div class="tr"><?
					?><div class="td"><?=GetMessage('SPOD_ORDER_TRACKING_NUMBER')?>:</div><?
					?><div class="td"><?=$arResult['TRACKING_NUMBER']?></div><?
				?></div><?
			}
			
			if($arResult['CAN_REPAY']=='Y' && $arResult['PAY_SYSTEM']['PSA_NEW_WINDOW']!='Y')
			{
				?><div class="full"><?
					$ORDER_ID = $ID;
					include($arResult["PAY_SYSTEM"]["PSA_ACTION_FILE"]);
				?></div><?
			}
			
			?><div class="full header"><?=GetMessage('SPOD_ORDER_BASKET')?></div><?
			
			?><div class="full prods"><?
				/////////////////////////////////////////// PRODUCTS ///////////////////////////////////////////
				?><table class="orderlistproducts"><?
					?><thead><?
						?><tr><?
							?><td colspan="2"><?=GetMessage('SPOD_NAME')?></td><?
							?><td class="custom price"><?=GetMessage('SPOD_PRICE')?></td><?
							if($arResult['HAS_PROPS'])
							{
								?><td class="custom amount"><?=GetMessage('SPOD_PROPS')?></td><?
							}
							if($arResult['HAS_DISCOUNT'])
							{
								?><td class="custom price"><?=GetMessage('SPOD_DISCOUNT')?></td><?
							}
							?><td class="custom amount"><?=GetMessage('SPOD_PRICETYPE')?></td><?
							?><td class="custom price"><?=GetMessage('SPOD_QUANTITY')?></td><?
						?></tr><?
					?></thead><?
					?><tbody><?
						foreach($arResult['BASKET'] as $prod)
						{
							?><tr><?
								$hasLink = !empty($prod['DETAIL_PAGE_URL']);
								?><td class="custom img"><?
									if($hasLink)
									{
										?><a href="<?=$prod['DETAIL_PAGE_URL']?>" target="_blank"><?
									}
									if($prod['PICTURE']['SRC'] && $prod['PICTURE']['SRC']!='')
									{
										?><img src="<?=$prod['PICTURE']['SRC']?>" width="<?=$prod['PICTURE']['WIDTH']?>" height="<?=$prod['PICTURE']['HEIGHT']?>" alt="<?=$prod['NAME']?>" /><?
									} else {
										?><img src="<?=$prod['PICTURE']['SRC']?>" width="<?=$prod['PICTURE']['WIDTH']?>" height="<?=$prod['PICTURE']['HEIGHT']?>" alt="<?=$prod['NAME']?>" /><?
									}
									if($hasLink)
									{
										?></a><?
									}
								?></td><?
								?><td class="custom name"><?
									if($hasLink)
									{
										?><a href="<?=$prod['DETAIL_PAGE_URL']?>" target="_blank"><?
									}
									?><?=htmlspecialcharsEx($prod['NAME'])?><?
									if($hasLink)
									{
										?></a><?
									}
								?></td><?
								?><td class="custom price nowrap"><?=$prod['PRICE_FORMATED']?></td><?
								if ($arResult['HAS_PROPS']) {
									?><td><?
										foreach($prod['PROPS'] as $arProp){
											?><div><?echo $arProp['NAME'].':&nbsp'.$arProp['VALUE']?></div><?
										}
									?></td><?
								}
								if($arResult['HAS_DISCOUNT'])
								{
									?><td class="custom price nowrap"><?=$prod["DISCOUNT_PRICE_PERCENT_FORMATED"]?></td><?
								}
								?><td class="custom amount nowrap"><?=htmlspecialcharsEx($prod["NOTES"])?></td><?
								?><td class="custom price nowrap"><?
									?><?=$prod["QUANTITY"]?> <?
									if(strlen($prod['MEASURE_TEXT']))
									{
										?><?=$prod['MEASURE_TEXT']?><?
									} else {
										?><?=GetMessage('SPOD_DEFAULT_MEASURE')?><?
									}
								?></td><?
							?></tr><?
						}
					?></tbody><?
				?></table><?
				/////////////////////////////////////////// /PRODUCTS ///////////////////////////////////////////
			?></div><?
			
			///// WEIGHT
			if(floatval($arResult['ORDER_WEIGHT']))
			{
				?><div class="tr"><?
					?><div class="td"><?=GetMessage('SPOD_TOTAL_WEIGHT')?>:</div><?
					?><div class="td"><?=$arResult['ORDER_WEIGHT_FORMATED']?></div><?
				?></div><?
			}
			
			///// PRICE SUM
			?><div class="tr"><?
				?><div class="td"><?=GetMessage('SPOD_PRODUCT_SUM')?>:</div><?
				?><div class="td"><?=$arResult['PRODUCT_SUM_FORMATTED']?></div><?
			?></div><?
			
			///// DELIVERY PRICE: print even equals 2 zero
			if(strlen($arResult['PRICE_DELIVERY_FORMATED']))
			{
				?><div class="tr"><?
					?><div class="td"><?=GetMessage('SPOD_DELIVERY')?>:</div><?
					?><div class="td"><?=$arResult['PRICE_DELIVERY_FORMATED']?></div><?
				?></div><?
			}
			
			///// TAXES DETAIL
			foreach($arResult['TAX_LIST'] as $tax)
			{
				?><div class="tr"><?
					?><div class="td"><?=$tax['TAX_NAME']?>:</div><?
					?><div class="td"><?=$tax['VALUE_MONEY_FORMATED']?></div><?
				?></div><?
			}
			
			///// TAX SUM
			if(floatval($arResult['TAX_VALUE']))
			{
				?><div class="tr"><?
					?><div class="td"><?=GetMessage('SPOD_TAX')?>:</div><?
					?><div class="td"><?=$arResult['TAX_VALUE_FORMATED']?></div><?
				?></div><?
			}
			
			///// DISCOUNT
			if(floatval($arResult['DISCOUNT_VALUE']))
			{
				?><div class="tr"><?
					?><div class="td"><?=GetMessage('SPOD_DISCOUNT')?>:</div><?
					?><div class="td"><?=$arResult['DISCOUNT_VALUE_FORMATED']?></div><?
				?></div><?
			}
			
			?><div class="tr"><?
				?><div class="td"><?=GetMessage('SPOD_SUMMARY')?>:</div><?
				?><div class="td"><?=$arResult['PRICE_FORMATED']?></div><?
			?></div><?
			
		?></div><?
	}

?></div><?

?><br /><br /><a class="fullback" href="<?=$arResult['URL_TO_LIST']?>"><i class="icon pngicons"></i><?=GetMessage('SPOD_CUR_ORDERS')?></a>