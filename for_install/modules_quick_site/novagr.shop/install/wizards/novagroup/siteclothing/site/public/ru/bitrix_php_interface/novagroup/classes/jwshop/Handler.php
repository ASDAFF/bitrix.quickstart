<?php
/**
 * Created by JetBrains PhpStorm.
 * User: anton
 * Date: 13.07.13
 * Time: 20:51
 * To change this template use File | Settings | File Templates.
 */

class Novagroup_Classes_General_Handler extends Novagroup_Classes_Abstract_Handler {

    function OnBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu)
    {
        parent::OnBuildGlobalMenu($aGlobalMenu, $aModuleMenu);
        global $APPLICATION;
        $APPLICATION->SetAdditionalCSS('/local/themes/.default/novagroup-jw.css');
    }

    function OnBasketAddHandler($ID, $arFields)
    {
        // get ib
        if(!CModule::IncludeModule("iblock")) return;
        global $USER;
        $userID = $USER->GetID();

        // update user field for smart site catalog
        if ($userID > 0) {
            $arProduct = CIBlockElement::GetByID($arFields["PRODUCT_ID"])->GetNext();
            Novagroup_Classes_General_Basket::updateSizesColorsUserField($userID, $arProduct["IBLOCK_ID"]);
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
                    "CODE" => 'products_offers'),
                false
            );
            if ($arRes = $res->Fetch())
            {
                Novagroup_Classes_General_Basket::updateSizesColorsUserField($userID, $arRes["ID"]);
            }
        }
    }


    function OnPanelCreateHandler()
    {
        if (CModule::IncludeModule('novagr.jwshop'))
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
        // access granted only for groups: administrators and sale_administrators
        if ($accessDenied == true) {

            return ;
        }

            __IncludeLang($_SERVER["DOCUMENT_ROOT"]."/local/php_interface/include/lang/ru/seo_element_edit.php");
        $APPLICATION->AddPanelButton(array(
            "HREF"      => "javascript:seoSettings.seoWindowOpen('".$_SERVER["REQUEST_URI"]."')",
            "SRC"       => NOVAGR_JSWSHOP_IMG_DIR."icon_big.png",
            "ALT"       => GetMessage("SEO_URLS_EDIT_LABEL"),
            "MAIN_SORT" => 700,
            "SORT"      => 10
        ));
        $APPLICATION->AddHeadString("<script src='/local/js/novagroup/seoForm.js'></script>");
        $APPLICATION->AddHeadString('<script>var messages = {"SAVE":"'.GetMessage("SEO_URLS_SAVE_LABEL").'", "SEO_URLS_EDIT_LABEL":"'.GetMessage("SEO_URLS_EDIT_LABEL").'", "SEO_URLS_ALERT_OLD_URL":"'.GetMessage("SEO_URLS_ALERT_OLD_URL").'", "SEO_URLS_CLOSE_LABEL":"'.GetMessage("SEO_URLS_CLOSE_LABEL").'", "SEO_URLS_UPDATE_LABEL":"'.GetMessage("SEO_URLS_UPDATE_LABEL").'", "SEO_URLS_ACCESS_DENIED":"'.GetMessage("SEO_URLS_ACCESS_DENIED").'", }; seoSettings.init(messages);</script>');
        }
    }

    function OnBeforeEventAddHandler(&$event, &$lid, &$arFields)
    {
        if ($event == 'SALE_NEW_ORDER') {

            CModule::IncludeModule('novagr.jwshop');
            $getVisibleProperty = NovagroupJewelryCart::getVisiblePropertyBySection('mail');
            $arSelectAdditional = array();
            if(is_array($getVisibleProperty))
            {
                foreach($getVisibleProperty as $item)
                {
                    $arSelectAdditional[$item['select']] = $item['select'] ;
                }
            }

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

                $arSelect = Array("ID", "NAME", "IBLOCK_ID", "PROPERTY_CML2_BAR_CODE") + $arSelectAdditional;
                $arFilter = Array("ID" => $arBasketItem['PRODUCT_ID']);
                $res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
                $PRODUCT_NAME = array();
                while ($ob = $res->GetNextElement()) {
                    $arFieldsElement = $ob->GetFields();
                    $PRODUCT_NAME[] = $arFieldsElement['NAME'];

                    if(is_array($getVisibleProperty))
                    {
                        foreach($getVisibleProperty as $key=>$item)
                        {
                            if (trim($arFieldsElement[$item['field']]) <> "") {
                                $resProperty = CIBlockProperty::GetByID($key, $arFieldsElement['IBLOCK_ID']);
                                $PROPERTY_NAME = ($ar_res = $resProperty->GetNext()) ? $ar_res['NAME'] . " " : "";
                                $PRODUCT_NAME[] = $PROPERTY_NAME . $arFieldsElement[$item['field']];
                            }
                        }
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

    function OnBeforeUserUpdateHandler(&$arFields) {
        // в полес с логином помещаем емэйл при регистрации
        $arFields["LOGIN"] = $arFields["EMAIL"];

    }
    
    function OnAfterIBlockElementUpdateHandler(&$arFields) {

        // обновление торговых предложений
        if ($arFields["IBLOCK_ID"] == 17 && $arFields["RESULT"] > 0) {
            CModule::IncludeModule("iblock");
            global $USER;
            $curUserId = $USER->GetID();

            //deb($arFields);
            $exchange1cGroup = 5; // Группа для 1С Обмена
            $sizesIblockId = 8; // ИБ Размеры
            $colorIblockId = 2; // ИБ Цвета
            // определяем, входит ли пользователь в группу для 1с обмена
            if ( !CSite::InGroup( array($exchange1cGroup) ) ) return;
            // при импорте каталога из 1С обрабатываем цвета и размеры тп

            foreach ($arFields["PROPERTY_VALUES"] as $key => $value) {

                if (is_array($value))
                    foreach ($value as $key2 => $item) {
                        if ($item["DESCRIPTION"] == "Размер" && !empty($item["VALUE"])) {

                            // определяем есть ли такой размер в справочнике размеров

                            $nameSize = $item["VALUE"];
                            $arFilter = array('IBLOCK_ID' => $sizesIblockId, "NAME" => $nameSize);
                            $arSelect = array('ID', 'NAME');
                            $rsElement = CIBlockElement::GetList(array('NAME'), $arFilter, false, false, $arSelect);
                            if ($arElement = $rsElement -> Fetch())
                            {
                                //deb($arElement);
                                $sizeId = $arElement["ID"];
                            } else {
                                // создаем размер
                                $el = new CIBlockElement;

                                $arLoad = Array(
                                    "MODIFIED_BY"    => $curUserId, // элемент изменен текущим пользователем
                                    "IBLOCK_ID"      => $sizesIblockId,
                                    "IBLOCK_SECTION" => false,          // элемент лежит в корне разде
                                    "NAME" => $nameSize,
                                    "ACTIVE" => "Y"            // активен
                                );

                                if ($sizeId = $el->Add($arLoad)) {
                                    //echo "New ID: ".$sizeId;
                                }

                            }
                            // привязываем найденный размер к нужному предложению
                            if ($sizeId>0) {

                                $resSaveProp = CIBlockElement::SetPropertyValuesEx($arFields["ID"], false, array("STD_SIZE"=>$sizeId));

                            }
                        } // end if ($item["DESCRIPTION"] == "Размер") {
                        if ($item["DESCRIPTION"] == "Цвет" && !empty($item["VALUE"])) {

                            // определяем есть ли такой цвет в справочнике цветов

                            $nameColor = $item["VALUE"];
                            $arFilter = array('IBLOCK_ID' => $colorIblockId, "NAME" => $nameColor);
                            $arSelect = array('ID', 'NAME');
                            $rsElement = CIBlockElement::GetList(array('NAME'), $arFilter, false, false, $arSelect);
                            if ($arElement = $rsElement -> Fetch())
                            {
                                //deb($arElement);
                                $colorId = $arElement["ID"];
                            } else {
                                // создаем цвет
                                $el = new CIBlockElement;

                                $arLoad = Array(
                                    "MODIFIED_BY"    => $curUserId, // элемент изменен текущим пользователем
                                    "IBLOCK_ID"      => $colorIblockId,
                                    "IBLOCK_SECTION" => false,          // элемент лежит в корне разде
                                    "NAME" => $nameColor,
                                	"PREVIEW_PICTURE" => CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].SITE_DIR."include/images/color-miss.jpg"),
                                    "ACTIVE" => "Y"            // активен
                                );

                                if ($colorId = $el->Add($arLoad)) {

                                }

                            }
                            // привязываем найденный цвет к нужному предложению
                            if ($colorId>0) {

                                $resSaveProp = CIBlockElement::SetPropertyValuesEx($arFields["ID"], false, array("COLOR_STONE"=>$colorId));

                            }

                        } // end if ($item["DESCRIPTION"] == "Цвет") {
                    }
            } // end foreach ($arFields["PROPERTY_VALUES"] as $key => $value) {

        } // end if ($arFields["IBLOCK_ID"] == 17 && $arFields["RESULT"] > 0) {

    }

    function OnAfterIBlockElementAddHandler(&$arFields) {

        // добавление торговых предложений
        if ($arFields["IBLOCK_ID"] == 17 && $arFields["RESULT"] > 0) {
            CModule::IncludeModule("iblock");
            global $USER;
            $curUserId = $USER->GetID();

            //deb($arFields);
            $exchange1cGroup = 5; // Группа для 1С Обмена
            $sizesIblockId = 8; // ИБ Размеры
            $colorIblockId = 2; // ИБ Цвета
            // определяем, входит ли пользователь в группу для 1с обмена
            /*if (user1c($curUserId, $exchange1cGroup) == false) {
                return;
            }*/
            if ( !CSite::InGroup( array($exchange1cGroup) ) ) return;

            // при импорте каталога из 1С обрабатываем цвета и размеры тп

            foreach ($arFields["PROPERTY_VALUES"] as $key => $value) {

                if (is_array($value))
                    foreach ($value as $key2 => $item) {

                        if ($item["DESCRIPTION"] == "Размер" && !empty($item["VALUE"])) {

                            //deb($item["VALUE"]);
                            // определяем есть ли такой размер в справочнике размеров

                            $nameSize = $item["VALUE"];
                            $arFilter = array('IBLOCK_ID' => $sizesIblockId, "NAME" => $nameSize);
                            $arSelect = array('ID', 'NAME');
                            $rsElement = CIBlockElement::GetList(array('NAME'), $arFilter, false, false, $arSelect);
                            if ($arElement = $rsElement -> Fetch())
                            {
                                //deb($arElement);
                                $sizeId = $arElement["ID"];
                            } else {
                                // создаем размер
                                $el = new CIBlockElement;

                                $arLoad = Array(
                                    "MODIFIED_BY"    => $curUserId, // элемент изменен текущим пользователем
                                    "IBLOCK_ID"      => $sizesIblockId,
                                    "IBLOCK_SECTION" => false,          // элемент лежит в корне разде
                                    "NAME" => $nameSize,
                                    "ACTIVE" => "Y"            // активен
                                );

                                if ($sizeId = $el->Add($arLoad)) {
                                    //echo "New ID: ".$sizeId;
                                }

                            }
                            // привязываем найденный размер к нужному предложению
                            if ($sizeId>0) {

                                $resSaveProp = CIBlockElement::SetPropertyValuesEx($arFields["ID"], false, array("STD_SIZE"=>$sizeId));

                            }

                        } // end if ($item["DESCRIPTION"] == "Размер") {
                        if ($item["DESCRIPTION"] == "Цвет" && !empty($item["VALUE"])) {

                            // определяем есть ли такой цвет в справочнике цветов

                            $nameColor = $item["VALUE"];
                            $arFilter = array('IBLOCK_ID' => $colorIblockId, "NAME" => $nameColor);
                            $arSelect = array('ID', 'NAME');
                            $rsElement = CIBlockElement::GetList(array('NAME'), $arFilter, false, false, $arSelect);
                            if ($arElement = $rsElement -> Fetch())
                            {
                                //deb($arElement);
                                $colorId = $arElement["ID"];
                            } else {
                                // создаем цвет
                                $el = new CIBlockElement;

                                $arLoad = Array(
                                    "MODIFIED_BY"    => $curUserId, // элемент изменен текущим пользователем
                                    "IBLOCK_ID"      => $colorIblockId,
                                    "IBLOCK_SECTION" => false,          // элемент лежит в корне разде
                                    "NAME" => $nameColor,
                                	"PREVIEW_PICTURE" => CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].SITE_DIR."include/images/color-miss.jpg"),
                                    "ACTIVE" => "Y"            // активен
                                );

                                if ($colorId = $el->Add($arLoad)) {

                                }

                            }
                            // привязываем найденный цвет к нужному предложению
                            if ($colorId>0) {

                                $resSaveProp = CIBlockElement::SetPropertyValuesEx($arFields["ID"], false, array("COLOR_STONE"=>$colorId));

                            }
                        }
                    }
            } // end foreach ($arFields["PROPERTY_VALUES"] as $key => $value) {

        } // end if ($arFields["IBLOCK_ID"] == 17) {


        // если добавили новость или блог отправляем рассылку
        if ($arFields["IBLOCK_ID"] == 12 && CModule::IncludeModule( "subscribe" ) ){
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

        } elseif ($arFields["IBLOCK_ID"] == 10 && CModule::IncludeModule( "subscribe" ) ){
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

    function OnBeforePostAddHandler(&$arFields) {
        // если добавлен пост в блог Гид в мире моды - создаем выпуск рассылки


    }

}