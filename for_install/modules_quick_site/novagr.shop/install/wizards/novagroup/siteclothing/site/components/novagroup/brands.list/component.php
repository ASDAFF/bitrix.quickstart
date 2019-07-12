<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (!$arParams['SORT_FIELD']) $arParams['SORT_FIELD'] = "SORT";

if (!$arParams['SORT_BY']) $arParams['SORT_BY'] = "ASC";

if (!$arParams['COUNT_RECORDS']) $arParams['COUNT_RECORDS'] = "5";

/**
 * @var CBitrixComponent $this
 */

$arResult['LETTER'] = mb_substr($_REQUEST['let'], 0, 1);
$arResult['LAT_ABC'] = range('A', 'Z');
$arResult['RUS_ABC'] = array(GetMessage("NOVAGR_SHOP_A"), GetMessage("NOVAGR_SHOP_B"), GetMessage("NOVAGR_SHOP_V"), GetMessage("NOVAGR_SHOP_G"), GetMessage("NOVAGR_SHOP_D"), GetMessage("NOVAGR_SHOP_E"), GetMessage("NOVAGR_SHOP_J"), GetMessage("NOVAGR_SHOP_Z"), GetMessage("NOVAGR_SHOP_I"), GetMessage("NOVAGR_SHOP_K"), GetMessage("NOVAGR_SHOP_L"), GetMessage("NOVAGR_SHOP_M"), GetMessage("NOVAGR_SHOP_N"), GetMessage("NOVAGR_SHOP_O"), GetMessage("NOVAGR_SHOP_P"), GetMessage("NOVAGR_SHOP_R"), GetMessage("NOVAGR_SHOP_S"), GetMessage("NOVAGR_SHOP_T"), GetMessage("NOVAGR_SHOP_U"), GetMessage("NOVAGR_SHOP_F"), GetMessage("NOVAGR_SHOP_H"), GetMessage("NOVAGR_SHOP_C"), GetMessage("NOVAGR_SHOP_C1"), GetMessage("NOVAGR_SHOP_S1"), GetMessage("NOVAGR_SHOP_S2"), GetMessage("NOVAGR_SHOP_E1"), GetMessage("NOVAGR_SHOP_U1"), GetMessage("NOVAGR_SHOP_A1"));

if (CModule::IncludeModule("iblock")) {
    global $USER;
    $bUSER_HAVE_ACCESS = !$arParams["USE_PERMISSIONS"];
    if ($arParams["USE_PERMISSIONS"] && isset($GLOBALS["USER"]) && is_object($GLOBALS["USER"])) {
        $arUserGroupArray = $USER->GetUserGroupArray();
        foreach ($arParams["GROUP_PERMISSIONS"] as $PERM) {
            if (in_array($PERM, $arUserGroupArray)) {
                $bUSER_HAVE_ACCESS = true;
                break;
            }
        }
    }
    $arFilter = array("IBLOCK_CODE" => $arParams['BRANDS_IBLOCK_CODE']);
    if (!empty($arResult['LETTER'])) {
        $arFilter['NAME'] = $arResult['LETTER'] . '%';
        $APPLICATION->AddChainItem(GetMessage("SIMBOL_LABEL") . " '" . $arResult['LETTER'] . "'", "");
    }
    $arNavParams = array(
        "nPageSize" => $arParams["COUNT_RECORDS"],
        "bShowAll" => false
    );
    $arNavigation = CDBResult::GetNavParams($arNavParams);
    $addCacheID =  array(($arParams["CACHE_GROUPS"] === "N" ? false : $USER->GetGroups()), $bUSER_HAVE_ACCESS, $arNavigation, $arFilter);
    if ($this->StartResultCache(false, $addCacheID )) {
        $arSelect = array(
            'ID',
            'NAME',
            'CODE',
            'PREVIEW_PICTURE',
            'DETAIL_TEXT',
            'DETAIL_PAGE_URL'
        );
        $rsElement = CIBlockElement::GetList(
            array($arParams['SORT_FIELD'] => $arParams['SORT_BY']),
            $arFilter,
            false,
            $arNavParams,
            $arSelect
        );
        while ($data = $rsElement->GetNext()) {
            $arResult['BRANDS'][$data['ID']] = $data;
        }
        $arResult["NAV_STRING"] = $rsElement->GetPageNavStringEx($navComponentObject, "", "bootstrap");
        $arResult["NAV_CACHED_DATA"] = $navComponentObject->GetTemplateCachedData();
        $arResult["NAV_RESULT"] = $rsElement;
        $this->SetResultCacheKeys(array(
            "NAV_CACHED_DATA",
        ));
        $this->IncludeComponentTemplate();
    }
    $this->SetTemplateCachedData($arResult["NAV_CACHED_DATA"]);
}
?>