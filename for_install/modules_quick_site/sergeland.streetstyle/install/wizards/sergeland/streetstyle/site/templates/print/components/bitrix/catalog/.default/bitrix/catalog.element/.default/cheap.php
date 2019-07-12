<?if ( isset($_SERVER[ "HTTP_X_REQUESTED_WITH" ] ) ) 
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

header("Cache-Control: no-store, no-cache, must-revalidate");

foreach($_REQUEST as $key=>&$value)
	$_REQUEST[$key] = iconv("UTF-8", LANG_CHARSET, urldecode($value));

$SITE_ID = isset($_SERVER["HTTP_X_REQUESTED_WITH"]) ? $_REQUEST["SITE_ID"] : SITE_ID;	
	
CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");

$arElement = array();
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
	$SERVER_NAME = $arSite["SERVER_NAME"];
	$LANG = $arSite["LANGUAGE_ID"];
}
	
if(strlen($LANG) <= 0)
	$LANG = "ru";	
	
// product element
$res = CIBlockElement::GetList(Array(), array("ID" => $_REQUEST["ID"]), false, false, $arSelect);
$obElement = $res->GetNextElement();
$arElement = $obElement->GetFields();
$arElement["PROPERTIES"] = $obElement->GetProperties();
$arElement["DETAIL_PICTURE"] = CFile::GetFileArray($arElement["DETAIL_PICTURE"]);
$res = CIBlockSection::GetByID($arElement["IBLOCK_SECTION_ID"]);
$arSection = $res->GetNext();
$arElement["SECTION"] = $arSection;


// mail message
$arFields = Array(

	"SITE_DIR"			=> $SITE_DIR,
	"SERVER_NAME"		=> $SERVER_NAME,

	"ELEMENT_ID" 		=> $arElement["ID"],
	"ELEMENT_NAME" 		=> $arElement["NAME"],
	"DETAIL_PICTURE" 	=> $arElement["DETAIL_PICTURE"]["SRC"],
	"DETAIL_PAGE"		=> "/bitrix/admin/iblock_element_edit.php?ID=".$arElement["ID"]."&type=".$arElement["IBLOCK_TYPE_ID"]."&lang=".$LANG."&IBLOCK_ID=".$arElement["IBLOCK_ID"],
	"DETAIL_TEXT"		=> $arElement["DETAIL_TEXT"],
	"SECTION_NAME"		=> $arElement["SECTION"]["NAME"],
	"SECTION_PAGE_URL"	=> $arElement["SECTION"]["SECTION_PAGE_URL"],
	
	"NAME" 				=> strlen($_REQUEST["NAME"]) > 0 ? $_REQUEST["NAME"] : "-",
	"PHONE" 			=> $_REQUEST["PHONE"],
	"URL" 				=> $_REQUEST["URL"],	
	"COMMENT"			=> $_REQUEST["COMMENT"],
	
	"ORDER_DATE" 		=> date("d-m-Y H:i"),
	"BCC" 				=> COption::GetOptionString("sale", "order_email", "order@".$SERVER_NAME),
	"SALE_EMAIL" 		=> COption::GetOptionString("sale", "order_email", "order@".$SERVER_NAME),
);

// save result
$rsEM = CEventMessage::GetList($by="site_id", $order="desc", array("TYPE_ID"=>"SALE_CHEAP_ORDER", "SITE_ID"=>$SITE_ID));
$arEM = $rsEM->Fetch();
$content = $arEM["MESSAGE"];
		
$IBLOCK_ID = COption::GetOptionInt("streetstyle", "catalogCheapID", false, $SITE_ID);
if($IBLOCK_ID)
{ 		
	$el = new CIBlockElement;
	$ID = $el->Add( array(
	 
		  "IBLOCK_SECTION_ID" 	=> false,
		  "IBLOCK_ID"         	=> $IBLOCK_ID,
		  "PREVIEW_TEXT"      	=> $content,
		  "PREVIEW_TEXT_TYPE" 	=> "html",
		  "NAME"              	=> $arFields["ELEMENT_NAME"],
		  "ACTIVE"            	=> "Y",
		  "DATE_ACTIVE_FROM"	=> ConvertTimeStamp(time(), "FULL"),
		  "PROPERTY_VALUES"		=> array(
										  "NAME"	=>	$arFields["NAME"], 
										  "PHONE"	=>	$arFields["PHONE"], 
										  "URL"		=>	$arFields["URL"], 
										  "COMMENT"	=>	$arFields["COMMENT"],
										),
	));
	
	$res = CIBlock::GetByID($IBLOCK_ID);
	$ar_res = $res->GetNext();	
	$arFields["EDIT_URL"] = "/bitrix/admin/iblock_element_edit.php?ID=".$ID."&type=".$ar_res["IBLOCK_TYPE_ID"]."&lang=".$LANG."&IBLOCK_ID=".$IBLOCK_ID;
	
	foreach($arFields as $key=>$value)
		$content = str_replace("#".$key."#", $value, $content);
	
	$el->Update($ID, array(		
		  "IBLOCK_SECTION_ID" 	=> false,
		  "IBLOCK_ID"         	=> $IBLOCK_ID,
		  "PREVIEW_TEXT"      	=> $content,
		  "PREVIEW_TEXT_TYPE" 	=> "html",
	));	
}

$event = new CEvent;
$event->SendImmediate("SALE_CHEAP_ORDER", $SITE_ID, $arFields, "N");
?>