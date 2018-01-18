<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

CBitrixComponent::includeComponentClass("bitrix:catalog.smart.filter");

class CBitrixCatalogSmartFilterExt extends CBitrixCatalogSmartFilter {
    
    /*
     * Подготовка параметров
     */
    public function onPrepareComponentParams($arParams){
        
        $result = parent::onPrepareComponentParams($arParams);
        
        $result["OFFERS_PROPERTY_CODE"] = isset($arParams["OFFERS_PROPERTY_CODE"]) ? $arParams["OFFERS_PROPERTY_CODE"]: array();
        $result["IS_SEF"] = $arParams["IS_SEF"]== "Y"? "Y": "N";
        $result["SEF_BASE_URL"] = isset($arParams["SEF_BASE_URL"])? $arParams["SEF_BASE_URL"]: "/catalog/";
        $result["SECTION_PAGE_URL"] = isset($arParams["SECTION_PAGE_URL"])? $arParams["SECTION_PAGE_URL"]: "#SECTION_ID#/";
        
        // <editor-fold defaultstate="collapsed" desc="Пробуем определить текущий раздел из URL">

        $result["IS_SEF"] = $arParams["IS_SEF"]== "Y"? "Y": "N";
        $result["SEF_BASE_URL"] = isset($arParams["SEF_BASE_URL"])? $arParams["SEF_BASE_URL"]: "/catalog/";
        $result["SECTIONS_PAGE_URL"] = !empty($arParams["SECTIONS_PAGE_URL"])? $arParams["SECTIONS_PAGE_URL"]: ""; 
        $result["SECTION_PAGE_URL"] = isset($arParams["SECTION_PAGE_URL"])? $arParams["SECTION_PAGE_URL"]: "#SECTION_ID#/"; 
        $result["ELEMENT_PAGE_URL"] = isset($arParams["ELEMENT_PAGE_URL"])? $arParams["ELEMENT_PAGE_URL"]: "#SECTION_ID#/#ELEMENT_ID#/"; 
        $result["COMPARE_PAGE_URL"] = isset($arParams["COMPARE_PAGE_URL"])? $arParams["COMPARE_PAGE_URL"]: "compare.php?action=COMPAR"; 
        
        $this->arParams["IBLOCK_ID"] = $result["IBLOCK_ID"];
        
        if($result["IS_SEF"] == "Y"){
            $arVariables = array();
            
            $engine = new CComponentEngine($this);
            if (CModule::IncludeModule('iblock'))
            {
                    $engine->addGreedyPart("#SECTION_CODE_PATH#");
                    $engine->setResolveCallback(array("CIBlockFindTools", "resolveComponentEngine"));
            }
            
            $componentPage = $engine->guessComponentPath(
                    $result["SEF_BASE_URL"],
                    array(
                            "sections" => $result["SECTIONS_PAGE_URL"],
                            "section" => $result["SECTION_PAGE_URL"],
                            "element" => $result["ELEMENT_PAGE_URL"],
                            "compare" => $result["COMPARE_PAGE_URL"]
                    ),
                    $arVariables
            );    
            if(!$componentPage && isset($_REQUEST["q"])) 
                    $componentPage = "search";
            else if($componentPage == "sections" && isset($_REQUEST["q"]))
                $componentPage = "search";
           
            $result["PAGE"] = $componentPage;
            
            if(isset($arVariables["SECTION_ID"]))
                $result["SECTION_ID"] = $arVariables["SECTION_ID"];
            else if(isset($arVariables["SECTION_CODE"])) 
                $result["SECTION_CODE"] = $arVariables["SECTION_CODE"];
        }

        // </editor-fold>
        
        // <editor-fold defaultstate="collapsed" desc="Получим раздел">
       
        //если задан символьный код раздела
        if(strlen($result["SECTION_CODE"])>0) {
            if(CModule::IncludeModule("iblock")) {
                $rsSections = CIBlockSection::GetList(array(), array("CODE" => $result["SECTION_CODE"], "IBLOCK_ID" => $arParams["IBLOCK_ID"]));
                $arSection = $rsSections->GetNext();
                $result["SECTION_ID"] = $arSection["ID"];
            }
        }
        
        // </editor-fold>
        
        if($result["SECTION_ID"] >0) 
            $result["PAGE"] = $componentPage;
        
        return $result;
    }

    /*
     * Получение списка свойств для фильтрации
     */
    public function getResultItems(){
        
        //получим список из стандартного компонента
        $items = parent::getResultItems();
        
        //переставим цены вверх
        $newItems = array();
        foreach($items as $pid => $item) {
            if($item["PRICE"] == true) 
                $newItems[$pid] = $item;
        }
        foreach($items as $pid => $item) {
            if($item["PRICE"] != true) 
                $newItems[$pid] = $item;
        }
        $items = $newItems;
        
        
        return $items;
    }

    /*
     * Переобпределение метода формирования фильтра
     */
    public function makeFilter($FILTER_NAME)  {
        $gFilter = $GLOBALS[$FILTER_NAME];

        $arFilter = array(
                "IBLOCK_ID" => $this->IBLOCK_ID,
                "IBLOCK_LID" => SITE_ID,
                "IBLOCK_ACTIVE" => "Y",
                "ACTIVE_DATE" => "Y",
                "ACTIVE" => "Y",
                "CHECK_PERMISSIONS" => "Y",
                "MIN_PERMISSION" => "R",
                "INCLUDE_SUBSECTIONS" => "Y", //($arParams["INCLUDE_SUBSECTIONS"] != 'N' ? 'Y' : 'N'),
        );
        if($this->SECTION_ID > 0)
            $arFilter["SECTION_ID"] = $this->SECTION_ID;
        
        if ('Y' == $this->arParams['HIDE_NOT_AVAILABLE'])
            $arFilter['CATALOG_AVAILABLE'] = 'Y';

        if(is_array($gFilter["OFFERS"]))
        {
                if(!empty($gFilter["OFFERS"]))
                {
                        $arSubFilter = $gFilter["OFFERS"];
                        $arSubFilter["IBLOCK_ID"] = $this->SKU_IBLOCK_ID;
                        $arSubFilter["ACTIVE_DATE"] = "Y";
                        $arSubFilter["ACTIVE"] = "Y";
                        if ('Y' == $this->arParams['HIDE_NOT_AVAILABLE'])
                            $arSubFilter['CATALOG_AVAILABLE'] = 'Y';
                        $arFilter["=ID"] = CIBlockElement::SubQuery("PROPERTY_".$this->SKU_PROPERTY_ID, $arSubFilter);
                }

                $arPriceFilter = array();
                foreach($gFilter as $key => $value)
                {
                        if(preg_match('/^(>=|<=|>|<|><)CATALOG_PRICE_/', $key))
                        {
                                $arPriceFilter[$key] = $value;
                                unset($gFilter[$key]);
                        }
                }

                if(!empty($arPriceFilter))
                {
                        $arSubFilter = $arPriceFilter;
                        $arSubFilter["IBLOCK_ID"] = $this->SKU_IBLOCK_ID;
                        $arSubFilter["ACTIVE_DATE"] = "Y";
                        $arSubFilter["ACTIVE"] = "Y";
                        if ('Y' == $this->arParams['HIDE_NOT_AVAILABLE'])
                            $arSubFilter['CATALOG_AVAILABLE'] = 'Y';
                        $arFilter[] = array(
                                "LOGIC" => "OR",
                                array($arPriceFilter),
                                "=ID" => CIBlockElement::SubQuery("PROPERTY_".$this->SKU_PROPERTY_ID, $arSubFilter),
                        );
                }

                unset($gFilter["OFFERS"]);
        }

        return array_merge($gFilter, $arFilter);
    }
}
?>