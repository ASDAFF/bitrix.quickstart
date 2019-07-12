<?
    CModule::IncludeModule('iblock');
    CModule::IncludeModule('catalog');
    CModule::IncludeModule('search');

    $arParams["USE_PRODUCT_QUANTITY"] = $arParams["USE_PRODUCT_QUANTITY"]==="Y";

    $arParams["BASKET_URL"]=trim($arParams["BASKET_URL"]);
    if(strlen($arParams["BASKET_URL"])<=0)
        $arParams["BASKET_URL"] = "/personal/basket.php";

    $arParams["ACTION_VARIABLE"]=trim($arParams["ACTION_VARIABLE"]);
    if(strlen($arParams["ACTION_VARIABLE"])<=0|| !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["ACTION_VARIABLE"]))
        $arParams["ACTION_VARIABLE"] = "action";

    $arParams["PRODUCT_ID_VARIABLE"]=trim($arParams["PRODUCT_ID_VARIABLE"]);
    if(strlen($arParams["PRODUCT_ID_VARIABLE"])<=0|| !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["PRODUCT_ID_VARIABLE"]))
        $arParams["PRODUCT_ID_VARIABLE"] = "id";

    $arParams["PRODUCT_QUANTITY_VARIABLE"]=trim($arParams["PRODUCT_QUANTITY_VARIABLE"]);
    if(strlen($arParams["PRODUCT_QUANTITY_VARIABLE"])<=0|| !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["PRODUCT_QUANTITY_VARIABLE"]))
        $arParams["PRODUCT_QUANTITY_VARIABLE"] = "quantity";
    /*************************************************************************
                Processing of the Buy link
    *************************************************************************/
    $strError = "";
    if (array_key_exists($arParams["ACTION_VARIABLE"], $_REQUEST) && array_key_exists($arParams["PRODUCT_ID_VARIABLE"], $_REQUEST))
    {
        if(array_key_exists($arParams["ACTION_VARIABLE"]."BUY", $_REQUEST))
            $action = "BUY";
        elseif(array_key_exists($arParams["ACTION_VARIABLE"]."ADD2BASKET", $_REQUEST))
            $action = "ADD2BASKET";
        else
            $action = strtoupper($_REQUEST[$arParams["ACTION_VARIABLE"]]);

        $productID = intval($_REQUEST[$arParams["PRODUCT_ID_VARIABLE"]]);
        if(($action == "ADD2BASKET" || $action == "BUY") && $productID > 0)
        {
            if(CModule::IncludeModule("iblock") && CModule::IncludeModule("sale") && CModule::IncludeModule("catalog"))
            {
                if($arParams["USE_PRODUCT_QUANTITY"])
                    $QUANTITY = intval($_POST[$arParams["PRODUCT_QUANTITY_VARIABLE"]]);
                if($QUANTITY <= 1)
                    $QUANTITY = 1;

                $product_properties = array();

                if(is_array($arParams["OFFERS_CART_PROPERTIES"]))
                {
                    foreach($arParams["OFFERS_CART_PROPERTIES"] as $i => $pid)
                        if($pid === "")
                            unset($arParams["OFFERS_CART_PROPERTIES"][$i]);

                    if(!empty($arParams["OFFERS_CART_PROPERTIES"]))
                    {
                        $product_properties = CIBlockPriceTools::GetOfferProperties(
                            $productID,
                            $arParams["IBLOCK_ID"],
                            $arParams["OFFERS_CART_PROPERTIES"]
                        );
                    }
                }

                if(!$strError && Add2BasketByProductID($productID, $QUANTITY, $product_properties))
                {
                    if($action == "BUY")
                        LocalRedirect($arParams["BASKET_URL"]);
                    else
                        LocalRedirect($APPLICATION->GetCurPageParam("", array($arParams["PRODUCT_ID_VARIABLE"], $arParams["ACTION_VARIABLE"])));
                }
                else
                {
                    if($ex = $GLOBALS["APPLICATION"]->GetException())
                        $strError = $ex->GetString();
                    else
                        $strError = GetMessage("CATALOG_ERROR2BASKET").".";
                }
            }
        }
    }
    if(strlen($strError)>0)
    {
        ShowError($strError);
        return;
    }

    //обработка параметров
    $bSearchInCatalog = ($arParams['SEARCH_IN_CATALOG'] == 'Y') ? true : false; //искать по каталогу
    $bSearchInContent = ($arParams['SEARCH_IN_CONTENT'] == 'Y') ? true : false; //искать по каталогу
    $bSearchSeparately = ($arParams['SEARCH_SEPARATELY'] == 'Y') ? true : false; //разбивать результат выдачи на модули
    $strSortCatalog = $arParams['SORT_CATALOG'];
    $strCatalogDirect = $arParams['SORT_DIRECT'];
    $strSortContent = $arParams['SORT_CONTENT'];
    $strContentDirect = $arParams['SORT_CONTENT_DIRECT'];
    $iCatalogLimit = intval($arParams['CATALOG_LIMIT']);
    $iContentLimit = intval($arParams['CONTENT_LIMIT']);

    $bUseNav = ($arParams['NEED_NAV'] == 'Y') ? true : false; //использовать постраничную навигацию
    $iNavPageCatalog = intval($arParams['NAV_CATALOG']);
    $iNavPageContent = intval($arParams['NAV_CONTENT']);
    $bCheckInBasket = ($arParams['CHECK_IN_BASKET'] == 'Y') ? true : false; //проверять наличие в корзине

    if(!isset($_REQUEST['q'])||(isset($_REQUEST['q'])&&$_REQUEST['q'] == '')){
        $arResult['ERRORS'][] = 'EMPTYQUERY';
    }

    if(!isset($arResult['ERRORS'])){
        //получаем каталоги
        $dbCatalog = CCatalog::GetList();
        $arCatalogs = array();

        while($arCat = $dbCatalog->GetNext()) $arCatalogs[] = $arCat["IBLOCK_ID"];

        $arResult['CATALS'] = $arCatalogs;

        //выберем массив для корзинки
        if($bCheckInBasket){
            $arInBasket = array();
            $dbTestInBasket = CSaleBasket::GetList(array('sort' => 'asc'),
                array('FUSER_ID' => CSaleBasket::GetBasketUserID(), 'ORDER_ID' => 'NULL'),
                false,
                false,
                array('ID', 'PRODUCT_ID'));
            while($arBasketItem = $dbTestInBasket->GetNext())
            {
                $arInBasket[] = $arBasketItem['PRODUCT_ID'];
            }
        }

        //ищем, ищем, ищем...
        $arResult['SEARCH_RESULT'] = array();

        $arResult['TOTAL_FOUND'] = array();
        $iMaxFound = 0;
        $obSearch = new CSearch();
        $strNavChain = '';

        $arResult['ALL_TOTAL_FOUND'] = 0;

        //если выбран поиск по каталогу - ищем по каталогу
        if(($bSearchInCatalog) && ($_REQUEST["area"] != "content")){

            /*SORT*/
            $arSort = array();
            if($strSortCatalog != "") $arSort[$strSortCatalog] = $strCatalogDirect;

            /*FILTER*/
            $arFilter = array(
                'IBLOCK_ID' => $arCatalogs,
                'ACTIVE' => 'Y',
                '%SEARCHABLE_CONTENT' => strtoupper($_REQUEST['q']),
            );

            /*SELECT*/
            $arSelect = array(
                'ID',
                'IBLOCK_ID',
                'NAME',
                'PREVIEW_TEXT',
                'PREVIEW_PICTURE',
                'DETAIL_PAGE_URL',
                'PROPERTY_model.PREVIEW_PICTURE',
                'PROPERTY_model',
                'CATALOG_QUANTITY'
            );

            /*NAV PARAMS*/
            if($_REQUEST["area"] != "catalog")
                $arNav = array(
                    'nTopCount' => $iCatalogLimit,
                );
            else $arNav = array();


            /*QUERY EXECUTE*/
            $dbSearchElements = CIBlockElement::GetList($arSort,
                $arFilter,
                false,
                $arNav,
                $arSelect);
            $arResult['TOTAL_FOUND']['CATALOG'] = $dbSearchElements->SelectedRowsCount();
            $arResult['ALL_TOTAL_FOUND'] += $dbSearchElements->SelectedRowsCount();

            if($_REQUEST["area"] == "catalog"){
                $dbSearchElements->NavStart($iNavPageCatalog);
                $strNavChain = $dbSearchElements->GetPageNavStringEx($navComponentObject, '', $arParams["NAV_TEMPLATE"], false);
            }

            while ($obElement = $dbSearchElements->GetNextElement()){
                $arItem = $obElement->GetFields();
                $arItem["PROPERTIES"] = $obElement->GetProperties();
                
                //цена
                $arItem['PRICE'] = CCatalogProduct::GetOptimalPrice($arItem['ID']);

                //параметры каталога
                $arItem['PRODUCT_PARAMS'] = CCatalogProduct::GetByID($arItem['ID']);

                //в корзине
                if($bCheckInBasket) {
                    if(in_array($arItem['ID'], $arInBasket)) $arItem['IN_BASKET'] = true;
                    else $arItem['IN_BASKET'] = false;
                }

                if($bSearchSeparately) $arResult['SEARCH_RESULT']['CATALOG'][] = $arItem;
                else $arResult['SEARCH_RESULT'][] = $arItem;
            }
        }

        //если выбран поиск по контенту
        if(($bSearchInContent) && ($_REQUEST["area"] != "catalog")){
            $arSearch = array(
                'QUERY' => $_REQUEST['q'],
                '!=PARAM2' => $arCatalogs,
                'CHECK_DATES' => 'Y',
            );

            if($strSortContent != '') $arSort[$strSortContent] = $strContentDirect;
            else $arSort = array();

            $obSearch = new CSearch;

            $obSearch->Search(
                $arSearch,
                $arSort,
                array()
            );

            $arResult['TOTAL_FOUND']['CONTENT'] = $obSearch->SelectedRowsCount();
            $arResult['ALL_TOTAL_FOUND'] += $obSearch->SelectedRowsCount();


            if($_REQUEST["area"] == 'content'){
                $obSearch->NavStart($iNavPageContent, false);
                $strNavChain = $obSearch->GetPageNavStringEx($navComponentObject, '', $arParams["NAV_TEMPLATE"], false);
            }
            else $obSearch->NavStart($iContentLimit, false);

            while($arSearchItem = $obSearch->GetNext())
            {
                if($bSearchSeparately) $arResult['SEARCH_RESULT']['CONTENT'][] = $arSearchItem;
                else $arResult['SEARCH_RESULT'][] = $arSearchItem;

            }
        }

        $arResult['NAV_CHAIN'] = $strNavChain;
    }

    $this->IncludeComponentTemplate();
?>
