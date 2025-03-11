<?
AddEventHandler("catalog", "OnProductUpdate", 'OnProductCatalogHandler');
AddEventHandler("catalog", "OnProductAdd", 'OnProductCatalogHandler');
function OnProductCatalogHandler( $ID, $arFields ){
		
	Bitrix\Main\Loader::includeModule('iblock');
	Bitrix\Main\Loader::includeModule('catalog');
	define('IBLOCK_ID_PRODUCTS', 2);
	define('IBLOCK_ID_OFFERS', 3);	
			
			
	$query = new \Bitrix\Main\Entity\Query(Bitrix\Iblock\ElementTable::getEntity());
	$query->setSelect(array("ID", "IBLOCK_ID"))
		  ->setFilter(array("ID" => $ID))
		  ->setOrder(array("ID" => "ASC"));
	$resElement = $query->exec()->fetch();
	if( $resElement['IBLOCK_ID'] == IBLOCK_ID_PRODUCTS ){
		CIBlockElement::SetPropertyValuesEx( $ID, $resElement['IBLOCK_ID'], array( "AVAILABLE_QUANTITY_CATALOG" => $arFields['QUANTITY'] ) );		
	}elseif( $resElement['IBLOCK_ID'] == IBLOCK_ID_OFFERS ){		
		//1	
		$rsElementOffer = CIBlockElement::GetList( array(), array( "ID" => $ID ), false, false, array("ID", "IBLOCK_ID", 'NAME', "PROPERTY_CML2_LINK") )->fetch();	
        $resElemOfferProduct = (int)$rsElementOffer['PROPERTY_CML2_LINK_VALUE'];		
		//2
		$resOffersCML2 = CIBlockElement::GetList( array(), array( "PROPERTY_CML2_LINK" => $resElemOfferProduct, 'IBLOCK_ID'=>IBLOCK_ID_OFFERS ), false, false, array("ID", "IBLOCK_ID", 'NAME') );	
		//3
		$arrOffersIDs = array();
		while ($res = $resOffersCML2->fetch()) {
			$arrOffersIDs[] = $res['ID'];
		}		
		//4
		$availQuant = array();
		foreach ($arrOffersIDs as $k => $v) {
			$ar_res_cat = CCatalogProduct::GetList( array("ID" => "DESC"), array("ID" => (int)$v ), false, false, array("ID", "QUANTITY", 'ELEMENT_IBLOCK_ID','ELEMENT_NAME') )->fetch();			
			$availQuant[] = $ar_res_cat['QUANTITY'];
		}		
		//5
		$minAvailQuant = (int)min($availQuant);		
		//6
		$resUpdateSCU = CIBlockElement::SetPropertyValuesEx( $resElemOfferProduct, IBLOCK_ID_PRODUCTS, array( "AVAILABLE_QUANTITY_CATALOG" => $minAvailQuant ) );			
	}	

}
?>
