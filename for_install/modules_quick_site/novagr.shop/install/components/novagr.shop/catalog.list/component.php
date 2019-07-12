<?
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

/**
 * @var CBitrixComponent $this
 */

global $USER, $CACHE_MANAGER;
if(!$USER->IsAdmin())
{
    ini_set('display_errors',0);
}

$currentUri = (isset($arParams['COMPONENT_CURRENT_PAGE']) and strlen($arParams['COMPONENT_CURRENT_PAGE'])>0) ? $arParams['COMPONENT_CURRENT_PAGE'] : $APPLICATION->GetCurPage(false);
$arResult['COMPONENT_CURRENT_PAGE'] = $currentUri;


if( !CModule::IncludeModule("iblock") ) exit;

if(!isset($arParams["CACHE_TIME"]))
	$arParams["CACHE_TIME"] = 36000000;

if ($arParams["SHOW_QUANTINY_NULL"] == "Y") {
	$showQuantityNull = true;
} else {
	$showQuantityNull = false;
}

$arUserGroups = $USER->GetUserGroupArray();
$arResult['OPT_USER'] = 0;
if (!empty($arParams["OPT_GROUP_ID"])) {
    if (in_array($arParams["OPT_GROUP_ID"], $arUserGroups)) {
        $arResult['OPT_USER'] = 1;
    }
}

$arElementsSearch = array();

if ($arParams["ROOT_PATH"] == $arResult['COMPONENT_CURRENT_PAGE'] && !isset($_REQUEST["q"]) &&!isset($_REQUEST['arFilter']))
{
    $arResult['CATALOG_ROOT'] = 1;
	
} else {
    $arResult['CATALOG_ROOT'] = 0;
    /*
    * инициализация начальных параметров
    */
    $generalCatalogOffers = new Novagroup_Classes_General_CatalogOffers(
        $arParams['CATALOG_IBLOCK_ID'], $arParams['OFFERS_IBLOCK_ID'], $showQuantityNull
    );
    $generalBrands = new Novagroup_Classes_General_Brands();

    $arParams['CATALOG_IBLOCK_CODE'] = $generalCatalogOffers->getCatalogIBlockCode();
    $arParams['BRAND_ROOT'] = $generalBrands->getCatalogPath();

    $generalCatalogOffers->setItemsOnPage($arParams['nPageSize']);
    $generalCatalogOffers->setCurrentPage($_REQUEST['iNumPage']);
    $generalCatalogOffers->addFilter($_REQUEST['arFilter']);
    $generalCatalogOffers->addFilter(array( 0 => array( "SECTION_CODE" => $_REQUEST['secid'] )));
    $generalCatalogOffers->addOffersFilter($_REQUEST['arOffer']);
    $generalCatalogOffers->setOrder(array($_REQUEST['orderRow']=>null));

    if ( !empty($_REQUEST['q']) )
    {
        $search = new Novagroup_Classes_General_Search($_REQUEST['q']);
        if( !empty($_REQUEST['arElementsSearch']) )
        {
            $arElementsSearch = $_REQUEST['arElementsSearch'];
        }
        else
        {
            $arElementsSearch = $search->searchByIblock($arParams['CATALOG_IBLOCK_TYPE'],$arParams['CATALOG_IBLOCK_ID'])->getPrepareArray();
        }
        $arParams["SEARCH_COLORS"] = $search->getColorsFromQuery();
        $generalCatalogOffers->addOffersFilter(array( 0 => array( 'PROPERTY_CML2_LINK' => $arElementsSearch ) ));
    }

    $arParams = $arParams + $generalCatalogOffers->getParams();
    $arParams['currentOrder'] = $generalCatalogOffers->getOrderRows();

    // опредлим текущую секцию в каталоге и запишем в фильтр каталога
    if (!empty($_REQUEST['secid']))
    {
        $arResult['CUR_SECTION_CODE'] = $_REQUEST['secid'];
        $arParams['CUR_SECTION_CODE'] = $_REQUEST['secid'];
    }
}

$catalogSection = new Novagroup_Classes_General_CatalogSection(
    $arParams["CATALOG_IBLOCK_ID"], 0, $arResult['CUR_SECTION_CODE']
);

if(is_array($arElementsSearch))
    ksort($arElementsSearch);

$cacheParams = array(
    $arResult['CATALOG_ROOT'],
    $arParams['CUR_SECTION_CODE'],
    $arElementsSearch,
    $USER->GetGroups()
);

// Если нет валидного кеша (то есть нужно запросить данные и сделать валидный кеш)
if ( $this -> StartResultCache( false, $cacheParams, SITE_ID."/novagr.shop/catalog.list/".md5( $USER->GetGroups() ) ) )
{
    $arrSection = $catalogSection->getSection();

    $hasSubcections = false;


    $arFilter = array(
        "IBLOCK_ID" => $arParams['CATALOG_IBLOCK_ID'], "SECTION_ID" => $arrSection["ID"]
    );
    $arSelect = array("ID", "NAME");
    $res = CIBlockSection::GetList(array("SORT"=>"ASC"), $arFilter, false,$arSelect, false);

    if ($arRes = $res->Fetch())
    {
        $hasSubcections = true;
    }

    if (!isset($_REQUEST["q"])
        && !isset($_REQUEST['arFilter'])
        && $hasSubcections == true
    )
    {
        $arResult['CATALOG_ROOT'] = 1;
    }


    if ($arResult['CATALOG_ROOT'] == 1) {
        $this -> IncludeComponentTemplate("catalog_root");
    } else {

        // сделаем выборку элементов
        $arResult['ELEMENT'] = $generalCatalogOffers->getElementList();

        // запомним полученные параметры для навигации
        if(method_exists($generalCatalogOffers, 'getLastResult'))
        {
            $getLastResult = $generalCatalogOffers->getLastResult();
            if(method_exists($getLastResult, 'GetPageNavStringEx'))
            {
                $arResult['NAV_STRING'] = $getLastResult->GetPageNavStringEx($navComponentObject, "", "catalog");
                $arResult['NavPageCount'] = $getLastResult->NavPageCount;
                $arResult['NavPageNomer'] = $getLastResult->NavPageNomer;
                $arResult['NavRecordCount'] = $getLastResult->NavRecordCount;
            }
        }

        $seoModule = new Novagroup_Classes_General_Seo($arParams["CATALOG_IBLOCK_ID"],0,$arResult['CUR_SECTION_CODE']);
        $arResult['SEO_DATA']['HEADER'] = $seoModule->getHeader();
        $arResult['SEO_DATA']['DESCRIPTION'] = $seoModule->getDescription();

        $CACHE_MANAGER->StartTagCache($this->getCachePath());
        foreach($arResult['ELEMENT'] as $element){
            $CACHE_MANAGER->RegisterTag("catalog.list.".$element['ID']);
        }
        $CACHE_MANAGER->EndTagCache();

        $this->SetResultCacheKeys(array(
                "CUR_SECTION_CODE",
                "SEO_DATA",
                "SEARCH_NOT_FOUND",
        ));
        if(count($arResult['ELEMENT']) == 0 )
        {
            @define("ERROR_404", "Y");
            $arResult['SEARCH_NOT_FOUND'] = "Y";
            $this -> IncludeComponentTemplate('notfound');
        } else {
            $this -> IncludeComponentTemplate();
        }
    }
}

if($arResult['SEARCH_NOT_FOUND']=="Y")
{
    @define("SEARCH_NOT_FOUND", "Y");
}

// операции которые не попадают в кэш
$arReturnUrl = array(
		
		"add_element" => (
				strlen($arParams["SECTION_URL"])?
				$arParams["SECTION_URL"]:
				CIBlock::GetArrayByID($arParams["CATALOG_IBLOCK_ID"], "SECTION_PAGE_URL")
		),
);
$arButtons = CIBlock::GetPanelButtons(
		$arParams["CATALOG_IBLOCK_ID"],
		0,
		$arResult["SECTION"]["ID"],
		array("RETURN_URL" =>  $arReturnUrl, "CATALOG"=>true)
);

$this->AddIncludeAreaIcons(CIBlock::GetComponentMenu($APPLICATION->GetPublicShowMode(), $arButtons));


if ($browserTitle == "") $catalogSection->setPageProperties();
$catalogSection->addChainItems();

$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/product.js');
?>