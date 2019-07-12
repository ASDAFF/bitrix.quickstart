<?php
/**
 * Created by JetBrains PhpStorm.
 * User: anton
 * Date: 13.07.13
 * Time: 19:49
 * To change this template use File | Settings | File Templates.
 */

abstract class Novagroup_Classes_Abstract_CatalogSection extends Novagroup_Classes_Abstract_Catalog
{

    protected $iBlockId, $sectionId, $sectionCode;

    function __construct($iBlockId, $sectionId = 0, $sectionCode = "")
    {
        $this->iBlockId = (int)$iBlockId;
        $this->sectionId = (int)$sectionId;
        $this->sectionCode = trim($sectionCode);
    }

    function getSection()
    {
        if ($this->iBlockId > 0) {
            $arFilter = array();
            if ($this->iBlockId <> "") {
                $arFilter['IBLOCK_ID'] = $this->iBlockId;
            }
            if ($this->sectionId > 0) {
                $arFilter['ID'] = $this->sectionId;
            }
            if ($this->sectionCode !== "") {
                $arFilter['CODE'] = $this->sectionCode;
            }
            if (!$arFilter['ID'] and !$arFilter['CODE']) {
                $arFilter['ID'] = -1;
            }

            return $arResult = parent::getSection(Array(), $arFilter, false, array("UF_*"));
        }
        return array();
    }

    function setPageProperties()
    {
        /**
         * get section properties
         */
        parent::setPageProperties();
        $arSection = $this->getSection();

        /**
         * get seo templates
         */
        $rsSeoData = new \Bitrix\Iblock\InheritedProperty\SectionValues($this->iBlockId, $arSection['ID']);
        $arResult["IPROPERTY_VALUES"] = $rsSeoData->getValues();

        /**
         * find and set title
         */
        if (trim($arResult["IPROPERTY_VALUES"]["SECTION_META_TITLE"]) <> "") {
            $browserTitle = $arResult["IPROPERTY_VALUES"]["SECTION_META_TITLE"];
        } elseif (trim($arSection["UF_BROWSER_TITLE"]) <> "") {
            $browserTitle = $arSection["UF_BROWSER_TITLE"];
        } else {
            $browserTitle = $arSection["NAME"];
        }
        Novagroup_Classes_General_Main::setTitle($browserTitle);

        /**
         * find and set keywords
         */
        if (trim($arResult["IPROPERTY_VALUES"]["SECTION_META_KEYWORDS"]) <> "") {
            $metaKeywords = $arResult["IPROPERTY_VALUES"]["SECTION_META_KEYWORDS"];
        } elseif (trim($arSection["UF_KEYWORDS"]) <> "") {
            $metaKeywords = $arSection["UF_KEYWORDS"];
        } else {
            $metaKeywords = "";
        }
        Novagroup_Classes_General_Main::setKeywords($metaKeywords);

        /**
         * find and set description
         */
        if (trim($arResult["IPROPERTY_VALUES"]["SECTION_META_DESCRIPTION"]) <> "") {
            $metaDescription = $arResult["IPROPERTY_VALUES"]["SECTION_META_DESCRIPTION"];
        } elseif (trim($arSection["UF_META_DESCRIPTION"]) <> "") {
            $metaDescription = $arSection["UF_META_DESCRIPTION"];
        } else {
            $metaDescription = "";
        }
        Novagroup_Classes_General_Main::setDescription($metaDescription);

    }

    function addChainItems()
    {
        parent::addChainItems();

        $arSection = $this->getSection();
        if (count($arSection) > 0) {
            // построем путь до текущей секции
            $rsSection = CIBlockSection::GetNavChain(
                $arSection['IBLOCK_ID'],
                $arSection['ID']
            );
            while ($arSection = $rsSection->Fetch()) {
                $URL = parent::getCatalogPath() . $arSection['CODE'] . "/";

                if ($arSection["DEPTH_LEVEL"] > 2) {
                    Novagroup_Classes_General_Main::AddChainItem($arSection['NAME'], $URL);
                } else {
                    Novagroup_Classes_General_Main::AddChainItem($arSection['NAME']);
                }
            }
        }
    }
}