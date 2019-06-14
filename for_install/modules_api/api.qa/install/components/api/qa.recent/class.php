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
	 Bitrix\Main\UserTable,
	 Bitrix\Main\Application,
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

use Api\QA\Converter,
	 Api\QA\QuestionTable,
	 Api\QA\Tools;


class ApiQaRecentComponent extends \CBitrixComponent
{

	public function onPrepareComponentParams($arParams)
	{
		$context = Application::getInstance()->getContext();
		$request = $context->getRequest();
		$server  = $context->getServer();

		//Inc template lang
		if($this->initComponentTemplate()) {
			Loc::loadMessages($server->getDocumentRoot() . $this->getTemplate()->GetFile());
		}

		if(!isset($arParams['ITEMS_LIMIT']))
			$arParams['ITEMS_LIMIT'] = 7;

		if(!isset($arParams['CACHE_TIME']))
			$arParams['CACHE_TIME'] = 3600;

		if(!isset($arParams['DATE_FORMAT']))
			$arParams['DATE_FORMAT'] = Loc::getMessage('AQAR_COMP_DATE_FORMAT');

		if(isset($arParams['DATE_FORMAT']))
			$arParams['DATE_FORMAT'] = preg_replace('/\\in/i', Loc::getMessage('AQAR_COMP_DATE_FORMAT_IN'), $arParams['DATE_FORMAT']);

		/** @deprecated in 1.7.0 */
		if(isset($arParams['ACTIVE_DATE_FORMAT']) && !isset($arParams['DATE_FORMAT']))
			$arParams['DATE_FORMAT'] = $arParams['ACTIVE_DATE_FORMAT'];


		//VISUAL
		if(!isset($arParams['HEADER_ON']))
			$arParams['HEADER_ON'] = 'Y';

		if(!isset($arParams['HEADER_TITLE']))
			$arParams['HEADER_TITLE'] = Loc::getMessage('AQAR_COMP_HEADER_TITLE');

		if(!isset($arParams['TEXT_ON']))
			$arParams['TEXT_ON'] = 'Y';

		if(!isset($arParams['TEXT_LIMIT']))
			$arParams['TEXT_LIMIT'] = 100;


		$arParams['HASH'] = '?question=#ID#' . trim($arParams['HASH']);

		return $arParams;
	}

	public function executeComponent()
	{
		Tools::formatParams($this->arParams);

		$this->initData();
	}

	protected function initData()
	{
		$obParser = new CTextParser();

		$arParams = $this->arParams;
		$arResult = &$this->arResult;

		$arSort   = array('DATE_CREATE' => 'desc', 'ID' => 'desc');
		$arSelect = array(
			 'ID',
			 'DATE_CREATE',
			 'ELEMENT_ID',
			 'XML_ID',
			 'CODE',
			 'USER_ID',
			 'GUEST_NAME',
			 'PAGE_TITLE',
			 'PAGE_URL',
		);

		if($arParams['TEXT_ON'] == 'Y') {
			$arSelect[] = 'TEXT';
		}

		$arFilter = array(
			 '=ACTIVE'  => 'Y',
			 '=SITE_ID' => SITE_ID,
		);

		if($arParams['IBLOCK_ID'])
			$arFilter['=IBLOCK_ID'] = $arParams['IBLOCK_ID'];

		if($this->startResultCache(false, array($arSort, $arFilter, $arSelect, $arParams['ITEMS_LIMIT']))) {

			$rsQuestion = QuestionTable::getList(array(
				 'order'  => $arSort,
				 'filter' => $arFilter,
				 'select' => $arSelect,
				 "limit"  => $arParams['ITEMS_LIMIT'],
			));

			$arElementId = array();
			$arXmlId     = array();
			$arCode      = array();
			$arIserId    = array();
			$arItems     = array();
			while($arItem = $rsQuestion->fetch(new Converter)) {

				if($arItem['TEXT'] && $arParams['TEXT_LIMIT'] > 0) {
					$arItem['TEXT'] = $obParser->html_cut($arItem['TEXT'], $arParams['TEXT_LIMIT']);
				}

				if(strlen($arItem['DATE_CREATE']) > 0)
					$arItem['DISPLAY_ACTIVE_FROM'] = Tools::formatDate($arParams['DATE_FORMAT'], MakeTimeStamp($arItem['DATE_CREATE'], CSite::GetDateFormat()));
				else
					$arItem['DISPLAY_ACTIVE_FROM'] = '';

				if($userId = $arItem['USER_ID'])
					$arIserId[ $userId ] = $userId;


				if($arItem['ELEMENT_ID'])
					$arElementId[] = $arItem['ELEMENT_ID'];
				if($arItem['XML_ID'])
					$arXmlId[] = $arItem['XML_ID'];
				if($arItem['CODE'])
					$arCode[] = $arItem['CODE'];

				$arItems[] = $arItem;
			}


			$arElFilter = false;
			if($arElementId)
				$arElFilter['=ID'] = $arElementId;
			if($arXmlId)
				$arElFilter['=XML_ID'] = $arXmlId;
			if($arCode)
				$arElFilter['=CODE'] = $arCode;


			$arElements = array();
			if($arElFilter) {
				$rsElement = \CIBlockElement::GetList(false, $arElFilter, false, false, array('ID', 'XML_ID', 'CODE', 'NAME', 'DETAIL_PAGE_URL'));
				while($arElement = $rsElement->GetNext(false, false)) {

					$elId = false;

					if($arElementId)
						$elId = $arElement['ID'];
					if($arXmlId)
						$elId = $arElement['XML_ID'];
					if($arCode)
						$elId = $arElement['CODE'];

					if($elId) {
						$arElements[ $elId ] = array(
							 'NAME'     => $arElement['NAME'],
							 'PAGE_URL' => $arElement['DETAIL_PAGE_URL'],
						);
					}
				}
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


			foreach($arItems as &$arItem) {

				$elementId = false;
				if($arItem['ELEMENT_ID'])
					$elementId = $arItem['ELEMENT_ID'];
				if($arItem['XML_ID'])
					$elementId = $arItem['XML_ID'];
				if($arItem['CODE'])
					$elementId = $arItem['CODE'];

				$user    = (array)$arUsers[ $arItem['USER_ID'] ];
				$element = (array)$arElements[ $elementId ];


				if(!$arItem['GUEST_NAME'] && $user['NAME'])
					$arItem['GUEST_NAME'] = $user['NAME'];

				if(!$arItem['GUEST_EMAIL'] && $user['EMAIL'])
					$arItem['GUEST_EMAIL'] = $user['EMAIL'];

				$arItem['NAME']     = $element['NAME'];

				//$arItem['PAGE_URL'] = $element['PAGE_URL'];
				$arItem['PAGE_URL'] = str_replace('#ID#', $arItem['ID'], $element['PAGE_URL'] . $arParams['HASH']);

				$arItem['PICTURE']  = Tools::getGravatar($arItem['GUEST_EMAIL']);
			}

			$arResult['ITEMS'] = $arItems;

			unset($arUsers, $arIserId, $arItems);

			$this->setResultCacheKeys(array());

			$this->includeComponentTemplate();
		}
	}
}