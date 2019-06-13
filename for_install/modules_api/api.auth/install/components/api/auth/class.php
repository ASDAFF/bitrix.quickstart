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

use Api\Auth\SettingsTable as Settings;

class ApiAuthComponent extends \CBitrixComponent
{
	public function onPrepareComponentParams($params)
	{
		$params['SET_TITLE'] = ($params['SET_TITLE'] <> 'N' ? 'Y' : 'N');

		$params['ALLOW_NEW_USER_REGISTRATION'] = (Option::get('main', 'new_user_registration', 'Y') != 'N' ? 'Y' : 'N');

		//Все настройки модуля
		/*if($arSettings = Settings::getAll()) {
			$params = array_merge($params, $arSettings);
		}*/

		return $params;
	}

	public function executeComponent()
	{
		$this->arResult['FORM_ID'] = $this->getEditAreaId($this->randString());

		\Api\Auth\Tools::setjQuery();

		if(Loader::includeModule('api.core')) {
			CUtil::InitJSCore(array(
				 'api_utility',
				 'api_width',
				 'api_modal',
				 'api_button',
				 'api_form',
				 'api_alert',
			));
		}

		$this->includeComponentTemplate();
	}
}