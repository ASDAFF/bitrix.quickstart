<?

use \Bitrix\Main\Loader;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

/**
 * Bitrix vars
 *
 * @var CBitrixComponent         $component
 * @var CBitrixComponentTemplate $this
 * @var array                    $arParams
 * @var array                    $arResult
 * @var array                    $arLangMessages
 * @var array                    $templateData
 *
 * @var string                   $templateFile
 * @var string                   $templateFolder
 * @var string                   $parentTemplateFolder
 * @var string                   $templateName
 * @var string                   $componentPath
 *
 * @var CDatabase                $DB
 * @var CUser                    $USER
 * @var CMain                    $APPLICATION
 */

if(Loader::includeModule('api.auth')) {
	$arUserField = $arParams['arUserField'];
	if($arUserField['MULTIPLE'] === 'Y') {
		$arUserField['FIELD_NAME'] = str_replace('[]', '', $arUserField['FIELD_NAME']);
	}

	echo \Api\Auth\UserType\Location::getPublicEdit($arUserField);
}
?>