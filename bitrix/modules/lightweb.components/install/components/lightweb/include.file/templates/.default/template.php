<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();


/** @var array $arParams */
/** @var array $arResult */

$APPLICATION->IncludeFile($arParams['FILE_CONNECTION'],Array(),Array("MODE"=>$arParams['CONTENT_TYPE']));
?>
 