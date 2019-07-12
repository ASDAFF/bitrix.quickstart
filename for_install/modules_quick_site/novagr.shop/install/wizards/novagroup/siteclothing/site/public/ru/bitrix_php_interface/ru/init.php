<?
// регистрируем обработчик события создания админ панели
AddEventHandler("catalog", "OnDiscountAdd", "OnDiscountUpdateHandler");
AddEventHandler("catalog", "OnDiscountUpdate", "OnDiscountUpdateHandler");
AddEventHandler("catalog", "OnDiscountDelete", "OnDiscountUpdateHandler");
AddEventHandler("catalog", "OnBeforePriceUpdate", "OnBeforePriceUpdate");
AddEventHandler("iblock", "OnAfterIBlockSectionAdd", "OnAfterIBlockSectionUpdateHandler");
AddEventHandler("iblock", "OnAfterIBlockSectionUpdate", "OnAfterIBlockSectionUpdateHandler");

function OnAfterIBlockSectionUpdateHandler(&$arFields) {

    if (defined('BX_COMP_MANAGED_CACHE') && is_object($GLOBALS['CACHE_MANAGER'])) {

        $res = CIBlock::GetByID($arFields["IBLOCK_ID"]);
        if($arRes = $res->Fetch()) {
            if ($arRes["IBLOCK_TYPE_ID"] == "catalog") {
                // clear cache for menu
                //$GLOBALS['CACHE_MANAGER']->ClearByTag('tree_menu_tag');
                $GLOBALS['CACHE_MANAGER']->ClearByTag('bitrix:menu');
            }
        }
    }
}

function OnDiscountUpdateHandler($ID,$arFields)
{
    BXClearCache(true, "");
}

function OnBeforePriceUpdate($ID,$arFields){
    if(CModule::IncludeModule('catalog'))
    {
        $mxResult = CCatalogSku::GetProductInfo(
            $arFields['PRODUCT_ID']
        );
        if (is_array($mxResult))
        {
            global $CACHE_MANAGER;
            $CACHE_MANAGER->ClearByTag("catalog.list.".$mxResult['ID']);
        }
    }
}

// регистрируем обработчик выполняющийся после добавления элемента
AddEventHandler("iblock", "OnAfterIBlockElementAdd", "OnAfterIBlockElementAddHandler");

// регистрируем обработчик выполняющийся после обновления элемента
AddEventHandler("iblock", "OnAfterIBlockElementUpdate", "OnAfterIBlockElementUpdateHandler");

// регистрируем обработчик выполняющийся до обновления элемента
AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", "OnBeforeIBlockElementUpdateHandler");

// регистрируем обработчик выполняющийся до добавления элемента
AddEventHandler("iblock", "OnBeforeIBlockElementAdd", "OnBeforeIBlockElementAddHandler");

// регистрируем обработчик выполняющийся до генерации меню
AddEventHandler("main", "OnBuildGlobalMenu", Array("Novagroup_Classes_General_Handler", "OnBuildGlobalMenu"));

// обработчик обновления сео ссылок
use Bitrix\Main;
use Bitrix\Main\Entity;

$eventManager = Main\EventManager::getInstance();
$eventManager->addEventHandler("", "SeoReferenceOnAfterUpdate", "SeoReferenceAfterUpdateHandler");
$eventManager->addEventHandler("", "SeoReferenceOnAfterAdd", "SeoReferenceAfterUpdateHandler");

function SeoReferenceAfterUpdateHandler(Entity\Event $event)
{
    //$primary = $event->getParameter("id");

    if (CModule::IncludeModule('novagr.shop'))
    {
        $params = $event->getParameters();
        seoUrlsFileUpdate($params["fields"]["UF_NAME"]);
    }
}

/**
 * отладка
 * @param array $arr
 */
if (!function_exists('deb')) {
    function deb($arr) {
        echo "<pre>";
        print_r($arr);
        echo "</pre>";
    }
}


class NovaGroupLibrary {

    static function init()
    {
        $CURRENT_PATH = dirname(__FILE__);
        $DOCUMENT_ROOT = realpath($CURRENT_PATH."/../../../");
        $ABSOLUTE_BITRIX_ROOT = realpath($CURRENT_PATH."/../../");
        $RELATIVE_BITRIX_ROOT = substr($ABSOLUTE_BITRIX_ROOT,strlen($DOCUMENT_ROOT));

        define('RELATIVE_BITRIX_ROOT',$RELATIVE_BITRIX_ROOT);
        define('ABSOLUTE_BITRIX_ROOT',$ABSOLUTE_BITRIX_ROOT);
    }

    static function load()
    {
        self::init();

        $relative_path = RELATIVE_BITRIX_ROOT.'/php_interface/novagroup/classes/abstract/';
        self::loadClasses($relative_path,"Novagroup_Classes_Abstract_");

        $relative_path = RELATIVE_BITRIX_ROOT.'/php_interface/novagroup/classes/shop/';
        self::loadClasses($relative_path,"Novagroup_Classes_General_");

        $relative_path = RELATIVE_BITRIX_ROOT.'/php_interface/novagroup/tables/mysql/';
        self::loadClasses($relative_path,"Novagroup_Tables_Mysql_");
    }

    static function loadClasses($relative_path,$prefix_class)
    {
        $dir_path = getenv("DOCUMENT_ROOT").$relative_path;
        if(is_dir($dir_path))
        {
            $files = scandir($dir_path);
            if(is_array($files))
            {
                $classes = array();
                foreach($files as $file){

                    $file_path = $dir_path.$file;
                    if(is_file($file_path)){
                        $file_name = pathinfo($file_path,PATHINFO_FILENAME);

                        $classes[$prefix_class.$file_name] = $relative_path.$file;
                    }
                }

                if(count($classes)>0)
                {
                    CModule::AddAutoloadClasses(
                        '', // не указываем имя модуля
                        $classes
                    );
                }

            }
        }
    }
}
NovaGroupLibrary::load();

function seoUrlsFileUpdate($oldUrl) {

    global $CACHE_MANAGER;
    // clear cache
    $CACHE_MANAGER->ClearByTag("seo_id_".$oldUrl);
    Novagroup_Classes_General_Main::updateSeoUrls();
}

/**
 * autofill metatags whel element add or update
 *
 * @param array $arFields
 */
function fillMetatags(&$arFields) {

    //check whether the option is active
    $result = COption::GetOptionString("main", "mt_autofill", 'on');

    if ($result == 'on') {
        $properties = CIBlockProperty::GetList(
            Array("sort"=>"asc", "name"=>"asc"),
            Array("ACTIVE"=>"Y", "IBLOCK_ID"=> $arFields["IBLOCK_ID"])
        );

        $titlePropID = '';
        $headerPropID = '';
        $keywordsPropID = '';
        $descriptionPropID = '';
        $brandPropId = '';
        $meterialPropId = '';
        $samplePropId = '';

        while ($propFields = $properties->GetNext()) {
            if ($propFields["CODE"] == "TITLE") $titlePropID = $propFields["ID"];
            if ($propFields["CODE"] == "HEADER1") $headerPropID = $propFields["ID"];
            if ($propFields["CODE"] == "KEYWORDS") $keywordsPropID = $propFields["ID"];
            if ($propFields["CODE"] == "META_DESCRIPTION") $descriptionPropID = $propFields["ID"];
            if ($propFields["CODE"] == "VENDOR") $brandPropId = $propFields["ID"];
            if ($propFields["CODE"] == "MATERIAL") $meterialPropId = $propFields["ID"];
            if ($propFields["CODE"] == "SAMPLES") $samplePropId = $propFields["ID"];
        }

        foreach ($arFields["PROPERTY_VALUES"][$brandPropId] as $key => $val) {
            if (!empty($val["VALUE"])) {
                $brandId = $val["VALUE"];
                break;
            }
        }

        if (!empty($brandId)) {
            $Arr = CIBlockElement::GetByID($brandId)->GetNext();
            $brandName = $Arr["NAME"];

        }

        // fill title
        foreach ($arFields["PROPERTY_VALUES"][$titlePropID] as $key => $val) {

            if (empty($val["VALUE"])) {
                $arFields["PROPERTY_VALUES"][$titlePropID][$key]["VALUE"] = $arFields["NAME"]." ".$brandName;
                break;
            }
        }

        // fill HEADER1
        foreach ($arFields["PROPERTY_VALUES"][$headerPropID] as $key => $val) {

            if (empty($val["VALUE"])) {
                $arFields["PROPERTY_VALUES"][$headerPropID][$key]["VALUE"] = $arFields["NAME"]." ".$brandName;
                break;
            }
        }
        // fill keywords
        foreach ($arFields["PROPERTY_VALUES"][$keywordsPropID] as $key => $val) {

            if (empty($val["VALUE"])) {
                $arFields["PROPERTY_VALUES"][$keywordsPropID][$key]["VALUE"] = $arFields["NAME"]." ".$brandName;
                break;
            }
        }
        // fill description

        foreach ($arFields["PROPERTY_VALUES"][$descriptionPropID] as $key => $val) {

            if (empty($val["VALUE"])) {

                foreach ($arFields["PROPERTY_VALUES"][$meterialPropId] as $k => $v) {
                    if (!empty($v["VALUE"])) {
                        $materialId = $v["VALUE"];
                        break;
                    }
                }
                if (!empty($materialId)) {
                    $Arr = CIBlockElement::GetByID($materialId)->GetNext();
                    $materialName = $Arr["NAME"];
                }

                $arFields["PROPERTY_VALUES"][$descriptionPropID][$key]["VALUE"] = $arFields["NAME"]." ".$brandName;
                if (!empty($materialName)) {
                    $arFields["PROPERTY_VALUES"][$descriptionPropID][$key]["VALUE"] .= " ".$materialName;

                }

                foreach ($arFields["PROPERTY_VALUES"][$samplePropId] as $k => $v) {

                    if (!empty($v["VALUE"])) {
                        $samplelIDSArr[] = $v["VALUE"];
                    }
                }

                if (count($samplelIDSArr) > 0) {

                    $arSelect = Array("ID", "NAME");
                    $arFilter = Array("IBLOCK_CODE"=>"samples", "ID" => $samplelIDSArr);
                    $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
                    $sampleNames = array();
                    while ($arr = $res->Fetch()) {

                        $sampleNames[] = $arr["NAME"];
                    }
                    $arFields["PROPERTY_VALUES"][$descriptionPropID][$key]["VALUE"] .= " ".implode(', ', $sampleNames);
                }
                break;
            }
        } // end foreach ($arFields["PROPERTY_VALUES"][$descriptionPropID] as $key => $val) {
    }
}


function OnBeforeIBlockElementUpdateHandler(&$arFields) {
    $res = CIBlock::GetByID($arFields["IBLOCK_ID"]);
    if ($arRes = $res->GetNext()) {

        if ( $arRes["CODE"] == "LandingPages") {

            $res = CIBlock::GetProperties($arFields["IBLOCK_ID"], Array(), Array("CODE"=>"URL"));
            if ($resArr = $res->Fetch()) {
                // set landing url
                foreach ($arFields["PROPERTY_VALUES"][$resArr["ID"]] as $key => $value) {
                    $arFields["PROPERTY_VALUES"][$resArr["ID"]][$key]["VALUE"] = "/product/".$arFields["CODE"]."/";
                    break;
                }
            }

        } elseif ( $arRes["CODE"] ==  "novagr_standard_products") {
            // autofill metatags
            fillMetatags($arFields);
        }
    }
}

function OnBeforeIBlockElementAddHandler(&$arFields) {
    $res = CIBlock::GetByID($arFields["IBLOCK_ID"]);
    if ($arRes = $res->GetNext()) {
        if ( $arRes["CODE"] == "LandingPages") {

            $res = CIBlock::GetProperties($arFields["IBLOCK_ID"], Array(), Array("CODE"=>"URL"));
            if ($resArr = $res->Fetch()) {
                // set landing url
                foreach ($arFields["PROPERTY_VALUES"][$resArr["ID"]] as $key => $value) {
                    $arFields["PROPERTY_VALUES"][$resArr["ID"]][$key]["VALUE"] = "/product/".$arFields["CODE"]."/";
                    break;
                }
            }

        } elseif ( $arRes["CODE"] ==  "novagr_standard_products") {
            // autofill metatags
            fillMetatags($arFields);
        }
    }
}


/**
 * обработчик устанавливающий свойства товаров при импорте из 1с
 * @param array $arFields
 */
function modifyPropsFrom1C(&$arFields)
{
    if ($arFields["IBLOCK_ID"] != "#CATALOG_IBLOCK_ID#" && $arFields["IBLOCK_ID"] != "#OFFERS_IBLOCK_ID#") return;

    $exchange1cGroup = "#GROUP_1C_EXCHANGE#"; // Группа для 1С Обмена

    // определяем, входит ли пользователь в группу для 1с обмена
    if (!CSite::InGroup(array($exchange1cGroup))) return;

    if ($arFields["IBLOCK_ID"] == "#CATALOG_IBLOCK_ID#"  ) {

        $importObj = new Novagroup_Classes_General_Import1C($exchange1cGroup, $arFields, 1);
        $importObj->processElement();

    }
    if ($arFields["IBLOCK_ID"] == "#OFFERS_IBLOCK_ID#" && $arFields["RESULT"] > 0) {

        $importObj = new Novagroup_Classes_General_Import1C($exchange1cGroup, $arFields, 2);
        $importObj->processElement();
    }
}

function OnAfterIBlockElementUpdateHandler(&$arFields) {
    // если флаг установлен в true - выходим из обработчика, дабы избежать бесконечной рекурсии
    global $updateHandlerFlag;
    if ($updateHandlerFlag === true) {
        $updateHandlerFlag = false;
        return;
    }
    // обновление торговых предложений
    modifyPropsFrom1C($arFields);
}

function OnAfterIBlockElementAddHandler(&$arFields) {
    global $updateHandlerFlag;
    if ($updateHandlerFlag === true) {
        $updateHandlerFlag = false;
        return;
    }
    // добавление торговых предложений
    modifyPropsFrom1C($arFields);

    // если добавили новость или блог отправляем рассылку
    if ($arFields["IBLOCK_ID"] == "#NEWS_IBLOCK_ID#" && CModule::IncludeModule( "subscribe" ) ){
        // новости
        $rsSites = CIBlock::GetSite($arFields["IBLOCK_ID"]);
        while($arSite = $rsSites->Fetch())
        {
            $rub = CRubric::GetList(array("SORT"=>"ASC", "NAME"=>"ASC"), array("ACTIVE"=>"Y", "LID"=>$arSite["SITE_ID"]));
            while($getRubric = $rub->GetNext())
            {
                if($getRubric['CODE']  == "news")
                {
                    $rsSites = CSite::GetByID($arSite["SITE_ID"]);
                    $arSite = $rsSites->Fetch();
                    $strEmail = $arSite['EMAIL'];


                    if (strpos($strEmail, ",") === false && !empty($strEmail)) {
                        $emailFrom = $strEmail;
                    } else {
                        $emailFrom = 'info@' . $arSite['SERVER_NAME'];
                    }

                    $marFields = Array(
                        "FROM_FIELD" => $emailFrom,
                        "SUBJECT" => "На сайте «". $arSite["SITE_NAME"] ."» размещена новость",
                        "BODY" => "На сайте http://" . $arSite['SERVER_NAME'] . $arSite['DIR'] . " размещена новость!\nНовость можно прочитать по ссылке http://" . $arSite['SERVER_NAME'] . $arSite['DIR'] . "news/" . $arFields["CODE"] . "/" . "/\n\nОтписаться от сообщений можно по ссылке " . "http://" . $arSite['SERVER_NAME'] . $arSite['DIR'] . "cabinet/subscr/",
                        "BODY_TYPE" => "text",
                        "DIRECT_SEND" => "Y",
                        "RUB_ID" => array($getRubric['ID']),
                        "CHARSET" => "Windows-1251"
                    );

                    $mposting = new CPosting();
                    $pID = $mposting->Add($marFields);
                    $mposting->ChangeStatus($pID, "P");
                    $mposting->SendMessage($pID);
                }
            }
        }

    } elseif ($arFields["IBLOCK_ID"] == "#BLOG_IBLOCK_ID#" && CModule::IncludeModule( "subscribe" ) ){
        // блоги
        $rsSites = CIBlock::GetSite($arFields["IBLOCK_ID"]);
        while($arSite = $rsSites->Fetch())
        {
            $rub = CRubric::GetList(array("SORT"=>"ASC", "NAME"=>"ASC"), array("ACTIVE"=>"Y", "LID"=>$arSite["SITE_ID"]));
            while($getRubric = $rub->GetNext())
            {
                if($getRubric['CODE'] == "blogs")
                {
                    $rsSites = CSite::GetByID($arSite["SITE_ID"]);
                    $arSite = $rsSites->Fetch();
                    $strEmail = $arSite['EMAIL'];


                    if (strpos($strEmail, ",") === false && !empty($strEmail)) {
                        $emailFrom = $strEmail;
                    } else {
                        $emailFrom = 'info@' . $arSite['SERVER_NAME'];
                    }

                    $marFields = Array(
                        "FROM_FIELD" => $emailFrom,
                        "SUBJECT" => "На сайте «" . $arSite["SITE_NAME"] . "» новая статья в блоге",
                        "BODY" => "На сайте http://" . $arSite['SERVER_NAME'] . $arSite['DIR'] . " размещена новая статья в блоге!\nПрочитать можно по ссылке http://" . $arSite['SERVER_NAME'] . $arSite['DIR'] . "blogs/" . $arFields["ID"] . "/\n\nОтписаться от сообщений можно по ссылке " . "http://" . $arSite['SERVER_NAME'] . $arSite['DIR']. "cabinet/subscr/",
                        "BODY_TYPE" => "text",
                        "DIRECT_SEND" => "Y",
                        "RUB_ID" => array($getRubric['ID']),
                        "CHARSET" => "Windows-1251"
                    );

                    $mposting = new CPosting();
                    $pID = $mposting->Add($marFields);
                    $mposting->ChangeStatus($pID, "P");
                    $mposting->SendMessage($pID);
                }
            }
        }
    }
}

/**
 *   функция определяет заканчивается ли строка подстрокой
 *
 *   @param string $haystack - строка
 *   @param string $needle - подстрока
 *   @return bool
 *
 */
function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    $start  = $length * -1; //negative
    return (substr($haystack, $start) === $needle);
}

?>