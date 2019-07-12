<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$cp = $this->__component;
if(is_object($cp))
{
    foreach($arResult["ITEMS"] as $i=>$arItem)
    {
        if($arItem["PROPERTIES"]["ID_RECORD"]["VALUE"] != "" && is_numeric($arItem["PROPERTIES"]["ID_RECORD"]["VALUE"]))
        {
            $cp->arResult["ITEMS"][$i]["DETAIL_PAGE_URL"] = str_replace("#ID_RECORD#", $arItem["PROPERTIES"]["ID_RECORD"]["VALUE"], $arItem["DETAIL_PAGE_URL"]);
            $arResult["ITEMS"][$i] = $cp->arResult["ITEMS"][$i];
        }
    }
}
?>