<?php
/**
 * Created by JetBrains PhpStorm.
 * User: anton
 * Date: 13.07.13
 * Time: 19:49
 * To change this template use File | Settings | File Templates.
 */

abstract class Novagroup_Classes_Abstract_Seo
{

    protected $iblockId, $sectionId, $sectionCode, $arrForCache, $getSection;

    function __construct($iblockId ,$sectionId = 0, $sectionCode = "")
    {
        $this->arrForCache = Novagroup_Classes_General_Main::getSeoArr();
        $this->iblockId = (int)$iblockId;
        $this->sectionId = (int)$sectionId;
        $this->sectionCode = trim($sectionCode);

        $section = new Novagroup_Classes_General_CatalogSection($this->iblockId, $this->sectionId, $this->sectionCode);

        $this->getSection = $section->getSection();
    }

    function getDescription()
    {
        $arResult = $this->getSection;
        $arrForCache = $this->arrForCache;

        if (trim($arrForCache["seoArr"]['DETAIL_TEXT'])<>"") {
            return $arrForCache["seoArr"]['DETAIL_TEXT'];
        } elseif (trim($arResult["DESCRIPTION"]) <> "") {
            return $arResult["DESCRIPTION"];
        } else {
            if($this->sectionCode=="")
            {
                $system = new Novagroup_Classes_General_System(0, "catalog-root");
                $arResult = $system->getElement();
                return $arResult["DETAIL_TEXT"];
            }
        }
    }

    function getHeader()
    {
        $arResult = $this->getSection;
        $arrForCache = $this->arrForCache;

        if (trim($arrForCache["seoArr"]['DETAIL_TEXT'])<>"") {
            return null;
        } elseif (trim($arResult["DESCRIPTION"]) <> "") {
            if (trim($arResult['UF_TITLE_H1']) <> "" && trim($arResult["DESCRIPTION"]) <> "") {
                return $arResult['UF_TITLE_H1'];
            }
        }
    }
}