<?
if (is_numeric($arResult["PHOTO"]))
{
	$arFileTmp = Novagroup_Classes_General_Main::MakeResizePicture(
		$arResult["PHOTO"],
		array(
			"WIDTH"		=> (int)$arParams['PHOTO_WIDTH'],
			"HEIGHT"	=> (int)$arParams['PHOTO_HEIGHT']
		)
	);
	$photoPath = $arFileTmp["src"];
} else {
	$photoPath = $arResult["PHOTO"];
}
return $photoPath;
?>