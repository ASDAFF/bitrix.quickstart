<?
use Bitrix\Main\Loader,
	 Bitrix\Main\Config\Option,
	 Bitrix\Main\Localization\Loc;

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
 * @var string           $templateFile
 * @var string           $templateFolder
 *
 * @var string           $parentComponentPath
 * @var string           $parentComponentName
 * @var string           $parentComponentTemplate
 *
 * @var CDatabase        $DB
 * @var CUser            $USER
 * @var CMain            $APPLICATION
 */

if(!Loader::includeModule('api.auth')) {
	ShowError(Loc::getMessage('API_QA_MODULE_ERROR'));
	return;
}

use Api\Auth\SettingsTable;
use Api\Auth\Tools;

class ApiAuthRestoreComponent extends \CBitrixComponent
{
	public function onPrepareComponentParams($arParams)
	{
		global $APPLICATION;

		//Inc template lang
		if($this->initComponentTemplate()) {
			Loc::loadMessages($_SERVER['DOCUMENT_ROOT'] . $this->getTemplate()->GetFile());
		}

		$arParamsToDelete = Tools::getDeleteParameters();

		$arParams['BACK_URL']   = $APPLICATION->GetCurPageParam('', $arParamsToDelete);
		$arParams['LOGIN_URL']  = ($arParams['LOGIN_URL'] ? $arParams['LOGIN_URL'] : $APPLICATION->GetCurPageParam('login=yes', $arParamsToDelete));
		$arParams['LAST_LOGIN'] = htmlspecialcharsbx($_COOKIE[ Option::get('main', 'cookie_name', 'BITRIX_SM') . '_LOGIN' ]);

		$arParams['LOGIN_MESS_HEADER']    = ($arParams['LOGIN_MESS_HEADER'] ? $arParams['LOGIN_MESS_HEADER'] : Loc::getMessage('API_AUTH_RESTORE_LOGIN_URL'));
		$arParams['RESTORE_MESS_HEADER']  = ($arParams['RESTORE_MESS_HEADER'] ? $arParams['RESTORE_MESS_HEADER'] : Loc::getMessage('API_AUTH_RESTORE_RESTORE_URL'));
		$arParams['REGISTER_MESS_HEADER'] = ($arParams['REGISTER_MESS_HEADER'] ? $arParams['REGISTER_MESS_HEADER'] : Loc::getMessage('API_AUTH_RESTORE_REGISTER_URL'));

		//Все настройки модуля
		if($arSettings = SettingsTable::getAll()){
			$arParams = array_merge($arParams,$arSettings);
		}

		//$arParams['MESS_PRIVACY'] = str_replace('#BUTTON#',Loc::getMessage('API_AUTH_RESTORE_BUTTON'),$arParams['MESS_PRIVACY']);

		return $arParams;
	}

	public function executeComponent()
	{
		global $APPLICATION;

		$arParams = &$this->arParams;
		$arResult = &$this->arResult;

		$arResult['FORM_ID'] = $this->getEditAreaId($this->randString());


		//---------- USER_CONSENT ----------//
		$arResult['DISPLAY_USER_CONSENT'] = array();
		if($arParams['USER_CONSENT_ID']) {
			$arResult['DISPLAY_USER_CONSENT'] = Tools::getUserConsent(
				 $arParams['USER_CONSENT_ID'],
				 Loc::getMessage('API_AUTH_RESTORE_BUTTON'),
				 Loc::getMessage('API_AUTH_RESTORE_USER_CONSENT_FIELDS')
			);
		}



		//---------- ComponentTemplate ----------//
		$this->includeComponentTemplate();

		if($arParams['SET_TITLE'] == 'Y')
		{
			$pageTitle = Loc::getMessage('API_AUTH_RESTORE_PAGE_TITLE');

			$APPLICATION->SetTitle($pageTitle);
			$APPLICATION->SetPageProperty('title',$pageTitle);
			$APPLICATION->AddChainItem($pageTitle);
		}
	}
}