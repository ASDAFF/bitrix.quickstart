<?

use Bitrix\Main\Loader,
	 Bitrix\Main\Config\Option,
	 Bitrix\Main\Application,
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

use Api\Auth\Rsa,
	 Api\Auth\Tools,
	 Api\Auth\SettingsTable as Settings;


class ApiAuthRegisterComponent extends \CBitrixComponent
{
	public function onPrepareComponentParams($params)
	{
		global $APPLICATION;

		//Inc template lang
		if($this->initComponentTemplate()) {
			Loc::loadMessages($_SERVER['DOCUMENT_ROOT'] . $this->getTemplate()->GetFile());
		}

		//Все настройки модуля
		if($arSettings = Settings::getAll()) {
			$params = array_merge($params, $arSettings);
		}

		$paramsToDelete = Tools::getDeleteParameters();

		$params['LOGIN_URL']   = ($params['LOGIN_URL'] ? $params['LOGIN_URL'] : $APPLICATION->GetCurPageParam('login=yes', $paramsToDelete));
		$params['RESTORE_URL'] = ($params['RESTORE_URL'] ? $params['RESTORE_URL'] : $APPLICATION->GetCurPageParam('restore=yes', $paramsToDelete));
		$params['BACK_URL']    = $APPLICATION->GetCurPageParam('', $paramsToDelete);

		$params['ALLOW_SOCSERV_AUTHORIZATION'] = (Option::get('main', 'allow_socserv_authorization', 'Y') != 'N' ? 'Y' : 'N');

		if($arRegister = $params['REGISTER']) {
			$params['SHOW_FIELDS']     = (array)$arRegister['SHOW_FIELDS'];
			$params['REQUIRED_FIELDS'] = (array)$arRegister['REQUIRED_FIELDS'];
			$params['GROUP_ID']        = (array)$arRegister['GROUP_ID'];
		}

		if(!$params['SHOW_FIELDS'])
			$params['SHOW_FIELDS'] = array('NAME', 'EMAIL'); //, 'PASSWORD','CONFIRM_PASSWORD'
		elseif(!in_array('EMAIL', $params['SHOW_FIELDS']))
			array_push($params['SHOW_FIELDS'], 'EMAIL');


		if(!$params['REQUIRED_FIELDS'])
			$params['REQUIRED_FIELDS'] = array('NAME', 'EMAIL');


		$params['LOGIN_MESS_HEADER']    = ($params['LOGIN_MESS_HEADER'] ? $params['LOGIN_MESS_HEADER'] : Loc::getMessage('API_AUTH_REGISTER_LOGIN_URL'));
		$params['RESTORE_MESS_HEADER']  = ($params['RESTORE_MESS_HEADER'] ? $params['RESTORE_MESS_HEADER'] : Loc::getMessage('API_AUTH_REGISTER_RESTORE_URL'));
		$params['REGISTER_MESS_HEADER'] = ($params['REGISTER_MESS_HEADER'] ? $params['REGISTER_MESS_HEADER'] : Loc::getMessage('API_AUTH_REGISTER_REGISTER_URL'));

		$params['MESS_PRIVACY'] = str_replace('#BUTTON#', Loc::getMessage('API_AUTH_REGISTER_BUTTON'), $params['MESS_PRIVACY']);

		return $params;
	}

	public function executeComponent()
	{
		global $USER, $APPLICATION;

		$arParams = &$this->arParams;
		$arResult = &$this->arResult;

		$arResult['FORM_ID'] = $this->getEditAreaId($this->randString());

		$bShowPassword = ($arParams['SHOW_FIELDS'] && in_array('PASSWORD', $arParams['SHOW_FIELDS']));


		//---------- Безопасная авторизация ----------//
		$arResult['SECURE_AUTH'] = false;
		$arResult['SECURE_DATA'] = array();
		if($bShowPassword && Option::get('main', 'use_encrypted_auth', 'N') == 'Y') //!CMain::IsHTTPS() &&
		{
			$sec = new Rsa();
			if(($arKeys = $sec->LoadKeys())) {
				$sec->SetKeys($arKeys);
				$arResult['SECURE_DATA'] = $sec->getFormData('api_auth_register_form_' . $arResult['FORM_ID'], array('FIELDS[PASSWORD]', 'FIELDS[CONFIRM_PASSWORD]'));
				$arResult['SECURE_AUTH'] = true;
			}
		}


		//---------- Социальные сервисы ----------//
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


		//---------- Группы пользователей ----------//
		$arResult['GROUP_ID'] = array();
		if($arParams['GROUP_ID']) {

			$arGroups = array();
			$rsGroups = CGroup::GetList($by = "c_sort", $order = "asc", Array("ACTIVE" => "Y"));
			while($arGroup = $rsGroups->Fetch()) {
				$arGroups[ $arGroup["ID"] ] = $arGroup["NAME"];
			}

			foreach($arParams['GROUP_ID'] as $groupId) {
				$arResult['GROUP_ID'][ $groupId ] = $arGroups[ $groupId ];
			}
		}


		//---------- USER_CONSENT ----------//
		$arResult['DISPLAY_USER_CONSENT'] = array();
		if($arParams['USER_CONSENT_ID']) {

			$fieldsCaption = array();
			foreach($arParams['SHOW_FIELDS'] as $fKey){
				$fieldsCaption[] = Loc::getMessage('REGISTER_FIELD_'. $fKey);
			}
			$fieldsCaption[] = Loc::getMessage('AUTH_IP_ADDRESS');

			$arResult['DISPLAY_USER_CONSENT'] = Tools::getUserConsent(
				 $arParams['USER_CONSENT_ID'],
				 Loc::getMessage('API_AUTH_REGISTER_BUTTON'),
				 implode(', ', $fieldsCaption)
			);
		}


		//---------- ComponentTemplate ----------//
		$this->includeComponentTemplate();

		if($arParams['SET_TITLE'] == 'Y') {
			$pageTitle = Loc::getMessage('API_AUTH_REGISTER_PAGE_TITLE');

			$APPLICATION->SetTitle($pageTitle);
			$APPLICATION->SetPageProperty('title', $pageTitle);
			$APPLICATION->AddChainItem($pageTitle);
		}
	}
}