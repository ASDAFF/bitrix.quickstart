<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

        /**
        * сортировка цветов по индексу
        */

        if(is_array($arResult["CURRENT_ELEMENT"]["COLORS"]))
        {
            $arSelect = Array("ID", "NAME", "IBLOCK_ID","SORT","PROPERTY_class_stone_color");
            $arFilter = Array("ID"=>$arResult["CURRENT_ELEMENT"]["COLORS"]);
            $res = CIBlockElement::GetList(Array('sort'=>'asc'), $arFilter, false, Array("nPageSize"=>50), $arSelect);

            $CURRENT_ELEMENT_COLORS = $CURRENT_ELEMENT_COLORS_DATA = $COLORS = array();
            while($ob = $res->GetNextElement())
            {
                $arFields = $ob->GetFields();
                $CURRENT_ELEMENT_COLORS_DATA[] = $arFields;
            }

            $arResult["CURRENT_ELEMENT"]["COLORS"] = $CURRENT_ELEMENT_COLORS_DATA;
        }
?>