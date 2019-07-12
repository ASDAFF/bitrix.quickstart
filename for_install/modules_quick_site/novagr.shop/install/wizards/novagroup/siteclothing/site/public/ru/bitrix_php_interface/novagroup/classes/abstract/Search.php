<?php
/**
 * Created by JetBrains PhpStorm.
 * User: anton
 * Date: 11.07.13
 * Time: 12:02
 * To change this template use File | Settings | File Templates.
 */

abstract class Novagroup_Classes_Abstract_Search
{

    protected $phrase = "";
    protected $useStatistic = false;
    protected $obSearch;
    protected $iBlockType, $iBlockId;

    function __construct($phrase)
    {
        if (CModule::IncludeModule('search')) {
            $this->setPhrase($phrase);
        } else {
            die('search module is not installed');
        }
    }

    function setPhrase($phrase)
    {
        $this->phrase = trim($phrase);
    }

    function getPhrase()
    {
        return rawurldecode($this->phrase);
    }

    function phraseStat($count)
    {
        if ($this->isValidPhrase() === true and (int)$count > 0) {
            $CSearchStatistic = new CSearchStatistic($this->getPhrase());
            $CSearchStatistic->PhraseStat((int)$count, 1);
        }
    }

    function setUseStatistic($use)
    {
        $this->useStatistic = (bool)$use;
    }

    function isUseStatistic()
    {
        return $this->useStatistic;
    }

    function isValidPhrase()
    {
        $checkPhrase = (strtoupper(LANG_CHARSET) == 'UTF-8') ? iconv('UTF-8', 'CP1251', $this->getPhrase()) : $this->getPhrase();
        $matches = preg_match_all("/^[\xe0-\xff0-9a-z ,.-]+$/i", $checkPhrase, $erty);
        return ($matches > 0) ? true : false;
    }

    function search($iBlockType, $iBlockId, $STEMMING)
    {
        $obSearch = new CSearch;
        $obSearch->Search(array(
            "QUERY" => $this->getPhrase(),
            "MODULE_ID" => "iblock",
            "PARAM1" => $iBlockType,
            "PARAM2" => $iBlockId
        ), array(), array('STEMMING' => $STEMMING));
        return $obSearch;
    }

    function searchByIblock($iBlockType = false, $iBlockId = false)
    {
        $obSearch = $this->search($iBlockType, $iBlockId, true);
        $this->iBlockType = $iBlockType;
        $this->iBlockId = $iBlockId;
        $cnt = $obSearch->SelectedRowsCount();
        if ($cnt == 0) {
            $obSearch = $this->search($iBlockType, $iBlockId, false);
            $cnt = $obSearch->SelectedRowsCount();
        }
        if ($this->isUseStatistic()) {
            if ($this->isValidPhrase() === true) {
                $this->phraseStat($cnt);
            }
        }
        $this->obSearch = & $obSearch;
        return $this;
    }

    function getPrepareArray()
    {
        if ($this->obSearch instanceof CSearch) {
            $arElementsSearch = array();
            while ($arSearchResult = $this->obSearch->Fetch()) {
                $arElementsSearch[$arSearchResult['ID']] = $arSearchResult['ITEM_ID'];
            }
            if (trim($query = $this->getPhrase()) <> "" and $this->iBlockId > 0) {
                $arOffersIBlock = CIBlockPriceTools::GetOffersIBlock($this->iBlockId);
                if ($arOffersIBlock['OFFERS_IBLOCK_ID'] > 0) {
                    $catalog = new Novagroup_Classes_General_CatalogOffers($this->iBlockId, $arOffersIBlock['OFFERS_IBLOCK_ID']);
                    $catalog->addFilter(array(0 => array("NAME" => "%" . $query . "%")));
                    $arFilter = $catalog->getFilterRows();
                    $getElementList = $catalog->getElementList();
                    foreach ($getElementList as $arSearchResult) {
                        $arElementsSearch[$arSearchResult['ID']] = $arSearchResult['ID'];
                    }
                }
            }
            return $arElementsSearch;
        }
    }

    static public function setPageProperties()
    {
        if (defined('SEARCH_NOT_FOUND') and SEARCH_NOT_FOUND == "Y") {
            Novagroup_Classes_General_Main::setTitle("Ничего не найдено");
        }
    }

    static public function addChainItems()
    {
        //if (defined('SEARCH_NOT_FOUND') and SEARCH_NOT_FOUND == "Y") {
            //Novagroup_Classes_General_Main::AddChainItem("Ничего не найдено", "#");
        //}
    }

    function getStemming()
    {
        return $getVariables = stemming($this->getPhrase());
    }

    function getColorsFromQuery()
    {
        $getStemming = $this->getStemming();
        if (count($getStemming) > 0 and CModule::IncludeModule("iblock")) {
            $parameters = array(
                "select" => array("ID", "NAME"),
                "filter" => array(
                    "IBLOCK_ID" => COLORS_IBLOCK_ID,
                ),
                "order" => array("NAME"=>"DESC")
            );
            foreach ($getStemming as $stemming => $rating) {
                $parameters["filter"]["NAME"][] = "%" . $stemming . "%";
            }
            $table = \Bitrix\Iblock\ElementTable::getList($parameters);
            return $table->fetchAll();
        }
    }

    function getColorIDFromQuery()
    {
        $getColors = $this->getColorsFromQuery();
        return (isset($getColors[0]['ID'])) ? $getColors[0]['ID'] : 0;
    }
}