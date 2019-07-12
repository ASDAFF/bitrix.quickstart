<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
//deb($_REQUEST);

if ($_REQUEST["elemID"]>0 && !empty($_REQUEST["code"])) {
    CModule::IncludeModule('iblock');
    global $USER;

    $arrayGroupCanEdit = array(1);
    $editGroup = GetGroupByCode ("content_editor");
    $arrayGroupCanEdit[] = $editGroup["ID"];

    $arUserGroups = $USER->GetUserGroupArray();

    if (count(array_intersect($arUserGroups, $arrayGroupCanEdit))>0) {

    } else {

        $arResult['result'] = 'ERROR';
        $arResult['message'] = "ACCESS DENIED";
        $arResultJson = json_encode($arResult);
        die($arResultJson);
    }

    $res = CIBlock::GetList(
        Array(),
        Array(
            'SITE_ID'=>SITE_ID,
            'CODE'=> array("novagr_standard_products", "novagr_lite_products", "LandingPages")),
        false
    );
    $catalogIDfound = false;
    while ($arRes = $res->Fetch())
    {
        if ($arRes["CODE"] == "LandingPages") {
            $iblockIDLanding = $arRes['ID'];
        }
        if ($arRes["CODE"] == "novagr_standard_products") {
            $iblockIDCatalog = $arRes['ID'];
            $catalogIDfound = true;
        }
        if ($arRes["CODE"] == "novagr_lite_products" && $catalogIDfound == false) {
            $iblockIDCatalog = $arRes['ID'];
        }
    }
    if (!$iblockIDLanding || !$iblockIDCatalog) return;

    $arFilt = Array("IBLOCK_ID" => $iblockIDCatalog, "CODE" => $_REQUEST["code"]);

    $arSelect = array(
        'ID',
        'NAME',
        'PROPERTY_TITLE',
        'PROPERTY_KEYWORDS',
        "PROPERTY_META_DESCRIPTION"
    );

    $rsElements = CIBlockElement::GetList(array('ID' => "DESC"), $arFilt, false, false, $arSelect);
    //$arResult["count"] = $count = $rsElements->SelectedRowsCount();

    if ($arElem = $rsElements -> Fetch())
    {

    } else {

        return;
    }

    $arFiltLanding = Array("IBLOCK_ID" => $iblockIDLanding, "PROPERTY_PRODUCT_ID" => $_REQUEST["elemID"]);

    $arSelectLanding = array('ID', 'NAME');

    $rsElements = CIBlockElement::GetList(array('ID' => "DESC"), $arFiltLanding, false, false, $arSelectLanding);
    if ($arElemLanding = $rsElements -> Fetch())
    {
        $arResult['iblockID'] = $iblockIDLanding;
        $arResult['elemID'] = $arElemLanding["ID"];
        $arResult['result'] = 'OK';

    } else {
        $el = new CIBlockElement;
        $arLoad = array();

        $PROP = array(
            "PRODUCT_ID" => array($_REQUEST["elemID"]),
            "TITLE" => array($arElem["PROPERTY_TITLE_VALUE"]),
            "DESCRIPTION" => array($arElem["PROPERTY_META_DESCRIPTION_VALUE"]),
            "KEYWORDS" => array($arElem["PROPERTY_KEYWORDS_VALUE"])
        );

        $arLoad ["NAME"] = $arElem["NAME"];
        $arLoad ["CODE"] = $_REQUEST["code"];
        $arLoad ["ACTIVE"] = "Y";
        $arLoad ["IBLOCK_ID"] = $iblockIDLanding;
        $arLoad ["IBLOCK_SECTION"] = false;
        $arLoad ["PROPERTY_VALUES"] = $PROP;

        if ($newElemId = $el->Add($arLoad)) {
            //echo "New ID: ".$newElemId;

            $arResult['iblockID'] = $iblockIDLanding;
            $arResult['elemID'] = $newElemId;
            $arResult['result'] = 'OK';
        }
        else {
            $arResult['result'] = 'ERROR';
            $arResult['message'] =  $el->LAST_ERROR;
            //deb( $el->LAST_ERROR);
            //deb($arLoad);
        }

    }

    $arResultJson = json_encode($arResult);
    die($arResultJson);

}