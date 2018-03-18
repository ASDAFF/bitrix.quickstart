<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock")){
	ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
	return;
}

if(!CModule::IncludeModule("klondike.pslider")) {
	$this->AbortResultCache();
	ShowError("KLONDIKE_PSLIDER_MODULE_NOT_INSTALLED");
	return false;
}

$arParams["PREVIEW_TRUNCATE_LEN"]   = intval($arParams["PREVIEW_TRUNCATE_LEN"]);
$arParams["INTERVAL"]               = intval($arParams["INTERVAL"])*1000;

if(!isset($arParams["COLOR_SCHEME"])){
	$arParams["COLOR_SCHEME"] = 'pink';
}

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 3600;

if (empty($arParams['IBLOCK_ID']))
{
	$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arParams["IBLOCK_TYPE_ID"], "ACTIVE"=>"Y", "IBLOCK_LID"=>SITE_ID));
	if($arr=$rsIBlock->Fetch())
		$arParams['IBLOCK_ID'] = $arr["ID"];
}

if(strlen($arParams["ELEMENT_SORT_FIELD"])<=0)
	$arParams["ELEMENT_SORT_FIELD"]="sort";
if($arParams["ELEMENT_SORT_ORDER"]!="desc")
	$arParams["ELEMENT_SORT_ORDER"]="asc";

$arParams["SECTION_URL"]=trim($arParams["SECTION_URL"]);
$arParams["DETAIL_URL"]=trim($arParams["DETAIL_URL"]);
$arParams["BASKET_URL"]=trim($arParams["BASKET_URL"]);
if(strlen($arParams["BASKET_URL"])<=0)
	$arParams["BASKET_URL"] = "/personal/basket.php";

if(strlen($arParams["DISPLAY_IMG_WIDTH"])<=0)
	$arParams["DISPLAY_IMG_WIDTH"] = "75";
if(strlen($arParams["DISPLAY_IMG_WIDTH"])<=0)
	$arParams["DISPLAY_IMG_WIDTH"] = "225";
if(strlen($arParams["SHARPEN"])<=0)
	$arParams["SHARPEN"] = "30";

$arParams["ACTION_VARIABLE"]=trim($arParams["ACTION_VARIABLE"]);
if(strlen($arParams["ACTION_VARIABLE"])<=0|| !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["ACTION_VARIABLE"]))
	$arParams["ACTION_VARIABLE"] = "action";

$arParams["PRODUCT_ID_VARIABLE"]=trim($arParams["PRODUCT_ID_VARIABLE"]);
if(strlen($arParams["PRODUCT_ID_VARIABLE"])<=0|| !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["PRODUCT_ID_VARIABLE"]))
	$arParams["PRODUCT_ID_VARIABLE"] = "id";

$arParams["PRODUCT_QUANTITY_VARIABLE"]=trim($arParams["PRODUCT_QUANTITY_VARIABLE"]);
if(strlen($arParams["PRODUCT_QUANTITY_VARIABLE"])<=0|| !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["PRODUCT_QUANTITY_VARIABLE"]))
	$arParams["PRODUCT_QUANTITY_VARIABLE"] = "quantity";

$arParams["PRODUCT_PROPS_VARIABLE"]=trim($arParams["PRODUCT_PROPS_VARIABLE"]);
if(strlen($arParams["PRODUCT_PROPS_VARIABLE"])<=0|| !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["PRODUCT_PROPS_VARIABLE"]))
	$arParams["PRODUCT_PROPS_VARIABLE"] = "prop";

$arParams["SECTION_ID_VARIABLE"]=trim($arParams["SECTION_ID_VARIABLE"]);
if(strlen($arParams["SECTION_ID_VARIABLE"])<=0|| !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["SECTION_ID_VARIABLE"]))
	$arParams["SECTION_ID_VARIABLE"] = "SECTION_ID";

$arParams["SET_TITLE"] = $arParams["SET_TITLE"]!="N";
$arParams["DISPLAY_PANEL"] = $arParams["DISPLAY_PANEL"]=="Y";


$arParams["ELEMENT_COUNT"] = intval($arParams["ELEMENT_COUNT"]);
if($arParams["ELEMENT_COUNT"]<=0)
	$arParams["ELEMENT_COUNT"]=9;


if(!is_array($arParams["PRICE_CODE"]))
	$arParams["PRICE_CODE"] = array();

//$arParams["USE_PRICE_COUNT"] = $arParams["USE_PRICE_COUNT"]=="Y";
$arParams["SHOW_PRICE_COUNT"] = intval($arParams["SHOW_PRICE_COUNT"]);
if($arParams["SHOW_PRICE_COUNT"]<=0)
	$arParams["SHOW_PRICE_COUNT"]=1;
$arParams["USE_PRODUCT_QUANTITY"] = $arParams["USE_PRODUCT_QUANTITY"]==="Y";

if(!is_array($arParams["PRODUCT_PROPERTIES"]))
	$arParams["PRODUCT_PROPERTIES"] = array();
foreach($arParams["PRODUCT_PROPERTIES"] as $k=>$v)
	if($v==="")
		unset($arParams["PRODUCT_PROPERTIES"][$k]);

$arParams["PRICE_VAT_INCLUDE"] = $arParams["PRICE_VAT_INCLUDE"] !== "N";

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

if(strlen($arParams["FILTER_NAME"])>0)
{
	global ${$arParams["FILTER_NAME"]};
	$arrFilter = ${$arParams["FILTER_NAME"]};
}
if(!is_array($arrFilter))
	$arrFilter=array();

$arParams["CACHE_FILTER"]=$arParams["CACHE_FILTER"]=="Y";
if(!$arParams["CACHE_FILTER"] && count($arrFilter)>0)
	$arParams["CACHE_TIME"] = 0;


/*************************************************************************
Processing of the Buy link
 *************************************************************************/
$strError = "";
if(array_key_exists($arParams["ACTION_VARIABLE"], $_REQUEST) && array_key_exists($arParams["PRODUCT_ID_VARIABLE"], $_REQUEST))
{
	if(array_key_exists($arParams["ACTION_VARIABLE"]."BUY", $_REQUEST))
		$action = "BUY";
	elseif(array_key_exists($arParams["ACTION_VARIABLE"]."ADD2BASKET", $_REQUEST))
		$action = "ADD2BASKET";
	else
		$action = strtoupper($_REQUEST[$arParams["ACTION_VARIABLE"]]);

	$productID = intval($_REQUEST[$arParams["PRODUCT_ID_VARIABLE"]]);
	if (($action == "ADD2BASKET" || $action == "BUY" || $action == "SUBSCRIBE_PRODUCT") && $productID > 0)
	{
		if (CModule::IncludeModule("sale") && CModule::IncludeModule("catalog"))
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

			$arRewriteFields = array();
			$notifyOption = COption::GetOptionString("sale", "subscribe_prod", "");
			$arNotify = unserialize($notifyOption);

			if ($action == "SUBSCRIBE_PRODUCT" && $arNotify[SITE_ID]['use'] == 'Y')
			{
				$arRewriteFields["SUBSCRIBE"] = "Y";
				$arRewriteFields["CAN_BUY"] = "N";
			}

			if(!$strError && Add2BasketByProductID($productID, $QUANTITY, $arRewriteFields, $product_properties))
			{
				if ($action == "BUY")
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

if($arParams['IBLOCK_ID'] > 0)
{
	$arrFilter["IBLOCK_ID"] = $arParams["IBLOCK_ID"];

	$arFilter = Array("ID" => $arParams['IBLOCK_ID'],"TYPE"=>"catalog", "SITE_ID"=>SITE_ID);
	$obCache = new CPHPCache;
	if($obCache->InitCache(36000, serialize($arFilter), "/iblock/catalog/top"))
	{
		$arIblock = $obCache->GetVars();
	}
	else
	{
		$arIBlock = array();
		$dbRes = CIBlock::GetList(Array(), $arFilter);
		$dbRes = new CIBlockResult($dbRes);

		if(defined("BX_COMP_MANAGED_CACHE"))
		{
			global $CACHE_MANAGER;
			$CACHE_MANAGER->StartTagCache("/iblock/catalog/top");

			if ($arIBlock = $dbRes->GetNext())
			{
				$CACHE_MANAGER->RegisterTag("iblock_id_".$arParams["IBLOCK_ID"]);
			}
			$CACHE_MANAGER->EndTagCache();
		}
		else
		{
			if(!$arIBlock = $dbRes->GetNext())
				$arIBlock = array();
		}

		$obCache->EndDataCache($arIBlock);
	}
}
else
{
	ShowError("IBLOCK_ERROR");
	return;
}




if($this->StartResultCache(false, array($arrFilter, ($arParams["CACHE_GROUPS"]==="N"? false: $USER->GetGroups())))){


	$arResult = CKlondikePslider::getData($arParams, $arrFilter);

	$this->SetResultCacheKeys(array("IDS"));
	$this->IncludeComponentTemplate();
}

CKlondikePslider::includeLibs($arParams);



?>