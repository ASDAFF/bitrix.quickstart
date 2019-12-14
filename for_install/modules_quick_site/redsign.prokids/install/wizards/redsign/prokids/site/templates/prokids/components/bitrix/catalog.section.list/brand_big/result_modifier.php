<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(is_array($arResult['SECTIONS']) && count($arResult['SECTIONS'])>0) {
	$size_max_width = 225;
	$size_max_height = 175;
	
	foreach($arResult['SECTIONS'] as $key => $arSection) {
		if(isset($arSection['PICTURE'])) {
			$arResult['SECTIONS'][$key]['PICTURE']['RESIZE'] = CFile::ResizeImageGet($arSection['PICTURE'], array(
				'width' => $size_max_width,
				'height' => $size_max_height
			), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true);
		}
		if( !in_array($arSection['ID'],$arParams['IDS']) ) {
			unset($arResult['SECTIONS'][$key]);
		} elseif( $arParams['FILTER_CONTROL_NAME']!='' ) {
			$arResult['SECTIONS'][$key]['SECTION_PAGE_URL'] = $arSection['SECTION_PAGE_URL'].'?'.$arParams['FILTER_CONTROL_NAME'];
		}
	}
}

// get no photo
$arResult['NO_PHOTO'] = RSDevFunc::GetNoPhoto(array('MAX_WIGHT' => $size_max_width, 'MAX_HEIGHT' => $size_max_height));
// /get no photo