<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
	die();
}

if ($arParams['~AUTH_RESULT']['TYPE'] == 'OK') {
	if ($arResult['LAST_LOGIN']) {
		$userData = CUser::GetByLogin($arResult['LAST_LOGIN'])->Fetch();
		if ($userData) {
			$USER->Authorize($userData['ID']);
		}
	}
	
	if ($arResult['BACKURL']) {
		LocalRedirect($arResult['BACKURL']);
	}
}