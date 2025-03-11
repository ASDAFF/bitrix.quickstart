<?
//ARTNUMBER
if(strlen($arResult["query"])>0)
{
    $MAX = $arParams["TOP_COUNT"];
    $CURRENT_COUNT = $MAX - count($arResult["SEARCH"]);
    $arResult["query"] = htmlspecialcharsbx($arResult["query"]);
    $dbRes = CIBlockElement::GetList(array(),array("ACTIVE"=>"Y","PROPERTY_ARTNUMBER"=>$arResult["query"]."%"),false,array("nPageSize"=>$CURRENT_COUNT),
        array("ID","IBLOCK_ID","NAME","DETAIL_PAGE_URL","DETAIL_PICTURE","PREVIEW_PICTURE"));
    while($arRes = $dbRes->GetNext())
    {
        if(!empty($arRes["PREVIEW_PICTURE"]))
        {
            $arRes["ICON"] = CFile::ResizeImageGet($arRes["PREVIEW_PICTURE"],$arResize,BX_RESIZE_IMAGE_PROPORTIONAL);
        }
        elseif(!empty($arRes["DETAIL_PICTURE"]))
        {
            $arRes["ICON"] = CFile::ResizeImageGet($arRes["DETAIL_PICTURE"],$arResize,BX_RESIZE_IMAGE_PROPORTIONAL);
        }
        $arResult["SEARCH"][]=array(
            "PARAM2"=>$arRes["IBLOCK_ID"],
            "ITEM_ID"=>$arRes["ID"],
            'MODULE_ID' => 'iblock',
            'PARAM1' => 'catalog',
            'NAME' => $arRes["NAME"],
            'URL' => $arRes["DETAIL_PAGE_URL"],
            "ICON"=>$arRes["ICON"]
        );

    }
}
?>
