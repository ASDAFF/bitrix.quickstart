<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if( !\Bitrix\Main\Loader::includeModule('redsign.easycart') ) {
	ShowError( GetMessage('RS.EC.ERROR_EC_NOT_INSTALLED') );
	return;
}
if( !\Bitrix\Main\Loader::includeModule('iblock') ) {
	ShowError( GetMessage('RS.EC.ERROR_IBLOCK_NOT_INSTALLED') );
	return;
}
if( !\Bitrix\Main\Loader::includeModule('catalog') ) {
	ShowError( GetMessage('RS.EC.ERROR_CATALOG_NOT_INSTALLED') );
	return;
}
if( !\Bitrix\Main\Loader::includeModule('sale') ) {
	ShowError( GetMessage('RS.EC.ERROR_SALE_NOT_INSTALLED') );
	return;
}

$arParams['SERVICE_URL'] = COption::GetOptionString('redsign.easycart', 'service_url', '');
$arParams['SERVICE_URL'] = str_replace('#SITE_DIR#',SITE_DIR,$arParams['SERVICE_URL']);

$this->IncludeComponentTemplate();