<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
	CModule::IncludeModule("iblock");
	CModule::IncludeModule("sale");

	if($arResult["USER_VALS"]["CONFIRM_ORDER"] == "Y"){
		$arResult['ORDER_ITEMS']=array();
		if(intval($arResult["ORDER_ID"])>0){
			$dbBasketItems = CSaleBasket::GetList(
			        array(
			                "NAME" => "ASC",
			                "ID" => "ASC"
			            ),
			        array(
			                "LID" => SITE_ID,
			                "ORDER_ID" => intval($arResult["ORDER_ID"])
			            ),
			        false,
			        false,
			        array("ID", "NAME", 
			              "PRODUCT_ID", "QUANTITY",  "PRICE",  "CURRENCY")
			    );
			while ($arItems = $dbBasketItems->Fetch())
			{
				$arItems["TOTAL"]=$arItems["PRICE"]*$arItems["QUANTITY"];
				$arItems["TOTAL_FORMATED"]=SaleFormatCurrency($arItems["PRICE"]*$arItems["QUANTITY"], $arItems["CURRENCY"]);
				$arResult['ORDER_ITEMS'][] = $arItems;
			}

			$arResult['USER']=array();
			$db_vals = CSaleOrderPropsValue::GetList(
				array("SORT"=>"ASC"),
				array(
					"ORDER_ID" => intval($arResult["ORDER_ID"]),
					"CODE" => array("PHONE", "ADDRESS", "EMAIL", "FIO")
				)
			);
			while ($arVals = $db_vals->Fetch())
				$arResult["USER"][$arVals["CODE"]] = array(
					"NAME"=>$arVals["NAME"],
					"VALUE"=>$arVals["VALUE"],
				);

			
		}

		if($arResult["ORDER"]["DELIVERY_ID"]){
			$arr_temp=explode(":", $arResult["ORDER"]["DELIVERY_ID"]);

			$db_dtype = CSaleDelivery::GetList(
			    array( ),
			    array(
			            "LID" => SITE_ID,
			            "ID" => $arr_temp[0]
			        ),
			    false,
			    false,
			    array("NAME", "DESCRIPTION")
			);
			if ($ar_dtype = $db_dtype->Fetch())
			{
				$arResult["DELIVERY"]["NAME"]=$ar_dtype["NAME"];
				$arResult["DELIVERY"]["DESCRIPTION"]=$ar_dtype["DESCRIPTION"];
			} else {

				$dbResult = CSaleDeliveryHandler::GetList(
				  array(), 
				  array(
				    "LID" => SITE_ID,
				    "SID" => $arr_temp[0]
				  )
				);
				if ($ar_dtype = $dbResult->GetNext())
				{
					$arResult["DELIVERY"]["NAME"]=$ar_dtype["NAME"];
					$arResult["DELIVERY"]["DESCRIPTION"]=$ar_dtype["DESCRIPTION"];
				}
			}
		}

	}

	//echo "<pre>"; print_r($arResult['DELIVERY']); echo "</pre>";

	if(count($arResult['BASKET_ITEMS'])>0){	
		$arIDs = array();
		foreach($arResult['BASKET_ITEMS'] as $k=>$v){
			$arIDs[$v["PRODUCT_ID"]] = "";
		}
		$res=CIBlockElement::GetList(array(), array("ID"=>array_keys($arIDs)), false, false, array("ID", "DETAIL_PICTURE", "PREVIEW_PICTURE"));
		while($ar=$res->GetNext()){
			$id=0;
			if($ar["PREVIEW_PICTURE"])
				$id= $ar["PREVIEW_PICTURE"];
			elseif($ar["DETAIL_PICTURE"])
				$id= $ar["DETAIL_PICTURE"];
			if($id>0){
				$arFileTmp = CFile::ResizeImageGet(
					$id,
					array("width" => 80, "height" => 80),
					BX_RESIZE_IMAGE_PROPORTIONAL,
					true
				);			
				$arIDs[$ar["ID"]]=array(
					'SRC' => $arFileTmp["src"],
					'WIDTH' => $arFileTmp["width"],
					'HEIGHT' => $arFileTmp["height"],
				);
			}
	
	
		}
	
	
		foreach($arResult["BASKET_ITEMS"] as $k=>$v){
			foreach($arIDs as $id=>$pic){
				if($id==$v["PRODUCT_ID"]){
					$arResult["BASKET_ITEMS"][$k]["DETAIL_PICTURE"]=$pic;
					break;
				}
			}
		}
	}


?>