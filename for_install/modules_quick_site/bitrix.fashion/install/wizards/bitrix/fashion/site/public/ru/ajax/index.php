<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if(CModule::IncludeModuleEx('bitrix.fashion')==3){
	echo 'Test period is over';
	return;
}

CModule::IncludeModule("iblock");
CModule::IncludeModule("sale");
CModule::IncludeModule("catalog");

if(isset($_REQUEST['id'])&&intval($_REQUEST['id'])>0){
    if(!(isset($_REQUEST['q'])&&intval($_REQUEST['q'])>0)) $_REQUEST['q'] = 1;
    
	$productProperties = array();
	$rsMainBID = CIBlockElement::GetByID(intval($_REQUEST["id"]));
	if($arMainBID = $rsMainBID->GetNext()){				
		$dbLink = CIBlockElement::GetProperty($arMainBID['IBLOCK_ID'], $_REQUEST['id'], array("sort" => "asc"), Array("CODE"=>"model"));
		if($arLink = $dbLink->Fetch()){			
			$productProperties = CIBlockPriceTools::GetOfferProperties(
				$_REQUEST["id"],
				$arLink['LINK_IBLOCK_ID'],
				array("item_color", "item_size")
			);
		}		
	}
    
    if(CSiteFashionStore::DVSAdd2BasketByProductID($_REQUEST['id'], $_REQUEST['q'], $productProperties)){
        $APPLICATION->IncludeComponent(
            "bitrix:sale.basket.basket.small",
            "",
            Array(
                "PATH_TO_BASKET" => SITE_DIR."personal/cart/",
                "PATH_TO_ORDER" => SITE_DIR."personal/order/"
            ),
        false
        );
    }
}


require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>