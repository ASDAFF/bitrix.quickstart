<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
 
if(!CModule::IncludeModule("iblock"))
{
	ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
	return;
}
/*************************************************************************
	Processing of received parameters
*************************************************************************/
 
$arParams["NAME"]=trim($arParams["NAME"]);
if(strlen($arParams["NAME"])<=0)
	$arParams["NAME"] = "CATALOG_COMPARE_LIST";

if(strlen($arParams["ELEMENT_SORT_FIELD"])<=0)
	$arParams["ELEMENT_SORT_FIELD"]="sort";

if(!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["ELEMENT_SORT_ORDER"]))
	$arParams["ELEMENT_SORT_ORDER"]="asc";

$arParams["DETAIL_URL"]=trim($arParams["DETAIL_URL"]);
$arParams["BASKET_URL"]=trim($arParams["BASKET_URL"]);
if(strlen($arParams["BASKET_URL"])<=0)
	$arParams["BASKET_URL"] = "/personal/basket.php";

$arParams["ACTION_VARIABLE"]=trim($arParams["ACTION_VARIABLE"]);
if(strlen($arParams["ACTION_VARIABLE"])<=0|| !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["ACTION_VARIABLE"]))
	$arParams["ACTION_VARIABLE"] = "action";

$arParams["PRODUCT_ID_VARIABLE"]=trim($arParams["PRODUCT_ID_VARIABLE"]);
if(strlen($arParams["PRODUCT_ID_VARIABLE"])<=0|| !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["PRODUCT_ID_VARIABLE"]))
	$arParams["PRODUCT_ID_VARIABLE"] = "id";

$arParams["SECTION_ID_VARIABLE"]=trim($arParams["SECTION_ID_VARIABLE"]);
if(strlen($arParams["SECTION_ID_VARIABLE"])<=0|| !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["SECTION_ID_VARIABLE"]))
	$arParams["SECTION_ID_VARIABLE"] = "SECTION_ID";

 
if(!is_array($arParams["FIELD_CODE"]))
	$arParams["FIELD_CODE"] = array();
foreach($arParams["FIELD_CODE"] as $k=>$v)
	if($v==="")
		unset($arParams["FIELD_CODE"][$k]);

if(!is_array($arParams["OFFERS_FIELD_CODE"]))
	$arParams["OFFERS_FIELD_CODE"] = array();
foreach($arParams["OFFERS_FIELD_CODE"] as $k=>$v)
	if($v==="")
		unset($arParams["OFFERS_FIELD_CODE"][$k]);

if(!is_array($arParams["OFFERS_PROPERTY_CODE"])){ 
    $arParams["OFFERS_PROPERTY_CODE"] = array();
}

foreach($arParams["OFFERS_PROPERTY_CODE"] as $k=>$v)
	if($v==="")
		unset($arParams["OFFERS_PROPERTY_CODE"][$k]);

if(!in_array("NAME", $arParams["FIELD_CODE"]))
	$arParams["FIELD_CODE"][]="NAME";
if(!is_array($arParams["PRICE_CODE"]))
	$arParams["PRICE_CODE"] = array();

$arParams["USE_PRICE_COUNT"] = $arParams["USE_PRICE_COUNT"]=="Y";
$arParams["SHOW_PRICE_COUNT"] = intval($arParams["SHOW_PRICE_COUNT"]);
if($arParams["SHOW_PRICE_COUNT"]<=0)
	$arParams["SHOW_PRICE_COUNT"]=1;

$arParams["DISPLAY_ELEMENT_SELECT_BOX"] = $arParams["DISPLAY_ELEMENT_SELECT_BOX"]=="Y";
if(strlen($arParams["ELEMENT_SORT_FIELD_BOX"])<=0)
	$arParams["ELEMENT_SORT_FIELD_BOX"]="sort";

if(!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["ELEMENT_SORT_ORDER_BOX"]))
	$arParams["ELEMENT_SORT_ORDER_BOX"]="asc";

$arParams["PRICE_VAT_INCLUDE"] = $arParams["PRICE_VAT_INCLUDE"] !== "N";

if($arParams["LINK_IBLOCK_ID"] >  0 && strlen($arParams["LINK_PROPERTY_SID"]) > 0)
{
	if(!is_array($arParams["LINK_PROPERTY_CODE"]))
		$arParams["LINK_PROPERTY_CODE"] = array();
	foreach($arParams["LINK_PROPERTY_CODE"] as $k=>$v)
		if($v==="")
			unset($arParams["LINK_PROPERTY_CODE"][$k]);
	if(!is_array($arParams["LINK_FIELD_CODE"]))
		$arParams["LINK_FIELD_CODE"] = array();
	foreach($arParams["LINK_FIELD_CODE"] as $k=>$v)
		if($v==="")
			unset($arParams["LINK_FIELD_CODE"][$k]);
}
else
{
	unset($arParams["LINK_PROPERTY_CODE"]);
	unset($arParams["LINK_FIELD_CODE"]);
}

$arParams['CONVERT_CURRENCY'] = (isset($arParams['CONVERT_CURRENCY']) && 'Y' == $arParams['CONVERT_CURRENCY'] ? 'Y' : 'N');
$arParams['CURRENCY_ID'] = trim(strval($arParams['CURRENCY_ID']));
if ('' == $arParams['CURRENCY_ID'])
{
	$arParams['CONVERT_CURRENCY'] = 'N';
}
elseif ('N' == $arParams['CONVERT_CURRENCY'])
{
	$arParams['CURRENCY_ID'] = '';
}

$arID = array();
if(isset($_REQUEST["ID"]))
{
	$arID = $_REQUEST["ID"];
	if(!is_array($arID))
		$arID = array($arID);
}
$arPR = array();
if(isset($_REQUEST["pr_code"]))
{
	$arPR = $_REQUEST["pr_code"];
	if(!is_array($arPR))
		$arPR = array($arPR);
}
$arOF = array();
if(isset($_REQUEST["of_code"]))
{
	$arOF = $_REQUEST["of_code"];
	if(!is_array($arOF))
		$arOF = array($arOF);
}
$arOP = array();
if(isset($_REQUEST["op_code"]))
{
	$arOP = $_REQUEST["op_code"];
	if(!is_array($arOP))
		$arOP = array($arOP);
}

$arResult = array();


if(!isset($_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DIFFERENT"]))
	$_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DIFFERENT"] = false;
if(isset($_REQUEST["DIFFERENT"]))
	$_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DIFFERENT"] = $_REQUEST["DIFFERENT"]=="Y";
$arResult["DIFFERENT"] = $_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DIFFERENT"];

if($_REQUEST['remove']){  
    removeFromCompare($_REQUEST['remove']); 
    LocalRedirect('/catalog/compare/'); 
}
 
if(strlen($strError)>0){ 	
    ShowError($strError);
	return;
}
   
$arCompare = $_SESSION[$arParams["NAME"]]["ITEMS"];  
   
 
if(is_array($arCompare) && count($arCompare)>1)
{
	if(
		!array_key_exists("DELETE_PROP", $_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]])
		|| !is_array($_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DELETE_PROP"])
	)
	{
		$_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DELETE_PROP"] = array();
	}

	if(
		!array_key_exists("DELETE_OFFER_FIELD", $_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]])
		|| !is_array($_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DELETE_OFFER_FIELD"])
	)
	{
		$_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DELETE_OFFER_FIELD"] = array();
	}

	if(
		!array_key_exists("DELETE_OFFER_PROP", $_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]])
		|| !is_array($_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DELETE_OFFER_PROP"])
	)
	{
		$_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DELETE_OFFER_PROP"] = array();
	}

	$arResult["PRICES"] = CIBlockPriceTools::GetCatalogPrices($arParams["IBLOCK_ID"], $arParams["PRICE_CODE"]);
  
	$arConvertParams = array();
	if ('Y' == $arParams['CONVERT_CURRENCY'])
	{
		if (!CModule::IncludeModule('currency'))
		{
			$arParams['CONVERT_CURRENCY'] = 'N';
			$arParams['CURRENCY_ID'] = '';
		}
		else
		{
			$arCurrencyInfo = CCurrency::GetByID($arParams['CURRENCY_ID']);
			if (!(is_array($arCurrencyInfo) && !empty($arCurrencyInfo)))
			{
				$arParams['CONVERT_CURRENCY'] = 'N';
				$arParams['CURRENCY_ID'] = '';
			}
			else
			{
				$arParams['CURRENCY_ID'] = $arCurrencyInfo['CURRENCY'];
				$arConvertParams['CURRENCY_ID'] = $arCurrencyInfo['CURRENCY'];
			}
		}
	}

	$arResult['CONVERT_CURRENCY'] = $arConvertParams;

	$arOffers = CIBlockPriceTools::GetOffersIBlock($arParams["IBLOCK_ID"]);
	if($arOffers)
	{
		$arResult["OFFERS_IBLOCK_ID"] = $arOffers["OFFERS_IBLOCK_ID"];
		$arResult["OFFERS_PROPERTY_ID"] = $arOffers["OFFERS_PROPERTY_ID"];
	}
	else
	{
		$arResult["OFFERS_IBLOCK_ID"] = 0;
		$arResult["OFFERS_PROPERTY_ID"] = 0;
	}

	// list of the element fields that will be used in selection
	$arSelect = array(
		"ID",
			"NAME",
			"CODE",
			"ACTIVE_FROM",
			"ACTIVE_TO",
			"DATE_CREATE",
			"CREATED_BY",
			"IBLOCK_ID",
			"IBLOCK_SECTION_ID",
			"DETAIL_PAGE_URL",
			"LIST_PAGE_URL",
			"DETAIL_TEXT",
			"DETAIL_TEXT_TYPE",
			"DETAIL_PICTURE",
			"PREVIEW_TEXT",
			"PREVIEW_TEXT_TYPE",
			"PREVIEW_PICTURE",
			"TAGS",
			"PROPERTY_*",
	);
	$arFilter = array(
		"ID" => array_keys($arCompare), 
	);
        
    
//	if($arResult["OFFERS_IBLOCK_ID"] > 0)
//		$arFilter["IBLOCK_ID"] = array($arParams["IBLOCK_ID"], $arResult["OFFERS_IBLOCK_ID"]);
//	else
//		$arFilter["IBLOCK_ID"] = $arParams["IBLOCK_ID"];

//	$arPriceTypeID = array();
//	if (!$arParams["USE_PRICE_COUNT"])
//	{
//		foreach($arResult["PRICES"] as &$value)
//		{
//			$arSelect[] = $value["SELECT"];
//			$arFilter["CATALOG_SHOP_QUANTITY_".$value["ID"]] = $arParams["SHOW_PRICE_COUNT"];
//		}
//		if (isset($value))
//			unset($value);
//	}
//	else
//	{
//		foreach($arResult["PRICES"] as &$value)
//		{
//			$arPriceTypeID[] = $value["ID"];
//		}
//		if (isset($value))
//			unset($value);
//	}

	$arSort = array(
		$arParams["ELEMENT_SORT_FIELD"] => $arParams["ELEMENT_SORT_ORDER"],
		"ID" => "DESC",
	);
	//EXECUTE

	$rsElements = CIBlockElement::GetList($arSort, $arFilter, false, 
                false, array_merge($arSelect, $arParams["FIELD_CODE"]));
	$rsElements->SetUrlTemplates($arParams["DETAIL_URL"]);
	$arResult["DELETED_PROPERTIES"] = array();
	$arResult["SHOW_PROPERTIES"] = array();
	$arResult["DELETED_OFFER_FIELDS"] = array();
	$arResult["SHOW_OFFER_FIELDS"] = array();
	$arResult["DELETED_OFFER_PROPERTIES"] = array();
	$arResult["SHOW_OFFER_PROPERTIES"] = array();
	$arResult["ITEMS"] = array();
	while($obElement = $rsElements->GetNextElement())
	{ 
		$arItem = $obElement->GetFields();
 
                
                 $rsPrices = CPrice::GetList(array(),
                         array('PRODUCT_ID' => $arItem['ID'], 'CATALOG_GROUP_ID' => 2));
                 
                 $arItem['PRICE'] = $rsPrices->Fetch();
        
                 $arItem['PRICE']['PRICE'] = CurrencyFormat($arItem['PRICE']['PRICE'], "RUB");  
                       
                if(!isset($hidden_props)){
                   CModule::IncludeModule('tc'); 
                   $hidden_props = tcConfig::getHiddenProps($arItem['IBLOCK_ID']); 
                }
                 
		if($arItem["IBLOCK_ID"] == $arResult["OFFERS_IBLOCK_ID"])
		{ 
			 
			     $arItem["PROPERTIES"] = $obElement->GetProperties();

			$rsMasterProperty = CIBlockElement::GetProperty($arItem["IBLOCK_ID"], $arItem["ID"], array(), array("ID" => $arResult["OFFERS_PROPERTY_ID"], "EMPTY" => "N"));
			if($arMasterProperty = $rsMasterProperty->Fetch())
			{
				$rsMaster = CIBlockElement::GetList(
					array()
					,array(
						"ID" => $arMasterProperty["VALUE"],
						"IBLOCK_ID" => $arMasterProperty["LINK_IBLOCK_ID"],
						"ACTIVE" => "Y",
					)
					,false
					,false
					,array_merge($arSelect, $arParams["FIELD_CODE"])
				);
				$rsMaster->SetUrlTemplates($arParams["DETAIL_URL"]);
				$obElement = $rsMaster->GetNextElement();
				if(!is_object($obElement))
					continue; //There should be linked element
			}
			else
			{
				continue; //There should be linked element
			}

			$arOffer = $arItem;
			$arItem = $obElement->GetFields();
		}
		else
		{ 
			$arOffer = false;
		}

		$arItem["DETAIL_PICTURE"] = CFile::GetFileArray($arItem["DETAIL_PICTURE"]);
		$arItem["PREVIEW_PICTURE"] = CFile::GetFileArray($arItem["PREVIEW_PICTURE"]);

		$arItem["FIELDS"] = array();
		foreach($arParams["FIELD_CODE"] as $code)
			if(array_key_exists($code, $arItem))
				$arItem["FIELDS"][$code] = $arItem[$code];

		$arItem["OFFER_FIELDS"] = array();
		$arItem["OFFER_PROPERTIES"] = array();
		$arItem["OFFER_DISPLAY_PROPERTIES"] = array();
		if($arOffer)
		{
			foreach($arParams["OFFERS_FIELD_CODE"] as $code)
			{
				if(array_key_exists($code, $arOffer))
				{
					if(!array_key_exists($code, $_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DELETE_OFFER_FIELD"]))
						$arItem["OFFER_FIELDS"][$code] = $arOffer[$code];

					if(array_key_exists($code, $_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DELETE_OFFER_FIELD"]))
					{
						if(!array_key_exists($code, $arResult["DELETED_OFFER_FIELDS"]))
							$arResult["DELETED_OFFER_FIELDS"][$code] = $code;
					}
					else
					{
						if(!array_key_exists($code, $arResult["SHOW_OFFER_FIELDS"]))
							$arResult["SHOW_OFFER_FIELDS"][$code] = $code;
					}
				}
			}

		 
		}

		 
	        $arItem["PROPERTIES"] = $obElement->GetProperties();
 
                foreach($arItem["PROPERTIES"] as $key_ => $prop){
                    
                    if(in_array($key_, $hidden_props))
                            unset($arItem["PROPERTIES"][$key_]);
                          
                } 
                
                
		$arItem["DISPLAY_PROPERTIES"] = array();
 
		if($arOffer)
		{
			if($arParams["USE_PRICE_COUNT"])
			{
				if(CModule::IncludeModule("catalog"))
				{
					$arItem["PRICE_MATRIX"] = CatalogGetPriceTableEx($arOffer["ID"], 0, $arPriceTypeID, 'Y', $arConvertParams);
					foreach($arItem["PRICE_MATRIX"]["COLS"] as $keyColumn=>$arColumn)
						$arItem["PRICE_MATRIX"]["COLS"][$keyColumn]["NAME_LANG"] = htmlspecialcharsbx($arColumn["NAME_LANG"]);
				}
				else
				{
					$arItem["PRICE_MATRIX"] = false;
				}
				$arItem["PRICES"] = array();
			}
			else
			{
				$arItem["PRICE_MATRIX"] = false;
				$arItem["PRICES"] = CIBlockPriceTools::GetItemPrices($arOffer["IBLOCK_ID"], $arResult["PRICES"], $arOffer, $arParams["PRICE_VAT_INCLUDE"], $arConvertParams);
			}
			$arItem["CAN_BUY"] = CIBlockPriceTools::CanBuy($arParams["IBLOCK_ID"], $arResult["PRICES"], $arOffer);
		}
		else
		{
			if($arParams["USE_PRICE_COUNT"])
			{
				if(CModule::IncludeModule("catalog"))
				{
				 	$arItem["PRICE_MATRIX"] = CatalogGetPriceTableEx($arItem["ID"], 0, $arPriceTypeID, 'Y', $arConvertParams);
					foreach($arItem["PRICE_MATRIX"]["COLS"] as $keyColumn=>$arColumn)
						$arItem["PRICE_MATRIX"]["COLS"][$keyColumn]["NAME_LANG"] = htmlspecialcharsbx($arColumn["NAME_LANG"]);
				}
				else
				{
					$arItem["PRICE_MATRIX"] = false;
				}
				$arItem["PRICES"] = array();
			}
			else
			{
				$arItem["PRICE_MATRIX"] = false;
				$arItem["PRICES"] = CIBlockPriceTools::GetItemPrices($arItem["IBLOCK_ID"], $arResult["PRICES"], $arItem, $arParams["PRICE_VAT_INCLUDE"], $arConvertParams);
			}
			$arItem["CAN_BUY"] = CIBlockPriceTools::CanBuy($arParams["IBLOCK_ID"], $arResult["PRICES"], $arItem);
		}

		if($arOffer)
			$arItem["ID"] = $arOffer["ID"];

		$arItem["BUY_URL"] = htmlspecialcharsbx($APPLICATION->GetCurPageParam($arParams["ACTION_VARIABLE"]."=COMPARE_BUY&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arItem["ID"], array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"])));
		$arItem["ADD_URL"] = htmlspecialcharsbx($APPLICATION->GetCurPageParam($arParams["ACTION_VARIABLE"]."=COMPARE_ADD2BASKET&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arItem["ID"], array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"])));

 
		$arResult["ITEMS"][] = $arItem;
	}
        
   
        foreach($arResult["ITEMS"][0]['PROPERTIES'] as $prop_code => $prop_val){
   
            $empty = true;
            foreach($arResult['ITEMS'] as $item2){ 
               if($item2['PROPERTIES'][$prop_code]['VALUE'])
                  $empty = false;
            } 
             if($empty == true){
                foreach($arResult['ITEMS'] as &$item)
                    unset($item['PROPERTIES'][$prop_code]);
            } else { // ----- пустые убрали  , теперь ищем oдинаковые
                    $od = true;
                    $val = $arResult['ITEMS'][0]['PROPERTIES'][$prop_code]['VALUE'];
                    foreach($arResult['ITEMS'] as $item1){
                       if($item1['PROPERTIES'][$prop_code]['VALUE'] != $val)
                          $od = false;
                    }
                    if($od){
                       foreach($arResult['ITEMS'] as &$item_)
                            $item_['PROPERTIES'][$prop_code]['CHANGES'] = true;
                            } 
    
            }
 
        }
             
  
	$arResult["ITEMS_TO_ADD"] = array();
	if($arParams["DISPLAY_ELEMENT_SELECT_BOX"])
	{
		$arSelect = array(
			"ID",
			"NAME",
		);
		$arFilter = array(
			"!"."ID" => array_keys($arCompare),
			"IBLOCK_LID" => SITE_ID,
			"IBLOCK_ACTIVE" => "Y",
			"ACTIVE_DATE" => "Y",
			"ACTIVE" => "Y",
			"CHECK_PERMISSIONS" => "Y",
		);

		if($arResult["OFFERS_IBLOCK_ID"] > 0)
		{
			$arFilter["IBLOCK_ID"] = array($arParams["IBLOCK_ID"], $arResult["OFFERS_IBLOCK_ID"]);
			$arFilter["!=ID"] = CIBlockElement::SubQuery("PROPERTY_".$arResult["OFFERS_PROPERTY_ID"], array(
				"IBLOCK_ID" => $arResult["OFFERS_IBLOCK_ID"]
			));
		}
		else
		{
			$arFilter["IBLOCK_ID"] = $arParams["IBLOCK_ID"];
		}

		$arSort = array(
			$arParams["ELEMENT_SORT_FIELD_BOX"] => $arParams["ELEMENT_SORT_ORDER_BOX"],
			"ID" => "DESC",
		);
		$rsElements = CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);
		while($arElement = $rsElements->GetNext())
		{
			$arResult["ITEMS_TO_ADD"][$arElement["ID"]]=$arElement["NAME"];
		}
	}
       
	$this->IncludeComponentTemplate();     
}elseif(count($arCompare)==1){
         
    $res = CIBlockSection::GetByID($_SESSION["CATALOG_COMPARE_LIST"]['SECTION_ID']);
    if($ar_res = $res->GetNext())
       LocalRedirect($ar_res["SECTION_PAGE_URL"]);
    else
       ShowNote("Для сравнения необходимо выбрать больше одного товара");
    
}
else
{
	ShowNote(GetMessage("CATALOG_COMPARE_LIST_EMPTY"));
}
