<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arViewModeList = array('LINE', 'TEXT', 'TILE');

if (!array_key_exists('VIEW_MODE', $arParams))
	$arParams['VIEW_MODE'] = 'LINE';
if (!in_array($arParams['VIEW_MODE'], $arViewModeList))
	$arParams['VIEW_MODE'] = 'LINE';

if (!array_key_exists('SHOW_PARENT_NAME', $arParams))
	$arParams['SHOW_PARENT_NAME'] = 'Y';
if ('N' != $arParams['SHOW_PARENT_NAME'])
	$arParams['SHOW_PARENT_NAME'] = 'Y';


$ufRes = CIBlockSection::GetList(Array(SORT=>"ASC"), $arFilter = Array("IBLOCK_ID"=>$arParams["IBLOCK_ID"], "ID"=>$arResult["SECTION"]["PATH"][0]["ID"]), true,$arSelect=Array("UF_CERTS")); 

while($certsAr = $ufRes->GetNext()){
    foreach ($certsAr['UF_CERTS'] as $cert) {
        $arResult['SECTION']['PATH'][0]['CERTS'][] = CFile::GetFileArray($cert);
    }
}
 
/*Добавляем в список свойств пользовательское поле 'UF_ADD_DESC' */
$arUserPropertySection = CIBlockSection::GetList(
        Array("SORT"=>"ASC"),
        Array('ID'=>$arResult['SECTION']['ID'], 'IBLOCK_ID'=>$arResult['SECTION']['IBLOCK_ID']),
        false,
        Array('ID','NAME','UF_ADD_DESC'),
        false
    );
while($UserPropertySection = $arUserPropertySection->GetNext())
{
    $arResult['SECTION']['ADD_DESCRIPTION'] = htmlspecialchars_decode($UserPropertySection['UF_ADD_DESC']);
}
    

if (0 < $arResult['SECTIONS_COUNT'])
{
	$boolPicture = false;
	$boolDescr = false;
	$arSelect = array('ID');
	$arMap = array();
	if ('LINE' == $arParams['VIEW_MODE'] || 'TILE' == $arParams['VIEW_MODE'])
	{
		$arCurrent = current($arResult['SECTIONS']);
		if (!array_key_exists('PICTURE', $arCurrent))
		{
			$boolPicture = true;
			$arSelect[] = 'PICTURE';
		}
		if ('LINE' == $arParams['VIEW_MODE'] && !array_key_exists('DESCRIPTION', $arCurrent))
		{
			$boolDescr = true;
			$arSelect[] = 'DESCRIPTION';
			$arSelect[] = 'DESCRIPTION_TYPE';
		}
	}
	if ($boolPicture || $boolDescr)
	{
		foreach ($arResult['SECTIONS'] as $key => $arSection)
		{
			$arMap[$arSection['ID']] = $key;
		}

		$rsSections = CIBlockSection::GetList(array(), array('ID' => array_keys($arMap)), false, $arSelect);
		while ($arSection = $rsSections->GetNext())
		{
			$key = $arMap[$arSection['ID']];
			if ($boolPicture)
			{
				$arSection['PICTURE'] = intval($arSection['PICTURE']);
				$arSection['PICTURE'] = (0 < $arSection['PICTURE'] ? CFile::GetFileArray($arSection['PICTURE']) : false);
				$arResult['SECTIONS'][$key]['PICTURE'] = $arSection['PICTURE'];
				$arResult['SECTIONS'][$key]['~PICTURE'] = $arSection['~PICTURE'];
			}
			if ($boolDescr)
			{
				$arResult['SECTIONS'][$key]['DESCRIPTION'] = $arSection['DESCRIPTION'];
				$arResult['SECTIONS'][$key]['~DESCRIPTION'] = $arSection['~DESCRIPTION'];
				$arResult['SECTIONS'][$key]['DESCRIPTION_TYPE'] = $arSection['DESCRIPTION_TYPE'];
				$arResult['SECTIONS'][$key]['~DESCRIPTION_TYPE'] = $arSection['~DESCRIPTION_TYPE'];
			}
		}
	}
}
?>