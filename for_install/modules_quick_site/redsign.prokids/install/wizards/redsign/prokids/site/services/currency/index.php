<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true)
	die();

if(!CModule::IncludeModule('catalog'))
	return;

$CURRENCY_CODE = 'RUB';
$LANG_CODE = 'ru';

$arFields = array(
   'CURRENCY' => 'RUB',
   'AMOUNT' => 1,
   'AMOUNT_CNT' => 1,
   'SORT' => 0
);

if(CCurrency::GetByID($CURRENCY_CODE)){
   CCurrency::Update($arFields);
}
else{
	CCurrency::Add($arFields);
}

$arFields = array(
	'FULL_NAME' => GetMessage('ALFA_CURRENCY_RU_NAME'),
	'FORMAT_STRING' => GetMessage('ALFA_CURRENCY_RU_FORMAT'),
	'DEC_POINT' => '.',
	'THOUSANDS_SEP' => ' ',
	'DECIMALS' => 0,
	'CURRENCY' => 'RUB',
	'LID' => 'ru'
);
if(CCurrencyLang::GetByID($CURRENCY_CODE, $LANG_CODE)){
	CCurrencyLang::Update($CURRENCY_CODE, $LANG_CODE, $arFields);
}
else{
	CCurrencyLang::Add($arFields);
}