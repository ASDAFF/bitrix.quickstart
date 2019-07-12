<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

//проверим наличие инфоблока товаров
$searchArray = $arFormAction = array();
if (GetIBlock($arParams["CATALOG_IBLOCK_ID"])) {
    $searchArray["catalog"] = $arParams['CATALOG_IBLOCK_NAME'];// GetMessage("CT_PRODUCT_SELECT_VALUE");
    $arFormAction["catalog"] = $arParams['CATALOG_IBLOCK_PATH'];
}
//проверим наличие инфоблока образов
if (GetIBlock($arParams["FASHION_IBLOCK_ID"])) {
    $searchArray["fashion"] = $arParams['FASHION_IBLOCK_NAME'];
    $arFormAction["fashion"] = $arParams['FASHION_IBLOCK_PATH'];
}
$arResult['SEARCH_ARRAY'] = $searchArray;
$arResult['FORM_ACTION'] = $arFormAction;

//если не найден ни один инфоблок
if (count($searchArray) < 1) return false;

//провер€ем секцию, в которой будет поиск (товары или образы)
$arParams['SEARCH_WHERE'] = $_REQUEST['SEARCH_WHERE'] = (isset($_REQUEST['SEARCH_WHERE']) and array_key_exists($_REQUEST['SEARCH_WHERE'], $searchArray)) ?
    $_REQUEST['SEARCH_WHERE'] : key($searchArray);


if (isset($arParams['QUERY']) and trim($arParams['QUERY']) <> "" and CModule::IncludeModule('search')) {

    //подготавливаем запрос (пробелы, кодировка)
    $query = trim($arParams['QUERY']);
    CUtil::decodeURIComponent($query);

    //конвертируем запрос, если он был набран в неверной ракладке
	$arResult["alt_query"] = "";
	if($arParams["USE_LANGUAGE_GUESS"] !== "N")
	{
		$arLang = CSearchLanguage::GuessLanguage($query);
		if(is_array($arLang) && $arLang["from"] != $arLang["to"] and $arLang["to"]=="ru")
			$arResult["alt_query"] = CSearchLanguage::ConvertKeyboardLayout($query, $arLang["from"], $arLang["to"]);
	}
    $arResult["query"] = $arResult["alt_query"]? $arResult["alt_query"]: $query;
	
    //обрабатываем запрос дл€ товаров
    if ($_REQUEST['SEARCH_WHERE'] == 'catalog') {
        $search = new Novagroup_Classes_General_Search($arResult["query"]);
        $arElementsSearch = $search->searchByIblock("catalog", $arParams['CATALOG_IBLOCK_ID'])->getPrepareArray();

        if (count($arElementsSearch) > 0) {
            $generalCatalogOffers = new Novagroup_Classes_General_CatalogOffers($arParams['CATALOG_IBLOCK_ID'], $arParams['OFFERS_IBLOCK_ID']);
            $generalCatalogOffers->addOffersFilter(array(0 => array('PROPERTY_CML2_LINK' => $arElementsSearch)));
            $generalCatalogOffers->setItemsOnPage(3);
            $generalCatalogOffers->setCurrentPage(1);
            $arResult["ITEMS"] = $generalCatalogOffers->getElementList();
            $arResult["QUERY_RESULT"] = $generalCatalogOffers->getLastResult();
            $arResult["PHOTO_ID"] = $search->getColorIDFromQuery();
        }
    }

    //обрабатываем запрос дл€ образов
    if ($_REQUEST['SEARCH_WHERE'] == 'fashion') {
        $search = new Novagroup_Classes_General_Search($arResult["query"]);
        $arElementsSearch = $search->searchByIblock("catalog", $arParams['FASHION_IBLOCK_ID'])->getPrepareArray();

        if (count($arElementsSearch) > 0) {
            $arFilter = Array(
                "IBLOCK_ID"=>IntVal($arParams['FASHION_IBLOCK_ID']),
                "ID"=>$arElementsSearch
            );
            $arNavStartParams = array(
                'nPageSize' => 3,
                'iNumPage' => 1
            );
            $iBlock = new Novagroup_Classes_General_IBlock();
            $arResult["ITEMS"] = $iBlock->getElementList(array(),$arFilter,false,$arNavStartParams);
            $arResult["QUERY_RESULT"] = $iBlock->getLastResult();
        }
    }
}

//формируем ссылку "¬се результаты"
$params = array(
    "q" => $arResult["query"],
    "SEARCH_WHERE" => $arParams['SEARCH_WHERE']
);
$url = CHTTP::urlAddParams(
    $arResult['FORM_ACTION'][$arParams['SEARCH_WHERE']]
    , $params
    , array("encode" => true)
);
$arResult["GET_ALL_RESULTS"] = array(
    "NAME" => GetMessage("CC_BST_ALL_RESULTS"),
    "URL" => $url,
);
$arResult["NOT_FOUND"] = GetMessage("NOT_FOUND");

//подключаем соотвествующий шаблон
if ($_REQUEST["ajax_call"] === "y" && (!isset($_REQUEST["INPUT_ID"]) || $_REQUEST["INPUT_ID"] == $arParams["INPUT_ID"])) {
    $APPLICATION->RestartBuffer();
    if (count($arResult["ITEMS"]) > 0)
	if (!empty($arResult["query"])) $this->IncludeComponentTemplate('ajax');
    require_once($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/include/epilog_after.php");
    die();
} else {
    $APPLICATION->AddHeadScript($this->GetPath() . '/script.js');
    CUtil::InitJSCore(array('ajax'));
    $this->IncludeComponentTemplate();
}