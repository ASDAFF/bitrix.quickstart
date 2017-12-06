<?
use Bitrix\Main\Loader,
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
	ShowError(Loc::getMessage('API_AUTH_MODULE_ERROR'));
	return;
}

class ApiAuthAjaxComponent extends \CBitrixComponent
{
	public function onPrepareComponentParams($arParams)
	{
		//Inc template lang
		if($this->initComponentTemplate()) {
			Loc::loadMessages($_SERVER['DOCUMENT_ROOT'] . $this->getTemplate()->GetFile());
		}

		//«аголовки модального окна по умолчанию
		$arParams['LOGIN_MESS_HEADER']    = ($arParams['LOGIN_MESS_HEADER'] ? $arParams['LOGIN_MESS_HEADER'] : Loc::getMessage('AAAP_LOGIN_MESS_HEADER'));
		$arParams['REGISTER_MESS_HEADER'] = ($arParams['REGISTER_MESS_HEADER'] ? $arParams['REGISTER_MESS_HEADER'] : Loc::getMessage('AAAP_REGISTER_MESS_HEADER'));
		$arParams['RESTORE_MESS_HEADER']  = ($arParams['RESTORE_MESS_HEADER'] ? $arParams['RESTORE_MESS_HEADER'] : Loc::getMessage('AAAP_RESTORE_MESS_HEADER'));

		$arParams['PROFILE_URL']  = ($arParams['PROFILE_URL'] ? $arParams['PROFILE_URL'] : '/personal/');
		$arParams['LOGOUT_URL']   = ($arParams['LOGOUT_URL'] ? $arParams['LOGOUT_URL']   : '?logout=yes');

		$arParams['LOGIN_URL']    = '#api_auth_login';
		$arParams['RESTORE_URL']  = '#api_auth_restore';
		$arParams['REGISTER_URL'] = '#api_auth_register';

		return $arParams;
	}

	public function executeComponent()
	{
		$this->arResult['FORM_ID'] = $this->getEditAreaId($this->randString());

		\Api\Auth\Tools::setjQuery();

		if(Loader::includeModule('api.core')){
			CUtil::InitJSCore(array(
				 'api_utility', 'api_width', 'api_form', 'api_modal', 'api_alert'
			));
		}

		$this->includeComponentTemplate();
	}
}