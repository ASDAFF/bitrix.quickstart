<?
if (is_numeric($arResult["PHOTO"])) {
	$cachedFile = Novagroup_Classes_General_Main::MakeResizePicture($arResult["PHOTO"], array("WIDTH"=>"177","HEIGHT"=>"236"));
	//$cachedFile = Novagroup_Classes_General_Main::reSizeAndCache($arResult["PHOTO"] , array('W' => 177, 'H' => 236), "");
	//deb($cachedFile);
	$photoPath = $cachedFile["src"];

} else {
    $photoPath = $arResult["PHOTO"];
}
echo $photoPath;
?>