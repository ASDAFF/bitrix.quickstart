<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule("iblock"))
{
	ShowError(GetMessage("SIM_SALE_MODULE_NOT_INSTALL"));
	return;
}
if (!CModule::IncludeModule("sale"))
{
	ShowError(GetMessage("SIM_SALE_MODULE_NOT_INSTALL"));
	return;
}
if (!CModule::IncludeModule("catalog"))
{
	ShowError(GetMessage("SIM_CATALOG_MODULE_NOT_INSTALL"));
	return;
}

global $USER, $APPLICATION;


//select price type
function getPriceTypes($ELEMENT_ID, $PRICE_CODE)
{
	$arFilter = array("ID"=>$ELEMENT_ID,"ACTIVE"=>"Y");		
	$resPrice = CIBlockElement::GetList(array(), $arFilter, false, false, array("IBLOCK_ID"));
	if ($arPrice = $resPrice->Fetch())
	{
		$arResultPrices = CIBlockPriceTools::GetCatalogPrices($arPrice["IBLOCK_ID"], $PRICE_CODE);
		
		return $arResultPrices;
	}
	else
		return false;
}


$arParams["AUCTION_IBLOCK_ID"] = intval($arParams["AUCTION_IBLOCK_ID"]);
$arParams["BETS_IBLOCK_ID"] = intval($arParams["BETS_IBLOCK_ID"]);

$arParams["ELEMENT_ID"] = intval($arParams["ELEMENT_ID"]);
if($arParams["ELEMENT_ID"] <= 0 || $arParams["ELEMENT_ID"]."" != $arParams["~ELEMENT_ID"])
{
	ShowError(GetMessage("SIM_ELEMENT_NOT_FOUND"));
	@define("ERROR_404", "Y");
	if($arParams["SET_STATUS_404"]==="Y")
		CHTTP::SetStatus("404 Not Found");
	return;
}

$arParams["IBLOCK_URL"] = trim($arParams["IBLOCK_URL"]);
if (strlen($arParams["IBLOCK_URL"]) <= 0)
	$arParams["IBLOCK_URL"] = $APPLICATION->GetCurDir();
	
$arParams["AUCTION_JQUERY"] = ($arParams["AUCTION_JQUERY"] != "Y")?"N":"Y";
$arParams["COUNT_BETS"] = intval($arParams["COUNT_BETS"]);
$arParams["AUCTION_HIDE"] = ($arParams["AUCTION_HIDE"] == "Y" ? 'Y' : 'N');
$arParams["AUCTION_PERMISSIONS"] = ($arParams["AUCTION_PERMISSIONS"] != "Y")?"N":"Y";
$arParams["AUCTION_SET_TITLE"] = ($arParams["AUCTION_SET_TITLE"] != "Y")?"N":"Y";
$arParams["AUCTION_EDIT_PRICE"] = ($arParams["AUCTION_EDIT_PRICE"] != "Y")?"N":"Y";
$arParams["AUCTION_DOUBLE_BETS"] = ($arParams["AUCTION_DOUBLE_BETS"] != "Y")?"N":"Y";
$arParams["AUCTION_PRICE_CONFIRM"] = ($arParams["AUCTION_PRICE_CONFIRM"] != "Y")?"N":"Y";
$arParams["AUCTION_BUY_LOT"] = ($arParams["AUCTION_BUY_LOT"] != "Y")?"N":"Y";
$arParams["AUCTION_CHAT"] = ($arParams["AUCTION_CHAT"] != "Y")?"N":"Y";
$arParams["AUCTION_EXTEND"] = intval($arParams["AUCTION_EXTEND"]);
$arParams["AUCTION_INTERVAL"] = intval($arParams["AUCTION_INTERVAL"]);
$arParams["AUCTION_MAX_BUY"] = floatval($arParams["AUCTION_MAX_BUY"]);

if(!is_array($arParams["PRICE_CODE"]) && strlen($arParams["PRICE_CODE"]) > 0)
	$arParams["PRICE_CODE"] = str_split($arParams["PRICE_CODE"], strlen($arParams["PRICE_CODE"]));
else
	$arParams["PRICE_CODE"] = array();

//select price type
/*$arFilter = array("ID"=>$arParams["ELEMENT_ID"],"ACTIVE"=>"Y");		
$resPrice = CIBlockElement::GetList(array(), $arFilter, false, false, array("IBLOCK_ID"));
$arPrice = $resPrice->Fetch();
$arResultPrices = CIBlockPriceTools::GetCatalogPrices($arPrice["IBLOCK_ID"], $arParams["PRICE_CODE"]);
*/

$arParams["AVATAR_WIDTH"] = intval($arParams["AVATAR_WIDTH"]);
$arParams["AVATAR_HEIGHT"] = intval($arParams["AVATAR_HEIGHT"]);
if ($arParams["AVATAR_WIDTH"] <= 0)
	$arParams["AVATAR_WIDTH"] = 50;
if ($arParams["AVATAR_HEIGHT"] <= 0)
	$arParams["AVATAR_HEIGHT"] = 50;

$arParams["IMAGE_WIDTH"] = intval($arParams["IMAGE_WIDTH"]);
$arParams["IMAGE_HEIGHT"] = intval($arParams["IMAGE_HEIGHT"]);
if ($arParams["IMAGE_WIDTH"] <= 0)
	$arParams["IMAGE_WIDTH"] = 250;
if ($arParams["IMAGE_HEIGHT"] <= 0)
	$arParams["IMAGE_HEIGHT"] = 250;

if ($arParams["AUCTION_JQUERY"] === "Y")
	CJSCore::Init(array("jquery"));

$arResult = array();
$CURRENCY = COption::GetOptionString("sale", "CURRENCY_DEFAULT", "RUB");
$arResult["AUCTION"] = array();
$arResult["MESSAGE"] = array();
$arResult["ERROR"] = array();

$cache = new CPHPCache();
$cacheId = 'auctionID'.$arParams["ELEMENT_ID"];
$cachePath = '/auction/';

// Check access
if ($arParams["AUCTION_PERMISSIONS"] === "Y")
	$auctionAccess = CIBlock::GetPermission($arParams["AUCTION_IBLOCK_ID"]);
else
	$auctionAccess = "W";
	
//Add user bet
if ($_SERVER["REQUEST_METHOD"] == "POST" && is_set($_POST["BTN_USER"]) && $USER->IsAuthorized() && check_bitrix_sessid())
{
	$arResultPrices = array();
	$userBets = trim($_POST["USER_BETS"]);
	$auctionId = $arParams["ELEMENT_ID"];
	$productId = 0;
	$userBets = floatval(str_replace(",", ".", $userBets));

	if (empty($arResult["ERROR"]))
	{
		if ($arParams["AUCTION_EDIT_PRICE"] === "Y" && $userBets <= 0)
		{
			$arResult["ERROR"][] = GetMessage('SIM_BETS_NULL');
		}
		else
		{
			//select auction params
			$arSelect = array("ID","ACTIVE_FROM","ACTIVE_TO","PROPERTY_BETS","PROPERTY_PRICE_BEGIN","PROPERTY_PRODUCTS");
			$arFilter = array(
				"IBLOCK_ID"=>$arParams["AUCTION_IBLOCK_ID"],    
				"ACTIVE"=>"Y",
				"ID"=>$auctionId,
				"CHECK_PERMISSIONS" => $arParams["AUCTION_PERMISSIONS"],
				">DATE_ACTIVE_TO" => ConvertTimeStamp(false, "FULL"),
			);
			$res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
			$bets = 0;
			$beginPrice = 0;
			$auctionDateTo = 0;
			$auctionDateFrom = 0;
			if ($arFields = $res->Fetch())
			{
				$bets = floatval($arFields["PROPERTY_BETS_VALUE"]);
				$beginPrice = floatval($arFields["PROPERTY_PRICE_BEGIN_VALUE"]);
				$auctionDateFrom = $arFields["ACTIVE_FROM"];
				$auctionDateTo = $arFields["ACTIVE_TO"];
				$productId = intval($arFields["PROPERTY_PRODUCTS_VALUE"]);
				$arResultPrices = getPriceTypes($productId, $arParams["PRICE_CODE"]);
			}
			else
				$arResult["ERROR"][] = GetMessage('SIM_ERR_AUCTION');
			
			$dateUnixFrom = MakeTimeStamp($auctionDateFrom, CSite::GetDateFormat("FULL"));
			$dateUnixTo = MakeTimeStamp($auctionDateTo, CSite::GetDateFormat("FULL"));
			
			if ($dateUnixFrom > time())
				$arResult["ERROR"][] = str_replace("#TIME#", date("d.m.Y H:i", $dateUnixFrom), GetMessage('SIM_ERR_AUCTION_BEGIN'));
			
			if ($dateUnixTo <= time())
				$arResult["ERROR"][] = GetMessage('SIM_AUCTION_BET_NO');
			
			//select price
			$lotPrice = 0;
			if (is_array($arResultPrices) && count($arResultPrices) > 0)
			{
				$priceType = "";
				$arSelect = array("ID","IBLOCK_ID","NAME","ACTIVE");
				
				foreach($arResultPrices as $key => $value)
				{
					$arSelect[] = $value["SELECT"];
					$priceType = $key;
				}
				
				$arFilter = array("ID"=>$productId, "ACTIVE"=>"Y");		
			
				$resLot = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
				$arLot = $resLot->Fetch();
				
				$arPrice = CIBlockPriceTools::GetItemPrices(1, $arResultPrices, $arLot, false);
				
				if ($arPrice[$priceType]["DISCOUNT_VALUE"] < $arPrice[$priceType]["VALUE"])
					$lotPrice = $arPrice[$priceType]["DISCOUNT_VALUE"];
				else
					$lotPrice = $arPrice[$priceType]["VALUE"];
			}
			//end select price
			
			//last user bets
			$userLastBets = 0;
			$userLastBetsId = 0;
			$userLastBetsDate = 0;
			$arSelect = array("ID","NAME","DATE_CREATE","CREATED_BY","PROPERTY_USER_BETS");
			$arFilter = array(
				"IBLOCK_ID"=>$arParams["BETS_IBLOCK_ID"],    
				"ACTIVE"=>"Y",
				"PROPERTY_PRODUCT_ID"=>$productId,
				"PROPERTY_AUCTION_ID"=>$auctionId,
			);
			$res = CIBlockElement::GetList(array("ID"=>"DESC"), $arFilter, false, false, $arSelect);
			if ($arFields = $res->Fetch())
			{
				$userLastBets = floatval($arFields["PROPERTY_USER_BETS_VALUE"]);
				$userLastBetsId = $arFields["CREATED_BY"];
				$userLastBetsDate = $arFields["DATE_CREATE"];
			}
			
			if ($userLastBets <= 0)
				$userLastBets = $beginPrice;
			
			$userNextBets = $userLastBets + $bets;
			
			if ($arParams["AUCTION_EDIT_PRICE"] === "N")
				$userBets = $userNextBets;
			
			if ($arParams["AUCTION_DOUBLE_BETS"] === "Y" && $userLastBetsId == $USER->GetID())
				$arResult["ERROR"][] = GetMessage('SIM_DOUBLE_BET_NO');
			
			//confirm auction if bets >= product price
			if ($arParams["AUCTION_PRICE_CONFIRM"] === "Y")
			{
				if ($userBets >= $lotPrice)
				{
					$userBets = $lotPrice;
					$userNextBets = $lotPrice;
					$el = new CIBlockElement;
					$arLoadProductArray = array(
						"ACTIVE_TO" => date("d.m.Y H:i:s"),
					);
					$res = $el->Update($auctionId, $arLoadProductArray);
				}
			}

			//interval between bets
			if ($arParams["AUCTION_INTERVAL"] > 0)
			{
				$interval = $arParams["AUCTION_INTERVAL"] * 60;
				$dateBetsUnix = MakeTimeStamp($userLastBetsDate, CSite::GetDateFormat("FULL"));
				if (time() < ($dateBetsUnix + $interval))
					$arResult["ERROR"][] = str_replace("#MINUTS#", $arParams["AUCTION_INTERVAL"], GetMessage('SIM_INTERVAL_BET_NO'));
			}
			
			if (empty($arResult["ERROR"]))
			{
				if ($userBets >= $userNextBets)
				{
					$arProperty = array();
					$dbProperty = CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $arParams["BETS_IBLOCK_ID"]));
					while($arProp = $dbProperty->Fetch())
						$arProperty[$arProp["CODE"]] = $arProp["ID"];
					
					if (strlen($USER->GetFullName()) <= 0)
						$userName = "[".$USER->GetLogin()."]";
					else
						$userName = $USER->GetFullName();
					
					$el = new CIBlockElement;
					$PROP = array();
					$PROP[$arProperty["PRODUCT_ID"]] = $productId;
					$PROP[$arProperty["AUCTION_ID"]] = $auctionId;
					$PROP[$arProperty["USER_BETS"]] = $userBets;
					$arLoadProductArray = array(
						"IBLOCK_SECTION_ID" => false,
						"IBLOCK_ID" => $arParams["BETS_IBLOCK_ID"],
						"NAME" => $userName,
						"PROPERTY_VALUES"=> $PROP,
					);
					if($PRODUCT_ID = $el->Add($arLoadProductArray))
					{
						$arResult["MESSAGE"][] = GetMessage('SIM_BETS_TAKE');
						$cache->CleanDir($cachePath);
											
						//update (extend) date auction
						if ($arParams["AUCTION_EXTEND"] > 0)
						{
							$bUpdate = true;
							if ($arParams["AUCTION_PRICE_CONFIRM"] === "Y" && $userBets >= $lotPrice)
								$bUpdate = false;
							
							if ($bUpdate)
							{
								if ($dateUnixTo > time())
								{
									$dateUnixTo = $dateUnixTo + ($arParams["AUCTION_EXTEND"]*60);
									$el = new CIBlockElement;
									$arLoadProductArray = array(
										"ACTIVE_TO" => date("d.m.Y H:i:s", $dateUnixTo),
									);
									$res = $el->Update($auctionId, $arLoadProductArray);
								}
							}
						}
					}
					else
						$arResult["ERROR"][] = GetMessage('SIM_BETS_ERROR');
				}
				else
					$arResult["ERROR"][] = GetMessage('SIM_BETS_MINIMAL')." ".SaleFormatCurrency((double)$userNextBets, $CURRENCY);
			}
		}
	}
}
elseif ($_SERVER["REQUEST_METHOD"] == "POST" && is_set($_POST["BTN_USER"]) && !$USER->IsAuthorized())
{
	$arResult["ERROR"][] = GetMessage('SIM_BETS_AUTH');
}
//end add

//Add to basket
if ($_SERVER["REQUEST_METHOD"] == "POST" && is_set($_POST["BTN_BUY"]) && $USER->IsAuthorized() && check_bitrix_sessid())
{
	$productId = 0;
	$auctionId = $arParams["ELEMENT_ID"];
	
	//select auction and find product id
	$arSelect = array("ID","ACTIVE_TO","PROPERTY_BETS","PROPERTY_PRICE_BEGIN","PROPERTY_PRODUCTS");
	$arFilter = array(
		"IBLOCK_ID"=>$arParams["AUCTION_IBLOCK_ID"],    
		"ACTIVE"=>"Y",
		"ID"=>$auctionId,
		"CHECK_PERMISSIONS" => $arParams["AUCTION_PERMISSIONS"],
		"<DATE_ACTIVE_TO" => ConvertTimeStamp(false, "FULL"),
	);
	$res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
	if ($arFields = $res->Fetch())
	{
		$productId = intval($arFields["PROPERTY_PRODUCTS_VALUE"]);
	}
	
	//select last bets	
	$arSelect = array("ID","NAME","CREATED_BY","DATE_CREATE","PROPERTY_PRODUCT_ID","PROPERTY_AUCTION_ID","PROPERTY_USER_BETS");
	$arFilter = array(
		"IBLOCK_ID"=>$arParams["BETS_IBLOCK_ID"],    
		"ACTIVE"=>"Y",
		"PROPERTY_PRODUCT_ID"=>$productId,
		"PROPERTY_AUCTION_ID"=>$auctionId,
	);
	$res = CIBlockElement::GetList(array("ID"=>"DESC"), $arFilter, false, array("nTopCount"=>$arParams["COUNT_BETS"]), $arSelect);
	$arFields = $res->Fetch();
	if ($arFields["CREATED_BY"] == $USER->GetID())
	{
		if ($arParams["AUCTION_MAX_BUY"] > 0)
		{
			$dbBasketItems = CSaleBasket::GetList(
				array("ID" => "ASC"),
				array(
						"FUSER_ID" => CSaleBasket::GetBasketUserID(),
						"LID" => SITE_ID,
						"MODULE" => "auction",
						"PRODUCT_XML_ID" => $auctionId,
						"PRODUCT_ID" => $productId,
					),
				array("SUM"=>"QUANTITY"),
				false,
				array("QUANTITY")
			);
			$arItems = $dbBasketItems->Fetch();
			if (floatval($arItems["QUANTITY"]) >= $arParams["AUCTION_MAX_BUY"])
				$arResult["ERROR"][] = GetMessage('SIM_ERR_LIMIT_BUY_QUANTITY');
		}

		if (empty($arResult["ERROR"]))
		{
			$price = floatval($arFields["PROPERTY_USER_BETS_VALUE"]);
			
			/*$arFilter = array(
				"IBLOCK_ID"=>$arParams["AUCTION_IBLOCK_ID"],    
				"ACTIVE"=>"Y",
				"ID"=>$auctionId,
			);
			$res = CIBlockElement::GetList(array("ID" => "DESC"), $arFilter, false, false, array("ID", "NAME", "ACTIVE_TO"));
			if ($arFields = $res->Fetch())
			{
				$dateEndUnix = MakeTimeStamp($arFields["ACTIVE_TO"], "DD.MM.YYYY HH:MI:SS");
				
				if ($dateEndUnix < time())
				{*/
					$arFilter = array(
						"ID" => $productId,    
						"ACTIVE" => "Y",
					);	
					$resTmp = CIBlockElement::GetList(array(), $arFilter, false, false, array("ID", "NAME", "DETAIL_PAGE_URL"));
					$arRes = $resTmp->GetNext();
					
					$arFields = array(
						"PRODUCT_ID" => $productId,
						"PRODUCT_XML_ID" => $auctionId,
						"PRODUCT_PRICE_ID" => 0,
						"PRICE" => $price,
						"CURRENCY" => $CURRENCY,
						"WEIGHT" => 0,
						"QUANTITY" => 1,
						"LID" => LANG,
						"DELAY" => "N",
						"CAN_BUY" => "Y",
						"NAME" => $arRes["NAME"],
						"MODULE" => "auction",
						"NOTES" => GetMessage('SALE_PRODUCT_NOTES'),
						"DISCOUNT_PRICE" => 0,
						"VAT_RATE" => 0,
						"DETAIL_PAGE_URL" => $arRes["DETAIL_PAGE_URL"],
					);
					if (CSaleBasket::Add($arFields))
						$arResult["MESSAGE"][] = GetMessage('SIM_AUCTION_BUY_OK');
					else
						$arResult["ERROR"][] = GetMessage('SIM_ERR_AUCTION_BASKET');
				//}
				//else
				//	$arResult["ERROR"][] = GetMessage('SIM_ERR_AUCTION_DATE');
			}
			else
				$arResult["ERROR"][] = GetMessage('SIM_ERR_AUCTION');
		}
		else
			$arResult["ERROR"][] = GetMessage('SIM_ERR_BUY_CREATED');
}

//Add to basket old price
if ($_SERVER["REQUEST_METHOD"] == "POST" && is_set($_POST["BTN_OLD_BUY"]) && check_bitrix_sessid())
{
	$productId = intval($_POST["PRODUCT_ID"]);
	
	if (Add2BasketByProductID($productId, 1, array(), array()))
		$arResult["MESSAGE"][] = GetMessage('SIM_AUCTION_BUY_OK');
}

// Cache data
if ($arParams["CACHE_TIME"] > 0 && $cache->InitCache($arParams["CACHE_TIME"], $cacheId, $cachePath))
{
	$arResult["AUCTION"] = array();
	$arResult["USERS_BETS"] = array();
	$arResult["USERS_VICTORY"] = array();
	
	$res = $cache->GetVars();
	
	$arResult["AUCTION"] = $res["AUCTION"];
	$arResult["AUCTION"]["ACCESS"] = $auctionAccess;
	$arResult["USERS_BETS"] = $res["USERS_BETS"];
	
	if ($arResult["AUCTION"]["DATE_ACTIVE_TO_TIMESTAMP"] <= time())
		$arResult["AUCTION"]["ACTIVE"] = "N";
		
	if ($arResult["AUCTION"]["DATE_ACTIVE_FROM_TIMESTAMP"] > time())
		$arResult["AUCTION"]["ACTIVE"] = "N";
	else
		unset($arResult["AUCTION"]["DATE_BEGIN"]);
	
	$countDown = $arResult["AUCTION"]["DATE_ACTIVE_TO_TIMESTAMP"] - time();
	if ($countDown <= 0)
		$countDown = 0;
	$arResult["AUCTION"]["COUNT_DOWN"] = $countDown;
	
	$countDown = $arResult["AUCTION"]["DATE_ACTIVE_FROM_TIMESTAMP"] - time();
	if ($countDown <= 0)
		$countDown = 0;
	$arResult["AUCTION"]["COUNT_DOWN_BEGIN"] = $countDown;
}
elseif($cache->StartDataCache())
{
	//select auction product property
	$AUCTION_ID = 0;
	
	$arSelect = array("ID","SECTION_ID","CODE","DATE_ACTIVE_FROM","DATE_ACTIVE_TO","PROPERTY_PRODUCTS","PROPERTY_PRICE_BEGIN","PROPERTY_BETS","NAME");
	$arFilter = array(
		"ID" => $arParams["ELEMENT_ID"],
		"IBLOCK_ID"=>$arParams["AUCTION_IBLOCK_ID"],
		"ACTIVE"=>"Y",
		"CHECK_PERMISSIONS" => $arParams["AUCTION_PERMISSIONS"],
		//"PROPERTY_PRODUCTS"=>$arParams["ELEMENT_ID"],
	);
	if ($arParams['AUCTION_HIDE'] == "Y")
		$arFilter[">DATE_ACTIVE_TO"] = ConvertTimeStamp(false, "FULL");
	
	$res = CIBlockElement::GetList(array("ID"=>"DESC"), $arFilter, false, false, $arSelect);
	$arTmp = array();
	if ($arFields = $res->GetNext())
	{
		$dateBeginUnix = MakeTimeStamp($arFields["DATE_ACTIVE_FROM"], "DD.MM.YYYY HH:MI:SS");
		$dateEndUnix = MakeTimeStamp($arFields["DATE_ACTIVE_TO"], "DD.MM.YYYY HH:MI:SS");
		
		$arTmp["ACCESS"] = $auctionAccess;
		$arTmp["ACTIVE"] = "N";
		if ($dateEndUnix > time())
			$arTmp["ACTIVE"] = "Y";
		
		if ($dateBeginUnix > time())
			$arTmp["ACTIVE"] = "N";
		
		$arTmp["ID"] = $arFields["ID"];
		$arTmp["PRODUCT_ID"] = $arFields["PROPERTY_PRODUCTS_VALUE"];
		$arResultPrices = getPriceTypes($arTmp["PRODUCT_ID"], $arParams["PRICE_CODE"]);
		
		//select product
		$arSelect = array("ID","IBLOCK_ID","NAME","CODE","ACTIVE","PREVIEW_TEXT","PREVIEW_TEXT_TYPE","PREVIEW_PICTURE","DETAIL_TEXT","DETAIL_TEXT_TYPE","DETAIL_PICTURE","DETAIL_PAGE_URL","IBLOCK_SECTION_ID");
		
		if (is_array($arResultPrices) && count($arResultPrices) > 0)
		{
			foreach($arResultPrices as &$value)
				$arSelect[] = $value["SELECT"];
		}
		
		$arFilter = array(
			"ID"=>$arTmp["PRODUCT_ID"],    
			"ACTIVE"=>"Y",
		);		
	
		$resTmp = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
		$obElement = $resTmp->GetNextElement();
		$arResTmp = $obElement->GetFields();
		
		$arResTmp["PREVIEW_PICTURE"] = ($arResTmp["PREVIEW_PICTURE"] > 0 ? CFile::GetFileArray($arResTmp["PREVIEW_PICTURE"]) : false);
		$arResTmp["DETAIL_PICTURE"] = ($arResTmp["DETAIL_PICTURE"] > 0 ? CFile::GetFileArray($arResTmp["DETAIL_PICTURE"]) : false);
		
		$arFile = array();
		if ($arResTmp["DETAIL_PICTURE"])
			$arFile = $arResTmp["DETAIL_PICTURE"];
		elseif ($arResTmp["PREVIEW_PICTURE"])
			$arFile = $arResTmp["PREVIEW_PICTURE"];
		
		if (count($arFile) > 0)
		{
			$file = CFile::ResizeImageGet($arFile, array('width'=>$arParams["IMAGE_WIDTH"], 'height'=>$arParams["IMAGE_HEIGHT"]), BX_RESIZE_IMAGE_PROPORTIONAL, true);
			$arResTmp["RESIZE_PICTURE"] = array(
				"SRC" => $file["src"],
				"WIDTH" => $file["width"],
				"HEIGHT" => $file["height"],
			);
		}
			
		$arTmp["DATE_ACTIVE_FROM"] = $arFields["DATE_ACTIVE_FROM"];
		$arTmp["DATE_ACTIVE_FROM_FORMAT"] = date("d.m.Y H:i", $dateBeginUnix);
		$arTmp["DATE_ACTIVE_FROM_TIMESTAMP"] = $dateBeginUnix;
		if ($dateBeginUnix > time())
			$arTmp["DATE_BEGIN"] = $arTmp["DATE_ACTIVE_FROM_FORMAT"];
		$arTmp["DATE_ACTIVE_TO"] = $arFields["DATE_ACTIVE_TO"];
		$arTmp["DATE_ACTIVE_TO_FORMAT"] = date("d.m.Y H:i", $dateEndUnix);
		$arTmp["DATE_ACTIVE_TO_TIMESTAMP"] = $dateEndUnix;
		
		$countDown = $dateEndUnix - time();
		if ($countDown < 0)
			$countDown = 0;
		$arTmp["COUNT_DOWN"] = $countDown;
		
		$countDown = $dateBeginUnix - time();
		if ($countDown <= 0)
			$countDown = 0;
		$arTmp["COUNT_DOWN_BEGIN"] = $countDown;
		
		$arTmp["BETS"] = $arFields["PROPERTY_BETS_VALUE"];
		$arTmp["BETS_FORMAT"] = SaleFormatCurrency((double)$arFields["PROPERTY_BETS_VALUE"], $CURRENCY);
		$arTmp["BETS_NEXT"] = 0;
		$arTmp["BETS_NEXT_FORMAT"] = 0;
		$arTmp["BETS_LAST"] = 0;
		$arTmp["BETS_LAST_FORMAT"] = 0;
		$arTmp["BETS_PROFIT"] = 0;
		$arTmp["BETS_PROFIT_FORMAT"] = 0;
		$arTmp["PRICE_BEGIN"] = $arFields["PROPERTY_PRICE_BEGIN_VALUE"];
		$arTmp["PRICE_BEGIN_FORMAT"] = SaleFormatCurrency((double)$arFields["PROPERTY_PRICE_BEGIN_VALUE"], $CURRENCY);
		
		$arTmp["DETAIL_URL"] = htmlspecialcharsbx(CIBlock::ReplaceDetailUrl($arParams["IBLOCK_URL"], $arFields, true, "E"));
		
		$arPrice = array();
		$priceType = "";
		
		if (is_array($arResultPrices) && count($arResultPrices) > 0)
		{
			foreach ($arResultPrices as $key => $val)
				$priceType = $key;
				
			$arPrice = CIBlockPriceTools::GetItemPrices(1, $arResultPrices, $arResTmp, false);
			
			$price = $arPrice[$priceType]["VALUE"];
			$priceP = $arPrice[$priceType]["PRINT_VALUE"];
			if ($arPrice[$priceType]["DISCOUNT_VALUE"] < $arPrice[$priceType]["VALUE"])
			{
				$price = $arPrice[$priceType]["DISCOUNT_VALUE"];
				$priceP = $arPrice[$priceType]["PRINT_DISCOUNT_VALUE"];
			}
			
			if (count($arPrice) > 0)
			{
				$arTmp["PRICE"]["CURRENCY"] = $arPrice[$priceType]["CURRENCY"];
				$arTmp["PRICE"]["CAN_BUY"] = $arPrice[$priceType]["CAN_BUY"];
				$arTmp["PRICE"]["VALUE"] = $price;
				$arTmp["PRICE"]["PRINT_VALUE"] = $priceP;
			}
		}
	
		$arTmp["PRODUCT"] = $arResTmp;
		$arResult["AUCTION"] = $arTmp;
	}
	
	//select users bets
	$putsLast = 0;
	$arResult["USERS_BETS"] = array();
	$arResult["USERS_VICTORY"] = array();
	$arUsersID = array();
	$arUsers = array();
	$arSelect = array("ID","CREATED_BY","DATE_CREATE","PROPERTY_PRODUCT_ID","PROPERTY_AUCTION_ID","PROPERTY_USER_BETS");
	$arFilter = array(
		"IBLOCK_ID"=>$arParams["BETS_IBLOCK_ID"],    
		"ACTIVE"=>"Y",
		"PROPERTY_PRODUCT_ID"=>$arResult["PRODUCT_ID"],
		"PROPERTY_AUCTION_ID"=>$arParams["ELEMENT_ID"],
	);
	$res = CIBlockElement::GetList(array("ID"=>"DESC"), $arFilter, false, array("nTopCount"=>$arParams["COUNT_BETS"]), $arSelect);
	while ($arFields = $res->Fetch())
	{
		$arFields["PROPERTY_USER_BETS_VALUE"] = floatval($arFields["PROPERTY_USER_BETS_VALUE"]);
		
		if ($putsLast <= 0)
			$putsLast = $arFields["PROPERTY_USER_BETS_VALUE"];
		
		$arResult["USERS_BETS"][] = array(
			"ID" => $arFields["ID"],
			"CREATED_BY" => $arFields["CREATED_BY"],
			"DATE_CREATE" => $arFields["DATE_CREATE"],
			"BETS" => $arFields["PROPERTY_USER_BETS_VALUE"],
			"BETS_FORMAT" => SaleFormatCurrency($arFields["PROPERTY_USER_BETS_VALUE"], $CURRENCY),
		);
		$arUsersID[$arFields["CREATED_BY"]] = $arFields["CREATED_BY"];
	}

	if (is_array($arResult["AUCTION"]) && count($arResult["AUCTION"]) > 0)
	{
		if ($putsLast > 0)
		{
			$arResult["AUCTION"]["BETS_LAST"] = $putsLast;
			$arResult["AUCTION"]["BETS_LAST_FORMAT"] = SaleFormatCurrency((double)$putsLast, $CURRENCY);
			if ($arResult["AUCTION"]["PRICE"]["VALUE"] > 0)
			{
				$arResult["AUCTION"]["BETS_PROFIT"] = $arResult["AUCTION"]["PRICE"]["VALUE"] - $putsLast;
				$arResult["AUCTION"]["BETS_PROFIT_FORMAT"] = SaleFormatCurrency($arResult["AUCTION"]["BETS_PROFIT"], $CURRENCY);
			}
		}
		else
			$putsLast = $arResult["AUCTION"]["PRICE_BEGIN"];
		
		$betsNext = $putsLast + $arResult["AUCTION"]["BETS"];
		
		if ($arParams["AUCTION_PRICE_CONFIRM"] === "Y" && $betsNext > $arResult["AUCTION"]["PRICE"]["VALUE"])
			$betsNext = $arResult["AUCTION"]["PRICE"]["VALUE"];
		
		$arResult["AUCTION"]["BETS_NEXT"] = $betsNext;
		$arResult["AUCTION"]["BETS_NEXT_FORMAT"] = SaleFormatCurrency($betsNext, $CURRENCY);
		
		$arResult["AUCTION"]["ID"] = $arParams["ELEMENT_ID"];
	}

	$arUsersID = implode("|", $arUsersID);
	$rsUser = CUser::GetList(
		$by="id", 
		$order="asc", 
		array("ID"=>$arUsersID), 
		array("FIELDS"=>array("ID","LOGIN","NAME","LAST_NAME","PERSONAL_PHOTO"))
	);
	while ($arUser = $rsUser->GetNext())
		$arUsers[$arUser["ID"]] = $arUser;

	if (is_array($arResult["USERS_BETS"]) && count($arResult["USERS_BETS"]) > 0)
	{
		foreach ($arResult["USERS_BETS"] as &$val)
		{
			$fio = $arUsers[$val["CREATED_BY"]]["LOGIN"];
			if (strlen($arUsers[$val["CREATED_BY"]]["LAST_NAME"]) > 0 || strlen($arUsers[$val["CREATED_BY"]]["NAME"]) > 0)
				$fio = $arUsers[$val["CREATED_BY"]]["LAST_NAME"]." ".$arUsers[$val["CREATED_BY"]]["NAME"];
			
			$arFile = CFile::GetFileArray($arUsers[$val["CREATED_BY"]]["PERSONAL_PHOTO"]);
			$file = CFile::ResizeImageGet($arFile, array('width'=>$arParams["AVATAR_WIDTH"], 'height'=>$arParams["AVATAR_HEIGHT"]), BX_RESIZE_IMAGE_PROPORTIONAL, true);
			
			$dateUnix = MakeTimeStamp($val["DATE_CREATE"], "DD.MM.YYYY HH:MI:SS");

			$val["BETS_DATE"] = date("d.m.Y H:i", $dateUnix);
			$val["BETS_FORMAT"] = SaleFormatCurrency((double)$val["BETS"], $CURRENCY);
			$val["AVATAR_SRC"] = $file["src"];
			$val["USER_NAME"] = $fio;
		}
	}

	$cache->EndDataCache(array(
		"AUCTION" => $arResult["AUCTION"],
        "USERS_BETS" => $arResult["USERS_BETS"],
    ));	
	//END AUCTION
}

if ($arResult["AUCTION"]["ACTIVE"] == "N" && $arResult["AUCTION"]["DATE_ACTIVE_TO_TIMESTAMP"] < time())
{
	$arResult["USERS_VICTORY"] = $arResult["USERS_BETS"][0];

	if ($USER->IsAuthorized() && $arResult["USERS_VICTORY"]["CREATED_BY"] == $USER->GetID())
	{
		$arResult["USERS_VICTORY"]["CAN_BUY"] = "Y";
	}
}

if ($arParams["AUCTION_SET_TITLE"] === "Y" && strlen($arResult["AUCTION"]["PRODUCT"]["NAME"]) > 0)
	$APPLICATION->SetTitle($arResult["AUCTION"]["PRODUCT"]["NAME"]);

	
//echo "<pre>";
//print_r($arResult);
//echo "</pre>";
$this->IncludeComponentTemplate();

?>