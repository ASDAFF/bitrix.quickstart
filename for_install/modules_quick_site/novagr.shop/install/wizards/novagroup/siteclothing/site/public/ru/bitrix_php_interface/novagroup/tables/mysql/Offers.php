<?php
/**
 * Created by JetBrains PhpStorm.
 * User: anton
 * Date: 01.08.13
 * Time: 14:57
 * To change this template use File | Settings | File Templates.
 */

class Novagroup_Tables_Mysql_Offers extends Novagroup_Classes_General_Catalog
{

    protected $selectRows = array(), $filterOffers = array(), $filterProduct = array(), $order = array("CATALOG_PRICE_1" => "ASC");

    function __construct()
    {
        $this->checkInstalledModule();
    }

    function addFilterByOffers($filterOffers = array())
    {
        $this->filterOffers = $filterOffers;
    }

    function addFilterByProducts($filterProduct = array())
    {
        $this->filterProduct = $filterProduct;
    }

    function getFilterByOffers()
    {
        return $filter = $this->filterOffers;
    }

    function getFilterByProducts()
    {
        return $filter = $this->filterProduct;
    }

    function setSelectRows($select)
    {
        $this->selectRows = $select;
    }

    function getSelectRows()
    {
        $this->selectRows[] = "ID";
        return $this->selectRows;
    }

    function setOrder($order)
    {
        $this->order = $order;
    }

    function getOrder()
    {
        if (isset($this->order['CATALOG_PRICE_1']))
            return ($this->order['CATALOG_PRICE_1'] == 'ASC') ? "CATALOG_PRICE_1 ASC" : "CATALOG_PRICE_1 DESC";
        else
            return "CATALOG_PRICE_1 ASC";
    }

    function getSql()
    {
        if($cml2link = $this->CML2_LINK())
        {
            $offersTable = \Bitrix\Sale\ProductTable::query();
            $offersTable->setSelect(array("ID","PRICE"));
            $offersQuery = $offersTable->getQuery();

            $offersQuery = "SELECT IBLOCK_ELEMENT_ID, CML2_LINK, ".substr($offersQuery,6);
            $pricesQuery = "$offersQuery JOIN (".$cml2link['query'].") CPT ON sale_product.ID=CPT.IBLOCK_ELEMENT_ID ORDER BY PRICE ASC";

            $query = CIBlockElement::SubQuery("ID", array());
            $productQuery = $query->GetList(array(), $this->getFilterByProducts(), false, false, $this->getSelectRows());

            $fullQuery[] = "SELECT PRT.*,PST.PRICE CATALOG_PRICE_1 FROM ($productQuery) PRT";
            $fullQuery[] = "JOIN ($pricesQuery) PST ON PRT.ID=PST.CML2_LINK";
            $fullQuery[] = "GROUP BY ID";
            $fullQuery[] = "ORDER BY ".$this->getOrder();

            $fullQuery = implode(" ", $fullQuery);
            return $fullQuery;
        }
        return false;
    }

    function CML2_LINK()
    {
        $getFilterByOffers = $this->getFilterByOffers();
        $IBLOCK_ID = $getFilterByOffers['IBLOCK_ID'];

        $parameters = array(
            "select" => array("*"),
            "filter" => array(
                "CODE" => "CML2_LINK",
                "IBLOCK_ID" => $IBLOCK_ID
            )
        );
        $getResult = \Bitrix\Iblock\PropertyTable::getList($parameters);
        if ($getRow = $getResult->fetch()) {
            if ($getRow['VERSION'] == 2) {
                $property = array();
                $property['table'] = "b_iblock_element_prop_s" . $IBLOCK_ID;
                $property['row'] = "PROPERTY_" . $getRow['ID'];
                $property['query'] = "SELECT ".$property['row']." CML2_LINK, IBLOCK_ELEMENT_ID FROM {$property['table']}";
                return $property;
            } else {
                //свойство хранится в общей таблице
                return false;
            }
        } else {
            //свойство не было найдено
            return false;
        }
    }
}