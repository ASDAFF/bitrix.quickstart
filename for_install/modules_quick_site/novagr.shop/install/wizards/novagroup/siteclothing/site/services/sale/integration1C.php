<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

if (!defined("WIZARD_SITE_ID"))
    return;

if (!CModule::IncludeModule("iblock"))
    return;

if (!CModule::IncludeModule("catalog"))
    return;

if (!CModule::IncludeModule("sale"))
    return;

//Показывать код загрузки из внешних источников
COption::SetOptionString("iblock", "show_xml_id", "Y");

//Настаиваем параметры интеграции с 1С
COption::SetOptionString("catalog", "1C_IBLOCK_TYPE", "catalog");
COption::SetOptionString("catalog", "1C_ELEMENT_ACTION", "N");
COption::SetOptionString("catalog", "1C_SECTION_ACTION", "N");
COption::SetOptionString("catalog", "1C_USE_OFFERS", "Y");
COption::SetOptionString("catalog", "1C_TRANSLIT_ON_ADD", "Y");
COption::SetOptionString("catalog", "1C_TRANSLIT_ON_UPDATE", "Y");
COption::SetOptionString("catalog", "1C_GENERATE_PREVIEW", "N");
COption::SetOptionString("catalog", "1C_DETAIL_RESIZE", "N");
COption::SetOptionString("catalog", "1C_USE_CRC", "N");
COption::SetOptionString('catalog', 'default_quantity_trace', "Y");
COption::SetOptionString("sale", "subscribe_prod", serialize(array(WIZARD_SITE_ID => array("use" => "Y", "del_after" => "30"))));
$SERVER_NAME = COption::GetOptionString("main", "server_name", $GLOBALS["SERVER_NAME"]);
COption::SetOptionString("sale", "order_email", "order@" . $SERVER_NAME);

//Начальные значения
$productID = $productsOffersID = $exchangeGroupID = 0;

//Даем необходимые права группе 1cexchange на модуль Торговый каталог (Управление импортом)
$rsGroups = CGroup::GetList($by = "c_sort", $order = "desc", array("NAME" => "1cexchange"));
if ($ar_result = $rsGroups->Fetch()) {
    $exchangeGroupID = $ar_result['ID'];

    $setGroupAccess = 'U';
    $id = CTask::GetIdByLetter($setGroupAccess, 'catalog');
    if ($id < 1) {
        $setGroupAccess = 'W';
        $id = CTask::GetIdByLetter($setGroupAccess, 'catalog');
    }
    $yasks = CGroup::GetTasks($setGroupAccess);
    $yasks['catalog'] = $id;
    CGroup::SetTasks($exchangeGroupID, $yasks);
    $APPLICATION->SetGroupRight('catalog', $exchangeGroupID, $setGroupAccess, false);
}

//Тип цены
$dbPriceType = CCatalogGroup::GetList(array(), array("BASE" => "Y"));
if ($arPriceType = $dbPriceType->Fetch()) {
    //Обновляем тип базовой цены, если существует
    $arFields = array(
        "NAME" => GetMessage('CATALOG_BASE_GROUP_NAME'),
        "XML_ID" => "54be187f-c151-11e2-b9a2-005056c00008",
    );

    //получаем группы, которые могут смотреть/покупать товары
    $BUY = $VIEW = array();
    $db_res = CCatalogGroup::GetGroupsList(array("CATALOG_GROUP_ID" => $arPriceType["ID"]));
    while ($ar_res = $db_res->Fetch()) {
        if ($ar_res["BUY"] == "Y") {
            $BUY[$ar_res['GROUP_ID']] = $ar_res['GROUP_ID'];
        } else {
            $VIEW[$ar_res['GROUP_ID']] = $ar_res['GROUP_ID'];
        }
    }
    $BUY[$exchangeGroupID] = $exchangeGroupID;
    $VIEW[$exchangeGroupID] = $exchangeGroupID;

    //устанавливаем группы, которые могут смотреть/покупать товары
    $arFields['USER_GROUP'] = array_keys($VIEW);
    $arFields['USER_GROUP_BUY'] = array_keys($BUY);

    CCatalogGroup::Update($arPriceType['ID'], $arFields);
}

//Внешний код - идентификатор каталога товаров из 1С
$res = CIBlock::GetList(Array(), Array('TYPE' => 'catalog', 'SITE_ID' => WIZARD_SITE_ID, "CODE" => 'novagr_standard_products'));
if ($ar_res = $res->Fetch()) {
    $arFields = Array(
        "XML_ID" => "543a86e6-0485-40d1-a3ae-73cc0ebe5d25"
    );
    if ($exchangeGroupID > 0) {
        $GetPermission = CIBlock::GetGroupPermissions($ar_res['ID']);
        $GetPermission[$exchangeGroupID] = "W";
        $arFields['GROUP_ID'] = $GetPermission;
    }

    $ib = new CIBlock;
    $res = $ib->Update($ar_res['ID'], $arFields);
    $productsID = $ar_res['ID'];


    $res = CIBlockProperty::GetByID("CML2_ARTICLE", $productsID, "novagr_standard_products");
    if ($ar_res = $res->Fetch()) {
        $arFields = $ar_res;
        $arFields['XML_ID'] = 'CML2_ARTICLE';
        $ibp = new CIBlockProperty;
        $ibp->Update($ar_res['ID'], $arFields);
    }
}

//Внешний код - идентификатор торговых предложений из 1С
$res = CIBlock::GetList(Array(), Array('TYPE' => 'offers', 'SITE_ID' => WIZARD_SITE_ID, "CODE" => 'novagr_standard_products_offers'));
if ($ar_res = $res->Fetch()) {
    $arFields = Array(
        "XML_ID" => "543a86e6-0485-40d1-a3ae-73cc0ebe5d25#"
    );
    if ($exchangeGroupID > 0) {
        $GetPermission = CIBlock::GetGroupPermissions($ar_res['ID']);
        $GetPermission[$exchangeGroupID] = "W";
        $arFields['GROUP_ID'] = $GetPermission;
    }

    $ib = new CIBlock;
    $res = $ib->Update($ar_res['ID'], $arFields);
    $productsOffersID = $ar_res['ID'];
}

if ($productsID > 0) {
    //Внешний код - идентификатор Обуви из 1С
    $arFilter = Array('IBLOCK_ID' => $productsID, "NAME" => GetMessage('SECTION_SHOES'), "DEPTH_LEVEL" => "1");
    $db_list = CIBlockSection::GetList(Array($by => $order), $arFilter);
    if ($ar_result = $db_list->Fetch()) {
        $bs = new CIBlockSection;
        $arFields = Array(
            "XML_ID" => "b05a99ca-c127-11e2-b9a2-005056c00008"
        );
        $res = $bs->Update($ar_result['ID'], $arFields);
    }

    //Внешний код - идентификатор Женской Обуви из 1С
    $arFilter = Array('IBLOCK_ID' => $productsID, "NAME" => GetMessage('SECTION_WOMEN_SHOES'), "DEPTH_LEVEL" => "2");
    $db_list = CIBlockSection::GetList(Array($by => $order), $arFilter);
    if ($ar_result = $db_list->Fetch()) {
        $bs = new CIBlockSection;
        $arFields = Array(
            "XML_ID" => "b05a99cb-c127-11e2-b9a2-005056c00008"
        );
        $res = $bs->Update($ar_result['ID'], $arFields);
    }

    //Внешний код - идентификатор Балетки из 1С
    $arFilter = Array('IBLOCK_ID' => $productsID, "NAME" => GetMessage("SECTION_BALETKI"), "DEPTH_LEVEL" => "3");
    $db_list = CIBlockSection::GetList(Array($by => $order), $arFilter);
    if ($ar_result = $db_list->Fetch()) {
        $bs = new CIBlockSection;
        $arFields = Array(
            "XML_ID" => "b05a99cd-c127-11e2-b9a2-005056c00008"
        );
        $res = $bs->Update($ar_result['ID'], $arFields);
    }

    //Внешний код - идентификатор Ботинки из 1С
    $arFilter = Array('IBLOCK_ID' => $productsID, "NAME" => GetMessage("SECTION_BOTINKI"), "DEPTH_LEVEL" => "3");
    $db_list = CIBlockSection::GetList(Array($by => $order), $arFilter);
    if ($ar_result = $db_list->Fetch()) {
        $bs = new CIBlockSection;
        $arFields = Array(
            "XML_ID" => "b05a99ce-c127-11e2-b9a2-005056c00008"
        );
        $res = $bs->Update($ar_result['ID'], $arFields);
    }
}

if ($exchangeGroupID > 0) {
    //Проставляем права для группы 1cexchange на вкладках Каталог, Экспорт Каталога
    $tabs = array("1C_GROUP_PERMISSIONS", "1CE_GROUP_PERMISSIONS");
    foreach ($tabs as $tab) {
        $_1C_GROUP_PERMISSIONS = COption::GetOptionString("catalog", $tab);
        $extractPerm = explode(',', $_1C_GROUP_PERMISSIONS);
        $extractPerm[] = $exchangeGroupID;
        $newPerms = array_unique($extractPerm, SORT_NUMERIC);
        $_1C_GROUP_PERMISSIONS = implode(',', $newPerms);
        COption::SetOptionString("catalog", $tab, $_1C_GROUP_PERMISSIONS);
    }
    //Проставляем права для группы 1cexchange на вкладке  Заказы
    $tabs = array("1C_SALE_GROUP_PERMISSIONS");
    foreach ($tabs as $tab) {
        $_1C_GROUP_PERMISSIONS = COption::GetOptionString("sale", $tab);
        $extractPerm = explode(',', $_1C_GROUP_PERMISSIONS);
        $extractPerm[] = $exchangeGroupID;
        $newPerms = array_unique($extractPerm, SORT_NUMERIC);
        $_1C_GROUP_PERMISSIONS = implode(',', $newPerms);
        COption::SetOptionString("sale", $tab, $_1C_GROUP_PERMISSIONS);
    }
    $APPLICATION->SetFileAccessPermission("/bitrix/admin/1c_exchange.php", array($exchangeGroupID => "R"));
}

//настраиваем параметры профиля обмена 1С
$arExportProfile = $arUserProfile = array();
$dbExport = CSaleExport::GetList();
while ($arExport = $dbExport->Fetch()) {
    $arExportProfile[$arExport["PERSON_TYPE_ID"]] = $arExport["ID"];
}

$dbPersonType = CSalePersonType::GetList(
    array("SORT" => "ASC"),
    array("ACTIVE" => "Y")
);
while ($arPersonType = $dbPersonType->GetNext()) {
    if(in_array(WIZARD_SITE_ID, $arPersonType['LIDS'])){
        $arUserProfile[] = $arPersonType;
    }
}

for($i=0; $i<=1; $i++){


    $db_props = CSaleOrderProps::GetList(
        array("SORT" => "ASC"),
        array(
            "PERSON_TYPE_LID"=>WIZARD_SITE_ID,
            "PERSON_TYPE_ID"=>$arUserProfile[$i]["ID"]
        ),
        false,
        false,
        array()
    );

    while ($props = $db_props->Fetch())
    {
        switch($props['CODE']){
            case 'FIO':
                $props_FIO = $props['ID'];
                break;
            case 'EMAIL':
                $props_EMAIL = $props['ID'];
                break;
            case 'PHONE':
                $props_PHONE = $props['ID'];
                break;
            case 'ZIP':
                $props_ZIP = $props['ID'];
                break;
            case 'CITY':
                $props_CITY = $props['ID'];
                break;
            case 'LOCATION':
                $props_LOCATION = $props['ID'];
                break;
            case 'ADDRESS':
                $props_ADDRESS = $props['ID'];
                break;
            case 'COMPANY':
                $props_COMPANY = $props['ID'];
                break;
            case 'COMPANY_ADR':
                $props_COMPANY_ADR = $props['ID'];
                break;
            case 'INN':
                $props_INN = $props['ID'];
                break;
            case 'KPP':
                $props_KPP = $props['ID'];
                break;
            case 'CONTACT_PERSON':
                $props_CONTACT_PERSON = $props['ID'];
                break;
            case 'FAX':
                $props_FAX = $props['ID'];
                break;
        }
    }

    $VARS = array();
    $VARS[0] = Array(
        "AGENT_NAME" => Array("TYPE" => "PROPERTY", "VALUE" => $props_EMAIL),
        "FULL_NAME" => Array("TYPE" => "PROPERTY", "VALUE" => $props_FIO),
        "SURNAME" => Array("TYPE" => "USER", "VALUE" => "LAST_NAME"),
        "NAME" => Array("TYPE" => "USER", "VALUE" => "NAME"),
        "SECOND_NAME" => Array("TYPE" => "USER", "VALUE" => "SECOND_NAME"),
        "BIRTHDAY" => Array("TYPE" => "", "VALUE" => ""),
        "MALE" => Array("TYPE" => "", "VALUE" => ""),
        "INN" => Array("TYPE" => "", "VALUE" => ""),
        "KPP" => Array("TYPE" => "", "VALUE" => ""),
        "ADDRESS_FULL" => Array("TYPE" => "PROPERTY", "VALUE" => $props_ADDRESS),
        "INDEX" => Array("TYPE" => "PROPERTY", "VALUE" => $props_ZIP),
        "COUNTRY" => Array("TYPE" => "PROPERTY", "VALUE" => $props_LOCATION."_COUNTRY"),
        "REGION" => Array("TYPE" => "", "VALUE" => ""),
        "STATE" => Array("TYPE" => "", "VALUE" => ""),
        "TOWN" => Array("TYPE" => "", "VALUE" => ""),
        "CITY" => Array("TYPE" => "PROPERTY", "VALUE" => $props_LOCATION."_CITY"),
        "STREET" => Array("TYPE" => "PROPERTY", "VALUE" => $props_ADDRESS),
        "BUILDING" => Array("TYPE" => "", "VALUE" => ""),
        "HOUSE" => Array("TYPE" => "", "VALUE" => ""),
        "FLAT" => Array("TYPE" => "", "VALUE" => ""),
        "PHONE" => Array("TYPE" => "PROPERTY", "VALUE" => $props_PHONE),
        "EMAIL" => Array("TYPE" => "PROPERTY", "VALUE" => $props_EMAIL),
        "CONTACT_PERSON" => Array("TYPE" => "PROPERTY", "VALUE" => $props_FIO),
        "F_ADDRESS_FULL" => Array("TYPE" => "", "VALUE" => ""),
        "F_INDEX" => Array("TYPE" => "", "VALUE" => ""),
        "F_COUNTRY" => Array("TYPE" => "", "VALUE" => ""),
        "F_REGION" => Array("TYPE" => "", "VALUE" => ""),
        "F_STATE" => Array("TYPE" => "", "VALUE" => ""),
        "F_TOWN" => Array("TYPE" => "", "VALUE" => ""),
        "F_CITY" => Array("TYPE" => "", "VALUE" => ""),
        "F_STREET" => Array("TYPE" => "", "VALUE" => ""),
        "F_BUILDING" => Array("TYPE" => "", "VALUE" => ""),
        "F_HOUSE" => Array("TYPE" => "", "VALUE" => ""),
        "F_FLAT" => Array("TYPE" => "", "VALUE" => ""),
        "IS_FIZ" => "Y",
        "REKV_0" => Array("TYPE" => "ORDER", "VALUE" => "PAY_SYSTEM_ID", "NAME" => "1C_CODE_PSYSTEM"),
        "REKV_1" => Array("TYPE" => "USER", "VALUE" => "ID", "NAME" => "1C_USER_ID")
    );

    $VARS[1] = Array(
        "AGENT_NAME" => Array("TYPE" => "PROPERTY", "VALUE" => $props_COMPANY),
        "FULL_NAME" => Array("TYPE" => "PROPERTY", "VALUE" => $props_COMPANY),
        "ADDRESS_FULL" => Array("TYPE" => "PROPERTY", "VALUE" => $props_COMPANY_ADR),
        "INDEX" => Array("TYPE" => "", "VALUE" => ""),
        "COUNTRY" => Array("TYPE" => "PROPERTY", "VALUE" => $props_LOCATION."_COUNTRY"),
        "REGION" => Array("TYPE" => "", "VALUE" => ""),
        "STATE" => Array("TYPE" => "", "VALUE" => ""),
        "TOWN" => Array("TYPE" => "", "VALUE" => ""),
        "CITY" => Array("TYPE" => "PROPERTY", "VALUE" => $props_LOCATION."_COUNTRY"),
        "STREET" => Array("TYPE" => "PROPERTY", "VALUE" => $props_COMPANY_ADR),
        "BUILDING" => Array("TYPE" => "", "VALUE" => ""),
        "HOUSE" => Array("TYPE" => "", "VALUE" => ""),
        "FLAT" => Array("TYPE" => "", "VALUE" => ""),
        "INN" => Array("TYPE" => "PROPERTY", "VALUE" => $props_INN),
        "KPP" => Array("TYPE" => "PROPERTY", "VALUE" => $props_KPP),
        "EGRPO" => Array("TYPE" => "", "VALUE" => ""),
        "OKVED" => Array("TYPE" => "", "VALUE" => ""),
        "OKDP" => Array("TYPE" => "", "VALUE" => ""),
        "OKOPF" => Array("TYPE" => "", "VALUE" => ""),
        "OKFC" => Array("TYPE" => "", "VALUE" => ""),
        "OKPO" => Array("TYPE" => "", "VALUE" => ""),
        "ACCOUNT_NUMBER" => Array("TYPE" => "", "VALUE" => ""),
        "B_NAME" => Array("TYPE" => "", "VALUE" => ""),
        "B_BIK" => Array("TYPE" => "", "VALUE" => ""),
        "B_ADDRESS_FULL" => Array("TYPE" => "", "VALUE" => ""),
        "B_INDEX" => Array("TYPE" => "", "VALUE" => ""),
        "B_COUNTRY" => Array("TYPE" => "", "VALUE" => ""),
        "B_REGION" => Array("TYPE" => "", "VALUE" => ""),
        "B_STATE" => Array("TYPE" => "", "VALUE" => ""),
        "B_TOWN" => Array("TYPE" => "", "VALUE" => ""),
        "B_CITY" => Array("TYPE" => "", "VALUE" => ""),
        "B_STREET" => Array("TYPE" => "", "VALUE" => ""),
        "B_BUILDING" => Array("TYPE" => "", "VALUE" => ""),
        "B_HOUSE" => Array("TYPE" => "", "VALUE" => ""),
        "PHONE" => Array("TYPE" => "PROPERTY", "VALUE" => $props_PHONE),
        "EMAIL" => Array("TYPE" => "PROPERTY", "VALUE" => $props_EMAIL),
        "CONTACT_PERSON" => Array("TYPE" => "PROPERTY", "VALUE" => $props_COMPANY),
        "F_ADDRESS_FULL" => Array("TYPE" => "PROPERTY", "VALUE" => $props_ADDRESS),
        "F_INDEX" => Array("TYPE" => "PROPERTY", "VALUE" => $props_ZIP),
        "F_COUNTRY" => Array("TYPE" => "PROPERTY", "VALUE" => $props_LOCATION."_COUNTRY"),
        "F_REGION" => Array("TYPE" => "", "VALUE" => ""),
        "F_STATE" => Array("TYPE" => "", "VALUE" => ""),
        "F_TOWN" => Array("TYPE" => "", "VALUE" => ""),
        "F_CITY" => Array("TYPE" => "PROPERTY", "VALUE" => $props_LOCATION."_CITY"),
        "F_STREET" => Array("TYPE" => "PROPERTY", "VALUE" => $props_ADDRESS),
        "F_BUILDING" => Array("TYPE" => "", "VALUE" => ""),
        "F_HOUSE" => Array("TYPE" => "", "VALUE" => ""),
        "F_FLAT" => Array("TYPE" => "", "VALUE" => ""),
        "IS_FIZ" => "N",
        "REKV_0" => Array("TYPE" => "ORDER", "VALUE" => "PAY_SYSTEM_ID", "NAME" => "1C_CODE_PSYSTEM"),
        "REKV_1" => Array("TYPE" => "USER", "VALUE" => "ID", "NAME" => "1C_USER_ID"
        )
    );

    if(isset($arUserProfile[$i]))
    {
        $res = CSaleExport::Update(
            $arUserProfile[$i]["ID"],
            Array("PERSON_TYPE_ID" => $arUserProfile[$i]["ID"],
                "VARS" => serialize($VARS[$i])
            )
        );
    } else {
        $res = CSaleExport::Add(
            Array("PERSON_TYPE_ID" => $arUserProfile[$i]["ID"],
                "VARS" => serialize($VARS[$i])
            )
        );
    }
}
