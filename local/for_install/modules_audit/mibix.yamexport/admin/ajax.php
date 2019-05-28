<?
define("STOP_STATISTICS", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
//CComponentUtil::__IncludeLang(dirname($_SERVER["SCRIPT_NAME"]), "/ajax.php");
CComponentUtil::__IncludeLang(dirname(__FILE__), "/ajax.php");

$MODULE_ID = "mibix.yamexport";
CModule::IncludeModule($MODULE_ID);

if (!CModule::IncludeModule("iblock")) return;

$arRes = array();
global $USER, $APPLICATION;
if (!check_bitrix_sessid() || $_SERVER["REQUEST_METHOD"] != "POST") return;

CUtil::JSPostUnescape();

// Получение списка инфоблоков на основе типа и сайта
if (!empty($_POST["action"]) && $_POST["action"]=="get_iblocks_options")
{
    $arIblockParams = array();
    $arIblockParams['TYPE'] = (!empty($_POST["iblock_type"]) ? $_POST["iblock_type"] : "");
    if (!empty($_POST["site_id"]) && $_POST["site_id"]!="")
    {
        $arIblockParams['SITE_ID'] = $_POST["site_id"];
    }

    // Вытаскиваем инфоблоки по фильтру
    $arRes["IBLOCK_OPTIONS"] = '<option value="none">'.GetMessage("MIBIX_YAM_AJAX_CHECK_IBLOCK").'</option>';
    $dbIblocks = CIBlock::GetList(array(), $arIblockParams, false, false, array("ID","NAME"));
    while ($arIblock = $dbIblocks->Fetch())
    {
        $arRes["IBLOCK_OPTIONS"] .= '<option value="'.$arIblock['ID'].'">'.$arIblock['NAME'].'</option>';
        //$arRes["IBLOCK_OPTIONS"] = print_r($arIblock,true);
    }
}

// Получение списка разделов на основе ID инфоблока
if (!empty($_POST["action"]) && $_POST["action"]=="get_iblock_sections")
{
    $arSectionsParams = array();
    $arSectionsParams['IBLOCK_ID'] = (!empty($_POST["iblock_id"]) ? intval($_POST["iblock_id"]) : 0);

    // Вытаскиваем разделы инфоблока
    $rsSections = CIBlockSection::GetList(array('LEFT_MARGIN'=>'ASC'), $arSectionsParams);
    while ($arSection = $rsSections->GetNext())
    {
        $arRes["IBLOCK_SECTIONS"] .= '<option value="'.$arSection['ID'].'">'.str_repeat("..", ($arSection['DEPTH_LEVEL']-1)).trim($arSection['NAME']).'</option>';
        //$arRes["IBLOCK_SECTIONS"] .= $arSection['NAME'].' LEFT_MARGIN: '.$arSection['LEFT_MARGIN'].' RIGHT_MARGIN: '.$arSection['RIGHT_MARGIN'].'<br>';
    }
}

// Получение списка категорий Яндекс.Маркета по ID
if (!empty($_POST["action"]) && $_POST["action"]=="get_market_categories")
{
    $parentId = (!empty($_POST["parent_id"]) ? intval($_POST["parent_id"]) : 0);
    $field = (!empty($_POST["field"]) ? intval($_POST["field"]) : 0);
    if(CMibixModelRules::getMarketCategoryCount($parentId)>0)
        $arRes["MARKET_CATEGORY"] = CMibixModelRules::getSelectBoxMarketCategory(0, $field, $parentId);
    else
        $arRes["MARKET_CATEGORY"] = false;
}

// Получение списка категорий Яндекс.Маркета по ID
if (!empty($_POST["action"]) && $_POST["action"]=="get_parameters_select")
{
    $datasourceId = (!empty($_POST["datasource_id"]) ? intval($_POST["datasource_id"]) : 0);
    $IBLOCK_ID = CMibixModelRules::GetIBlockByDatasourceID($datasourceId);

    // available
    $arParamsAvailable = CMibixModelRules::GetArrayParamsByCODE("available");
    $arRes["PARAM_SELECT_AVAILABLE"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParamsAvailable);

    // bid
    $arParams = CMibixModelRules::GetArrayParamsByCODE("bid");
    $arRes["PARAM_SELECT_BID"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // cbid
    $arParams = CMibixModelRules::GetArrayParamsByCODE("cbid");
    $arRes["PARAM_SELECT_CBID"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // price
    $arParams = CMibixModelRules::GetArrayParamsByCODE("price");
    $arRes["PARAM_SELECT_PRICE"] = CMibixModelRules::getOptionsPriceType("");
    $arRes["PARAM_SELECT_PRICE"] .= CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // oldprice
    $arParams = CMibixModelRules::GetArrayParamsByCODE("oldprice");
    $arRes["PARAM_SELECT_OLDPRICE"] = CMibixModelRules::getOptionsPriceType("", true);
    $arRes["PARAM_SELECT_OLDPRICE"] .= CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // picture
    $arParams = CMibixModelRules::GetArrayParamsByCODE("picture");
    $arRes["PARAM_SELECT_PICTURE"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams, "F", false);

    // typeprefix
    $arParams = CMibixModelRules::GetArrayParamsByCODE("typeprefix");
    $arRes["PARAM_SELECT_TYPEPREFIX"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // model
    $arParams = CMibixModelRules::GetArrayParamsByCODE("model");
    $arRes["PARAM_SELECT_MODEL"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // store
    $arParams = CMibixModelRules::GetArrayParamsByCODE("store");
    $arRes["PARAM_SELECT_STORE"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // pickup
    $arParams = CMibixModelRules::GetArrayParamsByCODE("pickup");
    $arRes["PARAM_SELECT_PICKUP"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // delivery
    $arParams = CMibixModelRules::GetArrayParamsByCODE("delivery");
    $arRes["PARAM_SELECT_DELIVERY"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // name
    $arParams = CMibixModelRules::GetArrayParamsByCODE("name");
    $arRes["PARAM_SELECT_NAME"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // description
    $arParams = CMibixModelRules::GetArrayParamsByCODE("description");
    $arRes["PARAM_SELECT_DESCRIPTION"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams, false, false);

    // vendor
    $arParams = CMibixModelRules::GetArrayParamsByCODE("vendor");
    $arRes["PARAM_SELECT_VENDOR"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // vendorcode
    $arParams = CMibixModelRules::GetArrayParamsByCODE("vendorcode");
    $arRes["PARAM_SELECT_VENDORCODE"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // local_delivery_cost
    $arParams = CMibixModelRules::GetArrayParamsByCODE("local_delivery_cost");
    $arRes["PARAM_SELECT_LOCALDELIVERYCOST"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // sales_notes
    $arParams = CMibixModelRules::GetArrayParamsByCODE("sales_notes");
    $arRes["PARAM_SELECT_SALESNOTES"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // manufacturer_warranty
    $arParams = CMibixModelRules::GetArrayParamsByCODE("manufacturer_warranty");
    $arRes["PARAM_SELECT_MANUFACTURERWARRANTY"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // seller_warranty
    $arParams = CMibixModelRules::GetArrayParamsByCODE("seller_warranty");
    $arRes["PARAM_SELECT_SELLERWARRANTY"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // country_of_origin
    $arParams = CMibixModelRules::GetArrayParamsByCODE("country_of_origin");
    $arRes["PARAM_SELECT_COUNTRYOFORIGIN"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // adult
    $arParams = CMibixModelRules::GetArrayParamsByCODE("adult");
    $arRes["PARAM_SELECT_ADULT"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // downloadable
    $arParams = CMibixModelRules::GetArrayParamsByCODE("downloadable");
    $arRes["PARAM_SELECT_DOWNLOADABLE"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // rec
    $arParams = CMibixModelRules::GetArrayParamsByCODE("rec");
    $arRes["PARAM_SELECT_REC"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams, "E");

    // age
    $arParams = CMibixModelRules::GetArrayParamsByCODE("age");
    $arRes["PARAM_SELECT_AGE"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // ageunit
    $arParams = CMibixModelRules::GetArrayParamsByCODE("ageunit");
    $arRes["PARAM_SELECT_AGEUNIT"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // barcode
    $arParams = CMibixModelRules::GetArrayParamsByCODE("barcode");
    $arRes["PARAM_SELECT_BARCODE"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // expiry
    $arParams = CMibixModelRules::GetArrayParamsByCODE("expiry");
    $arRes["PARAM_SELECT_EXPIRY"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // weight
    $arParams = CMibixModelRules::GetArrayParamsByCODE("weight");
    $arRes["PARAM_SELECT_WEIGHT"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // dimensions
    $arParams = CMibixModelRules::GetArrayParamsByCODE("dimensions");
    $arRes["PARAM_SELECT_DIMENSIONS"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // param
    $arParams = CMibixModelRules::GetArrayParamsByCODE("param");
    $arRes["PARAM_SELECT_PARAM"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // cpa
    $arParams = CMibixModelRules::GetArrayParamsByCODE("cpa");
    $arRes["PARAM_SELECT_CPA"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // author
    $arParams = CMibixModelRules::GetArrayParamsByCODE("author");
    $arRes["PARAM_SELECT_AUTHOR"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // publisher
    $arParams = CMibixModelRules::GetArrayParamsByCODE("publisher");
    $arRes["PARAM_SELECT_PUBLISHER"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // series
    $arParams = CMibixModelRules::GetArrayParamsByCODE("series");
    $arRes["PARAM_SELECT_SERIES"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // year
    $arParams = CMibixModelRules::GetArrayParamsByCODE("year");
    $arRes["PARAM_SELECT_YEAR"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // isbn
    $arParams = CMibixModelRules::GetArrayParamsByCODE("isbn");
    $arRes["PARAM_SELECT_ISBN"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // volume
    $arParams = CMibixModelRules::GetArrayParamsByCODE("volume");
    $arRes["PARAM_SELECT_VOLUME"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // part
    $arParams = CMibixModelRules::GetArrayParamsByCODE("part");
    $arRes["PARAM_SELECT_PART"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // language
    $arParams = CMibixModelRules::GetArrayParamsByCODE("language");
    $arRes["PARAM_SELECT_LANGUAGE"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // binding
    $arParams = CMibixModelRules::GetArrayParamsByCODE("binding");
    $arRes["PARAM_SELECT_BINDING"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // page_extent
    $arParams = CMibixModelRules::GetArrayParamsByCODE("page_extent");
    $arRes["PARAM_SELECT_PAGEEXTENT"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // table_of_contents
    $arParams = CMibixModelRules::GetArrayParamsByCODE("table_of_contents");
    $arRes["PARAM_SELECT_TABLEOFCONTENTS"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // performed_by
    $arParams = CMibixModelRules::GetArrayParamsByCODE("performed_by");
    $arRes["PARAM_SELECT_PERFORMEDBY"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // performance_type
    $arParams = CMibixModelRules::GetArrayParamsByCODE("performance_type");
    $arRes["PARAM_SELECT_PERFORMANCETYPE"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // format
    $arParams = CMibixModelRules::GetArrayParamsByCODE("format");
    $arRes["PARAM_SELECT_FORMAT"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // storage
    $arParams = CMibixModelRules::GetArrayParamsByCODE("storage");
    $arRes["PARAM_SELECT_STORAGE"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // recording_length
    $arParams = CMibixModelRules::GetArrayParamsByCODE("recording_length");
    $arRes["PARAM_SELECT_RECORDINGLENGTH"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // artist
    $arParams = CMibixModelRules::GetArrayParamsByCODE("artist");
    $arRes["PARAM_SELECT_ARTIST"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // title
    $arParams = CMibixModelRules::GetArrayParamsByCODE("title");
    $arRes["PARAM_SELECT_TITLE"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // media
    $arParams = CMibixModelRules::GetArrayParamsByCODE("media");
    $arRes["PARAM_SELECT_MEDIA"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // starring
    $arParams = CMibixModelRules::GetArrayParamsByCODE("starring");
    $arRes["PARAM_SELECT_STARRING"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // director
    $arParams = CMibixModelRules::GetArrayParamsByCODE("director");
    $arRes["PARAM_SELECT_DIRECTOR"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // originalname
    $arParams = CMibixModelRules::GetArrayParamsByCODE("originalname");
    $arRes["PARAM_SELECT_ORIGINALNAME"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // country
    $arParams = CMibixModelRules::GetArrayParamsByCODE("country");
    $arRes["PARAM_SELECT_COUNTRY"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // worldregion
    $arParams = CMibixModelRules::GetArrayParamsByCODE("worldregion");
    $arRes["PARAM_SELECT_WORLDREGION"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // region
    $arParams = CMibixModelRules::GetArrayParamsByCODE("region");
    $arRes["PARAM_SELECT_REGION"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // days
    $arParams = CMibixModelRules::GetArrayParamsByCODE("days");
    $arRes["PARAM_SELECT_DAYS"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // datatour
    $arParams = CMibixModelRules::GetArrayParamsByCODE("datatour");
    $arRes["PARAM_SELECT_DATATOUR"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // hotel_stars
    $arParams = CMibixModelRules::GetArrayParamsByCODE("hotel_stars");
    $arRes["PARAM_SELECT_HOTELSTARS"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // room
    $arParams = CMibixModelRules::GetArrayParamsByCODE("room");
    $arRes["PARAM_SELECT_ROOM"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // meal
    $arParams = CMibixModelRules::GetArrayParamsByCODE("meal");
    $arRes["PARAM_SELECT_MEAL"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // included
    $arParams = CMibixModelRules::GetArrayParamsByCODE("included");
    $arRes["PARAM_SELECT_INCLUDED"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // transport
    $arParams = CMibixModelRules::GetArrayParamsByCODE("transport");
    $arRes["PARAM_SELECT_TRANSPORT"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // place
    $arParams = CMibixModelRules::GetArrayParamsByCODE("place");
    $arRes["PARAM_SELECT_PLACE"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // hall_plan
    $arParams = CMibixModelRules::GetArrayParamsByCODE("hall_plan");
    $arRes["PARAM_SELECT_HALLPLAN"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // date
    $arParams = CMibixModelRules::GetArrayParamsByCODE("date");
    $arRes["PARAM_SELECT_DATE"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // is_premiere
    $arParams = CMibixModelRules::GetArrayParamsByCODE("is_premiere");
    $arRes["PARAM_SELECT_ISPREMIERE"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);

    // is_kids
    $arParams = CMibixModelRules::GetArrayParamsByCODE("is_kids");
    $arRes["PARAM_SELECT_ISKIDS"] = CMibixModelRules::getSelectBoxProperty("", $IBLOCK_ID, $arParams);
}

// Получение списка категорий Яндекс.Маркета по ID
if (!empty($_POST["action"]) && $_POST["action"]=="get_step_yml")
{
    $shop_id = (!empty($_POST["shop_id"]) ? intval($_POST["shop_id"]) : 1);

    // Получаем значения и вызываем функцию генерации XML-файла
    $YAM_EXPORT = CMibixYandexExport::get_step_settings($shop_id);
    if(is_array($YAM_EXPORT) && count($YAM_EXPORT) > 0)
    {
        $YAM_EXPORT_LIMIT = $YAM_EXPORT["step_limit"]; // количество элементов, обрабатываемых за 1 шаг
        $YAM_EXPORT_PATH = $DOCUMENT_ROOT . $YAM_EXPORT["step_path"]; // путь сохранения экспортируемого xml-файл

        // замер времени исполнения
        $startTime = microtime(true);

        // Выгрузка завершена или нет
        if(CMibixYandexExport::CreateYML($YAM_EXPORT_PATH, $YAM_EXPORT_LIMIT, false, $shop_id))
            $arRes["CREATE_YML_PROCCESS"] = "Y";
        else
            $arRes["CREATE_YML_PROCCESS"] = "N";

        // замер времени исполнения
        $endTimeSec = round((microtime(true) - $startTime), 3);
        $endTimeMin = round($endTimeSec / 60);
        $arRes["STEP_TIME"] = "[" . $endTimeSec . " ".GetMessage("MIBIX_YAM_AJAX_STEP_VALUE_SEC")."] => [".$endTimeMin." ".GetMessage("MIBIX_YAM_AJAX_STEP_VALUE_MIN")."]";

        if($arRes["CREATE_YML_PROCCESS"] != "Y")
            $arRes["STEP_TIME"] .= "<br><br>".GetMessage("MIBIX_YAM_AJAX_STEP_VALUE_SUCCESS");
    }
    else
    {
        $arRes["CREATE_YML_PROCCESS"] = "N";
        $arRes["STEP_TIME"] = "Error config steps load. Please check fill of the limits for the shop.";
    }
}

$APPLICATION->RestartBuffer();
header('Content-Type: application/json; charset='.LANG_CHARSET);
echo CUtil::PhpToJSObject($arRes);
die();

?>
