<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION,$JSON;

$JSON = array(
	'TYPE' => 'OK',
	'HTMLBYID' => array(
		'comparelist' => '<div class="comparelistinner"><div class="title">'.GetMessage('CATALOG_IN_COMPARE').':</div><a href="'.$arParams["COMPARE_URL"].'">'.$arResult['COMPARE_CNT'].' '.GetMessage('CATALOG_COMPARE_PRODUCT').$arResult["RIGHT_WORD"].'</a></div>',
	),
);
if($arResult['COMPARE_CNT']<1)
{
	$JSON['HTMLBYID']['comparelist'] = '';
}