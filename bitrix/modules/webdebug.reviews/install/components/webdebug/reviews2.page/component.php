<?
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule('webdebug.reviews')) {
	return;
}

if (!in_array($arParams['JS'],array('none','all','raty'))) {
	$arParams['JS'] = 'all';
}

if ($arParams['JS']=='all') {
	CWD_Reviews2::InitJQuery(true);
} elseif ($arParams['JS']=='raty') {
	CWD_Reviews2::InitJQuery(false);
}

$this->IncludeComponentTemplate();
?>