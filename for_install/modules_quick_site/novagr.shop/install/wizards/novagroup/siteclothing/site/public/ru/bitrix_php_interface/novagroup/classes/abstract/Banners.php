<?php

abstract class Novagroup_Classes_Abstract_Banners extends Novagroup_Classes_Abstract_IBlock
{
    protected $moduleID, $__IBlockActive = false;
    protected $optionElementName = "main_banners_element_id", $optionIBlockName = "main_banners_i_block_id";
    protected $arSelect = array(
        "ID",
        "NAME",
        "PREVIEW_TEXT",
        "DETAIL_TEXT",
        "PROPERTY_BANNER1",
        "PROPERTY_BANNER2",
        "PROPERTY_BANNER3",
        "PROPERTY_BANNER4",
        "PROPERTY_BANNER5",
        "PROPERTY_BANNER6",
        "PROPERTY_BANNER7",
        "PROPERTY_BANNER8",
        "PROPERTY_BANNER9",
        "PROPERTY_BANNER10",
        "PROPERTY_BANNER11",
        "PROPERTY_BANNER12",
        "PROPERTY_LINK_BANNER1",
        "PROPERTY_LINK_BANNER2",
        "PROPERTY_LINK_BANNER3",
        "PROPERTY_LINK_BANNER4",
        "PROPERTY_LINK_BANNER5",
        "PROPERTY_LINK_BANNER6",
        "PROPERTY_LINK_BANNER7",
        "PROPERTY_LINK_BANNER8",
        "PROPERTY_LINK_BANNER9",
        "PROPERTY_LINK_BANNER10",
        "PROPERTY_LINK_BANNER11",
        "PROPERTY_LINK_BANNER12",
        "PROPERTY_VIEW"
    );

    function __construct($moduleID)
    {
        $this->checkInstalledModule();
        $this->moduleID = $moduleID;
    }

    function setActive($ID, $description = false, $siteID="")
    {
        $el = new CIBlockElement;
        $arFields = Array("ACTIVE" => "Y");
        $el->Update($ID , $arFields);

        COption::SetOptionString($this->moduleID,$this->optionElementName,$ID,$description,$siteID);
    }

    function getActive()
    {
        $getActiveID = COption::GetOptionString($this->moduleID,$this->optionElementName);
        $getList = $this->getList();
        foreach($getList as $item)
        {
            if($item['ID']==$getActiveID)return $item;
        }
        return (is_array($getList[0])) ? $getList[0] : false;
    }

    function getList()
    {
        if($getIBlockActive = $this->getIBlockActive())
        {
            $arFilter = array("IBLOCK_ID"=>$getIBlockActive['ID']);
            return parent::getElementList(array(),$arFilter,false,false,$this->arSelect);
        }
        return array();
    }

    function __setIBlockActive($ID)
    {
        $this->__IBlockActive = $ID;
    }

    function setIBlockActive($ID, $description = false, $siteID="")
    {
        $this->__setIBlockActive($ID);
        COption::SetOptionString($this->moduleID,$this->optionIBlockName,$ID,$description,$siteID);
    }

    function getIBlockActive()
    {
        $getActiveID = ($this->__IBlockActive===false) ? COption::GetOptionString($this->moduleID,$this->optionIBlockName) : $this->__IBlockActive;
        $getList = $this->getIBlockList();
        foreach($getList as $item)
        {
            if($item['ID']==$getActiveID)return $item;
        }
        return (is_array($getList[0])) ? $getList[0] : false;
    }

    function getIBlockList()
    {
        $IBlockList = array();
        $res = CIBlock::GetList(
            Array(),
            Array(
                'CODE'=>'banners',
                'TYPE'=>'banners',
            )
        );
        while($ar_res = $res->Fetch())
        {
            $IBlockList[] = $ar_res;
        }
        return $IBlockList;
    }
}