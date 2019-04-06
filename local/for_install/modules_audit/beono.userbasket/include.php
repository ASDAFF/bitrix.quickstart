<?php 

class BeonoUserBasket {
	
	public static function getBasketItems () {
		
		global $arResult;
		
		$arBasketItemsFilter = array("ORDER_ID" => "NULL");
				
		if ($arResult['USER']) {
			$arSaleUser = CSaleUser::GetList(array('USER_ID' => $arResult['USER']));
			$arBasketItemsFilter['FUSER_ID'] = $arSaleUser['ID'];
		}
		
		if ($arResult['PRODUCT']) {
			$arBasketItemsFilter['PRODUCT_ID'] = $arResult['PRODUCT'];
		}

		if ($arResult['PRODUCT_NAME']) {
			$arBasketItemsFilter['%NAME'] = $arResult['PRODUCT_NAME'];
		}
			
		if ($arResult['DAYS_AGO']) {
			$arBasketItemsFilter['>=DATE_INSERT'] = ConvertTimeStamp(getmicrotime()-($arResult['DAYS_AGO']*86400), 'SHORT');
		}
		
		if ($arResult['DATE_FROM']) {
			$arBasketItemsFilter['>=DATE_INSERT'] = $arResult['DATE_FROM'];
		}
		
		if ($arResult['DATE_TO']) {
			$arBasketItemsFilter['<=DATE_INSERT'] = $arResult['DATE_TO'];
		}
		
		if (in_array($arResult['DELAY'], array('Y', 'N'))) {
			$arBasketItemsFilter['DELAY'] = $arResult['DELAY'];
		}
		
		$dbBasketItems = CSaleBasket::GetList(array("ID" => "DESC"), $arBasketItemsFilter, false, array('nPageSize' => 10000), array());
			
		$arResult['ITEMS'] = array();
		$arUsersCache = array();
		$arBasketItemsId = array();
		while ($arBasketItem = $dbBasketItems->Fetch())
		{
			
			if ($arBasketItem['FUSER_ID']) {				
				if (!is_set($arUsersCache[$arBasketItem['FUSER_ID']])) {				
					$arSaleUser = CSaleUser::GetList(array('ID' => $arBasketItem['FUSER_ID']));			
					$rsUser = CUser::GetByID($arSaleUser['USER_ID']);
					$arUsersCache[$arBasketItem['FUSER_ID']] = $rsUser->Fetch();		
				}	
				if ($arUsersCache[$arBasketItem['FUSER_ID']]['ID']) {
					$arBasketItem['USER'] = $arUsersCache[$arBasketItem['FUSER_ID']]['LOGIN'];
					$arBasketItem['USER_ID'] = $arUsersCache[$arBasketItem['FUSER_ID']]['ID'];
					$arBasketItem['USER_NAME'] = $arUsersCache[$arBasketItem['FUSER_ID']]['LAST_NAME'].' '.$arUsersCache[$arBasketItem['FUSER_ID']]['NAME'];
					$arBasketItem['EMAIL'] = $arUsersCache[$arBasketItem['FUSER_ID']]['EMAIL'];
				}				
			}
			
			$arResult['ITEMS'][$arBasketItem['ID']] = array(
				'ID' => $arBasketItem['ID'],
				'USER' => $arBasketItem['USER'],
				'USER_ID' => $arBasketItem['USER_ID'],
				'USER_NAME' => $arBasketItem['USER_NAME'],
				'EMAIL' => $arBasketItem['EMAIL'],
				'NAME' => $arBasketItem['NAME'],
				'DETAIL_PAGE_URL' => $arBasketItem['DETAIL_PAGE_URL'],
				'PRODUCT_ID' => $arBasketItem['PRODUCT_ID'],
				'PRODUCT_PRICE_ID' => $arBasketItem['PRODUCT_PRICE_ID'],
				'PRICE' => $arBasketItem['PRICE'].' '.$arBasketItem['CURRENCY'],
				'QUANTITY' => $arBasketItem['QUANTITY'],
				'LID' => $arBasketItem['LID'],
				'DELAY' => $arBasketItem['DELAY'],
				'CAN_BUY' => $arBasketItem['CAN_BUY'],
				'DATE_INSERT' => $arBasketItem['DATE_INSERT'],
				'PROPS' => ''
			);
			
			$arBasketItemsId[] = $arBasketItem['ID'];
		}	

		if (!empty($arBasketItemsId) && is_array($arResult['VISIBLE_COLUMNS']) && in_array('PROPS', $arResult['VISIBLE_COLUMNS'])) {
			if (COption::GetOptionString("sale", "show_order_product_xml_id", "N") == "Y") {
				$arPropsFilter = array("@BASKET_ID" => $arBasketItemsId);
			} else {
				$arPropsFilter = array("@BASKET_ID" => $arBasketItemsId, "!CODE" => array("CATALOG.XML_ID", "PRODUCT.XML_ID"));
			}
			$rsProps = CSaleBasket::GetPropsList(array("SORT" => "ASC"), $arPropsFilter);
			while ($arProps = $rsProps->Fetch()) {
				$arResult['ITEMS'][$arProps["BASKET_ID"]]['PROPS'] .= $arProps["NAME"]." = ".$arProps["VALUE"]."<br/>";
			}
		}
		
		return $arResult['ITEMS'];		
	}	
}

?>