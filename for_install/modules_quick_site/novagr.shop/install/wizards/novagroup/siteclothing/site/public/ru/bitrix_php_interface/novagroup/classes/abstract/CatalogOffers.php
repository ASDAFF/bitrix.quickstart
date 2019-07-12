<?php
/**
 * Created by JetBrains PhpStorm.
 * User: anton
 * Date: 23.07.13
 * Time: 18:29
 * To change this template use File | Settings | File Templates.
 */

abstract class Novagroup_Classes_Abstract_CatalogOffers extends Novagroup_Classes_Abstract_Catalog
{

    protected $catalogID, $offersID, $showQuantityNull = false, $arCatalogPrice;
    protected $arParams = array(
        'nPageSize' => 16,
        'iNumPage' => 1,
        'arFilterRequest' => array(),
        'arOfferRequest' => array(),
        'orderRows'=>  array(
            'SORT' => array("NAME"=>"популярности", "DEFAULT_BY"=>"DESC"),
            'ID' => array("NAME"=>"порядку"),
            "PROPERTY_NEWPRODUCT" => array("NAME"=>"новизне", "DEFAULT_BY"=>"DESC"),
            "CATALOG_PRICE_1_DESC" => array("NAME" => "цене по убыванию", "DEFAULT_BY" => "DESC"),
            "CATALOG_PRICE_1" => array("NAME" => "цене по возрастанию", "DEFAULT_BY" => "ASC"),
            //"DISCOUNT" => array("NAME"=>"Скидке"),
        ),
       'orderBy' => array(
	   		"DESC" => array("NAME" => "убыванию"),
	   		"ASC" => array("NAME" => "возрастанию")
		),
    );
    protected $arSelect = array(
        'IBLOCK_ID',
        'ID',
        'IBLOCK_SECTION_ID',
        'NAME',
        'CODE',
        'PREVIEW_TEXT',
        'DETAIL_TEXT',
        'DETAIL_PAGE_URL',
        'PROPERTY_SKU',
        'PROPERTY_SAMPLES',
        'PROPERTY_MATERIAL.NAME',
        'PROPERTY_VENDOR',
        'PROPERTY_VENDOR.NAME',
        'PROPERTY_VENDOR.CODE',
        'PROPERTY_VENDOR.PREVIEW_TEXT',
        'PROPERTY_VENDOR.DETAIL_TEXT',
        'PROPERTY_SPECIALOFFER',
        'PROPERTY_NEWPRODUCT',
        'PROPERTY_SALELEADER'
    );
    protected $order;
    static $getElementList;

    function __construct($catalogID, $offersID)
    {
        $this->catalogID = (int)$catalogID;
        $this->offersID = (int)$offersID;

        // set current price for filter
        include($_SERVER['DOCUMENT_ROOT'].SITE_DIR."include/catalog/incSetGroupPrice.php");
        $this -> arCatalogPrice = $arParams;
    }

    // определим количество элементов на страницу

    function setItemsOnPage($value = 0)
    {
        if ($value > 0) $this->arParams['nPageSize'] = (int)$value;
    }

    // определим номер страницы

    function setCurrentPage($value = 0)
    {
        if ($value > 0) $this->arParams['iNumPage'] = (int)$value;
    }

    // сформируем фильтр для каталога

    function addFilter($arFilter = array())
    {
		if (is_array($arFilter)) {
            foreach ($arFilter as $val) {
                if (is_array($val)) {
					foreach ($val as $subkey => $subval) {
                        switch ($subkey) {
                            case 'minCATALOG_PRICE_1':
                                $this->arParams['arOfferRequest']['>='.$this -> arCatalogPrice['CATALOG_PRICE']] = $subval;
                                break;
                            case 'maxCATALOG_PRICE_1':
                                $this->arParams['arOfferRequest']['<='.$this -> arCatalogPrice['CATALOG_PRICE']] = $subval;
                                break;
                            case 'SECTION_CODE':
                                if (strlen(trim($subval))>0)
                                    $this->arParams['arFilterRequest'][$subkey][] = $subval;
                                break;
                            default:
                                $this->arParams['arFilterRequest'][$subkey][] = $subval;
                        }
                    }
                }
            }
        }
    }

    // сформируем фильтр для торговых предложений

    function addOffersFilter($arOffer = array())
    {
        if (is_array($arOffer)) {
            foreach ($arOffer as $val) {
                if (is_array($val)) {
                    foreach ($val as $subkey => $subval) {
                        switch ($subkey) {
                            case 'PROPERTY_CML2_LINK':
                                $this->arParams['arOfferRequest'][$subkey] = (is_array($subval) and count($subval) > 0) ? $subval : -1;
                                break;
                            default:
                                $this->arParams['arOfferRequest'][$subkey][] = $subval;
                        }
                    }
                }
            }
        }
    }

    function getParams()
    {
        return $this->arParams;
    }

    function getParam($name)
    {
        return $this->arParams[$name];
    }

    function getOrderRows()
    {
        $sortingValue = COption::GetOptionString("novagroup", "sorting_catalog");
        if (empty($sortingValue)) {
            $defaultRow = key($this->arParams['orderRows']);
            $defaultBy = key($this->arParams['orderBy']);
        } else {
            $defaultRow = $sortingValue;
            $defaultBy = $this->arParams['orderRows'][$defaultRow]['DEFAULT_BY'];
            $defaultBy = (empty($defaultBy)) ? key($this->arParams['orderBy']) : $defaultBy;
        }

        $defaultOrder = array($defaultRow=>$defaultBy);

        $currentOrder = null;
        $arParams = $this->getParams();
        $orders = (is_array($this->order)) ? $this->order : array();
        foreach($orders as $order => $by)
        {
            if( array_key_exists($order, $arParams['orderRows']) )
            {
                if( array_key_exists($by, $arParams['orderBy']) )
                {
                    $currentOrder[$order] = $by;
                } else {
                    $currentOrder[$order] = (isset($this->arParams['orderRows'][$order]['DEFAULT_BY'])) ? $this->arParams['orderRows'][$order]['DEFAULT_BY'] : key($this->arParams['orderBy']);
                }
            }
        }
        return $this->arParams['currentOrder'] = (isset($currentOrder)) ? $currentOrder : $defaultOrder;
    }

     function getFilterRows()
    {
		if(
			isset($this->arParams['arFilterRequest']['PROPERTY_SPECIALOFFER_VALUE'])
            ||
            isset($this->arParams['arFilterRequest']['PROPERTY_NEWPRODUCT_VALUE'])
            ||
            isset($this->arParams['arFilterRequest']['PROPERTY_SALELEADER_VALUE'])
        )
        {
            $arSpecial[0]['LOGIC'] = "OR";
        }
        if( isset($this->arParams['arFilterRequest']['PROPERTY_SPECIALOFFER_VALUE']) )
        {
            $arSpecial[0]['PROPERTY_SPECIALOFFER_VALUE'] = $this->arParams['arFilterRequest']['PROPERTY_SPECIALOFFER_VALUE'][0];
            unset($this->arParams['arFilterRequest']['PROPERTY_SPECIALOFFER_VALUE']);
        }
        if( isset($this->arParams['arFilterRequest']['PROPERTY_NEWPRODUCT_VALUE']) )
        {
            $arSpecial[0]['PROPERTY_NEWPRODUCT_VALUE'] = $this->arParams['arFilterRequest']['PROPERTY_NEWPRODUCT_VALUE'][0];
            unset($this->arParams['arFilterRequest']['PROPERTY_NEWPRODUCT_VALUE']);
        }
        if( isset($this->arParams['arFilterRequest']['PROPERTY_SALELEADER_VALUE']) )
        {
            $arSpecial[0]['PROPERTY_SALELEADER_VALUE'] = $this->arParams['arFilterRequest']['PROPERTY_SALELEADER_VALUE'][0];
            unset($this->arParams['arFilterRequest']['PROPERTY_SALELEADER_VALUE']);
        }
		
		$arFilter = $this->arParams['arFilterRequest'];
		if( !empty($arSpecial) ) $arFilter = array_merge($arSpecial, $arFilter);
        $arFilter['INCLUDE_SUBSECTIONS'] = "Y";
        $arFilter['IBLOCK_ID'] = $this->catalogID;
        $arFilter['SECTION_GLOBAL_ACTIVE'] = "Y";
		
        $arOfferFilter = $this->getOffersFilter();
		
        $arFilter['ID'] = parent::SubQuery(
            'PROPERTY_CML2_LINK',
            $arOfferFilter
        );		
        return $arFilter;
    }

    function getOffersFilter()
    {
        $arOfferFilter = $this->arParams['arOfferRequest'];
        $arOfferFilter['IBLOCK_ID'] = $this->offersID;
        $arOfferFilter[">CATALOG_QUANTITY"] = 0;

        return $arOfferFilter;
    }

    function getSelectRows()
    {
        return $this->arSelect;
    }

    function getElementList()
    {
        //зададим параметры сортировки
        $arOrder = $this->getOrderRows();
        //обрабатываем ситуацию, как сделать выборку
        if(array_key_exists("CATALOG_PRICE_1",$arOrder) || array_key_exists("CATALOG_PRICE_1_DESC",$arOrder))
        {
            $arResult = $this->getElementListOrderByPrice();
            if($arResult!==false)
            {
                //если результат не равен FALSE, вернем его. иначе скрипт выполнится дальше и вернет результат выборки с обычной сортировкой
                return $arResult;
            }
            if(isset($arOrder['CATALOG_PRICE_1_DESC']))
            {
                $arOrder[$this -> arCatalogPrice['CATALOG_PRICE']] = $arOrder['CATALOG_PRICE_1_DESC'];
            } else {
                $arOrder[$this -> arCatalogPrice['CATALOG_PRICE']] = $arOrder['CATALOG_PRICE_1'];
            }
        }
        if(array_key_exists("DISCOUNT",$arOrder))
        {
            $arResult = $this->getElementListOrderByDiscount();
            if($arResult!==false) {
                //если результат не равен FALSE, вернем его. иначе скрипт выполнится дальше и вернет результат выборки с обычной сортировкой
                return $arResult;
            }
        }
        // зададим поля для выборки
        $arSelectFields = $this->getSelectRows();
        //дополнительные параметры фильтрации
        $arFilter = $this->getFilterRows();
        // зададим параметры постраничной навигации
        $arNavStartParams = array(
            'nPageSize' => $this->arParams['nPageSize'],
            'iNumPage' => $this->arParams['iNumPage']
        );

        //вернем результат в виде массива
        $arResult = self::$getElementList = parent::getElementList($arOrder, $arFilter, false, $arNavStartParams, $arSelectFields);
        return $arResult;
    }

    static function getElementsByLastResult()
    {
        return self::$getElementList;
    }

    static function getElementByLastResult($ID)
    {
        $getElementByLastResult = self::getElementsByLastResult();
        if(is_array($getElementByLastResult) and $ID>0)
        {
            foreach($getElementByLastResult as $element)
            {
                if($element['ID']==$ID)return $element;
            }
        }
        return false;
    }

    function getElementListOrderByDiscount()
    {
        $dbProductDiscounts = CCatalogDiscount::GetList();
        while ($arProductDiscounts = $dbProductDiscounts->Fetch())
        {
            Novagroup_Classes_General_Main::deb($arProductDiscounts);
        }
        return true;
    }

    function getElementListOrderByPrice()
    {
        $arOrder = $this->getOrderRows();
        if(isset($arOrder['CATALOG_PRICE_1_DESC']))
        {
            $arOrder['CATALOG_PRICE_1'] = $arOrder['CATALOG_PRICE_1_DESC'];
        }
        $query = new Novagroup_Tables_Mysql_Offers();
        $query->addFilterByOffers($this->prepareFilter($this->getOffersFilter()));
        $query->addFilterByProducts($this->prepareFilter($this->getFilterRows()));
        $query->setSelectRows($this->getSelectRows());
        $query->setOrder($arOrder);
        $strSql = $query->getSql();

        global $DB; $isQuery = false; $arResult = array();
        if(method_exists($DB,'Query'))
        {
            $list = $DB->Query($strSql, true, __LINE__);
            if($list!==false and method_exists($list, 'NavStart') and method_exists($list, 'GetNext'))
            {
                $list->NavStart($this->arParams['nPageSize'],false, $this->arParams['iNumPage']);
                while($item = $list->GetNext())
                {
                    $section = GetIBlockSection($item['IBLOCK_SECTION_ID']);
                    $item['DETAIL_PAGE_URL'] = str_replace("#"."SITE_DIR"."#",SITE_DIR,$item['DETAIL_PAGE_URL']);
                    $item['DETAIL_PAGE_URL'] = str_replace("#"."ELEMENT_CODE"."#",$item['CODE'],$item['DETAIL_PAGE_URL']);
                    $item['DETAIL_PAGE_URL'] = str_replace("#"."SECTION_CODE"."#",$section['CODE'],$item['DETAIL_PAGE_URL']);
                    $item['DETAIL_PAGE_URL'] = str_replace('//','/',$item['DETAIL_PAGE_URL']);


                    $arResult[] = $item;
                }
                $this->lastResult = $list;
                $isQuery = true;
            }
        }
        return ($isQuery === true) ? self::$getElementList = $arResult : false;
    }

    function setOrder($rows = array())
    {
        $this->order = array();
        if(is_array($rows))
        {
            foreach($rows as $row=>$by)
            {
                $this->order[$row] = $by;
            }
        }
    }

}