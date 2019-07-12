<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if (!\Bitrix\Main\Loader::includeModule("citrus.realty"))
	return;

$arResult = array(
	'COUNT' => \Citrus\Realty\Favourites::getCount(),
	'LIST' => \Citrus\Realty\Favourites::getList(),
);

$this->IncludeComponentTemplate();

