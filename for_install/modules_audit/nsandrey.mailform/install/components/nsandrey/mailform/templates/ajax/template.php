<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

if(sizeof($arResult['ERRORS']) > 0)
{
	echo json_encode(array(
			'FORM_ID' => $arResult['FORM_ID'],
			'TYPE' => 'ERRORS',
			'MESSAGES' => $arResult['ERRORS']
		)
	);
}
else if ($arResult['MESSAGE_SENDED'] == 'Y')
{
	echo json_encode(array(
			'FORM_ID' => $arResult['FORM_ID'],
			'TYPE' => 'OK'
		)
	);
}

?>