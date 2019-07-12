<?
if((isset($_POST['PRODUCT_ID']) && $_POST['PRODUCT_ID']) || (isset($_POST['ID']) && $_POST['ID']) || (isset($_POST['BASKET']) && $_POST['BASKET']) || (isset($_POST['ORDER_ID']) && $_POST['ORDER_ID'])){
	require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
	\Bitrix\Main\Loader::includeModule('iblock');
	\Bitrix\Main\Loader::includeModule('sale');

	$arItem = $arSections = $arBasketItems = $arOrder = $arItemsIDs = array();
	$arSite = CSite::GetByID(SITE_ID)->Fetch();

	function conv($n){
		return iconv(SITE_CHARSET, 'UTF-8', $n);
	}

	if(isset($_POST['PRODUCT_ID']) && $_POST['PRODUCT_ID']){
		\Bitrix\Main\Loader::includeModule('catalog');
		$arItem = CIBlockElement::GetList(array(), array('ID' => $PRODUCT_ID), false, false, array('ID', 'NAME', 'PROPERTY_BRAND', 'IBLOCK_SECTION_ID'))->Fetch();
		$arItem['BRAND'] = '';
		if(strlen($arItem['PROPERTY_BRAND_VALUE'])){
			$arItemBrand = CIBlockElement::GetList(array(), array('ID' => $arItem['PROPERTY_BRAND_VALUE']), false, false, array('ID', 'NAME'))->Fetch();
			if($arItemBrand){
				$arItem['BRAND'] = $arItemBrand['NAME'];
			}
		}
		
		if(strlen($_POST['PRICE_ID'])){
			$priceTypeIterator = \Bitrix\Catalog\GroupTable::getList(array('select' => array('ID', 'NAME', 'NAME_LANG' => 'CURRENT_LANG.NAME'), 'order' => array('SORT' => 'ASC', 'ID' => 'ASC')));
			while($priceType = $priceTypeIterator->fetch()){
				if($priceType['NAME'] == $PRICE_ID){
					$priceCode = $priceType['ID'];
					break;
				}
			}
		
			if(strlen($priceCode)){
				$arPrice = CPrice::GetList(array(), array('PRODUCT_ID' => $PRODUCT_ID, 'CATALOG_GROUP_ID' => $priceCode))->Fetch();
				$arItem['PRICE'] = $arPrice['PRICE'];
			}
		}

		$obSections = CIBlockSection::GetNavChain(false, $arItem['IBLOCK_SECTION_ID'], array('NAME'));
		while($arSection = $obSections->Fetch()){
			$arSections[] = $arSection['NAME'];
		}
		if($arSections){
			$arItem['CATEGORY'] = implode(' / ', $arSections);
		}
		
		$arItem['SHOP_NAME'] = $arSite['SITE_NAME'];
		
		$arItem = array_map('conv', $arItem);
		echo json_encode($arItem);
	}

	if(isset($_POST['ID']) && $_POST['ID']){
		$arItem = CIBlockElement::GetList(array(), array('ID' => $ID), false, false, array('ID', 'NAME', 'PROPERTY_BRAND', 'IBLOCK_SECTION_ID'))->Fetch();
		$arItem['BRAND'] = $arItem['CATEGORY'] = '';
		if(strlen($arItem['PROPERTY_BRAND_VALUE'])){
			$arItemBrand = CIBlockElement::GetList(array(), array('ID' => $arItem['PROPERTY_BRAND_VALUE']), false, false, array('ID', 'NAME'))->Fetch();
			if($arItemBrand){
				$arItem['BRAND'] = $arItemBrand['NAME'];
			}
		}
		
		$arBasketItems = CSaleBasket::GetList(array(), array('ORDER_ID' => NULL, 'FUSER_ID' => CSaleBasket::GetBasketUserID(), 'LID' => SITE_ID, 'PRODUCT_ID' => $ID), false, false, array('QUANTITY', 'PRICE', 'CURRENCY'))->Fetch();
	    $obSections = CIBlockSection::GetNavChain(false, $arItem['IBLOCK_SECTION_ID'], array('NAME'));
		while($arSection = $obSections->Fetch()){
			$arSections[] = $arSection['NAME'];
		}
		if($arSections){
			$arItem['CATEGORY'] = implode(' / ', $arSections);
		}

		$arBasketItems['SHOP_NAME'] = $arSite['SITE_NAME'];

		$arItem = array_map('conv', $arItem);
		$arBasketItems= array_map('conv', $arBasketItems);
		echo json_encode(array_merge($arItem, $arBasketItems));
	}
	elseif(isset($_POST['BASKET']) && $_POST['BASKET']){
		$dbBasket = CSaleBasket::GetList(array('DATE_INSERT' => 'ASC', 'NAME' => 'ASC'), array('ORDER_ID' => NULL, 'FUSER_ID' => CSaleBasket::GetBasketUserID(), 'LID' => SITE_ID), false, false, array('PRODUCT_ID', 'PRICE', 'QUANTITY', 'CURRENCY'));
		while($arBasketItem = $dbBasket->Fetch()){
			$arBasketItems['ITEMS'][$arBasketItem['PRODUCT_ID']] = $arBasketItem;
			$arItemsIDs[] = $arBasketItem['PRODUCT_ID'];
		}


		if($arItemsIDs){
			$resItem = CIBlockElement::GetList(array(), array('ID' => $arItemsIDs), false, false, array('ID', 'NAME', 'PROPERTY_BRAND', 'IBLOCK_SECTION_ID'));
			while($arTmpItem = $resItem->Fetch()){
				$arSections = array();
				$arTmpItem['BRAND'] = $arTmpItem['CATEGORY'] = '';

				if(strlen($arTmpItem['PROPERTY_BRAND_VALUE'])){
					$arItemBrand = CIBlockElement::GetList(array(), array('ID' => $arTmpItem['PROPERTY_BRAND_VALUE']), false, false, array('ID', 'NAME'))->Fetch();
					if($arItemBrand){
						$arTmpItem['BRAND'] = $arItemBrand['NAME'];
					}
				}

				$obSections = CIBlockSection::GetNavChain(false, $arTmpItem['IBLOCK_SECTION_ID'], array('NAME'));
				while($arSection = $obSections->Fetch()){
					$arSections[] = $arSection['NAME'];
				}
				if($arSections){
					$arTmpItem['CATEGORY'] = implode(' / ', $arSections);
				}

				$arBasketItems['ITEMS'][$arTmpItem['ID']] = array_map('conv', array_merge($arBasketItems['ITEMS'][$arTmpItem['ID']], $arTmpItem));
			}
		}

		$arBasketItems['SHOP_NAME'] = $arSite['SITE_NAME'];

		$arBasketItemsItems = $arBasketItems['ITEMS'];
		$arBasketItems = array_map('conv', $arBasketItems);
		$arBasketItems['ITEMS'] = $arBasketItemsItems;
		echo json_encode($arBasketItems);
	}
	elseif(isset($_POST['ORDER_ID']) && $_POST['ORDER_ID']){
		$bUseAccountNumber = \Bitrix\Main\Config\Option::get('sale', 'account_number_template', '') !== '';
		if($bUseAccountNumber){
			$arOrder = CSaleOrder::GetList(array(), array('ACCOUNT_NUMBER' => $ORDER_ID))->GetNext();
		}

		if(!$arOrder){
			$arOrder = CSaleOrder::GetList(array(), array('ID' => $ORDER_ID))->GetNext();
		}

		$dbBasket = CSaleBasket::GetList(array('DATE_INSERT' => 'ASC', 'NAME' => 'ASC'), array('ORDER_ID' => $ORDER_ID), false, false, array('PRODUCT_ID', 'PRICE', 'QUANTITY', 'CURRENCY'));
		while($arBasketItem = $dbBasket->Fetch()){
			$arOrder['ITEMS'][$arBasketItem['PRODUCT_ID']] = $arBasketItem;
			$arItemsIDs[] = $arBasketItem['PRODUCT_ID'];
		}

		if($arItemsIDs){
			$resItem = CIBlockElement::GetList(array(), array('ID' => $arItemsIDs), false, false, array('ID', 'NAME', 'PROPERTY_BRAND', 'IBLOCK_SECTION_ID'));
			while($arTmpItem = $resItem->Fetch()){
				$arSections = array();
				$arTmpItem['BRAND'] = $arTmpItem['CATEGORY'] = '';
				
				if(strlen($arTmpItem['PROPERTY_BRAND_VALUE'])){
					$arItemBrand = CIBlockElement::GetList(array(), array('ID' => $arTmpItem['PROPERTY_BRAND_VALUE']), false, false, array('ID', 'NAME'))->Fetch();
					if($arItemBrand){
						$arTmpItem['BRAND'] = $arItemBrand['NAME'];
					}
				}

				$obSections = CIBlockSection::GetNavChain(false, $arTmpItem['IBLOCK_SECTION_ID'], array('NAME'));
				while($arSection = $obSections->Fetch()){
					$arSections[] = $arSection['NAME'];
				}
				if($arSections){
					$arTmpItem['CATEGORY'] = implode(' / ', $arSections);
				}

				$arOrder['ITEMS'][$arTmpItem['ID']] = array_map('conv', array_merge($arOrder['ITEMS'][$arTmpItem['ID']], $arTmpItem));
			}
		}

		$arOrder['SHOP_NAME'] = $arSite['SITE_NAME'];
		$arOrderItems = $arOrder['ITEMS'];
		$arOrder = array_map('conv', $arOrder);
		$arOrder['ITEMS'] = $arOrderItems;
		echo json_encode($arOrder);
	}
}?>