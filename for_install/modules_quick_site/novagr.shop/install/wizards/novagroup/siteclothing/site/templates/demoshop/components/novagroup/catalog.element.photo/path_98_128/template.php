<?
if (is_numeric($arResult["PHOTO"])) {
	$arFileTmp = Novagroup_Classes_General_Main::MakeResizePicture($arResult["PHOTO"], array("WIDTH"=>"98","HEIGHT"=>"128"));
	$photoPath = $arFileTmp["src"];
	
	//$photo = CFile::GetFileArray($arResult["PHOTO"]) ;
    //$photoPath = $photo['SRC'];
} else {
    $photoPath = $arResult["PHOTO"];
}
echo $photoPath;
?>