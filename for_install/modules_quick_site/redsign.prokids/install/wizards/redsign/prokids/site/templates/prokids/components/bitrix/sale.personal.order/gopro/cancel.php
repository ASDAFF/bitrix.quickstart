<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

?><?$APPLICATION->IncludeComponent(
	'bitrix:sale.personal.order.cancel',
	'gopro',
	array(
		'PATH_TO_LIST' => $arResult['PATH_TO_LIST'],
		'PATH_TO_DETAIL' => $arResult['PATH_TO_DETAIL'],
		'SET_TITLE' =>$arParams['SET_TITLE'],
		'ID' => $arResult['VARIABLES']['ID'],
	),
	$component,
	array('HIDE_ICONS'=>'Y')
);?>