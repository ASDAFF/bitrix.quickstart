<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(isset($arResult['SECTION']) && $arResult['SECTION']['DESCRIPTION']!='')
{
	$arSection = $arResult['SECTION'];
	$mxPicture = false;
	$arSection['PICTURE'] = intval($arSection['PICTURE']);
	if (0 < $arSection['PICTURE'])
		$mxPicture = CFile::GetFileArray($arSection['PICTURE']);
	$arSection['PICTURE'] = $mxPicture;
	if ($arSection['PICTURE'])
	{
		$arSection['PICTURE']['ALT'] = $arSection['IPROPERTY_VALUES']['SECTION_PICTURE_FILE_ALT'];
		if ($arSection['PICTURE']['ALT'] == '')
			$arSection['PICTURE']['ALT'] = $arSection['NAME'];
		$arSection['PICTURE']['TITLE'] = $arSection['IPROPERTY_VALUES']['SECTION_PICTURE_FILE_TITLE'];
		if ($arSection['PICTURE']['TITLE'] == '')
			$arSection['PICTURE']['TITLE'] = $arSection['NAME'];
	}
	$arResult['SECTION'] = $arSection;
}


if (isset($arParams['FILTER_IDS'])) {
    $arParams['FILTER_IDS'] = is_array($arParams['FILTER_IDS']) ? $arParams['FILTER_IDS'] : array();
    
    $prevLevel = -1;
    for ($i = $arResult['SECTIONS_COUNT'] - 1; $i >= 0; --$i) {

        if (in_array($arResult['SECTIONS'][$i]['ID'], $arParams['FILTER_IDS'])) {

            $prevLevel = $arResult['SECTIONS'][$i]['DEPTH_LEVEL'];
            
            if ($arResult['SECTIONS'][$i]['DEPTH_LEVEL'] != $arResult['SECTION']['DEPTH_LEVEL'] + 1) {
                unset ($arResult['SECTIONS'][$i]);
            }

        } else {

            if ($prevLevel != -1 && $prevLevel > $arResult['SECTIONS'][$i]['DEPTH_LEVEL']) {
                $prevLevel = $arResult['SECTIONS'][$i]['DEPTH_LEVEL'];
                if ($arResult['SECTIONS'][$i]['DEPTH_LEVEL'] != $arResult['SECTION']['DEPTH_LEVEL'] + 1) {
                    unset ($arResult['SECTIONS'][$i]);
                }
                
            } elseif ($prevLevel == $arResult['SECTIONS'][$i]['DEPTH_LEVEL']) {
                $prevLevel = $arResult['SECTIONS'][$i]['DEPTH_LEVEL'];
                unset($arResult['SECTIONS'][$i]);
                
            } else {
                unset($arResult['SECTIONS'][$i]);
                if ($arResult['SECTIONS'][$i]['DEPTH_LEVEL'] == $arResult['SECTION']['DEPTH_LEVEL'] + 1) {
                    $prevLevel = -1;
                }
            }

        }

    }
    $arResult['SECTIONS'] = array_values($arResult['SECTIONS']);
}