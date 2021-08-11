<?php
/**
 * Copyright (c) 11/8/2021 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

namespace Helper\Content;


class CSection
{
    /**
     * Список элементов инфоблока с названиями разделов в качестве заголовков
     *
     * @param $eltype
     */
    function MakeElementsTree($eltype){
        global $APPLICATION;
        if(!CModule::IncludeModule("iblock")){
            echo "не подключается модуль инфоблоки";
        }

        // Идентификатор раздела
//        $eltype = 27;


        if (!empty($eltype))
        {

            //ID инфоблока
            $res = \CIBlockSection::GetByID($eltype);
            if($ar_res = $res->GetNext())
            {
                $parentIBlockID = $ar_res['IBLOCK_ID'];
                $RootSectionName = $ar_res['NAME'];
            }

            $arFilterRoot = array(
                "IBLOCK_ID" => $parentIBlockID,
                "SECTION_ID" => $eltype,
            );

            // массив для хранения корневых элементов
            $ar_rootElements = array();
            $ar_rootElements["NAME"] = $RootSectionName;
            $rootRes = \CIBlockElement::GetList(Array(), $arFilterRoot, false);
            while($rootOb = $rootRes->GetNextElement())
            {
                $RootarFields = $rootOb->GetFields();

                $arRootSelFlds["NAME"] = $RootarFields["NAME"];
                $arRootSelFlds["PREVIEW_TEXT"] = $RootarFields["PREVIEW_TEXT"];
                $arRootSelFlds["DETAIL_PAGE_URL"] = $RootarFields["DETAIL_PAGE_URL"];
                $arRootSelFlds["DETAIL_TEXT_SIZE"] = strlen($RootarFields["DETAIL_TEXT"]);

                $ar_rootElements["ITEMS"][] = $arRootSelFlds;
            }

            $arFilter=array(
                "IBLOCK_ID" => $parentIBlockID,
                "SECTION_ID" => $eltype,
            );

            $ar_result=Array();

            $arProj = \CIBlockSection::GetList(array("SORT"=>"ASC"),$arFilter,false);

            while($projRes = $arProj->GetNextElement())
            {
                $arFields = $projRes->GetFields();

                $ar_result[$arFields["ID"]]["NAME"] = $arFields["NAME"];
            }

            foreach($ar_result as $arrkey => $arrvalue){
                $arProjElem = \CIBlockElement::GetList(array(),array("SECTION_ID"=>$arrkey),false);
                while($projResElem = $arProjElem->GetNextElement())
                {
                    $arElemFields = $projResElem->GetFields();

                    $arSelFlds["NAME"] = $arElemFields["NAME"];
                    $arSelFlds["PREVIEW_TEXT"] = $arElemFields["PREVIEW_TEXT"];
                    $arSelFlds["DETAIL_PAGE_URL"] = $arElemFields["DETAIL_PAGE_URL"];
                    $arSelFlds["DETAIL_TEXT_SIZE"] = strlen($arElemFields["DETAIL_TEXT"]);


                    $ar_result[$arrkey]["ITEMS"][] = $arSelFlds;
                }
            }




            if(isset($ar_rootElements["ITEMS"]) && count($ar_rootElements["ITEMS"]) > 0){
                echo "<h4>".$ar_rootElements["NAME"]."</h4>";
                echo "<ul style=\"margin-bottom:10px;\">";

                foreach($ar_rootElements["ITEMS"] as $ar_rootItem){
                    echo "<li class=\"gvert\">";
                    if($ar_rootItem["DETAIL_TEXT_SIZE"] > 0)
                    {
                        echo "<a href=\"".$ar_rootItem["DETAIL_PAGE_URL"]."\" style=\"font-weight:bold;\" >".$ar_rootItem["NAME"]."</a><br />";
                    }
                    else
                    {
                        echo "<span style=\"font-weight:bold;\">".$ar_rootItem["NAME"]."</span><br />";
                    }

                    if(strlen($ar_rootItem["PREVIEW_TEXT"]) > 0){
                        echo "<span>".$ar_rootItem["PREVIEW_TEXT"]."</span>";
                    }
                    echo "</li>";

                }
                echo "</ul>";
            }

            if(count($ar_rootElements["ITEMS"]) > 0 ){
                echo "<div style=\"margin-left:45px;\">";
            }

            foreach($ar_result as $key => $arrValues)
            {
                echo "<h4>".$arrValues["NAME"]."</h4>";
                if(is_array($arrValues["ITEMS"]) && count($arrValues["ITEMS"]) > 0)
                {
                    echo "<ul style=\"margin-bottom:10px;\">";

                    foreach ($arrValues["ITEMS"] as $arrItem)
                    {
                        echo "<li class=\"gvert\">";

                        if($arrItem["DETAIL_TEXT_SIZE"] > 0)
                        {
                            echo "<a href=\"".$arrItem["DETAIL_PAGE_URL"]."\" style=\"font-weight:bold;\" >".$arrItem["NAME"]."</a><br />";
                        }
                        else
                        {
                            echo "<span style=\"font-weight:bold;\">".$arrItem["NAME"]."</span><br />";
                        }

                        if(strlen($arrItem["PREVIEW_TEXT"]) > 0){
                            echo "<span>".$arrItem["PREVIEW_TEXT"]."</span>";
                        }
                        echo "</li>";
                    }

                    echo "</ul>";

                }

            }
            if(count($ar_rootElements["ITEMS"]) > 0 ){
                echo "</div>";
            }
        }
        else{
            showError("В свойствах страницы не указан ID раздела с элементами");
        }

    } //end MakeElementsTree
}