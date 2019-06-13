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
	 Bitrix\Main\Application,
	 Bitrix\Main\UserTable,
	 Bitrix\Main\Localization\Loc;

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
	die();


//ID компонента
//$cpId = $this->getEditAreaId($this->__currentCounter);

//Объект родительского компонента
//$parent = $this->getParent();
//$parentPath = $parent->getPath();

Loc::loadMessages(__FILE__);

if(!Loader::includeModule('api.qa')) {
	ShowError(Loc::getMessage('API_QA_MODULE_ERROR'));
	return;
}

if(!Loader::includeModule('iblock')) {
	ShowError(Loc::getMessage('IBLOCK_MODULE_ERROR'));
	return;
}


use Api\QA\QuestionTable,
	 Api\QA\Tools,
	 Api\QA\Converter;


class ApiQaListComponent extends \CBitrixComponent
{
	public function onPrepareComponentParams($arParams)
	{
		global $USER, $APPLICATION;

		$context = Application::getInstance()->getContext();
		$request = $context->getRequest();
		$server  = $context->getServer();

		//Inc template lang
		if($this->initComponentTemplate()) {
			Loc::loadMessages($server->getDocumentRoot() . $this->getTemplate()->GetFile());
		}

		$arParams['HTTP_HOST'] = ($request->isHttps() ? 'https://' : 'http://') . $request->getHttpHost();
		$arParams['HASH']      = '?question=#ID#' . trim($arParams['HASH']);

		$arParams['SITE_ID']    = SITE_ID;
		$arParams['IBLOCK_ID']  = intval($arParams['IBLOCK_ID']);
		$arParams['ELEMENT_ID'] = intval($arParams['ELEMENT_ID']);
		$arParams['XML_ID']     = trim($arParams['XML_ID']);
		$arParams['CODE']       = trim($arParams['CODE']);

		$arParams['IS_EDITOR'] = ($APPLICATION->GetGroupRight('api.qa') >= 'W');
		$arParams['THEME']     = $arParams['THEME'] ? $arParams['THEME'] : 'flat';
		$arParams['COLOR']     = $arParams['COLOR'] ? $arParams['COLOR'] : 'orange1';
		$arParams['COUNT']     = 0;
		//$arParams['CACHE_TIME']  = $arParams['CACHE_TIME'] ? $arParams['CACHE_TIME'] : 3600;

		if(!isset($arParams['ACTIVE']))
			$arParams['ACTIVE'] = 'Y';


		//ALLOW
		if(!isset($arParams['ALLOW']))
			$arParams['ALLOW'] = 'ALL';

		$arParams['IS_ALLOW'] = ($arParams['ALLOW'] == 'ALL' || ($arParams['ALLOW'] == 'USER' && $USER->IsAuthorized()) || $arParams['IS_EDITOR']);

		if(!isset($arParams['MESS_ALLOW_USER']))
			$arParams['MESS_ALLOW_USER'] = Loc::getMessage('AQAL_JS_MESS_ALLOW_USER');
		if(!isset($arParams['MESS_ALLOW_EDITOR']))
			$arParams['MESS_ALLOW_EDITOR'] = Loc::getMessage('AQAL_JS_MESS_ALLOW_EDITOR');


		//PRIVACY
		$arParams['USE_PRIVACY']          = (!$USER->IsAuthorized() && $arParams['USE_PRIVACY'] == 'Y');
		$arParams['MESS_PRIVACY']         = trim($arParams['MESS_PRIVACY']);
		$arParams['MESS_PRIVACY_LINK']    = trim($arParams['MESS_PRIVACY_LINK']);
		$arParams['MESS_PRIVACY_CONFIRM'] = trim($arParams['MESS_PRIVACY_CONFIRM']);


		if(!isset($arParams['DATE_FORMAT']))
			$arParams['DATE_FORMAT'] = Loc::getMessage('AQAL_COMP_DATE_FORMAT');

		if(isset($arParams['DATE_FORMAT']))
			$arParams['DATE_FORMAT'] = preg_replace('/\\in/i', Loc::getMessage('AQAL_COMP_DATE_FORMAT_IN'), $arParams['DATE_FORMAT']);


		if(Loader::includeModule('api.core')) {
			CUtil::InitJSCore(array('api_alert'));
		}

		return $arParams;
	}

	public function executeComponent()
	{
		Tools::formatParams($this->arParams);
		$this->initData();
	}


	/**
	 * @var CBitrixComponentTemplate $template
	 *
	 * @return array
	 */
	public function getAjaxParams($template)
	{
		global $USER;

		$arParams       = &$this->arParams;
		$templateFolder = $template->GetFolder();

		$params = array(
			 'PAGE_URL'                          => $arParams['PAGE_URL'],
			 'PAGE_TITLE'                        => $arParams['PAGE_TITLE'],
			 'ACTIVE'                            => $arParams['ACTIVE'],
			 'ALLOW'                             => $arParams['ALLOW'],
			 'SITE_ID'                           => $arParams['SITE_ID'],
			 'IBLOCK_ID'                         => $arParams['IBLOCK_ID'],
			 'ELEMENT_ID'                        => $arParams['ELEMENT_ID'],
			 'XML_ID'                            => $arParams['XML_ID'],
			 'CODE'                              => $arParams['CODE'],
			 'HASH'                              => $arParams['HASH'],
			 'COUNT'                             => $arParams['COUNT'],
			 'DATE_FORMAT'                       => $arParams['DATE_FORMAT'],
			 'IS_ALLOW'                          => $arParams['IS_ALLOW'],
			 'AJAX_URL'                          => $templateFolder . '/ajax.php',
			 'ACTION_URL'                        => $templateFolder . '/action.php',
			 'MESS_ACTIVE'                       => $arParams['MESS_ACTIVE'],
			 'FORM_QUESTION_MESS_SUBMIT'         => $arParams['FORM_QUESTION_MESS_SUBMIT'],
			 'FORM_QUESTION_MESS_SUBMIT_AJAX'    => $arParams['FORM_QUESTION_MESS_SUBMIT_AJAX'],
			 'FORM_ANSWER_MESS_SUBMIT'           => $arParams['FORM_ANSWER_MESS_SUBMIT'],
			 'FORM_ANSWER_MESS_SUBMIT_AJAX'      => $arParams['FORM_ANSWER_MESS_SUBMIT_AJAX'],
			 'LIST_QUESTION_MESS_EXPERT'         => $arParams['LIST_QUESTION_MESS_EXPERT'],
			 'LIST_QUESTION_MESS_LINK'           => $arParams['LIST_QUESTION_MESS_LINK'],
			 'LIST_QUESTION_MESS_BUTTON_ANSWER'  => $arParams['LIST_QUESTION_MESS_BUTTON_ANSWER'],
			 'LIST_QUESTION_MESS_BUTTON_EDIT'    => $arParams['LIST_QUESTION_MESS_BUTTON_EDIT'],
			 'LIST_QUESTION_MESS_BUTTON_SAVE'    => $arParams['LIST_QUESTION_MESS_BUTTON_SAVE'],
			 'LIST_QUESTION_MESS_BUTTON_CANCEL'  => $arParams['LIST_QUESTION_MESS_BUTTON_CANCEL'],
			 'LIST_QUESTION_MESS_BUTTON_DELETE'  => $arParams['LIST_QUESTION_MESS_BUTTON_DELETE'],
			 'LIST_QUESTION_MESS_CONFIRM_DELETE' => $arParams['LIST_QUESTION_MESS_CONFIRM_DELETE'],
			 'LIST_QUESTION_MESS_BUTTON_ERASE'   => $arParams['LIST_QUESTION_MESS_BUTTON_ERASE'],
			 'LIST_QUESTION_MESS_CONFIRM_ERASE'  => $arParams['LIST_QUESTION_MESS_CONFIRM_ERASE'],
			 'LIST_QUESTION_MESS_TEXT_ERASE'     => $arParams['LIST_QUESTION_MESS_TEXT_ERASE'],
			 'USE_PRIVACY'                       => $arParams['USE_PRIVACY'],
			 'ADMIN_EMAIL'                       => $arParams['ADMIN_EMAIL'],
			 'MESS_PRIVACY_CONFIRM'              => $arParams['MESS_PRIVACY_CONFIRM'],
			 'MESS_ALLOW_USER'                   => $arParams['MESS_ALLOW_USER'],
			 'MESS_ALLOW_EDITOR'                 => $arParams['MESS_ALLOW_EDITOR'],
			 'USER'                              => array(
					'IS_AUTHORIZED' => $USER->IsAuthorized(),
					'IS_EDITOR'     => $arParams['IS_EDITOR'],
			 ),
			 'alert'                             => array(
					'labelOk'     => Loc::getMessage('alert_labelOk'),
					'labelCancel' => Loc::getMessage('alert_labelCancel'),
			 ),
		);

		return $params;
	}

	public function initData()
	{
		$arParams = &$this->arParams;
		$arResult = &$this->arResult;
		$request  = &$this->request;

		$pageUrl = trim($arParams['PAGE_URL']);

		//Получим ссылку на товар если не передали PAGE_URL через настройки компонента
		if(!$pageUrl) {
			$arElFilter = array(
				 '=IBLOCK_ID' => $arParams['IBLOCK_ID'],
			);

			if($arParams['ELEMENT_ID'])
				$arElFilter['=ID'] = $arParams['ELEMENT_ID'];

			if($arParams['XML_ID'])
				$arElFilter['=XML_ID'] = $arParams['XML_ID'];

			if($arParams['CODE'])
				$arElFilter['=CODE'] = $arParams['CODE'];

			$rsElement = \CIBlockElement::GetList(
				 false,
				 $arElFilter,
				 false,
				 array('nTopCount' => 1),
				 array('ID', 'NAME', 'DETAIL_PAGE_URL')
			);

			$arElement = $rsElement->GetNext(false, false);

			$pageUrl = trim($arElement['DETAIL_PAGE_URL']);
		}


		//Прокрутка до отзыва
		$arResult['SCROLL_TO'] = (int)$request->get('question');

		$arResult['ITEMS'] = array();

		//---------- $arSort ----------//
		$arSort = array('ID' => 'ASC');


		//---------- $arSelect ----------//
		$arSelect = array(
			 'ID',
			 'DATE_CREATE',
			 'USER_ID',
			 'TYPE',
			 'PARENT_ID',
			 'LEVEL',
			 'TEXT',
			 'GUEST_NAME',
			 'GUEST_EMAIL',
		);


		//---------- $arFilter ----------//
		$arFilter = array(
			 '=ACTIVE'  => 'Y',
			 '=SITE_ID' => $arParams['SITE_ID'],
		);

		$arFilter['=IBLOCK_ID'] = $arParams['IBLOCK_ID'];

		if($arParams['ELEMENT_ID'])
			$arFilter['=ELEMENT_ID'] = $arParams['ELEMENT_ID'];

		if($arParams['XML_ID'])
			$arFilter['=XML_ID'] = $arParams['XML_ID'];

		if($arParams['CODE'])
			$arFilter['=CODE'] = $arParams['CODE'];


		//---------- Element search filter ----------//
		if($this->startResultCache(false, array($arSort, $arSelect, $arFilter))) {

			$rsQuestions = QuestionTable::getList(array(
				 'order'  => $arSort,
				 'filter' => $arFilter,
				 'select' => $arSelect,
			));

			$arParams['COUNT'] = $rsQuestions->getSelectedRowsCount();

			$arIserId    = array();
			$arQuestions = array();

			$siteDateFormat = \CSite::GetDateFormat();
			while($arQuestion = $rsQuestions->fetch(new Converter)) {

				$arQuestion['TEXT'] = Converter::replace($arQuestion['TEXT']);

				if($arQuestion['USER_ID'])
					$arIserId[ $arQuestion['USER_ID'] ] = $arQuestion['USER_ID'];

				$arQuestion['URL'] = str_replace('#ID#', $arQuestion['ID'], $arParams['HTTP_HOST'] . $pageUrl . $arParams['HASH']);

				$arQuestion['DATE_CREATE'] = Tools::formatDate($arParams['DATE_FORMAT'], MakeTimeStamp($arQuestion['DATE_CREATE'], $siteDateFormat));

				$arQuestions[] = $arQuestion;
			}

			$arUsers = array();
			if($arIserId) {
				$rsUsers = UserTable::getList(array(
					 'filter' => array('=ID' => array_values($arIserId)),
					 'select' => array('ID', 'TITLE', 'NAME', 'LAST_NAME', 'SECOND_NAME', 'LOGIN', 'EMAIL'),
				));

				$siteNameFormat = \CSite::GetNameFormat(false);
				while($arUser = $rsUsers->fetch()) {
					$arUsers[ $arUser['ID'] ] = array(
						 'NAME'  => \CUser::FormatName($siteNameFormat, $arUser, true, true),
						 'EMAIL' => $arUser['EMAIL'],
					);
				}
			}

			foreach($arQuestions as &$arItem) {

				$user = (array)$arUsers[ $arItem['USER_ID'] ];

				if(!$arItem['GUEST_NAME'] && $user['NAME'])
					$arItem['GUEST_NAME'] = $user['NAME'];

				if(!$arItem['GUEST_EMAIL'] && $user['EMAIL'])
					$arItem['GUEST_EMAIL'] = $user['EMAIL'];

				$arItem['PICTURE'] = Tools::getGravatar($arItem['GUEST_EMAIL']);
				$arItem['USER']    = $user;
			}

			unset($rsUsers, $arUsers);

			$arResult['ITEMS'] = $this->sortData($arQuestions);

			$this->setResultCacheKeys(array(
				 'COUNT',
			));

			$this->includeComponentTemplate();
		}
	}

	public function sortData(&$items, $parentItemId = 0, $level = 0, $count = null)
	{
		if(is_array($items) && count($items)) {

			$return = array();

			if(is_null($count)) {
				$cnt = count($items);
			}
			else {
				$cnt = $count;
			}

			for($i = 0; $i < $cnt; $i++) {

				if(!isset($items[ $i ]))
					continue;

				$item     = $items[ $i ];
				$parentId = $item['PARENT_ID'];

				if($parentId == $parentItemId) {

					$item['LEVEL'] = $level;
					$itemId        = $item['ID'];
					$return[]      = $item;

					unset($items[ $i ]);

					while($nextReturn = $this->sortData($items, $itemId, $level + 1, $cnt)) {
						$return = array_merge($return, $nextReturn);
					}
				}
			}
			return $return;
		}
		return false;
	}

}