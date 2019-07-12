<?if ( isset($_SERVER[ "HTTP_X_REQUESTED_WITH" ] ) ) 
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

header("Cache-Control: no-store, no-cache, must-revalidate");

foreach($_REQUEST as $key=>&$value)
	$_REQUEST[$key] = iconv("UTF-8", LANG_CHARSET, urldecode($value));

$SITE_ID = isset($_SERVER["HTTP_X_REQUESTED_WITH"]) ? $_REQUEST["SITE_ID"] : SITE_ID;	
	
CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");

$arOffers = array();
$arElement = array();

$color 		= "-";
$size 		= "-";
$artnumber 	= "-";

$arSelect = array(
	"ID",
	"IBLOCK_ID",
	"CODE",
	"XML_ID",
	"NAME",
	"ACTIVE",
	"DATE_ACTIVE_FROM",
	"DATE_ACTIVE_TO",
	"SORT",
	"PREVIEW_TEXT",
	"PREVIEW_TEXT_TYPE",
	"DETAIL_TEXT",
	"DETAIL_TEXT_TYPE",
	"DATE_CREATE",
	"CREATED_BY",
	"TIMESTAMP_X",
	"MODIFIED_BY",
	"TAGS",
	"IBLOCK_SECTION_ID",
	"DETAIL_PAGE_URL",
	"DETAIL_PICTURE",
	"PREVIEW_PICTURE",
	"PROPERTY_*",
);

$dbSite = CSite::GetByID($SITE_ID);
if($arSite = $dbSite -> Fetch())
{
	$SITE_DIR = $arSite["DIR"];
	$lang = $arSite["LANGUAGE_ID"];
}
	
if(strlen($lang) <= 0)
	$lang = "ru";	
	
// product element
$res = CIBlockElement::GetList(Array(), array("ID" => $_REQUEST["ID"]), false, false, $arSelect);
$obElement = $res->GetNextElement();
$arElement = $obElement->GetFields();
$arElement["PROPERTIES"] = $obElement->GetProperties();
$arElement["DETAIL_PICTURE"] = CFile::GetFileArray($arElement["DETAIL_PICTURE"]);
$res = CIBlockSection::GetByID($arElement["IBLOCK_SECTION_ID"]);
$arSection = $res->GetNext();
$arElement["SECTION"] = $arSection;

$color 		= $arElement["PROPERTIES"]["COLOR"]["VALUE"][$_REQUEST["COLOR"]];
$size  		= $arElement["PROPERTIES"]["SIZE"]["VALUE"][$_REQUEST["SIZE"]];
$artnumber 	= $arElement["PROPERTIES"]["ARTNUMBER"]["VALUE"][0];

$arSKU = CCatalogSKU::GetInfoByProductIBlock($arElement["IBLOCK_ID"]);
if($arSKU)
{
	// cml2_link code
	$res = CIBlockProperty::GetByID($arSKU["SKU_PROPERTY_ID"], $arSKU["IBLOCK_ID"]);
	$ar_cml2_link = $res->GetNext();

	// sku elements
	$rsElements = CIBlockElement::GetList(Array(), array("IBLOCK_ID" => $arSKU["IBLOCK_ID"], "ACTIVE"=>"Y"), false, false, $arSelect);
	while($obElement = $rsElements->GetNextElement())
	{
		$arItem = $obElement->GetFields();
		$arItem["PROPERTIES"] = $obElement->GetProperties();		
		$arOffers[ $arItem["PROPERTIES"][$ar_cml2_link["CODE"]]["VALUE"] ][ $arItem["ID"] ] = $arItem;
	}
	
	// offers product
	if( count($arOffers[$arElement["ID"]]) > 0)
	{		
		// color enum id
		$db_enum_color = CIBlockProperty::GetPropertyEnum("COLOR", array(), array("IBLOCK_ID"=>$arSKU["IBLOCK_ID"]));
		while($ar_enum_color = $db_enum_color->GetNext()) 
			$arProps["COLOR"][$ar_enum_color["ID"]] = $ar_enum_color;
			
		// size enum id
		$db_enum_size = CIBlockProperty::GetPropertyEnum("SIZE", array(), array("IBLOCK_ID"=>$arSKU["IBLOCK_ID"]));
		while($ar_enum_size = $db_enum_size->GetNext()) 
			$arProps["SIZE"][$ar_enum_size["ID"]] = $ar_enum_size;

		// artnumber value
		foreach($arOffers[$_REQUEST["ID"]] as $arOffersElement)
			if( $arOffersElement["PROPERTIES"]["SIZE"]["VALUE"] == $arProps["SIZE"][$_REQUEST["SIZE"]]["VALUE"] &&
				$arOffersElement["PROPERTIES"]["COLOR"]["VALUE"] == $arProps["COLOR"][$_REQUEST["COLOR"]]["VALUE"] )
					$arProps["ARTNUMBER"] = $arOffersElement["PROPERTIES"]["ARTNUMBER"];
		
		$color 		= $arProps["COLOR"][$_REQUEST["COLOR"]]["VALUE"];
		$size  		= $arProps["SIZE" ][$_REQUEST["SIZE"] ]["VALUE"];
		$artnumber 	= $arProps["ARTNUMBER"]["VALUE"][0];
	}
}

// mail message
$arFields = Array(

	"ELEMENT_ID" 		=> $arElement["ID"],
	"ELEMENT_NAME" 		=> $arElement["NAME"],
	"DETAIL_PICTURE" 	=> $arElement["DETAIL_PICTURE"]["SRC"],
	"DETAIL_PAGE"		=> "/bitrix/admin/iblock_element_edit.php?ID=".$arElement["ID"]."&type=".$arElement["IBLOCK_TYPE_ID"]."&lang=".$lang."&IBLOCK_ID=".$arElement["IBLOCK_ID"],
	"DETAIL_TEXT"		=> $arElement["DETAIL_TEXT"],
	"SECTION_NAME"		=> $arElement["SECTION"]["NAME"],
	"SECTION_PAGE_URL"	=> $arElement["SECTION"]["LIST_PAGE_URL"].$arElement["SECTION"]["SECTION_PAGE_URL"],
	"SITE_DIR"			=> $SITE_DIR,
	"EDIT_URL"			=> "",
	
	"NAME" 				=> strlen($_REQUEST["NAME"]) > 0 ? $_REQUEST["NAME"] : "-",
	"PHONE" 			=> $_REQUEST["PHONE"],
	"COLOR"	 			=> $color,
	"SIZE" 				=> $size,
	"ARTNUMBER" 		=> $artnumber,	
	"QUANTITY" 			=> is_numeric($_REQUEST["QUANTITY"]) ? $_REQUEST["QUANTITY"] : 1,
	
	"ORDER_DATE" 		=> date("d-m-Y H:i"),
	"BCC" 				=> COption::GetOptionString("sale", "order_email", "order@".$SERVER_NAME),
	"SALE_EMAIL" 		=> COption::GetOptionString("sale", "order_email", "order@".$SERVER_NAME),
);

// save result
$rsEM = CEventMessage::GetList($by="site_id", $order="desc", array("TYPE_ID"=>"SALE_QUICKLY_ORDER", "SITE_ID"=>$SITE_ID));
$arEM = $rsEM->Fetch();
$content = $arEM["MESSAGE"];

		
$quicklyID = COption::GetOptionInt("streetstyle", "catalogQuicklyID", false, $SITE_ID);	
if($quicklyID)
{ 		
	$el = new CIBlockElement;
	$ID = $el->Add( array(
	 
			  "IBLOCK_SECTION_ID" 	=> false,
			  "IBLOCK_ID"         	=> $quicklyID,
			  "PREVIEW_TEXT"      	=> $content,
			  "PREVIEW_TEXT_TYPE" 	=> "html",
			  "NAME"              	=> $arFields["ELEMENT_NAME"]." ".$arFields["SIZE"]." ".$arFields["COLOR"]." - ".$arFields["ARTNUMBER"],
			  "ACTIVE"            	=> "Y",
			  "DATE_ACTIVE_FROM"	=> ConvertTimeStamp(time(), "FULL"),
			  "PROPERTY_VALUES"		=> array("NAME"=>$arFields["NAME"], "PHONE"=>$arFields["PHONE"], "COLOR"=>$arFields["COLOR"], "SIZE"=>$arFields["SIZE"], "QUANTITY"=>$arFields["QUANTITY"], "ARTNUMBER"=>$arFields["ARTNUMBER"]),
		));
	
	$res = CIBlock::GetByID($quicklyID);
	$arQuickly = $res->GetNext();
	
	$arFields["EDIT_URL"] = "/bitrix/admin/iblock_element_edit.php?ID=".$ID."&type=".$arQuickly["IBLOCK_TYPE_ID"]."&lang=".$lang."&IBLOCK_ID=".$quicklyID;
	$arFields["SERVER_NAME"]= "";
	
	foreach($arFields as $key=>$value)
		$content = str_replace("#".$key."#", $value, $content);

	unset($arFields["SERVER_NAME"]);	
	$el->Update( $ID, 
	
			array(	 
				  "IBLOCK_SECTION_ID" 	=> false,
				  "IBLOCK_ID"         	=> $quicklyID,
				  "PREVIEW_TEXT"      	=> $content,
				  "PREVIEW_TEXT_TYPE" 	=> "html",
				)
		);	
}

$event = new CEvent;
$event->SendImmediate("SALE_QUICKLY_ORDER", $SITE_ID, $arFields, "N");
?>