<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if (!CModule::IncludeModule('redsign.devfunc')
	|| !CModule::IncludeModule('redsign.flyaway')) {
  return;
}

$max_width_size = 300;
$max_height_size = 300;

if(!empty($arResult)) {
    $params = array(
        'PROP_MORE_PHOTO' => $arParams['RSFLYAWAY_PROP_MORE_PHOTO'],
        'SKU_PROP_MORE_PHOTO' => $arParams['RSFLYAWAY_SKU_PROP_MORE_PHOTO'],
        'MAX_WIDTH' => $max_width_size,
        'MAX_HEIGHT' => $max_height_size,
        'PAGE' => 'detail',
    );
    $arItems = array(0 => &$arResult);
    RSDevFunc::GetDataForProductItem($arItems,$params);
    // /get flyaway data
}

// get no photo
$arResult['NO_PHOTO'] = RSDevFunc::GetNoPhoto(array('MAX_WIDTH'=>$max_width_size,'MAX_HEIGHT'=>$max_height_size));
// /get no photo