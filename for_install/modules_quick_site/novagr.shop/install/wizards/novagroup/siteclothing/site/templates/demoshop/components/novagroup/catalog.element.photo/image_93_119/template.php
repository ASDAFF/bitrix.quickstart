<?
if (is_numeric($arResult["PHOTO"])) {
	$arFileTmp = Novagroup_Classes_General_Main::MakeResizePicture($arResult["PHOTO"], array("WIDTH"=>"93","HEIGHT"=>"119"));
    $photoPath = $arFileTmp["src"];
} else {
    $photoPath = $arResult["PHOTO"];
}
    echo CFile::ShowImage(
        $photoPath,
        (int)$arParams["PHOTO_WIDTH"], 
        (int)$arParams["PHOTO_HEIGHT"], 
        "alt='".htmlspecialchars($arResult["ELEMENT"]['NAME'],null,SITE_CHARSET)."'",
        "",
        false,
        "",
        (int)$arParams["PHOTO_WIDTH"], 
        (int)$arParams["PHOTO_HEIGHT"] 
    );
?>