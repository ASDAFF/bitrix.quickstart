<?

use \Bitrix\Main;
use \Bitrix\Main\Web;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Result;
use \Bitrix\Main\Error;
use \Bitrix\Main\Page\Asset;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Type\DateTime;


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
class ApiAuthProfileComponent extends \CBitrixComponent
{
	protected $storage = array();

	/** @var Result */
	protected $result;

	public function onPrepareComponentParams($params)
	{
		$this->result = new Result();

		if($this->initComponentTemplate()) {
			Loc::loadMessages($_SERVER['DOCUMENT_ROOT'] . $this->getTemplate()->GetFile());
		}

		$params['CHECK_RIGHTS'] = $params['CHECK_RIGHTS'] == 'Y' ? 'Y' : 'N';

		$params['USER_FIELDS']     = array_diff((array)$params['USER_FIELDS'], array(''));
		$params['CUSTOM_FIELDS']   = array_diff((array)$params['CUSTOM_FIELDS'], array(''));
		$params['REQUIRED_FIELDS'] = array_diff((array)$params['REQUIRED_FIELDS'], array(''));
		$params['READONLY_FIELDS'] = array_diff((array)$params['READONLY_FIELDS'], array(''));

		$required                  = array('LOGIN', 'EMAIL');
		$params['REQUIRED_FIELDS'] = array_unique(array_merge($required, $params['REQUIRED_FIELDS']));

		$readonly                  = array('ID', 'ACTIVE', 'DATE_REGISTER', 'LAST_LOGIN', 'LAST_ACTIVITY_DATE', 'TIMESTAMP_X');
		$params['READONLY_FIELDS'] = array_unique(array_merge($readonly, $params['READONLY_FIELDS']));

		return $params;
	}

	public function executeComponent()
	{
		$this->arResult['ID']      = intval($GLOBALS['USER']->GetID());
		$this->arResult['FORM_ID'] = $this->getEditAreaId($this->randString());

		if($this->checkRights()) {

			$this->checkModules();

			if($this->request->isPost()) {
				$this->postHandler();
			}

			if(!$this->result->isSuccess()) {
				$this->arResult['MESSAGE_DANGER'] = join('<br>', $this->result->getErrorMessages());
			}

			$this->prepareResult();

			$this->checkMessage();
			$this->includeComponentTemplate();
			$this->clearMessage();
		}
	}

	protected function prepareResult()
	{
		if($this->arResult['ID']) {
			$this->initResult();
		}
		else {
			$this->result->addError(new Error(Loc::getMessage('API_MAIN_PROFILE_CL_USER_ID_ERROR')));
		}
	}

	protected function initResult()
	{
		$rsUser = \CUser::GetByID($this->arResult['ID']);
		$arUser = $rsUser->Fetch();

		if($arUser) {

			$this->arResult['GROUP_POLICY'] = \CUser::GetGroupPolicy($arUser['ID']);

			//---------- User ----------//
			if(!isset($this->arResult['POST'])) {
				foreach($this->arParams['USER_FIELDS'] as $key) {
					$value = $arUser[ $key ];

					if($key == 'PASSWORD' || $key == 'CONFIRM_PASSWORD') {
						$value = '';
					}

					$this->arResult['POST'][ $key ] = $value;
				}
			}

			//---------- time zones ----------//
			if($this->arResult['TIME_ZONE_ENABLED'] = CTimeZone::Enabled()) {

				$this->arResult['POST']['AUTO_TIME_ZONE'] = $arUser['AUTO_TIME_ZONE'];
				$this->arResult['POST']['TIME_ZONE']      = $arUser['TIME_ZONE'];

				$this->arResult['TIME_ZONE_LIST'] = CTimeZone::GetZones();
			}


			//---------- Language ----------//
			$rsLang = CLanguage::GetList($by = "name", $order = "asc", Array("ACTIVE" => "Y"));
			while($arLang = $rsLang->Fetch()) {
				$this->arResult['LANGUAGE_LIST'][ $arLang['LID'] ] = $arLang;
			}


			//---------- Countries ----------//
			$arCountry = array();
			if($countries = GetCountryArray()) {
				$reference    = $countries['reference'];
				$reference_id = $countries['reference_id'];
				foreach($reference_id as $key => $id) {
					$arCountry[ $id ] = $reference[ $key ];
				}
			}
			$this->arResult['COUNTRY_LIST'] = $arCountry;


			//---------- Location ----------//
			if(Loader::includeModule('sale') && \CSaleLocation::isLocationProEnabled()) {

				//PERSONAL_CITY
				if($arUser['PERSONAL_CITY']) {
					$code = trim($arUser['PERSONAL_CITY']);
					$lang = LANGUAGE_ID;

					/*
					Вернет только Город
					$rsLocation = \Bitrix\Sale\Location\LocationTable::getByCode($code, array(
						 'filter' => array('=NAME.LANGUAGE_ID' => LANGUAGE_ID),
						 'select' => array('ID','CODE','PARENT_ID','NAME_RU' => 'NAME.NAME'),
					));
					$arLocation = $rsLocation->fetch();
					$arUser['PERSONAL_CITY'] = $arLocation['NAME_RU'];
					*/


					//Вернет полный адрес
					$arLocations = \Bitrix\Sale\Location\LocationTable::getList(array(
						 'filter' => array(
								'=CODE'                          => $code,
								'=PARENTS.NAME.LANGUAGE_ID'      => $lang,
								'=PARENTS.TYPE.NAME.LANGUAGE_ID' => $lang,
						 ),
						 'select' => array(
								'I_ID'            => 'PARENTS.ID',
								'I_NAME_' . $lang => 'PARENTS.NAME.NAME',
								'I_TYPE_CODE'     => 'PARENTS.TYPE.CODE',
								'I_TYPE_NAME_RU'  => 'PARENTS.TYPE.NAME.NAME',
						 ),
						 'order'  => array(
								'PARENTS.DEPTH_LEVEL' => 'asc',
						 ),
					))->fetchAll();


					$userAddress = '';
					if($arLocations) {
						foreach($arLocations as $arLocation) {
							$location = $arLocation[ 'I_NAME_' . $lang ];
							if(strlen($location) > 0)
								$userAddress .= $location . ', ';
						}

						$userAddress = TrimEx($userAddress, ',');
					}
					$arUser['PERSONAL_CITY'] = $userAddress;
				}

				//WORK_CITY
				if($arUser['WORK_CITY']) {
					$code = trim($arUser['WORK_CITY']);
					$lang = LANGUAGE_ID;

					/*
					Вернет только Город
					$rsLocation = \Bitrix\Sale\Location\LocationTable::getByCode($code, array(
						 'filter' => array('=NAME.LANGUAGE_ID' => LANGUAGE_ID),
						 'select' => array('ID','CODE','PARENT_ID','NAME_RU' => 'NAME.NAME'),
					));
					$arLocation = $rsLocation->fetch();
					$arUser['PERSONAL_CITY'] = $arLocation['NAME_RU'];
					*/


					//Вернет полный адрес
					$arLocations = \Bitrix\Sale\Location\LocationTable::getList(array(
						 'filter' => array(
								'=CODE'                          => $code,
								'=PARENTS.NAME.LANGUAGE_ID'      => $lang,
								'=PARENTS.TYPE.NAME.LANGUAGE_ID' => $lang,
						 ),
						 'select' => array(
								'I_ID'            => 'PARENTS.ID',
								'I_NAME_' . $lang => 'PARENTS.NAME.NAME',
								'I_TYPE_CODE'     => 'PARENTS.TYPE.CODE',
								'I_TYPE_NAME_RU'  => 'PARENTS.TYPE.NAME.NAME',
						 ),
						 'order'  => array(
								'PARENTS.DEPTH_LEVEL' => 'asc',
						 ),
					))->fetchAll();


					$userAddress = '';
					if($arLocations) {
						foreach($arLocations as $arLocation) {
							$location = $arLocation[ 'I_NAME_' . $lang ];
							if(strlen($location) > 0)
								$userAddress .= $location . ', ';
						}

						$userAddress = TrimEx($userAddress, ',');
					}
					$arUser['WORK_CITY'] = $userAddress;
				}
			}

			$this->arResult['USER'] = $arUser;

			unset($countries, $reference, $reference_id, $key, $id);
		}
		else {
			$this->result->addError(new Error(Loc::getMessage('API_MAIN_PROFILE_CL_USER_ERROR')));
		}
	}

	protected function postHandler()
	{
		global $USER, $APPLICATION, $USER_FIELD_MANAGER;

		$arResult = &$this->arResult;

		if(!check_bitrix_sessid()) {
			$this->result->addError(new Error(Loc::getMessage('API_MAIN_PROFILE_CL_ERROR_SESSID')));
		}

		$post = $this->request->getPostList()->toArray();

		$arFields = array();
		$arLang   = (array)Loc::getMessage('API_MAIN_PROFILE_TPL_FIELDS');

		//Фильтрация данных
		if($post['FIELDS']) {
			foreach($post['FIELDS'] as $key => $val) {
				$arFields[ $key ] = is_array($val) ? $val : trim($val);
			}
			unset($key, $val);
		}

		//Если в форме не выводится Логин или E-mail возьмем данные из пользователя
		$rsUser = CUser::GetByID($arResult["ID"]);
		if($arUser = $rsUser->Fetch()) {
			if(!in_array('LOGIN', $this->arParams['USER_FIELDS']) || in_array('LOGIN', $this->arParams['READONLY_FIELDS']))
				$arFields['LOGIN'] = $arUser['LOGIN'];

			if(!in_array('EMAIL', $this->arParams['USER_FIELDS']) || in_array('EMAIL', $this->arParams['READONLY_FIELDS']))
				$arFields['EMAIL'] = $arUser['EMAIL'];
		}
		else {
			$this->result->addError(new Error(Loc::getMessage('API_MAIN_PROFILE_CL_ERROR_USER')));
		}


		//Прикрепим файлы
		if(in_array('PERSONAL_PHOTO', $this->arParams['USER_FIELDS'])) {
			$arFields['PERSONAL_PHOTO'] = $_FILES['PERSONAL_PHOTO'];

			$arFields['PERSONAL_PHOTO']['old_file'] = $arUser['PERSONAL_PHOTO'];
			$arFields['PERSONAL_PHOTO']['del']      = $post['PERSONAL_PHOTO_del'];
		}
		if(in_array('WORK_LOGO', $this->arParams['USER_FIELDS'])) {
			$arFields['WORK_LOGO'] = $_FILES['WORK_LOGO'];

			$arFields['WORK_LOGO']['old_file'] = $arUser['WORK_LOGO'];
			$arFields['WORK_LOGO']['del']      = $post['WORK_LOGO_del'];
		}

		//Проверка обязательных полей
		$reqValues = $this->arParams['REQUIRED_FIELDS'];
		foreach($reqValues as $key) {

			if($key == 'PERSONAL_PHOTO' || $key == 'WORK_LOGO'){
				if(!$arFields[$key]['old_file']){
					if(!$arFields[$key]['tmp_name'])
						unset($arFields[ $key ]);
				}
			}

			if(!$arFields[ $key ]) {
				$error = Loc::getMessage('API_MAIN_PROFILE_CL_MESSAGE_DANGER', array('#NAME#' => $arLang[ $key ]));
				$this->result->addError(new Error($error));
			}
			unset($key, $val);
		}

		$USER_FIELD_MANAGER->EditFormAddFields("USER", $arFields);


		//Default form result
		$arResult['POST'] = $arFields;

		if($this->result->isSuccess()) {

			//Если поле Пароль пустое, изменять не нужно, только когда введено пользователем
			if(strlen($arFields['PASSWORD']) == 0) {
				unset($arFields['PASSWORD'], $arFields['CONFIRM_PASSWORD']);
			}

			$user = new CUser;
			if(!$user->Update($arResult["ID"], $arFields)) {
				$this->result->addError(new Error($user->LAST_ERROR));
			}
			else {
				$_SESSION['API_MAIN_ROFILE_MESSAGE_SUCCESS'] = Loc::getMessage('API_MAIN_PROFILE_CL_MESSAGE_SUCCESS');
				LocalRedirect($APPLICATION->GetCurPageParam());
			}
		}
	}

	protected function checkMessage()
	{
		if($_SESSION['API_MAIN_ROFILE_MESSAGE_SUCCESS'])
			$this->arResult['MESSAGE_SUCCESS'] = $_SESSION['API_MAIN_ROFILE_MESSAGE_SUCCESS'];
	}

	protected function clearMessage()
	{
		unset($_SESSION['API_MAIN_ROFILE_MESSAGE_SUCCESS']);
	}

	protected function checkRights()
	{
		global $USER, $APPLICATION;

		if(!($this->arParams['CHECK_RIGHTS'] == 'N' || $USER->CanDoOperation('edit_own_profile')) || $this->arResult["ID"]<=0){
			$APPLICATION->ShowAuthForm('');
			return false;
		}

		return true;
	}

	protected function checkModules()
	{
		if(!Loader::includeModule('main')) {
			$this->result->addError(new Error(Loc::getMessage('API_MAIN_PROFILE_CL_MODULE_ERROR')));
		}

		if(Loader::includeModule('api.core')) {
			CUtil::InitJSCore('api_form');
		}
	}


	/** TODO: Don't used */
	protected function getUserGroups()
	{
		/** @global \CUser $USER */
		global $USER;
		$result = array(2);
		if(isset($USER) && $USER instanceof \CUser) {
			$result = $USER->GetUserGroupArray();
			Main\Type\Collection::normalizeArrayValuesByInt($result, true);
		}
		return $result;
	}
}