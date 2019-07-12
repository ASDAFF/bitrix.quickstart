<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arSections = array();

foreach ($arResult['SECTIONS'] as $key => $arSection)
{
	if ($arSection['IBLOCK_SECTION_ID'] > 0)
		$arSections[$arSection['IBLOCK_SECTION_ID']]['CHILDREN'][$arSection['ID']] = $arSection;
	else
	{
		$arSections[$arSection['ID']] = $arSection;
		$arSection['CHILDREN'] = array();
	}
}
 
$arResult['SECTIONS'] = $arSections;
foreach ($arResult['SECTIONS'] as $key => $arElement)
{
	if(is_array($arElement["PICTURE"]))
	{
		$arFilter = '';
		if($arParams["SHARPEN"] != 0)
		{
			$arFilter = array(array("name" => "sharpen", "precision" => $arParams["SHARPEN"]));
		}
		
		$arFileTmp = CFile::ResizeImageGet(
			$arElement['PICTURE'],
			array("width" => $arParams["DISPLAY_IMG_WIDTH"], "height" => $arParams["DISPLAY_IMG_HEIGHT"]),
			BX_RESIZE_IMAGE_PROPORTIONAL,
			true, $arFilter
		);

		$arResult['SECTIONS'][$key]['PICTURE_PREVIEW'] = array(
			'SRC' => $arFileTmp["src"],
			'WIDTH' => $arFileTmp["width"],
			'HEIGHT' => $arFileTmp["height"],
		);
	}
}

?>