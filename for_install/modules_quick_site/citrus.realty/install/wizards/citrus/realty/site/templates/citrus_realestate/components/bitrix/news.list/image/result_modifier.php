<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arParams['RESIZE_IMAGE_WIDTH'] = intval($arParams['RESIZE_IMAGE_WIDTH']) <= 0 ? 150 : intval($arParams['RESIZE_IMAGE_WIDTH']);
$arParams['RESIZE_IMAGE_HEIGHT'] = intval($arParams['RESIZE_IMAGE_HEIGHT']) <= 0 ? 150 : intval($arParams['RESIZE_IMAGE_HEIGHT']);
$arParams['MORE_PHOTO_PROPERTY'] = strlen($arParams['MORE_PHOTO_PROPERTY']) > 0 ? trim($arParams['MORE_PHOTO_PROPERTY']) : false;
$arParams['SHOW_INCLUDE_AREAS'] = $APPLICATION->GetShowIncludeAreas();

?>