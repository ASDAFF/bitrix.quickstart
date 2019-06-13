<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

/**
 * Bitrix vars
 *
 * @var CBitrixComponent $this
 * @var array            $arParams
 * @var array            $arResult
 * @var string           $componentPath
 * @var string           $componentName
 * @var string           $componentTemplate
 *
 * @var CDatabase        $DB
 * @var CUser            $USER
 * @var CMain            $APPLICATION
 *
 */


if(!CModule::IncludeModule("api.yashare")) {
	ShowError(GetMessage("YASHARE_MODULE_NOT_INSTALLED"));
	return;
}


///////////////////////////////////////////////////////////////
//                      PARAMETERS
///////////////////////////////////////////////////////////////
$arParams['USE_JQUERY']      = $arParams['USE_JQUERY'] == 'Y';
$arParams['LANG']            = ($arParams['LANG'] ? $arParams['LANG'] : 'ru');
$arParams['TYPE']            = ($arParams['TYPE'] ? $arParams['TYPE'] : 'counter');
$arParams['COPY']            = ($arParams['COPY'] ? $arParams['COPY'] : 'first');
$arParams['POPUP_DIRECTION'] = ($arParams['POPUP_DIRECTION'] ? $arParams['POPUP_DIRECTION'] : 'bottom');
$arParams['POPUP_POSITION']  = ($arParams['POPUP_POSITION'] ? $arParams['POPUP_POSITION'] : 'inner');
$arParams['SIZE']            = ($arParams['SIZE'] == 's' ? 's' : 'm');
$arParams['LIMIT']           = (intval($arParams['LIMIT']) > 0 ? intval($arParams['LIMIT']) : 5);

foreach($arParams['QUICKSERVICES'] as $k => $v)
	if($v === "")
		unset($arParams['QUICKSERVICES'][ $k ]);


$arParams['UNUSED_CSS'] = $arParams['UNUSED_CSS'] == 'Y';

//data-attr
$arParams['DATA_TITLE']       = trim($arParams['DATA_TITLE']);
$arParams['DATA_URL']         = trim($arParams['DATA_URL']);
$arParams['DATA_IMAGE']       = trim($arParams['DATA_IMAGE']);
$arParams['DATA_DESCRIPTION'] = htmlspecialcharsEx(strip_tags(htmlspecialcharsback($arParams['DATA_DESCRIPTION'])));



///////////////////////////////////////////////////////////////
//                         $arResult
///////////////////////////////////////////////////////////////
$arResult['element'] = $this->GetEditAreaId($this->__currentCounter);

$this->includeComponentTemplate();
