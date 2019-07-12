<?
// число выводимых элементов на странице каталога
define('N_PAGE_SIZE_1', 16);
define('N_PAGE_SIZE_2', 160);

// регистрируем обработчик события создания админ панели
AddEventHandler("main", "OnPanelCreate", "OnPanelCreateHandler");

AddEventHandler("catalog", "OnGetDiscount", Array("Novagroup_Classes_General_Handler", "OnGetDiscountHandler"));

//регистрируем обработчик выполняющийся в эпилоге
AddEventHandler("main", "OnEpilog", Array("Novagroup_Classes_General_Handler", "OnEpilogEventAddHandler"));

// регистрируем обработчики регистрации юзера
AddEventHandler("main", "OnBeforeUserRegister", "OnBeforeUserUpdateHandler");

// регистрируем обработчик выполняющийся до добавления элемента
AddEventHandler("iblock", "OnBeforeIBlockElementAdd", "OnBeforeIBlockElementAddHandler");

//регистрируем обработчик выполняющийся до создания почтового события
AddEventHandler("main", "OnBeforeEventAdd", "OnBeforeEventAddHandler");

// регистрируем обработчик выполняющийся после добавления элемента
AddEventHandler("iblock", "OnAfterIBlockElementAdd", "OnAfterIBlockElementAddHandler");

// регистрируем обработчик выполняющийся после обновления элемента
AddEventHandler("iblock", "OnAfterIBlockElementUpdate", "OnAfterIBlockElementUpdateHandler");

// регистрируем обработчик выполняющийся после совершения заказа
AddEventHandler("sale", "OnSaleComponentOrderOneStepFinal", "OnSaleComponentOrderOneStepFinalHandler");

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

class NovaGroupLibrary {

    static function init()
    {
        $CURRENT_PATH = dirname(__FILE__);
        $DOCUMENT_ROOT = realpath($CURRENT_PATH."/../../../");
        $ABSOLUTE_BITRIX_ROOT = realpath($CURRENT_PATH."/../../");
        $RELATIVE_BITRIX_ROOT = substr($ABSOLUTE_BITRIX_ROOT,strlen($DOCUMENT_ROOT));

        define('RELATIVE_BITRIX_ROOT',$RELATIVE_BITRIX_ROOT);
        define('ABSOLUTE_BITRIX_ROOT',$ABSOLUTE_BITRIX_ROOT);
        define('NOVAGROUP_MODULE_ID','novagr.shop');
        define('COLORS_IBLOCK_ID',"#COLORS_IBLOCK_ID#");
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

function getCabinetLink() {
	
	if (isManager()) {
		$cabinetLink = SITE_DIR . 'cabinet/';// 'managers-cabinet/';
	} else {
		$cabinetLink = SITE_DIR . 'cabinet/';
	}
	return $cabinetLink;
}

function isManager() {
	global $USER;
	$userId = $USER->GetID();
	$validGroup = array();
	$rsGroups = CGroup::GetList(($by="c_sort"), ($order="desc"), Array("STRING_ID" => "sale_administrator"));

	if  ($arGroup = $rsGroups->Fetch()) {
		$validGroup[] = $arGroup["ID"];
	}

	$arGroups = CUser::GetUserGroup($userId);

	foreach ($arGroups as $groupID) {
		if (in_array($groupID, $validGroup)) {
			return true;
		}
	}
	return false;
}

function getCountProducts() {
	CModule::IncludeModule("iblock");
	$arFilter = array('IBLOCK_CODE' => 'novagr_standard_products');
	$countProducts = CIBlockElement::GetList(array(), $arFilter, array());
	return number_format($countProducts, 0, ' ', ' ');;
}

function clearFolder($path){

	if ($handle = opendir($path)) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {

				unlink($path.$file);
			}
		}
		closedir($handle);
	}
}

//return group by code
function GetGroupByCode ($code)
{
    $rsGroups = CGroup::GetList ($by = "c_sort", $order = "asc", Array ("STRING_ID" => $code));
    return $rsGroups->Fetch();
}

function OnBeforeIBlockElementAddHandler(&$arFields) {

    $res = CIBlock::GetByID($arFields["IBLOCK_ID"]);
    if ($arRes = $res->GetNext()) {
        if ( $arRes["CODE"] == "LandingPages") {

            $arFields["PROPERTY_VALUES"]["URL"] = "/product/".$arFields["CODE"]."/";
        }
    }
}


function OnSaleComponentOrderOneStepFinalHandler($ID, &$arFields) {
    // update user field for smart site catalog
    global $USER;
    $userID = $USER->GetID();
    if ($userID > 0) {
        CModule::IncludeModule("iblock");
        $res = CIBlock::GetList(
            Array(),
            Array('TYPE'=>'offers', 'SITE_ID' => SITE_ID, 'ACTIVE'=>'Y',
                "CODE" => 'novagr_standard_products_offers'),
            false
        );
        if ($arRes = $res->Fetch())
        {
            Novagroup_Classes_General_Basket::updateSizesColorsUserField($userID, $arRes["ID"]);
        }
    }
}

/**
 * событие обновления сео записи в публичной части
 * @param $arFields
 */
function OnAfterIBlockElementUpdateHandler(&$arFields) {


}

/**
 * событие добавления сео записи в публичной части
 * @param $arFields
 */
function OnAfterIBlockElementAddHandler(&$arFields) {

}

/**
 * сео
 * @param $arFields
 */
function seoUrlsFileUpdate($oldUrl) {
    global $CACHE_MANAGER;
    // clear cache
    $CACHE_MANAGER->ClearByTag("seo_id_".$oldUrl);

    Novagroup_Classes_General_Main::updateSeoUrls();
}

/**
 * show panel with SEO button in public (only for admins and sale_administrators)
 */
function OnPanelCreateHandler()
{
    if (CModule::IncludeModule('novagr.shop'))
    {
        // add button in control panel
        global $APPLICATION;       
        global $USER;
        $userId = $USER->GetID();
              
        $validGroup = array(1);
        $filter = Array("STRING_ID" => "sale_administrator");
        $rsGroups = CGroup::GetList(($by="c_sort"), ($order="desc"), $filter); 
        
        if  ($arGroup = $rsGroups->Fetch()) {
        	$validGroup[] = $arGroup["ID"];
        }
        
        $arGroups = CUser::GetUserGroup($userId);
        
        $accessDenied = true;
        foreach ($arGroups as $groupID) {
        	if (in_array($groupID, $validGroup)) {
        		$accessDenied = false;
        		break;
        	}
        }
        // access granted only for groups: administrator and sale_administrators
        if ($accessDenied == true) {
        	
        	return ;
        }

        __IncludeLang($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/include/lang/ru/seo_element_edit.php");
        $APPLICATION->AddPanelButton(array(
            "HREF"      => "javascript:seoSettings.seoWindowOpen('".$_SERVER["REQUEST_URI"]."')",
            "SRC"       => "/local/images/novagr.shop/icon_big.png",
            "ALT"       => GetMessage("SEO_URLS_EDIT_LABEL"),
            "MAIN_SORT" => 700,
            "SORT"      => 10
        ));
        $APPLICATION->AddHeadString("<script src='/local/js/novagroup/seoForm.js'></script>");
        $APPLICATION->AddHeadString('<script>var messages = {"SAVE":"'.GetMessage("SEO_URLS_SAVE_LABEL").'", "SEO_URLS_EDIT_LABEL":"'.GetMessage("SEO_URLS_EDIT_LABEL").'", "SEO_URLS_ALERT_OLD_URL":"'.GetMessage("SEO_URLS_ALERT_OLD_URL").'", "SEO_URLS_CLOSE_LABEL":"'.GetMessage("SEO_URLS_CLOSE_LABEL").'", "SEO_URLS_UPDATE_LABEL":"'.GetMessage("SEO_URLS_UPDATE_LABEL").'", "SEO_URLS_ACCESS_DENIED":"'.GetMessage("SEO_URLS_ACCESS_DENIED").'", }; seoSettings.init(messages);</script>');

    }
}

/**
 * функция определения версии установленного модуля
 *
 * @return string
 */
function NovaGroupGetVersionModule()
{
    if(defined('NOVAGROUP_GET_VERSION_MODULE'))
    {
        $version = NOVAGROUP_GET_VERSION_MODULE;
    } else {
        //if exists novagr.shop
        if (file_exists($includeFile = getenv('DOCUMENT_ROOT') . '/bitrix/modules/novagr.shop/install/index.php')) {
            include($includeFile); $module = new novagr_shop(); if($module->IsInstalled()) $version = $module->MODULE_VERSION;
        }
        //if nothing
        else
            $version = "0.0.0";
        //set current version
        define('NOVAGROUP_GET_VERSION_MODULE',$version);
    }
    return $version;
}

/**
 *   функция определяет начинается ли строка с подстроки
 *
 *   @param string $haystack - строка
 *   @param string $needle - подстрока
 *   @return bool
 *
 */
function startsWith($haystack, $needle)
{
    $length = strlen($needle);
    return (substr($haystack, 0, $length) === $needle);
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

/**
 *   функция возвращает название класса для четных и нечетных строк таблицы
 *   используется когда число строк таблицы неизвестно
 *
 *   @param array $variants - массив значений для нечетной и четной строки $variants[0] - нечет,
 *    $variants[1] - четная строка
 *   @param bool $evenClass - признак того что текущий элемент четный
 *   @return string
 *
 */
function getClassForRow($variants = array(), &$evenClass)
{
    if ($evenClass == true) {
        $result = $variants[0];
        $evenClass = false;
    } else {
        $result = $variants[1];
        $evenClass = true;
    }
    return $result;
}

/**
 * Склонение существительных с числительными
 * @param int $n число
 * @param string $form1 Единственная форма: 1 секунда
 * @param string $form2 Двойственная форма: 2 секунды
 * @param string $form5 Множественная форма: 5 секунд
 * @return string Правильная форма
 */
function pluralForm($n, $form1, $form2, $form5) {
    $n = abs($n) % 100;
    $n1 = $n % 10;
    if ($n > 10 && $n < 20) return $form5;
    if ($n1 > 1 && $n1 < 5) return $form2;
    if ($n1 == 1) return $form1;
    return $form5;
}

/**
 * функция ресайзит картинку и сохраняет в кэш, при последующем запросе возвращает из кеша
 *
 * @param int $photoId
 * @return array
 */
function MakeResizePicture($photoId, $params = array()) {
    
	if ($params["WIDTH"]>0) $width = $params["WIDTH"];
	else $width = 180;
	
	if ($params["HEIGHT"]>0) $height = $params["HEIGHT"];
	else $height = 240;
	
	$arFileTmp = CFile::ResizeImageGet(
        $photoId,
        array("width" => $width, 'height' => $height),
        BX_RESIZE_IMAGE_PROPORTIONAL,
        true );
    return $arFileTmp;
}

/**
 * фукнкция по коду валюты возвращает ее строковое представление на сайте
 * @param string $currency
 * @return string
 */
function getCurrencyAbbr($currency) {

    switch ($currency) {
        case "UAH":
            $result = "грн";
            break;
        case "USD":
            $result = "дол";
            break;
        case "EUR":
            $result = "евро";
            break;
        default:
            $result = "руб";
    }
    return $result;
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

/**
 * событие вызывается перед регистрацией пользователя через сайт
 * (в административном интерфейсе при добавлении нового пользователя ничего не вызывается, если это потребуется, то нужно вынести событие в модуль)
 *
 * @param $arFields
 */
function OnBeforeUserUpdateHandler(&$arFields) {
    // в полес с логином помещаем емэйл при регистрации
    $arFields["LOGIN"] = $arFields["EMAIL"];

}

/**
 * предполагаем что заказы будут создаваться через публичную часть сайта, тогда письма будут приходить с цветами и размерами
 *
 * @param $event
 * @param $lid
 * @param $arFields
 */
function OnBeforeEventAddHandler(&$event, &$lid, &$arFields)
{
    if ($event == 'SALE_NEW_ORDER') {
        //список товаров пуст
        $ORDER_LIST = array();
        //получаем товары из корзины
        $dbBasketItems = CSaleBasket::GetList(
            array(),
            array(
                "LID" => $lid,
                "ORDER_ID" => $arFields['ORDER_ID']
            )
        );
        //заполняем массив товаров доп. данными - размер, цвет, штрихкод
        while ($arBasketItem = $dbBasketItems->Fetch()) {
            $arSelect = Array("ID", "NAME", "IBLOCK_ID", "PROPERTY_STD_SIZE.NAME", "PROPERTY_COLOR.NAME", "PROPERTY_CML2_BAR_CODE");
            $arFilter = Array("ID" => $arBasketItem['PRODUCT_ID']);
            $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
            $PRODUCT_NAME = array();
            while ($ob = $res->GetNextElement()) {
                $arFieldsElement = $ob->GetFields();

                $PRODUCT_NAME[] = $arFieldsElement['NAME'];
                if (trim($arFieldsElement['PROPERTY_COLOR_NAME']) <> "") {
                    $resProperty = CIBlockProperty::GetByID("COLOR", $arFieldsElement['IBLOCK_ID']);
                    $PROPERTY_NAME = ($ar_res = $resProperty->GetNext()) ? $ar_res['NAME'] . " " : "";
                    $PRODUCT_NAME[] = $PROPERTY_NAME . $arFieldsElement['PROPERTY_COLOR_NAME'];
                }
                if (trim($arFieldsElement['PROPERTY_STD_SIZE_NAME']) <> "") {
                    $resProperty = CIBlockProperty::GetByID("STD_SIZE", $arFieldsElement['IBLOCK_ID']);
                    $PROPERTY_NAME = ($ar_res = $resProperty->GetNext()) ? $ar_res['NAME'] . " " : "";
                    $PRODUCT_NAME[] = $PROPERTY_NAME . $arFieldsElement['PROPERTY_STD_SIZE_NAME'];
                }
                $ORDER_LIST[] = implode(", ", $PRODUCT_NAME) . " - " . (int)$arBasketItem['QUANTITY'] . " шт. по " . SaleFormatCurrency($arBasketItem['PRICE'], $arBasketItem['CURRENCY']);
            }
        }
        //добавляем пустую строку, для красивости в почтовом шаблоне
        $ORDER_LIST[] = "";
        //теперь в почтовый шаблон улетают новые данные, с размером, цветом и т.д.
        $arFields['ORDER_LIST'] = implode("\n", $ORDER_LIST);
    }
}

function novagr_main_sort($a, $b)
{
    if ($a['SORT'] == $b['SORT']) {
        return 0;
    }
    return ($a['SORT'] < $b['SORT']) ? -1 : 1;
}
function isMobile() {

	if (preg_match("#Android|webOS|iPhone|iPad|BlackBerry#i", $_SERVER["HTTP_USER_AGENT"], $arr)) {

		return true;

	} else {
		return false;
	}
}
?>