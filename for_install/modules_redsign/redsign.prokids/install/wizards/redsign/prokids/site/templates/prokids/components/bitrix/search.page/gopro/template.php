<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

?><div class="search_page clearfix"><?
	?><form action="" method="get" class="form_search"><?
		?><input type="hidden" name="tags" value="<?echo $arResult['REQUEST']['TAGS']?>" /><?
		?><input type="text" class="q" name="q" value="<?=$arResult['REQUEST']['QUERY']?>" size="40" /><?
		?><input class="btn btn1" type="submit" class="search_94" value="<?=GetMessage('SEARCH_GO')?>" /><?
		?><input type="hidden" name="how" value="<?=$arResult['REQUEST']['HOW']=='d'? 'd': 'r'?>" /><?
	?></form><?
?></div><?

if($arResult['ERROR_CODE']!=0)
{
	ShowError($arResult['ERROR_TEXT']);
	?><p><?=GetMessage('SEARCH_CORRECT_AND_CONTINUE')?></p><br /><br /><?
} elseif(count($arResult['SEARCH'])>0)
{
	?><div class="spage"><?
		////////////////// IBLOCKS
		if(!empty($arResult["EXT_SEARCH"]["IBLOCK"]["IBLOCKS"]))
		{
			foreach($arResult["EXT_SEARCH"]["IBLOCK"]["IBLOCKS"] as $iblock_id => $arIblock)
			{
				// catalog
				if(in_array($iblock_id,$arParams['IBLOCK_ID']))
				{
					global $arrSearchFilter;
					$arIds = array();
					foreach($arResult['EXT_SEARCH']['IBLOCK']['ITEMS'][$iblock_id] as $arItem)
					{
						if( $arItem['ITEM_ID']!=$arItem['ID'] && IntVal($arItem['ITEM_ID'])>0 )
						{
							$arIds[] = $arItem['ITEM_ID'];
						}
					}
					if( is_array($arIds) && count($arIds)>0 )
					{
						?><div class="iblock"><?
							?><div class="title"><h3><?=$arIblock["NAME"]?></h3></div><?
							$arrSearchFilter = array('ID'=>$arIds);
							$APPLICATION->IncludeComponent(
								"bitrix:catalog.section",
								"searchpage",
								Array(
									"IBLOCK_TYPE" => "",
									"IBLOCK_ID" => $iblock_id,
									"ELEMENT_SORT_FIELD" => "SORT",//$arParams["ELEMENT_SORT_FIELD"],
									"ELEMENT_SORT_ORDER" => "ASC",//$arParams["ELEMENT_SORT_ORDER"],
									"PROPERTY_CODE" => array($arParams["PROPCODE_MORE_PHOTO"]),
									"META_KEYWORDS" => "",
									"META_DESCRIPTION" => "",
									"BROWSER_TITLE" => "",
									"INCLUDE_SUBSECTIONS" => "Y",
									"BASKET_URL" => "",
									"ACTION_VARIABLE" => "",
									"PRODUCT_ID_VARIABLE" => "",
									"SECTION_ID_VARIABLE" => "",
									"PRODUCT_QUANTITY_VARIABLE" => "",
									"FILTER_NAME" => "arrSearchFilter",
									"CACHE_TYPE" => "N",
									"CACHE_TIME" => "0",
									"CACHE_FILTER" => "",
									"CACHE_GROUPS" => "",
									"SET_TITLE" => "N",
									"SET_STATUS_404" => "N",
									"DISPLAY_COMPARE" => "N",
									"PAGE_ELEMENT_COUNT" => "10",//$arParams["PAGE_ELEMENT_COUNT"]
									"LINE_ELEMENT_COUNT" => "",
									"PRICE_CODE" => $arParams["PRICE_CODE"],
									"USE_PRICE_COUNT" => "N",
									"SHOW_PRICE_COUNT" => "N",
									"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
									"USE_PRODUCT_QUANTITY" => "N",
									"DISPLAY_TOP_PAGER" => "N",
									"DISPLAY_BOTTOM_PAGER" => "N",
									"PAGER_TITLE" => "",
									"PAGER_SHOW_ALWAYS" => "N",
									"PAGER_TEMPLATE" => "",
									"PAGER_DESC_NUMBERING" => "",
									"PAGER_DESC_NUMBERING_CACHE_TIME" => "0",
									"PAGER_SHOW_ALL" => "N",
									"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
									"OFFERS_FIELD_CODE" => $arParams["OFFERS_FIELD_CODE"],
									"OFFERS_PROPERTY_CODE" => $arParams["OFFERS_PROPERTY_CODE"],
									"OFFERS_SORT_FIELD" => "catalog_PRICE_".$arParams["SKU_PRICE_SORT_ID"],
									"OFFERS_SORT_ORDER" => "ASC",
									"OFFERS_LIMIT" => "0",
									"SECTION_ID" => "",
									"SECTION_CODE" => "",
									"SECTION_URL" => "",
									"DETAIL_URL" => "",
									"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
									"CURRENCY_ID" => $arParams["CURRENCY_ID"],
									"BY_LINK" => "Y",
								),
								false//,
								//array('HIDE_ICONS'=>'Y')
							);
						?></div><?
					}
				}
			}
			foreach($arResult["EXT_SEARCH"]["IBLOCK"]["IBLOCKS"] as $iblock_id => $arIblock)
			{
				// catalog
				if(in_array($iblock_id,$arParams['IBLOCK_ID']))
				{
					$c = false;
					foreach($arResult['EXT_SEARCH']['IBLOCK']['ITEMS'][$iblock_id] as $arItem)
					{
						if( IntVal($arItem['ITEM_ID'])<1 )
						{
							$c = true;
							break;
						}
					}
					if($c)
					{
						?><div class="iblock"><?
							?><div class="title"><h3><?=$arIblock["NAME"]?></h3></div><?
							foreach($arResult['EXT_SEARCH']['IBLOCK']['ITEMS'][$iblock_id] as $arItem)
							{
								if( IntVal($arItem['ITEM_ID'])<1 )
								{
									?><div class="sitem"><?
										?><div class="name"><a href="<?=$arItem['URL']?>"><?=$arItem['TITLE_FORMATED']?></a></div><?
										?><div class="description"><?=$arItem['BODY_FORMATED']?></div><?
										?><div class="chain"><?=$arItem['CHAIN_PATH']?></div><?
									?></div><?
								}
							}
						?></div><?
					}
				}
			}
			// other
			foreach($arResult['EXT_SEARCH']['IBLOCK']['IBLOCKS'] as $iblock_id => $arIblock)
			{
				if(!in_array($iblock_id,$arParams['IBLOCK_ID']))
				{
					?><div class="iblock"><?
						?><div class="title<?if($isFirst):?> first<?endif;?>"><h3><?=$arIblock['NAME']?></h3></div><?
						foreach($arResult['EXT_SEARCH']['IBLOCK']['ITEMS'][$iblock_id] as $arItem)
						{
							?><div class="sitem"><?
								?><div class="name"><a href="<?=$arItem['URL']?>"><?=$arItem['TITLE_FORMATED']?></a></div><?
								?><div class="description"><?=$arItem['BODY_FORMATED']?></div><?
								?><div class="chain"><?=$arItem['CHAIN_PATH']?></div><?
							?></div><?
						}
					?></div><?
					$isFirst = false;
				}
			}
		}
		////////////////// OTHER
		if(!empty($arResult['EXT_SEARCH']['OTHER']['ITEMS']))
		{
			?><div class="iblock"><?
				foreach($arResult['EXT_SEARCH']['OTHER']['ITEMS'] as $arOther)
				{
					?><div class="sitem"><?
						?><div class="name"><a href="<?=$arOther['URL']?>"><?=$arOther['TITLE_FORMATED']?></a></div><?
						?><div class="description"><?=$arOther['BODY_FORMATED']?></div><?
						?><div class="chain"><?=$arOther['CHAIN_PATH']?></div><?
					?></div><?
				}
			?></div><?
		}
	?></div><?
	
	if($arParams['DISPLAY_BOTTOM_PAGER'] != 'N'){
		echo '<br />'.$arResult['NAV_STRING'];
	}
}
else{
	ShowNote(GetMessage('SEARCH_NOTHING_TO_FOUND'));
};