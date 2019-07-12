<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

// get all colors, materials in special array $arResult['mixData']

if (isset($arParams['VENDOR_IBLOCK_ID']))
	$arFilter = array('ACTIVE' => "Y", 'IBLOCK_ID' => $arParams['VENDOR_IBLOCK_ID']);
else
	$arFilter = array(
		'ACTIVE' => "Y",
		'IBLOCK_CODE' => array(
				$arParams['BRANDNAME_IBLOCK_CODE'],
				$arParams['MATERIALS_IBLOCK_CODE'],
				$arParams['SAMPLES_IBLOCK_CODE'],
				$arParams['STD_SIZES_IBLOCK_CODE'],
				$arParams['COLORS_IBLOCK_CODE'],
		)
);

$arSelect = array(
	'ID',
	'NAME',
	'CODE',
	'IBLOCK_CODE',
	'PREVIEW_PICTURE',
	'DETAIL_TEXT',
	'SORT'	
);

$rsElement = CIBlockElement::GetList(false, $arFilter, false, false, $arSelect);
while($data = $rsElement -> GetNext())
{
    $arResult['mixData'][ $data['ID'] ] = $data;
	//$arResult[ $data['IBLOCK_CODE'] ][ $data['ID'] ] = $data;
	
	// get id of colors and their PREVIEW_PICTURE
	if ($data['IBLOCK_CODE'] == $arParams['COLORS_IBLOCK_CODE'])
	{
		$colorsPreviewPictures[$data['ID']] =  $data['PREVIEW_PICTURE'];
	}
}
?>