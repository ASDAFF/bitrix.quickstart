<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

// get all colors, materials in special array $arResult['mixData']

if (isset($arParams['VENDOR_IBLOCK_ID']))
	$arFilter = array('ACTIVE' => "Y", 'IBLOCK_ID' => $arParams['VENDOR_IBLOCK_ID']);
else
	$arFilter = array(
		'ACTIVE' => "Y",
		'IBLOCK_CODE' => array(
				$arParams['BRANDNAME_IBLOCK_CODE'],
				$arParams['STYLE_IBLOCK_CODE'],
				$arParams['STONE_IBLOCK_CODE'],
				$arParams['STD_SIZES_IBLOCK_CODE'],
				$arParams['COLORS_IBLOCK_CODE'],
				$arParams['METAL_IBLOCK_CODE'],
				$arParams['METAL_COLOR_IBLOCK_CODE'],
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
    if($data['IBLOCK_CODE']==$arParams['STD_SIZES_IBLOCK_CODE'])
    {
        $STD_SIZE_DEFAULT = $data;
        $STD_SIZE_DEFAULT['ID'] = $STD_SIZE_DEFAULT['~ID'] = -1;
        $STD_SIZE_DEFAULT['NAME'] = $STD_SIZE_DEFAULT['~NAME'] = GetMessage("NO");
        $STD_SIZE_DEFAULT['SORT'] = "10000";
    }

	$arResult['mixData'][ $data['ID'] ] = $data;
	//$arResult[ $data['IBLOCK_CODE'] ][ $data['ID'] ] = $data;
	
	// а заодно соберём ID картинок цветов и их PREVIEW_PICTURE
	if ($data['IBLOCK_CODE'] == $arParams['COLORS_IBLOCK_CODE'])
	{
		//deb($data['PREVIEW_PICTURE']);
		//$PREVIEW_PICTURE_ID[$data['ID']] =  $data['PREVIEW_PICTURE'];
        $colorsPreviewPictures[$data['ID']] =  $data['PREVIEW_PICTURE'];
	}
}
$arResult['mixData'][ -1 ] = $STD_SIZE_DEFAULT;

//deb($PREVIEW_PICTURE_ID);
?>