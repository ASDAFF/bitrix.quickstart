<?php

use Bitrix\Iblock\ElementTable;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();


class ComponentNecessaryEquipment extends CBitrixComponent
{
    /**
     * Массив полей похода для которого подбираются товары.
     *
     * @var array
     */
    private $_route = [];

    /**
     * Массив полей похода для которого подбираются товары.
     *
     * @var array
     */
    private $_productIBlockId = 2;

    /**
     * @param $params
     * @override
     * @return array
     */
    public function onPrepareComponentParams($params)
    {
        $params["RouteId"] = (int)trim($params["RouteId"]);
        $params["RouteIBlockId"] = (int)trim($params["RouteIBlockId"]);
        if ($params["RouteId"] && $params["RouteIBlockId"]) {
            $this->SetRouteInfoById($params["RouteId"], $params["RouteIBlockId"]);
        }

        return $params;
    }

    /**
	 * Start Component
	 */
    public function executeComponent()
    {
        /** @global CMain $APPLICATION */
        global $APPLICATION;

        $this->PrepareData();
        
        $this->includeComponentTemplate();
    }

    /**
     * Подгатавливает массив $arResult для шаблона.
     *
     * @return void
     */
    private function PrepareData() : void {

        $sectionsIds = [];
        $this->arResult["sections"] = [];
        $db_list = CIBlockSection::GetList([], $this->GetCatalogCategoriesFilters(), true);
        while ($result = $db_list->GetNext()) {
            $this->arResult["sections"][(int)$result["ID"]] = $result;
        }
        
        $products = $this->GetProducts(array_keys($this->arResult["sections"]));
        
        foreach ($products as $product) {
            if (is_null($this->arResult["sections"][$product["IBLOCK_SECTION_ID"]]["products"])) {
                $this->arResult["sections"][(int)$product["IBLOCK_SECTION_ID"]]["products"] = [];
            }
            if (count($this->arResult["sections"][$product["IBLOCK_SECTION_ID"]]["products"]) < 5) {
                $this->arResult["sections"][(int)$product["IBLOCK_SECTION_ID"]]["products"][] = $product;
            }
        }
    }

    /**
     * Устанавливает данные о походе, получая ее по id.
     *
     * @param int $routeId
     * @param int $routeIBlockId
     * @return void
     */
    private function SetRouteInfoById(int $routeId, int $routeIBlockId) : void {
        if ($routeId > 0 && $routeIBlockId > 0) {
            $res = CIBlockElement::GetList(
                [],
                ["IBLOCK_ID" => $routeIblockId, "ID" => $routeId],
                false,
                [],
                ["ID", "IBLOCK_ID", "NAME", "PROPERTY_*"]
            );
            while ($ob = $res->GetNextElement()) {
                $this->_route = $ob->GetFields();
                $this->_route["properties"] = $ob->GetProperties();
            }
        }
    }

    /**
     * Возвращает фильтры для получения категорий.
     *
     * @return array
     */
    private function GetCatalogCategoriesFilters() : array {
        $filters = [
            'IBLOCK_ID' => $this->_productIBlockId,
            'GLOBAL_ACTIVE' => 'Y',
            [
                "LOGIC" => "OR",
                ['UF_REGION' => $this->_route["properties"]["direction"]["VALUE"]],
                ['UF_REGION' => false]
            ]
        ];
        
        $typesFilter = ["LOGIC" => "OR"];
        foreach($this->_route["properties"]["types"]["VALUE"] as $type) {
            $typesFilter[] = ["UF_TYPES" => $type];
        }

        $filters[] = $typesFilter;
        return $filters;
    }

    /**
     * Возвращает самые продоваемые товары в категории.
     *
     * @param array $categoriesIds
     * @return array
     */
    private function GetProducts(array $categoriesIds) : array {
        $products = [];
        
        if (count($categoriesIds) > 0) {
            $res = CIBlockElement::GetList(
                ["CREATED" => "DESC"],
                ["IBLOCK_ID" => $this->_productIBlockId, "CATALOG_AVAILABLE" => "Y", "SECTION_ID" => $categoriesIds],
                false,
                ["nPageSize" => $this->arParams['PAGE_ELEMENT_COUNT']],
                ['ID', 'IBLOCK_SECTION_ID', 'NAME', 'PROPERTY_*']
            );
            while ($ob = $res->GetNextElement()) {
                $product = $ob->GetFields();
                $product["properties"] = $ob->GetProperties();
                $products[] = $product;
            }
        }
        
        return $products;
    }
}
