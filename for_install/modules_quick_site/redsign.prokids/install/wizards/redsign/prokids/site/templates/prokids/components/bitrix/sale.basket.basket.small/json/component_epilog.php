<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION,$JSON;
$JSON = array(
	'TYPE' => 'OK',
	'HTMLBYID' => array(
		'basketinfo' => $arResult['NUM_PRODUCTS'].' '.GetMessage('RSGOPRO.SMALLBASKET_TOVAR').$arResult['RIGHT_WORD'].' '.GetMessage('RSGOPRO.SMALLBASKET_NA').' '.$arResult['PRINT_FULL_PRICE'],
	),
);
if($arResult['NUM_PRODUCTS']<1)
{
	$JSON['HTMLBYID']['basketinfo'] = GetMessage('RSGOPRO.SMALLBASKET_PUSTO');
}