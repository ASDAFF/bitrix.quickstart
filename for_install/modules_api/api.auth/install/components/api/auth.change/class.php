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

use Api\Auth\Rsa;
use Api\Auth\Tools;

class ApiAuthChangeComponent extends \CBitrixComponent
{
	public function onPrepareComponentParams($arParams)
	{
		return $arParams;
	}

	public function executeComponent()
	{
		global $APPLICATION, $USER;

		$arParams = &$this->arParams;
		$arResult = &$this->arResult;

		$arResult['FORM_ID'] = $this->getEditAreaId($this->randString());

		$arParamsToDelete = Tools::getDeleteParameters();

		if(defined('AUTH_404')) {
			$arResult['AUTH_URL'] = POST_FORM_ACTION_URI;
		}
		else {
			$arResult['AUTH_URL'] = $APPLICATION->GetCurPageParam('change_password=yes', $arParamsToDelete);
		}

		$arResult['BACKURL'] = $APPLICATION->GetCurPageParam('', $arParamsToDelete);

		$arResult['AUTH_AUTH_URL'] = $APPLICATION->GetCurPageParam('login=yes', $arParamsToDelete);

		foreach($arResult as $key => $value) {
			if(!is_array($value))
				$arResult[ $key ] = htmlspecialcharsbx($value);
		}

		$arRequestParams = array(
			 'USER_CHECKWORD',
			 'USER_PASSWORD',
			 'USER_CONFIRM_PASSWORD',
		);

		foreach($arRequestParams as $param) {
			$arResult[ $param ] = strlen($_REQUEST[ $param ]) > 0 ? $_REQUEST[ $param ] : '';
			$arResult[ $param ] = htmlspecialcharsbx($arResult[ $param ]);
		}

		if(isset($_GET['USER_LOGIN']))
			$arResult['~LAST_LOGIN'] = CUtil::ConvertToLangCharset($_GET['USER_LOGIN']);
		elseif(isset($_POST['USER_LOGIN']))
			$arResult['~LAST_LOGIN'] = $_POST['USER_LOGIN'];
		else
			$arResult['~LAST_LOGIN'] = $_COOKIE[ COption::GetOptionString('main', 'cookie_name', 'BITRIX_SM') . '_LOGIN' ];

		$arResult['LAST_LOGIN'] = htmlspecialcharsbx($arResult['~LAST_LOGIN']);

		$userId = 0;
		if($arResult['~LAST_LOGIN'] <> '') {
			$res = CUser::GetByLogin($arResult['~LAST_LOGIN']);
			if($profile = $res->Fetch()) {
				$userId = $profile['ID'];
			}
		}
		$arResult['GROUP_POLICY'] = CUser::GetGroupPolicy($userId);


		//Безопасная авторизация
		$arResult['SECURE_AUTH'] = false;
		$arResult['SECURE_DATA'] = array();
		if(!CMain::IsHTTPS() && Option::get('main', 'use_encrypted_auth', 'N') == 'Y') {
			$sec = new Rsa();
			if(($arKeys = $sec->LoadKeys())) {
				$sec->SetKeys($arKeys);
				$arResult['SECURE_DATA'] = $sec->getFormData('api_auth_change_form_' . $arResult['FORM_ID'], array('USER_PASSWORD', 'USER_CONFIRM_PASSWORD'));
				$arResult['SECURE_AUTH'] = true;
			}
		}

		$arResult['USE_CAPTCHA'] = (Option::get('main', 'captcha_restoring_password', 'N') == 'Y');
		if($arResult['USE_CAPTCHA']) {
			$arResult['CAPTCHA_CODE'] = htmlspecialcharsbx($APPLICATION->CaptchaGetCode());
		}

		$this->includeComponentTemplate();

		if($arParams['SET_TITLE'] == 'Y') {
			$pageTitle = Loc::getMessage('API_AUTH_CHANGE_PAGE_TITLE');

			$APPLICATION->SetTitle($pageTitle);
			$APPLICATION->SetPageProperty('title', $pageTitle);
			$APPLICATION->AddChainItem($pageTitle);
		}
	}
}