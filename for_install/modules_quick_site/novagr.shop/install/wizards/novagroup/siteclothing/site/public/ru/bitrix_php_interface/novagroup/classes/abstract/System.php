<?php
/**
 * Created by JetBrains PhpStorm.
 * User: anton
 * Date: 13.07.13
 * Time: 19:49
 * To change this template use File | Settings | File Templates.
 */

abstract class Novagroup_Classes_Abstract_System extends Novagroup_Classes_Abstract_IBlock
{

    protected $iblockCode = "system", $iblockType = "system", $elementId, $elementCode;

    function __construct($elementId = 0, $elementCode = "")
    {
        $this->elementId = (int)$elementId;
        $this->elementCode = trim($elementCode);
    }

    function getElement()
    {
        $arFilter = array();
        if ($this->elementId > 0) {
            $arFilter['ID'] = $this->elementId;
        }
        if ($this->elementCode !== "") {
            $arFilter['CODE'] = $this->elementCode;
        }
        if (!$arFilter['ID'] and !$arFilter['CODE']) {
            $arFilter['ID'] = -1;
        }
        $arFilter["IBLOCK_TYPE_ID"] = $this->iblockType;
        $arFilter["IBLOCK_CODE"] = $this->iblockCode;

        return $arResult = parent::getElement(null, $arFilter);
    }
}