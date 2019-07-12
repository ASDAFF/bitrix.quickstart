<?
if(is_numeric($arResult["PHOTO"]))
{
    echo CFile::ShowImage(
        $arResult["PHOTO"],
        (int)$arParams["PHOTO_WIDTH"],
        (int)$arParams["PHOTO_HEIGHT"],
        "alt=''",
        "",
        false,
        "",
        (int)$arParams["PHOTO_WIDTH"],
        (int)$arParams["PHOTO_HEIGHT"]
    );
}

?>