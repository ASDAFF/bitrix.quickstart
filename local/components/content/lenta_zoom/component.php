<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if($arParams["INCLUDE_JQUERY"]=="Y"){
	$APPLICATION->AddHeadScript("/bitrix/components/aprof/lenta_zoom/js/jquery-1.7.min.js");
}
CModule::IncludeModule("fileman");
CMedialib::Init();
$arElements = CMedialibItem::GetList(array("arCollections"=>array($arParams["MEDIA_ID"])));

$max_w = $arParams["SLIDE_WIDTH"];
$max_h = $arParams["SLIDE_HEIGHT"];
if($arParams["MEDIA_SORT_FIELD"]!="DESCRIPTION"&&$arParams["MEDIA_SORT_FIELD"]!="KEYWORDS")
	unset($arParams["MEDIA_SORT_FIELD"]);
if($arParams["MEDIA_SORT_ORDER"]!="DESC")
	$arParams["MEDIA_SORT_ORDER"] = "ASC";
if(strlen($arParams["MEDIA_SORT_FIELD"])>0)
{
	foreach($arElements as $arElement)
	{
		if(strlen($arElement[$arParams["MEDIA_SORT_FIELD"]])>0)
			$sort[$arElement[$arParams["MEDIA_SORT_FIELD"]]] = $arElement;
		else
			$sort[] = $arElement;
	}
	if($arParams["MEDIA_SORT_ORDER"]=="ASC")
		ksort($sort);
	else
		krsort($sort);
	$arElements = $sort;
}
$max_h = 0;
$max_w = 0;
$max_z_w = 0;
$max_z_h = 0;
foreach($arElements as $arElement)
{
	$pic = CFile::ResizeImageGet($arElement,array("width"=>$arParams["SLIDE_WIDTH"],"height"=>$arParams["SLIDE_HEIGHT"]),BX_RESIZE_IMAGE_PROPORTIONAL,true);
	$pic1 = CFile::ResizeImageGet($arElement,array("width"=>$arParams["SLIDE_ZOOM_WIDTH"],"height"=>$arParams["SLIDE_ZOOM_HEIGHT"]),BX_RESIZE_IMAGE_PROPORTIONAL,true);
	$arResult["ELEMENTS"][] = array(
		"PREVIEW_PICTURE"=>array(
			"SRC"=>$pic["src"] ? $pic["src"] : $arElement["PATH"],
			"WIDTH"=>$pic["width"],
			"HEIGHT"=>$pic["height"]
		),
		"DETAIL_PICTURE"=>array(
			"SRC"=>$pic1["src"] ? $pic1["src"] : $arElement["PATH"],
			"WIDTH"=>$pic1["width"],
			"HEIGHT"=>$pic1["height"]
		),
		"DESCRIPTION" => $arElement["DESCRIPTION"]
	);
	if($max_h<$pic["height"])
		$max_h = $pic["height"];
	if($max_w<$pic["width"])
		$max_w = $pic["width"];
	if($max_z_h<$pic1["height"])
		$max_z_h = $pic1["height"];
	if($max_z_w<$pic1["width"])
		$max_z_w = $pic1["width"];
}
$arParams["SLIDE_HEIGHT"] = $max_h;
$arParams["SLIDE_WIDTH"] = $max_w;
$arParams["SLIDE_ZOOM_HEIGHT"] = $max_z_h;
$arParams["SLIDE_ZOOM_WIDTH"] = $max_z_w;
$this->IncludeComponentTemplate();
?>