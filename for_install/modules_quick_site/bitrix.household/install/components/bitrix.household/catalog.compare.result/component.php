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
unset($arParams["IBLOCK_TYPE"]); //was used only for IBLOCK_ID setup with Editor
$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);

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

if(!is_array($arParams["PROPERTY_CODE"]))
	$arParams["PROPERTY_CODE"] = array();
foreach($arParams["PROPERTY_CODE"] as $k=>$v)
	if($v==="")
		unset($arParams["PROPERTY_CODE"][$k]);

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

if(!is_array($arParams["OFFERS_PROPERTY_CODE"]))
	$arParams["OFFERS_PROPERTY_CODE"] = array();
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

/*************************************************************************
			Handling the Compare button
*************************************************************************/
if(isset($_REQUEST["action"]))
{
	switch($_REQUEST["action"])
	{
		case "ADD_TO_COMPARE_RESULT":
			if(
				intval($_REQUEST["id"]) > 0
				&& !array_key_exists($_REQUEST["id"], $_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["ITEMS"])
			)
			{
				$arOffers = CIBlockPriceTools::GetOffersIBlock($arParams["IBLOCK_ID"]);
				$OFFERS_IBLOCK_ID = $arOffers? $arOffers["OFFERS_IBLOCK_ID"]: 0;

				//SELECT
				$arSelect = array(
					"ID",
					"IBLOCK_ID",
					"IBLOCK_SECTION_ID",
					"NAME",
					"DETAIL_PAGE_URL",
				);
				//WHERE
				$arFilter = array(
					"ID" => intval($_REQUEST["id"]),
					"IBLOCK_ID" => $arParams["IBLOCK_ID"],
					"IBLOCK_LID" => SITE_ID,
					"IBLOCK_ACTIVE" => "Y",
					"ACTIVE_DATE" => "Y",
					"ACTIVE" => "Y",
					"CHECK_PERMISSIONS" => "Y",
				);
				if($OFFERS_IBLOCK_ID > 0)
					$arFilter["IBLOCK_ID"] = array($arParams["IBLOCK_ID"], $OFFERS_IBLOCK_ID);
				else
					$arFilter["IBLOCK_ID"] = $arParams["IBLOCK_ID"];

				$rsElement = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
				$rsElement->SetUrlTemplates($arParams["DETAIL_URL"]);
				$arElement = $rsElement->GetNext();

				$arMaster = false;
				if($arElement && $arElement["IBLOCK_ID"] == $OFFERS_IBLOCK_ID)
				{
					$rsMasterProperty = CIBlockElement::GetProperty($arElement["IBLOCK_ID"], $arElement["ID"], array(), array("ID" => $arOffers["OFFERS_PROPERTY_ID"], "EMPTY" => "N"));
					if($arMasterProperty = $rsMasterProperty->Fetch())
					{
						$rsMaster = CIBlockElement::GetList(
							array()
							,array(
								"ID" => $arMasterProperty["VALUE"],
								"IBLOCK_ID" => $arMasterProperty["LINK_IBLOCK_ID"],
								"ACTIVE" => "Y",
							)
						,false, false, $arSelect);
						$rsMaster->SetUrlTemplates($arParams["DETAIL_URL"]);
						$arMaster = $rsMaster->GetNext();
					}
				}

				if($arMaster)
				{
					$arMaster["NAME"] = $arElement["NAME"];
					$arMaster["DELETE_URL"] = htmlspecialchars($APPLICATION->GetCurPageParam("action=DELETE_FROM_COMPARE_RESULT&id=".$arMaster["ID"], array("action", "id")));
					$_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["ITEMS"][$_REQUEST["id"]] = $arMaster;
				}
				elseif($arElement)
				{
					$arElement["DELETE_URL"] = htmlspecialchars($APPLICATION->GetCurPageParam("action=DELETE_FROM_COMPARE_RESULT&id=".$arElement["ID"], array("action", "id")));
					$_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["ITEMS"][$_REQUEST["id"]] = $arElement;
				}
			}
			break;
		case "DELETE_FROM_COMPARE_RESULT":
			foreach($arID as $ID)
				unset($_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["ITEMS"][$ID]);
			break;
		case "ADD_FEATURE":
			foreach($arPR as $ID)
				unset($_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DELETE_PROP"][$ID]);

			foreach($arOF as $ID)
				unset($_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DELETE_OFFER_FIELD"][$ID]);

			foreach($arOP as $ID)
				unset($_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DELETE_OFFER_PROP"][$ID]);
			break;
		case "DELETE_FEATURE":
			foreach($arPR as $ID)
				$_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DELETE_PROP"][$ID]=true;

			foreach($arOF as $ID)
				$_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DELETE_OFFER_FIELD"][$ID]=true;

			foreach($arOP as $ID)
				$_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DELETE_OFFER_PROP"][$ID]=true;
			break;
	}
}

if(!isset($_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DIFFERENT"]))
	$_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DIFFERENT"] = false;
if(isset($_REQUEST["DIFFERENT"]))
	$_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DIFFERENT"] = $_REQUEST["DIFFERENT"]=="Y";
$arResult["DIFFERENT"] = $_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DIFFERENT"];

/*************************************************************************
			Processing of the Buy link
*************************************************************************/
$strError = "";
if (array_key_exists($arParams["ACTION_VARIABLE"], $_REQUEST) && array_key_exists($arParams["PRODUCT_ID_VARIABLE"], $_REQUEST))
{
	if(array_key_exists($arParams["ACTION_VARIABLE"]."ADD2BASKET", $_REQUEST))
		$action = "ADD2BASKET";
		
	else $action = strtoupper($_REQUEST[$arParams["ACTION_VARIABLE"]]);
	
	$productID = intval($_REQUEST[$arParams["PRODUCT_ID_VARIABLE"]]);
	
	if(($action == "ADD2BASKET" || $action == "BUY") && $productID > 0)
	{
		if(CModule::IncludeModule("iblock") && CModule::IncludeModule("sale") && CModule::IncludeModule("catalog"))
		{
			if($arParams["USE_PRODUCT_QUANTITY"])
				$QUANTITY = intval($_POST[$arParams["PRODUCT_QUANTITY_VARIABLE"]]);
			if($QUANTITY <= 1)
				$QUANTITY = 1;

			$product_properties = array();
			if(count($arParams["PRODUCT_PROPERTIES"]))
			{
				if(is_array($_POST[$arParams["PRODUCT_PROPS_VARIABLE"]]))
				{
					$product_properties = CIBlockPriceTools::CheckProductProperties(
						$arParams["IBLOCK_ID"],
						$productID,
						$arParams["PRODUCT_PROPERTIES"],
						$_POST[$arParams["PRODUCT_PROPS_VARIABLE"]]
					);
					if(!is_array($product_properties))
						$strError = GetMessage("CATALOG_ERROR2BASKET").".";
				}
				else
				{
					$strError = GetMessage("CATALOG_ERROR2BASKET").".";
				}
			}

			if(!$strError && Add2BasketByProductID($productID, $QUANTITY, $product_properties))
			{
				if($action == "BUY")
					LocalRedirect($arParams["BASKET_URL"]);
				else
					LocalRedirect($APPLICATION->GetCurPageParam("", array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"])));
			}
			else
			{
				if($ex = $GLOBALS["APPLICATION"]->GetException())
					$strError = $ex->GetString();
				else
					$strError = GetMessage("CATALOG_ERROR2BASKET").".";
			}
		}
	}
	
	else if (($action == "COMPARE_ADD2BASKET" || $action == "COMPARE_BUY") && $productID > 0)
	{
		if (CModule::IncludeModule("sale") && CModule::IncludeModule("catalog"))
		{
			$QUANTITY = 1;
			$product_properties = array();
			if(is_array($arParams["OFFERS_CART_PROPERTIES"]))
			{
				foreach($arParams["OFFERS_CART_PROPERTIES"] as $i => $pid)
					if($pid === "")
						unset($arParams["OFFERS_CART_PROPERTIES"][$i]);

				if(!empty($arParams["OFFERS_CART_PROPERTIES"]))
				{
					$product_properties = CIBlockPriceTools::GetOfferProperties(
						$productID,
						$arParams["IBLOCK_ID"],
						$arParams["OFFERS_CART_PROPERTIES"]
					);
				}
			}

			if (Add2BasketByProductID($productID, $QUANTITY, $product_properties))
			{
				if ($action == "COMPARE_BUY" || $action == "COMPARE_ADD2BASKET")
					LocalRedirect($arParams["BASKET_URL"]);
				else
					LocalRedirect($APPLICATION->GetCurPageParam("", array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"])));
			}
			else
			{
				if ($ex = $GLOBALS["APPLICATION"]->GetException())
					$strError = $ex->GetString();
				else
					$strError = GetMessage("CATALOG_ERROR2BASKET").".";
			}
		}
	}
}
if(strlen($strError)>0)
{
	ShowError($strError);
	return;
}

$arCompare = $_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["ITEMS"];
if(is_array($arCompare) && count($arCompare)>0)
{
	//$_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DELETE_PROP"] expected to be an array
	if(
		!array_key_exists("DELETE_PROP", $_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]])
		|| !is_array($_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DELETE_PROP"])
	)
	{
		$_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DELETE_PROP"] = array();
	}

	//$_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DELETE_OFFER_FIELD"] expected to be an array
	if(
		!array_key_exists("DELETE_OFFER_FIELD", $_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]])
		|| !is_array($_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DELETE_OFFER_FIELD"])
	)
	{
		$_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DELETE_OFFER_FIELD"] = array();
	}

	//$_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DELETE_OFFER_PROP"] expected to be an array
	if(
		!array_key_exists("DELETE_OFFER_PROP", $_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]])
		|| !is_array($_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DELETE_OFFER_PROP"])
	)
	{
		$_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DELETE_OFFER_PROP"] = array();
	}

	//This function returns array with prices description and access rights
	//in case catalog module n/a prices get values from element properties
	$arResult["PRICES"] = CIBlockPriceTools::GetCatalogPrices($arParams["IBLOCK_ID"], $arParams["PRICE_CODE"]);

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
		"IBLOCK_ID",
		"IBLOCK_SECTION_ID",
		"DETAIL_PAGE_URL",
		"PREVIEW_TEXT_TYPE",
		"DETAIL_TEXT_TYPE",
		"PROPERTY_*",
	);
	$arFilter = array(
		"ID" => array_keys($arCompare),
		"IBLOCK_LID" => SITE_ID,
		"IBLOCK_ACTIVE" => "Y",
		"ACTIVE_DATE" => "Y",
		"ACTIVE" => "Y",
		"CHECK_PERMISSIONS" => "Y",
	);
	if($arResult["OFFERS_IBLOCK_ID"] > 0)
		$arFilter["IBLOCK_ID"] = array($arParams["IBLOCK_ID"], $arResult["OFFERS_IBLOCK_ID"]);
	else
		$arFilter["IBLOCK_ID"] = $arParams["IBLOCK_ID"];

	if(!$arParams["USE_PRICE_COUNT"])
	{
		foreach($arResult["PRICES"] as $key => $value)
		{
			$arSelect[] = $value["SELECT"];
			$arFilter["CATALOG_SHOP_QUANTITY_".$value["ID"]] = $arParams["SHOW_PRICE_COUNT"];
		}
	}
	$arSort = array(
		$arParams["ELEMENT_SORT_FIELD"] => $arParams["ELEMENT_SORT_ORDER"],
		"ID" => "DESC",
	);
	//EXECUTE
	$rsElements = CIBlockElement::GetList($arSort, $arFilter, false, false, array_merge($arSelect, $arParams["FIELD_CODE"]));
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

		if($arItem["IBLOCK_ID"] == $arResult["OFFERS_IBLOCK_ID"])
		{
			if(count($arParams["OFFERS_PROPERTY_CODE"]) > 0)
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

			$arItem["OFFER_PROPERTIES"] = $arOffer["PROPERTIES"];
			foreach($arParams["OFFERS_PROPERTY_CODE"] as $pid)
			{
				if(!array_key_exists($pid, $_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DELETE_OFFER_PROP"]))
					$arItem["OFFER_DISPLAY_PROPERTIES"][$pid] = CIBlockFormatProperties::GetDisplayValue($arOffer, $arOffer["PROPERTIES"][$pid], "catalog_out");

				if(array_key_exists($pid, $_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DELETE_OFFER_PROP"]))
				{
					if(!array_key_exists($pid, $arResult["DELETED_OFFER_PROPERTIES"]))
						$arResult["DELETED_OFFER_PROPERTIES"][$pid] = $arOffer["PROPERTIES"][$pid];
				}
				else
				{
					if(!array_key_exists($pid, $arResult["SHOW_OFFER_PROPERTIES"]))
						$arResult["SHOW_OFFER_PROPERTIES"][$pid] = $arOffer["PROPERTIES"][$pid];
				}
			}
		}

		if(count($arParams["PROPERTY_CODE"]) > 0)
			$arItem["PROPERTIES"] = $obElement->GetProperties();

		$arItem["DISPLAY_PROPERTIES"] = array();
		foreach($arParams["PROPERTY_CODE"] as $pid)
		{
			if(!array_key_exists($pid, $_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DELETE_PROP"]))
				$arItem["DISPLAY_PROPERTIES"][$pid] = CIBlockFormatProperties::GetDisplayValue($arItem, $arItem["PROPERTIES"][$pid], "catalog_out");

			if(array_key_exists($pid, $_SESSION[$arParams["NAME"]][$arParams["IBLOCK_ID"]]["DELETE_PROP"]))
			{
				if(!array_key_exists($pid, $arResult["DELETED_PROPERTIES"]))
				{
					$arResult["DELETED_PROPERTIES"][$pid] = $arItem["PROPERTIES"][$pid];
				}
			}
			else
			{
				if(!array_key_exists($pid, $arResult["SHOW_PROPERTIES"]))
				{
					$arResult["SHOW_PROPERTIES"][$pid] = $arItem["DISPLAY_PROPERTIES"][$pid];
				}
			}
		}

		if($arOffer)
		{
			if($arParams["USE_PRICE_COUNT"])
			{
				if(CModule::IncludeModule("catalog"))
				{
					$arItem["PRICE_MATRIX"] = CatalogGetPriceTableEx($arOffer["ID"]);
					foreach($arItem["PRICE_MATRIX"]["COLS"] as $keyColumn=>$arColumn)
						$arItem["PRICE_MATRIX"]["COLS"][$keyColumn]["NAME_LANG"] = htmlspecialchars($arColumn["NAME_LANG"]);
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
				$arItem["PRICES"] = CIBlockPriceTools::GetItemPrices($arOffer["IBLOCK_ID"], $arResult["PRICES"], $arOffer, $arParams["PRICE_VAT_INCLUDE"]);
			}
			$arItem["CAN_BUY"] = CIBlockPriceTools::CanBuy($arParams["IBLOCK_ID"], $arResult["PRICES"], $arOffer);
		}
		else
		{
			if($arParams["USE_PRICE_COUNT"])
			{
				if(CModule::IncludeModule("catalog"))
				{
					$arItem["PRICE_MATRIX"] = CatalogGetPriceTableEx($arItem["ID"]);
					foreach($arItem["PRICE_MATRIX"]["COLS"] as $keyColumn=>$arColumn)
						$arItem["PRICE_MATRIX"]["COLS"][$keyColumn]["NAME_LANG"] = htmlspecialchars($arColumn["NAME_LANG"]);
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
				$arItem["PRICES"] = CIBlockPriceTools::GetItemPrices($arItem["IBLOCK_ID"], $arResult["PRICES"], $arItem, $arParams["PRICE_VAT_INCLUDE"]);
			}
			$arItem["CAN_BUY"] = CIBlockPriceTools::CanBuy($arParams["IBLOCK_ID"], $arResult["PRICES"], $arItem);
		}

		if($arOffer)
			$arItem["ID"] = $arOffer["ID"];

		$arItem["BUY_URL"] = htmlspecialchars($APPLICATION->GetCurPageParam($arParams["ACTION_VARIABLE"]."=COMPARE_BUY&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arItem["ID"], array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"])));
		$arItem["ADD_URL"] = htmlspecialchars($APPLICATION->GetCurPageParam($arParams["ACTION_VARIABLE"]."=COMPARE_ADD2BASKET&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arItem["ID"], array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"])));
		
		$arItem["ADD_URL"] = htmlspecialchars($APPLICATION->GetCurPageParam($arParams["ACTION_VARIABLE"]."=ADD2BASKET&".$arParams["PRODUCT_ID_VARIABLE"]."=".$arItem["ID"], array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"])));


		$arResult["ITEMS"][] = $arItem;
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
	//echo "<pre>",htmlspecialchars(print_r($arResult,true)),"</pre>";
	$this->IncludeComponentTemplate();
}
else
{
	ShowNote(GetMessage("CATALOG_COMPARE_LIST_EMPTY"));
}
?>
