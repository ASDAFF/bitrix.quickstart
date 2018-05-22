<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$bDefaultColumns = $arResult['GRID']['DEFAULT_COLUMNS'];
$colspan = ($bDefaultColumns) ? count($arResult['GRID']['HEADERS']) : count($arResult['GRID']['HEADERS']) - 1;
$bPropsColumn = false;
$bUseDiscount = false;
$bPriceType = false;
$bShowNameWithPicture = ($bDefaultColumns) ? true : false; // flat to show name and picture column in one column


?><div class="section summary"><?
	?><h4><?=GetMessage('SALE_PRODUCTS_SUMMARY');?></h4><?
	?><div class="body"><?
	
		?><table class="products"><?
			?><thead><?
				?><tr><?
					$bPreviewPicture = false;
					$bDetailPicture = false;
					$imgCount = 0;
					// prelimenary column handling
					foreach($arResult['GRID']['HEADERS'] as $id => $arColumn)
					{
						if($arColumn['id']=='PROPS')
						{
							$bPropsColumn = true;
						}
						if($arColumn['id']=='NOTES')
						{
							$bPriceType = true;
						}
						if($arColumn['id']=='PREVIEW_PICTURE')
						{
							$bPreviewPicture = true;
						}
						if($arColumn['id']=='DETAIL_PICTURE')
						{
							$bDetailPicture = true;
						}
					}
					if($bPreviewPicture || $bDetailPicture)
					{
						$bShowNameWithPicture = true;
					}

					foreach($arResult['GRID']['HEADERS'] as $id => $arColumn)
					{
						if(in_array($arColumn['id'], array('PROPS','TYPE','NOTES'))) // some values are not shown in columns in this template
							continue;

						if($arColumn['id']=='PREVIEW_PICTURE' && $bShowNameWithPicture)
							continue;

						if($arColumn['id']=='NAME' && $bShowNameWithPicture)
						{
							?><td class="item" colspan="2"><?=GetMessage('SALE_PRODUCTS')?><?
						} elseif ($arColumn["id"] == "NAME" && !$bShowNameWithPicture)
						{
							?><td class="item"><?=$arColumn["name"]?><?
						} elseif ($arColumn["id"] == "PRICE")
						{
							?><td class="price"><?=$arColumn["name"]?><?
						} else {
							?><td class="custom"><?=$arColumn["name"]?><?
						}
						?></td><?
					}
				?></tr><?
			?></thead><?

			?><tbody><?
				foreach($arResult['GRID']['ROWS'] as $k => $arData)
				{
					?><tr><?
						if($bShowNameWithPicture)
						{
							?><td><?
								?><div class="img"><?
								if(strlen($arData['data']['PREVIEW_PICTURE_SRC'])>0)
								{
									$url = $arData['data']['PREVIEW_PICTURE_SRC'];
								} elseif (strlen($arData['data']['DETAIL_PICTURE_SRC'])>0)
								{
									$url = $arData['data']['DETAIL_PICTURE_SRC'];
								} else {
									$url = $arResult['NO_PHOTO']['src'];
								}
								if(strlen($arData['data']['DETAIL_PAGE_URL'])>0)
								{
									?><a href="<?=$arData['data']['DETAIL_PAGE_URL'] ?>"><?
								}
								?><img src="<?=$url?>" alt="" title="" /><?
								if(strlen($arData['data']['DETAIL_PAGE_URL'])>0)
								{
									?></a><?
								}
								?></div><?
							?></td><?
						}
						// prelimenary check for images to count column width
						foreach($arResult['GRID']['HEADERS'] as $id => $arColumn)
						{
							$arItem = (isset($arData['columns'][$arColumn['id']])) ? $arData['columns'] : $arData['data'];
							if(is_array($arItem[$arColumn['id']]))
							{
								foreach($arItem[$arColumn['id']] as $arValues)
								{
									if($arValues['type']=='image')
										$imgCount++;
								}
							}
						}

						foreach($arResult['GRID']['HEADERS'] as $id => $arColumn)
						{
							$class = ($arColumn['id']=='PRICE_FORMATED')?'price':'';
							if(in_array($arColumn['id'], array('PROPS','TYPE','NOTES'))) // some values are not shown in columns in this template
								continue;

							if($arColumn['id']=='PREVIEW_PICTURE' && $bShowNameWithPicture)
								continue;

							$arItem = (isset($arData['columns'][$arColumn['id']]))?$arData['columns']:$arData['data'];

							if($arColumn['id']=='NAME')
							{
								?><td class="item"><?
									?><span class="bx_ordercart_itemtitle"><?
										if(strlen($arItem['DETAIL_PAGE_URL'])>0)
										{
											?><a href="<?=$arItem['DETAIL_PAGE_URL'] ?>"><?
										}
										?><?=$arItem['NAME']?><?
										if(strlen($arItem['DETAIL_PAGE_URL'])>0)
										{
											?></a><?
										}
									?></span><?
									?><div class="bx_ordercart_itemart"><?
										if($bPropsColumn)
										{
											foreach($arItem['PROPS'] as $val)
											{
												?><?=$val['NAME']?>:&nbsp;<span><?=$val['VALUE']?><span><br/><?
											}
										}
									?></div><?
								?></td><?
							} elseif ($arColumn['id']=='PRICE_FORMATED')
							{
								?><td class="price right"><?
									?><div class="current_price"><?=$arItem['PRICE_FORMATED']?> </div><?
									?><div class="old_price right"><?
										if(doubleval($arItem['DISCOUNT_PRICE'])>0)
										{
											?><?=SaleFormatCurrency($arItem['PRICE'] + $arItem['DISCOUNT_PRICE'], $arItem['CURRENCY'])?><?
											$bUseDiscount = true;
										}
									?></div><?
									if($bPriceType && strlen($arItem['NOTES'])>0)
									{
										?><div style="text-align: left"><?
											?><div class="type_price"><?=GetMessage('SALE_TYPE')?></div><?
											?><div class="type_price_value"><?=$arItem['NOTES']?></div><?
										?></div><?
									}
								?></td><?
							} elseif ($arColumn['id']=='DISCOUNT')
							{
								?><td class="custom right"><?
									?><?=$arItem['DISCOUNT_PRICE_PERCENT_FORMATED']?><?
								?></td><?
							} elseif ($arColumn['id']=='DETAIL_PICTURE' && $bPreviewPicture)
							{
								?><td><?
									?><div class="img"><?
										$url = '';
										if($arColumn['id']=='DETAIL_PICTURE' && strlen($arData['data']['DETAIL_PICTURE_SRC'])>0)
										{
											$url = $arData['data']['DETAIL_PICTURE_SRC'];
										}
										if($url=='')
										{
											$url = $templateFolder.'/images/no_photo.png';
										}
										if(strlen($arData['data']['DETAIL_PAGE_URL'])>0)
										{
											?><a href="<?=$arData['data']['DETAIL_PAGE_URL']?>"><?
										}
										?><img src="<?=$url?>" alt="" title="" /><?
										if(strlen($arData['data']['DETAIL_PAGE_URL'])>0)
										{
											?></a><?
										}
									?></div><?
								?></td><?
							} elseif (in_array($arColumn['id'], array('QUANTITY','WEIGHT_FORMATED','DISCOUNT_PRICE_PERCENT_FORMATED','SUM')))
							{
								?><td class="custom right"><?
									?><?=$arItem[$arColumn['id']]?><?
								?></td><?
							} else { // some property value
								if(is_array($arItem[$arColumn['id']]))
								{
									foreach($arItem[$arColumn['id']] as $arValues)
									{
										if($arValues['type']=='image')
										{
											$columnStyle = 'width:20%';
										}
									}
									?><td class="custom" style="<?=$columnStyle?>"><?
										foreach($arItem[$arColumn['id']] as $arValues)
										{
											if($arValues['type']=='image')
											{
												?><div class="img"><?
													?><img src="<?=$arValues['value']?>" alt="" title="" /><?
												?></div><?
											} else { // not image
												?><?=$arValues['value']?><br/><?
											}
										}
									?></td><?
								} else { // not array, but simple value
									?><td class="custom" style="<?=$columnStyle?>"><?
										?><?=$arItem[$arColumn['id']];
									?></td><?
								}
							}
						}
					?></tr><?
				}
			?></tbody><?
		?></table><?
		
		?><div class="order_pay"><?
			?><div class="right"><?
				?><table class="bx_ordercart_order_sum"><?
					?><tbody><?
						if( doubleval($arResult['ORDER_WEIGHT'])>0 )
						{
							?><tr><?
								?><td class="custom_t1" colspan="<?=$colspan?>" class="itog"><?=GetMessage('SOA_TEMPL_SUM_WEIGHT_SUM')?></td><?
								?><td class="custom_t2 price"><?=$arResult['ORDER_WEIGHT_FORMATED']?></td><?
							?></tr><?
						}
						if( $arResult['ORDER_PRICE_FORMATED']!=$arResult['ORDER_TOTAL_PRICE_FORMATED'] )
						{
							?><tr><?
								?><td class="custom_t1" colspan="<?=$colspan?>" class="itog"><?=GetMessage('SOA_TEMPL_SUM_SUMMARY')?></td><?
								?><td class="custom_t2 price"><?=$arResult['ORDER_PRICE_FORMATED']?></td><?
							?></tr><?
						}
						if(doubleval($arResult['DISCOUNT_PRICE'])>0)
						{
							?><tr><?
								?><td class="custom_t1" colspan="<?=$colspan?>" class="itog"><?=GetMessage('SOA_TEMPL_SUM_DISCOUNT')?><?if(strLen($arResult['DISCOUNT_PERCENT_FORMATED'])>0):?> (<?=$arResult['DISCOUNT_PERCENT_FORMATED'];?>)<?endif;?>:</td><?
								?><td class="custom_t2 price"><?=$arResult['DISCOUNT_PRICE_FORMATED']?></td><?
							?></tr><?
						}
						if(!empty($arResult['TAX_LIST']))
						{
							foreach($arResult['TAX_LIST'] as $val)
							{
								?><tr><?
									?><td class="custom_t1" colspan="<?=$colspan?>" class="itog"><?=$val['NAME']?> <?=$val['VALUE_FORMATED']?>:</td><?
									?><td class="custom_t2 price"><?=$val['VALUE_MONEY_FORMATED']?></td><?
								?></tr><?
							}
						}
						if(doubleval($arResult["DELIVERY_PRICE"])>0)
						{
							?><tr><?
								?><td class="custom_t1" colspan="<?=$colspan?>" class="itog"><?=GetMessage('SOA_TEMPL_SUM_DELIVERY')?></td><?
								?><td class="custom_t2 price"><?=$arResult['DELIVERY_PRICE_FORMATED']?></td><?
							?></tr><?
						}
						if(strlen($arResult['PAYED_FROM_ACCOUNT_FORMATED'])>0)
						{
							?><tr><?
								?><td class="custom_t1" colspan="<?=$colspan?>" class="itog"><?=GetMessage('SOA_TEMPL_SUM_PAYED')?></td><?
								?><td class="custom_t2 price"><?=$arResult['PAYED_FROM_ACCOUNT_FORMATED']?></td><?
							?></tr><?
						}
						if($bUseDiscount)
						{
							?><tr><?
								?><td class="custom_t1 fwb" colspan="<?=$colspan?>" class="itog"><?=GetMessage('SOA_TEMPL_SUM_IT')?></td><?
								?><td class="custom_t2 fwb price"><?=$arResult['ORDER_TOTAL_PRICE_FORMATED']?></td><?
							?></tr><?
							?><tr><?
								?><td class="custom_t1" colspan="<?=$colspan?>"></td><?
								?><td class="custom_t2" style="text-decoration:line-through; color:#828282;"><?=$arResult['PRICE_WITHOUT_DISCOUNT']?></td><?
							?></tr><?
						} else {
							?><tr><?
								?><td class="custom_t1 fwb" colspan="<?=$colspan?>" class="itog"><?=GetMessage('SOA_TEMPL_SUM_IT')?></td><?
								?><td class="custom_t2 fwb price"><?=$arResult['ORDER_TOTAL_PRICE_FORMATED']?></td><?
							?></tr><?
						}
					?></tbody><?
				?></table><?
			?></div><?
		?></div><?
		
	?></div><?
?></div><?

?><div class="section"><?
	?><h4><?=GetMessage('SOA_TEMPL_SUM_COMMENTS')?></h4><?
	?><div class="body"><?
		?><textarea name="ORDER_DESCRIPTION" id="ORDER_DESCRIPTION" style="max-width:100%;min-height:120px"><?=$arResult['USER_VALS']['ORDER_DESCRIPTION']?></textarea><?
	?></div><?
	?><input type="hidden" name="" value="" /><?
?></div><?
?><p><div>&nbsp;</div></p>