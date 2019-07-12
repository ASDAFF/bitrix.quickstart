<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule('redsign.devfunc'))
	return;

$max_width_size = 225;
$max_height_size = 150;

// get other data
$params = array(
	'MAX_WIDTH' => $max_width_size,
	'MAX_HEIGHT' => $max_height_size,
);
RSDevFunc::GetDataForProductItem($arResult['ITEMS'],$params);
// /get other data

// get no photo
$arResult['NO_PHOTO'] = RSDevFunc::GetNoPhoto(array('MAX_WIDTH'=>$max_width_size,'MAX_HEIGHT'=>$max_height_size));
// /get no photo