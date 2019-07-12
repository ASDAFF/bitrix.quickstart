<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

if(!CModule::IncludeModule("sale") || !CModule::IncludeModule("catalog") || !CModule::IncludeModule("iblock")){
	echo "failure";
	return;
}

if(!empty($_REQUEST["add_item"])){
	if($_REQUEST["add_item"] == "Y"){
		if($_REQUEST["quantity"]){
			$_REQUEST["quantity"] = floatval($_REQUEST["quantity"]);
		}
		$dbBasketItems = CSaleBasket::GetList(
			array("NAME" => "ASC", "ID" => "ASC"),
			array("PRODUCT_ID" => $_REQUEST["item"], "FUSER_ID" => CSaleBasket::GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => "NULL"),
			false, false, array("ID", "DELAY")
		)->Fetch();
		if(!empty($dbBasketItems) && $dbBasketItems["DELAY"] == "Y"){
			$arFields = array("DELAY" => "N", "SUBSCRIBE" => "N");
			if($_REQUEST["quantity"]){
				$arFields['QUANTITY'] = $_REQUEST["quantity"];
			}
			CSaleBasket::Update($dbBasketItems["ID"], $arFields);
		}
		else{
			$product_properties=$arSkuProp=array();
			$successfulAdd = true;
			$intProductIBlockID = (int)CIBlockElement::GetIBlockByID($_REQUEST["item"]);
			$strErrorExt='';
			if(0 < $intProductIBlockID){			
				if($_REQUEST["add_props"]=="Y"){
					$arSkuProp=json_decode($_REQUEST["props"]);
					if ($intProductIBlockID == $_REQUEST["iblockID"]){
						if($_REQUEST["props"]){
							$product_properties = CIBlockPriceTools::CheckProductProperties(
								$_REQUEST["iblockID"],
								$_REQUEST["item"],
								$arSkuProp,
								$_REQUEST["prop"],
								$_REQUEST['part_props'] == 'Y'
							);
							if (!is_array($product_properties)){
								$strError = "CATALOG_PARTIAL_BASKET_PROPERTIES_ERROR";
								$successfulAdd = false;
							}
						}else{
							$strError = "CATALOG_EMPTY_BASKET_PROPERTIES_ERROR";
							$successfulAdd  = false;
						}
					}else{
						$skuAddProps = (isset($_REQUEST['basket_props']) && !empty($_REQUEST['basket_props']) ? $_REQUEST['basket_props'] : '');
						if ($arSkuProp || !empty($skuAddProps))
						{
							$product_properties = CIBlockPriceTools::GetOfferProperties(
								$_REQUEST["item"],
								$_REQUEST["iblockID"],
								$arSkuProp,
								$skuAddProps
							);
						}
					}
				}			
			}else{
				$strError = 'CATALOG_ELEMENT_NOT_FOUND';
				$successfulAdd = false;
			}
			if($successfulAdd){
				if(!Add2BasketByProductID($_REQUEST["item"], $_REQUEST["quantity"], $arRewriteFields, $product_properties))
				{
					if ($ex = $APPLICATION->GetException())
						$strErrorExt = $ex->GetString();
					
					$strError = "ERROR_ADD2BASKET";
					$successfulAdd = false;
				}
			}
			if ($successfulAdd){
				$addResult = array('STATUS' => 'OK', 'MESSAGE' => 'CATALOG_SUCCESSFUL_ADD_TO_BASKET', 'MESSAGE_EXT' => $strErrorExt);
			}else{
				$addResult = array('STATUS' => 'ERROR', 'MESSAGE' => $strError, 'MESSAGE_EXT' => $strErrorExt);
			}
			echo json_encode($addResult);
			die();
		}
	}
}
elseif(!empty($_REQUEST["subscribe_item"])){
	if($_REQUEST["subscribe_item"] == "Y"){
		$dbBasketItems = CSaleBasket::GetList(
			array("NAME" => "ASC", "ID" => "ASC"),
			array("PRODUCT_ID" => $_REQUEST["item"], "FUSER_ID" => CSaleBasket::GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => "NULL"),
			false, false, array("ID", "PRODUCT_ID", "SUBSCRIBE", "CAN_BUY")
		)->Fetch();		
		if(!empty($dbBasketItems) && $dbBasketItems["SUBSCRIBE"] == "N"){
			$arFields = array("SUBSCRIBE" => "Y", "CAN_BUY" => "N", "DELAY" => "N"); 
			CSaleBasket::Update($dbBasketItems["ID"], $arFields); 
		}
		elseif(!empty($dbBasketItems) && $dbBasketItems["SUBSCRIBE"] == "Y"){	
			CSaleBasket::Delete($dbBasketItems["ID"]); 
		}
		else{
			$arRewriteFields = array("SUBSCRIBE" => "Y", "CAN_BUY" => "N", "DELAY" => "N");	
			Add2BasketByProductID(intVal($_REQUEST["item"]), 1, $arRewriteFields, array());
		}
	}
}
elseif(!empty($_REQUEST["wish_item"])){ 
	if($_REQUEST["wish_item"] == "Y"){
		if($_REQUEST["quantity"]){
			$_REQUEST["quantity"] = floatval($_REQUEST["quantity"]);
		}
		$dbBasketItems = CSaleBasket::GetList(
			array("NAME" => "ASC", "ID" => "ASC"),
			array("PRODUCT_ID" => $_REQUEST["item"], "FUSER_ID" => CSaleBasket::GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => "NULL", "CAN_BUY" => "Y", "SUBSCRIBE" => "N"),
			false, false, array("ID", "PRODUCT_ID", "DELAY")
		)->Fetch();
		if(!empty($dbBasketItems) && $dbBasketItems["DELAY"] == "N"){
			$arFields = array("DELAY" => "Y", "SUBSCRIBE" => "N");
			if($_REQUEST["quantity"]){
				$arFields['QUANTITY'] = $_REQUEST["quantity"];
			}
			CSaleBasket::Update($dbBasketItems["ID"], $arFields); 
		}
		elseif(!empty($dbBasketItems) && $dbBasketItems["DELAY"] == "Y"){
			CSaleBasket::Delete($dbBasketItems["ID"]); 
		}
		else{
			$successfulAdd = true;
			if($_REQUEST["offers"] == "Y" && $_REQUEST["iblockID"]){
				$product_properties = $arSkuProp = array();
				$arSkuProp = json_decode($_REQUEST["props"]);
				if($arSkuProp){
					$product_properties = CIBlockPriceTools::GetOfferProperties($_REQUEST["item"], $_REQUEST["iblockID"], $arSkuProp, $skuAddProps);
				}
				$id = Add2BasketByProductID($_REQUEST["item"], $_REQUEST["quantity"], array(), $product_properties);
			}
			else{
				$id = Add2BasketByProductID($_REQUEST["item"], $_REQUEST["quantity"]);
			}
			if(!$id){
				if ($ex = $APPLICATION->GetException())
					$strErrorExt = $ex->GetString();
				$successfulAdd=false;
				$strError = "ERROR_ADD2BASKET";
			}
			
			$arFields = array("DELAY" => "Y", "SUBSCRIBE" => "N");		
			CSaleBasket::Update($id, $arFields);

			if ($successfulAdd){
				$addResult = array('STATUS' => 'OK', 'MESSAGE' => 'CATALOG_SUCCESSFUL_ADD_TO_BASKET', 'MESSAGE_EXT' => $strErrorExt);
			}else{
				$addResult = array('STATUS' => 'ERROR', 'MESSAGE' => $strError, 'MESSAGE_EXT' => $strErrorExt);
			}
			echo json_encode($addResult);
			die();
		}
	}
}
elseif(!empty($_REQUEST["compare_item"])){
	$iblock_id = $_REQUEST["iblock_id"];
	if(!empty($_SESSION["CATALOG_COMPARE_LIST"]) && !empty($_SESSION["CATALOG_COMPARE_LIST"][$iblock_id]) && array_key_exists($_REQUEST["item"], $_SESSION["CATALOG_COMPARE_LIST"][$iblock_id]["ITEMS"])){
		unset($_SESSION["CATALOG_COMPARE_LIST"][$iblock_id]["ITEMS"][$_REQUEST["item"]]);
	}
	else{
		$_SESSION["CATALOG_COMPARE_LIST"][$iblock_id]["ITEMS"][$_REQUEST["item"]] = CIBlockElement::GetByID($_REQUEST["item"])->Fetch();
	}
}
elseif(!empty($_REQUEST["delete_item"])){
	$dbBasketItems = CSaleBasket::GetList(
		array("NAME" => "ASC", "ID" => "ASC"),
		array("PRODUCT_ID" => $_REQUEST["item"], "FUSER_ID" => CSaleBasket::GetBasketUserID(), "LID" => SITE_ID, "ORDER_ID" => "NULL"),
		false, false, array("ID", "DELAY")
	)->Fetch();
	if(!empty($dbBasketItems)){
		CSaleBasket::Delete($dbBasketItems["ID"]);
	}
}

if(CModule::IncludeModule('aspro.optimus')){
	COptimus::clearBasketCounters();
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>