<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule("iblock"))
{
	ShowError(GetMessage("SIM_IBLOCK_MODULE_NOT_INSTALL"));
	return;
}

if (!CModule::IncludeModule("sale"))
{
	ShowError(GetMessage("SIM_SALE_MODULE_NOT_INSTALL"));
	return;
}

$arParams["AUCTION_IBLOCK_ID"] = intval($arParams["AUCTION_IBLOCK_ID"]);
$arParams["SECTION_ID"] = intval($arParams["SECTION_ID"]);
$arParams["SECTION_CODE"] = trim($arParams["SECTION_CODE"]);

$arParams["IMG_WIDTH"] = intval($arParams["IMG_WIDTH"]);
if ($arParams["IMG_WIDTH"] <= 0)
	$arParams["IMG_WIDTH"] = 150;
$arParams["IMG_HEIGHT"] = intval($arParams["IMG_HEIGHT"]);
if ($arParams["IMG_HEIGHT"] <= 0)
	$arParams["IMG_HEIGHT"] = 150;

$arParams["AUCTION_PRODUCT_PROPERTY"] = ($arParams["AUCTION_PRODUCT_PROPERTY"] == "Y" ? 'Y' : 'N');
$arParams["AUCTION_HIDE"] = ($arParams["AUCTION_HIDE"] == "Y" ? 'Y' : 'N');
$arParams["AUCTION_JQUERY"] = ($arParams["AUCTION_JQUERY"] != "Y")?"N":"Y";
$arParams["AUCTION_LAST_BETS"] = ($arParams["AUCTION_LAST_BETS"] != "Y")?"N":"Y";
$arParams["AUCTION_PERMISSIONS"] = ($arParams["AUCTION_PERMISSIONS"] != "Y")?"N":"Y";

$arParams["PAGE_ELEMENT_COUNT"] = intval($arParams["PAGE_ELEMENT_COUNT"]);
if ($arParams["PAGE_ELEMENT_COUNT"] <= 0)
	$arParams["PAGE_ELEMENT_COUNT"] = 30;
$arParams["LINE_ELEMENT_COUNT"] = intval($arParams["LINE_ELEMENT_COUNT"]);
if ($arParams["LINE_ELEMENT_COUNT"] <= 0)
	$arParams["LINE_ELEMENT_COUNT"] = 3;

$arParams["AUCTION_NAME"] = ($arParams["AUCTION_NAME"] != "Y")?"N":"Y";
$arParams["AUCTION_LOT"] = ($arParams["AUCTION_LOT"] != "Y")?"N":"Y";
$arParams["DETAIL_URL"] = trim($arParams["DETAIL_URL"]);
//if (strlen($arParams["DETAIL_URL"]) <= 0)
//	$arParams["DETAIL_URL"] = $APPLICATION->GetCurDir()."detail.php?ELEMENT_ID=#ELEMENT_ID#";

$arParams["AUCTION_SET_TITLE"] = ($arParams["AUCTION_SET_TITLE"] != "Y")?"N":"Y";
$arParams["AUCTION_TITLE"] = trim($arParams["AUCTION_TITLE"]);
if (strlen($arParams["AUCTION_TITLE"]) <= 0)
	$arParams["AUCTION_TITLE"] = GetMessage('SIM_TITLE_VALUE');

$arParams["DISPLAY_TOP_PAGER"] = $arParams["DISPLAY_TOP_PAGER"]=="Y";
$arParams["DISPLAY_BOTTOM_PAGER"] = $arParams["DISPLAY_BOTTOM_PAGER"]!="N";
$arParams["PAGER_TITLE"] = trim($arParams["PAGER_TITLE"]);
$arParams["PAGER_SHOW_ALWAYS"] = $arParams["PAGER_SHOW_ALWAYS"]!="N";
$arParams["PAGER_TEMPLATE"] = trim($arParams["PAGER_TEMPLATE"]);
$arParams["PAGER_DESC_NUMBERING"] = $arParams["PAGER_DESC_NUMBERING"]=="Y";
$arParams["PAGER_DESC_NUMBERING_CACHE_TIME"] = intval($arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]);
$arParams["PAGER_SHOW_ALL"] = $arParams["PAGER_SHOW_ALL"]!=="N";

$arNavParams = array(
	"nPageSize" => $arParams["PAGE_ELEMENT_COUNT"],
	"bDescPageNumbering" => $arParams["PAGER_DESC_NUMBERING"],
	"bShowAll" => $arParams["PAGER_SHOW_ALL"],
);
$arNavigation = CDBResult::GetNavParams($arNavParams);
if($arNavigation["PAGEN"]==0 && $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"]>0)
	$arParams["CACHE_TIME"] = $arParams["PAGER_DESC_NUMBERING_CACHE_TIME"];
	
if ($arParams["AUCTION_JQUERY"] === "Y")
	CJSCore::Init(array("jquery"));
	
$arParams["CACHE_TIME"] = intval($arParams["CACHE_TIME"]);
$arResult = array();
$CURRENCY = COption::GetOptionString("sale", "CURRENCY_DEFAULT", "RUB");

$arParams["DETAIL_PAGE"] = trim($arParams["DETAIL_PAGE"]);
if (strlen($arParams["DETAIL_PAGE"]) <= 0)
	$arParams["DETAIL_PAGE"] = $APPLICATION->GetCurDir();

if(strlen($arParams["FILTER_NAME"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
{
	$arrFilter = array();
}
else
{
	global ${$arParams["FILTER_NAME"]};
	$arrFilter = ${$arParams["FILTER_NAME"]};
	if(!is_array($arrFilter))
		$arrFilter = array();
}

if(strlen($arParams["FILTER_NAME_LOT"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME_LOT"]))
{
	$arrLotFilter = array();
}
else
{
	global ${$arParams["FILTER_NAME_LOT"]};
	$arrLotFilter = ${$arParams["FILTER_NAME_LOT"]};
	if(!is_array($arrLotFilter))
		$arrLotFilter = array();
}

if (empty($arParams["LOT_SORT_FIELD1"]))
	$arParams["LOT_SORT_FIELD1"] = "sort";
if (!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["LOT_SORT_ORDER1"]))
	$arParams["LOT_SORT_ORDER1"] = "desc";
if (empty($arParams["LOT_SORT_FIELD2"]))
	$arParams["LOT_SORT_FIELD2"] = "sort";
if (!preg_match('/^(asc|desc|nulls)(,asc|,desc|,nulls){0,1}$/i', $arParams["LOT_SORT_ORDER2"]))
	$arParams["LOT_SORT_ORDER2"] = "desc";

// Check access
if ($arParams["AUCTION_PERMISSIONS"] === "Y")
	$auctionAccess = CIBlock::GetPermission($arParams["AUCTION_IBLOCK_ID"]);
else
	$auctionAccess = "W";

$arResult["ACCESS"] = $auctionAccess;
$arResult["AUCTION"] = array();

$cache = new CPHPCache();
$cacheId = 'auctionListID'.serialize($arrFilter).serialize($arrLotFilter).serialize($arNavigation).$arParams["SECTION_ID"].$arParams["SECTION_CODE"];
$cachePath = '/auctionList/';
if ($arParams["CACHE_TIME"] > 0 && $cache->InitCache($arParams["CACHE_TIME"], $cacheId, $cachePath))
{
	$res = $cache->GetVars();
	if (!empty($res["AUCTION"]))
	{
		$arResult["AUCTION"] = $res["AUCTION"];
		$arResult["NAV_STRING"] = $res["NAV_STRING"];
		$arResult["NAV_CACHED_DATA"] = $res["NAV_CACHED_DATA"];
		$arResult["NAV_RESULT"] = $res["NAV_RESULT"];
		
		$arTmp = array();
		foreach ($arResult["AUCTION"] as $item)
		{
			if ($item["DATE_ACTIVE_TO_TIMESTAMP"] > time())
				$item["ACTIVE"] = "Y";
			else
				$item["ACTIVE"] = "N";
			
			if ($item["DATE_ACTIVE_FROM_TIMESTAMP"] > time())
				$item["ACTIVE"] = "N";
			else
				unset($item["DATE_BEGIN"]);
			
			$countDown = $item["DATE_ACTIVE_TO_TIMESTAMP"] - time();
			if ($countDown < 0)
				$countDown = 0;
			$item["COUNT_DOWN"] = $countDown;
			
			if ($arParams['AUCTION_HIDE'] == "N" || ($arParams['AUCTION_HIDE'] == "Y" && $item["ACTIVE"] == "Y"))
				$arTmp[] = $item;
		}
		
		$arResult["AUCTION"] = $arTmp;
	}
}
elseif($cache->StartDataCache())
{
	$arProductId = array();
	$arAuction = array();
	$arSectionId = array();
	
	//select auction list
	$arSelect = array("ID","IBLOCK_ID","NAME","CODE","DATE_ACTIVE_FROM","DATE_ACTIVE_TO","DETAIL_PAGE_URL","PROPERTY_PRODUCTS","PROPERTY_PRICE_BEGIN","PROPERTY_BETS","IBLOCK_SECTION_ID");

	$arFilter = array(
		"IBLOCK_ID"=>$arParams["AUCTION_IBLOCK_ID"],    
		"ACTIVE"=>"Y",
		"CHECK_PERMISSIONS" => $arParams["AUCTION_PERMISSIONS"],
	);
	
	if ($arParams['AUCTION_HIDE'] == "Y")
		$arFilter[">DATE_ACTIVE_TO"] = ConvertTimeStamp(false, "FULL");
	
	if ($arParams['SECTION_ID']  > 0)
		$arFilter["SECTION_ID"] = $arParams["SECTION_ID"];
	elseif (strlen($arParams['SECTION_CODE']) > 0)
		$arFilter["SECTION_CODE"] = $arParams["SECTION_CODE"];
	
	$arSort = array("ID" => "DESC");
	
	$res = CIBlockElement::GetList($arSort, array_merge($arrFilter, $arFilter), false, $arNavParams, $arSelect);
	$res->SetUrlTemplates($arParams["DETAIL_URL"]);
	while ($arFields = $res->GetNext())
	{
		$arSectionId[$arFields["PROPERTY_PRODUCTS_VALUE"]] = $arFields["IBLOCK_SECTION_ID"];
		$arTmp = array();
		$dateBeginUnix = MakeTimeStamp($arFields["DATE_ACTIVE_FROM"], "DD.MM.YYYY HH:MI:SS");
		$dateEndUnix = MakeTimeStamp($arFields["DATE_ACTIVE_TO"], "DD.MM.YYYY HH:MI:SS");
		
		$arTmp["ACTIVE"] = "N";
		if ($dateEndUnix > time())
			$arTmp["ACTIVE"] = "Y";
		
		if ($dateBeginUnix > time())
			$arTmp["ACTIVE"] = "N";
		
		$arTmp["AUCTION_ID"] = $arFields["ID"];
		$arTmp["PRODUCT_ID"] = $arFields["PROPERTY_PRODUCTS_VALUE"];
		$arTmp["SECTION_ID"] = $arFields["IBLOCK_SECTION_ID"];
		$arTmp["NAME"] = $arFields["NAME"];
		$arTmp["CODE"] = $arFields["CODE"];
		$arTmp["DETAIL_PAGE_URL"] = $arFields["DETAIL_PAGE_URL"];
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
		
		$arTmp["BETS"] = $arFields["PROPERTY_BETS_VALUE"];
		$arTmp["BETS_FORMAT"] = SaleFormatCurrency((double)$arFields["PROPERTY_BETS_VALUE"], $CURRENCY);
		$arTmp["PRICE_BEGIN"] = $arFields["PROPERTY_PRICE_BEGIN_VALUE"];
		$arTmp["PRICE_BEGIN_FORMAT"] = SaleFormatCurrency((double)$arFields["PROPERTY_PRICE_BEGIN_VALUE"], $CURRENCY);

		$arAuction[$arFields["PROPERTY_PRODUCTS_VALUE"]] = $arTmp;
		$arProductId[] = $arTmp["PRODUCT_ID"];
	}

	$arResult["NAV_STRING"] = $res->GetPageNavStringEx($navComponentObject, $arParams["PAGER_TITLE"], $arParams["PAGER_TEMPLATE"], $arParams["PAGER_SHOW_ALWAYS"]);
	$arResult["NAV_CACHED_DATA"] = $navComponentObject->GetTemplateCachedData();
	$arResult["NAV_RESULT"] = $res;
		
	//select product list
	$arIdProduct = array();
	$arIdAuction = array();
	$arProducts = array();
	if (count($arAuction) > 0)
	{
		$arSelect = array("ID","IBLOCK_ID","NAME","CODE","ACTIVE","PREVIEW_TEXT","PREVIEW_TEXT_TYPE","PREVIEW_PICTURE","DETAIL_TEXT","DETAIL_TEXT_TYPE","DETAIL_PICTURE","DETAIL_PAGE_URL","IBLOCK_SECTION_ID");
		$arFilter = array(
			"ACTIVE"=>"Y",
			"ID" => $arProductId,
		);
		$arSort = array(
			$arParams["LOT_SORT_FIELD1"] => $arParams["LOT_SORT_ORDER1"],
			$arParams["LOT_SORT_FIELD2"] => $arParams["LOT_SORT_ORDER2"],
		);

		$res = CIBlockElement::GetList($arSort, array_merge($arrLotFilter, $arFilter), false, false, $arSelect);
		while($obElement = $res->GetNextElement())
		{
			$arFields = $obElement->GetFields();
			
			$arFields["PREVIEW_PICTURE"] = ($arFields["PREVIEW_PICTURE"] > 0 ? CFile::GetFileArray($arFields["PREVIEW_PICTURE"]) : false);
			$arFields["DETAIL_PICTURE"] = ($arFields["DETAIL_PICTURE"] > 0 ? CFile::GetFileArray($arFields["DETAIL_PICTURE"]) : false);
			
			$arFile = array();
			if ($arFields["DETAIL_PICTURE"])
				$arFile = $arFields["DETAIL_PICTURE"];
			elseif ($arFields["PREVIEW_PICTURE"])
				$arFile = $arFields["PREVIEW_PICTURE"];
			
			if (count($arFile) > 0)
			{
				$file = CFile::ResizeImageGet($arFile, array('width'=>$arParams["IMG_WIDTH"], 'height'=>$arParams["IMG_HEIGHT"]), BX_RESIZE_IMAGE_PROPORTIONAL, true);
				$arFields["RESIZE_PICTURE"] = array(
					"SRC" => $file["src"],
					"WIDTH" => $file["width"],
					"HEIGHT" => $file["height"],
				);
			}
			
			if ($arParams['AUCTION_PRODUCT_PROPERTY'] === "Y")
				$arFields["PROPERTIES"] = $obElement->GetProperties();
			
			$arIdProduct[] = $arFields["ID"];
			$arIdAuction[] = $arAuction[$arFields["ID"]]["AUCTION_ID"];
			$arProducts[$arFields["ID"]] = $arFields;
		}
		
		//select user bets
		$arUserBets = array();
		if ($arParams["AUCTION_LAST_BETS"] === "Y")
		{	
			$arSelect = array("ID","NAME","CREATED_BY","DATE_CREATE","PROPERTY_USER_BETS","PROPERTY_PRODUCT_ID","PROPERTY_AUCTION_ID");
			$arFilter = array(
				"IBLOCK_ID"=>$arParams["BETS_IBLOCK_ID"],    
				"ACTIVE"=>"Y",
				"PROPERTY_PRODUCT_ID"=>$arIdProduct,
				"PROPERTY_AUCTION_ID"=>$arIdAuction,
			);
			$res = CIBlockElement::GetList(array("ID"=>"DESC"), $arFilter, false, false, $arSelect);
			while($arFields = $res->Fetch())
				$arUserBets[] = $arFields;
		}
	}
	
	//add last price bets to product
	if (is_array($arProducts) && count($arProducts) > 0)
	{
		$arSections = array();
		if (is_array($arSectionId) && count($arSectionId) > 0)
		{
			$dbRes = CIBlockSection::GetList(array(), array("ID" => $arSectionId), false, array("ID", "CODE", "NAME"));
			while ($arRes = $dbRes->Fetch())
				$arSections[$arRes["ID"]] = $arRes;
			
			foreach ($arSectionId as $k => $v)
			{
				$arSectionId[$k] = $arSections[$v];
			}
		}
				
		foreach ($arProducts as $item)
		{
			if ($arParams["AUCTION_LAST_BETS"] === "Y")
			{
				foreach ($arUserBets as $bets)
				{
					if ($item["ID"] == $bets["PROPERTY_PRODUCT_ID_VALUE"]
						&& $arAuction[$item["ID"]]["AUCTION_ID"] == $bets["PROPERTY_AUCTION_ID_VALUE"])
					{
						$arAuction[$item["ID"]]["LAST_PRICE"] = floatval($bets["PROPERTY_USER_BETS_VALUE"]);
						$arAuction[$item["ID"]]["LAST_PRICE_FORMAT"] = SaleFormatCurrency($bets["PROPERTY_USER_BETS_VALUE"], $CURRENCY);
						$arAuction[$item["ID"]]["LAST_PRICE_USER_ID"] = $bets["CREATED_BY"];
						$arAuction[$item["ID"]]["LAST_PRICE_USER_NAME"] = $bets["NAME"];
						
						$date = MakeTimeStamp($bets["DATE_CREATE"], "DD.MM.YYYY HH:MI:SS");
						$arAuction[$item["ID"]]["LAST_PRICE_DATE"] = date("d.m.Y H:i", $date);
						$arAuction[$item["ID"]]["LAST_PRICE_TIMESTAMP"] = $date;
						
						break 1;
					}
				}
			}

			$item["ELEMENT_CODE"] = $item["CODE"];
			$item["SECTION_ID"] = $arSectionId[$item["ID"]]["ID"];
			$item["SECTION_CODE"] = $arSectionId[$item["ID"]]["CODE"];
			$item["SECTION_NAME"] = $arSectionId[$item["ID"]]["NAME"];
			unset($item["CODE"]);
			
			//$typeUrlReplace = "E";
			//if (strpos($arParams["DETAIL_URL"], "SECTION_CODE") || strpos($arParams["DETAIL_URL"], "ELEMENT_CODE"))
			//	$typeUrlReplace = false;
			
			//$arAuction[$item["ID"]]["DETAIL_AUCTION_URL"] = htmlspecialcharsbx(CIBlock::ReplaceDetailUrl($arParams["DETAIL_URL"], $item, false, $typeUrlReplace));
			
			$arAuction[$item["ID"]]["PRODUCT"] = $item;
			$arResult["AUCTION"][] = $arAuction[$item["ID"]];			
		}
	}

	$cache->EndDataCache(array(
		"AUCTION" => $arResult["AUCTION"],
		"NAV_STRING" => $arResult["NAV_STRING"],
		"NAV_CACHED_DATA" => $arResult["NAV_CACHED_DATA"],
		"NAV_RESULT" => $arResult["NAV_RESULT"],
    ));
}

if ($arParams["AUCTION_SET_TITLE"] === "Y")
	$APPLICATION->SetTitle($arParams["AUCTION_TITLE"]);

//print_r($arResult);
$this->IncludeComponentTemplate();
?>