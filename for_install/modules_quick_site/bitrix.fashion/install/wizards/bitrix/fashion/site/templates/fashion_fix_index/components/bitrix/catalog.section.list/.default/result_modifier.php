<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
foreach($arResult["SECTIONS"] as &$section){
	$pic = false;
	if(!empty($section["PICTURE"])){
		$pic = CFile::ResizeImageGet($section["PICTURE"]["ID"], array('width'=>241, 'height'=>400), BX_RESIZE_IMAGE_PROPORTIONAL, true);
		$section["PICTURE"]["SRC"] = $pic["src"];
		$section["PICTURE"]["WIDTH"] = $pic["width"];
		$section["PICTURE"]["HEIGHT"] = $pic["height"];
	}
}