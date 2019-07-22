<?php

use Bitrix\Main\Localization\Loc,
    Bitrix\Main\SystemException,
    Bitrix\Main\Loader,
    Bitrix\Sale,
    Bitrix\Main\Application,
    Bitrix\Main\Entity\Query,
    Bitrix\Iblock\ElementTable;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

CBitrixComponent::includeComponentClass("custom:catalog.viewed.products");

class CCatalogElementsComponent extends CCustomCatalogViewedProductsComponent
{
    /**
     * @param $params
     * @override
     * @return array
     */
    public function onPrepareComponentParams($params)
    {
        $params = parent::onPrepareComponentParams($params);

        if (!isset($params["CACHE_TIME"]))
            $params["CACHE_TIME"] = 86400;

        $params["DETAIL_URL"] = trim($params["DETAIL_URL"]);

        if (!isset($params['FILTER']) || empty($params['FILTER']) || !is_array($params['FILTER']))
            $params['FILTER'] = array();

        return $params;
    }

    /**
     * Возвращает массив параметров сортировки из параметров компонента.
     *
     * @return array
     */
    protected function getSort() // : array
    {
        $sortFields = array();
        
        $request = Application::getInstance()->getContext()->getRequest();
        $order = $request->getQuery("order");
        $by = $request->getQuery("by");
        if ($order && $by) {
            $sortFields[$by] = $order;
        }

        if (!isset($sortFields[$this->arParams['ELEMENT_SORT_FIELD']])
            && $this->arParams['ELEMENT_SORT_FIELD']
            && $this->arParams['ELEMENT_SORT_ORDER']
        ) {
            $sortFields[$this->arParams['ELEMENT_SORT_FIELD']] = $this->arParams['ELEMENT_SORT_ORDER'];
        }

        if (!isset($sortFields[$this->arParams['ELEMENT_SORT_FIELD2']])
            && $this->arParams['ELEMENT_SORT_FIELD']
            && $this->arParams['ELEMENT_SORT_ORDER']) {
            $sortFields[$this->arParams['ELEMENT_SORT_FIELD2']] = $this->arParams['ELEMENT_SORT_ORDER2'];
        }

        if (count($sortFields) == 0) {
            $sortFields = ["CREATED" => "DESC"];
        }
        
        return $sortFields;
    }

    /**
     * @override
     * @return bool
     */
    protected function extractDataFromCache()
    {
        if ($this->arParams['CACHE_TYPE'] == 'N')
            return false;

        $userGroups = implode(",", Bitrix\Main\UserTable::getUserGroupIds($this->getUserId()));
        return !($this->startResultCache(false, $userGroups));
    }

    /**
     * @override
     * @return void
     */
    protected function formatResult()
    {
        parent::formatResult();
        $this->arResult['PERIOD'] = $this->arParams['PERIOD'];
        $this->arResult['BY'] = $this->arParams['BY'];
    }

    /**
     * Возвращает массив ид товаров для вывода.
     *
     * @override
     * @return integer[]
     */
    protected function getProductIds()
    {
        if (!CModule::IncludeModule("iblock"))
            return [];

        $arFilter = array_merge($this->GetSectionForFilter(), $this->GetFilersFromParameters());
        unset($arFilter["FACET_OPTIONS"]);
        $arFilter['IBLOCK_ID'] = $this->arParams["IBLOCK_ID"];
        
        $arFilter = $this->SetFilterForOffers($arFilter);

        $this->SetPageTitleInSection($arFilter);
        $productIds = [];
        if ($this->arParams['PAGE_ELEMENT_COUNT'] > 0) {
            $productIds = $this->GetProductsIdsArray($arFilter);
        }

        return $productIds;
    }

    /**
     * Возвращает массив для фильтрации из, заданного в параметрах компонента, элемента $GLOBALS.
     *
     * @return array
     */
    private function GetFilersFromParameters()
    {
        if (strlen($this->arParams["FILTER_NAME"]) <= 0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $this->arParams["FILTER_NAME"])) {
            $arrFilter = [];
        } else {
            $arrFilter = $GLOBALS[$this->arParams["FILTER_NAME"]];
            if (!is_array($arrFilter)) {
                $arrFilter = [];
            }
        }
        return $arrFilter;
    }

    /**
     * @override
     * @throws Exception
     */
    protected function checkModules()
    {
        parent::checkModules();
        if (!$this->isSale)
            throw new SystemException(Loc::getMessage("CVP_SALE_MODULE_NOT_INSTALLED"));
    }

    /**
     * Возвращает массив с ид и кодом раздела (при наличии) для фильтрации товаров.
     *
     * @return array
     */
    private function GetSectionForFilter()
    {
        $arFilter = [];
        if ((int)$this->arParams["SECTION_ID"] > 0) {
            $arFilter['SECTION_ID'] = $this->arParams["SECTION_ID"];
        } elseif ((int)$this->arParams["~~SECTION_ID"] > 0) {
            $arFilter['SECTION_ID'] = $this->arParams["~~SECTION_ID"];
        }

        if ($this->arParams["SECTION_CODE"]) {
            $arFilter["SECTION_CODE"] = $this->arParams["SECTION_CODE"];
        } elseif ($this->arParams["~~SECTION_CODE"]) {
            $arFilter["SECTION_CODE"] = $this->arParams["~~SECTION_CODE"];
        }
        return $arFilter;
    }

    /**
     * Устанавливает аттрибут $this->arResult строку пагинации.
     *
     * @param $res
     */
    protected function SetPagination($res)
    {
        $res->NavStart($this->arParams['PAGE_ELEMENT_COUNT']);
        $this->arResult["NavPageCount"] = $res->NavPageCount;
        $res->nPageWindow = $this->arParams['PAGE_ELEMENT_COUNT'];
        $this->arResult["NAV_STRING"] = $res->GetPageNavString(null, "", true, ['BASE_LINK' => "/catalog/"]);
    }

    /**
     * Устанавливает title для раздела.
     *
     * @param $arFilter
     */
    private function SetPageTitleInSection($arFilter)
    {
        global $APPLICATION;
        if ($arFilter['SECTION_ID'] || $arFilter['SECTION_CODE']) {
            $db_list = CIBlockSection::GetList([], ['IBLOCK_ID' => $this->arParams["IBLOCK_ID"], 'ID' => $arFilter['SECTION_ID'], 'CODE' => $arFilter['SECTION_CODE']], true)->Fetch();
            $APPLICATION->AddChainItem($db_list["NAME"]);
            $APPLICATION->SetTitle($db_list["NAME"]);
        }
    }

    /**
     * Возвращает массив ид товаров для вывода.
     *
     * @param $arFilter
     * @return array
     */
    private function GetProductsIdsArray($arFilter)
    {
        $request = Application::getInstance()->getContext()->getRequest();

        $temp = $request->getPost("PAGEN_1");
        $this->arResult["pageNumber"] = $temp ? $temp : $request->getQuery("PAGEN_1");
        $this->arResult["pageNumber"] = $this->arResult["pageNumber"] > 0 ? $this->arResult["pageNumber"] : 1;

        $productIds = [];
        $res = $this->GetProductsFromDB($arFilter, $this->arResult["pageNumber"]);

        if ($this->arResult["pageNumber"] > $res->NavPageCount) {
            return [];
        }

        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $productIds[] = $arFields['ID'];
        }

        $this->SetPagination($res);
        return $productIds;
    }

    /**
     * Возвращает результат выборки товаров.
     *
     * @param $arFilter
     * @param $iNumPage
     * @return CDBResult
     */
    private function GetProductsFromDB($arFilter, $iNumPage)
    {
        return CIBlockElement::GetList(
            $this->getSort(),
            $arFilter,
            false,
            [
                "nPageSize" => $this->arParams['PAGE_ELEMENT_COUNT'],
                "iNumPage" => (int)$iNumPage
            ],
            ['ID']
        );
    }

    /**
     * Устанавливает фильтры для товаров, из фильтров для торговых предложений.
     *
     * @param $arFilter
     * @return array
     */
    private function SetFilterForOffers($arFilter)//: array
    {
        $priceFilterKeys = [];
        foreach (array_keys($arFilter) as $key) {
            if (strpos($key, "CATALOG_PRICE") !== false) {
                $priceFilterKeys[] = $key;
            }
        }

        if (is_array($arFilter["OFFERS"]) || count($priceFilterKeys) > 0) {

            $offersFilter = [
                "IBLOCK_ID" => 3,
                "ACTIVE" => "Y",
            ];
            if ($arFilter["OFFERS"] == false) {
                $arFilter[] = [
                    "LOGIC" => "OR"
                ];
                $key = array_key_last($arFilter);
                foreach ($priceFilterKeys as $item) {
                    $arFilter[$key][] = [$item => $arFilter[$item]];
                    $offersFilter[$item] = $arFilter[$item];
                    unset($arFilter[$item]);
                }
            } else {
                foreach ($arFilter["OFFERS"] as $key => $value) {
                    $offersFilter[$key] = $value;
                }
                // $arFilter = [];
            }

            $res = CIBlockElement::GetList(
                ["ID" => "DESC"],
                $offersFilter,
                false,
                [],
                ['ID', "NAME", "IBLOCK_ID", "PROPERTY_CML2_LINK"]
            );
            while ($ob = $res->GetNextElement()) {
                if ($arFilter["OFFERS"] == false) {
                    foreach ($priceFilterKeys as $item) {
                        $arFilter[$key][] = ["ID" => $ob->GetFields()["PROPERTY_CML2_LINK_VALUE"]];
                    }
                } else {
                    $arFilter["ID"][] = (int)$ob->GetFields()["PROPERTY_CML2_LINK_VALUE"];
                }
            }
            unset($arFilter["OFFERS"]);
        }
        return $arFilter;
    }
}