<?
if (is_numeric($arResult["PHOTO"])) {
    $photo = CFile::GetFileArray($arResult["PHOTO"]) ;
    $photoPath = $photo['SRC'];
} else {
    $photoPath = $arResult["PHOTO"];
}
echo $photoPath;
?>