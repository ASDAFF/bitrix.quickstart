<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

        /**
        * сортировка цветов по индексу
        */

        if(is_array($arResult["CURRENT_ELEMENT"]["COLORS"]))
        {
            $CURRENT_ELEMENT_COLORS = $CURRENT_ELEMENT_COLORS_DATA = $COLORS = array();
            foreach ($arResult["CURRENT_ELEMENT"]["COLORS"] as $color) {
                $arElementColor = GetIBlockElement($color);
                $CURRENT_ELEMENT_COLORS[$color] = $arElementColor['SORT'];
                $CURRENT_ELEMENT_COLORS_DATA[$color] = $arElementColor;
            }
            asort($CURRENT_ELEMENT_COLORS);  

            foreach ($CURRENT_ELEMENT_COLORS as $key=>$color) {
                $COLORS[] = $CURRENT_ELEMENT_COLORS_DATA[$key];
            }                
            $arResult["CURRENT_ELEMENT"]["COLORS"] = $COLORS;    
        }
?>