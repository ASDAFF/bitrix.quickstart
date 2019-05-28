<?
//define("NO_KEEP_STATISTIC", true);
//define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
//require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->RestartBuffer();
//require_once($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/functions.php");
//require_once($_SERVER["DOCUMENT_ROOT"].'/local/vendor/autoload.php');
GLOBAL $APPLICATION, $USER;
$json = true;
$RESULT = array();

$_SERVER['X-Api-Token'] = "b955cda66e75cb0ace5987a2e042fd50";

if (!class_exists('itsferaRest')) {
    class itsferaRest
    {
        function authByToken($token)
        {
            $result = false;
            $filter = Array("UF_TOKEN" => $token);
            $arSel = array("ID", "ACTIVE");
            $rsUsers = CUser::GetList(($by = "name"), ($order = "asc"), $filter, array("FIELDS" => $arSel));
            if ($arUser = $rsUsers->GetNext()) {
                if ($arUser['ACTIVE'] == "Y") {
                    if ($USER->Authorize($arUser['ID']))
                        $result = true;
                }
            }
            return $result;
        }

        function getUserData($string)
        {
            // поиск по логину
            $rsUser = CUser::GetByLogin($string);
            if (!$arUser = $rsUser->Fetch()) {

                //поиск по email
                $filter = Array("EMAIL" => $string);
                $rsUsers = CUser::GetList(($by = "name"), ($order = "asc"), $filter);
                if (!$arUser = $rsUsers->GetNext()) {

                    //поиск по телефону
                    $filter = Array("PERSONAL_PHONE" => $string);
                    $rsUsers = CUser::GetList(($by = "name"), ($order = "asc"), $filter);
                    $arUser = $rsUsers->GetNext();
                }
            }
            return $arUser;
        }
    }
}

switch ($_REQUEST['mode']) {


    case 'register': // 1. /users
        $userNew = new CUser;
        $token = md5(time() + $_REQUEST['user']['login']);
        $arFields = Array(
            "NAME" => $_REQUEST['user']['first_name'],
            "LAST_NAME" => $_REQUEST['user']['second_name'],
            "EMAIL" => $_REQUEST['user']['email'],
            "LOGIN" => $_REQUEST['user']['login'],
            "LID" => "ru",
            "ACTIVE" => "Y",
            "GROUP_ID" => array(6),
            "PASSWORD" => $_REQUEST['user']['pass'],
            "CONFIRM_PASSWORD" => $_REQUEST['user']['confirm_pass'],
            "PERSONAL_PHONE" => $_REQUEST['user']['phone'],
            "UF_TOKEN" => $token
        );
        $ID = $userNew->Add($arFields);
        if (intval($ID) > 0) {
            $RESULT['erroe_code'] = 0;
            $RESULT['token'] = $token;
        } else {
            $RESULT['erroe_code'] = 1;
            $RESULT['erroe_desc'] = $userNew->LAST_ERROR;
        }
        break;


    case 'login': // 2. /users/login
        if ($_REQUEST['user']['login']) {
            $rsUser = CUser::GetByLogin($_REQUEST['user']['login']);
            if (!$arUser = $rsUser->Fetch()) {
                $filter = Array("EMAIL" => $_REQUEST['user']['login']);
                $arSel = array("LOGIN", "ACTIVE");
                $rsUsers = CUser::GetList(($by = "name"), ($order = "asc"), $filter, array("FIELDS" => $arSel));
                if ($arUser = $rsUsers->GetNext()) {
                    if ($arUser['ACTIVE'] == "Y")
                        $_REQUEST['user']['login'] = $arUser['LOGIN'];
                } else {
                    $filter = Array("PERSONAL_PHONE" => $_REQUEST['user']['login']);
                    $arSel = array("LOGIN", "ACTIVE");
                    $rsUsers = CUser::GetList(($by = "name"), ($order = "asc"), $filter, array("FIELDS" => $arSel));
                    if ($arUser = $rsUsers->GetNext()) {
                        $_REQUEST['user']['login'] = $arUser['LOGIN'];
                    }
                }
            }
            if ($arUser['ACTIVE'] == 'N') {
                $RESULT['erroe_code'] = 2;
                $RESULT['erroe_desc'] = "Пользователь деактивирован.";
            }

            global $USER;
            if (!is_object($USER)) $USER = new CUser;
            $arAuthResult = $USER->Login($_REQUEST['user']['login'], $_REQUEST['user']['pass'], "Y");
            $APPLICATION->arAuthResult = $arAuthResult;
            if ($arAuthResult) {
                $RESULT['erroe_code'] = 0;
                $RESULT['token'] = $token;
            } else {
                $RESULT['erroe_code'] = 1;
                $RESULT['erroe_desc'] = "Не удалось авторизовать.";
            }
        } elseif ($_REQUEST['user']['social_type']) {
            // @TODO авторизация по соцсети
            if ($arAuthResult) {
                $RESULT['erroe_code'] = 0;
                $RESULT['token'] = $token;
            } else {
                $RESULT['erroe_code'] = 1;
                $RESULT['erroe_desc'] = "Не удалось авторизовать.";
            }
        }
        break;


    case 'feedback': // 3. /feedback
        itsferaRest::authByToken($_SERVER['X-Api-Token']);

        // @TODO добавляем фидбек
        if ($feedback) {
            $RESULT['erroe_code'] = 0;
        } else {
            $RESULT['erroe_code'] = 1;
            $RESULT['erroe_desc'] = "Не удалось авторизовать.";
        }

        break;


    case 'reviews': // 4. /reviews
        CModule::IncludeModule("iblock");
        itsferaRest::authByToken($_SERVER['X-Api-Token']);

        if ((int)$_REQUEST['object_id'] > 0) {
            $arSelect = Array("ID", "NAME", "PREVIEW_TEXT");
            $arFilter = Array("IBLOCK_ID" => getIBlockIdByCode("product_comment"), "ACTIVE" => "Y", "PROPERTY_SKU" => $_REQUEST['object_id']);
            $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
            while ($ob = $res->GetNextElement()) {
                $arFields = $ob->GetFields();
                $data[] = array("id" => "", "name" => "", "description" => "text", "rate" => "", "date" => "");
                // @TODO вывести список отзывов
            }
            $RESULT['erroe_code'] = 0;
            $RESULT['data'] = $data;
        } else {
            $RESULT['erroe_code'] = 1;
            $RESULT['erroe_desc'] = "Не указан id товара.";
        }

        break;


    case 'reviews_add': // 5. /reviews @TODO ошибка адреса?
        CModule::IncludeModule("iblock");
        itsferaRest::authByToken($_SERVER['X-Api-Token']);

        $el = new CIBlockElement;
        $PROP = array();
        $PROP["USER_NAME"] = $_REQUEST['user']['name'];
        $PROP["RATE"] = $_REQUEST['review']['rate'];
        $PROP["SKU"] = $_REQUEST['store']['id'];
        $arLoadProductArray = Array(
            "MODIFIED_BY" => $USER->GetID(),
            "IBLOCK_SECTION_ID" => false,
            "IBLOCK_ID" => getIBlockIdByCode("product_comment"),
            "PROPERTY_VALUES" => $PROP,
            "NAME" => "Отзыв от " . $_REQUEST['user']['name'],
            "ACTIVE" => "N",
            "PREVIEW_TEXT" => $_REQUEST['review']['body'],
        );
        if ($PRODUCT_ID = $el->Add($arLoadProductArray)) {
            $RESULT['erroe_code'] = 0;
        } else {
            $RESULT['erroe_code'] = 1;
            $RESULT['erroe_desc'] = $el->LAST_ERROR;
        }

        break;
    case 'shops': // 6. /shops
        CModule::IncludeModule("iblock");
        itsferaRest::authByToken($_SERVER['X-Api-Token']);

        $iblockId = getIBlockIdByCode("shops");
        $arSelect = Array("ID", "NAME", "PROPERTY_TIME", "PROPERTY_COORDS");
        $arFilter = Array("IBLOCK_ID" => $iblockId, "ACTIVE" => "Y");
        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $PHONES = array();
            $coords = explode(",", $arFields['PROPERTY_COORDS_VALUE']);
            $resProp = CIBlockElement::GetProperty($iblockId, $arFields['ID'], "sort", "asc", array("CODE" => "PHONES"));
            while ($obProp = $resProp->GetNext())
                $PHONES[] = $obProp['VALUE'];
            $data[] = array(
                "id" => $arFields['ID'],
                "name" => $arFields['NAME'],
                "adress" => $arFields['ID'],
                "phone" => implode(", ", $PHONES),
                "time" => $arFields['PROPERTY_TIME_VALUE'],
                "latitude" => $coords[0],
                "longtitude" => $coords[1],
                "subway" => $arFields['PROPERTY_SUBWAY_VALUE'],
                "subway_color" => $arFields['PROPERTY_SUBWAY_COLOR_VALUE']
            );
        }
        if (intval($res->SelectedRowsCount()) > 0) {
            $RESULT['erroe_code'] = 0;
            $RESULT['data'] = $data;
        } else {
            $RESULT['erroe_code'] = 1;
            $RESULT['erroe_desc'] = "Не удалось найти информацию о магазинах.";
        }
        break;

    case 'stores': // 7. /stores
        CModule::IncludeModule("iblock");
        itsferaRest::authByToken($_SERVER['X-Api-Token']);

        $iblockId = getIBlockIdByCode("stores");
        $arSelect = Array("ID", "NAME", "PROPERTY_TIME", "PROPERTY_COORDS");
        $arFilter = Array("IBLOCK_ID" => $iblockId, "ACTIVE" => "Y");
        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $PHONES = array();
            $coords = explode(",", $arFields['PROPERTY_COORDS_VALUE']);
            $resProp = CIBlockElement::GetProperty($iblockId, $arFields['ID'], "sort", "asc", array("CODE" => "PHONES"));
            while ($obProp = $resProp->GetNext())
                $PHONES[] = $obProp['VALUE'];
            $data[] = array(
                "id" => $arFields['ID'],
                "name" => $arFields['NAME'],
                "adress" => $arFields['ID'],
                "phone" => implode(", ", $PHONES),
                "time" => $arFields['PROPERTY_TIME_VALUE'],
                "latitude" => $coords[0],
                "longtitude" => $coords[1],
            );
        }
        if (intval($res->SelectedRowsCount()) > 0) {
            $RESULT['erroe_code'] = 0;
            $RESULT['data'] = $data;
        } else {
            $RESULT['erroe_code'] = 1;
            $RESULT['erroe_desc'] = "Не удалось найти информацию о магазинах.";
        }
        break;


    case 'dictionaries': // 8. /dictionaries
        CModule::IncludeModule("iblock");
        itsferaRest::authByToken($_SERVER['X-Api-Token']);

        // @TODO города откуда?
        $res = CIBlock::GetList(
            Array(),
            Array(
                'TYPE' => 'mht_products',
                'SITE_ID' => SITE_ID,
                'ACTIVE' => 'Y',
            ), false
        );
        while ($ar_res = $res->Fetch()) {
            $categories[] = array("id" => $ar_res['ID'], "name" => $ar_res['NAME']);

            $arFilter = Array('IBLOCK_ID' => $ar_res['ID'], 'GLOBAL_ACTIVE' => 'Y');
            $db_list = CIBlockSection::GetList(Array($by => $order), $arFilter, false);
            while ($ar_result = $db_list->GetNext()) {
                $subcategories[] = array("id" => $ar_result['ID'], "name" => $ar_result['NAME']);
            }
        }

        $RESULT['erroe_code'] = 0;
        $RESULT['data']['categories'] = $categories;
        $RESULT['data']['subcategories'] = $subcategories;


        break;

    case 'objects': // 9. /objects
        CModule::IncludeModule("iblock");
        CModule::IncludeModule("catalog");
        CModule::IncludeModule("sale");
        itsferaRest::authByToken($_SERVER['X-Api-Token']);

        $iblockId = $_REQUEST['category_id'];
        $arSelect = Array("ID", "NAME", "PREVIEW_TEXT", "DETAIL_PICTURE", "PROPERTY_CML2_ARTICLE", "PROPERTY_vote_count", "PROPERTY_rating");
        $arFilter = Array("IBLOCK_ID" => $iblockId, "SECTION_ID" => $_REQUEST['subcategory_id'], "ACTIVE" => "Y");
        $res = CIBlockElement::GetList(Array(), $arFilter, false, Array("nPageSize" => 50, "iNumPage" => $_REQUEST['page'] ? $_REQUEST['page'] : 1), $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $PHOTOS = array();
            if($arFields['DETAIL_PICTURE'])
                $PHOTOS[] = "https://moshoztorg.ru".cfile::getpath($arFields['DETAIL_PICTURE']);
            $resProp = CIBlockElement::GetProperty($iblockId, $arFields['ID'], "sort", "asc", array("CODE" => "MORE_PHOTOS"));
            while ($obProp = $resProp->GetNext())
                $PHOTOS[] = "https://moshoztorg.ru" . cfile::getpath($obProp['VALUE']);
            $arPrice = CCatalogProduct::GetOptimalPrice($arFields['ID'], 1, $USER->GetUserGroupArray(), "N");
            $data[] = array(
                'object_id' => $arFields['ID'],
                'name' => $arFields['NAME'],
                'desc' => $arFields['PREVIEW_TEXT'],
                'price' => $arPrice['DISCOUNT_PRICE'],
                'code' => $arFields['CML2_ARTICLE'],
                'vendor_code' => $arFields['ID'], // @TODO коды откуда?
                'picture' => $PHOTOS,
                'rate_count' => $arFields['PROPERTY_VOTE_COUNT_VALUE'],
                'rate_average' => $arFields['PROPERTY_RATING_VALUE']
            );
        }
        $RESULT['erroe_code'] = 0;
        $RESULT['data'] = $data;
        break;


    case 'stocks': // 10. /stocks
        CModule::IncludeModule("iblock");
        itsferaRest::authByToken($_SERVER['X-Api-Token']);


        $arSelect = Array("ID", "NAME", "PREVIEW_PICTURE", "ACTIVE_FROM", "ACTIVE_TO");
        $arFilter = Array("IBLOCK_ID" => getIBlockIdByCode("actions"), "ACTIVE" => "Y");
        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $objects = array();
            $resProp = CIBlockElement::GetProperty(getIBlockIdByCode("actions"), $arFields['ID'], "sort", "asc", array("CODE" => "ELEMENT_ID"));
            while ($obProp = $resProp->GetNext())
                $objects[] = $obProp['VALUE'];

            $data = array(
                'id' => $arFields['ID'],
                'name' => $arFields['NAME'],
                'picture' => "https://moshoztorg.ru" . cfile::getpath($arFields['PREVIEW_PICTURE']),
                'time' => $arFields['ACTIVE_FROM'] . " - " . $arFields['ACTIVE_TO']
            );

            $arSelect = Array("ID", "NAME", "PREVIEW_TEXT", "DETAIL_PICTURE", "PROPERTY_CML2_ARTICLE", "PROPERTY_vote_count", "PROPERTY_rating");
            $arFilter = Array("ID" => $objects, "ACTIVE" => "Y");
            $resObj = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
            while ($obObj = $resObj->GetNextElement()) {
                $arFields = $obObj->GetFields();
                $objects = $PHOTOS = array();
                if($arFields['DETAIL_PICTURE'])
                    $PHOTOS[] = "https://moshoztorg.ru".cfile::getpath($arFields['DETAIL_PICTURE']);
                $resProp = CIBlockElement::GetProperty($iblockId, $arFields['ID'], "sort", "asc", array("CODE" => "MORE_PHOTOS"));
                while ($obProp = $resProp->GetNext())
                    $PHOTOS[] = "https://moshoztorg.ru" . cfile::getpath($obProp['VALUE']);
                $arPrice = CCatalogProduct::GetOptimalPrice($arFields['ID'], 1, $USER->GetUserGroupArray(), "N");
                $objects[] = array(
                    'object_id' => $arFields['ID'],
                    'name' => $arFields['NAME'],
                    'desc' => $arFields['PREVIEW_TEXT'],
                    'price' => $arPrice['DISCOUNT_PRICE'],
                    'code' => $arFields['CML2_ARTICLE'],
                    'vendor_code' => $arFields['ID'], // @TODO коды откуда?
                    'picture' => $PHOTOS,
                    'rate_count' => $arFields['PROPERTY_VOTE_COUNT_VALUE'],
                    'rate_average' => $arFields['PROPERTY_RATING_VALUE']
                );
            }
            $data['objects'] = $objects;

            $RESULT['data'][] = $data;
        }
        $RESULT['erroe_code'] = 0;

        break;


    case 'passwords': // 11. /passwords
        itsferaRest::authByToken($_SERVER['X-Api-Token']);
        if ($arUser = itsferaRest::getUserData($_REQUEST['user']['auth'])) {
            $arResult = $USER->SendPassword($arUser['LOGIN'], $arUser['EMAIL']);
            if ($arResult["TYPE"] == "OK")
                $RESULT['erroe_code'] = 0;
            else {
                $RESULT['erroe_code'] = 2;
                $RESULT['erroe_desc'] = "Пользователь с введенным логином/email/телефоном не найден.";
            }
        } else {
            $RESULT['erroe_code'] = 1;
            $RESULT['erroe_desc'] = "Не удалось авторизоваться";
        }


        break;


    case 'questions': // 12. /questions
        CModule::IncludeModule("iblock");
        itsferaRest::authByToken($_SERVER['X-Api-Token']);

        $el = new CIBlockElement;
        $PROP = array();
        $PROP["NAME"] = $_REQUEST['user']['name'];
        $PROP["EMAIL"] = $_REQUEST['user']['email'];
        $PROP["QUESTION"][0] = Array("VALUE" => Array("TEXT" => $_REQUEST['message']['body'], "TYPE" => "text"));
        $arLoadProductArray = Array(
            "MODIFIED_BY" => $USER->GetID(),
            "IBLOCK_SECTION_ID" => false,
            "IBLOCK_ID" => getIBlockIdByCode("faq"),
            "PROPERTY_VALUES" => $PROP,
            "NAME" => "Вопрос от " . $_REQUEST['user']['name'],
            "ACTIVE" => "N",
            "PREVIEW_TEXT" => $_REQUEST['message']['body'],
        );
        if ($PRODUCT_ID = $el->Add($arLoadProductArray)) {
            $RESULT['erroe_code'] = 0;
        } else {
            $RESULT['erroe_code'] = 1;
            $RESULT['erroe_desc'] = $el->LAST_ERROR;
        }
        break;


    case 'faq': // 13. /faq
        CModule::IncludeModule("iblock");
        itsferaRest::authByToken($_SERVER['X-Api-Token']);

        $arSelect = Array("ID", "NAME", "PROPERTY_QUESTION", "PROPERTY_ANSWER", "PROPERTY_NAME", "PROPERTY_EMAIL");
        $arFilter = Array("IBLOCK_ID" => getIBlockIdByCode("faq"), "ACTIVE" => "Y");
        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();

            // TODO структура кривая?
            $data[] = array(
                "question" => strip_tags($arFields['PROPERTY_QUESTION_VALUE']['TEXT']),
                "answer" => strip_tags($arFields['PROPERTY_ANSWER_VALUE']['TEXT']),
                "name" => $arFields['PROPERTY_NAME_VALUE'],
                "email" => $arFields['PROPERTY_EMAIL_VALUE'],
            );
        }
        $RESULT['erroe_code'] = 0;
        $RESULT['data'] = $data;
        break;


    case 'orders': // 14. /orders
        CModule::IncludeModule("iblock");
        CModule::IncludeModule("catalog");
        CModule::IncludeModule("sale");
        itsferaRest::authByToken($_SERVER['X-Api-Token']);
        $arFilter = Array("USER_ID" => CUser::GetID(),);
        $db_sales = CSaleOrder::GetList(array("DATE_INSERT" => "ASC"), $arFilter);
        while ($arOrder = $db_sales->Fetch()) {
            $db_props = CSaleOrderPropsValue::GetOrderProps($arOrder['ID']);
            while ($arProps = $db_props->Fetch())
                if ($arProps['CODE'] == 'ADDRESS')
                    $deliveryAddress = $arProps['VALUE'];
            $items = array();
            $dbBasketItems = CSaleBasket::GetList(
                array(
                    "NAME" => "ASC",
                    "ID" => "ASC"
                ),
                array(
                    "ORDER_ID" => $arOrder['ID']
                ),
                false,
                false,
                array("NAME", "QUANTITY", "PRODUCT_ID")
            );
            while ($arItems = $dbBasketItems->Fetch()) {
                $res = CIBlockElement::GetByID($arItems["PRODUCT_ID"]);
                $product = $res->GetNext();
                $items[] = array(
                    "name" => $arItems['NAME'],
                    "desc" => $product['PREVIEW_TEXT'],
                    "count" => $arItems["QUANTITY"],
                );
            }
            $data[] = array(
                "id" => $arOrder["ID"],
                "number" => $arOrder["ID"],
                "date" => MakeTimeStamp($arOrder["DATE_INSERT"], "DD.MM.YYYY HH:MI:SS"),
                "price" => $arOrder["PRICE"],
                "store" => $deliveryAddress,
                "items" => $items,
            );
        }
        break;


    case 'loyality': // 15. /loyality
        itsferaRest::authByToken($_SERVER['X-Api-Token']);

        // TODO нет готового функционала
        break;


    case 'buy_with_call': // 16. /buy_with_call
        CModule::IncludeModule("iblock");
        itsferaRest::authByToken($_SERVER['X-Api-Token']);

        $el = new CIBlockElement;
        $PROP = array();
        $PROP["PRODUCT"] = $_REQUEST['object_id']; // TODO нужен id, не передаётся в ТЗ
        $PROP["PHONE"] = $_REQUEST['user']['phone'];
        $arLoadProductArray = Array(
            "MODIFIED_BY" => $USER->GetID(),
            "IBLOCK_SECTION_ID" => false,
            "IBLOCK_ID" => getIBlockIdByCode("one-click"),
            "PROPERTY_VALUES" => $PROP,
            "NAME" => $_REQUEST['user']['name'],
            "ACTIVE" => "N",
        );
        if ($PRODUCT_ID = $el->Add($arLoadProductArray)) {
            $RESULT['erroe_code'] = 0;
        } else {
            $RESULT['erroe_code'] = 1;
            $RESULT['erroe_desc'] = $el->LAST_ERROR;
        }
        break;


    case 'user_edit': // 17. /users/edit
        itsferaRest::authByToken($_SERVER['X-Api-Token']);
        $arUser = itsferaRest::getUserData($_REQUEST['user']['login']);

        $user = new CUser;
        $fields = Array(
            "LOGIN" => $_REQUEST['user']['login'],
            "NAME" => $_REQUEST['user']['first_name'],
            "LAST_NAME" => $_REQUEST['user']['second_name'],
            "EMAIL" => $_REQUEST['user']['email'],
            "PERSONAL_PHONE" => $_REQUEST['user']['phone'],
            "PASSWORD" => $_REQUEST['user']['pass'],
            "CONFIRM_PASSWORD" => $_REQUEST['user']['confirm_pass'],
        );
        if ($arUser['ID']) {
            if ($user->Update($arUser['ID'], $fields)) {
                $RESULT['erroe_code'] = 0;
                $RESULT['api-token'] = $arUser['UF_TOKEN'];
            } else {
                $RESULT['erroe_code'] = 1;
                $RESULT['erroe_desc'] = $user->LAST_ERROR;
            }
        } else {
            $RESULT['erroe_code'] = 2;
            $RESULT['erroe_desc'] = "Пользователь с логином \"" . $_REQUEST['user']['login'] . "\" не найден";
        }
        break;


    case 'order_reject': // 18. /orders/reject
        itsferaRest::authByToken($_SERVER['X-Api-Token']);
        CModule::IncludeModule("iblock");
        CModule::IncludeModule("catalog");
        CModule::IncludeModule("sale");

        CSaleOrder::CancelOrder($_REQUEST['order']['id'], "Y", "Отмена заказа из приложения");
        $RESULT['erroe_code'] = 0;

        break;


    case 'subscribe': // 19. /subscribe
        itsferaRest::authByToken($_SERVER['X-Api-Token']);
        CModule::IncludeModule("subscribe");

        $arFields = Array(
            "USER_ID" => CUSER::GetID(),
            "FORMAT" => "html",
            "EMAIL" => $_REQUEST['user']['email'],
            "ACTIVE" => "Y",
            "RUB_ID" => 1
        );
        $subscr = new CSubscription;
        $ID = $subscr->Add($arFields);
        if ($ID > 0) {
            CSubscription::Authorize($ID);
            $RESULT['erroe_code'] = 0;
        } else {
            $RESULT['erroe_code'] = 1;
            $RESULT['erroe_desc'] = $subscr->LAST_ERROR;
        }
        break;


    case 'order_add': // 20. /orders
        itsferaRest::authByToken($_SERVER['X-Api-Token']);

        // TODO впилить функционал добавления заказа
        CSaleBasket::DeleteAll(3);
        $_REQUEST['user']['country'];
        if(!empty($_REQUEST['user']))
            $PERSON_TYPE_ID = 1;
        else
            $PERSON_TYPE_ID = 2;
        $arFields = array(
            "LID" => SITE_ID,
            "PERSON_TYPE_ID" => $PERSON_TYPE_ID,
            "PAYED" => "N",
            "CANCELED" => "N",
            "STATUS_ID" => "N",
            "PRICE" => 279.32,
            "CURRENCY" => "USD",
            "USER_ID" => IntVal($USER->GetID()),
            "PAY_SYSTEM_ID" => 3,
            "PRICE_DELIVERY" => 11.37,
            "DELIVERY_ID" => 2,
            "DISCOUNT_VALUE" => 1.5,
            "TAX_VALUE" => 0.0,
            "USER_DESCRIPTION" => ""
        );

        if (CModule::IncludeModule("statistic"))
            $arFields["STAT_GID"] = CStatistic::GetEventParam();

        $ORDER_ID = CSaleOrder::Add($arFields);
        $ORDER_ID = IntVal($ORDER_ID);

        break;


    case 'videos': // 21. /videos
        itsferaRest::authByToken($_SERVER['X-Api-Token']);
        CModule::IncludeModule("iblock");

        $arSelect = Array("ID", "NAME", "PROPERTY_SHORT_URL");
        $arFilter = Array("IBLOCK_ID" => getIBlockIdByCode("videos"), "ACTIVE" => "Y");
        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();

            $data[] = array(
                "name" => $arFields['NAME'],
                "video_url" => "https://www.youtube.com/watch?v=" . $arFields['PROPERTY_SHORT_URL_VALUE'],
            );
        }
        $RESULT['erroe_code'] = 0;
        $RESULT['data'] = $data;

        break;


    case 'recall': // 22. /recall
        itsferaRest::authByToken($_SERVER['X-Api-Token']);
        $_REQUEST['user']['phone'];
        //TODO чо делаем то?
        $RESULT['erroe_code'] = 0;

        break;


    case 'articles': // 23. /articles
        itsferaRest::authByToken($_SERVER['X-Api-Token']);
        CModule::IncludeModule("iblock");

        $arSelect = Array("ID", "NAME", "PREVIEW_TEXT", "DETAIL_TEXT", "DATE_CREATE_UNIX", "ACTIVE_FROM");
        $arFilter = Array("IBLOCK_ID" => getIBlockIdByCode("statii"), "ACTIVE" => "Y");
        $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();

            $data[] = array(
                "article_name" => $arFields['NAME'],
                "article_date" => $arFields['ACTIVE_FROM'] ? MakeTimeStamp($arFields['ACTIVE_FROM'], "DD.MM.YYYY HH:MI:SS") : $arFields['DATE_CREATE_UNIX'],
                "article_body" => $arFields['PREVIEW_TEXT'],
                "article_body_full" => $arFields['DETAIL_TEXT'],
            );
        }
        $RESULT['erroe_code'] = 0;
        $RESULT['data'] = $data;
        break;


    case 'objects_filter': // 24. /objects
        itsferaRest::authByToken($_SERVER['X-Api-Token']);
        CModule::IncludeModule("iblock");
        CModule::IncludeModule("catalog");
        CModule::IncludeModule("price");

        if($_REQUEST['filter']['sort_type'] == 'priceup'){
            $sort = "CATALOG_PRICE_SCALE_0";
            $order = "ASC";
        }
        elseif($_REQUEST['filter']['sort_type'] == 'pricedown'){
            $sort = "CATALOG_PRICE_SCALE_0";
            $order = "DESC";
        }
        elseif($_REQUEST['filter']['sort_type'] == 'popular'){
            $sort = "show_counter";
            $order = "DESC";
        }
        elseif($_REQUEST['filter']['sort_type'] == 'name'){
            $sort = "NAME";
            $order = "ASC";
        }
        $arSelect = Array("ID", "NAME", "PREVIEW_TEXT", "DETAIL_PICTURE", "PROPERTY_CML2_ARTICLE", "PROPERTY_vote_count", "PROPERTY_rating");
        $arFilter = Array("IBLOCK_TYPE" => "mht_products", "ACTIVE"=>"Y");
        if($_REQUEST['filter']['color_id'])
            $arFilter["PROPERTY_TSVET"] = $_REQUEST['filter']['color_id'];
        if($_REQUEST['filter']['manufacturer_id'])
            $arFilter["PROPERTY_CML2_MANUFACTURER"] = $_REQUEST['filter']['manufacturer_id'];
        if($_REQUEST['filter']['material_id'])
            $arFilter[] = array(
                "LOGIC" => "OR",
                array("PROPERTY_MATERIALOBSHCHIY" => $_REQUEST['filter']['material_id']),
                array("PROPERTY_MATERIAL" => $_REQUEST['filter']['material_id']),
            );
        $res = CIBlockElement::GetList(Array($sort,$order), $arFilter, false, Array("nPageSize"=>50, "iNumPage"=>$_REQUEST['page']?$_REQUEST['page']:1), $arSelect);
        while($ob = $res->GetNextElement())
        {
            $arFields = $ob->GetFields();
            $PHOTOS = array();
            if($arFields['DETAIL_PICTURE'])
                $PHOTOS[] = "https://moshoztorg.ru".cfile::getpath($arFields['DETAIL_PICTURE']);
            $resProp = CIBlockElement::GetProperty($arFields['IBLOCK_ID'], $arFields['ID'], "sort", "asc", array("CODE" => "MORE_PHOTOS"));
            while ($obProp = $resProp->GetNext())
                $PHOTOS[] = "https://moshoztorg.ru".cfile::getpath($obProp['VALUE']);
            $arPrice = CCatalogProduct::GetOptimalPrice($arFields['ID'], 1, CUSER::GetUserGroupArray(), "N");
            $data[] = array(
                'object_id' => $arFields['ID'],
                'name' => $arFields['NAME'],
                'desc' => $arFields['PREVIEW_TEXT'],
                'price' => $arPrice['DISCOUNT_PRICE'],
                'code' => $arFields['CML2_ARTICLE'],
                'vendor_code' => $arFields['ID'], // @TODO коды откуда?
                'picture' => $PHOTOS,
                'rate_count' => $arFields['PROPERTY_VOTE_COUNT_VALUE'],
                'rate_average' => $arFields['PROPERTY_RATING_VALUE']
            );
        }
        if (intval($res->SelectedRowsCount()) > 0){
            $RESULT['erroe_code'] = 0;
            $RESULT['data'] = $data;
        }else{
            $RESULT['erroe_code'] = 1;
            $RESULT['erroe_desc'] = "Не удалось найти товаров.";
        }

        break;









}


if($json)
    echo json_encode($RESULT);

//require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");