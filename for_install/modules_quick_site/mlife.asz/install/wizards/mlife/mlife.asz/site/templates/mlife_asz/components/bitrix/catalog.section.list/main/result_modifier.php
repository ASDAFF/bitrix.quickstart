<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (0 < $arResult['SECTIONS_COUNT'])
{
	$arSelect = array('ID');
	$arSelect[] = 'PICTURE';
	$arSelect[] = 'DESCRIPTION';
	$arSelect[] = 'DESCRIPTION_TYPE';
	$arMap = array();

	foreach ($arResult['SECTIONS'] as $key => $arSection)
	{
		$arMap[$arSection['ID']] = $key;
	}
	$rsSections = CIBlockSection::GetList(array(), array('ID' => array_keys($arMap)), false, $arSelect);
	while ($arSection = $rsSections->GetNext())
	{
		if (!isset($arMap[$arSection['ID']]))
			continue;
		$key = $arMap[$arSection['ID']];
			$arSection['PICTURE'] = intval($arSection['PICTURE']);
			$arSection['PICTURE'] = (0 < $arSection['PICTURE'] ? CFile::GetFileArray($arSection['PICTURE']) : false);
			$arResult['SECTIONS'][$key]['PICTURE'] = $arSection['PICTURE'];
			$arResult['SECTIONS'][$key]['~PICTURE'] = $arSection['~PICTURE'];

			$arResult['SECTIONS'][$key]['DESCRIPTION'] = $arSection['DESCRIPTION'];
			$arResult['SECTIONS'][$key]['~DESCRIPTION'] = $arSection['~DESCRIPTION'];
			$arResult['SECTIONS'][$key]['DESCRIPTION_TYPE'] = $arSection['DESCRIPTION_TYPE'];
			$arResult['SECTIONS'][$key]['~DESCRIPTION_TYPE'] = $arSection['~DESCRIPTION_TYPE'];

	}
	
}
?>