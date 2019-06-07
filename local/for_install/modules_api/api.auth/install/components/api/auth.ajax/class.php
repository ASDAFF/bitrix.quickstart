<?
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

use Bitrix\Main\Loader,
	 Bitrix\Main\Config\Option,
	 Bitrix\Main\Localization\Loc;

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();

if(!Loader::includeModule('api.auth')) {
	ShowError(Loc::getMessage('API_AUTH_MODULE_ERROR'));
	return;
}

class ApiAuthAjaxComponent extends \CBitrixComponent
{
	public function onPrepareComponentParams($params)
	{
		//Inc template lang
		if($this->initComponentTemplate()) {
			Loc::loadMessages($_SERVER['DOCUMENT_ROOT'] . $this->getTemplate()->GetFile());
		}

		//«аголовки модального окна по умолчанию
		$params['LOGIN_MESS_HEADER']    = ($params['LOGIN_MESS_HEADER'] ? $params['LOGIN_MESS_HEADER'] : Loc::getMessage('AAAP_LOGIN_MESS_HEADER'));
		$params['REGISTER_MESS_HEADER'] = ($params['REGISTER_MESS_HEADER'] ? $params['REGISTER_MESS_HEADER'] : Loc::getMessage('AAAP_REGISTER_MESS_HEADER'));
		$params['RESTORE_MESS_HEADER']  = ($params['RESTORE_MESS_HEADER'] ? $params['RESTORE_MESS_HEADER'] : Loc::getMessage('AAAP_RESTORE_MESS_HEADER'));

		$params['PROFILE_URL']  = ($params['PROFILE_URL'] ? $params['PROFILE_URL'] : '/personal/');
		$params['LOGOUT_URL']   = ($params['LOGOUT_URL'] ? $params['LOGOUT_URL']   : '?logout=yes');

		$params['LOGIN_URL']    = '#api_auth_login';
		$params['RESTORE_URL']  = '#api_auth_restore';
		$params['REGISTER_URL'] = '#api_auth_register';

		$params['ALLOW_NEW_USER_REGISTRATION'] = (Option::get('main', 'new_user_registration', 'Y') != 'N' ? 'Y' : 'N');

		return $params;
	}

	public function executeComponent()
	{
		$this->arResult['FORM_ID'] = $this->getEditAreaId($this->randString());

		\Api\Auth\Tools::setjQuery();

		if(Loader::includeModule('api.core')){
			CUtil::InitJSCore(array(
				 'api_utility',
				 'api_width',
				 'api_form',
				 'api_modal',
				 'api_alert'
			));
		}

		$this->includeComponentTemplate();
	}
}