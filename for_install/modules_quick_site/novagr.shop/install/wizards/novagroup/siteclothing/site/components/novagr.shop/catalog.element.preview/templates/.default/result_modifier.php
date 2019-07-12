<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

        /**
        * sort color by index
        */

        if(is_array($arResult["CURRENT_ELEMENT"]["COLORS"]))
        {
            $CURRENT_ELEMENT_COLORS = $CURRENT_ELEMENT_COLORS_DATA = $COLORS = array();
            foreach ($arResult["CURRENT_ELEMENT"]["COLORS"] as $key=>$color) {
                $CURRENT_ELEMENT_COLORS[$color] = $arResult["CURRENT_ELEMENT"]["COLORS_SORT"][$key];
                $CURRENT_ELEMENT_COLORS_DATA[$color] = array(
                    "ID"=>$color,
                    "PREVIEW_PICTURE"=>$arResult["CURRENT_ELEMENT"]["COLORS_PREVIEW_PICTURE"][$key],
                    "NAME"=>$arResult["CURRENT_ELEMENT"]["COLORS_NAME"][$key],
                    "SORT"=>$arResult["CURRENT_ELEMENT"]["COLORS_SORT"][$key]
                );
            }
            asort($CURRENT_ELEMENT_COLORS);  

            foreach ($CURRENT_ELEMENT_COLORS as $key=>$color) {
                $COLORS[] = $CURRENT_ELEMENT_COLORS_DATA[$key];
            }                
            $arResult["CURRENT_ELEMENT"]["COLORS"] = $COLORS;

        }
?>