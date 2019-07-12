<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

// if ($arResult['SECTION'])
	// $arSections = array($arSection['ID'] => $arResult['SECTION']);
// else

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
		$arFileTmp = CFile::ResizeImageGet(
			$arElement['PICTURE'],
			array("width" => 80, 'height' => 80),
			BX_RESIZE_IMAGE_PROPORTIONAL,
			false
		);
		$arSize = getimagesize($_SERVER["DOCUMENT_ROOT"].$arFileTmp["src"]);

		$arResult['SECTIONS'][$key]['PICTURE_PREVIEW'] = array(
			'SRC' => $arFileTmp["src"],
			'WIDTH' => IntVal($arSize[0]),
			'HEIGHT' => IntVal($arSize[1]),
		);
	}
}

?>