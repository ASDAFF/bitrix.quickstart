<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

function arraySort($a, $b)
{
	$a = MakeTimeStamp($a["DATE_VISIT"], "DD.MM.YYYY HH:MI:SS");
	$b = MakeTimeStamp($b["DATE_VISIT"], "DD.MM.YYYY HH:MI:SS");

	if ($a == $b) {
		return 0;
	}
	return ($a > $b) ? -1 : 1;
}

$arParams["VIEWED_COUNT"] = IntVal($arParams["VIEWED_COUNT"]);
if ($arParams["VIEWED_COUNT"] <= 0)
	$arParams["VIEWED_COUNT"] = 5;
$arParams["VIEWED_IMG_HEIGHT"] = IntVal($arParams["VIEWED_IMG_HEIGHT"]);
if($arParams["VIEWED_IMG_HEIGHT"] <= 0)
	$arParams["VIEWED_IMG_HEIGHT"] = 150;
$arParams["VIEWED_IMG_WIDTH"] = IntVal($arParams["VIEWED_IMG_WIDTH"]);
if ($arParams["VIEWED_IMG_WIDTH"] <= 0)
	$arParams["VIEWED_IMG_WIDTH"] = 150;

if($arParams["SET_TITLE"] == "Y")
	$APPLICATION->SetTitle(GetMessage("VIEW_TITLE"));

$arParams["VIEWED_NAME"] = (($arParams["VIEWED_NAME"] == "Y") ? "Y" : "N");
$arParams["VIEWED_IMAGE"] = (($arParams["VIEWED_IMAGE"] == "Y") ? "Y" : "N");
$arParams["VIEWED_PRICE"] = (($arParams["VIEWED_PRICE"] == "Y") ? "Y" : "N");

if (!isset($arParams["VIEWED_CURRENCY"]) || strlen($arParams["VIEWED_CURRENCY"]) <= 0)
	$arParams["VIEWED_CURRENCY"] = "default";

$arResult = array();
$arFilter = array();

if (!CModule::IncludeModule("sale"))
{
	ShowError(GetMessage("VIEWE_NOT_INSTALL"));
	return;
}
if (!CModule::IncludeModule("iblock"))
{
	ShowError(GetMessage("VIEWIBLOCK_NOT_INSTALL"));
	return;
}
if (!CModule::IncludeModule("catalog"))
{
	ShowError(GetMessage("VIEWCATALOG_NOT_INSTALL"));
	return;
}

$arFilter["LID"] = SITE_ID;
$arFilter["FUSER_ID"] = CSaleBasket::GetBasketUserID();
$arGroups = $USER->GetUserGroupArray();

$arViewed = array();
$arViewedId = array();

if (!empty($arParams["EXCLUDE_ID"])) {

    //$arFilter["!PRODUCT_ID"] = $arParams["EXCLUDE_ID"];
}
$db_res = CSaleViewedProduct::GetList(
		array(
			"DATE_VISIT" => "DESC"
		),
		$arFilter,
		false,
		array(
			"nTopCount" => $arParams["VIEWED_COUNT"]
		),
		array('ID', 'IBLOCK_ID', 'PRICE', 'CURRENCY', 'CAN_BUY', 'PRODUCT_ID', 'DATE_VISIT', 'DETAIL_PAGE_URL', 'DETAIL_PICTURE', 'PREVIEW_PICTURE', 'NAME', 'NOTES')
);
while ($arItems = $db_res->Fetch())
{
    $arViewedId[] = $arItems["PRODUCT_ID"];
	$arViewed[$arItems["PRODUCT_ID"]] = $arItems;
}
$arElementSort = array();

//check catalog
if (count($arViewedId) > 0 && CModule::IncludeModule("iblock") && CModule::IncludeModule("catalog"))
{
	$arIBlockSectionID = array();
	$res = CIBlockElement::GetList(
		array(),
		array("ID" => $arViewedId),
		false,
		false,
		array(
			"ID",
			"IBLOCK_ID",
			"IBLOCK_TYPE_ID",
			"IBLOCK_CODE",
			"IBLOCK_EXTERNAL_ID",
			"IBLOCK_SECTION_ID",
			"DETAIL_PICTURE",
			"PREVIEW_PICTURE",
			"DETAIL_PAGE_URL",
			"CODE",
			"XML_ID",
			"SECTION_CODE",
			"EXTERNAL_ID",
			"SITE_DIR"
		)
	);
	while ($arElements = $res->GetNext())
	{
		$arElements["DATE_VISIT"] = $arViewed[$arElements["ID"]]["DATE_VISIT"];
		$arElements["ELEMENT_CODE"] = $arElements["CODE"];
		$arElements["ELEMENT_ID"] = $arElements["ID"];
		$arElements["SECTION_ID"] = $arElements["IBLOCK_SECTION_ID"];

		$arElementSort[] = $arElements;
		$arIBlockSectionID[] = $arElements["IBLOCK_SECTION_ID"];
	}

	// get additional info for updated detail URLs
	$dbSectionRes = CIBlockSection::GetList(array(), array("ID" => array_unique($arIBlockSectionID)), false, array("ID", "CODE"));
	while ($arSectionRes = $dbSectionRes->GetNext())
	{
		foreach ($arElementSort as &$arElements)
		{
			if ($arElements["IBLOCK_SECTION_ID"] == $arSectionRes["ID"])
				$arElements["SECTION_CODE"] = $arSectionRes["CODE"];
		}
		unset($arElements);
	}

	usort($arElementSort, "arraySort");

	foreach ($arElementSort as $arElements)
	{
		static $arCacheOffersIblock = array();
		$priceMin = 0;
		$arItems = $arViewed[$arElements["ID"]];
		$arItems["IBLOCK_ID"] = $arElements["IBLOCK_ID"];
		$arItems["DETAIL_PICTURE"] = $arElements["DETAIL_PICTURE"];
		$arItems["PREVIEW_PICTURE"] = $arElements["PREVIEW_PICTURE"];

		$arElements["DETAIL_PAGE_URL"] = CIBlock::ReplaceDetailUrl($arElements["DETAIL_PAGE_URL"], $arElements, false);

		$arItems["DETAIL_PAGE_URL"] = $arElements["DETAIL_PAGE_URL"];

		$arResult[] = $arItems;
	}
}

$this->IncludeComponentTemplate();
?>