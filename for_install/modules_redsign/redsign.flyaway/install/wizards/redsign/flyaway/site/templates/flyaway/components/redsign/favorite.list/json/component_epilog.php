<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)die();

global $APPLICATION,$JSON;

$JSON = array(
	'TYPE' => 'OK',
	'HTMLBYID' => array(
		'favorinfo' => '<span class="count">'.$arResult['COUNT'].'</span> <span class="hidden">'.GetMessage('RS.MONOPOLY.PRODUCTS').$arResult["RIGHT_WORD"].'</span>'
	),
);