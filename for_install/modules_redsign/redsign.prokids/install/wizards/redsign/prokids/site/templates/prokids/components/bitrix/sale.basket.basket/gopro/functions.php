<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!function_exists("ShowTable"))
{
	function ShowTable($arParams,$arResult,$mode='')
	{
		global $APPLICATION;
		$arUrls = Array(
			'delete' => $APPLICATION->GetCurPage().'?'.$arParams['ACTION_VARIABLE'].'=delete&id=#ID#',
			'delay' => $APPLICATION->GetCurPage().'?'.$arParams['ACTION_VARIABLE'].'=delay&id=#ID#',
			'add' => $APPLICATION->GetCurPage().'?'.$arParams['ACTION_VARIABLE'].'=add&id=#ID#',
		);
		$arShowColumns = ShowColumns($arParams,$arResult,$mode);
//echo"<pre>";print_r($arShowColumns);echo"</pre>";
		?><div class="artable"><?
			?><table class="items"><?
				?><thead><?
					?><tr><?
						?><th class="xxx"><div class="xxx"><?=GetMessage('SALE_COLUMN_PRODUCTS')?></div></th><? // checkox, imagem, name
						if($arShowColumns['article']>0)
						{
							?><th class="nowrap"><?=$arResult['GRID']['HEADERS'][$arShowColumns['article']]['name']?></th><? // article
						}
						if($arShowColumns['discount']>0)
						{
							?><th class="nowrap"><?=GetMessage('SALE_DISCOUNT')?></th><?
						}
						if($arShowColumns['pricetype']>0)
						{
							?><th class="nowrap"><?=GetMessage('SALE_TYPE')?></th><?
						}
						if($arShowColumns['price']>0)
						{
							?><th class="nowrap"><?=GetMessage('SALE_PRICE')?></th><?
						}
						if($arShowColumns['quantity']>0)
						{
							?><th class="nowrap"><?=GetMessage('SALE_QUANTITY')?></th><?
						}
						if($arShowColumns['sum']>0)
						{
							?><th class="nowrap"><?=GetMessage('SALE_SUM')?></th><?
						}
						if($arShowColumns['delete']>0)
						{
							?><th class="nowrap"><?=GetMessage('SALE_DELETE')?></th><?
						}
						if($arShowColumns['delay']>0)
						{
							if($mode=='')
							{
								?><th class="nowrap"><?=GetMessage('SALE_DELAY')?></th><?
							} elseif( $mode=='delayed')
							{
								?><th class="nowrap"><?=GetMessage('SALE_ADD_TO_BASKET')?></td><?
							}
						}
						if($mode=='' || $mode=='delayed')
						{
							foreach($arResult['GRID']['HEADERS'] as $id => $arHeader)
							{
								if(
									in_array($arHeader['id'], array('NAME','DISCOUNT','PROPS','DELETE','DELAY','TYPE','PRICE','QUANTITY','WEIGHT','SUM')) ||
									$arHeader['id']=='PROPERTY_'.$arParams['PROP_ARTICLE'].'_VALUE' ||
									$arHeader['id']=='PROPERTY_'.$arParams['PROP_SKU_ARTICLE'].'_VALUE'
								) {
									continue;
								}
								?><th class="nowrap"><?=$arHeader['name']?></th><?
							}
						}
					?></tr><?
				?></thead><?
				
				?><tbody><?
					foreach($arResult['GRID']['ROWS'] as $arItem)
					{
						if(
							($mode=='' && $arItem['DELAY']=='N' && $arItem['CAN_BUY']=='Y') ||
							($mode=='delayed' && $arItem['DELAY']=='Y' && $arItem['CAN_BUY']=='Y') ||
							($mode=='notavailable' && isset($arItem['NOT_AVAILABLE']) && $arItem['NOT_AVAILABLE']==true)
						)
						{
							
							// some values are not shown in the columns in this template
							?><tr class="js-element"><?
								?><td class="xxx"><?
									?><div class="xxx"><?
										?><table class="prod"><?
											?><tr><?
												?><td class="checkbox"><?
													?><div><?
														// checkbox
														?><input type="checkbox" name="DELETE_<?=$arItem['ID']?>" id="DELETE_<?=$arItem['ID']?>" value="Y"><label for="DELETE_<?=$arItem['ID']?>"></label><?
													?></div><?
												?></td><?
												?><td class="image"><?
													?><div><?
													// image
														if(strlen($arItem['PREVIEW_PICTURE_SRC'])>0)
														{
															$url = $arItem['PREVIEW_PICTURE_SRC'];
														} elseif(strlen($arItem['DETAIL_PICTURE_SRC'])>0) {
															$url = $arItem['DETAIL_PICTURE_SRC'];
														} else {
															$url = $arResult['NO_PHOTO']['src'];
														}
														if(strlen($arItem['DETAIL_PAGE_URL'])>0)
														{
															?><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?
														}
														?><img src="<?=$url?>" alt="" /><?
														if(strlen($arItem['DETAIL_PAGE_URL'])>0)
														{
															?></a><?
														}
													?></div><?
												?></td><?
												?><td><?
													// name
													if(strlen($arItem['DETAIL_PAGE_URL'])>0)
													{
														?><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?
													}
													?><?=$arItem['NAME']?><?
													if(strlen($arItem['DETAIL_PAGE_URL'])>0)
													{
														?></a><?
													}
												?></td><?
											?></tr><?
										?></table><?
									?></div><?
								?></td><?
								
								if($arShowColumns['article']>0)
								{
									?><td class="tc"><?
										?><span class="lppadding"><?
											if( isset($arItem['PROPERTY_'.$arParams['PROP_ARTICLE'].'_VALUE']) )
											{
												?><?=$arItem['PROPERTY_'.$arParams['PROP_ARTICLE'].'_VALUE']?><?
											} elseif( isset($arItem['PROPERTY_'.$arParams['PROP_SKU_ARTICLE'].'_VALUE']) )
											{
												?><?=$arItem['PROPERTY_'.$arParams['PROP_SKU_ARTICLE'].'_VALUE']?><?
											}
										?></span><?
									?></td><?
								}
								
								if($arShowColumns['discount']>0)
								{
									?><td class="tc"><?
										?><span class="lppadding"><?
											if(floatval($arItem['DISCOUNT_PRICE_PERCENT'])>0)
											{
												?><?=$arItem['DISCOUNT_PRICE_PERCENT_FORMATED']?><?
											}
										?></span><?
									?></td><?
								}
								
								if($arShowColumns['pricetype']>0)
								{
									?><td class="tc"><?
										?><span class="lppadding"><?
											?><?=$arItem['NOTES']?><?
										?></span><?
									?></td><?
								}
								
								if($arShowColumns['price']>0)
								{
									?><td class="tc"><?
										?><span class="lppadding nowrap"><?
											?><?=$arItem['PRICE_FORMATED']?><?
										?></span><?
									?></td><?
								}
								
								if($arShowColumns['quantity']>0 && $mode=='')
								{
									?><td class="tc"><?
										?><span class="quantity nowrap"><?
											?><a class="minus js-minus">-</a><?
											?><input type="text" class="js-quantity" name="QUANTITY_<?=$arItem['ID']?>" value="<?=$arItem['QUANTITY']?>" data-ratio="<? if (!empty($arItem['MEASURE_RATIO']) && isset($arItem['MEASURE_RATIO'])) { echo $arItem['MEASURE_RATIO']; } else { echo "1"; } ?>" data-avaliable="<?=$arItem['AVAILABLE_QUANTITY']?>"><?
											?><span class="js-measurename"><?=$arItem['MEASURE_TEXT']?></span><?
											?><a class="plus js-plus">+</a><?
										?></span><?
									?></td><?
								} else {
									?><td class="tc"><?
										?><?=$arItem['QUANTITY']?><?
										?><span class="js-measurename"><?=$arItem['MEASURE_TEXT']?></span><?
									?></td><?
								}
								
								if($arShowColumns['sum']>0)
								{
									?><td class="tc"><?
										?><span class="lppadding nowrap"><?
											?><?=$arItem['SUM']?><?
										?></span><?
									?></td><?
								}
								
								if($arShowColumns['delete']>0)
								{
									?><td class="tc"><?
										?><a class="delete" href="<?=str_replace("#ID#", $arItem["ID"], $arUrls["delete"])?>" title="<?=GetMessage('SALE_DELETE')?>"><i class="icon pngicons"></i></a><?
									?></td><?
								}
								
								if($arShowColumns['delay']>0)
								{
									?><td class="tc"><?
										if($mode=='')
										{
											?><a class="delay" href="<?=str_replace("#ID#", $arItem["ID"], $arUrls["delay"])?>" title="<?=GetMessage('SALE_DELAY')?>"><i class="icon pngicons"></i></a><?
										} elseif( $mode=='delayed')
										{
											?><a class="add" href="<?=str_replace("#ID#", $arItem["ID"], $arUrls["add"])?>" title="<?=GetMessage('SALE_ADD_TO_BASKET')?>"><i class="icon pngicons"></i></a><?
										}
									?></td><?
								}
								
								if($mode=='' || $mode=='delayed')
								{
									foreach($arResult['GRID']['HEADERS'] as $id => $arHeader)
									{
										if(
											in_array($arHeader['id'], array('NAME','DISCOUNT','PROPS','DELETE','DELAY','TYPE','PRICE','QUANTITY','WEIGHT','SUM')) ||
											$arHeader['id']=='PROPERTY_'.$arParams['PROP_ARTICLE'].'_VALUE' ||
											$arHeader['id']=='PROPERTY_'.$arParams['PROP_SKU_ARTICLE'].'_VALUE'
										) {
											continue;
										}
										?><td data-id="<?=$arHeader["id"]?>"><?=$arItem[$arHeader["id"]]?></td><?
									}
								}
								
							?></tr><?
							
						}
					}
				?></tbody><?
			?></table><?
			
		?></div><?
	}
}

if(!function_exists("ShowColumns"))
{
	function ShowColumns($arParams,$arResult,$mode='')
	{
		$arReturn = array(
			'article' => 0,
			'discount' => 0,
			'pricetype' => 0,
			'price' => 0,
			'quantity' => 0,
			'sum' => 0,
			'delete' => 0,
			'delay' => 0,
		);
		foreach($arResult['GRID']['HEADERS'] as $id => $arHeader)
		{
			// article
			if(
				(isset($arParams['PROP_ARTICLE']) || isset($arParams['PROP_SKU_ARTICLE'])) &&
				($arHeader['id']=='PROPERTY_'.$arParams['PROP_ARTICLE'].'_VALUE' || $arHeader['id']=='PROPERTY_'.$arParams['PROP_SKU_ARTICLE'].'_VALUE')
			) {
				$arReturn['article'] = $id;
			}
			// discount
			if( $arHeader['id']=='DISCOUNT' && $arResult['DISCOUNT_PRICE_ALL']>0 && ($mode=='' || $mode=='delayed') )
			{
				$arReturn['discount'] = $id;
			}
			// pricetype
			if( $arHeader['id']=='TYPE' )
			{
				$arReturn['pricetype'] = $id;
			}
			// price
			if( $arHeader['id']=='PRICE' )
			{
				$arReturn['price'] = $id;
			}
			// quantity
			if( $arHeader['id']=='QUANTITY' )
			{
				$arReturn['quantity'] = $id;
			}
			// sum
			if( $arHeader['id']=='SUM' && $mode=='' )
			{
				$arReturn['sum'] = $id;
			}
			// delete
			if( $arHeader['id']=='DELETE' && ($mode=='' || $mode=='delayed') )
			{
				$arReturn['delete'] = $id;
			}
			// delay
			if( $arHeader['id']=='DELAY' && ($mode=='' || $mode=='delayed') )
			{
				$arReturn['delay'] = $id;
			}
		}
		return $arReturn;
	}
}