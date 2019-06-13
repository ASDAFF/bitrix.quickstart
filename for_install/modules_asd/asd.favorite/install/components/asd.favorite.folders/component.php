<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if (!$USER->IsAuthorized() && !$arParams['USER_ID'])
	return;
if (!CModule::IncludeModule('asd.favorite'))
	return;

$arParams['FAV_TYPE'] = trim($arParams['FAV_TYPE']);
$arParams['FOLDER_ID'] = intval($arParams['FOLDER_ID']);
if (!strlen($arParams['FAV_TYPE']))
	$arParams['FAV_TYPE'] = 'unknown';
if ($arParams['MAX_CHARS'] <= 0)
	$arParams['MAX_CHARS'] = 255;
else
	$arParams['MAX_CHARS'] = intval($arParams['MAX_CHARS']);
if ($arParams['USER_ID'] <= 0)
	$arParams['USER_ID'] = $USER->GetID();
if ($arParams['USER_ID'] != $USER->GetID())
	$arParams['ALLOW_EDIT'] = 'N';
else
	$arParams['ALLOW_EDIT'] = 'Y';

$arResult = array('ITEMS' => CASDfavorite::GetFolders($arParams['FAV_TYPE'], $arParams['USER_ID']), 'COUNTS' => array());

$rsLikes = CASDfavorite::GetLikes(array('FOLDER_ID' => array_keys($arResult['ITEMS'])), 'FOLDER_ID');
while ($arLikes = $rsLikes->Fetch())
	$arResult['COUNTS'][$arLikes['FOLDER_ID']] = $arLikes['CNT'];

$this->IncludeComponentTemplate();
?>