<?
/**
 * Copyright (c) 2019 Created by ASDAFF asdaff.asad@yandex.ru
 */

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

if(!isset($_REQUEST['ajax']) && !isset($_REQUEST["ajax_start"]) && !isset($_REQUEST["ajax_count"]) && !isset($_POST["auth"])):
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sota.parser/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sota.parser/prolog.php");

//require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sota.parser/classes/mysql/list_parser.php");
//require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sota.parser/classes/general/rss_content_parser.php");
define("HELP_FILE", "add_issue.php");
CJSCore::Init(array("jquery"));



?>

<?
if(!CModule::IncludeModule('iblock')) return false;
CModule::IncludeModule('catalog');
CModule::IncludeModule("highloadblock");
CModule::IncludeModule('sota.parser');

IncludeModuleLangFile(__FILE__);
global $sota_DEMO;
$POST_RIGHT = $APPLICATION->GetGroupRight("sota.parser");
if($POST_RIGHT=="D")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$parentID = 0;
if(isset($_REQUEST["parent"]) && $_REQUEST["parent"])
{
    $parentID = $_REQUEST["parent"];
}
/*$aTabs = array(
    array("DIV" => "edit1", "TAB" => GetMessage("parser_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_tab")),
    array("DIV" => "edit2", "TAB" => GetMessage("parser_preview_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_preview_tab")),
    array("DIV" => "edit3", "TAB" => GetMessage("parser_detail_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_detail_tab")),
    array("DIV" => "edit4", "TAB" => GetMessage("parser_settings_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_settings_tab")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);*/


$ID = intval($ID);        // Id of the edited record
$bCopy = ($action == "copy");
$message = null;
$bVarsFromForm = false;

/*function sotaParserSetSettings(&$SETTINGS)
{
    foreach($SETTINGS as &$v)
    {
        if(is_array($v)) sotaParserSetSettings($v);
        else $v = htmlspecialcharsEx($v);
    }
}

function sotaParserGetSettings(&$SETTINGS)
{
    foreach($SETTINGS as &$v)
    {
        if(is_array($v)) sotaParserGetSettings($v);
        else $v = htmlspecialcharsBack($v);
    }
}*/

if($REQUEST_METHOD == "POST" && ($save!="" || $apply!="") && $POST_RIGHT=="W" && check_bitrix_sessid())
{
    $parser = new SotaParserContent();
    RssContentParser::sotaParserSetSettings($SETTINGS);

    $arFields = Array(
        "NAME"    => $NAME,
        "TYPE"    => $TYPE,
        "RSS"    => $RSS,
        "SORT"    => $SORT,
        "ACTIVE"    => ($ACTIVE <> "Y"? "N":"Y"),
        "IBLOCK_ID"    => $IBLOCK_ID,
        "SECTION_ID" => $SECTION_ID,
        "SELECTOR"    => $SELECTOR,
        "FIRST_URL"    => $FIRST_URL,
        "ENCODING"    => $ENCODING,
        "PREVIEW_TEXT_TYPE" => $PREVIEW_TEXT_TYPE,
        "DETAIL_TEXT_TYPE" => $DETAIL_TEXT_TYPE,
        "PREVIEW_DELETE_TAG" => $PREVIEW_DELETE_TAG,
        "DETAIL_DELETE_TAG" => $DETAIL_DELETE_TAG,
        "PREVIEW_FIRST_IMG" => ($PREVIEW_FIRST_IMG <> "Y"? "N":"Y"),
        "DETAIL_FIRST_IMG" => ($DETAIL_FIRST_IMG <> "Y"? "N":"Y"),
        "PREVIEW_SAVE_IMG" => ($PREVIEW_SAVE_IMG <> "Y"? "N":"Y"),
        "DETAIL_SAVE_IMG" => ($DETAIL_SAVE_IMG <> "Y"? "N":"Y"),
        "BOOL_PREVIEW_DELETE_TAG" =>($BOOL_PREVIEW_DELETE_TAG <> "Y"? "N":"Y"),
        "BOOL_DETAIL_DELETE_TAG" =>($BOOL_DETAIL_DELETE_TAG <> "Y"? "N":"Y"),
        "PREVIEW_DELETE_ELEMENT" => $PREVIEW_DELETE_ELEMENT,
        "DETAIL_DELETE_ELEMENT" => $DETAIL_DELETE_ELEMENT,
        "PREVIEW_DELETE_ATTRIBUTE" => $PREVIEW_DELETE_ATTRIBUTE,
        "DETAIL_DELETE_ATTRIBUTE" => $DETAIL_DELETE_ATTRIBUTE,
        "INDEX_ELEMENT" => ($INDEX_ELEMENT <> "Y"? "N":"Y"),
        "CODE_ELEMENT" => ($CODE_ELEMENT <> "Y"? "N":"Y"),
        "RESIZE_IMAGE" => ($RESIZE_IMAGE <> "Y"? "N":"Y"),
        "CREATE_SITEMAP" => ($CREATE_SITEMAP <> "Y"? "N":"Y"),
        "DATE_ACTIVE" => ($DATE_ACTIVE <> "Y"? "N":$DATE_PROP_ACTIVE),
        "DATE_PUBLIC" => ($DATE_PUBLIC <> "Y"? "N":$DATE_PROP_PUBLIC),
        "FIRST_TITLE" => ($FIRST_TITLE <> "Y"? "N":$FIRST_PROP_TITLE),
        "META_TITLE" => ($META_TITLE <> "Y"? "N":$META_PROP_TITLE),
        "META_DESCRIPTION" => ($META_DESCRIPTION <> "Y"? "N":$META_PROP_DESCRIPTION),
        "META_KEYWORDS" => ($META_KEYWORDS <> "Y"? "N":$META_PROP_KEYWORDS),
        "START_AGENT" => ($START_AGENT <> "Y"? "N":"Y"),
        "TIME_AGENT" => $TIME_AGENT,
        "ACTIVE_ELEMENT" => ($ACTIVE_ELEMENT <> "Y"? "N":"Y"),
        "SETTINGS" => base64_encode(serialize($SETTINGS)),
        "CATEGORY_ID"    => $CATEGORY_ID,
        //"START_LAST_TIME" => $START_LAST_TIME
    );
    if($ID>0)
    {
        $res = $parser->Update($ID, $arFields);

    }
    else
    {
        $ID = $parser->Add($arFields);
        $res = ($ID>0);
    }
 
    if($res)
    {
        if($apply!="")
            LocalRedirect("/bitrix/admin/parser_edit.php?ID=".$ID."&mess=ok&lang=".LANG."&tabControl_active_tab=".$_POST["tabControl_active_tab"]);
        else
            LocalRedirect("/bitrix/admin/list_parser_admin.php?lang=".LANG);
    }
    else
    {
        if($e = $APPLICATION->GetException())
            $message = new CAdminMessage(GetMessage("parser_save_error"), $e);
        $bVarsFromForm = true;
    }

}
//Edit/Add part
ClearVars();


if($ID>0 || $copy)
{
    if($ID)$parser = SotaParserContent::GetByID($ID);
    elseif($copy) $parser = SotaParserContent::GetByID($copy);
    if(!$parser->ExtractFields("sota_"))
        $ID=0;
    if($ID>0 && $sota_TIME_AGENT>0){
        $arAgent = CAgent::GetList(array(), array("NAME"=>"CSotaParser::startAgent(".$ID.");"))->Fetch();
        if(!$arAgent && $sota_START_AGENT=="Y"){CAgent::AddAgent(
            "CSotaParser::startAgent(".$ID.");", // имя функции
            "sota.parser",                          // идентификатор модуля
            "N",                                  // агент не критичен к кол-ву запусков
            $sota_TIME_AGENT,                                // интервал запуска - 1 сутки
            "",                // дата первой проверки на запуск
            "Y",                                  // агент активен
            "",                // дата первого запуска
            100
          );}
        elseif($arAgent){
            CAgent::Update($arAgent['ID'], array(
                "AGENT_INTERVAL"=>$sota_TIME_AGENT,
                "ACTIVE"=>$sota_START_AGENT=="Y"?"Y":"N"
            ));
        }
    }
    
    
    $properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$sota_IBLOCK_ID, "PROPERTY_TYPE"=>"S"));
    while($arProp = $properties->Fetch())
    {   //printr($arProp);
        $arrProp['REFERENCE'][] = "[".$arProp["CODE"]."] ".$arProp["NAME"];
        $arrProp['REFERENCE_ID'][] = $arProp["CODE"];
    }

    $properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$sota_IBLOCK_ID, "PROPERTY_TYPE"=>"F"));
    while($arProp = $properties->Fetch())
    {
        $arrPropFile['REFERENCE'][] = "[".$arProp["CODE"]."] ".$arProp["NAME"];
        $arrPropFile['REFERENCE_ID'][] = $arProp["CODE"];
    }

    $properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$sota_IBLOCK_ID));

    $arrPropDop['REFERENCE'][] = GetMessage("sota_parser_select_prop_new");
    $arrPropDop['REFERENCE_ID'][] = "[]";
    
    $arrPropField['REFERENCE'][] = GetMessage("parser_sota_PARSER_NAME_E");
    $arrPropField['REFERENCE_ID'][] = "sota_PARSER_NAME_E";
    
    while($arProp = $properties->Fetch())
    {

        if($arProp["PROPERTY_TYPE"]=="S")
        {
            $arrPropField['REFERENCE'][] = $arProp["NAME"];
            $arrPropField['REFERENCE_ID'][] = $arProp["CODE"];    
        }
        if($arProp["PROPERTY_TYPE"]=="L" || $arProp["PROPERTY_TYPE"]=="N" || $arProp["PROPERTY_TYPE"]=="S" || $arProp["PROPERTY_TYPE"]=="E" || $arProp["PROPERTY_TYPE"]=="F")
        {
            $arrPropDop['REFERENCE'][] = $arProp["NAME"];
            $arrPropDop['REFERENCE_ID'][] = $arProp["CODE"];
            $arrPropDop['REFERENCE_TYPE'][$arProp["CODE"]] = $arProp["PROPERTY_TYPE"];
            $arrPropDop['USER_TYPE'][$arProp["CODE"]] = $arProp["USER_TYPE"];
            $arrPropDop['REFERENCE_CODE_NAME'][$arProp["CODE"]] = $arProp["NAME"];
        }
        
        if($arProp["PROPERTY_TYPE"]=="L"/* && $arProp["ID"]==14*/)
        {
            $rsEnum = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$sota_IBLOCK_ID, "property_id"=>$arProp["ID"]));
            $arrPropDop["LIST_VALUES"][$arProp["CODE"]]["REFERENCE"][] = GetMessage("parser_prop_default");
            $arrPropDop["LIST_VALUES"][$arProp["CODE"]]["REFERENCE_ID"][] = "";
            while($arEnum = $rsEnum->Fetch())
            {
                $arrPropDop["LIST_VALUES"][$arProp["CODE"]]["REFERENCE"][] = $arEnum["VALUE"];
                $arrPropDop["LIST_VALUES"][$arProp["CODE"]]["REFERENCE_ID"][] = $arEnum["ID"];
            }
        }
        if($arProp['USER_TYPE']=="directory")
        {
            $nameTable = $arProp["USER_TYPE_SETTINGS"]["TABLE_NAME"];
            if($nameTable)
            {
                $directorySelect = array("*");
                $directoryOrder = array();
                $entityGetList = array(
                    'select' => $directorySelect,
                    'order' => $directoryOrder
                );
                $highBlock = \Bitrix\Highloadblock\HighloadBlockTable::getList(array("filter" => array('TABLE_NAME' => $nameTable)))->fetch();
                $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($highBlock);
                $entityDataClass = $entity->getDataClass();
                $propEnums = $entityDataClass::getList($entityGetList);
                $arrPropDop["LIST_VALUES"][$arProp["CODE"]]["REFERENCE"][] = GetMessage("parser_prop_default");
                $arrPropDop["LIST_VALUES"][$arProp["CODE"]]["REFERENCE_ID"][] = "";
                while ($oneEnum = $propEnums->fetch())
                {
                    $arrPropDop["LIST_VALUES"][$arProp["CODE"]]["REFERENCE"][] = $oneEnum["UF_NAME"];
                    $arrPropDop["LIST_VALUES"][$arProp["CODE"]]["REFERENCE_ID"][] = $oneEnum["UF_XML_ID"];
                }    
            }
            
        }
    }
}

/*if($bVarsFromForm)
    $DB->InitTableVarsForEdit("b_sota_list_parser", "", "sota_");*/

$APPLICATION->SetTitle(($ID>0? GetMessage("parser_title_edit") : GetMessage("parser_title_add")));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$aMenu = array(
    array(
        "TEXT"=>GetMessage("parser_list"),
        "TITLE"=>GetMessage("parser_list_title"),
        "LINK"=>"list_parser_admin.php?parent=".$parentID."&lang=".LANG,
        "ICON"=>"btn_list",
    )
);
if($ID>0)
{
    $aMenu[] = array("SEPARATOR"=>"Y");
    $aMenu[] = array(
        "TEXT"=>GetMessage("parser_add"),
        "TITLE"=>GetMessage("rubric_mnu_add"),
        "LINK"=>"parser_edit.php?lang=".LANG,
        "ICON"=>"btn_new",
    );
    $aMenu[] = array(
        "TEXT"=>GetMessage("parser_copy"),
        "TITLE"=>GetMessage("rubric_mnu_copy"),
        "LINK"=>"parser_edit.php?copy=".$ID."&lang=".LANG,
        "ICON"=>"btn_copy",
    );
    $aMenu[] = array(
        "TEXT"=>GetMessage("parser_delete"),
        "TITLE"=>GetMessage("parser_mnu_del"),
        "LINK"=>"javascript:if(confirm('".GetMessage("parser_mnu_del_conf")."'))window.location='list_parser_admin.php?ID=".$ID."&action=delete&lang=".LANG."&".bitrix_sessid_get()."';",
        "ICON"=>"btn_delete",
    );

    if($sota_ACTIVE=="Y"){
        $aMenu[] = array("SEPARATOR"=>"Y");
        if($sota_TYPE=="catalog" || $_GET["type"]=="catalog"):
        $aMenu[] = array(
            "TEXT"=>GetMessage("parser_start"),
            "TITLE"=>GetMessage("parser_start_title"),
            "LINK"=>"parser_edit.php?start=1&lang=".LANG."&ID=".$ID,
            "ICON"=>"btn_start_catalog"
        );
        elseif($sota_TYPE=="page" || $_GET["type"]=="page" || $sota_TYPE=="rss" || $_GET["type"]=="rss"):
        $aMenu[] = array(
            "TEXT"=>GetMessage("parser_start"),
            "TITLE"=>GetMessage("parser_start_title"),
            "LINK"=>"parser_edit.php?start=1&lang=".LANG."&ID=".$ID,
            "ICON"=>"btn_start"
        );
        elseif($sota_TYPE=="xml" || $_GET["type"]=="xml"):
        $aMenu[] = array(
            "TEXT"=>GetMessage("parser_start"),
            "TITLE"=>GetMessage("parser_start_title"),
            "LINK"=>"parser_edit.php?start=1&lang=".LANG."&ID=".$ID,
            "ICON"=>"btn_start_xml"
        );
        endif;
    }

}
$context = new CAdminContextMenu($aMenu);
$context->Show();

$rsSection = SotaParserSectionTable::getList(array(
    'limit' =>null,
    'offset' => null,
    'select' => array("*"),
    "filter" => array()
));

$parentID = 0;
if(isset($_REQUEST["parent"]) && $_REQUEST["parent"])
{
    $parentID = $_REQUEST["parent"];
}

while($arSection = $rsSection->Fetch())
{
    $arCategory['REFERENCE'][] = "[".$arSection["ID"]."] ".$arSection["NAME"];
    $arCategory['REFERENCE_ID'][] = $arSection["ID"];
}

$arLocType['reference'] = array(GetMessage("parser_loc_no"), GetMessage("parser_loc_yandex"));
$arLocType['reference_id'] = array('', 'yandex');

if(isset($_REQUEST['start']) && $ID>0){
    $rssParser = new RssContentParser();
    $result = $rssParser->startParser();
    if(isset($result[SUCCESS][0]))
    foreach($result[SUCCESS] as $i=>$success){
      $resultUrl .= "&SUCCESS[".$i."]=".urlencode($success);
    }
    if(isset($result[ERROR][0]))
     foreach($result[ERROR] as $i=>$error){
      $resultUrl .= "&ERROR[".$i."]=".urlencode($error);
    }
    if(!RssContentParser::TEST)LocalRedirect($APPLICATION->GetCurPageParam("end=1".$resultUrl, array("start")));
}

/***
**** Парсинг каталог и XML
***/

if($sota_TYPE=="catalog" || $_GET["type"]=="catalog" || $sota_TYPE=="xml" || $_GET["type"]=="xml")
{   $isOfferCatalog = false;
    if(isset($sota_IBLOCK_ID) && $sota_IBLOCK_ID && CModule::IncludeModule('catalog'))
    {
        $arIblock = CCatalogSKU::GetInfoByIBlock($sota_IBLOCK_ID);
        //printr($arIblock);
        if(is_array($arIblock) && !empty($arIblock) && $arIblock["PRODUCT_IBLOCK_ID"]!=0 && $arIblock["SKU_PROPERTY_ID"]!=0)$isOfferCatalog = true;
        
        
        if($arIblock["IBLOCK_ID"] && $arIblock["PRODUCT_IBLOCK_ID"])
            $OFFER_IBLOCK_ID = $arIblock["IBLOCK_ID"];
        if($OFFER_IBLOCK_ID)
        {
            $properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$OFFER_IBLOCK_ID));
    
            $arrPropDopOffer['REFERENCE'][] = GetMessage("sota_parser_select_prop_new");
            $arrPropDopOffer['REFERENCE_ID'][] = "[]";
            
            while($arProp = $properties->Fetch())
            {

                if($arProp["PROPERTY_TYPE"]=="L" || $arProp["PROPERTY_TYPE"]=="N" || $arProp["PROPERTY_TYPE"]=="S" || $arProp["PROPERTY_TYPE"]=="E" || $arProp["PROPERTY_TYPE"]=="F")
                {
                    $arrPropDopOffer['REFERENCE'][] = $arProp["NAME"];
                    $arrPropDopOffer['REFERENCE_ID'][] = $arProp["CODE"];
                    $arrPropDopOfferName['REFERENCE'][] = $arProp["NAME"];
                    $arrPropDopOfferName['REFERENCE_ID'][] = $arProp["CODE"];
                    $arrPropDopOffer['REFERENCE_TYPE'][$arProp["CODE"]] = $arProp["PROPERTY_TYPE"];
                    $arrPropDopOffer['USER_TYPE'][$arProp["CODE"]] = $arProp["USER_TYPE"];
                    $arrPropDopOffer['REFERENCE_CODE_NAME'][$arProp["CODE"]] = $arProp["NAME"];
                }
                
                if($arProp["PROPERTY_TYPE"]=="L"/* && $arProp["ID"]==14*/)
                {
                    $rsEnum = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$OFFER_IBLOCK_ID, "property_id"=>$arProp["ID"]));
                    $arrPropDopOffer["LIST_VALUES"][$arProp["CODE"]]["REFERENCE"][] = GetMessage("parser_prop_default");
                    $arrPropDopOffer["LIST_VALUES"][$arProp["CODE"]]["REFERENCE_ID"][] = "";
                    while($arEnum = $rsEnum->Fetch())
                    {
                        $arrPropDopOffer["LIST_VALUES"][$arProp["CODE"]]["REFERENCE"][] = $arEnum["VALUE"];
                        $arrPropDopOffer["LIST_VALUES"][$arProp["CODE"]]["REFERENCE_ID"][] = $arEnum["ID"];
                    }
                }
                if($arProp['USER_TYPE']=="directory")
                {
                    $nameTable = $arProp["USER_TYPE_SETTINGS"]["TABLE_NAME"];
                    $directorySelect = array("*");
                    $directoryOrder = array();
                    $entityGetList = array(
                        'select' => $directorySelect,
                        'order' => $directoryOrder
                    );
                    $highBlock = \Bitrix\Highloadblock\HighloadBlockTable::getList(array("filter" => array('TABLE_NAME' => $nameTable)))->fetch();
                    $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($highBlock);
                    $entityDataClass = $entity->getDataClass();
                    $propEnums = $entityDataClass::getList($entityGetList);
                    $arrPropDopOffer["LIST_VALUES"][$arProp["CODE"]]["REFERENCE"][] = GetMessage("parser_prop_default");
                    $arrPropDopOffer["LIST_VALUES"][$arProp["CODE"]]["REFERENCE_ID"][] = "";
                    while ($oneEnum = $propEnums->fetch())
                    {
                        $arrPropDopOffer["LIST_VALUES"][$arProp["CODE"]]["REFERENCE"][] = $oneEnum["UF_NAME"];
                        $arrPropDopOffer["LIST_VALUES"][$arProp["CODE"]]["REFERENCE_ID"][] = $oneEnum["UF_XML_ID"];
                    }
                }
            }    
        }    
            
            
    }
    
    /***
    **** Табы для каталога
    ***/
    
    if ($sota_TYPE=="catalog" || $_GET["type"]=="catalog")
    {
        if(CModule::IncludeModule('catalog') && (($sota_IBLOCK_ID && CCatalog::GetList(Array("name" => "asc"), Array("ACTIVE"=>"Y", "ID"=>$sota_IBLOCK_ID))->Fetch()) || (is_array($arIblock) && !empty($arIblock) && $arIblock["PRODUCT_IBLOCK_ID"]!=0 && $arIblock["SKU_PROPERTY_ID"]!=0)  || !$sota_IBLOCK_ID))
        {
            //unset($aTabs[5]);
            $aTabs = array(
                array("DIV" => "edit1", "TAB" => GetMessage("parser_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_tab")),
                array("DIV" => "edit2", "TAB" => GetMessage("parser_pagenavigation_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_pagenavigation_tab")),
                array("DIV" => "edit3", "TAB" => GetMessage("parser_preview_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_preview_tab")),
                array("DIV" => "edit4", "TAB" => GetMessage("parser_detail_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_detail_tab")),
                array("DIV" => "edit5", "TAB" => GetMessage("parser_props_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_props_tab")),
                array("DIV" => "edit6", "TAB" => GetMessage("parser_catalog_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_catalog_tab")),
                array("DIV" => "edit7", "TAB" => GetMessage("parser_offer_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_offer_tab")),
                array("DIV" => "edit8", "TAB" => GetMessage("parser_settings_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_settings_tab")),
                array("DIV" => "edit9", "TAB" => GetMessage("parser_uniq_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_uniq_tab")),
                array("DIV" => "edit10", "TAB" => GetMessage("parser_auth"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_auth")),
                array("DIV" => "edit11", "TAB" => GetMessage("parser_logs_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_logs_tab")),
                array("DIV" => "edit12", "TAB" => GetMessage("parser_local_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_local_tab")),
            );
            $isCatalog = true;
        }else{
            $aTabs = array(
                array("DIV" => "edit1", "TAB" => GetMessage("parser_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_tab")),
                array("DIV" => "edit2", "TAB" => GetMessage("parser_pagenavigation_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_pagenavigation_tab")),
                array("DIV" => "edit3", "TAB" => GetMessage("parser_preview_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_preview_tab")),
                array("DIV" => "edit4", "TAB" => GetMessage("parser_detail_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_detail_tab")),
                array("DIV" => "edit5", "TAB" => GetMessage("parser_props_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_props_tab")),
                array("DIV" => "edit7", "TAB" => GetMessage("parser_settings_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_settings_tab")),
                array("DIV" => "edit8", "TAB" => GetMessage("parser_uniq_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_uniq_tab")),
                array("DIV" => "edit9", "TAB" => GetMessage("parser_auth"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_auth")),
                array("DIV" => "edit10", "TAB" => GetMessage("parser_logs_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_logs_tab")),
                array("DIV" => "edit11", "TAB" => GetMessage("parser_local_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_local_tab")),
            );
            $isCatalog = false;
        }
    }
    
    /***
    **** Табы для XML
    ***/
    
    if ($sota_TYPE=="xml" || $_GET["type"]=="xml")
    {
        if(CModule::IncludeModule('catalog') && (($sota_IBLOCK_ID && CCatalog::GetList(Array("name" => "asc"), Array("ACTIVE"=>"Y", "ID"=>$sota_IBLOCK_ID))->Fetch()) || (is_array($arIblock) && !empty($arIblock) && $arIblock["PRODUCT_IBLOCK_ID"]!=0 && $arIblock["SKU_PROPERTY_ID"]!=0)  || !$sota_IBLOCK_ID))
        {
            $aTabs = array(
                array("DIV" => "edit1", "TAB" => GetMessage("parser_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_tab")),
                //array("DIV" => "edit2", "TAB" => GetMessage("parser_pagenavigation_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_pagenavigation_tab")),
                array("DIV" => "edit2", "TAB" => GetMessage("parser_basic_settings_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_basic_settings_tab")),
                //array("DIV" => "edit4", "TAB" => GetMessage("parser_detail_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_detail_tab")),
                array("DIV" => "edit3", "TAB" => GetMessage("parser_props_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_props_tab")),
                array("DIV" => "edit4", "TAB" => GetMessage("parser_catalog_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_catalog_tab")),
                array("DIV" => "edit5", "TAB" => GetMessage("parser_offer_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_offer_tab")),
                array("DIV" => "edit6", "TAB" => GetMessage("parser_settings_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_settings_tab")),
                array("DIV" => "edit7", "TAB" => GetMessage("parser_uniq_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_uniq_tab")),
                array("DIV" => "edit8", "TAB" => GetMessage("parser_auth"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_auth")),
                array("DIV" => "edit9", "TAB" => GetMessage("parser_logs_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_logs_tab")),
                array("DIV" => "edit10", "TAB" => GetMessage("parser_local_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_local_tab")),
            );
            $isCatalog = true;
        }else{
            $aTabs = array(
                array("DIV" => "edit1", "TAB" => GetMessage("parser_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_tab")),
                //array("DIV" => "edit2", "TAB" => GetMessage("parser_pagenavigation_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_pagenavigation_tab")),
                array("DIV" => "edit2", "TAB" => GetMessage("parser_preview_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_preview_tab")),
                //array("DIV" => "edit4", "TAB" => GetMessage("parser_detail_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_detail_tab")),
                array("DIV" => "edit3", "TAB" => GetMessage("parser_props_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_props_tab")),
                array("DIV" => "edit4", "TAB" => GetMessage("parser_settings_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_settings_tab")),
                array("DIV" => "edit5", "TAB" => GetMessage("parser_uniq_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_uniq_tab")),
                array("DIV" => "edit6", "TAB" => GetMessage("parser_auth"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_auth")),
                array("DIV" => "edit7", "TAB" => GetMessage("parser_logs_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_logs_tab")),
                array("DIV" => "edit8", "TAB" => GetMessage("parser_local_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_local_tab")),
            );
            $isCatalog = false;
        }
    }
    
    //$rsIBlock = CCatalog::GetList(Array("name" => "asc"), Array("ACTIVE"=>"Y"));
    $rsIBlock = CIBlock::GetList(Array("name" => "asc"), Array("ACTIVE"=>"Y"));
    while($arr=$rsIBlock->Fetch()){
        $arIBlock['REFERENCE'][] = "[".$arr["ID"]."] ".$arr["NAME"];
        $arIBlock['REFERENCE_ID'][] = $arr["ID"];
    }
}

/***
**** RSS и PAGE
***/

else{
    $rsIBlock = CIBlock::GetList(Array("name" => "asc"), Array("ACTIVE"=>"Y"));
    while($arr=$rsIBlock->Fetch()){
        $arIBlock['REFERENCE'][] = "[".$arr["ID"]."] ".$arr["NAME"];
        $arIBlock['REFERENCE_ID'][] = $arr["ID"];
    }

    $aTabs = array(
        array("DIV" => "edit1", "TAB" => GetMessage("parser_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_tab")),
        array("DIV" => "edit2", "TAB" => GetMessage("parser_preview_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_preview_tab")),
        array("DIV" => "edit3", "TAB" => GetMessage("parser_detail_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_detail_tab")),
        array("DIV" => "edit4", "TAB" => GetMessage("parser_settings_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_settings_tab")),
        array("DIV" => "edit5", "TAB" => GetMessage("parser_local_tab"), "ICON"=>"main_user_edit", "TITLE"=>GetMessage("parser_local_tab")),
    );
}
$tabControl = new CAdminTabControl("tabControl", $aTabs);



if(!empty($sota_IBLOCK_ID)){
    $rsSections = CIBlockSection::GetList(array("left_margin"=>"asc"), array(/*'ACTIVE'=>"Y",*/ "IBLOCK_ID"=>$sota_IBLOCK_ID), false, array('ID', 'NAME', "IBLOCK_ID", "DEPTH_LEVEL"));

    while($arr=$rsSections->Fetch()){
        $arr["NAME"] = str_repeat(" . ", $arr["DEPTH_LEVEL"]).$arr["NAME"];
        $arSection['REFERENCE'][] = $arr["NAME"];
        $arSection['REFERENCE_ID'][] = $arr["ID"];
    }



}
$arrDateActive['REFERENCE'][0] =  GetMessage("parser_date_active_now");
$arrDateActive['REFERENCE'][1] =  GetMessage("parser_date_active_now_time");
$arrDateActive['REFERENCE'][2] =  GetMessage("parser_date_active_public");
$arrDateActive['REFERENCE_ID'][0] = "NOW";
$arrDateActive['REFERENCE_ID'][1] = "NOW_TIME";
$arrDateActive['REFERENCE_ID'][2] = "PUBLIC";
?>

<div id="status_bar" style="display:none;overflow:hidden;">
    <div id="progress_bar" style="width: 500px;float:left;" class="adm-progress-bar-outer">
        <div id="progress_bar_inner" style="width: 0px;" class="adm-progress-bar-inner"></div>
        <div id="progress_text" style="width: 500px;" class="adm-progress-bar-inner-text">0%</div>
    </div>
    <div id="catalog_bar" style="float:left;width:700px;height:62px;line-height:20px;font-weight:bold;margin-left:30px;"></div>
    <div id="current_test"></div>
</div>
<div style="clear:both;"></div>
<?
if(isset($_REQUEST["mess"]) && $_REQUEST["mess"] == "ok" && $ID>0)
    CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("parser_saved"), "TYPE"=>"OK"));

if($message)
    echo $message->Show();
elseif($rubric->LAST_ERROR!="")
    CAdminMessage::ShowMessage($rubric->LAST_ERROR);

if(isset($_REQUEST['end']) && $_REQUEST['end']==1 && $ID>0){
    if(isset($_GET['SUCCESS'][0])){
      foreach($_GET['SUCCESS'] as $success) CAdminMessage::ShowMessage(array("MESSAGE"=>$success, "TYPE"=>"OK"));
    }
    if(isset($_GET['ERROR'][0])){
        foreach($_GET['ERROR'] as $error) CAdminMessage::ShowMessage($error);
    }

}
$sota_SETTINGS = (string)$sota_SETTINGS;
$sota_SETTINGS = unserialize(base64_decode($sota_SETTINGS));

$sotaDebug = $sota_SETTINGS["catalog"]["mode"];


if($sota_DEMO==2)CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("parser_demo")));
if($sota_DEMO==3)CAdminMessage::ShowMessage(array("MESSAGE"=>GetMessage("parser_demo_end")));
?>
<div id="sota_message"></div>
<form method="POST" id="sota-parser" Action="<?echo $APPLICATION->GetCurPage()?>" ENCTYPE="multipart/form-data" name="post_form">
<?
$tabControl->Begin();


if($sota_TYPE=="page" || (isset($_GET["type"]) && $_GET["type"]=="page")) include("parser_edit_page.php");
elseif($sota_TYPE=="catalog" || (isset($_GET["type"]) && $_GET["type"]=="catalog")) include("parser_edit_catalog.php");
elseif($sota_TYPE=="xml" || (isset($_GET["type"]) && $_GET["type"]=="xml")) include("parser_edit_xml.php");
elseif((!$sota_TYPE && $ID) || $sota_TYPE=="rss" || (isset($_GET["type"]) && $_GET["type"]=="rss") || !isset($ID) || !$ID) include("parser_edit_rss.php");
?>






<?echo BeginNote();?>
<span class="required">*</span><?echo GetMessage("REQUIRED_FIELDS")?>
<?echo EndNote();?>
<script language="JavaScript">
            var target_id = '';
            var target_select_id = '';
            var target_shadow_id = '';
            function addSectionProperty(iblock_id, select_id, tr, table_id)
            {   
                {
                    target_id = table_id;
                    target_select_id = select_id;
                    target_shadow_id = tr;
                    (new BX.CDialog({
                        'content_url' : '/bitrix/admin/iblock_edit_property.php?lang=<?echo LANGUAGE_ID?>&IBLOCK_ID='+iblock_id+'&ID=n0&bxpublic=Y&from_module=iblock&return_url=section_edit',
                        'width' : 700,
                        'height' : 400,
                        'buttons': [BX.CDialog.btnSave, BX.CDialog.btnCancel]
                    })).Show();
                }
            }
            function deleteSectionProperty(id, select_id, shadow_id, table_id)
            {
                var hidden = BX('hidden_SECTION_PROPERTY_' + id);
                var tr = BX('tr_SECTION_PROPERTY_' + id);
                if(hidden && tr)
                {
                    hidden.value = 'N';
                    tr.style.display = 'none';
                    var select = BX(select_id);
                    var shadow = BX(shadow_id);
                    if(select && shadow)
                    {
                        jsSelectUtils.deleteAllOptions(select);
                        for(var i = 0; i < shadow.length; i++)
                        {
                            if(shadow[i].value <= 0)
                                jsSelectUtils.addNewOption(select, shadow[i].value, shadow[i].text);
                            else if (BX('hidden_SECTION_PROPERTY_' + shadow[i].value).value == 'N')
                                jsSelectUtils.addNewOption(select, shadow[i].value, shadow[i].text);
                        }
                    }
                    adjustEmptyTR(table_id);
                }
            }
            function createSectionProperty(id, name, type)
            {   
                jQuery.ajax({
                    url: "",
                    type: "POST",
                    data: 'ajax=1&prop_id='+id,
                    dataType: 'html',
                    success: function(code){
                        code = $.trim(code);
                        if(target_select_id=="loadDopPropOffer")
                        {
                            str = '<tr><td width="40%" class="adm-detail-content-cell-l">'+name+'&nbsp;['+code+']:</td><td width="60%" class="adm-detail-content-cell-r"><input type="text" value="" name="SETTINGS[offer][selector_prop]['+code+']" data-code="'+code+'" size="40">&nbsp;<a href="#" class="prop_delete">Delete</a></td></tr>';
                            target_shadow_id.before(str);
                            
                            strName = '<option value="'+code+'">'+name+'</option>'; 
                            $(".add_name").append(strName);   
                        }else if(target_select_id=="loadDopPropOffer1")
                        {
                            str = '<tr><td width="40%" class="adm-detail-content-cell-l">'+name+'&nbsp;['+code+']:</td><td width="60%" class="adm-detail-content-cell-r"><input type="text" value="" name="SETTINGS[offer][find_prop]['+code+']" data-code="'+code+'" size="40">&nbsp;<a href="#" class="prop_delete">Delete</a></td></tr>';
                            target_shadow_id.before(str);
                            strName = '<option value="'+code+'">'+name+'</option>';
                            $(".add_name").append(strName);    
                        }
                        else if(target_select_id=="loadDopProp")
                        {
                            str = '<tr><td width="40%" class="adm-detail-content-cell-l">'+name+'&nbsp;['+code+']:</td><td width="60%" class="adm-detail-content-cell-r"><input type="text" value="" name="SETTINGS[catalog][selector_prop]['+code+']" data-code="'+code+'" size="40">&nbsp;<a href="#" class="prop_delete">Delete</a></td></tr>';
                            target_shadow_id.before(str);
                    
                        }else if(target_select_id=="loadDopProp1")
                        {
                            str = '<tr><td width="40%" class="adm-detail-content-cell-l">'+name+'&nbsp;['+code+']:</td><td width="60%" class="adm-detail-content-cell-r"><input type="text" value="" name="SETTINGS[catalog][find_prop]['+code+']" data-code="'+code+'" size="40">&nbsp;<a href="#" class="prop_delete">Delete</a></td></tr>';
                            target_shadow_id.before(str);    
                        }else if(target_select_id=="loadDopProp2")
                        {
                            str = '<tr><td width="40%" class="adm-detail-content-cell-l">'+name+'&nbsp;['+code+']:</td><td width="60%" class="adm-detail-content-cell-r"><input type="text" value="" name="SETTINGS[catalog][selector_prop_preview]['+code+']" data-code="'+code+'" size="40">&nbsp;<a href="#" class="prop_delete">Delete</a></td></tr>';
                            target_shadow_id.before(str);    
                        }else if(target_select_id=="loadDopProp3")
                        {
                            str = '<tr><td width="40%" class="adm-detail-content-cell-l">'+name+'&nbsp;['+code+']:</td><td width="60%" class="adm-detail-content-cell-r"><input type="text" value="" name="SETTINGS[catalog][find_prop_preview]['+code+']" data-code="'+code+'" size="40">&nbsp;<a href="#" class="prop_delete">Delete</a></td></tr>';
                            target_shadow_id.before(str);    
                        }
                        else if(target_select_id=="loadPropField")
                        {
                            str = '<tr><td width="40%" class="adm-detail-content-cell-l">'+name+'&nbsp;['+code+']:</td><td width="60%" class="adm-detail-content-cell-r"><input type="text" value="" name="SETTINGS[catalog][action_props_val]['+code+'][]" data-code="'+code+'" size="40">&nbsp; <select id="SETTINGS[catalog][action_props]['+code+']" name="SETTINGS[catalog][action_props]['+code+'][]"><option value=""><?=GetMessage("sota_parser_select_action_props")?></option><option value="delete"><?=GetMessage("parser_action_props_delete")?></option><option value="add_b"><?=GetMessage("parser_action_props_add_begin")?></option><option value="add_e"><?=GetMessage("parser_action_props_add_end")?></option></select> <a href="#" class="find_delete">Delete</a></td></tr>';
                            target_shadow_id.before(str);    
                        }
                    }

                 })
                
            }
            function createSectionPropertyOffer(id, name, type)
            {   
                jQuery.ajax({
                    url: "",
                    type: "POST",
                    data: 'ajax=1&iblock=<?=$OFFER_IBLOCK_ID?>&prop_id='+id,
                    dataType: 'html',
                    success: function(code){
                        code = $.trim(code);
                        if(target_select_id=="loadDopProp")
                        {
                            str = '<tr><td width="40%" class="adm-detail-content-cell-l">'+name+'&nbsp;['+code+']:</td><td width="60%" class="adm-detail-content-cell-r"><input type="text" value="" name="SETTINGS[catalog][selector_prop]['+code+']" data-code="'+code+'" size="40">&nbsp;<a href="#" class="prop_delete">Delete</a></td></tr>';
                            target_shadow_id.before(str);
                    
                        }else if(target_select_id=="loadDopProp1")
                        {
                            str = '<tr><td width="40%" class="adm-detail-content-cell-l">'+name+'&nbsp;['+code+']:</td><td width="60%" class="adm-detail-content-cell-r"><input type="text" value="" name="SETTINGS[catalog][find_prop]['+code+']" data-code="'+code+'" size="40">&nbsp;<a href="#" class="prop_delete">Delete</a></td></tr>';
                            target_shadow_id.before(str);    
                        }else if(target_select_id=="loadDopProp2")
                        {
                            str = '<tr><td width="40%" class="adm-detail-content-cell-l">'+name+'&nbsp;['+code+']:</td><td width="60%" class="adm-detail-content-cell-r"><input type="text" value="" name="SETTINGS[catalog][selector_prop_preview]['+code+']" data-code="'+code+'" size="40">&nbsp;<a href="#" class="prop_delete">Delete</a></td></tr>';
                            target_shadow_id.before(str);    
                        }else if(target_select_id=="loadDopProp3")
                        {
                            str = '<tr><td width="40%" class="adm-detail-content-cell-l">'+name+'&nbsp;['+code+']:</td><td width="60%" class="adm-detail-content-cell-r"><input type="text" value="" name="SETTINGS[catalog][find_prop_preview]['+code+']" data-code="'+code+'" size="40">&nbsp;<a href="#" class="prop_delete">Delete</a></td></tr>';
                            target_shadow_id.before(str);    
                        }
                    }

                 })
                
            }
            function adjustEmptyTR(table_id)
            {
                var tbl = BX(table_id);
                if(tbl)
                {
                    var cnt = tbl.rows.length;
                    var tr = tbl.rows[cnt-1];

                    var display = 'table-row';
                    for(var i = 1; i < cnt-1; i++)
                    {
                        if(tbl.rows[i].style.display != 'none')
                            display = 'none';
                    }
                    tr.style.display = display;
                }
            }
</script>    
<script language="JavaScript">
    jQuery(document).ready(function(){
        
        
        $("#loadDopPropOffer").on("click", function(e){
            e.preventDefault();
            tr = $(this).parents("tr").eq(0);
            
            v = tr.find("select[name=arrPropDopOffer] option:selected").val();
            t = tr.find("select[name=arrPropDopOffer] option:selected").text();
            if(v=="") return false;
            else if(v=="[]")
            {
                sota_iblock_edit_property_offer("loadDopPropOffer", tr);
                return false;
            }
            str = '<tr><td width="40%" class="adm-detail-content-cell-l">'+t+'&nbsp;['+v+']:</td><td width="60%" class="adm-detail-content-cell-r"><input type="text" value="" name="SETTINGS[offer][selector_prop]['+v+']" data-code="'+v+'" size="40">&nbsp;<a href="#" class="prop_delete">Delete</a></td></tr>';
            tr.before(str);
        });
        
        $("#loadDopPropOffer2").on("click", function(e){
            e.preventDefault();
            tr = $(this).parents("tr").eq(0);
            
            v = tr.find("select[name=arrPropDopOffer] option:selected").val();
            t = tr.find("select[name=arrPropDopOffer] option:selected").text();
            if(v=="") return false;
            else if(v=="[]")
            {
                sota_iblock_edit_property_offer("loadDopPropOffer", tr);
                return false;
            }
            str = '<tr><td width="40%" class="adm-detail-content-cell-l">'+t+'&nbsp;['+v+']:</td><td width="60%" class="adm-detail-content-cell-r"><input type="text" value="" name="SETTINGS[offer][selector_prop_more]['+v+']" data-code="'+v+'" size="40">&nbsp;<a href="#" class="prop_delete">Delete</a></td></tr>';
            tr.before(str);
        });
        
        $("#loadDopPropOffer1").on("click", function(e){
            e.preventDefault();
            tr = $(this).parents("tr").eq(0);
            
            v = tr.find("select[name=arrPropDopOffer] option:selected").val();
            t = tr.find("select[name=arrPropDopOffer] option:selected").text();
            if(v=="") return false;
            else if(v=="[]")
            {
                sota_iblock_edit_property_offer("loadDopPropOffer1", tr);
                return false;
            }
            str = '<tr><td width="40%" class="adm-detail-content-cell-l">'+t+'&nbsp;['+v+']:</td><td width="60%" class="adm-detail-content-cell-r"><input type="text" value="" name="SETTINGS[offer][find_prop]['+v+']" data-code="'+v+'" size="40">&nbsp;<a href="#" class="prop_delete">Delete</a></td></tr>';
            tr.before(str);
        });
        
        $("#loadDopProp").on("click", function(e){
            e.preventDefault();
            tr = $(this).parents("tr").eq(0);
            
            v = tr.find("select[name=arrPropDop] option:selected").val();
            t = tr.find("select[name=arrPropDop] option:selected").text();
            if(v=="") return false;
            else if(v=="[]")
            {
                sota_iblock_edit_property("loadDopProp", tr);
                return false;
            }
            str = '<tr><td width="40%" class="adm-detail-content-cell-l">'+t+'&nbsp;['+v+']:</td><td width="60%" class="adm-detail-content-cell-r"><input type="text" value="" name="SETTINGS[catalog][selector_prop]['+v+']" data-code="'+v+'" size="40">&nbsp;<a href="#" class="prop_delete">Delete</a></td></tr>';
            tr.before(str);
        });
        
        $("#loadPropDefault").on("click", function(e){
            e.preventDefault();
            tr = $(this).parents("tr").eq(0);
            
            v = tr.find("select[name=arrPropDefault] option:selected").val();
            t = tr.find("select[name=arrPropDefault] option:selected").text();
            if(v=="") return false;
            /*else if(v=="[]")
            {
                sota_iblock_edit_property("loadPropDefault", tr);
                return false;
            }*/
            
            jQuery.ajax({
                    url: "",
                    type: "POST",
                    data: 'default=1&ajax=1&prop_id='+v+"&iblock_id="+<?=isset($sota_IBLOCK_ID)?$sota_IBLOCK_ID:0?>,
                    dataType: 'html',
                    success: function(data){
                        str = '<tr><td width="40%" class="adm-detail-content-cell-l">'+t+'&nbsp;['+v+']:</td><td width="60%" class="adm-detail-content-cell-r">'+data+'</td></tr>';
                        tr.before(str);    
                    }
            })
        });
        
        $("#loadDopProp2").on("click", function(e){
            e.preventDefault();
            tr = $(this).parents("tr").eq(0);
            
            v = tr.find("select[name=arrPropDop] option:selected").val();
            t = tr.find("select[name=arrPropDop] option:selected").text();
            if(v=="") return false;
            else if(v=="[]")
            {
                sota_iblock_edit_property("loadDopProp2", tr);
                return false;
            }
            str = '<tr><td width="40%" class="adm-detail-content-cell-l">'+t+'&nbsp;['+v+']:</td><td width="60%" class="adm-detail-content-cell-r"><input type="text" value="" name="SETTINGS[catalog][selector_prop_preview]['+v+']" data-code="'+v+'" size="40">&nbsp;<a href="#" class="prop_delete">Delete</a></td></tr>';
            tr.before(str);
        });
        
        $("#loadDopProp1").on("click", function(e){
            e.preventDefault();
            tr = $(this).parents("tr").eq(0);
            
            v = tr.find("select[name=arrPropDop1] option:selected").val();
            t = tr.find("select[name=arrPropDop1] option:selected").text();
            if(v=="") return false;
            else if(v=="[]")
            {
                sota_iblock_edit_property("loadDopProp1", tr);
                return false;
            }
            str = '<tr><td width="40%" class="adm-detail-content-cell-l">'+t+'&nbsp;['+v+']:</td><td width="60%" class="adm-detail-content-cell-r"><input type="text" value="" name="SETTINGS[catalog][find_prop]['+v+']" data-code="'+v+'" size="40">&nbsp;<a href="#" class="prop_delete">Delete</a></td></tr>';
            tr.before(str);
        });
        
        $("#loadDopProp3").on("click", function(e){
            e.preventDefault();
            tr = $(this).parents("tr").eq(0);
            
            v = tr.find("select[name=arrPropDop1] option:selected").val();
            t = tr.find("select[name=arrPropDop1] option:selected").text();
            if(v=="") return false;
            else if(v=="[]")
            {
                sota_iblock_edit_property("loadDopProp3", tr);
                return false;
            }
            str = '<tr><td width="40%" class="adm-detail-content-cell-l">'+t+'&nbsp;['+v+']:</td><td width="60%" class="adm-detail-content-cell-r"><input type="text" value="" name="SETTINGS[catalog][find_prop_preview]['+v+']" data-code="'+v+'" size="40">&nbsp;<a href="#" class="prop_delete">Delete</a></td></tr>';
            tr.before(str);
        });
        
        $("label").on("click", function(e){
            checked = $(this).prev("input[type='checkbox']");
            name = checked.attr("name");
            if(name == "SETTINGS[catalog][update][active]")
            {
                if(checked.is(":checked"))
                {
                    $("tr.show_block_add_element").hide();
                    $("input[name='SETTINGS[catalog][update][add_element]']").removeAttr("checked");
                }
                else
                {
                    $("tr.show_block_add_element").show();
                }
            }
        });
        
        $("#loadPropField").on("click", function(e){
            e.preventDefault();
            tr = $(this).parents("tr").eq(0);
            
            v = tr.find("select[name=arrPropField] option:selected").val();
            t = tr.find("select[name=arrPropField] option:selected").text();
            if(v=="") return false;
            else if(v=="[]")
            {
                sota_iblock_edit_property("loadPropField", tr);
                return false;
            }
            //str = '<tr><td width="40%" class="adm-detail-content-cell-l">'+t+'&nbsp;['+v+']:</td><td width="60%" class="adm-detail-content-cell-r"><input type="text" value="" name="SETTINGS[catalog][find_prop_preview]['+v+']" data-code="'+v+'" size="40">&nbsp;<a href="#" class="prop_delete">Delete</a></td></tr>';
            if(v=="sota_PARSER_NAME_E") str = '<tr><td width="40%" class="adm-detail-content-cell-l">'+t+':</td><td width="60%" class="adm-detail-content-cell-r"><input type="text" value="" name="SETTINGS[catalog][action_props_val]['+v+'][]" data-code="'+v+'" size="40">&nbsp; <select id="SETTINGS[catalog][action_props]['+v+']" name="SETTINGS[catalog][action_props]['+v+'][]"><option value=""><?=GetMessage("sota_parser_select_action_props")?></option><option value="delete"><?=GetMessage("parser_action_props_delete")?></option><option value="add_b"><?=GetMessage("parser_action_props_add_begin")?></option><option value="add_e"><?=GetMessage("parser_action_props_add_end")?></option></select> <a href="#" class="find_delete">Delete</a></td></tr>';
            else str = '<tr><td width="40%" class="adm-detail-content-cell-l">'+t+'&nbsp;['+v+']:</td><td width="60%" class="adm-detail-content-cell-r"><input type="text" value="" name="SETTINGS[catalog][action_props_val]['+v+'][]" data-code="'+v+'" size="40">&nbsp; <select id="SETTINGS[catalog][action_props]['+v+']" name="SETTINGS[catalog][action_props]['+v+'][]"><option value=""><?=GetMessage("sota_parser_select_action_props")?></option><option value="delete"><?=GetMessage("parser_action_props_delete")?></option><option value="add_b"><?=GetMessage("parser_action_props_add_begin")?></option><option value="add_e"><?=GetMessage("parser_action_props_add_end")?></option></select> <a href="#" class="find_delete">Delete</a></td></tr>';
            
            tr.before(str);
        });
        
        function sota_iblock_edit_property(select_id, tr)
        {
            <?if(isset($sota_IBLOCK_ID) && $sota_IBLOCK_ID):?>addSectionProperty(<?echo $sota_IBLOCK_ID;?>, select_id, tr, 'table_SECTION_PROPERTY')<?endif;?>
        }
        
        function sota_iblock_edit_property_offer(select_id, tr)
        {
            <?if(isset($OFFER_IBLOCK_ID) && $OFFER_IBLOCK_ID):?>addSectionProperty(<?echo $OFFER_IBLOCK_ID;?>, select_id, tr, 'table_SECTION_PROPERTY')<?endif;?>
        }
        
        
        $("body").on("click", ".prop_delete", function(e){
            e.preventDefault();
            tr = $(this).parents("tr").eq(0);
            tr.hide();
            tr.find("input").val("");
            v = tr.find("input").attr("data-code");
            prev = $("#delete_selector_prop").val();
            $("#delete_selector_prop").val(prev+","+v);
        });
        
        $("body").on("click", ".dop_rss_delete", function(e){
            e.preventDefault();
            tr = $(this).parents("tr").eq(0);
            tr.html("");
            tr.remove();
            var arrDopRss = $(".admin_tr_rss_dop");
            remove_element_dop_rss();
        });

        function remove_element_dop_rss()
        {
            numer = 1;
            $(".admin_tr_rss_dop").each(function(){
                td_1 = $(this).children("td").eq(0);
                td_2 = $(this).children("td").eq(1);
                td_1.html("<?echo GetMessage("parser_dop_load_rss");?>" + numer);
                select = td_2.children("select").eq(0);
                input = td_2.children("input").eq(0);
                select.attr("name", "SETTINGS[catalog][section_dop]["+numer+"]");
                input.attr("name", "SETTINGS[catalog][rss_dop]["+numer+"]");
                numer ++;
            });
        }
        
        $("body").on("click", ".find_delete", function(e){
            e.preventDefault();
            tr = $(this).parents("tr").eq(0);
            tr.hide();
            tr.find("input").val("");
            v = tr.find("input").attr("data-code");
            prev = $("#delete_find_prop").val();
            $("#delete_find_prop").val(prev+","+v);
        })
        
        $("body").on("click", ".show_prop", function(e){
            e.preventDefault();
            id = $(this).attr("data-name");
            $("#"+id).val("");
            tr = $(this).parents("tr").eq(0);
            if(!tr.is("#header_find_prop"))tr.nextAll().not("#header_find_prop~tr").show();
            else tr.nextAll().show();    
        })
        
        $("body").on("click", ".add_usl", function(e){
            e.preventDefault();
            
            tr = $(".tr_add").clone(); 
            console.log(tr);
            n = parseInt($(".tr_add.heading").attr("data-num"));
            $(".tr_add").removeClass("tr_add");
            $(".tr_last").after(tr);
            $(".tr_last").not(".tr_add").removeClass("tr_last");
            $(this).remove();
            $(".tr_add.heading").attr("data-num", n+1); 
            $(".tr_add.heading span").text(n+1);
            $(".tr_add .del_usl").show();  
        })
        
        $("body").on("click", "#auth", function(e){
            e.preventDefault();
            url = $(this).attr("data-href");
            jQuery.ajax({
               url: url,
               type: "POST",
               data: "auth=1",
               dataType: 'html',
               success: function(data){
                 $("#sota_message").html(data);
               }

            })
        })
        
        $("body").on("click", ".del_usl", function(e){
            e.preventDefault();
            tr = $(this).parents(".heading").eq(0);
            if(tr.is(".tr_add"))
            {
                tr.prev().addClass("tr_last");
                bool = false;
                tr.prevAll("tr").each(function(){
                    if(!bool) $(this).addClass("tr_add");
                    if($(this).is(".heading") && !bool)
                    {
                        bool = true;
                        attr = parseInt($(this).attr("data-num"));
                        if(attr!=1)$(this).html(tr.html());    
                    }
                         
                })        
            }
                bool = false;
                tr.nextAll("tr").each(function(){
                    if($(this).is(".heading")) bool = true;
                    if(!bool) $(this).remove();    
                })    
            tr.remove();
        })

        jQuery('#iblock').change(function(){
            iblock = jQuery(this).val();
            jQuery.ajax({
               url: "",
               type: "POST",
               data: 'ajax=1&iblock='+iblock,
               dataType: 'html',
               beforeSend: function(){BX.showWait();},
               success: function(data){
                 var ar = new Array();
                 ar = data.split("#SOTA#");
                 $('#section').html(ar[0]);
                 add_list_section(ar[0]);
                 BX.closeWait();
                 /*$(".prop-iblock").html(ar[1]);
                 $("#edit5 .image_props").html(ar[4]);
                 $("#edit5 #header_selector_prop+tr+tr").nextAll().not("#header_find_prop, .tr_find_prop").remove();
                 $("#edit5 #header_find_prop+tr+tr+tr").nextAll().not(".tr_find_prop").remove();
                 $("#edit5 #header_selector_prop+tr+tr").after(ar[2]);
                 $("#edit5 #header_find_prop+tr+tr+tr").after(ar[3]);*/
               }

            })

        })
        
        function add_list_section(ar_html)
        {
            $(".admin_tr_rss_dop td select").each(function(){
                $(this).html(ar_html);
            });
        }
        
        jQuery('#loadDopRSS').click(function(){
            iblock = jQuery("#iblock").val();
            var el = $(this);
            jQuery.ajax({
               url: "",
               type: "POST",
               data: 'ajax=1&iblock='+iblock,
               dataType: 'html',
               beforeSend: function(){BX.showWait();},
               success: function(data){
                 var ar = new Array();
                 ar = data.split("#SOTA#");
                 var count_rss = $(".admin_tr_rss_dop").length;
                 count_rss = count_rss*1 + 1;
                 var str = '<tr class="admin_tr_rss_dop"><td class="adm-detail-content-cell-l"><?echo GetMessage("parser_dop_load_rss");?>'+count_rss+'</td><td class="adm-detail-content-cell-r"><input type="text" name="SETTINGS[catalog][rss_dop]['+count_rss+']" value="" size="50" maxlength="500"/><select style="width:262px;" name="SETTINGS[catalog][section_dop]['+count_rss+']">'+ar[0]+'</select><a class="dop_rss_delete" href="#"><?=GetMessage("parser_caption_detete_button");?></a></td></tr>';
                 var element = el.closest("tr");
                 element.before(str);
                 BX.closeWait();
               }

            })

        })

        $('.bool-delete').change(function(){
          if($(this).prop('checked')){
            $(this).next().removeAttr('disabled');
            $(this).next().next().removeAttr('disabled');
          }
          else{
            $(this).next().val("");
            $(this).next().attr('disabled', "");

            $(this).next().next().val("");
            $(this).next().next().attr('disabled', "");
          }
        })

        $('.number_img').change(function(){
          if(!$(this).prop('checked')){
            $(this).next().removeAttr('disabled');
            $(this).next().next().removeAttr('disabled');
          }
          else{
            $(this).next().val("");
            $(this).next().attr('disabled', "");

            $(this).next().next().val("");
            $(this).next().next().attr('disabled', "");

          }
        })

        $("#TYPE").change(function(e){
                href = location.href;
                location.href=href+'&type='+$(this).val();
        })
        
        $(".select_load").change(function(e){
            $("input[name=apply]").click();    
        })
        
        $("body").on("click", "#btn_stop_catalog", function(e){
            e.preventDefault();
                
        })
        var ajaxInterval;
        var sotaStop;
        var sotaStopStart;
        var href1, href2;
        var debug = 0;
        <?if($sotaDebug=="debug"):?>
        debug = 1;
        <?endif;?>

        function sotaAjaxCatalog(href2, start)
        {
                if(start==1)
                {
                    sotaStopStart = 0;
                    href = href2+"&begin=1";
                    sotaStop = 0;
                }
                else href=href2;
                BX.ajax.get(href, "", function(data){
                    //clearInterval(ajaxInterval);
                    prog = 100;
                    $('#progress_text').html(prog + '%');
                    $('#progress_bar_inner').width(500 * prog / 100);
                    //$("#status_bar").hide();
                    $('#progress_text').html(100 + '%');
                    if(data!="stop")$("#sota_message").html(data);
                    //if(sotaStop!=1)sotaAjaxCatalog(href2, 0);
                    //else sotaStop = 0;
                    if(data!="stop" && sotaStopStart!=1 && debug!=1) sotaAjaxCatalog(href2, 0);
                    else
                    {   
                        if(sotaStopStart==1)sotaStop = 1;
                        //if(debug==1) 
                        sotaStop = 1;
                        $("#btn_stop_catalog").attr("id", "btn_start_catalog");
                        $("#btn_start_catalog").text(<?echo '"'.GetMessage("parser_start").'"'?>); 
                        setTimeout(function(){
                            sotaCountAjax(href1, 1);
                        },1000)
                            
                    }                            
                    
                })    
        }
        
        //get xml
        function sotaAjaxXML(href2, start)
        {
                if(start==1)
                {
                    sotaStopStart = 0;
                    href = href2+"&begin=1";
                    sotaStop = 0;
                }
                else href=href2;
                BX.ajax.get(href, "", function(data){
                    //clearInterval(ajaxInterval);
                    prog = 100;
                    $('#progress_text').html(prog + '%');
                    $('#progress_bar_inner').width(500 * prog / 100);
                    //$("#status_bar").hide();
                    $('#progress_text').html(100 + '%');
                    if(data!="stop")$("#sota_message").html(data);
                    //if(sotaStop!=1)sotaAjaxCatalog(href2, 0);
                    //else sotaStop = 0;
                    
                    if(data!="stop" && sotaStopStart!=1 && debug!=1) sotaAjaxXML(href2, 0);
                    else
                    {   
                        if(sotaStopStart==1)sotaStop = 1;
                        //if(debug==1) 
                        sotaStop = 1;
                        $("#btn_stop_xml").attr("id", "btn_start_xml");
                        $("#btn_start_xml").text(<?echo '"'.GetMessage("parser_start").'"'?>); 
                        setTimeout(function(){
                            sotaCountAjax(href1, 1);
                        },1000)
                            
                    }                            
                    
                })    
        }
        
        $("body").on("click", "#btn_stop_catalog", function(e){
            e.preventDefault();
            sotaStopStart = 1;
        })
        
        //stop parsing xml
        $("body").on("click", "#btn_stop_xml", function(e){
            e.preventDefault();
            sotaStopStart = 1;
        })
        
        $("body").on('click', "#btn_start_catalog", function(e) {   //alert("test");
            e.preventDefault();
            $(this).attr("id", "btn_stop_catalog");
            $("#status_bar").show();
            $(this).text(<?echo '"'.GetMessage("btn_stop_catalog").'"'?>);
            href1 = $(this).attr("href")+"&ajax_count=1&type=catalog";
            href2 = $(this).attr("href")+"&ajax_start=1&type=catalog";

            //ajaxInterval = setInterval(function(){
            sotaCountAjax(href1, 0);
            //}, 1000);
            
            

            sotaAjaxCatalog(href2, 1);

            return false;

        })
         //start parsing xml
         $("body").on('click', "#btn_start_xml", function(e) {   
            e.preventDefault();
            $(this).attr("id", "btn_stop_xml");
            $("#status_bar").show();
            $(this).text(<?echo '"'.GetMessage("btn_stop_catalog").'"'?>);
            href1 = $(this).attr("href")+"&ajax_count=1&type=xml";
            href2 = $(this).attr("href")+"&ajax_start=1&type=xml";

            //ajaxInterval = setInterval(function(){
            sotaCountAjax(href1, 0);
            //}, 1000);
            
         

            sotaAjaxXML(href2, 1);

            return false;

        })
        
        function sotaCountAjax(href1, num)
        {   
            BX.ajax.post(href1, "sessid="+BX.bitrix_sessid(), function(data){
                    arData = data.split("|");
                    if(sotaStop!=1)
                    {
                        if(arData[1]>0)prog = Math.ceil((arData[1]/arData[0])*100);
                        else prog = 0;
                        $('#progress_text').html(prog + '%');
                        $('#progress_bar_inner').width(500 * prog / 100);    
                    }
                    
                    page = arData[2];
                    elements = arData[3];
                    elementError = arData[4];
                    allError = arData[5];
                    if(sotaStop!=1)sotaStop = parseInt(arData[6]);
                    msg = <?echo '"'.GetMessage("parser_load_page").'"'?> + page + <?echo '"'.GetMessage("parser_load_product").'"'?> + elements + <?echo '"<span style=\"color:red\">'.GetMessage("parser_load_product_error").'"'?> + elementError + '</span>' + <?echo '"<span style=\"color:red\">'.GetMessage("parser_all_error").'"'?> + allError + '</span>';
                    $("#catalog_bar").html(msg);
                    var ArrGet = parseGetParams(href1);
                    //var arrTypePars = JSON.parse(ArrGet);
                    if(ArrGet['type'] == 'xml')
                    {
                        if((arData[6] == null) || (arData[6] == 0) || (arData[6] == "") || (arData[6] == false))
                        {
                            arData[6] = 0;
                        }
                        var sec = "<?echo GetMessage('parser_add_all_section');?>"+arData[6]+"</span>";
                        $("#catalog_bar").html($("#catalog_bar").html() + sec);
                    }
                    
                    if(sotaStop==1)
                    {   
                        prog = 100;
                        $('#progress_text').html(prog + '%');
                        $('#progress_bar_inner').width(500 * prog / 100);
                        $('#progress_text').html(<?echo '"'.GetMessage("parser_loading_end").'"'?>); 
                        //$("#catalog_bar").html($("#catalog_bar").html() + arData[6]);
                    }else sotaCountAjax(href1, 0);
                    
                    
                })    
        }

    })
    function parseGetParams(href) { 
       var $_GET = {}; 
       var __GET = href.split("&"); 
       for(var i=0; i<__GET.length; i++) { 
          var getVar = __GET[i].split("="); 
          $_GET[getVar[0]] = typeof(getVar[1])=="undefined" ? "" : getVar[1]; 
       } 
       return $_GET; 
    }

    BX.ready(function(){

        BX.bind(BX('btn_start'), 'click', function(e) {
            e.preventDefault();
            BX.show(BX('status_bar'));
            var href1 = BX(this).getAttribute("href")+"&ajax_count=1";
            var href2 = BX(this).getAttribute("href")+"&ajax_start=1";

            var ajaxInterval = setInterval(function(){
                BX.ajax.post(href1, "sessid="+BX.bitrix_sessid(), function(data){
                    arData = data.split("|");
                    if(arData[1]>0)prog = Math.ceil((arData[1]/arData[0])*100);
                    else prog = 0;
                    BX('progress_text').innerHTML = prog + '%';
                    BX('progress_bar_inner').style.width = 500 * prog / 100 + 'px';
                    if(prog==1) clearInterval(ajaxInterval);
                })
            }, 500);


            BX.ajax.get(href2, "", function(data){
                clearInterval(ajaxInterval);
                prog = 100;
                BX('progress_text').innerHTML = prog + '%';
                BX('progress_bar_inner').style.width = 500 * prog / 100 + 'px';
                BX.hide(BX('status_bar'));
                BX('progress_text').innerHTML = 0 + '%';
                BX("sota_message").innerHTML = data;

            })

            return false;

        })

        
    })
</script>
<style>
.adm-info-message table td, .heading .adm-info-message td, .heading .adm-info-message td{
    padding:0!important;
}
.item_row td{
  padding:0!important;  
}
</style>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>
<?elseif(isset($_REQUEST["ajax_start"]) && isset($_REQUEST["ID"]) && isset($_REQUEST["start"]) && !isset($_REQUEST["ajax_count"])):
    set_time_limit(0);
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sota.parser/include.php");
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sota.parser/prolog.php");
    IncludeModuleLangFile(__FILE__);
    if(CModule::IncludeModule('iblock') && CModule::IncludeModule('main')):
    $parser = SotaParserContent::GetByID($ID);
    if(!$parser->ExtractFields("sota_")) $ID=0;
    $rssParser = new RssContentParser();
    $result = $rssParser->startParser(0);
    endif;
?>
<?elseif(isset($_REQUEST["ID"]) && isset($_REQUEST["start"]) && isset($_REQUEST["ajax_count"])):
    set_time_limit(0);
    $file = $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sota.parser/include/count_parser".$_REQUEST["ID"].".txt";
    if(isset($_REQUEST["ID"]) && file_exists($file)){
        $count = file_get_contents($file);
        echo $count;
    }else{
        echo "0|0";
    }
    if($_GET["type"]=="catalog" || $_GET["type"]=="xml")
    {
        $file1 = $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sota.parser/include/count_parser_catalog".$_REQUEST["ID"].".txt";
        if(isset($_REQUEST["ID"]) && file_exists($file1))
        {
            $count = file_get_contents($file1);
            echo $count;
        }else{
            echo "|0|0|0|0";
        }
        $file2 = $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sota.parser/include/catalog_parser_current_page".$_REQUEST["ID"].".txt";
        if(isset($_REQUEST["ID"]) && file_exists($file2))
        {
            $content = file_get_contents($file2);
            if($content=="")
            {
                echo "|1";
                unlink($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sota.parser/include/catalog_parser_current_page".$_REQUEST["ID"].".txt");
            }
        }    
    }
    //print_r(array($_REQUEST["ID"], file_exists($file), $file));
    //file_put_contents(dirname(__FILE__)."/log.log", print_r(array("REQIEST" => $_REQUEST, "GET" => $_GET), true), FILE_APPEND);
?>
<?
elseif(isset($_REQUEST["prop_id"])):
        require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
        require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sota.parser/include.php");
        require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sota.parser/prolog.php");
        IncludeModuleLangFile(__FILE__);
    CModule::IncludeModule('iblock');
    if(isset($_REQUEST["default"])) $properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "CODE"=>$_REQUEST["prop_id"], "IBLOCK_ID"=>$_REQUEST["iblock_id"]));
    else $properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "ID"=>$_REQUEST["prop_id"]));
    while($arProp = $properties->Fetch())
    {   //printr($arProp);
        if(!isset($_REQUEST["default"]))
        {
            echo $arProp["CODE"];
            return false;    
        }else{
            $code = $arProp["CODE"];
            if($arProp["PROPERTY_TYPE"]=="L" || $arProp["PROPERTY_TYPE"]=="N" || $arProp["PROPERTY_TYPE"]=="S" || $arProp["PROPERTY_TYPE"]=="E" || $arProp["PROPERTY_TYPE"]=="F")
            {
                $arrPropDop['REFERENCE'][] = $arProp["NAME"];
                $arrPropDop['REFERENCE_ID'][] = $arProp["CODE"];
                $arrPropDop['REFERENCE_TYPE'][$arProp["CODE"]] = $arProp["PROPERTY_TYPE"];
                $arrPropDop['USER_TYPE'][$arProp["CODE"]] = $arProp["USER_TYPE"];
                $arrPropDop['REFERENCE_CODE_NAME'][$arProp["CODE"]] = $arProp["NAME"];
            }
            
            if($arProp["PROPERTY_TYPE"]=="L"/* && $arProp["ID"]==14*/)
            {
                $rsEnum = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$sota_IBLOCK_ID, "property_id"=>$arProp["ID"]));
                $arrPropDop["LIST_VALUES"][$arProp["CODE"]]["REFERENCE"][] = GetMessage("parser_prop_default");
                $arrPropDop["LIST_VALUES"][$arProp["CODE"]]["REFERENCE_ID"][] = "";
                while($arEnum = $rsEnum->Fetch())
                {
                    $arrPropDop["LIST_VALUES"][$arProp["CODE"]]["REFERENCE"][] = $arEnum["VALUE"];
                    $arrPropDop["LIST_VALUES"][$arProp["CODE"]]["REFERENCE_ID"][] = $arEnum["ID"];
                }
            }
            if($arProp['USER_TYPE']=="directory")
            {
                $nameTable = $arProp["USER_TYPE_SETTINGS"]["TABLE_NAME"];
                $directorySelect = array("*");
                $directoryOrder = array();
                $entityGetList = array(
                    'select' => $directorySelect,
                    'order' => $directoryOrder
                );
                $highBlock = \Bitrix\Highloadblock\HighloadBlockTable::getList(array("filter" => array('TABLE_NAME' => $nameTable)))->fetch();
                $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($highBlock);
                $entityDataClass = $entity->getDataClass();
                $propEnums = $entityDataClass::getList($entityGetList);
                $arrPropDop["LIST_VALUES"][$arProp["CODE"]]["REFERENCE"][] = GetMessage("parser_prop_default");
                $arrPropDop["LIST_VALUES"][$arProp["CODE"]]["REFERENCE_ID"][] = "";
                while ($oneEnum = $propEnums->fetch())
                {
                    $arrPropDop["LIST_VALUES"][$arProp["CODE"]]["REFERENCE"][] = $oneEnum["UF_NAME"];
                    $arrPropDop["LIST_VALUES"][$arProp["CODE"]]["REFERENCE_ID"][] = $oneEnum["UF_XML_ID"];
                }
            }
                
                ?><?if($arrPropDop['REFERENCE_TYPE'][$code]=="L"):
            ?>
            <?=SelectBoxFromArray('SETTINGS[catalog][default_prop]['.$code.']', $arrPropDop["LIST_VALUES"][$code], "", "", "");?>
            <?elseif($arrPropDop['USER_TYPE'][$code]=="directory"):?>
            <?=SelectBoxFromArray('SETTINGS[catalog][default_prop]['.$code.']', $arrPropDop["LIST_VALUES"][$code], "", "", "");?>
            <?else:?>
            <input type="text" placeholder="<?=GetMessage("parser_prop_default")?>" name="SETTINGS[catalog][default_prop][<?=$code?>]" value="" />
            <?endif?><?
                
                
        }
        
        
    }
    
    
elseif(isset($_REQUEST["iblock"])):
        require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
        require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sota.parser/include.php");
        require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sota.parser/prolog.php");
        IncludeModuleLangFile(__FILE__);
    CModule::IncludeModule('iblock');
    $rsSections = CIBlockSection::GetList(array("left_margin"=>"asc"), array(/*'ACTIVE'=>"Y", */"IBLOCK_ID"=>$_REQUEST["iblock"]), false, array('ID', 'NAME', "IBLOCK_ID", "DEPTH_LEVEL"));

    $first = true;
    echo '<option value="">'.GetMessage("parser_section_id").'</option>';
    while($arr=$rsSections->Fetch()){
        $arr["NAME"] = str_repeat(" . ", $arr["DEPTH_LEVEL"]).$arr["NAME"];
        echo '<option value="'.$arr["ID"].'">'.$arr["NAME"].'</option>';
    }
    echo '#SOTA#<option value="">'.GetMessage("parser_prop_id").'</option>';
    $properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$_REQUEST['iblock'], "PROPERTY_TYPE"=>"S"));
    while($arProp = $properties->Fetch())
    {
        echo '<option value="'.$arProp["CODE"].'">'."[".$arProp["CODE"]."] ".$arProp["NAME"].'</option>';
    }




    echo '#SOTA#';
    $properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$_REQUEST['iblock']));
    while($arProp = $properties->Fetch())
    {
        if($arProp["PROPERTY_TYPE"]=="S" || $arProp["PROPERTY_TYPE"]=="L" || $arProp["PROPERTY_TYPE"]=="N" ||  $arProp["PROPERTY_TYPE"]=="E")
        {
            $arPropsDop[] = $arProp;
        }

         //echo '<option value="'.$arProp["CODE"].'">'."[".$arProp["CODE"]."] ".$arProp["NAME"].'</option>';
    }
    foreach($arPropsDop as $val)
    {
        echo '<tr><td width="40%" class="adm-detail-content-cell-l">'.$val["NAME"].'&nbsp;['.$val["CODE"].']:</td><td width="60%" class="adm-detail-content-cell-r"><input data-code="'.$val["CODE"].'" size="40" type="text" value="" name="SETTINGS[catalog][selector_prop]['.$val["CODE"].']">&nbsp;<a class="prop_delete" href="#">Delete</a></td></tr>';
    }
    echo '#SOTA#';
    foreach($arPropsDop as $val)
    {
        echo '<tr><td width="40%" class="adm-detail-content-cell-l">'.$val["NAME"].'&nbsp;['.$val["CODE"].']:</td><td width="60%" class="adm-detail-content-cell-r"><input data-code="'.$val["CODE"].'" size="40" type="text" value="" name="SETTINGS[catalog][find_prop]['.$val["CODE"].']">&nbsp;<a class="find_delete" href="#">Delete</a></td></tr>';
    }

    echo '#SOTA#<option value="">'.GetMessage("parser_prop_id").'</option>';
    $properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$_REQUEST['iblock'], "PROPERTY_TYPE"=>"F"));
    while($arProp = $properties->Fetch())
    {
        echo '<option value="'.$arProp["CODE"].'">'."[".$arProp["CODE"]."] ".$arProp["NAME"].'</option>';
    }
elseif(isset($_POST["auth"])):
    set_time_limit(0);
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sota.parser/include.php");
    require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sota.parser/prolog.php");
    IncludeModuleLangFile(__FILE__);
    if(CModule::IncludeModule('iblock') && CModule::IncludeModule('main')):
    $parser = SotaParserContent::GetByID($ID);
    if(!$parser->ExtractFields("sota_")) $ID=0;
    $rssParser = new RssContentParser();
    $rssParser->auth(true);
    endif;
endif;
?>