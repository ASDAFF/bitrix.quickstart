<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if( $arParams['RS_TAB_IDENT']=='rsec_thistab_viewed' )
{
	$tabJSIdent = 'rsec_viewed';
	$ident = 'viewed';
} elseif( $arParams['RS_TAB_IDENT']=='rsec_thistab_compare' )
{
	$tabJSIdent = 'rsec_compare';
	$ident = 'compare';
} elseif( $arParams['RS_TAB_IDENT']=='rsec_thistab_favorite' )
{
	$tabJSIdent = 'rsec_favorite';
	$ident = 'favorite';
}

?><div class="<?=$arParams['RS_TAB_IDENT']?>" data-ident="<?=$ident?>"><?
	?><form><?
		?><input type="hidden" name="<?=$arParams['RS_SECONDARY_ACTION_VARIABLE']?>" id="rsec_indent" value="<?=$arParams['ACTION_VAL']?>" /><?
		if( is_array($arResult['ITEMS']) && count($arResult['ITEMS'])>0 )
		{
			?><table class="rsec_table"><?
				?><thead><?
					?><tr><?
						?><th class="rsec_hov"></th><?
						?><th colspan="<?if($ident!='viewed'):?>3<?else:?>2<?endif;?>"><?=GetMessage('TABLE_COLUMN_NAME_PRODUCT')?></th><?
						?><th class="rsec_cen"><?=GetMessage('TABLE_COLUMN_NAME_PRICE')?></th><?
						/*?><th class="rsec_cen"><?=GetMessage('TABLE_COLUMN_NAME_ADD_TO_CART')?></th><?*/
						if($ident!='viewed')
						{
							?><th class="rsec_cen"><?=GetMessage('TABLE_COLUMN_NAME_DELETE')?></th><?
						}
					?></tr><?
				?></thead><?
				?><tbody><?
					foreach($arResult['ITEMS'] as $arItem)
					{
						$HAVE_OFFERS = (is_array($arItem['OFFERS']) && count($arItem['OFFERS'])>0) ? true : false;
						if($HAVE_OFFERS) { $PRODUCT = &$arItem['OFFERS'][0]; } else { $PRODUCT = &$arItem; }
						?><tr class="rsec_jsline" data-id="<?=$arItem['ID']?>"><?
							?><td class="rsec_hov"></td><?
							if($ident!='viewed')
							{
								?><td class="rsec_min rsec_cen"><?
									// checkbox
									?><input type="checkbox" name="DELETE_<?=$arItem['ID']?>" id="DELETE_<?=$arItem['ID']?>" value="Y"><label class="rsec_checkbox" for="DELETE_<?=$arItem['ID']?>"></label><?
								?></td><?
							}
							?><td class="rsec_image rsec_cen rsec_min"><?
								if(strlen($arItem['PREVIEW_PICTURE']['RESIZE']['src'])>0)
								{
									?><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><img src="<?=$arItem['PREVIEW_PICTURE']['RESIZE']['src']?>" <?
										?>alt="<?=$arItem['PREVIEW_PICTURE']['ALT']?>" <?
										?>title="<?=$arItem['PREVIEW_PICTURE']['TITLE']?>" <?
										?>/></a><?
								}
							?></td><?
							?><td><?
								?><a href="<?=$arItem['DETAIL_PAGE_URL']?>"><?=$arItem['NAME']?></a><?
							?></td><?
							?><td class="rsec_nowrap rsec_min rsec_padd"><?
								?><?=$PRODUCT['MIN_PRICE']['PRINT_DISCOUNT_VALUE']?><?
							?></td><?
							/*
							?><td class="rsec_nowrap rsec_min rsec_padd"><?
								?><noindex><?
									?><form class="rsec_add2basketform<?=$arItem['ID']?><?if(!$PRODUCT['CAN_BUY']>0):?> rsec_cantbuy<?endif;?>" name="add2basketform"><?
										?><input type="hidden" name="<?=$arParams['ACTION_VARIABLE']?>" value="ADD2BASKET" /><?
										if($arParams['USE_PRODUCT_QUANTITY'])
										{
											?><span class="rsec_quantity"><?
												?><a class="rsec_minus">-</a><?
												?><input type="text" class="rsec_quantity" name="<?=$arParams['PRODUCT_QUANTITY_VARIABLE']?>" value="<?=$PRODUCT['CATALOG_MEASURE_RATIO']?>" data-ratio="<?=$PRODUCT['CATALOG_MEASURE_RATIO']?>"><?
												?><span class="rsec_measurename"><?=$PRODUCT['CATALOG_MEASURE_NAME']?></span><?
												?><a class="rsec_plus">+</a><?
											?></span><?
										}
										?><input type="hidden" name="<?=$arParams['PRODUCT_ID_VARIABLE']?>" class="rsec_pid" value="<?=$PRODUCT['ID']?>" /><?
										?><a rel="nofollow" href="#" title="<?=GetMessage("ADD2BASKET")?>"><i class="rsec_add rsec_iconka"></i></a><?
										?><i class="rsec_tick rsec_iconka"></i><?
									?></form><?
								?></noindex><?
							?></td><?
							*/
							if($ident!='viewed')
							{
								?><td class="rsec_min rsec_cen rsec_padd"><?
									?><a class="rsec_delete" href="#"><i class="rsec_iconka"></i></a><?
								?></td><?
							}
						?></tr><?
					}
				?></tbody><?
			?></table><?
			?><div class="rsec_buttons rsec_clearfix"><?
				if($ident!='viewed')
				{
					?><div class="rsec_leftp"><?
						?><input class="rsec_btn rsec_btn2 rsec_delall" type="button" name="Refresh" value="<?=GetMessage('BTN_DEL_ALL')?>" /><?
						?><input class="rsec_btn rsec_btn2 rsec_delsome" type="button" name="Refresh" value="<?=GetMessage('DELETE')?>" /><?
					?></div><?
				}
				if($ident=='compare' && $arParams['COMPARE_RESULT_PATH']!='')
				{
					?><div class="rsec_rightp"><?
						?><input class="rsec_btn rsec_btn1 rsec_delsome" type="button" name="Refresh" value="<?=GetMessage('TAB_NAME_GO_COMPARE')?>" onclick="location.href='<?=$arParams['COMPARE_RESULT_PATH']?>';return false;" /><?
					?></div><?
				}
			?></div><?
			
			$this->SetViewTarget($arParams['RS_TAB_IDENT']);
				?><div class="rsec_orlink"><a class="<?=$tabJSIdent?> rsec_changer" href="#<?=$tabJSIdent?>"><i class="rsec_iconka"></i><span class="rsec_name"><?=GetMessage('TAB_NAME_'.$arParams['RS_TAB_IDENT'])?>&nbsp;</span><span class="rsec_color rsec_cnt"><?=count($arResult['ITEMS'])?></span></a></div><?
			$this->EndViewTarget();
		} else {
			?><div class="rsec_emptytab rsec_clearfix"><?
				?><div class="rsec_emptytab_icon"><?=GetMessage('NO_ITEMS_'.$arParams['RS_TAB_IDENT'])?></div><?
			?></div><?
			
			$this->SetViewTarget($arParams['RS_TAB_IDENT']);
				?><div class="rsec_orlink"><a class="<?=$tabJSIdent?> rsec_changer" href="#<?=$tabJSIdent?>"><i class="rsec_iconka"></i><span class="rsec_name"><?=GetMessage('TAB_NAME_'.$arParams['RS_TAB_IDENT'])?>&nbsp;</span><span class="rsec_color rsec_cnt">0</span></a></div><?
			$this->EndViewTarget();
		}
	?></form><?
?></div>