<?

use Bitrix\Main,
	 Bitrix\Main\Loader,
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
	ShowError(Loc::getMessage('API_AUTH_MODULE_ERROR'));
	return;
}

use Api\Auth\SettingsTable as Settings,
	 Api\Auth\Tools,
	 Api\Auth\Rsa;


class ApiAuthLoginComponent extends \CBitrixComponent
{
	/** @var Main\Context $context */
	protected $context;

	public function onPrepareComponentParams($params)
	{
		global $APPLICATION;

		//Inc template lang
		if($this->initComponentTemplate()) {
			Loc::loadMessages($_SERVER['DOCUMENT_ROOT'] . $this->getTemplate()->GetFile());
		}

		$arParamsToDelete = Tools::getDeleteParameters();

		if(defined('AUTH_404'))
			$params['LOGIN_URL'] = htmlspecialcharsback(POST_FORM_ACTION_URI);
		else
			$params['LOGIN_URL'] = $APPLICATION->GetCurPageParam('login=yes', $arParamsToDelete);

		$custom_reg_page          = Option::get('main', 'custom_register_page');
		$params['REGISTER_URL'] = ($params['REGISTER_URL'] ? $params['REGISTER_URL'] : $custom_reg_page);
		if(!$params['REGISTER_URL'])
			$params['REGISTER_URL'] = $APPLICATION->GetCurPageParam('reg=yes', $arParamsToDelete);

		$params['RESTORE_URL'] = ($params['RESTORE_URL'] ? $params['RESTORE_URL'] : $APPLICATION->GetCurPageParam('restore=yes', $arParamsToDelete));
		$params['BACK_URL']    = $APPLICATION->GetCurPageParam('', $arParamsToDelete);


		$params['ALLOW_SOCSERV_AUTHORIZATION'] = (Option::get('main', 'allow_socserv_authorization', 'Y') != 'N' ? 'Y' : 'N');
		$params['ALLOW_NEW_USER_REGISTRATION'] = (Option::get('main', 'new_user_registration', 'Y') != 'N' ? 'Y' : 'N');

		$params['LOGIN_MESS_HEADER']    = ($params['LOGIN_MESS_HEADER'] ? $params['LOGIN_MESS_HEADER'] : Loc::getMessage('API_AUTH_LOGIN_LOGIN_URL'));
		$params['RESTORE_MESS_HEADER']  = ($params['RESTORE_MESS_HEADER'] ? $params['RESTORE_MESS_HEADER'] : Loc::getMessage('API_AUTH_LOGIN_RESTORE_URL'));
		$params['REGISTER_MESS_HEADER'] = ($params['REGISTER_MESS_HEADER'] ? $params['REGISTER_MESS_HEADER'] : Loc::getMessage('API_AUTH_LOGIN_REGISTER_URL'));

		$params['LOGIN_MESS_SUCCESS'] = ($params['LOGIN_MESS_SUCCESS'] ? $params['LOGIN_MESS_SUCCESS'] : Loc::getMessage('API_AUTH_LOGIN_MESS_SUCCESS'));

		$params['COOKIE_NAME'] = Option::get('main', 'cookie_name', 'BITRIX_SM');

		//Все настройки модуля
		if($arSettings = Settings::getAll()) {
			$params = array_merge($params, $arSettings);
		}

		//$params['MESS_PRIVACY'] = str_replace('#BUTTON#',Loc::getMessage('API_AUTH_LOGIN_BUTTON'),$params['MESS_PRIVACY']);

		return $params;
	}

	public function executeComponent()
	{
		global $USER, $APPLICATION;

		$arParams = &$this->arParams;
		$arResult = &$this->arResult;

		$arResult['FORM_ID'] = $this->getEditAreaId($this->randString());

		//Безопасная авторизация
		$arResult['SECURE_AUTH'] = false;
		$arResult['SECURE_DATA'] = array();
		if(Option::get('main', 'use_encrypted_auth', 'N') == 'Y') //!CMain::IsHTTPS() &&
		{
			$sec = new Rsa();
			if(($arKeys = $sec->LoadKeys())) {
				$sec->SetKeys($arKeys);
				$arResult['SECURE_DATA'] = $sec->getFormData('api_auth_login_form_' . $arResult['FORM_ID'], array('PASSWORD'));
				$arResult['SECURE_AUTH'] = true;
			}
		}

		//Авторизация через соц. сервисы
		$arResult['AUTH_SERVICES']   = false;
		$arResult['CURRENT_SERVICE'] = false;
		if($arParams["ALLOW_SOCSERV_AUTHORIZATION"] == 'Y' && !$USER->IsAuthorized() && Loader::includeModule('socialservices')) {
			$oAuthManager = new CSocServAuthManager();
			$arServices   = $oAuthManager->GetActiveAuthServices($arResult);

			if(!empty($arServices)) {
				$arResult['AUTH_SERVICES'] = $arServices;
				if(isset($_REQUEST['auth_service_id']) && $_REQUEST['auth_service_id'] <> '' && isset($arResult['AUTH_SERVICES'][ $_REQUEST['auth_service_id'] ])) {
					$arResult['CURRENT_SERVICE'] = $_REQUEST['auth_service_id'];
					if(isset($_REQUEST['auth_service_error']) && $_REQUEST['auth_service_error'] <> '') {
						$arResult['ERROR_MESSAGE'] = $oAuthManager->GetError($arResult['CURRENT_SERVICE'], $_REQUEST['auth_service_error']);
					}
					elseif(!$oAuthManager->Authorize($_REQUEST['auth_service_id'])) {
						$ex = $APPLICATION->GetException();
						if($ex)
							$arResult['ERROR_MESSAGE'] = $ex->GetString();
					}
				}
			}
		}

		//Плейсхолдеры полей
		$arResult['LOGIN_PLACEHOLDER']    = Loc::getMessage('API_AUTH_LOGIN_LOGIN_OR_EMAIL');
		$arResult['PASSWORD_PLACEHOLDER'] = Loc::getMessage('API_AUTH_LOGIN_FIELD_PASSWORD');

		if($arParams['AUTH_FIELDS']) {
			$arAuthKeys = array_flip($arParams['AUTH_FIELDS']);

			if(isset($arAuthKeys['LOGIN']) && isset($arAuthKeys['EMAIL']))
				$arResult['LOGIN_PLACEHOLDER'] = Loc::getMessage('API_AUTH_LOGIN_LOGIN_OR_EMAIL');
			elseif(isset($arAuthKeys['LOGIN']))
				$arResult['LOGIN_PLACEHOLDER'] = Loc::getMessage('API_AUTH_LOGIN_FIELD_LOGIN');
			elseif(isset($arAuthKeys['EMAIL']))
				$arResult['LOGIN_PLACEHOLDER'] = Loc::getMessage('API_AUTH_LOGIN_FIELD_EMAIL');
		}


		$arResult['LAST_LOGIN'] = htmlspecialcharsbx($_COOKIE[$arParams['COOKIE_NAME'].'_LOGIN']);

		$arResult['CAPTCHA_CODE'] = false;
		if($APPLICATION->NeedCAPTHAForLogin($arResult['LAST_LOGIN'])){
			$arResult['CAPTCHA_CODE'] = $APPLICATION->CaptchaGetCode();
		}


		$this->includeComponentTemplate();

		if($arParams['SET_TITLE'] == 'Y') {
			$pageTitle = Loc::getMessage('API_AUTH_LOGIN_PAGE_TITLE');

			$APPLICATION->SetTitle($pageTitle);
			$APPLICATION->SetPageProperty('title', $pageTitle);
			$APPLICATION->AddChainItem($pageTitle);
		}
	}

}